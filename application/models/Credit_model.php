<?php
defined('BASEPATH') or exit('');

class Credit_model extends CI_Model
{
    private $ceh;
    private $UC = 'UNICODE';
    private $yard_id = "";

    function __construct()
    {
        parent::__construct();
        $this->ceh = $this->load->database('mssql', TRUE);

        $this->yard_id = $this->config->item("YARD_ID");
    }

    public function generatePinCode($digits = 8)
    {
        $chk = array();

        do {
            $nb = rand(1, pow(10, $digits) - 1);
            $nb = substr("0000000" . $nb, -8);
            $chk = $this->ceh->select('COUNT(*) CountID')
                ->where('PinCode', $nb)
                ->where('YARD_ID', $this->yard_id)
                ->limit(1)
                ->get('INV_VAT')->row_array();
        } while ($chk['CountID'] > 0);

        return $nb;
    }

    public function searchShip($arrStatus = '', $year = '', $name = '')
    {
        $this->ceh->select('vs.ShipKey, vv.ShipName, vs.ShipID, vs.ShipYear, vs.ShipVoy, vs.ImVoy, vs.ExVoy, vs.ETB, vs.ETD, vs.BerthDate');
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
        $this->ceh->select('CusID, CusName, Address, VAT_CD, CusType, IsOpr, IsAgency, IsOwner, IsLogis, IsTrans, IsOther, Email, EMAIL_DD');
        if ($user != '' && $user != 'Admin')
            $this->ceh->where('NameDD', $user);

        $this->ceh->where('VAT_CD IS NOT NULL');
        $this->ceh->where('CusType', 'C');
        $this->ceh->where('YARD_ID', $this->yard_id);

        $this->ceh->order_by('CusName', 'ASC');
        $stmt = $this->ceh->get('CUSTOMERS');
        return $stmt->result_array();
    }

    public function getOpr($args = array())
    {
        $this->ceh->select("CusID, CusName");
        $this->ceh->where("IsOpr", 1);
        $this->ceh->where('YARD_ID', $this->yard_id);

        $this->ceh->order_by('CusName', 'ASC');
        $stmt = $this->ceh->get('CUSTOMERS');
        return $stmt->result_array();
    }

    public function getDMethods($args = array())
    {
        $this->ceh->select("DMethod_CD, DMethod_Name");
        $this->ceh->where('YARD_ID', $this->yard_id);

        $this->ceh->order_by('DMethod_Name', 'ASC');
        $stmt = $this->ceh->get('DELIVERY_METHODS');
        return $stmt->result_array();
    }

    public function getTransits($args = array())
    {
        $this->ceh->select("Transit_CD, Transit_Name");
        $this->ceh->where('YARD_ID', $this->yard_id);

        $this->ceh->order_by('Transit_Name', 'ASC');
        $stmt = $this->ceh->get('Transit_Mode');
        return $stmt->result_array();
    }

    public function getYardJobs($args = array())
    {
        $this->ceh->select("CJMode_CD, CJModeName, IsYardSRV, ischkCFS");
        $this->ceh->where('YARD_ID', $this->yard_id);
        $this->ceh->group_start();
        $this->ceh->where('IsYardSRV', '1');
        $this->ceh->or_where_in('ischkCFS', array('1', '2'));
        $this->ceh->group_end();

        $this->ceh->order_by('CJMode_CD', 'ASC');
        $stmt = $this->ceh->get('DELIVERY_MODE');
        return $stmt->result_array();
    }

    public function getCntrClass($args = array())
    {
        $this->ceh->select("CLASS_Code, CLASS_Name");
        $this->ceh->where('YARD_ID', $this->yard_id);

        $this->ceh->order_by('CLASS_Code', 'ASC');
        $stmt = $this->ceh->get('CLASS_MODE');
        return $stmt->result_array();
    }

