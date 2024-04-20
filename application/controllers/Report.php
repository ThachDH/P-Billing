<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Report extends CI_Controller
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

    public function rptRevenue()
    {
        $access = $this->user->access('rptRevenue');
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
            $args = $this->input->post('args') ? $this->input->post('args') : [];
            if (count($args) == 0) {
                $this->data['results'] = [];
                echo json_encode($this->data);
                exit;
            }

            $this->data['results'] = $this->mdlRpt->rptRevenue($args);
            echo json_encode($this->data);
            exit;
        }

        $this->data['title'] = "Tổng hợp doanh thu";

        $this->load->view('header', $this->data);
        $this->load->view('report/revenue', $this->data);
        $this->load->view('footer');
    }

    public function rptReleasedInv()
    {
        $access = $this->user->access('rptReleasedInv');
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
            $fromdate = $this->input->post('fromdate') ? $this->input->post('fromdate') : '';
            $todate = $this->input->post('todate') ? $this->input->post('todate') : '';
            $jmode = $this->input->post('jmode') ? $this->input->post('jmode') : '*';
            $paymentType = $this->input->post('paymentType') ? $this->input->post('paymentType') : '*';
            $currency = $this->input->post('currency') ? $this->input->post('currency') : '*';
            $adjustType = $this->input->post('adjust_type');
            $sys = $this->input->post('sys', TRUE);

            $this->data['results'] = $this->mdlRpt->rptReleasedInv($fromdate, $todate, $jmode, $paymentType, $currency, $sys, $adjustType);
            echo json_encode($this->data);
            exit;
        }

        $this->data['title'] = "Báo cáo phát hành hóa đơn";

        $this->load->view('header', $this->data);
        $this->load->view('report/releasedInv', $this->data);
        $this->load->view('footer');
    }

    public function rptRevenueByInvoices()
    {
        $access = $this->user->access('rptRevenueByInvoices');
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

                $this->data['vsls'] = $this->mdlRpt->searchShip($arrstt, $year, $name);
                echo json_encode($this->data);
                exit;
            }
            if ($act == 'load_payer') {
                $this->data['payers'] = $this->mdlRpt->getPayers();
                echo json_encode($this->data);
                exit;
            }

            $args = [
                "fromDate" => $this->input->post('fromDate') ? $this->input->post('fromDate') : '',
                "toDate" => $this->input->post('toDate') ? $this->input->post('toDate') : '',
                "shipKey" => $this->input->post('shipKey') ? $this->input->post('shipKey') : '',
                "cusID" => $this->input->post('cusID') ? $this->input->post('cusID') : '',
                "createdBy" => $this->input->post('createdBy') ? $this->input->post('createdBy') : '',
                "currencyId" => $this->input->post('currencyId') ? $this->input->post('currencyId') : '',
                "payment_type" => $this->input->post('payment_type') ? $this->input->post('payment_type') : '',
                "adjust_type" => $this->input->post('adjust_type'),
                "isDFT_to_INV" => $this->input->post('isDFT_to_INV'),
                "sys" => $this->input->post('sys') ? $this->input->post('sys') : ''
            ];

            $this->data['results'] = $this->mdlRpt->rptRevenueByInvoices($args);
            echo json_encode($this->data);
            exit;
        }

        $this->data['title'] = "Báo cáo doanh thu hoá đơn thu ngay";

        $this->load->view('header', $this->data);
        $this->data['userIds'] = $this->mdlRpt->getUserId();
        $this->data['paymentMethods'] = $this->mdlRpt->getPaymentMethod();
        $this->load->view('report/revenue_byInvoices', $this->data);
        $this->load->view('footer');
    }

    public function rptCreditByInvoices()
    {
        $access = $this->user->access('rptCreditByInvoices');
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

                $this->data['vsls'] = $this->mdlRpt->searchShip($arrstt, $year, $name);
                echo json_encode($this->data);
                exit;
            }
            if ($act == 'load_payer') {
                $this->data['payers'] = $this->mdlRpt->getPayers();
                echo json_encode($this->data);
                exit;
            }

            $args = [
                "fromDate" => $this->input->post('fromDate') ? $this->input->post('fromDate') : '',
                "toDate" => $this->input->post('toDate') ? $this->input->post('toDate') : '',
                "shipKey" => $this->input->post('shipKey') ? $this->input->post('shipKey') : '',
                "createdBy" => $this->input->post('createdBy') ? $this->input->post('createdBy') : '',
                "currencyId" => $this->input->post('currencyId') ? $this->input->post('currencyId') : '',
                "payment_type" => $this->input->post('payment_type') ? $this->input->post('payment_type') : '',
                "adjust_type" => $this->input->post('adjust_type'),
                "sys" => $this->input->post('sys') ? $this->input->post('sys') : ''
            ];

            $this->data['results'] = $this->mdlRpt->rptCreditByInvoices($args);
            echo json_encode($this->data);
            exit;
        }

        $this->data['title'] = "Báo cáo doanh thu hoá đơn thu sau";

        $this->load->view('header', $this->data);
        $this->data['userIds'] = $this->mdlRpt->getUserId();
        $this->data['paymentMethods'] = $this->mdlRpt->getPaymentMethod('CRE');
        $this->load->view('report/credit_byInvoices', $this->data);
        $this->load->view('footer');
    }

    public function rptCreditRevenueInv()
    {
        $access = $this->user->access('rptReleasedInv');
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

                $this->data['vsls'] = $this->mdlRpt->searchShip($arrstt, $year, $name);
                echo json_encode($this->data);
                exit;
            }

            $args = [
                "fromDate" => $this->input->post('fromDate') ? $this->input->post('fromDate') : '',
                "toDate" => $this->input->post('toDate') ? $this->input->post('toDate') : '',
                "shipKey" => $this->input->post('shipKey') ? $this->input->post('shipKey') : '',
                "currencyId" => $this->input->post('currencyId') ? $this->input->post('currencyId') : '',
                "publishBy" => $this->input->post('publishBy') ? $this->input->post('publishBy') : '',
            ];

            $this->data['results'] = $this->mdlRpt->rptCreditRevenueInv($args);
            echo json_encode($this->data);
            exit;
        }

        $this->data['title'] = "Báo cáo hóa đơn thu sau";

        $this->load->view('header', $this->data);
        $this->load->view('report/credit_revenue_invoice', $this->data);
        $this->load->view('footer');
    }

    public function rptCancelInv()
    {
        $access = $this->user->access('rptCancelInv');
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

            $args = [
                "fromDate" => $this->input->post('fromDate') ? $this->input->post('fromDate') : '', "toDate" => $this->input->post('toDate') ? $this->input->post('toDate') : '', "paymentType" => $this->input->post('paymentType'), "sys" => $this->input->post('sys')
            ];

            $this->data['results'] = $this->mdlRpt->rptCancelInvoices($args);
            echo json_encode($this->data);
            exit;
        }

        $this->data['title'] = "Thống kê hoá đơn huỷ";

        $this->load->view('header', $this->data);
        $this->data['userIds'] = $this->mdlRpt->getUserId();
        $this->load->view('report/inv_cancel_summary', $this->data);
        $this->load->view('footer');
    }

    public function rptDraftWithoutInv()
    {
        $access = $this->user->access('rptDraftWithoutInv');
        if ($access === false) {
            show_404();
        }

        if (strlen($access) > 5) {
            $this->data['deny'] = $access;
            echo json_encode($this->data);
            exit;
        }

        $action = $this->input->post('action') ? $this->input->post('action') : '';
        if ($action == 'view') {
            $act = $this->input->post('act') ? $this->input->post('act') : '';

            if ($act == 'load_payer') {
                $this->data['payers'] = $this->mdlRpt->getPayers();
                echo json_encode($this->data);
                exit;
            }

            if ($act == "search_draft") {
                $fromDate = $this->input->post('fromDate') ? $this->funcs->dbDateTime($this->input->post('fromDate')) : '';
                $toDate = $this->input->post('toDate') ? $this->funcs->dbDateTime($this->input->post('toDate')) : '';
                $paymentType = $this->input->post('paymentType') ? $this->input->post('paymentType') : array();
                $currency = $this->input->post('currency') ? $this->input->post('currency') : '';
                $paymentStatus = $this->input->post('paymentStatus') ? $this->input->post('paymentStatus') : array();
                $isManualInv = $this->input->post('isManualInv') ? $this->input->post('isManualInv') : array();
                $cusID = $this->input->post('cusID') ? $this->input->post('cusID') : '';
                $paymentFor = $this->input->post('paymentFor') ? $this->input->post('paymentFor') : '';
                $byCancel = $this->input->post('byCancel');
                $userId = $this->input->post('userId') ? $this->input->post('userId') : '';

                $args = array(
                    "FromDate" => $fromDate,
                    "ToDate" => $toDate,
                    "PaymentType" => $paymentType,
                    "CurrencyID" => $currency,
                    "paymentStatus" => $paymentStatus,
                    "isManualInv" => $isManualInv,
                    "PAYER" => $cusID,
                    "PaymentFor" => $paymentFor,
                    "ByCancel" => $byCancel,
                    "UserId" => $userId
                );

                $this->data["draftdetails"] = $this->mdlRpt->loadDraftDetails($args);
                echo json_encode($this->data);
                exit;
            }
        }

        $this->data['title'] = "Danh sách phiếu tính cước";

        $this->load->view('header', $this->data);
        $this->data['userIds'] = $this->user->getAllUserId();
        $this->load->view('report/draft_without_inv', $this->data);
        $this->load->view('footer');
    }
	
	public function export_revenue()
    {
        $datajson = $this->input->post('exportdata') ? $this->input->post('exportdata') : '';
        $fromdate = $this->input->post('fromdate') ? $this->input->post('fromdate') : '';
        $todate = $this->input->post('todate') ? $this->input->post('todate') : '';
        $cjmodeName = $this->input->post('cjmodeName') ? $this->input->post('cjmodeName') : '';
        $paymentType = $this->input->post('paymentType') ? $this->input->post('paymentType') : '';
        $currency = $this->input->post('currency') ? $this->input->post('currency') : '';
        $args = json_decode($datajson, true);

        $this->load->library('excel');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, "Excel2007");
        ob_end_clean();

        $this->excel->getDefaultStyle()->getFont()->setName('Times New Roman');
        $this->excel->getDefaultStyle()->getFont()->setSize(11);

        $objSheet0 = $this->excel->getActiveSheet();
        $objSheet0->getSheetView()->setZoomScale(90);

        //thong tin cang
        $fullName = $this->config->item('YARD_FULL_NAME');
        $hotLine = $this->config->item('YARD_HOT_LINE');
        $fax = $this->config->item('YARD_FAX');
        $address = $this->config->item('YARD_ADDRESS');
        $telAndFax = (!empty($hotLine) ? ("Tel: " . $hotLine) : "") . (!empty($fax) ? (" - Fax: " . $fax) : "");

        $objSheet0->getCell('E1')->setValue(mb_strtoupper($fullName));
        $objSheet0->getStyle('E1')->applyFromArray(array(
            'alignment' => [
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ],
            'font' => [
                'bold' => true,
                'size' => 12
            ]
        ));

        $objSheet0->getCell('E2')->setValue($address);
        $objSheet0->getStyle('E2')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));

        $objSheet0->getCell('E3')->setValue($telAndFax);
        $objSheet0->getStyle('E3')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));
        $objSheet0->getStyle("D3:F3")->applyFromArray(array(
            'borders' => array('bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
        ));

        $logoPath = FCPATH . "assets/img/logos/logo.jpg";
        if (file_exists($logoPath)) {
            $objDrawing = new PHPExcel_Worksheet_Drawing();
            $objDrawing->setPath($logoPath);
            $objDrawing->setCoordinates('B1');

            // set resize to false first
            $objDrawing->setResizeProportional(false);
            // set width later
            $objDrawing->setWidth(170);
            $objDrawing->setOffsetY(4);
            $objDrawing->setHeight(75);

            $objDrawing->setWorksheet($objSheet0);
        }

        //name of report
        $objSheet0->mergeCells('B5:I5');
        $objSheet0->getStyle('B5:I5')->getFont()->setBold(true)->setSize(20);
        $objSheet0->getStyle('B5')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));
        $objSheet0->getCell('B5')->setValue("TỔNG HỢP DOANH THU");
        $objSheet0->getRowDimension('5')->setRowHeight(38);
        $objSheet0->getStyle('B')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));

        //tu ngay, den ngay
        $objSheet0->getCell('C6')->setValue("Từ ngày");
        $objSheet0->getCell('D6')->setValue($fromdate);
        $objSheet0->getCell('E6')->setValue("Đến ngày");
        $objSheet0->getCell('F6')->setValue($todate);
        //them thong tin chung
        $objSheet0->getCell('C7')->setValue("Tác nghiệp");
        $objSheet0->getCell('D7')->setValue($cjmodeName);
        $objSheet0->getCell('E7')->setValue("Loại tiền");
        $objSheet0->getCell('F7')->setValue($currency);
        $objSheet0->getCell('C8')->setValue("Loại thanh toán");
        $objSheet0->getCell('D8')->setValue($paymentType);
        //dinh dang thong tin chung
        $objSheet0->getStyle('C6:C8')->applyFromArray(array(
            'alignment' => [
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ],
            'font' => ['bold' => true]
        ));
        $objSheet0->getStyle('D6:D8')->applyFromArray(array(
            'alignment' => [
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ]
        ));
        $objSheet0->getStyle('E6:E8')->applyFromArray(array(
            'alignment' => [
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ],
            'font' => ['bold' => true]
        ));
        $objSheet0->getStyle('F6:F8')->applyFromArray(array(
            'alignment' => [
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ]
        ));
        $objSheet0->getRowDimension('9')->setRowHeight(8);


        //sheet name
        $objSheet0->setTitle('TONG HOP DOANH THU');
        //
        // row header
        $objSheet0->getStyle('B10:I12')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 'wrap' => true
        ));

        $objSheet0->getStyle('B10:I12')->getFont()->setBold(true)->setSize(13);
        $objSheet0->getStyle('B10:I12')->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'A6A6A6'))));

        $objSheet0->mergeCells("B10:B11");
        $objSheet0->getCell('B10')->setValue('STT');
        $objSheet0->getCell('B12')->setValue('(1)');
        $objSheet0->mergeCells("C10:C11");
        $objSheet0->getCell('C10')->setValue('DIỄN GIẢI');
        $objSheet0->getCell('C12')->setValue('(2)');

        $objSheet0->mergeCells("D10:F10");
        $objSheet0->getCell('D10')->setValue('LOẠI CONTAINER');
        $objSheet0->getCell('D11')->setValue(strval(20));
        $objSheet0->getCell('D12')->setValue('(3)');
        $objSheet0->getCell('E11')->setValue(strval(40));
        $objSheet0->getCell('E12')->setValue('(4)');
        $objSheet0->getCell('F11')->setValue(strval(45));
        $objSheet0->getCell('F12')->setValue('(5)');

        $objSheet0->mergeCells("G10:G11");
        $objSheet0->getCell('G10')->setValue('THÀNH TIỀN');
        $objSheet0->getCell('G12')->setValue('(6)');
        $objSheet0->mergeCells("H10:H11");
        $objSheet0->getCell('H10')->setValue('TIỀN THUẾ');
        $objSheet0->getCell('H12')->setValue('(7)');
        $objSheet0->mergeCells("I10:I11");
        $objSheet0->getCell('I10')->setValue('TỔNG TIỀN');
        $objSheet0->getCell('I12')->setValue('(8)');

        $objSheet0->getRowDimension('10')->setRowHeight(32);
        $objSheet0->getRowDimension('11')->setRowHeight(32);
        $objSheet0->getRowDimension('12')->setRowHeight(18);

        $a = 12;
        $startRow = 13;
        $j = 0;
        if ($args === null) goto xxx;
        // goto xxx;
        $sumAmt = 0;
        $sumVat = 0;
        $sumTamt = 0;
        $sum20 = 0;
        $sum40 = 0;
        $sum45 = 0;
        foreach ($args as $arg) {
            $a++;
            $j++;
            $objSheet0->getCell('B' . $a)->setValue($j);
            $objSheet0->getCell('C' . $a)->setValue($arg['TRF_CODE'] . ' - ' . $arg['TRF_DESC']);
            $objSheet0->getCell('D' . $a)->setValue($arg['20']);
            $objSheet0->getCell('E' . $a)->setValue($arg['40']);
            $objSheet0->getCell('F' . $a)->setValue($arg['45']);
            $objSheet0->getCell('G' . $a)->setValue($arg['SUMAMOUNT']);
            $objSheet0->getCell('H' . $a)->setValue($arg['SUMVAT']);
            $objSheet0->getCell('I' . $a)->setValue(strval($arg['SUMTAMOUNT']));
            $objSheet0->getRowDimension($a)->setRowHeight(-1);
            $sumAmt += $arg['SUMAMOUNT'];
            $sumVat += $arg['SUMVAT'];
            $sumTamt += $arg['SUMTAMOUNT'];
            $sum20 += $arg['20'];
            $sum40 += $arg['40'];
            $sum45 += $arg['45'];
        }

        xxx:
        //for total row
        $endRow = $a;
        $totalRow = $a + 1;
        // $objSheet0->mergeCells("B$totalRow:I$totalRow");
        $objSheet0->getCell("C$totalRow")->setValue('TỔNG CỘNG');
        $objSheet0->getStyle("C$totalRow")->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));
        $objSheet0->getCell('D' . $totalRow)->setValue($sum20);
        $objSheet0->getCell('E' . $totalRow)->setValue($sum40);
        $objSheet0->getCell('F' . $totalRow)->setValue($sum45);
        $objSheet0->getCell('G' . $totalRow)->setValue($sumAmt);
        $objSheet0->getCell('H' . $totalRow)->setValue($sumVat);
        $objSheet0->getCell('I' . $totalRow)->setValue($sumTamt);

        $objSheet0->getRowDimension($totalRow)->setRowHeight(26);
        $objSheet0->getStyle("B$totalRow:I$totalRow")->applyFromArray(
            array(
                'font' => array('size' => 12, 'bold' => true, 'color' => array('rgb' => 'ff0000')),
            )
        );
        //for total row
        //vertical tat ca cac dong
        $objSheet0->getStyle("B$startRow:I$totalRow")->getAlignment()->applyFromArray(array(
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 'wrap' => true
        ));

        //canh trai
        $objSheet0->getStyle("C$startRow:C$endRow")->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
        ));

        //dinh dang so
        $formatCurrency = $currency == 'VND' ? '_(* #,##0_);_(* (#,##0);_(* ""_);_(@_)' : '_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)';
        $objSheet0->getStyle("D$startRow:I$totalRow")->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
        ));
        $objSheet0->getStyle("D$startRow:F$totalRow")->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* ""_);_(@_)');
        $objSheet0->getStyle("G$startRow:I$totalRow")->getNumberFormat()->setFormatCode($formatCurrency);

        //them border cho bang detail
        $objSheet0->getStyle("B10:I$totalRow")->applyFromArray(array(
            'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
        ));

        //chu ky
        $s1 = $totalRow + 2;
        $objSheet0->getCell("H$s1")->setValue("TP. Hồ Chí Minh, ngày " . date('d') . " tháng " . date('m') . " năm " . date('Y') . "");

        $s2 = $s1 + 1;
        $objSheet0->getStyle("C$s2")->getFont()->setBold(true);
        $objSheet0->getCell("C$s2")->setValue('Người giao');
        $objSheet0->getStyle("H$s2")->getFont()->setBold(true);
        $objSheet0->getCell("H$s2")->setValue('Người nhận');
        $objSheet0->getStyle("C$s1:H$s2")->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));

        $objSheet0->getColumnDimension('A')->setWidth(2);
        $objSheet0->getColumnDimension('B')->setWidth(8);
        $objSheet0->getColumnDimension('C')->setWidth(58);
        $objSheet0->getColumnDimension('D')->setWidth(16);
        $objSheet0->getColumnDimension('E')->setWidth(16);
        $objSheet0->getColumnDimension('F')->setWidth(16);
        $objSheet0->getColumnDimension('G')->setWidth(22);
        $objSheet0->getColumnDimension('H')->setWidth(22);
        $objSheet0->getColumnDimension('I')->setWidth(22);
        $objSheet0->setShowGridlines(false);

        header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="tong-hop-doanh-thu.xlsx"');
        $objWriter->save('php://output');
    }

    public function export_revenue_inv()
    {
        $datajson = $this->input->post('exportdata') ? $this->input->post('exportdata') : '';
        $fromdate = $this->input->post('fromDate') ? $this->input->post('fromDate') : '';
        $todate = $this->input->post('toDate') ? $this->input->post('toDate') : '';
        $args = json_decode($datajson, true);

        $this->load->library('excel');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, "Excel2007");
        ob_end_clean();

        $this->excel->getDefaultStyle()->getFont()->setName('Times New Roman');
        $this->excel->getDefaultStyle()->getFont()->setSize(11);

        $objSheet0 = $this->excel->getActiveSheet();
        $objSheet0->getSheetView()->setZoomScale(85);

        //thong tin cang
        $fullName = $this->config->item('YARD_FULL_NAME');
        $hotLine = $this->config->item('YARD_HOT_LINE');
        $fax = $this->config->item('YARD_FAX');
        $address = $this->config->item('YARD_ADDRESS');
        $telAndFax = (!empty($hotLine) ? ("Tel: " . $hotLine) : "") . (!empty($fax) ? (" - Fax: " . $fax) : "");

        $objSheet0->getCell('H1')->setValue(mb_strtoupper($fullName));
        $objSheet0->getStyle('H1')->applyFromArray(array(
            'alignment' => [
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ],
            'font' => [
                'bold' => true,
                'size' => 12
            ]
        ));

        $objSheet0->getCell('H2')->setValue($address);
        $objSheet0->getStyle('H2')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));

        $objSheet0->getCell('H3')->setValue($telAndFax);
        $objSheet0->getStyle('H3')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));
        $objSheet0->getStyle("G3:I3")->applyFromArray(array(
            'borders' => array('bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
        ));

        $logoPath = FCPATH . "assets/img/logos/logo.jpg";
        if (file_exists($logoPath)) {
            $objDrawing = new PHPExcel_Worksheet_Drawing();
            $objDrawing->setPath($logoPath);
            $objDrawing->setCoordinates('B1');

            // set resize to false first
            $objDrawing->setResizeProportional(false);
            // set width later
            $objDrawing->setWidth(170);
            $objDrawing->setOffsetY(4);
            $objDrawing->setHeight(75);

            $objDrawing->setWorksheet($objSheet0);
        }

        //name of report
        $objSheet0->mergeCells('B5:N5');
        $objSheet0->getStyle('B5:N5')->getFont()->setBold(true)->setSize(20);
        $objSheet0->getStyle('B5')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));
        $objSheet0->getCell('B5')->setValue("DOANH THU HÓA ĐƠN THU NGAY");
        $objSheet0->getRowDimension('5')->setRowHeight(31);
        $objSheet0->getStyle('B')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));

        //tu ngay, den ngay
        $objSheet0->getCell('F6')->setValue("Từ Ngày");
        $objSheet0->getStyle('F6')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));

        $objSheet0->getCell('G6')->setValue($fromdate);
        $objSheet0->getStyle('G6')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'indent' => 3
        ));

        $objSheet0->getCell('I6')->setValue("Đến Ngày");
        $objSheet0->getStyle('I6')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));

        $objSheet0->getCell('J6')->setValue($todate);
        $objSheet0->getStyle('J6')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));


        //      sheet name
        $objSheet0->setTitle('DT HOA DON THU NGAY');
        //
        // row header
        $objSheet0->getStyle('B8:N9')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 'wrap' => true
        ));

        $objSheet0->getStyle('B8:N9')->getFont()->setBold(true)->setSize(13);
        $objSheet0->getStyle('B8:N9')->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'A6A6A6'))));

        $objSheet0->getCell('B8')->setValue('STT');
        $objSheet0->getCell('B9')->setValue('(1)');
        $objSheet0->getCell('C8')->setValue('Ngày hóa đơn GTGT');
        $objSheet0->getCell('C9')->setValue('(2)');
        $objSheet0->getCell('D8')->setValue('Ký hiệu hóa đơn');
        $objSheet0->getCell('D9')->setValue('(3)');
        $objSheet0->getCell('E8')->setValue('Số hóa đơn GTGT');
        $objSheet0->getCell('E9')->setValue('(4)');
        $objSheet0->getCell('F8')->setValue('Số phiếu tính cước');
        $objSheet0->getCell('F9')->setValue('(5)');
        $objSheet0->getCell('G8')->setValue('Số lệnh');
        $objSheet0->getCell('G9')->setValue('(6)');
        $objSheet0->getCell('H8')->setValue('Tên khách hàng');
        $objSheet0->getCell('H9')->setValue('(7)');
        $objSheet0->getCell('I8')->setValue('Mã số thuế');
        $objSheet0->getCell('I9')->setValue('(8)');
        $objSheet0->getCell('J8')->setValue('Diễn giải dịch vụ');
        $objSheet0->getCell('J9')->setValue('(9)');
        $objSheet0->getCell('K8')->setValue('Thành tiền');
        $objSheet0->getCell('K9')->setValue('(10)');
        $objSheet0->getCell('L8')->setValue('Thuế VAT');
        $objSheet0->getCell('L9')->setValue('(11)');
        $objSheet0->getCell('M8')->setValue('Tổng tiền');
        $objSheet0->getCell('M9')->setValue('(12)=(10)+(11)');
        $objSheet0->getCell('N8')->setValue('Loại hóa đơn');
        $objSheet0->getCell('N9')->setValue('(13)');

        $objSheet0->getRowDimension('8')->setRowHeight(52);
        $objSheet0->getRowDimension('9')->setRowHeight(18);

        $a = 9;
        $startRow = 10;
        $j = 0;
        if ($args === null) goto xxx;
        $invPattern = $this->config->item('INV_PATTERN');
        $sumAmt = 0;
        $sumVat = 0;
        $sumTamt = 0;
        foreach ($args as $arg) {
            $adjustDesc = '';
            switch ($arg['AdjustType']) {
                case 1:
                    $adjustDesc = 'Thay thế';
                    break;
                case 2:
                    $adjustDesc = 'Điều chỉnh tăng';
                    break;
                case 3:
                    $adjustDesc = 'Điều chỉnh giảm';
                    break;
                case 4:
                    $adjustDesc = 'Điều chỉnh thông tin';
                    break;
                default:
                    $adjustDesc = 'HĐ gốc';
                    break;
            }

            $a++;
            $j++;
            $objSheet0->getCell('B' . $a)->setValue($j);
            $objSheet0->getCell('C' . $a)->setValue(date('Y-m-d H:i', strtotime($arg['INV_DATE'])));
            $objSheet0->getCell('D' . $a)->setValue($invPattern);
            $objSheet0->getCell('E' . $a)->setValue($arg['INV_NO']);
            $objSheet0->getCell('F' . $a)->setValue($arg['DRAFT_INV_NO']);
            $objSheet0->getCell('G' . $a)->setValue($arg['REF_NO']);
            $objSheet0->getCell('H' . $a)->setValue($arg['CusName']);
            $objSheet0->getCell('I' . $a)->setValue(strval($arg['VAT_CD']));
            $objSheet0->getCell('J' . $a)->setValue($arg['TRF_STD_DESC']);
            $objSheet0->getCell('K' . $a)->setValue($arg['AMOUNT']);
            $objSheet0->getCell('L' . $a)->setValue($arg['VAT']);
            $objSheet0->getCell('M' . $a)->setValue($arg['TAMOUNT']);
            $objSheet0->getCell('N' . $a)->setValue($adjustDesc);
            $objSheet0->getRowDimension($a)->setRowHeight(-1);
            $sumAmt += $arg['AMOUNT'];
            $sumVat += $arg['VAT'];
            $sumTamt += $arg['TAMOUNT'];
        }

        xxx:
        //for total row
        $endRow = $a;
        $totalRow = $a + 1;
        $objSheet0->mergeCells("B$totalRow:J$totalRow");
        $objSheet0->getCell("H$totalRow")->setValue('TỔNG CỘNG');

        // $objSheet0->getCell('K' . $totalRow)->setValue("=SUM(K$startRow:K$endRow)");
        // $objSheet0->getCell('L' . $totalRow)->setValue("=SUM(L$startRow:L$endRow)");
        // $objSheet0->getCell('M' . $totalRow)->setValue("=SUM(M$startRow:M$endRow)");
        $objSheet0->getCell('K' . $totalRow)->setValue($sumAmt);
        $objSheet0->getCell('L' . $totalRow)->setValue($sumVat);
        $objSheet0->getCell('M' . $totalRow)->setValue($sumTamt);

        $objSheet0->getRowDimension($totalRow)->setRowHeight(26);
        $objSheet0->getStyle("B$totalRow:N$totalRow")->applyFromArray(
            array(
                'font' => array('size' => 13, 'bold' => true, 'color' => array('rgb' => 'ff0000')),
            )
        );

        //for total row
        //vertical tat ca cac dong
        $objSheet0->getStyle("B$startRow:N$totalRow")->getAlignment()->applyFromArray(array(
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 'wrap' => true
        ));

        //canh trai
        $objSheet0->getStyle("I$startRow:I$endRow")->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
        ));
        //canh giua
        $objSheet0->getStyle("B$startRow:G$endRow")->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
        ));

        //dinh dang so
        $objSheet0->getStyle("K$startRow:M$totalRow")->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
        ));
        $objSheet0->getStyle("K$startRow:M$totalRow")->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* ""_);_(@_)');

        //them border cho bang detail
        $objSheet0->getStyle("B8:N$totalRow")->applyFromArray(array(
            'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
        ));

        //chu ky
        $s1 = $totalRow + 2;
        $objSheet0->getCell("L$s1")->setValue("TP. Hồ Chí Minh, ngày " . date('d') . " tháng " . date('m') . " năm " . date('Y') . "");

        $s2 = $s1 + 1;
        $objSheet0->getStyle("E$s2")->getFont()->setBold(true);
        $objSheet0->getCell("E$s2")->setValue('Người giao');
        $objSheet0->getStyle("L$s2")->getFont()->setBold(true);
        $objSheet0->getCell("L$s2")->setValue('Người nhận');
        $objSheet0->getStyle("E$s1:L$s2")->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));

        $objSheet0->getColumnDimension('A')->setWidth(2);
        $objSheet0->getColumnDimension('B')->setWidth(8);
        $objSheet0->getColumnDimension('C')->setWidth(16);
        $objSheet0->getColumnDimension('D')->setWidth(10);
        $objSheet0->getColumnDimension('E')->setWidth(16);
        $objSheet0->getColumnDimension('F')->setWidth(17);
        $objSheet0->getColumnDimension('G')->setWidth(13);
        $objSheet0->getColumnDimension('H')->setWidth(29);
        $objSheet0->getColumnDimension('I')->setWidth(13);
        $objSheet0->getColumnDimension('J')->setWidth(23);
        $objSheet0->getColumnDimension('K')->setWidth(16);
        $objSheet0->getColumnDimension('L')->setWidth(14);
        $objSheet0->getColumnDimension('M')->setWidth(17);
        $objSheet0->getColumnDimension('N')->setWidth(20);
        $objSheet0->setShowGridlines(false);

        header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="doanh-thu-hoa-don-thu-ngay.xlsx"');
        $objWriter->save('php://output');
    }

    public function export_credit()
    {
        $datajson = $this->input->post('exportdata') ? $this->input->post('exportdata') : '';
        $fromdate = $this->input->post('fromDate') ? $this->input->post('fromDate') : '';
        $todate = $this->input->post('toDate') ? $this->input->post('toDate') : '';
		$currencyid = $this->input->post('currencyid') ? $this->input->post('currencyid') : '';
        $args = json_decode($datajson, true);

        $this->load->library('excel');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, "Excel2007");
        ob_end_clean();

        $this->excel->getDefaultStyle()->getFont()->setName('Times New Roman');
        $this->excel->getDefaultStyle()->getFont()->setSize(11);

        $objSheet0 = $this->excel->getActiveSheet();
        $objSheet0->getSheetView()->setZoomScale(85);

        //thong tin cang
        $fullName = $this->config->item('YARD_FULL_NAME');
        $hotLine = $this->config->item('YARD_HOT_LINE');
        $fax = $this->config->item('YARD_FAX');
        $address = $this->config->item('YARD_ADDRESS');
        $telAndFax = (!empty($hotLine) ? ("Tel: " . $hotLine) : "") . (!empty($fax) ? (" - Fax: " . $fax) : "");

        $objSheet0->getCell('H1')->setValue(mb_strtoupper($fullName));
        $objSheet0->getStyle('H1')->applyFromArray(array(
            'alignment' => [
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ],
            'font' => [
                'bold' => true,
                'size' => 12
            ]
        ));

        $objSheet0->getCell('H2')->setValue($address);
        $objSheet0->getStyle('H2')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));

        $objSheet0->getCell('H3')->setValue($telAndFax);
        $objSheet0->getStyle('H3')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));
        $objSheet0->getStyle("G3:I3")->applyFromArray(array(
            'borders' => array('bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
        ));

        $logoPath = FCPATH . "assets/img/logos/logo.jpg";
        if (file_exists($logoPath)) {
            $objDrawing = new PHPExcel_Worksheet_Drawing();
            $objDrawing->setPath($logoPath);
            $objDrawing->setCoordinates('B1');

            // set resize to false first
            $objDrawing->setResizeProportional(false);
            // set width later
            $objDrawing->setWidth(170);
            $objDrawing->setOffsetY(4);
            $objDrawing->setHeight(75);

            $objDrawing->setWorksheet($objSheet0);
        }

        //name of report
        $objSheet0->mergeCells('B5:N5');
        $objSheet0->getStyle('B5:N5')->getFont()->setBold(true)->setSize(20);
        $objSheet0->getStyle('B5')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));
        $objSheet0->getCell('B5')->setValue("DOANH THU HÓA ĐƠN THU SAU");
        $objSheet0->getRowDimension('5')->setRowHeight(31);
        $objSheet0->getStyle('B')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));

        //tu ngay, den ngay
        $objSheet0->getCell('F6')->setValue("Từ Ngày");
        $objSheet0->getStyle('F6')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));

        $objSheet0->getCell('G6')->setValue($fromdate);
        $objSheet0->getStyle('G6')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'indent' => 3
        ));

        $objSheet0->getCell('I6')->setValue("Đến Ngày");
        $objSheet0->getStyle('I6')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));

        $objSheet0->getCell('J6')->setValue($todate);
        $objSheet0->getStyle('J6')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));


        //      sheet name
        $objSheet0->setTitle('DT HOA DON THU SAU');
        //
        // row header
        $objSheet0->getStyle('B8:N9')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 'wrap' => true
        ));

        $objSheet0->getStyle('B8:N9')->getFont()->setBold(true)->setSize(13);
        $objSheet0->getStyle('B8:N9')->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'A6A6A6'))));

        $objSheet0->getCell('B8')->setValue('STT');
        $objSheet0->getCell('B9')->setValue('(1)');
        $objSheet0->getCell('C8')->setValue('Ngày hóa đơn GTGT');
        $objSheet0->getCell('C9')->setValue('(2)');
        $objSheet0->getCell('D8')->setValue('Ký hiệu hóa đơn');
        $objSheet0->getCell('D9')->setValue('(3)');
        $objSheet0->getCell('E8')->setValue('Số hóa đơn GTGT');
        $objSheet0->getCell('E9')->setValue('(4)');
        $objSheet0->getCell('F8')->setValue('Số phiếu tính cước');
        $objSheet0->getCell('F9')->setValue('(5)');
        $objSheet0->getCell('G8')->setValue('Số lệnh');
        $objSheet0->getCell('G9')->setValue('(6)');
        $objSheet0->getCell('H8')->setValue('Tên khách hàng');
        $objSheet0->getCell('H9')->setValue('(7)');
        $objSheet0->getCell('I8')->setValue('Mã số thuế');
        $objSheet0->getCell('I9')->setValue('(8)');
        $objSheet0->getCell('J8')->setValue('Diễn giải dịch vụ');
        $objSheet0->getCell('J9')->setValue('(9)');
        $objSheet0->getCell('K8')->setValue('Thành tiền');
        $objSheet0->getCell('K9')->setValue('(10)');
        $objSheet0->getCell('L8')->setValue('Thuế VAT');
        $objSheet0->getCell('L9')->setValue('(11)');
        $objSheet0->getCell('M8')->setValue('Tổng tiền');
        $objSheet0->getCell('M9')->setValue('(12)=(10)+(11)');
        $objSheet0->getCell('N8')->setValue('Loại hóa đơn');
        $objSheet0->getCell('N9')->setValue('(13)');

        $objSheet0->getRowDimension('8')->setRowHeight(52);
        $objSheet0->getRowDimension('9')->setRowHeight(18);

        $a = 9;
        $startRow = 10;
        $j = 0;
        if ($args === null) goto xxx;
        $invPattern = $this->config->item('INV_CRE')['INV_PATTERN'];
        $sumAmt = 0;
        $sumVat = 0;
        $sumTamt = 0;
        foreach ($args as $arg) {
            $adjustDesc = '';
            switch ($arg['AdjustType']) {
                case 1:
                    $adjustDesc = 'Thay thế';
                    break;
                case 2:
                    $adjustDesc = 'Điều chỉnh tăng';
                    break;
                case 3:
                    $adjustDesc = 'Điều chỉnh giảm';
                    break;
                case 4:
                    $adjustDesc = 'Điều chỉnh thông tin';
                    break;
                default:
                    $adjustDesc = 'HĐ gốc';
                    break;
            }

            $a++;
            $j++;
            $objSheet0->getCell('B' . $a)->setValue($j);
            $objSheet0->getCell('C' . $a)->setValue(date('Y-m-d H:i', strtotime($arg['INV_DATE'])));
            $objSheet0->getCell('D' . $a)->setValue($invPattern);
            $objSheet0->getCell('E' . $a)->setValue($arg['INV_NO']);
            $objSheet0->getCell('F' . $a)->setValue($arg['DRAFT_INV_NO']);
            $objSheet0->getCell('G' . $a)->setValue($arg['REF_NO']);
            $objSheet0->getCell('H' . $a)->setValue($arg['CusName']);
            $objSheet0->getCell('I' . $a)->setValue(strval($arg['VAT_CD']));
            $objSheet0->getCell('J' . $a)->setValue($arg['TRF_STD_DESC']);
            $objSheet0->getCell('K' . $a)->setValue($arg['AMOUNT']);
            $objSheet0->getCell('L' . $a)->setValue($arg['VAT']);
            $objSheet0->getCell('M' . $a)->setValue($arg['TAMOUNT']);
            $objSheet0->getCell('N' . $a)->setValue($adjustDesc);
            $objSheet0->getRowDimension($a)->setRowHeight(-1);
            $sumAmt += $arg['AMOUNT'];
            $sumVat += $arg['VAT'];
            $sumTamt += $arg['TAMOUNT'];
        }

        xxx:
        //for total row
        $endRow = $a;
        $totalRow = $a + 1;
        $objSheet0->mergeCells("B$totalRow:J$totalRow");
        $objSheet0->getCell("H$totalRow")->setValue('TỔNG CỘNG');

        // $objSheet0->getCell('K' . $totalRow)->setValue("=SUM(K$startRow:K$endRow)");
        // $objSheet0->getCell('L' . $totalRow)->setValue("=SUM(L$startRow:L$endRow)");
        // $objSheet0->getCell('M' . $totalRow)->setValue("=SUM(M$startRow:M$endRow)");
        $objSheet0->getCell('K' . $totalRow)->setValue($sumAmt);
        $objSheet0->getCell('L' . $totalRow)->setValue($sumVat);
        $objSheet0->getCell('M' . $totalRow)->setValue($sumTamt);

        $objSheet0->getRowDimension($totalRow)->setRowHeight(26);
        $objSheet0->getStyle("B$totalRow:N$totalRow")->applyFromArray(
            array(
                'font' => array('size' => 13, 'bold' => true, 'color' => array('rgb' => 'ff0000')),
            )
        );

        //for total row
        //vertical tat ca cac dong
        $objSheet0->getStyle("B$startRow:N$totalRow")->getAlignment()->applyFromArray(array(
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 'wrap' => true
        ));

        //canh trai
        $objSheet0->getStyle("I$startRow:I$endRow")->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
        ));
        //canh giua
        $objSheet0->getStyle("B$startRow:G$endRow")->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
        ));

        //dinh dang so
        $objSheet0->getStyle("K$startRow:M$totalRow")->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
        ));
		$formatCurrency = $currencyid == 'VND' ? '_(* #,##0_);_(* (#,##0);_(* ""_);_(@_)' : '_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)';
        $objSheet0->getStyle("K$startRow:M$totalRow")->getNumberFormat()->setFormatCode($formatCurrency);

        //them border cho bang detail
        $objSheet0->getStyle("B8:N$totalRow")->applyFromArray(array(
            'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
        ));

        //chu ky
        $s1 = $totalRow + 2;
        $objSheet0->getCell("L$s1")->setValue("TP. Hồ Chí Minh, ngày " . date('d') . " tháng " . date('m') . " năm " . date('Y') . "");

        $s2 = $s1 + 1;
        $objSheet0->getStyle("E$s2")->getFont()->setBold(true);
        $objSheet0->getCell("E$s2")->setValue('Người giao');
        $objSheet0->getStyle("L$s2")->getFont()->setBold(true);
        $objSheet0->getCell("L$s2")->setValue('Người nhận');
        $objSheet0->getStyle("E$s1:L$s2")->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));

        $objSheet0->getColumnDimension('A')->setWidth(2);
        $objSheet0->getColumnDimension('B')->setWidth(8);
        $objSheet0->getColumnDimension('C')->setWidth(16);
        $objSheet0->getColumnDimension('D')->setWidth(10);
        $objSheet0->getColumnDimension('E')->setWidth(16);
        $objSheet0->getColumnDimension('F')->setWidth(17);
        $objSheet0->getColumnDimension('G')->setWidth(13);
        $objSheet0->getColumnDimension('H')->setWidth(29);
        $objSheet0->getColumnDimension('I')->setWidth(13);
        $objSheet0->getColumnDimension('J')->setWidth(23);
        $objSheet0->getColumnDimension('K')->setWidth(16);
        $objSheet0->getColumnDimension('L')->setWidth(14);
        $objSheet0->getColumnDimension('M')->setWidth(17);
        $objSheet0->getColumnDimension('N')->setWidth(20);
        $objSheet0->setShowGridlines(false);

        header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="doanh-thu-hoa-don-thu-sau.xlsx"');
        $objWriter->save('php://output');
    }

    public function export_draft_without_inv()
    {
        $datajson = $this->input->post('exportdata') ? $this->input->post('exportdata') : '';
        $fromdate = $this->input->post('fromDate') ? $this->input->post('fromDate') : '';
        $todate = $this->input->post('toDate') ? $this->input->post('toDate') : '';
        $args = json_decode($datajson, true);

        $this->load->library('excel');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, "Excel2007");
        ob_end_clean();

        $this->excel->getDefaultStyle()->getFont()->setName('Times New Roman');
        $this->excel->getDefaultStyle()->getFont()->setSize(11);

        $objSheet0 = $this->excel->getActiveSheet();
        $objSheet0->getSheetView()->setZoomScale(85);

        //thong tin cang
        $fullName = $this->config->item('YARD_FULL_NAME');
        $hotLine = $this->config->item('YARD_HOT_LINE');
        $fax = $this->config->item('YARD_FAX');
        $address = $this->config->item('YARD_ADDRESS');
        $telAndFax = (!empty($hotLine) ? ("Tel: " . $hotLine) : "") . (!empty($fax) ? (" - Fax: " . $fax) : "");

        $objSheet0->getCell('H1')->setValue(mb_strtoupper($fullName));
        $objSheet0->getStyle('H1')->applyFromArray(array(
            'alignment' => [
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ],
            'font' => [
                'bold' => true,
                'size' => 12
            ]
        ));

        $objSheet0->getCell('H2')->setValue($address);
        $objSheet0->getStyle('H2')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));

        $objSheet0->getCell('H3')->setValue($telAndFax);
        $objSheet0->getStyle('H3')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));
        $objSheet0->getStyle("G3:I3")->applyFromArray(array(
            'borders' => array('bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
        ));

        $logoPath = FCPATH . "assets/img/logos/logo.jpg";
        if (file_exists($logoPath)) {
            $objDrawing = new PHPExcel_Worksheet_Drawing();
            $objDrawing->setPath($logoPath);
            $objDrawing->setCoordinates('B1');

            // set resize to false first
            $objDrawing->setResizeProportional(false);
            // set width later
            $objDrawing->setWidth(170);
            $objDrawing->setOffsetY(4);
            $objDrawing->setHeight(75);

            $objDrawing->setWorksheet($objSheet0);
        }

        //name of report
        $objSheet0->mergeCells('B5:O5');
        $objSheet0->getStyle('B5:O5')->getFont()->setBold(true)->setSize(20);
        $objSheet0->getStyle('B5')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));
        $objSheet0->getCell('B5')->setValue("DANH SÁCH PHIẾU TÍNH CƯỚC");
        $objSheet0->getRowDimension('5')->setRowHeight(31);
        $objSheet0->getStyle('B')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));

        //tu ngay, den ngay
        $objSheet0->getCell('F6')->setValue("Từ Ngày");
        $objSheet0->getStyle('F6')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));

        $objSheet0->getCell('G6')->setValue($fromdate);
        $objSheet0->getStyle('G6')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'indent' => 3
        ));

        $objSheet0->getCell('I6')->setValue("Đến Ngày");
        $objSheet0->getStyle('I6')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));

        $objSheet0->getCell('J6')->setValue($todate);
        $objSheet0->getStyle('J6')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));


        //      sheet name
        $objSheet0->setTitle('DANH SACH PHIEU TINH CUOC');
        //
        // row header
        $objSheet0->getStyle('B8:O9')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 'wrap' => true
        ));

        $objSheet0->getStyle('B8:O9')->getFont()->setBold(true)->setSize(13);
        $objSheet0->getStyle('B8:O9')->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'A6A6A6'))));

        $objSheet0->getCell('B8')->setValue('STT');
        $objSheet0->getCell('B9')->setValue('(1)');
        $objSheet0->getCell('C8')->setValue('Số phiếu tính cước');
        $objSheet0->getCell('C9')->setValue('(2)');
        $objSheet0->getCell('D8')->setValue('Ngày tạo');
        $objSheet0->getCell('D9')->setValue('(3)');
        $objSheet0->getCell('E8')->setValue('Số lệnh');
        $objSheet0->getCell('E9')->setValue('(4)');
        $objSheet0->getCell('F8')->setValue('Tên khách hàng');
        $objSheet0->getCell('F9')->setValue('(5)');
        $objSheet0->getCell('G8')->setValue('Mã số thuế');
        $objSheet0->getCell('G9')->setValue('(6)');
        $objSheet0->getCell('H8')->setValue('Diễn giải');
        $objSheet0->getCell('H9')->setValue('(7)');
        $objSheet0->getCell('I8')->setValue('Kích cỡ');
        $objSheet0->getCell('I9')->setValue('(8)');
        $objSheet0->getCell('J8')->setValue('Loại hàng');
        $objSheet0->getCell('J9')->setValue('(9)');
        $objSheet0->getCell('K8')->setValue('Số lượng');
        $objSheet0->getCell('K9')->setValue('(10)');
        $objSheet0->getCell('L8')->setValue('Thành tiền');
        $objSheet0->getCell('L9')->setValue('(11)');
        $objSheet0->getCell('M8')->setValue('Tiền thuế');
        $objSheet0->getCell('M9')->setValue('(12)');
        $objSheet0->getCell('N8')->setValue('Tổng tiền');
        $objSheet0->getCell('N9')->setValue('(13)=(11)+(12)');
        $objSheet0->getCell('O8')->setValue('Hình thức TT');
        $objSheet0->getCell('O9')->setValue('(14)');

        $objSheet0->getRowDimension('8')->setRowHeight(52);
        $objSheet0->getRowDimension('9')->setRowHeight(18);

        $a = 9;
        $startRow = 10;
        $j = 0;
        if ($args === null) goto xxx;
        $invPattern = $this->config->item('INV_PATTERN');
        $sumAmt = 0;
        $sumVat = 0;
        $sumTamt = 0;
        foreach ($args as $arg) {
            $a++;
            $j++;
            $objSheet0->getCell('B' . $a)->setValue($j);
            $objSheet0->getCell('C' . $a)->setValue($arg['DRAFT_INV_NO']);
            $objSheet0->getCell('D' . $a)->setValue(date('d/m/Y H:i:s', strtotime($arg['DRAFT_INV_DATE'])));
            $objSheet0->getCell('E' . $a)->setValue($arg['REF_NO']);
            $objSheet0->getCell('F' . $a)->setValue($arg['CusName']);
            $objSheet0->getCell('G' . $a)->setValue(strval($arg['PAYER']));
            $objSheet0->getCell('H' . $a)->setValue($arg['TRF_DESC']);
            $objSheet0->getCell('I' . $a)->setValue($arg['SZ']);
            $objSheet0->getCell('J' . $a)->setValue($arg['CARGO_TYPE']);
            $objSheet0->getCell('K' . $a)->setValue($arg['QTY']);
            $objSheet0->getCell('L' . $a)->setValue($arg['AMOUNT']);
            $objSheet0->getCell('M' . $a)->setValue($arg['VAT']);
            $objSheet0->getCell('N' . $a)->setValue($arg['TAMOUNT']);
            $objSheet0->getCell('O' . $a)->setValue($arg['INV_TYPE'] == 'CAS' ? 'Thu ngay' : 'Thu sau');
            $objSheet0->getRowDimension($a)->setRowHeight(-1);
            $sumAmt += $arg['AMOUNT'];
            $sumVat += $arg['VAT'];
            $sumTamt += $arg['TAMOUNT'];
        }

        xxx:
        //for total row
        $endRow = $a;
        $totalRow = $a + 1;
        $objSheet0->mergeCells("B$totalRow:J$totalRow");
        $objSheet0->getCell("H$totalRow")->setValue('TỔNG CỘNG');

        // $objSheet0->getCell('K' . $totalRow)->setValue("=SUM(K$startRow:K$endRow)");
        // $objSheet0->getCell('L' . $totalRow)->setValue("=SUM(L$startRow:L$endRow)");
        // $objSheet0->getCell('M' . $totalRow)->setValue("=SUM(M$startRow:M$endRow)");
        $objSheet0->getCell('L' . $totalRow)->setValue($sumAmt);
        $objSheet0->getCell('M' . $totalRow)->setValue($sumVat);
        $objSheet0->getCell('N' . $totalRow)->setValue($sumTamt);

        $objSheet0->getRowDimension($totalRow)->setRowHeight(26);
        $objSheet0->getStyle("B$totalRow:O$totalRow")->applyFromArray(
            array(
                'font' => array('size' => 13, 'bold' => true, 'color' => array('rgb' => 'ff0000')),
            )
        );

        //for total row
        //vertical tat ca cac dong
        $objSheet0->getStyle("B$startRow:O$totalRow")->getAlignment()->applyFromArray(array(
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 'wrap' => true
        ));

        //canh giua
        $objSheet0->getStyle("B$startRow:E$endRow")->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
        ));
        $objSheet0->getStyle("G$startRow:G$endRow")->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
        ));
        $objSheet0->getStyle("I$startRow:U$endRow")->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
        ));

        //dinh dang so
        $objSheet0->getStyle("K$startRow:N$totalRow")->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
        ));
        $objSheet0->getStyle("K$startRow:N$totalRow")->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* ""_);_(@_)');

        //them border cho bang detail
        $objSheet0->getStyle("B8:O$totalRow")->applyFromArray(array(
            'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
        ));

        //chu ky
        $s1 = $totalRow + 2;
        $objSheet0->getCell("L$s1")->setValue("TP. Hồ Chí Minh, ngày " . date('d') . " tháng " . date('m') . " năm " . date('Y') . "");

        $s2 = $s1 + 1;
        $objSheet0->getStyle("E$s2")->getFont()->setBold(true);
        $objSheet0->getCell("E$s2")->setValue('Người giao');
        $objSheet0->getStyle("L$s2")->getFont()->setBold(true);
        $objSheet0->getCell("L$s2")->setValue('Người nhận');
        $objSheet0->getStyle("E$s1:L$s2")->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));

        $objSheet0->getColumnDimension('A')->setWidth(2);
        $objSheet0->getColumnDimension('B')->setWidth(8);
        $objSheet0->getColumnDimension('C')->setWidth(17);
        $objSheet0->getColumnDimension('D')->setWidth(19);
        $objSheet0->getColumnDimension('E')->setWidth(16);
        $objSheet0->getColumnDimension('F')->setWidth(30);
        $objSheet0->getColumnDimension('G')->setWidth(13);
        $objSheet0->getColumnDimension('H')->setWidth(29);
        $objSheet0->getColumnDimension('I')->setWidth(8);
        $objSheet0->getColumnDimension('J')->setWidth(12);
        $objSheet0->getColumnDimension('K')->setWidth(8);
        $objSheet0->getColumnDimension('L')->setWidth(14);
        $objSheet0->getColumnDimension('M')->setWidth(14);
        $objSheet0->getColumnDimension('N')->setWidth(17);
        $objSheet0->getColumnDimension('O')->setWidth(17);
        $objSheet0->setShowGridlines(false);

        header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="danh-sach-phieu-tinh-cuoc.xlsx"');
        $objWriter->save('php://output');
    }

    public function export_releaseInv()
    {
        $datajson = $this->input->post('exportdata') ? $this->input->post('exportdata') : '';
        $fromdate = $this->input->post('fromdate') ? $this->input->post('fromdate') : '';
        $todate = $this->input->post('todate') ? $this->input->post('todate') : '';

        $args = json_decode($datajson, true);

        $this->load->library('excel');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, "Excel2007");
        ob_end_clean();

        $this->excel->getDefaultStyle()->getFont()->setName('Times New Roman');
        $this->excel->getDefaultStyle()->getFont()->setSize(11);

        $objSheet0 = $this->excel->getActiveSheet();
        $objSheet0->getSheetView()->setZoomScale(85);

        //thong tin cang
        $fullName = $this->config->item('YARD_FULL_NAME');
        $hotLine = $this->config->item('YARD_HOT_LINE');
        $fax = $this->config->item('YARD_FAX');
        $address = $this->config->item('YARD_ADDRESS');
        $telAndFax = (!empty($hotLine) ? ("Tel: " . $hotLine) : "") . (!empty($fax) ? (" - Fax: " . $fax) : "");

        $objSheet0->getCell('G1')->setValue(mb_strtoupper($fullName));
        $objSheet0->getStyle('G1')->applyFromArray(array(
            'alignment' => [
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ],
            'font' => [
                'bold' => true,
                'size' => 12
            ]
        ));

        $objSheet0->getCell('G2')->setValue($address);
        $objSheet0->getStyle('G2')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));

        $objSheet0->getCell('G3')->setValue($telAndFax);
        $objSheet0->getStyle('G3')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));
        $objSheet0->getStyle("F3:H3")->applyFromArray(array(
            'borders' => array('bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
        ));

        $logoPath = FCPATH . "assets/img/logos/logo.jpg";
        if (file_exists($logoPath)) {
            $objDrawing = new PHPExcel_Worksheet_Drawing();
            $objDrawing->setPath($logoPath);
            $objDrawing->setCoordinates('B1');

            // set resize to false first
            $objDrawing->setResizeProportional(false);
            // set width later
            $objDrawing->setWidth(170);
            $objDrawing->setOffsetY(4);
            $objDrawing->setHeight(75);

            $objDrawing->setWorksheet($objSheet0);
        }

        //row header
        $objSheet0->mergeCells('B4:K4');
        $objSheet0->getStyle('B4:K4')->getFont()->setBold(true)->setSize(16);
        $objSheet0->getStyle('B4')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));
        $objSheet0->getCell('B4')->setValue("BÁO CÁO PHÁT HÀNH HÓA ĐƠN");
        $objSheet0->getRowDimension('4')->setRowHeight(35);
        $objSheet0->getStyle('B')->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER));


        //        //tu ngay, den ngay
        $objSheet0->getCell('E5')->setValue("Từ Ngày");
        $objSheet0->getStyle('E5')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP
        ));

        $objSheet0->getCell('F5')->setValue($fromdate);
        $objSheet0->getStyle('F5')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP
        ));

        $objSheet0->getCell('G5')->setValue("Đến Ngày");
        $objSheet0->getStyle('G5')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP
        ));

        $objSheet0->getCell('H5')->setValue($todate);
        $objSheet0->getStyle('H5')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP
        ));
        $objSheet0->getRowDimension('5')->setRowHeight(27);

        //      sheet name
        $objSheet0->setTitle('HOA DON PHAT HANH');
        //
        ////header
        $objSheet0->getStyle('B6:K7')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 'wrap' => true
        ));

        $objSheet0->getStyle('B6:K7')->getFont()->setBold(true)->setSize(12);
        $objSheet0->getStyle('B6:K7')->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'A6A6A6'))));

        $objSheet0->getCell('B6')->setValue('STT');
        $objSheet0->getCell('B7')->setValue('(1)');
        $objSheet0->getCell('C6')->setValue('Số phiếu tính cước');
        $objSheet0->getCell('C7')->setValue('(2)');
        $objSheet0->getCell('D6')->setValue('Ngày tạo phiếu');
        $objSheet0->getCell('D7')->setValue('(3)');
        $objSheet0->getCell('E6')->setValue('Quyển hóa đơn');
        $objSheet0->getCell('E7')->setValue('(4)');
        $objSheet0->getCell('F6')->setValue('Số hóa đơn');
        $objSheet0->getCell('F7')->setValue('(5)');
        $objSheet0->getCell('G6')->setValue('Ngày tạo HĐ');
        $objSheet0->getCell('G7')->setValue('(6)');
        $objSheet0->getCell('H6')->setValue('Thành tiền');
        $objSheet0->getCell('H7')->setValue('(7)');
        $objSheet0->getCell('I6')->setValue('Thuế VAT');
        $objSheet0->getCell('I7')->setValue('(8)');
        $objSheet0->getCell('J6')->setValue('Tổng tiền');
        $objSheet0->getCell('J7')->setValue('(9)=(7)+(8)');
        $objSheet0->getCell('K6')->setValue('Loại hóa đơn');
        $objSheet0->getCell('K7')->setValue('(10)');
        $objSheet0->getRowDimension('6')->setRowHeight(50);

        $a = 7;
        $grID = "";
        $j = 0;
        if ($args === null) goto xxx;

        $amt = 0;
        $vat = 0;
        $totalAMT = 0;
        foreach ($args as $arg) {
            $adjustDesc = '';
            switch ($arg['AdjustType']) {
                case 1:
                    $adjustDesc = 'Thay thế';
                    break;
                case 2:
                    $adjustDesc = 'Điều chỉnh tăng';
                    break;
                case 3:
                    $adjustDesc = 'Điều chỉnh giảm';
                    break;
                case 4:
                    $adjustDesc = 'Điều chỉnh thông tin';
                    break;
                default:
                    $adjustDesc = 'HĐ gốc';
                    break;
            }

            $a++;
            $j++;
            $objSheet0->getCell('B' . $a)->setValue($j);
            $objSheet0->getCell('C' . $a)->setValue($arg['DRAFT_INV_NO']);
            $objSheet0->getCell('D' . $a)->setValue($this->funcs->clientDateTime($arg['DRAFT_INV_DATE'], '/'));
            $objSheet0->getCell('E' . $a)->setValue($arg['INV_PREFIX']);
            $objSheet0->getCell('F' . $a)->setValue($arg['INV_NO']);
            $objSheet0->getCell('G' . $a)->setValue($this->funcs->clientDateTime($arg['INV_DATE'], '/'));
            $objSheet0->getCell('H' . $a)->setValue($arg['AMOUNT']);
            $objSheet0->getCell('I' . $a)->setValue($arg['VAT']);
            $objSheet0->getCell('J' . $a)->setValue($arg['TAMOUNT']);
            $objSheet0->getCell('K' . $a)->setValue($adjustDesc);
            $objSheet0->getRowDimension($a)->setRowHeight(20);

            $amt += floatval($arg['AMOUNT']);
            $vat += floatval($arg['VAT']);
            $totalAMT += floatval($arg['TAMOUNT']);
        }

        xxx:

        //canh giữa các thông tin trừ số tiền
        $objSheet0->getStyle('C7:G' . $a)->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER));
        $objSheet0->getStyle('K7:K' . $a)->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER));

        //for total row
        $a += 1;
        $objSheet0->mergeCells('B' . $a . ':G' . $a);
        //text color
        $objSheet0->getStyle('B' . $a . ':K' . $a)->applyFromArray(array('font' => array('size' => 13, 'bold' => true, 'color' => array('rgb' => 'ff0000'))));
        //aligment
        $objSheet0->getStyle('B' . $a)->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER));

        $objSheet0->getCell('B' . $a)->setValue("Tổng Cộng");
        $objSheet0->getCell('H' . $a)->setValue($amt);
        $objSheet0->getCell('I' . $a)->setValue($vat);
        $objSheet0->getCell('J' . $a)->setValue($totalAMT);
        $objSheet0->getRowDimension($a)->setRowHeight(30);
        //for total row

        $objSheet0->getStyle('H8:J' . $a)->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER));
        $objSheet0->getStyle('H8:J' . $a)->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* ""_);_(@_)');

        $objSheet0->getStyle('B6:K' . $a)->applyFromArray(array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))));


        //chu ky
        $s1 = $a + 2;
        $objSheet0->getCell("J$s1")->setValue("TP. Hồ Chí Minh, ngày " . date('d') . " tháng " . date('m') . " năm " . date('Y') . "");

        $s2 = $s1 + 1;
        $objSheet0->getStyle("D$s2")->getFont()->setBold(true);
        $objSheet0->getCell("D$s2")->setValue('Người giao');
        $objSheet0->getStyle("J$s2")->getFont()->setBold(true);
        $objSheet0->getCell("J$s2")->setValue('Người nhận');
        $objSheet0->getStyle("D$s1:J$s2")->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));


        $objSheet0->getColumnDimension('A')->setWidth(2);
        $objSheet0->getColumnDimension('B')->setWidth(8);
        $objSheet0->getColumnDimension('C')->setWidth(20);
        $objSheet0->getColumnDimension('D')->setWidth(22);
        $objSheet0->getColumnDimension('E')->setWidth(12);
        $objSheet0->getColumnDimension('F')->setWidth(16);
        $objSheet0->getColumnDimension('G')->setWidth(22);
        $objSheet0->getColumnDimension('H')->setWidth(20);
        $objSheet0->getColumnDimension('I')->setWidth(18);
        $objSheet0->getColumnDimension('J')->setWidth(22);
        $objSheet0->getColumnDimension('K')->setWidth(16);
        $objSheet0->setShowGridlines(false);

        header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="HoaDonPhatHanh.xlsx"');
        $objWriter->save('php://output');
    }

    public function export_revenue_detail()
    {
        $datajson = $this->input->post('exportdata') ? $this->input->post('exportdata') : '';
        $groupingJson = $this->input->post('groupingData') ? $this->input->post('groupingData') : '';
        $fromdate = $this->input->post('fromDate') ? $this->input->post('fromDate') : '';
        $todate = $this->input->post('toDate') ? $this->input->post('toDate') : '';
        $depotid = $this->input->post('depotid') ? $this->input->post('depotid') : '';

        $args = json_decode($datajson, true);
        $groupingData = json_decode($groupingJson, true);

        $this->load->library('excel');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, "Excel2007");
        ob_end_clean();

        $this->excel->getDefaultStyle()->getFont()->setName('Times New Roman');
        $this->excel->getDefaultStyle()->getFont()->setSize(10);

        $objSheet0 = $this->excel->getActiveSheet();

        //thong tin cang
        $fullName = $this->config->item('YARD_FULL_NAME');
        $hotLine = $this->config->item('YARD_HOT_LINE');
        $taxCode = $this->config->item('YARD_TAX_CODE');
        $address = $this->config->item('YARD_ADDRESS');

        $objSheet0->getCell('D2')->setValue(mb_strtoupper($fullName));
        $objSheet0->getStyle('D2')->getFont()->setSize(14);
        $objSheet0->getStyle('D2')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));

        $objSheet0->getCell('D3')->setValue("Mã số thuế: " . implode(" ", str_split($taxCode)));
        $objSheet0->getStyle('D3')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));

        $objSheet0->getCell('D4')->setValue($address);
        $objSheet0->getStyle('D4')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));

        $objSheet0->getCell('D5')->setValue("Điện thoại: " . $hotLine);
        $objSheet0->getStyle('D5')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));

        //DIEM PHAT HANH
        $ports = $depotid != '' ? $depotid : implode(' / ', array_keys($this->config->item('DEPOT_LIST')));
        $objSheet0->getCell('B7')->setValue("Điểm phát hành: " . $ports);
        $objSheet0->getStyle('B7')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));

        $logoPath = FCPATH . "assets/img/logos/logo.jpg";
        if (file_exists($logoPath)) {
            $objDrawing = new PHPExcel_Worksheet_Drawing();
            $objDrawing->setPath($logoPath);
            $objDrawing->setCoordinates('B2');

            // set resize to false first
            $objDrawing->setResizeProportional(false);
            // set width later
            $objDrawing->setWidth(170);
            $objDrawing->setHeight(75);

            $objDrawing->setWorksheet($objSheet0);
        }
        //thong tin cang

        //row header
        $objSheet0->mergeCells('F6:I6');
        $objSheet0->getStyle('F6:I6')->getFont()->setBold(true)->setSize(20);
        $objSheet0->getStyle('F6')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));
        $objSheet0->getCell('F6')->setValue("BÁO CÁO CHI TIẾT THU");
        $objSheet0->getRowDimension('6')->setRowHeight(35);

        ////tu ngay, den ngay
        $objSheet0->mergeCells('F7:I7');
        $objSheet0->getStyle('F7')->getFont()->setSize(12);
        $objSheet0->getCell('F7')->setValue("Từ ngày " . $fromdate . " Đến ngày " . $todate);
        $objSheet0->getStyle('F7')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));

        //sheet name
        $objSheet0->setTitle('BC CHI TIET THU');
        //

        /// header of table
        $objSheet0->getStyle('B9:L9')->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 'wrap' => true
        ));
        $objSheet0->getStyle('B9:L9')->getFont()->setBold(true)->setSize(12);
        $objSheet0->getStyle('B9:L9')->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'BDDCEF'))));

        $objSheet0->getCell('B9')->setValue('STT');
        $objSheet0->getCell('C9')->setValue('NGÀY');
        $objSheet0->getCell('D9')->setValue('SỐ CTỪ');
        $objSheet0->getCell('E9')->setValue('SỐ HÓA ĐƠN');
        $objSheet0->getCell('F9')->setValue('CONTAINER/BILL');
        $objSheet0->getCell('G9')->setValue('LOẠI');
        $objSheet0->getCell('H9')->setValue('SỐ LƯỢNG');
        $objSheet0->getCell('I9')->setValue('SNTL');
        $objSheet0->getCell('J9')->setValue('TIỀN DỊCH VỤ');
        $objSheet0->getCell('K9')->setValue('TIỀN THUẾ');
        $objSheet0->getCell('L9')->setValue('TỔNG CỘNG');
        $objSheet0->getRowDimension('9')->setRowHeight(50);

        $a = 9;
        $j = 0;
        if ($args === null) goto xxx;
        $totals = array(
            "VND" => array(
                "AMT" => 0,
                "VAT" => 0,
                "TAMT" => 0,
            ),
            "USD" => array(
                "AMT" => 0,
                "VAT" => 0,
                "TAMT" => 0,
            )
        );

        // $sort1 = array_column($args, 'TRF_CODE');
        // $sort2 = array_column($args, 'CURRENCYID');
        // array_multisort($sort1, SORT_ASC, $sort2, SORT_DESC, $args);
        $rowGroups = array();
        $last = null;

        for ($index = 0; $index < count($args); $index++) {
            $a++;
            $j++;
            $arg = $args[$index];
            $groupKey = $arg['TRF_CODE'] . '__' . $arg['CURRENCYID'];
            if ($last !== $groupKey) {
                $last = $groupKey;
                $j = 0;
                $index--;

                $objSheet0->mergeCells('B' . $a . ':I' . $a);
                $objSheet0->getCell('B' . $a)->setValue($arg['TRF_CODE'] . " (" . $arg['CURRENCYID'] . ") - " . $groupingData[$groupKey]['TITLE']);
                $objSheet0->getCell('J' . $a)->setValue($groupingData[$groupKey]['AMT']);
                $objSheet0->getCell('K' . $a)->setValue($groupingData[$groupKey]['VAT']);
                $objSheet0->getCell('L' . $a)->setValue($groupingData[$groupKey]['TAMT']);

                array_push($rowGroups, $a);
                continue;
            }

            $objSheet0->getCell('B' . $a)->setValue($j);
            $objSheet0->getCell('C' . $a)->setValue(date('d/m/Y H:i:s', strtotime($arg['DRAFT_INV_DATE'])));
            $objSheet0->getCell('D' . $a)->setValue($arg['DRAFT_INV_NO']);
            $objSheet0->getCell('E' . $a)->setValue($arg['INV_NO']);
            $objSheet0->getCell('F' . $a)->setValue($arg['REMARK'] !== null ? $arg['REMARK'] : '');
            $objSheet0->getCell('G' . $a)->setValue($arg['SZ']);
            $objSheet0->getCell('H' . $a)->setValue(count(explode(",", $arg['REMARK'])));
            $objSheet0->getCell('I' . $a)->setValue($arg['QTY']);
            $objSheet0->getCell('J' . $a)->setValue($arg['AMOUNT']);
            $objSheet0->getCell('K' . $a)->setValue($arg['VAT']);
            $objSheet0->getCell('L' . $a)->setValue($arg['TAMOUNT']);
            $objSheet0->getRowDimension($a)->setRowHeight(20);

            $totals[$arg['CURRENCYID']]["AMT"] += $arg['AMOUNT'];
            $totals[$arg['CURRENCYID']]["VAT"] += $arg['VAT'];
            $totals[$arg['CURRENCYID']]["TAMT"] += $arg['TAMOUNT'];
        }

        xxx:

        //for total row
        $b = $a;
        foreach ($totals as $key => $item) {
            $b++;
            $objSheet0->mergeCells('H' . $b . ':I' . $b);
            $objSheet0->getCell('H' . $b)->setValue('Tổng cộng ' . $key);

            $objSheet0->getCell('J' . $b)->setValue($item['AMT']);
            $objSheet0->getCell('K' . $b)->setValue($item['VAT']);
            $objSheet0->getCell('L' . $b)->setValue($item['TAMT']);
            $objSheet0->getRowDimension($b)->setRowHeight(24);
        }

        //for total row
        //vertical tat ca cac dong
        $objSheet0->getStyle('B10:L' . $b)->getAlignment()->applyFromArray(array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 'wrap' => true));

        //canh giua
        $objSheet0->getStyle('B10:E' . $a)->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
        //canh trai cot ms thue
        $objSheet0->getStyle('F10:F' . $a)->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT));

        //dinh dang so
        $objSheet0->getStyle('G10:L' . $b)->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT));
        $objSheet0->getStyle('G10:L' . $b)->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* ""_);_(@_)');

        //them border cho bang detail
        $objSheet0->getStyle('B9:L' . $a)->applyFromArray(array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '9EBAE0')))));

        //dinh dang bang sum
        $objSheet0->getStyle('H' . ($a + 1) . ':L' . $b)->applyFromArray(
            array(
                'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '9EBAE0'))),
                'font' => array('size' => 13, 'bold' => true, 'color' => array('rgb' => 'ff0000')),
                // 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'fffee2'))
            )
        );
        //dinh dang bang sum

        //chu ky
        $s1 = $b + 2;
        $objSheet0->mergeCells('J' . $s1 . ':L' . $s1);
        $objSheet0->getCell('J' . $s1)->setValue('TP Hồ Chí Minh ngày ' . date('d/m/Y'));

        $s2 = $s1 + 1;
        $objSheet0->getStyle('J' . $s2)->getFont()->setBold(true);
        $objSheet0->getCell('J' . $s2)->setValue('Lập biểu');

        $objSheet0->mergeCells('K' . $s2 . ':L' . $s2);
        $objSheet0->getStyle('K' . $s2)->getFont()->setBold(true);
        $objSheet0->getCell('K' . $s2)->setValue('Thủ trưởng đơn vị');

        $objSheet0->getStyle('J' . $s1 . ':L' . $s2)->getAlignment()->applyFromArray(array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ));

        //dinh dang cac dong group
        foreach ($rowGroups as $r) {
            $objSheet0->getRowDimension($r)->setRowHeight(30);
            $objSheet0->getStyle('B' . $r . ':L' . $r)->getAlignment()->applyFromArray(array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 'wrap' => true
            ));

            $objSheet0->getStyle('B' . $r . ':L' . $r)->getFont()->setBold(true);
            $objSheet0->getStyle('B' . $r . ':L' . $r)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'DDDDDD'))));
        }

        $objSheet0->getColumnDimension('B')->setWidth(10);
        $objSheet0->getColumnDimension('C')->setWidth(22);
        $objSheet0->getColumnDimension('D')->setWidth(22);
        $objSheet0->getColumnDimension('E')->setWidth(23);
        $objSheet0->getColumnDimension('F')->setWidth(77);
        $objSheet0->getColumnDimension('G')->setWidth(15);
        $objSheet0->getColumnDimension('H')->setWidth(16);
        $objSheet0->getColumnDimension('I')->setWidth(15);
        $objSheet0->getColumnDimension('J')->setWidth(29);
        $objSheet0->getColumnDimension('K')->setWidth(29);
        $objSheet0->getColumnDimension('L')->setWidth(34);
        $objSheet0->setShowGridlines(false);

        header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="chi-tiet-thu.xlsx"');
        $objWriter->save('php://output');
    }
}
