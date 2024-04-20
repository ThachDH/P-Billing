<?php
defined('BASEPATH') or exit('No direct script access allowed');

class InvoiceManagement_VNPT extends CI_Controller
{
    public $data;
    private $ceh;
    private $_responseXML = '';

    function __construct()
    {
        parent::__construct();

        if (empty($this->session->userdata('UserID')) && strpos($this->uri->uri_string(), md5('downloadInvPDF')) === false) {
            redirect(md5('user') . '/' . md5('login'));
        }

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

    public function ccurl($funcname, $servicename, $xmlbody)
    {
        try {
            $config = $this->config->item("VNPT");
            $subdomain = $config["SUB_DOMAIN"];
            $headers = array(
                "Content-Type:application/soap+xml;charset=UTF-8",
                'SOAPAction:  "http://tempuri.org/' . $funcname . '"',
                "Host: $subdomain.vnpt-invoice.com.vn"
            );

            $xml12 = $config['xmlv1.2'];
            //            $xmlfomart = htmlentities($xml);
            $xmlsend = str_replace('XML_BODY', $xmlbody, $xml12);

            $curlOptions = array(
                CURLOPT_CONNECTTIMEOUT => 120, // timeout on connect
                CURLOPT_TIMEOUT => 120, // timeout on response
                CURLOPT_URL => "https://$subdomain.vnpt-invoice.com.vn/$servicename.asmx",
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_SSL_VERIFYPEER => 0, // Skip SSL Verification
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_USERAGENT => "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36",
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $xmlsend
            );

            $curl = curl_init();
            curl_setopt_array($curl, $curlOptions);
            $this->_responseXML = curl_exec($curl); //??? -> _responseXML = false??

            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ((int)$http_code != 200 || !$this->_responseXML) {
                $this->data['error'] = 'Thất bại: Giao dịch với Hệ Thống Hóa Đơn Điện Tử!';
                return false;
            }

            // if (!curl_errno($curl)) {
            //     $info = curl_getinfo($curl);
            //     log_message('error', $info['total_time'].' seconds to send a request to '.$info['url']."\n");
            // }

        } catch (Exception $e) {
            $this->data['error'] = $e->getMessage();
        }
        return true;
    }

    public function importAndPublish()
    {
        $datas = $this->input->post('datas') ? $this->input->post('datas') : array();
        $cusTaxCode = $this->input->post('cusTaxCode') ? $this->input->post('cusTaxCode') : "";
        $cusAddr = $this->input->post('cusAddr') ? htmlspecialchars($this->input->post('cusAddr')) : "";
        $cusName = $this->input->post('cusName') ? htmlspecialchars($this->input->post('cusName')) : "";
        $inv_type = $this->input->post('inv_type') ? $this->input->post('inv_type') : 'VND';
        $roundNum = $this->config->item('ROUND_NUM')[$inv_type]; //ROUND_NUM_QTY_UNIT

        $sum_amount = $this->input->post('sum_amount') != "" ? (float)str_replace(",", "", $this->input->post('sum_amount')) : "";
        $vat_amount = $this->input->post('vat_amount') != "" ? (float)str_replace(",", "", $this->input->post('vat_amount')) : "";
        $total_amount = $this->input->post('total_amount') != "" ? (float)str_replace(",", "", $this->input->post('total_amount')) : "";
        $exchange_rate = $this->input->post('exchange_rate') != "" ? (float)str_replace(",", "", $this->input->post('exchange_rate')) : 1;
        $had_exchange = $this->input->post('had_exchange') ? (int)$this->input->post('had_exchange') : 0;

        $currencyInDetails = isset($datas[0]["CURRENCYID"]) ? $datas[0]["CURRENCYID"] : "VND";
        $paymentMethod = $this->input->post('paymentMethod') ? $this->input->post('paymentMethod') : 'TM/CK';
        $shipInfo = $this->input->post('shipInfo') ? $this->input->post('shipInfo') : "";
        $vat_rate = isset($datas[0]["VAT_RATE"]) && $datas[0]["VAT_RATE"] != ""  ? (float)str_replace(",", "", $datas[0]["VAT_RATE"]) : "";

        $view_exchange_rate = "";
        if ($exchange_rate != 1) {
            $view_exchange_rate = $exchange_rate;
        }

        if ($inv_type == $currencyInDetails || $had_exchange == 1) {
            $exchange_rate = 1;
        }

        $sum_amount = round($sum_amount * $exchange_rate, $roundNum);
        $total_amount = round($total_amount * $exchange_rate, $roundNum);
        $vat_amount = round($vat_amount * $exchange_rate, $roundNum);

        $amount_in_words = $this->funcs->convert_number_to_words($total_amount, $inv_type); //doc tien usd
        $amount_in_words = htmlspecialchars($amount_in_words);

        $pincode = $this->mdltask->generatePinCode();

        $cusCode = trim($cusTaxCode);
        /* $checkTaxCode = str_replace('-', "", $cusCode);
        if (!in_array(strlen($checkTaxCode), array(10, 13)) || !is_numeric($checkTaxCode)) {
            $cusTaxCode = "";
        }*/

        if ($vat_rate === "") {
            $vat_rate = "-1";
            $vat_amount = "-1";
        }

        $cusName = preg_replace("/[\n\r]/", "", $cusName);
        $cusAddr = preg_replace("/[\n\r]/", "", $cusAddr);

        $invData = <<<EOT
                <Inv>
                    <key>$pincode</key>
                    <Invoice>
                        <CusCode>$cusCode</CusCode>
                        <CusName>$cusName</CusName>
                        <CusAddress>$cusAddr</CusAddress>
                        <CusPhone></CusPhone>
                        <CusTaxCode>$cusTaxCode</CusTaxCode>
                        <PaymentMethod>$paymentMethod</PaymentMethod>
                        <KindOfService></KindOfService>
                        <ShipName>$shipInfo</ShipName>
                        <Products>
                            PRODUCT_CONTENT
                        </Products>
                        <Total>$sum_amount</Total>
                        <VATRate>$vat_rate</VATRate>
                        <VATAmount>$vat_amount</VATAmount>
                        <Amount>$total_amount</Amount>
                        <AmountInWords>$amount_in_words</AmountInWords>
                        <DiscountAmount>$exchange_rate</DiscountAmount>
                        <CurrencyUnit>$inv_type</CurrencyUnit>
                        <Extra>$pincode</Extra>
                        <ExchangeRate>$view_exchange_rate</ExchangeRate>
                    </Invoice>
                </Inv>
EOT;

        $product_content = <<<EOT
                <Product>
                    <ProdName>TRF_DESC</ProdName>
                    <ProdUnit>INV_UNIT</ProdUnit>
                    <ProdQuantity>QTY</ProdQuantity>
                    <ProdPrice>UNIT_RATE</ProdPrice>
                    <Total>AMT</Total>
                </Product>
EOT;
        // <Total>AMT</Total>

        //lam tron so luong+don gia theo yeu cau KT
        $roundNumQty_Unit = $this->config->item('ROUND_NUM_QTY_UNIT');
        $strFinal = "";
        foreach ($datas as $item) { //UNIT_AMT
            if (is_array($item)) {
                $temp = $item['TRF_DESC'];

                $unit = $this->mdltask->getUnitName($item['INV_UNIT']);
                if (in_array(strtoupper($unit), array("CONT", "CNT", "BOX"))) {
                    $sz = isset($item["ISO_SZTP"]) ? $this->getContSize($item["ISO_SZTP"]) : (isset($item["SZ"]) ? $item["SZ"] : "");
                    if ($sz != "") {
                        $temp .= " " . $sz . $item['FE']; //CONT 40F
                    }
                }

                $moreDesc = isset($item['TRF_DESC_MORE']) ? trim($item['TRF_DESC_MORE']) : "";
                $cntrList = isset($item['Remark']) ? trim($item['Remark']) : "";

                if (count(explode(',', $cntrList)) > 5) { //nhieu hon 5cont
                    $temp .= " " . explode('||', $moreDesc)[0];
                    //lenh nang ha + dong rut : $moreDesc = BLNO / BKNO
                    //lenh luu bai + dien lanh : $moreDesc = BLNO / BKNO || UETU5196773: 18/02/2022 14:17:09 - 29/03/2022 14:04|TGBU8763621: ....
                } elseif (strpos($moreDesc, "||") !== false) { //it hon 5cont + ccó chuỗi || -> luubai + dien lanh (desc co dang: BLNO/BKNO || .... ....)
                    $temp .= " (" . $moreDesc . ")";
                } else {
                    $temp .= " " . $cntrList; //nguoc lai su dung list cont
                }

                //encode content of TRF_DESC because it contain <,> ..
                $item['TRF_DESC'] = htmlspecialchars(preg_replace("/[\n\r]/", "", $temp));
                //add info to UNIT CODE
                $item['INV_UNIT'] = htmlspecialchars($unit);

                //them moi lam tron so
                $urate = (float)str_replace(",", "", $item['UNIT_RATE']);
                $i_amt = (float)str_replace(",", "", $item['AMOUNT']);

                $item['QTY'] = round($item['QTY'], $roundNumQty_Unit); //lam tron so luong+don gia theo yeu cau KT
                $item['UNIT_RATE'] = round($urate * $exchange_rate, $roundNumQty_Unit); //lam tron so luong+don gia theo yeu cau KT

                $item['AMT'] = round($i_amt * $exchange_rate, $roundNum);

                unset($item['AMOUNT']);
                unset($item['SZ']);
                unset($item['FE']);
                $strFinal .= $this->strReplaceAssoc($item, $product_content);
            }
        }

        if ($strFinal == "") {
            $this->data['results'] = "nothing to publish";
            echo json_encode($this->data);
            exit;
        }

        $xmlInvData = "<![CDATA[<Invoices>" . str_replace("PRODUCT_CONTENT", $strFinal, $invData) . "</Invoices>]]>";

        $config = $this->config->item('VNPT');
        $p_acc = $config['PUBLISH_INV_ID'];
        $p_pwd = $config['PUBLISH_INV_PWD'];
        $srv_acc = $config['SRV_ID'];
        $srv_pwd = $config['SRV_PWD'];

        $inv_pattern = $config['INV_PATTERN'];
        $inv_serial = $config['INV_SERIAL'];

        $isCredit = $this->input->post('isCredit') ? $this->input->post('isCredit') : '0';
        if ($isCredit == '1') {
            $configInvCre = $config['INV_CRE'];
            $inv_pattern = $configInvCre['INV_PATTERN'];
            $inv_serial = $configInvCre['INV_SERIAL'];
        }

        $xmlphrase = <<<EOT
                <ImportAndPublishInv xmlns="http://tempuri.org/">
                    <Account>$p_acc</Account>
                    <ACpass>$p_pwd</ACpass>
                    <xmlInvData>INV_CONTENT</xmlInvData>
                    <username>$srv_acc</username>
                    <password>$srv_pwd</password>
                    <pattern>$inv_pattern</pattern>
                    <serial>$inv_serial</serial>
                    <convert>0</convert>
                </ImportAndPublishInv>
EOT;

        $xmlbody = str_replace("INV_CONTENT", $xmlInvData, $xmlphrase);
        //remove all space between tag
        $xmlbody = preg_replace('/(\>)(\s)+(\<)/', '$1$3', $xmlbody);

        $isSuccess = $this->ccurl("ImportAndPublishInv", "PublishService", $xmlbody);

        if ($isSuccess) {
            $responseContent = $this->getResultData("ImportAndPublishInv");
            $responses = explode(":", $responseContent);
            if (count($responses) > 0) {
                if ($responses[0] == "ERR") {
                    $this->data['error'] = $this->getERR_ImportAndPublish($responses[1]);
                } elseif ($responses[0] == "OK") {
                    $invinfo = explode(";", $responses[1]);

                    if (count($invinfo) > 0) {
                        $this->data['pattern'] = $invinfo[0];
                        $this->data['serial'] = explode("-", $invinfo[1])[0];
                        $this->data['fkey'] = $pincode;
                        $this->data['invno'] = explode("_", $invinfo[1])[1];
                        $this->data['hddt'] = 1; //them moi hd thu sau
                        if ($config['CONFIRM_PAY'] == "1") {
                            if ($this->data['fkey']) {
                                $this->confirmPaymentFkey($this->data['fkey']);
                            }
                        }
                    }
                }
            } else {
                $this->data['error'] = $responseContent;
            }
        }

        echo json_encode($this->data);
        exit;
    }

    public function downloadInvPDF()
    {
        $config = $this->config->item('VNPT');
        $portalURL = $config['PORTAL_URL'];
        $srv_acc = $config['SRV_ID'];
        $srv_pwd = $config['SRV_PWD'];

        $pattern = $this->input->get('pattern') ? $this->input->get('pattern') : "";
        $serial = $this->input->get('serial') ? $this->input->get('serial') : "";
        $number = $this->input->get('number') ? $this->input->get('number') : "";
        $fkey = $this->input->get('fkey') ? $this->input->get('fkey') : "";

        $funcName = $fkey != "" ? "downloadInvPDFFkeyNoPay" : "downloadInvPDFNoPay";
        $tagFindingInfo = $fkey != "" ? "<fkey>$fkey</fkey>" : "<token>$pattern;$serial;$number</token>";
        $xmlcontent = <<<EOT
        <$funcName xmlns="http://tempuri.org/">
          $tagFindingInfo
          <userName>$srv_acc</userName>
          <userPass>$srv_pwd</userPass>
        </$funcName>
EOT;
        $isSuccess = $this->ccurl($funcName, "PortalService", $xmlcontent);
        if ($isSuccess) {
            $responseContent = $this->getResultData($funcName);
            $errContent = "";

            if (strpos($responseContent, 'ERR:') !== false) {
                switch ($responseContent) {
                    case 'ERR:1':
                        $errContent = "Tài khoản đăng nhập sai!";
                        break;
                    case 'ERR:6':
                        $errContent = "Không tìm thấy hóa đơn";
                        break;
                    case 'ERR:7':
                        $errContent = "User name không phù hợp, không tìm thấy company tương ứng cho user.";
                        break;
                    case 'ERR:11':
                        $errContent = "Hóa đơn chưa thanh toán nên không xem được";
                        break;
                    case 'ERR:12':
                        $errContent = "Do lỗi đường truyền hóa đơn chưa được cấp mã cơ quan thuế (CQT), quý khách vui lòng truy cập sau để nhận hóa đơn hoặc truy cập link <a>$portalURL</a> để xem trước hóa đơn chưa có mã";
                        break;
                    case 'ERR:':
                        $errContent = "Lỗi khác!";
                        break;
                    default:
                        $errContent = $responseContent;
                        break;
                }
            }

            if ($errContent != "") {
                echo "<div style='width: 100vw;text-align: center;margin: -8px 0 0 -8px;font-weight: 600;font-size: 27px;color: white;background-color:#614040;line-height: 2;'>" . $errContent . "</div>";
                exit();
            }

            $name = $fkey != "" ? "$fkey.pdf" : "$number.pdf";
            $content = base64_decode($responseContent);
            header('Content-Type: application/pdf');
            header('Content-Length: ' . strlen($content));
            header('Content-disposition: inline; filename="' . $name . '"');
            echo $content;
        } else {
            echo $this->_responseXML;
        }
    }

    public function getInvView()
    {
        $config = $this->config->item('VNPT');
        $portalURL = $config['PORTAL_URL'];
        $srv_acc = $config['SRV_ID'];
        $srv_pwd = $config['SRV_PWD'];

        $pattern = $this->input->get('pattern') ? $this->input->get('pattern') : "";
        $serial = $this->input->get('serial') ? $this->input->get('serial') : "";
        $number = $this->input->get('number') ? $this->input->get('number') : "";
        $fkey = $this->input->get('fkey') ? $this->input->get('fkey') : "";
        $inv = $this->input->get('inv') ? $this->input->get('inv') : "";
        if (!$fkey && $inv) {
            $temp = $this->ceh->select('PinCode')->where("INV_NO LIKE '%$inv'")->order_by('INV_DATE', 'DESC')->get('INV_VAT')->row_array();
            if ($temp === NULL) {
                echo "<div style='width: 100vw;text-align: center;margin: -8px 0 0 -8px;font-weight: 600;font-size: 27px;color: white;background-color:#614040;line-height: 2;'>Không tìm thấy thông tin hoá đơn này!</div>";
                exit();
            }

            $fkey = $temp['PinCode'];
        }

        $funcName = $fkey != "" ? "getInvViewFkeyNoPay" : "getInvViewNoPay";
        $tagFindingInfo = $fkey != "" ? "<fkey>$fkey</fkey>" : "<token>$pattern;$serial;$number</token>";
        $xmlcontent = <<<EOT
        <$funcName xmlns="http://tempuri.org/">
          $tagFindingInfo
          <userName>$srv_acc</userName>
          <userPass>$srv_pwd</userPass>
        </$funcName>
EOT;
        $isSuccess = $this->ccurl($funcName, "PortalService", $xmlcontent);
        if ($isSuccess) {
            $responseContent = $this->getResultData($funcName, 's');

            $errContent = "";
            if (strpos($responseContent, 'ERR:') !== false) {
                switch ($responseContent) {
                    case 'ERR:1':
                        $errContent = "Tài khoản đăng nhập sai!";
                        break;
                    case 'ERR:6':
                        $errContent = "Không tìm thấy hóa đơn";
                        break;
                    case 'ERR:7':
                        $errContent = "User name không phù hợp, không tìm thấy company tương ứng cho user.";
                        break;
                    case 'ERR:11':
                        $errContent = "Hóa đơn chưa thanh toán nên không xem được";
                        break;
                    case 'ERR:12':
                        $errContent = "Do lỗi đường truyền hóa đơn chưa được cấp mã cơ quan thuế (CQT), quý khách vui lòng truy cập sau để nhận hóa đơn hoặc truy cập link <a>$portalURL</a> để xem trước hóa đơn chưa có mã";
                        break;
                    case 'ERR:':
                        $errContent = "Lỗi khác!";
                        break;
                    default:
                        $errContent = $responseContent;
                        break;
                }
            }

            if ($errContent != "") {
                echo "<div style='width: 100vw;text-align: center;margin: -8px 0 0 -8px;font-weight: 600;font-size: 27px;color: white;background-color:#614040;line-height: 2;'>" . $errContent . "</div>";
                exit();
            }

            echo html_entity_decode($responseContent); //html_entity_decode();
            exit();
        } else {
            echo $this->_responseXML;
        }
    }

    public function confirmPayment()
    {
        $fkeys = $this->input->post('fkeys') ? $this->input->post('fkeys') : "";
        $this->confirmPaymentFkey($fkeys);

        echo json_encode($this->data);
        exit;
    }

    public function viewDraftInv()
    {
        $datas = $this->input->post('datas') ? $this->input->post('datas') : array();
        $cusTaxCode = $this->input->post('cusTaxCode') ? $this->input->post('cusTaxCode') : '';
        $cusAddr = $this->input->post('cusAddr') ? htmlspecialchars($this->input->post('cusAddr')) : '';
        $cusName = $this->input->post('cusName') ? htmlspecialchars($this->input->post('cusName')) : '';
        $inv_type = $this->input->post('inv_type') ? $this->input->post('inv_type') : 'VND';
        $roundNum = $this->config->item('ROUND_NUM')[$inv_type]; //ROUND_NUM_QTY_UNIT

        $sum_amount = $this->input->post('sum_amount') != '' ? (float)str_replace(",", "", $this->input->post('sum_amount')) : '';
        $vat_amount = $this->input->post('vat_amount') != '' ? (float)str_replace(",", "", $this->input->post('vat_amount')) : '';
        $total_amount = $this->input->post('total_amount') != '' ? (float)str_replace(",", "", $this->input->post('total_amount')) : '';
        $exchange_rate = $this->input->post('exchange_rate') != '' ? (float)str_replace(",", "", $this->input->post('exchange_rate')) : 1;
        $had_exchange = $this->input->post('had_exchange') ? (int)$this->input->post('had_exchange') : 0;

        $currencyInDetails = isset($datas[0]["CURRENCYID"]) ? $datas[0]["CURRENCYID"] : "VND";
        $paymentMethod = $this->input->post('paymentMethod') ? $this->input->post('paymentMethod') : 'TM/CK';
        $shipInfo = $this->input->post('shipInfo') ? $this->input->post('shipInfo') : '';
        $vat_rate = isset($datas[0]["VAT_RATE"]) && $datas[0]["VAT_RATE"] != ""  ? (float)str_replace(",", "", $datas[0]["VAT_RATE"]) : "";

        $view_exchange_rate = '';
        if ($exchange_rate != 1) {
            $view_exchange_rate = number_format($exchange_rate, $roundNum);
        }

        if ($inv_type == $currencyInDetails || $had_exchange == 1) {
            $exchange_rate = 1;
        }

        $sum_amount = round($sum_amount * $exchange_rate, $roundNum);
        $total_amount = round($total_amount * $exchange_rate, $roundNum);
        $vat_amount = round($vat_amount * $exchange_rate, $roundNum);

        $amount_in_words = $this->funcs->convert_number_to_words($total_amount, $inv_type); //doc tien usd

        // $cusCode = trim($cusTaxCode);
        /* $checkTaxCode = str_replace('-', '', $cusCode);
        if (!in_array(strlen($checkTaxCode), array(10, 13)) || !is_numeric($checkTaxCode)) {
            $cusTaxCode = '';
        }*/

        if ($vat_rate === '') {
            $vat_amount = "";
        }

        $cusName = preg_replace("/[\n\r]/", "", $cusName);
        $cusAddr = preg_replace("/[\n\r]/", "", $cusAddr);

        $tempRowDetails = <<<EOT
            <tr style="height: 30px;">
                <td valign="top">
                <center>1</center>
                </td>
                <td style="padding-left:10px;" valign="top">
                    <div class="ProdData" style="width:400px;">PROD_NAME</div>
                </td>
                <td style="padding-left:10px;" valign="top">
                <div class="ProdData" style="width:87px;">PROD_UNIT</div>
                </td>
                <td style="text-align:right; padding-right:10px;" valign="top">PROD_QTY</td>
                <td style="text-align:right; padding-right: 10px;" valign="top">PROD_PRICE</td>
                <td style="text-align:right; padding-right: 10px;" valign="top">PROD_AMOUNT</td>
            </tr>
EOT;

        //lam tron so luong+don gia theo yeu cau KT
        $roundNumQty_Unit = $this->config->item('ROUND_NUM_QTY_UNIT');
        $prodDetail = '';
        foreach ($datas as $item) { //UNIT_AMT
            if (is_array($item)) {
                $needless = [];
                $temp = $item['TRF_DESC'];

                //add info to UNIT CODE
                $unit = $this->mdltask->getUnitName($item['INV_UNIT']);
                if (in_array(strtoupper($unit), array("CONT", "CNT", "BOX"))) {
                    $sz = isset($item["ISO_SZTP"]) ? $this->getContSize($item["ISO_SZTP"]) : (isset($item["SZ"]) ? $item["SZ"] : '');
                    if ($sz != '') {
                        $temp .= " " . $sz . $item['FE']; //CONT 40F
                    }
                }

                $moreDesc = isset($item['TRF_DESC_MORE']) ? trim($item['TRF_DESC_MORE']) : "";
                $cntrList = isset($item['Remark']) ? trim($item['Remark']) : "";

                if (count(explode(',', $cntrList)) > 5) { //nhieu hon 5cont
                    $temp .= " " . explode('||', $moreDesc)[0];
                    //lenh nang ha + dong rut : $moreDesc = BLNO / BKNO
                    //lenh luu bai + dien lanh : $moreDesc = BLNO / BKNO || UETU5196773: 18/02/2022 14:17:09 - 29/03/2022 14:04|TGBU8763621: ....
                } elseif (strpos($moreDesc, "||") !== false) { //it hon 5cont + ccó chuỗi || -> luubai + dien lanh (desc co dang: BLNO/BKNO || .... ....)
                    $temp .= " (" . $moreDesc . ")";
                } else {
                    $temp .= " " . $cntrList; //nguoc lai su dung list cont
                }

                //encode content of TRF_DESC because it contain <,> ..
                $needless['PROD_NAME'] = (preg_replace("/[\n\r]/", "", $temp));
                $needless['PROD_UNIT'] = ($unit);

                //them moi lam tron so
                $urate = (float)str_replace(",", "", $item['UNIT_RATE']);
                $i_amt = (float)str_replace(",", "", $item['AMOUNT']);

                $needless['PROD_QTY'] = number_format(round($item['QTY'], $roundNumQty_Unit), $roundNumQty_Unit); //lam tron so luong+don gia theo yeu cau KT
                $needless['PROD_PRICE'] = number_format(round($urate * $exchange_rate, $roundNumQty_Unit), $roundNumQty_Unit); //lam tron so luong+don gia theo yeu cau KT
                $needless['PROD_AMOUNT'] = number_format(round($i_amt * $exchange_rate, $roundNum), $roundNum);

                $prodDetail .= $this->strReplaceAssoc($needless, $tempRowDetails);
            }
        }

        if ($prodDetail == '') {
            echo (json_encode([
                'success' => false,
                'message' => 'Không có có dữ liệu chi tiết'
            ]));
            exit;
        }

        $isCredit = $this->input->post('isCredit') ? $this->input->post('isCredit') : '0';
        $config = $this->config->item('VNPT');
        if ($isCredit == '1') {
            $inv_serial = $config['INV_CRE']['INV_SERIAL'];
            $htmlTemplate = 'invoice_temp_a4';
        } else {
            $inv_serial = $config['INV_SERIAL'];
            $htmlTemplate = 'invoice_temp_a5';
        }

        $logoInvPath = FCPATH . "assets/img/logos/temp-inv-logo.png";
        $backgroundInvPath = FCPATH . "assets/img/logos/temp-inv-background.png";

        $logoImgType = pathinfo($logoInvPath, PATHINFO_EXTENSION);
        $logoImgData = file_get_contents($logoInvPath);
        $logoImgBase64 = 'data:image/' . $logoImgType . ';base64,' . base64_encode($logoImgData);

        $bgImgType = pathinfo($backgroundInvPath, PATHINFO_EXTENSION);
        $bgImgData = file_get_contents($backgroundInvPath);
        $bgImgBase64 = 'data:image/' . $bgImgType . ';base64,' . base64_encode($bgImgData);

        $invoiceData = [
            "[TEMP_INV_YARD_NAME]" => $this->config->item('YARD_FULL_NAME'),
            "[TEMP_INV_YARD_NAME_ENG]" => $this->config->item('YARD_NAME_ENG'),
            "[TEMP_INV_YARD_TAXCODE]" => $this->config->item('YARD_TAX_CODE'),
            "[TEMP_INV_YARD_ADDR]" => $this->config->item('YARD_ADDRESS'),
            "[TEMP_INV_YARD_TEL]" => $this->config->item('YARD_HOT_LINE'),
            "[TEMP_INV_YARD_FAX]" => $this->config->item('YARD_FAX'),
            "[TEMP_INV_YARD_BANK]" => $this->config->item('YARD_BANK_INFO'),
            "[TEMP_INV_SERIAL]" => $inv_serial,
            "[TEMP_INV_DAY]" => date('d'),
            "[TEMP_INV_MONTH]" => date('m'),
            "[TEMP_INV_YEAR]" => date('Y'),
            "[TEMP_INV_CUS_NAME]" => ($cusName),
            "[TEMP_INV_CUS_ADDR]" => $cusAddr,
            "[TEMP_INV_CUS_TAXCODE]" => $cusTaxCode,
            "[TEMP_INV_PAY_METHOD]" => $paymentMethod,
            "[TEMP_INV_CURRENCY]" => $currencyInDetails,
            "[TEMP_INV_AMT]" => number_format($sum_amount, $roundNum),
            "[TEMP_INV_VAT_RATE]" => number_format($vat_rate, $roundNum),
            "[TEMP_INV_VAT_AMT]" => number_format($vat_amount, $roundNum),
            "[TEMP_INV_TAMT]" => number_format($total_amount, $roundNum),
            "[TEMP_INV_IN_WORDS]" => $amount_in_words,
            "[TEMP_INV_EXCHANGE_RATE]" => $view_exchange_rate,
            "[TEMP_INV_PINCODE]" => "_________________",
            "[TEMP_INV_LOGO]" => $logoImgBase64,
            "[TEMP_INV_TABLE_BACKGROUND]" => $bgImgBase64,
            "[TEMP_INV_SHIPINFO]" => $shipInfo
        ];

        $invTemplate = $this->load->view("print_file/$htmlTemplate", NULL, TRUE);
        $invoiceData['[TEMP_INV_PROD_DETAIL]'] = $prodDetail;
        $finalStrInvoiceHtml = $this->strReplaceAssoc($invoiceData, $invTemplate);

        echo (json_encode([
            'success' => true,
            'html' => $finalStrInvoiceHtml
        ]));
        exit;
    }

    //business
    public function adjustInvoice()
    {
        $datas = $this->input->post('datas') ? $this->input->post('datas') : array();
        $cusTaxCode = $this->input->post('cusTaxCode') ? $this->input->post('cusTaxCode') : "";
        $cusAddr = $this->input->post('cusAddr') ? htmlspecialchars($this->input->post('cusAddr')) : "";
        $cusName = $this->input->post('cusName') ? htmlspecialchars($this->input->post('cusName')) : "";
        $inv_type = $this->input->post('inv_type') ? $this->input->post('inv_type') : 'VND';
        $roundNum = $this->config->item('ROUND_NUM')[$inv_type]; //ROUND_NUM_QTY_UNIT

        $sum_amount = $this->input->post('sum_amount') != "" ? (float)str_replace(",", "", $this->input->post('sum_amount')) : "";
        $vat_amount = $this->input->post('vat_amount') != "" ? (float)str_replace(",", "", $this->input->post('vat_amount')) : "";
        $total_amount = $this->input->post('total_amount') != "" ? (float)str_replace(",", "", $this->input->post('total_amount')) : "";
        $exchange_rate = $this->input->post('exchange_rate') != "" ? (float)str_replace(",", "", $this->input->post('exchange_rate')) : 1;
        $had_exchange = $this->input->post('had_exchange') ? (int)$this->input->post('had_exchange') : 0;

        $currencyInDetails = isset($datas[0]["CURRENCYID"]) ? $datas[0]["CURRENCYID"] : "VND";
        $paymentMethod = $this->input->post('paymentMethod') ? $this->input->post('paymentMethod') : 'TM/CK';
        $shipInfo = $this->input->post('shipInfo') ? $this->input->post('shipInfo') : "";
        $vat_rate = isset($datas[0]["VAT_RATE"]) && $datas[0]["VAT_RATE"] != ""  ? (float)str_replace(",", "", $datas[0]["VAT_RATE"]) : "";

        $old_pincode = $this->input->post('old_pincode');
        $isCredit = $this->input->post('isCredit') ? $this->input->post('isCredit') : '0';
        $adjust_type = $this->input->post('adjust_type');

        $view_exchange_rate = "";
        if ($exchange_rate != 1) {
            $view_exchange_rate = $exchange_rate;
        }

        if ($inv_type == $currencyInDetails || $had_exchange == 1) {
            $exchange_rate = 1;
        }

        $sum_amount = round($sum_amount * $exchange_rate, $roundNum);
        $total_amount = round($total_amount * $exchange_rate, $roundNum);
        $vat_amount = round($vat_amount * $exchange_rate, $roundNum);

        $amount_in_words = $this->funcs->convert_number_to_words($total_amount, $inv_type); //doc tien usd
        $amount_in_words = htmlspecialchars($amount_in_words);

        $pincode = $this->mdltask->generatePinCode();

        $cusCode = trim($cusTaxCode);
        if ($vat_rate === "") {
            $vat_rate = "-1";
            $vat_amount = "-1";
        }

        $cusName = preg_replace("/[\n\r]/", "", $cusName);
        $cusAddr = preg_replace("/[\n\r]/", "", $cusAddr);

        if ($adjust_type == '1') {
            $type = "";
            $mainTagXML = 'ReplaceInv';
            $function = 'ReplaceInvoiceAction';
        } else {
            $type = "<Type>$adjust_type</Type>";
            $mainTagXML = 'AdjustInv';
            $function = 'AdjustInvoiceAction';
        }

        $invData = <<<EOT
            <$mainTagXML>
                <key>$pincode</key>
                <CusCode>$cusCode</CusCode>
                <CusName>$cusName</CusName>
                <CusAddress>$cusAddr</CusAddress>
                <CusPhone></CusPhone>
                <CusTaxCode>$cusTaxCode</CusTaxCode>
                <PaymentMethod>$paymentMethod</PaymentMethod>
                <KindOfService></KindOfService>
                <ShipName>$shipInfo</ShipName>
                <Products>
                    PRODUCT_CONTENT
                </Products>
                <Total>$sum_amount</Total>
                <VATRate>$vat_rate</VATRate>
                <VATAmount>$vat_amount</VATAmount>
                <Amount>$total_amount</Amount>
                <AmountInWords>$amount_in_words</AmountInWords>
                <DiscountAmount>$exchange_rate</DiscountAmount>
                <CurrencyUnit>$inv_type</CurrencyUnit>
                <Extra>$pincode</Extra>
                <ExchangeRate>$view_exchange_rate</ExchangeRate>
                $type
            </$mainTagXML>
EOT;

        $product_content = <<<EOT
            <Product>
                <ProdName>TRF_DESC</ProdName>
                <ProdUnit>INV_UNIT</ProdUnit>
                <ProdQuantity>QTY</ProdQuantity>
                <ProdPrice>UNIT_RATE</ProdPrice>
                <Total>AMT</Total>
            </Product>
EOT;
        // <Total>AMT</Total>

        //lam tron so luong+don gia theo yeu cau KT
        $roundNumQty_Unit = $this->config->item('ROUND_NUM_QTY_UNIT');
        $strFinal = "";
        foreach ($datas as $item) { //UNIT_AMT
            if (is_array($item)) {
                $temp = $item['TRF_DESC'];

                $unit = $this->mdltask->getUnitName($item['INV_UNIT']);
                if (in_array(strtoupper($unit), array("CONT", "CNT", "BOX"))) {
                    $sz = isset($item["ISO_SZTP"]) ? $this->getContSize($item["ISO_SZTP"]) : (isset($item["SZ"]) ? $item["SZ"] : "");
                    if ($sz != "") {
                        $temp .= " " . $sz . $item['FE']; //CONT 40F
                    }
                }

                $moreDesc = isset($item['TRF_DESC_MORE']) ? trim($item['TRF_DESC_MORE']) : "";
                $cntrList = isset($item['Remark']) ? trim($item['Remark']) : "";

                if (count(explode(',', $cntrList)) > 5) { //nhieu hon 5cont
                    $temp .= " " . explode('||', $moreDesc)[0];
                    //lenh nang ha + dong rut : $moreDesc = BLNO / BKNO
                    //lenh luu bai + dien lanh : $moreDesc = BLNO / BKNO || UETU5196773: 18/02/2022 14:17:09 - 29/03/2022 14:04|TGBU8763621: ....
                } elseif (strpos($moreDesc, "||") !== false) { //it hon 5cont + ccó chuỗi || -> luubai + dien lanh (desc co dang: BLNO/BKNO || .... ....)
                    $temp .= " (" . $moreDesc . ")";
                } else {
                    $temp .= " " . $cntrList; //nguoc lai su dung list cont
                }

                //encode content of TRF_DESC because it contain <,> ..
                $item['TRF_DESC'] = htmlspecialchars(preg_replace("/[\n\r]/", "", $temp));
                //add info to UNIT CODE
                $item['INV_UNIT'] = htmlspecialchars($unit);

                //them moi lam tron so
                $urate = (float)str_replace(",", "", $item['UNIT_RATE']);
                $i_amt = (float)str_replace(",", "", $item['AMOUNT']);

                $item['QTY'] = round($item['QTY'], $roundNumQty_Unit); //lam tron so luong+don gia theo yeu cau KT
                $item['UNIT_RATE'] = round($urate * $exchange_rate, $roundNumQty_Unit); //lam tron so luong+don gia theo yeu cau KT

                $item['AMT'] = round($i_amt * $exchange_rate, $roundNum);

                unset($item['AMOUNT']);
                unset($item['SZ']);
                $strFinal .= $this->strReplaceAssoc($item, $product_content);
            }
        }

        if ($strFinal == "") {
            $this->data['results'] = "nothing to adjust";
            echo json_encode($this->data);
            exit;
        }

        $xmlInvData = "<![CDATA[" . str_replace("PRODUCT_CONTENT", $strFinal, $invData) . "]]>";

        $config = $this->config->item('VNPT');
        $p_acc = $config['PUBLISH_INV_ID'];
        $p_pwd = $config['PUBLISH_INV_PWD'];

        $srv_acc = $config['SRV_ID'];
        $srv_pwd = $config['SRV_PWD'];

        if ($isCredit == '1') {
            $configInvCre = $config['INV_CRE'];
            $inv_pattern = $configInvCre['INV_PATTERN'];
            $inv_serial = $configInvCre['INV_SERIAL'];
        } else {
            $inv_pattern = $config['INV_PATTERN'];
            $inv_serial = $config['INV_SERIAL'];
        }

        $xmlphrase = <<<EOT
            <$function xmlns="http://tempuri.org/">
                <Account>$p_acc</Account>
                <ACpass>$p_pwd</ACpass>
                <xmlInvData>INV_CONTENT</xmlInvData>
                <username>$srv_acc</username>
                <pass>$srv_pwd</pass>
                <fkey>$old_pincode</fkey>
                <convert>0</convert>
                <pattern>$inv_pattern</pattern>
                <serial>$inv_serial</serial>
            </$function>
EOT;

        $xmlbody = str_replace("INV_CONTENT", $xmlInvData, $xmlphrase);
        //remove all space between tag
        $xmlbody = preg_replace('/(\>)(\s)+(\<)/', '$1$3', $xmlbody);

        $isSuccess = $this->ccurl($function, "BusinessService", $xmlbody);

        if ($isSuccess) {
            $responseContent = $this->getResultData($function);
            $responses = explode(":", $responseContent);
            if (count($responses) > 0) {
                if ($responses[0] == "ERR") {
                    $this->data['error'] = $this->getERR_AdjustInvoice($responses[1]);
                } elseif ($responses[0] == "OK") {
                    $invinfo = explode(";", $responses[1]);

                    if (count($invinfo) > 0) {
                        //1/002;C22TIN;TOS22122612499_277
                        $this->data['pattern'] = $invinfo[0];
                        $this->data['serial'] = $invinfo[1];
                        $this->data['fkey'] = $pincode;
                        $this->data['invno'] = explode("_", $invinfo[2])[1];
                        $this->data['hddt'] = 1; //them moi hd thu sau
                    }
                }
            } else {
                $this->data['error'] = $responseContent;
            }
        }

        echo json_encode($this->data);
        exit;
    }

    public function cancelInv()
    {
        $fkey = $this->input->post('fkey') ? $this->input->post('fkey') : "";
        $config = $this->config->item('VNPT');
        $p_acc = $config['PUBLISH_INV_ID'];
        $p_pwd = $config['PUBLISH_INV_PWD'];
        $srv_acc = $config['SRV_ID'];
        $srv_pwd = $config['SRV_PWD'];

        if ($config['CONFIRM_PAY'] == "1") {
            // bỏ gạch nợ hóa đơn trước khi hủy hóa đơn đó
            $isUnConfirm = $this->unConfirmPaymentFkey($fkey, $srv_acc, $srv_pwd);
            if (!$isUnConfirm) {
                echo json_encode($this->data);
                exit;
            }
        }

        $xmlcontent = <<<EOT
        <cancelInv xmlns="http://tempuri.org/">
            <Account>$p_acc</Account>
            <ACpass>$p_pwd</ACpass>
            <fkey>$fkey</fkey>
            <userName>$srv_acc</userName>
            <userPass>$srv_pwd</userPass>
        </cancelInv>
EOT;
        $isSuccess = $this->ccurl("cancelInv", "BusinessService", $xmlcontent);

        if ($isSuccess) {
            $responseContent = $this->getResultData("cancelInv");
            $responses = explode(":", $responseContent);

            if (count($responses) > 0) {
                if ($responses[0] == "ERR") {
                    $this->data['error'] = $this->getERR_CancelInv($responses[1]);
                } else {
                    $this->data['success'] = true;
                }
            } else {
                $this->data['error'] = $responseContent;
            }
        }

        echo json_encode($this->data);
        exit;
    }

    public function confirmPaymentFkey($fkeys)
    {
        $config = $this->config->item('VNPT');
        $srv_acc = $config['SRV_ID'];
        $srv_pwd = $config['SRV_PWD'];

        $strfkey = is_array($fkeys) ? implode("_", $fkeys) : $fkeys;
        $xmlphrase = <<<EOT
                <confirmPaymentFkey xmlns="http://tempuri.org/">
                  <lstFkey>$strfkey</lstFkey>
                  <userName>$srv_acc</userName>
                  <userPass>$srv_pwd</userPass>
                </confirmPaymentFkey>
EOT;
        $isSuccess = $this->ccurl("confirmPaymentFkey", "BusinessService", $xmlphrase);
        if ($isSuccess) {
            $responseContent = $this->getResultData("confirmPaymentFkey");
            if (strpos($responseContent, 'ERR') != false) {
                $this->data['error'] = $this->getERR_ConfirmPaymentFkey(explode(":", $responseContent)[1]);
            } elseif (strpos($responseContent, 'OK') != false) {
                $this->data['error'] = $responseContent;
            }
        }
    }

    private function unConfirmPaymentFkey($fkeys, $srv_acc, $srv_pwd)
    {
        $strfkey = is_array($fkeys) ? implode("_", $fkeys) : $fkeys;
        $xmlphrase = <<<EOT
                <UnConfirmPaymentFkey xmlns="http://tempuri.org/">
                  <lstFkey>$strfkey</lstFkey>
                  <userName>$srv_acc</userName>
                  <userPass>$srv_pwd</userPass>
                </UnConfirmPaymentFkey>
EOT;
        $isSuccess = $this->ccurl("UnConfirmPaymentFkey", "BusinessService", $xmlphrase);

        $resultString = "";
        if ($isSuccess) {
            $responseContent = $this->getResultData("UnConfirmPaymentFkey");

            $errContent = "";
            switch ($responseContent) {
                case "ERR:1":
                    $errContent = "Tài khoản đăng nhập sai";
                    break;
                case "ERR:6":
                    $errContent = "Không tìm thấy hóa đơn tương ứng chuỗi đưa vào";
                    break;
                case "ERR:7":
                    $errContent = "Không bỏ gạch nợ được";
                    break;
            }
        }

        if ($resultString != "") {
            $this->data["error"] = $resultString;
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function updateCustomer()
    {
        $cusDatas = $this->input->post('data') ? $this->input->post('data') : array();
        $config = $this->config->item('VNPT');
        $srv_acc = $config['SRV_ID'];
        $srv_pwd = $config['SRV_PWD'];

        $xmlphrase = <<<EOT
                <UpdateCus xmlns="http://tempuri.org/">
                    <XMLCusData>CUS_DATA</XMLCusData>
                    <username>$srv_acc</username>
                    <pass>$srv_pwd</pass>
                    <convert>0</convert>
                </UpdateCus>
EOT;
        $cusXml = <<<EOT
                <Customer>
                    <Name>CUS_NAME</Name>
                    <Code>CUS_CODE</Code>
                    <TaxCode>CUS_TAXCODE</TaxCode>
                    <Address>CUS_ADDRESS</Address>
                    <Email>CUS_EMAIL</Email>
                    <Fax>CUS_FAX</Fax>
                    <Phone>CUS_TEL</Phone>
                    <CusType>0</CusType>
                </Customer>
EOT;
        $xmlcusTemp = "";
        foreach ($cusDatas as $key => $cus) {
            $taxCode = str_replace('-', "", trim($cus['VAT_CD']));
            if (in_array(strlen($taxCode), array(10, 13)) && is_numeric($taxCode)) {
                $taxCode = "";
            }

            $cusVal = array(
                'CUS_NAME' => htmlspecialchars($cus['CusName']),
                'CUS_CODE' => $cus['CusID'],
                'CUS_TAXCODE' => $taxCode,
                'CUS_ADDRESS' => htmlspecialchars($cus['Address']),
                'CUS_EMAIL' => $cus['Email'],
                'CUS_FAX' => $cus['Fax'],
                'CUS_TEL' => $cus['Tel'],
            );

            $xmlcusTemp .= $this->strReplaceAssoc($cusVal, $cusXml);
        }

        $xmlCusData = "<![CDATA[<Customers>" . $xmlcusTemp . "</Customers>]]>";
        $xmlbody = str_replace("CUS_DATA", $xmlCusData, $xmlphrase);

        $isSuccess = $this->ccurl("UpdateCus", "PublishService", $xmlbody);
        $errContent = "";
        $successMessage = "";
        if ($isSuccess) {
            $responseContent = $this->getResultData("UpdateCus");

            switch ($responseContent) {
                case "-1":
                    $errContent = "Tài khoản đăng nhập sai hoặc không có quyền";
                    break;
                case "-2":
                    $errContent = "Không import được khách hàng vào db";
                    break;
                case "-3":
                    $errContent = "Dữ liệu xml đầu vào không đúng quy định";
                    break;
                case "-5":
                    $errContent = "Có khách hàng đã tồn tại";
                    break;
                default:
                    if (is_numeric($responseContent) && (int)$responseContent > 0) {
                        $successMessage = "$responseContent khách hàng đã import và update";
                    } else {
                        $errContent = $responseContent;
                    }

                    break;
            }
        }

        if ($errContent != "") {
            $this->data["error"] = $errContent;
            $this->data["success"] = FALSE;
        } else {
            $this->data["message"] = $successMessage;
            $this->data["success"] = TRUE;
        }

        echo json_encode($this->data);
        exit;
    }

    private function getResultData($funcname, $regexType = "")
    {
        if (!$this->_responseXML || $this->_responseXML == "") {
            return "";
        }
        $funcresult = $funcname . "Result";
        $regx = <<<EOT
/\<$funcresult\>(.*)\<\/$funcresult\>/$regexType
EOT;

        preg_match($regx, $this->_responseXML, $result);
        return count($result) > 1 ? $result[1] : "";
    }
    private function getERR_ImportAndPublish($errnumber)
    {
        $result = "";
        switch ($errnumber) {
            case "1":
                $result = "Tài khoản đăng nhập sai hoặc không có quyền thêm khách hàng";
                break;
            case "3":
                $result = "Dữ liệu xml đầu vào không đúng quy định";
                break;
            case "7":
                $result = "User name không phù hợp, không tìm thấy company tương ứng cho user.";
                break;
            case "20":
                $result = "Pattern và serial không phù hợp, hoặc không tồn tại hóa đơn đã đăng kí có sử dụng Pattern và serial truyền vào";
                break;
            case "5":
                $result = "Không phát hành được hóa đơn.";
                break;
            case "10":
                $result = "Lô có số hóa đơn vượt quá max cho phép";
                break;
            default:
                $result = "Lỗi phát hành hoá đơn, mã lỗi: " . $errnumber;
                break;
        }
        return $result;
    }

    private function getERR_AdjustInvoice($errnumber)
    {
        $result = "";
        switch ($errnumber) {
            case "1":
                $result = "Tài khoản đăng nhập sai hoặc không có quyền thêm khách hàng";
                break;
            case "2":
                $result = "Hóa đơn cần điều chỉnh không tồn tại";
                break;
            case "3":
                $result = "Dữ liệu xml đầu vào không đúng quy định";
                break;
            case "5":
                $result = "Không phát hành được hóa đơn";
                break;
            case "6":
                $result = "Dải hóa đơn cũ đã hết";
                break;
            case "7":
                $result = "User name không phù hợp, không tìm thấy company tương ứng cho user.";
                break;
            case "8":
                $result = "Hóa đơn cần điều chỉnh đã bị thay thế. Không thể điều chỉnh được nữa.";
                break;
            case "9":
                $result = "Trạng thái hóa đơn không được điều chỉnh";
                break;
            case "13":
                $result = "Fkey của hóa đơn mới đã tồn tại trên hệ thống";
                break;
            case "14":
                $result = "Lỗi trong quá trình thực hiện cấp số hóa đơn";
                break;
            case "15":
                $result = "Lỗi khi thực hiện Deserialize chuỗi hóa đơn đầu vào";
                break;
            case "19":
                $result = "Pattern truyền vào không giống với hóa đơn cần điều chỉnh";
                break;
            case "20":
                $result = "Dải hóa đơn hết, User/Account không có quyền với Serial/Pattern và serial không phù hợp";
                break;
            case "29":
                $result = "Lỗi chứng thư hết hạn";
                break;
            case "30":
                $result = "Danh sách hóa đơn tồn tại ngày hóa đơn nhỏ hơn ngày hóa đơn đã phát hành";
                break;
            default:
                $result = "Lỗi phát hành hoá đơn, mã lỗi: " . $errnumber;
                break;
        }
        return $result;
    }

    private function getERR_ConfirmPaymentFkey($errnumber)
    {
        $result = "";
        switch ($errnumber) {
            case "1":
                $result = "Tài khoản đăng nhập sai";
                break;
            case "6":
                $result = "Không tìm thấy hóa đơn tương ứng chuỗi đưa vào";
                break;
            case "7":
                $result = "Không gạch nợ được";
                break;
            case "13":
                $result = "Hóa đơn đã được gạch nợ";
                break;
            default:
                $result = "[$errnumber] Unknown error";
                break;
        }
        return $result;
    }

    private function getERR_CancelInv($errnumber)
    {
        $result = "";

        switch ($errnumber) {
            case "1":
                $result = "Tài khoản đăng nhập sai";
                break;
            case "2":
                $result = "Không tồn tại hóa đơn cần hủy";
                break;
            case "8":
                $result = "Hóa đơn đã được thay thế rồi, hủy rồi";
                break;
            case "9":
                $result = "Trạng thái hóa đơn ko được hủy";
                break;
            default:
                $result = "[$errnumber] Unknown error";
                break;
        }

        return $result;
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
