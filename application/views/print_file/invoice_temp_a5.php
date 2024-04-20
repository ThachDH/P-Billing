<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<div>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>VAT</title>
	<style type="text/css" rel="stylesheet">
		@charset "utf-8";

		@page {
			size: A5 landscape;
			margin: 2mm 5mm 0mm 5mm;
		}

		html,
		body {
			margin: 0;
			padding: 0;
		}

		.number {
			color: red;
			font-size: 20px;
			font-family: Arial, Helvetica, sans-serif;
		}

		.detail-text .left-label {
			float: left;
			margin-right: 4px;
		}

		.detail-text .right-label {
			overflow: hidden;
			border-bottom: 1px dotted #000;
		}

		.detail-text .right-label-no-border {
			overflow: hidden;
			border-bottom: none;
			min-height: 17px;
		}

		.bgimg {
			border: 1px solid !important;
			cursor: pointer
		}

		.bgimg p {
			color: #f22121;
			padding-left: 10px;
			font-size: 13px;
			text-align: left
		}

		.LogoImage {
			height: 87px;
			width: 137px;
			background: url("[TEMP_INV_LOGO]") no-repeat;
			position: absolute;
			left: 0px;
			margin-top: 0px;
			background-size: auto 100%;
		}

		.InvoiceTemplate {
			position: relative;
		}

		.InvHeader {
			width: 910px;
			float: left;
		}

		.LeftHeader {
			padding-left: 0px;
			float: left;
			width: 720px;
		}

		.RightHeader {
			overflow: hidden;
			width: 188px
		}

		.CompanyInfo label {
			font-size: 14px;
		}

		label {
			display: inline-block;
		}

		.InvTitle h2 {
			margin: 0px;
			color: #0D47A1;
			font-size: 20px;
		}

		.InvTitle h3 {
			margin: 0px;
			color: #0D47A1;
			font-size: 17px;
		}

		.InvTitle h3 i {
			margin: 0px;
			color: #0D47A1;
			font-size: 15px;
		}

		.InvTitle i {
			margin: 0px;
			font-size: 12px;
		}

		.InvTitle {
			padding-top: 0px;
			width: 100%;
			float: left;
		}

		.ProductTable {
			overflow: hidden;
			position: relative;
			width: 100%;
			float: left;
			font-size: 12px
		}

		.ProductTable:after {
			content: ' ';
			display: table-row-group;
			position: absolute;
			z-index: 1;
			opacity: 0.3;
			background-image: url("[TEMP_INV_TABLE_BACKGROUND]");
			width: 360px;
			height: 152px;
			left: 50%;
			top: 45%;
			margin-left: -180px;
			margin-top: -62px;
			background-repeat: no-repeat;
			background-position: center top;
			-webkit-background-size: cover;
			background-size: contain;
			color-adjust: exact !important;
		}

		.ProductTable table {
			height: 172px;
			margin-top: 6px;
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
			width: 100%;
			float: left;
		}

		.PaymentBox .Left {
			float: left;
			width: 45%;
		}

		.PaymentBox .Right {
			overflow: hidden;
			width: 55%;
		}

		.SignatureBox {
			padding-top: 2px;
			font-size: 12px !important;
			width: 100%;
			float: left;
			padding-bottom: 40px;
		}

		.SignatureBox #Left {
			float: left;
			width: 225px;
		}

		.SignatureBox #Middle {
			float: left;
			overflow: hidden;
			width: 340px;
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
			line-height: 2px;
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

		#inbt {
			margin-top: 0px !important;
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
			<div id="PrintView" style="width:1000px; margin:auto; padding-bottom:20px">
				<div class="MiddleView">
					<div class="InvoiceTemplate">
						<div class="InvHeader">
							<div class="LeftHeader">
								<div class="LogoImage" style="width:140px"></div>
								<div style="float:left;margin-left:155px;width:680px">
									<div style="font-size:18px; text-transform:uppercase; padding-top:0x;padding-bottom:3px;">
										<b style="color:#0D47A1; font-size:18px;line-height: 1.42857143;">[TEMP_INV_YARD_NAME] <br /></b>
										<b style="color:#0D47A1; font-size:16.5px;line-height:13px">([TEMP_INV_YARD_NAME_ENG])</b>
									</div>
									<div style="width:750px;">
										<label style="height:10px;padding-top:20px;font-size:10px;font-weight: normal">
											Mã số thuế (Tax code): <b style="letter-spacing: 5px">[TEMP_INV_YARD_TAXCODE]</b></label>
										<br />
										<label style="height:8px;font-size:10px;font-weight: normal;">
											Địa chỉ (Address): [TEMP_INV_YARD_ADDR]
										</label>
										<br />
										<label style="height:10px;font-size:10px;font-weight: normal;">
											Điện thoại (Tel): [TEMP_INV_YARD_TEL]
										</label>
										<label style="height:10px;padding-left:15px;font-size:10px;font-weight: normal;">
											Fax:(+84) [TEMP_INV_YARD_FAX]
										</label>
										<!-- <label style="padding-left:20px;font-size:10px;font-weight: normal;"><br /></labe'> -->
										<br />
										<label style="height:10px;width:750px;font-size:10px;font-weight: normal;">
											Số tài khoản (A/C No.): [TEMP_INV_YARD_BANK]
										</label>
									</div>
								</div>
							</div>
							<div class="RightHeader">
								<div class="detail-text" style="float:left; padding-bottom:5px; width:200px;font-size:11.5px">
									<div class="left-label">Ký hiệu <i>(Series)</i>:</div>
									<div><b>[TEMP_INV_SERIAL]</b></div>
								</div>
								<div class="detail-text" style="float:left; width:200px;font-size:11.5px">
									<div class="left-label" style="padding-top:5px">Số <i>(No)</i>:</div>
									<div class="right-label-no-border" style="font-size:18px; color:red;padding-top:5px"><b><span class="number" style="color:#FA5858">0000000</span></b></div>
								</div>
							</div>
						</div>
						<div class="InvTitle">
							<hr style="height:1px;background-color:#000;" />
							<center>
								<h3 style="text-transform:uppercase; font-size: bold;">
									HÓA ĐƠN GIÁ TRỊ GIA TĂNG (BẢN NHÁP)
								</h3>
								<h3 style="text-transform:uppercase;margin-bottom:0px">
									<i style="color:#0D47A1">VAT INVOICE (DRAFT)</i>
								</h3>
								<i>
									Ngày (date) <label style="border-bottom: 1px dotted #000;">[TEMP_INV_DAY]</label>&ensp;
									tháng (month) <label style="border-bottom: 1px dotted #000;">[TEMP_INV_MONTH]</label>&ensp;
									năm (year) <label style="border-bottom: 1px dotted #000;">[TEMP_INV_YEAR]</label>&ensp;
									<center style="font-style: italic;">
										<p style="margin-bottom: 3px; margin-top: 3px;">
											<b>Mã của cơ quan thuế:</b>
											<b style="border-bottom: 1px dotted #000;padding-right: 185px"></b>
										</p>
									</center>
								</i>
							</center>
						</div>
						<div class="CustomerInfo">
							<div class="detail-text" style="float: left; min-width:639px;padding-top:8px;font-size:12px">
								<div class="left-label">Họ tên người mua hàng <i>(Buyer)</i>:</div>
								<div class="right-label"></div>
							</div>
							<div class="detail-text" style="padding-top:8px;font-size:12px;">
								<div class="left-label">Điện thoại <i>(Tel)</i>:</div>
								<div class="right-label"></div>
							</div>
							<div class="detail-text" style="float:left;min-width:639px;font-size:12px;padding-top:8px">
								<div class="left-label">Tên đơn vị <i>(Company's name)</i>:</div>
								<div class="right-label"> [TEMP_INV_CUS_NAME]</div>
							</div>
							<div class="detail-text" style="float: left; width:261px;font-size:12px;padding-top:8px">
								<div class="left-label">Mã số thuế <i>(Tax code)</i>:</div>
								<div class="right-label">[TEMP_INV_CUS_TAXCODE]</div>
							</div>
							<div class="detail-text" style="overflow: hidden; width:100%;font-size:12px;padding-top:8px">
								<div class="left-label">Địa chỉ<i>(Address)</i>:</div>
								<div class="right-label">[TEMP_INV_CUS_ADDR]</div>
							</div>
							<div class="detail-text" style="float:left;min-width:639px;font-size:12px;padding-top:8px">
								<div class="left-label">Hình thức thanh toán <i>(Payment method)</i>:</div>
								<div class="right-label">[TEMP_INV_PAY_METHOD]</div>
							</div>
							<div class="detail-text" style="overflow: hidden; width:261px;font-size:12px;padding-top:8px">
								<div class="left-label">Đơn vị tiền tệ <i>(Currency Unit)</i>:</div>
								<div class="right-label">[TEMP_INV_CURRENCY]</div>
							</div>
						</div>
						<div class="ProductTable">
							<table width="100%">
								<col />
								<col />
								<col />
								<col />
								<col />
								<col />
								<tbody>
									<tr>
										<th width="5%  !important" style="text-transform:none;font-size:11px">STT <br /><i>(No.)</i></th>
										<th width="45% !important" style="text-transform:none;font-size:11px">Tên hàng hóa, dịch vụ <br /><i>(Description)</i></th>
										<th width="10%  !important" style="text-transform:none;font-size:11px">Đơn vị tính <br /><i>(Unit)</i></th>
										<th width="10%  !important" style="text-transform:none;font-size:11px">Số lượng <br /><i>(Quantity)</i></th>
										<th width="13% !important" style="text-transform:none;font-size:11px">Đơn giá <br /><i>(Unit Price)</i></th>
										<th width="17% !important" style="text-transform:none;;font-size:11px">Thành tiền <br /><i>(Amount)</i></th>
									</tr>
									<tr style="font-size:11px;height: 20px">
										<th width="5%  !important">1</th>
										<th width="45% !important" style="text-transform:none">2</th>
										<th width="10%  !important" style="text-transform:none">3</th>
										<th width="10%  !important" style="text-transform:none">4</th>
										<th width="13% !important" style="text-transform:none">5</th>
										<th width="17% !important" style="text-transform:none;">6 = 4 x 5</th>
									</tr>
									[TEMP_INV_PROD_DETAIL]
									<tr style="height:18px">
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
									</tr>
									<tr style="height:18px">
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="PaymentBox">
							<div class="Left">
								<div class="detail-text" style="margin-top:8px;height:20px;font-size:12px">
									<div class="left-label">Thuế suất GTGT
										<i>(VAT rate):</i>
									</div>
									<div class="right-label">[TEMP_INV_VAT_RATE]%</div>
								</div>
								<div class="detail-text" style="margin-top:0px;height:20px;font-size:12px">


								</div>
							</div>
							<div class="Right">
								<div class="detail-text" style="padding-top:3px;font-size:12px">
									<div class="left-label">Cộng tiền hàng
										<i>(Total amount):</i>
									</div>
									<div class="right-label" style="text-align: right;margin-right:10px">[TEMP_INV_AMT]</div>
								</div>
								<div class="detail-text" style="padding-top:3px;font-size:12px">
									<div class="left-label">Tiền thuế GTGT
										<i>(VAT amount):</i>
									</div>
									<div class="right-label" style="text-align: Right;;margin-right:10px">[TEMP_INV_VAT_AMT]</div>
								</div>
								<div class="detail-text" style="padding-top:3px;font-size:12px">
									<div class="left-label">Tổng cộng tiền thanh toán
										<i>(Grand total):</i>
									</div>
									<div class="right-label" style="text-align: Right;;margin-right:10px">[TEMP_INV_TAMT]</div>
								</div>
							</div>
							<div class="Bottom">
								<div class="detail-text" style="padding-top:0px;">
									<div class="left-label" style="font-size:12px;padding-top:3px">
										Số tiền viết bằng chữ
										<i>(In words of grand total):</i>
									</div>
									<div class="right-label">[TEMP_INV_IN_WORDS]</div>
									<div class="right-label"></div>
								</div>
							</div>
						</div>
						<div class="SignatureBox">
							<div id="Left">
								<center>
									<div class="detail-text" style="padding-top:5px">
										<b style="font-size:14px">Người mua hàng (Buyer)</b>
										<br />
										<i>(Ký, ghi rõ họ tên/ Sign, full name)</i>
										<br />
										<br />
										[TEMP_INV_CUS_TAXCODE]
									</div>
								</center>
							</div>
							<div id="Middle">
								<div style="text-align:center;">
									&emsp;
								</div>
							</div>
							<div id="Right">
								<center>
									<div class="detail-text" style="padding-top:5px">
										<b style="font-size:14px">Người bán hàng (Seller)</b>
										<br />
										<i>(Ký, đóng dấu, ghi rõ họ tên/ Sign, stamp, full name)</i>
									</div>
								</center>
							</div>
						</div>
						<div id="SearchInvUrl" style="text-align:center;font-size:11px;margin-top:3px;padding-bottom:9px">
							Quý khách vui lòng tra cứu thông tin và in hóa đơn điện tử tại:<a href="#">https://icdnamhai-tt78.vnpt-invoice.com.vn</a><br />
						</div>
						<div id="SearchInvUrl" style="text-align:center;font-size:11px;margin-top:3px;padding-bottom:9px">

							Mã tra cứu: <span style="font-size:11px">[TEMP_INV_PINCODE]</span> -Mã khách hàng: [TEMP_INV_CUS_TAXCODE]</div>
						<div id="VnptInfo">
							<center style="font-size:15px;border-bottom:0px dashed #fff; border-top: 1px dashed #000; margin-top:2px; padding-top:1px;line-height:23px">
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