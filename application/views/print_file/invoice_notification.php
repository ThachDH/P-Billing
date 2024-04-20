<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<style type="text/css">
		.p-container{
			font-size: 12px;
			padding: 4px;
			margin-bottom: 30px;
		}

		.p-container .title{
			margin-bottom: 16px;
		}

		.p-container .divider{
			margin: 12px 0;
			width: 85%;
			border-top: 1px dashed;
		}

		.p-container > h4, h3{
			text-transform: uppercase;
			font-weight: bold;
		}

		.p-container .title{
			text-align: center;
		}

		.textwlabel{
			display: flex;
			flex-direction: row;
			margin-bottom: 8px;
		}

		.textwlabel > p{
			margin: 0;
			flex-basis: 70%;
		}

		.detail-1 .textwlabel > span{
			flex-basis: 38%;
		}

		.detail-1 .textwlabel > p{
			font-weight: bold;
		}

		.detail-2 .textwlabel > span{
			flex-basis: 30%;
		}
	</style>
</head>
<body>
	<div class="p-container">
		<h4 class="terminal">
			<?= $yardName ?>
		</h4>
		<div class="title">
			<h3>
				Phiếu báo tra cứu hóa đơn
			</h3>
			<span id="p-time">[PrintDate]</span>
		</div>
		<div class="detail-1">
			<div class="textwlabel">
				<span>Số hóa đơn: </span>
				<p id="p-blNo">[INV_NO]</p>
			</div>
			<div class="textwlabel">
				<span>Số tiền: </span>
				<p id="p-moneyAmount">[TAMOUNT] <span id="p-moneyType">[CURRENCYID]</span></p>
			</div>
			<div class="textwlabel">
				<span>Mã tra cứu: </span>
				<p style="font-size: 15px;" id="p-trackingNo">[PinCode]</p>
			</div>
			<div class="textwlabel">
				<span>Mã khách hàng: </span>
				<p id="p-custumerNo">[CusID]</p>
			</div>
		</div>
		<div class="divider"></div>
		<div class="detail-2">
			<div class="textwlabel">
				<span>Tên đơn vị: </span>
				<p id="p-customerName">[CusName]</p>
			</div>
			<div class="textwlabel">
				<span>Địa chỉ: </span>
				<p id="p-blNo">[Address]</p>
			</div>
			<div class="textwlabel">
				<span>MST: </span>
				<p id="p-MST">[VAT_CD]</p>
			</div>
		</div>
		<div class="divider"></div>
		<div class="footer">
			<p class="description">Quý khách vui lòng tra cứu hóa đơn tại
				<br>
				<?= $vnptPortalUrl ?>
			</p > 
			<b style="font-size: 15px">HOTLINE: <?= $hotline ?></b>
		</div>
	</div>
</body>
</html>

