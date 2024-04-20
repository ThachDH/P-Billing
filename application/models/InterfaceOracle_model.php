<?php
defined('BASEPATH') or exit('');

class interfaceOracle_model extends CI_Model
{
    private $ceh;
    private $yard_id = '';

    function __construct()
    {
        parent::__construct();
        $this->ceh = $this->load->database('mssql', TRUE);
        $this->yard_id = $this->config->item('YARD_ID');
    }

    private function orcDateTime($format, $d = NULL)
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
                'message' => 'nothing to transfer to oracle'
            );
        }

        $configOracle = $this->config->item('ORACLE'); //await FunctionModel['getConfig']('ORACLE', 'NHP')

        $invoiceDate = $this->orcDateTime($configOracle['date_format'], $invoiceData['INV_DATE']);
        $serialPrefix = $configOracle['parameter']['SERIAL_PREFIX'];
        if ($serialPrefix === NULL) {
            return array(
                'success' => FALSE,
                'message' => 'Chưa cấu hình ký hiệu hoá đơn [SERIAL_PREFIX]',
                'invoiceNo' => $invoiceData['INV_NO']
            );
        }

        $invPrefix = $invoiceData['INV_PREFIX'] . $serialPrefix[$invoiceData['INV_TYPE']];
        $header = array(
            'BATCH_SOURCE_NAME' => $configOracle['parameter']['BATCH_SOURCE_NAME'],
            'SERVER' => $configOracle['parameter']['SERVER'],
            'MAKH' => $invoiceData['PAYER'],
            'CHARGECODE' => '',
            'CREATION_DATE' => $invoiceDate, //new Date($this->funcs->dbDateTime(invoiceData[)),
            'SOURCE_CODE' => $configOracle['parameter']['SOURCE_CODE'],
            'ORIG_SYSTEM_BATCH_NAME' => $configOracle['parameter']['SERVER'] . "-" . date('Ymd'),
            'TERM_NAME' => $configOracle['parameter']['TERM_NAME'],
            'TRX_DATE' => $invoiceDate,
            'TRX_NUMBER' => $invPrefix . $invoiceData['INV_NO_PRE'],
            'CURRENCY_CODE' => $invoiceData['CURRENCYID'],
            'CUST_TRX_TYPE_NAME' => $configOracle['parameter']['CUST_TRX_TYPE_NAME'],
            'GL_DATE' => $invoiceDate,
            'PRINTING_OPTION' => $configOracle['parameter']['PRINTING_OPTION'],
            'CUSTOMER_NUMBER' => '',
            'ORG_ID' => $configOracle['parameter']['ORG_ID'],
            'SET_OF_BOOKS_ID' => $configOracle['parameter']['SET_OF_BOOKS_ID'],
            'CONVERSION_DATE' => '',
            'CONVERSION_RATE' => floatval($invoiceData['RATE']),
            'CONVERSION_TYPE' => $configOracle['parameter']['CONVERSION_TYPE'],
            'CREATED_BY' => 0,
            'HEADER_ATTRIBUTE_CATEGORY' => '',
            'HEADER_ATTRIBUTE1' => '',
            'HEADER_ATTRIBUTE2' => '',
            'HEADER_ATTRIBUTE3' => '',
            'HEADER_ATTRIBUTE4' => '',
            'HEADER_ATTRIBUTE5' => '',
            'HEADER_ATTRIBUTE6' => '',
            'HEADER_ATTRIBUTE7' => '',
            'HEADER_ATTRIBUTE8' => '',
            'HEADER_ATTRIBUTE9' => '',
            'HEADER_ATTRIBUTE10' => '',
            'HEADER_ATTRIBUTE11' => '',
            'HEADER_ATTRIBUTE12' => '',
            'HEADER_ATTRIBUTE13' => '',
            'HEADER_ATTRIBUTE14' => '',
            'HEADER_ATTRIBUTE15' => '',
            'STATUS' => 0,
            'CANCEL_STATUS' => 0,
            'DIEMPH' => $configOracle['parameter']['DIEMPH'],
            'MADVI' => $configOracle['parameter']['MADVI']
        );

        //neu la goi huy, thi thay dooi cac thong tin
        if ($isCancelInvoice) {
            $cancelDate = date($configOracle['date_format'], strtotime($this->funcs->dbDateTime($invoiceData['CancelDate'])));
            $header['TRX_NUMBER'] = $header['TRX_NUMBER'] . 'C';
            $header['CREATION_DATE'] = $this->orcDateTime($configOracle['date_format']);
            $header['TRX_DATE'] = $cancelDate;
            $header['GL_DATE'] = $cancelDate;
            $header['CUST_TRX_TYPE_NAME'] = 'Credit Memo';
            $header['TERM_NAME'] = ''; //-- A TUAN (IT) 16:43 31/05/2021 (Grp zalo)
            // $header['STATUS'] = 1; -- A TUAN (IT) 16:09 31/05/2021 (Grp zalo)
            $header['CANCEL_STATUS'] = 1;
        }

        $detail = array();

        for ($i = 0; $i < count($draftDetail); $i++) {
            $item = $draftDetail[$i];
            $attr2 = floor((int)($invoiceData['INV_NO_PRE']) / 100);
            //them thong tin dong doanh thu
            $temp = array(
                'TRX_NUMBER' => $header['TRX_NUMBER'],
                'LINE_NUMBER' => $i + 1,
                'AMOUNT_INCLUDING_TAX_FLAG' => $configOracle['parameter']['AMOUNT_INCLUDING_TAX_FLAG'],
                'LINE_TYPE' => 'LINE',
                'DESCRIPTION' => $item['TRF_DESC'] ? mb_substr($item['TRF_DESC'], 0, 240 - (strlen($item['TRF_DESC']) - mb_strlen($item['TRF_DESC']))) : '',
                'CHARGECODE' => $item['TRF_CODE'],
                'MEMO_LINE_NAME' => '',
                'PRIMARY_SALESREP_NUMBER' => '',
                'PRINTING_OPTION' => $configOracle['parameter']['PRINTING_OPTION'],
                'QUANTITY' => $item['QTY'],
                'UNIT_SELLING_PRICE' => $item['UNIT_RATE'],
                'AMOUNT' => $item['AMOUNT'],
                'UOM_NAME' => $item['INV_UNIT'],
                'SET_OF_BOOKS_ID' => $configOracle['parameter']['SET_OF_BOOKS_ID'],
                'ATTRIBUTE_CATEGORY' => $configOracle['parameter']['ATTRIBUTE_CATEGORY'],
                'ATTRIBUTE1' => $item['Remark'] ? mb_substr($item['Remark'], 0, 150 - (strlen($item['Remark']) - mb_strlen($item['Remark']))) : '',
                'ATTRIBUTE2' => '',
                'ATTRIBUTE3' => '',
                'ATTRIBUTE4' => '',
                'ATTRIBUTE5' => '',
                'ATTRIBUTE6' => '',
                'ATTRIBUTE7' => '',
                'ATTRIBUTE8' => '',
                'ATTRIBUTE9' => '',
                'ATTRIBUTE10' => '',
                'ATTRIBUTE11' => '',
                'ATTRIBUTE12' => '',
                'ATTRIBUTE13' => '',
                'ATTRIBUTE14' => '',
                'ATTRIBUTE15' => '',
                'INTERFACE_LINE_CONTEXT' => $configOracle['parameter']['ATTRIBUTE_CATEGORY'],
                'INTERFACE_LINE_ATTRIBUTE1' => $invoiceData['INV_NO_PRE'], //so hoa don 0000001
                'INTERFACE_LINE_ATTRIBUTE2' => (int)$invoiceData['INV_NO_PRE'] % 100 == 0 ? $attr2 : $attr2 + 1, //so quyen hoa don
                'INTERFACE_LINE_ATTRIBUTE3' => $invPrefix, // ky hieu hoa don
                'INTERFACE_LINE_ATTRIBUTE4' => $i + 1, // so thu tu dong
                'INTERFACE_LINE_ATTRIBUTE5' => 'LINE', //loại dòng (LINE/TAX)
                'INTERFACE_LINE_ATTRIBUTE6' => $invoiceData['CreatedBy'], //nguoi len hoa don
                'INTERFACE_LINE_ATTRIBUTE7' => $configOracle['parameter']['SERVER'],
                'INTERFACE_LINE_ATTRIBUTE8' => $configOracle['parameter']['SERVER'],
                'INTERFACE_LINE_ATTRIBUTE9' => '',
                'INTERFACE_LINE_ATTRIBUTE10' => '',
                'INTERFACE_LINE_ATTRIBUTE11' => '',
                'INTERFACE_LINE_ATTRIBUTE12' => '',
                'INTERFACE_LINE_ATTRIBUTE13' => '',
                'INTERFACE_LINE_ATTRIBUTE14' => '',
                'INTERFACE_LINE_ATTRIBUTE15' => '',
                'LOAITHUE' => '',
                'THUESUAT' => '',
                'TAX_REGIME_CODE' => '',
                'TAX' => '',
                'TAX_JURISDICTION_CODE' => '',
                'TAX_STATUS_CODE' => '',
                'TAX_RATE_CODE' => '',
                'TAX_RATE' => '',
                'LINK_TO_LINE_CONTEXT' => '',
                'LINK_TO_LINE_ATTRIBUTE1' => '',
                'LINK_TO_LINE_ATTRIBUTE2' => '',
                'LINK_TO_LINE_ATTRIBUTE3' => '',
                'LINK_TO_LINE_ATTRIBUTE4' => '',
                'LINK_TO_LINE_ATTRIBUTE5' => '',
                'LINK_TO_LINE_ATTRIBUTE6' => '',
                'LINK_TO_LINE_ATTRIBUTE7' => '',
                'REFERENCE_LINE_CONTEXT' => '',
                'REFERENCE_LINE_ATTRIBUTE1' => '',
                'REFERENCE_LINE_ATTRIBUTE2' => '',
                'REFERENCE_LINE_ATTRIBUTE3' => '',
                'REFERENCE_LINE_ATTRIBUTE4' => '',
                'REFERENCE_LINE_ATTRIBUTE5' => '',
                'REFERENCE_LINE_ATTRIBUTE6' => '',
                'REFERENCE_LINE_ATTRIBUTE7' => '',
                'STATUS' => 0
            );

            if ($isCancelInvoice) {
                $temp['REFERENCE_LINE_CONTEXT'] = $temp['INTERFACE_LINE_CONTEXT'];
                $temp['REFERENCE_LINE_ATTRIBUTE1'] = $temp['INTERFACE_LINE_ATTRIBUTE1'];
                $temp['REFERENCE_LINE_ATTRIBUTE2'] = $temp['INTERFACE_LINE_ATTRIBUTE2'];
                $temp['REFERENCE_LINE_ATTRIBUTE3'] = $temp['INTERFACE_LINE_ATTRIBUTE3'];
                $temp['REFERENCE_LINE_ATTRIBUTE4'] = $temp['INTERFACE_LINE_ATTRIBUTE4'];
                $temp['REFERENCE_LINE_ATTRIBUTE5'] = $temp['INTERFACE_LINE_ATTRIBUTE5'];
                $temp['REFERENCE_LINE_ATTRIBUTE6'] = $temp['INTERFACE_LINE_ATTRIBUTE6'];

                $temp['UNIT_SELLING_PRICE'] = $temp['UNIT_SELLING_PRICE'] * -1;
                $temp['AMOUNT'] = $temp['AMOUNT'] * -1;
                $temp['INTERFACE_LINE_ATTRIBUTE5'] = 'LINE(VOID)';
                $temp['INTERFACE_LINE_ATTRIBUTE6'] = $invoiceData['CancelBy'];
            }

            array_push($detail, $temp);

            //them dong thue
            $temp1 = $temp;

            $temp1['LINK_TO_LINE_CONTEXT'] = $configOracle['parameter']['ATTRIBUTE_CATEGORY'];
            $temp1['LINK_TO_LINE_ATTRIBUTE1'] = $temp1['INTERFACE_LINE_ATTRIBUTE1'];
            $temp1['LINK_TO_LINE_ATTRIBUTE2'] = $temp1['INTERFACE_LINE_ATTRIBUTE2'];
            $temp1['LINK_TO_LINE_ATTRIBUTE3'] = $temp1['INTERFACE_LINE_ATTRIBUTE3'];
            $temp1['LINK_TO_LINE_ATTRIBUTE4'] = $temp1['INTERFACE_LINE_ATTRIBUTE4'];
            $temp1['LINK_TO_LINE_ATTRIBUTE5'] = $temp1['INTERFACE_LINE_ATTRIBUTE5'];
            $temp1['LINK_TO_LINE_ATTRIBUTE6'] = $temp1['INTERFACE_LINE_ATTRIBUTE6'];

            $temp1['LINE_TYPE'] = 'TAX';
            $temp1['INTERFACE_LINE_ATTRIBUTE5'] = 'TAX';
            $temp1['LOAITHUE'] = 'VAT';

            if( $item['VAT_RATE'] === NULL ) { //hd ko chiu thue
                $temp1['UNIT_SELLING_PRICE'] = 0;
                $temp1['AMOUNT'] = 0;
                $temp1['THUESUAT'] = '';
            }
            else {
                $vatAmt = floatval($item['VAT']);
                $temp1['UNIT_SELLING_PRICE'] = $item['VAT_RATE'] > 0 ? ($vatAmt / $item['VAT_RATE']) : $vatAmt;
                $temp1['AMOUNT'] = $vatAmt;
                $temp1['THUESUAT'] = floatval($item['VAT_RATE']);
            }

            if ($isCancelInvoice) {
                $temp1['REFERENCE_LINE_CONTEXT'] = $temp1['INTERFACE_LINE_CONTEXT'];
                $temp1['REFERENCE_LINE_ATTRIBUTE1'] = $temp1['INTERFACE_LINE_ATTRIBUTE1'];
                $temp1['REFERENCE_LINE_ATTRIBUTE2'] = $temp1['INTERFACE_LINE_ATTRIBUTE2'];
                $temp1['REFERENCE_LINE_ATTRIBUTE3'] = $temp1['INTERFACE_LINE_ATTRIBUTE3'];
                $temp1['REFERENCE_LINE_ATTRIBUTE4'] = $temp1['INTERFACE_LINE_ATTRIBUTE4'];
                $temp1['REFERENCE_LINE_ATTRIBUTE5'] = $temp1['INTERFACE_LINE_ATTRIBUTE5'];
                $temp1['REFERENCE_LINE_ATTRIBUTE6'] = $temp1['INTERFACE_LINE_ATTRIBUTE6'];

                $temp1['UNIT_SELLING_PRICE'] = $temp1['UNIT_SELLING_PRICE'] * -1;
                $temp1['AMOUNT'] = $temp1['AMOUNT'] * -1;
                $temp1['INTERFACE_LINE_ATTRIBUTE5'] = 'TAX(VOID)';
                $temp1['INTERFACE_LINE_ATTRIBUTE6'] = $invoiceData['CancelBy'];
            }

            array_push($detail, $temp1);
        }

        try {
            $oracleDb = $this->load->database('oracle', TRUE);
            $oracleDb->query("alter session set nls_date_format = ?", array($configOracle['orc_date_format']));
        } catch (Exception $e) {
            return array(
                'success' => false,
                'message' => 'Connect to ORACLE: ' . $e->getMessage()
            );
        }

        //get inv Cont
        $oracleDb->trans_start();
        $oracleDb->trans_strict(TRUE);
        $oracleDb->insert('GMD_AR_INVOICE_HEADERS', $header);
        $oracleDb->insert_batch('GMD_AR_INVOICE_LINES', $detail);

        $oracleDb->trans_complete();
        if ($oracleDb->trans_status() === FALSE) {
            $oracleDb->trans_rollback();
            $errorMsg = $oracleDb->error()['message'];
            $oracleDb->close();

            $isPosted = strpos($errorMsg, 'unique constraint') !== false ? 1 : 2;
            if ($isCancelInvoice) {
                $isPosted += 5;
            }

            $this->ceh->where('INV_NO', $invoiceData['INV_NO'])
                ->where('YARD_ID', $this->yard_id)
                ->update('INV_VAT', array('isPosted' => $isPosted));

            return array(
                'success' => $isPosted == 1,
                'message' => $errorMsg,
                'invoiceNo' => $invoiceData['INV_NO']
            );
        } else {
            $this->ceh->where('INV_NO', $invoiceData['INV_NO'])
                ->where('YARD_ID', $this->yard_id)
                ->update('INV_VAT', array('isPosted' => $isCancelInvoice ? 6 : 1));

            $oracleDb->trans_commit();
            $oracleDb->close();
            return array('success' => true, 'invoiceNo' => $invoiceData['INV_NO']);
        }
    }

    public function transferMultipleInvoice($invoiceDatas, $withDraft = false)
    {
        $allResult = array();
        $draftNos = array();
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
                        $tempDraft = $this->ceh->select('DRAFT_INV_NO')
                                                ->where_in("DRAFT_INV_NO", $drafts)->get('INV_DFT')->result_array();
                    }

                    if (count($tempDraft) > 0) {
                        $DRAFT_INV_NOs = array_column($tempDraft, 'DRAFT_INV_NO');
                        $draftDetail = $this->ceh->where_in('DRAFT_INV_NO', $DRAFT_INV_NOs)->get('INV_DFT_DTL')->result_array();
                    }
                }

                $isPosted = intval($element['isPosted']);
                if ($isCancelInvoice && in_array($isPosted, array(0, 2, 6))) { //hoa don huy + chua goi hd thuong(0) | goi hd thuong bi loi(2) | goi hd huy thanh cong (6)
                    $withDraft = false; // 1. khong goi phieu thu
                    $res_new = $this->transferInvoice($element, $draftDetail); // 2. goi hoa don thuong
                    array_push($allResult, $res_new);
                }

                $res = $this->transferInvoice($element, $draftDetail, $isCancelInvoice);
                array_push($allResult, $res);
                if ($res['success'] === TRUE && !$isCancelInvoice && $withDraft) {
                    $draftNos = array_merge($draftNos, array_column($draftDetail, 'DRAFT_INV_NO'));
                }
            }
        } catch (Exception $error) {
            array_push($allResult, array(
                'success' => false,
                'message' => $error->getMessage()
            ));

            return $allResult;
        }

        if ($withDraft && count($draftNos) > 0) {
            $resDraft = $this->transferMultiReceiptByDraftNos($draftNos);
            array_merge($allResult, $resDraft);
        }

        $op = array_filter($allResult, function ($var) {
            return ($var['success'] === false);
        });

        if (count($op) > 0) {
            return $op;
        } else {
            return array(
                'success' => true
            );
        }
    }

    public function transferMultiReceipt($list)
    {
        $arrResult = array();
        if (count($list) == 0) {
            array_push($arrResult, array(
                'success' => false,
                'message' => 'nothing to transfer to oracle'
            ));
            return $arrResult;
        }

        $configOracle = $this->config->item('ORACLE');
        if ($configOracle === NULL || count($configOracle) == 0) {
            array_push($arrResult, array(
                'success' => false,
                'message' => 'Không tìm thấy cấu hình ORACLE parameters'
            ));
            return $arrResult;
        }

        try {
            $oracleDb = $this->load->database('oracle', TRUE);
            $oracleDb->query("alter session set nls_date_format = ?", array($configOracle['orc_date_format']));
        } catch (Exception $error) {
            array_push($arrResult, array(
                'success' => false,
                'message' => $error->getMessage()
            ));
            return $arrResult;
        }

        for ($i = 0; $i < count($list); $i++) {
            $item = $list[$i];
            $out = $this->transferReceipt($item, $configOracle, $oracleDb, $i + 1);
            array_push($arrResult, $out);
        }

        $op = array_filter($arrResult, function ($var) {
            return ($var['success'] === false);
        });

        if (count($op) > 0) {
            return $op;
        } else {
            return array(
                'success' => true
            );
        }

        return $arrResult;
    }

    public function transferMultiReceiptByDraftNos($draftNos)
    {
        $arrResult = array();
        if (count($draftNos) == 0) {
            array_push($arrResult, array(
                'success' => false,
                'message' => 'nothing to transfer to oracle'
            ));
            return $arrResult;
        }

        //suw dung array_chunk neu draftnos qua nhieu
        $drafts = $this->ceh->where_in('DRAFT_INV_NO', $draftNos)->get('INV_DFT')->result_array();
        if (count($drafts) == 0) {
            array_push($arrResult, array(
                'success' => false,
                'message' => 'nothing to transfer to oracle'
            ));
            return $arrResult;
        }

        $arrResult = $this->transferMultiReceipt($drafts);
        return $arrResult;
    }

    public function transferReceipt($item, $configOracle = array(), $oracleDb = NULL, $batch_index = 1)
    {
        if (count($item) == 0) {
            return array(
                'success' => false,
                'message' => 'nothing to transfer to oracle'
            );
        }

        if (count($configOracle) == 0) {
            $configOracle = $this->config->item('ORACLE');
            if ($configOracle === NULL || count($configOracle) == 0) {
                return array(
                    'success' => false,
                    'message' => 'Không tìm thấy cấu hình ORACLE parameters'
                );
            }
        }

        $isCloseDb = false;
        if ($oracleDb === NULL) {
            try {
                $oracleDb = $this->load->database('oracle', TRUE);
                $oracleDb->query("alter session set nls_date_format = ?", array($configOracle['orc_date_format']));
                $isCloseDb = true;
            } catch (Exception $error) {
                log_message('error', json_encode($error));
                return array(
                    'success' => false,
                    'message' => $error->getMessage()
                );
            }
        }

        $serialPrefix = $configOracle['parameter']['SERIAL_PREFIX'];
        if ($serialPrefix === NULL) {
            return array(
                'success' => FALSE,
                'message' => 'Chưa cấu hình ký hiệu hoá đơn [SERIAL_PREFIX]'
            );
        }

        $inv = NULL;
        if ($item['INV_NO'] !== NULL) {
            $inv = $this->ceh->select('INV_DATE, TAMOUNT, CURRENCYID, RATE, INV_TYPE, INV_PREFIX, INV_NO_PRE')
                ->where('INV_NO', $item['INV_NO'])
                ->limit(1)
                ->get('INV_VAT')->row_array();
        }

        // ---- Yeu cau cua KT: chi chuyen nhung phieu thu da phat hanh hoa don
        if( $inv === NULL ){
            return array(
                'success' => FALSE,
                'message' => 'Phiếu thu ['. $item['DRAFT_INV_NO'] .'] không có hoá đơn phát hành'
            );
        }

        //lay so seq(gan vao field CHECK_NUMBER) doi voi truong hop nhieu draft phat hanh chung 1 hd
        $checkNumSeq = 1;
        $temp = $this->ceh->select('DRAFT_INV_NO, INV_NO')
            ->where('INV_NO', $item['INV_NO'])
            ->order_by('insert_time', 'ASC')->get('INV_DFT')->result_array();

        if( count($temp) > 1 ){ //nhiu draft chung 1 hoa don
            $temp1 = array_filter($temp, function($v) use ($item) {
                return $v['DRAFT_INV_NO'] === $item['DRAFT_INV_NO'];
            });
            if( count($temp1) > 0 ){
                $checkNumSeq = array_keys($temp1)[0] + 1;
            }
        }

        $invNo = $inv !== NULL ? ($inv['INV_PREFIX'] . $serialPrefix[$inv['INV_TYPE']] . $inv['INV_NO_PRE']) : ($item['INV_NO'] !== NULL ? $item['INV_NO'] : '');
        // $ngayGD = $this->orcDateTime($configOracle['date_format'], $item['DRAFT_INV_DATE']);
        $ngayGD = $this->orcDateTime($configOracle['date_format'], $inv['INV_DATE']); // ---- Yeu cau cua KT: lay = ngay hoa don
        //them thong tin receipt
        $receiptItem = array(
            'SOURCE_CODE' => $configOracle['parameter']['SOURCE_CODE'],
            'SERVER' => $configOracle['parameter']['SERVER'],
            'MAKH' => $item['PAYER'], //new
            'CREATION_DATE' => $ngayGD, //$this->orcDateTime($configOracle['date_format']), -> creation_date=DEPOSIT_DATE =  ANTICIPATED_CLEARING_DATE  =RECEIPT_DATE receipt_date làm chuẩn - A TUAN (IT) 10:57 29/07/2021 (Grp zalo)
            'ORG_ID' => $configOracle['parameter']['ORG_ID'],
            'DEPOSIT_DATE' => $ngayGD, //moment(item['NGAY_GD'])['format']('YY-MMM-DD'),
            'RECORD_TYPE' => $configOracle['parameter']['RECEIPT_RECORD_TYPE'],
            'LOCKBOX_NUMBER' => $configOracle['parameter']['LOCKBOX_NUMBER'],
            'BATCH_NAME' => $configOracle['parameter']['BANK_ID'] . date('Ymd') . '-' . $configOracle['parameter']['SERVER'],
            'BATCH_AMOUNT' => '',
            'BATCH_RECORD_COUNT' => '',
            'ITEM_NUMBER' => $batch_index,
            'REMITTANCE_AMOUNT' => $item['TAMOUNT'],
            'CURRENCY_CODE' => $item['CURRENCYID'],
            'EXCHANGE_RATE' => floatval($item['RATE']),
            'EXCHANGE_RATE_TYPE' => $configOracle['parameter']['CONVERSION_TYPE'],
            'RECEIPT_DATE' => $ngayGD,
            'RECEIPT_METHOD' => $configOracle['parameter']['RECEIPT_METHOD'],
            'CHECK_NUMBER' => $invNo !== '' ? ($invNo . "-$checkNumSeq") : '',
            'ANTICIPATED_CLEARING_DATE' => $ngayGD,
            'TRANSIT_ROUTING_NUMBER' => '',
            'ACCOUNT' => '',
            'CUSTOMER_NUMBER' => '',
            'COMMENTS' => $item['REMARK'],
            'BILL_TO_LOCATION' => '',
            'REMITTANCE_BANK_NAME' => $configOracle['parameter']['RECEIPT_METHOD'],
            'REMITTANCE_BANK_BRANCH_NAME' => $configOracle['parameter']['RECEIPT_METHOD'],
            'ATTRIBUTE1' => '',
            'ATTRIBUTE2' => '',
            'STATUS' => 0,
            'DIEMPH' => $configOracle['parameter']['DIEMPH'],
            'MADVI' => $configOracle['parameter']['MADVI']
        );

        if ($inv !== NULL) {
            $receiptItem['COMMENTS'] .= $invNo;
            $receiptItem["INVOICE1"] = $invNo;
            $receiptItem["MATCHING1_DATE"] = $this->orcDateTime($configOracle['date_format'], $inv['INV_DATE']);
            $receiptItem["AMOUNT_APPLIED1"] = count($temp) > 1 ? $item['TAMOUNT'] : $inv['TAMOUNT']; //19/08/2021 (gr zalo) : "vậy chốt là sẽ điều chỉnh số tiền cho trường AMOUNT_APPLIED1 = remittance_amount trong trường hợp 1 hđ nhiều pt , ok a nhỉ"
            $receiptItem["INVOICE_CURRENCY_CODE1"] = $inv['CURRENCYID'];
            $receiptItem["TRANS_TO_RECEIPT_RATE1"] = $inv['RATE'];
        }

        $oracleDb->trans_start();
        $oracleDb->trans_strict(TRUE);

        $oracleDb->insert('GMD_AR_PAYMENTS_INTERFACE_ALL', $receiptItem);

        $oracleDb->trans_complete();
        if ($oracleDb->trans_status() === FALSE) {
            $oracleDb->trans_rollback();
            $errorMsg = $oracleDb->error()['message'];
            $oracleDb->close();

            log_message('error', 'ORC RECEIPT ERR: ' . $errorMsg);
            $isPosted = strpos($errorMsg, 'unique constraint') !== false ? 1 : 2;
            $this->ceh->where('DRAFT_INV_NO', $item['DRAFT_INV_NO'])
                ->update('INV_DFT', array('PostToORCStatus' => $isPosted, 'PostToORCRemark' => $errorMsg));
            return array(
                'success' => $isPosted == 1,
                'message' => $isPosted == 1 ? '' : $errorMsg,
                'draftNo' => $receiptItem['DRAFT_INV_NO']
            );
        } else {
            $oracleDb->trans_commit();
            $oracleDb->close();
            $this->ceh->where('DRAFT_INV_NO', $item['DRAFT_INV_NO'])->update('INV_DFT', array('PostToORCStatus' => 1, 'PostToORCRemark' => ''));
            return array('success' => true);
        }
    }

    public function transferTariffCode($datas)
    {
        if (count($datas) == 0) {
            return array(
                'success' => false,
                'message' => 'nothing to transfer to oracle'
            );
        }

        $configOracle = $this->config->item('ORACLE');
        $detail = array();
        for ($i = 0; $i < count($datas); $i++) {
            $item = $datas[$i];
            $temp = array(
                'NGUON' => $configOracle['parameter']['SOURCE_CODE'],
                'SERVER' => $configOracle['parameter']['SERVER'],
                'CHARGECODE' => $item['TRF_CODE'],
                'DIENGIAI' => $item['TRF_STD_DESC'],
                'ACCCODE' => '',
                'LASTUPDATE' => ''
            );

            array_push($detail, $temp);
        }

        try {
            $oracleDb = $this->load->database('oracle', TRUE);
        } catch (Exception $error) {
            log_message('error', json_encode($error));
            return array(
                'success' => false,
                'message' => $error->getMessage()
            );
        }

        $oracleDb->trans_start();
        $oracleDb->trans_strict(TRUE);

        $oracleDb->insert_batch('GMD_MAP_CHARGECODE', $detail);

        $oracleDb->trans_complete();
        if ($oracleDb->trans_status() === FALSE) {
            $oracleDb->trans_rollback();
            $errorMsg = $oracleDb->error()['message'];
            $oracleDb->close();

            return array(
                'success' => false,
                'message' => $errorMsg
            );
        } else {
            $oracleDb->trans_commit();
            $oracleDb->close();
            return array('success' => true);
        }
    }

    public function transferNewCustomer($datas)
    {
        if (count($datas) == 0) {
            return array(
                'success' => false,
                'message' => 'nothing to transfer to oracle'
            );
        }

        $configOracle = $this->config->item('ORACLE');

        try {
            $oracleDb = $this->load->database('oracle', TRUE);
            $oracleDb->query("alter session set nls_date_format = ?", array($configOracle['orc_date_format']));
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
                'NGUON' => $configOracle['parameter']['SOURCE_CODE'],
                'SERVER' => $configOracle['parameter']['SERVER'],
                'MAKH' => $item['CusID']
            );

            //check exist
            $exist = $oracleDb->select('MAKH')->where($temp)->get('GMD_MAP_CUSTOMER')->row_array();

            $temp['TENKH'] = $item['CusName'];
            $temp['DIACHI'] = $item['Address'];
            $temp['MSTHUE'] = $item['VAT_CD'];
            $temp['LASTUPDATE'] = $this->orcDateTime($configOracle['date_format']); // moment().format('YY-MMM-DD'),

            if ($exist !== NULL) {
                array_push($listDataUpd, $temp);
            } else {
                $temp['ACCCODE'] = '';
                array_push($listDataIns, $temp);
            }
        }

        if (count($listDataIns) == 0 && count($listDataUpd) == 0) {
            return array('success' => false, 'message' => 'Nothing to transfer');
        }

        $oracleDb->trans_start();
        $oracleDb->trans_strict(TRUE);

        if (count($listDataIns) > 0) {
            $oracleDb->insert_batch('GMD_MAP_CUSTOMER', $listDataIns);
        }

        if (count($listDataUpd) > 0) {
            foreach ($listDataUpd as $upd) {
                $oracleDb->where(array(
                    'NGUON' => $upd['NGUON'],
                    'SERVER' => $upd['SERVER'],
                    'MAKH' => $upd['MAKH']
                ))->update('GMD_MAP_CUSTOMER', $upd);
            }
        }

        $oracleDb->trans_complete();
        if ($oracleDb->trans_status() === FALSE) {
            $oracleDb->trans_rollback();
            $errorMsg = $oracleDb->error()['message'];
            $oracleDb->close();

            log_message('error', 'ORC CUSTOMER ERR: ' . $errorMsg);
            $isPosted = strpos($errorMsg, 'unique constraint') !== false ? 1 : -1;
            return array(
                'success' => $isPosted == 1,
                'message' => $errorMsg
            );
        } else {
            $oracleDb->trans_commit();
            $oracleDb->close();
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
                                , a.CreatedBy, a.CancelDate, a.CancelBy, a.CancelRemark, a.INV_NO_PRE, a.INV_PREFIX, a.PAYER, a.isPosted
                                , d.AMOUNT, d.VAT, d.TAMOUNT, d.DRAFT_INV_NO, d.RATE, d.CURRENCYID, d.PostToORCStatus, m.VAT_CD, m.CusName, m.CusID')
            ->join('INV_DFT as d', 'd.INV_NO = a.INV_NO AND d.YARD_ID = a.YARD_ID', 'left')
            ->join('CUSTOMERS as m', 'm.CusID = a.PAYER AND m.YARD_ID = a.YARD_ID', 'left');

        if ($issueFromDate) {
            $stmt->where('a.INV_DATE >=', $issueFromDate);
        }

        if ($issueToDate) {
            $stmt->where('a.INV_DATE <=', $issueToDate);
        }

        if ($paymentType) {
            $stmt->where('a.INV_TYPE', $paymentType);
        }

        if ($status) {
            $operator = $status == 'C' ? '=' : '!=';
            $stmt->where('a.PAYMENT_STATUS ' . $operator, 'C');
        }

        if ($isPosted !== '') {
            $stmt->where('a.isPosted', $isPosted);
        }

        if ($cusID) {
            $stmt->where('a.PAYER', $cusID);
        }

        if ($searchVal) {
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
}
