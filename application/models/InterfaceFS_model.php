<?php
defined('BASEPATH') or exit('');

class interfaceFS_model extends CI_Model
{
    private $ceh;
    private $yard_id = '';

    function __construct()
    {
        parent::__construct();
        $this->ceh = $this->load->database('mssql', TRUE);
        $this->yard_id = $this->config->item('YARD_ID');
    }

    private function fsDateTime($format, $d = NULL)
    {
        if ($d !== NULL) {
            return date($format, strtotime($this->funcs->dbDateTime($d)));
        } else {
            return date($format);
        }
    }

    public function transferInvoice($invoiceData, $draftDetail, $isCancelInvoice = false)
    {
        if (count($draftDetail) == 0) {
            return array(
                'success' => false,
                'message' => 'nothing to transfer to FS'
            );
        }

        $configFS = $this->config->item('FS'); //await FunctionModel['getConfig']('FS', 'NHP')
        $invoiceDate = $this->fsDateTime($configFS['date_format'], $invoiceData['INV_DATE']);

        if (empty($invoiceData['CusID'])) {
            $payer = $this->retrievePayerById($invoiceData['PAYER']);
            if ($payer === NULL) {
                return array(
                    'success' => false,
                    'message' => 'Customer [' . $invoiceData['PAYER'] . '] infor is not found!'
                );
            }
            $invoiceData['CusID'] = $payer['CusID'];
            $invoiceData['VAT_CD'] = $payer['VAT_CD'];
        }

        //get vessel info
        if ($invoiceData['ShipKey'] == 'STORE') {
            $invoiceData['BerthDate'] = $invoiceData['INV_DATE'];
            $invoiceData['ShipID'] = 'STORE';
            $invoiceData['ShipVoy'] = '0000';
        }

        if (empty($invoiceData['BerthDate'])) {
            $vss = $this->retrieveVesselByKey($invoiceData['ShipKey']);
            if ($vss === NULL) {
                return array(
                    'success' => false,
                    'message' => 'ShipKey [' . $invoiceData['ShipKey'] . '] infor is not found!'
                );
            }
            $invoiceData['BerthDate'] = $vss['BerthDate'];
            $invoiceData['ShipID'] = $vss['ShipID'];
            $invoiceData['ShipVoy'] = $vss['ShipVoy'];
            $invoiceData['ShipYear'] = $vss['ShipYear'];
        }

        try {
            $fsDb = $this->load->database('FS', TRUE);
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            return array(
                'success' => false,
                'message' => 'Connect to FS: ' . $e->getMessage()
            );
        }

        $serial = 'DV' . $invoiceData['INV_PREFIX'];
        $invNo = $invoiceData['INV_NO_PRE'];
        try {
            $checkingFS  = $fsDb->select('ID,  TRANGTHAI')->where(['SERIAL' => $serial, 'SOHOADON' => $invNo])->get('HDKETOAN')->result_array();
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            return [
                'success' => false,
                'message' => 'Connect to FS: ' . $e->getMessage()
            ];
        }

        //check inv status
        $fsInvoiceStatus = count($checkingFS) > 0 ? $checkingFS[0]['TRANGTHAI'] : '';
        $fsInvoiceStatus = strtoupper(trim($fsInvoiceStatus));
        if ($fsInvoiceStatus == 'T' || $fsInvoiceStatus == 'P') {
            return [
                'success' => false,
                'message' =>  "Invoice [$serial$invNo] with status [$fsInvoiceStatus] - can not transfer! Please check FS"
            ];
        }

        //if cancel invoice + status in FS is [U] -> update status U -> E , return
        if ($fsInvoiceStatus == 'U') {
            //if is exist in FS + not cancel => return;
            if (!$isCancelInvoice) {
                return [
                    'success' => true,
                    'message' => '',
                ];
            }

            //else update status E
            try {
                $fsDb->where_in('ID', array_column($checkingFS, 'ID'))->update('HDKETOAN', ['TRANGTHAI' => 'E']);
                $fsDb->close();
                return [
                    'success' => true,
                    'message' => '',
                    'invoiceNo' => $invoiceData['INV_NO'],
                    'isPosted' => 6
                ];
            } catch (Exception $e) {
                log_message('error', $e->getMessage());
                return [
                    'success' => false,
                    'message' => 'Can not change status [E] for invoice [' . $invoiceData['INV_NO'] . '] : ' . $e->getMessage(),
                    'invoiceNo' => $invoiceData['INV_NO'],
                    'isPosted' => 7
                ];
            }
        }

        $trfCodes = array_unique(array_map(function ($dr) {
            return trim($dr['TRF_CODE']);
        }, $draftDetail));

        $mappingConfigs = $this->ceh->select('TRF_CODE, MA_DV_ACC')->where_in('TRF_CODE', $trfCodes)->get('TRF_CODES')->result_array();
        $mappingMaDV = array_filter($mappingConfigs, function ($var) {
            return !empty($var['MA_DV_ACC']);
        });

        if (count($mappingMaDV) == 0) {
            return [
                'success' => false,
                'message' =>  "Can not find [MA_DV] mapping from [Tariff Code]"
            ];
        }

        $insertList = array();
        for ($i = 0; $i < count($draftDetail); $i++) {
            $item = $draftDetail[$i];
            $getMaDVMapping = array_filter($mappingMaDV, function ($var) use ($item) {
                return ($var['TRF_CODE'] === $item['TRF_CODE']);
            });
            $madv = array_column($getMaDVMapping, 'MA_DV_ACC')[0];

            $temp = array(
                'SERIAL' => $serial,
                'SOHOADON' => $invNo,
                'MA_DVI' =>  $invoiceData['CusID'],
                'MA_VAT' => $invoiceData['VAT_CD'],
                'NGAY_PS' => $invoiceDate,
                'NOIDUNG' => $this->unicode2VietwareF($item['TRF_DESC']),
                'MA_DV' => $madv,
                'MA_LHANG' => $item['CARGO_TYPE'],
                'SO_LUONG' => $item['QTY'],
                'SO_NGAY' => 0,
                'DON_GIA' => $item['UNIT_RATE'],
                'SO_TIEN' => $item['AMOUNT'],
                'TIEN_THUE' => $item['VAT'],
                'THUE_VAT' => $item['VAT_RATE'],
                'MA_NTE' => $invoiceData['CURRENCYID'], //inv_dft
                'TY_GIA' => $invoiceData['RATE'], //inv_dft
                'TRANGTHAI' => $isCancelInvoice ? 'E' : 'U',
                'NGAY_CC' => $this->fsDateTime($configFS['date_format'], $invoiceData['BerthDate']),
                'TEN_TAU' => $invoiceData['ShipID'],
                'CHUYEN_TAU' => $invoiceData['ShipVoy'],
                'MA_CTY' => $configFS['parameter']['MA_CTY'],
                'THANGNAM' => $this->fsDateTime('mY', $invoiceData['INV_DATE']),
                'TRF_CODE' => $item['TRF_CODE'],
                'PaymentType' => $invoiceData['INV_TYPE'],
                'InvoiceForm' => $invoiceData['ACC_CD']
            );
            array_push($insertList, $temp);
        }

        log_message('error', json_encode($temp));
        //get inv Cont
        $fsDb->trans_start();
        $fsDb->trans_strict(TRUE);
        $fsDb->insert_batch('HDKETOAN', $insertList);

        $fsDb->trans_complete();
        if ($fsDb->trans_status() === FALSE) {
            $fsDb->trans_rollback();
            $errorMsg = $fsDb->error()['message'];
            $fsDb->close();

            $isPosted = strpos($errorMsg, 'unique constraint') !== false ? 1 : 2;
            if ($isCancelInvoice) {
                $isPosted += 5;
            }

            return [
                'success' => $isPosted == 1 || $isPosted == 6, //1: chuyen hd moi  | 6: chuyen hd huy
                'message' => $errorMsg,
                'invoiceNo' => $invoiceData['INV_NO'],
                'isPosted' => $isPosted
            ];
        } else {
            $fsDb->trans_commit();
            $fsDb->close();
            return [
                'success' => true,
                'invoiceNo' => $invoiceData['INV_NO'],
                'isPosted' => $isCancelInvoice ? 6 : 1
            ];
        }
    }

