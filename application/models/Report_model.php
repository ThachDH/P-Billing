<?php
defined('BASEPATH') or exit('');

class report_model extends CI_Model
{
    private $ceh;
    private $UC = 'UNICODE';
    private $yard_id = '';

    function __construct()
    {
        parent::__construct();
        $this->ceh = $this->load->database('mssql', TRUE);

        $this->yard_id = $this->config->item("YARD_ID");
    }

    public function searchShip($arrStatus = '', $year = '', $name = '')
    {
        $this->ceh->select('vs.ShipKey, vv.ShipName, vs.ShipID, vs.ShipYear, vs.ShipVoy, vs.ImVoy, vs.ExVoy, vs.ETB, vs.ETD, vs.BerthDate, vs.YARD_CLOSE');
        $this->ceh->join('VESSELS vv', 'vv.ShipID = vs.ShipID');
        $this->ceh->where('vv.VESSEL_TYPE', 'V');
        $this->ceh->where('vs.YARD_ID', $this->yard_id);

        if ($arrStatus != '') {
            $pre = (int)$arrStatus == 1 ? " !=" : "";
            $this->ceh->where('vs.ShipArrStatus' . $pre, 2);
        }

        if ($year != '') {
            $this->ceh->where('vs.ShipYear', $year);
        }

        if ($name != '') {
            $this->ceh->like('vv.ShipName', $name);
        }

        $this->ceh->order_by('vs.ETB', 'DESC');
        $stmt = $this->ceh->get('VESSEL_SCHEDULE vs');
        return $stmt->result_array();
    }

    public function getPayers($user = '')
    {
        $this->ceh->select('CusID, CusName, Address, VAT_CD, CusType, IsOpr, IsAgency, IsOwner, IsLogis, IsTrans, IsOther ');
        if ($user != '' && $user != 'admin')
            $this->ceh->where('NameDD', $user);

        $this->ceh->where('VAT_CD IS NOT NULL');

        $this->ceh->where('YARD_ID', $this->yard_id);

        $this->ceh->order_by('CusName', 'ASC');
        $stmt = $this->ceh->get('CUSTOMERS');
        return $stmt->result_array();
    }

    public function getPaymentMethod($type = 'CAS')
    {
        $this->ceh->select('rowguid, ACC_CD, ACC_NO, ACC_TYPE, ACC_NAME')->where('YARD_ID', $this->yard_id)->where('ACC_TYPE', $type);
        $stmt = $this->ceh->get('ACCOUNTS');
        return $stmt->result_array();
    }

    public function getUserId()
    {
        return $this->ceh->select("UserID")->where_in("UserGroupID", ["GroupAdmin"])->where("IsActive", "1")->get("SA_USERS")->result_array();
    }

