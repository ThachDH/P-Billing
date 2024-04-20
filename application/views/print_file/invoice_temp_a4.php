<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<div>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>VAT</title>
	<div style="display:none"> if (lt IE 9)&lt;script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"&gt;&lt;/script&gt;</div>
	<style type="text/css" rel="stylesheet">
		html,
		body {
			margin: 0;
			padding: 0;
			height: 100%;
		}

		@page {
			size: 21cm 29.7cm;
			margin: 5px 5px 0px 5px;
		}

		.VATTEMP {
			font-family: Arial;
			width: 930px;
			overflow: hidden;
		}

		.VATTEMP:first-child {
			page-break-before: avoid;
		}

		.number {
			color: red;
			font-size: 20px;
			font-family: Arial, Helvetica, sans-serif;
		}

		.detail-text .left-label {
			float: left;
			margin-right: 10px;
		}

		.detail-text .right-label {
			overflow: hidden;
			border-bottom: 1px dotted #000;
		}

		.detail-text .right-label-no-border {
			overflow: hidden;
			border-bottom: none;
			min-height: 15px;
		}

		.bgimg {
			border: 1px solid !important;
			cursor: pointer
		}

		.bgimg p {
			color: #f22121;
			padding-left: 10px;
			font-size: 14px;
			text-align: left
		}

		.LogoImage {
			height: 99px;
			width: 145px;
			background: url("[TEMP_INV_LOGO]") no-repeat;
			position: absolute;
		}

		.InvoiceTemplate {
			position: relative;
		}

		.InvHeader {
			width: 100%;
			float: left;
		}

		.LeftHeader {
			padding-left: 0px;
			float: left;
			width: 735px;
		}

		.RightHeader {
			overflow: hidden;
		}

		.CompanyInfo label {
			font-size: 14px;
		}

		.InvTitle h2 {
			margin: 0px;
			color: #0D47A1;
			font-size: 20px;
		}

		.InvTitle h3 {
			margin: 0px;
		}

		.InvTitle h3 i {
			margin: 0px;
			color: #0D47A1;
			font-size: 18px;
		}

		.InvTitle i {
			margin: 0px;
			font-size: 12px;
		}

		.InvTitle {
			padding-top: 3px;
			width: 100%;
			float: left;
		}

		.ProductTable {
			overflow: hidden;
			position: relative;
			width: 100%;
			float: left;
		}

		.ProductTable:after {
			content: ' ';
			display: block;
			position: absolute;
			z-index: 1;
			opacity: 0.2;
			background-image: url('[TEMP_INV_TABLE_BACKGROUND]');
			width: 600px;
			height: 355px;
			left: 160px;
			top: 85px;
			background-repeat: no-repeat;
			background-position: center top;
			-webkit-background-size: cover;
			background-size: contain;
		}

		.ProductTable table {
			margin-top: 10px;
			border-collapse: collapse;
			border-bottom: 1px solid;
			z-index: 2;
			position: relative;
		}

		.ProductTable th {
			border: 1px solid #000;
			text-align: center;
		}

		.ProductTable td {
			border-left: 1px solid #000;
			border-right: 1px solid #000;
			z-index: 1;
		}

		.PaymentBox {
			width: 880px;
			float: left;
			position: relative
		}

		.PaymentBox_Left {
			float: left;
			width: 80%;
			position: relative
		}

		.PaymentBox_Right {
			float: right;
			width: 76%;
			position: relative
		}

		.Bottom {
			width: 880px;
			position: relative;
		}

		.SignatureBox {
			padding-top: 5px;
			font-size: 12px !important;
			width: 100%;
			float: left;
		}

		.SignatureBox #Left {
			float: left;
			width: 220px;
		}

		.SignatureBox #Middle {
			float: left;
			overflow: hidden;
			width: 345px;
		}

		.SignatureBox #Right {
			overflow: hidden;
			width: 335px;
		}

		.CustomerInfo {
			padding-top: 3px;
			width: 100%;
			float: left;
		}

		#SearchInvUrl {
			text-align: left;
			line-height: 3px;
			padding-top: 0px;
			width: 100%;
			float: left;
		}

		.ProdData {
			position: relative;
			word-wrap: break-word;
			float: left;
			text-align: left;
		}

		#VnptInfo {
			width: 100%;
			float: left;
		}

		.ConvertBox {
			width: 100%;
			float: left;
		}

		#ViewInvoice {
			height: 1309px !important;
			width: 965px;
			box-sizing: border-box;
		}

		#inbt {
			margin-top: 0px !important;
		}

		.VATTEMP div label.fl-l,
		div label {
			margin-right: 0;
			margin-top: 3px;

		}
	</style>
	<script>
		var ua = window.navigator.userAgent;
		var msie = ua.indexOf("MSIE 8");
		var activePage = 1;
		var totalPage = 0;
		var pageCount = 0;

		function ShowCurrentPage() {
			totalPage = $('.VATTEMP').length; // draw controls showPaginationBar(totalPage);	pageCount =totalPage; // show first page showPageContent(1); }; function showPaginationBar (numPages) { var pagins = ''; for (var i = 1; i <= numPages; i++) { $($('.VATTEMP')[i-1]).hide(); pagins += '<span class="number" id="ap' + i + '" onclick="showPageContent(' + i + '); return false;">' + i + '</span>'; }	 $('.pagination span:first-child').after(pagins);	$('#prev').click(function () { if (activePage > 1) showPageContent(activePage - 1); }); // and Next $('#next').click(function () { if (activePage < pageCount) showPageContent(activePage + 1); }); }; function showPageContent (page) {  $($('.VATTEMP')[activePage-1]).hide();  $($('.VATTEMP')[page-1]).show(); $(".number").removeClass("selected"); $("#ap" + page).addClass("selected"); activePage = page;         
		};
	</script>
	<script>
		function displayCert(serialCert) {
			plugin().ShowCertInfo(serialCert);
		}
	</script>

	<div id="container">
		<div class="VATTEMP">
			<div id="PrintView" style="width:900px; margin:auto; padding-bottom:10px;height:auto!important">
				<div class="MiddleView">
					<div class="InvoiceTemplate">
						<div class="InvHeader">
							<div class="LeftHeader">
								<div class="LogoImage" style="width:145px"></div>
								<div style="margin-left:148px;text-align:left;width:645px">
									<div style="font-size:18px; text-transform:uppercase; padding-top:3px;padding-bottom:10px;">
										<b style="color:#0D47A1; font-size:20px;">[TEMP_INV_YARD_NAME]<br /></b>
										<b style="color:#0D47A1; font-size:18px;">([TEMP_INV_YARD_NAME_ENG])</b>
									</div>
									<div>
										<label style="padding-top:20px;font-size:10px">
											Mã số thuế <i>(Tax code)</i>: <b style="letter-spacing: 5px;">[TEMP_INV_YARD_TAXCODE]</b>
										</label>
										<br />
										<label style="height:20px;font-size:10px">
											Địa chỉ <i>(Address)</i>: [TEMP_INV_YARD_ADDR]
										</label>
										<br />
										<label style="height:20px; ;padding-bottom:15px;font-size:10px">
											Điện thoại <i>(Tel)</i>: [TEMP_INV_YARD_TEL]
										</label>
										<label style="margin-left:25px;font-size:10px">Fax: [TEMP_INV_YARD_FAX]</label>
										<label style="margin-left:25px;font-size:10px"><br /></label>
										<label style="font-size:9.5px;width:645px;">
											Số tài khoản <i>(A/C No.)</i>: [TEMP_INV_YARD_BANK]
										</label>
									</div>
								</div>
							</div>
							<div class="RightHeader">
								<div class="detail-text" style="float:left; padding-bottom:5px; width:145px;font-size:12px">
									<div class="left-label">Ký hiệu:</div>
									<div><b>[TEMP_INV_SERIAL]</b></div>
								</div>
								<div class="detail-text" style="float:left; width:145px">
									<div class="left-label" style="padding-top:7px;font-size:12px">Số:</div>
									<div class="right-label-no-border" style="font-size:18px; color:red">
										<b><span class="number" style="color:#FA5858;;padding-top:0px">0000000</span></b>
									</div>
								</div>
							</div>
						</div>
						<div class="InvTitle">
							<hr style="height:1px;background-color:#000;" />
							<center>
								<h2 style="text-transform:uppercase;"><b>HÓA ĐƠN GIÁ TRỊ GIA TĂNG (BẢN NHÁP) </b></h2>
								<h3 style="text-transform:uppercase;margin-bottom:10px">
									<i>VAT INVOICE (DRAFT)</i>
								</h3>
								<i>
									Ngày (date) <label style="border-bottom: 1px dotted #000;">[TEMP_INV_DAY]</label>
									tháng (month) <label style="border-bottom: 1px dotted #000;">[TEMP_INV_MONTH]</label>
									năm (year) <label style="border-bottom: 1px dotted #000;">[TEMP_INV_YEAR]</label>
									<center style="font-style: italic;">
										<p style="margin-bottom: 3px; margin-top: 3px;"><b>Mã của cơ quan thuế:</b>
											<b style="border-bottom: 1px dotted #000;padding-right: 185px"></b>
										</p>
									</center>
								</i>
							</center>
						</div>
						<div class="CustomerInfo">
							<div class="detail-text" style="float: left; min-width:550px;padding-bottom:3px;padding-top:10px;">
								<div class="left-label">Họ tên người mua hàng <i>(Buyer)</i>:</div>
								<div class="right-label">&nbsp;&nbsp;</div>
							</div>
							<div class="detail-text" style="padding-bottom:3px;;padding-top:10px;">
								<div class="left-label">Điện thoại <i>(Tel)</i>:</div>
								<div class="right-label">&nbsp;&nbsp;</div>
							</div>
							<div class="detail-text" style="padding-bottom:3px;padding-top:3px;">
								<div class="left-label">Tên đơn vị <i>(Company)</i>:</div>
								<div class="right-label">[TEMP_INV_CUS_NAME]</div>
							</div>
							<div style="padding-bottom:3px;padding-top:3px;">
								<div class="detail-text">
									<div class="left-label">Địa chỉ <i>(Address)</i>:</div>
									<div class="right-label">[TEMP_INV_CUS_ADDR]</div>
								</div>
							</div>
							<div class="detail-text" style="float: left; min-width:450px;padding-bottom:3px;padding-top:3px;">
								<div class="left-label">Mã số thuế <i>(Tax code)</i>:</div>
								<div class="right-label">[TEMP_INV_CUS_TAXCODE]</div>
							</div>
							<div class="detail-text" style="padding-bottom:3px;padding-top:3px;">
								<div class="left-label">Hình thức thanh toán <i>(Payment method)</i>:</div>
								<div class="right-label">&ensp;[TEMP_INV_PAY_METHOD]</div>
							</div>
							<div class="detail-text" style="float:Left; width: 50%;padding-bottom:3px;">
								<div class="left-label">Tỷ giá <i>(Exchange Rate)</i>:</div>
								<div class="right-label">&ensp;[TEMP_INV_EXCHANGE_RATE]</div>
							</div>
							<div class="detail-text" style="overflow: hidden; width:50%; padding-bottom:3px;">
								<div class="left-label">Đơn vị tiền tệ <i>(Currency Unit)</i>:</div>
								<div class="right-label">&ensp;[TEMP_INV_CURRENCY]</div>
							</div>
							<div class="detail-text" style="padding-bottom:3px;;padding-top:3px;">
								<div class="left-label">Tên tàu/Chuyến <i>(Name of vessel/Voyage's number)</i>:</div>
								<div class="right-label">&ensp;[TEMP_INV_SHIPINFO]</div>
							</div>
						</div>
						<div class="ProductTable">
							<table width="100%" style="height:450px; font-size:12px;">
								<tbody>
									<tr>
										<th width="5%  !important" height="35px">STT <br /><i>(No.)</i></th>
										<th width="45% !important" style="text-transform:none">Tên hàng hóa, dịch vụ <br /><i>(Description)</i></th>
										<th width="10%  !important" style="text-transform:none">Đơn vị tính <br /><i>(Unit)</i></th>
										<th width="10%  !important" style="text-transform:none">Số lượng <br /><i>(Quantity)</i></th>
										<th width="13% !important" style="text-transform:none">Đơn giá <br /><i>(Unit Price)</i></th>
										<th width="17% !important" style="text-transform:none;">Thành tiền <br /><i>(Amount)</i></th>
									</tr>
									<tr>
										<th width="5%  !important" height="20px">1</th>
										<th width="45% !important" style="text-transform:none">2</th>
										<th width="10%  !important" style="text-transform:none">3</th>
										<th width="10%  !important" style="text-transform:none">4</th>
										<th width="13% !important" style="text-transform:none">5</th>
										<th width="17% !important" style="text-transform:none;">6 = 4 x 5</th>
									</tr>
									<tr style="height:2px">
										<td style="border-right:1px solid #000;" />
										<td style="text-align:left; padding-left:10px;">
											<div style="height:auto"><label /></div>
										</td>
										<td style="border-right:1px solid #000;"> </td>
										<td style="border-right:1px solid #000;"> </td>
										<td style="border-right:1px solid #000;"> </td>
										<td></td>
									</tr>
									[TEMP_INV_PROD_DETAIL]
									<tr>
										<td valign="top"></td>
										<td>
											<div style="width:100%;margin-left:243px;text-align:center">
												<label style="width:200px;color:#0067ac;text-align:center"></label>
											</div>
										</td>
										<td style="padding-left:10px;"> </td>
										<td style="text-align:right; padding-right:10px;"> </td>
										<td style="text-align:right; padding-right: 10px;"> </td>
										<td style="text-align:right; padding-right: 10px;"> </td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="PaymentBox">
							<table style="width:900px;">
								<tr>
									<td>
										<div class="PaymentBox_Left">
											<div class="detail-text" style="margin-top:8px;height:20px;font-size:12px">
												<div class="left-label">Thuế suất GTGT
													<i>(VAT rate):</i>
												</div>
												<div class="right-label" style="max-width: 25%;">[TEMP_INV_VAT_RATE]%</div>
											</div>
											<div class="detail-text" style="margin-top:0px;height:20px;font-size:12px" />
										</div>
									</td>
									<td>
										<div class="PaymentBox_Right">
											<div class="detail-text" style="padding-top:8px;height:20px">
												<div class="left-label">Cộng tiền hàng <i>(Total amount)</i>:</div>
												<div class="right-label" style="text-align: right;margin-right:5px">[TEMP_INV_AMT]</div>
											</div>
											<div class="detail-text" style="padding-top:8px;height:20px">
												<div class="left-label">Tiền thuế GTGT <i>(VAT amount)</i>:</div>
												<div class="right-label" style="text-align: right;margin-right:5px">[TEMP_INV_VAT_AMT]</div>
											</div>
											<div class="detail-text" style="padding-top:8px;height:20px">
												<div class="left-label">Tổng cộng tiền thanh toán <i>(Grand total)</i>:</div>
												<div class="right-label" style="text-align: right;;margin-right:5px">[TEMP_INV_TAMT]</div>
											</div>
										</div>
									</td>
								<tr>
									<td colspan="2">
										<div class="Bottom">
											<div class="detail-text" style="width:890px">
												<div>
													<div class="clearfix">
														<div class="clearfix" style="position:relative;width:890px;display:inline-block;">
															<label class="fl-l" style="font-weight:normal; color:#202020;float:left;width:280px;font-size:13px;">
																Số tiền viết bằng chữ <i>(In words of grand total)</i>:
															</label>
															<label class="fl-l input-name" style="float:left; padding-left:10px; width:600px; height:20px; min-width:100px; display:block;color:#202020; border-bottom:1px dotted #202020!important">
																[TEMP_INV_IN_WORDS]
															</label>
														</div>
														<div class="clearfix">
															<label class="fl-l input-name" style="padding-left:10px; width:880px; height:20px; min-width:100px; display:block; color:#202020; border-bottom:1px dotted #202020!important; float:left"></label>
														</div>
													</div>
												</div>
											</div>
										</div>
									</td>
								</tr>
								</tr>
							</table>
						</div>
						<div class="SignatureBox">
							<div id="Left">
								<center>
									<div class="detail-text" style="padding-top:5px">
										<b style="font-size:14px">Người mua hàng <i>(Buyer)</i></b>
										<br />
										<i>(Ký, ghi rõ họ tên/ Sign, full name)</i>
										<br />
										<br />
										[TEMP_INV_CUS_TAXCODE]
									</div>
									<div id="dialogClient" style="background-color:white;display:none">
										<div style="color:blue" id="messClt">Unknown!</div>
										<br />
										<br />
										<a href="#" onclick="displayCert('')">
											<div style="color:#184D4E">Xem thông tin chứng thư</div>
										</a>
									</div>
								</center>
							</div>
							<div id="Middle">
								<div style="text-align:center;">
									<div>&ensp;</div>
								</div>
							</div>
							<div id="Right">
								<center>
									<div class="detail-text" style="padding-top:5px">
										<b style="font-size:14px">Người bán hàng <i>(Seller)</i>
										</b><br />
										<i>(Ký, đóng dấu, ghi rõ họ tên/ Sign, stamp, full name)</i>
									</div>
									<div id="dialogServer" style="background-color:white;display:none">
										<div style="color:blue" id="messSer">Unknown!</div>
										<br /><br />
										<a href="#" onclick="displayCert('')">
											<div style="color:#184D4E">Xem thông tin chứng thư</div>
										</a>
									</div>
								</center>
							</div>
						</div>
						<div id="SearchInvUrl" style="text-align:center;font-size:11px;margin-bottom:0px;padding-top:65px">
							<br />
							Quý khách vui lòng tra cứu thông tin và in hóa đơn điện tử tại:
							<a href="https://icdnamhai-tt78.vnpt-invoice.com.vn">https://icdnamhai-tt78.vnpt-invoice.com.vn</a>
							- Mã tra cứu: <span style="font-size:12px">[TEMP_INV_PINCODE]</span> - Mã khách hàng: [TEMP_INV_CUS_TAXCODE]
						</div>
						<div id="VnptInfo">
							<center style="font-size:15px;border-bottom:0px dashed #fff; border-top: 1px dashed #000; margin-top:8px;padding-top:0px;line-height:23px">
								<i>********* HÓA ĐƠN BẢN NHÁP, KHÔNG CÓ GIÁ TRỊ *********</i>
							</center>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="clearfix" id="bt"> </div>
	</div>
</div>