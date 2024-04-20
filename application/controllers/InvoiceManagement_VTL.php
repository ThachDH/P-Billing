<?php
defined('BASEPATH') or exit('No direct script access allowed');

class InvoiceManagement_VTL extends CI_Controller
{
    public $data;
    private $ceh;
    private $_responseData = NULL;
    private $_vtResult = NULL;
    private $_access_token = NULL;
    private $yard_id = "";

    function __construct()
    {
        parent::__construct();

        if (empty($this->session->userdata('UserID')) && strpos($this->uri->uri_string(), md5('downloadInvPDF')) === false) {
            redirect(md5('user') . '/' . md5('login'));
        }

        $this->yard_id = $this->config->item("YARD_ID");
        $this->load->model("task_model", "mdltask");

        $this->ceh = $this->load->database('mssql', TRUE);
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
                if ($method == md5($smethod) || $method == $smethod) {
                    $this->$smethod();
                    break;
                }
            }
        }

        if (!in_array($method, $a_methods)) {
            show_404();
        }
    }

    private function strReplaceAssoc(array $replace, $subject)
    {
        return str_replace(array_keys($replace), array_values($replace), $subject);
    }

    public function ccurl($url, $data, $config = [])
    {
        try {
            $contentType = $config['contentType'] ?? 'application/json';
            $headers = array(
                "Content-Type: $contentType"
            );

            if (!$this->_access_token !== NULL) {
                array_push($headers, "Cookie: access_token=" . $this->_access_token);
            }

            $curlOptions = array(
                CURLOPT_CONNECTTIMEOUT => 120, // timeout on connect
                CURLOPT_TIMEOUT => 120, // timeout on response
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_SSL_VERIFYPEER => 0, // Skip SSL Verification
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_USERAGENT => "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36",
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $data
            );

            $curl = curl_init();
            curl_setopt_array($curl, $curlOptions);
            $result = curl_exec($curl); //??? -> _responseData = false??

            $this->_vtResult = $result;

            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ((int)$http_code != 200 || empty($result)) {
                $errorMsg = 'Thất bại: Giao dịch với Hệ Thống Hóa Đơn Điện Tử!';
                if (!empty($result)) {
                    $response = json_decode($result, true);
                    $errorMsg =  ($response['code'] ?? $response['status'] ?? $http_code)
                        . ' - ' . ($response['data'] ?? $response['error'] ?? $response['message'] ?? 'Thất bại: Giao dịch với Hệ Thống Hóa Đơn Điện Tử!');
                }

                $this->data['error'] = "[VT] " . trim($errorMsg);
                return false;
            }

            $this->_responseData = json_decode($result, true);

            if (!empty($this->_responseData['errorCode']) && $this->_responseData['errorCode'] != 200) {
                $this->data['error'] = '[VT] ' . $this->_responseData['errorCode'] . ' : ' . $this->_responseData['description'];
                return false;
            }

            return true;
        } catch (Exception $e) {
            $this->data['error'] = "[VT] " . $e->getMessage();
            return false;
        }
    }

    public function getToken($id, $pass)
    {
        $vt_config = $this->config->item("VTL");
        $reqData = json_encode([
            // 'username' => $vt_config['SRV_ID'],
            // 'password' => $vt_config['SRV_PWD']
            'username' => $id,
            'password' => $pass
        ]);
        $url = $vt_config['URL'] . $vt_config['AUTH_PATH'];
        $isSuccess = $this->ccurl($url, $reqData);
        if (!$isSuccess) {
            return false;
        }

        if (empty($this->_responseData['access_token'])) {
            $this->data['error'] = $this->_responseData['title'] . ':' . $this->_responseData['message'];
            return false;
        }

        $this->_access_token = $this->_responseData['access_token'];
        return true;
    }

    public function importAndPublish()
    {
        $datas = $this->input->post('datas') ? $this->input->post('datas') : array();
        $cusTaxCode = $this->input->post('cusTaxCode') ? $this->input->post('cusTaxCode') : "";
        $cusAddr = $this->input->post('cusAddr') ? ($this->input->post('cusAddr')) : "";
        $cusName = $this->input->post('cusName') ? ($this->input->post('cusName')) : "";
        $cusEmail = $this->input->post('cusEmail') ? ($this->input->post('cusEmail')) : "";
        $inv_type = $this->input->post('inv_type') ? $this->input->post('inv_type') : 'VND';
        $roundNum = $inv_type == "VND" ? 0 : $this->config->item('ROUND_NUM')[$inv_type]; //ROUND_NUM_QTY_UNIT

        $sum_amount = $this->input->post('sum_amount') != "" ? (float)str_replace(",", "", $this->input->post('sum_amount')) : 0;
        $vat_amount = $this->input->post('vat_amount') != "" ? (float)str_replace(",", "", $this->input->post('vat_amount')) : 0;
        $total_amount = $this->input->post('total_amount') != "" ? (float)str_replace(",", "", $this->input->post('total_amount')) : 0;
        $exchange_rate = $this->input->post('exchange_rate') != "" ? (float)str_replace(",", "", $this->input->post('exchange_rate')) : 1;
        $had_exchange = $this->input->post('had_exchange') ? (int)$this->input->post('had_exchange') : 0;

        $currencyInDetails = isset($datas[0]["CURRENCYID"]) ? $datas[0]["CURRENCYID"] : "VND";
        $paymentMethod = 'TM/CK';
        $shipInfo = $this->input->post('shipInfo') ? $this->input->post('shipInfo') : null;
        $shipKey = $this->input->post('shipKey') ? $this->input->post('shipKey') : '';
        $note = $this->input->post('note') ? $this->input->post('note') : '';
        $vat_rate = isset($datas[0]["VAT_RATE"]) && $datas[0]["VAT_RATE"] != ""  ? (float)str_replace(",", "", $datas[0]["VAT_RATE"]) : "";
        $isCredit = $this->input->post('isCredit') ? $this->input->post('isCredit') : '0';
        $publishBy = $this->input->post('publishBy') ? $this->input->post('publishBy') : 'HAP';

        if (empty($shipInfo) && !empty($shipKey)) {
            $shipInfo = $this->searchShip($shipKey);
        }

        $view_exchange_rate = "";
        if ($exchange_rate != 1) {
            $view_exchange_rate = $exchange_rate;
        }

        if ($inv_type == $currencyInDetails || $had_exchange == 1) {
            $exchange_rate = 1;
        }

        $sum_amount = round($sum_amount * $exchange_rate, $roundNum);
        // $total_amount = round($total_amount * $exchange_rate, $roundNum);
        $vat_amount = round($vat_amount * $exchange_rate, $roundNum);
        $total_amount = $sum_amount + $vat_amount;

        $amount_in_words = $this->funcs->convert_number_to_words_en($total_amount, $inv_type); //doc tien usd
        $amount_in_words = ucfirst($amount_in_words);

        $rowguid = $this->funcs->newGuid();
        $pincode = $this->input->post('pinCode') ? $this->input->post('pinCode') : $this->mdltask->generatePinCode();

        $cusCode = trim($cusTaxCode);
        $checkTaxCode = str_replace('-', "", $cusCode);
        if (!in_array(strlen($checkTaxCode), array(10, 13)) || !is_numeric($checkTaxCode)) {
            $cusTaxCode = "";
        }

        if ($vat_rate === "") {
            $vat_rate = "-2";
            $vat_amount = "";
        }

        $cusName = preg_replace("/[\n\r]/", "", $cusName);
        $cusAddr = preg_replace("/[\n\r]/", "", $cusAddr);
        $cusEmail = preg_replace("/[\n\r]/", "", $cusEmail);

        $vt_config = $this->config->item('VTL');
        $inv_pattern = $vt_config['INV_PATTERN'];
        $inv_serial = $vt_config['INV_SERIAL'];

        if ($isCredit == '1') {
            // $creditForms = $vt_config['INV_CRE'];
            // if( empty($publishBy) ){
            // 	echo (json_encode([
            // 		'success' => false,
            // 		'message' => 'Chưa chọn đơn vị phát hành!'
            // 	]));
            // 	exit;
            // }
            // if(empty($creditForms[$publishBy])) {
            if (empty($vt_config[$publishBy])) {
                echo (json_encode([
                    'success' => false,
                    'message' => 'Chưa cấu hình mẫu hóa đơn cho đơn vị này!'
                ]));
                exit;
            }
            $inv_pattern = $vt_config[$publishBy]['INV_PATTERN'];
            $inv_serial = $vt_config[$publishBy]['INV_SERIAL'];
        }

        $invoice = [
            "generalInvoiceInfo" => [
                "invoiceType" => "1",
                "templateCode" => $inv_pattern,
                "transactionUuid" => $rowguid,
                "invoiceSeries" => $inv_serial,
                "invoiceIssuedDate" => $this->dateTimeToMillisecond(),
                "currencyCode" => $inv_type,
                "adjustmentType" => "1", //hoa don goc
                "paymentStatus" => true,
                "paymentType" => $paymentMethod,
                "paymentTypeName" => $paymentMethod,
                "cusGetInvoiceRight" => true,
                "exchangeRate" => $view_exchange_rate
            ],
            "buyerInfo" => [
                "buyerCode" => $cusCode,
                "buyerName" => '',
                // "buyerName" => $cusName,
                "buyerLegalName" => $cusName,
                "buyerTaxCode" => $cusTaxCode,
                "buyerAddressLine" => $cusAddr,
                "buyerPhoneNumber" => "",
                "buyerEmail" => $cusEmail,
                "buyerIdNo" => "",
                "buyerIdType" => "",
                "buyerBankAccount" => "",
                "buyerBankName" => ""
            ],
            // "sellerInfo" => new stdClass(),
            "extAttribute" => [
                [
                    "key" => "reservationCode",
                    "value" => $rowguid,
                ]
            ],
            "payments" => [
                [
                    "paymentMethodName" => $paymentMethod
                ]
            ],
            // "deliveryInfo" => [],
            "itemInfo" => [],
            "discountItemInfo" => [],
            "summarizeInfo" => [
                "sumOfTotalLineAmountWithoutTax" => $sum_amount,
                "totalAmountWithoutTax" => $sum_amount,
                "totalTaxAmount" => $vat_amount,
                "totalAmountWithTax" => $total_amount,
                "totalAmountWithTaxInWords" => "",
                "discountAmount" => 0,
                "settlementDiscountAmount" => 0,
                "taxPercentage" => $vat_rate
            ],
            "taxBreakdowns" => [],
            "metadata" => [
                [
                    "id" => null,
                    "invoiceTemplatePrototypeId" => "2875",
                    "keyLabel" => "Ghi chú",
                    "keyTag" => "invoiceNote",
                    "valueType" => "text",
                    "isRequired" => false,
                    "isSeller" => false,
                    "stringValue" => ''
                ]
            ]
        ];

        //lam tron so luong+don gia theo yeu cau KT
        $roundNumQty_Unit = $this->config->item('ROUND_NUM_QTY_UNIT');
        $itemInfos = [];
        $taxBreakdowns = [];
        foreach ($datas as $k => $item) { //UNIT_AMT
            if (is_array($item)) {
                $temp = $item['TRF_DESC'];
                $unit = $this->mdltask->getUnitName($item['INV_UNIT']);
                // if (in_array(strtoupper($unit), array("CONT", "CNT", "BOX"))) {
                $sz = isset($item["ISO_SZTP"]) ? $this->getContSize($item["ISO_SZTP"]) : (isset($item["SZ"]) ? $item["SZ"] : "");
                if ($sz != "") {
                    $temp .= "-" . $sz . $item['FE']; //CONT 40F
                }
                $temp .= "-" . $item['CARGO_TYPE'];
                // }

                $moreDesc = isset($item['TRF_DESC_MORE']) ? trim($item['TRF_DESC_MORE']) : "";
                $cntrList = isset($item['Remark']) ? trim($item['Remark']) : "";

                if ($isCredit == '1') {
                    // k hiển thị list cont
                } elseif (count(explode(',', $cntrList)) > 9) { //nhieu hon 9cont
                    $temp .= "-" . explode('||', $moreDesc)[0];
                    //lenh nang ha + dong rut : $moreDesc = BLNO / BKNO
                    //lenh luu bai + dien lanh : $moreDesc = BLNO / BKNO || UETU5196773: 18/02/2022 14:17:09 - 29/03/2022 14:04|TGBU8763621: ....
                } elseif (strpos($moreDesc, "||") !== false) { //it hon 9cont + ccó chuỗi || -> luubai + dien lanh (desc co dang: BLNO/BKNO || .... ....)
                    $temp .=  "-" . " (" . $moreDesc . ")";
                } else {
                    $temp .= "-" . $cntrList; //nguoc lai su dung list cont
                }

                //encode content of TRF_DESC because it contain <,> ..
                $itemName = (preg_replace("/[\n\r]/", "", $temp));
                //add info to UNIT CODE
                $unitName = ($unit);

                //them moi lam tron so
                $urate = (float)str_replace(",", "", $item['UNIT_RATE']);
                $i_amt = (float)str_replace(",", "", $item['AMOUNT']);

                $qty = round($item['QTY'], $roundNumQty_Unit); //lam tron so luong+don gia theo yeu cau KT
                if($inv_type == 'USD') {
                    $unitPrice = round($urate * $exchange_rate, 4); //lam tron so luong+don gia theo yeu cau KT
                } else {
                $unitPrice = round($urate * $exchange_rate, $roundNumQty_Unit); //lam tron so luong+don gia theo yeu cau KT
                }
                $amount = round($i_amt * $exchange_rate, $roundNum);
                $taxPerText = !empty($item["VAT_RATE"]) || $item["VAT_RATE"] == '0'  ? (float)str_replace(",", "", $item["VAT_RATE"]) : "-2"; //-2 : Hoa dơn KCT
                $vat_amt = $taxPerText == "-2" ? '' : (float)str_replace(",", "", $item['VAT']);
                $vat = $taxPerText == "-2" ? '' : round($vat_amt * $exchange_rate, $roundNum);
                $kd = [
                    "itemCode" => "",
                    "itemName" => $itemName,
                    "unitName" => $unitName,
                    "unitPrice" => $unitPrice,
                    "quantity" => $qty,
                    "itemTotalAmountWithoutTax" => $amount,
                    "taxPercentage" => $taxPerText,
                    "taxAmount" => $vat,
                    "discount" => 0,
                    "itemDiscount" => 0
                ];
                array_push($itemInfos, $kd);

                if (empty($taxBreakdowns[$taxPerText])) {
                    $taxBreakdowns[$taxPerText] = [
                        "taxPercentage" => $taxPerText,
                        "taxableAmount" => $amount,
                        "taxAmount" => $vat
                    ];
                } else {
                    $taxBreakdowns[$taxPerText]['taxableAmount'] += $amount;
                    if (!empty($vat)) {
                        $taxBreakdowns[$taxPerText]['taxAmount'] += $vat;
                    }
                }
            }
        }

        if (count($itemInfos) == 0) {
            $this->data['results'] = "nothing to publish!";
            echo json_encode($this->data);
            exit;
        }

        if (!empty($note)) {
            array_push($itemInfos, [
                'itemName' => $note,
                'selection' => 2
            ]);
        }

        //add prod detail
        $invoice['itemInfo'] = $itemInfos;
        $invoice['taxBreakdowns'] = array_values($taxBreakdowns);
        if (empty($this->_access_token)) {
            if (!$this->getToken($vt_config[$publishBy]['SRV_ID'], $vt_config[$publishBy]['SRV_PWD'])) {
                echo json_encode($this->data);
                exit;
            }
        }
        $url = $vt_config['URL'] . $vt_config['API_PATH'] . '/InvoiceWS/createInvoice/' . $vt_config[$publishBy]['SUPPLIER_TAX_CODE'];
        $isSuccess = $this->ccurl($url, json_encode($invoice));

        //log
        $newData = [
            "error" => $this->data['error'],
            "content" => !empty($this->_responseData) ? $this->_responseData :  $this->_vtResult
        ];
        $this->ceh->logEvent($pincode, 'VT_PUBLISH_INVOICE', 'T', $invoice, $newData);

        if (!$isSuccess) {
            echo json_encode($this->data);
            exit;
        }

        $this->data['pattern'] = $invoice['generalInvoiceInfo']['templateCode']; //invoiceSeries
        $result = $this->_responseData['result'];
        if (!$result['invoiceNo']) {
            $this->data['results'] = "Error invoice!";
            echo json_encode($this->data);
            exit;
        }

        //$this->data['serial'] = $invoice['generalInvoiceInfo']['invoiceSeries']; //c21taa
        $this->data['serial'] = substr($result['invoiceNo'], 0, 6);
        $this->data['fkey'] = $pincode;
        $this->data['inv'] = $result['invoiceNo']; //c23taa 
        //$this->data['invno'] = str_replace($invoice['generalInvoiceInfo']['invoiceSeries'], '', $result['invoiceNo']);
        $this->data['invno'] = str_replace($this->data['serial'], '', $result['invoiceNo']);
        $this->data['hddt'] = 1; //them moi hd thu sau
        $this->data['reservationCode'] = $result['reservationCode'];
        $this->data['INV_DATE'] = date("Y-m-d H:i:s", $invoice['generalInvoiceInfo']['invoiceIssuedDate'] / 1000);
        $this->data['publishBy'] = $publishBy;

        echo json_encode($this->data);
        exit;
    }

    public function downloadInvPDF()
    {
        $this->getInvView();
    }

    public function getInvView()
    {
        $vt_config = $this->config->item('VTL');
        $pattern = $this->input->get('pattern') ? $this->input->get('pattern') : $vt_config['INV_PATTERN'];
        $fkey = $this->input->get('fkey') ? $this->input->get('fkey') : "";
        $inv = $this->input->get('inv') ? $this->input->get('inv') : "";
        if ($fkey && !$inv) {
            $temp = $this->ceh->select('INV_NO, REF_TYPE, INV_TYPE')->where("PinCode", $fkey)->order_by('INV_DATE', 'DESC')->get('INV_VAT')->row_array();
        } else {
            $temp = $this->ceh->select('INV_NO, REF_TYPE, INV_TYPE')->where("INV_NO", $inv)->order_by('INV_DATE', 'DESC')->get('INV_VAT')->row_array();
        }
        if ($temp === NULL) {
            echo "<div style='width: 100vw;text-align: center;margin: -8px 0 0 -8px;font-weight: 600;font-size: 27px;color: white;background-color:#614040;line-height: 2;'>Không tìm thấy thông tin hoá đơn này!</div>";
            exit();
        }

        $inv = $temp['INV_NO'];
        if ($temp['INV_TYPE'] == 'CRE' && !empty($temp['REF_TYPE'])) {
            $publishBy = $temp['REF_TYPE'];
            if (empty($publishBy)) {
                echo (json_encode([
                    'success' => false,
                    'message' => 'Không xác định được phát hành!'
                ]));
                exit;
            }
            if (empty($vt_config[$publishBy])) {
                echo (json_encode([
                    'success' => false,
                    'message' => "Chưa cấu hình mẫu hóa đơn TS cho đơn vị [$publishBy]!"
                ]));
                exit;
            }

            $pattern = $vt_config[$publishBy]['INV_PATTERN'];
        }
        if (!$publishBy) {
            $publishBy = $this->input->get('publishBy') ? $this->input->get('publishBy') : 'HAP';
        }

        if (empty($this->_access_token)) {
            if (!$this->getToken($vt_config[$publishBy]['SRV_ID'], $vt_config[$publishBy]['SRV_PWD'])) {
                echo json_encode($this->data);
                exit;
            }
        }

        $inputData = [
            "supplierTaxCode" => $vt_config[$publishBy]['SUPPLIER_TAX_CODE'],
            "templateCode" => $pattern,
            "invoiceNo" => $inv,
            "fileType" => "pdf"
        ];

        $url = $vt_config['URL'] . $vt_config['API_PATH'] . '/InvoiceUtilsWS/getInvoiceRepresentationFile';
        $isSuccess = $this->ccurl($url, json_encode($inputData));
        log_message('error', json_encode($inputData));
        if (!$isSuccess) {
            echo "<div style='width: 100vw;text-align: center;margin: -8px 0 0 -8px;font-weight: 600;font-size: 27px;color: white;background-color:#614040;line-height: 2;'>"
                . $this->data['error']
                . "</div>";
            exit;
        }

        $base64File = $this->_responseData['fileToBytes'];
        if (empty($base64File)) {
            echo "<div style='width: 100vw;text-align: center;margin: -8px 0 0 -8px;font-weight: 600;font-size: 27px;color: white;background-color:#614040;line-height: 2;'>"
                . "Không thể tải tệp từ hệ thống HDDT"
                . "</div>";
            exit;
        }

        $content = base64_decode($base64File);
        header('Content-Type: application/pdf');
        header('Content-Length: ' . strlen($content));
        header('Content-disposition: inline; filename="' . $this->_responseData['fileName'] . '"');
        echo $content;
        exit();
    }

    public function viewDraftInv()
    {
        $datas = $this->input->post('datas') ? $this->input->post('datas') : array();
        $cusTaxCode = $this->input->post('cusTaxCode') ? $this->input->post('cusTaxCode') : "";
        $cusAddr = $this->input->post('cusAddr') ? ($this->input->post('cusAddr')) : "";
        $cusName = $this->input->post('cusName') ? ($this->input->post('cusName')) : "";
        $cusEmail = $this->input->post('cusEmail') ? ($this->input->post('cusEmail')) : "";
        $inv_type = $this->input->post('inv_type') ? $this->input->post('inv_type') : 'VND';
        $roundNum = $this->config->item('ROUND_NUM')[$inv_type]; //ROUND_NUM_QTY_UNIT

        $sum_amount = $this->input->post('sum_amount') != "" ? (float)str_replace(",", "", $this->input->post('sum_amount')) : 0;
        $vat_amount = $this->input->post('vat_amount') != "" ? (float)str_replace(",", "", $this->input->post('vat_amount')) : 0;
        $total_amount = $this->input->post('total_amount') != "" ? (float)str_replace(",", "", $this->input->post('total_amount')) : 0;
        $exchange_rate = $this->input->post('exchange_rate') != "" ? (float)str_replace(",", "", $this->input->post('exchange_rate')) : 1;
        $had_exchange = $this->input->post('had_exchange') ? (int)$this->input->post('had_exchange') : 0;

        $currencyInDetails = isset($datas[0]["CURRENCYID"]) ? $datas[0]["CURRENCYID"] : "VND";
        $paymentMethod = 'TM/CK';
        $shipInfo = $this->input->post('shipInfo') ? $this->input->post('shipInfo') : null;
        $shipKey = $this->input->post('shipKey') ? $this->input->post('shipKey') : "";
        $vat_rate = isset($datas[0]["VAT_RATE"]) && $datas[0]["VAT_RATE"] != ""  ? (float)str_replace(",", "", $datas[0]["VAT_RATE"]) : "";
        $isCredit = $this->input->post('isCredit') ? $this->input->post('isCredit') : '0';
        $publishBy = $this->input->post('publishBy') ? $this->input->post('publishBy') : 'HAP';

        $old_pincode = $this->input->post('old_pincode') ?? '';
        $old_invNo = $this->input->post('old_invNo') ?? '';
        $old_invDate = $this->input->post('old_invDate') ?? '';

        $adjust_type_text = $this->input->post('adjust_type') ? $this->input->post('adjust_type') : ''; //3: THAY THE | 5.1.1: DIEU CHINH TANG TIEN | 5.1.2: DIEU CHINH GIAM TIEN | 5.2: DIEU CHINH TT
        $note = $this->input->post('note') ? $this->input->post('note') : ''; //3: THAY THE | 5.1: DIEU CHINH TIEN | 5.2: DIEU CHINH TT
        $adjust_infor = explode('.', $adjust_type_text);
        $adjust_type = $adjust_infor[0] ?? '';
        $adjust_inv_type = $adjust_infor[1] ?? '';
        $isIncreament = empty($adjust_infor[2]) ? NULL : ($adjust_infor[2] == '1' ? true : false);
        if (!empty($adjust_type_text) && empty($adjust_type)) {
            $this->data['error'] = 'Loại điều chỉnh không phù hợp!';
            echo json_encode($this->data);
            exit;
        }

        if ($adjust_type && $old_invNo) { // mới làm view hóa đơn ở màn hình điều chỉnh/ thay thế
            $temp = $this->ceh->select('REF_TYPE')->where("INV_NO", $old_invNo)->order_by('INV_DATE', 'DESC')->get('INV_VAT')->row_array();
            $publishBy = $temp['REF_TYPE'] ? $temp['REF_TYPE'] : 'HAP';
        }

        if (empty($shipInfo) && !empty($shipKey)) {
            $shipInfo = $this->searchShip($shipKey);
        }

        $view_exchange_rate = "";
        if ($exchange_rate != 1) {
            $view_exchange_rate = $exchange_rate;
        }

        if ($inv_type == $currencyInDetails || $had_exchange == 1) {
            $exchange_rate = 1;
        }

        $sum_amount = round($sum_amount * $exchange_rate, $roundNum);
        // $total_amount = round($total_amount * $exchange_rate, $roundNum);
        $vat_amount = round($vat_amount * $exchange_rate, $roundNum);
        $total_amount = $sum_amount + $vat_amount;
        $amount_in_words = $this->funcs->convert_number_to_words_en($total_amount, $inv_type); //doc tien usd
        $amount_in_words = ucfirst($amount_in_words);
        $pincode = $this->mdltask->generatePinCode();
        $rowguid = $this->funcs->newGuid();
        $cusCode = trim($cusTaxCode);
        $checkTaxCode = str_replace('-', "", $cusCode);
        if (!in_array(strlen($checkTaxCode), array(10, 13)) || !is_numeric($checkTaxCode)) {
            $cusTaxCode = "";
        }

        if ($vat_rate === "") {
            $vat_rate = "-2";
            $vat_amount = "";
        }

        $cusName = preg_replace("/[\n\r]/", "", $cusName);
        $cusAddr = preg_replace("/[\n\r]/", "", $cusAddr);
        $cusEmail = preg_replace("/[\n\r]/", "", $cusEmail);
        $vt_config = $this->config->item('VTL');
        $inv_pattern = $vt_config['INV_PATTERN'];
        $inv_serial = $vt_config['INV_SERIAL'];

        if ($isCredit == '1') {
            // $creditForms = $vt_config['INV_CRE'];
            // if (empty($publishBy)) {
            //     echo (json_encode([
            //         'success' => false,
            //         'message' => 'Chưa chọn đơn vị phát hành!'
            //     ]));
            //     exit;
            // }
            // if (empty($creditForms[$publishBy])) {
            if (empty($vt_config[$publishBy])) {
                echo (json_encode([
                    'success' => false,
                    'message' => 'Chưa cấu hình mẫu hóa đơn cho đơn vị này!'
                ]));
                exit;
            }
            $inv_pattern = $vt_config[$publishBy]['INV_PATTERN'];
            $inv_serial = $vt_config[$publishBy]['INV_SERIAL'];
        }

        $invoice = [
            "generalInvoiceInfo" => [
                "invoiceType" => "1",
                "templateCode" => $inv_pattern,
                "transactionUuid" => $rowguid,
                "invoiceSeries" => $inv_serial,
                "invoiceIssuedDate" => $this->dateTimeToMillisecond(),
                "currencyCode" => $inv_type,
                "adjustmentType" => "1", //hoa don goc
                "paymentStatus" => true,
                "paymentType" => $paymentMethod,
                "paymentTypeName" => $paymentMethod,
                "cusGetInvoiceRight" => true,
                "exchangeRate" => $view_exchange_rate
            ],
            "buyerInfo" => [
                "buyerCode" => $cusCode,
                "buyerName" => '',
                // "buyerName" => $cusName,
                "buyerLegalName" => $cusName,
                "buyerTaxCode" => $cusTaxCode,
                "buyerAddressLine" => $cusAddr,
                "buyerPhoneNumber" => "",
                "buyerEmail" => $cusEmail,
                "buyerIdNo" => "",
                "buyerIdType" => "",
                "buyerBankAccount" => "",
                "buyerBankName" => ""
            ],
            // "sellerInfo" => new stdClass(),
            "extAttribute" => [
                [
                    "key" => "reservationCode",
                    "value" => $rowguid,
                ]
            ],
            "payments" => [
                [
                    "paymentMethodName" => $paymentMethod
                ]
            ],
            // "deliveryInfo" => [],
            "itemInfo" => [],
            "discountItemInfo" => [],
            "summarizeInfo" => [
                "sumOfTotalLineAmountWithoutTax" => $sum_amount,
                "totalAmountWithoutTax" => $sum_amount,
                "totalTaxAmount" => $vat_amount,
                "totalAmountWithTax" => $total_amount,
                "totalAmountWithTaxInWords" => "",
                "discountAmount" => 0,
                "settlementDiscountAmount" => 0,
                "taxPercentage" => $vat_rate
            ],
            "taxBreakdowns" => [],
            "metadata" => [
                [
                    "id" => null,
                    "invoiceTemplatePrototypeId" => "2875",
                    "keyLabel" => "Ghi chú",
                    "keyTag" => "invoiceNote",
                    "valueType" => "text",
                    "isRequired" => false,
                    "isSeller" => false,
                    "stringValue" => ''
                ]
            ]
        ];

        //neu la hd dieu chinh | thay the => bo sung them thong tin cho generalInvoiceInfo
        if (!empty($adjust_type)) {
            $invoice["generalInvoiceInfo"]["adjustmentType"] = explode('.', $adjust_type)[0]; //3 | 5
            $invoice["generalInvoiceInfo"]["adjustedNote"] = $note;
            $invoice["generalInvoiceInfo"]["originalInvoiceId"] = $old_invNo;
            $invoice["generalInvoiceInfo"]["originalInvoiceIssueDate"] = $this->dateTimeToMillisecond($old_invDate);
            $invoice["generalInvoiceInfo"]["additionalReferenceDesc"] = "Văn bản";
            $invoice["generalInvoiceInfo"]["additionalReferenceDate"] = $this->dateTimeToMillisecond();

            if ($adjust_type == "5") { //neu la hd dieu chinh
                $invoice['generalInvoiceInfo']['adjustmentInvoiceType'] = $adjust_inv_type;  //doi với loại hd diueu chinh -> them thong tin adjustmentInvoiceType (1: dc tien | 2: dc ttin)
                if ($adjust_inv_type === '1' && !$isIncreament) { //neu la hd dieu chinh GIAM
                    unset($invoice['summarizeInfo']['totalTaxAmount']);
                    unset($invoice['summarizeInfo']['totalAmountWithTax']);
                    $invoice['summarizeInfo']['isTotalAmountPos'] = false;
                    $invoice['summarizeInfo']['isTotalTaxAmountPos'] = false;
                    $invoice['summarizeInfo']['isTotalAmtWithoutTaxPos'] = false;
                }
            }
        }
        if ($adjust_inv_type == '2') { //nếu là hóa đơn điều chỉnh thông tin 
            unset($invoice['summarizeInfo']);
        }

        $itemInfos = [];
        $taxBreakdowns = [];
        //neu không phải là HĐ điều chỉnh thông tin (5.2) => thực hiện tính toán cho phần detail
        if (!($adjust_type == '5' && $adjust_inv_type == '2')) {
            //lam tron so luong+don gia theo yeu cau KT
            $roundNumQty_Unit = $this->config->item('ROUND_NUM_QTY_UNIT');
            $roundNumAmount = $inv_type == "VND" ? 0 : $roundNumQty_Unit;
            foreach ($datas as $k => $item) { //UNIT_AMT
                if (is_array($item)) {
                    $temp = $item['TRF_DESC'];
                    $unit = $this->mdltask->getUnitName($item['INV_UNIT']);
                    // if (in_array(strtoupper($unit), array("CONT", "CNT", "BOX"))) {
                    $sz = isset($item["ISO_SZTP"]) ? $this->getContSize($item["ISO_SZTP"]) : (isset($item["SZ"]) ? $item["SZ"] : "");
                    if ($sz != "") {
                        $temp .= "-" . $sz . $item['FE']; //CONT 40F
                    }
                    $temp .= "-" . $item['CARGO_TYPE'];
                    // }

                    $moreDesc = isset($item['TRF_DESC_MORE']) ? trim($item['TRF_DESC_MORE']) : "";
                    $cntrList = isset($item['Remark']) ? trim($item['Remark']) : "";
                    if ($isCredit == '1') {
                        // k hiển thị list cont
                    } elseif (count(explode(',', $cntrList)) > 9) { //nhieu hon 5cont
                        $temp .= "-" . explode('||', $moreDesc)[0];
                        //lenh nang ha + dong rut : $moreDesc = BLNO / BKNO
                        //lenh luu bai + dien lanh : $moreDesc = BLNO / BKNO || UETU5196773: 18/02/2022 14:17:09 - 29/03/2022 14:04|TGBU8763621: ....
                    } elseif (strpos($moreDesc, "||") !== false) { //it hon 5cont + ccó chuỗi || -> luubai + dien lanh (desc co dang: BLNO/BKNO || .... ....)
                        $temp .=   "-" . " (" . $moreDesc . ")";
                    } else {
                        $temp .= "-" . $cntrList; //nguoc lai su dung list cont
                    }

                    //encode content of TRF_DESC because it contain <,> ..
                    $itemName = (preg_replace("/[\n\r]/", "", $temp));
                    //add info to UNIT CODE
                    $unitName = ($unit);

                    //them moi lam tron so
                    $urate = (float)str_replace(",", "", $item['UNIT_RATE']);
                    $i_amt = (float)str_replace(",", "", $item['AMOUNT']);

                    $qty = round($item['QTY'], $roundNumQty_Unit); //lam tron so luong+don gia theo yeu cau KT
                    if($inv_type == 'USD') {
                        $unitPrice = round($urate * $exchange_rate, 4); //lam tron so luong+don gia theo yeu cau KT
                    } else {
                    $unitPrice = round($urate * $exchange_rate, $roundNumQty_Unit); //lam tron so luong+don gia theo yeu cau KT
                    }
                    $amount = round($i_amt * $exchange_rate, $roundNumAmount);
                    $taxPerText = !empty($item["VAT_RATE"]) || $item["VAT_RATE"] == '0'  ? (float)str_replace(",", "", $item["VAT_RATE"]) : "-2"; //-2 : Hoa dơn KCT
                    $vat_amt = $taxPerText == "-2" ? '' : (float)str_replace(",", "", $item['VAT']);
                    $vat = $taxPerText == "-2" ? '' : round($vat_amt * $exchange_rate, $roundNumAmount);
                    if (!$isIncreament && $adjust_type == '5' && $adjust_inv_type == '1') {
                        $unitPrice = -$unitPrice;
                    }
                    $kd = [
                        "itemCode" => "",
                        "itemName" => $itemName,
                        "unitName" => $unitName,
                        "unitPrice" => $unitPrice,
                        "quantity" => $qty,
                        "itemTotalAmountWithoutTax" => $amount,
                        "taxPercentage" => $taxPerText,
                        "taxAmount" => $vat,
                        "discount" => 0,
                        "itemDiscount" => 0
                    ];
                    //xet truong hop 5.1 (dieu chinh tien)
                    if ($adjust_type == '5' && $isIncreament !== NULL) {
                        $kd['isIncreaseItem'] = $isIncreament;
                    }
                    array_push($itemInfos, $kd);

                    if (empty($taxBreakdowns[$taxPerText])) {
                        $taxBreakdowns[$taxPerText] = [
                            "taxPercentage" => $taxPerText,
                            "taxableAmount" => $amount,
                            "taxAmount" => $vat
                        ];
                    } else {
                        $taxBreakdowns[$taxPerText]['taxableAmount'] += $amount;
                        if (!empty($vat)) {
                            $taxBreakdowns[$taxPerText]['taxAmount'] += $vat;
                        }
                    }
                }
            }

            if (count($itemInfos) == 0) {
                $this->data['results'] = "Kiểm tra lại dữ liệu [detail not null]!";
                echo json_encode($this->data);
                exit;
            }
        } else {
            // thêm dòng cước hóa đơn điêu chỉnh thông tin
            if (is_array($datas[0])) {
                $item = $datas[0];
                $temp = $item['TRF_DESC'];
                $unit = $this->mdltask->getUnitName($item['INV_UNIT']);
                // if (in_array(strtoupper($unit), array("CONT", "CNT", "BOX"))) {
                $sz = isset($item["ISO_SZTP"]) ? $this->getContSize($item["ISO_SZTP"]) : (isset($item["SZ"]) ? $item["SZ"] : "");
                if ($sz != "") {
                    $temp .= "-" . $sz . $item['FE']; //CONT 40F
                }
                $temp .= "-" . $item['CARGO_TYPE'];
                // }

                $moreDesc = isset($item['TRF_DESC_MORE']) ? trim($item['TRF_DESC_MORE']) : "";
                $cntrList = isset($item['Remark']) ? trim($item['Remark']) : "";
                if ($isCredit == '1') {
                    // k hiển thị list cont
                } elseif (count(explode(',', $cntrList)) > 9) { //nhieu hon 5cont
                    $temp .= "-" . explode('||', $moreDesc)[0];
                    //lenh nang ha + dong rut : $moreDesc = BLNO / BKNO
                    //lenh luu bai + dien lanh : $moreDesc = BLNO / BKNO || UETU5196773: 18/02/2022 14:17:09 - 29/03/2022 14:04|TGBU8763621: ....
                } elseif (strpos($moreDesc, "||") !== false) { //it hon 5cont + ccó chuỗi || -> luubai + dien lanh (desc co dang: BLNO/BKNO || .... ....)
                    $temp .=   "-" . " (" . $moreDesc . ")";
                } else {
                    $temp .= "-" . $cntrList; //nguoc lai su dung list cont
                }

                //encode content of TRF_DESC because it contain <,> ..
                $itemName = (preg_replace("/[\n\r]/", "", $temp));
            }
            //thêm thông tin điều chỉnh thông tin hóa đơn
            if ($adjust_inv_type == '2') {
                $kd = [
                    "itemCode" => "",
                    'selection' => 2,
                    "itemName" => $itemName,
                ];
                array_push($itemInfos, $kd);
            }
        }

        if (!empty($note)) {
            array_push($itemInfos, [
                'itemName' => $note,
                'selection' => 2
            ]);
        }

        //add prod detail
        $invoice['itemInfo'] = $itemInfos;
        $invoice['taxBreakdowns'] = array_values($taxBreakdowns);

        if (empty($this->_access_token)) {
            if (!$this->getToken($vt_config[$publishBy]['SRV_ID'], $vt_config[$publishBy]['SRV_PWD'])) {
                echo json_encode($this->data);
                exit;
            }
        }

        log_message('error', json_encode($invoice));
        $url = $vt_config['URL'] . $vt_config['API_PATH'] . '/InvoiceUtilsWS/createInvoiceDraftPreview/' . $vt_config[$publishBy]['SUPPLIER_TAX_CODE'];
        log_message('error', ($url));
        log_message('error', ($this->_access_token));
        $isSuccess = $this->ccurl($url, json_encode($invoice));
        if (!$isSuccess) {
            echo json_encode($this->data);
            exit;
        }

        $base64File = $this->_responseData['fileToBytes'];
        if (empty($base64File)) {
            $this->data['error'] = '[VT] ' . 'Không lấy được file hóa đơn nháp!';
            echo json_encode($this->data);
            exit;
        }

        echo (json_encode([
            'success' => true,
            'pdfData' => $base64File
        ]));
        exit;
    }

    //business
    public function adjustInvoice()
    {
        $datas = $this->input->post('datas') ? $this->input->post('datas') : array();
        $cusTaxCode = $this->input->post('cusTaxCode') ? $this->input->post('cusTaxCode') : "";
        $cusAddr = $this->input->post('cusAddr') ? ($this->input->post('cusAddr')) : "";
        $cusName = $this->input->post('cusName') ? ($this->input->post('cusName')) : "";
        $inv_type = $this->input->post('inv_type') ? $this->input->post('inv_type') : 'VND';
        $roundNum = $this->config->item('ROUND_NUM')[$inv_type]; //ROUND_NUM_QTY_UNIT

        $sum_amount = $this->input->post('sum_amount') != "" ? (float)str_replace(",", "", $this->input->post('sum_amount')) : 0;
        $vat_amount = $this->input->post('vat_amount') != "" ? (float)str_replace(",", "", $this->input->post('vat_amount')) : 0;
        $total_amount = $this->input->post('total_amount') != "" ? (float)str_replace(",", "", $this->input->post('total_amount')) : 0;
        $exchange_rate = $this->input->post('exchange_rate') != "" ? (float)str_replace(",", "", $this->input->post('exchange_rate')) : 1;
        $had_exchange = $this->input->post('had_exchange') ? (int)$this->input->post('had_exchange') : 0;

        $currencyInDetails = isset($datas[0]["CURRENCYID"]) ? $datas[0]["CURRENCYID"] : "VND";
        $paymentMethod = 'TM/CK';
        $shipInfo = $this->input->post('shipInfo') ? $this->input->post('shipInfo') : null;
        $shipKey = $this->input->post('shipKey') ? $this->input->post('shipKey') : "";
        $vat_rate = isset($datas[0]["VAT_RATE"]) && $datas[0]["VAT_RATE"] != ""  ? (float)str_replace(",", "", $datas[0]["VAT_RATE"]) : "";

        $old_pincode = $this->input->post('old_pincode') ?? '';
        $old_invNo = $this->input->post('old_invNo') ?? '';
        $old_invDate = $this->input->post('old_invDate') ?? '';
        $isCredit = $this->input->post('isCredit') ? $this->input->post('isCredit') : '0';
        if ($isCredit == '0') {
            $publishBy = 'HAP';
        } else {
            $temp = $this->ceh->select('REF_TYPE')->where("INV_NO", $old_invNo)->order_by('INV_DATE', 'DESC')->get('INV_VAT')->row_array();
            $publishBy = $temp['REF_TYPE'] ? $temp['REF_TYPE'] : 'HAP';
        }

        $adjust_type_text = $this->input->post('adjust_type') ? $this->input->post('adjust_type') : ''; //3: THAY THE | 5.1.1: DIEU CHINH TANG TIEN | 5.1.2: DIEU CHINH GIAM TIEN | 5.2: DIEU CHINH TT
        $note = $this->input->post('note') ? $this->input->post('note') : ''; //3: THAY THE | 5.1: DIEU CHINH TIEN | 5.2: DIEU CHINH TT
        $adjust_infor = explode('.', $adjust_type_text);
        $adjust_type = $adjust_infor[0] ?? '';
        $adjust_inv_type = $adjust_infor[1] ?? '';
        $isIncreament = empty($adjust_infor[2]) ? NULL : ($adjust_infor[2] == '1' ? true : false);
        if (empty($adjust_type)) {
            $this->data['error'] = 'Loại điều chỉnh không phù hợp!';
            echo json_encode($this->data);
            exit;
        }

        if (empty($shipInfo) && !empty($shipKey)) {
            $shipInfo = $this->searchShip($shipKey);
        }

        $view_exchange_rate = "";
        if ($exchange_rate != 1) {
            $view_exchange_rate = $exchange_rate;
        }

        if ($inv_type == $currencyInDetails || $had_exchange == 1) {
            $exchange_rate = 1;
        }

        $sum_amount = round($sum_amount * $exchange_rate, $roundNum);
        // $total_amount = round($total_amount * $exchange_rate, $roundNum);
        $vat_amount = round($vat_amount * $exchange_rate, $roundNum);
        $total_amount = $sum_amount + $vat_amount;
        $amount_in_words = $this->funcs->convert_number_to_words_en($total_amount, $inv_type); //doc tien usd
        $amount_in_words = ucfirst($amount_in_words);
        $rowguid = $this->funcs->newGuid();
        $pincode = $this->mdltask->generatePinCode();
        $cusCode = trim($cusTaxCode);
        $checkTaxCode = str_replace('-', "", $cusCode);
        if (!in_array(strlen($checkTaxCode), array(10, 13)) || !is_numeric($checkTaxCode)) {
            $cusTaxCode = "";
        }

        if ($vat_rate === "") {
            $vat_rate = "-2";
            $vat_amount = "";
        }

        $cusName = preg_replace("/[\n\r]/", "", $cusName);
        $cusAddr = preg_replace("/[\n\r]/", "", $cusAddr);
        $vt_config = $this->config->item('VTL');
        $inv_pattern = $vt_config['INV_PATTERN'];
        $inv_serial = $vt_config['INV_SERIAL'];

        if ($isCredit == '1') {
            // $creditForms = $vt_config['INV_CRE'];
            // if (empty($publishBy)) {
            //     echo (json_encode([
            //         'success' => false,
            //         'message' => 'Chưa chọn đơn vị phát hành!'
            //     ]));
            //     exit;
            // }
            // if (empty($creditForms[$publishBy])) {
            if (empty($vt_config[$publishBy])) {
                echo (json_encode([
                    'success' => false,
                    'message' => 'Chưa cấu hình mẫu hóa đơn cho đơn vị này!'
                ]));
                exit;
            }
            $inv_pattern = $vt_config[$publishBy]['INV_PATTERN'];
            $inv_serial = $vt_config[$publishBy]['INV_SERIAL'];
        }

        $invoice = [
            "generalInvoiceInfo" => [
                "invoiceType" => "1",
                "templateCode" => $inv_pattern,
                "transactionUuid" => $rowguid,
                "invoiceSeries" => $inv_serial,
                "invoiceIssuedDate" => $this->dateTimeToMillisecond(),
                "currencyCode" => $inv_type,

                "adjustmentType" => explode('.', $adjust_type)[0], //3 | 5
                "adjustedNote" => $note,
                "originalInvoiceId" => $old_invNo,
                "originalInvoiceIssueDate" => $this->dateTimeToMillisecond($old_invDate),
                "additionalReferenceDesc" => "Văn bản",
                "additionalReferenceDate" => $this->dateTimeToMillisecond(),

                "paymentStatus" => true,
                "paymentType" => $paymentMethod,
                "paymentTypeName" => $paymentMethod,
                "cusGetInvoiceRight" => true,
                "exchangeRate" => $view_exchange_rate,
            ],
            "buyerInfo" => [
                "buyerCode" => $cusCode,
                "buyerName" => '',
                // "buyerName" => $cusName,
                "buyerLegalName" => $cusName,
                "buyerTaxCode" => $cusTaxCode,
                "buyerAddressLine" => $cusAddr,
                "buyerPhoneNumber" => "",
                "buyerEmail" => "",
                "buyerIdNo" => "",
                "buyerIdType" => "",
                "buyerBankAccount" => "",
                "buyerBankName" => ""
            ],
            // "sellerInfo" => new stdClass(),
            "extAttribute" => [
                [
                    "key" => "reservationCode",
                    "value" => $rowguid,
                ]
            ],
            "payments" => [
                [
                    "paymentMethodName" => $paymentMethod
                ]
            ],
            // "deliveryInfo" => [],
            "itemInfo" => [],
            "discountItemInfo" => [],
            "summarizeInfo" => [
                "sumOfTotalLineAmountWithoutTax" => $sum_amount,
                "totalAmountWithoutTax" => $sum_amount,
                "totalTaxAmount" => $vat_amount,
                "totalAmountWithTax" => $total_amount,
                "totalAmountWithTaxInWords" => "",
                "discountAmount" => 0,
                "settlementDiscountAmount" => 0,
                "taxPercentage" => $vat_rate
            ],
            "taxBreakdowns" => [],
            "metadata" => [
                [
                    "id" => null,
                    "invoiceTemplatePrototypeId" => "2875",
                    "keyLabel" => "Ghi chú",
                    "keyTag" => "invoiceNote",
                    "valueType" => "text",
                    "isRequired" => false,
                    "isSeller" => false,
                    "stringValue" => ''
                ]
            ]
        ];

        if ($adjust_type == "5") {
            $invoice['generalInvoiceInfo']['adjustmentInvoiceType'] = $adjust_inv_type;  //doi với loại hd diueu chinh -> them thong tin adjustmentInvoiceType (1: dc tien | 2: dc ttin)
            if ($adjust_inv_type === '1' && !$isIncreament) {
                unset($invoice['summarizeInfo']['totalTaxAmount']);
                unset($invoice['summarizeInfo']['totalAmountWithTax']);
                $invoice['summarizeInfo']['isTotalAmountPos'] = false;
                $invoice['summarizeInfo']['isTotalTaxAmountPos'] = false;
                $invoice['summarizeInfo']['isTotalAmtWithoutTaxPos'] = false;
            }
            if ($adjust_inv_type == '2') { //nếu là hóa đơn điều chỉnh thông tin 
                unset($invoice['summarizeInfo']);
            }
        }

        $itemInfos = [];
        $taxBreakdowns = [];
        //neu không phải là HĐ điều chỉnh thông tin (5.2) => thực hiện tính toán cho phần detail
        if (!($adjust_type == '5' && $adjust_inv_type == '2')) {
            //lam tron so luong+don gia theo yeu cau KT
            $roundNumQty_Unit = $this->config->item('ROUND_NUM_QTY_UNIT');
            foreach ($datas as $k => $item) { //UNIT_AMT
                if (is_array($item)) {
                    $temp = $item['TRF_DESC'];
                    $unit = $this->mdltask->getUnitName($item['INV_UNIT']);
                    // if (in_array(strtoupper($unit), array("CONT", "CNT", "BOX"))) {
                    $sz = isset($item["ISO_SZTP"]) ? $this->getContSize($item["ISO_SZTP"]) : (isset($item["SZ"]) ? $item["SZ"] : "");
                    if ($sz != "") {
                        $temp .= "-" . $sz . $item['FE']; //CONT 40F
                    }
                    $temp .= "-" . $item['CARGO_TYPE'];
                    // }

                    $moreDesc = isset($item['TRF_DESC_MORE']) ? trim($item['TRF_DESC_MORE']) : "";
                    $cntrList = isset($item['Remark']) ? trim($item['Remark']) : "";

                    if ($isCredit == '1') {
                        // k hiển thị list cont
                    } elseif (count(explode(',', $cntrList)) > 9) { //nhieu hon 5cont
                        $temp .= "-" . explode('||', $moreDesc)[0];
                        //lenh nang ha + dong rut : $moreDesc = BLNO / BKNO
                        //lenh luu bai + dien lanh : $moreDesc = BLNO / BKNO || UETU5196773: 18/02/2022 14:17:09 - 29/03/2022 14:04|TGBU8763621: ....
                    } elseif (strpos($moreDesc, "||") !== false) { //it hon 5cont + ccó chuỗi || -> luubai + dien lanh (desc co dang: BLNO/BKNO || .... ....)
                        $temp .=  "-" . " (" . $moreDesc . ")";
                    } else {
                        $temp .= "-" . $cntrList; //nguoc lai su dung list cont
                    }

                    //encode content of TRF_DESC because it contain <,> ..
                    $itemName = (preg_replace("/[\n\r]/", "", $temp));
                    //add info to UNIT CODE
                    $unitName = ($unit);

                    //them moi lam tron so
                    $urate = (float)str_replace(",", "", $item['UNIT_RATE']);
                    $i_amt = (float)str_replace(",", "", $item['AMOUNT']);

                    $qty = round($item['QTY'], $roundNumQty_Unit); //lam tron so luong+don gia theo yeu cau KT
                    if($inv_type == 'USD') {
                        $unitPrice = round($urate * $exchange_rate, 4); //lam tron so luong+don gia theo yeu cau KT
                    } else {
                    $unitPrice = round($urate * $exchange_rate, $roundNumQty_Unit); //lam tron so luong+don gia theo yeu cau KT
                    }
                    $amount = round($i_amt * $exchange_rate, $roundNum);
                    $taxPerText = !empty($item["VAT_RATE"]) || $item["VAT_RATE"] == '0'  ? (float)str_replace(",", "", $item["VAT_RATE"]) : "-2"; //-2 : Hoa dơn KCT
                    $vat_amt = $taxPerText == "-2" ? '' : (float)str_replace(",", "", $item['VAT']);
                    $vat = $taxPerText == "-2" ? '' : round($vat_amt * $exchange_rate, $roundNum);
                    if (!$isIncreament && $adjust_type == '5' && $adjust_inv_type == '1') {
                        $unitPrice = -$unitPrice;
                    }
                    $kd = [
                        "itemCode" => "",
                        "itemName" => $itemName,
                        "unitName" => $unitName,
                        "unitPrice" => $unitPrice,
                        "quantity" => $qty,
                        "itemTotalAmountWithoutTax" => $amount,
                        "taxPercentage" => $taxPerText,
                        "taxAmount" => $vat,
                        "discount" => 0,
                        "itemDiscount" => 0
                    ];
                    //xet truong hop 5.1 (dieu chinh tien)
                    if ($adjust_type == '5' && $isIncreament !== NULL) {
                        $kd['isIncreaseItem'] = $isIncreament;
                    }
                    array_push($itemInfos, $kd);

                    if (empty($taxBreakdowns[$taxPerText])) {
                        $taxBreakdowns[$taxPerText] = [
                            "taxPercentage" => $taxPerText,
                            "taxableAmount" => $amount,
                            "taxAmount" => $vat
                        ];
                    } else {
                        $taxBreakdowns[$taxPerText]['taxableAmount'] += $amount;
                        if (!empty($vat)) {
                            $taxBreakdowns[$taxPerText]['taxAmount'] += $vat;
                        }
                    }
                }
            }

            if (count($itemInfos) == 0) {
                $this->data['results'] = "Kiểm tra lại dữ liệu [detail not null]!";
                echo json_encode($this->data);
                exit;
            }
        } else {
            // thêm dòng cước hóa đơn điêu chỉnh thông tin
            if (is_array($datas[0])) {
                $item = $datas[0];
                $temp = $item['TRF_DESC'];
                $unit = $this->mdltask->getUnitName($item['INV_UNIT']);
                // if (in_array(strtoupper($unit), array("CONT", "CNT", "BOX"))) {
                $sz = isset($item["ISO_SZTP"]) ? $this->getContSize($item["ISO_SZTP"]) : (isset($item["SZ"]) ? $item["SZ"] : "");
                if ($sz != "") {
                    $temp .= "-" . $sz . $item['FE']; //CONT 40F
                }
                $temp .= "-" . $item['CARGO_TYPE'];
                // }

                $moreDesc = isset($item['TRF_DESC_MORE']) ? trim($item['TRF_DESC_MORE']) : "";
                $cntrList = isset($item['Remark']) ? trim($item['Remark']) : "";
                if ($isCredit == '1') {
                    // k hiển thị list cont
                } elseif (count(explode(',', $cntrList)) > 9) { //nhieu hon 5cont
                    $temp .= "-" . explode('||', $moreDesc)[0];
                    //lenh nang ha + dong rut : $moreDesc = BLNO / BKNO
                    //lenh luu bai + dien lanh : $moreDesc = BLNO / BKNO || UETU5196773: 18/02/2022 14:17:09 - 29/03/2022 14:04|TGBU8763621: ....
                } elseif (strpos($moreDesc, "||") !== false) { //it hon 5cont + ccó chuỗi || -> luubai + dien lanh (desc co dang: BLNO/BKNO || .... ....)
                    $temp .=   "-" . " (" . $moreDesc . ")";
                } else {
                    $temp .= "-" . $cntrList; //nguoc lai su dung list cont
                }

                //encode content of TRF_DESC because it contain <,> ..
                $itemName = (preg_replace("/[\n\r]/", "", $temp));
            }
            //thêm thông tin điều chỉnh thông tin hóa đơn
            if ($adjust_inv_type == '2') {
                $kd = [
                    "itemCode" => "",
                    'selection' => 2,
                    "itemName" => $itemName,
                ];
                array_push($itemInfos, $kd);
            }
        }

        if ($adjust_type == '5' && !empty($note)) {
            array_push($itemInfos, [
                'itemName' => $note,
                'selection' => 2
            ]);
        }

        //add prod detail
        $invoice['itemInfo'] = $itemInfos;
        $invoice['taxBreakdowns'] = array_values($taxBreakdowns);

        if (empty($this->_access_token)) {
            if (!$this->getToken($vt_config[$publishBy]['SRV_ID'], $vt_config[$publishBy]['SRV_PWD'])) {
                echo json_encode($this->data);
                exit;
            }
        }

        $url = $vt_config['URL'] . $vt_config['API_PATH'] . '/InvoiceWS/createInvoice/' . $vt_config[$publishBy]['SUPPLIER_TAX_CODE'];

        // log_message('error', json_encode($invoice));

        $isSuccess = $this->ccurl($url, json_encode($invoice));

        //log
        $newData = [
            "error" => $this->data['error'],
            "content" => !empty($this->_responseData) ? $this->_responseData :  $this->_vtResult
        ];
        $this->ceh->logEvent($pincode, 'VT_ADJUST_INVOICE', 'T', $invoice, $newData);

        if (!$isSuccess) {
            echo json_encode($this->data);
            exit;
        }

        $this->data['pattern'] = $invoice['generalInvoiceInfo']['templateCode']; //invoiceSeries
        $result = $this->_responseData['result'];
        if (!$result['invoiceNo']) {
            $this->data['results'] = "Error invoice!";
            echo json_encode($this->data);
            exit;
        }
        // $this->data['serial'] = $invoice['generalInvoiceInfo']['invoiceSeries'];
        $this->data['serial'] = substr($result['invoiceNo'], 0, 6);
        $this->data['fkey'] = $pincode;
        $this->data['inv'] = $result['invoiceNo'];
        $this->data['invno'] = str_replace($this->data['serial'], '', $result['invoiceNo']);
        // $this->data['invno'] = str_replace($invoice['generalInvoiceInfo']['invoiceSeries'], '', $result['invoiceNo']);
        $this->data['hddt'] = 1; //them moi hd thu sau
        $this->data['reservationCode'] = $result['reservationCode'];
        $this->data['INV_DATE'] = date("Y-m-d H:i:s", $invoice['generalInvoiceInfo']['invoiceIssuedDate'] / 1000);
        $this->data['publishBy'] = $publishBy;

        echo json_encode($this->data);
        exit;
    }

    public function cancelInv()
    {
        $vt_config = $this->config->item('VTL');
        $fkey = $this->input->post('fkey') ? $this->input->post('fkey') : "";
        $inv = $this->input->post('inv') ? $this->input->post('inv') : "";
        $invType = $this->input->post('invType') ? $this->input->post('invType') : "";
        $issueDate = $this->input->post('issueDate') ? $this->input->post('issueDate') : "";
        $cancelReason = $this->input->post('cancelReason') ? $this->input->post('cancelReason') : "";
        $publishBy = $this->input->post('publishBy') ? $this->input->post('publishBy') : "HAP";

        if ($fkey && !$inv) {
            $temp = $this->ceh->select('INV_NO, INV_DATE, INV_TYPE, REF_TYPE')->where("PinCode", $fkey)->order_by('INV_DATE', 'DESC')->get('INV_VAT')->row_array();
            if ($temp === NULL) {
                $this->data['error'] = 'Không tìm thấy dữ liệu hóa đơn theo Mã PIN';
                echo json_encode($this->data);
                exit();
            }

            $inv = $temp['INV_NO'];
            $issueDate = $temp['INV_DATE'];
            $invType = $temp['INV_TYPE'];
        } else {
            $temp = $this->ceh->select('INV_NO, REF_TYPE, INV_TYPE')->where("INV_NO", $inv)->order_by('INV_DATE', 'DESC')->get('INV_VAT')->row_array();
        }

        $pattern = $vt_config['INV_PATTERN'];

        $inv = $temp['INV_NO'];
        if ($temp['INV_TYPE'] == 'CRE' && !empty($temp['REF_TYPE'])) {
            $publishBy = $temp['REF_TYPE'];
            if (empty($publishBy)) {
                echo (json_encode([
                    'success' => false,
                    'message' => 'Không xác định được phát hành!'
                ]));
                exit;
            }
            if (empty($vt_config[$publishBy])) {
                echo (json_encode([
                    'success' => false,
                    'message' => "Chưa cấu hình mẫu hóa đơn TS cho đơn vị [$publishBy]!"
                ]));
                exit;
            }

            $pattern = $vt_config[$publishBy]['INV_PATTERN'];
        }

        if (empty($this->_access_token)) {
            if (!$this->getToken($vt_config[$publishBy]['SRV_ID'], $vt_config[$publishBy]['SRV_PWD'])) {
                echo json_encode($this->data);
                exit;
            }
        }

        $url = $vt_config['URL'] . $vt_config['API_PATH'] . '/InvoiceWS/cancelTransactionInvoice';
        $inputData = [
            'supplierTaxCode' => $vt_config[$publishBy]['SUPPLIER_TAX_CODE'],
            'templateCode' => $pattern,
            'invoiceNo' => $inv,
            'strIssueDate' => $this->dateTimeToMillisecond($issueDate),
            'additionalReferenceDesc' => $cancelReason,
            'additionalReferenceDate' => $this->dateTimeToMillisecond(),
            'reasonDelete' => $cancelReason
        ];

        $isSuccess = $this->ccurl($url, http_build_query($inputData), ['contentType' => 'application/x-www-form-urlencoded']);

        //log
        $newData = [
            "error" => $this->data['error'],
            "content" => !empty($this->_responseData) ? $this->_responseData :  $this->_vtResult
        ];
        $this->ceh->logEvent($inv, 'VT_CANCEL_INVOICE', 'T', $inputData, $newData);

        if (!$isSuccess) {
            echo json_encode($this->data);
            exit;
        }

        $this->data['success'] = true;
        echo json_encode($this->data);
        exit;
    }

    public function updateCustomer()
    {
    }

    private function dateTimeToMillisecond($dateTime = NULL)
    {
        if (empty($dateTime)) {
            $dateTime = date('Y-m-d H:i:s.v');
        }

        $local_timestamp = strtotime($this->funcs->dbDateTime($dateTime));
        // date_default_timezone_set('UTC');
        // $utcDateTime = date("Y-m-d H:i:s.v", $local_timestamp);
        // $utc_timestamp = strtotime($utcDateTime);
        return $local_timestamp * 1000;
    }

    private function searchShip($shipKey)
    {
        $this->ceh->select('vs.ShipKey, vv.ShipName, vs.ShipID, vs.ShipYear, vs.ShipVoy, vs.ImVoy, vs.ExVoy, vs.BERTH_NO
                            , vs.ETB, vs.ETD, vs.ATD, vs.BerthDate, vs.YARD_CLOSE, vs.LaneID, vv.Nation_CD, vv.DWT, vv.GRW');
        $this->ceh->join('VESSELS vv', 'vv.ShipID = vs.ShipID');
        $this->ceh->where('vs.ShipKey', $shipKey);
        $this->ceh->where('vs.YARD_ID', $this->yard_id);

        $this->ceh->order_by('vs.ETB', 'DESC');
        $stmt = $this->ceh->get('VESSEL_SCHEDULE vs')->row_array();
        return $stmt;
    }

    private function getContSize($sztype)
    {
        if (!isset($sztype)) {
            return "";
        }

        switch (substr($sztype, 0, 1)) {
            case "2":
                return 20;
            case "4":
                return 40;
            case "L":
            case "M":
            case "9":
                return 45;
            default:
                return "";
        }

        return "";
    }
}
