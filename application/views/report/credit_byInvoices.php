<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<link href="<?= base_url('assets/vendors/bootstrap-select/dist/css/bootstrap-select.min.css'); ?>" rel="stylesheet" />
<link href="<?= base_url('assets/vendors/dataTables/extensions/buttons.dataTables.min.css'); ?>" rel="stylesheet" />

<style>
	.ui-icon {
		margin-top: -0.9em !important;
		margin-left: -8px !important;
	}

	tfoot tr th {
		font-size: 13px !important;
		padding: 10px 8px !important;
	}

	#payer-modal .dataTables_filter {
		padding-left: 10px !important;
	}

	.ui-autocomplete {
		max-height: 200px;
		overflow-y: auto;
		/* prevent horizontal scrollbar */
		overflow-x: hidden;
		/* add padding to account for vertical scrollbar */
		padding-right: 20px;
	}
</style>

<div class="row">
	<div class="col-xl-12">
		<div class="ibox collapsible-box">
			<i class="la la-angle-double-up dock-right"></i>
			<div class="ibox-head">
				<div class="ibox-title">BÁO CÁO DOANH THU HOÁ ĐƠN THU SAU</div>
				<div class="button-bar-group mr-3">
					<button type="button" id="search" title="Nạp dữ liệu" data-loading-text="<i class='la la-spinner spinner'></i>Đang nạp" class="btn btn-sm btn-outline-primary mr-1">
						<i class="fa fa-refresh"></i>
						Nạp dữ liệu
					</button>
					<button type="button" id="report-excel" title="Xuất báo cáo" data-loading-text="<i class='la la-spinner spinner'></i>Đang xuất" class="btn btn-sm btn-outline-secondary mr-1">
						<i class="fa fa-fa-file-excel-o"></i>
						Xuất báo cáo
					</button>
				</div>
			</div>
			<div class="ibox-body pt-3 pb-3 bg-f9 border-e">
				<form id="frmdata_export" method="post" action="<?= site_url(md5('Report') . '/' . md5('export_credit')); ?>">
					<div class="row border-e bg-white pb-1">
						<div class="col-xs-3 col-md-3 col-lg-2 col-xl-2 mt-3">
							<div class="form-group">
								<label class="mb-0">Ngày hoá đơn</label>
							</div>
							<div class="form-group input-group">
								<input class="form-control form-control-sm text-center mr-2" id="fromDate" name="fromDate" type="text" placeholder="Từ ngày">
								<input id="toDate" name="toDate" class="form-control form-control-sm text-center" type="text" placeholder="Đến ">
							</div>
						</div>
						<div class="col-xs-3 col-md-3 col-lg-2 col-xl-2 mt-3">
							<div class="form-group">
								<label class="mb-0">Tàu chuyến</label>
							</div>
							<div class="form-group input-group">
								<input class="form-control form-control-sm input-required" id="shipid" placeholder="Tàu/chuyến" type="text" readonly>
								<span class="input-group-addon bg-white btn mobile-hiden text-warning" style="padding: 0 .5rem" title="chọn tàu" data-toggle="modal" data-target="#ship-modal">
									<i class="ti-search"></i>
								</span>
							</div>
						</div>
						<div class="col-xs-2 col-md-2 col-lg-2 col-xl-2 mt-3">
							<div class="form-group">
								<label class="mb-0">Đối tượng thanh toán</label>
							</div>
							<div class="form-group input-group">
								<input class="hiden-input" id="cusID" readonly>
								<input class="form-control form-control-sm input-required" id="taxcode" placeholder="ĐTTT" type="text" readonly>
								<span class="input-group-addon bg-white btn mobile-hiden text-warning" style="padding: 0 .5rem" title="Chọn đối tượng thanh toán" data-toggle="modal" data-target="#payer-modal">
									<i class="ti-search"></i>
								</span>
							</div>
						</div>
						<div class="col-xs-2 col-md-2 col-lg-1 col-xl-1 mt-3">
							<div class="form-group">
								<label class="mb-0">Hình thức</label>
							</div>
							<div class="form-group">
								<select id="payment_type" name="payment_type" class="selectpicker" data-style="btn-default btn-sm" data-width="100%">
									<option value="" selected>*</option>
									<option value="TM">Tiền mặt</option>
									<option value="CK">Chuyển khoản</option>
									<option value="TM/CK">TM / CK</option>
								</select>
							</div>
						</div>

						<div class="col-xs-1 col-md-1 col-lg-1 col-xl-1 mt-3">
							<div class="form-group">
								<label class="mb-0">Loại tiền</label>
							</div>
							<div class="form-group">
								<select id="currencyid" name="currencyid" class="selectpicker" data-style="btn-default btn-sm" data-width="100%">
									<option value="VND" selected>VND</option>
									<option value="USD">USD</option>
								</select>
							</div>
						</div>

						<div class="col-xs-12 col-md-3 col-lg-2 col-xl-2 mt-3">
							<div class="form-group">
								<label class="mb-0">Loại hóa đơn</label>
							</div>
							<div class="form-group">
								<select id="adjust-type" name="adjust-type" class="selectpicker" data-style="btn-default btn-sm bg-white" data-width="100%">
									<option value="" selected>* (Tất cả)</option>
									<option value="0">Hóa đơn gốc</option>
									<option value="1">HĐ thay thế</option>
									<option value="2">HĐ điều chỉnh tăng</option>
									<option value="3">HĐ điều chỉnh giảm</option>
									<option value="4">HĐ điều chỉnh thông tin</option>
								</select>
							</div>
						</div>

						<div class="col-xs-1 col-md-1 col-lg-2 col-xl-2 mt-3">
							<div class="form-group">
								<label class="mb-0">Lập bởi</label>
							</div>
							<div class="form-group">
								<input id="createdBy" name="CreatedBy" class="form-control form-control-sm" type="text" placeholder="Người lập hoá đơn" autocomplete="on">
							</div>
						</div>

					</div>
					<div class="row border-e bg-white pb-1 mt-2 hidden-input">
						<div class="col-xs-3 col-md-3 col-lg-2 col-xl-2 mt-3">
							<div class="row form-group">
								<div class="col-sm-12 input-group input-group-sm col-form-label">
									<label class="radio radio-inline">
										<input type="radio" name="sys" value="BL" checked>
										<span class="input-span"></span>BILLING</label>
									<label class="radio radio-inline">
										<input type="radio" name="sys" value="EP">
										<span class="input-span"></span>EPORT</label>
								</div>
							</div>
						</div>
					</div>
					<input id="exportdata" name="exportdata" type="text" style="display: none">
				</form>
			</div>
			<div class="row ibox-footer" style="border-top: 0">
				<div class="col-md-12 col-sm-12 col-xs-12 table-responsive">
					<div id="tablecontent">
						<table id="contenttable" class="table table-striped display nowrap" cellspacing="0" style="width: 99.9%">
							<thead>
								<tr>
									<th>STT</th>
									<th>Số HĐ</th>
									<th>Ngày HĐ</th>
									<th>Số PTC</th>
									<th>Số Lệnh</th>
									<th>Số PinCode</th>
									<th>Mã Biểu Cước</th>
									<th>Diễn Giải</th>
									<th>Kích Cỡ</th>
									<th>Số Lượng</th>
									<th>Thành Tiền</th>
									<th>Chiết Khấu</th>
									<th>% Thuế</th>
									<th>Tiền Thuế</th>
									<th>Tổng Tiền</th>
									<th>Loại Hóa đơn</th>
									<th>Tỷ giá</th>
									<th>HTTT</th>
									<th>ĐTTT</th>
									<th>MST</th>
									<th>Lập Bởi</th>
									<th>Ghi Chú</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
							<tfoot>
								<tr style="color:red; font-size:13px">
									<th colspan="6"></th>
									<th style="font-weight: bold;">TỔNG CỘNG</th>
									<th></th>
									<th></th>
									<th class="text-right">0</th>
									<th class="text-right">0</th>
									<th class="text-right">0</th>
									<th class="text-right"></th>
									<th class="text-right">0</th>
									<th class="text-right">0</th>
									<th colspan="7"></th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!--select ship-->
