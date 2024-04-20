<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Task extends CI_Controller
{

    public $data;
    private $ceh;

    function __construct()
    {
        parent::__construct();

        if (empty($this->session->userdata('UserID'))) {
            redirect(md5('user') . '/' . md5('login'));
        }

        $this->load->helper(array('form', 'url'));
        $this->load->model("task_model", "mdltask");
        $this->load->model("common_model", "mdlcommon");
        $this->load->model("user_model", "user");

        $this->ceh = $this->load->database('mssql', TRUE);
        $this->data['menus'] = $this->menu->getMenu();
    }

    public function _remap($method)
    {
        $methods = get_class_methods($this);

        $skip = array("_remap", "__construct", "get_instance");
        $a_methods = array();

        if (($method == 'index')) {
            $method = md5('index');
        }

        foreach ($methods as $smethod) {
            if (!in_array($smethod, $skip)) {
                $a_methods[] = md5($smethod);
                if ($method == md5($smethod)) {
                    $this->$smethod();
                    break;
                }
            }
        }

        if (!in_array($method, $a_methods)) {
            // show_404();
            $this->show_developing();
        }
    }

    private function show_developing()
    {
        $this->data['title'] = "Ohhhh";

        $this->load->view('header', $this->data);

        $this->load->view('errors/html/error_developing');
        $this->load->view('footer');
    }

    public function tskEDO()
    {
        $access = $this->user->access('tskEDO');
        if ($access === false) {
            show_404();
        }

        if (strlen($access) > 5) {
            $this->data['deny'] = $access;
            echo json_encode($this->data);
            exit;
        }

        $action = $this->input->post('action') ? $this->input->post('action') : '';
        if ($action == "view") {
            $act = $this->input->post('act') ? $this->input->post('act') : '';

            if ($act == 'load_data') {
                $oprs = $this->input->post('oprs') ? $this->input->post('oprs') : array();
                $fromDate = $this->input->post('fromDate') ? $this->input->post('fromDate') : '';
                $toDate = $this->input->post('toDate') ? $this->input->post('toDate') : '';
                $searchVal = $this->input->post('searchVal') ? $this->input->post('searchVal') : '';

                $this->data["results"] = $this->mdltask->loadDO($oprs, $fromDate, $toDate, $searchVal);
                echo json_encode($this->data);
                exit;
            }
        }

        if ($action == "add") {
            $args = $this->input->post('args') ? $this->input->post('args') : array();

            $this->data["message"] = $this->mdltask->saveBooking($args);

            echo json_encode($this->data);
            exit;
        }

        if ($action == "edit") {
            $data = $this->input->post('data') ? $this->input->post('data') : array();

            $this->data["message"] = $this->mdltask->updateDO($data);

            echo json_encode($this->data);
            exit;
        }

        $this->data['title'] = 'Quản lý EDI Delivery Order (EDO)';

        $this->load->view('header', $this->data);

        $this->load->view('task/edo', $this->data);
        $this->load->view('footer');
    }

    public function tskBooking()
    {
        $access = $this->user->access('tskBooking');
        if ($access === false) {
            show_404();
        }

        if (strlen($access) > 5) {
            $this->data['deny'] = $access;
            echo json_encode($this->data);
            exit;
        }

        $action = $this->input->post('action') ? $this->input->post('action') : '';
        if ($action == "view") {
            $act = $this->input->post('act') ? $this->input->post('act') : '';

            if ($act == 'load_cntr_for_booking') {
                $this->data["conts"] = $this->mdltask->loadCntrForBooking();
                echo json_encode($this->data);
                exit;
            }

            if ($act == 'load_cntr_for_edit') {
                $args = $this->input->post('args') ? $this->input->post('args') : array();
                if (count($args)  == 0) {
                    echo json_encode(array('conts' => array()));
                    exit;
                }
                $conts = $this->mdltask->loadCntrForUpdateBooking($args);
                echo json_encode(array('conts' => $conts));
                exit;
            }

            if ($act == 'load_booking') {
                $args = $this->input->post('args') ? $this->input->post('args') : '';
                $this->data["list"] = $this->mdltask->loadBooking($args);
                echo json_encode($this->data);
                exit;
            }

            if ($act == 'searh_ship') {
                $arrstt = $this->input->post('arrStatus') ? $this->input->post('arrStatus') : '';
                $year = $this->input->post('shipyear') ? $this->input->post('shipyear') : '';
                $name = $this->input->post('shipname') ? $this->input->post('shipname') : '';

                $this->data['vsls'] = $this->mdltask->searchShip($arrstt, $year, $name);
                echo json_encode($this->data);
                exit;
            }

            if ($act == 'getLane') {
                $shipkey = $this->input->post('shipkey') ? $this->input->post('shipkey') : '';
                $this->data['ports'] = $this->mdltask->getLanePortID($shipkey);
                echo json_encode($this->data);
                exit;
            }

            if ($act == 'search_cntr') {
                $cntrNo = $this->input->post('cntrNo') ? $this->input->post('cntrNo') : '';
                $result = $this->ceh->select('BLNo')->where('CntrNo', $cntrNo)->limit(1)->get('CNTR_DETAILS')->row_array();
                if (count($result) > 0) {
                    $this->data['BLNo'] = $result['BLNo'];
                }
                echo json_encode($this->data);
                exit;
            }

            if ($act == 'load_payment') {
                $list = $this->input->post('list') ? $this->input->post('list') : array();
                $cusID = $this->input->post('cusID') ? $this->input->post('cusID') : '';
                $this->calculate_payment($list, $cusID, 'services');
                exit;
            }
        }

        if ($action == "add") {
            $args = $this->input->post('args') ? $this->input->post('args') : array();

            $this->data["message"] = $this->mdltask->saveBooking($args);

            echo json_encode($this->data);
            exit;
        }

        if ($action == "edit") {
            $data = $this->input->post('data') ? $this->input->post('data') : array();

            $this->data["message"] = $this->mdltask->updateBooking($data);

            echo json_encode($this->data);
            exit;
        }

        if ($action == "delete") {
            $data = $this->input->post('data') ? $this->input->post('data') : array();

            if (count($data) > 0) {
                $outMsg = '';
                $success = $this->mdltask->deleteBooking($data, $outMsg);
                if (!$success) {
                    $this->data['error'] = $outMsg;
                }
            } else {
                $this->data['error'] = 'Không có gì để xoá!';
            }

            echo json_encode($this->data);
            exit;
        }

        $this->data['title'] = 'Đăng ký booking';

        $this->data["oprs"] = $this->mdlcommon->getOprs();
        $this->data["sztps"] = $this->mdlcommon->getSizeType();
        $this->data['cargoTypes'] = $this->mdltask->getCargoTypes();

        $this->load->view('header', $this->data);

        $this->load->view('task/booking', $this->data);
        $this->load->view('footer');
    }

    public function tskServiceOrder()
    {
        $access = $this->user->access('tskServiceOrder');
        if ($access === false) {
            show_404();
        }

        if (strlen($access) > 5) {
            $this->data['deny'] = $access;
            echo json_encode($this->data);
            exit;
        }

        $action = $this->input->post('action') ? $this->input->post('action') : '';
        if ($action == "view") {
            $act = $this->input->post('act') ? $this->input->post('act') : '';
            if ($act == 'load_payer') {
                $this->data['payers'] = $this->mdltask->getPayers();
                echo json_encode($this->data);
                exit;
            }

            if ($act == 'search_cus_by_tax') {
                $taxCode = $this->input->post('taxCode') ? $this->input->post('taxCode') : '';
                $cus = $this->mdltask->getPayerByTaxCode($taxCode);
                echo json_encode(array('cus' => $cus));
                exit;
            }

            if ($act == 'search_bill') {
                $blno = $this->input->post('billNo') ? $this->input->post('billNo') : '';
                $cntrNo = $this->input->post('cntrNo') ? $this->input->post('cntrNo') : '';
                $this->data['list'] = $this->mdltask->load_service_orders($blno, $cntrNo);
                echo json_encode($this->data);
                exit;
            }
            if ($act == 'search_cntr') {
                $cntrNo = $this->input->post('cntrNo') ? $this->input->post('cntrNo') : '';
                $result = $this->ceh->select('BLNo')->where('CntrNo', $cntrNo)->limit(1)->get('CNTR_DETAILS')->row_array();
                if (count($result) > 0) {
                    $this->data['BLNo'] = $result['BLNo'];
                }
                echo json_encode($this->data);
                exit;
            }
            if ($act == 'load_payment') {
                $list = $this->input->post('list') ? $this->input->post('list') : array();
                $cusID = $this->input->post('cusID') ? $this->input->post('cusID') : '';

                if ($cusID == '') {
                    array_push($this->data["no_payer"], "Vui lòng chọn lại đối tượng thanh toán!");
                    json_encode($this->data);
                    exit;
                }

                if ($list[0]["CJMode_CD"] == "SDD") {
                    $totalPluginHour = [];
                    $seq = 1;
                    $sddDesc = [];
                    $this->data["error_plugin"] = array();

                    foreach ($list as $key => $value) {
                        //reg key for diff cont
                        $sz = $this->getContSize($value['ISO_SZTP']);
                        if ($sz == '0') {
                            //không đúng size => unset $list để k phải tính tiền cont điện lạnh này
                            unset($list[$key]);

                            array_push($this->data["error_plugin"], "Container [" . $value["CntrNo"] . "] - ISO Size không đúng!");
                            continue;
                        }

                        $difKeyPlugin = $sz . '-' . $value['Status'] . '-' . $value['CARGO_TYPE'] . '-' . $value['IsLocal'];

                        // get plugin from
                        $pluginInRF_ONOFF = $this->mdltask->getPluginDate($value["CntrNo"], $value["ShipKey"], $value["CntrClass"]);

                        if ($pluginInRF_ONOFF == '') {
                            //không thể lấy được ngày cắm điện của cont này => unset $list để k phải tính tiền cont điện lạnh này
                            unset($list[$key]);

                            array_push($this->data["error_plugin"], "Container [" . $value["CntrNo"] . "] - không tìm thấy thời gian cắm điện!");
                            continue;
                        }

                        $pluginFrom = strtotime($pluginInRF_ONOFF);

                        //get plugin to
                        $pluginTo = strtotime($this->funcs->dbDateTime($value["ExpPluginDate"]));

                        if ($pluginFrom >= $pluginTo) {
                            //thời gian tính tiền cắm điện không hợp lý
                            unset($list[$key]);

                            array_push($this->data["error_plugin"], "Container [" . $value["CntrNo"] . "] - Hạn điện phải lớn hơn (>) thời gian cắm điện!");
                            continue;
                        }

                        $plHour = $this->calcTimePlugin($value["OprID"], $pluginFrom, $pluginTo);
                        if (!$plHour) {
                            //không có cấu hình
                            unset($list[$key]);
                            array_push($this->data["error_plugin"], "Hãng khai thác [" . $value["OprID"] . "] chưa được cấu hình tính điện lạnh!");
                            continue;
                        }

                        $oldSum = isset($totalPluginHour[$difKeyPlugin]) ? $totalPluginHour[$difKeyPlugin] : 0;
                        $totalPluginHour[$difKeyPlugin] = $oldSum + $plHour;

                        $list[$key]["PTI_Hour"] = "0";
                        $list[$key]["DMethod_CD"] = NULL;

                        $strNote = sprintf(
                            "Điện lạnh container <b>%s</b><br><br>Từ <b>%s</b> - đến <b>%s</b><br>Tổng thời gian: <b>%s</b> giờ",
                            $value["CntrNo"],
                            $this->funcs->clientDateTime($pluginInRF_ONOFF),
                            $value["ExpPluginDate"],
                            $plHour
                        );

                        //add them thong tin dien lanh vao draft detail
                        // $sddDesc .= sprintf(
                        //     "%s: %s - %s|", //CNTR0123456: 2021/01/01 00:00 - 2021/01/02 23:59|
                        //     $value["CntrNo"],
                        //     $this->funcs->clientDateTime($pluginInRF_ONOFF, "/"),
                        //     $value["ExpPluginDate"]
                        // );
                        if ($sddDesc[$difKeyPlugin]) {
                            $sddDesc[$difKeyPlugin] .= sprintf(
                                // "%s: %s - %s|", //CNTR0123456: 2021/01/01 00:00 - 2021/01/02 23:59|
                                "%s|", //CNTR0123456: 2021/01/01 00:00 - 2021/01/02 23:59|
                                $value["CntrNo"],
                                // $this->funcs->clientDateTime($pluginInRF_ONOFF, "/"),
                                // $value["ExpPluginDate"]
                            );
                        } else {
                            $sddDesc[$difKeyPlugin] = sprintf(
                                // "%s: %s - %s|", //CNTR0123456: 2021/01/01 00:00 - 2021/01/02 23:59|
                                "%s|", //CNTR0123456: 2021/01/01 00:00 - 2021/01/02 23:59|
                                $value["CntrNo"],
                                // $this->funcs->clientDateTime($pluginInRF_ONOFF, "/"),
                                // $value["ExpPluginDate"]
                            );
                        }
                        // -- add them thong tin dien lanh vao draft detail

                        $this->data["ps_notify"] = isset($this->data["ps_notify"])
                            ? $this->data["ps_notify"] . "<br/><hr/>" . $strNote
                            : $strNote;

                        $list[$key]["EIR_SEQ"] = $seq++;
                    }
                    if (count($list) == 0) {
                        echo json_encode($this->data);
                        exit();
                    }

                    $addInforOf = array(
                        "Quantity" => array("SDD" => $totalPluginHour),
                        "TRF_DESC_MORE" => $sddDesc
                    );

                    $this->calculate_payment($list, $cusID, 'services', $addInforOf);
                } elseif ($list[0]["CJMode_CD"] == "LBC") {
                    $totalDayInYard = [];
                    $freeContInYard = [];
                    $seq = 1;
                    $lbcDesc = "";

                    foreach ($list as $key => $value) {
                        //reg key for diff cont
                        $sz = $this->getContSize($value['ISO_SZTP']);

                        $difKeyStorage = $sz . '-' . $value['Status'] . '-' . $value['CARGO_TYPE'] . '-' . $value['IsLocal'];

                        //storage from date
                        $storageFrom = strtotime($this->funcs->dbDateTime(explode(' ', $value["DateIn"])[0]));

                        //storage to date
                        $storageTo = strtotime($this->funcs->dbDateTime(explode(' ', $value["ExpDate"])[0]));

                        //get storage free date follow OprID
                        $freeDays = $this->mdltask->getStorageFreeDay($value["OprID"], $value['CntrClass'], $value['Status'], $value['CARGO_TYPE']);

                        $daysinYard = ceil(($storageTo - $storageFrom) / (60 * 60 * 24) - $freeDays + 1);
                        $daysinYard = $daysinYard > 0 ? $daysinYard : 0;

                        $strNote = sprintf(
                            "Lưu bãi container <b>%s</b><br><br>
                                                            Từ <b>%s</b> - đến <b>%s</b><br>Miễn phí <b>%s</b> ngày<br>Tổng thời gian: <b>%s</b> ngày",
                            $value["CntrNo"],
                            $this->funcs->clientDateTime($value["DateIn"]),
                            $value["ExpDate"],
                            $freeDays,
                            $daysinYard
                        );

                        $this->data["ps_notify"] = isset($this->data["ps_notify"])
                            ? $this->data["ps_notify"] . "<br/><hr/>" . $strNote
                            : $strNote;

                        if ($daysinYard == 0) {
                            array_push($freeContInYard, $value["CntrNo"]);
                            unset($list[$key]);
                            continue;
                        }

                        //add them thong tin luu bai vao draft detail
                        $lbcDesc .= sprintf(
                            // "%s: %s - %s|", //CNTR0123456: 2021/01/01 00:00 - 2021/01/02 23:59|
                            "%s|", //CNTR0123456: 2021/01/01 00:00 - 2021/01/02 23:59|
                            $value["CntrNo"],
                            // $this->funcs->clientDateTime($value["DateIn"], "/"),
                            // $value["ExpDate"]
                        );
                        // -- add them thong tin luu bai vao draft detail

                        $oldSum = isset($totalDayInYard[$difKeyStorage]) ? $totalDayInYard[$difKeyStorage] : 0;
                        $totalDayInYard[$difKeyStorage] = $oldSum + $daysinYard;

                        $lbc[$key]["PTI_Hour"] = "0";
                        $lbc[$key]["DMethod_CD"] = NULL;

                        $lbc[$key]["EIR_SEQ"] =  $seq++;
                    }

                    if (count($freeContInYard) > 0) {
                        $this->data["freeContInYard"] = $freeContInYard;
                    }

                    if (count($totalDayInYard) > 0 && array_sum(array_values($totalDayInYard)) > 0) {
                        $this->calculate_payment($list, $cusID, 'services', ["Quantity" => ["LBC" => $totalDayInYard], "TRF_DESC_MORE" => $lbcDesc]);
                    } else {
                        echo json_encode($this->data);
                        exit;
                    }
                } else {
                    $this->calculate_payment($list, $cusID, 'services');
                }

                exit;
            }
        }

        if ($action == "save") {
            $act = $this->input->post('act') ? $this->input->post('act') : '';

            if ($act == 'use_manual_Inv') {
                $data = $this->input->post('useInvData') ? $this->input->post('useInvData') : array();

                //them moi hd thu sau
                $this->setSessionInvInfo($data);
                echo true;
                exit;
            }

            if ($act == 'save_new_payer') {
                $taxCode = $this->input->post('taxCode') ? $this->input->post('taxCode') : '';
                $cusName = $this->input->post('cusName') ? $this->input->post('cusName') : '';
                $address = $this->input->post('address') ? $this->input->post('address') : '';

                $errorMsg = "";
                $result = $this->mdlcommon->savePayerQuickly($taxCode, $cusName, $address, $errorMsg);

                if (!$result['success']) {
                    $this->data["error"] = $errorMsg;
                } else {
                    $this->data["saveType"] = $errorMsg;
                }

                if ($result['success'] === TRUE && isset($result['newCus']) && count($result['newCus']) > 0) {
                    $this->load->model("interfaceOracle_model", "mdlOracle");
                    $this->data['transfer_result'] = $this->mdlOracle->transferNewCustomer($result['newCus']);
                }

                echo json_encode($this->data);
                exit;
            }

            $data = $this->input->post('data') ? $this->input->post('data') : array();

            //them moi hd thu sau
            if (!$this->hasSessionInvInfo($data["pubType"])) {
                echo json_encode($this->data);
                exit();
            }

            $outInfo = array();

            $this->data['message'] = $this->mdltask->save_SRV_ODR_INV($data, "", $outInfo);

            if (isset($data['invInfo'])) {
                $this->data['invInfo'] = $data['invInfo'];
            } else {
                if (isset($data["pubType"])) {
                    if ($data["pubType"] == "m-inv") {
                        $this->data['invInfo'] = $data['invInfo'] = $outInfo[0];
                        $outInfo = [];
                    } else {
                        $this->data['dftInfo'] = $outInfo;
                    }
                }
            }

            // /send mail
            $mailTo = isset($data["odr"][0]["Mail"]) ? $data["odr"][0]["Mail"] : "";
            $argMailInfo = array();

            if ($mailTo != "") {
                if (count($outInfo) > 0) {
                    foreach ($outInfo as $key => $item) {
                        $arrTemp = array(
                            "mailTo" => $mailTo,
                            "pinCode" => $item["PinCode"],
                        );

                        if (isset($item["DRAFT_NO"])) {
                            $arrTemp["draftNo"] = $item["DRAFT_NO"];
                        }

                        array_push($argMailInfo, $arrTemp);
                    }

                    if ($outInfo[0]["PinCode"] != "") {
                        $this->funcs->generateQRCode($outInfo[0]["PinCode"]);
                    }
                } else {
                    $pinCode = (isset($data['invInfo']['fkey']) ? $data['invInfo']['fkey'] : "");
                    $itemMail = array(
                        "mailTo" => $mailTo
                    );

                    if (isset($data['invInfo']) && count($data['invInfo']) > 0) {
                        $itemMail["inv"] = $data['invInfo']['serial'] . $data['invInfo']['invno'];
                    }
                    $itemMail["reservationCode"] = $data['invInfo']['reservationCode']  ?? '';
                    $itemMail["pinCode"] = $pinCode;

                    if ($pinCode != "") {
                        $this->funcs->generateQRCode($pinCode);
                    }

                    array_push($argMailInfo, $itemMail);
                }

                if (count($argMailInfo) > 0) {
                    $n = isset($data["odr"][0]) ? $data["odr"][0] : array();
                    foreach ($argMailInfo as $item) {
                        $item["CusID"] = isset($n["CusID"]) ? $n["CusID"] : null;
                        $item["PersonalID"] = isset($n["PersonalID"]) ? $n["PersonalID"] : null;
                        $item["NameDD"] = isset($n["NameDD"]) ? $n["NameDD"] : null;
                        $this->sendmail($item);
                    }
                }
            }

            echo json_encode($this->data);
            exit;
        }

        $this->data['title'] = 'Lệnh dịch vụ';

        $this->load->view('header', $this->data);
        $this->data['services'] = $this->mdltask->getServices(['isYardSRV' => 1, 'IsNonContSRV' => 1]);
        $this->data['paymentMethod'] = $this->mdltask->getPaymentMethod();

        //them moi hd thu sau
        $this->data["isDup"] = false;
        if (($this->session->userdata("invInfo") !== null && count(json_decode($this->session->userdata("invInfo"), true)) > 0)) {
            //them moi hd thu sau
            $ssInvInfo = json_decode($this->session->userdata("invInfo"), true);
            $ssInvInfoCas = $ssInvInfo['CAS'];

            $this->data["ssInvInfo"] = $ssInvInfoCas;
            $this->data["isDup"] = $this->mdlInv->checkInvNo($ssInvInfo['serial'], $ssInvInfo['invno']);
        }

        $this->load->view('task/service_order', $this->data);
        $this->load->view('footer');
    }

    public function tskStuffingOrder()
    {
        $access = $this->user->access('tskStuffingOrder');
        if ($access === false) {
            show_404();
        }

        if (strlen($access) > 5) {
            $this->data['deny'] = $access;
            echo json_encode($this->data);
            exit;
        }

        $action = $this->input->post('action') ? $this->input->post('action') : '';
        if ($action == "view") {
            $act = $this->input->post('act') ? $this->input->post('act') : '';
            if ($act == 'load_payer') {
                $this->data['payers'] = $this->mdltask->getPayers();
                echo json_encode($this->data);
                exit;
            }

            if ($act == 'search_cus_by_tax') {
                $taxCode = $this->input->post('taxCode') ? $this->input->post('taxCode') : '';
                $cus = $this->mdltask->getPayerByTaxCode($taxCode);
                echo json_encode(array('cus' => $cus));
                exit;
            }

            if ($act == 'searh_ship') {
                $arrstt = $this->input->post('arrStatus') ? $this->input->post('arrStatus') : '';
                $year = $this->input->post('shipyear') ? $this->input->post('shipyear') : '';
                $name = $this->input->post('shipname') ? $this->input->post('shipname') : '';

                $this->data['vsls'] = $this->mdltask->searchShip($arrstt, $year, $name);
                echo json_encode($this->data);
                exit;
            }
            if ($act == 'search_barge') {
                $this->data['barges'] = $this->mdltask->getBarge();
                echo json_encode($this->data);
                exit;
            }
            if ($act == 'load_booking') {
                $bkno = $this->input->post('bkno') ? $this->input->post('bkno') : '';
                $cntrno = $this->input->post('cntrno') ? $this->input->post('cntrno') : '';
                $this->data['bookinglist'] = $this->mdltask->getBookingList($bkno, $cntrno);
                echo json_encode($this->data);
                exit;
            }
            if ($act == 'getLane') {
                $shipkey = $this->input->post('shipkey') ? $this->input->post('shipkey') : '';
                $this->data['oprs'] = $this->mdltask->getLaneOprs($shipkey);
                $this->data['ports'] = $this->mdltask->getLanePortID($shipkey);
                echo json_encode($this->data);
                exit;
            }

            if ($act == 'load_attach_srv') {
                $orderType = $this->input->post('order_type') ? $this->input->post('order_type') : '';
                $this->data['lists'] = $this->mdltask->getAttachServices($orderType);

                echo json_encode($this->data);
                exit;
            }

            if ($act == 'load_payment') {
                $cusID = $this->input->post('cusID') ? $this->input->post('cusID') : '';

                if ($cusID == '') {
                    array_push($this->data["no_payer"], "Vui lòng chọn lại đối tượng thanh toán!");
                    json_encode($this->data);
                    exit;
                }

                $list = $this->input->post('list') ? $this->input->post('list') : array();

                $nonAttach = $this->input->post('nonAttach') ? $this->input->post('nonAttach') : array();

                if (count($nonAttach) > 0) {
                    $this->calculate_payment($nonAttach, $cusID, 'services', array('calc_continue' => '1'));
                    $this->calculate_payment($list, $cusID, 'services', array('calc_continue' => '1'));
                    echo json_encode($this->data);
                    exit();
                }

                $this->calculate_payment($list, $cusID, 'services');
                exit;
            }
        }

        if ($action == "save") {
            $act = $this->input->post('act') ? $this->input->post('act') : '';

            if ($act == 'use_manual_Inv') {
                $data = $this->input->post('useInvData') ? $this->input->post('useInvData') : array();

                //them moi hd thu sau
                $this->setSessionInvInfo($data);
                echo true;
                exit;
            }

            if ($act == 'save_new_payer') {
                $taxCode = $this->input->post('taxCode') ? $this->input->post('taxCode') : '';
                $cusName = $this->input->post('cusName') ? $this->input->post('cusName') : '';
                $address = $this->input->post('address') ? $this->input->post('address') : '';

                $errorMsg = "";
                $result = $this->mdlcommon->savePayerQuickly($taxCode, $cusName, $address, $errorMsg);

                if (!$result['success']) {
                    $this->data["error"] = $errorMsg;
                } else {
                    $this->data["saveType"] = $errorMsg;
                }

                // if ($result['success'] === TRUE && isset($result['newCus']) && count($result['newCus']) > 0) {
                //     $this->load->model("interfaceOracle_model", "mdlOracle");
                //     $this->data['transfer_result'] = $this->mdlOracle->transferNewCustomer($result['newCus']);
                // }

                echo json_encode($this->data);
                exit;
            }

            $data = $this->input->post('data') ? $this->input->post('data') : array();

            //them moi hd thu sau
            if (!$this->hasSessionInvInfo($data["pubType"])) {
                echo json_encode($this->data);
                exit();
            }

            $outInfo = array();

            $this->data['message'] = $this->mdltask->save_SRV_ODR_INV($data, "STUFF_CHK", $outInfo); // đánh dấu đóng hàng "STUFF_CHK"

            if (isset($data['invInfo'])) {
                $this->data['invInfo'] = $data['invInfo'];
            } else {
                if (isset($data["pubType"])) {
                    if ($data["pubType"] == "m-inv") {
                        $this->data['invInfo'] = $data['invInfo'] = $outInfo[0];
                        $outInfo = [];
                    } else {
                        $this->data['dftInfo'] = $outInfo;
                    }
                }
            }

            // /send mail
            $mailTo = isset($data["odr"][0]["Mail"]) ? $data["odr"][0]["Mail"] : "";
            $argMailInfo = array();

            if ($mailTo != "") {
                if (count($outInfo) > 0) {
                    foreach ($outInfo as $key => $item) {
                        $arrTemp = array(
                            "mailTo" => $mailTo,
                            "pinCode" => $item["PinCode"],
                        );

                        if (isset($item["DRAFT_NO"])) {
                            $arrTemp["draftNo"] = $item["DRAFT_NO"];
                        }

                        array_push($argMailInfo, $arrTemp);
                    }

                    if ($outInfo[0]["PinCode"] != "") {
                        $this->funcs->generateQRCode($outInfo[0]["PinCode"]);
                    }
                } else {
                    $pinCode = (isset($data['invInfo']['fkey']) ? $data['invInfo']['fkey'] : "");
                    $itemMail = array(
                        "mailTo" => $mailTo
                    );

                    if (isset($data['invInfo']) && count($data['invInfo']) > 0) {
                        $itemMail["inv"] = $data['invInfo']['serial'] . $data['invInfo']['invno'];
                    }
                    $itemMail["reservationCode"] = $data['invInfo']['reservationCode']  ?? '';
                    $itemMail["pinCode"] = $pinCode;

                    if ($pinCode != "") {
                        $this->funcs->generateQRCode($pinCode);
                    }

                    array_push($argMailInfo, $itemMail);
                }

                if (count($argMailInfo) > 0) {
                    $n = isset($data["odr"][0]) ? $data["odr"][0] : array();
                    foreach ($argMailInfo as $item) {
                        $item["CusID"] = isset($n["CusID"]) ? $n["CusID"] : null;
                        $item["PersonalID"] = isset($n["PersonalID"]) ? $n["PersonalID"] : null;
                        $item["NameDD"] = isset($n["NameDD"]) ? $n["NameDD"] : null;
                        $this->sendmail($item);
                    }
                }
            }

            echo json_encode($this->data);
            exit;
        }

        $this->data['title'] = 'Lệnh đóng hàng vào container';

        $this->load->view('header', $this->data);
        $this->data['services'] = $this->mdltask->getServices(['ischkCFS' => 1]);
        // $this->data['contList'] = $this->mdltask->load_stuffing_conts();

        $this->data['sizeTypes'] = $this->mdlcommon->getSizeType();
        $this->data['cargoTypes'] = $this->mdltask->getCargoTypes();
        $this->data['paymentMethod'] = $this->mdltask->getPaymentMethod();

        //them moi hd thu sau
        $this->data["isDup"] = false;
        if (($this->session->userdata("invInfo") !== null && count(json_decode($this->session->userdata("invInfo"), true)) > 0)) {
            //them moi hd thu sau
            $ssInvInfo = json_decode($this->session->userdata("invInfo"), true);
            $ssInvInfoCas = $ssInvInfo['CAS'];

            $this->data["ssInvInfo"] = $ssInvInfoCas;
            $this->data["isDup"] = $this->mdlInv->checkInvNo($ssInvInfo['serial'], $ssInvInfo['invno']);
        }

        $this->load->view('task/stuffing_order', $this->data);
        $this->load->view('footer');
    }

    public function tskUnstuffingOrder()
    {
        $access = $this->user->access('tskUnstuffingOrder');
        if ($access === false) {
            show_404();
        }

        if (strlen($access) > 5) {
            $this->data['deny'] = $access;
            echo json_encode($this->data);
            exit;
        }

        $action = $this->input->post('action') ? $this->input->post('action') : '';
        if ($action == "view") {
            $act = $this->input->post('act') ? $this->input->post('act') : '';
            if ($act == 'load_payer') {
                $this->data['payers'] = $this->mdltask->getPayers();
                echo json_encode($this->data);
                exit;
            }

            if ($act == 'search_cus_by_tax') {
                $taxCode = $this->input->post('taxCode') ? $this->input->post('taxCode') : '';
                $cus = $this->mdltask->getPayerByTaxCode($taxCode);
                echo json_encode(array('cus' => $cus));
                exit;
            }

            if ($act == 'searh_ship') {
                $arrstt = $this->input->post('arrStatus') ? $this->input->post('arrStatus') : '';
                $year = $this->input->post('shipyear') ? $this->input->post('shipyear') : '';
                $name = $this->input->post('shipname') ? $this->input->post('shipname') : '';

                $this->data['vsls'] = $this->mdltask->searchShip($arrstt, $year, $name);
                echo json_encode($this->data);
                exit;
            }
            if ($act == 'load_attach_srv') {
                $orderType = $this->input->post('order_type') ? $this->input->post('order_type') : '';
                $this->data['lists'] = $this->mdltask->getAttachServices($orderType);

                echo json_encode($this->data);
                exit;
            }

            if ($act == 'load_payment') {
                $cusID = $this->input->post('cusID') ? $this->input->post('cusID') : '';

                if ($cusID == '') {
                    $this->data["no_payer"] = array();
                    array_push($this->data["no_payer"], "Vui lòng chọn lại đối tượng thanh toán!");
                    json_encode($this->data);
                    exit;
                }

                $list = $this->input->post('list') ? $this->input->post('list') : array();
                $nonAttach = $this->input->post('nonAttach') ? $this->input->post('nonAttach') : array();

                //calculate storage
                $lbc = $this->input->post('lbc') ? $this->input->post('lbc') : array();
                $totalDayInYard = [];
                $freeContInYard = [];

                if (count($lbc) > 0) {
                    $seq = 1;
                    $lbcDesc = "";

                    foreach ($lbc as $key => $value) {
                        //reg key for diff cont
                        $sz = $this->getContSize($value['ISO_SZTP']);
                        $difKeyStorage = $sz . '-' . $value['Status'] . '-' . $value['CARGO_TYPE'] . '-' . $value['IsLocal'];

                        //storage from date
                        $storageFrom = strtotime($this->funcs->dbDateTime(explode(' ', $value["DateIn"])[0]));

                        //storage to date
                        $storageTo = strtotime($this->funcs->dbDateTime(explode(' ', $value["ExpDate"])[0]));

                        //get storage free date follow OprID
                        $freeDays = $this->mdltask->getStorageFreeDay($value["OprID"], $value['CntrClass'], $value['Status'], $value['CARGO_TYPE']);

                        $daysinYard = ceil(($storageTo - $storageFrom) / (60 * 60 * 24) - $freeDays + 1);
                        $daysinYard = $daysinYard > 0 ? $daysinYard : 0;

                        $strNote = sprintf(
                            "Lưu bãi container <b>%s</b><br><br>
                                                            Từ <b>%s</b> - đến <b>%s</b><br>Miễn phí <b>%s</b> ngày<br>Tổng thời gian: <b>%s</b> ngày",
                            $value["CntrNo"],
                            $this->funcs->clientDateTime($value["DateIn"]),
                            $value["ExpDate"],
                            $freeDays,
                            $daysinYard
                        );

                        $this->data["ps_notify"] = isset($this->data["ps_notify"])
                            ? $this->data["ps_notify"] . "<br/><hr/>" . $strNote
                            : $strNote;

                        if ($daysinYard == 0) {
                            array_push($freeContInYard, $value["CntrNo"]);
                            unset($lbc[$key]);
                            continue;
                        }

                        $oldSum = isset($totalDayInYard[$difKeyStorage]) ? $totalDayInYard[$difKeyStorage] : 0;
                        $totalDayInYard[$difKeyStorage] = $oldSum + $daysinYard;

                        $lbc[$key]["PTI_Hour"] = "0";
                        $lbc[$key]["DMethod_CD"] = NULL;
                        $lbc[$key]["EIR_SEQ"] =  $seq++;

                        //add them thong tin luu bai vao draft detail
                        $lbcDesc .= sprintf(
                            // "%s: %s - %s|", //CNTR0123456: 2021/01/01 00:00 - 2021/01/02 23:59|
                            "%s|", //CNTR0123456: 2021/01/01 00:00 - 2021/01/02 23:59|
                            $value["CntrNo"],
                            // $this->funcs->clientDateTime($value["DateIn"], "/"),
                            // $value["ExpDate"]
                        );
                        // -- add them thong tin luu bai vao draft detail

                        unset(
                            $lbc[$key]["SSOderNo"],
                            $lbc[$key]["BARGE_CODE"],
                            $lbc[$key]["BARGE_YEAR"],
                            $lbc[$key]["BARGE_CALL_SEQ"],
                            $lbc[$key]["DELIVERYORDER"]
                        );
                    }

                    if (count($freeContInYard) > 0) {
                        $this->data["freeContInYard"] = $freeContInYard;
                    }

                    if (count($totalDayInYard) > 0 && array_sum(array_values($totalDayInYard)) > 0) {
                        $addinfoStorage = array(
                            "calc_continue" => "1", // đánh dấu để hàm calculate_payment return biểu cước và tiếp tục tính lệnh tiếp theo
                            "Quantity" => array(
                                "LBC" => $totalDayInYard
                            ),
                            "TRF_DESC_MORE" => $lbcDesc
                        );

                        $this->calculate_payment($lbc, $cusID, 'services', $addinfoStorage);
                    }
                }

                //CALCULATE PLUGIN DATE
                $sdd = $this->input->post('sdd') ? $this->input->post('sdd') : array();
                $totalPluginHour = [];

                if (count($sdd) > 0) {
                    $seq = 1;
                    $sddDesc = [];
                    $this->data["error_plugin"] = array();

                    foreach ($sdd as $key => $value) {
                        //reg key for diff cont
                        $sz = $this->getContSize($value['ISO_SZTP']);
                        $difKeyPlugin = $sz . '-' . $value['Status'] . '-' . $value['CARGO_TYPE'] . '-' . $value['IsLocal'];

                        // get plugin from
                        $pluginInRF_ONOFF = $this->mdltask->getPluginDate($value["CntrNo"], $value["ShipKey"], $value["CntrClass"]);

                        if ($pluginInRF_ONOFF == '') {
                            //không thể lấy được ngày cắm điện của cont này => unset $sdd để k phải tính tiền cont điện lạnh này
                            unset($sdd[$key]);

                            array_push($this->data["error_plugin"], "Container [" . $value["CntrNo"] . "] - không tìm thấy thời gian cắm điện!");
                            continue;
                        }

                        $pluginFrom = strtotime($pluginInRF_ONOFF);

                        //get plugin to
                        $pluginTo = strtotime($this->funcs->dbDateTime($value["ExpPluginDate"]));

                        if ($pluginFrom >= $pluginTo) {
                            //thời gian tính tiền cắm điện không hợp lý
                            unset($sdd[$key]);

                            array_push($this->data["error_plugin"], "Container [" . $value["CntrNo"] . "] - Hạn điện phải lớn hơn (>) thời gian cắm điện!");
                            continue;
                        }

                        $plHour = $this->calcTimePlugin($value["OprID"], $pluginFrom, $pluginTo);
                        if (!$plHour) {
                            //không có cấu hình
                            unset($sdd[$key]);
                            array_push($this->data["error_plugin"], "Hãng khai thác [" . $value["OprID"] . "] chưa được cấu hình tính điện lạnh!");
                            continue;
                        }

                        $oldSumPlugin = isset($totalPluginHour[$difKeyPlugin]) ? $totalPluginHour[$difKeyPlugin] : 0;
                        $totalPluginHour[$difKeyPlugin] = $oldSumPlugin + $plHour;

                        $sdd[$key]["CJMode_CD"] = "SDD";
                        $sdd[$key]["PTI_Hour"] = "0";
                        $sdd[$key]["DMethod_CD"] = NULL;

                        $strNote = sprintf(
                            "Điện lạnh container <b>%s</b><br><br>Từ <b>%s</b> - đến <b>%s</b><br>Tổng thời gian: <b>%s</b> giờ",
                            $value["CntrNo"],
                            $this->funcs->clientDateTime($pluginInRF_ONOFF),
                            $value["ExpPluginDate"],
                            $plHour
                        );

                        $this->data["ps_notify"] = isset($this->data["ps_notify"])
                            ? $this->data["ps_notify"] . "<br/><hr/>" . $strNote
                            : $strNote;

                        $sdd[$key]["EIR_SEQ"] = $seq++;

                        //add them thong tin dien lanh vao draft detail
                        // $sddDesc .= sprintf(
                        //     "%s: %s - %s|", //CNTR0123456: 2021/01/01 00:00 - 2021/01/02 23:59|
                        //     $value["CntrNo"],
                        //     $this->funcs->clientDateTime($pluginInRF_ONOFF, "/"),
                        //     $value["ExpPluginDate"]
                        // );
                        if ($sddDesc[$difKeyPlugin]) {
                            $sddDesc[$difKeyPlugin] .= sprintf(
                                // "%s: %s - %s|", //CNTR0123456: 2021/01/01 00:00 - 2021/01/02 23:59|
                                "%s|", //CNTR0123456: 2021/01/01 00:00 - 2021/01/02 23:59|
                                $value["CntrNo"],
                                // $this->funcs->clientDateTime($pluginInRF_ONOFF, "/"),
                                // $value["ExpPluginDate"]
                            );
                        } else {
                            $sddDesc[$difKeyPlugin] = sprintf(
                                // "%s: %s - %s|", //CNTR0123456: 2021/01/01 00:00 - 2021/01/02 23:59|
                                "%s", //CNTR0123456: 2021/01/01 00:00 - 2021/01/02 23:59|
                                $value["CntrNo"],
                                // $this->funcs->clientDateTime($pluginInRF_ONOFF, "/"),
                                // $value["ExpPluginDate"]
                            );
                        }
                        // -- add them thong tin dien lanh vao draft detail

                        unset(
                            $sdd[$key]["SSOderNo"],
                            $sdd[$key]["BARGE_CODE"],
                            $sdd[$key]["BARGE_YEAR"],
                            $sdd[$key]["BARGE_CALL_SEQ"],
                            $sdd[$key]["DELIVERYORDER"]
                        );
                    }

                    $addinfoPlugin = array(
                        "calc_continue" => "1", // đánh dấu để hàm calculate_payment return biểu cước và tiếp tục tính lệnh tiếp theo
                        "Quantity" => array(
                            "SDD" => $totalPluginHour
                        ),
                        "TRF_DESC_MORE" => $sddDesc
                    );

                    if (count($sdd) > 0) {
                        $this->calculate_payment($sdd, $cusID, 'services', $addinfoPlugin);
                    }
                }

                if (count($nonAttach) > 0) {
                    $this->calculate_payment($nonAttach, $cusID, 'services', array('calc_continue' => '1'));
                }

                if (count($lbc) > 0 || count($sdd) > 0 || count($nonAttach) > 0) {
                    $this->calculate_payment($list, $cusID, 'services', array('calc_continue' => '1'));
                    echo json_encode($this->data);
                    exit();
                }

                $this->calculate_payment($list, $cusID, 'services');
                exit;
            }
        }

        if ($action == "save") {
            $act = $this->input->post('act') ? $this->input->post('act') : '';

            if ($act == 'use_manual_Inv') {
                $data = $this->input->post('useInvData') ? $this->input->post('useInvData') : array();

                //them moi hd thu sau
                $this->setSessionInvInfo($data);
                echo true;
                exit;
            }

            if ($act == 'save_new_payer') {
                $taxCode = $this->input->post('taxCode') ? $this->input->post('taxCode') : '';
                $cusName = $this->input->post('cusName') ? $this->input->post('cusName') : '';
                $address = $this->input->post('address') ? $this->input->post('address') : '';

                $outMsg = "";
                $result = $this->mdlcommon->savePayerQuickly($taxCode, $cusName, $address, $outMsg);

                if (!$result['success']) {
                    $this->data["error"] = $outMsg;
                } else {
                    $this->data["saveType"] = $outMsg;
                }

                // if ($result['success'] === TRUE && isset($result['newCus']) && count($result['newCus']) > 0) {
                //     $this->load->model("interfaceOracle_model", "mdlOracle");
                //     $this->data['transfer_result'] = $this->mdlOracle->transferNewCustomer($result['newCus']);
                // }

                echo json_encode($this->data);
                exit;
            }

            $data = $this->input->post('data') ? $this->input->post('data') : array();

            //them moi hd thu sau
            if (!$this->hasSessionInvInfo($data["pubType"])) {
                echo json_encode($this->data);
                exit();
            }

            $outInfo = array();

            $this->data['message'] = $this->mdltask->save_SRV_ODR_INV($data, "UNSTUFF_CHK", $outInfo); //, đánh dấu rút hàng "UNSTUFF_CHK"

            if (isset($data['invInfo'])) {
                $this->data['invInfo'] = $data['invInfo'];
            } else {
                if (isset($data["pubType"])) {
                    if ($data["pubType"] == "m-inv") {
                        $this->data['invInfo'] = $data['invInfo'] = $outInfo[0];
                        $outInfo = [];
                    } else {
                        $this->data['dftInfo'] = $outInfo;
                    }
                }
            }

            // /create data for send mail
            $mailTo = isset($data["odr"][0]["Mail"]) ? $data["odr"][0]["Mail"] : "";
            $argMailInfo = array();

            if (!empty($mailTo)) {
                if (count($outInfo) > 0) {
                    foreach ($outInfo as $key => $item) {
                        $arrTemp = array(
                            "mailTo" => $mailTo,
                            "pinCode" => $item["PinCode"],
                        );

                        if (isset($item["DRAFT_NO"])) {
                            $arrTemp["draftNo"] = $item["DRAFT_NO"];
                        }

                        array_push($argMailInfo, $arrTemp);
                    }

                    if ($outInfo[0]["PinCode"] != "") {
                        $this->funcs->generateQRCode($outInfo[0]["PinCode"]);
                    }
                } else {
                    $pinCode = (isset($data['invInfo']['fkey']) ? $data['invInfo']['fkey'] : "");
                    $itemMail = array(
                        "mailTo" => $mailTo
                    );

                    if (isset($data['invInfo']) && count($data['invInfo']) > 0) {
                        $itemMail["inv"] = $data['invInfo']['serial'] . $data['invInfo']['invno'];
                    }
                    $itemMail["reservationCode"] = $data['invInfo']['reservationCode']  ?? '';
                    $itemMail["pinCode"] = $pinCode;

                    if ($pinCode != "") {
                        $this->funcs->generateQRCode($pinCode);
                    }

                    array_push($argMailInfo, $itemMail);
                }

                if (count($argMailInfo) > 0) {
                    $n = isset($data["odr"][0]) ? $data["odr"][0] : array();
                    foreach ($argMailInfo as $item) {
                        $item["CusID"] = isset($n["CusID"]) ? $n["CusID"] : null;
                        $item["PersonalID"] = isset($n["PersonalID"]) ? $n["PersonalID"] : null;
                        $item["NameDD"] = isset($n["NameDD"]) ? $n["NameDD"] : null;
                        $this->sendmail($item);
                    }
                }
            }

            echo json_encode($this->data);
            exit;
        }

        $this->data['title'] = 'Lệnh rút hàng từ container';

        $this->load->view('header', $this->data);
        $this->data['services'] = $this->mdltask->getServices(['ischkCFS' => 2]);
        $this->data['contList'] = $this->mdltask->load_unstuffing_conts();
        $this->data['paymentMethod'] = $this->mdltask->getPaymentMethod();

        //them moi hd thu sau
        $this->data["isDup"] = false;
        if (($this->session->userdata("invInfo") !== null && count(json_decode($this->session->userdata("invInfo"), true)) > 0)) {
            //them moi hd thu sau
            $ssInvInfo = json_decode($this->session->userdata("invInfo"), true);
            $ssInvInfoCas = $ssInvInfo['CAS'];

            $this->data["ssInvInfo"] = $ssInvInfoCas;
            $this->data["isDup"] = $this->mdlInv->checkInvNo($ssInvInfo['serial'], $ssInvInfo['invno']);
        }

        $this->load->view('task/unstuffing_order', $this->data);
        $this->load->view('footer');
    }

    public function tskTransStuffOrder()
    {
        $access = $this->user->access('tskTransStuffOrder');
        if ($access === false) {
            show_404();
        }

        if (strlen($access) > 5) {
            $this->data['deny'] = $access;
            echo json_encode($this->data);
            exit;
        }

        $action = $this->input->post('action') ? $this->input->post('action') : '';
        if ($action == "view") {
            $act = $this->input->post('act') ? $this->input->post('act') : '';
            if ($act == 'load_payer') {
                $this->data['payers'] = $this->mdltask->getPayers();
                echo json_encode($this->data);
                exit;
            }

            if ($act == 'search_cus_by_tax') {
                $taxCode = $this->input->post('taxCode') ? $this->input->post('taxCode') : '';
                $cus = $this->mdltask->getPayerByTaxCode($taxCode);
                echo json_encode(array('cus' => $cus));
                exit;
            }

            if ($act == 'searh_ship') {
                $arrstt = $this->input->post('arrStatus') ? $this->input->post('arrStatus') : '';
                $year = $this->input->post('shipyear') ? $this->input->post('shipyear') : '';
                $name = $this->input->post('shipname') ? $this->input->post('shipname') : '';

                $this->data['vsls'] = $this->mdltask->searchShip($arrstt, $year, $name);
                echo json_encode($this->data);
                exit;
            }

            if ($act == 'load_attach_srv') {
                $orderType = $this->input->post('order_type') ? $this->input->post('order_type') : '';
                $this->data['lists'] = $this->mdltask->getAttachServices($orderType);

                echo json_encode($this->data);
                exit;
            }

            if ($act == 'load_unstuff_cont') {
                $cntrNo = $this->input->post('cntrNo') ? $this->input->post('cntrNo') : '';

                $outMsg = "";
                $unstuffCont = $this->mdltask->load_transstuffing_unstuff_cont($cntrNo, $outMsg);

                if (count($unstuffCont) == 0) {
                    $this->data["error"] = $outMsg;
                    echo json_encode($this->data);
                    exit();
                }

                $this->data["unstuffCont"] = $unstuffCont;

                $loadStuffContCondition = array(
                    "OprID" => $unstuffCont["OprID"],
                    "ISO_SZTP" => $unstuffCont["ISO_SZTP"]
                );

                $stuffConts = $this->mdltask->load_transstuffing_stuff_cont($loadStuffContCondition, $outMsg);

                if (count($stuffConts) == 0) {
                    $this->data["error"] = $outMsg;
                    echo json_encode($this->data);
                    exit();
                }

                $this->data["stuffConts"] = $stuffConts;
                echo json_encode($this->data);
                exit();
            }

            if ($act == 'load_payment') {
                $cusID = $this->input->post('cusID') ? $this->input->post('cusID') : '';

                if ($cusID == '') {
                    array_push($this->data["no_payer"], "Vui lòng chọn lại đối tượng thanh toán!");
                    json_encode($this->data);
                    exit;
                }

                $list = $this->input->post('list') ? $this->input->post('list') : array();

                $nonAttach = $this->input->post('nonAttach') ? $this->input->post('nonAttach') : array();

                if (count($nonAttach) > 0) {
                    $this->calculate_payment($nonAttach, $cusID, 'services', array('calc_continue' => '1'));
                    $this->calculate_payment($list, $cusID, 'services', array('calc_continue' => '1'));
                    echo json_encode($this->data);
                    exit();
                }

                $this->calculate_payment($list, $cusID, 'services');
                exit;
            }
        }

        if ($action == "save") {
            $act = $this->input->post('act') ? $this->input->post('act') : '';

            if ($act == 'use_manual_Inv') {
                $data = $this->input->post('useInvData') ? $this->input->post('useInvData') : array();

                //them moi hd thu sau
                $this->setSessionInvInfo($data);
                echo true;
                exit;
            }

            if ($act == 'save_new_payer') {
                $taxCode = $this->input->post('taxCode') ? $this->input->post('taxCode') : '';
                $cusName = $this->input->post('cusName') ? $this->input->post('cusName') : '';
                $address = $this->input->post('address') ? $this->input->post('address') : '';

                $errorMsg = "";
                $result = $this->mdlcommon->savePayerQuickly($taxCode, $cusName, $address, $errorMsg);

                if (!$result['success']) {
                    $this->data["error"] = $errorMsg;
                } else {
                    $this->data["saveType"] = $errorMsg;
                }

                // if ($result['success'] === TRUE && isset($result['newCus']) && count($result['newCus']) > 0) {
                //     $this->load->model("interfaceOracle_model", "mdlOracle");
                //     $this->data['transfer_result'] = $this->mdlOracle->transferNewCustomer($result['newCus']);
                // }

                echo json_encode($this->data);
                exit;
            }

            $data = $this->input->post('data') ? $this->input->post('data') : array();

            //them moi hd thu sau
            if (!$this->hasSessionInvInfo($data["pubType"])) {
                echo json_encode($this->data);
                exit();
            }

            $outInfo = array();
            $this->data['message'] = $this->mdltask->save_SRV_ODR_INV($data, "", $outInfo);

            if (isset($data['invInfo'])) {
                $this->data['invInfo'] = $data['invInfo'];
            } else {
                if (isset($data["pubType"])) {
                    if ($data["pubType"] == "m-inv") {
                        $this->data['invInfo'] = $data['invInfo'] = $outInfo[0];
                        $outInfo = [];
                    } else {
                        $this->data['dftInfo'] = $outInfo;
                    }
                }
            }

            // /send mail
            $mailTo = isset($data["odr"][0]["Mail"]) ? $data["odr"][0]["Mail"] : "";
            $argMailInfo = array();

            if ($mailTo != "") {
                if (count($outInfo) > 0) {
                    foreach ($outInfo as $key => $item) {
                        $arrTemp = array(
                            "mailTo" => $mailTo,
                            "pinCode" => $item["PinCode"],
                        );

                        if (isset($item["DRAFT_NO"])) {
                            $arrTemp["draftNo"] = $item["DRAFT_NO"];
                        }

                        array_push($argMailInfo, $arrTemp);
                    }

                    if ($outInfo[0]["PinCode"] != "") {
                        $this->funcs->generateQRCode($outInfo[0]["PinCode"]);
                    }
                } else {
                    $pinCode = (isset($data['invInfo']['fkey']) ? $data['invInfo']['fkey'] : "");
                    $itemMail = array(
                        "mailTo" => $mailTo
                    );

                    if (isset($data['invInfo']) && count($data['invInfo']) > 0) {
                        $itemMail["inv"] = $data['invInfo']['serial'] . $data['invInfo']['invno'];
                    }
                    $itemMail["reservationCode"] = $data['invInfo']['reservationCode']  ?? '';
                    $itemMail["pinCode"] = $pinCode;

                    if ($pinCode != "") {
                        $this->funcs->generateQRCode($pinCode);
                    }

                    array_push($argMailInfo, $itemMail);
                }

                if (count($argMailInfo) > 0) {
                    $n = isset($data["odr"][0]) ? $data["odr"][0] : array();
                    foreach ($argMailInfo as $item) {
                        $item["CusID"] = isset($n["CusID"]) ? $n["CusID"] : null;
                        $item["PersonalID"] = isset($n["PersonalID"]) ? $n["PersonalID"] : null;
                        $item["NameDD"] = isset($n["NameDD"]) ? $n["NameDD"] : null;
                        $this->sendmail($item);
                    }
                }
            }

            echo json_encode($this->data);
            exit;
        }

        $this->data['title'] = 'Lệnh đóng rút sang container';

        $this->load->view('header', $this->data);
        $this->data['services'] = $this->mdltask->getServices(['ischkCFS' => 3]);
        $this->data['paymentMethod'] = $this->mdltask->getPaymentMethod();

        //them moi hd thu sau
        $this->data["isDup"] = false;
        if (($this->session->userdata("invInfo") !== null && count(json_decode($this->session->userdata("invInfo"), true)) > 0)) {
            //them moi hd thu sau
            $ssInvInfo = json_decode($this->session->userdata("invInfo"), true);
            $ssInvInfoCas = $ssInvInfo['CAS'];

            $this->data["ssInvInfo"] = $ssInvInfoCas;
            $this->data["isDup"] = $this->mdlInv->checkInvNo($ssInvInfo['serial'], $ssInvInfo['invno']);
        }

        $this->load->view('task/transstuff_order', $this->data);
        $this->load->view('footer');
    }

    public function tskImportPickup()
    {
        $access = $this->user->access('tskImportPickup');
        if ($access === false) {
            show_404();
        }

        if (strlen($access) > 5) {
            $this->data['deny'] = $access;
            echo json_encode($this->data);
            exit;
        }

        $action = $this->input->post('action') ? $this->input->post('action') : '';
        if ($action == "view") {
            $act = $this->input->post('act') ? $this->input->post('act') : '';
            if ($act == 'load_payer') {
                $this->data['payers'] = $this->mdltask->getPayers();
                echo json_encode($this->data);
                exit;
            }

            if ($act == 'search_cus_by_tax') {
                $taxCode = $this->input->post('taxCode') ? $this->input->post('taxCode') : '';
                $cus = $this->mdltask->getPayerByTaxCode($taxCode);
                echo json_encode(array('cus' => $cus));
                exit;
            }

            if ($act == 'search_bill') {
                $blno = $this->input->post('billNo') ? $this->input->post('billNo') : '';
                $cntrNo = $this->input->post('cntrNo') ? $this->input->post('cntrNo') : '';
                $isGiaoCntrXuat = $this->input->post('isGiaoCntrXuat') ? boolval($this->input->post('isGiaoCntrXuat')) : false;
                $result = $this->mdltask->load_ip_cntr_details($blno, $cntrNo, $isGiaoCntrXuat);

                if (!is_array($result)) {
                    $this->data["error"] = $result;
                } else {
                    $this->data['list'] = $result;
                }

                echo json_encode($this->data);
                exit;
            }

            if ($act == 'search_barge') {
                $this->data['barges'] = $this->mdltask->getBarge();
                echo json_encode($this->data);
                exit;
            }

            if ($act == 'load_attach_srv') {
                $orderType = $this->input->post('order_type') ? $this->input->post('order_type') : '';
                $this->data['lists'] = $this->mdltask->getAttachServices($orderType);

                echo json_encode($this->data);
                exit;
            }

            if ($act == 'load_payment') {
                $cusID = $this->input->post('cusID') ? $this->input->post('cusID') : '';

                if ($cusID == '') {
                    array_push($this->data["no_payer"], "Vui lòng chọn lại đối tượng thanh toán!");
                    json_encode($this->data);
                    exit;
                }

                $list = $this->input->post('list') ? $this->input->post('list') : array();
                $nonAttach = $this->input->post('nonAttach') ? $this->input->post('nonAttach') : array();

                //calculate storage
                $lbc = $this->input->post('lbc') ? $this->input->post('lbc') : array();
                // $totalDayInYard = 0;
                $totalDayInYard = array();
                $freeContInYard = array();

                if (count($lbc) > 0) {
                    $seq = 1;
                    $lbcDesc = "";

                    foreach ($lbc as $key => $value) {
                        //reg key for diff cont
                        $sz = $this->getContSize($value['ISO_SZTP']);
                        $difKeyStorage = $sz . '-' . $value['Status'] . '-' . $value['CARGO_TYPE'] . '-' . $value['IsLocal'];

                        //storage from date
                        $storageFrom = strtotime($this->funcs->dbDateTime(explode(' ', $value["DateIn"])[0]));

                        //storage to date
                        $storageTo = strtotime($this->funcs->dbDateTime(explode(' ', $value["ExpDate"])[0]));

                        //get storage free date follow OprID
                        $freeDays = $this->mdltask->getStorageFreeDay($value["OprID"], $value['CntrClass'], $value['Status'], $value['CARGO_TYPE']);

                        $daysinYard = ceil(($storageTo - $storageFrom) / (60 * 60 * 24) - $freeDays + 1);
                        $daysinYard = $daysinYard > 0 ? $daysinYard : 0;

                        $strNote = sprintf(
                            "Lưu bãi container <b>%s</b><br><br>
                                                            Từ <b>%s</b> - đến <b>%s</b><br>Miễn phí <b>%s</b> ngày<br>Tổng thời gian: <b>%s</b> ngày",
                            $value["CntrNo"],
                            $this->funcs->clientDateTime($value["DateIn"]),
                            $value["ExpDate"],
                            $freeDays,
                            $daysinYard
                        );

                        $this->data["ps_notify"] = isset($this->data["ps_notify"])
                            ? $this->data["ps_notify"] . "<br/><hr/>" . $strNote
                            : $strNote;

                        if ($daysinYard == 0) {
                            array_push($freeContInYard, $value["CntrNo"]);
                            unset($lbc[$key]);
                            continue;
                        }

                        $oldSum = isset($totalDayInYard[$difKeyStorage]) ? $totalDayInYard[$difKeyStorage] : 0;
                        $totalDayInYard[$difKeyStorage] = $oldSum + $daysinYard;

                        $lbc[$key]["PTI_Hour"] = "0";
                        $lbc[$key]["DMethod_CD"] = NULL;
                        $lbc[$key]["EIR_SEQ"] =  $seq++;

                        //add them thong tin luu bai vao draft detail
                        $lbcDesc .= sprintf(
                            // "%s: %s - %s|", //CNTR0123456: 2021/01/01 00:00 - 2021/01/02 23:59|
                            "%s|", //CNTR0123456: 2021/01/01 00:00 - 2021/01/02 23:59|
                            $value["CntrNo"],
                            // $this->funcs->clientDateTime($value["DateIn"], "/"),
                            // $value["ExpDate"]
                        );
                        // -- add them thong tin luu bai vao draft detail

                        unset(
                            $lbc[$key]["EIRNo"],
                            $lbc[$key]["BARGE_CODE"],
                            $lbc[$key]["BARGE_YEAR"],
                            $lbc[$key]["BARGE_CALL_SEQ"],
                            $lbc[$key]["DELIVERYORDER"]
                        );
                    }

                    if (count($freeContInYard) > 0) {
                        $this->data["freeContInYard"] = $freeContInYard;
                    }

                    if (count($totalDayInYard) > 0 && array_sum(array_values($totalDayInYard)) > 0) {
                        $addinfoStorage = array(
                            "calc_continue" => "1", // đánh dấu để hàm calculate_payment return biểu cước và tiếp tục tính lệnh tiếp theo
                            "Quantity" => array(
                                "LBC" => $totalDayInYard
                            ),
                            "TRF_DESC_MORE" => $lbcDesc
                        );

                        $this->calculate_payment($lbc, $cusID, 'services', $addinfoStorage);
                    }
                }

                //CALCULATE PLUGIN DATE
                $sdd = $this->input->post('sdd') ? $this->input->post('sdd') : array();
                $totalPluginHour = [];

                if (count($sdd) > 0) {
                    $seq = 1;
                    $sddDesc = [];
                    $this->data["error_plugin"] = array();

                    foreach ($sdd as $key => $value) {
                        //reg key for diff cont
                        $sz = $this->getContSize($value['ISO_SZTP']);
                        $difKeyPlugin = $sz . '-' . $value['Status'] . '-' . $value['CARGO_TYPE'] . '-' . $value['IsLocal'];

                        // get plugin from
                        $pluginInRF_ONOFF = $this->mdltask->getPluginDate($value["CntrNo"], $value["ShipKey"], $value["CntrClass"]);

                        if ($pluginInRF_ONOFF == '') {
                            //không thể lấy được ngày cắm điện của cont này => unset $sdd để k phải tính tiền cont điện lạnh này
                            unset($sdd[$key]);

                            array_push($this->data["error_plugin"], "Container [" . $value["CntrNo"] . "] - không tìm thấy thời gian cắm điện!");
                            continue;
                        }

                        $pluginFrom = strtotime($pluginInRF_ONOFF);

                        //get plugin to
                        $pluginTo = strtotime($this->funcs->dbDateTime($value["ExpPluginDate"]));

                        if ($pluginFrom >= $pluginTo) {
                            //thời gian tính tiền cắm điện không hợp lý
                            unset($sdd[$key]);

                            array_push($this->data["error_plugin"], "Container [" . $value["CntrNo"] . "] - Hạn điện phải lớn hơn (>) thời gian cắm điện!");
                            continue;
                        }

                        $plHour = $this->calcTimePlugin($value["OprID"], $pluginFrom, $pluginTo);

                        if (!$plHour) {
                            //không có cấu hình
                            unset($sdd[$key]);
                            array_push($this->data["error_plugin"], "Hãng khai thác [" . $value["OprID"] . "] chưa được cấu hình tính điện lạnh!");
                            continue;
                        }

                        $oldSumPlugin = isset($totalPluginHour[$difKeyPlugin]) ? $totalPluginHour[$difKeyPlugin] : 0;
                        $totalPluginHour[$difKeyPlugin] = $oldSumPlugin + $plHour;

                        // $totalPluginHour += $plHour; //round( ( $pluginTo - $pluginFrom ) / ( 60 * 60 ) ); 

                        $sdd[$key]["CJMode_CD"] = "SDD";
                        $sdd[$key]["PTI_Hour"] = "0";
                        $sdd[$key]["DMethod_CD"] = NULL;

                        $strNote = sprintf(
                            "Điện lạnh container <b>%s</b><br><br>Từ <b>%s</b> - đến <b>%s</b><br>Tổng thời gian: <b>%s</b> giờ",
                            $value["CntrNo"],
                            $this->funcs->clientDateTime($pluginInRF_ONOFF, "/"),
                            $value["ExpPluginDate"],
                            $plHour
                        );

                        $this->data["ps_notify"] = isset($this->data["ps_notify"])
                            ? $this->data["ps_notify"] . "<br/><hr/>" . $strNote
                            : $strNote;

                        $sdd[$key]["EIR_SEQ"] = $seq++;

                        // if else tạo array sddDesc theo key
                        if ($sddDesc[$difKeyPlugin]) {
                            $sddDesc[$difKeyPlugin] .= sprintf(
                                // "%s: %s - %s|", //CNTR0123456: 2021/01/01 00:00 - 2021/01/02 23:59|
                                "%s", //CNTR0123456: 2021/01/01 00:00 - 2021/01/02 23:59|
                                $value["CntrNo"],
                                // $this->funcs->clientDateTime($pluginInRF_ONOFF, "/"),
                                // $value["ExpPluginDate"]
                            );
                        } else {
                            $sddDesc[$difKeyPlugin] = sprintf(
                                // "%s: %s - %s|", //CNTR0123456: 2021/01/01 00:00 - 2021/01/02 23:59|
                                "%s", //CNTR0123456: 2021/01/01 00:00 - 2021/01/02 23:59|
                                $value["CntrNo"],
                                // $this->funcs->clientDateTime($pluginInRF_ONOFF, "/"),
                                // $value["ExpPluginDate"]
                            );
                        }

                        // -- add them thong tin dien lanh vao draft detail

                        unset(
                            $sdd[$key]["EIRNo"],
                            $sdd[$key]["BARGE_CODE"],
                            $sdd[$key]["BARGE_YEAR"],
                            $sdd[$key]["BARGE_CALL_SEQ"],
                            $sdd[$key]["DELIVERYORDER"]
                        );
                    }

                    $addinfoPlugin = array(
                        "calc_continue" => "1", // đánh dấu để hàm calculate_payment return biểu cước và tiếp tục tính lệnh tiếp theo
                        "Quantity" => array(
                            "SDD" => $totalPluginHour
                        ),
                        "TRF_DESC_MORE" => $sddDesc
                    );

                    if (count($sdd) > 0) {
                        $this->calculate_payment($sdd, $cusID, 'services', $addinfoPlugin);
                    }
                }

                if (count($nonAttach) > 0) {
                    $this->calculate_payment($nonAttach, $cusID, 'services', array('calc_continue' => '1'));
                }

                if (count($lbc) > 0 || count($sdd) > 0 || count($nonAttach) > 0) {
                    $this->calculate_payment($list, $cusID, '', array('calc_continue' => '1'));
                    echo json_encode($this->data);
                    exit();
                }

                $this->calculate_payment($list, $cusID);
                exit;
            }
        }

        if ($action == "save") {
            $act = $this->input->post('act') ? $this->input->post('act') : '';

            if ($act == 'use_manual_Inv') {
                $data = $this->input->post('useInvData') ? $this->input->post('useInvData') : array();

                //them moi hd thu sau
                $this->setSessionInvInfo($data);
                echo true;
                exit;
            }

            if ($act == 'save_new_payer') {
                $taxCode = $this->input->post('taxCode') ? $this->input->post('taxCode') : '';
                $cusName = $this->input->post('cusName') ? $this->input->post('cusName') : '';
                $address = $this->input->post('address') ? $this->input->post('address') : '';

                $errorMsg = "";
                $result = $this->mdlcommon->savePayerQuickly($taxCode, $cusName, $address, $errorMsg);

                if (!$result['success']) {
                    $this->data["error"] = $errorMsg;
                } else {
                    $this->data["saveType"] = $errorMsg;
                }

                // if ($result['success'] === TRUE && isset($result['newCus']) && count($result['newCus']) > 0) {
                //     $this->load->model("interfaceOracle_model", "mdlOracle");
                //     $this->data['transfer_result'] = $this->mdlOracle->transferNewCustomer($result['newCus']);
                // }

                echo json_encode($this->data);
                exit;
            }

            $data = $this->input->post('data') ? $this->input->post('data') : array();

            //them moi hd thu sau
            if (!$this->hasSessionInvInfo($data["pubType"])) {
                echo json_encode($this->data);
                exit();
            }

            $outInfo = array();

            //get infor from DO (retlocation, free day)
            $do = $data['eir'][0]['DELIVERYORDER'];
            $blNo = $data['eir'][0]['BLNo'];

            if (isset($do)) {
                $smt = $this->ceh->select('CntrNo, RetLocation, Haulage_Instruction')
                    ->where('DELIVERYORDER', $do)
                    ->where('BLNo', $blNo)
                    ->get('EDI_EDO')->result_array();
                if (count($smt) > 0) {
                    foreach ($data['eir'] as $k => $v) {
                        $cntrNo = $v['CntrNo'];
                        $new = array_filter($smt, function ($var) use ($cntrNo) {
                            return ($var['CntrNo'] === $cntrNo);
                        });
                        if (count($new) > 0) {
                            $new = array_values($new);
                            $data['eir'][$k]['RetLocation'] = $new[0]['RetLocation'];
                            $data['eir'][$k]['FreeDays'] = $new[0]['Haulage_Instruction'];
                        }
                    }
                }
            }

            $this->data['message'] = $this->mdltask->save_EIR_INV($data, $outInfo);

            if (isset($data['invInfo'])) {
                $this->data['invInfo'] = $data['invInfo'];
            } else {
                if (isset($data["pubType"])) {
                    if ($data["pubType"] == "m-inv") {
                        $this->data['invInfo'] = $data['invInfo'] = $outInfo[0];
                        $outInfo = [];
                    } else {
                        $this->data['dftInfo'] = $outInfo;
                    }
                }
            }

            // /send mail
            $mailTo = isset($data["eir"][0]["Mail"]) ? $data["eir"][0]["Mail"] : "";
            $argMailInfo = array();

            if ($mailTo != "") {
                if (count($outInfo) > 0) {
                    foreach ($outInfo as $key => $item) {
                        $arrTemp = array(
                            "mailTo" => $mailTo,
                            "pinCode" => $item["PinCode"],
                        );

                        if (isset($item["DRAFT_NO"])) {
                            $arrTemp["draftNo"] = $item["DRAFT_NO"];
                        }

                        array_push($argMailInfo, $arrTemp);
                    }

                    if ($outInfo[0]["PinCode"] != "") {
                        $this->funcs->generateQRCode($outInfo[0]["PinCode"]);
                    }
                } else {
                    $pinCode = (isset($data['invInfo']['fkey']) ? $data['invInfo']['fkey'] : "");
                    $itemMail = array(
                        "mailTo" => $mailTo
                    );

                    if (isset($data['invInfo']) && count($data['invInfo']) > 0) {
                        $itemMail["inv"] = $data['invInfo']['serial'] . $data['invInfo']['invno'];
                    }
                    $itemMail["reservationCode"] = $data['invInfo']['reservationCode']  ?? '';
                    $itemMail["pinCode"] = $pinCode;

                    if ($pinCode != "") {
                        $this->funcs->generateQRCode($pinCode);
                    }

                    array_push($argMailInfo, $itemMail);
                }

                if (count($argMailInfo) > 0) {
                    $n = isset($data["eir"][0]) ? $data["eir"][0] : array();
                    foreach ($argMailInfo as $item) {
                        $item["CusID"] = isset($n["CusID"]) ? $n["CusID"] : null;
                        $item["PersonalID"] = isset($n["PersonalID"]) ? $n["PersonalID"] : null;
                        $item["NameDD"] = isset($n["NameDD"]) ? $n["NameDD"] : null;
                        $this->sendmail($item);
                    }
                }
            }

            echo json_encode($this->data);
            exit;
        }

        $this->data['title'] = "Lệnh giao cont hàng";

        $this->load->view('header', $this->data);

        $this->data['relocation'] = $this->mdltask->getRelocation();
        $this->data["transists"] = $this->mdlcommon->loadTransits();
        $this->data['paymentMethod'] = $this->mdltask->getPaymentMethod();

        //them moi hd thu sau
        $this->data["isDup"] = false;
        if (($this->session->userdata("invInfo") !== null && count(json_decode($this->session->userdata("invInfo"), true)) > 0)) {
            //them moi hd thu sau
            $ssInvInfo = json_decode($this->session->userdata("invInfo"), true);
            $ssInvInfoCas = $ssInvInfo['CAS'];

            $this->data["ssInvInfo"] = $ssInvInfoCas;
            $this->data["isDup"] = $this->mdlInv->checkInvNo($ssInvInfo['serial'], $ssInvInfo['invno']);
        }

        $rowguidsFromEDO = $this->input->post('rowguidsFromEDO') ? json_decode($this->input->post('rowguidsFromEDO'), TRUE) : array();
        if (count($rowguidsFromEDO) > 0) {
            $this->data["cntrFromEDO"] = $this->mdltask->getImportPickupByRowguids($rowguidsFromEDO);
        }

        $this->load->view('task/import_pickup', $this->data);
        $this->load->view('footer');
    }

    public function tskEmptyPickup()
    {
        $access = $this->user->access('tskEmptyPickup');
        if ($access === false) {
            show_404();
        }

        if (strlen($access) > 5) {
            $this->data['deny'] = $access;
            echo json_encode($this->data);
            exit;
        }
        $action = $this->input->post('action') ? $this->input->post('action') : '';
        if ($action == "view") {
            $act = $this->input->post('act') ? $this->input->post('act') : '';
            if ($act == 'load_payer') {
                $this->data['payers'] = $this->mdltask->getPayers();
                echo json_encode($this->data);
                exit;
            }

            if ($act == 'search_cus_by_tax') {
                $taxCode = $this->input->post('taxCode') ? $this->input->post('taxCode') : '';
                $cus = $this->mdltask->getPayerByTaxCode($taxCode);
                echo json_encode(array('cus' => $cus));
                exit;
            }

            if ($act == 'searh_ship') {
                $arrstt = $this->input->post('arrStatus') ? $this->input->post('arrStatus') : '';
                $year = $this->input->post('shipyear') ? $this->input->post('shipyear') : '';
                $name = $this->input->post('shipname') ? $this->input->post('shipname') : '';

                $this->data['vsls'] = $this->mdltask->searchShip($arrstt, $year, $name);
                echo json_encode($this->data);
                exit;
            }
            if ($act == 'search_barge') {
                $this->data['barges'] = $this->mdltask->getBarge();
                echo json_encode($this->data);
                exit;
            }
            if ($act == 'load_booking') {
                $bkno = $this->input->post('bkno') ? $this->input->post('bkno') : '';
                $cntrno = $this->input->post('cntrno') ? $this->input->post('cntrno') : '';
                $this->data['bookinglist'] = $this->mdltask->getBookingList($bkno, $cntrno);
                echo json_encode($this->data);
                exit;
            }

            if ($act == 'load_attach_srv') {
                $orderType = $this->input->post('order_type') ? $this->input->post('order_type') : '';
                $this->data['lists'] = $this->mdltask->getAttachServices($orderType);

                echo json_encode($this->data);
                exit;
            }

            if ($act == 'load_payment') {
                $cusID = $this->input->post('cusID') ? $this->input->post('cusID') : '';

                if ($cusID == '') {
                    array_push($this->data["no_payer"], "Vui lòng chọn lại đối tượng thanh toán!");
                    json_encode($this->data);
                    exit;
                }

                $list = $this->input->post('list') ? json_decode($this->input->post('list'), true) : array();

                $nonAttach = $this->input->post('nonAttach') ? json_decode($this->input->post('nonAttach'), true) : array();

                if (count($nonAttach) > 0) {
                    $this->calculate_payment($nonAttach, $cusID, 'services', array('calc_continue' => '1'));
                    $this->calculate_payment($list, $cusID, '', array('calc_continue' => '1'));
                    echo json_encode($this->data);
                    exit();
                }

                $this->calculate_payment($list, $cusID);
                exit;
            }
        }
        if ($action == "save") {
            $act = $this->input->post('act') ? $this->input->post('act') : '';

            if ($act == 'use_manual_Inv') {
                $data = $this->input->post('useInvData') ? $this->input->post('useInvData') : array();

                //them moi hd thu sau
                $this->setSessionInvInfo($data);
                echo true;
                exit;
            }

            if ($act == 'save_new_payer') {
                $taxCode = $this->input->post('taxCode') ? $this->input->post('taxCode') : '';
                $cusName = $this->input->post('cusName') ? $this->input->post('cusName') : '';
                $address = $this->input->post('address') ? $this->input->post('address') : '';

                $errorMsg = "";
                $result = $this->mdlcommon->savePayerQuickly($taxCode, $cusName, $address, $errorMsg);

                if (!$result['success']) {
                    $this->data["error"] = $errorMsg;
                } else {
                    $this->data["saveType"] = $errorMsg;
                }

                // if ($result['success'] === TRUE && isset($result['newCus']) && count($result['newCus']) > 0) {
                //     $this->load->model("interfaceOracle_model", "mdlOracle");
                //     $this->data['transfer_result'] = $this->mdlOracle->transferNewCustomer($result['newCus']);
                // }

                echo json_encode($this->data);
                exit;
            }

            $data = $this->input->post('data') ? $this->input->post('data') : array();

            //them moi hd thu sau
            if (!$this->hasSessionInvInfo($data["pubType"])) {
                echo json_encode($this->data);
                exit();
            }

            $outInfo = array();
            $this->data['message'] = $this->mdltask->save_EIR_INV($data, $outInfo);

            if (isset($data['invInfo'])) {
                $this->data['invInfo'] = $data['invInfo'];
            } else {
                if (isset($data["pubType"])) {
                    if ($data["pubType"] == "m-inv") {
                        $this->data['invInfo'] = $data['invInfo'] = $outInfo[0];
                        $outInfo = [];
                    } else {
                        $this->data['dftInfo'] = $outInfo;
                    }
                }
            }

            // /send mail
            $mailTo = isset($data["eir"][0]["Mail"]) ? $data["eir"][0]["Mail"] : "";
            $argMailInfo = array();

            if ($mailTo != "") {
                if (count($outInfo) > 0) {
                    foreach ($outInfo as $key => $item) {
                        $arrTemp = array(
                            "mailTo" => $mailTo,
                            "pinCode" => $item["PinCode"],
                        );

                        if (isset($item["DRAFT_NO"])) {
                            $arrTemp["draftNo"] = $item["DRAFT_NO"];
                        }

                        array_push($argMailInfo, $arrTemp);
                    }

                    if ($outInfo[0]["PinCode"] != "") {
                        $this->funcs->generateQRCode($outInfo[0]["PinCode"]);
                    }
                } else {
                    $pinCode = (isset($data['invInfo']['fkey']) ? $data['invInfo']['fkey'] : "");
                    $itemMail = array(
                        "mailTo" => $mailTo
                    );

                    if (isset($data['invInfo']) && count($data['invInfo']) > 0) {
                        $itemMail["inv"] = $data['invInfo']['serial'] . $data['invInfo']['invno'];
                    }
                    $itemMail["reservationCode"] = $data['invInfo']['reservationCode']  ?? '';
                    $itemMail["pinCode"] = $pinCode;

                    if ($pinCode != "") {
                        $this->funcs->generateQRCode($pinCode);
                    }

                    array_push($argMailInfo, $itemMail);
                }

                if (count($argMailInfo) > 0) {
                    $n = isset($data["eir"][0]) ? $data["eir"][0] : array();
                    foreach ($argMailInfo as $item) {
                        $item["CusID"] = isset($n["CusID"]) ? $n["CusID"] : null;
                        $item["PersonalID"] = isset($n["PersonalID"]) ? $n["PersonalID"] : null;
                        $item["NameDD"] = isset($n["NameDD"]) ? $n["NameDD"] : null;
                        $this->sendmail($item);
                    }
                }
            }

            echo json_encode($this->data);
            exit;
        }

        $this->data['title'] = "Lệnh giao cont rỗng";

        $this->load->view('header', $this->data);

        //them moi hd thu sau
        $this->data["isDup"] = false;
        if (($this->session->userdata("invInfo") !== null && count(json_decode($this->session->userdata("invInfo"), true)) > 0)) {
            //them moi hd thu sau
            $ssInvInfo = json_decode($this->session->userdata("invInfo"), true);
            $ssInvInfoCas = $ssInvInfo['CAS'];

            $this->data["ssInvInfo"] = $ssInvInfoCas;
            $this->data["isDup"] = $this->mdlInv->checkInvNo($ssInvInfo['serial'], $ssInvInfo['invno']);
        }

        $this->data["transists"] = $this->mdlcommon->loadTransits();
        $this->data['paymentMethod'] = $this->mdltask->getPaymentMethod();
        $this->data['cargoTypes'] = $this->mdltask->getCargoTypes();

        $this->load->view('task/empty_pickup', $this->data);
        $this->load->view('footer');
    }

    public function tskFCL_Pre_Advice()
    {
        $access = $this->user->access('tskFCL_Pre_Advice');
        if ($access === false) {
            show_404();
        }

        if (strlen($access) > 5) {
            $this->data['deny'] = $access;
            echo json_encode($this->data);
            exit;
        }

        $action = $this->input->post('action') ? $this->input->post('action') : '';
        if ($action == "view") {
            $act = $this->input->post('act') ? $this->input->post('act') : '';
            if ($act == 'searh_ship') {
                $arrstt = $this->input->post('arrStatus') ? $this->input->post('arrStatus') : '';
                $year = $this->input->post('shipyear') ? $this->input->post('shipyear') : '';
                $name = $this->input->post('shipname') ? $this->input->post('shipname') : '';

                $this->data['vsls'] = $this->mdltask->searchShip($arrstt, $year, $name);
                echo json_encode($this->data);
                exit;
            }

            if ($act == 'load_payer') {
                $this->data['payers'] = $this->mdltask->getPayers();
                echo json_encode($this->data);
                exit;
            }

            if ($act == 'search_cus_by_tax') {
                $taxCode = $this->input->post('taxCode') ? $this->input->post('taxCode') : '';
                $cus = $this->mdltask->getPayerByTaxCode($taxCode);
                echo json_encode(array('cus' => $cus));
                exit;
            }

            if ($act == 'search_cntr') {
                $cntrNo = $this->input->post('cntrNo') ? $this->input->post('cntrNo') : '';
                $result = $this->ceh->select('BLNo')->where('CntrNo', $cntrNo)->limit(1)->get('CNTR_DETAILS')->row_array();
                if (count($result) > 0) {
                    $this->data['BLNo'] = $result['BLNo'];
                }
                echo json_encode($this->data);
                exit;
            }
            if ($act == 'search_barge') {
                $this->data['barges'] = $this->mdltask->getBarge();
                echo json_encode($this->data);
                exit;
            }
            if ($act == 'getLane') {
                $shipkey = $this->input->post('shipkey') ? $this->input->post('shipkey') : '';
                $this->data['oprs'] = $this->mdltask->getLaneOprs($shipkey);
                $this->data['ports'] = $this->mdltask->getLanePortID($shipkey);
                echo json_encode($this->data);
                exit;
            }

            if ($act == 'load_attach_srv') {
                $orderType = $this->input->post('order_type') ? $this->input->post('order_type') : '';
                $this->data['lists'] = $this->mdltask->getAttachServices($orderType);

                echo json_encode($this->data);
                exit;
            }

            if ($act == "check_cntr_no") {
                $cntrNo = $this->input->post('cntrNo') ? $this->input->post('cntrNo') : '';
                $fe = $this->input->post('fe') ? $this->input->post('fe') : '';

                if ($cntrNo !== '' && $fe !== '') {
                    $checkCntrHoldByConfig = $this->mdltask->checkCntrHoldByConfig($cntrNo, $fe);
                    if ($checkCntrHoldByConfig !== NULL) {
                        $this->data["cntr_hold_by_config"] = true;
                        $this->data["hold_content"] = $checkCntrHoldByConfig;
                        echo json_encode($this->data);
                        exit();
                    }
                }

                $notAllow = $this->mdltask->checkEIR($cntrNo);
                if (!$notAllow) {
                    $this->data["is_stacking"] = $this->mdltask->checkCntrStacking($cntrNo);
                }
                $this->data["cont_not_allow"] = $notAllow;

                echo json_encode($this->data);
                exit();
            }

            if ($act == "check_cntr_hold_by_config") {
                $cntrNo = $this->input->post('cntrNo') ? $this->input->post('cntrNo') : '';
                $fe = $this->input->post('fe') ? $this->input->post('fe') : '';

                $checkCntrHoldByConfig = $this->mdltask->checkCntrHoldByConfig($cntrNo, $fe);
                if ($checkCntrHoldByConfig !== NULL) {
                    $this->data["cntr_hold_by_config"] = true;
                    $this->data["hold_content"] = $checkCntrHoldByConfig;
                }

                echo json_encode($this->data);
                exit();
            }

            if ($act == 'load_payment') {
                $cusID = $this->input->post('cusID') ? $this->input->post('cusID') : '';

                if ($cusID == '') {
                    array_push($this->data["no_payer"], "Vui lòng chọn lại đối tượng thanh toán!");
                    json_encode($this->data);
                    exit;
                }

                $list = $this->input->post('list') ? json_decode($this->input->post('list'), true) : array();

                $arrCont = array_column($list, "CntrNo");

                $nonAttach = $this->input->post('nonAttach') ? json_decode($this->input->post('nonAttach'), true) : array();

                if (count($nonAttach) > 0) {
                    $this->calculate_payment($nonAttach, $cusID, 'services', array('calc_continue' => '1'));
                    $this->calculate_payment($list, $cusID, '', array('calc_continue' => '1'));
                    echo json_encode($this->data);
                    exit();
                }

                $this->calculate_payment($list, $cusID);
                exit;
            }
        }

        if ($action == "save") {
            $act = $this->input->post('act') ? $this->input->post('act') : '';

            if ($act == 'use_manual_Inv') {
                $data = $this->input->post('useInvData') ? $this->input->post('useInvData') : array();

                //them moi hd thu sau
                $this->setSessionInvInfo($data);
                echo true;
                exit;
            }

            if ($act == 'save_new_payer') {
                $taxCode = $this->input->post('taxCode') ? $this->input->post('taxCode') : '';
                $cusName = $this->input->post('cusName') ? $this->input->post('cusName') : '';
                $address = $this->input->post('address') ? $this->input->post('address') : '';

                $outMsg = "";
                $result = $this->mdlcommon->savePayerQuickly($taxCode, $cusName, $address, $outMsg);

                if (!$result['success']) {
                    $this->data["error"] = $outMsg;
                } else {
                    $this->data["saveType"] = $outMsg;
                }

                // if ($result['success'] === TRUE && isset($result['newCus']) && count($result['newCus']) > 0) {
                //     $this->load->model("interfaceOracle_model", "mdlOracle");
                //     $this->data['transfer_result'] = $this->mdlOracle->transferNewCustomer($result['newCus']);
                // }

                echo json_encode($this->data);
                exit;
            }

            $data = $this->input->post('data') ? $this->input->post('data') : array();

            //them moi hd thu sau
            if (!$this->hasSessionInvInfo($data["pubType"])) {
                echo json_encode($this->data);
                exit();
            }

            $outInfo = array();
            $this->data['message'] = $this->mdltask->save_EIR_INV($data, $outInfo);

            if (isset($data['invInfo'])) {
                $this->data['invInfo'] = $data['invInfo'];
            } else {
                if (isset($data["pubType"])) {
                    if ($data["pubType"] == "m-inv") {
                        $this->data['invInfo'] = $data['invInfo'] = $outInfo[0];
                        $outInfo = [];
                    } else {
                        $this->data['dftInfo'] = $outInfo;
                    }
                }
            }

            // /send mail
            $mailTo = isset($data["eir"][0]["Mail"]) ? $data["eir"][0]["Mail"] : "";
            $argMailInfo = array();

            if ($mailTo != "") {
                if (count($outInfo) > 0) {
                    foreach ($outInfo as $key => $item) {
                        $arrTemp = array(
                            "mailTo" => $mailTo,
                            "pinCode" => $item["PinCode"],
                        );

                        if (isset($item["DRAFT_NO"])) {
                            $arrTemp["draftNo"] = $item["DRAFT_NO"];
                        }

                        array_push($argMailInfo, $arrTemp);
                    }

                    if ($outInfo[0]["PinCode"] != "") {
                        $this->funcs->generateQRCode($outInfo[0]["PinCode"]);
                    }
                } else {
                    $pinCode = (isset($data['invInfo']['fkey']) ? $data['invInfo']['fkey'] : "");
                    $itemMail = array(
                        "mailTo" => $mailTo
                    );

                    if (isset($data['invInfo']) && count($data['invInfo']) > 0) {
                        $itemMail["inv"] = $data['invInfo']['serial'] . $data['invInfo']['invno'];
                    }
                    $itemMail["reservationCode"] = $data['invInfo']['reservationCode']  ?? '';
                    $itemMail["pinCode"] = $pinCode;

                    if ($pinCode != "") {
                        $this->funcs->generateQRCode($pinCode);
                    }

                    array_push($argMailInfo, $itemMail);
                }

                if (count($argMailInfo) > 0) {
                    $n = isset($data["eir"][0]) ? $data["eir"][0] : array();
                    foreach ($argMailInfo as $item) {
                        $item["CusID"] = isset($n["CusID"]) ? $n["CusID"] : null;
                        $item["PersonalID"] = isset($n["PersonalID"]) ? $n["PersonalID"] : null;
                        $item["NameDD"] = isset($n["NameDD"]) ? $n["NameDD"] : null;
                        $this->sendmail($item);
                    }
                }
            }

            echo json_encode($this->data);
            exit;
        }

        $this->data['title'] = "Lệnh hạ cont hàng";

        $this->load->view('header', $this->data);
        $this->data['cargoTypes'] = json_encode($this->mdltask->getCargoTypes());
        $this->data['sizeTypes'] = json_encode($this->mdlcommon->getSizeType());
        $this->data["transists"] = json_encode($this->mdlcommon->loadTransits());
        $this->data["terminals"] = $this->mdltask->getRelocation();
        $this->data['paymentMethod'] = $this->mdltask->getPaymentMethod();

        //them moi hd thu sau
        $this->data["isDup"] = false;
        if (($this->session->userdata("invInfo") !== null && count(json_decode($this->session->userdata("invInfo"), true)) > 0)) {
            //them moi hd thu sau
            $ssInvInfo = json_decode($this->session->userdata("invInfo"), true);
            $ssInvInfoCas = $ssInvInfo['CAS'];

            $this->data["ssInvInfo"] = $ssInvInfoCas;
            $this->data["isDup"] = $this->mdlInv->checkInvNo($ssInvInfo['serial'], $ssInvInfo['invno']);
        }

        $this->load->view('task/fcl_pre_advice', $this->data);
        $this->load->view('footer');
    }

    public function tskPre_Advice()
    {
        $access = $this->user->access('tskPre_Advice');
        if ($access === false) {
            show_404();
        }

        if (strlen($access) > 5) {
            $this->data['deny'] = $access;
            echo json_encode($this->data);
            exit;
        }

        $action = $this->input->post('action') ? $this->input->post('action') : '';
        if ($action == "view") {
            $act = $this->input->post('act') ? $this->input->post('act') : '';
            if ($act == 'load_payer') {
                $this->data['payers'] = $this->mdltask->getPayers();
                echo json_encode($this->data);
                exit;
            }

            if ($act == 'search_cus_by_tax') {
                $taxCode = $this->input->post('taxCode') ? $this->input->post('taxCode') : '';
                $cus = $this->mdltask->getPayerByTaxCode($taxCode);
                echo json_encode(array('cus' => $cus));
                exit;
            }

            if ($act == 'search_barge') {
                $this->data['barges'] = $this->mdltask->getBarge();
                echo json_encode($this->data);
                exit;
            }

            if ($act == "check_cntr_no") {
                $cntrNo = $this->input->post('cntrNo') ? $this->input->post('cntrNo') : '';

                $checkCntrHoldByConfig = $this->mdltask->checkCntrHoldByConfig($cntrNo, 'E');
                if ($checkCntrHoldByConfig !== NULL) {
                    $this->data["cntr_hold_by_config"] = true;
                    $this->data["hold_content"] = $checkCntrHoldByConfig;
                    echo json_encode($this->data);
                    exit();
                }

                $notAllow = $this->mdltask->checkEIR($cntrNo);
                if (!$notAllow) {
                    $this->data["is_stacking"] = $this->mdltask->checkCntrStacking($cntrNo);
                }
                $this->data["cont_not_allow"] = $notAllow;

                echo json_encode($this->data);
                exit();
            }

            if ($act == 'load_attach_srv') {
                $orderType = $this->input->post('order_type') ? $this->input->post('order_type') : '';
                $this->data['lists'] = $this->mdltask->getAttachServices($orderType);

                echo json_encode($this->data);
                exit;
            }

            if ($act == 'load_payment') {
                $cusID = $this->input->post('cusID') ? $this->input->post('cusID') : '';

                if ($cusID == '') {
                    array_push($this->data["no_payer"], "Vui lòng chọn lại đối tượng thanh toán!");
                    json_encode($this->data);
                    exit;
                }

                $list = $this->input->post('list') ? json_decode($this->input->post('list'), true) : array();
                $nonAttach = $this->input->post('nonAttach') ? json_decode($this->input->post('nonAttach'), true) : array();

                if (count($nonAttach) > 0) {
                    $this->calculate_payment($nonAttach, $cusID, 'services', array('calc_continue' => '1'));
                    $this->calculate_payment($list, $cusID, '', array('calc_continue' => '1'));
                    echo json_encode($this->data);
                    exit();
                }

                $this->calculate_payment($list, $cusID);
                exit;
            }
        }

        if ($action == "save") {

            $act = $this->input->post('act') ? $this->input->post('act') : '';

            if ($act == 'use_manual_Inv') {
                $data = $this->input->post('useInvData') ? $this->input->post('useInvData') : array();

                //them moi hd thu sau
                $this->setSessionInvInfo($data);
                echo true;
                exit;
            }

            if ($act == 'save_new_payer') {
                $taxCode = $this->input->post('taxCode') ? $this->input->post('taxCode') : '';
                $cusName = $this->input->post('cusName') ? $this->input->post('cusName') : '';
                $address = $this->input->post('address') ? $this->input->post('address') : '';

                $errorMsg = "";
                $result = $this->mdlcommon->savePayerQuickly($taxCode, $cusName, $address, $errorMsg);

                if (!$result['success']) {
                    $this->data["error"] = $errorMsg;
                } else {
                    $this->data["saveType"] = $errorMsg;
                }

                // if ($result['success'] === TRUE && isset($result['newCus']) && count($result['newCus']) > 0) {
                //     $this->load->model("interfaceOracle_model", "mdlOracle");
                //     $this->data['transfer_result'] = $this->mdlOracle->transferNewCustomer($result['newCus']);
                // }

                echo json_encode($this->data);
                exit;
            }

            $data = $this->input->post('data') ? $this->input->post('data') : array();

            //them moi hd thu sau
            if (!$this->hasSessionInvInfo($data["pubType"])) {
                echo json_encode($this->data);
                exit();
            }

            $outInfo = array();
            $this->data['message'] = $this->mdltask->save_EIR_INV($data, $outInfo);

            if (isset($data['invInfo'])) {
                $this->data['invInfo'] = $data['invInfo'];
            } else {
                if (isset($data["pubType"])) {
                    if ($data["pubType"] == "m-inv") {
                        $this->data['invInfo'] = $data['invInfo'] = $outInfo[0];
                        $outInfo = [];
                    } else {
                        $this->data['dftInfo'] = $outInfo;
                    }
                }
            }

            // /send mail
            $mailTo = isset($data["eir"][0]["Mail"]) ? $data["eir"][0]["Mail"] : "";
            $argMailInfo = array();

            if ($mailTo != "") {
                if (count($outInfo) > 0) {
                    foreach ($outInfo as $key => $item) {
                        $arrTemp = array(
                            "mailTo" => $mailTo,
                            "pinCode" => $item["PinCode"],
                        );

                        if (isset($item["DRAFT_NO"])) {
                            $arrTemp["draftNo"] = $item["DRAFT_NO"];
                        }

                        array_push($argMailInfo, $arrTemp);
                    }

                    if ($outInfo[0]["PinCode"] != "") {
                        $this->funcs->generateQRCode($outInfo[0]["PinCode"]);
                    }
                } else {
                    $pinCode = (isset($data['invInfo']['fkey']) ? $data['invInfo']['fkey'] : "");
                    $itemMail = array(
                        "mailTo" => $mailTo,
                    );

                    if (isset($data['invInfo']) && count($data['invInfo']) > 0) {
                        $itemMail["inv"] = $data['invInfo']['serial'] . $data['invInfo']['invno'];
                    }
                    $itemMail["reservationCode"] = $data['invInfo']['reservationCode']  ?? '';
                    $itemMail["pinCode"] = $pinCode;

                    if ($pinCode != "") {
                        $this->funcs->generateQRCode($pinCode);
                    }

                    array_push($argMailInfo, $itemMail);
                }

                if (count($argMailInfo) > 0) {
                    $n = isset($data["eir"][0]) ? $data["eir"][0] : array();
                    foreach ($argMailInfo as $item) {
                        $item["CusID"] = isset($n["CusID"]) ? $n["CusID"] : null;
                        $item["PersonalID"] = isset($n["PersonalID"]) ? $n["PersonalID"] : null;
                        $item["NameDD"] = isset($n["NameDD"]) ? $n["NameDD"] : null;
                        $this->sendmail($item);
                    }
                }
                // $this->data["sendMailInfo"] = $argMailInfo;
            }

            echo json_encode($this->data);
            exit;
        }

        $this->data['title'] = "Lệnh hạ cont rỗng";

        $this->load->view('header', $this->data);
        $this->data['cargoTypes'] = json_encode($this->mdltask->getCargoTypes());
        $this->data['sizeTypes'] = json_encode($this->mdlcommon->getSizeType());
        $this->data['oprs'] = json_encode($this->mdlcommon->getOprs());
        $this->data['paymentMethod'] = $this->mdltask->getPaymentMethod();

        //them moi hd thu sau
        $this->data["isDup"] = false;
        if (($this->session->userdata("invInfo") !== null && count(json_decode($this->session->userdata("invInfo"), true)) > 0)) {
            //them moi hd thu sau
            $ssInvInfo = json_decode($this->session->userdata("invInfo"), true);
            $ssInvInfoCas = $ssInvInfo['CAS'];

            $this->data["ssInvInfo"] = $ssInvInfoCas;
            $this->data["isDup"] = $this->mdlInv->checkInvNo($ssInvInfo['serial'], $ssInvInfo['invno']);
        }

        $this->load->view('task/empty_pre_advice', $this->data);
        $this->load->view('footer');
    }

    public function tskRenewedOrder()
    {
        $access = $this->user->access('tskRenewedOrder');
        if ($access === false) {
            show_404();
        }

        if (strlen($access) > 5) {
            $this->data['deny'] = $access;
            echo json_encode($this->data);
            exit;
        }

        $action = $this->input->post('action') ? $this->input->post('action') : '';
        if ($action == "view") {
            $act = $this->input->post('act') ? $this->input->post('act') : '';
            if ($act == 'search') {
                $args = array(
                    "fromDate" => $this->input->post('fromDate') ? $this->input->post('fromDate') : '',
                    "toDate" => $this->input->post('toDate') ? $this->input->post('toDate') : '',
                    "ordNo" => $this->input->post('ordNo') ? $this->input->post('ordNo') : '',
                    "cntrNo" => $this->input->post('cntrNo') ? $this->input->post('cntrNo') : '',
                    "pinCode" => $this->input->post('pinCode') ? $this->input->post('pinCode') : '',
                    "ordType" => $this->input->post('ordType') ? $this->input->post('ordType') : ''
                );

                $this->data['list'] = $this->mdltask->getRenewedOrder($args);
                echo json_encode($this->data);
                exit;
            }

            if ($act == 'load_payer') {
                $this->data['payers'] = $this->mdltask->getPayers();
                echo json_encode($this->data);
                exit;
            }

            if ($act == 'search_cus_by_tax') {
                $taxCode = $this->input->post('taxCode') ? $this->input->post('taxCode') : '';
                $cus = $this->mdltask->getPayerByTaxCode($taxCode);
                echo json_encode(array('cus' => $cus));
                exit;
            }

            if ($act == 'load_payment') {
                $datas = $this->input->post('datas') ? $this->input->post('datas') : array();
                $cusID = $this->input->post('cusID') ? $this->input->post('cusID') : "";

                $lstCalcPayment = array();
                $totalPluginHour = [];
                $totalDayInYard = [];
                $this->data["error"] = array();
                $moreDesc = "";
                $sddDesc = [];

                foreach ($datas as $item) {
                    $orderItem = $this->mdltask->load_SSOrder_Renewed($item["OrderType"], $item["OrderNo"], $item["CntrNo"]);

                    if (!is_array($orderItem) || count($orderItem) == 0) {
                        array_push(
                            $this->data["error"],
                            "số lệnh/số cont [" . $item["OrderNo"] . "/ " . $item["CntrNo"] . "] không tìm thấy lệnh gốc! Kiểm tra lại!"
                        );
                    } else {
                        if ($item["NewExpPluginDate"] != "") {
                            //reg key for diff cont
                            $sz = $this->getContSize($orderItem['ISO_SZTP']);
                            $difKeyPlugin = $sz . '-' . $orderItem['Status'] . '-' . $orderItem['CARGO_TYPE'] . '-' . $orderItem['IsLocal'];

                            $timeOldPlug = strtotime($this->funcs->dbDateTime($item["ExpPluginDate"]));
                            $timeNewPlug = strtotime($this->funcs->dbDateTime($item["NewExpPluginDate"]));

                            $timePlugin = $this->calcTimePlugin($orderItem["OprID"], $timeOldPlug, $timeNewPlug);

                            if (!$timePlugin) {
                                array_push($this->data["error"], "Hãng khai thác [" . $orderItem["OprID"] . "] chưa được cấu hình tính điện lạnh!");
                                continue;
                            }

                            $oldSumPlugin = isset($totalPluginHour[$difKeyPlugin]) ? $totalPluginHour[$difKeyPlugin] : 0;
                            $totalPluginHour[$difKeyPlugin] = $oldSumPlugin + $timePlugin;

                            $strNote = " [Số lệnh / Hạn điện cũ / Hạn điện mới: "
                                . $item["OrderNo"] . " / " . $item["ExpPluginDate"]
                                . " / " . $item["NewExpPluginDate"] . "] ";
                            $orderItem["CJMode_CD"] = "SDD";
                            $orderItem["ExpPluginDate"] = $item["NewExpPluginDate"];
                            $orderItem["Note"] = isset($orderItem["Note"])
                                ? $orderItem["Note"] . $strNote
                                : $strNote;

                            //add them thong tin dien lanh vao draft detail
                            // $moreDesc .= sprintf(
                            //     "%s: %s - %s|", //CNTR0123456: 2021/01/01 00:00 - 2021/01/02 23:59|
                            //     $item["CntrNo"],
                            //     $item["ExpPluginDate"],
                            //     $item["NewExpPluginDate"]
                            //     // $this->funcs->clientDateTime($item["ExpPluginDate"], "/"),
                            //     // $this->funcs->clientDateTime($item["NewExpPluginDate"], "/"),
                            // );
                            if ($sddDesc[$difKeyPlugin]) {
                                $sddDesc[$difKeyPlugin] .= sprintf(
                                    // "%s: %s - %s|", //CNTR0123456: 2021/01/01 00:00 - 2021/01/02 23:59|
                                    "%s|", //CNTR0123456: 2021/01/01 00:00 - 2021/01/02 23:59|
                                    $item["CntrNo"],
                                    // $item["ExpPluginDate"],
                                    // $item["NewExpPluginDate"]
                                );
                            } else {
                                $sddDesc[$difKeyPlugin] = sprintf(
                                    // "%s: %s - %s|", //CNTR0123456: 2021/01/01 00:00 - 2021/01/02 23:59|
                                    "%s", //CNTR0123456: 2021/01/01 00:00 - 2021/01/02 23:59|
                                    $item["CntrNo"],
                                    // $item["ExpPluginDate"],
                                    // $item["NewExpPluginDate"]
                                );
                            }
                            // -- add them thong tin dien lanh vao draft detail

                            array_push($lstCalcPayment, $orderItem);
                        } else {
                            $oldDateYard = strtotime($this->funcs->dbDateTime(explode(' ', $item["ExpDate"])[0]));
                            $newDateYard = strtotime($this->funcs->dbDateTime(explode(' ', $item["NewExpDate"])[0]));

                            // $daysinYard = round( ( $newDateYard - $oldDateYard ) / ( 60 * 60 * 24 ) + 1);

                            $daysinYard = ceil(($newDateYard - $oldDateYard) / (60 * 60 * 24));
                            $daysinYard = $daysinYard > 0 ? $daysinYard : 0;

                            //reg key for diff cont
                            $sz = $this->getContSize($orderItem['ISO_SZTP']);
                            $difKeyStorage = $sz . '-' . $orderItem['Status'] . '-' . $orderItem['CARGO_TYPE'] . '-' . $orderItem['IsLocal'];

                            $orderItem["CJMode_CD"] = "LBC";
                            $orderItem["ExpDate"] = $item["NewExpDate"];

                            $strNote = " [Số lệnh / Hạn lệnh cũ / Hạn lệnh mới: "
                                . $item["OrderNo"] . " / " . $item["ExpDate"] . " / " . $item["NewExpDate"] . "] ";

                            $orderItem["Note"] = isset($orderItem["Note"])
                                ? $orderItem["Note"] . $strNote
                                : $strNote;

                            $oldSum = isset($totalDayInYard[$difKeyStorage]) ? $totalDayInYard[$difKeyStorage] : 0;
                            $totalDayInYard[$difKeyStorage] = $oldSum + $daysinYard;

                            //add them thong tin luu bai vao draft detail
                            $moreDesc .= sprintf(
                                // "%s: %s - %s|", //CNTR0123456: 2021/01/01 00:00 - 2021/01/02 23:59|
                                "%s", //CNTR0123456: 2021/01/01 00:00 - 2021/01/02 23:59|
                                $item["CntrNo"],
                                // $item["ExpDate"],
                                // $item["NewExpDate"]
                                // $this->funcs->clientDateTime($item["ExpDate"], "/"),
                                // $this->funcs->clientDateTime($item["NewExpDate"], "/"),
                            );
                            // -- add them thong tin luu bai vao draft detail

                            array_push($lstCalcPayment, $orderItem);
                        }
                    }
                }

                if (count($lstCalcPayment) == 0) {
                    echo json_encode($this->data);
                    exit();
                }

                $qty = array();

                if (count($totalPluginHour) > 0 && array_sum(array_values($totalPluginHour)) > 0) {
                    $qty["SDD"] = $totalPluginHour;
                }

                if (count($totalDayInYard) > 0 && array_sum(array_values($totalDayInYard)) > 0) {
                    $qty["LBC"] = $totalDayInYard;
                }

                if (count($qty) > 0) {
                    empty($sddDesc) ? "" :  $moreDesc =   $sddDesc;
                    $addinfo = array(
                        "Quantity" => $qty,
                        "TRF_DESC_MORE" => $moreDesc
                    );

                    $this->calculate_payment($lstCalcPayment, $cusID, 'services', $addinfo);
                } else {
                    echo json_encode($this->data);
                }
                exit;
            }
        }

        if ($action == "save") {
            $act = $this->input->post('act') ? $this->input->post('act') : "";
            $updateOrders = $this->input->post('updateOrder') ? $this->input->post('updateOrder') : array();

            if ($act == 'use_manual_Inv') {
                $data = $this->input->post('useInvData') ? $this->input->post('useInvData') : array();

                //them moi hd thu sau
                $this->setSessionInvInfo($data);
                echo true;
                exit;
            }

            if ($act == "updateOnly") {
                $this->data["message"] = $this->mdltask->updateOrder_byRenewed($updateOrders);
                echo json_encode($this->data);
                exit;
            }

            $data = $this->input->post('data') ? $this->input->post('data') : array();

            //them moi hd thu sau
            if (!$this->hasSessionInvInfo($data["pubType"])) {
                echo json_encode($this->data);
                exit();
            }

            $outInfo = array();

            $this->data['message'] = $this->mdltask->save_SRV_ODR_INV($data, "", $outInfo);

            if (isset($data['invInfo'])) {
                $this->data['invInfo'] = $data['invInfo'];
            } else {
                if (isset($data["pubType"])) {
                    if ($data["pubType"] == "m-inv") {
                        $this->data['invInfo'] = $data['invInfo'] = $outInfo[0];
                        $outInfo = [];
                    } else {
                        $this->data['dftInfo'] = $outInfo;
                    }
                }
            }

            $pinCode = count($outInfo) > 0 ? $outInfo[0]["PinCode"] : (isset($data['invInfo']['fkey']) ? $data['invInfo']['fkey'] : "");

            if ($pinCode != "") {
                $this->funcs->generateQRCode($pinCode);
            }

            $this->mdltask->updateOrder_byRenewed($updateOrders);

            if (isset($data['invInfo'])) {
                $this->data['invInfo'] = $data['invInfo'];
            }

            echo json_encode($this->data);
            exit;
        }

        $this->data['title'] = 'Gia hạn lệnh';

        $this->load->view('header', $this->data);

        $this->data['paymentMethod'] = $this->mdltask->getPaymentMethod();
        //them moi hd thu sau
        $this->data["isDup"] = false;
        if (($this->session->userdata("invInfo") !== null && count(json_decode($this->session->userdata("invInfo"), true)) > 0)) {
            //them moi hd thu sau
            $ssInvInfo = json_decode($this->session->userdata("invInfo"), true);
            $ssInvInfoCas = $ssInvInfo['CAS'];

            $this->data["ssInvInfo"] = $ssInvInfoCas;
            $this->data["isDup"] = $this->mdlInv->checkInvNo($ssInvInfo['serial'], $ssInvInfo['invno']);
        }

        $this->load->view('task/renewed_order', $this->data);
        $this->load->view('footer');
    }

    // public function tskExample(){
    //     $draw = intval( $this->input->post("draw") );

    //     $start = intval( $this->input->post("start") );
    //     $length = intval( $this->input->post("length") );

    //     $data = array();

    //     $args = $this->input->post("args") ? $this->input->post("args") : array();

    //     $resultInquiry = $this->mdltask->loadEirInquiry( $args , $start, $length );

    //     foreach ( $resultInquiry as $item ) {
    //         $start++;

    //         array_push( $data, array( $start
    //                         , $item["bXNVC"]
    //                         , $item["CJModeName"]
    //                         , $item["OrderNo"]
    //                         , $item["PinCode"]
    //                         , $item["CntrNo"]
    //                         , $item["ISO_SZTP"]
    //                         , $item["DMethod_CD"]
    //                         , $item["ShipName"] !== null ? ($item["ShipName"]." / ".$item["ImVoy"]." / ".$item["ExVoy"]) : ""
    //                         , $item["BLNo"]
    //                         , $item["BookingNo"]
    //                         , $item["CusID"]
    //                         , $item["SHIPPER_NAME"]
    //                         , $item["Note"]
    //                     )
    //         );
    //     }

    //     $rowCount = $this->mdltask->countEirInquiry( $args );
    //     $output = array(
    //         "draw" => $_POST['draw'],
    //         "recordsTotal" => $rowCount,
    //         "recordsFiltered" => $rowCount,
    //         "data" => $data,
    //     );

    //     echo json_encode($output);
    //     exit();
    // }

    public function tskEirInquiry()
    {
        $access = $this->user->access('tskEirInquiry');
        if ($access === false) {
            show_404();
        }

        if (strlen($access) > 5) {
            $this->data['deny'] = $access;
            echo json_encode($this->data);
            exit;
        }

        $action = $this->input->post('action') ? $this->input->post('action') : '';
        if ($action == "view") {
            $act = $this->input->post('act') ? $this->input->post('act') : '';
            if ($act == 'searh_ship') {
                $arrstt = $this->input->post('arrStatus') ? $this->input->post('arrStatus') : '';
                $year = $this->input->post('shipyear') ? $this->input->post('shipyear') : '';
                $name = $this->input->post('shipname') ? $this->input->post('shipname') : '';

                $this->data['vsls'] = $this->mdltask->searchShip($arrstt, $year, $name);
                echo json_encode($this->data);
                exit;
            }

            if ($act == 'search_order') {
                $args = $this->input->post('args') ? $this->input->post('args') : array();

                $this->data["results"] = $this->mdltask->loadEirInquiry($args);
                $this->data["countOrder"] = $this->mdltask->countOrder($args);

                echo json_encode($this->data);
                exit;
            }

            if ($act == 'search_order_2') {
                $args = $this->input->post('args') ? $this->input->post('args') : array();

                $draw = intval($this->input->post("draw"));
                $start = intval($this->input->post("start"));
                $length = intval($this->input->post("length"));

                $ouput = $this->mdltask->loadEirInquiry2($args, $start, $length, $draw);

                echo json_encode($ouput);
                exit();
            }

            if ($act == 'count_order') {
                $args = $this->input->post('args') ? $this->input->post('args') : array();

                $this->data["countOrder"] = $this->mdltask->countOrder($args);

                echo json_encode($this->data);
                exit();
            }

            if ($act == "load_img") {
                $orderNo = $this->input->post('orderNo') ? $this->input->post('orderNo') : '';
                $imageNames = array();

                if ($orderNo != '') {
                    foreach (glob(FCPATH . "/assets/img/ct/" . $orderNo . "*.{jpg,png,gif}", GLOB_BRACE) as $filename) {
                        array_push($imageNames, basename($filename));
                    }
                }

                $this->data["imgs"] = $imageNames;
                echo json_encode($this->data);
                exit;
            }

            if ($act == 'load_payment') {
                $list = $this->input->post('list') ? $this->input->post('list') : array();
                $cusID = $this->input->post('cusID') ? $this->input->post('cusID') : '';
                $this->calculate_payment($list, $cusID);
                exit;
            }
        }

        if ($action == "save") {
            $data = $this->input->post('data') ? $this->input->post('data') : array();
            $this->data['message'] = $this->mdltask->save_EIR_INV($data);
            $this->data['invInfo'] = $data['invInfo'];

            $attach_img = $this->input->post('attach_img') ? $this->input->post('attach_img') : array();
            $eirNo = $this->input->post('eirno') ? $this->input->post('eirno') : '';

            $i = 1;
            foreach ($attach_img as $imgData) {
                if (!isset($imgData) || $imgData == "") {
                    continue;
                }

                if (preg_match("/^data:image\/(?<extension>(?:png|gif|jpg|jpeg));base64,(?<image>.+)$/", $imgData, $matchings)) {
                    $img = base64_decode($matchings['image']);
                    $extension = $matchings['extension'] == "jpeg" ? "jpg" : $matchings['extension'];
                    $filename = sprintf("%s/assets/img/ct/%s_%s.%s", FCPATH, $eirNo, $i, $extension);

                    file_put_contents($filename, $img);

                    $i++;
                }
            }

            echo json_encode($this->data);
            exit;
        }

        $this->data['title'] = "Truy vấn thông tin lệnh";

        $this->load->view('header', $this->data);
        $this->data['cargoTypes'] = json_encode($this->mdltask->getCargoTypes());
        $this->data['sizeTypes'] = json_encode($this->mdlcommon->getSizeType());
        $this->data['oprIDs'] = json_encode($this->mdlcommon->getAllOpr());

        $this->load->view('task/eir_inquiry', $this->data);
        $this->load->view('footer');
    }

    public function tskUpdateOrder()
    {
        $access = $this->user->access('tskUpdateOrder');
        if ($access === false) {
            show_404();
        }

        if (strlen($access) > 5) {
            $this->data['deny'] = $access;
            echo json_encode($this->data);
            exit;
        }

        $action = $this->input->post('action') ? $this->input->post('action') : '';
        if ($action == "view") {
            $act = $this->input->post('act') ? $this->input->post('act') : '';

            if ($act == 'search') {
                $ordNo = $this->input->post('ordNo') ? $this->input->post('ordNo') : '';
                $cntrNo = $this->input->post('cntrNo') ? $this->input->post('cntrNo') : '';
                $pinCode = $this->input->post('pinCode') ? $this->input->post('pinCode') : '';
                $ordType = $this->input->post('ordType') ? $this->input->post('ordType') : '';

                if ($ordType == "NH") {
                    $this->data['list'] = $this->mdltask->getEir4Update($ordNo, $cntrNo, $pinCode);
                } else {
                    $this->data['list'] = $this->mdltask->getOrder4Update($ordNo, $cntrNo, $pinCode);
                }
                echo json_encode($this->data);
                exit;
            }

            if ($act == 'getLane') {
                $shipkey = $this->input->post('shipkey') ? $this->input->post('shipkey') : '';
                $this->data['oprs'] = $this->mdltask->getLaneOprs($shipkey);
                $this->data['ports'] = $this->mdltask->getLanePortID($shipkey);
                echo json_encode($this->data);
                exit;
            }
        }

        if ($action == "edit") {

            $act = $this->input->post('act') ? $this->input->post('act') : '';
            $ordType = $this->input->post('ordType') ? $this->input->post('ordType') : '';
            $data = $this->input->post('data') ? $this->input->post('data') : array();
            $dataOrder = $this->input->post('dataOrder') ? $this->input->post('dataOrder') : array();
            if ($act == 'unlink-booking') {
                $this->data['success'] = $this->mdltask->unlink_booking($data, $ordType);
                echo json_encode($this->data);
                exit;
            }

            if ($ordType == '') {
                $this->data['no_ordType'] = 'Vui lòng thao tác lại dữ liệu!';
                echo json_encode($this->data);
                exit;
            }

            $this->data['message'] = $this->mdltask->update_order_monitor($data, $ordType, $dataOrder);

            echo json_encode($this->data);
            exit;
        }

        $this->data['title'] = "Cập nhật thông tin lệnh";

        $this->load->view('header', $this->data);

        $this->data["transists"] = json_encode($this->mdlcommon->loadTransits());
        $this->data["terminals"] = $this->mdltask->getRelocation();
        $this->data['barges'] = $this->mdltask->getBarge();
        $this->data['cargoTypes'] = $this->mdltask->getCargoTypes();
        $this->data['paymentMethod'] = $this->mdltask->getPaymentMethod();
        $this->data['payers'] = $this->mdltask->getPayers();

        $this->load->view('task/update_order', $this->data);
        $this->load->view('footer');
    }

    public function payment_success()
    {
        if (!isset($_SERVER['HTTP_REFERER'])) {
            redirect(md5('home'));
        }

        $invInfo = $this->input->post('invInfo') ? (array)json_decode($this->input->post('invInfo'), true) : array(); //$this->data['invInfo']

        $pinCode = '';
        if (count($invInfo) > 0) {
            $pinCode = $invInfo["fkey"];
            $this->data['invInfo'] = $invInfo;
        } else {
            redirect(md5('home'));
        }

        if ($pinCode == '') {
            $this->data['error'] = "Phát sinh sự cố !";
        }

        $pngAbsoluteFilePath = FCPATH . "assets/img/qrcode_gen/" . $pinCode . ".png";

        if (file_exists($pngAbsoluteFilePath)) {
            $qrCodeData = base64_encode(file_get_contents($pngAbsoluteFilePath));
            $this->data['qr'] = 'data: ' . mime_content_type($pngAbsoluteFilePath) . ';base64,' . $qrCodeData;
        } else {
            $this->funcs->generateQRCode($pinCode);
            $qrCodeData = base64_encode(file_get_contents($pngAbsoluteFilePath));
            $this->data['qr'] = 'data: ' . mime_content_type($pngAbsoluteFilePath) . ';base64,' . $qrCodeData;
        }

        //khog phai hddt
        if (!isset($invInfo["hddt"]) || $invInfo["hddt"] != '1') { //them moi hd thu sau
            $this->data["m_inv_data"] = $this->mdltask->getInv4Print($pinCode);
            $this->data['type'] = isset($invInfo['type']) ? $invInfo['type'] : 'CAS'; //them moi hd thu sau

            if (count($this->data["m_inv_data"]) > 0) {
                $totalAmt = (float)$this->data["m_inv_data"][0]["TAMOUNT"];

                //them moi lam tron so
                $currency = $this->data["m_inv_data"][0]["CURRENCYID"];
                $roundNum = $this->config->item('ROUND_NUM')[$currency];
                $formatNum = "#,###";
                if ($roundNum > 0) {
                    $formatNum .= '.' . substr("0000000000000", -$roundNum);
                }

                //lam tron so luong+don gia theo yeu cau KT
                $roundNumQty_Unit = $this->config->item('ROUND_NUM_QTY_UNIT');
                $formatNumQty_Unit = "#,###";
                if ($roundNumQty_Unit > 0) {
                    $formatNumQty_Unit .= '.' . substr("0000000000000", -$roundNumQty_Unit);
                }
                $this->data['formatNumQty_Unit'] = $formatNumQty_Unit; //lam tron so luong+don gia theo yeu cau KT

                $this->data['formatNum'] = $formatNum; //them moi lam tron so
                $this->data["AmountInWords"] = $this->funcs->convert_number_to_words(round($totalAmt, $roundNum), $currency); //doc tien usd
            }
        }

        $this->data['title'] = $invInfo["serial"] . $invInfo['invno'];

        $this->data['menus'] = $this->menu->getMenu();
        $this->load->view('header', $this->data);

        $islaser = $this->config->item('IS_LASER_PRINT');
        $this->data['is_laser'] = $islaser;
        if ($islaser === 1) {
            $this->data['eir_laser'] =  $this->load->view('print_file/print_eir_laser', array('qr_url' => $this->data['qr']), TRUE);
            $this->data['srv_laser'] =  $this->load->view('print_file/print_service_laser', array('qr_url' => $this->data['qr']), TRUE);
        }

        $this->data["print_data"] = $this->mdltask->getOrder4Print($pinCode);
        $this->load->view('task/payment_success', $this->data);
        $this->load->view('footer');
    }

    public function draft_success()
    {
        if (!isset($_SERVER['HTTP_REFERER'])) {
            redirect(md5('home'));
        }

        $dftInfo = $this->input->post('dftInfo') ? (array)json_decode($this->input->post('dftInfo'), true) : array();

        if (count($dftInfo) > 0) {
            $this->data['dftInfo'] = $dftInfo;
        } else {
            redirect(md5('home'));
        }

        $results = array();
        $pinCode = $dftInfo[0]["PinCode"];

        $pngAbsoluteFilePath = FCPATH . "assets/img/qrcode_gen/" . $pinCode . ".png";

        if (file_exists($pngAbsoluteFilePath)) {
            $qrCodeData = base64_encode(file_get_contents($pngAbsoluteFilePath));
            $qr = 'data: ' . mime_content_type($pngAbsoluteFilePath) . ';base64,' . $qrCodeData;
        } else {
            $this->funcs->generateQRCode($pinCode);
            $qrCodeData = base64_encode(file_get_contents($pngAbsoluteFilePath));
            $qr = 'data: ' . mime_content_type($pngAbsoluteFilePath) . ';base64,' . $qrCodeData;
        }

        $this->data["qr"] = $qr;
        $this->data["pinCode"] = array_column($dftInfo, "PinCode");
        $this->data["draftNos"] = array_column($dftInfo, "DRAFT_NO");
        $this->data["print_data"] = $this->mdltask->getOrder4Print($pinCode);

        $this->data['title'] = is_array($this->data['draftNos']) ? implode(" - ", $this->data['draftNos']) : "Thành công!";

        $this->data['menus'] = $this->menu->getMenu();
        $this->load->view('header', $this->data);

        $islaser = $this->config->item('IS_LASER_PRINT');
        $this->data['is_laser'] = $islaser;
        if ($islaser === 1) {
            $this->data['eir_laser'] =  $this->load->view('print_file/print_eir_laser', array('qr_url' => $this->data['qr']), TRUE);
            $this->data['srv_laser'] =  $this->load->view('print_file/print_service_laser', array('qr_url' => $this->data['qr']), TRUE);
        }

        $this->load->view('task/draft_success', $this->data);
        $this->load->view('footer');
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

    private function calculate_payment($list, $cusID, $task_types = '', $addInfo = array())
    {
        $trf_stds = array();
        switch ($task_types) {
            case 'services':
                $trf_stds = $this->mdltask->loadServiceTariff($list);
                break;
            default:
                $trf_stds = $this->mdltask->loadTariffSTD($list);
                break;
        }

        $err = array();

        if (count($trf_stds) == 0) {

            if (count($addInfo) > 0) { //nếu là dịch vụ đính kèm
                $this->data["no_tariff"] = 'Không tìm thấy biểu cước dịch vụ phù hợp!';
            } else {
                $this->data['no_tariff_end'] = 'Không tìm thấy biểu cước phù hợp! Vui lòng kiểm tra lại!';
                $this->data['results'] = array();
                echo json_encode($this->data);
                exit();
            }

            return;
        }

        $newarray = array();
        $calc_arr = array();
        $sumAMT = 0;
        $sumVAT_AMT = 0;
        $sumSub_AMT = 0;
        $sumDIS_AMT = 0;

        foreach ($trf_stds as $key => $val) {
            if (!is_array($val)) {
                array_push($err, $val);
                continue;
            }

            $sz = $this->getContSize($val['ISO_SZTP']);
            $newKey = $sz . "-" . $val["FE"] . "-" . $val["CARGO_TYPE"] . "-" . $val["IsLocal"] . "-" . $val['CJMode_CD'];

            $newarray[$newKey][$key] = $val;
        }
        //kiểm tra loại thanh toán khách hàng
        if (isset($list[0]['PAYMENT_TYPE']) && $list[0]['PAYMENT_TYPE']) {
            $payment_type = '*';
            switch ($list[0]['PAYMENT_TYPE']) {
                case 'M':
                    $payment_type = 'CAS';
                    break;
                case 'C':
                    $payment_type = 'CRE';
                    break;
            }
        }

        foreach ($newarray as $newkey => $newitem) {
            $cont_count_in_tariff = count($newitem);
            foreach ($newitem as $ka => $kv) {
                $check_continue = false;
                if (count($calc_arr) > 0) {
                    foreach ($calc_arr as $idx => $tr) {
                        $sz_prefix = $this->getContSize($tr['ISO_SZTP']);
                        $prefix_compare = $sz_prefix . "-" . $tr['FE'] . "-" . $tr['Cargotype'] . "-" . $tr['IsLocal'] . "-" . $tr['CNTR_JOB_TYPE'];

                        if ($kv['TRF_CODE'] == $tr['TariffCode'] && $newkey == $prefix_compare) {
                            $check_continue = true;
                            continue;
                        }
                    }
                }

                if ($check_continue) continue;

                $cont_ISO_SIZE = explode("-", $newkey)[0];

                $rs = array(
                    'DraftInvoice' => '',
                    // 'OrderNo'=> $kv['OrderNo'],
                    'TariffCode' => $kv['TRF_CODE'],
                    'TariffDescription' => $kv['TRF_STD_DESC'],
                    'Unit' => '',
                    'JobMode' => $kv['JOB_KIND'],
                    'DeliveryMethod' => $kv['DMETHOD_CD'],
                    'Cargotype' => $kv['CARGO_TYPE'],
                    'ISO_SZTP' => $kv['ISO_SZTP'],
                    'FE' => $kv['FE'],
                    'IsLocal' => $kv['IsLocal'],
                    'Quantity' => 0,
                    'StandardTariff' => $kv['AMT_' . $kv['FE'] . $cont_ISO_SIZE],
                    'DiscountTariff' => 0,
                    'DiscountedTariff' => 0,
                    'Amount' => 0,
                    'VatRate' => $kv['VAT'],
                    'VATAmount' => 0,
                    'SubAmount' => 0,
                    'Currency' => $kv['CURRENCYID'],
                    'SIZE' => $cont_ISO_SIZE,
                    'CNTR_JOB_TYPE' => $kv['CJMode_CD'],
                    'IX_CD' => $kv['IX_CD'],
                    'VAT_CHK' => $kv['INCLUDE_VAT'],
                    'Remark' => isset($kv['Remark']) ? $kv['Remark'] : NULL,
                    'TRF_DESC_MORE' => isset($kv['TRF_DESC_MORE']) ? $kv['TRF_DESC_MORE'] : NULL
                );

                if ($rs["Currency"] === "USD") {
                    $rate = $this->mdltask->getExchangeRate("USD");
                    $rs["StandardTariff"] = $rs["StandardTariff"] * $rate;
                    $rs["Currency"] = "VND";
                }

                $rs['Unit'] = $this->mdltask->getTRF_unitCode($kv['TRF_CODE']);

                if (isset($addInfo["Quantity"][$kv["CJMode_CD"]])) {
                    foreach ($addInfo["Quantity"][$kv["CJMode_CD"]] as $ki => $vi) {
                        if (preg_match('/(' . implode('|\*)\-(', explode('-', $ki)) . '|\*)/', $newkey)) {
                            $rs["Quantity"] = $vi;
                            $moreDesc = isset($addInfo["TRF_DESC_MORE"]) && !is_array($addInfo["TRF_DESC_MORE"]) ? rtrim($addInfo["TRF_DESC_MORE"], "|") : '';
                            $rs['TRF_DESC_MORE'] = $rs['TRF_DESC_MORE'] . " || " . $moreDesc;
                        }
                    }
                    if (is_array($addInfo["TRF_DESC_MORE"])) {
                        foreach ($addInfo['TRF_DESC_MORE'] as $ki => $vi) {
                            if (preg_match('/(' . implode('|\*)\-(', explode('-', $ki)) . '|\*)/', $newkey)) {
                                $rs['TRF_DESC_MORE'] = $rs['TRF_DESC_MORE'] . $vi;
                            }
                        }
                    }
                } else {
                    $rs['Quantity'] = $cont_count_in_tariff;
                }

                //get discount for tariff
                $wheres = array(
                    $this->funcs->dbDateTime($kv['IssueDate']),
                    $this->funcs->dbDateTime($kv['IssueDate']),
                    $this->funcs->dbDateTime($kv['IssueDate']),
                    $kv['TRF_CODE'],
                    $kv['OprID'],
                    $cusID,
                    $kv['CARGO_TYPE'],
                    $kv['IX_CD'],
                    $kv['DMETHOD_CD'],
                    $kv['JOB_KIND'],
                    $kv['CNTR_JOB_TYPE'],
                    $kv['CURRENCYID'],
                    $payment_type,
                    $kv['IsLocal'],
                    $kv['LANE'] ?? '*'

                );

                $rs['DiscountTariff'] = ($rs['Quantity'] === null || $rs['Quantity'] == 0)
                    ? 0 : $this->mdltask->getDiscount($cont_ISO_SIZE, $rs['FE'], $wheres);

                $discount1 = ($rs['StandardTariff'] + $rs['DiscountTariff']) / (((int)$kv['VAT'] / 100) + 1);
                $discount2 = $rs['StandardTariff'] + $rs['DiscountTariff'];

                $rs['DiscountedTariff'] = $kv['INCLUDE_VAT'] === "1" ? round($discount1) : round($discount2);

                $am = $rs['Quantity'] * ($kv['INCLUDE_VAT'] === "1" ? $discount1 : $discount2);
                $rs['Amount'] = round($am);

                $vatAM = $am * ($rs['VatRate'] / 100);
                $rs['VATAmount'] = round($vatAM);

                $rs['SubAmount'] = ($rs['Amount'] + $rs['VATAmount']);

                if ($rs['SubAmount'] == 0) {
                    $format = '[%s-%s-%s%s] không tìm thấy biểu cước phù hợp! Vui lòng kiểm tra lại!'; //LAYN-GP-40F
                    $this->data['no_tariff_end'] = sprintf($format, $rs['CNTR_JOB_TYPE'], $rs['Cargotype'], $rs['SIZE'], $rs['FE']);
                    $this->data['results'] = array();
                    echo json_encode($this->data);
                    exit();
                }

                $sumAMT += $rs['Amount'];
                $sumVAT_AMT += $rs['VATAmount'];
                $sumSub_AMT += $rs['SubAmount'];

                $sumDIS_AMT += $rs['DiscountTariff'];

                array_push($calc_arr, $rs);
            }
        }

        if (count($err) > 0) {
            $this->data['error'] = $err;
        }

        if (debug_backtrace()[1]['function'] == "tskRenewedOrder") {
            $this->data["renewed_ord"] = $list;
        }

        if (count($addInfo) > 0 && isset($addInfo["calc_continue"]) && $addInfo["calc_continue"] == "1") {
            if (isset($this->data["results"])) {
                foreach ($calc_arr as $item) {
                    array_push($this->data["results"], $item);
                }
            } else {
                $this->data["results"] = $calc_arr;
            }

            if (isset($this->data["SUM_AMT"])) {
                $this->data["SUM_AMT"] += $sumAMT;
            } else {
                $this->data["SUM_AMT"] = $sumAMT;
            }

            if (isset($this->data["SUM_VAT_AMT"])) {
                $this->data["SUM_VAT_AMT"] += $sumVAT_AMT;
            } else {
                $this->data["SUM_VAT_AMT"] = $sumVAT_AMT;
            }

            if (isset($this->data["SUM_SUB_AMT"])) {
                $this->data["SUM_SUB_AMT"] += $sumSub_AMT;
            } else {
                $this->data["SUM_SUB_AMT"] = $sumSub_AMT;
            }

            if (isset($this->data["SUM_DIS_AMT"])) {
                $this->data["SUM_DIS_AMT"] += $sumDIS_AMT;
            } else {
                $this->data["SUM_DIS_AMT"] = $sumDIS_AMT;
            }

            return;
        }

        $this->data['results'] = $calc_arr;
        $this->data['SUM_AMT'] = $sumAMT;
        $this->data['SUM_VAT_AMT'] = $sumVAT_AMT;
        $this->data['SUM_SUB_AMT'] = $sumSub_AMT;
        $this->data['SUM_DIS_AMT'] = $sumDIS_AMT;

        echo json_encode($this->data);
        //exit;
    }

    private function calcTimePlugin($oprID, $plugFrom, $plugTo)
    {
        $rounding = $this->mdlcommon->getPlugConfig($oprID);
        if ($rounding == NULL) {
            return false;
        }

        $minutes = floor(($plugTo - $plugFrom) / 60);

        $hr = floor($minutes / 60);
        $minute = $minutes % 60;

        if ($minute == 0) {
            return $hr;
        }

        $extraHour = 0;
        switch ($rounding) {
            case 'R1':
                $extraHour = $minute <= 30 ? 0.5 : 1;
                break;
            case 'R2':
                $extraHour = $minute > 0 ? 1 : 0;
                break;
            case 'R3': //lam tron 24h
                $extraHour = $minute > 0 ? 24 : 0;
                break;
        }

        return $hr + $extraHour;
    }

    private function sendmail($args)
    {
        $pinCode = $args["pinCode"];
        $reservationCode = $args["reservationCode"] ?? '';
        $orderNo = isset($args["orderNo"]) ? "<li>Số lệnh: <b>" . $args["orderNo"] . "</b></li>" : "";
        $amount = isset($args["amount"]) ? "<li>Số tiền: <b>" . $args["amount"] . "</b></li>" : "";

        if (isset($args["inv"])) {
            $invNo = $args["inv"];
            $invContent = "<li>Số hóa đơn: <b>" . $invNo . "</b></li>";
            $searchUrl = site_url(md5("InvoiceManagement") . '/' . md5("downloadInvPDF")) . "?" . http_build_query(['fkey' => $pinCode, 'inv' => $invNo]);
            $invButtonInquiry = '<a href="' . $searchUrl . '" style="font-family:Tahoma,serif;background-color:#3f00ff;color:#ffffff;font-weight:500;padding:10px 50px 10px 50px;border-radius:4px;border-style:none;text-decoration:none" target="_blank" >XEM HÓA ĐƠN</a>';
        } else {
            $invContent = "<li><b>THU SAU</b></li>";
            $invButtonInquiry = "";
        }

        $draftNo = isset($args["draftNo"]) ? "<li>Số phiếu tính cước: <b>" . $args["draftNo"] . "</b></li>" : "";
        $printOrderUrl = site_url(md5("ExportRPT") . '/' . md5("viewPDFOrderByList")) . "?fkey=" . $pinCode;

        $this->load->library('email');
        $config = array(
            'protocol' => 'smtp',
            'smtp_host' => $this->config->item('SYS_MAIL_HOST'),
            'smtp_port' => $this->config->item('SYS_MAIL_PORT'),
            'smtp_user' => $this->config->item('SYS_MAIL_ADDR'),
            'smtp_pass' => $this->config->item('SYS_MAIL_PASS'),
            'charset' => 'utf-8',
			'smtp_crypto' => 'ssl',
            'wordwrap' => TRUE,
            'crlf' => "\r\n",
            'newline' => "\r\n",
            'mailtype' => 'html'
        );

        $yard_id = $this->config->item('YARD_ID') ?? '';
        $yard_name = $this->config->item('YARD_FULL_NAME') ?? '';
        $invSys = $this->config->item('INV_SYS');
        $inv_portal = $this->config->item($invSys)['PORTAL_URL'] ?? '';

        $this->email->initialize($config);
        $this->email->clear(TRUE);
        $this->email->from($config['smtp_user'], "$yard_id Mail Center");
        $this->email->to($args["mailTo"]);
        $cc = $this->config->item('SYS_MAIL_CC');
        if ($cc !== NULL) {
            $this->email->cc($cc);
        }

        $this->email->subject('[Thông báo] Thanh toán lệnh & phát hành hóa đơn điện tử!');

        $pngAbsoluteFilePath = FCPATH . "assets/img/qrcode_gen/" . $pinCode . ".png";
        $embedQRCode = "";
        if (file_exists($pngAbsoluteFilePath)) {
            $this->email->attach($pngAbsoluteFilePath);

            $cid = $this->email->attachment_cid($pngAbsoluteFilePath);

            if ($cid !== FALSE) {
                $embedQRCode = '<img style="width:95px;height:95px" src="cid:' . $cid . '" alt="' . $pinCode . '" />';
            }
        }

        $mailContent = <<<EOT
            <body>
                <div style="padding: 40px;">
                    <div style="background-color:#f10f0f;border-top-left-radius:4px;border-top-right-radius:4px;padding:20px">
                        <span style="margin-top:20px;font-family:Tahoma;font-size:22px;color:#fff">
                            $yard_name thông báo Phát hành lệnh & Xuất hóa đơn điện tử
                        </span>
                    </div>
                    <div style="border-style:none solid solid;border-width:1px;border-color:#e1e1e1;background-color:#fafafa">
                        <div style="padding:10px 20px 10px 20px;font-family:Tahoma,serif;color:#030303;line-height:26px">
                            <b>Kính gửi: Quý khách hàng</b>
                            <br>
                            <span>$yard_name xin gửi cho Quý khách hóa đơn điện tử với các thông tin như sau: </span>
                        </div>
                        <div style="line-height:30px;background-color:#e1eefb;padding:1px;display:inline-flex;width:100%">
                            <ul style="margin-left:25px;list-style:disc;">
                                $orderNo
                                $draftNo
                                <li>Mã tra cứu: <b>$pinCode</b></li>
                                $invContent
                                $amount
                            </ul>
                            <div style="margin:auto;padding-top:10px">
                                $embedQRCode
                            </div>
                        </div>

                        <div style="padding:10px 20px 10px 20px;font-family:Tahoma,serif;color:#030303;line-height:26px;">
                            <br>
                            <br><br>
                            <span>Link tra cứu hoá đơn: <a href="$inv_portal" style="font-weight: bold;" target="_blank" >$inv_portal</a></span>
                            <span> Mã số bí mật : <b>$reservationCode</b></span>
                        	<br><br>
                            <span>
                                Lưu ý: mã Pin này có thể thay thế cho Lệnh hạ/lấy cont. Đề nghị quý khách hàng bảo mật và giao chỉ định cho Tài xế thực hiện tác nghiệp theo yêu cầu chủ hàng. 
                            </span>
                            <br>
                            <span>
                                Tài xế trình mã PIN tại cổng Cảng để thực hiện thủ tục.
                            </span>
                        </div>
                        <div style="padding:30px 20px 10px 20px;font-family:Tahoma,serif;color:#030303;line-height:26px;">
                            <span>Trân trọng!</span>
                            <br>
                            <span><b>$yard_name</b></span>
                        </div>
                    </div>
                </div>
            </body>
EOT;

        try {
            $this->email->message($mailContent);
            $this->email->send();
            return 'sent';
        } catch (Exception $e) {
            log_message("error", $e->getMessage());
            return 'send mail failed!';
        }
    }

    //them moi hd thu sau
    private function setSessionInvInfo($data, $paymentType = 'CAS')
    {
        $session_inv_info = array();
        if (count($data) > 0) {
            $useInvData = array(
                'serial' => trim($data['serial']),
                'invno' => trim($data['invno'])
            );

            $checkInvNo = $this->mdltask->checkInvNo($useInvData['serial'], $useInvData['invno'], $paymentType);
            if ($checkInvNo) {
                $this->data["isDup"] = true;
                echo json_encode($this->data);
                exit;
            }

            if (($this->session->userdata("invInfo") !== NULL && count(json_decode($this->session->userdata("invInfo"), true)) > 0)) {
                $session_inv_info = json_decode($this->session->userdata("invInfo"), TRUE);
            }

            $session_inv_info[$paymentType] = $useInvData;
            $this->session->set_userdata("invInfo", json_encode($session_inv_info));
        }
    }

    //them moi hd thu sau
    private function hasSessionInvInfo($pubType, $paymentType = 'CAS')
    {
        if ($pubType == "m-inv") {
            if (($this->session->userdata("invInfo") === null || count(json_decode($this->session->userdata("invInfo"), true)) == 0)) {
                $this->data["non_invInfo"] = "Chưa cấu hình hóa đơn!";
                return FALSE;
            }

            $ssInv = json_decode($this->session->userdata("invInfo"), true);
            if (!isset($ssInv[$paymentType])) {
                $this->data["non_invInfo"] = "Chưa cấu hình hóa đơn!";
                return FALSE;
            }

            $manualInvInfo = $ssInv[$paymentType];
            $checkInvNo = $this->mdltask->checkInvNo($manualInvInfo['serial'], $manualInvInfo['invno']);

            if ($checkInvNo) {
                $this->data["isDup"] = true;
                return FALSE;
            }
        }

        return TRUE;
    }
}