    public function transferMultipleInvoice($invoiceDatas)
    {
        $allResult = array();
        try {
            foreach ($invoiceDatas as $element) {
                $draftDetail = array();
                if (isset($element['details']) && is_array($element['details']) && count($element['details']) > 0) {
                    $draftDetail = $element['details'];
                } else if (isset($element['INV_NO']) && $element['INV_NO'] !== '') { //PinCode
                    $tempDraft = $this->ceh->select('DRAFT_INV_NO')->where('INV_NO', trim($element['INV_NO']))->get('INV_DFT')->result_array();
                    if (count($tempDraft) > 0) {
                        $DRAFT_INV_NOs = array_column($tempDraft, 'DRAFT_INV_NO');
                        $draftDetail = $this->ceh->where_in('DRAFT_INV_NO', $DRAFT_INV_NOs)->get('INV_DFT_DTL')->result_array();
                    }
                } else if (isset($element['DRAFT_INV_NO']) && $element['DRAFT_INV_NO'] !== '') {
                    $draftDetail = $this->ceh->where('DRAFT_INV_NO', trim($element['DRAFT_INV_NO']))->get('INV_DFT_DTL')->result_array();
                }

                $isCancelInvoice = isset($element['PAYMENT_STATUS']) && $element['PAYMENT_STATUS'] == "C" ? true : false;
                if ($isCancelInvoice) {
                    $tempDraft = array();
                    if (isset($element['REF_NO']) && $element['REF_NO'] !== '') {
                        $tempDraft = $this->ceh->select('DRAFT_INV_NO')->where('REF_NO', trim($element['REF_NO']))->get('INV_DFT')->result_array();
                    } else {
                        $regex = "/.*\(([^)]*)\)/";
                        preg_match($regex, $element['CancelRemark'], $matches);
                        $result = explode(',', end($matches));
                        $drafts = array_map(function ($dr) {
                            return trim($dr);
                        }, $result);
                        $tempDraft = $this->ceh->select('DRAFT_INV_NO')->where_in("DRAFT_INV_NO", $drafts)->get('INV_DFT')->result_array();
                    }

                    if (count($tempDraft) > 0) {
                        $DRAFT_INV_NOs = array_column($tempDraft, 'DRAFT_INV_NO');
                        $draftDetail = $this->ceh->where_in('DRAFT_INV_NO', $DRAFT_INV_NOs)->get('INV_DFT_DTL')->result_array();
                    }
                }

                $res = $this->transferInvoice($element, $draftDetail, $isCancelInvoice);
                array_push($allResult, $res);
            }
        } catch (Exception $error) {
            array_push($allResult, array(
                'success' => false,
                'message' => $error->getMessage()
            ));
        }

        //check update to INV_VAT
        $hasUpdates = array_filter($allResult, function ($var) {
            return !empty($var['invoiceNo']);
        });
        $updates = array_map(function ($res) {
            return ['isPosted' => $res['isPosted'], 'INV_NO' => $res['invoiceNo']];
        }, $hasUpdates);

        if (count($updates) > 0) {
            $this->ceh->where('YARD_ID', $this->yard_id)->update_batch('INV_VAT', $updates, 'INV_NO');
        }

        //filter error message;
        $errors = array_filter($allResult, function ($var) {
            return ($var['success'] === false);
        });

        return count($errors) > 0 ? $errors : ['success' => true];
    }

