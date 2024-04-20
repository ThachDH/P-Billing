<?php
defined('BASEPATH') or exit('');

class task_model extends CI_Model
{
    private $ceh;
    private $UC = 'UNICODE';
    private $yard_id = "";

    function __construct()
    {
        parent::__construct();
        $this->ceh = $this->load->database('mssql', TRUE);

        $this->yard_id = $this->config->item("YARD_ID");

        $this->load->model("common_model", "mdlcommon");
    }

    public function generate_PinCode($digits = 8)
    {
        $chk = array();

        $query = <<<EOT
        SELECT COUNT(rowguid) COUNTA FROM EIR
            WHERE PinCode = '?' AND YARD_ID = '?'
        union
        SELECT COUNT(rowguid) COUNTA FROM SRV_ODR
            WHERE PinCode = '?' AND YARD_ID = '?'
EOT;
        do {
            $nb = rand(1, pow(10, $digits) - 1);
            $nb = substr("0000000" . $nb, -8);

            $chk = $this->ceh->query($query, array($nb, $this->yard_id, $nb, $this->yard_id))->result_array();
        } while (array_sum(array_column($chk, 'COUNTA')) > 0);

        return $nb;
    }

    private function excludeServiceOrderExpDate()
    {
        $stmt = $this->ceh->select("CJMode_CD")
            ->where("YARD_ID", $this->yard_id)
            ->where_not_in("CJMode_CD", array('LBC', 'SDD'))
            ->where("IsNonContSRV = 1")
            ->get("DELIVERY_MODE");
        return $stmt->result_array();
    }

    public function getExchangeRate($currency)
    {
        $maxDateOfRate = $this->ceh->select("MAX(DATEOFRATE)")
            ->where("YARD_ID", $this->yard_id)
            ->get_compiled_select("EXCHANGE_RATE", TRUE);
        $this->ceh->select("RATE");
        $this->ceh->where("CURRENCYID", $currency);
        $this->ceh->where("YARD_ID", $this->yard_id);
        $this->ceh->where("DATEOFRATE = (" . $maxDateOfRate . ")");
        $this->ceh->where("YARD_ID", $this->yard_id);
        $stmt = $this->ceh->get("EXCHANGE_RATE");
        $stmt = $stmt->row_array();

        return count($stmt) > 0 ? floatval($stmt["RATE"]) : 1;
    }

    public function getUnitName($unitcode)
    {
        $stmt = $this->ceh->select('UNIT_NM')->where('UNIT_CODE', $unitcode)->where('YARD_ID', $this->yard_id)->limit(1)->get('UNIT_CODES');
        $row = $stmt->row();
        return isset($row) ? $row->UNIT_NM : $unitcode;
    }

