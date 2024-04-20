<?php
defined('BASEPATH') or exit('');
?>

<!-- <button class="hidden_when_print" style="position:fixed;top:10px;left:10px" onClick="window.print()">In</button> -->
<html>

<head>
	<meta http-equiv=Content-Type content="text/html; charset=utf-8">
	<meta name=Generator content="Microsoft Word 14 (filtered)">
	<style>
		/* Font Definitions */
		@font-face {
			font-family: Calibri;
			panose-1: 2 15 5 2 2 2 4 3 2 4;
		}

		@font-face {
			font-family: Tahoma;
			panose-1: 2 11 6 4 3 5 4 4 2 4;
		}

		/* Style Definitions */
		p.MsoNormal,
		li.MsoNormal,
		div.MsoNormal {
			margin-top: 0cm;
			margin-right: 0cm;
			margin-bottom: 10.0pt;
			margin-left: 0cm;
			line-height: 115%;
			font-size: 9.0pt;
			font-family: "Calibri", "sans-serif";
		}

		p.MsoHeader,
		li.MsoHeader,
		div.MsoHeader {
			mso-style-link: "Header Char";
			margin: 0cm;
			margin-bottom: .0001pt;
			font-size: 9.0pt;
			font-family: "Calibri", "sans-serif";
		}

		p.MsoFooter,
		li.MsoFooter,
		div.MsoFooter {
			mso-style-link: "Footer Char";
			margin: 0cm;
			margin-bottom: .0001pt;
			font-size: 9.0pt;
			font-family: "Calibri", "sans-serif";
		}

		span.HeaderChar {
			mso-style-name: "Header Char";
			mso-style-link: Header;
		}

		span.FooterChar {
			mso-style-name: "Footer Char";
			mso-style-link: Footer;
		}

		span.BalloonTextChar {
			mso-style-name: "Balloon Text Char";
			mso-style-link: "Balloon Text";
			font-family: "Tahoma", "sans-serif";
		}

		.MsoChpDefault {
			font-family: "Calibri", "sans-serif";
		}

		.MsoPapDefault {
			margin-bottom: 10.0pt;
			line-height: 115%;
		}

		/* Page Definitions */
		@page {
			size: auto;
			margin: 0;
			orientation: landscape;
		}

		@page WordSection1 {
			size: 215mm 157mm;
			margin: 5mm 5mm 0mm 5mm;
		}

		@media print {
			div.WordSection1::after {
				page-break-after: always;
			}

			div.WordSection1 {
				display: inline-block;
				position: relative;
				size: 215mm 157mm;
				margin: 5mm 5mm 0mm 5mm;
				orientation: landscape;
			}
		}

		div.WordSection1 {
			page: WordSection1
		}
	</style>

</head>