<div class="modal fade" id="ship-modal" tabindex="-1" role="dialog" aria-labelledby="groups-modalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-mw" role="document" style="min-width: 960px">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="groups-modalLabel">Chọn tàu</h5>
			</div>
			<div class="modal-body" style="padding: 10px 0">
				<div class="row col-xl-12">
					<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 mt-1">
						<div class="form-group">
							<label class="radio radio-outline-primary" style="padding-right: 15px!important;">
								<input name="shipArrStatus" type="radio" value="1" checked>
								<span class="input-span"></span>
								Đến cảng
							</label>
							<label class="radio radio-outline-primary">
								<input name="shipArrStatus" value="2" type="radio">
								<span class="input-span"></span>
								Rời Cảng
							</label>
						</div>
					</div>
					<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 pr-0">
						<div class="row form-group">
							<div class="col-sm-12 pr-0">
								<div class="input-group">
									<select id="cb-searh-year" class="selectpicker" data-width="30%" data-style="btn-default btn-sm">
									</select>
									<input class="form-control form-control-sm mr-2 ml-2" id="search-ship-name" type="text" placeholder="Nhập tên tàu">
									<img id="btn-search-ship" class="pointer" src="<?= base_url('assets/img/icons/Search.ico'); ?>" style="height:25px; width:25px; margin-top: 5px;cursor: pointer" title="Tìm kiếm" />
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<table id="search-ship" class="table table-striped display nowrap table-popup single-row-select" cellspacing="0" style="width: 99.8%">
						<thead>
							<tr>
								<th>Mã Tàu</th>
								<th style="width: 20px">STT</th>
								<th>Tên Tàu</th>
								<th>Chuyến Nhập</th>
								<th>Chuyến Xuất</th>
								<th>Ngày Cập</th>
								<th>ShipKey</th>
								<th>BerthDate</th>
								<th>Ship Year</th>
								<th>Ship Voy</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<div style="display: flex; flex: 1; justify-content: flex-start; align-items: center;">
					<button type="button" id="reload-ship" class="btn btn-sm btn-warning">
						<i class="fa fa-refresh"></i>
						Tải lại
					</button>
				</div>
				<button type="button" id="select-ship" class="btn btn-sm btn-outline-primary" data-dismiss="modal">
					<i class="fa fa-check"></i>
					Chọn
				</button>
				<button type="button" class="btn btn-sm btn-outline-secondary" data-dismiss="modal">
					<i class="fa fa-close"></i>
					Đóng
				</button>
			</div>
		</div>
	</div>