    public function transferTariffCode($datas)
    {
        if (count($datas) == 0) {
            return array(
                'success' => false,
                'message' => 'nothing to transfer to FS'
            );
        }

        $configFS = $this->config->item('FS');
        $detail = array();
        for ($i = 0; $i < count($datas); $i++) {
            $item = $datas[$i];
            $temp = array(
                'NGUON' => $configFS['parameter']['SOURCE_CODE'],
                'SERVER' => $configFS['parameter']['SERVER'],
                'CHARGECODE' => $item['TRF_CODE'],
                'DIENGIAI' => $item['TRF_STD_DESC'],
                'ACCCODE' => '',
                'LASTUPDATE' => ''
            );

            array_push($detail, $temp);
        }

        try {
            $fsDb = $this->load->database('FS', TRUE);
        } catch (Exception $error) {
            log_message('error', json_encode($error));
            return array(
                'success' => false,
                'message' => $error->getMessage()
            );
        }

        $fsDb->trans_start();
        $fsDb->trans_strict(TRUE);

        $fsDb->insert_batch('GMD_MAP_CHARGECODE', $detail);

        $fsDb->trans_complete();
        if ($fsDb->trans_status() === FALSE) {
            $fsDb->trans_rollback();
            $errorMsg = $fsDb->error()['message'];
            $fsDb->close();

            return array(
                'success' => false,
                'message' => $errorMsg
            );
        } else {
            $fsDb->trans_commit();
            $fsDb->close();
            return array('success' => true);
        }
    }