<div class="print_background" style="width: 215mm;" lang=EN-US>
	<div class="A4_print">
		<div class=WordSection1>

			<table class=MsoTableGrid border=0 cellspacing=0 cellpadding=0 style='border-collapse:collapse;border:none'>
				<tr>
					<td width=110 valign=top align=center style='width:90.9pt;padding:1.4pt 5.4pt 0cm 5.4pt'>
						<p class="MsoHeader">
							<img id="img-logo" width="105" src="<?= base_url('assets/img/logos/logo.jpg'); ?>" align="center" hspace="12">
						</p>
					</td>
					<td width=630 valign=top style="width:485.4pt;padding:0cm 5.4pt 0cm 5.4pt">
						<p class=MsoHeader align=center style='text-align:center'>
							<b><span style='font-size:16.9px;font-family:"Times New Roman","serif"'><?= $this->config->item('YARD_FULL_NAME'); ?></span></b>
						</p>
						<p class=MsoHeader align=center style='text-align:center'>
							<b><span style='font-size:13px;font-family:"Times New Roman","serif"'>Address: <?= $this->config->item('YARD_ADDRESS'); ?></span></b>
						</p>
						<p class=MsoHeader align=center style='text-align:center'>
							<b><span style='font-size:13px;font-family:"Times New Roman","serif"'>Telephone: <?= $this->config->item('YARD_HOT_LINE'); ?> - Email: <?= $this->config->item('YARD_EMAIL'); ?></span></b>
						</p>
					</td>
				</tr>
			</table>

			<p class="MsoHeader" align="center" style='margin-left:40.5pt;text-align:center;text-indent:-40.5pt'>
				<span style="position:absolute;z-index:251676672;left:0px;margin-left:162px;margin-top:3px;width:605px;height:1px">

				</span>
			</p>
			<hr style="border-top: 0">

			<table class=MsoTableGrid border=0 cellspacing=0 cellpadding=0 style='border-collapse:collapse;border:none'>
				<tr style='height:23.35pt'>
					<td width=764 colspan=3 valign=top style='width:573.3pt;padding:0cm 5.4pt 0cm 5.4pt;height:23.35pt;position:relative;'>
						<p class=MsoHeader align=center style='text-align:center'>
							<b><span style='font-size:1.3rem;font-family:"Times New Roman","serif"'>PHIẾU TẠM THU</span></b>
						</p>
						<div style="position: absolute;top: 15px;right: 12px;">
							<p class="MsoHeader" align="right">
								<span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>Số:&ensp;</span>
								<span style="font-size:1.1rem;display:inline-block;text-align:left;color: navy"><b>[DR_NO]</b></span>
							</p>
						</div>
					</td>
				</tr>
				<tr>
					<td width=764 colspan=3 valign=top style='width:573.3pt;padding:0cm 5.4pt 0cm 5.4pt'>
						<p class=MsoHeader align=center style='text-align:center'>
							<b><i><span style='font-size:0.8rem;font-family:"Times New Roman","serif"'>Ngày [DR_DAY] tháng [DR_MONTH] năm [DR_YEAR]</span></i></b>
						</p>
					</td>
				</tr>
				<tr>
					<td valign=top colspan="3" style='padding:8px 0'>
					</td>
				</tr>
				<tr>
					<td valign=top align=left style='width:70pt; padding: 0cm 4.4pt;'>
						<p class=MsoHeader>
							<b><span style='font-size:0.85rem;font-family:"Times New Roman","serif"'>Khách hàng: </span> </b>
						</p>
						<p class=MsoHeader>
							<i><span style='font-size:0.65rem;position:relative;top:-3px;font-family:"Times New Roman","serif"'>Customer:</span></i>
						</p>
					</td>
					<td width=764 valign=top align=left style='width:573.3pt;padding:0cm 4.4pt 4.4pt 0'>
						<div class=MsoHeader>
							<span style='font-size:0.85rem;font-family:"Times New Roman","serif"'>[PAYER_NAME]</span>
						</div>
					</td>
					<td width=764 valign=top align=left style='width:167.3pt;padding:0cm 4.4pt 4.4pt 0'>
						<p class=MsoHeader>
							<b><span style='font-size:0.85rem;font-family:"Times New Roman","serif"'>Mã số thuế: </span></b>
							<span style='font-size:0.85rem;font-family:"Times New Roman","serif"'>[TAX_CODE]</span>
						</p>
						<p class=MsoHeader>
							<i><span style='font-size:0.65rem;position:relative;top:-3px;font-family:"Times New Roman","serif"'>Tax code:</span></i>
						</p>
					</td>
				</tr>
				<tr>
					<td valign=top align=left style='width:70pt; padding: 0cm 4.4pt;'>
						<p class=MsoHeader>
							<b><span style='font-size:0.85rem;font-family:"Times New Roman","serif"'>Địa chỉ: </span> </b>
						</p>
						<p class=MsoHeader>
							<i><span style='font-size:0.65rem;position:relative;top:-3px;font-family:"Times New Roman","serif"'>Address:</span></i>
						</p>
					</td>
					<td width=764 colspan="2" valign=top align=left style='padding:0cm 4.4pt 4.4pt 0'>
						<p class=MsoHeader>
							<span style='font-size:0.85rem;font-family:"Times New Roman","serif"'>[PAYER_ADDRESS]</span>
						</p>
					</td>
				</tr>
			</table>

			<p class=MsoHeader><b><span style='font-size:5.0pt;font-family:"Times New Roman","serif"'>&nbsp;</span></b></p>

			<table class=MsoTableGrid border=1 cellspacing=0 cellpadding=0 style='border-collapse:collapse;border:none'>
				<thead>
					<tr style='height:28.25pt'>
						<td valign=middle style='border:solid windowtext 1.0pt;padding:0cm 4.4pt;'>
							<p class=MsoHeader align=center style='text-align:center'>
								<b><span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>TT</span></b>
							</p>
							<p class=MsoHeader align=center style='text-align:center'>
								<span style='font-size:0.8rem;font-family:"Times New Roman","serif"'>Seq.</span>
							</p>
						</td>
						<td width=11% valign=middle style='width:156.9pt;border:solid windowtext 1.0pt;border-left:none;padding:0cm 4.4pt;'>
							<p class=MsoHeader align=center style='text-align:center'>
								<b><span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>Nội dung</span></b>
							</p>
							<p class=MsoHeader align=center style='text-align:center'>
								<span style='font-size:0.8rem;font-family:"Times New Roman","serif"'>Description</span>
							</p>
						</td>
						<td width=14% valign=middle style='width:62.0pt;border:solid windowtext 1.0pt;border-left:none;padding:0cm 4.4pt;'>
							<p class=MsoHeader align=center style='text-align:center'>
								<b><span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>ĐVT</span></b>
							</p>
							<p class=MsoHeader align=center style='text-align:center'>
								<span style='font-size:0.8rem;font-family:"Times New Roman","serif"'>Unit</span>
							</p>
						</td>
						<td width=14% valign=middle style='width:62.5pt;border:solid windowtext 1.0pt;border-left:none;padding:0cm 4.4pt;'>
							<p class=MsoHeader align=center style='text-align:center'>
								<b><span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>Số lượng</span></b>
							</p>
							<p class=MsoHeader align=center style='text-align:center'>
								<span style='font-size:0.8rem;font-family:"Times New Roman","serif"'>Quantity</span>
							</p>
						</td>
						<td width=14% valign=middle style='width:76.5pt;border:solid windowtext 1.0pt;border-left:none;padding:0cm 4.4pt;'>
							<p class=MsoHeader align=center style='text-align:center'>
								<b><span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>Đơn giá</span></b>
							</p>
							<p class=MsoHeader align=center style='text-align:center'>
								<span style='font-size:0.8rem;font-family:"Times New Roman","serif"'>Price</span>
							</p>
						</td>
						<td width=14% valign=middle style='width:72.0pt;border:solid windowtext 1.0pt;border-left:none;padding:0cm 4.4pt;'>
							<p class=MsoHeader align=center style='text-align:center'>
								<b><span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>VAT</span></b>
							</p>
							<p class=MsoHeader align=center style='text-align:center'>
								<span style='font-size:0.8rem;font-family:"Times New Roman","serif"'>Vat</span>
							</p>
						</td>
						<td width=14% valign=middle style='width:106.9pt;border:solid windowtext 1.0pt;border-left:none;padding:0cm 4.4pt;'>
							<p class=MsoHeader align=center style='text-align:center'><b>
									<span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>Thành tiền</span></b></p>
							<p class=MsoHeader align=center style='text-align:center'>
								<span style='font-size:0.8rem;font-family:"Times New Roman","serif"'>Amount</span>
							</p>
						</td>
						<td width=14% valign=middle style='width:116.9pt;border:solid windowtext 1.0pt;border-left:none;padding:0cm 4.4pt;'>
							<p class=MsoHeader align=center style='text-align:center'><b>
									<span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>Ghi chú</span></b></p>
							<p class=MsoHeader align=center style='text-align:center'>
								<span style='font-size:0.8rem;font-family:"Times New Roman","serif"'>Remark</span>
							</p>
						</td>
					</tr>
				</thead>
				<tbody>
					<ROW_DETAIL>
						<tr style='height:33.25pt'>
							<td valign=middle style='border:solid windowtext 1.0pt;border-top:none;padding:0cm 4.4pt;'>
								<p class=MsoHeader align=center style='text-align:center'>
									<span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>[SOTHUTU]</span>
								</p>
							</td>
							<td width=72 valign=middle style='width:54.0pt;border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;padding:0cm 4.4pt;'>
								<p class=MsoHeader align=center style='text-align:left'>
									<span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>[TRF_DESC]</span>
								</p>
							</td>
							<td width=96 valign=middle style='width:72.0pt;border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;padding:0cm 4.4pt;'>
								<p class=MsoHeader align=center style='text-align:center'>
									<span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>[INV_UNIT]</span>
								</p>
							</td>
							<td width=96 valign=middle style='width:72.0pt;border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;padding:0cm 4.4pt;'>
								<p class=MsoHeader align=center style='text-align:right'>
									<span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>[QTY]</span>
								</p>
							</td>
							<td width=114 valign=middle style='width:85.5pt;border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;padding:0cm 4.4pt;'>
								<p class=MsoHeader align=center style='text-align:right'>
									<span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>[UNIT_RATE]</span>
								</p>
							</td>
							<td width=102 valign=middle style='width:76.5pt;border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;padding:0cm 4.4pt;'>
								<p class=MsoHeader align=center style='text-align:right'>
									<span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>[VAT]</span>
								</p>
							</td>
							<td width=96 valign=middle style='width:72.0pt;border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;padding:0cm 4.4pt;'>
								<p class=MsoHeader align=center style='text-align:right'>
									<span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>[TAMOUNT]</span>
								</p>
							</td>
							<td width=169 valign=middle style='width:126.9pt;border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;padding:0cm 4.4pt;'>
								<p class=MsoHeader align=center style='text-align:center;text-align:center;height:100%;'>
									<span class="resize-font" style='font-size:0.8rem;font-family:"Times New Roman","serif";width: 100px;'>[REMARK]</span>
								</p>
							</td>
						</tr>
					</ROW_DETAIL>
					<tr style='height:28.25pt'>
						<td colspan="6" valign=middle style='border:solid windowtext 1.0pt;border-top:none;padding:0cm 4.4pt;'>
							<p class=MsoHeader align=center style='text-align:center'>
								<span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>TỔNG CỘNG</span>
							</p>
						</td>
						<td width=96 valign=middle style='width:72.0pt;border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;padding:0cm 4.4pt;'>
							<p class=MsoHeader align=center style='text-align:right'>
								<span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>[TOTAL_AMOUNT]</span>
							</p>
						</td>
						<td width=169 valign=middle style='width:126.9pt;border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;padding:0cm 4.4pt;'>
						</td>
					</tr>
				</tbody>
			</table>

			<p class=MsoHeader><span style='font-size:0.5rem;font-family:"Times New Roman","serif"'>&nbsp;</span></p>

			<table class=MsoTableGrid border=0 cellspacing=0 cellpadding=0 style='border-collapse:collapse;border:none'>
				<tr>
					<td width=764 colspan=3 valign=top align=left style='width:573.3pt;padding:0cm 5.4pt 0cm 5.4pt'>
						<p class=MsoHeader>
							<b><span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>Tổng tiền bằng chữ: </span></b>
							<span style='font-size:0.9rem;font-family:"Times New Roman","serif";font-style: italic'>[IN_WORDS]</span>
						</p>
						<p class=MsoHeader>
							<i><span style='font-size:0.7rem;position:relative;top:-3px;font-family:"Times New Roman","serif"'>Total amount in words</span></i>
						</p>
					</td>
				</tr>
				<tr style="height:63.25pt">
					<td width=280 valign=top>
						<p class=MsoHeader align=center style='text-align:center'>
							<b><span style='font-size:1rem;font-family:"Times New Roman","serif"'>&nbsp;</span></b>
						</p>
					</td>
					<td width=200 valign=top style='padding:0cm 5.4pt 0cm 5.4pt'>
						<p class=MsoHeader align=center style='text-align:center'>
							<b><span style='font-size:1rem;font-family:"Times New Roman","serif"'>&nbsp;</span></b>
						</p>
					</td>
					<td width=253 valign=top style='width:373.65pt;padding:0cm 5.4pt 0cm 5.4pt'>
						<p class=MsoHeader align=center style='text-align:center'>
							<span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>Người lập phiếu</span>
						</p>
					</td>
				</tr>
				<tr>
					<td width=280 valign=top align=center style='width:200.0pt;padding:0cm 5.4pt 0cm 5.4pt'>
						<p class=MsoHeader align=center style='text-align:center'>
							<b><span style='font-size:1rem;font-family:"Times New Roman","serif"'>&nbsp;</span></b>
						</p>
					</td>
					<td width=232 valign=top style='width:183.65pt;padding:0cm 5.4pt 0cm 5.4pt'>
						<p class=MsoHeader align=center style='text-align:center'>
							<b><span style='font-size:1rem;font-family:"Times New Roman","serif"'>&nbsp;</span></b>
						</p>
					</td>
					<td width=253 valign=bottom style='width:373.65pt;padding:0cm 5.4pt 5.4pt 5.4pt'>
						<p class=MsoHeader align=center style='text-align:center'>
							<b><span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>[USER_NAME]</span></b>
						</p>
					</td>
				</tr>
				<tr>
					<td width=764 colspan=3 valign=top style='width:573.3pt;padding:0cm 5.4pt 0cm 5.4pt'>
						<p class=MsoHeader align=center style='text-align:center'>
							<b><i><span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>Phiếu tạm thu có giá trị đổi sang HĐ GTGT hết ngày 27 trong tháng, riêng tháng 02 hết ngày 26.</span></i></b>
						</p>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>

</html>