</div>

<!--payer modal-->
<div class="modal fade" id="payer-modal" tabindex="-1" role="dialog" aria-labelledby="groups-modalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-mw" role="document" style="min-width: 960px">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="groups-modalLabel">Chọn đối tượng thanh toán</h5>
			</div>
			<div class="modal-body" style="padding: 10px 0">
				<div class="table-responsive">
					<table id="search-payer" class="table table-striped display nowrap table-popup single-row-select" cellspacing="0" style="width: 100%">
						<thead>
							<tr>
								<th>STT</th>
								<th>Mã ĐT</th>
								<th>MST</th>
								<th>Tên</th>
								<th>Địa chỉ</th>
								<th>HTTT</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer" style="position: relative; padding: 22px 15px !important">
				<button type="button" id="select-payer" class="btn btn-outline-primary" data-dismiss="modal">
					<i class="fa fa-check"></i>
					Chọn
				</button>
				<button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
					<i class="fa fa-close"></i>
					Đóng
				</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		var _selectShipKey,
			_colPayer = ["STT", "CusID", "VAT_CD", "CusName", "Address", "CusType"];

		$('#report-excel').on('click', function() {
			if (!$("#exportdata").val()) {
				toastr.warning('Không có dữ liệu!');
				return;
			}
			$('#frmdata_export').submit();
		});

		$('#contenttable').DataTable({
			buttons: [{
				extend: 'excel',
				text: '<i class="fa fa-table fainfo"></i> Xuất Excel',
				titleAttr: 'Xuất Excel',
				footer: true,
				// customize: function(xlsx) {
				// 	var sheet = xlsx.xl.worksheets['sheet1.xml'];
				// 	$('row c[r^="K"], row c[r^="M"], row c[r^="N"]', sheet).attr('s', 63);
				// }
			}],
			paging: true,
			searching: true,
			scrollY: '42vh',
			columnDefs: [{
					targets: [7, 18, 21],
					render: function(data, type, full, meta) {
						return "<div class='wrap-text width-250'>" + data + "</div>";
					}
				},
				{
					className: "text-center",
					targets: [0, 1, 2, 3, 8]
				},
				{
					className: "text-right",
					type: 'num',
					targets: [9],
					render: $.fn.dataTable.render.number(',', '.', 2)
				},
				{
					className: "text-right",
					targets: [10, 11, 12, 13, 14],
					render: $.fn.dataTable.render.number(',', '.', 2)
				},
				{
					className: "text-right",
					visible: false,
					targets: [16],
					render: $.fn.dataTable.render.number(',', '.', 0)
				},
			],
			order: [
				[0, 'asc']
			],
			scroller: {
				displayBuffer: 20,
				boundaryScale: 0.5,
				loadingIndicator: true
			},
			footerCallback: function(row, datas, start, end, display) {
				let api = this.api();
				let data = api.rows({
					search: 'applied'
				}).data().toArray();
				if (data.length > 0) {
					let frmat = $('#currencyid').val() == 'VND' ? "#,###" : "#,###.00";
					let sumQty = data.map(p => p[9] || 0).reduce(function(a, b) {
						return parseFloat(a) + parseFloat(b);
					}, 0);
					let sumAmt = data.map(p => p[10] || 0).reduce(function(a, b) {
						return parseFloat(a) + parseFloat(b);
					}, 0);
					let sumDis = data.map(p => p[11] || 0).reduce(function(a, b) {
						return parseFloat(a) + parseFloat(b);
					}, 0);
					let sumVat = data.map(p => p[13] || 0).reduce(function(a, b) {
						return parseFloat(a) + parseFloat(b);
					}, 0);
					let sumTamount = data.map(p => p[14] || 0).reduce(function(a, b) {
						return parseFloat(a) + parseFloat(b);
					}, 0);

					$(api.column(9).footer()).html($.formatNumber(sumQty, {
						format: "#,###.00",
						locale: "us"
					}));
					$(api.column(10).footer()).html($.formatNumber(sumAmt, {
						format: frmat,
						locale: "us"
					}));
					$(api.column(11).footer()).html($.formatNumber(sumDis, {
						format: frmat,
						locale: "us"
					}));
					$(api.column(13).footer()).html($.formatNumber(sumVat, {
						format: frmat,
						locale: "us"
					}));
					$(api.column(14).footer()).html($.formatNumber(sumTamount, {
						format: frmat,
						locale: "us"
					}));

				} else {
					$(api.column(9).footer()).html(0);
					$(api.column(10).footer()).html(0);
					$(api.column(11).footer()).html(0);
					$(api.column(13).footer()).html(0);
					$(api.column(14).footer()).html(0);
				}
			}
		});

		$('#search-ship').DataTable({
			scrollY: '35vh',
			paging: false,
			columnDefs: [{
					className: "input-hidden",
					targets: [0, 6, 7]
				},
				{
					className: "text-center",
					targets: [1]
				}
			],
			buttons: [],
			info: false,
			searching: false
		});

		$('#search-payer').DataTable({
			paging: true,
			scroller: {
				displayBuffer: 12,
				boundaryScale: 0.5
			},
			columnDefs: [{
					type: "num",
					targets: [0]
				},
				{
					render: function(data, type, full, meta) {
						return "<div class='wrap-text width-250'>" + data + "</div>";
					},
					targets: _colPayer.getIndexs(["CusName", "Address"])
				}
			],
			buttons: [],
			infor: false,
			scrollY: '45vh'
		});

		var usid = <?= json_encode($userIds) ?>;
		$("#createdBy").autocomplete({
			source: usid.map(p => p.UserID),
			minLength: 0
		});

		$('#createdBy').mousedown(function() {
			if (document.activeElement == this) return;
			$(this).focus();
		});

		//set from date, to date
		var fromDate = $('#fromDate');
		var toDate = $('#toDate');
		setDateTimeRange(fromDate, toDate); //, 'yy-mm-dd', 'HH:mm:ss'

		fromDate.val(moment().subtract(1, 'day').format('DD/MM/YYYY 00:00'));
		toDate.val(moment().format('DD/MM/YYYY 23:59'));

		///////// SEARCH SHIP
		autoLoadYearCombo('cb-searh-year');

		search_ship();

		$('#btn-search-ship').on('click', function() {
			search_ship();
		});

		$(document).on('click', '#search-ship tbody tr', function() {
			$('.m-row-selected').removeClass('m-row-selected');
			$(this).addClass('m-row-selected');
		});
		$('#search-ship-name').on('keypress', function(e) {
			if (e.which == 13) {
				search_ship();
			}
		});
		$('#select-ship').on('click', function() {
			var r = $('#search-ship tbody').find('tr.m-row-selected').first();

			$('#shipid').val($(r).find('td:eq(0)').text() + "/" + $(r).find('td:eq(3)').text() + "/" + $(r).find('td:eq(4)').text());
			$('#shipid').removeClass('error');

			_selectShipKey = $(r).find('td:eq(6)').text();
		});

		$('#search-ship').on('dblclick', 'tbody tr td', function() {
			var r = $(this).parent();

			$('#shipid').val($(r).find('td:eq(0)').text() + "/" + $(r).find('td:eq(3)').text() + "/" + $(r).find('td:eq(4)').text());
			$('#shipid').removeClass('error');

			_selectShipKey = $(r).find('td:eq(6)').text();

			$('#ship-modal').modal("hide");
		});

		$('#unselect-ship').on('click', function() {
			$('#shipid').val('');
		});

		$('#reload-ship').on("click", function() {
			$('#search-ship-name').val("");
			search_ship();
		})
		///////// END SEARCH SHIP

		///////// SEARCH PAYER
		load_payer();
		$(document).on('click', '#search-payer tbody tr', function() {
			$("#search-payer").DataTable().rows('.m-row-selected').nodes().to$().removeClass("m-row-selected");
			$($("#search-payer").DataTable().row($(this)).node()).addClass("m-row-selected");
		});

		$('#select-payer').on('click', function() {
			var r = $('#search-payer tbody').find('tr.m-row-selected').first();

			$('#taxcode').val($(r).find('td:eq(' + _colPayer.indexOf("VAT_CD") + ')').text());
			$('#cusID').val($(r).find('td:eq(' + _colPayer.indexOf("CusID") + ')').text());
		});

		$('#search-payer').on('dblclick', 'tbody tr td', function() {
			var r = $(this).parent();

			$('#taxcode').val($(r).find('td:eq(' + _colPayer.indexOf("VAT_CD") + ')').text());
			$('#cusID').val($(r).find('td:eq(' + _colPayer.indexOf("CusID") + ')').text());

			$('#payer-modal').modal("toggle");
		});
		///////// END SEARCH PAYER

		$('#ship-modal, #payer-modal').on('shown.bs.modal', function(e) {
			$($.fn.dataTable.tables(true)).DataTable()
				.columns
				.adjust();
		});

		$('#search').on('click', function() {
			$("#contenttable").waitingLoad();

			// var jmode = $('#jmode').val();

			// var jdate = getFilterDate();
			var formData = {
				'action': 'view',
				'fromDate': $("#fromDate").val(),
				'toDate': $("#toDate").val(),
				'cusID': $("#cusID").val(),
				'shipKey': _selectShipKey,
				'createdBy': $("#createdBy").val(),
				'currencyId': $("#currencyid").val(),
				'payment_type': $("#payment_type").val(),
				"sys": $("input[name='sys']:checked").val()
			};

			$.ajax({
				url: "<?= site_url(md5('Report') . '/' . md5('rptCreditByInvoices')); ?>",
				dataType: 'json',
				data: formData,
				type: 'POST',
				success: function(data) {
					var rows = [];

					if (data.results.length > 0) {
						$('#exportdata').val(JSON.stringify(data.results));

						var i = 1;
						$.each(data.results, function(idx, r) {
							r.INV_DATE = getDateTime(r.INV_DATE);
							r.REMARK = r.REMARK ? r.REMARK.split(',').join(', ') : '';
							switch (parseInt(r.AdjustType)) {
								case 1:
									r.AdjustType = 'Thay thế';
									break;
								case 2:
									r.AdjustType = 'Điều chỉnh tăng';
									break;
								case 3:
									r.AdjustType = 'Điều chỉnh giảm';
									break;
								case 4:
									r.AdjustType = 'Điều chỉnh thông tin';
									break;
								default:
									r.AdjustType = 'HĐ gốc';
									break;
							}

							var temp = Object.values(r);
							temp.splice(0, 0, i);
							rows.push(temp);
							i++;
						});
					}

					$('#contenttable').dataTable().fnClearTable();
					if (rows.length > 0) {
						$('#contenttable').dataTable().fnAddData(rows);
					}

					let isShowExchangeRateCol = formData.currencyId === 'USD';
					$("#contenttable").DataTable().column(16).visible(isShowExchangeRateCol);
				},
				error: function(err) {
					console.log(err);
				}
			});
		});

		function load_payer() {
			var tblPayer = $('#search-payer');
			tblPayer.waitingLoad();

			$.ajax({
				url: "<?= site_url(md5('Report') . '/' . md5('rptCreditByInvoices')); ?>",
				dataType: 'json',
				data: {
					'action': 'view',
					'act': 'load_payer'
				},
				type: 'POST',
				success: function(data) {
					var rows = [];

					if (data.payers && data.payers.length > 0) {
						payers = data.payers;

						var i = 0;
						$.each(payers, function(index, rData) {
							var r = [];
							$.each(_colPayer, function(idx, colname) {
								var val = "";
								switch (colname) {
									case "STT":
										val = i + 1;
										break;
									case "CusType":
										val = !rData[colname] ? "" : (rData[colname] == "M" ? "Thu ngay" : "Thu sau");
										break;
									default:
										val = rData[colname] ? rData[colname] : "";
										break;
								}
								r.push(val);
							});
							i++;
							rows.push(r);
						});
					}

					tblPayer.dataTable().fnClearTable();
					if (rows.length > 0) {
						tblPayer.dataTable().fnAddData(rows);
					}
				},
				error: function(err) {
					tblPayer.dataTable().fnClearTable();
					console.log(err);
					toastr["error"]("Có lỗi xảy ra! Vui lòng liên hệ với kỹ thuật viên! <br/>Cảm ơn!");
				}
			});
		};

		function search_ship() {
			$("#search-ship").waitingLoad();
			var formdata = {
				'action': 'view',
				'act': 'searh_ship',
				'arrStatus': $('input[name="shipArrStatus"]:checked').val(),
				'shipyear': $('#cb-searh-year').val(),
				'shipname': $('#search-ship-name').val()
			};
			$.ajax({
				url: "<?= site_url(md5('Report') . '/' . md5('rptCreditByInvoices')); ?>",
				dataType: 'json',
				data: formdata,
				type: 'POST',
				success: function(data) {
					var rows = [];
					if (data.vsls.length > 0) {
						for (i = 0; i < data.vsls.length; i++) {
							rows.push([
								data.vsls[i].ShipID, (i + 1), data.vsls[i].ShipName, data.vsls[i].ImVoy, data.vsls[i].ExVoy, getDateTime(data.vsls[i].ETB), data.vsls[i].ShipKey, getDateTime(data.vsls[i].BerthDate), data.vsls[i].ShipYear, data.vsls[i].ShipVoy
							]);
						}
					}
					$('#search-ship').DataTable({
						scrollY: '35vh',
						paging: false,
						order: [
							[1, 'asc']
						],
						columnDefs: [{
								className: "input-hidden",
								targets: [0, 6, 7]
							},
							{
								className: "text-center",
								targets: [1]
							}
						],
						buttons: [],
						info: false,
						searching: false,
						data: rows
					});
				},
				error: function(err) {
					console.log(err);
				}
			});
		}
	});
</script>

<script src="<?= base_url('assets/vendors/moment/min/moment.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/bootstrap-select/dist/js/bootstrap-select.min.js'); ?>"></script>
<!--format number-->
<script src="<?= base_url('assets/js/jshashtable-2.1.js'); ?>"></script>
<script src="<?= base_url('assets/js/jquery.numberformatter-1.2.3.min.js'); ?>"></script>

<script src="<?= base_url('assets/vendors/dataTables/datatables.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/dataTables/dataTables.buttons.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/dataTables/extensions/jszip.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/dataTables/extensions/buttons.html5.min.js'); ?>"></script>