    public function transferNewCustomer($datas)
    {
        if (count($datas) == 0) {
            return array(
                'success' => false,
                'message' => 'nothing to transfer to FS'
            );
        }

        $configFS = $this->config->item('FS');
        try {
            $fsDb = $this->load->database('FS_KH', TRUE);
        } catch (Exception $error) {
            log_message('error', json_encode($error));
            return array(
                'success' => false,
                'message' => $error->getMessage()
            );
        }

        $listDataIns = array();
        $listDataUpd = array();
        foreach ($datas as $item) {
            $temp = array(
                "MA_DVI" => $item['CusID'],
                "TEN_DVI" => $this->unicode2VietwareF(trim($item['CusName'])),
                "TEN_DVI_E" => "",
                "TEN_DVI2" => "",
                "TMAT_CKH" => "",
                "MA_VAT" => trim($item['VAT_CD']),
                "DIENTHOAI" => $item['Tel'] ?? '',
                "EMAIL" => $item['Email'] ?? '',
                "FAX" => $item['Fax'] ?? '',
                "DIACHI" => $this->unicode2VietwareF($item['Address'] ?? ''),
                "DIACHI_E" => "",
                "NGUOI_LLAC" => $item['PersonalID'] ?? '',
                "TK_NH" => "",
                "TEN_NH" => "",
                "LOAI_DVI" => "",
                "NOI_BO" => "",
                "MA_NSX" => "",
                "TEN_NHASX" => "",
                "MA_NVIEN" => $this->session->userdata("UserID"),
                "MA_CTY" => $configFS['parameter']['MA_CTY']
            );

            //check exist
            $exist = $fsDb->select('ID')->where([
                'MA_DVI' => $temp['MA_DVI'],
                'MA_VAT' => $temp['MA_VAT'],
                'MA_CTY' => $temp['MA_CTY']
            ])->get('DM_DVI')->row_array();
            if ($exist !== NULL) {
                $temp['ID'] = $exist['ID'];
                $temp['THAYDOI'] = date('ymdHis') . rand(1, pow(10, 8) - 1);
                array_push($listDataUpd, $temp);
            } else {
                array_push($listDataIns, $temp);
            }
        }

        if (count($listDataIns) == 0 && count($listDataUpd) == 0) {
            return array('success' => false, 'message' => 'Nothing to transfer');
        }

        $fsDb->trans_start();
        $fsDb->trans_strict(TRUE);

        if (count($listDataIns) > 0) {
            $fsDb->insert_batch('DM_DVI', $listDataIns);
        }

        if (count($listDataUpd) > 0) {
            $fsDb->update_batch('DM_DVI', $listDataUpd, 'ID');
        }

        $fsDb->trans_complete();
        if ($fsDb->trans_status() === FALSE) {
            $fsDb->trans_rollback();
            $errorMsg = $fsDb->error()['message'];
            $fsDb->close();

            log_message('error', 'FS CUSTOMER ERR: ' . $errorMsg);
            log_message('error', $fsDb->error());
            $isPosted = strpos($errorMsg, 'unique constraint') !== false ? 1 : -1;
            return array(
                'success' => $isPosted == 1,
                'message' => $errorMsg
            );
        } else {
            $fsDb->trans_commit();
            $fsDb->close();
            return array('success' => true);
        }
    }

