<?php
defined('BASEPATH') or exit('');
?>

<!-- <button class="hidden_when_print" style="position:fixed;top:10px;left:10px" onClick="window.print()">In</button> -->
<html>

<head>
	<meta http-equiv=Content-Type content="text/html; charset=windows-1252">
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

<div class="print_background" lang=EN-US>
	<div class="A4_print">
		<div class=WordSection1>

			<table class=MsoTableGrid border=0 cellspacing=0 cellpadding=0 style='border-collapse:collapse;border:none'>
				<tr>
					<td width=110 valign=top align=center style='width:90.9pt;padding:1.4pt 5.4pt 0cm 5.4pt'>
						<p class="MsoHeader">
							<img id="img-logo" width="105"  src="<?= base_url('assets/img/logos/logo.jpg'); ?>" align="center" hspace="12">
						</p>
					</td>
					<td width=630 valign=top style="width:485.4pt;padding:0cm 5.4pt 0cm 5.4pt">
						<p class=MsoHeader align=center style='text-align:center'>
							<b><span style='font-size:16.9px;font-family:"Times New Roman","serif"'><?= $this->config->item('YARD_FULL_NAME'); ?></span></b>
						</p>
						<p class=MsoHeader align=center style='text-align:center'>
							<b><span style='font-size:12px;font-family:"Times New Roman","serif"'>Address: <?= $this->config->item('YARD_ADDRESS'); ?></span></b>
						</p>
						<p class=MsoHeader align=center style='text-align:center'>
							<b><span style='font-size:12px;font-family:"Times New Roman","serif"'>Telephone: <?= $this->config->item('YARD_HOT_LINE'); ?> - Email: <?= $this->config->item('YARD_EMAIL'); ?></span></b>
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
							<b><span style='font-size:1.3rem;font-family:"Times New Roman","serif"'>LỆNH GIAO NHẬN CONTAINER</span></b>
						</p>
					</td>
				</tr>
				<tr>
					<td width=764 colspan=3 valign=top style='width:573.3pt;padding:0cm 5.4pt 0cm 5.4pt'>
						<p class=MsoHeader align=center style='text-align:center'>
							<b><i><span style='font-size:1rem;font-family:"Times New Roman","serif"'>EQUIPMENT INTERCHANGE ORDER</span></i></b>
						</p>
					</td>
				</tr>
				<tr>
					<td width=764 colspan=3 valign=top style='width:573.3pt;padding:0cm 5.4pt 0cm 5.4pt'>
						<p class=MsoHeader align=right style='text-align:right'>
							<span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>Số Lệnh <i>(Order No)</i>:</span>
							<span style='width:130px;display:inline-block;text-align:left'><b>[OrderNo]</b></span>
						</p>
					</td>
				</tr>
				<tr>
					<td width=764 colspan=3 valign=top style='width:573.3pt;padding:0cm 5.4pt 0cm 5.4pt'>
						<p class=MsoHeader align=right style='text-align:right'>
							<span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>Ngày lệnh <i>(Issue Date)</i>:</span>
							<span style='width:130px;display:inline-block;text-align:left'><b>[IssueDate]</b></span>
						</p>
					</td>
				</tr>
				<tr>
					<td width=764 colspan=3 valign=top style='width:573.3pt;padding:0cm 5.4pt 0cm 5.4pt'>
						<p class=MsoHeader align=right style='text-align:right'>
							<span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>Hạn điện <i>(Plug exp. date)</i>:</span>
							<span style='width:130px;display:inline-block;text-align:left'><b>[ExpPluginDate]</b></span>
						</p>
					</td>
				</tr>
				<tr>
					<td width=764 colspan=3 valign=top align=left style='width:573.3pt;padding:0cm 5.4pt 0cm 5.4pt'>
						<p class=MsoHeader>
							<b><span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>Giao cho/ Nhận của: </span></b>
							<span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>[SHIPPER_NAME]</span>
						</p>
						<p class=MsoHeader>
							<i><span style='font-size:0.7rem;position:relative;top:-4px;font-family:"Times New Roman","serif"'>Delivered to / Received fr:</span></i>
						</p>
					</td>
				</tr>
				<tr>
					<td width=463 colspan=2 valign=top align=left style='width:347.4pt;padding:0cm 5.4pt 0cm 5.4pt'>
						<p class=MsoHeader>
							<b><span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>Số vận đơn/ Số Booking: </span></b>
							<span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>[BL_BKNo]</span>
						</p>
						<p class=MsoHeader>
							<i><span style='font-size:0.7rem;position:relative;top:-4px;font-family:"Times New Roman","serif"'>Bill No / Booking No:</span></i>
						</p>
					</td>
					<td width=301 valign=top align=left style='width:225.9pt;padding:0cm 5.4pt 0cm 5.4pt'>
						<table border=0 cellspacing="0" cellpadding="0">
							<tr>
								<td>
									<b><span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>Giá trị đến:&ensp;</span></b>
								</td>
								<td><span style='font-size:0.8rem;position:relative;font-family:"Times New Roman","serif"'>Lưu bãi:&ensp;</span></td>
								<td style='font-size:0.8rem;font-family:"Times New Roman","serif"'><b>[ExpDate]</b></td>
							</tr>
							<tr>
								<td>
									<i><span style='font-size:0.7rem;position:relative;top:-4px;font-family:"Times New Roman","serif"'>Valid to: </span></i>
								</td>
								<td>
									<span style='font-size:0.8rem;position:relative;font-family:"Times New Roman","serif"'>Lưu cont:&ensp;</span>
								</td>
								<td>
									<span style='font-size:0.8rem;position:relative;font-family:"Times New Roman","serif"'><b>[OprExpDate]</b></span>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td width=265 valign=top align=left style='width:198.9pt;padding:0cm 5.4pt 0cm 5.4pt'>
						<p class=MsoHeader>
							<b><span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>Tên tàu: </span></b>
							<span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>[ShipName]</span>
						</p>
						<p class=MsoHeader>
							<i><span style='font-size:0.7rem;position:relative;top:-4px;font-family:"Times New Roman","serif"'>Vessel:</span></i>
							<span style='font-size:1rem;font-family:"Times New Roman","serif"'></span>
						</p> 
					</td>
					<td width=198 valign=top align=left style='width:148.5pt;padding:0cm 5.4pt 0cm 5.4pt'>
						<p class=MsoHeader>
							<b><span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>Chuyến: </span></b>
							<span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>[ImVoy]/[ExVoy]</span>
						</p>
						<p class=MsoHeader>
							<i><span style='font-size:0.7rem;position:relative;top:-4px;font-family:"Times New Roman","serif"'>Voy:</span></i>
							<span style='font-size:0.9rem;font-family:"Times New Roman","serif"'> </span>
						</p>
					</td>
					<td width=301 valign=top align=left style='width:225.9pt;padding:0cm 5.4pt 0cm 5.4pt'>
						<p class=MsoHeader style="line-height: 1.2rem;">
							<b><span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>Ngày vào bãi:</span></b>
							<span style='position:relative;font-size:0.8rem;font-family:"Times New Roman","serif"'>
								[DateIn] 
							</span>
						</p>
						<p class=MsoHeader>
							<i><span style='font-size:0.7rem;position:relative;top:-4px;font-family:"Times New Roman","serif"'>Date In:</span></i>
							<span style='font-size:0.8rem;font-family:"Times New Roman","serif"'></span>
						</p>
					</td>
				</tr>
				<tr>
					<td width=265 valign=top align=left style='width:198.9pt;padding:0cm 5.4pt 0cm 5.4pt'>
						<p class=MsoHeader>
							<b><span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>Hãng khai thác:</span></b>
							<span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>[OprID]</span>
						</p>
						<p class=MsoHeader>
							<i><span style='font-size:0.7rem;position:relative;top:-4px;font-family:"Times New Roman","serif"'>Operator:</span></i>
						</p>
					</td>
					<td width=198 valign=top align=left style='width:148.5pt;padding:0cm 5.4pt 0cm 5.4pt'>
						<p class=MsoHeader>
							<b><span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>Hóa đơn:</span></b>
							<span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>[InvNo]</span>
						</p>
						<p class=MsoHeader>
							<i><span style='font-size:0.8rem;position:relative;top:-4px;font-family:"Times New Roman","serif"'>Invoice:</span></i>
						</p>
					</td>
					<td width=301 valign=top align=left style='width:225.9pt;padding:0cm 5.4pt 0cm 5.4pt'>
						<p class=MsoHeader>
							<b><span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>Phương án giao / nhận: </span></b>
							<span style='font-size: 0.9rem;font-family:"Times New Roman","serif";'>[CJModeName]</span>
						</p>
						<p class=MsoHeader>
							<i><span style='font-size:0.7rem;position:relative;top:-4px;font-family:"Times New Roman","serif"'>Delivering / Receiving task:</span></i>
						</p>
					</td>
				</tr>
				<tr>
					<td width=265 valign=top align=left style='width:198.9pt;height: 17px;padding:0cm 5.4pt 0cm 5.4pt'>
						<p class="MsoHeader">
							<b><span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>Cảng dỡ/đích:</span></b>
							<span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>[POD]/[FPOD]</span>
						</p>
						<p class=MsoHeader>
							<i><span style='font-size:0.7rem;position:relative;top:-4px;font-family:"Times New Roman","serif"'>POD/FPOD:</span></i>
						</p>
					</td>
					<td width=198 valign=top align=left style='width:148.5pt;padding:0cm 5.4pt 0cm 5.4pt'>
						<p class=MsoHeader>
							<b><span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>Nội/Ngoại:</span></b>
							<b><span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>[IsLocal]</span></b>
						</p>
						<p class=MsoHeader>
							<i><span style='font-size:0.8rem;position:relative;top:-4px;font-family:"Times New Roman","serif"'>Local/Foreign:</span></i>
						</p>
					</td>
					<td width=301 valign=top align=left style='width:225.9pt;padding:0cm 5.4pt 0cm 5.4pt'>
						<p class=MsoHeader>
							<b><span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>Phương thức giao nhận:</span></b>
							<span style='font-size: 0.9rem;font-family:"Times New Roman","serif";'>[DMethod_CD]</span>
						</p>
						<p class=MsoHeader>
							<i><span style='font-size:0.7rem;position:relative;top:-4px;font-family:"Times New Roman","serif"'>Delivery Method:</span></i>
						</p>
					</td>
				</tr>
			</table>

			<p class=MsoHeader><b><span style='font-size:5.0pt;font-family:"Times New Roman","serif"'>&nbsp;</span></b></p>

			<table class=MsoTableGrid border=1 cellspacing=0 cellpadding=0 style='border-collapse:collapse;border:none'>
				<tr style='height:30.25pt'>
					<td width=17% valign=middle style='width:86.4pt;border:solid windowtext 1.0pt;padding:0cm 4.4pt 0cm 4.4pt;'>
						<p class=MsoHeader align=center style='text-align:center'>
							<b><span style='font-size:0.8rem;font-family:"Times New Roman","serif"'>Số Container</span></b>
						</p>
						<p class=MsoHeader align=center style='text-align:center'>
							<span style='font-size:0.75rem;font-family:"Times New Roman","serif"'>Container No.</span>
						</p>
					</td>
					<td width=11% valign=middle style='width:54.0pt;border:solid windowtext 1.0pt;border-left:none;padding:0cm 4.4pt 0cm 4.4pt;'>
						<p class=MsoHeader align=center style='text-align:center'>
							<b><span style='font-size:0.8rem;font-family:"Times New Roman","serif"'>Kích cỡ</span></b>
						</p>
						<p class=MsoHeader align=center style='text-align:center'>
							<span style='font-size:0.75rem;font-family:"Times New Roman","serif"'>Size</span>
						</p>
					</td>
					<td width=14% valign=middle style='width:72.0pt;border:solid windowtext 1.0pt;border-left:none;padding:0cm 4.4pt 0cm 4.4pt;'>
						<p class=MsoHeader align=center style='text-align:center'>
							<b><span style='font-size:0.8rem;font-family:"Times New Roman","serif"'>Vị trí</span></b>
						</p>
						<p class=MsoHeader align=center style='text-align:center'>
							<span style='font-size:0.75rem;font-family:"Times New Roman","serif"'>Location</span>
						</p>
					</td>
					<td width=14% valign=middle style='width:85.5pt;border:solid windowtext 1.0pt;border-left:none;padding:0cm 4.4pt 0cm 4.4pt;'>
						<p class=MsoHeader align=center style='text-align:center'>
							<b><span style='font-size:0.8rem;font-family:"Times New Roman","serif"'>Seal thực tế</span></b>
						</p>
						<p class=MsoHeader align=center style='text-align:center'>
							<span style='font-size:0.75rem;font-family:"Times New Roman","serif"'>Actual Seal</span>
						</p>
					</td>
					<td width=14% valign=middle style='width:76.5pt;border:solid windowtext 1.0pt;border-left:none;padding:0cm 4.4pt 0cm 4.4pt;'>
						<p class=MsoHeader align=center style='text-align:center'>
							<b><span style='font-size:0.8rem;font-family:"Times New Roman","serif"'>T. lượng</span></b>
						</p>
						<p class=MsoHeader align=center style='text-align:center'>
							<span style='font-size:0.75rem;font-family:"Times New Roman","serif"'>Weight</span>
						</p>
					</td>
					<td width=14% valign=middle style='width:72.0pt;border:solid windowtext 1.0pt;border-left:none;padding:0cm 4.4pt 0cm 4.4pt;'>
						<p class=MsoHeader align=center style='text-align:center'>
							<b><span style='font-size:0.8rem;font-family:"Times New Roman","serif"'>Loại hàng</span></b>
						</p>
						<p class=MsoHeader align=center style='text-align:center'>
							<span style='font-size:0.75rem;font-family:"Times New Roman","serif"'>Commodity</span>
						</p>
					</td>
					<td width=14% valign=middle style='width:72.0pt;border:solid windowtext 1.0pt;border-left:none;padding:0cm 4.4pt 0cm 4.4pt;'>
						<p class=MsoHeader align=center style='text-align:center'>
							<b><span style='font-size:0.8rem;font-family:"Times New Roman","serif"'>Nhiệt độ</span></b>
						</p>
						<p class=MsoHeader align=center style='text-align:center'>
							<span style='font-size:0.75rem;font-family:"Times New Roman","serif"'>Temp</span>
						</p>
					</td>
					<td width=14% valign=middle style='width:72.0pt;border:solid windowtext 1.0pt;border-left:none;padding:0cm 3.4pt 0cm 3.4pt;'>
						<p class=MsoHeader align=center style='text-align:center'>
							<b><span style='font-size:0.8rem;font-family:"Times New Roman","serif"'>Nguy hiểm</span></b>
						</p>
						<p class=MsoHeader align=center style='text-align:center'>
							<span style='font-size:0.75rem;font-family:"Times New Roman","serif"'>Class-Unno</span>
						</p>
					</td>
					<td width=14% valign=middle style='width:126.9pt;border:solid windowtext 1.0pt;border-left:none;padding:0cm 4.4pt 0cm 4.4pt;'>
						<p class=MsoHeader align=center style='text-align:center'><b>
								<span style='font-size:rem;font-family:"Times New Roman","serif"'>Ghi chú</span></b></p>
						<p class=MsoHeader align=center style='text-align:center'>
							<span style='font-size:0.75rem;font-family:"Times New Roman","serif"'>Remark</span>
						</p>
					</td>
				</tr>

				<tr style='height:33.25pt'>
					<td width=115 valign=middle style='width:86.4pt;border:solid windowtext 1.0pt;border-top:none;padding:0cm 4.4pt 0cm 4.4pt;'>
						<p class=MsoHeader align=center style='text-align:center'>
							<b><span style='font-size:0.8rem;font-family:"Times New Roman","serif"'>[CntrNo]</span></b>
						</p>
					</td>
					<td width=72 valign=middle style='width:54.0pt;border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;padding:0cm 4.4pt 0cm 4.4pt;'>
						<p class=MsoHeader align=center style='text-align:center'>
							<span style='font-size:0.8rem;font-family:"Times New Roman","serif"'>[LocalSZPT]</span>
						</p>
					</td>
					<td width=96 valign=middle style='width:72.0pt;border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;padding:0cm 4.4pt 0cm 4.4pt;'>
						<p class=MsoHeader align=center style='text-align:center'>
							<span style='font-size:0.8rem;font-family:"Times New Roman","serif"'>[YardPos]</span>
						</p>
					</td>
					<td width=114 valign=middle style='width:85.5pt;border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;padding:0cm 4.4pt 0cm 4.4pt;'>
						<p class=MsoHeader align=center style='text-align:center'>
							<span style='font-size:0.8rem;font-family:"Times New Roman","serif"'>[SealNo]</span>
						</p>
					</td>
					<td width=102 valign=middle style='width:76.5pt;border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;padding:0cm 4.4pt 0cm 4.4pt;'>
						<p class=MsoHeader align=center style='text-align:center'>
							<span style='font-size:0.8rem;font-family:"Times New Roman","serif"'>[CMDWeight]</span>
						</p>
					</td>
					<td width=96 valign=middle style='width:72.0pt;border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;padding:0cm 4.4pt 0cm 4.4pt;'>
						<p class=MsoHeader align=center style='text-align:center'>
							<span style='font-size:0.8rem;font-family:"Times New Roman","serif"'>[CARGO_TYPE_NAME]</span>
							<br />
							<span style='font-size:0.8rem;font-family:"Times New Roman","serif"'>[CARGO_ADD_INFO]</span>
						</p>
					</td>
					<td width=102 valign=middle style='width:76.5pt;border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;padding:0cm 4.4pt 0cm 4.4pt;'>
						<p class=MsoHeader align=center style='text-align:center'>
							<span style='font-size:0.8rem;font-family:"Times New Roman","serif"'>[Temperature]</span>
						</p>
					</td>
					<td width=102 valign=middle style='width:76.5pt;border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;padding:0cm 4.4pt 0cm 4.4pt;'>
						<p class=MsoHeader align=center style='text-align:center'>
							<span style='font-size:0.8rem;font-family:"Times New Roman","serif"'>[CLASS] - [UNNO]</span>
						</p>
					</td>
					<td width=169 valign=middle style='width:126.9pt;border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;padding:0cm 4.4pt 0cm 4.4pt;'>
						<p class=MsoHeader align=center style='text-align:center;text-align:center;height:100%'>
							<span style='font-size:0.8rem;font-family:"Times New Roman","serif"'>[Note]</span>
						</p>
					</td>
				</tr>
			</table>

			<p class=MsoHeader><span style='font-size:0.5rem;font-family:"Times New Roman","serif"'>&nbsp;</span></p>

			<table class=MsoTableGrid border=0 cellspacing=0 cellpadding=0 style='border-collapse:collapse;border:none'>
				<tr>
					<td width=280 valign=top style='width:200.0pt;'>
						<p class=MsoHeader align=center style='text-align:center'>
							<b><span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>QR Code</span></b>
						</p>
					</td>
					<td width=232 valign=top style='width:173.65pt;padding:0cm 5.4pt 0cm 5.4pt'>
						<p class=MsoHeader align=center style='text-align:center'>
							<b><span style='font-size:1rem;font-family:"Times New Roman","serif"'>&nbsp;</span></b>
						</p>
					</td>
					<td width=253 valign=top style='width:189.65pt;padding:0cm 5.4pt 0cm 5.4pt'>
						<p class=MsoHeader align=center style='text-align:center'>
							<b><span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>Người phát hành</span></b>
						</p>
					</td>
				</tr>
				<tr>
					<td width=280 valign=top align=center style='width:200.0pt;padding:0cm 5.4pt 0cm 5.4pt'>
						<p class=MsoHeader><img id="qr-img" width=100 src="<?= $qr_url ?>" align=center hspace=12></p>
					</td>
					<td width=232 valign=top style='width:183.65pt;padding:0cm 5.4pt 0cm 5.4pt'>
						<p class=MsoHeader align=center style='text-align:center'>
							<span style='position:absolute;z-index:251674624;left:0px;margin-left:-4px;width:226px;height:75px'>
								[cTLHQ]
							</span>
						</p>
					</td>
					<td width=253 valign=bottom style='width:189.65pt;padding:0cm 5.4pt 5.4pt 5.4pt'>
						<p class=MsoHeader align=center style='text-align:center'>
							<b><span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>[UserName]</span></b>
						</p>
					</td>
				</tr>
				<tr>
					<td width=764 colspan=3 valign=top style='width:573.3pt;padding:0cm 5.4pt 0cm 5.4pt'>
						<p class=MsoHeader align=center style='text-align:center'>
							<b><i><span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>Đề nghị khách
										hàng không đưa tiền cho Xe nâng/ Giao nhận</span></i></b>
							</br>
							<b><i><span style='font-size:0.9rem;font-family:"Times New Roman","serif"'>Hotline: Lãnh đạo bãi: 0945.101.199 – Trực ban Khai thác bãi: 0948.977.956</span></i></b>
						</p>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>

</html>