    public function getPayers($user = '', $cusId = '')
    {
        $this->ceh->select('CusID, CusName, Address, VAT_CD, CusType, IsOpr, IsAgency, IsOwner, IsLogis, IsTrans, IsOther
        					, Email, EMAIL_DD, NameDD, PersonalID');
        if ($user != '' && $user != 'Admin') {
            $this->ceh->where('NameDD', $user);
        }

        if ($cusId != '') {
            $this->ceh->where('CusID', $cusId);
        }

        $this->ceh->where('VAT_CD IS NOT NULL');

        $this->ceh->where('YARD_ID', $this->yard_id);

        $this->ceh->order_by('CusName', 'ASC');
        $stmt = $this->ceh->get('CUSTOMERS');
        return $stmt->result_array();
    }

    public function getPayerByTaxCode($taxCode = '')
    {
        $this->ceh->select('CusID, CusName, Address, VAT_CD, CusType, IsOpr, IsAgency, IsOwner, IsLogis, IsTrans, IsOther
        					, Email, EMAIL_DD, NameDD, PersonalID');

        $this->ceh->where('VAT_CD', $taxCode);

        $this->ceh->where('YARD_ID', $this->yard_id);
        $this->ceh->limit(1);
        $this->ceh->order_by('CusName', 'ASC');
        $stmt = $this->ceh->get('CUSTOMERS');
        return $stmt->row_array();
    }

    public function getRelocation()
    {
        $this->ceh->select('GNRL_CODE, GNRL_NM');
        $this->ceh->where('GNRL_TYPE', 'REP');
        $this->ceh->where('YARD_ID', $this->yard_id);

        $this->ceh->order_by('GNRL_NM', 'ASC');
        $stmt = $this->ceh->get('DMG_CODES');
        return $stmt->result_array();
    }

    public function getServices($args)
    {
        $this->ceh->select('CJMode_CD, CJModeName');
        if (is_array($args) && count($args) > 0) {
            $this->ceh->group_start();
            $this->ceh->where("1 != 1");
            foreach ($args as $key => $value) {
                $this->ceh->or_where($key, $value);
            }
            $this->ceh->group_end();
        }

        $this->ceh->where('YARD_ID', $this->yard_id);

        $this->ceh->order_by('CJMode_CD', 'ASC');
        $stmt = $this->ceh->get('DELIVERY_MODE');
        return $stmt->result_array();
    }

    public function getAttachServices($orderType)
    {
        $this->ceh->select(" '' AS SSOderNo, sm.CjMode_CD, CJModeName, '' AS CntrNo_List, 0 AS PTI_Hour");
        $this->ceh->join('DELIVERY_MODE dm', 'sm.CJMode_CD = dm.CJMode_CD', 'left');
        $this->ceh->where('ORD_TYPE', $orderType);

        $this->ceh->where('sm.YARD_ID', $this->yard_id);

        $this->ceh->order_by('sm.CJMode_CD', 'ASC');
        $stmt = $this->ceh->get('SRVMORE sm');
        return $stmt->result_array();
    }

    public function getPaymentMethod($type = 'CAS')
    {
        $this->ceh->select('rowguid, ACC_CD, ACC_NO, ACC_TYPE, ACC_NAME')->where('YARD_ID', $this->yard_id)->where('ACC_TYPE', $type);
        $stmt = $this->ceh->get('ACCOUNTS');
        return $stmt->result_array();
    }

    public function getInv4PrintSlip($pinCode = '', $invNo = '')
    {
        $this->ceh->select("iv.INV_NO, iv.CURRENCYID, iv.PinCode, iv.TAMOUNT, iv.PinCode, cm.CusID, cm.CusName, cm.Address, cm.VAT_CD");
        $this->ceh->join("CUSTOMERS cm", 'cm.CusID = iv.PAYER AND cm.YARD_ID = cm.YARD_ID');

        if ($pinCode != '') {
            $this->ceh->where("iv.PinCode", $pinCode);
        }
        if ($invNo != '') {
            $this->ceh->where("iv.INV_NO", $invNo);
        }

        return $this->ceh->get("INV_VAT iv")->result_array();
    }

    public function searchShip($arrStatus = '', $year = '', $name = '')
    {
        $this->ceh->select('vs.ShipKey, vv.ShipName, vs.ShipID, vs.ShipYear, vs.ShipVoy, vs.ImVoy, vs.ExVoy
                            , vs.ETB, vs.ETD, vs.BerthDate, vs.YARD_CLOSE, vs.LaneID');
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

    public function getMaxOrderNo()
    {
        $prefix_t = date('y') . date('m') . date('d');
        $getMaxEir = $this->ceh->select('ISNULL(Max(EIRNo), 0) MaxEirNo')
            ->where('LEFT(EIRNo, 6) = ', $prefix_t)
            ->where('YARD_ID', $this->yard_id)
            ->get("EIR")->row_array();

        $getMaxOdr = $this->ceh->select('ISNULL(Max(SSOderNo), 0) MaxOdr')
            ->where('LEFT(SSOderNo, 6) = ', $prefix_t)
            ->where('YARD_ID', $this->yard_id)
            ->get("SRV_ODR")->row_array();
        $maxEir = intval($getMaxEir['MaxEirNo']);
        $maxOdr = intval($getMaxOdr['MaxOdr']);
        $max = $maxEir > $maxOdr ? $maxEir : $maxOdr;
        return $max > 0 ? ($max + 1) : $prefix_t . '0001';
    }

    public function generateOrderNo()
    {
        $prefix_t = date('y') . date('m') . date('d');
        $orderNo = "";
        $file = APPPATH . 'cache/draft_order.txt';
        $fp = fopen($file, "a+");
        do {
            $getLock = flock($fp, LOCK_EX | LOCK_NB);
            if ($getLock) {
                $fileSz = filesize($file);
                if ($fileSz == 0) {
                    $out = $this->getMaxOrderNo();
                } else {
                    $orderNo = fread($fp, $fileSz);
                    if (substr($orderNo, 0, 6) == $prefix_t) {
                        $out = intval($orderNo) + 1;
                    } else {
                        $out = $prefix_t . '0001';
                    }
                }

                ftruncate($fp, 0);
                fwrite($fp, $out);
                flock($fp, LOCK_UN);
            }
        } while (!$getLock);

        fclose($fp);

        return $out;
    }

    public function getRenewedOrder($args = array())
    {
        $orderName = $args["ordType"] == "NH" ? "e.EIRNo" : "e.SSOderNo";
        $tblName = $args["ordType"] == "NH" ? "EIR e" : "SRV_ODR e";
        $orderName = $args["ordType"] == "NH" ? "e.EIRNo" : "e.SSOderNo";
        $whereFinish = $args["ordType"] == "NH" ? "e.bXNVC = 0" : "e.FDate IS NULL";
        $masteRowguidVal = $tblName == "EIR e" ? "''" : "e.RowguidCntrDetails";

        $this->ceh->select("e.rowguid, $orderName AS OrderNo, e.CntrNo, e.ExpDate, e.ExpPluginDate
                            , e.PinCode, e.CusID, e.CJMode_CD, dm.CJModeName
                            , '" . $args['ordType'] . "' as OrderType
                            , " . $masteRowguidVal . " AS MASTER_ROWGUID");
        $this->ceh->join("DELIVERY_MODE dm", "dm.CJMode_CD = e.CJMode_CD AND dm.YARD_ID = e.YARD_ID", "left");
        // $this->ceh->where("e.ExpDate IS NOT NULL");
        // $this->ceh->where( $whereFinish );
        $this->ceh->where("e.YARD_ID", $this->yard_id);

        if ($args["fromDate"] != "") {
            $this->ceh->where("e.IssueDate >=", $this->funcs->dbDateTime($args['fromDate']));
        }

        if ($args["toDate"] != "") {
            $this->ceh->where("e.IssueDate <=", $this->funcs->dbDateTime($args['toDate'] . " 23:59:59"));
        }

        if ($args["ordNo"] != "") {
            $this->ceh->where($orderName, $args["ordNo"]);
        }

        if ($args["cntrNo"] != "") {
            $this->ceh->where("e.CntrNo", $args["cntrNo"]);
        }

        if ($args["pinCode"] != "") {
            $this->ceh->like("e.PinCode", $args["pinCode"], 'after');
        }

        $this->ceh->order_by($orderName);

        return $this->ceh->get($tblName)->result_array();
    }

    public function getEir4Update($ordNo = '', $cntrNo = '', $pinCode = '')
    {
        $this->ceh->select("e.rowguid, EIRNo AS OrderNo, BookingNo, BLNo, CntrNo, e.OprID, LocalSZPT, ISO_SZTP, IssueDate, ExpDate
                            , DELIVERYORDER, Retlocation, FreeDays, e.ShipKey, e.ShipID, vv.ShipName, ImVoy, ExVoy, e.CJMode_CD
                            , e.CJModeName, e.InvNo, e.DRAFT_INV_NO, e.CntrClass, SHIPPER_NAME, e.CusID, cm.CusName, PAYMENT_TYPE
                            , POD, FPOD, e.CARGO_TYPE, e.CmdID, SealNo, SealNo1, IsLocal, bXNVC, '' AS FDate, Transist, TERMINAL_CD
                            , e.NameDD, e.PersonalID, e.Mail, BARGE_CODE + '/' + BARGE_YEAR + '/' + BARGE_CALL_SEQ AS BargeInfo
                            , e.Note, e.CMDWeight, Temperature, Vent, Vent_Unit, CLASS, UNNO, e.InvNo, e.DRAFT_INV_NO
                            , (CASE WHEN e.CJMode_CD = 'CAPR' AND e.bXNVC != 1 AND e.PAYMENT_TYPE = 'C' AND e.CntrNo != '*' THEN 1 ELSE 0 END) AS BookingUnlinkable");
        $this->ceh->join("VESSELS vv", "vv.ShipID = e.ShipID AND vv.YARD_ID = e.YARD_ID", "left");
        $this->ceh->join("CUSTOMERS cm", "cm.CusID = e.CusID AND cm.YARD_ID = e.YARD_ID", "left");
        // $this->ceh->where( "e.CJMode_CD IN ('HBAI', 'TRAR')" );
        $this->ceh->where("e.YARD_ID", $this->yard_id);

        if ($ordNo != '') {
            $this->ceh->where("EIRNo", $ordNo);
        }

        if ($cntrNo != '') {
            $this->ceh->where("CntrNo", $cntrNo);
        }

        if ($pinCode != '') {
            $this->ceh->like("PinCode", $pinCode, "after");
        }

        $this->ceh->order_by("IssueDate", "DESC");
        $stmt = $this->ceh->get("EIR e")->result_array();

        $sizeTypes = $this->mdlcommon->getSizeType($stmt[0]['OprID']);

        $parent = [];
        if (count($stmt) > 0) {
            foreach ($stmt as $key => $item) {
                $k = sprintf(
                    "%s-%s-%s-%s-%s-%s-%s-%s-%s-%s-%s",
                    $item["OrderNo"] !== null ? $item["OrderNo"] : "",
                    $item["IssueDate"] !== null ? $item["IssueDate"] : "",
                    $item["ExpDate"] !== null ? $item["ExpDate"] : "",
                    $item["ShipID"] !== null ? $item["ShipID"] : "",
                    $item["ImVoy"] !== null ? $item["ImVoy"] : "",
                    $item["ExVoy"] !== null ? $item["ExVoy"] : "",
                    $item["BLNo"] !== null ? $item["BLNo"] : "",
                    $item["SHIPPER_NAME"] !== null ? $item["SHIPPER_NAME"] : "",
                    $item["CusID"] !== null ? $item["CusID"] : "",
                    $item["PAYMENT_TYPE"] !== null ? $item["PAYMENT_TYPE"] : "",
                    $item["CJModeName"] !== null ? $item["CJModeName"] : ""
                );

                if (!isset($parent[$k])) {
                    $parent[$k] = $item;
                }
            }

            return ["header" => $parent, "detail" => $stmt, "sizeTypes" => $sizeTypes];
        } else {
            return [];
        }
    }

    public function getOrder4Update($ordNo = '', $cntrNo = '', $pinCode = '')
    {
        $this->ceh->select("e.rowguid, SSOderNo as OrderNo, BookingNo, BLNo, CntrNo, e.OprID, LocalSZPT, ISO_SZTP, IssueDate, ExpDate
                            , e.ShipKey, e.ShipID, vv.ShipName, ImVoy, ExVoy, e.CJMode_CD, dm.CJModeName, e.InvNo, e.DRAFT_INV_NO, e.CntrClass
                            , SHIPPER_NAME, e.CusID, cm.CusName, PAYMENT_TYPE, POD, FPOD, e.CARGO_TYPE, e.CmdID, SealNo, SealNo1, IsLocal
                            , FDate, '' AS bXNVC, '' AS Transist, '' AS TERMINAL_CD
                            , e.NameDD, e.PersonalID, e.Mail, BARGE_CODE + '/' + BARGE_YEAR + '/' + BARGE_CALL_SEQ AS BargeInfo
                            , e.Note, e.CMDWeight, Temperature, Vent, Vent_Unit, e.InvNo, e.DRAFT_INV_NO
                            , (CASE WHEN dm.ischkCFS = 1 AND FDate IS NULL AND e.PAYMENT_TYPE = 'C' AND e.CntrNo != '*' THEN 1 ELSE 0 END) AS BookingUnlinkable");
        $this->ceh->join("VESSELS vv", "vv.ShipID = e.ShipID AND vv.YARD_ID = e.YARD_ID", "left");
        $this->ceh->join("CUSTOMERS cm", "cm.CusID = e.CusID AND cm.YARD_ID = e.YARD_ID", "left");
        $this->ceh->join("DELIVERY_MODE dm", "dm.CJMode_CD = e.CJMode_CD AND dm.YARD_ID = e.YARD_ID", "left");
        // $this->ceh->where( "e.CJMode_CD IN ('HBAI', 'TRAR')" );
        $this->ceh->where("e.YARD_ID", $this->yard_id);

        if ($ordNo != '') {
            $this->ceh->where("SSOderNo", $ordNo);
        }

        if ($cntrNo != '') {
            $this->ceh->where("CntrNo", $cntrNo);
        }

        if ($pinCode != '') {
            $this->ceh->like("PinCode", $pinCode, "after");
        }

        $this->ceh->order_by("IssueDate", "DESC");
        $stmt = $this->ceh->get("SRV_ODR e")->result_array();

        $sizeTypes = $this->mdlcommon->getSizeType($stmt[0]['OprID']);

        $parent = [];
        if (count($stmt) > 0) {
            foreach ($stmt as $key => $item) {
                // $k = $item["OrderNo"] !== null ? $item["OrderNo"] : "";
                $k = sprintf(
                    "%s-%s-%s-%s-%s-%s-%s-%s-%s-%s-%s",
                    $item["OrderNo"] !== null ? $item["OrderNo"] : "",
                    $item["IssueDate"] !== null ? $item["IssueDate"] : "",
                    $item["ExpDate"] !== null ? $item["ExpDate"] : "",
                    $item["ShipID"] !== null ? $item["ShipID"] : "",
                    $item["ImVoy"] !== null ? $item["ImVoy"] : "",
                    $item["ExVoy"] !== null ? $item["ExVoy"] : "",
                    $item["BLNo"] !== null ? $item["BLNo"] : "",
                    $item["SHIPPER_NAME"] !== null ? $item["SHIPPER_NAME"] : "",
                    $item["CusID"] !== null ? $item["CusID"] : "",
                    $item["PAYMENT_TYPE"] !== null ? $item["PAYMENT_TYPE"] : "",
                    $item["CJModeName"] !== null ? $item["CJModeName"] : ""
                );

                if (!isset($parent[$k])) {
                    $parent[$k] = $item;
                }
            }

            return ["header" => $parent, "detail" => $stmt, "sizeTypes" => $sizeTypes];
        } else {
            return [];
        }
    }

    public function unlink_booking($data, $ordType)
    {
        $this->ceh->trans_start();
        $this->ceh->trans_strict(FALSE);
        $colName = $ordType == 'NH' ? "EIRNo" : "SSOderNo";
        $tableName = $ordType == 'NH' ? "EIR" : "SRV_ODR";
        $rowguidOrders = array();
        foreach ($data as $key => $item) {
            array_push($rowguidOrders, $item['rowguid']);

            unset($item['rowguid']);
            //update cntr_details
            $w4Cntr = $item;
            $w4Cntr['CntrClass'] = '2';
            $this->ceh->where($w4Cntr)->update('CNTR_DETAILS', array($colName => NULL));

            //update booking
            $w4Booking = $item;
            unset($w4Booking['CntrNo']);
            $this->ceh->set('StackingAmount', 'StackingAmount-1', FALSE);
            $this->ceh->where($w4Booking);
            $this->ceh->update('EMP_BOOK');
        }

        //xoa cont trong lenh
        if (count($rowguidOrders) > 0) {
            $this->ceh->where_in('rowguid', $rowguidOrders)->delete($tableName);
        }

        $this->ceh->trans_complete();

        if ($this->ceh->trans_status() === FALSE) {
            $outMsg = $this->ceh->_error_message();
            $this->ceh->trans_rollback();
            return FALSE;
        } else {
            $this->ceh->trans_commit();
            return TRUE;
        }
    }

    public function getStorageFreeDay($oprID, $cntrClass = 1, $fe = 'F', $cargoType = '')
    {
        $this->ceh->select("IFREE_DAYS");
        $this->ceh->where("CntrClass", $cntrClass);
        $this->ceh->where("FE", $fe);
        $this->ceh->where("EXPIRE_DATE >=", $this->funcs->dbDateTime(date("Y-m-d H:i:s")));
        $this->ceh->where("PTNR_CODE", $oprID);
        $this->ceh->where("YARD_ID", $this->yard_id);
        if ($cargoType != '') {
            $this->ceh->group_start();
            $this->ceh->where("CARGO_TYPE", $cargoType);
            $this->ceh->or_where("CARGO_TYPE", '*');
            $this->ceh->group_end();
        }

        $stmt = $this->ceh->limit(1)->get("FREE_DAYS")->row_array();

        return (is_array($stmt) && count($stmt) > 0) ? intval($stmt["IFREE_DAYS"]) : 0;
    }

    public function getPluginDate($cntrNo, $shipKey, $cntrClass = '1')
    {
        $this->ceh->select("DatePlugIn");
        $this->ceh->where("CntrClass", $cntrClass);
        $this->ceh->where("CntrNo", $cntrNo);
        $this->ceh->where("ShipKey", $shipKey);
        $this->ceh->where("DatePlugIn IS NOT NULL");
        $this->ceh->where("YARD_ID", $this->yard_id);

        $stmt = $this->ceh->limit(1)->get("RF_ONOFF")->row_array();

        return (is_array($stmt) && count($stmt) > 0) ? $this->funcs->dbDateTime($stmt["DatePlugIn"]) : '';
    }

    public function getInvDFT4ViewPDF($draftNo)
    {
        $this->ceh->select('l.DRAFT_INV_NO, DRAFT_INV_DATE, l.TRF_DESC, l.INV_UNIT, l.QTY, l.standard_rate, l.UNIT_RATE, l.VAT
                            , l.TAMOUNT, l.Remark, l.SZ, l.FE
							, c.VAT_CD TAX_CODE, CusName PAYER_NAME, c.Address PAYER_ADDRESS, d.CURRENCYID, us.UserName');
        $this->ceh->join("INV_DFT_DTL l", "d.DRAFT_INV_NO = l.DRAFT_INV_NO AND d.YARD_ID = l.YARD_ID", "left");
        $this->ceh->join("CUSTOMERS c", "c.CusID = d.PAYER AND c.YARD_ID = d.YARD_ID", "left");
        $this->ceh->join("SA_USERS us", "us.UserID = d.CreatedBy AND us.YARD_ID = d.YARD_ID", 'left');

        if (strpos($draftNo, ' ') !== false) {
            $this->ceh->where_in('d.DRAFT_INV_NO', explode(" ", $draftNo));
        } else {
            $this->ceh->where('d.DRAFT_INV_NO', $draftNo);
        }

        $this->ceh->where('d.YARD_ID', $this->yard_id);

        $stmt = $this->ceh->get('INV_DFT d');
        return $stmt->result_array();
    }

    public function checkInvNo($inv_prefix, $invNo)
    {
        $this->ceh->select('INV_NO_PRE');

        $this->ceh->where('INV_PREFIX', $inv_prefix);
        $this->ceh->where('INV_NO_PRE', $invNo);
        $this->ceh->where('YARD_ID', $this->yard_id);
        $this->ceh->limit(1);
        $this->ceh->order_by("INV_NO_PRE", "DESC");
        $stmt = $this->ceh->get('INV_VAT')->row_array();

        return is_array($stmt) && count($stmt) > 0;
    }

    public function getInvPrefix()
    {
        $this->ceh->select('INV_PREFIX, FROM_INV_NO, TO_INV_NO, PCODE');

        $this->ceh->where('PTYPE', 'VAT');
        $this->ceh->where('YARD_ID', $this->yard_id);

        $stmt = $this->ceh->get('INV_PREFIX');
        return $stmt->result_array();
    }

    public function loadEirInquiry2($args = array(), $start = 0, $length = 0, $draw = 1)
    {

        $data = array();

        $resultInquiry = $this->loadEirInquiry($args, $start, $length);
        $rowCount = $this->countEirInquiry($args);

        foreach ($resultInquiry as $item) {
            $start++;

            array_push(
                $data,
                array(
                    $start, $item["bXNVC"], $item["CJModeName"], $item["OrderNo"], $item["PinCode"], $item["CntrNo"], $item["ISO_SZTP"], $item["DMethod_CD"], $item["ShipName"] !== null ? ($item["ShipName"] . " / " . $item["ImVoy"] . " / " . $item["ExVoy"]) : "", $item["BLNo"], $item["BookingNo"], $item["CusID"], $item["SHIPPER_NAME"], $item["Note"]
                )
            );
        }

        return array(
            "draw" => $draw,
            "recordsTotal" => $rowCount,
            "recordsFiltered" => $rowCount,
            "data" => $data,
        );
    }

    public function loadEirInquiry($args = array(), $start = 0, $length = 0)
    {
        $wShip = (isset($args["ShipKey"]) && $args["ShipKey"] != "") ? "ShipKey =" . $args["ShipKey"] : "";

        $wLike_EIR = (isset($args["searchValue"]) && $args["searchValue"] != "")
            ? sprintf("( EIRNo = '%s' OR e.PinCode like '%s' OR CntrNo = '%s' )", $args["searchValue"], $args["searchValue"] . "%", $args["searchValue"])
            : "";

        $wLike_SRV = $wLike_EIR != "" ? str_replace("EIRNo", "SSOderNo", $wLike_EIR) : "";
        $wLike_SRV = $wLike_SRV != "" ? str_replace("e.PinCode", "srv.PinCode", $wLike_SRV) : "";

        $checkCJMode_CD = array();
        $appendCJModeWheres = '';

        $finalWhere = "";
        if (isset($args["CJMode_CDs"]) && count($args["CJMode_CDs"]) > 0) {
            $checkCJMode_CD = $args["CJMode_CDs"];

            if (in_array("ALL", $checkCJMode_CD)) {
                goto all_cjmode;
            }

            if (in_array("DH", $checkCJMode_CD)) {
                $appendCJModeWheres .= $appendCJModeWheres == "" ? " ischkCFS = 1 " : " OR ischkCFS = 1 ";
                unset($checkCJMode_CD[array_search("DH", $checkCJMode_CD)]);
            }
            if (in_array("RH", $checkCJMode_CD)) {
                $appendCJModeWheres .= $appendCJModeWheres == "" ? " ischkCFS = 2 " : " OR ischkCFS = 2 ";
                unset($checkCJMode_CD[array_search("RH", $checkCJMode_CD)]);
            }
            if (in_array("OTHER", $checkCJMode_CD)) {
                $appendCJModeWheres .= $appendCJModeWheres == "" ? " isYardSRV = 1 " : " OR isYardSRV = 1 ";
                unset($checkCJMode_CD[array_search("OTHER", $checkCJMode_CD)]);
            }
        }

        if (count($checkCJMode_CD) > 0) {
            $finalWhere = sprintf(" CJMode_CD IN ('%s') ", implode("','", $checkCJMode_CD));
        }

        if ($appendCJModeWheres != "") {
            $temp = $this->ceh->select("CJMode_CD")->get_compiled_select("DELIVERY_MODE", TRUE);
            $finalWhere = sprintf("(%s %s CJMode_CD IN (%s))", $finalWhere, empty($finalWhere) ? "" : "OR", $temp . " WHERE " . $appendCJModeWheres);
        }

        all_cjmode:

        if (count($args['paymentType']) > 0) {
            $finalWhere = sprintf("%s %s PAYMENT_TYPE IN ('%s')", $finalWhere, empty($finalWhere) ? "" : "and", implode("','", $args['paymentType']));
        }

        $getCJModeName = $this->ceh->select("CJModeName")->where("dm.CJMode_CD = srv.CJMode_CD")
            ->limit(1)
            ->get_compiled_select("DELIVERY_MODE dm", TRUE);

        $unionEIR = $this->ceh->select("CJMode_CD, CJModeName, EIRNo AS OrderNo, e.PinCode, CntrNo, ISO_SZTP
                                        , e.CusID, cus.CusName, SHIPPER_NAME, e.Note, e.IssueDate, e.ExpDate, CAST(NULL as datetime) AS ExpPluginDate, e.CreatedBy
                                        , bXNVC, BLNo, BookingNo, e.DMethod_CD, ShipName, ImVoy, ExVoy
                                        , e.OprID, e.Status, cm.CLASS_Name, e.PersonalID + ISNULL(' - ' + e.NameDD, '') as PersonalInfo
                                        , e.CARGO_TYPE, e.CMDWeight, e.SealNo, e.IsLocal, e.PAYMENT_TYPE
                                        , e.Transist, e.TERMINAL_CD, e.RetLocation
                                        , inv.INV_NO, inv.INV_DATE, e.DRAFT_INV_NO, idd.TRF_CODE, idd.TRF_DESC, idd.TAMOUNT")
            ->join("VESSELS vv", "vv.ShipID = e.ShipID AND vv.YARD_ID = e.YARD_ID", "left")
            ->join("CLASS_MODE cm", "cm.CLASS_Code = e.CntrClass AND cm.YARD_ID = e.YARD_ID", "left")
            ->join("INV_VAT inv", "inv.INV_NO = e.InvNo AND inv.REF_NO = e.EIRNo AND inv.YARD_ID = e.YARD_ID", "left")
            ->join("CUSTOMERS cus", "cus.CusID = e.CusID", "left")
            ->join("INV_DFT id", "id.DRAFT_INV_NO = e.DRAFT_INV_NO AND id.REF_NO = e.EIRNo AND id.YARD_ID = e.YARD_ID", "left")
            ->join("INV_DFT_DTL idd", "idd.DRAFT_INV_NO = e.DRAFT_INV_NO 
                        AND (idd.CNTR_JOB_TYPE = e.CJMode_CD OR idd.CNTR_JOB_TYPE = '*')
                        AND (idd.CARGO_TYPE = e.CARGO_TYPE OR idd.CARGO_TYPE = '*')
                        AND (idd.FE = e.Status OR idd.FE = '*')
                        AND idd.YARD_ID = e.YARD_ID 
                        AND (idd.SZ = (CASE LEFT(e.ISO_SZTP, 1) WHEN '2' THEN '20' WHEN '4' THEN '40' WHEN 'L' THEN '45' WHEN 'M' THEN '45' WHEN '9' THEN '45' ELSE '' END) OR idd.SZ = '*')", "left")
            ->where('e.YARD_ID', $this->yard_id)
            // ->where("idd.SZ = (CASE LEFT(e.ISO_SZTP, 1) WHEN '2' THEN 20 WHEN '4' THEN 40 WHEN 'L' THEN 45 WHEN 'M' THEN 45 WHEN '9' THEN 45 ELSE 0 END)")
            ->where("IssueDate >=", $this->funcs->dbdatetime($args["IssueDateFrom"]))
            ->where("IssueDate <=", $this->funcs->dbdatetime($args["IssueDateTo"] . " 23:59:59"))

            // ->where(($args["sys"] == "EP") ? (" LEFT(e.PinCode,1) = 'A' ") : (" LEFT(e.PinCode,1) != 'A' "))
            ->where($wShip != "" ? $wShip : "1=1")
            ->where($wLike_EIR != "" ? $wLike_EIR : "1=1")
            ->where($finalWhere != "" ? $finalWhere : "1=1");
        if ($args['oprId']) {
            $this->ceh->where('e.oprId', $args['oprId']);
        };

        if ($args['sys'] != '') {
            $pinPrefixes = $this->config->item('PIN_PREFIX');
            if ($args['sys'] == 'BL') {
                $this->ceh->where_in("LEFT(e.PinCode, " . strlen($pinPrefixes['CAS']) . ")", $pinPrefixes);
            } else {
                $this->ceh->where_not_in("LEFT(e.PinCode, " . strlen($pinPrefixes['CAS']) . ")", $pinPrefixes);
            }
        }

        $unionEIR = $this->ceh->get_compiled_select('EIR e', TRUE);

        $unionSRV = $this->ceh->select("CJMode_CD, (" . $getCJModeName . ") AS CJModeName, SSOderNo AS OrderNo, srv.PinCode, CntrNo, ISO_SZTP
                                        , srv.CusID, cus.CusName, SHIPPER_NAME, srv.Note, srv.IssueDate, srv.ExpDate, srv.ExpPluginDate, srv.CreatedBy
                                        , (CASE WHEN Fdate IS NULL THEN 0 ELSE 1 END) AS bXNVC, BLNo, BookingNo, srv.DMethod_CD
                                        , ShipName, ImVoy, ExVoy, srv.OprID, srv.Status, cm.CLASS_Name, srv.PersonalID + ISNULL(' - ' + srv.NameDD, '') as PersonalInfo
                                        , srv.CARGO_TYPE, srv.CMDWeight, srv.SealNo, srv.isLocal AS IsLocal, srv.PAYMENT_TYPE
                                        , '' AS Transist, '' AS TERMINAL_CD, '' AS RetLocation
                                        , inv.INV_NO, inv.INV_DATE, srv.DRAFT_INV_NO, idd.TRF_CODE, idd.TRF_DESC, idd.TAMOUNT")
            ->join("VESSELS vv", "vv.ShipID = srv.ShipID AND vv.YARD_ID = srv.YARD_ID", "left")
            ->join("CLASS_MODE cm", "cm.CLASS_Code = srv.CntrClass AND cm.YARD_ID = srv.YARD_ID", "left")
            ->join("INV_VAT inv", "inv.INV_NO = srv.InvNo AND inv.REF_NO = srv.SSOderNo AND inv.YARD_ID = srv.YARD_ID", "left")
            ->join("CUSTOMERS cus", "cus.CusID = srv.CusID", "left")
            ->join("INV_DFT id", "id.DRAFT_INV_NO = srv.DRAFT_INV_NO AND id.REF_NO = srv.SSOderNo AND id.YARD_ID = srv.YARD_ID", "left")
            ->join("INV_DFT_DTL idd", "idd.DRAFT_INV_NO = srv.DRAFT_INV_NO 
                        AND (idd.CNTR_JOB_TYPE = srv.CJMode_CD OR idd.CNTR_JOB_TYPE = '*')
                        AND (idd.CARGO_TYPE = srv.CARGO_TYPE OR idd.CARGO_TYPE = '*')
                        AND (idd.FE = srv.Status OR idd.FE = '*')
                        AND idd.YARD_ID = srv.YARD_ID 
                        AND (idd.SZ = (CASE LEFT(srv.ISO_SZTP, 1) WHEN '2' THEN '20' WHEN '4' THEN '40' WHEN 'L' THEN '45' WHEN 'M' THEN '45' WHEN '9' THEN '45' ELSE '' END) OR idd.SZ = '*')", "left")
            // ->where(($args["sys"] == "EP") ? (" LEFT(srv.PinCode,1) = 'A' ") : (" LEFT(srv.PinCode,1) != 'A' "))
            ->where('srv.YARD_ID', $this->yard_id)
            // ->where("idd.SZ = (CASE LEFT(srv.ISO_SZTP, 1) WHEN '2' THEN 20 WHEN '4' THEN 40 WHEN 'L' THEN 45 WHEN 'M' THEN 45 WHEN '9' THEN 45 ELSE 0 END)")
            ->where("IssueDate >=", $this->funcs->dbdatetime($args["IssueDateFrom"]))
            ->where("IssueDate <=", $this->funcs->dbdatetime($args["IssueDateTo"] . " 23:59:59"))

            ->where($wShip != "" ? $wShip : "1=1")
            ->where($wLike_SRV != "" ? $wLike_SRV : "1=1")
            ->where($finalWhere != "" ? $finalWhere : "1=1");
        if ($args['oprId']) {
            $this->ceh->where('srv.oprId', $args['oprId']);
        };
        if ($args['sys'] != '') {
            $pinPrefixes = $this->config->item('PIN_PREFIX');
            if ($args['sys'] == 'BL') {
                $this->ceh->where_in("LEFT(srv.PinCode, " . strlen($pinPrefixes['CAS']) . ")", $pinPrefixes);
            } else {
                $this->ceh->where_not_in("LEFT(srv.PinCode, " . strlen($pinPrefixes['CAS']) . ")", $pinPrefixes);
            }
        }

        $unionSRV = $this->ceh->get_compiled_select("SRV_ODR srv", TRUE);

        if (isset($args["isCountResult"]) && $args["isCountResult"] === TRUE) {
            $count = $this->ceh->from("(" . $unionEIR . " UNION ALL " . $unionSRV . ") A")
                ->count_all_results();
            return $count;
        }

        if ($start >= 0 && $length > 0) {
            $stmt = $this->ceh->limit($length, $start)
                ->order_by("IssueDate", "DESC")
                ->order_by("PinCode", "ASC")
                ->get("(" . $unionEIR . " UNION ALL " . $unionSRV . ") A")
                ->result_array();
        } else {
            $stmt = $this->ceh->order_by("IssueDate", "DESC")
                ->order_by("PinCode", "ASC")
                ->get("(" . $unionEIR . " UNION ALL " . $unionSRV . ") A")->result_array();
        }

        return $stmt;
    }

    public function countEirInquiry($args = array())
    {
        $args["isCountResult"] = TRUE;
        return $this->loadEirInquiry($args);
    }

    public function countOrder($args = array())
    {
        $wShip = (isset($args["ShipKey"]) && $args["ShipKey"] != "") ? "ShipKey =" . $args["ShipKey"] : "";

        $wLike_EIR = (isset($args["searchValue"]) && $args["searchValue"] != "")
            ? sprintf("( EIRNo = '%s' OR e.PinCode like '%s' OR CntrNo = '%s' )", $args["searchValue"], $args["searchValue"] . "%", $args["searchValue"])
            : "";

        $wLike_SRV = $wLike_EIR != "" ? str_replace("EIRNo", "SSOderNo", $wLike_EIR) : "";
        $wLike_SRV = $wLike_SRV != "" ? str_replace("e.PinCode", "srv.PinCode", $wLike_SRV) : "";

        $checkCJMode_CD = array();
        $appendCJModeWheres = '';

        $finalWhere = "";
        if (isset($args["CJMode_CDs"]) && count($args["CJMode_CDs"]) > 0) {
            $checkCJMode_CD = $args["CJMode_CDs"];

            if (in_array("ALL", $checkCJMode_CD)) {
                goto all_cjmode;
            }

            if (in_array("DH", $checkCJMode_CD)) {
                $appendCJModeWheres .= $appendCJModeWheres == "" ? " ischkCFS = 1 " : " OR ischkCFS = 1 ";
                unset($checkCJMode_CD[array_search("DH", $checkCJMode_CD)]);
            }
            if (in_array("RH", $checkCJMode_CD)) {
                $appendCJModeWheres .= $appendCJModeWheres == "" ? " ischkCFS = 2 " : " OR ischkCFS = 2 ";
                unset($checkCJMode_CD[array_search("RH", $checkCJMode_CD)]);
            }
            if (in_array("OTHER", $checkCJMode_CD)) {
                $appendCJModeWheres .= $appendCJModeWheres == "" ? " isYardSRV = 1 " : " OR isYardSRV = 1 ";
                unset($checkCJMode_CD[array_search("OTHER", $checkCJMode_CD)]);
            }
        }

        if (count($checkCJMode_CD) > 0) {
            $finalWhere = sprintf(" CJMode_CD IN ('%s') ", implode("','", $checkCJMode_CD));
        }

        if ($appendCJModeWheres != "") {
            $temp = $this->ceh->select("CJMode_CD")->get_compiled_select("DELIVERY_MODE", TRUE);
            $finalWhere = sprintf("(%s %s CJMode_CD IN (%s))", $finalWhere, empty($finalWhere) ? "" : "OR", $temp . " WHERE " . $appendCJModeWheres);
        }

        all_cjmode:
        if (count($args['paymentType']) > 0) {
            $finalWhere = sprintf("%s %s PAYMENT_TYPE IN ('%s')", $finalWhere, empty($finalWhere) ? "" : "and", implode("','", $args['paymentType']));
        }

        $getCJModeName = $this->ceh->select("CJModeName")->where("dm.CJMode_CD = srv.CJMode_CD")
            ->limit(1)
            ->get_compiled_select("DELIVERY_MODE dm", TRUE);

        $unionEIR = $this->ceh->select('CJMode_CD, CJModeName, ISO_SZTP, COUNT(ISO_SZTP) COUNT_ISO')
            ->where('YARD_ID', $this->yard_id)
            ->where("IssueDate >=", $this->funcs->dbdatetime($args["IssueDateFrom"]))
            ->where("IssueDate <=", $this->funcs->dbdatetime($args["IssueDateTo"] . " 23:59:59"))
            // ->where(($args["sys"] == "EP") ? (" LEFT(e.PinCode,1) = 'A' ") : (" LEFT(e.PinCode,1) != 'A' "))
            ->where($wShip != "" ? $wShip : "1=1")
            ->where($wLike_EIR != "" ? $wLike_EIR : "1=1")
            ->where($finalWhere != "" ? $finalWhere : "1=1");
        if ($args['oprId']) {
            $this->ceh->where('e.oprId', $args['oprId']);
        };
        if ($args['sys'] != '') {
            $pinPrefixes = $this->config->item('PIN_PREFIX');
            if ($args['sys'] == 'BL') {
                $this->ceh->where_in("LEFT(e.PinCode, " . strlen($pinPrefixes['CAS']) . ")", $pinPrefixes);
            } else {
                $this->ceh->where_not_in("LEFT(e.PinCode, " . strlen($pinPrefixes['CAS']) . ")", $pinPrefixes);
            }
        }
        $unionEIR = $this->ceh->group_by(array("CJMode_CD", "ISO_SZTP", "CJModeName"))->get_compiled_select('EIR e', TRUE);

        $unionSRV_ODR = $this->ceh->select("CJMode_CD, (" . $getCJModeName . ") AS CJModeName, ISO_SZTP, COUNT(ISO_SZTP) COUNT_ISO")
            ->where('YARD_ID', $this->yard_id)
            ->where("IssueDate >=", $this->funcs->dbdatetime($args["IssueDateFrom"]))
            ->where("IssueDate <=", $this->funcs->dbdatetime($args["IssueDateTo"] . " 23:59:59"))
            // ->where(($args["sys"] == "EP") ? (" LEFT(srv.PinCode,1) = 'A' ") : (" LEFT(srv.PinCode,1) != 'A' "))
            ->where($wShip != "" ? $wShip : "1=1")
            ->where($wLike_SRV != "" ? $wLike_SRV : "1=1")
            ->where($finalWhere != "" ? $finalWhere : "1=1")
            ->where($finalWhere != "" ? $finalWhere : "1=1");
        if ($args['oprId']) {
            $this->ceh->where('srv.oprId', $args['oprId']);
        };
        if ($args['sys'] != '') {
            $pinPrefixes = $this->config->item('PIN_PREFIX');
            if ($args['sys'] == 'BL') {
                $this->ceh->where_in("LEFT(srv.PinCode, " . strlen($pinPrefixes['CAS']) . ")", $pinPrefixes);
            } else {
                $this->ceh->where_not_in("LEFT(srv.PinCode, " . strlen($pinPrefixes['CAS']) . ")", $pinPrefixes);
            }
        }

        $unionSRV_ODR = $this->ceh->group_by(array("CJMode_CD", "ISO_SZTP"))->get_compiled_select('SRV_ODR srv', TRUE);

        $stmt = $this->ceh->query($unionEIR . " UNION ALL " . $unionSRV_ODR);
        $stmt = $stmt->result_array();

        $newarray = array();
        foreach ($stmt as $k => $v) {
            $newarray[$v["CJMode_CD"]][$k] = $v;
        }

        $result = array();
        foreach ($newarray as $key => $value) {
            if (is_array($value)) {
                $bySize = array(
                    "CJMode_CD" => $key,
                    "CJModeName" => array_column($value, "CJModeName")[0],
                    "SZ_20" => 0,
                    "SZ_40" => 0,
                    "SZ_45" => 0,
                    "SumRow" => (float)array_sum(array_column($value, "COUNT_ISO"))
                );

                foreach ($value as $n => $m) {
                    $cntrSize = $this->getContSize($m["ISO_SZTP"]);
                    if ($cntrSize == "0") {
                        continue;
                    }

                    $size = "SZ_" . $cntrSize;

                    if ($bySize[$size] != 0) {
                        $bySize[$size] += (float)$m["COUNT_ISO"];
                    } else {
                        $bySize[$size] = (float)$m["COUNT_ISO"];
                    }
                }

                array_push($result, $bySize);
            }
        }

        if (count($result) > 0) {
            array_push(
                $result,
                array(
                    "CJMode_CD" => "TOTAL",
                    "CJModeName" => "TỔNG CỘNG",
                    "SZ_20" => array_sum(array_column($result, "SZ_20")),
                    "SZ_40" => array_sum(array_column($result, "SZ_40")),
                    "SZ_45" => array_sum(array_column($result, "SZ_45")),
                    "SumRow" => array_sum(array_column($result, "SumRow"))
                )
            );
        }

        return $result;
    }

    public function sumaryOrder($args = array())
    {
        $getCJModeName = $this->ceh->select("CJModeName")->where("dm.CJMode_CD = srv.CJMode_CD")
            ->limit(1)
            ->get_compiled_select("DELIVERY_MODE dm", TRUE);

        $joinEIR = $this->ceh->distinct()->select("EIRNo, CJMode_CD, ISO_SZTP, CJModeName, IssueDate")->get_compiled_select("EIR", TRUE);

        $unionEIR = $this->ceh->select('CJMode_CD, CJModeName, ISO_SZTP, SUM(TAMOUNT) SUM_AMOUNT')
            ->join("(" . $joinEIR . ") e", "i.REF_NO = e.EIRNo", "LEFT")
            ->where("CJMode_CD IS NOT NULL")
            ->where('YARD_ID', $this->yard_id)
            ->where("IssueDate >=", $this->funcs->dbdatetime($args["IssueDateFrom"]))
            ->where("IssueDate <=", $this->funcs->dbdatetime($args["IssueDateTo"] . " 23:59:59"))
            ->group_by(array("CJMode_CD", "ISO_SZTP", "CJModeName"))
            ->get_compiled_select('INV_DFT i', TRUE);

        $joinSRV_ODR = $this->ceh->distinct()->select("SSOderNo, CJMode_CD, ISO_SZTP, (" . $getCJModeName . ") AS CJModeName, IssueDate")
            ->get_compiled_select("SRV_ODR srv", TRUE);

        $unionSRV_ODR = $this->ceh->select('CJMode_CD, CJModeName, ISO_SZTP, SUM(TAMOUNT) SUM_AMOUNT')
            ->join("(" . $joinSRV_ODR . ") s", "i.REF_NO = s.SSOderNo", "LEFT")
            ->where("CJMode_CD IS NOT NULL")
            ->where('YARD_ID', $this->yard_id)
            ->where("IssueDate >=", $this->funcs->dbdatetime($args["IssueDateFrom"]))
            ->where("IssueDate <=", $this->funcs->dbdatetime($args["IssueDateTo"] . " 23:59:59"))
            ->group_by(array("CJMode_CD", "ISO_SZTP", "CJModeName"))
            ->get_compiled_select('INV_DFT i', TRUE);

        $stmt = $this->ceh->query($unionEIR . " UNION ALL " . $unionSRV_ODR)->result_array();

        $newarray = array();

        foreach ($stmt as $k => $v) {
            $newarray[$v["CJMode_CD"]][$k] = $v;
        }

        $result = array();
        foreach ($newarray as $key => $value) {
            if (is_array($value)) {
                $bySize = array(
                    "CJMode_CD" => $key,
                    "CJModeName" => array_column($value, "CJModeName")[0],
                    "SZ_20" => 0,
                    "SZ_40" => 0,
                    "SZ_45" => 0,
                    "SumRow" => (float)array_sum(array_column($value, "SUM_AMOUNT"))
                );

                foreach ($value as $n => $m) {
                    $size = "SZ_" . $this->getContSize($m["ISO_SZTP"]);
                    if ($bySize[$size] != 0) {
                        $bySize[$size] += (float)$m["SUM_AMOUNT"];
                    } else {
                        $bySize[$size] = (float)$m["SUM_AMOUNT"];
                    }
                }

                array_push($result, $bySize);
            }
        }

        if (count($result) > 0) {
            array_push(
                $result,
                array(
                    "CJMode_CD" => "TOTAL",
                    "CJModeName" => "TỔNG CỘNG",
                    "SZ_20" => array_sum(array_column($result, "SZ_20")),
                    "SZ_40" => array_sum(array_column($result, "SZ_40")),
                    "SZ_45" => array_sum(array_column($result, "SZ_45")),
                    "SumRow" => array_sum(array_column($result, "SumRow"))
                )
            );
        }

        return $result;
    }

    public function loadCntrForBooking($args = array())
    {
        $this->ceh->select("rowguid, OprID, LocalSZPT, ISO_SZTP, CntrNo, cBlock, cBay, cRow, cTier, cArea, SealNo, ContCondition, cTLHQ, Note");

        // $this->ceh->group_start();
        // $this->ceh->where("ContCondition IN ('A', 'B')");
        // $this->ceh->or_where('ContCondition IS NULL');
        // $this->ceh->group_end();

        $this->ceh->where("CMStatus", 'S');
        $this->ceh->where("Status", "E");
        $this->ceh->where("CntrClass", 2);
        $this->ceh->where("BookingNo IS NULL");
        $this->ceh->where("DateOut IS NULL");

        $this->ceh->group_start();
        $this->ceh->where('EIRNo IS NULL');
        $this->ceh->or_where('EIRNo IN (SELECT EIRNo FROM EIR WHERE bXNVC = 1)');
        $this->ceh->group_end();

        if (count($args) > 0) {
            if (isset($args["OprID"])) {
                $this->ceh->where("OprID", $args["OprID"]);
            }
            if (isset($args["LocalSZPT"])) {
                $this->ceh->where("LocalSZPT", $args["LocalSZPT"]);
            }
        }

        $temp = $this->ceh->get("CNTR_DETAILS")->result_array();
        return $temp;
    }

    public function loadCntrForUpdateBooking($args = array())
    {
        $countEir = $this->ceh->select('COUNT(rowguid)')
            ->where('e.BookingNo = cd.BookingNo AND e.CntrNo = cd.CntrNo AND e.CntrClass = cd.CntrClass')
            ->get_compiled_select('EIR e', TRUE);
        $countSrv = $this->ceh->select('COUNT(rowguid)')
            ->where('srv.BookingNo = cd.BookingNo AND srv.CntrNo = cd.CntrNo AND srv.CntrClass = cd.CntrClass')
            ->get_compiled_select('SRV_ODR srv', TRUE);

        $this->ceh->select("rowguid, OprID, LocalSZPT, ISO_SZTP, CntrNo, cBlock, cBay, cRow, cTier, cArea, BookingNo, DateOut
                            , SealNo, ContCondition, cTLHQ, Note, 0 AS Selector
                            , ($countEir) AS CountEir, ($countSrv) AS CountSrv");

        $this->ceh->group_start();
        $this->ceh->group_start();
        $this->ceh->where("BookingNo IS NULL");
        $this->ceh->where("CMStatus", 'S');
        $this->ceh->where("Status", "E");
        $this->ceh->where("CntrClass", 2);
        $this->ceh->where("DateOut IS NULL");

        // $this->ceh->group_start();
        // $this->ceh->where("ContCondition IN ('A', 'B')");
        // $this->ceh->or_where('ContCondition IS NULL');
        // $this->ceh->group_end();

        //ISSUE van load cac cont co so lenh (da hoan tat)
        // $this->ceh->where("ISNULL(EIRNo, '_giatrinul_') NOT IN (SELECT EIRNo FROM EIR WHERE bXNVC != 1)"); -- hao cmt 17/08 -> load tat ca , ko phan biet cont da lam lenh hay chua -> xu ly dua vao Selector de xac dinh co dc go cont ra khoi book hay ko

        // $this->ceh->group_start();
        // $this->ceh->where('EIRNo IS NULL');
        // $this->ceh->or_where('EIRNo IN (SELECT EIRNo FROM EIR WHERE bXNVC = 1)');
        // $this->ceh->group_end();
        $this->ceh->group_end();

        //hoac co so booking = bkno va phai la book cap rong (loai tru truong hop ha hang nhung nhap vao giong so book bkno)
        $this->ceh->or_group_start();
        $this->ceh->or_where("BookingNo", $args['BookingNo']);
        $this->ceh->where("Status", "E");
        $this->ceh->where("CntrClass", 2);
        $this->ceh->group_end();
        //hoac co so booking = bkno va phai la book cap rong (loai tru truong hop ha hang nhung nhap vao giong so book bkno)

        $this->ceh->group_end();

        if (count($args) > 0) {
            if (isset($args["OprID"])) {
                $this->ceh->where("OprID", $args["OprID"]);
            }
            if (isset($args["LocalSZPT"])) {
                $this->ceh->where("LocalSZPT", $args["LocalSZPT"]);
            }
        }

        $temp = $this->ceh->get("CNTR_DETAILS cd")->result_array();

        if (count($temp) > 0) {
            foreach ($temp as $key => $item) {
                if (mb_strtoupper(trim($item['BookingNo'])) == mb_strtoupper(trim($args['BookingNo']))) {
                    $isNotAllowAssign = (int)$item['CountEir'] + (int)$item['CountSrv'] > 0;
                    if ($isNotAllowAssign) {
                        $temp[$key]['Selector'] = 2; // da lam lenh -> [2]: selected + khong the unselect 
                    } else {
                        $temp[$key]['Selector'] = 1; // chua lam lenh -> [1]: selected + co the unselect 
                    }
                }
            }
        }

        return $temp;
    }

    public function checkCntrHoldByConfig($cntrNo, $fe)
    {
        $item = $this->ceh->select("Hold_Reason")
            ->where("CntrNo", $cntrNo)
            ->where("Status", $fe)
            ->where("ExpDate >=", date('Y-m-d H:i:s'))
            ->where("YARD_ID", $this->yard_id)
            ->limit(1)
            ->get("HOLD_CONTAINER")->row_array();

        return $item !== NULL ? ($item['Hold_Reason'] !== NULL ? $item['Hold_Reason'] : '') : NULL;
    }

    public function checkCntrStacking($cntrNo)
    {
        $item = $this->ceh->select("rowguid")
            ->where("CntrNo", $cntrNo)
            ->where("CMStatus !=", 'D') //ISSUE Chỉ cho làm lệnh lhạ bãi với tình trạng cont Delivery
            ->where("YARD_ID", $this->yard_id)
            ->limit(1)->get("CNTR_DETAILS")->row_array();

        return is_array($item) && count($item) > 0;
    }

    public function checkEIR($cntrNo)
    {
        $item = $this->ceh->select("rowguid")
            ->where("CntrNo", $cntrNo)
            ->where_in("CJMode_CD", array('HBAI', 'TRAR'))
            ->where("bXNVC", 0)
            ->where("YARD_ID", $this->yard_id)
            ->limit(1)->get("EIR")->row_array();

        return is_array($item) && count($item) > 0;
    }

    public function saveBooking($args = array())
    {
        $rowguidCntrs = array();
        if ($args["isAssignCntr"] == "Y") {
            $rowguidCntrs = $args["rowguids"];
            unset($args["rowguids"]);
        }

        $args["BOOK_STATUS"] = "A";

        if (isset($args["BookingNo"])) {
            $args["BookingNo"] = mb_strtoupper(trim($args["BookingNo"]));
        }

        if (isset($args["BookingDate"])) {
            $args["BookingDate"] = $this->funcs->dbDateTime($args["BookingDate"]);
        }

        if (isset($args["ExpDate"])) {
            $args["ExpDate"] = $this->funcs->dbDateTime($args["ExpDate"]);
        }

        if (isset($args["ShipName"])) {
            $args["ShipName"] = UNICODE . $args["ShipName"];
        }

        if (isset($args["Note"])) {
            $args["Note"] = UNICODE . $args["Note"];
        }

        if (isset($args['AssignedCont']) && count($args['AssignedCont']) > 0) {
            $args['AssignedCont'] = implode(', ', $args['AssignedCont']);
        }

        $checkBkNo = $this->ceh->where("BookingNo", $args["BookingNo"])
            ->where("LocalSZPT", $args["LocalSZPT"])
            ->where("OprID", $args["OprID"])
            ->get("EMP_BOOK")->row_array();
        if (is_array($checkBkNo) && count($checkBkNo) > 0) {
            return "error:Số Booking [" . $args["BookingNo"] . "] đã tồn tại! Vui lòng nhập số Booking khác!";
        }

        //multi yard
        $args["YARD_ID"] = $this->yard_id;

        $args['ModifiedBy'] = $this->session->userdata("UserID");
        $args['update_time'] = date('Y-m-d H:i:s');
        $args['insert_time'] = date('Y-m-d H:i:s');
        $args['CreatedBy'] = $args['ModifiedBy'];

        $this->ceh->insert('EMP_BOOK', $args);
        if ($this->ceh->affected_rows() < 1) {
            return 'error:' . $this->ceh->error();
        }

        if (count($rowguidCntrs) > 0) {
            $this->ceh->where_in("rowguid", $rowguidCntrs)
                ->update("CNTR_DETAILS", array("BookingNo" => $args["BookingNo"]));
        }

        return 'success';
    }

    public function updateBooking($data = array(), &$outMsg = '')
    {
        $this->ceh->trans_start();
        $this->ceh->trans_strict(FALSE);

        foreach ($data as $key => $item) {
            $rguid = $item['rowguid'];
            unset($item['rowguid']);

            if (isset($item["BookingNo"])) {
                $item["BookingNo"] = mb_strtoupper(trim($item["BookingNo"]));
            }

            if (isset($item['ShipName'])) {
                $item['ShipName'] = UNICODE . $item['ShipName'];
            }

            if (isset($item['Note'])) {
                $item['Note'] = UNICODE . $item['Note'];
            }

            if (isset($item['ExpDate'])) {
                $item['ExpDate'] = $this->funcs->dbDateTime($item['ExpDate'] . " 23:59:59");
            }

            if (isset($item['AssignedCont'])) {
                $item['AssignedCont'] = trim(ltrim($item['AssignedCont'], ','));
            }

            $addContRowguids = array();
            $removeContRowguids = array();
            if (isset($item["AttachCont"])) {
                $tempx = json_decode($item["AttachCont"], true);
                // array(
                //     "NewSelected" => array("1", "2"),
                //     "OldSelected" => array("1", "2"),
                // )

                // check neu co thay doi o truong attach cont
                // neu co thay doi -> co truong NewSelected
                // nguoc lai se ko co -> ko can update
                if (isset($tempx['NewSelected'])) {
                    $oldSelected = isset($tempx['OldSelected']) ? $tempx['OldSelected'] : array();
                    $newSelected = $tempx['NewSelected'];

                    $removeContRowguids = array_diff($oldSelected, $newSelected);
                    $addContRowguids = array_diff($newSelected, $oldSelected);
                }

                unset($item['AttachCont']);
            }

            if (isset($item['BOOK_STATUS'])) {
                $item['BOOK_STATUS'] = "U";
            }

            $item['YARD_ID'] = $this->yard_id;

            $item['ModifiedBy'] = $this->session->userdata("UserID");
            $item['update_time'] = date('Y-m-d H:i:s');

            /** [ISSUE 77] Billing/Lệnh cấp rỗng chỉ định **/
            // if ($addContRowguids !== NULL && count($addContRowguids) > 0) {
            //     unset($item['BookAmount']);
            //     $c = count($addContRowguids);
            //     $this->ceh->set("BookAmount", "BookAmount + $c", FALSE);
            // }

            // foreach ($item as $key => $value) {
            //     $this->ceh->set($key, $value);
            // }

            $this->ceh->where('rowguid', $rguid)->update('EMP_BOOK', $item);

            if ($addContRowguids !== NULL && count($addContRowguids) > 0) {
                $upContItem = array(
                    "ModifiedBy" => $this->session->userdata("UserID"),
                    "update_time" => date('Y-m-d H:i:s'),
                    "BookingNo" => $item['BookingNo']
                );

                $this->ceh->where_in("rowguid", $addContRowguids)->update("CNTR_DETAILS", $upContItem);
            }

            if ($removeContRowguids !== NULL && count($removeContRowguids) > 0) {
                $detachBookings = array(
                    "ModifiedBy" => $this->session->userdata("UserID"),
                    "update_time" => date('Y-m-d H:i:s'),
                    "BookingNo" => NULL
                );

                $this->ceh->where_in("rowguid", $removeContRowguids)->update("CNTR_DETAILS", $detachBookings);
            }
        }

        $this->ceh->trans_complete();

        if ($this->ceh->trans_status() === FALSE) {
            $outMsg = $this->ceh->_error_message();
            $this->ceh->trans_rollback();
            return FALSE;
        } else {
            $this->ceh->trans_commit();
            return TRUE;
        }
    }

    public function deleteBooking($data, &$outMsg = '')
    {
        $this->ceh->trans_start();
        $this->ceh->trans_strict(FALSE);

        if (isset($data['delCntrRowguids']) && count($data['delCntrRowguids']) > 0) {
            $detachBookings = array(
                "ModifiedBy" => $this->session->userdata("UserID"),
                "update_time" => date('Y-m-d H:i:s'),
                "BookingNo" => NULL
            );
            $this->ceh->where_in("rowguid", $data['delCntrRowguids'])->update("CNTR_DETAILS", $detachBookings);
        }

        if (isset($data['delRowguids']) && count($data['delRowguids']) > 0) {
            $this->ceh->where_in('rowguid', $data['delRowguids'])->delete('EMP_BOOK');
        }

        $this->ceh->trans_complete();

        if ($this->ceh->trans_status() === FALSE) {
            $outMsg = $this->ceh->_error_message();
            $this->ceh->trans_rollback();
            return FALSE;
        } else {
            $this->ceh->trans_commit();
            return TRUE;
        }
    }

    public function update_order_monitor($data = array(), $ordType, $dataOrder = array())
    {
        $this->ceh->trans_start();
        $this->ceh->trans_strict(FALSE);

        $tblname = $ordType == 'NH' ? "EIR" : "SRV_ODR";
        $colOrderName = $tblname == 'EIR' ? 'EIRNo' : 'SSOderNo';

        foreach ($dataOrder as $key => $item) {
            $this->ceh->where($colOrderName, $item['orderNo'])->update($tblname, array('CusID' => $item['CusID']));
        }

        foreach ($data as $key => $item) {
            $rguid = $item['rowguid'];
            unset($item['rowguid']);

            if (isset($item['NameDD'])) {
                $item['NameDD'] = UNICODE . $item['NameDD'];
            }

            if (isset($item['Note'])) {
                $item['Note'] = UNICODE . $item['Note'];
            }

            if (isset($item['TERMINAL_CD'])) {
                $item['TERMINAL_CD'] = UNICODE . $item['TERMINAL_CD'];
            }

            $isBarge = false;
            if (isset($item['BargeInfo'])) {
                $barges = explode('/', $item['BargeInfo']);
                $item['BARGE_CODE'] = count($barges) > 0 ? $barges[0] : "";
                $item['BARGE_YEAR'] = count($barges) > 1 ? $barges[1] : "";
                $item['BARGE_CALL_SEQ'] =  count($barges) > 2 ? $barges[2] : "";
                $isBarge = !empty($item['BARGE_CODE']);
                unset($item['BargeInfo']);
            }

            if (isset($item['ShipEditedInfo'])) {
                $ships = explode(';', $item['ShipEditedInfo']); //shipkey;shipid;imvoy;exvoy

                if (count($ships) > 3 && $ships[0] != '') {
                    $item['ShipKey'] = $ships[0];
                    $item['ShipID'] = $ships[1];
                    $item['ImVoy'] = $ships[2];
                    $item['ExVoy'] = $ships[3];
                }

                if (isset($item['ShipInfo'])) {
                    $temp = explode('/', $item['ShipInfo']);
                    $shipName = count($temp) > 0 ? $temp[0] : "";

                    if (in_array($shipName, array("STORAGE", "EXPT"))) {
                        unset($item['ImVoy']);
                        unset($item['ExVoy']);
                    }

                    unset($item['ShipInfo']);
                }

                unset($item['ShipEditedInfo']);
            }

            //cap nhat neu chon salan
            $item['DMETHOD_CD'] = $isBarge ? 'BAI-SALAN' : 'BAI-XE';

            $item['YARD_ID'] = $this->yard_id;
            $item['ModifiedBy'] = $this->session->userdata("UserID");
            $item['update_time'] = date('Y-m-d H:i:s');

            //nếu là lệnh dcih vụ
            if ($ordType == 'DV') {
                unset($item['bXNVC'], $item['TERMINAL_CD'], $item['CLASS'], $item['UNNO']);
                if (isset($item["FDate"])) {
                    $item["FDate"] = $this->funcs->dbDateTime($item["FDate"]);
                }
            } else {
                unset($item["FDate"]);
                //cap nhat neu chon salan + la lenh nang ha
                $item['IsTruckBarge'] =  $isBarge ?  'B' : 'T';
            }

            $this->ceh->where('rowguid', $rguid)->update($tblname, $item);
        }

        $this->ceh->trans_complete();

        if ($this->ceh->trans_status() === FALSE) {
            $outMsg = $this->ceh->_error_message();
            $this->ceh->trans_rollback();
            return FALSE;
        } else {
            $this->ceh->trans_commit();
            return TRUE;
        }
    }

    public function load_SSOrder_Renewed($orderType, $orderNo, $cntrNo)
    {
        $colOrderNo = $orderType == "NH" ? "e.EIRNo" : "e.SSOderNo";
        $tblName = $orderType == "NH" ? "EIR e" : "SRV_ODR e";

        $this->ceh->select('e.ShipKey, e.CntrClass, e.CntrNo, e.IssueDate, e.ExpDate, e.ShipID, e.BerthDate, e.ImVoy, e.ExVoy, e.Status
                            , e.Temperature, e.SealNo, e.OprID, e.CusID, e.CmdID, e.CJMode_CD, e.InvNo, e.Note, e.LocalSZPT
                            , e.ISO_SZTP, e.DG_CD, e.CWeight, e.CMDWeight, e.PAYMENT_TYPE, e.PAYER_TYPE, e.PAYMENT_CHK
                            , e.CARGO_TYPE, e.SealNo1, e.SealNo2, e.BookingNo, e.BLNo, e.SEQ, e.cNoCont, e.BARGE_CODE, e.BARGE_YEAR
                            , e.BARGE_CALL_SEQ, e.OOG_TOP, e.OOG_LEFT, e.OOG_RIGHT, e.OOG_BACK, e.OOG_FRONT, e.DRAFT_INV_NO, e.DELIVERYORDER
                            , e.PersonalID, e.NameDD, e.EIR_SEQ, e.POD, e.FPOD, e.IsLocal
                            , e.ExpPluginDate, e.SHIPPER_NAME, e.ExecTime, e.Vent, e.Vent_Unit, e.DateIn, (CASE e.VGM WHEN 1 THEN 1 ELSE 0 END) VGM
                            , e.Mail, e.cArea, cd.rowguid RowguidCntrDetails, cd.ShipYear, cd.ShipVoy');
        $this->ceh->join('CNTR_DETAILS cd', 'cd.CntrNo = e.CntrNo AND cd.ShipKey = e.ShipKey AND cd.CntrClass = e.CntrClass AND cd.YARD_ID = e.YARD_ID');

        $this->ceh->where($colOrderNo, $orderNo);
        $this->ceh->where('e.CntrNo', $cntrNo);
        $this->ceh->where('CMStatus', 'S');
        $this->ceh->where('cd.YARD_ID', $this->yard_id);

        $this->ceh->order_by('CntrNo', 'ASC');
        $stmt = $this->ceh->limit(1)->get($tblName);
        return $stmt->row_array();
    }

    public function loadDO($oprs = array(), $fromDate = '', $toDate = '', $searchVal = '')
    {
        $this->ceh->select("e.rowguid as EdoRowguid, CntrNo, BLNo, OprID, LocalSZPT, ISO_SZTP, Status
                            , CntrClass, cm.CLASS_Name, DELIVERYORDER, EdoDate, ExpDate, Shipper_Name, ShipID, ShipName
                            , ImVoy, ExVoy, POD, FPOD, RetLocation, Haulage_Instruction, Note, e.YARD_ID"); //STT, Select, ComparedStatus,
        $this->ceh->join("CLASS_MODE cm", "cm.CLASS_Code = e.CntrClass AND cm.YARD_ID = e.YARD_ID", "left");
        $this->ceh->where("e.YARD_ID", $this->yard_id);
        if (count($oprs) > 0) {
            $this->ceh->where_in("e.OprID", $oprs);
        }
        if ($fromDate != '') {
            $this->ceh->where("e.insert_time >=", $this->funcs->dbDateTime($fromDate));
        }
        if ($toDate != '') {
            $this->ceh->where("e.insert_time <=", $this->funcs->dbDateTime($toDate));
        }
        if ($searchVal != '') {
            $this->ceh->group_start();
            $this->ceh->where("e.DELIVERYORDER", $searchVal);
            $this->ceh->or_where("e.BLNo", $searchVal);
            $this->ceh->or_where("e.CntrNo", $searchVal);
            $this->ceh->group_end();
        }
        $stmt = $this->ceh->order_by("e.EdoDate", "ASC")->get("EDI_EDO e")->result_array();
        if (count($stmt) == 0) {
            return array();
        }
        foreach ($stmt as $k => $n) {
            $w = array(
                "BLNo" => $n["BLNo"],
                "CntrNo" => $n["CntrNo"],
                "OprID" => $n["OprID"],
                //"LocalSZPT" => $n["LocalSZPT"],
                // "ISO_SZTP" => $n["ISO_SZTP"],
                "YARD_ID" => $n["YARD_ID"],
                "Status" => $n["Status"],
                //"CntrClass" => $n["CntrClass"],
                //"ShipID" => $n["ShipID"],
                //"ImVoy" => $n["ImVoy"],
                //"ExVoy" => $n["ExVoy"],
            );
            $check = $this->checkCntrByDO($w);
            $stmt[$k]["rowguid"] = null;
            if (!is_array($check)) {
                $stmt[$k]["ComparedStatus"] = "Không tồn tại trong hệ thống!";
                continue;
            }

            if ($check["CMStatus"] != "S") {
                $stmt[$k]["ComparedStatus"] = "Không có trên bãi!";
                continue;
            }

            if ($check["DateIn"] === null) {
                $stmt[$k]["ComparedStatus"] = "Chưa có ngày vào bãi!";
                continue;
            }

            if ($check["DateOut"] !== null) {
                $stmt[$k]["ComparedStatus"] = "Đã ra khỏi bãi!";
                continue;
            }

            if ($check["EIRNo"] !== null && $check["bXNVC"] != "1") {
                $stmt[$k]["ComparedStatus"] = "Đang thực hiện lệnh nâng/hạ với số lệnh [" . $check["EIRNo"] . "]!";
                continue;
            }
            $stmt[$k]["rowguid"] = $check["rowguid"];
        }
        return $stmt;
    }

    public function updateDO($data = array(), &$outMsg = '')
    {
        $this->ceh->trans_start();
        $this->ceh->trans_strict(FALSE);

        foreach ($data as $key => $item) {
            if (isset($item['ExpDate'])) {
                $data[$key]['ExpDate'] = $this->funcs->dbDateTime($item['ExpDate'] . " 23:59:00");
            }

            $data[$key]['YARD_ID'] = $this->yard_id;

            $data[$key]['ModifiedBy'] = $this->session->userdata("UserID");
            $data[$key]['update_time'] = date('Y-m-d H:i:s');
        }

        $this->ceh->update_batch('EDI_EDO', $data, 'rowguid');

        $this->ceh->trans_complete();

        if ($this->ceh->trans_status() === FALSE) {
            $outMsg = $this->ceh->_error_message();
            $this->ceh->trans_rollback();
            return FALSE;
        } else {
            $this->ceh->trans_commit();
            return TRUE;
        }
    }

    private function checkCntrByDO($args = array())
    {
        $this->ceh->select("cn.rowguid, cn.EIRNo, cn.SSOderNo, e.bXNVC, cn.CMStatus, cn.DateIn, cn.DateOut");
        $this->ceh->join('EIR e', 'e.CntrNo = cn.CntrNo AND e.CntrClass = cn.CntrClass AND e.ShipKey = cn.ShipKey
                                                        AND e.OprID = cn.OprID AND e.YARD_ID = cn.YARD_ID', 'LEFT');
        $this->ceh->where("cn.YARD_ID", $this->yard_id);
        if (count($args) > 0) {
            foreach ($args as $k => $w) {
                $this->ceh->where("cn.$k", $w);
            }
        }
        return $this->ceh->limit(1)->get("CNTR_DETAILS cn")->row_array();
    }

    public function getImportPickupByRowguids($rowguids)
    {
        $this->ceh->select('cd.rowguid AS RowguidCntrDetails, ShipKey, BerthDate, ShipID, ShipYear, ShipVoy, CntrNo
                            , BLNo, cd.BookingNo, cd.CusID AS ShipperName
                            , cd.CntrClass, OprID, LocalSZPT, ISO_SZTP, Status
                            , DateIn, (CASE VGM WHEN 1 THEN 1 ELSE 0 END) VGM, Ter_Hold_CHK
                            , SealNo, SealNo1, SealNo2, IsLocal, CMDWeight, CARGO_TYPE, Temperature, cd.CJMode_CD, dm.CJModeName
                            , ImVoy, ExVoy, CmdID, POD, FPOD, Port_CD, OOG_TOP, OOG_LEFT, OOG_RIGHT, OOG_BACK, OOG_FRONT
                            , cBlock, cBay, cRow, cTier, cArea, CLASS ,UNNO, Note, cTLHQ, ct.Description');
        $this->ceh->join('CARGO_TYPE ct', 'ct.Code = cd.CARGO_TYPE AND ct.YARD_ID = cd.YARD_ID', 'LEFT');
        $this->ceh->join('DELIVERY_MODE dm', 'dm.CJMode_CD = cd.CJMode_CD AND dm.YARD_ID = cd.YARD_ID', 'LEFT');
        $this->ceh->where("cd.YARD_ID", $this->yard_id);
        $this->ceh->where_in("cd.rowguid", $rowguids);
        $stmt = $this->ceh->get("CNTR_DETAILS cd");
        return $stmt->result_array();
    }

    public function load_ip_cntr_details($blbkNo = '', $cntrNo = '', $isGiaoCntrXuat = false)
    {
        $rule045 = $this->ceh->select('CFG_CHK')->where('IDENTITY_CD', 'DEF_045')->get('YP_RULE')->row_array();
        $inBL = array();
        $cjmodeUnstuffs = $this->ceh->select('CJMode_CD')->where('ischkCFS', 2)->where('YARD_ID', $this->yard_id)->get_compiled_select('DELIVERY_MODE', TRUE);
        if ($cntrNo != '') {
            $checkCntrNo = $this->ceh->select("PinCode")
                ->where("bXNVC = 0")
                ->where("CntrNo", $cntrNo)
                ->limit(1)
                ->get("EIR")->row_array();

            if (isset($checkCntrNo["PinCode"])) {
                return "Container này đã được làm lệnh NH [PIN: " . $checkCntrNo["PinCode"] . "]";
            }

            $checkCntrNoInSRV_ODR = $this->ceh->select("PinCode")
                ->where("FDate IS NULL")
                ->where("CntrNo", $cntrNo)
                ->where("YARD_ID", $this->yard_id)
                ->where("CJMode_CD IN ($cjmodeUnstuffs)")
                ->limit(1)
                ->get("SRV_ODR")->row_array();

            if (isset($checkCntrNoInSRV_ODR["PinCode"])) {
                return "Container này đã được làm lệnh rút hàng [PIN: " . $checkCntrNoInSRV_ODR["PinCode"] . "]";
            }

            $inBL = $this->ceh->select('BLNo, BookingNo')
                ->where('CntrNo', $cntrNo)
                ->where('YARD_ID', $this->yard_id);
            if ($rule045['CFG_CHK'] == '1') {
                $inBL =  $inBL->where("((IsLocal = 'L' AND CMStatus = 'S') OR (IsLocal = 'F' AND CMStatus = 'S'))");
            } else {
                $inBL =  $inBL->where("((IsLocal = 'L' AND CMStatus IN('B', 'I', 'S')) OR (IsLocal = 'F' AND CMStatus = 'S'))"); //issue Làm lệnh lấy nguyên cont hàng nội khi cont chưa stacking
            }
            if ($isGiaoCntrXuat) {
                $inBL =  $inBL->where('BookingNo IS NOT NULL')->where('CntrClass', '3');
            } else {
                $inBL =  $inBL->where('BLNo IS NOT NULL')->where_in('CntrClass', array('1', '4'));
            }
            $inBL =  $inBL->get('CNTR_DETAILS')->result_array();
        }

        $this->ceh->select('cd.rowguid AS RowguidCntrDetails, ShipKey, BerthDate, ShipID, ShipYear, ShipVoy, CntrNo
                            , BLNo, cd.BookingNo, cd.CusID AS ShipperName
                            , cd.CntrClass, OprID, LocalSZPT, ISO_SZTP, Status
                            , DateIn, (CASE VGM WHEN 1 THEN 1 ELSE 0 END) VGM, Ter_Hold_CHK
                            , SealNo, SealNo1, SealNo2, IsLocal, CMDWeight, CARGO_TYPE, Temperature, cd.CJMode_CD, dm.CJModeName
                            , ImVoy, ExVoy, CmdID, POD, FPOD, Port_CD, OOG_TOP, OOG_LEFT, OOG_RIGHT, OOG_BACK, OOG_FRONT
                            , cBlock, cBay, cRow, cTier, cArea, CLASS ,UNNO, Note, cTLHQ, ct.Description');
        $this->ceh->join('CARGO_TYPE ct', 'ct.Code = cd.CARGO_TYPE AND ct.YARD_ID = cd.YARD_ID', 'LEFT');
        $this->ceh->join('DELIVERY_MODE dm', 'dm.CJMode_CD = cd.CJMode_CD AND dm.YARD_ID = cd.YARD_ID', 'LEFT');

        //, Transist, TERMINAL_CD

        if ($cntrNo == '') {
            if ($isGiaoCntrXuat) {
                $this->ceh->where("BookingNo", $blbkNo);
                $this->ceh->where("cd.CntrClass", '3');
                $this->ceh->where("cd.IsCancelLKH", '1');
                $this->ceh->where("cd.bTraLai", '1');
            } else {
                $this->ceh->where("BLNo", $blbkNo);
                $this->ceh->where_in('cd.CntrClass', ['1', '4']);
            }
        }

        if ($blbkNo == '') {
            $newBL = array_filter($inBL, function ($var) {
                return ($var['BLNo'] !== NULL);
            });
            $newBK = array_filter($inBL, function ($var) {
                return ($var['BookingNo'] !== NULL);
            });

            $this->ceh->where("CntrNo", $cntrNo);
            if ($isGiaoCntrXuat) {
                $this->ceh->where_in("BookingNo", count($newBK) > 0 ? array_column($newBK, 'BookingNo') : array(''));
                $this->ceh->where("cd.CntrClass", '3');
                $this->ceh->where("cd.IsCancelLKH", '1');
                $this->ceh->where("cd.bTraLai", '1');
            } else {
                $this->ceh->where_in("BLNo", count($newBL) > 0 ? array_column($newBL, 'BLNo') : array(''));
                $this->ceh->where_in('cd.CntrClass', ['1', '4']);
            }
        }

        // $this->ceh->where('Status', 'F');
        if ($rule045['CFG_CHK'] == '1') {
            $this->ceh->where("((IsLocal = 'L' AND CMStatus = 'S') OR (IsLocal = 'F' AND CMStatus = 'S'))");
        } else {
            $this->ceh->where("((IsLocal = 'L' AND CMStatus IN('B', 'I', 'S')) OR (IsLocal = 'F' AND CMStatus = 'S' AND DateIn IS NOT NULL))"); //issue Làm lệnh lấy nguyên cont hàng nội khi cont chưa stacking
        }

        // $this->ceh->group_start();
        // $this->ceh->where("cd.CntrClass = ( CASE WHEN IsCancelLKH = 1 AND bTraLai = 1 THEN 3 ELSE 0 END ) ");
        // $this->ceh->or_where('cd.CntrClass IN (1, 4)');
        // $this->ceh->group_end();

        // $this->ceh->where('DateIn IS NOT NULL'); //issue Làm lệnh lấy nguyên cont hàng nội khi cont chưa stacking
        $this->ceh->where('DateOut IS NULL');

        $this->ceh->group_start();
        $this->ceh->where('EIRNo IS NULL');
        $this->ceh->or_where('EIRNo NOT IN (SELECT EIRNo FROM EIR WHERE (bXNVC = 0 OR bXNVC IS NULL) AND CntrNo = cd.CntrNo )');
        $this->ceh->group_end();

        $this->ceh->group_start();
        $this->ceh->where('SSOderNo IS NULL'); //srv.ShipKey = cd.ShipKey AND srv.CntrNo = cd.CntrNo AND srv.CntrClass = cd.CntrClass
        $this->ceh->or_where('SSOderNo NOT IN (SELECT SSOderNo FROM SRV_ODR WHERE FDate IS NULL AND CntrNo = cd.CntrNo AND ShipKey = cd.ShipKey AND CntrClass = cd.CntrClass AND YARD_ID = cd.YARD_ID )');
        $this->ceh->group_end();

        $this->ceh->where('cd.YARD_ID', $this->yard_id);

        $this->ceh->order_by('CntrNo', 'ASC');
        $stmt = $this->ceh->get('CNTR_DETAILS cd');
        return $stmt->result_array();
    }

    public function load_service_orders($billno = '', $cntrNo = '')
    {
        $this->ceh->select('cd.rowguid AS RowguidCntrDetails, ShipKey, BerthDate, ShipID, ShipYear, ShipVoy, CntrNo, BLNo, BookingNo, cd.CntrClass, cd.CusID AS ShipperName
                            , OprID, LocalSZPT, ISO_SZTP, Status, DateIn, (CASE VGM WHEN 1 THEN 1 ELSE 0 END) VGM, cd.Ter_Hold_CHK
                            , SealNo, SealNo1, SealNo2, IsLocal, CMDWeight, CARGO_TYPE, Temperature, cd.CJMode_CD, dm.CJModeName
                            , ImVoy, ExVoy, CmdID, POD, FPOD, Port_CD, OOG_TOP, OOG_LEFT, OOG_RIGHT, OOG_BACK, OOG_FRONT, Transist
                            , cBlock, cBay, cRow, cTier, UNNO, Note, cTLHQ, ct.Description, cd.EIRNo, cd.SSOderNo');
        $this->ceh->join('CARGO_TYPE ct', 'ct.Code = cd.CARGO_TYPE AND ct.YARD_ID = cd.YARD_ID', 'LEFT');
        $this->ceh->join('DELIVERY_MODE dm', 'dm.CJMode_CD = cd.CJMode_CD AND dm.YARD_ID = cd.YARD_ID', 'LEFT');
        if ($cntrNo != '') {
            $this->ceh->where('CntrNo', $cntrNo);
        }
        if ($billno != '') {
            $this->ceh->group_start();
            $this->ceh->where('cd.Ter_Hold_CHK', '0');
            $this->ceh->or_where('cd.Ter_Hold_CHK IS NULL');
            $this->ceh->group_end();
            $this->ceh->where('BLNo', $billno);
        }
        $this->ceh->where('CMStatus', 'S');

        $this->ceh->where('cd.YARD_ID', $this->yard_id);

        $this->ceh->order_by('CntrNo', 'ASC');
        $stmt = $this->ceh->get('CNTR_DETAILS cd');
        return $stmt->result_array();
    }

    public function load_stuffing_conts($cntrNo = '')
    {
        $this->ceh->select('cd.rowguid AS RowguidCntrDetails, ShipKey, BerthDate, ShipID, ShipYear, ShipVoy, CntrNo, BookingNo
                            , BLNo, cd.CntrClass, OprID, LocalSZPT, ISO_SZTP, Status, DateIn, (CASE VGM WHEN 1 THEN 1 ELSE 0 END) VGM, Vent, Vent_Unit
                            , SealNo, SealNo1, SealNo2, IsLocal, CWeight, CMDWeight, Temperature, DG_CD, cd.CJMode_CD, dm.CJModeName
                            , ImVoy, ExVoy, CmdID, POD, FPOD, Port_CD, OOG_TOP, OOG_LEFT, OOG_RIGHT, OOG_BACK, OOG_FRONT, Transist
                            , cBlock, cBay, cRow, cTier, Note, cTLHQ, ct.Description');
        $this->ceh->join('CARGO_TYPE ct', 'ct.Code = cd.CARGO_TYPE AND ct.YARD_ID = cd.YARD_ID', 'LEFT');
        $this->ceh->join('DELIVERY_MODE dm', 'dm.CJMode_CD = cd.CJMode_CD AND dm.YARD_ID = cd.YARD_ID', 'LEFT');

        $this->ceh->where('CMStatus', 'S');
        $this->ceh->where('Status', 'E');
        $this->ceh->where('cd.CntrClass', '2');
        // $this->ceh->where("cd.ContCondition IN('A', 'B')");

        $this->ceh->where('cd.YARD_ID', $this->yard_id);

        // $this->ceh->group_start();
        // $this->ceh->where('SSOderNo IS NULL');
        // $this->ceh->or_where('CntrNo NOT IN (SELECT CntrNo FROM SRV_ODR WHERE (Fdate IS NULL))');
        // $this->ceh->group_end();
        $this->ceh->where("ISNULL(CntrNo, '_giatrinul_') NOT IN (SELECT CntrNo FROM SRV_ODR WHERE (Fdate IS NULL))");

        $this->ceh->order_by('CntrNo', 'ASC');
        $stmt = $this->ceh->get('CNTR_DETAILS cd');
        return $stmt->result_array();
    }

    public function load_unstuffing_conts($cntrNo = '')
    {
        $bXNVCsql = $this->ceh->select("bXNVC")->where('e.ShipKey = cd.ShipKey AND e.CntrNo = cd.CntrNo AND e.CntrClass = cd.CntrClass AND e.YARD_ID = cd.YARD_ID')
            ->limit(1)->get_compiled_select("EIR e", TRUE);

        $cjmodeUnstuffs = $this->ceh->select('CJMode_CD')->where('ischkCFS', 2)->where('YARD_ID', $this->yard_id)
            ->get_compiled_select('DELIVERY_MODE', TRUE);

        $fDate = $this->ceh->select("Fdate")
            ->where("srv.ShipKey = cd.ShipKey AND srv.CntrNo = cd.CntrNo AND srv.CntrClass = cd.CntrClass AND srv.YARD_ID = cd.YARD_ID AND srv.CJMode_CD IN ($cjmodeUnstuffs)")
            ->limit(1)->get_compiled_select("SRV_ODR srv", TRUE);

        $ssOderNo = $this->ceh->select("SSOderNo")
            ->where("srv.ShipKey = cd.ShipKey AND srv.CntrNo = cd.CntrNo AND srv.CntrClass = cd.CntrClass AND srv.YARD_ID = cd.YARD_ID AND srv.CJMode_CD IN ($cjmodeUnstuffs)")
            ->limit(1)->get_compiled_select("SRV_ODR srv", TRUE);

        $this->ceh->select("cd.rowguid AS RowguidCntrDetails, ShipKey, BerthDate, ShipID, ShipYear, ShipVoy, CntrNo, BookingNo
                            , BLNo, cd.CntrClass, OprID, LocalSZPT, ISO_SZTP, Status, DateIn, (CASE VGM WHEN 1 THEN 1 ELSE 0 END) VGM
                            , Vent, Vent_Unit, Ter_Hold_CHK, SealNo, SealNo1, SealNo2, IsLocal, CWeight, CMDWeight, CARGO_TYPE
                            , Temperature, DG_CD, cd.CJMode_CD, dm.CJModeName
                            , ImVoy, ExVoy, CmdID, POD, FPOD, Port_CD, OOG_TOP, OOG_LEFT, OOG_RIGHT, OOG_BACK, OOG_FRONT, Transist
                            , cBlock, cBay, cRow, cTier, cArea, Note, cTLHQ, ct.Description
                            , (" . $ssOderNo . ") AS SSOderNo
                            , cd.EIRNo, cd.CusID AS ShipperName
                            , (" . $bXNVCsql . ") AS bXNVC
                            , (" . $fDate . ") AS FDATE");
        $this->ceh->join('CARGO_TYPE ct', 'ct.Code = cd.CARGO_TYPE AND ct.YARD_ID = cd.YARD_ID', 'LEFT');
        $this->ceh->join('DELIVERY_MODE dm', 'dm.CJMode_CD = cd.CJMode_CD AND dm.YARD_ID = cd.YARD_ID', 'LEFT');

        //$this->ceh->where('BLNo IS NOT NULL');
        $this->ceh->where('CMStatus', 'S');
        $this->ceh->where('Status', 'F');

        // $this->ceh->where('cd.CntrClass', '1');
        $this->ceh->where('cd.YARD_ID', $this->yard_id);

        // $this->ceh->group_start();
        // $this->ceh->where('EIRNo IS NULL');
        // $this->ceh->or_where('CntrNo NOT IN (SELECT CntrNo FROM SRV_ODR WHERE (Fdate IS NULL))');
        // $this->ceh->group_end();

        $this->ceh->order_by('CntrNo', 'ASC');
        $stmt = $this->ceh->get('CNTR_DETAILS cd');

        return $stmt->result_array();
    }

    public function load_transstuffing_unstuff_cont($cntrNo, &$outMsg)
    {
        $bXNVCsql = $this->ceh->select("bXNVC")->where('e.ShipKey = cd.ShipKey AND e.CntrNo = cd.CntrNo AND e.CntrClass = cd.CntrClass AND e.YARD_ID = cd.YARD_ID')
            ->limit(1)->get_compiled_select("EIR e", TRUE);

        $this->ceh->select("cd.rowguid AS RowguidCntrDetails, ShipKey, BerthDate, ShipID, ShipYear, ShipVoy, CntrNo, cd.BookingNo, cd.Ter_Hold_CHK
                            , BLNo, cd.CntrClass, OprID, LocalSZPT, ISO_SZTP, Status, DateIn, (CASE VGM WHEN 1 THEN 1 ELSE 0 END) VGM
                            , Vent, Vent_Unit, cd.EIRNo, SealNo, SealNo1, SealNo2, IsLocal, CWeight, CMDWeight, CARGO_TYPE
                            , Temperature, DG_CD, cd.CJMode_CD, dm.CJModeName, ImVoy, ExVoy, CmdID, POD, FPOD, Port_CD, OOG_TOP
                            , (" . $bXNVCsql . ") AS bXNVC
                            , OOG_LEFT, OOG_RIGHT, OOG_BACK, OOG_FRONT, Transist, cBlock, cBay, cRow, cTier, Note, cTLHQ, ct.Description");
        $this->ceh->join('CARGO_TYPE ct', 'ct.Code = cd.CARGO_TYPE AND ct.YARD_ID = cd.YARD_ID', 'LEFT');
        $this->ceh->join('DELIVERY_MODE dm', 'dm.CJMode_CD = cd.CJMode_CD AND dm.YARD_ID = cd.YARD_ID', 'LEFT');
        $this->ceh->where("CntrNo", $cntrNo);
        $this->ceh->where('CMStatus', 'S');
        $this->ceh->where('Status', 'F');
        $this->ceh->where_in('cd.CntrClass', array('1', '3'));

        $this->ceh->where('cd.YARD_ID', $this->yard_id);

        $this->ceh->order_by('CntrNo', 'ASC');
        $stmt = $this->ceh->limit(1)->get('CNTR_DETAILS cd');
        $stmt = $stmt->row_array();

        if ($stmt === NULL) {
            $outMsg = "Container [" . $cntrNo . "] không đủ điều kiện làm lệnh! Kiểm tra lại!";
            return array();
        }

        if ($stmt["EIRNo"] !== null && $stmt["bXNVC"] != "1") {
            $outMsg = "Container [" . $cntrNo . "] đã được cấp lệnh số [" . $stmt['EIRNo'] . "]!";
            return array();
        }

        return $stmt;
    }

    public function load_transstuffing_stuff_cont($args, &$outMsg)
    {
        $this->ceh->select("cd.rowguid AS RowguidCntrDetails, cd.ShipKey, cd.BerthDate, cd.ShipID, cd.ShipYear, cd.ShipVoy, cd.CntrNo
                            , cd.BookingNo, BLNo, cd.CntrClass, cd.OprID, cd.LocalSZPT, cd.ISO_SZTP, cd.Status, cd.DateIn
                            , (CASE VGM WHEN 1 THEN 1 ELSE 0 END) VGM, Vent, Vent_Unit, cd.EIRNo, cd.SealNo, cd.SealNo1, cd.SealNo2
                            , cd.IsLocal, cd.CWeight, cd.CMDWeight, cd.CARGO_TYPE, cd.Temperature, cd.DG_CD, cd.CJMode_CD, dm.CJModeName
                            , cd.ImVoy, cd.ExVoy, cd.CmdID, cd.POD, cd.FPOD, cd.Port_CD, OOG_TOP, OOG_LEFT, OOG_RIGHT, OOG_BACK
                            , OOG_FRONT, Transist, cBlock, cBay, cRow, cTier, cd.Note, cTLHQ, ct.Description, bk.BookingNo AS CheckBooking");
        $this->ceh->join('CARGO_TYPE ct', 'ct.Code = cd.CARGO_TYPE AND ct.YARD_ID = cd.YARD_ID', 'LEFT');
        $this->ceh->join('DELIVERY_MODE dm', 'dm.CJMode_CD = cd.CJMode_CD AND dm.YARD_ID = cd.YARD_ID', 'LEFT');
        $this->ceh->join('EMP_BOOK bk', "cd.BookingNo = bk.BookingNo AND bk.isAssignCntr = 'Y' AND cd.YARD_ID = bk.YARD_ID", 'LEFT');

        $this->ceh->where("cd.OprID", $args["OprID"]);
        $this->ceh->where("cd.ISO_SZTP", $args["ISO_SZTP"]);
        $this->ceh->where('cd.CMStatus', 'S');
        $this->ceh->where('cd.Status', 'E');
        $this->ceh->where('cd.CntrClass', '2');

        $this->ceh->where('cd.YARD_ID', $this->yard_id);

        $this->ceh->order_by('cd.CntrNo', 'ASC');
        $stmt = $this->ceh->get('CNTR_DETAILS cd');
        $stmt = $stmt->result_array();

        if (count($stmt) == 0) {
            $outMsg = "Không tìm thấy Container đủ điều kiện đóng hàng!";
            return array();
        }

        return $stmt;
    }

    public function getBarge()
    {
        $this->ceh->select('vs.ShipID, ShipName, ShipVoy, ShipYear');
        $this->ceh->join('VESSELS vv', 'vv.ShipID = vs.ShipID');
        $this->ceh->where('VESSEL_TYPE', 'B');
        $this->ceh->where('ShipArrStatus <', '2');

        $this->ceh->where('vs.YARD_ID', $this->yard_id);

        $this->ceh->order_by('ShipName', 'ASC');
        $stmt = $this->ceh->get('VESSEL_SCHEDULE vs');
        return $stmt->result_array();
    }

    public function getLanePortID($shipkey = '')
    {
        $this->ceh->select("l.Port_CD, l.Port_CD + ' : ' + ISNULL( t.PortName, '' ) AS Port_Name");
        $this->ceh->join('TERMINALS t', 'l.Port_CD = (t.Nation_CD + t.Port_CD) AND l.YARD_ID = t.YARD_ID', 'left');
        $this->ceh->where(sprintf('l.LaneID IN (select LaneID from VESSEL_SCHEDULE WHERE ShipKey = \'%1$s\')', $shipkey));

        $this->ceh->where('l.YARD_ID', $this->yard_id);

        $this->ceh->order_by('l.Port_CD', 'ASC');
        $stmt = $this->ceh->get('LANE_FPOD l');
        return $stmt->result_array();
    }

    public function getLaneOprs($shipkey = '')
    {
        $this->ceh->select('CusID, LaneID');
        $this->ceh->where(sprintf('LaneID IN (select LaneID from VESSEL_SCHEDULE WHERE ShipKey = \'%1$s\')', $shipkey));

        $this->ceh->where('YARD_ID', $this->yard_id);

        $this->ceh->order_by('CusID', 'ASC');
        $stmt = $this->ceh->get('LANE_OPR');
        return $stmt->result_array();
    }

    public function getCargoTypes($cargo_id = '')
    {
        $this->ceh->select('Code, Description');
        if ($cargo_id != '') {
            $this->ceh->where('Code', $cargo_id);
        }
        $this->ceh->where('Code != ', '*');

        $this->ceh->where("YARD_ID", $this->yard_id);
        $this->ceh->order_by('Description', 'ASC');
        $stmt = $this->ceh->get('CARGO_TYPE');
        return $stmt->result_array();
    }

    private function getUnitRate($sz, $fe, $currency, $trf_code, $IsLocal)
    {
        $this->ceh->select('AMT_' . $fe . $sz . ' AMT');
        $this->ceh->where('CURRENCYID', $currency);
        $this->ceh->where('TRF_CODE', $trf_code);
        $this->ceh->where('IsLocal', $IsLocal);

        $this->ceh->where('YARD_ID', $this->yard_id);

        $stmt = $this->ceh->get('TRF_STD')->row_array();
        if (count($stmt) > 0) {
            return $stmt['AMT'];
        }
        return 0;
    }

    private function filter_trf_dis($inputs, $fwheres, $mskey)
    { //$mskey là khóa (tên cột) để xác định dòng/item sẽ được remove khỏi $inputs nếu k thỏa điêu kiện
        foreach ($fwheres as $k => $v) { //$k : col name, $v : col val
            $arrcol_val = array_column($inputs, $k, $mskey);
            if (in_array($fwheres[$k], $arrcol_val)) {
                foreach ($arrcol_val as $idx => $item) {
                    if ($fwheres[$k] == $item) continue; //thoa dieu kien filter
                    unset($inputs[$idx]);
                }
            } else {
                foreach ($arrcol_val as $idx => $item) {
                    if ($item == '*') continue;
                    unset($inputs[$idx]);
                }
            }
            if (count($inputs) > 1) {
                unset($fwheres[$k]);
                return $this->filter_trf_dis($inputs, $fwheres, $mskey);
            } else {
                return $inputs;
            }
        }
        return array();
    }

    public function getDiscount($sz, $fe, $wheres)
    {
        array_push($wheres, $this->yard_id);

        $sql = 'SELECT rowguid, AMT_' . $fe . $sz . ' AMT, FIX_RATE, Opr, PAYER, CARGO_TYPE, IX_CD, DMETHOD_CD, JOB_KIND, CNTR_JOB_TYPE, CURRENCYID, IsLocal FROM TRF_DIS';
        $sql .= ' WHERE ((EXPIRE_DATE IS NULL AND (APPLY_DATE=\'*\' OR (APPLY_DATE<>\'*\'
                            AND (CONVERT(datetime,CASE WHEN APPLY_DATE=\'*\' THEN \'1900-01-01\' ELSE APPLY_DATE END,103)) <= ? )))
                            OR (EXPIRE_DATE IS NOT NULL AND (EXPIRE_DATE >= ?) AND (APPLY_DATE=\'*\' OR (APPLY_DATE<>\'*\'
                            AND ? BETWEEN (CONVERT(datetime,CASE WHEN APPLY_DATE=\'*\' THEN \'1900-01-01\' ELSE APPLY_DATE END,103)) AND EXPIRE_DATE ))))';
        $sql .= ' AND (TRF_CODE = ? OR TRF_CODE = \'*\')';

        $sql .= ' AND (Opr = ? OR Opr = \'*\')';
        $sql .= ' AND (PAYER = ? OR PAYER = \'*\')';
        $sql .= ' AND (CARGO_TYPE = ? OR CARGO_TYPE = \'*\')';
        $sql .= ' AND (IX_CD = ? OR IX_CD = \'*\')';
        $sql .= ' AND (DMETHOD_CD = ? OR DMETHOD_CD = \'*\')';
        $sql .= ' AND (JOB_KIND = ? OR JOB_KIND = \'*\')';
        $sql .= ' AND (CNTR_JOB_TYPE = ? OR CNTR_JOB_TYPE = \'*\')';
        $sql .= ' AND (CURRENCYID = ? OR CURRENCYID = \'*\')';
        $sql .= ' AND (PAYMENT_TYPE = ? OR PAYMENT_TYPE = \'*\')';
        $sql .= ' AND (IsLocal = ? OR IsLocal = \'*\')';
        $sql .= ' AND (LANE = ? OR LANE = \'*\')';
        $sql .= ' AND (EQU_TYPE = \'*\')';
        $sql .= ' AND (YARD_ID = ?)';

        $sql .= ' ORDER BY OPR DESC,LANE DESC,PAYER_TYPE DESC,PAYER DESC,APPLY_DATE DESC';

        $stmt = $this->ceh->query($sql, $wheres);
        $stmt = $stmt->result_array();

        if (count($stmt) == 0) return 0;

        if (count($stmt) > 1) {
            $fwhere = array(
                'PAYER' => $wheres[5],
                'Opr' => $wheres[4],
                'CARGO_TYPE' => $wheres[6],
                'IX_CD' => $wheres[7],
                'DMETHOD_CD' => $wheres[8],
                'JOB_KIND' => $wheres[9],
                'CNTR_JOB_TYPE' => $wheres[10],
                'CURRENCYID' => $wheres[11],
                'IsLocal' => $wheres[12]
            );

            //đổi key của từng row trong $stmt thành giá trị của cột ID
            foreach ($stmt as $k => $v) {
                $stmt[$v['rowguid']] = $v;
                unset($stmt[$k]);
            }

            $temp = $this->filter_trf_dis($stmt, $fwhere, 'rowguid');
            if (count($temp) == 0) return 0;
            $temp = array_reverse($temp);
            $result = array_pop($temp);
        } else {
            if (count($stmt) == 1) {
                $result = $stmt[0];
            }
        }

        if (count($result) > 0) {
            $result = count(array_keys($result)) == 1 ? reset($result) : $result;
            if ($result['FIX_RATE'] == 1) {
                $unit_rate = $this->getUnitRate($sz, $fe, $wheres[8], $wheres[4], $wheres[9]);
                return $unit_rate * ($result['AMT'] !== null ? $result['AMT'] : 0) * 0.01;
            } else {
                return $result['AMT'] !== null ? $result['AMT'] : 0;
            }
        }

        return 0;
    }

    public function loadTariffSTD($listeir)
    {
        $sql = 'SELECT * FROM TRF_STD WHERE (CARGO_TYPE = ? OR CARGO_TYPE = \'*\') ';
        $sql .= ' AND (IX_CD = ? OR IX_CD = \'*\')';
        $sql .= ' AND (DMETHOD_CD = ?)';
        $sql .= ' AND (JOB_KIND = ? OR JOB_KIND = \'*\')';
        $sql .= ' AND (CNTR_JOB_TYPE = ? OR CNTR_JOB_TYPE = \'*\')';
        $sql .= ' AND (IsLocal = ? OR IsLocal = \'*\')';
        $sql .= ' AND ((CONVERT(date, ?, 104) >= CONVERT(date, FROM_DATE, 104) and TO_DATE = \'*\') or
	                (CONVERT(date, ?, 104) between CONVERT(date, FROM_DATE, 104) AND CONVERT(date, TO_DATE, 104)))';

        $sql .= ' AND (YARD_ID = ?)';

        $final_result = array();
        if (isset($listeir) && is_array($listeir)) {
            $resArr = array();
            foreach ($listeir as $item) {
                $temp1 = array_filter($resArr, function ($v, $k) use ($item) {
                    return $v['CARGO_TYPE'] == $item['CARGO_TYPE']
                        && $v['CntrClass'] == $item['CntrClass']
                        && $v['CJMode_CD'] == $item['CJMode_CD']
                        && $v['IsLocal'] == $item['IsLocal']
                        && $this->getContSize($v['ISO_SZTP']) == $this->getContSize($item['ISO_SZTP']);
                }, ARRAY_FILTER_USE_BOTH);

                if (count($temp1) == 0) {
                    $temp2 = array_filter($listeir, function ($v, $k) use ($item) {
                        return $v['CARGO_TYPE'] == $item['CARGO_TYPE']
                            && $v['CntrClass'] == $item['CntrClass']
                            && $v['CJMode_CD'] == $item['CJMode_CD']
                            && $v['IsLocal'] == $item['IsLocal']
                            && $this->getContSize($v['ISO_SZTP']) == $this->getContSize($item['ISO_SZTP']);
                    }, ARRAY_FILTER_USE_BOTH);
                    $item['CountOf'] = count($temp2);
                    array_push($resArr, $item);
                }
            }

            foreach ($resArr as $item) {
                $result = array();
                $JOB_KIND = ($item['CJMode_CD'] == 'LAYN' || $item['CJMode_CD'] == 'NTAU' || $item['CJMode_CD'] == 'CAPR')
                    ? "GO" : (($item['CJMode_CD'] == 'HBAI' || $item['CJMode_CD'] == 'TRAR') ? "GF" : "*");
                $wheres = array(
                    $item['CARGO_TYPE'],
                    (string)$item['CntrClass'],
                    $item['DMETHOD_CD'],
                    $JOB_KIND,
                    $item['CJMode_CD'],
                    $item['IsLocal'],
                    date('d/m/Y'),
                    date('d/m/Y'),
                    $this->yard_id
                );

                $stmt = $this->ceh->query($sql, $wheres);
                $stmt = $stmt->result_array();

                if (count($stmt) > 1) {
                    $fwhere = array(
                        'CARGO_TYPE' => $item['CARGO_TYPE'],
                        'IX_CD' => $item['CntrClass'],
                        'DMETHOD_CD' => $item['DMETHOD_CD'],
                        'JOB_KIND' => $JOB_KIND,
                        'CNTR_JOB_TYPE' => $item['CJMode_CD'],
                        'IsLocal' => $item['IsLocal']
                    );
                    //đổi key của từng row trong $stmt thành giá trị của cột rowguid
                    foreach ($stmt as $k => $v) {
                        $stmt[$v['rowguid']] = $v;
                        unset($stmt[$k]);
                    }

                    $temp = $this->filter_trf_dis($stmt, $fwhere, 'rowguid');
                    if (count($temp) == 0) continue;
                    $temp = array_reverse($temp);
                    $result = array_pop($temp);
                } else {
                    if (count($stmt) == 1) {
                        $result = $stmt[0];
                    }
                }

                // $ordNo = isset($item['EIRNo']) ? $item['EIRNo'] : $item['SSOderNo'];

                if (count($result) > 0) {
                    // $result['OrderNo'] = $ordNo;
                    $result['CJMode_CD'] = $item['CJMode_CD'];
                    $result['ISO_SZTP'] = $item['ISO_SZTP'];
                    $result['FE'] = $item['Status'];
                    $result['CntrNo'] = $item['CntrNo'];
                    $result['OprID'] = $item['OprID'];
                    $result['IssueDate'] = $item['IssueDate'];

                    $result['LANE'] = isset($item['LaneID']) ? $item['LaneID'] : '';
                    for ($n = 1; $n <= $item['CountOf']; $n++) {
                        array_push($final_result, $result);
                    }
                } else {
                    $cjmode = $item['CJMode_CD'];
                    $cargoType = $item['CARGO_TYPE'];
                    array_push($final_result, "[$cjmode]/[$cargoType] không tìm thấy biểu cước phù hợp!");
                }
            }
        }

        // danh dau them truong RemarkContainer de luu vao inv_draft_Detail
        foreach ($final_result as $key => $p) {
            $remarkCntrs = array_filter($listeir, function ($v, $k) use ($p) {
                return ($v['CJMode_CD'] == $p['CNTR_JOB_TYPE'])
                    && ($v['CARGO_TYPE'] == $p['CARGO_TYPE'] || $p['CARGO_TYPE'] == '*')
                    && ($v['CntrClass'] == $p['IX_CD'] || $p['IX_CD'] == '*')
                    && ($v['IsLocal'] == $p['IsLocal'] || $p['IsLocal'] == '*')
                    && $this->getContSize($v['ISO_SZTP']) == $this->getContSize($p['ISO_SZTP']);
            }, ARRAY_FILTER_USE_BOTH);

            $cntrs = implode(', ', array_unique(array_column($remarkCntrs, 'CntrNo')));
            $final_result[$key]['Remark'] = $cntrs;

            //lấy số bill | book add sẵn vào lưới cước để phát hành hđ + lưu vào cột TRF_DESC_MORE 
            $blnos = array_unique(array_column($remarkCntrs, 'BLNo'));
            $bknos = array_unique(array_column($remarkCntrs, 'BookingNo'));
            $bnos = array_unique(array_column($remarkCntrs, 'BookNo'));

            $mores = count($blnos) > 0 ? $blnos : (count($bknos) > 0 ? $bknos : $bnos);
            if (count($mores) > 0) {
                $final_result[$key]['TRF_DESC_MORE'] = implode(', ', $mores);
            }
        }
        return $final_result;
    }

    public function loadServiceTariff($listeir)
    {
        $sql = 'SELECT * FROM TRF_STD WHERE (CNTR_JOB_TYPE = ?)';
        $sql .= ' AND ((CONVERT(date, ?, 104) >= CONVERT(date, FROM_DATE, 104) and TO_DATE = \'*\') or
                    (CONVERT(date, ?, 104) between CONVERT(date, FROM_DATE, 104) AND CONVERT(date, TO_DATE, 104)))';

        $sql .= ' AND (YARD_ID = ?)';

        $final_result = array();
        if (isset($listeir) && is_array($listeir)) {
            $resArr = array();
            foreach ($listeir as $item) {
                $temp1 = array_filter($resArr, function ($v, $k) use ($item) {
                    return $v['CARGO_TYPE'] == $item['CARGO_TYPE']
                        && $v['CntrClass'] == $item['CntrClass']
                        && $v['DMETHOD_CD'] == $item['DMETHOD_CD']
                        && $v['CJMode_CD'] == $item['CJMode_CD']
                        && $v['IsLocal'] == $item['IsLocal']
                        && $this->getContSize($v['ISO_SZTP']) == $this->getContSize($item['ISO_SZTP']);
                }, ARRAY_FILTER_USE_BOTH);

                if (count($temp1) == 0) {
                    $temp2 = array_filter($listeir, function ($v, $k) use ($item) {
                        return $v['CARGO_TYPE'] == $item['CARGO_TYPE']
                            && $v['CntrClass'] == $item['CntrClass']
                            && $v['DMETHOD_CD'] == $item['DMETHOD_CD']
                            && $v['CJMode_CD'] == $item['CJMode_CD']
                            && $v['IsLocal'] == $item['IsLocal']
                            && $this->getContSize($v['ISO_SZTP']) == $this->getContSize($item['ISO_SZTP']);
                    }, ARRAY_FILTER_USE_BOTH);
                    $item['CountOf'] = count($temp2);
                    array_push($resArr, $item);
                }
            }

            foreach ($resArr as $item) {
                $result = array();
                $wheres = array(
                    $item['CJMode_CD'],
                    date('d/m/Y'),
                    date('d/m/Y'),

                    $this->yard_id
                );

                $stmt = $this->ceh->query($sql, $wheres);
                $stmt = $stmt->result_array();

                if (count($stmt) > 1) {
                    $fwhere = array(
                        'CARGO_TYPE' => $item['CARGO_TYPE'],
                        'IX_CD' => $item['CntrClass'],
                        'DMETHOD_CD' => isset($item['DMETHOD_CD']) ? $item['DMETHOD_CD'] : "*",
                        'JOB_KIND' => '*',
                        'CNTR_JOB_TYPE' => $item['CJMode_CD'],
                        'IsLocal' => $item['IsLocal'],
                        'CURRENCYID' => 'USD'
                    );

                    //đổi key của từng row trong $stmt thành giá trị của cột rowguid
                    foreach ($stmt as $k => $v) {
                        $stmt[$v['rowguid']] = $v;
                        unset($stmt[$k]);
                    }

                    $temp = $this->filter_trf_dis($stmt, $fwhere, 'rowguid');
                    if (count($temp) == 0) continue;
                    $temp = array_reverse($temp);
                    $result = array_pop($temp);
                } else {
                    if (count($stmt) == 1) {
                        $result = $stmt[0];
                    }
                }

                if (count($result) > 0) {
                    $result['CntrNo'] = $item['CntrNo'];
                    $result['CJMode_CD'] = $item['CJMode_CD'];
                    $result['ISO_SZTP'] = $item['ISO_SZTP'];
                    $result['FE'] = $item['Status'];
                    $result['CntrNo'] = $item['CntrNo'];
                    $result['OprID'] = $item['OprID'];
                    $result['IssueDate'] = isset($item['IssueDate']) ? $item['IssueDate'] : date("Y-m-d H:i:s");

                    for ($n = 1; $n <= $item['CountOf']; $n++) {
                        array_push($final_result, $result);
                    }
                } else {
                    $cjmode = $item['CJMode_CD'];
                    array_push($final_result, "[$cjmode] Không tìm thấy biểu cước phù hợp!");
                }
            }
        }

        // danh dau them truong RemarkContainer de luu vao inv_draft_Detail
        foreach ($final_result as $key => $p) {
            $remarkCntrs = array_filter($listeir, function ($v, $k) use ($p) {
                return ($v['CJMode_CD'] == $p['CNTR_JOB_TYPE']);
            }, ARRAY_FILTER_USE_BOTH);

            $cntrs = implode(', ', array_unique(array_column($remarkCntrs, 'CntrNo')));
            $final_result[$key]['Remark'] = $cntrs;

            //lấy số bill | book add sẵn vào lưới cước để phát hành hđ + lưu vào cột TRF_DESC_MORE 
            $blnos = array_unique(array_column($remarkCntrs, 'BLNo'));
            $bknos = array_unique(array_column($remarkCntrs, 'BookingNo'));
            $bnos = array_unique(array_column($remarkCntrs, 'BookNo'));

            $mores = count($blnos) > 0 ? $blnos : (count($bknos) > 0 ? $bknos : $bnos);
            if (count($mores) > 0) {
                $final_result[$key]['TRF_DESC_MORE'] = implode(', ', $mores);
            }
        }

        return $final_result;
    }

    public function loadTariffByTemplate($temp)
    {
        $sql = 'SELECT t.*, c.INV_UNIT FROM TRF_STD t';
        $sql .= ' LEFT JOIN TRF_CODES c ON c.TRF_CODE = t.TRF_CODE AND c.YARD_ID = t.YARD_ID';
        $sql .= ' WHERE t.rowguid IN ( SELECT STD_ROW_ID FROM INV_TPLT WHERE TPLT_NM = ?)';
        $sql .= ' AND ((CONVERT(date, ?, 104) >= CONVERT(date, FROM_DATE, 104) and TO_DATE = \'*\') or
	                (CONVERT(date, ?, 104) between CONVERT(date, FROM_DATE, 104) AND CONVERT(date, TO_DATE, 104)))';

        $sql .= ' AND (t.YARD_ID = ?)';

        $wheres = array(
            $temp,
            date('d/m/Y'),
            date('d/m/Y'),
            $this->yard_id
        );

        $stmt = $this->ceh->query($sql, $wheres);
        return $stmt->result_array();
    }

    public function getTRF_unitCode($tarriffcode)
    {
        $stmt = $this->ceh->select('INV_UNIT')
            ->where('TRF_CODE', $tarriffcode)
            ->where('YARD_ID', $this->yard_id)
            ->limit(1)
            ->get('TRF_CODES')->row_array();
        return $stmt['INV_UNIT'];
    }

    public function loadBooking($args = array())
    {

        $assignedCont = <<<EOT
        SELECT distinct ',' + CntrNo FROM CNTR_DETAILS cd
            WHERE cd.BookingNo = eb.BookingNo 
                AND cd.CntrClass = 2
                AND cd.Status = 'E'
                AND cd.YARD_ID = eb.YARD_ID AND cd.LocalSZPT = eb.LocalSZPT AND cd.OprID = eb.OprID FOR XML PATH('')
EOT;

        $assignedContRowguid = <<<EOT
        SELECT distinct ',' + CAST(cd.rowguid AS varchar(MAX)) FROM CNTR_DETAILS cd
            WHERE cd.BookingNo = eb.BookingNo
                AND cd.CntrClass = 2
                AND cd.Status = 'E'
                AND cd.YARD_ID = eb.YARD_ID AND cd.LocalSZPT = eb.LocalSZPT AND cd.OprID = eb.OprID FOR XML PATH('')
EOT;

        $this->ceh->select("eb.rowguid, BOOK_STATUS, BookingNo, BookingDate, ExpDate, OprID, LocalSZPT, ISO_SZTP, BookAmount, StackingAmount, ShipName
                            , (" . $assignedCont . ") AS AssignedCont, isAssignCntr, (" . $assignedContRowguid . ") AS AttachCont
                            , CARGO_TYPE, ct.Description
                            , eb.VoyAge, eb.POL, eb.POD, eb.FPOD, eb.CmdID, eb.Temperature, eb.DG_CD, eb.Note, eb.CreatedBy, eb.VesselName");
        $this->ceh->join("CARGO_TYPE ct", "ct.Code = eb.CARGO_TYPE AND ct.YARD_ID = eb.YARD_ID", "left");

        foreach ($args as $key => $value) {
            if (!empty($value)) {
                switch ($key) {
                    case 'FromDate':
                        $this->ceh->where("BookingDate >=", $this->funcs->dbDateTime($value));
                        break;
                    case 'ToDate':
                        $this->ceh->where("BookingDate <=", $this->funcs->dbDateTime($value . " 23:59:59"));
                        break;
                    case 'ShipName':
                        $this->ceh->like("ShipName", UNICODE . $value);
                        break;
                    default:
                        $this->ceh->where($key, $value);
                        break;
                }
            }
        }

        $this->ceh->where("eb.YARD_ID", $this->yard_id);
        $this->ceh->order_by("BookingNo", "ASC");

        $stmt = $this->ceh->get("EMP_BOOK eb");

        return $stmt->result_array();
    }

    public function getBookingList($bkno = '', $cntrno = '')
    {
        if ($bkno == '' && $cntrno == '') {
            return array("error" => "Vui lòng nhập thông tin truy vấn!");
        }

        $incont = array();
        if ($cntrno != '') {
            $incont = $this->ceh->select('BookingNo')
                ->where('CntrNo', $cntrno)
                ->where('CntrClass', 2)
                ->where('Status', 'E')
                ->where('DateOut IS NULL')
                ->where('BookingNo IS NOT NULL')
                ->where('YARD_ID', $this->yard_id)
                ->get('CNTR_DETAILS');
            $incont = $incont->result_array();
            if (count($incont) == 0) {
                return array("error" => "Container này chưa được đăng ký Booking!");
            }
        }

        if ($bkno != '') {
            $bktemp = $this->ceh->select('BOOK_STATUS, BookingNo, LocalSZPT, ISO_SZTP, BookingDate, ExpDate, CARGO_TYPE, POD, Description
                                            , Status, BookAmount, StackingAmount, OprID, isAssignCntr')
                ->join("CARGO_TYPE ct", "ct.Code = bk.CARGO_TYPE AND ct.YARD_ID = bk.YARD_ID", "left")
                ->where('bk.BookingNo', $bkno)
                // ->where('ExpDate >=', date('Y-m-d H:i:s'))
                ->where('bk.YARD_ID', $this->yard_id)
                ->get('EMP_BOOK bk')->result_array();

            if (count($bktemp) == 0) {
                return array("error" => "Số Booking này không tồn tại! <br/>Vui lòng kiểm tra lại!");
            }

            if ($bktemp[0]['isAssignCntr'] == "N") {
                // if($bktemp[0]['ExpDate'] < date("Y-m-d H:i:s")){
                //     return array("error" => "Booking này đã hết hạn!");
                // }

                // if($bktemp[0]['StackingAmount'] == $bktemp[0]['BookAmount']){
                //     return array("error" => "Booking hết chỗ!");
                // }

                return $bktemp;
            }
        }

        $this->ceh->select('cn.rowguid AS RowguidCntrDetails, cn.rowguid AS Rowguid_CntrDetails, bk.BOOK_STATUS, bk.BookingNo, bk.LocalSZPT, bk.ISO_SZTP, BookingDate, bk.ExpDate
                            , ISNULL(bk.Status, cn.Status) Status, BookAmount, StackingAmount, bk.OprID, cn.CARGO_TYPE, cn.CntrNo, cn.CntrClass
                            , cn.SealNo, cn.IsLocal, cn.CMDWeight, cn.Note, cTLHQ, cn.cBlock, cn.cBay, cn.cRow, cn.cTier, cn.cArea, ContCondition
                            , isAssignCntr, bk.CARGO_TYPE, ct.Description, cn.ShipKey, cn.BerthDate, cn.ShipID, cn.ShipYear, cn.ShipVoy, cn.BerthDate
                            , cn.EIRNo, cn.BLNo, cn.DateIn, DateOut, (CASE cn.VGM WHEN 1 THEN 1 ELSE 0 END) VGM, cn.SealNo1, cn.SealNo2
                            , bk.Temperature, cn.CJMode_CD, dm.CJModeName, cn.ImVoy, cn.ExVoy, bk.CmdID, bk.POD, cn.FPOD, cn.Port_CD, cn.OOG_TOP
                            , cn.OOG_LEFT, cn.OOG_RIGHT, cn.OOG_BACK, cn.OOG_FRONT, cn.CLASS, cn.UNNO, dm1.ischkCFS
                            , cn.CJMode_OUT_CD, cn.SSOderNo, cn.Ter_Hold_CHK, \'NULL\' AS bXNVC'); //, e.bXNVC
        $this->ceh->join('Cntr_Details cn', 'cn.BookingNo = bk.BookingNo AND cn.OprID = bk.OprID AND cn.ISO_SZTP = bk.ISO_SZTP and cn.YARD_ID = bk.YARD_ID', 'left');
        $this->ceh->join('DELIVERY_MODE dm', 'dm.CJMode_CD = cn.CJMode_CD AND dm.YARD_ID = cn.YARD_ID', 'LEFT');
        $this->ceh->join('DELIVERY_MODE dm1', 'dm1.CJMode_CD = cn.CJMode_OUT_CD and dm1.YARD_ID = cn.YARD_ID', 'LEFT');
        $this->ceh->join('CARGO_TYPE ct', 'ct.Code = bk.CARGO_TYPE AND ct.YARD_ID = bk.YARD_ID', 'LEFT');
        // $this->ceh->join('EIR e', 'e.CntrNo = cn.CntrNo AND e.CntrClass = cn.CntrClass AND e.ShipKey = cn.ShipKey
        //                                                 AND e.OprID = cn.OprID AND e.YARD_ID = cn.YARD_ID' , 'LEFT');
        $this->ceh->where('bk.YARD_ID', $this->yard_id);
        // $this->ceh->where('cn.CntrClass', '2');
        // $this->ceh->where('bk.ExpDate >=', date('Y-m-d H:i:s'));
        // $this->ceh->where('CMStatus', 'S');
        // $this->ceh->where('DateOut IS NULL');
        // $this->ceh->where('EIRNo IS NULL');

        $this->ceh->where('isAssignCntr', 'Y');

        //nếu filter theo số book
        if ($cntrno == '') {
            $this->ceh->where('cn.Status', 'E');
            $this->ceh->where('cn.CntrClass', '2');
            $this->ceh->where('CMStatus', 'S');
            $this->ceh->where('DateOut IS NULL');

            // $this->ceh->group_start();
            // $this->ceh->where('cn.EIRNo IS NULL');
            // $this->ceh->or_where('e.bXNVC', '1');
            // $this->ceh->group_end();

            // $this->ceh->where('(ContCondition IN(\'A\',\'B\') OR ContCondition IS NULL)');

            $this->ceh->where('bk.BookingNo', $bkno);
        }

        //nếu filter theo số cont
        if ($bkno == '') {
            $this->ceh->where_in('bk.BookingNo', array_column($incont, 'BookingNo'));
            $this->ceh->where('cn.CntrClass', 2);
            $this->ceh->where('cn.Status', 'E');
            $this->ceh->where('cn.DateOut IS NULL');
        }

        // log_message( "error", $this->ceh->get_compiled_select('EMP_BOOK bk', TRUE) );

        // return array();
        $stmt = $this->ceh->get('EMP_BOOK bk')->result_array();

        if (count($stmt) > 0) {
            if ($cntrno != '') {
                $key = array_search($cntrno, array_column($stmt, 'CntrNo'));

                if ($stmt[$key]["BOOK_STATUS"] == 'C') {
                    return array("error" => "Booking này đã huỷ!");
                }

                if ($stmt[$key]["ExpDate"] < date('Y-m-d H:i:s')) {
                    return array("error" => "Booking này đã hết hạn!");
                }

                if ($stmt[$key]["CntrClass"] != '2' || $stmt[$key]["Status"] != "E") {
                    return array("error" => "Chỉ có container LƯU RỖNG [Storage Empty] mới được phép cấp lệnh!");
                }

                if ($stmt[$key]["DateOut"] !== NULL) {
                    return array("error" => "Container đã ra khỏi bãi không thể cấp lệnh!");
                }

                if ($stmt[$key]["CJMode_OUT_CD"] !== NULL && in_array($stmt[$key]["ischkCFS"], array("1", "2", "3"))) {
                    return array("error" => "Container đã được cấp lệnh đóng/rút/sang cont số [" . $stmt[$key]["SSOderNo"] . "]!");
                }

                // if ($stmt[$key]["ContCondition"] !== NULL && !in_array($stmt[$key]["ContCondition"], array("A", "B"))) {
                //     return array("error" => "Trạng thái Container không đủ điều kiện cấp lệnh!");
                // }

                if ($stmt[$key]["EIRNo"] !== NULL) {
                    $checkXNVC = $this->ceh->select('bXNVC')->where('EIRNo', $stmt[$key]["EIRNo"])->where('CntrNo', $stmt[$key]["CntrNo"])->where('YARD_ID', $this->yard_id)->limit(1)->get('EIR')->row_array();
                    $stmt[$key]["bXNVC"] = $checkXNVC['bXNVC'];
                    if ($checkXNVC !== NULL && $checkXNVC['bXNVC'] != '1') {
                        return array("error" => "Container đã được cấp lệnh số [" . $stmt[$key]["EIRNo"] . "]!");
                    }
                }
            } else {
                $stmt = array_map(function ($dt) {
                    $checkXNVC = $this->ceh->select('bXNVC')->where('EIRNo', $dt["EIRNo"])->where('CntrNo', $dt["CntrNo"])->where('YARD_ID', $this->yard_id)->limit(1)->get('EIR')->row_array();
                    $dt["bXNVC"] = empty($checkXNVC) ? "0" : $checkXNVC['bXNVC'];
                    return $dt;
                }, $stmt);
            }
        } else {
            return array("error" => "Số booking nhập vào không đúng hoặc không thỏa mãn các điều kiện làm lệnh! <br>Tạo lệnh theo từng số Container để biết chi tiết hoặc kiểm tra lại thông tin booking ở chức năng [Thủ Tục / Đăng ký booking]!");
        }

        return $stmt;
    }

    public function updateOrder_byRenewed($args)
    {
        foreach ($args as $arg) {
            $updateItem = array();

            if ($arg["NewExpDate"] != "") {
                $updateItem["ExpDate"] = $this->funcs->dbDateTime($arg["NewExpDate"]);
            }

            if ($arg["NewExpPluginDate"] != "") {
                $updateItem["ExpPluginDate"] =  $this->funcs->dbDateTime($arg["NewExpPluginDate"]);
            }

            $updateItem["update_time"] = date("Y-m-d H:i:s");

            $tblUpdate = $arg["OrderType"] == "NH" ? "EIR" : "SRV_ODR";

            $this->ceh->where("rowguid", $arg["rowguid"])->update($tblUpdate, $updateItem);

            if ($arg["OrderType"] == "NH") {
                if (isset($updateItem["ExpDate"])) {
                    $whereGateMonitor = array(
                        "EIRNo" => $arg["OrderNo"],
                        "CntrNo" => $arg["CntrNo"],
                        "CJMode_CD" => $arg["CJMode_CD"],
                        "PinCode" => $arg["PinCode"],
                        "YARD_ID" => $this->yard_id
                    );

                    $u = array(
                        "ExpDate" => $updateItem["ExpDate"],
                        "update_time" => date("Y-m-d H:i:s")
                    );

                    $this->ceh->where($whereGateMonitor)->update("GATE_MONITOR", $u);
                }
            } else {
                if (isset($updateItem["ExpPluginDate"])) {
                    $u = array(
                        "expplugindate" => $updateItem["ExpPluginDate"],
                        "update_time" => date("Y-m-d H:i:s")
                    );
                    $this->ceh->where("MASTER_ROWGUID", $arg["MASTER_ROWGUID"])
                        ->update("RF_ONOFF", $u);
                }
            }
        }

        return 'success';
    }

    public function saveSplitDraft($args, $order)
    {
        if (!is_array($args) || count($args) == 0) return true;

        $draft_details = array();
        if (isset($args['draft_detail']) && count($args['draft_detail'])) {
            $draft_details = $args['draft_detail'];
        }

        $draft_total = array();
        if (isset($args['draft_total']) && count($args['draft_total'])) {
            $draft_total = $args['draft_total'];
        }

        $draftMarker = $args["DRAFT_MARKER"];

        $inv_draft = array();

        foreach ($draftMarker as $key => $item) {
            //get inv draft
            $amount = array_sum(array_map(function ($dt) use ($key) {
                return $dt["CNTR_JOB_TYPE"] == $key ? (float)str_replace(',', '', $dt['AMOUNT']) : 0;
            }, $draft_details));

            $vat = array_sum(array_map(function ($dt) use ($key) {
                return $dt["CNTR_JOB_TYPE"] == $key ? (float)str_replace(',', '', $dt['VAT']) : 0;
            }, $draft_details));

            $disAMT = array_sum(array_map(function ($dt) use ($key) {
                return $dt["CNTR_JOB_TYPE"] == $key ? (float)str_replace(',', '', $dt['extra_rate']) : 0;
            }, $draft_details));

            $totalAMT = array_sum(array_map(function ($dt) use ($key) {
                return $dt["CNTR_JOB_TYPE"] == $key ? (float)str_replace(',', '', $dt['TAMOUNT']) : 0;
            }, $draft_details));

            $inv_draft_item = array(
                "DRAFT_INV_NO" => $item["DRAFT_INV_NO"],
                "INV_NO" => NULL,
                "DRAFT_INV_DATE" => date('Y-m-d H:i:s'),
                "REF_NO" => $item['REF_NO'],
                "ShipKey" => $order['ShipKey'],
                "ShipID" => $order['ShipID'],
                "ShipYear" => $order['ShipYear'],
                "ShipVoy" => $order['ShipVoy'],
                "PAYER_TYPE" => $order['PAYER_TYPE'],
                "PAYER" => $order['CusID'],
                "OPR" => $order['OprID'],
                "AMOUNT" => $amount,
                "VAT" => $vat,
                "DIS_AMT" => $disAMT,
                "PAYMENT_STATUS" => $order['PAYMENT_TYPE'] == "C" ? "U" : "Y",
                "REF_TYPE" => isset($draft_total['PUBLISH_BY']) ? $draft_total['PUBLISH_BY'] : NULL,
                "CURRENCYID" => $draft_details[0]["CURRENCYID"],
                "RATE" => 1,
                "INV_TYPE" => $order['PAYMENT_TYPE'] == "C" ? "CRE" : "CAS",
                "INV_TYPE_2" => "L",
                "TPLT_NM" => "EB",
                "TAMOUNT" => $totalAMT,
                "PinCode" => isset($item['PinCode']) ? $item['PinCode'] : NULL,
                "ACC_CD" => isset($draft_total['ACC_CD']) ? $draft_total['ACC_CD'] : NULL,
                "YARD_ID" => $this->yard_id,
                "ModifiedBy" => $this->session->userdata("UserID"),
                "update_time" => date('Y-m-d H:i:s'),
                "CreatedBy" => $this->session->userdata("UserID")
            );

            array_push($inv_draft, $inv_draft_item);
        }

        //get inv draft details
        $inv_draft_details = array();
        foreach ($draft_details as $idx => $dd) {

            if (!isset($draftMarker[$dd["CNTR_JOB_TYPE"]])) continue;

            $draftno = $draftMarker[$dd["CNTR_JOB_TYPE"]]["DRAFT_INV_NO"];

            $dd['DRAFT_INV_NO'] = $draftno;
            $dd['SEQ'] = $idx;
            $dd['SZ'] =  $this->getContSize($dd['ISO_SZTP']);
            $dd['DIS_AMT'] = (float)str_replace(',', '', $dd['extra_rate']);
            $dd['standard_rate'] = (float)str_replace(',', '', $dd['standard_rate']);
            $dd['DIS_RATE'] = (float)str_replace(',', '', $dd['DIS_RATE']);
            $dd['extra_rate'] = (float)str_replace(',', '', $dd['extra_rate']);
            $dd['UNIT_RATE'] = (float)str_replace(',', '', $dd['UNIT_RATE']);
            $dd['AMOUNT'] = (float)str_replace(',', '', $dd['AMOUNT']);
            $dd['VAT'] = (float)str_replace(',', '', $dd['VAT']);
            $dd['TAMOUNT'] = (float)str_replace(',', '', $dd['TAMOUNT']);
            $dd['TRF_DESC'] = UNICODE . $dd['TRF_DESC'];

            $dd['GRT'] = 1;
            $dd['SOGIO'] = 1;
            $dd['ModifiedBy'] = $this->session->userdata("UserID");
            $dd['CreatedBy'] = $this->session->userdata("UserID");
            $dd['update_time'] = date('Y-m-d H:i:s');

            $dd['REF_NO'] = $args['DRAFT_MARKER'][$dd['CNTR_JOB_TYPE']]['REF_NO'];
            if (!isset($dd['TRF_DESC_MORE']) || empty($dd['TRF_DESC_MORE']) || $dd['TRF_DESC_MORE'] === '') {
                $dd['TRF_DESC_MORE'] = isset($order['BLNo']) ? $order['BLNo'] : (isset($order['BookingNo']) ? $order['BookingNo'] : (isset($order['BookNo']) ? $order['BookNo'] : ''));
            }

            unset($dd['JobMode'], $dd['ISO_SZTP'], $dd['CURRENCYID']);
            array_push($inv_draft_details, $dd);
        }

        //get inv Cont
        $this->ceh->trans_start();
        $this->ceh->trans_strict(FALSE);

        foreach ($inv_draft as $item) {
            $this->ceh->insert('INV_DFT', $item);
        }

        foreach ($inv_draft_details as $item) {
            $item["YARD_ID"] = $this->yard_id;
            $this->ceh->insert('INV_DFT_DTL', $item);
        }

        $this->ceh->trans_complete();

        if ($this->ceh->trans_status() === FALSE) {
            $this->ceh->trans_rollback();
            return FALSE;
        } else {
            $this->ceh->trans_commit();
            return TRUE;
        }
    }

    public function saveInvoice($args, $order, $cntrRowguids = array(), $isAttachSrv = false)
    {
        if (!is_array($args) || count($args) == 0) return true;

        $draft_details = array();
        if (isset($args['draft_detail']) && count($args['draft_detail'])) {
            $draft_details = $args['draft_detail'];
        }

        $draft_total = array();
        if (isset($args['draft_total']) && count($args['draft_total'])) {
            $draft_total = $args['draft_total'];
        }

        $invPrefix = isset($args["INV_CONTENT"]["INV_PREFIX"]) ? $args["INV_CONTENT"]["INV_PREFIX"] : "";
        $invNoPre = isset($args["INV_CONTENT"]["INV_NO_PRE"]) ? $args["INV_CONTENT"]["INV_NO_PRE"] : "";
        $invDate = isset($args["INV_CONTENT"]["INV_DATE"]) ? $args["INV_CONTENT"]["INV_DATE"] : date('Y-m-d H:i:s');
        $draftno = $args["INV_CONTENT"]["DRAFT_NO"];
        $pincode = $args["INV_CONTENT"]["PIN_CODE"];

        //get inv draft
        $draftMarker = $args["DRAFT_MARKER"];

        $inv_draft = array();

        foreach ($draftMarker as $key => $item) {
            //get inv draft
            $amount = array_sum(array_map(function ($dt) use ($key) {
                return $dt["CNTR_JOB_TYPE"] == $key ? (float)str_replace(',', '', $dt['AMOUNT']) : 0;
            }, $draft_details));

            $vat = array_sum(array_map(function ($dt) use ($key) {
                return $dt["CNTR_JOB_TYPE"] == $key ? (float)str_replace(',', '', $dt['VAT']) : 0;
            }, $draft_details));

            $disAMT = array_sum(array_map(function ($dt) use ($key) {
                return $dt["CNTR_JOB_TYPE"] == $key ? (float)str_replace(',', '', $dt['extra_rate']) : 0;
            }, $draft_details));

            $totalAMT = array_sum(array_map(function ($dt) use ($key) {
                return $dt["CNTR_JOB_TYPE"] == $key ? (float)str_replace(',', '', $dt['TAMOUNT']) : 0;
            }, $draft_details));

            $inv_draft_item = array(
                "DRAFT_INV_NO" => $item["DRAFT_INV_NO"],
                "INV_NO" => $invPrefix . $invNoPre != "" ? $invPrefix . $invNoPre : NULL,
                "DRAFT_INV_DATE" => date('Y-m-d H:i:s'),
                "REF_NO" => $item['REF_NO'],
                "ShipKey" => $order['ShipKey'],
                "ShipID" => $order['ShipID'],
                "ShipYear" => $order['ShipYear'],
                "ShipVoy" => $order['ShipVoy'],
                "PAYER_TYPE" => $order['PAYER_TYPE'],
                "PAYER" => $order['CusID'],
                "OPR" => $order['OprID'],
                "AMOUNT" => $amount,
                "VAT" => $vat,
                "DIS_AMT" => $disAMT,
                "PAYMENT_STATUS" => $order['PAYMENT_TYPE'] == "C" ? "U" : "Y",
                "REF_TYPE" => isset($draft_total['PUBLISH_BY']) ? $draft_total['PUBLISH_BY'] : NULL,
                "CURRENCYID" => $draft_details[0]["CURRENCYID"],
                "RATE" => 1,
                "INV_TYPE" => $order['PAYMENT_TYPE'] == "C" ? "CRE" : "CAS",
                "INV_TYPE_2" => "L",
                "TPLT_NM" => "EB",
                "TAMOUNT" => $totalAMT,
                "PinCode" => isset($item['PinCode']) ? $item['PinCode'] : NULL,
                "ACC_CD" => isset($draft_total['ACC_CD']) ? $draft_total['ACC_CD'] : NULL,
                "YARD_ID" => $this->yard_id,
                "ModifiedBy" => $this->session->userdata("UserID"),
                "update_time" => date('Y-m-d H:i:s'),
                "CreatedBy" => $this->session->userdata("UserID")
            );

            array_push($inv_draft, $inv_draft_item);
        }

        //get inv draft details
        $inv_draft_details = array();
        foreach ($draft_details as $idx => $dd) {
            if (!isset($draftMarker[$dd["CNTR_JOB_TYPE"]])) continue;

            $draftno = $draftMarker[$dd["CNTR_JOB_TYPE"]]["DRAFT_INV_NO"];
            $dd['DRAFT_INV_NO'] = $draftno;
            $dd['SEQ'] = $idx;
            $dd['SZ'] =  $this->getContSize($dd['ISO_SZTP']);
            $dd['DIS_AMT'] = (float)str_replace(',', '', $dd['extra_rate']);
            $dd['standard_rate'] = (float)str_replace(',', '', $dd['standard_rate']);
            $dd['DIS_RATE'] = (float)str_replace(',', '', $dd['DIS_RATE']);
            $dd['extra_rate'] = (float)str_replace(',', '', $dd['extra_rate']);
            $dd['UNIT_RATE'] = (float)str_replace(',', '', $dd['UNIT_RATE']);
            $dd['AMOUNT'] = (float)str_replace(',', '', $dd['AMOUNT']);
            $dd['VAT'] = (float)str_replace(',', '', $dd['VAT']);
            $dd['TAMOUNT'] = (float)str_replace(',', '', $dd['TAMOUNT']);
            $dd['TRF_DESC'] = UNICODE . $dd['TRF_DESC'];

            $dd['GRT'] = 1;
            $dd['SOGIO'] = 1;
            $dd['ModifiedBy'] = $this->session->userdata("UserID");
            $dd['CreatedBy'] = $this->session->userdata("UserID");
            $dd['update_time'] = date('Y-m-d H:i:s');

            $dd['REF_NO'] = $args['DRAFT_MARKER'][$dd['CNTR_JOB_TYPE']]['REF_NO'];
            // if (!isset($dd['TRF_DESC_MORE']) || empty($dd['TRF_DESC_MORE']) || $dd['TRF_DESC_MORE'] === '') {
            // $dd['TRF_DESC_MORE'] = isset($order['BLNo']) ? $order['BLNo'] : (isset($order['BookingNo']) ? $order['BookingNo'] : (isset($order['BookNo']) ? $order['BookNo'] : ''));
            // }

            unset($dd['JobMode'], $dd['ISO_SZTP'], $dd['CURRENCYID']);
            array_push($inv_draft_details, $dd);
        }

        //get inv VAT
        if ($invPrefix . $invNoPre != "" && !$isAttachSrv) {
            $inv_vat = array(
                "INV_NO" => $invPrefix . $invNoPre,
                "INV_DATE" => $this->funcs->dbDateTime($invDate),
                "REF_NO" => implode(", ", $args['REF_NOs']),
                "ShipKey" => $order['ShipKey'],
                "ShipID" => $order['ShipID'],
                "ShipYear" => $order['ShipYear'],
                "ShipVoy" => $order['ShipVoy'],
                "PAYER_TYPE" => $order['PAYER_TYPE'],
                "PAYER" => $order['CusID'],
                "OPR" => $order['OprID'],
                "isPosted" => '0',
                "AMOUNT" => (float)str_replace(',', '', $draft_total['AMOUNT']),
                "VAT" => (float)str_replace(',', '', $draft_total['VAT']),
                "DIS_AMT" => (float)str_replace(',', '', $draft_total['DIS_AMT']),
                "PAYMENT_STATUS" => "Y", //"U", //$order['PAYMENT_TYPE'] == "C" ? "U" : "Y",
                "REF_TYPE" => isset($draft_total['PUBLISH_BY']) ? $draft_total['PUBLISH_BY'] : NULL,
                "CURRENCYID" => $draft_details[0]["CURRENCYID"],
                "RATE" => 1,
                "INV_TYPE" => $order['PAYMENT_TYPE'] == "C" ? "CRE" : "CAS",
                "INV_TYPE_2" => "L",
                "TPLT_NM" => "EB",
                "PRINT_CHECK" => 0,
                "TAMOUNT" => (float)str_replace(',', '', $draft_total['TAMOUNT']),
                "ACC_CD" => isset($draft_total['ACC_CD']) ? $draft_total['ACC_CD'] : NULL,
                "INV_PREFIX" => $invPrefix,
                "INV_NO_PRE" => $invNoPre,
                "PinCode" => $pincode,
                "CreatedBy" => $this->session->userdata("UserID"),
                "ModifiedBy" => $this->session->userdata("UserID"),
                "update_time" => date('Y-m-d H:i:s')
            );
        }

        //get inv Cont
        $this->ceh->trans_start();
        $this->ceh->trans_strict(FALSE);


        foreach ($inv_draft as $item) {
            $this->ceh->insert('INV_DFT', $item);
        }
        foreach ($inv_draft_details as $item) {

            $item["YARD_ID"] = $this->yard_id;

            $this->ceh->insert('INV_DFT_DTL', $item);
        }

        if (isset($inv_vat) && count($inv_vat) > 0) {
            $inv_vat["YARD_ID"] = $this->yard_id;
            $this->ceh->insert('INV_VAT', $inv_vat);

            if ($this->session->userdata("invInfo") !== null && $args["pubType"] == 'm-inv') {
                $temp_session = json_decode($this->session->userdata("invInfo"), TRUE); //them moi hd thu sau
                $session_inv_info = $temp_session['CAS'];

                //nếu đã đến số cuối cùng thì remove invInfo để user tự set lại
                if ($session_inv_info["invno"] == $session_inv_info["toNo"]) {
                    $this->session->unset_userdata('invInfo');
                } else {
                    //set laij soo hóa đơn tay tăng lên 1
                    $session_inv_info["invno"] = intval($session_inv_info["invno"]) + 1;

                    $temp_session['CAS'] = $session_inv_info; //them moi hd thu sau
                    $this->session->set_userdata("invInfo", json_encode($temp_session)); //them moi hd thu sau
                }
            }
        }

        if (count($cntrRowguids) > 0) {
            $this->ceh->where('YARD_ID', $this->yard_id)
                ->where_in('rowguid', $cntrRowguids)
                ->update('CNTR_DETAILS', array("InvNo" => $inv_vat["INV_NO"]));
        }

        $this->ceh->trans_complete();

        if ($this->ceh->trans_status() === FALSE) {
            $this->ceh->trans_rollback();
            return FALSE;
        } else {
            $this->ceh->trans_commit();
            return TRUE;
        }
    }

    public function save_EIR_INV($args, &$outInfo = array())
    {
        //$lst, $pincode
        if (!is_array($args) || count($args) == 0) return "";

        $eirs = array();
        if (isset($args['eir']) && count($args['eir'])) {
            $eirs = $args['eir'];
        }

        if (count($eirs) == 0) {
            return "";
        }

        //get invoice info
        $invInfo = (isset($args['invInfo']) && count($args['invInfo']) > 0) ? $args['invInfo'] : array();

        $invContents = array();
        $pinCode = "";
        $draftNo = "";

        $arrCntrRowguids = array();
        $arrEIRNo = array();
        $eirSeq = 1;
        $checkEIRNo = array();
        $checkPincode = array();
        $eirParentForAttach = '';

        $draftMarker = array();
        //add for pin cont
        $contSeq = 1;
        //add for pin cont

        if (count($invInfo) > 0) {
            $pinCode = $invInfo['fkey'];
            $draftNo = $this->generateDraftNo();

            $invContents = array(
                "INV_NO_PRE" => $invInfo['invno'],
                "INV_PREFIX" => $invInfo['serial'],
                "INV_DATE" => $invInfo['INV_DATE'] ?? date('Y-m-d H:i:s'),
                "DRAFT_NO" => $draftNo,
                "PIN_CODE" => $pinCode
            );

            $args['invInfo']['DRAFT_NO'] = $draftNo;
            $checkPincode[$eirs[0]["CJMode_CD"]] = $pinCode;
        } else {
            //generate số pin
            $pintype = $args["pubType"] == 'credit' ? 'CRE' : 'CAS';
            $prefix = $this->config->item('PIN_PREFIX')[$pintype];
            if (isset($args["pubType"])) {
                if ($args["pubType"] != 'credit') {
                    $draftNo = $this->generateDraftNo();
                }

                if ($args["pubType"] == 'm-inv') // trường hợp xuất hóa đơn tay, giấy
                {
                    $pinCode = $this->generatePinCode($prefix);
                    $temp_session = json_decode($this->session->userdata("invInfo"), TRUE); //them moi hd thu sau
                    $session_inv_info = $temp_session['CAS']; // lấy thông tin hóa đơn đc lưu trữ trong biến session

                    $invContents = array(
                        "INV_NO_PRE" => $session_inv_info['invno'],
                        "INV_PREFIX" => $session_inv_info['serial'],
                        "INV_DATE" => $invInfo['INV_DATE'] ?? date('Y-m-d H:i:s'),
                        "DRAFT_NO" => $draftNo,
                        "PIN_CODE" => $pinCode
                    );

                    //trả về thông tin hóa đơn
                    $invOut = [
                        "invno" => $session_inv_info['invno'],
                        "serial" => $session_inv_info['serial'],
                        "fkey" => $pinCode
                    ];

                    array_push($outInfo, $invOut);

                    //gán thông tin hóa đơn vào biến $args để phục vụ cho lệnh dv đính kèm nếu có
                    $args["invInfo"] = $invOut;
                } elseif ($args["pubType"] == 'credit') {
                    $pinCode = $this->generatePinCode($prefix);
                    //trả về thông tin số PIN
                    array_push($outInfo, ["PinCode" => $pinCode]);

                    //gán thông tin hóa đơn vào biến $args để phục vụ cho lệnh dv đính kèm nếu có
                    $args["invInfo"] = ["PinCode" => $pinCode];
                }
            }
        }

        foreach ($eirs as $item) {
            //unset column use for inv
            unset($item['ShipYear'], $item['ShipVoy'], $item['BOOK_STATUS']);

            //unset column in importpickup 
            unset($item['cTLHQ'], $item['Description'], $item['ShipperName']);

            //unset item RowguidCntrDetails -> in function load_ip. . . get it for attach services
            unset($item["RowguidCntrDetails"]);

            //unset LaneID was used for get discount
            unset($item["LaneID"]);

            //unset column in emptypickup
            unset(
                $item['BookingDate'],
                $item['BookAmount'],
                $item['StackingAmount'],
                $item['ContCondition'],
                $item['isAssignCntr'],
                $item["Ter_Hold_CHK"]
            );

            //VIET HOA SO CONTAINER
            if (isset($item['CntrNo'])) {
                $item['CntrNo'] = strtoupper(str_replace(' ', '', $item['CntrNo']));
            }
            if (isset($item['BLNo'])) {
                $item['BLNo'] = strtoupper(str_replace(' ', '', $item['BLNo']));
            }
            if (isset($item['BookingNo'])) {
                $item['BookingNo'] = strtoupper(str_replace(' ', '', $item['BookingNo']));
            }
            if (isset($item['OprID'])) {
                $item['OprID'] = strtoupper(str_replace(' ', '', $item['OprID']));
            }

            //convert datetime in client to dbdatetime
            $item['IssueDate'] = $this->funcs->dbDateTime($item['IssueDate']);

            if (isset($item['ExpDate'])) {
                $item['ExpDate'] = $this->funcs->dbDateTime($item['ExpDate']);
            }
            if (isset($item['OprExpDate'])) {
                $item['OprExpDate'] = $this->funcs->dbDateTime($item['OprExpDate']);
            }
            if (isset($item['BerthDate'])) {
                $item['BerthDate'] = $this->funcs->dbDateTime($item['BerthDate']);
            }

            if (isset($item['ExpPluginDate'])) {
                $item['ExpPluginDate'] = $this->funcs->dbDateTime($item['ExpPluginDate']);
            }

            if (isset($item['CJModeName'])) {
                $item['CJModeName'] = UNICODE . $item['CJModeName'];
            }

            if (isset($item['SHIPPER_NAME'])) {
                $item['SHIPPER_NAME'] = UNICODE . $item['SHIPPER_NAME'];
            }

            if (isset($item['NameDD'])) {
                $item['NameDD'] = UNICODE . $item['NameDD'];
            }

            if (isset($item['CmdID'])) {
                $item['CmdID'] = UNICODE . $item['CmdID'];
            }

            if (isset($item['Note'])) {
                $item['Note'] = UNICODE . $item['Note'];
            }

            if (isset($item["CMDWeight"])) {
                $item['CMDWeight'] = (float)str_replace(',', '', $item['CMDWeight']);
            }

            //update inv info into
            if (count($invContents) > 0) {
                $item["InvNo"] = isset($invContents["INV_PREFIX"]) ? $invContents["INV_PREFIX"] . $invContents["INV_NO_PRE"] : NULL;
            }

            //multi yard
            $item["YARD_ID"] = $this->yard_id;

            //basic info
            $item['ModifiedBy'] = $this->session->userdata("UserID");
            $item['update_time'] = date('Y-m-d H:i:s');

            //insert database

            $item['CreatedBy'] = $item['ModifiedBy'];

            $item["EIRNo"] = isset($checkEIRNo[$item["CJMode_CD"]]) ? $checkEIRNo[$item["CJMode_CD"]] : $this->generateOrderNo();
            $eirSeq = isset($checkEIRNo[$item["CJMode_CD"]]) ? ($eirSeq + 1) : 1;
            $item["EIR_SEQ"] = $eirSeq;
            if (!count($invInfo) && $args["pubType"] != 'credit') { // trường hợp phiếu thu
                $pinCode = isset($checkPincode[$item["CJMode_CD"]])  ? $checkPincode[$item["CJMode_CD"]] : $this->generatePinCode();
            } elseif (count($invInfo) && $contSeq > 1) {
                // trường hợp vòng lặp thứ 2 khi phát hành hóa đơn từ lệnh vì lần đầu có pincode từ khi phát hành
                $pinCode = isset($checkPincode[$item["CJMode_CD"]])  ? $checkPincode[$item["CJMode_CD"]] : $this->generatePinCode();
            }

            // $item['PinCode'] = $pinCode;
            //add for pin cont
            $newPin = $pinCode . "-" . substr('000' . $contSeq, -3);
            $item['PinCode'] = $newPin;
            $contSeq++;
            //add for pin cont

            if ($args["pubType"] == 'dft' || count($invInfo)) {
                if (count($draftMarker) == 0) {
                    $item["DRAFT_INV_NO"] = $draftNo;
                } else {
                    $item["DRAFT_INV_NO"] = isset($draftMarker[$item["CJMode_CD"]])
                        ? $draftMarker[$item["CJMode_CD"]]['DRAFT_INV_NO']
                        : $this->generateDraftNo();
                }
            } elseif ($args["pubType"] != 'credit') {
                $item["DRAFT_INV_NO"] = $draftNo;
            }

            $draftMarker[$item["CJMode_CD"]] = array(
                'PinCode' => $pinCode,
                "REF_NO" => $item["EIRNo"],
                "DRAFT_INV_NO" => $item["DRAFT_INV_NO"]
            );

            //gán số eir đầu tiên để lưu ssrmore cho dịch vụ đính kèm nếu có
            if ($eirParentForAttach == '') {
                $eirParentForAttach = $item["EIRNo"];
            }

            $checkEIRNo[$item["CJMode_CD"]] = $item["EIRNo"];
            array_push($arrEIRNo, $item["EIRNo"]);
            $checkPincode[$item["CJMode_CD"]] = $pinCode;

            $this->ceh->insert('EIR', $item);
            if ($this->ceh->affected_rows() < 1) {
                return 'error:' . $this->ceh->error();
            }

            $cntrWhere = array(
                "CntrNo" => $item["CntrNo"] ? $item["CntrNo"] : "",
                // "CMStatus" => 'S',
                "CntrClass" => $item["CntrClass"] ? $item["CntrClass"] : "",
                "ShipKey" => $item["ShipKey"] ? $item["ShipKey"] : "",
                "OprID" => $item["OprID"] ? $item["OprID"] : "",

                "YARD_ID" => $this->yard_id
            );

            $uCntr = $this->ceh->select('rowguid')
                ->where($cntrWhere)
                ->where("DateOut IS NULL") // bo sung them dieu kien dateout de lay dung vong doi cont : zalo gr (vtos-tk hp) // Kiểm tra giúp a cont CAXU6672346 vẫn gặp tình trạng lệnh nhảy vào vòng đời cũ
                ->limit(1)
                ->get('CNTR_DETAILS')->row_array();

            if ($uCntr !== NULL) {
                // $eir = $item["EIRNo"];
                // $this->ceh->set( "EIRNo", "CASE WHEN EIRNo IS NULL THEN '$eir' ELSE EIRNo + '/' + '$eir' END", FALSE );
                // $this->ceh->set("EIRNo", $item["EIRNo"]);
                // $this->ceh->set("CJMode_OUT_CD", $item["CJMode_CD"]);
                // $this->ceh->set("DMethod_OUT_CD", $item["DMETHOD_CD"]);

                $upCont = array(
                    'EIRNo' => $item['EIRNo'],
                    'CJMode_OUT_CD' => $item['CJMode_CD'],
                    'DMethod_OUT_CD' => $item['DMETHOD_CD']
                );

                $this->ceh->where('rowguid', $uCntr['rowguid'])
                    ->update('CNTR_DETAILS', $upCont);

                array_push($arrCntrRowguids, $uCntr['rowguid']);
            } else {
                log_message('error', 'eir update container failed: ' . json_encode($cntrWhere));
            }
        }

        if (isset($args["stackingAmount"]) && count($args["stackingAmount"]) > 0) {
            $bkNo = $eirs[0]["BookingNo"];
            foreach ($args["stackingAmount"] as $localSize => $count) {
                $this->ceh->where("BookingNo", $bkNo)->where("LocalSZPT", $localSize)->update('EMP_BOOK', array('StackingAmount' => $count));
            }

            unset($args["stackingAmount"]);
        }

        //set Inv Content to args
        $args["DRAFT_MARKER"] = $draftMarker;
        if ($args["pubType"] == 'dft') {
            //trả về thông tin phiếu tính cước
            foreach ($draftMarker as $key => $value) {
                array_push($outInfo, array(
                    "PinCode" => $value["PinCode"],
                    "DRAFT_NO" => $value["DRAFT_INV_NO"]
                ));
            }

            $this->saveSplitDraft($args, $eirs[0]);
        } else {
            if (count($invContents) > 0) {
                //set Inv Content to args
                $args["INV_CONTENT"] = $invContents;

                //add to args for save INV
                $args["REF_NOs"] = array_unique($arrEIRNo);

                $this->saveInvoice($args, $eirs[0], $arrCntrRowguids);
            }
        }

        if (isset($args['odr']) && count($args['odr'])) {
            foreach ($args['odr'] as $key => $value) {
                $args['odr'][$key]['SSRMORE'] = $eirParentForAttach;
            }

            $this->save_SRV_ODR_INV($args, '', $outInfo);
        }

        return TRUE;
    }

    public function save_SRV_ODR_INV($args, $stuff_unstuff_chk = "", &$outInfo = array())
    {

        //$lst, $pincode
        if (!is_array($args) || count($args) == 0) return "";

        $orders = array();
        if (isset($args['odr']) && count($args['odr'])) {
            $orders = $args['odr'];
        }

        if (count($orders) == 0) {
            return "";
        }

        //get draft no
        $draftNo = '';

        //get invoice info
        $invInfo = isset($args['invInfo']) ? $args['invInfo'] : array();

        $pinCode = "";
        $invContents = array();

        $arrCntrRowguids = array();
        $arrSSOderNo = array();
        $forSSMore = "";
        $checkSSOderNo = array();
        $checkPincode = array();
        $contSeq = 1;
        $draftMarker = array();
        $isCheckAttachSrv = false;

        if (count($invInfo) > 0) {
            $pinCode = $invInfo['fkey'];
            $draftNo = isset($invInfo['DRAFT_NO']) ? $invInfo['DRAFT_NO'] : $this->generateDraftNo();

            $invContents = array(
                "INV_NO_PRE" => $invInfo['invno'],
                "INV_PREFIX" => $invInfo['serial'],
                "INV_DATE" => $invInfo['INV_DATE'] ?? date('Y-m-d H:i:s'),
                "DRAFT_NO" => $draftNo,
                "PIN_CODE" => $pinCode
            );
            $checkPincode[$orders[0]["CJMode_CD"]] = $pinCode;
        } else {
            //generate số pin
            $pintype = $args["pubType"] == 'credit' ? 'CRE' : 'CAS';
            $prefix = $this->config->item('PIN_PREFIX')[$pintype];
            // $pinCode = count($outInfo) > 0 ? $outInfo[0]["PinCode"] : $this->generatePinCode($prefix);

            if (isset($args["pubType"])) {
                if ($args["pubType"] != 'credit') {
                    $draftNo = $this->generateDraftNo();
                }

                if ($args["pubType"] == 'm-inv') // trường hợp xuất hóa đơn tay
                {
                    $temp_session = json_decode($this->session->userdata("invInfo"), TRUE); //them moi hd thu sau
                    $session_inv_info = $temp_session['CAS']; // lấy thông tin hóa đơn đc lưu trữ trong biến session
                    $pinCode = $this->generatePinCode($prefix);

                    $invContents = array(
                        "INV_NO_PRE" => $session_inv_info['invno'],
                        "INV_PREFIX" => $session_inv_info['serial'],
                        "INV_DATE" => $invInfo['INV_DATE'] ?? date('Y-m-d H:i:s'),
                        "DRAFT_NO" => $draftNo,
                        "PIN_CODE" => $pinCode
                    );

                    //trả về thông tin hóa đơn tay
                    array_push(
                        $outInfo,
                        array(
                            "invno" => $session_inv_info['invno'],
                            "serial" => $session_inv_info['serial'],
                            "fkey" => $pinCode
                        )
                    );
                } elseif ($args["pubType"] == 'credit') {
                    $pinCode = $this->generatePinCode($prefix);
                    //trả về thông tin số PIN
                    array_push(
                        $outInfo,
                        array(
                            "PinCode" => $pinCode
                        )
                    );
                }
            }
        }

        $getnotallowExpDate = $this->excludeServiceOrderExpDate();
        $notallowExpDate = array_map(function ($cjmode) {
            return $cjmode['CJMode_CD'];
        }, $getnotallowExpDate);

        foreach ($orders as $item) {
            //unset column use for inv
            unset($item['ShipYear'], $item['ShipVoy']);
            unset($item['BOOK_STATUS']);
            //unset LaneID was used for get discount
            unset($item["LaneID"]);

            //unset column
            unset(
                $item['cTLHQ'],
                $item['Description'],
                $item['CJModeName'],
                $item['Transist'],
                $item["UNNO"],
                $item["Ter_Hold_CHK"],
                $item["SSOderNo"],
                $item["EIRNo"],
                $item["bXNVC"],
                $item["FDATE"],
                $item['ShipperName'],
                $item['OprExpDate'],
                $item['RetLocation'],
                $item['Rowguid_CntrDetails']
            );

            if (isset($item['CntrNo'])) {
                $item['CntrNo'] = strtoupper(str_replace(' ', '', $item['CntrNo']));
            }

            if (isset($item['BLNo'])) {
                $item['BLNo'] = strtoupper(str_replace(' ', '', $item['BLNo']));
            }
            if (isset($item['BookingNo'])) {
                $item['BookingNo'] = strtoupper(str_replace(' ', '', $item['BookingNo']));
            }
            if (isset($item['OprID'])) {
                $item['OprID'] = strtoupper(str_replace(' ', '', $item['OprID']));
            }

            //convert datetime in client to dbdatetime
            $item['IssueDate'] = $this->funcs->dbDateTime(isset($item['IssueDate']) ? $item['IssueDate'] : date("Y-m-d H:i:s"));

            //nếu là các loại công việc isyardsrv, isnoncont -> k lưu expdate
            if (in_array($item["CJMode_CD"], $notallowExpDate)) {
                unset($item['ExpDate']);
            } else {
                if (isset($item['ExpDate'])) {
                    $item['ExpDate'] = $this->funcs->dbDateTime($item['ExpDate']);
                }
            }

            if (isset($item['ExpPluginDate'])) {
                $item['ExpPluginDate'] = $this->funcs->dbDateTime($item['ExpPluginDate']);
            }
            if (isset($item['BerthDate'])) {
                $item['BerthDate'] = $this->funcs->dbDateTime($item['BerthDate']);
            }
            if (isset($item['Note'])) {
                $item['Note'] = UNICODE . $item['Note'];
            }

            // if(isset($item['CJModeName'])){
            //     $item['CJModeName'] = UNICODE.$item['CJModeName'];
            // }

            if (isset($item['SHIPPER_NAME'])) {
                $item['SHIPPER_NAME'] = UNICODE . $item['SHIPPER_NAME'];
            }

            if (isset($item['NameDD'])) {
                $item['NameDD'] = UNICODE . $item['NameDD'];
            }

            if (isset($item['Port_CD'])) {
                $item['POL'] = $item['Port_CD'];
                unset($item['Port_CD']);
            }

            //update inv info into 
            if (count($invContents) > 0) {
                $item["InvNo"] = isset($invContents["INV_PREFIX"]) ? $invContents["INV_PREFIX"] . $invContents["INV_NO_PRE"] : NULL;
            }

            //multi yard
            $item["YARD_ID"] = $this->yard_id;

            //basic info
            $item['ModifiedBy'] = $this->session->userdata("UserID");
            $item['update_time'] = date('Y-m-d H:i:s');

            //insert database

            $item['CreatedBy'] = $item['ModifiedBy'];

            //generate SSOderNo for save data
            //
            $item["SSOderNo"] = isset($checkSSOderNo[$item["CJMode_CD"]]) ? $checkSSOderNo[$item["CJMode_CD"]] : $this->generateOrderNo();

            //nếu item đã có SSRMORE (từ lệnh nâng hạ gán sang) -> bỏ qua, ngược lại
            if (!isset($item["SSRMORE"]) || $item["SSRMORE"] == '') {
                //nếu forSSMore != '' và là lệnh đính kèm (dựa theo $checkSSOderNo), cập nhật vào cột SSRMORE, đánh dấu dịch vụ đính kèm
                if ($forSSMore != "" && !isset($checkSSOderNo[$item["CJMode_CD"]])) {
                    $item['SSRMORE'] = $forSSMore;
                } else {
                    //nếu forSSMore == '' => vòng for đầu tiên => là lệnh gốc => đánh dấu lại số lệnh để update cho các lệnh đính kèm
                    $forSSMore = $item["SSOderNo"];
                }
            } else {
                $isCheckAttachSrv = true;
            }
            // $item["SSOderNo"] =  $this->generateOrderNo();
            if (!count($invInfo) && $args["pubType"] != 'credit') { // trường hợp phiếu thu
                $pinCode = isset($checkPincode[$item["CJMode_CD"]])  ? $checkPincode[$item["CJMode_CD"]] : $this->generatePinCode();
            } elseif (count($invInfo) && $contSeq > 1) {
                // trường hợp vòng lặp thứ 2 khi phát hành hóa đơn từ lệnh vì lần đầu có pincode từ khi phát hành
                $pinCode = isset($checkPincode[$item["CJMode_CD"]])  ? $checkPincode[$item["CJMode_CD"]] : $this->generatePinCode();
            } elseif ($isCheckAttachSrv && count($invInfo)) { // nếu là dịch vụ đính kèm + phát hành hóa đơn thì sinh ra pincode ở vòng lặp đầu
                $pinCode = $this->generatePinCode();
            }
            // $item['PinCode'] = $pinCode;

            //add for pin cont
            $newPin = $pinCode . "-" . substr('000' . $contSeq, -3);
            $item['PinCode'] = $newPin;
            $contSeq++;
            //add for pin cont

            if ($args["pubType"] == 'dft' || count($invInfo)) {
                if (count($draftMarker) == 0) {
                    $item["DRAFT_INV_NO"] = $draftNo;
                    if (count($invInfo) && $isCheckAttachSrv) {
                        $item["DRAFT_INV_NO"] = isset($draftMarker[$item["CJMode_CD"]])
                            ? $draftMarker[$item["CJMode_CD"]]['DRAFT_INV_NO']
                            : $this->generateDraftNo();
                    }
                } else {
                    $item["DRAFT_INV_NO"] = isset($draftMarker[$item["CJMode_CD"]])
                        ? $draftMarker[$item["CJMode_CD"]]['DRAFT_INV_NO']
                        : $this->generateDraftNo();
                }
            } elseif ($args["pubType"] != 'credit') {
                $item["DRAFT_INV_NO"] = $draftNo;
            }
            $draftMarker[$item["CJMode_CD"]] = array(
                "PinCode" => $pinCode,
                "REF_NO" => $item["SSOderNo"],
                "DRAFT_INV_NO" => $item["DRAFT_INV_NO"]
            );

            $checkSSOderNo[$item["CJMode_CD"]] = $item["SSOderNo"];

            array_push($arrSSOderNo, $item["SSOderNo"]);

            $this->ceh->insert('SRV_ODR', $item);
            if ($this->ceh->affected_rows() < 1) {
                return 'error:' . $this->ceh->error();
            }

            //save to STORAGE for LBC

            if (isset($item["CJMode_CD"]) && $item["CJMode_CD"] == "LBC" && isset($item["RowguidCntrDetails"])) {
                $free = $this->getStorageFreeDay($item["OprID"], $item['CntrClass'], $item['Status'], $item['CARGO_TYPE']);
                $storageFrom = strtotime(explode(' ', isset($item["DateIn"]) ? $item["DateIn"] : date('Y-m-d'))[0]);
                $storageTo = strtotime(explode(' ', isset($item["ExpDate"]) ? $item["ExpDate"] : date('Y-m-d'))[0]);

                $daysinYard = ceil(($storageTo - $storageFrom) / (60 * 60 * 24) + 1);
                $daysinYard = $daysinYard > 0 ? $daysinYard : 0;

                $storage = array(
                    "ROWGUID_FK" => $item["RowguidCntrDetails"],
                    "ShipKey" => isset($item["ShipKey"]) ? $item["ShipKey"] : NULL,
                    "CntrClass" => $item["CntrClass"],
                    "CntrNo" => $item["CntrNo"],
                    "ShipID" => isset($item["ShipID"]) ? $item["ShipID"] : NULL,
                    "OprID" => $item["OprID"],
                    "ISO_SZTP" => $item["ISO_SZTP"],
                    "Status" => $item["Status"],
                    "CARGO_TYPE" => $item["CARGO_TYPE"],
                    "IsLocal" => $item["IsLocal"],
                    "CusID" => $item["CusID"],
                    "DateIn" => $item["DateIn"],
                    "IFREE_DAYS" => $free,
                    "OV_DAYS" => $daysinYard - $free,
                    "InvNo" => isset($item["InvNo"]) ? $item["InvNo"] : NULL,
                    "DRAFT_INV_NO" => isset($item["DRAFT_INV_NO"]) ? $item["DRAFT_INV_NO"] : NULL,
                    "EXPIRED_DATE" => isset($item["ExpDate"]) ? $item["ExpDate"] : date('Y-m-d'),
                    "STORAGE_DAYS" => $daysinYard,
                    "PAYMENT_TYPE" => "M",
                    "PAYMENT_CHK" => 0,
                    "CreatedBy" => $this->session->userdata("UserID"),
                    "ModifiedBy" => $this->session->userdata("UserID"),
                    "insert_time" => date("Y-m-d H:i:s"),
                    "update_time" => date("Y-m-d H:i:s"),
                    "YARD_ID" => $this->yard_id
                );

                $this->ceh->insert("STORAGE", $storage);
            }

            //update to CNTR_DETAILS
            if (isset($item["RowguidCntrDetails"]) && !empty($item["RowguidCntrDetails"])) {

                // $ss = $item["SSOderNo"];
                // $this->ceh->set( "SSOderNo", "CASE WHEN SSOderNo IS NULL THEN '$ss' ELSE SSOderNo + '/' + '$ss' END", FALSE );
                // $updateCntrDetail = array(
                //     "SSOderNo" => $item["SSOderNo"]
                // );

                if ($stuff_unstuff_chk != "" && (!isset($item["SSRMORE"]) || $item["SSRMORE"] == '')) { //kiem tra dieu kien SSRMORE -> neu co SSRMORE -> la lenh dinh kem -> ko update vao CNTR_DETAILS
                    $updateCntrDetail = array(
                        "SSOderNo" => $item["SSOderNo"],
                        "CJMode_OUT_CD" => $item["CJMode_CD"],
                        "DMethod_OUT_CD" => $item["DMETHOD_CD"]
                    );

                    $this->ceh->where('rowguid', $item['RowguidCntrDetails'])->update('CNTR_DETAILS', $updateCntrDetail);
                    if ($this->ceh->affected_rows() < 1) {
                        log_message('error', 'update cntr stuff - unstuff failed: ' . json_encode($item));
                    }
                }

                if (isset($item["ExpPluginDate"])) { //neu co dien lanh -> lenh dien lanh -> update vao RF_ONOFF
                    //UPDATE REFEER
                    $upRF = array(
                        'payment_chk' => 0,
                        'payment_type' => 'M',
                        'DRAFT_INV_NO' => isset($item["DRAFT_INV_NO"]) ? $item["DRAFT_INV_NO"] : NULL,
                        'InvNo' => isset($item["InvNo"]) ? $item["InvNo"] : NULL,
                        'expplugindate' => isset($item["ExpPluginDate"]) ? $item["ExpPluginDate"] : NULL,
                        'ModifiedBy' => $this->session->userdata("UserID"),
                        'update_time' => date('Y-m-d H:i:s')
                    );

                    $this->ceh->where("MASTER_ROWGUID", $item["RowguidCntrDetails"])
                        ->where("YARD_ID", $this->yard_id)
                        ->update("RF_ONOFF", $upRF);
                }

                array_push($arrCntrRowguids, $item['RowguidCntrDetails']);
            }

            //unset session EIRNO
            unset($_SESSION['EirNoQueue'][$item['SSOderNo']]);
        }

        if (isset($args["stackingAmount"]) && count($args["stackingAmount"]) > 0) {
            $bkNo = $orders[0]["BookingNo"];
            foreach ($args["stackingAmount"] as $localSize => $count) {
                $this->ceh->where("BookingNo", $bkNo)->where("LocalSZPT", $localSize)->update('EMP_BOOK', array('StackingAmount' => $count));
            }

            unset($args["stackingAmount"]);
        }

        $args["DRAFT_MARKER"] = $draftMarker;
        if ($args["pubType"] == 'dft') {
            //set Inv Content to args

            //trả về thông tin phiếu tính cước
            foreach ($draftMarker as $key => $value) {
                array_push($outInfo, array(
                    "PinCode" => $value["PinCode"],
                    "DRAFT_NO" => $value["DRAFT_INV_NO"]
                ));
            }

            $results = $this->saveSplitDraft($args, $orders[0]);
            return $results;
        } else {
            if (count($invContents) > 0) {
                //set Inv Content to args
                $args["INV_CONTENT"] = $invContents;

                //add to args for save INV
                $args["REF_NOs"] = array_unique($arrSSOderNo);

                $results = $this->saveInvoice($args, $orders[0], $arrCntrRowguids, $isCheckAttachSrv);
                return $results;
            }
        }

        return TRUE;
    }

    private function getContSize($sztype)
    {
        switch (substr($sztype, 0, 1)) {
            case "2":
                return 20;
            case "4":
                return 40;
            case "L":
            case "M":
            case "9":
                return 45;
        }

        return "0";
    }

    public function generateDraftNo()
    {
        $year = date('Y');
        $file = APPPATH . 'cache/draft_temp' . $year . '.txt';
        $fp = fopen($file, "a+");

        do {
            $getLock = flock($fp, LOCK_EX | LOCK_NB);
            if ($getLock) {
                $filesz = filesize($file);
                if ($filesz == 0) {
                    $out = $this->getMaxDraftInDB();
                } else {
                    $dftNo = fread($fp, $filesz);
                    $out = intval($dftNo) + 1;
                }

                ftruncate($fp, 0);
                fwrite($fp, $out);
                flock($fp, LOCK_UN);
            }
        } while (!$getLock);

        fclose($fp);

        return 'DR/' . $year . '/' . substr('0000000' . $out, -7);
    }

    public function getMaxDraftInDB()
    {
        $this->ceh->select('MAX( CONVERT( bigint, SUBSTRING(DRAFT_INV_NO,9, 6)) ) AS DRAFT_NO');
        $this->ceh->where("SUBSTRING(DRAFT_INV_NO,4, 4) = ", date('Y'));
        $this->ceh->where("YARD_ID", $this->yard_id);
        $stmt = $this->ceh->limit(1)->get('INV_DFT');
        $stmt = $stmt->row_array();

        return $stmt['DRAFT_NO'] === null ? 1 : (int)$stmt['DRAFT_NO'] + 1;
    }

    public function generatePinCode($prefix = '', $digits = 5)
    {
        $yearmonthday = date('ymd');
        $file = APPPATH . "/cache/pins_temp.txt";
        $fp = fopen($file, "a+");
        if ($prefix == '') {
            $prefix = $this->config->item('PIN_PREFIX')['CAS'];
        }

        do {
            $getLock = flock($fp, LOCK_EX | LOCK_NB);
            if ($getLock) {
                $filesz = filesize($file);
                $content = $filesz > 0 ? fread($fp, $filesz) : $this->retrieveInDayPinCodes($prefix, $yearmonthday, $digits);
                $temps = !empty($content) ? explode(':', $content) : array();
                $isDuplicate = true;
                do {
                    $randomNum = rand(1, pow(10, $digits) - 1);
                    if (count($temps) > 0 && $temps[0] == $yearmonthday) { //trong ngay
                        $checkpins = array();
                        if (isset($temps[1]) && !empty($temps[1]) && $temps[1] !== '') {
                            $listOfPin = explode('|', $temps[1]);
                            $checkpins = array_filter($listOfPin, function ($p) {
                                return !empty($p) && $p !== '';
                            });
                        }

                        $duplicatePin = array_filter($checkpins, function ($p) use ($randomNum) {
                            return (int)trim($p) === (int)$randomNum;
                        });
                        $isDuplicate = count($duplicatePin) > 0;
                    } else { //sang ngay moi
                        $content = "$yearmonthday:";
                        $isDuplicate = false;
                    }
                } while ($isDuplicate);

                $content .= "$randomNum|";
                //truncate file
                ftruncate($fp, 0);
                //save to file
                fwrite($fp, $content);
                //unlock file
                flock($fp, LOCK_UN);
            }
        } while (!$getLock);

        fclose($fp);

        $result = "$prefix$yearmonthday" . substr("00000000000000" . $randomNum, -$digits);
        return $result;
    }

    public function retrieveInDayPinCodes($prefix, $yearmonthday, $digits)
    {
        //[TOS][210705][00000]
        $lenOfYearMonth = strlen($yearmonthday);
        $lenOfPrefix = strlen($prefix);
        $startSubStrPin =  $lenOfPrefix + $lenOfYearMonth + 1;
        $startSubStrYM = $lenOfPrefix + 1;

        $query = <<<EOT
        select substring(pincode, $startSubStrPin, $digits) AS PIN from EIR
            where substring(pincode, 1, $lenOfPrefix) = ?
            and substring(pincode, $startSubStrYM, $lenOfYearMonth) = ?
            and right(pincode, 3) = '001' and yard_id = ?
        union
        select substring(pincode, $startSubStrPin, $digits) AS PIN from SRV_ODR
            where substring(pincode, 1, $lenOfPrefix) = ?
            and substring(pincode, $startSubStrYM, $lenOfYearMonth) = ?
            and right(pincode, 3) = '001' and yard_id = ?
EOT;
        $params = array(
            $prefix, $yearmonthday, $this->yard_id,
            $prefix, $yearmonthday, $this->yard_id
        );

        $temp = $this->ceh->query($query, $params)->result_array();
        if (count($temp) == 0) {
            return '';
        }

        $pinStr = implode('|', array_unique(array_column($temp, 'PIN')));
        return $yearmonthday . ":$pinStr|";
    }

    public function getOrder4ViewPDFByList($pinCode)
    {
        $getJobModeSql = $this->ceh->select("CJModeName")->where("dm.CJMode_CD = e.CJMode_CD AND dm.YARD_ID = e.YARD_ID")
            ->limit(1)
            ->get_compiled_select("DELIVERY_MODE dm", TRUE);

        $getCusName = $this->ceh->select("CusName")->where("cm.CusID = e.CusID AND cm.CusType = e.PAYMENT_TYPE AND cm.YARD_ID = e.YARD_ID")
            ->limit(1)
            ->get_compiled_select("CUSTOMERS cm", TRUE);

        $eirSql = $this->ceh->select("EIRNo AS OrderNo, SHIPPER_NAME, PersonalID, NameDD, CJMode_CD, CJModeName, Note, ShipID, ImVoy
                                    , ExVoy, ExpDate, BLNo, BookingNo, CntrNo, OprID, ISO_SZTP, Status, SealNo, IsLocal, DMethod_CD, InvNo
                                    , (" . $getCusName . ") CusName, Note")
            ->like("PinCode", $pinCode, 'after')
            ->where("YARD_ID", $this->yard_id)
            ->get_compiled_select("EIR e", TRUE);
        $srvSql = $this->ceh->select("SSOderNo AS OrderNo, SHIPPER_NAME, PersonalID, NameDD, CJMode_CD
                                    , (" . $getJobModeSql . ") CJModeName
                                    , Note, ShipID, ImVoy, ExVoy, ExpDate
                                    , BLNo, BookingNo, CntrNo, OprID, ISO_SZTP, Status, SealNo, IsLocal, DMethod_CD, InvNo
                                    , (" . $getCusName . ") CusName, Note")
            ->like("PinCode", $pinCode, 'after')
            ->where("YARD_ID", $this->yard_id)
            ->get_compiled_select("SRV_ODR e", TRUE);

        $outInfo = $this->ceh->query($eirSql . " UNION " . $srvSql);
        $outInfo = $outInfo->result_array();

        return $outInfo;
    }

    public function getOrder4Print($pinCode)
    {

        $getOrderType = $this->ceh->select("CASE
                                                WHEN isLoLo = 1 THEN 'NH'
                                                WHEN ischkCFS IN (1, 2, 3) THEN 'DR'
                                                WHEN IsYardSRV = 1 OR IsNonContSRV = 1 THEN 'DV' END AS OrderType")
            ->where("dd.CJMode_CD = e.CJMode_CD AND dd.YARD_ID = e.YARD_ID")
            ->limit(1)
            ->get_compiled_select("DELIVERY_MODE dd", TRUE);

        $eirSql = $this->ceh->select("e.PinCode, e.EIRNo AS OrderNo, SHIPPER_NAME, e.PersonalID, e.NameDD, e.CJMode_CD, UPPER(e.CJModeName) AS CJModeName
                                    , e.IssueDate, e.Note, e.ShipID, vs.ShipName, e.ImVoy, e.BerthDate, '' AS Remark, us.UserName
                                    , e.ExVoy, e.ExpDate, e.BookingNo, e.CntrNo, e.OprID, e.Status, e.SealNo, e.SealNo1, e.IsLocal, e.DMethod_CD
                                    , e.InvNo
                                    , CASE WHEN e.CJMode_CD = 'CAPR'
                                            THEN e.BookingNo
                                            ELSE ISNULL(e.BLNo, e.BookingNo)
                                            END AS BL_BKNo
                                    , cm.CusName, ct.Description as CARGO_TYPE_NAME
                                    , '' AS POL, e.POD, e.FPOD, e.LocalSZPT, e.CMDWeight, e.ExpPluginDate
                                    , e.Temperature, e.Vent, e.Vent_Unit, e.UNNO, e.CLASS, e.OOG_TOP, e.OOG_LEFT, e.OOG_RIGHT, e.OOG_BACK, e.OOG_FRONT
                                    , ISNULL( cd.cBlock + '-' + cd.cBay + '-' + cd.cRow + '-' + cd.cTier, cd.cArea ) AS YardPos, cd.cTLHQ, cd.DateIn
                                    , e.DRAFT_INV_NO, e.OprExpDate, vsc.ETA, vsc.ETD, e.FreeDays, e.DMethod_CD, e.RetLocation, e.InvNo, e.CARGO_TYPE
                                    , (" . $getOrderType . ") OrderType")
            ->join("CUSTOMERS cm", "cm.CusID = e.CusID AND cm.CusType = e.PAYMENT_TYPE AND cm.YARD_ID = e.YARD_ID", 'left')
            ->join("CARGO_TYPE ct", "ct.Code = e.CARGO_TYPE AND ct.YARD_ID = e.YARD_ID", 'left')
            ->join("CNTR_DETAILS cd", "cd.CntrNo = e.CntrNo AND cd.ShipKey = e.ShipKey
                                    AND cd.CntrClass = e.CntrClass AND cd.CMStatus = 'S' AND cd.YARD_ID = e.YARD_ID", 'left')
            ->join("SA_USERS us", "us.UserID = e.CreatedBy AND us.YARD_ID = e.YARD_ID", 'left')
            ->join("VESSELS vs", "vs.ShipID = e.ShipID AND vs.YARD_ID = e.YARD_ID", 'left')
            ->join("VESSEL_SCHEDULE vsc", "vsc.ShipKey = e.ShipKey AND vsc.YARD_ID = e.YARD_ID", 'left')
            ->where("e.YARD_ID", $this->yard_id);

        if (is_array($pinCode)) {
            $eirSql = $this->ceh->where_in("PinCode", $pinCode);
        } else {
            $eirSql = $this->ceh->like("PinCode", $pinCode, 'after');
        }

        $eirSql = $this->ceh->get_compiled_select("EIR e", TRUE);

        $srvSql = $this->ceh->select("e.PinCode, e.SSOderNo AS OrderNo, SHIPPER_NAME, e.PersonalID, e.NameDD, e.CJMode_CD, UPPER(CJModeName) AS CJModeName
                                    , e.IssueDate, e.Note, e.ShipID, vs.ShipName, e.ImVoy, e.BerthDate, cd.Note AS Remark, us.UserName
                                    , e.ExVoy, e.ExpDate, e.BookingNo, e.CntrNo, e.OprID, e.Status, e.SealNo, e.SealNo1, e.IsLocal, e.DMethod_CD
                                    , e.InvNo
                                    , CASE WHEN e.CJMode_CD IN (SELECT CJMode_CD FROM DELIVERY_MODE WHERE ischkCFS = 1)
                                            THEN e.BookingNo
                                            ELSE ISNULL(e.BLNo, e.BookingNo)
                                            END AS BL_BKNo
                                    , cm.CusName, ct.Description as CARGO_TYPE_NAME
                                    , e.POL, e.POD, e.FPOD, e.LocalSZPT, e.CMDWeight, e.ExpPluginDate
                                    , e.Temperature, e.Vent, e.Vent_Unit, '' AS UNNO, '' AS CLASS, e.OOG_TOP, e.OOG_LEFT, e.OOG_RIGHT, e.OOG_BACK, e.OOG_FRONT
                                    , ISNULL( cd.cBlock + '-' + cd.cBay + '-' + cd.cRow + '-' + cd.cTier, cd.cArea ) AS YardPos, cd.cTLHQ, cd.DateIn
                                    , e.DRAFT_INV_NO, '' AS OprExpDate, vsc.ETA, vsc.ETD,'' AS FreeDays, e.DMethod_CD, '' AS RetLocation, e.InvNo, e.CARGO_TYPE
                                    , (" . $getOrderType . ") OrderType")
            ->join("CUSTOMERS cm", "cm.CusID = e.CusID AND cm.CusType = e.PAYMENT_TYPE AND cm.YARD_ID = e.YARD_ID", 'left')
            ->join("DELIVERY_MODE dm", "dm.CJMode_CD = e.CJMode_CD AND dm.YARD_ID = e.YARD_ID", 'left')
            ->join("CARGO_TYPE ct", "ct.Code = e.CARGO_TYPE AND ct.YARD_ID = e.YARD_ID", 'left')
            ->join("CNTR_DETAILS cd", "cd.CntrNo = e.CntrNo AND cd.ShipKey = e.ShipKey
                                                            AND cd.CntrClass = e.CntrClass AND cd.CMStatus = 'S' AND cd.YARD_ID = e.YARD_ID", 'left')
            ->join("SA_USERS us", "us.UserID = e.CreatedBy AND us.YARD_ID = e.YARD_ID", 'left')
            ->join("VESSELS vs", "vs.ShipID = e.ShipID AND vs.YARD_ID = e.YARD_ID", 'left')
            ->join("VESSEL_SCHEDULE vsc", "vsc.ShipKey = e.ShipKey AND vsc.YARD_ID = e.YARD_ID", 'left')
            ->where("e.YARD_ID", $this->yard_id);
        if (is_array($pinCode)) {
            $srvSql = $this->ceh->where_in("PinCode", $pinCode);
        } else {
            $srvSql = $this->ceh->like("PinCode", $pinCode, 'after');
        }

        $srvSql = $this->ceh->order_by('e.PinCode')->get_compiled_select("SRV_ODR e", TRUE);

        $outInfo = $this->ceh->query($eirSql . " UNION ALL " . $srvSql);
        $outInfo = $outInfo->result_array();
        foreach ($outInfo as $key => $value) {
            $pngAbsoluteFilePath = FCPATH . "assets/img/qrcode_gen/" . $value['PinCode'] . ".png";
            $this->funcs->generateQRCode($value['PinCode']);
            $qrCodeData = base64_encode(file_get_contents($pngAbsoluteFilePath));
            $outInfo[$key]['QrData'] = 'data: ' . mime_content_type($pngAbsoluteFilePath) . ';base64,' . $qrCodeData;
        }

        return $outInfo;
    }

    public function getOrder4RePrint($args)
    {
        $getOrderType = $this->ceh->select("CASE
                                                WHEN isLoLo = 1 THEN 'NH'
                                                WHEN ischkCFS IN (1, 2, 3) THEN 'DR'
                                                WHEN IsYardSRV = 1 OR IsNonContSRV = 1 THEN 'DV' END AS OrderType")
            ->where("dd.CJMode_CD = e.CJMode_CD AND dd.YARD_ID = e.YARD_ID")
            ->limit(1)
            ->get_compiled_select("DELIVERY_MODE dd", TRUE);

        $tableName = $args["OrderType"] == "NH" ? "EIR e" : "SRV_ODR e";
        $colOrderName = $args["OrderType"] == "NH" ? "EIRNo" : "SSOderNo";
        $unno = $args["OrderType"] == "NH" ? "e.UNNO" : "''";
        $class = $args["OrderType"] == "NH" ? "e.CLASS" : "''";
        $oprExpDate = $args["OrderType"] == "NH" ? "e.OprExpDate" : "''";

        $this->ceh->select(" e.$colOrderName AS OrderNo, SHIPPER_NAME, e.PersonalID, e.NameDD, e.CJMode_CD, UPPER(dm.CJModeName) AS CJModeName
                        , e.IssueDate, e.Note, e.ShipID, vs.ShipName, e.ImVoy, e.BerthDate, '' AS Remark, us.UserName
                        , e.ExVoy, e.ExpDate, e.BookingNo, e.CntrNo, e.OprID, e.ISO_SZTP, e.Status, e.SealNo, e.SealNo1, e.IsLocal, e.DMethod_CD
                        , e.InvNo
                        , CASE WHEN e.CJMode_CD = 'CAPR' OR e.CJMode_CD IN (SELECT CJMode_CD FROM DELIVERY_MODE WHERE ischkCFS = 1)
                                THEN e.BookingNo
                                ELSE ISNULL(e.BLNo, e.BookingNo)
                                END AS BL_BKNo
                        , cm.CusName, ct.Description as CARGO_TYPE_NAME
                        , '' AS POL, e.POD, e.FPOD, e.ISO_SZTP, e.CMDWeight, e.ExpPluginDate, e.LocalSZPT
                        , e.Temperature, e.Vent, e.Vent_Unit, $unno AS UNNO, $class AS CLASS, e.OOG_TOP, e.OOG_LEFT, e.OOG_RIGHT, e.OOG_BACK, e.OOG_FRONT
                        , ISNULL( cd.cBlock + '-' + cd.cBay + '-' + cd.cRow + '-' + cd.cTier, cd.cArea ) AS YardPos, cd.cTLHQ, cd.DateIn, e.PinCode, e.rowguid
                        , e.DRAFT_INV_NO, (" . $oprExpDate . ") AS OprExpDate, vsc.ETA, vsc.ETD, '' AS FreeDays, '' AS RetLocation, e.InvNo, e.CARGO_TYPE
                        , (" . $getOrderType . ") OrderType")
            ->join("CUSTOMERS cm", "cm.CusID = e.CusID AND cm.CusType = e.PAYMENT_TYPE AND cm.YARD_ID = e.YARD_ID", 'left')
            ->join("CARGO_TYPE ct", "ct.Code = e.CARGO_TYPE AND ct.YARD_ID = e.YARD_ID", 'left')
            ->join("SA_USERS us", "us.UserID = e.CreatedBy AND us.YARD_ID = e.YARD_ID", 'left')
            ->join("DELIVERY_MODE dm", "dm.CJMode_CD = e.CJMode_CD AND dm.YARD_ID = e.YARD_ID", 'left')
            ->join("VESSELS vs", "vs.ShipID = e.ShipID AND vs.YARD_ID = e.YARD_ID", 'left')
            ->join("VESSEL_SCHEDULE vsc", "vsc.ShipKey = e.ShipKey AND vsc.YARD_ID = e.YARD_ID", 'left')
            ->join("CNTR_DETAILS cd", "cd.CntrNo = e.CntrNo AND cd.ShipKey = e.ShipKey
                                    AND cd.CntrClass = e.CntrClass AND cd.CMStatus = 'S' AND cd.YARD_ID = e.YARD_ID", 'left')
            ->where("e.YARD_ID", $this->yard_id);

        if ($args["PinCode"] != "") {
            $this->ceh->like("e.PinCode", $args["PinCode"], 'after');
        }

        if ($args["OrderNo"] != "") {
            $this->ceh->where($args["OrderType"] == "NH" ? "e.EIRNo" : "e.SSOderNo", $args["OrderNo"]);
        }

        if ($args["CntrNo"] != "") {
            $this->ceh->where("e.CntrNo", $args["CntrNo"]);
        }

        if ($args["InvNo"] != "") {
            $this->ceh->where("e.InvNo", $args["InvNo"]);
        }

        $sql = $this->ceh->order_by('e.PinCode')->get($tableName);
        $outInfo = $sql->result_array();
        foreach ($outInfo as $key => $value) {
            $pngAbsoluteFilePath = FCPATH . "assets/img/qrcode_gen/" . $value['PinCode'] . ".png";
            $this->funcs->generateQRCode($value['PinCode']);
            $qrCodeData = base64_encode(file_get_contents($pngAbsoluteFilePath));
            $outInfo[$key]['QrData'] = 'data: ' . mime_content_type($pngAbsoluteFilePath) . ';base64,' . $qrCodeData;
        }

        return $outInfo;
    }

    public function getInv4Print($pinCode)
    {
        $this->ceh->select("iv.INV_NO, iv.INV_DATE, iv.AMOUNT AS SUB_AMOUNT, iv.VAT, iv.TAMOUNT, iv.CURRENCYID
                            , cm.CusName, cm.Address, cm.VAT_CD AS PAYER, us.UserName
                            , un.UNIT_NM, idd.TRF_CODE, idd.TRF_DESC + ' ' + idd.SZ + idd.FE + ' - ' + idd.CARGO_TYPE AS TRF_DESC
                            , idd.QTY, idd.UNIT_RATE, idd.AMOUNT, idd.VAT_RATE, idd.Remark, idd.TRF_DESC_MORE
                            , CASE WHEN iv.ShipKey IS NOT NULL THEN (vv.ShipName + '/' + vsc.ImVoy + '/' + vsc.ExVoy) ELSE '' END AS VESSEL_INFO"); //them moi hd thu sau
        $this->ceh->join("INV_DFT id", "id.INV_NO = iv.INV_NO AND id.YARD_ID = iv.YARD_ID");
        $this->ceh->join("INV_DFT_DTL idd", 'idd.DRAFT_INV_NO = id.DRAFT_INV_NO AND idd.YARD_ID = id.YARD_ID', 'left');
        $this->ceh->join("CUSTOMERS cm", 'cm.CusID = iv.PAYER AND cm.YARD_ID = cm.YARD_ID');
        $this->ceh->join("UNIT_CODES un", 'un.UNIT_CODE = idd.INV_UNIT AND un.YARD_ID = idd.YARD_ID', 'left');
        $this->ceh->join("SA_USERS us", "us.UserID = iv.CreatedBy AND us.YARD_ID = iv.YARD_ID", 'left');
        $this->ceh->join("VESSEL_SCHEDULE vsc", "vsc.ShipKey = iv.ShipKey AND vsc.YARD_ID = iv.YARD_ID", 'left'); //them moi hd thu sau
        $this->ceh->join('VESSELS vv', 'vv.ShipID = iv.ShipID AND vv.YARD_ID = iv.YARD_ID', 'left'); //them moi hd thu sau

        $this->ceh->where("iv.PinCode", $pinCode);
        $this->ceh->where("iv.YARD_ID", $this->yard_id);
        return $this->ceh->get("INV_VAT iv")->result_array();
    }
}