    public function loadInterfaceInvoice($args)
    {
        $issueFromDate = $args['issueFromDate'] ? $this->funcs->dbDateTime($args['issueFromDate']) : '';
        $issueToDate = $args['issueToDate'] ? $this->funcs->dbDateTime($args['issueToDate']) : '';
        $cusID = isset($args['cusID']) ? $args['cusID'] : '';
        $searchVal = isset($args['searchVal']) ? $args['searchVal'] : '';
        $paymentType = isset($args['paymentType']) ? $args['paymentType'] : '';
        $status = isset($args['status']) ? $args['status'] : '';
        $isPosted = isset($args['isPosted']) ? $args['isPosted'] : '';

        $stmt = $this->ceh->select('a.INV_NO, a.INV_DATE, a.INV_TYPE, a.PAYMENT_STATUS, a.PinCode, a.REF_NO
                                , a.CreatedBy, a.CancelDate, a.CancelBy, a.CancelRemark, a.INV_NO_PRE, a.INV_PREFIX, a.PAYER, a.isPosted, a.ShipKey, a.ShipID, a.ShipVoy, a.ShipYear
                                , a.AMOUNT, a.VAT, a.TAMOUNT, a.RATE, a.CURRENCYID, a.ACC_CD, a.INV_TYPE
                                , m.VAT_CD, m.CusName, m.CusID, vs.BerthDate')
            // ->join('INV_DFT as d', 'd.INV_NO = a.INV_NO AND d.YARD_ID = a.YARD_ID', 'left')
            ->join('VESSEL_SCHEDULE as vs', 'vs.ShipKey = a.ShipKey AND vs.YARD_ID = a.YARD_ID', 'left')
            ->join('CUSTOMERS as m', 'm.CusID = a.PAYER AND m.YARD_ID = a.YARD_ID', 'left');

        if ($issueFromDate !== '') {
            $stmt->where('a.INV_DATE >=', $issueFromDate);
        }

        if ($issueToDate !== '') {
            $stmt->where('a.INV_DATE <=', $issueToDate);
        }

        if ($paymentType !== '') {
            $stmt->where('a.INV_TYPE', $paymentType);
        }

        if ($status !== '') {
            $operator = $status == 'C' ? '=' : '!=';
            $stmt->where('a.PAYMENT_STATUS ' . $operator, 'C');
        }

        if ($isPosted !== '') {
            $stmt->where('a.isPosted', $isPosted);
        }

        if ($cusID !== '') {
            $stmt->where('a.PAYER', $cusID);
        }

        if ($searchVal !== '') {
            $stmt->group_start();
            $stmt->where('a.PinCode', $searchVal);
            $stmt->or_like('a.INV_NO', $searchVal);
            $stmt->or_like('a.REF_NO', $searchVal);
            $stmt->group_end();
        }

        $stmt = $stmt->order_by('a.INV_NO, a.INV_DATE, a.PAYER')->get('INV_VAT a')->result_array();
        return $stmt;
    }

    public function getPayers($user = '')
    {
        $this->ceh->select('CusID, CusName, Address, VAT_CD, CusType, IsOpr, IsAgency, IsOwner, IsLogis, IsTrans, IsOther
        					, Email, EMAIL_DD, NameDD, PersonalID');
        if ($user != '' && $user != 'Admin')
            $this->ceh->where('NameDD', $user);

        $this->ceh->where('VAT_CD IS NOT NULL');

        $this->ceh->where('YARD_ID', $this->yard_id);

        $this->ceh->order_by('CusName');
        $stmt = $this->ceh->get('CUSTOMERS');
        return $stmt->result_array();
    }

    public function retrievePayerById($cusId)
    {
        $this->ceh->select('CusID, CusName, Address, VAT_CD, CusType, IsOpr, IsAgency, IsOwner, IsLogis, IsTrans, IsOther
        					, Email, EMAIL_DD, NameDD, PersonalID');

        $this->ceh->where('CusID', $cusId);
        $this->ceh->where('YARD_ID', $this->yard_id);
        $this->ceh->order_by('CusID');
        $stmt = $this->ceh->get('CUSTOMERS')->row_array();
        return $stmt;
    }

    public function retrieveVesselByKey($shipKey = '')
    {
        $this->ceh->select('vs.ShipKey, vv.ShipName, vs.ShipID, vs.ShipYear, vs.ShipVoy, vs.ImVoy, vs.ExVoy, vs.ETB, vs.ETD, vs.BerthDate');
        $this->ceh->join('VESSELS vv', 'vv.ShipID = vs.ShipID');
        $this->ceh->where('vs.YARD_ID', $this->yard_id);

        if ($shipKey != '') {
            $this->ceh->where('vs.ShipKey', $shipKey);
        }

        $stmt = $this->ceh->get('VESSEL_SCHEDULE vs')->row_array();
        return $stmt;
    }

    function unicode2VietwareF($text)
    {
        $UNI = array(
            "À", "Á", "Â", "Ã", "È", "É", "Ê", "Ì", "Í", "Ò",
            "Ó", "Ô", "Õ", "Ù", "Ú", "Ý", "à", "á", "â", "ã",
            "è", "é", "ê", "ì", "í", "ò", "ó", "ô", "õ", "ù",
            "ú", "ý", "Ă", "ă", "Đ", "đ", "Ĩ", "ĩ", "Ũ", "ũ",
            "Ơ", "ơ", "Ư", "ư", "Ạ", "ạ", "Ả", "ả", "Ấ", "ấ",
            "Ầ", "ầ", "Ẩ", "ẩ", "Ẫ", "ẫ", "Ậ", "ậ", "Ắ", "ắ",
            "Ằ", "ằ", "Ẳ", "ẳ", "Ẵ", "ẵ", "Ặ", "ặ", "Ẹ", "ẹ",
            "Ẻ", "ẻ", "Ẽ", "ẽ", "Ế", "ế", "Ề", "ề", "Ể", "ể",
            "Ễ", "ễ", "Ệ", "ệ", "Ỉ", "ỉ", "Ị", "ị", "Ọ", "ọ",
            "Ỏ", "ỏ", "Ố", "ố", "Ồ", "ồ", "Ổ", "ổ", "Ỗ", "ỗ",
            "Ộ", "ộ", "Ớ", "ớ", "Ờ", "ờ", "Ở", "ở", "Ỡ", "ỡ",
            "Ợ", "ợ", "Ụ", "ụ", "Ủ", "ủ", "Ứ", "ứ", "Ừ", "ừ",
            "Ử", "ử", "Ữ", "ữ", "Ự", "ự", "Ỳ", "ỳ", "Ỵ", "ỵ",
            "Ỷ", "ỷ", "Ỹ", "ỹ"
        );
        $TCVN3 = array(
            "Aµ", "A¸", "¢", "A·", "EÌ", "EÐ", "£", "I×", "IÝ", "Oß",
            "Oã", "¤", "Oâ", "Uï", "Uó", "Yý", "µ", "¸", "©", "·",
            "Ì", "Ð", "ª", "×", "Ý", "ß", "ã", "«", "â", "ï",
            "ó", "ý", "¡", "¨", "§", "®", "IÜ", "Ü", "Uò", "ò",
            "¥", "¬", "¦", "­", "A¹", "¹", "A¶", "¶", "¢Ê", "Ê",
            "¢Ç", "Ç", "¢È", "È", "¢É", "É", "¢Ë", "Ë", "¡¾", "¾",
            "¡»", "»", "¡¼", "¼", "¡½", "½", "¡Æ", "Æ", "EÑ", "Ñ",
            "EÎ", "Î", "EÏ", "Ï", "£Õ", "Õ", "£Ò", "Ò", "£Ó", "Ó",
            "£Ô", "Ô", "£Ö", "Ö", "IØ", "Ø", "IÞ", "Þ", "Oä", "ä",
            "Oá", "á", "¤è", "è", "¤å", "å", "¤æ", "æ", "¤ç", "ç",
            "¤é", "é", "¥í", "í", "¥ê", "ê", "¥ë", "ë", "¥ì", "ì",
            "¥î", "î", "Uô", "ô", "Uñ", "ñ", "¦ø", "ø", "¦õ", "õ",
            "¦ö", "ö", "¦÷", "÷", "¦ù", "ù", "Yú", "ú", "Yþ", "þ",
            "Yû", "û", "Yü", "ü"
        );
        $VIETWAREF = array(
            "ª", "À", "—", "º", "Ì", "Ï", "™", "Ø", "Û", "ß",
            "â", "š", "á", "î", "ò", "ü", "ª", "À", "¡", "º",
            "Ì", "Ï", "£", "Ø", "Û", "ß", "â", "¤", "á", "î",
            "ò", "ü", "–", "Ÿ", "˜", "¢", "Ú", "Ú", "ñ", "ñ",
            "›", "¥", "œ", "§", "Á", "Á", "¶", "¶", "Ê", "Ê",
            "Ç", "Ç", "È", "È", "É", "É", "Ë", "Ë", "Å", "Å",
            "Â", "Â", "Ã", "Ã", "Ä", "Ä", "Æ", "Æ", "Ñ", "Ñ",
            "Í", "Í", "Î", "Î", "Õ", "Õ", "Ò", "Ò", "Ó", "Ó",
            "Ô", "Ô", "Ö", "Ö", "Ù", "Ù", "Ü", "Ü", "ã", "ã",
            "à", "à", "ç", "ç", "ä", "ä", "å", "å", "æ", "æ",
            "è", "è", "ì", "ì", "é", "é", "ê", "ê", "ë", "ë",
            "í", "í", "ó", "ó", "ï", "ï", "÷", "÷", "ô", "ô",
            "õ", "õ", "ö", "ö", "ø", "ø", "ù", "ù", "ÿ", "ÿ",
            "ú", "ú", "û", "û"
        );
        for ($i = 0; $i < count($UNI); $i++) {
            $text = str_replace($UNI[$i], $VIETWAREF[$i], $text);
        }
        return $text;
    }
}
