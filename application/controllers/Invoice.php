<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Invoice extends CI_Controller
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

    public function invPrefix()
    {
        $access = $this->user->access('invPrefix');
        if ($access === false) {
            show_404();
        }

        if (strlen($access) > 5) {
            $this->data['deny'] = $access;
            echo json_encode($this->data);
            exit;
        }

        $action = $this->input->post('action') ? $this->input->post('action') : '';

        $this->data['title'] = "Khai báo quyển hóa đơn";

        if ($action == 'view') {
            $fromDate = $this->input->post('fromDate') ? $this->funcs->dbDateTime($this->input->post('fromDate')) : '';
            $toDate = $this->input->post('toDate') ? $this->funcs->dbDateTime($this->input->post('toDate') . " 23:59:59") : '';

            $this->data["fr"] = $fromDate;
            $this->data["to"] = $toDate;
            $this->data["list"] = $this->mdlInv->loadInvPrefix($fromDate, $toDate);

            $this->data["invInfo"] = $this->session->userdata("invInfo");

            echo json_encode($this->data);
            exit;
        }

        if ($action == 'add' || $action == 'edit') {
            $act = $this->input->post('act') ? $this->input->post('act') : '';
            $data = $this->input->post('data') ? $this->input->post('data') : array();
            $useInvData = $this->input->post('useInvData') ? $this->input->post('useInvData') : array();

            if ($act == 'useInv' && count($useInvData) > 0) {

                $checkInvNo = $this->mdlInv->checkInvNo($useInvData['serial'], $useInvData['invno']);

                if ($checkInvNo) {
                    $this->data["isDup"] = true;
                    echo json_encode($this->data);
                    exit;
                }

                $this->session->set_userdata("invInfo", json_encode($useInvData));
                echo true;
                exit;
            }

            if (count($data) > 0) {
                $this->data['result'] = $this->mdlInv->saveInvPrefix($data);

                if (count($useInvData) > 0) {

                    $checkInvNo = $this->mdlInv->checkInvNo($useInvData['serial'], $useInvData['invno']);

                    if ($checkInvNo) {
                        $this->data["isDup"] = true;
                        echo json_encode($this->data);
                        exit;
                    }

                    $this->session->set_userdata("invInfo", json_encode($useInvData));
                }

                echo json_encode($this->data);
                exit;
            }
        }

        if ($action == 'delete') {
            $delRowguids = $this->input->post('data') ? $this->input->post('data') : array();
            if (count($delRowguids) > 0) {
                $this->data['result'] = $this->mdlInv->deleteInvPrefix($delRowguids);
                echo json_encode($this->data['result']);
                exit();
            }
        }

        $this->load->view('header', $this->data);
        $this->load->view('invoices/inv_prefix', $this->data);
        $this->load->view('footer');
    }

    public function invPublishInvoice()
    {
        $access = $this->user->access('invPublishInvoice');
        if ($access === false) {
            show_404();
        }

        if (strlen($access) > 5) {
            $this->data['deny'] = $access;
            echo json_encode($this->data);
            exit;
        }

        $action = $this->input->post('action') ? $this->input->post('action') : '';

        $this->data['title'] = "Phát hành hóa đơn";

        if ($action == 'view') {
            $act = $this->input->post('act') ? $this->input->post('act') : '';

            if ($act == 'load_payer') {
                $this->data['payers'] = $this->mdlInv->getPayers();
                echo json_encode($this->data);
                exit;
            }

            if ($act == "search_draft") {
                $fromDate = $this->input->post('fromDate') ? $this->funcs->dbDateTime($this->input->post('fromDate')) : '';
                $toDate = $this->input->post('toDate') ? $this->funcs->dbDateTime($this->input->post('toDate') . " 23:59:59") : '';
                $paymentType = $this->input->post('paymentType') ? $this->input->post('paymentType') : '';
                $currency = $this->input->post('currency') ? $this->input->post('currency') : '';
                $createdBy = $this->input->post('createdBy') ? $this->input->post('createdBy') : '';

                $args = array(
                    "FromDate" => $fromDate,
                    "ToDate" => $toDate,
                    "PaymentType" => $paymentType,
                    "CurrencyID" => $currency,
                    "CreatedBy" => $createdBy
                );

                $this->data["drafts"] = $this->mdlInv->loadDraft($args);
                $this->data["draftdetails"] = $this->mdlInv->loadDraftDetails($args);
                echo json_encode($this->data);
                exit;
            }

            if ($act == "send_mail") {
                $args = $this->input->post('args') ? $this->input->post('args') : array();

                $this->data["result"] = $this->sendmail($args);
                echo json_encode($this->data);
                exit;
            }
        }

        if ($action == 'add' || $action == 'edit') {

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

                    //them moi hd thu sau
                    $paymentType = $data['paymentType'];

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

            $saveData = $this->input->post('data') ? $this->input->post('data') : array();
            $paymentType = $saveData['paymentType']; //them moi hd thu sau
            if (isset($saveData["pubType"]) && $saveData["pubType"] == "m-inv") {
                if (($this->session->userdata("invInfo") === null || count(json_decode($this->session->userdata("invInfo"), true)) == 0)) {
                    $this->data["non_invInfo"] = "Chưa cấu hình hóa đơn!";
                    echo json_encode($this->data);
                    exit();
                } else {
                    //them moi hd thu sau
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

                    $saveData["invInfo"]["invno"] = $manualInvInfo['invno'];
                    $saveData["invInfo"]["serial"] = $manualInvInfo['serial'];
                    $saveData["invInfo"]["fkey"] = $this->mdlInv->generatePinCode();
                    //them moi hd thu sau
                }
            }

            if (is_array($saveData) && count($saveData) > 0) {
                //truyền vào biến outInfo để lấy ra các giá trị trong eir: số eir, tên người đại diện, email
                $outInfo = array();
                $result = $this->mdlInv->saveInvoiceVat($saveData, $outInfo);

                if ($result != "success") {
                    $this->data['error'] = $result;
                }

                if (isset($saveData['invInfo'])) {
                    //them moi hd thu sau
                    if ($saveData["pubType"] == "m-inv") {
                        $saveData['invInfo']['type'] = $paymentType;
                    }

                    $this->data['invInfo'] = $saveData['invInfo'];

                    //khng phai hddt 
                    if (!isset($saveData['invInfo']['hddt']) || $saveData['invInfo']['hddt'] != '1') { //them moi hd thu sau
                        $ivdta = $this->mdlInv->getInv4Print($saveData['invInfo']['fkey']);
                        $this->data['type'] = isset($saveData['invInfo']['type']) ? $saveData['invInfo']['type'] : 'CAS'; //them moi hd thu sau

                        if (is_array($ivdta) && count($ivdta) > 0) {
                            $total = floatval($ivdta[0]['TAMOUNT']); //

                            //them moi lam tron so
                            $currency = $ivdta[0]["CURRENCYID"];

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

                            $amountWords = $this->funcs->convert_number_to_words(round($total, $roundNum), $currency); //doc tien usd
                            $this->data['invdata'] = $ivdta;
                            $this->data['amtwords'] = $amountWords;
                            $this->data['formatNum'] = $formatNum; //them moi lam tron so
                            $this->data['formatNumQty_Unit'] = $formatNumQty_Unit; //lam tron so luong+don gia theo yeu cau KT
                        }
                    }
                }

                if (isset($saveData["pubType"]) && $saveData["pubType"] == "m-inv" && $this->session->userdata("invInfo") !== null) {
                    //them moi hd thu sau
                    $ssInv = json_decode($this->session->userdata("invInfo"), true);
                    $manualInvInfo = $ssInv[$paymentType];

                    $this->data["ssInvInfo"] = $manualInvInfo;
                    $this->data["hasDup"] = $this->mdlInv->checkInvNo($manualInvInfo['serial'], $manualInvInfo['invno'], $paymentType);
                }

                $this->data["outInfo"] = $outInfo;
            }

            echo json_encode($this->data);
            exit;
        }

        $this->load->view('header', $this->data);

        if (($this->session->userdata("invInfo") !== null && count(json_decode($this->session->userdata("invInfo"), true)) > 0)) {
            //them moi hd thu sau
            $ssInvInfo = json_decode($this->session->userdata("invInfo"), true);
            $ssInvInfoCas = $ssInvInfo['CAS'];

            $this->data["ssInvInfo"] = $ssInvInfoCas;
            $this->data["isDup"] = $this->mdlInv->checkInvNo($ssInvInfo['serial'], $ssInvInfo['invno']);
        }

        $this->data['paymentMethod'] = $this->mdlInv->getPaymentMethod();
        $yardInfo = array(
            'yardName' => $this->config->item('YARD_NAME'),
            'hotline' => $this->config->item('YARD_HOT_LINE'),
            'vnptPortalUrl' => $this->config->item('VNPT_PORTAL_URL')
        );
        $this->data['inv_noti'] =  $this->load->view('print_file/invoice_notification', $yardInfo, TRUE);
        
        $this->data['userIds'] = $this->user->getAllUserId();
        $this->load->view('invoices/inv_publish', $this->data);
        $this->load->view('footer');
    }

    private function sendmail($args)
    {
        $invNo = $args["inv"];
        $pinCode = $args["pinCode"];
        $orderNo = $args["orderNo"];
        $amount = $args["amount"];
        $reservationCode = $args['reservationCode'];


        $invPrefix = substr($args["inv"], 0, strlen($args["inv"]) - 7);
        $searchUrl = site_url(md5("InvoiceManagement") . '/' . md5("downloadInvPDF")) . "?" . http_build_query(['fkey' => $pinCode, 'inv' => $invNo]);
        $yard_id = $this->config->item('YARD_ID');
        $yard_name = $this->config->item('YARD_FULL_NAME');
        $invSys = $this->config->item('INV_SYS');
        $inv_portal = $this->config->item($invSys)['PORTAL_URL'] ?? '';
        
        $mailContent = <<<EOT
            <body>
            <div style="padding: 40px;">
                <div style="background-color:#f10f0f;border-top-left-radius:4px;border-top-right-radius:4px;padding:30px">
                    <span style="margin-top:20px;font-family:Tahoma;font-size:22px;color:#fff">
                        $yard_name thông báo gửi hóa đơn điện tử cho Quý khách
                    </span>
                </div>
                <div style="border-style:none solid solid;border-width:1px;border-color:#e1e1e1;background-color:#fafafa">
                    <div style="padding:10px 20px 10px 20px;font-family:Tahoma,serif;color:#030303;line-height:26px">
                        <b>Kính gửi: Quý khách hàng</b>
                        <br>
                        <span>$yard_name xin gửi cho Quý khách hóa đơn điện tử với các thông tin như sau: </span>
                    </div>
                    <div style="line-height:30px;background-color:#e1eefb;padding:1px">
                        <ul style="margin-left:25px;list-style:disc">
                            <li>Số lệnh: <b>$orderNo</b></li>
                            <li>Mã tra cứu: <b>$pinCode</b></li>
                            <li>Ký hiệu hóa đơn: <b>$invPrefix</b></li>
                            <li>Số hóa đơn: <b>$invNo</b></li>
                            <li>Số tiền: <b>$amount</b></li>
                        </ul>
                    </div>
                    <div style="padding:10px 20px 10px 20px;font-family:Tahoma,serif;color:#030303;line-height:26px">
                        <br>
                        <br><br>
                        <span>Link tra cứu hoá đơn: <a href="$inv_portal" style="font-weight: bold;" target="_blank" >$inv_portal</a></span>
                        <span> Mã số bí mật : <b>$reservationCode</b></span>
                        <br>
                        <span>Quý khách vui lòng kiểm tra, đối chiếu nội dung ghi trên hóa đơn.</span>
                        <div style="margin-top:60px;margin-bottom:40px">
                            <span>Trân trọng!</span>
                            <br>
                            <span><b>$yard_name</b></span>
                        </div>
                    </div>
                </div>
            </div>
            </body>
EOT;

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

        try {

            $this->email->initialize($config);

            $this->email->from($config['smtp_user'], "$yard_id Mail Center");
            $this->email->to($args["mailTo"]);
            $cc = $this->config->item('SYS_MAIL_CC');
            if ($cc !== NULL) {
                $this->email->cc($cc);
            }

            $this->email->subject('[Thông báo] Phát hành hóa đơn!');

            $this->email->message($mailContent);
            $this->email->send();
            return 'sent';
        } catch (Exception $e) {
            return 'send mail failed!';
        }
    }
}