    public function getInvTemp()
    {
        return $this->ceh->distinct()->select("TPLT_NM, TPLT_DESC, CURRENCYID")
            ->get("INV_TPLT")
            ->result_array();
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


    public function loadShipTotal($args = array())
    {

        $this->ceh->select("QJ.rowguid, QJ.ShipKey, QJ.CntrClass, QJ.Job_Type_RF, QJ.MASTER_ROWGUID, QJ.ShipID,
        QJ.ShipYear, QJ.ShipVoy, 
   QJ.CntrNo, QJ.OprID, QJ.ISO_SZTP, QJ.Status,QJ.BILL_CHK, QJ.CARGO_TYPE,
    QJ.IsLocal,QJ.Transist, 
   CASE 
       WHEN QJ.CntrClass = 1 THEN QJ.CJMODE_CD 
       WHEN QJ.CntrClass = 3 THEN QJ.CJMODE_OUT_CD 
       ELSE '' 
       END AS CJMode_CD, 
   QJ.CJMODE_OUT_CD, 
   CASE
       WHEN QJ.CntrClass = 1 THEN QJ.DMethod_CD
       WHEN QJ.CntrClass = 3 THEN QJ.DMethod_OUT_CD
       ELSE ''
       END AS DMethod_CD,
   CL.CLASS_Name,
   CG.Description,
   TM.Transit_Name,
   ISNULL( JB.NameGate, ISNULL( JB.NameYard, JB.NameQuay )) AS JobName");
        $this->ceh->join("CLASS_MODE AS CL", "CL.CLASS_Code = QJ.CntrClass", "LEFT");
        $this->ceh->join("CARGO_TYPE AS CG", "CG.Code = QJ.CARGO_TYPE", "LEFT");
        $this->ceh->join("Transit_Mode AS TM", "TM.Transit_CD = QJ.Transist", "LEFT");
        $this->ceh->join("ALLJOB_TYPE AS JB", "JB.Code = QJ.Job_Type_RF", "LEFT");
        $this->ceh->where("QJ.Fdate IS NOT NULL");
        $this->ceh->where("QJ.PAYMENT_TYPE", 'C');

        $this->ceh->where("QJ.YARD_ID", $this->yard_id);

        //where by shipkey
        if (isset($args["shipKey"]) && $args["shipKey"] != "") {
            $this->ceh->where("QJ.ShipKey", $args["shipKey"]);
        }

        //where by cntrClass
        if (isset($args["cntrClass"]) && $args["cntrClass"] != "") {
            $this->ceh->where("QJ.CntrClass", $args["cntrClass"]);
        }

        //where by isLocal
        if (isset($args["isLocal"]) && $args["isLocal"] != "") {
            $this->ceh->where("QJ.IsLocal", $args["isLocal"]);
        }

        //where by transit
        if (isset($args["transit"]) && $args["transit"] != "") {
            $this->ceh->where("QJ.Transist", $args["transit"]);
        }

        //where by status
        if (isset($args["status"]) && $args["status"] != "") {
            $this->ceh->where("QJ.Status", $args["status"]);
        }

        //where by oprs
        if (isset($args["oprs"]) && $args["oprs"] != '') {
            $this->ceh->where_in("QJ.OprID", $args["oprs"]);
        }

        //where by loai hang
        if (isset($args["cargoType"]) && $args["cargoType"] != '') {
            $this->ceh->where_in("QJ.CARGO_TYPE", $args["cargoType"]);
        }

        if (isset($args["cjmode"]) && $args["cjmode"] != '') {
            $this->ceh->where_in("CASE WHEN QJ.CntrClass = '1' THEN QJ.CJMODE_CD WHEN QJ.CntrClass = '3' THEN QJ.CJMODE_OUT_CD END", $args["cjmode"]);
        }

        if (isset($args["dmethod"]) && $args["dmethod"] != '') {
            $this->ceh->where_in("CASE WHEN QJ.CntrClass = '1' THEN QJ.DMethod_CD WHEN QJ.CntrClass = '3' THEN QJ.DMethod_OUT_CD END", $args["dmethod"]);
        }

        if (isset($args["localSZPT"]) && $args["localSZPT"] != '') {
            $finalWhere = "";
            foreach ($args["localSZPT"] as $k => $v) {
                switch ($v) {
                    case '20':
                        $finalWhere = sprintf("(%s %s LEFT(QJ.ISO_SZTP,2) = '22')", $finalWhere, empty($finalWhere) ? "" : "OR");
                        break;
                    case '40':
                        $finalWhere = sprintf("(%s %s LEFT(QJ.ISO_SZTP,2) in ('42','45'))", $finalWhere, empty($finalWhere) ? "" : "OR");
                        break;
                    default:
                        $finalWhere = sprintf("(%s %s LEFT(QJ.ISO_SZTP,2) not in ('22','42','45'))", $finalWhere, empty($finalWhere) ? "" : "OR");
                        break;
                }
            }
            $this->ceh->where($finalWhere);
        }

        $this->ceh->order_by("QJ.Fdate");

        $stmt = $this->ceh->get("QUAYJOB AS QJ");
        $stmt = $stmt->result_array();

        $newarray = array();

        foreach ($stmt as $k => $v) {
            $newarray[$v["OprID"] . "-" . $v["Job_Type_RF"] . "-" . $v["CntrClass"]][$k] = $v;
        }

        $result = array();
        foreach ($newarray as $key => $value) {
            if (is_array($value)) {
                $bySize = array(
                    "OprID" => array_column($value, "OprID")[0],
                    "JobName" => array_column($value, "JobName")[0],
                    "CLASS_Name" => array_column($value, "CLASS_Name")[0],
                    "SZ_20F" => 0,
                    "SZ_40F" => 0,
                    "SZ_45F" => 0,
                    "SZ_20E" => 0,
                    "SZ_40E" => 0,
                    "SZ_45E" => 0
                );

                foreach ($value as $n => $m) {
                    $size = "SZ_" . $this->getContSize($m["ISO_SZTP"]) . $m["Status"];
                    if ($bySize[$size] != 0) {
                        $bySize[$size] += 1;
                    } else {
                        $bySize[$size] = 1;
                    }
                }

                array_push($result, $bySize);
            }
        }

        return array("DETAIL" => $stmt, "SUM" => $result);
    }

    public function loadContLiftTotal($args = array())
    {

        $this->ceh->select("G.rowguid,
        G.EIRNo,
        G.CntrNo,
        G.OprID,
        G.ISO_SZTP,
        G.Status,
        G.CARGO_TYPE,
        G.ShipKey,
        G.ShipID,
        G.ImVoy,
        G.ExVoy,
        G.cGateJob,
        G.CJMode_CD,
        G.DMethod_CD,
        G.Status,
        G.CntrClass,
        G.IsLocal,
        G.BILL_CHK,
        G.CusID,
        G.Transist,
        CL.CLASS_Name,
        CG.Description,
        TM.Transit_Name, ISNULL( JB.NameGate, ISNULL( JB.NameYard, JB.NameQuay ) ) AS JobName");
        $this->ceh->join("CLASS_MODE AS CL", "CL.CLASS_Code = G.CntrClass", "LEFT");
        $this->ceh->join("CARGO_TYPE AS CG", "CG.Code = G.CARGO_TYPE", "LEFT");
        $this->ceh->join("Transit_Mode AS TM", "TM.Transit_CD = G.Transist", "LEFT");
        $this->ceh->join("ALLJOB_TYPE AS JB", "JB.Code = G.cGateJob", "LEFT");
        $this->ceh->where_in("G.cGateJob", array("GO", "GF"));
        $this->ceh->where("G.PAYMENT_TYPE", 'C');
        $this->ceh->where_not_in("G.CJMode_CD", array("NGTH", "XGTH"));


        //where by TimeIn
        if (isset($args["formDate"]) && $args["formDate"] != "") {
            $this->ceh->where("G.TimeIn >=", $this->funcs->dbDateTime($args["formDate"]));
        }

        if (isset($args["toDate"]) && $args["toDate"] != "") {
            $this->ceh->where("G.TimeIn <=", $this->funcs->dbDateTime($args["toDate"] . " 23:59:59"));
        }

        if (isset($args["cusID"]) && $args["cusID"] != "") {
            $this->ceh->where("G.CusID", $args["cusID"]);
        }

        //where by shipkey
        if (isset($args["shipKey"]) && $args["shipKey"] != "") {
            $this->ceh->where("G.ShipKey", $args["shipKey"]);
        }

        //where by cntrClass
        if (isset($args["cntrClass"]) && $args["cntrClass"] != "") {
            $this->ceh->where("G.CntrClass", $args["cntrClass"]);
        }

        //where by dmethod
        if (isset($args["dmethod"]) && $args["dmethod"] != "") {
            $this->ceh->where("G.DMethod_CD", $args["dmethod"]);
        }

        //where by isLocal
        if (isset($args["isLocal"]) && $args["isLocal"] != "") {
            $this->ceh->where("G.IsLocal", $args["isLocal"]);
        }

        //where by transit
        if (isset($args["transit"]) && $args["transit"] != "") {
            $this->ceh->where("G.Transist", $args["transit"]);
        }

        //where by status
        if (isset($args["status"]) && $args["status"] != "") {
            $this->ceh->where("G.Status", $args["status"]);
        }

        //where by loai hang
        if (isset($args["cargoType"]) && $args["cargoType"] != '') {
            $this->ceh->where_in("G.CARGO_TYPE", $args["cargoType"]);
        }

        //where by oprs
        if (isset($args["oprs"]) && $args["oprs"] != '') {
            $this->ceh->where_in("G.OprID", $args["oprs"]);
        }

        if (isset($args["localSZPT"]) && $args["localSZPT"] != '') {
            $finalWhere = "";
            foreach ($args["localSZPT"] as $k => $v) {
                switch ($v) {
                    case '20':
                        $finalWhere = sprintf("(%s %s LEFT(G.ISO_SZTP,2) = '22')", $finalWhere, empty($finalWhere) ? "" : "OR");
                        break;
                    case '40':
                        $finalWhere = sprintf("(%s %s LEFT(G.ISO_SZTP,2) in ('42','45'))", $finalWhere, empty($finalWhere) ? "" : "OR");
                        break;
                    default:
                        $finalWhere = sprintf("(%s %s LEFT(G.ISO_SZTP,2) not in ('22','42','45'))", $finalWhere, empty($finalWhere) ? "" : "OR");
                        break;
                }
            }
            $this->ceh->where($finalWhere);
        }

        //where by cjmode
        if (isset($args["cjmode"]) && $args["cjmode"] != '') {
            $this->ceh->where_in("G.CJMode_CD", $args["cjmode"]);
        }

        $this->ceh->order_by("G.TimeIn", 'ASC');
        $this->ceh->order_by("G.CusID", 'ASC');

        $stmt = $this->ceh->get("GATE_MONITOR AS G");
        $stmt = $stmt->result_array();

        $newarray = array();

        foreach ($stmt as $k => $v) {
            $newarray[$v["OprID"] . "-" . $v["cGateJob"] . "-" . $v["CntrClass"]][$k] = $v;
        }

        $result = array();
        foreach ($newarray as $key => $value) {
            if (is_array($value)) {
                $bySize = array(
                    "OprID" => array_column($value, "OprID")[0],
                    "JobName" => array_column($value, "JobName")[0],
                    "CLASS_Name" => array_column($value, "CLASS_Name")[0],
                    "SZ_20F" => 0,
                    "SZ_40F" => 0,
                    "SZ_45F" => 0,
                    "SZ_20E" => 0,
                    "SZ_40E" => 0,
                    "SZ_45E" => 0
                );

                foreach ($value as $n => $m) {
                    $size = "SZ_" . $this->getContSize($m["ISO_SZTP"]) . $m["Status"];
                    if ($bySize[$size] != 0) {
                        $bySize[$size] += 1;
                    } else {
                        $bySize[$size] = 1;
                    }
                }

                array_push($result, $bySize);
            }
        }

        return array("DETAIL" => $stmt, "SUM" => $result);
    }

    public function loadShipService($shipkey)
    {
        $sql = sprintf("
        select 
vsrv.FromDate as fShifting, vsrv.ToDate as tShifting
,vsvd.CJMode_CD, vsvd.CJModeName, vsvd.CJMode_SL
, vs.nGrt, vs.nDwt, vs.nLoa
,vssc.Agency, vssc.ETA, vssc.ETB
,na.NationName
From NOTICE_VERSSEL_SERVICE vsrv
left join NOTICE_VERSSEL_SERVICE_DTL vsvd on vsrv.rowguid = vsvd.MASTER_ROWGUID
left join VESSEL_SCHEDULE vssc on vsrv.shipkey = vssc.ShipKey
left join VESSELS vs on  vs.ShipID = vssc.ShipID 
left join NATIONALITIES na on vs.Nation_CD = na.Nation_CD
where vsrv.shipkey ='%s'
        ", $shipkey);

        $stmt = $this->ceh->query($sql)->result_array();

        $result = array();
        $info = array();

        if (isset($stmt) && count($stmt)) {
            $info = array(
                'fShifting' => $stmt[0]['fShifting'],
                'tShifting' => $stmt[0]['tShifting'],
                'NationName' => $stmt[0]['NationName'],
                'Agency' => $stmt[0]['Agency'],
                'ETA' => $stmt[0]['ETA'],
                'ETB' => $stmt[0]['ETB'],
                'Loa' => $stmt[0]['nLoa'],
                'Grt' => $stmt[0]['nGrt'],
                'Dwt' => $stmt[0]['nDwt']
            );
        }

        foreach ($stmt as $key => $value) {
            if (is_array($value)) {
                $service = array(
                    'CJMode_CD' => $value['CJMode_CD'],
                    'CJModeName' => $value['CJModeName'],
                    'CJMode_SL' => $value['CJMode_SL'],
                );
                array_push($result, $service);
            }
        }
        return array('service' => $result, 'info' => $info);
    }

    public function loadYardServiceTotal($args = array())
    {

        $this->ceh->select("SRV.rowguid, SRV.CntrClass, SSOderNo, CntrNo, OprID, ISO_SZTP, SRV.Status, CARGO_TYPE
                                , SRV.ShipKey, SRV.ShipID, VV.ShipVoy, VV.ShipYear
                                , SRV.CJMode_CD, SRV.IsLocal
                                , (CASE WHEN SRV.DRAFT_INV_NO IS NULL THEN 0
                                       ELSE 1 END) AS BILL_CHK
                                , DMethod_CD
                                , CusID
                                , CL.CLASS_Name
                                , CG.Description
                                , DM.CJModeName AS JobName");
        $this->ceh->join("CLASS_MODE AS CL", "CL.CLASS_Code = SRV.CntrClass AND CL.YARD_ID = SRV.YARD_ID", "LEFT");
        $this->ceh->join("CARGO_TYPE AS CG", "CG.Code = SRV.CARGO_TYPE AND CG.YARD_ID = SRV.YARD_ID", "LEFT");
        $this->ceh->join("DELIVERY_MODE AS DM", "DM.CJMode_CD = SRV.CJMode_CD AND DM.YARD_ID = SRV.YARD_ID", "LEFT");
        $this->ceh->join("VESSEL_SCHEDULE AS VV", "VV.ShipKey = SRV.ShipKey AND VV.ShipID = SRV.ShipID AND VV.YARD_ID = SRV.YARD_ID", "LEFT");
        $this->ceh->where("SRV.FDate IS NOT NULL");
        $this->ceh->where("SRV.PAYMENT_TYPE", 'C');

        $this->ceh->where("SRV.YARD_ID", $this->yard_id);

        //where by FDate
        if (isset($args["formDate"]) && $args["formDate"] != "") {
            $this->ceh->where("SRV.FDate >=", $this->funcs->dbDateTime($args["formDate"]));
        }

        if (isset($args["toDate"]) && $args["toDate"] != "") {
            $this->ceh->where("SRV.FDate <=", $this->funcs->dbDateTime($args["toDate"] . " 23:59:59"));
        }

        if (isset($args["cusID"]) && $args["cusID"] != "") {
            $this->ceh->where("SRV.CusID", $args["cusID"]);
        }

        //where by shipkey
        if (isset($args["shipKey"]) && $args["shipKey"] != "") {
            $this->ceh->where("SRV.ShipKey", $args["shipKey"]);
        }

        //where by cntrClass
        if (isset($args["cntrClass"]) && $args["cntrClass"] != "") {
            $this->ceh->where("SRV.CntrClass", $args["cntrClass"]);
        }

        //where by dmethod
        if (isset($args["dmethod"]) && $args["dmethod"] != "") {
            $this->ceh->where("SRV.DMethod_CD", $args["dmethod"]);
        }

        //where by isLocal
        if (isset($args["isLocal"]) && $args["isLocal"] != "") {
            $this->ceh->where("SRV.IsLocal", $args["isLocal"]);
        }

        //where by status
        if (isset($args["status"]) && $args["status"] != "") {
            $this->ceh->where("SRV.Status", $args["status"]);
        }

        //where by oprs
        if (isset($args["oprs"]) && count($args["oprs"]) > 0) {
            $this->ceh->where_in("SRV.OprID", $args["oprs"]);
        }

        //where by cjmode
        if (isset($args["cjmode"]) && count($args["cjmode"]) > 0) {
            $this->ceh->where_in("SRV.CJMode_CD", $args["cjmode"]);
        }

        if (isset($args["jobTypes"]) && count($args["jobTypes"]) > 0) {
            $firstJob = $args["jobTypes"][0];

            $this->ceh->group_start();

            $this->ceh->where($firstJob["key"], $firstJob["value"]);

            unset($args["jobTypes"][$firstJob["key"]]);

            foreach ($args["jobTypes"] as $key => $item) {
                $this->ceh->or_where($item["key"], $item["value"]);
            }

            $this->ceh->group_end();
        }

        $this->ceh->order_by("SRV.FDate", 'ASC');

        $stmt = $this->ceh->get("SRV_ODR AS SRV");
        $stmt = $stmt->result_array();

        $newarray = array();

        foreach ($stmt as $k => $v) {
            $newarray[$v["OprID"] . "-" . $v["CJMode_CD"] . "-" . $v["CntrClass"]][$k] = $v;
        }

        $result = array();
        foreach ($newarray as $key => $value) {
            if (is_array($value)) {
                $bySize = array(
                    "OprID" => array_column($value, "OprID")[0],
                    "JobName" => array_column($value, "JobName")[0],
                    "CLASS_Name" => array_column($value, "CLASS_Name")[0],
                    "SZ_20F" => 0,
                    "SZ_40F" => 0,
                    "SZ_45F" => 0,
                    "SZ_20E" => 0,
                    "SZ_40E" => 0,
                    "SZ_45E" => 0
                );

                foreach ($value as $n => $m) {
                    $size = "SZ_" . $this->getContSize($m["ISO_SZTP"]) . $m["Status"];
                    if ($bySize[$size] != 0) {
                        $bySize[$size] += 1;
                    } else {
                        $bySize[$size] = 1;
                    }
                }

                array_push($result, $bySize);
            }
        }

        return array("DETAIL" => $stmt, "SUM" => $result);
    }

    public function loadPlugTotal($args)
    {
        $this->ceh->select("rf.rowguid, ShipKey, CntrClass, CLASS_Name, CntrNo, CHUONGCAM, CHUONGRUT, ShipID, ShipYear, ShipVoy, Fdate, Job_Type_RF
                            , Status, ISO_SZTP, OprID, CBLOCK, CBAY, CROW, CTIER, CAREA, CVBAY, CVROW, CVTIER, Temperature, DateIn
                            , DateOut, DatePlugIn, DatePlugOut, BILL_CHK, TIME");
        $this->ceh->join("CLASS_MODE AS CL", "CL.CLASS_Code = rf.CntrClass AND CL.YARD_ID = rf.YARD_ID", "LEFT");

        $this->ceh->where("rf.YARD_ID", $this->yard_id);
        $this->ceh->where("DatePlugOut IS NOT NULL");
        $this->ceh->where("Status", "F");
        $this->ceh->where("payment_type", "C");

        $this->ceh->where("DatePlugOut >=", $this->funcs->dbDateTime($args["fromDate"]));
        $this->ceh->where("DatePlugOut <=", $this->funcs->dbDateTime($args["toDate"]));
        $this->ceh->where_in("OprID", $args['oprs']);

        if ($args['cntrClass'] != '' && $args['cntrClass'] != '*') {
            $this->ceh->where("OprID", $args['cntrClass']);
        }

        $this->ceh->order_by("BILL_CHK", "DESC");
        $stmt = $this->ceh->get("RF_ONOFF rf");
        return $stmt->result_array();
    }



    private function filter_trf_dis($inputs, $fwheres, $mskey)
    { //$mskey là khóa (tên cột) để xác định dòng/item sẽ được remove khỏi $inputs nếu k thỏa điêu kiện
        if(count($fwheres) == 0) {
            return $inputs;
        }
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
            if (count($inputs) > 0) {
                unset($fwheres[$k]);
                return $this->filter_trf_dis($inputs, $fwheres, $mskey);
            } else {
                return $inputs;
            }
        }
        return array();
    }

    public function loadTariffSTD($listeir, $invTemp)
    {
        $sql = 'SELECT * FROM TRF_STD WHERE ( rowguid IN ( SELECT STD_ROW_ID FROM INV_TPLT WHERE TPLT_NM = ? ) )';
        $sql .= ' AND ((CONVERT(date, ?, 104) >= CONVERT(date, FROM_DATE, 104) and TO_DATE = \'*\') or
                    (CONVERT(date, ?, 104) between CONVERT(date, FROM_DATE, 104) AND CONVERT(date, TO_DATE, 104)))';

        $sql .= ' AND (YARD_ID = ?)';

        $wheres = array(
            $invTemp,
            date('d/m/Y'),
            date('d/m/Y'),
            $this->yard_id
        );

        $result = array();
        $final_result = array();

        if (isset($listeir) && is_array($listeir)) {
            foreach ($listeir as $item) {
                $stmt = $this->ceh->query($sql, $wheres);
                $stmt = $stmt->result_array();
                //nếu có job_type_rf -> dịch vụ tàu , ngược lại là nâng hạ
                $JOB_KIND = isset($item["Job_Type_RF"])
                    ? $item["Job_Type_RF"]
                    : (($item['CJMode_CD'] == 'LAYN' || $item['CJMode_CD'] == 'NTAU' || $item['CJMode_CD'] == 'CAPR')
                        ? "GO"
                        : (($item['CJMode_CD'] == 'HBAI' || $item['CJMode_CD'] == 'TRAR')
                            ? "GF"
                            : "*"));

                if (count($stmt) > 1) {
                    $fwhere = array(
                        'IX_CD' => $item['CntrClass'],
                        'JOB_KIND' => $JOB_KIND,
                        'CARGO_TYPE' => $item['CARGO_TYPE'],
                        'DMETHOD_CD' => isset($item['DMethod_CD']) ? $item['DMethod_CD'] : "*",
                        'CNTR_JOB_TYPE' => $item['CJMode_CD'],
                        'IsLocal' =>  isset($item['IsLocal']) ? $item['IsLocal'] : "*"
                    );

                    // nếu có job_type_rf -> dv tàu -> bỏ đk filter theo CNTR_JOB_TYPE
                    if (isset($item["Job_Type_RF"])) {
                        unset($fwhere["CNTR_JOB_TYPE"]);
                    }

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
                    $result['CJMode_CD'] = isset($item['CJMode_CD']) ? $item['CJMode_CD'] : $result['CNTR_JOB_TYPE'];
                    $result['ISO_SZTP'] = $item['ISO_SZTP'];
                    $result['FE'] = $item['Status'];
                    $result['CntrNo'] = $item['CntrNo'];
                    $result['OprID'] = $item['OprID'];
                    $result['IssueDate'] = isset($item['IssueDate']) ? $item['IssueDate'] : date("Y-m-d H:i:s");

                    array_push($final_result, $result);
                } else {
                    $cjmode = isset($item['CJMode_CD']) ? "[" . $item['CJMode_CD'] . "]" : '';
                    array_push($final_result, "$cjmode không tìm thấy biểu cước phù hợp!");
                }
            }
        }

        return $final_result;
    }

    public function loadShipServices($listServices)
    {
        $sql = 'SELECT * FROM TRF_STD WHERE (TRF_CODE = ?)';
        $sql .= ' AND ((CONVERT(date, ?, 104) >= CONVERT(date, FROM_DATE, 104) and TO_DATE = \'*\') or
                    (CONVERT(date, ?, 104) between CONVERT(date, FROM_DATE, 104) AND CONVERT(date, TO_DATE, 104)))';
        $sql .= 'AND (CURRENCYID = ?)';
        $sql .= ' AND (YARD_ID = ?)';

        $final_result = array();
        if (isset($listServices) && is_array($listServices)) {
            foreach ($listServices as $item) {
                $result = array();
                $wheres = array(
                    $item['CJMode_CD'],
                    date('d/m/Y'),
                    date('d/m/Y'),
                    $item['currency'],
                    $this->yard_id
                );

                $stmt = $this->ceh->query($sql, $wheres);
                $stmt = $stmt->result_array();

                if (count($stmt) > 1) {
                    $fwhere = array(
                        'JOB_KIND' => '*',
                        'CNTR_JOB_TYPE' => $item['CJMode_CD'],
                        'CURRENCYID' => $item['currency']
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
                    $result['CJMode_CD'] = $item['CJMode_CD'];
                    $result['Quantity'] = $item['CJMode_SL'];
                    array_push($final_result, $result);
                } else {
                    $cjmode = $item['CJMode_CD'];
                    array_push($final_result, "[$cjmode] Không tìm thấy biểu cước phù hợp!");
                }
            }
        }
        return $final_result;
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

    public function save_draft_invoice($tableName, $args, &$outInfo)
    {
        //get invoice info
        $invInfo = (isset($args['invInfo']) && count($args['invInfo']) > 0) ? $args['invInfo'] : array();

        $invContents = array();
        $pinCode = "";
        $checkDraftNo = array();
        $draftMarker = array();
        $draftNo = $this->generateDraftNo();

        if (count($invInfo) > 0) {
            $pinCode = $invInfo['fkey'];
            $invContents = array(
                "INV_NO_PRE" => $invInfo['invno'],
                "INV_PREFIX" => $invInfo['serial'],
                "DRAFT_NO" => $draftNo,
                "PIN_CODE" => $pinCode
            );
        } else {
            //generate số pin
            $args['draft_total']['PinCode'] = $pinCode;

            if (isset($args["pubType"])) {
                if ($args["pubType"] == 'm-inv') // trường hợp xuất hóa đơn tay
                {
                    $session_inv_info = json_decode($this->session->userdata("invInfo"), true); // lấy thông tin hóa đơn đc lưu trữ trong biến session

                    $invContents = array(
                        "INV_NO_PRE" => $session_inv_info['invno'],
                        "INV_PREFIX" => $session_inv_info['serial'],
                        "DRAFT_NO" => $draftNo,
                        "PIN_CODE" => $pinCode
                    );

                    //trả về thông tin hóa đơn
                    array_push($outInfo, [
                        "invno" => $session_inv_info['invno'],
                        "serial" => $session_inv_info['serial'],
                        "fkey" => $pinCode
                    ]);
                } elseif ($args["pubType"] == 'credit') {
                    //trả về thông tin số PIN
                    array_push($outInfo, ["PinCode" => $pinCode]);
                }
            }
        }

        $updateInfos = array();
        $datas = $args["datas"];
        foreach ($datas as $key => $item) {
            $updItem = array(
                "rowguid" => $item["rowguid"],
                "BILL_CHK" => 1,
                "ModifiedBy" => $this->session->userdata("UserID"),
                "update_time" => date('Y-m-d H:i:s')
            );

            if ($tableName == "SRV_ODR") {
                unset($updItem["BILL_CHK"]);
            }

            //update inv info into
            if (count($invContents) > 0) {
                $invNoColumnName = $tableName == "QUAYJOB" ? "INV_NO" : "InvNo";
                $updItem[$invNoColumnName] = isset($invContents["INV_PREFIX"]) ? $invContents["INV_PREFIX"] . $invContents["INV_NO_PRE"] : NULL;
            }

            // $markerKey = isset($item["Job_Type_RF"]) ? "Job_Type_RF" : "CJMode_CD";
            $markerKey = "CJMode_CD";

            if ($args["pubType"] == 'dft') {
                $updItem["DRAFT_INV_NO"] = isset($checkDraftNo[$item[$markerKey]])
                    ? $checkDraftNo[$item[$markerKey]]
                    : $this->generateDraftNo();

                if (in_array($updItem["DRAFT_INV_NO"], array_column($draftMarker, "DRAFT_INV_NO"))) {
                    $tempDft = explode("/", $updItem["DRAFT_INV_NO"]);
                    $updItem["DRAFT_INV_NO"] = $tempDft[0] . "/" . $tempDft[1] . "/" . substr('000000' . (intval($tempDft[2]) + 1), -6);
                }

                $draftMarker[$item[$markerKey]] = array(
                    'PinCode' => $pinCode,
                    "DRAFT_INV_NO" => $updItem["DRAFT_INV_NO"]
                );
            } else {
                $updItem["DRAFT_INV_NO"] = $draftNo;
            }

            $checkDraftNo[$item[$markerKey]] = $updItem["DRAFT_INV_NO"];

            array_push($updateInfos, $updItem);
            // $this->ceh->where('rowguid', $item["rowguid"])->update( $tableName, $updItem );
        }

        $continue_proccess = true;
        $outputMsg = "";
        if ($args["pubType"] == 'dft') {
            //set Inv Content to args
            $args["DRAFT_MARKER"] = $draftMarker;

            //trả về thông tin phiếu tính cước
            foreach ($draftMarker as $key => $value) {
                array_push($outInfo, array(
                    "PinCode" => $value['PinCode'],
                    "DRAFT_NO" => $value["DRAFT_INV_NO"]
                ));
            }

            $continue_proccess = $this->saveSplitDraft($args, $datas[0], $outputMsg);
        } else {
            if (count($invContents) > 0) {
                //set Inv Content to args
                $args["INV_CONTENT"] = $invContents;

                $arrCntrRowguids = array_column($datas, "MASTER_ROWGUID");
                $continue_proccess = $this->saveInvoice($args, $datas[0], $arrCntrRowguids, $outputMsg);
            }
        }

        if ($continue_proccess) {
            $this->ceh->trans_start();
            $this->ceh->trans_strict(FALSE);
            ////////////////////////////////////////////////////

            $this->ceh->update_batch($tableName, $updateInfos, 'rowguid');

            ////////////////////////////////////////////////////
            $this->ceh->trans_complete();

            if ($this->ceh->trans_status() === FALSE) {
                $this->ceh->trans_rollback();
                return $this->ceh->_error_message();
            } else {
                $this->ceh->trans_commit();
                return 'success';
            }
        } else {
            return $outputMsg;
        }
    }

    public function saveSplitDraft($args, $order, &$outMsg)
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
                "REF_NO" => NULL,
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
                "PAYMENT_STATUS" => "Y", //$order['PAYMENT_TYPE'] == "C" ? "U" : "Y",
                "REF_TYPE" => isset($draft_total['PUBLISH_BY']) ? $draft_total['PUBLISH_BY'] : NULL,
                "CURRENCYID" => $draft_details[0]["CURRENCYID"],
                "RATE" => 1,
                "INV_TYPE" => "CRE", //$order['PAYMENT_TYPE'] == "C" ? "CRE" : "CAS",
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

            unset($dd['REF_NO'], $dd['JobMode'], $dd['ISO_SZTP'], $dd['CURRENCYID']);
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
            $outMsg = $this->ceh->_error_message();
            $this->ceh->trans_rollback();
            return FALSE;
        } else {
            $this->ceh->trans_commit();
            return TRUE;
        }
    }

    public function saveInvoice($args, $order, $cntrRowguids, &$outMsg)
    {
        if (!is_array($args) || count($args) == 0) return true;

        $draft_details = array();
        if (isset($args['draft_detail']) && count($args['draft_detail'])) {
            $draft_details = $args['draft_detail'];
        }
        $currencyId = $args['currencyId'] ? $args['currencyId'] : $draft_details[0]["CURRENCYID"];

        $draft_total = array();
        if (isset($args['draft_total']) && count($args['draft_total'])) {
            $draft_total = $args['draft_total'];
        }

        $invPrefix = isset($args["INV_CONTENT"]["INV_PREFIX"]) ? $args["INV_CONTENT"]["INV_PREFIX"] : "";
        $invNoPre = isset($args["INV_CONTENT"]["INV_NO_PRE"]) ? $args["INV_CONTENT"]["INV_NO_PRE"] : "";
        $invDate = isset($args['invInfo']['INV_DATE']) ? $args['invInfo']['INV_DATE'] : date('Y-m-d H:i:s');
        $draftno = $args["INV_CONTENT"]["DRAFT_NO"];
        $pincode = $args["INV_CONTENT"]["PIN_CODE"];

        //get inv draft
        $inv_draft = array(
            "DRAFT_INV_NO" => $draftno,
            "INV_NO" => $invPrefix . $invNoPre != "" ? $invPrefix . $invNoPre : NULL,
            "DRAFT_INV_DATE" => date('Y-m-d H:i:s'),
            "REF_NO" => NULL,
            "ShipKey" => $order['ShipKey'],
            "ShipID" => $order['ShipID'],
            "ShipYear" => $order['ShipYear'],
            "ShipVoy" => $order['ShipVoy'],
            "PAYER_TYPE" => $order['PAYER_TYPE'],
            "PAYER" => $order['CusID'],
            "OPR" => $order['OprID'],
            "AMOUNT" => (float)str_replace(',', '', $draft_total['AMOUNT']),
            "VAT" => (float)str_replace(',', '', $draft_total['VAT']),
            "DIS_AMT" => (float)str_replace(',', '', $draft_total['DIS_AMT']),
            "PAYMENT_STATUS" => "Y", //$order['PAYMENT_TYPE'] == "C" ? "U" : "Y",
            "REF_TYPE" => isset($draft_total['PUBLISH_BY']) ? $draft_total['PUBLISH_BY'] : NULL,
            "CURRENCYID" => $draft_details[0]["CURRENCYID"],
            "RATE" => 1,
            "INV_TYPE" => "CRE", //$order['PAYMENT_TYPE'] == "C" ? "CRE" : "CAS",
            "INV_TYPE_2" => "L",
            "TPLT_NM" => "EB",
            "TAMOUNT" => (float)str_replace(',', '', $draft_total['TAMOUNT']),
            "PinCode" => isset($draft_total['PinCode']) ? $draft_total['PinCode'] : NULL,
            "ACC_CD" => isset($draft_total['ACC_CD']) ? $draft_total['ACC_CD'] : NULL,
            "ModifiedBy" => $this->session->userdata("UserID"),
            "update_time" => date('Y-m-d H:i:s'),
            "CreatedBy" => $this->session->userdata("UserID")
        );

        //get inv draft details
        $inv_draft_details = array();
        foreach ($draft_details as $idx => $dd) {
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

            unset($dd['REF_NO'], $dd['JobMode'], $dd['ISO_SZTP'], $dd['CURRENCYID']);
            array_push($inv_draft_details, $dd);
        }

        //get inv VAT
        if ($invPrefix . $invNoPre != "") {
            $inv_vat = array(
                "INV_NO" => $invPrefix . $invNoPre,
                "INV_DATE" => $invDate,
                "REF_NO" => NULL,
                "ShipKey" => $order['ShipKey'],
                "ShipID" => $order['ShipID'],
                "ShipYear" => $order['ShipYear'],
                "ShipVoy" => $order['ShipVoy'],
                "PAYER_TYPE" => $order['PAYER_TYPE'],
                "PAYER" => $order['CusID'],
                "OPR" => $order['OprID'],
                "AMOUNT" => (float)str_replace(',', '', $draft_total['AMOUNT']),
                "VAT" => (float)str_replace(',', '', $draft_total['VAT']),
                "DIS_AMT" => (float)str_replace(',', '', $draft_total['DIS_AMT']),
                "PAYMENT_STATUS" => "Y", //$order['PAYMENT_TYPE'] == "C" ? "U" : "Y",
                "REF_TYPE" => isset($draft_total['PUBLISH_BY']) ? $draft_total['PUBLISH_BY'] : NULL,
                "CURRENCYID" => $currencyId,
                "RATE" => 1,
                "INV_TYPE" => "CRE", //$order['PAYMENT_TYPE'] == "C" ? "CRE" : "CAS",
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

        $inv_draft["YARD_ID"] = $this->yard_id;

        $this->ceh->insert('INV_DFT', $inv_draft);
        foreach ($inv_draft_details as $item) {

            $item["YARD_ID"] = $this->yard_id;

            $this->ceh->insert('INV_DFT_DTL', $item);
        }

        if (isset($inv_vat) && count($inv_vat) > 0) {
            $inv_vat["YARD_ID"] = $this->yard_id;
            $this->ceh->insert('INV_VAT', $inv_vat);

            if ($this->session->userdata("invInfo") !== null && $args["pubType"] == 'm-inv') {
                $session_inv_info = json_decode($this->session->userdata("invInfo"), TRUE);

                //nếu đã đến số cuối cùng thì remove invInfo để user tự set lại
                if ($session_inv_info["invno"] == $session_inv_info["toNo"]) {
                    $this->session->unset_userdata('invInfo');
                } else {
                    //set laij soo hóa đơn tay tăng lên 1
                    $session_inv_info["invno"] = intval($session_inv_info["invno"]) + 1;
                    $this->session->set_userdata("invInfo", json_encode($session_inv_info));
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
            $outMsg = $this->ceh->_error_message();
            $this->ceh->trans_rollback();
            return FALSE;
        } else {
            $this->ceh->trans_commit();
            return TRUE;
        }
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

    public function getDraftTemp()
    {
        $this->ceh->select('DRAFT_INV_NO');
        $this->ceh->where("YARD_ID", $this->yard_id);
        $this->ceh->order_by('DRAFT_INV_NO', 'DESC');
        $stmt = $this->ceh->limit(1)->get('INV_DFT');
        $stmt = $stmt->row_array();
        if ($stmt['DRAFT_INV_NO'] === null) {
            return 'DR/' . date('Y') . '/000001';
        } else {
            $tmp = explode('/', $stmt['DRAFT_INV_NO']);
            if (count($tmp) == 0) return 'DR/' . date('Y') . '/000001';
            if ($tmp[1] !== date('Y')) return 'DR/' . date('Y') . '/000001';

            return 'DR/' . date('Y') . '/' . substr('000000' . ((int)$tmp[2] + 1), -6);
        }
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


    public function getDiscount($sz, $fe, $wheres)
    {
        array_push($wheres, $this->yard_id);

        $sql = 'SELECT rowguid, AMT_' . $fe . $sz . ' AMT, VAT, FIX_RATE, Opr, PAYER, CARGO_TYPE, IX_CD, DMETHOD_CD, JOB_KIND, CNTR_JOB_TYPE, CURRENCYID, IsLocal FROM TRF_DIS';
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
        $sql .= ' AND (IsLocal = ? OR IsLocal = \'*\')';
        $sql .= ' AND (LANE = ? OR LANE = \'*\')';
        $sql .= ' AND (PAYMENT_TYPE = \'CRE\' OR PAYMENT_TYPE = \'*\')';
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

    public function getLaneID($shipkey = '')
    {
        $this->ceh->select("l.LaneID");
        $this->ceh->where(sprintf('l.LaneID IN (select LaneID from VESSEL_SCHEDULE WHERE ShipKey = \'%1$s\')', $shipkey));

        $this->ceh->where('l.YARD_ID', $this->yard_id);

        $stmt = $this->ceh->get('LANE_FPOD l');
        return $stmt->row()->LaneID;
    }

    //Thu sau
    public function filterOprInQJ($shipkey)
    {
        $this->ceh->distinct();
        $this->ceh->select('QJ.OprID, cm.CusName');
        $this->ceh->join('CUSTOMERS cm', "QJ.OprID = cm.CusID", 'left');
        $this->ceh->where("cm.IsOpr", 1);
        $this->ceh->where("QJ.ShipKey", $shipkey);
        $this->ceh->order_by('cm.CusName', 'ASC');

        $stmt = $this->ceh->get("QUAYJOB AS QJ");
        $stmt = $stmt->result_array();
        return $stmt;
    }

    public function filerCagoTypeInQJ($shipkey)
    {
        $this->ceh->distinct();
        $this->ceh->select('QJ.CARGO_TYPE , cg.Description');
        $this->ceh->join('CARGO_TYPE cg', "QJ.CARGO_TYPE = cg.Code", 'left');
        $this->ceh->where('cg.Code != ', '*');
        $this->ceh->where("QJ.ShipKey", $shipkey);
        $this->ceh->order_by('cg.Description', 'ASC');

        $stmt = $this->ceh->get("QUAYJOB AS QJ");
        $stmt = $stmt->result_array();
        return $stmt;
    }

    public function getCreServiceDis($wheres)
    {
        array_push($wheres, $this->yard_id);

        $sql = 'SELECT rowguid, AMT_NCNTR AMT, VAT, FIX_RATE, Opr, PAYER, CARGO_TYPE, IX_CD, DMETHOD_CD, JOB_KIND, CNTR_JOB_TYPE, CURRENCYID, IsLocal FROM TRF_DIS';
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
        $sql .= ' AND (PAYMENT_TYPE = \'CRE\' OR PAYMENT_TYPE = \'*\')';
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
        // if (count($result) > 0) {
        //     $result = count(array_keys($result)) == 1 ? reset($result) : $result;
        //     if ($result['FIX_RATE'] == 1) {
        //         $unit_rate = $this->getUnitRate($sz, $fe, $wheres[8], $wheres[4], $wheres[9]);
        //         return $unit_rate * ($result['AMT'] !== null ? $result['AMT'] : 0) * 0.01;
        //     } else {
        //         return $result['AMT'] !== null ? $result['AMT'] : 0;
        //     }
        // }

        return 0;
    }
}
