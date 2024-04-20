<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tools extends CI_Controller
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
        $this->load->model("invoice_model", "mdlInv");
        $this->load->model("task_model", "mdltask");
        $this->load->model("interfaceFS_model", "mdlHTKT");
        $this->load->model("report_model", "mdlRpt");
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

    public function tlCancelEntries()
    {
        $access = $this->user->access('tlCancelEntries');
        if ($access === false) {
            show_404();
        }

        if (strlen($access) > 5) {
            $this->data['deny'] = $access;
            echo json_encode($this->data);
            exit;
        }

        $action = $this->input->post('action') ? $this->input->post('action') : '';

        $this->data['title'] = "Hủy hoá đơn - phiếu tính cước";

        if ($action == 'view') {
            $act = $this->input->post('act') ? $this->input->post('act') : '';

            if ($act == 'load_payer') {
                $this->data['payers'] = $this->mdlInv->getPayers();
                echo json_encode($this->data);
                exit;
            }

            if ($act == "search_inv") {
                $fromDate = $this->input->post('fromDate') ? $this->funcs->dbDateTime($this->input->post('fromDate', TRUE)) : '';
                $toDate = $this->input->post('toDate') ? $this->funcs->dbDateTime($this->input->post('toDate', TRUE)) : '';
                $tyeOfDate = $this->input->post('typeOfDate') ? $this->input->post('typeOfDate', TRUE) : '';
                $cusID = $this->input->post('cusID') ? $this->input->post('cusID', TRUE) : '';
                $searchVal = $this->input->post('searchVal') ? $this->input->post('searchVal', TRUE) : '';
                $paymentStatus = $this->input->post('paymentStatus') ? $this->input->post('paymentStatus') : array();
                $paymentType = $this->input->post('paymentType') ? $this->input->post('paymentType') : array();
                $sys = $this->input->post('sys') ? $this->input->post('sys') : '';
                $createdBy = $this->input->post('createdBy') ? $this->input->post('createdBy') : '';

                $operator = $sys == "EP" ? "=" : "!=";

                if ($tyeOfDate == "PTC") {
                    $args = array(
                        "searchVal" => $searchVal,
                        "dft.PAYER" => $cusID,
                        "dft.CreatedBy" => $createdBy,
                        "dft.PAYMENT_STATUS" => $paymentStatus,
                        "dft.INV_TYPE" => $paymentType,
                        "DRAFT_INV_DATE >=" => $fromDate,
                        "DRAFT_INV_DATE <=" => $toDate,
                        "LEFT(dft.DRAFT_INV_NO,2) " . $operator => 'TT',
                        // "LEFT(iv.PinCode,1) " . $operator => 'A'
                    );

                    $this->data["invs"] = $this->mdlInv->loadDraftForCancel($args);
                } else {
                    $args = array(
                        "searchVal" => $searchVal,
                        "iv.PAYER" => $cusID,
                        "iv.CreatedBy" => $createdBy,
                        "iv.PAYMENT_STATUS" => $paymentStatus,
                        "iv.INV_TYPE" => $paymentType,
                        "INV_DATE >=" => $fromDate,
                        "INV_DATE <=" => $toDate,
                        "LEFT(dft.DRAFT_INV_NO,2) " . $operator => 'TT',
                        "LEFT(iv.PinCode,1) " . $operator => 'A'
                    );

                    $this->data["invs"] = $this->mdlInv->loadInvForCancel($args);
                }

                echo json_encode($this->data);
                exit;
            }
        }

        if ($action == 'edit') {
            $act = $this->input->post('act') ? $this->input->post('act') : '';

            if ($act == "cancelLocalInv") {

                $invNo = $this->input->post('invNo', TRUE) ? $this->input->post('invNo', TRUE) : '';
                $draftNo = $this->input->post('draftNo', TRUE) ? $this->input->post('draftNo', TRUE) : '';
                $cancelReason = $this->input->post('cancelReason', TRUE) ? $this->input->post('cancelReason', TRUE) : '';
                $isRemoveOrder = $this->input->post('removeOrder', TRUE) ? $this->input->post('removeOrder', TRUE) : '0';
                $invType = $this->input->post('invType') ? $this->input->post('invType') : '';

                $outputMsg = '';
                $isSuccessCancelInv = $this->mdlInv->cancelLocalInv($invNo, $cancelReason, $outputMsg);

                if (!$isSuccessCancelInv) {
                    $this->data["error"] = $outputMsg;
                    echo json_encode($this->data);
                    exit();
                }

                if ($draftNo != '' && $isSuccessCancelInv) {
                    $isCancelDraft = $this->mdlInv->cancelDraft($draftNo, $cancelReason, $isRemoveOrder, $outputMsg, $invType);

                    if (!$isCancelDraft) {
                        $this->data["error"] = $outputMsg;
                        echo json_encode($this->data);
                        exit();
                    }
                }

                echo json_encode($this->data);
                exit();
            }

            if ($act == "cancelDraft") {
                $dftNo = $this->input->post('draftNo', TRUE) ? $this->input->post('draftNo', TRUE) : '';
                $reason = $this->input->post('cancelReason', TRUE) ? $this->input->post('cancelReason', TRUE) : '';
                $isRemoveOrder = $this->input->post('removeOrder', TRUE) ? $this->input->post('removeOrder', TRUE) : '0';
                $invType = $this->input->post('invType') ? $this->input->post('invType') : '';

                $outmsg = '';
                $isCancelDraft = $this->mdlInv->cancelDraft($dftNo, $reason, $isRemoveOrder, $outmsg, $invType);

                if (!$isCancelDraft) {
                    $this->data["error"] = $outmsg;
                }

                echo json_encode($this->data);
                exit();
            }

            echo json_encode($this->data);
            exit;
        }

        $this->load->view('header', $this->data);
        $this->data['userIds'] = $this->user->getAllUserId();
        $this->load->view('tools/entries_cancel', $this->data);
        $this->load->view('footer');
    }

    public function tlReprint()
    {
        $access = $this->user->access('tlReprint');
        if ($access === false) {
            show_404();
        }

        if (strlen($access) > 5) {
            $this->data['deny'] = $access;
            echo json_encode($this->data);
            exit;
        }

        $action = $this->input->post('action') ? $this->input->post('action') : '';

        $this->data['title'] = "In lại chứng từ";

        if ($action == 'view') {
            $act = $this->input->post('act') ? $this->input->post('act') : '';

            if ($act == 'load_template') {
                $pinCode = $this->input->post('pinCode') ? $this->input->post('pinCode') : '';
                $ordType = $this->input->post('ordType') ? $this->input->post('ordType') : '';
                if ($ordType == 'NH') {
                    $this->data['templaser'] =  $this->load->view('print_file/print_eir_laser', array('qr_url' => $pinCode), TRUE);
                } else {
                    $this->data['templaser'] = $this->load->view('print_file/print_service_laser', array('qr_url' => $pinCode), TRUE);
                }
                echo json_encode($this->data);
                exit;
            }

            $ordNo = $this->input->post('ordNo') ? $this->input->post('ordNo') : '';
            $cntrNo = $this->input->post('cntrNo') ? $this->input->post('cntrNo') : '';
            $pinCode = $this->input->post('pinCode') ? $this->input->post('pinCode') : '';
            $invNo = $this->input->post('invNo') ? $this->input->post('invNo') : '';
            $ordType = $this->input->post('ordType') ? $this->input->post('ordType') : '';

            $w = array(
                "PinCode" => $pinCode,
                "OrderNo" => $ordNo,
                "CntrNo" => $cntrNo,
                "InvNo" => $invNo,
                "OrderType" => $ordType
            );

            $this->load->model("task_model");
            $this->data["list"] = $this->task_model->getOrder4RePrint($w);
            echo json_encode($this->data);
            exit;
        }

        $this->load->view('header', $this->data);
        $this->load->view('tools/reprint', $this->data);
        $this->load->view('footer');
    }

    public function tlManualInvoice()
    {
        $access = $this->user->access('tlManualInvoice');
        if ($access === false) {
            show_404();
        }

        if (strlen($access) > 5) {
            $this->data['deny'] = $access;
            echo json_encode($this->data);
            exit;
        }

        $this->load->model("Credit_model", "mdlcre");

        $action = $this->input->post('action') ? $this->input->post('action') : '';
        if ($action == "view") {
            $act = $this->input->post('act') ? $this->input->post('act') : '';

            if ($act == 'load_payer') {
                $this->data['payers'] = $this->mdltask->getPayers();
                echo json_encode($this->data);
                exit;
            }

            if ($act == 'search_ship') {
                $arrstt = $this->input->post('arrStatus') ? $this->input->post('arrStatus') : '';
                $year = $this->input->post('shipyear') ? $this->input->post('shipyear') : '';
                $name = $this->input->post('shipname') ? $this->input->post('shipname') : '';

                $this->data['vsls'] = $this->mdlcre->searchShip($arrstt, $year, $name);
                echo json_encode($this->data);
                exit;
            }

            if ($act == 'load_data') {
                $args = $this->input->post('args') ? $this->input->post('args') : [];

                $out = $this->mdlcre->loadContLiftTotal($args);

                $this->data['results'] = $out["DETAIL"];
                $this->data['totals'] = $out["SUM"];
                echo json_encode($this->data);
                exit;
            }

            if ($act == 'load_tariff') {
                $invTemp = $this->input->post('invTemp') ? $this->input->post('invTemp') : '';
                $this->data['results'] = $this->mdltask->loadTariffByTemplate($invTemp);
                echo json_encode($this->data);
                exit;
            }
        }

        if ($action == "save") {
            $args = $this->input->post('args') ? $this->input->post('args') : [];
            $act = $this->input->post('act') ? $this->input->post('act') : '';

            //them moi hd thu sau
            if ($act == 'use_manual_Inv') {
                $data = $this->input->post('useInvData') ? $this->input->post('useInvData') : array();
                $session_inv_info = array();
                if (count($data) > 0) {
                    $useInvData = array(
                        'serial' => trim($data['serial']),
                        'invno' => trim($data['invno'])
                    );

                    $paymentType = trim($data['paymentType']);

                    $checkInvNo = $this->mdlInv->checkInvNo($useInvData['serial'], $useInvData['invno'], $paymentType);
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

                echo true;
                exit;
            }

            //them moi hd thu sau
            $paymentType = $args['draft_total']['PAYMENT_TYPE'];

            //them moi hd thu sau
            if (isset($data["pubType"]) && $data["pubType"] == "m-inv") {
                if (($this->session->userdata("invInfo") === null || count(json_decode($this->session->userdata("invInfo"), true)) == 0)) {
                    $this->data["non_invInfo"] = "Chưa cấu hình hóa đơn!";
                    echo json_encode($this->data);
                    exit();
                } else {
                    $ssInv = json_decode($this->session->userdata("invInfo"), true);
                    if (!isset($ssInv[$paymentType])) {
                        $this->data["non_invInfo"] = "Chưa cấu hình hóa đơn!";
                        echo json_encode($this->data);
                        exit();
                    }

                    $manualInvInfo = $ssInv[$paymentType];
                    $checkInvNo = $this->mdlInv->checkInvNo($manualInvInfo['serial'], $manualInvInfo['invno'], $paymentType);

                    if ($checkInvNo) {
                        $this->data["isDup"] = true;
                        echo json_encode($this->data);
                        exit();
                    }
                }
            }

            $outInfo = [];
            $this->data['message'] = $this->mdlInv->saveDraft_MANUAL($args, $outInfo);

            if (isset($args['invInfo'])) {
                $this->data['invInfo'] = $args['invInfo'];
            } else {
                if (isset($args["pubType"])) {
                    if ($args["pubType"] == "m-inv") {
                        $outInfo['type'] = $paymentType; //them moi hd thu sau
                        $this->data['invInfo'] = $data['invInfo'] = $outInfo;
                        $outInfo = [];
                    } else if ($args["pubType"] == "dft") {
                        $this->data['dftInfo'] = $outInfo;
                    }
                }
            }

            // /create data for send mail
            $mailTo = $this->input->post('mailTo') ? $this->input->post('mailTo') : '';

            if ($mailTo != '' && $args['pubType'] == 'e-inv' && count($args['invInfo']) > 0) {
                $pinCode = (isset($args['invInfo']['fkey']) ? $args['invInfo']['fkey'] : "");
                if ($pinCode != '') {
                    $itemMail = array(
                        "mailTo" => str_replace(';', ',', $mailTo)
                    );

                    if (isset($args['invInfo']) && count($args['invInfo']) > 0) {
                        $itemMail["inv"] = $args['invInfo']['serial'] . $args['invInfo']['invno'];
                    }

                    $itemMail["pinCode"] = $pinCode;
                    $this->funcs->generateQRCode($pinCode);

                    // log_message( "error", $this->sendmail( $itemMail ));m
                    $this->sendmail($itemMail);
                }
            }

            echo json_encode($this->data);
            exit;
        }

        $this->data['title'] = "Tạo hoá đơn tay";

        $this->load->view('header', $this->data);

        $this->data['dmethods'] = $this->mdlcre->getDMethods();
        $this->data['transits'] = $this->mdlcre->getTransits();
        $this->data['invTemps'] = $this->mdlcre->getInvTemp();
        $this->data['cargoTypes'] = $this->mdltask->getCargoTypes();
        $this->data['paymentMethod'] = $this->mdlInv->getPaymentMethod();
        $this->data["cntrClass"] = $this->mdlInv->loadCntrClass();

        $ssInvInfo = json_decode($this->session->userdata("invInfo"), true);

        $this->data["ssInvInfo"] = $ssInvInfo;
        $this->data["isDup"] = false; //them moi hd thu sau

        $this->load->view('tools/manual_inv', $this->data);
        $this->load->view('footer');
    }

    public function tlPaymentComplete()
    {
        $access = $this->user->access('tlManualInvoice');
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
            if ($act == "search_inv") {
                $fromDate = $this->input->post('fromDate') ? $this->funcs->dbDateTime($this->input->post('fromDate', TRUE)) : '';
                $toDate = $this->input->post('toDate') ? $this->funcs->dbDateTime($this->input->post('toDate', TRUE)) : '';
                $tyeOfDate = $this->input->post('typeOfDate') ? $this->input->post('typeOfDate', TRUE) : '';
                $paymentStatus = $this->input->post('paymentStatus') ? $this->input->post('paymentStatus') : array();
                $acccd = $this->input->post('AccCd') ? $this->input->post('AccCd') : array();
                $cntrNo = $this->input->post('cntrNo') ? $this->input->post('cntrNo') : '';
                $invNo = $this->input->post('invNo') ? $this->input->post('invNo') : '';
                $ordNo = $this->input->post('ordNo') ? $this->input->post('ordNo') : '';
                $pinCode = $this->input->post('pinCode') ? $this->input->post('pinCode') : '';
                if ($tyeOfDate == "NH") {
                    $args = array(
                        "eir.PAYMENT_CHK" => $paymentStatus,
                        "IssueDate >=" => $fromDate,
                        "IssueDate <=" => $toDate,
                        "CntrNo" => $cntrNo,
                        "INV_NO" => $invNo,
                        "PinCode" => $pinCode,
                        "EIRNo" => $ordNo,
                        "ACC_CD" => $acccd
                    );
                    $this->data["invs"] = $this->mdlInv->loadEirForPayment($args);
                } else {
                    $args = array(
                        "srv.PAYMENT_CHK" => $paymentStatus,
                        "IssueDate >=" => $fromDate,
                        "IssueDate <=" => $toDate,
                        "CntrNo" => $cntrNo,
                        "INV_NO" => $invNo,
                        "PinCode" => $pinCode,
                        "SSOderNo" => $ordNo,
                        "ACC_CD" => $acccd

                    );
                    $this->data["invs"] = $this->mdlInv->loadSrc_ordForPayment($args);
                }

                echo json_encode($this->data);
                exit;
            }
        }
        if ($action == "save") {
            $act = $this->input->post('act') ? $this->input->post('act') : '';
            if ($act == 'update') {
                $tbl = $this->input->post('tbl') ? $this->input->post('tbl') : '';
                if (!$tbl) return FALSE;
                $dataPayment = $this->input->post('data') ? $this->input->post('data') : '';
                $this->data['result'] = $this->mdlInv->updatePayment($dataPayment, $tbl);
                echo json_encode($this->data);
                exit;
            }
        }
        $this->data['title'] = "Xác nhận thanh toán";
        $this->load->view('header', $this->data);

        $ssInvInfo = json_decode($this->session->userdata("invInfo"), true);

        $this->data["ssInvInfo"] = $ssInvInfo;
        $this->data["isDup"] = false; //them moi hd thu sau
        $this->data['userIds'] = $this->user->getAllUserId();
        $this->data['paymentMethod'] = $this->mdlInv->getPaymentMethod('CAS');

        $this->load->view('tools/payment_complete', $this->data);
        $this->load->view('footer');
    }

    public function tlAdjustInvoice()
    {
        $access = $this->user->access('tlAdjustInvoice');
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

            if ($act == 'search_ship') {
                $arrstt = $this->input->post('arrStatus') ? $this->input->post('arrStatus') : '';
                $year = $this->input->post('shipyear') ? $this->input->post('shipyear') : '';
                $name = $this->input->post('shipname') ? $this->input->post('shipname') : '';

                $this->data['vsls'] = $this->mdltask->searchShip($arrstt, $year, $name);
                echo json_encode($this->data);
                exit;
            }

            if ($act == 'load_data') {
                $args = $this->input->post('args') ? $this->input->post('args') : [];
                $result = $this->mdlInv->loadInvoiceForAdjust($args);
                $this->data['results'] = $result;
                echo json_encode($this->data);
                exit;
            }

            if ($act == 'load_tariff') {
                $invTemp = $this->input->post('invTemp') ? $this->input->post('invTemp') : '';
                $this->data['results'] = $this->mdltask->loadTariffByTemplate($invTemp);
                echo json_encode($this->data);
                exit;
            }
        }

        if ($action == "save") {
            $args = $this->input->post('args') ? $this->input->post('args') : [];
            $act = $this->input->post('act') ? $this->input->post('act') : '';

            if ($act == 'edit_paymentMethod') {
                $this->data['error'] = $this->mdlInv->editInvoicePaymentMethod($args);
                echo json_encode($this->data);
                exit;
            }
            //them moi hd thu sau
            $paymentType = $args['draft_total']['PAYMENT_TYPE'];
            $outInfo = [];
            // update payment_status khi hóa đơn thay thế
            if ($args['draft_total']['AdjustType'] == 3) {
                $this->data['update'] = $this->mdlInv->updateStatusAlterInv($args['draft_total']['AdjustInvNo']);
            }

            $this->data['message'] = $this->mdlInv->saveDraft_MANUAL($args, $outInfo);

            if (isset($args['invInfo'])) {
                $this->data['invInfo'] = $args['invInfo'];
            } else {
                if (isset($args["pubType"])) {
                    if ($args["pubType"] == "m-inv") {
                        $outInfo['type'] = $paymentType; //them moi hd thu sau
                        $this->data['invInfo'] = $data['invInfo'] = $outInfo;
                        $outInfo = [];
                    } else if ($args["pubType"] == "dft") {
                        $this->data['dftInfo'] = $outInfo;
                    }
                }
            }

            // /create data for send mail
            $mailTo = $this->input->post('mailTo') ? $this->input->post('mailTo') : '';

            if ($mailTo != '' && $args['pubType'] == 'e-inv' && count($args['invInfo']) > 0) {
                $pinCode = (isset($args['invInfo']['fkey']) ? $args['invInfo']['fkey'] : "");
                if ($pinCode != '') {
                    $itemMail = array(
                        "mailTo" => str_replace(';', ',', $mailTo)
                    );

                    if (isset($args['invInfo']) && count($args['invInfo']) > 0) {
                        $itemMail["inv"] = $args['invInfo']['serial'] . $args['invInfo']['invno'];
                    }

                    $itemMail["pinCode"] = $pinCode;
                    $this->funcs->generateQRCode($pinCode);

                    // log_message( "error", $this->sendmail( $itemMail ));m
                    $this->sendmail($itemMail);
                }
            }

            echo json_encode($this->data);
            exit;
        }

        $this->data['title'] = "Điều chỉnh / thay thế hóa đơn";

        $this->load->view('header', $this->data);

        $configEInv = $this->config->item($this->config->item('INV_SYS'));
        $this->data['pattern_serials'] = [
            'CAS' => [
                'PATTERN' => $configEInv['INV_PATTERN'],
                'SERIAL' => $configEInv['INV_SERIAL']
            ],
            'CRE' => [
                'PATTERN' => $configEInv['INV_CRE']['INV_PATTERN'],
                'SERIAL' => $configEInv['INV_CRE']['INV_SERIAL']
            ]
        ];

        $this->load->model("Credit_model", "mdlcre");
        $this->data['dmethods'] = $this->mdlcre->getDMethods();
        $this->data['transits'] = $this->mdlcre->getTransits();
        $this->data['invTemps'] = $this->mdlcre->getInvTemp();
        $this->data['cargoTypes'] = $this->mdltask->getCargoTypes();
        $this->data['paymentMethod'] = $this->mdlInv->getPaymentMethod();
        $this->data["cntrClass"] = $this->mdlInv->loadCntrClass();

        $ssInvInfo = json_decode($this->session->userdata("invInfo"), true);

        $this->data["ssInvInfo"] = $ssInvInfo;
        $this->data["isDup"] = false; //them moi hd thu sau

        $this->load->view('tools/adjust_inv', $this->data);
        $this->load->view('footer');
    }

    public function tlViewLogging()
    {
        $access = $this->user->access('tlViewLogging');
        if ($access === false) {
            show_404();
        }

        if (strlen($access) > 5) {
            $this->data['deny'] = $access;
            echo json_encode($this->data);
            exit;
        }

        $action = $this->input->post('action') ? $this->input->post('action') : '';
        $this->data['title'] = "Lịch sử thay đổi dữ liệu";

        if ($action == 'view') {
            $act = $this->input->post('act') ? $this->input->post('act') : '';
            if ($act == 'search_value') {
                $args = $this->input->post('args') ? $this->input->post('args') : array();
                $this->load->model("task_model");
                $this->data["results"] = count($args) == 0 ? array() : $this->mdlRpt->getSysLogging($args);
                echo json_encode($this->data);
                exit;
            }

            if ($act == 'view_detail') {
                $id = $this->input->post('id') ? $this->input->post('id') : '';
                $temp = $this->ceh->select('OldContent, NewContent, ChangedType')->where('ID', $id)->get('SYS_LOG_EVENT')->row_array();
                echo json_encode($temp);
                exit;
            }
        }

        $this->data['userIds'] = $this->mdlRpt->getUserId();
        $this->load->view('header', $this->data);
        $this->load->view('tools/log_viewer', $this->data);
        $this->load->view('footer');
    }

    public function tlInvoice2Oracle()
    {
        $access = $this->user->access('tlInvoice2Oracle');
        if ($access === false) {
            show_404();
        }

        if (strlen($access) > 5) {
            $this->data['deny'] = $access;
            echo json_encode($this->data);
            exit;
        }

        $action = $this->input->post('action') ? $this->input->post('action') : '';
        $this->data['title'] = "Quản lý tích hợp Hoá đơn - HTKT";

        if ($action == 'view') {
            $act = $this->input->post('act') ? $this->input->post('act') : '';
            if ($act == 'search_value') {
                $args = $this->input->post('args') ? $this->input->post('args') : array();
                $this->data["results"] = count($args) == 0 ? array() : $this->mdlHTKT->loadInterfaceInvoice($args);
                echo json_encode($this->data);
                exit;
            }

            if ($act == 'load_payer') {
                $this->data['payers'] = $this->mdlHTKT->getPayers();
                echo json_encode($this->data);
                exit;
            }
        }

        if ($action == 'add') {
            $act = $this->input->post('act') ? $this->input->post('act') : '';
            if ($act == 'send') {
                $data = $this->input->post('data') ? $this->input->post('data') : array();
                $this->data["results"] = $this->mdlHTKT->transferMultipleInvoice($data);

                echo json_encode($this->data);
                exit;
            }
        }

        $this->load->view('header', $this->data);
        $this->load->view('tools/invoice_to_fs', $this->data);
        $this->load->view('footer');
    }

    private function sendmail($args)
    {
        $pinCode = $args["pinCode"];
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
                    <div style="background-color:#f10f0f;border-top-left-radius:4px;border-top-right-radius:4px;padding:30px">
                        <span style="margin-top:20px;font-family:Tahoma;font-size:22px;color:#fff">
                        $yard_name thông báo Phát hành lệnh và Xuất hóa đơn điện tử
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

        $this->email->message($mailContent);

        return $this->email->send() ? 'sent' : $this->email->print_debugger();
    }

    public function downloadManualInvTemp()
    {
        $this->load->library('excel');

        $inputFileName = FCPATH . '/download/hoa-don-tay.xlsx';

        /*check point*/

        $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($inputFileName);

        $objPHPExcel->setActiveSheetIndex(0);

        // set source 
        // $objPHPExcel->getActiveSheet()
        //             ->setCellValue('A10', 'EDITED Last Name')
        //             ->setCellValue('B11', 'EDITED First Name')
        //             ->setCellValue('C12', 'EDITED Age')
        //             ->setCellValue('D13', 'EDITED Sex')
        //             ->setCellValue('E14', 'EDITED Location');


        header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="hoa-don-tay.xlsx"');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }
}