    public function rptRevenue($args)
    {
        //fromdate = '', $todate = '', $jmode = '', $sys = ''
        $this->ceh->select('idd.TRF_CODE, tc.TRF_DESC, SZ, idd.INV_UNIT, SUM(ISNULL(QTY, 0)) SUMQTY, SUM(ISNULL(idd.AMOUNT, 0)) SUMAMOUNT, SUM(ISNULL(idd.VAT, 0)) SUMVAT
                            , SUM(ISNULL(idd.TAMOUNT, 0)) SUMTAMOUNT');
        $this->ceh->join('INV_DFT id', 'id.DRAFT_INV_NO = idd.DRAFT_INV_NO AND id.YARD_ID = idd.YARD_ID', 'left');
        $this->ceh->join('TRF_CODES tc', 'idd.TRF_CODE = tc.TRF_CODE AND tc.YARD_ID = idd.YARD_ID');
        // $this->ceh->join('DELIVERY_MODE dm', "dm.CJMode_CD = idd.CNTR_JOB_TYPE AND dm.YARD_ID = idd.YARD_ID", "left");
        
        $this->ceh->where('idd.AMOUNT !=', 0);
        $this->ceh->where('id.INV_NO IS NOT NULL');
        $this->ceh->where('id.PAYMENT_STATUS !=', 'C');
        // $this->ceh->join('INV_VAT iv', 'iv.INV_NO = id.INV_NO AND iv.YARD_ID = id.YARD_ID');
        // $this->ceh->where('id.INV_TYPE', 'CAS');
        // $this->ceh->where('SZ IS NOT NULL');
        // $this->ceh->where('CNTR_JOB_TYPE IS NOT NULL');

        $this->ceh->where('idd.YARD_ID', $this->yard_id);

        if ($args['fromdate'] != '') {
            $this->ceh->where('id.DRAFT_INV_DATE >=', $this->funcs->dbDateTime($args['fromdate']));
        }
        if ($args['todate'] != '') {
            $this->ceh->where('id.DRAFT_INV_DATE <=', date('Y-m-d 23:59:59', strtotime($this->funcs->dbDateTime($args['todate']))));
            // $this->ceh->where('id.DRAFT_INV_DATE <=', $this->funcs->dbDateTime($args['todate']));
        }
        if ($args['payment-type'] != '*') {
            $this->ceh->where('id.INV_TYPE', $args['payment-type']);
        }
        if ($args['currency'] != '*') {
            $this->ceh->where('id.CURRENCYID', $args['currency']);
        }

        // if ($args['jmode'] != '' && $args['jmode'] != '*') {
        //     switch ($args['jmode']) {
        //         case 'NH':
        //             $this->ceh->where('dm.isLoLo', '1');
        //             break;
        //         case 'DH':
        //             $this->ceh->where('dm.ischkCFS', '1');
        //             break;
        //         case 'RH':
        //             $this->ceh->where('dm.ischkCFS', '2');
        //             break;
        //         case 'CC':
        //             $this->ceh->where('dm.ischkCFS', '3');
        //             break;
        //         case 'DVB':
        //             $this->ceh->where('dm.IsYardSRV', '1');
        //             break;
        //     }
        // }

        // if ($args['sys'] != '') {
        //     $operator = $args['sys'] == "EP" ? "" : "!=";
        //     $this->ceh->where("LEFT(id.DRAFT_INV_NO,2) " . $operator, "TT");
        // }
        if ($args['sys'] != '') {
            if ($args['sys'] == 'VSL') {
                $this->ceh->where('id.TPLT_NM', 'VSL');
            } else {
                $this->ceh->where("ISNULL(id.TPLT_NM,'') <> 'VSL'");
            }
        }

        $this->ceh->group_by(array("idd.TRF_CODE", "tc.TRF_DESC", "SZ", "idd.INV_UNIT"));
        $draftDetail = $this->ceh->get_compiled_select("INV_DFT_DTL idd", TRUE);
        $invTPLT = $this->ceh->select('TPLT_NM, TPLT_DESC, TRF_CODE, YARD_ID, ROW_NUMBER() OVER (PARTITION BY TPLT_NM ORDER BY (SELECT NULL)) AS RowNum')
            ->get_compiled_select("INV_TPLT", TRUE);

        $stmt = $this->ceh->select('dtl.*')
            // $stmt = $this->ceh->select('TPLT_NM, TPLT_DESC, dtl.*')
            // ->join("($invTPLT) tplt", "tplt.TRF_CODE = dtl.TRF_CODE AND tplt.RowNum = '1' AND tplt.YARD_ID = '" . $this->yard_id . "'", 'left')
            // ->where('tplt.YARD_ID', $this->yard_id)
            ->get("($draftDetail) AS dtl")->result_array();

        // log_message('error', $this->ceh->last_query());

        if (count($stmt) == 0) return array();
        $newarray = array();
        $isConts = array("CONT", "CNT", "BOX");

        foreach ($stmt as $key => $val) {
            $kk = $val["TPLT_NM"] ?? $val["TRF_CODE"];
            $unit = in_array($val["INV_UNIT"], $isConts) && $val["SZ"] != '*' ? $val["INV_UNIT"] : '*';
            $newKey = $kk . '(^_^)' . $unit;
            $val['TRF_DESC'] = $val["TPLT_DESC"] ?? $val["TRF_DESC"];
            $newarray[$newKey][!isset($newarray[$newKey]) ? 0 : count($newarray[$newKey])] = $val;
        }

        if (count($newarray) == 0) return array();

        $results = array();
        foreach ($newarray as $k => $item) {
            $colsz = array();
            foreach ($item as $n) {
                if (in_array($n["INV_UNIT"], $isConts)) {
                    @$colsz[$n["SZ"]] += floatval($n['SUMQTY']);
                }
            }

            $sumAmout = array_sum(array_column($item, "SUMAMOUNT"));
            $sumVat = array_sum(array_column($item, "SUMVAT"));
            $sumTamout = array_sum(array_column($item, "SUMTAMOUNT"));

            $item[0]["20"] = isset($colsz["20"]) ? (int)$colsz["20"] : 0;
            $item[0]["40"] = isset($colsz["40"]) ? (int)$colsz["40"] : 0;
            $item[0]["45"] = isset($colsz['45']) ? (int)$colsz["45"] : 0;
            $item[0]['SUMAMOUNT'] = $sumAmout;
            $item[0]['SUMVAT'] = $sumVat;
            $item[0]['SUMTAMOUNT'] = $sumTamout;

            if (explode('(^_^)', $k)[1] == "*") {
                $item[0]['TRF_DESC'] .= ' (Non-cont)';
            }
            array_push($results, $item[0]);
        }

        return $results;
    }

    public function rptReleasedInv($fromdate = '', $todate = '', $jmode = '*', $paymentType = '*', $currency = '*', $sys = "", $adjustType = "*")
    {
        $this->ceh->select('id.DRAFT_INV_NO, DRAFT_INV_DATE, INV_PREFIX, iv.INV_NO, iv.INV_DATE, idd.AMOUNT, idd.VAT, idd.TAMOUNT, iv.AdjustType');
        $this->ceh->join('INV_DFT id', 'id.INV_NO = iv.INV_NO');
        $this->ceh->join('INV_DFT_DTL idd', 'idd.DRAFT_INV_NO = id.DRAFT_INV_NO ');
        $this->ceh->where('iv.INV_NO IS NOT NULL');

        $this->ceh->where('iv.YARD_ID', $this->yard_id);

        // if ($sys != '') {
        //     $pinPrefixes = $this->config->item('PIN_PREFIX');
        //     if ($sys == 'BL') {
        //         $this->ceh->where_in("LEFT(iv.PinCode, " . strlen($pinPrefixes['CAS']) . ")", $pinPrefixes);
        //     } else {
        //         $this->ceh->where_not_in("LEFT(iv.PinCode, " . strlen($pinPrefixes['CAS']) . ")", $pinPrefixes);
        //     }
        // }

        if ($sys != '') {
            if ($sys == 'VSL') {
                $this->ceh->where('id.TPLT_NM', 'VSL');
            } else {
                $this->ceh->where("ISNULL(id.TPLT_NM,'') <> 'VSL'");
            }
        }

        if ($fromdate != '') {
            $this->ceh->where('DRAFT_INV_DATE >=', $this->funcs->dbDateTime($fromdate));
        }

        if ($todate != '') {
            $this->ceh->where('DRAFT_INV_DATE <=', date('Y-m-d 23:59:59', strtotime($this->funcs->dbDateTime($todate))));
        }

        if ($adjustType != '' && $adjustType != '*') {
            $this->ceh->where('iv.AdjustType', $adjustType);
        }

        if ($jmode != '' && $jmode != '*') {
            $this->ceh->where('idd.CNTR_JOB_TYPE', $jmode);
        }

        if ($paymentType != '' && $paymentType != '*') {
            $this->ceh->where('id.INV_TYPE', $paymentType);
        }

        if ($currency != '' && $currency != '*') {
            $this->ceh->where('id.CURRENCYID', $currency);
        }

        $stmt = $this->ceh->order_by("iv.INV_DATE", "ASC")->get("INV_VAT iv");
        return $stmt->result_array();
    }

    public function rptRevenueByInvoices($args = [])
    {
        $this->ceh->select("iv.isDFT_to_INV,iv.INV_NO, id.DRAFT_INV_DATE INV_DATE, id.DRAFT_INV_NO, id.REF_NO
                            , iv.PinCode, (CASE WHEN iv.ShipKey IS NULL THEN NULL ELSE (vs.ShipName + ' / ' + vsc.ImVoy + ' / ' + vsc.ExVoy) END) AS ShipInfo
                            , idd.TRF_CODE, idd.TRF_DESC AS TRF_STD_DESC, iv.OPR
                            , idd.SZ, idd.QTY, idd.AMOUNT, idd.DIS_AMT, idd.VAT_RATE, idd.VAT, idd.TAMOUNT, iv.AdjustType,
                            , iv.ACC_CD, cus.CusName, cus.VAT_CD, iv.CreatedBy, idd.Remark REMARK");
        $this->ceh->join('INV_DFT id', 'id.INV_NO = iv.INV_NO AND id.YARD_ID = id.YARD_ID');
        $this->ceh->join('INV_DFT_DTL idd', 'idd.DRAFT_INV_NO = id.DRAFT_INV_NO AND idd.YARD_ID = id.YARD_ID', 'left');
        $this->ceh->join('CUSTOMERS cus', 'cus.CusID = iv.PAYER AND cus.YARD_ID = iv.YARD_ID', 'left');
        $this->ceh->join("VESSELS vs", "vs.ShipID = iv.ShipID AND vs.YARD_ID = iv.YARD_ID", 'left');
        $this->ceh->join("VESSEL_SCHEDULE vsc", "vsc.ShipKey = iv.ShipKey AND vsc.YARD_ID = iv.YARD_ID", 'left');

        $this->ceh->where('iv.INV_NO IS NOT NULL');
        $this->ceh->where('iv.INV_TYPE', 'CAS');
        $this->ceh->where('iv.PAYMENT_STATUS !=', 'C');

        if ($args["fromDate"] != '') {
            $this->ceh->where('id.DRAFT_INV_DATE >=', $this->funcs->dbDateTime($args["fromDate"]));
        }
        if ($args["toDate"] != '') {
            $this->ceh->where('id.DRAFT_INV_DATE <=', $this->funcs->dbDateTime($args["toDate"]));
        }
        if ($args["shipKey"] != '') {
            $this->ceh->where('iv.ShipKey', $args["shipKey"]);
        }
        if ($args["cusID"] != '') {
            $this->ceh->where('iv.PAYER', $args["cusID"]);
        }
        if ($args["createdBy"] != '') {
            $this->ceh->where('iv.CreatedBy', $args["createdBy"]);
        }
        if ($args["currencyId"] != '') {
            $this->ceh->where('iv.CURRENCYID', $args["currencyId"]);
        }

        if ($args["payment_type"] != '') {
            $this->ceh->where('iv.ACC_CD', $args["payment_type"]);
        }

        if ($args["adjust_type"] != '') {
            $this->ceh->where('iv.AdjustType', $args["adjust_type"]);
        }

        // if ($args['sys'] != '') {
        //     $pinPrefixes = $this->config->item('PIN_PREFIX');
        //     if ($args['sys'] == 'BL') {
        //         $this->ceh->where_in("LEFT(iv.PinCode, " . strlen($pinPrefixes['CAS']) . ")", $pinPrefixes);
        //     } else {
        //         $this->ceh->where_not_in("LEFT(iv.PinCode, " . strlen($pinPrefixes['CAS']) . ")", $pinPrefixes);
        //     }
        // }

        if ($args['sys'] != '') {
            if ($args['sys'] == 'VSL') {
                $this->ceh->where('id.TPLT_NM', 'VSL');
            } else {
                $this->ceh->where("ISNULL(id.TPLT_NM,'') <> 'VSL'");
            }
        }


        if ($args["isDFT_to_INV"] != '') {
            $opr = $args["isDFT_to_INV"] === 'true' ? '=' : '!=';
            $this->ceh->where("ISNULL(iv.isDFT_to_INV, '') $opr '2'");
        }

        $this->ceh->where('iv.YARD_ID', $this->yard_id);

        $stmt = $this->ceh->order_by("iv.INV_DATE", "DESC")->get("INV_VAT iv")->result_array();
        return $stmt;
    }

    public function rptCreditByInvoices($args = [])
    {
        $this->ceh->select('iv.INV_NO, iv.INV_DATE, id.DRAFT_INV_NO, id.REF_NO, iv.PinCode, idd.TRF_CODE, idd.TRF_DESC AS TRF_STD_DESC
                            , idd.SZ, idd.QTY, idd.AMOUNT, idd.DIS_AMT, idd.VAT_RATE, idd.VAT, idd.TAMOUNT, iv.AdjustType, iv.RATE
                            , iv.ACC_CD, cus.CusName, cus.VAT_CD, iv.CreatedBy, idd.Remark REMARK');
        $this->ceh->join('INV_DFT id', 'id.INV_NO = iv.INV_NO AND id.YARD_ID = id.YARD_ID');
        $this->ceh->join('INV_DFT_DTL idd', 'idd.DRAFT_INV_NO = id.DRAFT_INV_NO AND idd.YARD_ID = id.YARD_ID');
        $this->ceh->join('CUSTOMERS cus', 'cus.CusID = iv.PAYER AND cus.YARD_ID = iv.YARD_ID');
        $this->ceh->where('iv.INV_NO IS NOT NULL');
        $this->ceh->where('iv.INV_TYPE', 'CRE');
        $this->ceh->where('iv.PAYMENT_STATUS !=', 'C');

        if ($args["fromDate"] != '') {
            $this->ceh->where('iv.INV_DATE >=', $this->funcs->dbDateTime($args["fromDate"]));
        }
        if ($args["toDate"] != '') {
            $this->ceh->where('iv.INV_DATE <=', $this->funcs->dbDateTime($args["toDate"]));
        }
        if ($args["shipKey"] != '') {
            $this->ceh->where('iv.ShipKey', $args["shipKey"]);
        }
        if ($args["createdBy"] != '') {
            $this->ceh->where('iv.CreatedBy', $args["createdBy"]);
        }
        if ($args["currencyId"] != '') {
            $this->ceh->where('iv.CURRENCYID', $args["currencyId"]);
        }

        if ($args["payment_type"] != '') {
            $this->ceh->where('iv.ACC_CD', $args["payment_type"]);
        }

        if ($args["adjust_type"] != '') {
            $this->ceh->where('iv.AdjustType', $args["adjust_type"]);
        }

        if ($args['sys'] != '') {
            $pinPrefixes = $this->config->item('PIN_PREFIX');
            if ($args['sys'] == 'BL') {
                $this->ceh->where_in("LEFT(iv.PinCode, " . strlen($pinPrefixes['CAS']) . ")", $pinPrefixes);
            } else {
                $this->ceh->where_not_in("LEFT(iv.PinCode, " . strlen($pinPrefixes['CAS']) . ")", $pinPrefixes);
            }
        }

        $this->ceh->where('iv.YARD_ID', $this->yard_id);

        $stmt = $this->ceh->order_by("iv.INV_DATE", "DESC")->get("INV_VAT iv");
        return $stmt->result_array();
    }

    public function rptCancelInvoices($args = [])
    {
        $this->ceh->select('iv.INV_NO, iv.INV_DATE, iv.PAYER, cus.CusName, iv.AMOUNT, iv.VAT, iv.TAMOUNT, iv.CreatedBy
                            ,iv.CancelBy, iv.CancelDate, iv.CancelRemark, iv.PinCode');
        $this->ceh->join('CUSTOMERS cus', 'cus.CusID = iv.PAYER AND cus.YARD_ID = iv.YARD_ID');
        $this->ceh->where('iv.INV_NO IS NOT NULL');
        $this->ceh->where('iv.PAYMENT_STATUS', 'C');
        $this->ceh->where('iv.YARD_ID', $this->yard_id);

        if ($args["fromDate"] != '') {
            $this->ceh->where('iv.INV_DATE >=', $this->funcs->dbDateTime($args["fromDate"]));
        }
        if ($args["toDate"] != '') {
            $this->ceh->where('iv.INV_DATE <=', $this->funcs->dbDateTime($args["toDate"]));
        }
        if ($args["paymentType"] != '') {
            $this->ceh->where('iv.INV_TYPE', $args["paymentType"]);
        }
        // if ($args['sys'] != '') {
        //     $pinPrefixes = $this->config->item('PIN_PREFIX');
        //     if ($args['sys'] == 'BL') {
        //         $this->ceh->where_in("LEFT(iv.PinCode, " . strlen($pinPrefixes['CAS']) . ")", $pinPrefixes);
        //     } else {
        //         $this->ceh->where_not_in("LEFT(iv.PinCode, " . strlen($pinPrefixes['CAS']) . ")", $pinPrefixes);
        //     }
        // }

        $stmt = $this->ceh->order_by("iv.INV_DATE", "DESC")->get("INV_VAT iv");
        return $stmt->result_array();
    }

    public function loadDraftDetails($args = array())
    {
        $this->ceh->select("d.DRAFT_INV_NO, d.DRAFT_INV_DATE, d.IS_MANUAL_INV, d.REF_NO, cm.CusName, d.PAYER, d.INV_TYPE
                        , d.IS_MANUAL_INV, d.LOCAL_INV, d.PAYMENT_STATUS, d.ModifiedBy AS CancelBy, d.update_time AS CancelTime, d.REMARK AS CancelReason
                        , dtl.TRF_CODE, dtl.TRF_DESC, dtl.SZ, ct.Description AS CARGO_TYPE
                        , dtl.QTY, dtl.AMOUNT, dtl.VAT_RATE, dtl.VAT, dtl.TAMOUNT, dtl.CreatedBy, dtl.Remark")
            ->join("INV_DFT d", "d.DRAFT_INV_NO = dtl.DRAFT_INV_NO AND d.YARD_ID = dtl.YARD_ID", "LEFT")
            ->join("CARGO_TYPE ct", "dtl.CARGO_TYPE = ct.Code AND dtl.YARD_ID = ct.YARD_ID", "LEFT")
            ->join("CUSTOMERS cm", "cm.CusID = d.PAYER AND cm.YARD_ID = d.YARD_ID", "left")
            ->where("d.INV_NO IS NULL")
            ->where("dtl.YARD_ID", $this->yard_id)
            ->where("d.YARD_ID", $this->yard_id);

        if ($args["CurrencyID"] != '') {
            $this->ceh->where("CURRENCYID", $args["CurrencyID"]);
        }

        if ($args["PAYER"] != '') {
            $this->ceh->where("PAYER", $args["PAYER"]);
        }
        if (is_array($args["PaymentType"]) && count($args["PaymentType"]) > 0) {
            $this->ceh->where_in("INV_TYPE", $args["PaymentType"]);
        }

        if (is_array($args["isManualInv"]) && count($args["isManualInv"]) == 1) {
            $operator = $args['isManualInv'][0] == "1" ? "=" : "!=";
            $this->ceh->where("ISNULL(d.IS_MANUAL_INV, '0') $operator", "1");
        }

        //hinh thu thu (thu khach hang/ thu hang tau)
        if ($args["PaymentFor"] != '') {
            $this->ceh->where("LOCAL_INV", $args["PaymentFor"] === 'NULL' ? NULL : $args["PaymentFor"]);
        }

        //tim theo nguoi huy / nguoi tao + ngay huy/ngay tao
        $colDate = $args["ByCancel"] == "1" ? "d.update_time" : "d.DRAFT_INV_DATE";
        $colUser = $args["ByCancel"] == "1" ? "d.ModifiedBy" : "d.CreatedBy";
        $pStatus = $args["ByCancel"] == "1" ? ['C'] : (is_array($args["paymentStatus"]) && count($args["paymentStatus"]) > 0 ? $args["paymentStatus"] : []);

        if (!empty($args['FromDate'])) {
            $this->ceh->where("$colDate >=", $args["FromDate"]);
        }
        if (!empty($args['ToDate'])) {
            $this->ceh->where("$colDate <=", $args["ToDate"]);
        }
        if (!empty($args["UserId"])) {
            $this->ceh->where($colUser, $args["UserId"]);
        }
        if (count($pStatus) > 0) {
            $this->ceh->where_in("d.PAYMENT_STATUS", $pStatus);
        }

        $tmp = $this->ceh->order_by("d.DRAFT_INV_DATE", 'DESC')->get("INV_DFT_DTL dtl")->result_array();
        return $tmp;
    }

    public function getSysLogging($args)
    {
        $fromDate = isset($args['changedDateFrom']) ? $this->funcs->dbDateTime($args['changedDateFrom']) : '';
        $toDate = isset($args['changedDateTo']) ? $this->funcs->dbDateTime($args['changedDateTo']) : '';
        $temp = '';
        if (isset($args["orderType"]) && $args["orderType"] != '') { //nh, dr, dv
            $orderType = $args["orderType"];
            $this->ceh->select("CJMode_CD");
            if ($orderType == 'nh') {
                $this->ceh->where('isLoLo', 1);
            } elseif ($orderType == 'dr') {
                $this->ceh->where_in('ischkCFS', array(1, 2, 3));
            } else { //dv
                $this->ceh->where('isYardSRV', 1);
            }

            $temp = $this->ceh->get_compiled_select('DELIVERY_MODE', TRUE);
        }

        $this->ceh->select("ID, ChangedTime, TableName, OrderNo, CntrNo, FeautureName, ChangedType, ChangedBy
                                , ChangedIPAddress, CJMode_CD, BLNo, BookingNo, DRAFT_INV_NO, InvNo");

        if ($fromDate != '') {
            $this->ceh->where('ChangedTime >=', $fromDate);
        }
        if ($toDate != '') {
            $this->ceh->where('ChangedTime <=', $toDate);
        }
        if ($temp != '') {
            $this->ceh->where("CJMode_CD IN ($temp)");
        }

        if (isset($args['changedBy']) && $args['changedBy'] != '') {
            $this->ceh->where('ChangedBy', $args['changedBy']);
        }
        if (isset($args['changedType']) && $args['changedType'] != '') {
            $this->ceh->where('ChangedType', $args['changedType']);
        }
        if (isset($args['tableName']) && $args['tableName'] != '') {
            $this->ceh->where('TableName', $args['tableName']);
        }
        if (isset($args['searchValue']) && $args['searchValue'] != '') {
            $searchs = explode(',', $args['searchValue']);
            $search0 = str_replace('"', '', json_encode($searchs[0], JSON_UNESCAPED_UNICODE));
            $this->ceh->group_start();
            if (isset($args['findIn']) && $args['findIn'] != '') {
                $this->ceh->like($args['findIn'], $search0);
                array_shift($searchs);
                if (count($searchs) > 0) {
                    foreach ($searchs as $key => $value) {
                        $searchio = str_replace('"', '', json_encode($value, JSON_UNESCAPED_UNICODE));
                        $this->ceh->or_like($args['findIn'], $searchio);
                    }
                }
            } else {
                $this->ceh->like('NewContent', $search0);
                $this->ceh->or_like('OldContent', $search0);
                array_shift($searchs);
                if (count($searchs) > 0) {
                    foreach ($searchs as $key => $value) {
                        $searchio = str_replace('"', '', json_encode($value, JSON_UNESCAPED_UNICODE));
                        $this->ceh->or_like('NewContent', $searchio);
                        $this->ceh->or_like('OldContent', $searchio);
                    }
                }
            }
            $this->ceh->group_end();
        }

        $qry = $this->ceh->order_by('ChangedTime', 'DESC')->get('SYS_LOG_EVENT')->result_array();
        return $qry;
    }

    public function rptCreditRevenueInv($args = [])
    {
        $this->ceh->select("
        vat.INV_NO,
        vat.INV_DATE,
        vat.REF_TYPE,
        CASE vat.CURRENCYID WHEN 'VND' THEN vat.AMOUNT ELSE '0' END as AMVND,
        CASE vat.CURRENCYID WHEN 'VND' THEN vat.VAT ELSE '0' END as VATVND,
        CASE vat.CURRENCYID WHEN 'VND' THEN vat.TAMOUNT ELSE '0' END as TMVND,
        CASE vat.CURRENCYID WHEN 'USD' THEN vat.AMOUNT ELSE '0' END as AMUSD,
        CASE vat.CURRENCYID WHEN 'USD' THEN vat.VAT ELSE '0' END as VATUSD,
        CASE vat.CURRENCYID WHEN 'USD' THEN vat.TAMOUNT ELSE '0' END as TMUSD,
        CONCAT(v.ShipName, ' ', vs.ImVoy, '/', vs.ExVoy) as TauChuyen,
        vat.RATE,
        vat.CreatedBy", FALSE);
        $this->ceh->distinct();
        $this->ceh->join('INV_DFT dft', 'vat.INV_NO = dft.INV_NO', 'inner');
        $this->ceh->join('VESSEL_SCHEDULE vs', 'vat.ShipKey = vs.ShipKey', 'left');
        $this->ceh->join('VESSELS v', 'vs.ShipID = v.ShipID', 'left');
        $this->ceh->group_by(array(
            'vat.INV_NO',
            'vat.INV_DATE',
            'vat.REF_TYPE',
            'vat.CURRENCYID',
            'vat.AMOUNT',
            'vat.VAT',
            'vat.TAMOUNT',
            'v.ShipName',
            'vs.ImVoy',
            'vs.ExVoy',
            'vat.RATE',
            'vat.CreatedBy'
        ));
        $this->ceh->where('vat.PAYMENT_STATUS', 'Y');
        if ($args["fromDate"] != '') {
            $this->ceh->where('vat.INV_DATE >=', $this->funcs->dbDateTime($args["fromDate"]));
        }
        if ($args["toDate"] != '') {
            $this->ceh->where('vat.INV_DATE <=', $this->funcs->dbDateTime($args["toDate"]));
        }
        if ($args["shipKey"] != '') {
            $this->ceh->where('vat.ShipKey', $args["shipKey"]);
        }
        if ($args["publishBy"] != '') {
            $this->ceh->where('vat.REF_TYPE', $args["publishBy"]);
        }
        if ($args["currencyId"] != '') {
            $this->ceh->where('vat.CURRENCYID', $args["currencyId"]);
        }
        $stmt = $this->ceh->order_by('vat.INV_DATE', 'ASC')->get("INV_VAT vat");
        return $stmt->result_array();
    }
}
