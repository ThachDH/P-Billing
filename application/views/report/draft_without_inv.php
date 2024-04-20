<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<link href="<?= base_url('assets/vendors/bootstrap-select/dist/css/bootstrap-select.min.css'); ?>" rel="stylesheet" />
<link href="<?= base_url('assets/vendors/jquery-confirm/jquery-confirm.min.css'); ?>" rel="stylesheet" />
<link href="<?= base_url('assets/vendors/dataTables/extensions/buttons.dataTables.min.css'); ?>" rel="stylesheet" />

<style>
	.nav-tabs {
		height: inherit !important;
	}

	.m-row-selected {
		background: violet;
	}

	.MT-toggle,
	.PY-toggle {
		display: none;
	}

	.MT-toggle button,
	.PY-toggle button {
		background-color: #fff !important;
	}

	.grid-hidden {
		display: none;
	}

	.modal-dialog-mw-py {
		position: fixed;
		top: 20%;
		margin: 0;
		width: 100%;
		padding: 0;
		max-width: 100% !important;
	}

	.modal-dialog-mw-py .modal-body {
		width: 90% !important;
		margin: auto;
	}

	.unchecked-Salan {
		pointer-events: none;
	}

	span.col-form-label {
		width: 70%;
		border-bottom: dotted 1px;
		display: inline-block;
		word-wrap: break-word;
	}

	#INV_DRAFT_TOTAL span.col-form-label {
		width: 64%;
		border-bottom: dotted 1px;
		display: inline-block;
		word-wrap: break-word;
	}

	.dataTable th label.checkbox span.input-span,
	.dataTable td label.checkbox span.input-span {
		height: 16px !important;
		width: 16px !important;
		left: 5px !important;
		border-color: #000060 !important;

	}

	.dataTable th label.checkbox span.input-span:after,
	.dataTable td label.checkbox span.input-span:after {
		left: 5px !important;
		top: 1px !important;
	}

	#payer-modal .dataTables_filter {
		padding-left: 10px !important;
	}

	.success-head-icon {
		position: relative;
		height: 100px;
		width: 100px;
		display: inline-flex;
		align-items: center;
		justify-content: center;
		font-size: 55px;
		background-color: #fff;
		color: green;
		border-radius: 50%;
		transform: translateY(-25%);
		z-index: 2;
		border: solid 10px green;
	}

	button:disabled {
		color: #929394 !important;
		border-color: #929394;
		cursor: not-allowed;
	}

	button:disabled:hover {
		background-color: transparent !important;
	}

	.m-show-modal {
		position: fixed;
		top: 0;
		left: 0;
		width: 100vw;
		height: 100vh;
		display: none;
		z-index: 1002
	}

	.m-show-modal .m-modal-background {
		background-color: rgba(0, 0, 0, 0.5);
		width: 100%;
		height: 100%;
		top: 0;
		left: 0;
		position: absolute;
		z-index: 98
	}

	.m-show-modal .m-modal-content {
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		z-index: 99
	}

	.m-close-modal {
		position: fixed;
		z-index: 100;
		top: 0;
		right: 7.5vw;
		color: #4a4a4b;
		cursor: pointer;
	}

	.m-close-modal i {
		padding: 5px;
		border-radius: 50%;
	}

	.m-close-modal i:hover {
		background-color: rgba(255, 255, 255, 0.1);
	}

	.dropdown-item {
		padding: .95rem 3.5rem !important;
	}

	.btn.dropdown-arrow:after {
		left: .7rem !important;
	}

	.m-hidden {
		display: none;
	}

	.col-form-label .checkbox.checkbox-inline {
		min-width: 95px;
	}

	.filter-group .row.form-group {
		margin-bottom: .5rem !important;
	}

	.ui-autocomplete {
		max-height: 200px;
		overflow-y: auto;
		/* prevent horizontal scrollbar */
		overflow-x: hidden;
		/* add padding to account for vertical scrollbar */
		padding-right: 20px;
	}

	.box-group {
		border: 1px solid #ccc !important;
		margin-left: -10px;
		padding-top: 10px !important;
		border-radius: 3px !important;
	}

	.box-group::before {
		content: "Tìm kiếm theo loại phiếu";
		top: -16px;
		position: absolute;
		left: 19px;
		font-size: 12px;
		background: white;
		padding: 5px;
	}
</style>

<div class="row" style="font-size: 12px!important;">
	<div class="col-xl-12">
		<div class="ibox collapsible-box">
			<i class="la la-angle-double-up dock-right"></i>
			<div class="ibox-head">
				<div class="ibox-title">DANH SÁCH PHIẾU TÍNH CƯỚC</div>
				<div class="button-bar-group mr-3">
					<button type="button" id="load-data" title="Nạp dữ liệu" data-loading-text="<i class='la la-spinner spinner'></i>Đang nạp" class="btn btn-sm btn-outline-primary mr-1">
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
				<form id="frmdata_export" method="post" action="<?= site_url(md5('Report') . '/' . md5('export_draft_without_inv')); ?>">
					<div class="row">
						<div class="col-sm-12 ibox border-e pt-3 mb-2">
							<div class="ml-3">
								<div class="row">
									<div class="col-sm-3 filter-group">
										<div class="row form-group box-group">
											<div class="col-sm-4">
												<div class="form-group mt-2">
													<label class="radio radio-ebony">
														<input type="radio" name="by-cancel" value="0" checked>
														<span class="input-span"></span>
														TẠO MỚI
													</label>
												</div>
												<div class="form-group mt-2">
													<label class="radio radio-ebony">
														<input type="radio" name="by-cancel" value="1">
														<span class="input-span"></span>
														HỦY
													</label>
												</div>
											</div>
											<!-- <label class="col-sm-4 col-form-label">Ngày tạo PTC</label> -->
											<div class="col-sm-8">
												<div class="input-group input-group-sm mb-2">
													<input class="form-control form-control-sm text-center" id="fromDate" name="fromDate" type="text" placeholder="Từ ngày">
													<span>&ensp;</span>
													<input class="form-control form-control-sm text-center" id="toDate" name="toDate" type="text" placeholder="Đến ngày">
												</div>
												<div class="form-group mb-3">
													<input class="form-control form-control-sm" id="userid" type="text" placeholder="Người tạo phiếu">
												</div>
											</div>
										</div>

										<div class="row form-group pt-1">
											<label class="col-sm-4 col-form-label">Loại tiền</label>
											<div class="col-sm-8 input-group input-group-sm">
												<select id="moneyType" class="selectpicker" data-style="btn-default btn-sm" data-width="100%" title="Chọn loại tiền">
													<option value="VND">VND</option>
													<option value="USD">USD</option>
												</select>
											</div>
										</div>

									</div>
									<div class="col-sm-4 filter-group">

										<div class="row form-group">
											<label class="col-sm-5 col-form-label">Hình thức thanh toán</label>
											<div class="col-sm-7">
												<div class="col-form-label">
													<label class="checkbox checkbox-inline">
														<input name="paymentType" type="checkbox" value="CAS">
														<span class="input-span"></span>Thu ngay</label>
													<label class="checkbox checkbox-inline">
														<input name="paymentType" type="checkbox" value="CRE">
														<span class="input-span"></span>Thu sau</label>
												</div>
											</div>
										</div>
										<div class="row form-group">
											<label class="col-sm-5 col-form-label">Trạng thái thanh toán</label>
											<div class="col-sm-7">
												<div class="col-form-label">
													<label class="checkbox checkbox-inline">
														<input name="payment-status" type="checkbox" value="Y">
														<span class="input-span"></span>Thanh toán</label>
													<label class="checkbox checkbox-inline">
														<input name="payment-status" type="checkbox" value="C">
														<span class="input-span"></span>Đã huỷ</label>
												</div>
											</div>
										</div>
										<div class="row form-group">
											<label class="col-sm-5 col-form-label">Phương thức tạo</label>
											<div class="col-sm-7">
												<div class="col-form-label">
													<label class="checkbox checkbox-inline">
														<input name="manual-inv" type="checkbox" value="0">
														<span class="input-span"></span>Tự động</label>
													<label class="checkbox checkbox-inline">
														<input name="manual-inv" type="checkbox" value="1">
														<span class="input-span"></span>Tạo tay</label>
												</div>
											</div>
										</div>

										<!-- <div class="row form-group">
											<div class="col-sm-5 col-form-label">
												<label class="checkbox checkbox-inline">
													<input name="by-cancel" type="checkbox" value="1">
													<span class="input-span"></span>Tìm theo người hủy phiếu
												</label>
											</div>
											<div class="col-sm-7">
												<input class="form-control form-control-sm" id="userid" type="text" placeholder="Người tạo phiếu">
											</div>
										</div> -->

										<div class="row form-group">
											<label class="col-sm-5 col-form-label">Hình thức thu</label>
											<div class="col-sm-7 input-group input-group-sm">
												<select id="paymentFor" class="selectpicker" data-style="btn-default btn-sm" data-width="100%">
													<option value="">Chọn hình thức thu</option>
													<option value="NULL">Thu khách hàng</option>
													<option value="THUHANGTAU">Thu hãng tàu</option>
												</select>
											</div>
										</div>

									</div>
									<div class="col-sm-5 filter-group">
										<div class="row form-group">
											<label class="col-sm-3 col-form-label" title="Đối tượng thanh toán">Đối tượng TT</label>
											<div class="col-sm-9 input-group">
												<input class="form-control form-control-sm input-required" id="taxcode" placeholder="Đối tượng thanh toán" type="text" readonly>
												<span class="input-group-addon bg-white btn mobile-hiden text-warning" style="padding: 0 .5rem" title="Chọn đối tượng thanh toán" data-toggle="modal" data-target="#payer-modal">
													<i class="ti-search"></i>
												</span>
											</div>
											<input class="hiden-input" id="cusID" readonly>
										</div>
										<div class="row form-group">
											<div class="col-sm-12 col-form-label">
												<i class="fa fa-id-card" style="font-size: 15px!important;"></i> - <span id="payer-name"> [Tên đối tượng thanh toán]</span>&emsp;
												<br>
												<br>
												<i class="fa fa-home" style="font-size: 15px!important;"></i> - <span id="payer-addr"> [Địa chỉ]</span>&emsp;
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<input id="exportdata" name="exportdata" type="text" style="display: none">
				</form>
				<div class="row">
					<div class="col-12 ibox mb-0 border-e pb-1 pt-3">
						<table id="tbl-draft-details" class="table table-striped display nowrap" cellspacing="0" style="width: 99.8%">
							<thead>
								<tr>
									<th>STT</th>
									<th>Số PTC</th>
									<th>Ngày tạo</th>
									<th>Loại PTC</th>
									<th>Số lệnh</th>
									<th>Đối tượng thanh toán</th>
									<th>Mã số thuế</th>
									<th>Mã biểu cước</th>
									<th>Diễn Giải</th>
									<th>Kích cỡ</th>
									<th>Loại hàng</th>
									<th>Số lượng</th>
									<th>Thành tiền</th>
									<th>% Thuế</th>
									<th>Tiền Thuế</th>
									<th>Tổng tiền</th>
									<th>Hình thức TT</th>
									<th>Người lập</th>
									<th>Người hủy</th>
									<th>Ngày hủy</th>
									<th>Lý do hủy</th>
									<th>Ghi Chú</th>
									<th>Tạo tay</th>
									<th>Hình thức thu</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
							<tfoot>
								<tr style="color:red; font-size:13px">
									<th colspan="12" style="font-weight: bold; text-align: right">TỔNG CỘNG</th>
									<th class="text-right">0</th>
									<th class="text-right"></th>
									<th class="text-right">0</th>
									<th class="text-right">0</th>
									<th colspan="8"></th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
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
	moment.tz.setDefault('Asia/Ho_Chi_Minh');
	$(document).ready(function() {
		var tblDraftDetail = $("#tbl-draft-details"),
			tblPayer = $("#search-payer"),
			_colDraftDetail = ["STT", "DRAFT_INV_NO", "DRAFT_INV_DATE", "IS_MANUAL_INV", "REF_NO", "CusName", "PAYER", "TRF_CODE", "TRF_DESC", "SZ", "CARGO_TYPE", "QTY", "AMOUNT", "VAT_RATE", "VAT", "TAMOUNT", "INV_TYPE", "CreatedBy", "CancelBy", "CancelTime", "CancelReason", "Remark", "IS_MANUAL_INV", "LOCAL_INV"],
			_colPayer = ["STT", "CusID", "VAT_CD", "CusName", "Address", "CusType"];

		var _payers = [];

		var dtDraftDetail = tblDraftDetail.DataTable({
			scrollY: '39vh',
			columnDefs: [{
					type: "num",
					className: "text-center",
					targets: _colDraftDetail.indexOf('STT')
				},
				{
					className: "text-center",
					targets: _colDraftDetail.getIndexs(["DRAFT_INV_NO", "SZ"]),
				},
				{
					render: function(data, type, full, meta) {
						return data ? getDateTime(data) : '';
					},
					targets: _colDraftDetail.getIndexs(["DRAFT_INV_DATE"])
				},
				{
					className: "text-center",
					render: function(data, type, full, meta) {
						return data == "1" ? "Tạo tay" : 'Tự động';
					},
					targets: _colDraftDetail.getIndexs(["IS_MANUAL_INV"])
				},
				{
					className: "text-center",
					render: function(data, type, full, meta) {
						return data == "THUHANGTAU" ? "Thu hãng tàu" : 'Thu khách hàng';
					},
					targets: _colDraftDetail.getIndexs(["LOCAL_INV"])
				},
				{
					render: function(data, type, full, meta) {
						return "<div class='wrap-text width-300'>" + data + "</div>";
					},
					targets: _colDraftDetail.getIndexs(["CusName", "Remark"])
				},
				{
					className: "text-center",
					render: function(data, type, full, meta) {
						return data == "CAS" ? "THU NGAY" : (data == "CRE" ? "THU SAU" : "");
					},
					targets: _colDraftDetail.getIndexs(["INV_TYPE"])
				},
				{
					className: "text-center",
					render: function(data, type, full, meta) {
						var ischeck = data === '1' ? 'checked' : '';
						return `<label class="checkbox disabled">
                                        <input type="checkbox" disabled ${ischeck}>
                                        <span class="input-span"></span></label>`
					},
					targets: _colDraftDetail.getIndexs(["IS_MANUAL_INV"])
				},
				{
					className: "text-right",
					targets: _colDraftDetail.getIndexs(["QTY", "AMOUNT", "VAT_RATE", "VAT", "TAMOUNT"]),
					render: $.fn.dataTable.render.number(',', '.', 2)
				}
			],
			searching: true,
			info: true,
			order: [
				[_colDraftDetail.indexOf('STT'), 'asc']
			],
			paging: false,
			rowReorder: false,
			buttons: [{
				extend: 'excel',
				text: '<i class="fa fa-table fainfo"></i> Xuất Excel',
				titleAttr: 'Xuất Excel',
				footer: true,
				customize: function(xlsx) {
					var sheet = xlsx.xl.worksheets['sheet1.xml'];
					$('row c[r^="K"], row c[r^="M"], row c[r^="N"]', sheet).attr('s', 63);
				}
			}],
			footerCallback: function(row, datas, start, end, display) {
				var api = this.api();
				var data = api.rows({
					search: 'applied'
				}).data().toArray();
				var idxAmt = _colDraftDetail.indexOf('AMOUNT')
				var idxVat = _colDraftDetail.indexOf('VAT')
				var idxTAmt = _colDraftDetail.indexOf('TAMOUNT')

				if (data.length > 0) {
					var sumAmt = data.map(p => p[idxAmt] || 0).reduce(function(a, b) {
						return parseFloat(a) + parseFloat(b);
					}, 0);
					var sumVat = data.map(p => p[idxVat] || 0).reduce(function(a, b) {
						return parseFloat(a) + parseFloat(b);
					}, 0);
					var sumTamount = data.map(p => p[idxTAmt] || 0).reduce(function(a, b) {
						return parseFloat(a) + parseFloat(b);
					}, 0);

					$(api.column(idxAmt).footer()).html($.formatNumber(sumAmt, {
						format: "#,###",
						locale: "us"
					}));
					$(api.column(idxVat).footer()).html($.formatNumber(sumVat, {
						format: "#,###",
						locale: "us"
					}));
					$(api.column(idxTAmt).footer()).html($.formatNumber(sumTamount, {
						format: "#,###",
						locale: "us"
					}));
				} else {
					$(api.column(idxAmt).footer()).html(0);
					$(api.column(idxVat).footer()).html(0);
					$(api.column(idxTAmt).footer()).html(0);
				}
			}
		});

		tblPayer.DataTable({
			paging: true,
			scroller: {
				displayBuffer: 9,
				boundaryScale: 0.95
			},
			columnDefs: [{
					type: "num",
					className: 'center',
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

		load_payer();

		var usid = <?= json_encode($userIds) ?>;
		$("#userid").autocomplete({
			source: usid.map(p => p.UserID),
			minLength: 0
		});

		$('#userid').mousedown(function() {
			if (document.activeElement == this) return;
			$(this).focus();
		});

		//set from date, to date
		var fromDate = $('#fromDate');
		var toDate = $('#toDate');

		setDateTimeRange(fromDate, toDate, 'dd/mm/yy', "HH:mm:ss");

		fromDate.val(moment().subtract('month', 1).format('DD/MM/YYYY HH:mm:ss'));
		toDate.val(moment().format('DD/MM/YYYY HH:mm:ss'));
		//end set fromdate, todate

		//////// SEARCH PAYER
		$(document).on('click', '#search-payer tbody tr', function() {
			$('.m-row-selected').removeClass('m-row-selected');
			$(this).addClass('m-row-selected');
		});
		$('#select-payer').on('click', function() {
			var r = $('#search-payer tbody').find('tr.m-row-selected').first();

			$('#taxcode').val($(r).find('td:eq(' + _colPayer.indexOf("VAT_CD") + ')').text());
			$('#cusID').val($(r).find('td:eq(' + _colPayer.indexOf("CusID") + ')').text());

			fillPayer();

			$('#taxcode').trigger("change");
		});

		$('#search-payer').on('dblclick', 'tbody tr td', function() {
			var r = $(this).parent();

			$('#taxcode').val($(r).find('td:eq(' + _colPayer.indexOf("VAT_CD") + ')').text());
			$('#cusID').val($(r).find('td:eq(' + _colPayer.indexOf("CusID") + ')').text());

			fillPayer();

			$('#payer-modal').modal("toggle");
			$('#taxcode').trigger("change");
		});
		///////// END SEARCH PAYER

		$('input[name=by-cancel]').on('change', function(e) {
			$('#userid').attr("placeholder", $('input[name=by-cancel]:checked').val() == '1' ? "Người hủy phiếu" : "Người tạo phiếu");
		})
		$("#load-data").on("click", function() {
			loadDraft();
		});

		$('#payer-modal').on('shown.bs.modal', function(e) {
			$($.fn.dataTable.tables(true)).DataTable()
				.columns
				.adjust();
		});

		$('#report-excel').on('click', function() {
			if (!$("#exportdata").val()) {
				toastr.warning('Không có dữ liệu!');
				return;
			}
			$('#frmdata_export').submit();
		});

		function loadDraft() {
			tblDraftDetail.dataTable().fnClearTable();
			tblDraftDetail.waitingLoad();

			var btn = $("#load-data");
			btn.button("loading");

			var formData = {
				"action": "view",
				"act": "search_draft",
				"fromDate": $("#fromDate").val(),
				"toDate": $("#toDate").val(),
				"paymentType": $('input[name="paymentType"]:checked').get().map(t => $(t).val()),
				"currency": $("#moneyType").val(),
				"paymentStatus": $('input[name="payment-status"]:checked').get().map(t => $(t).val()),
				"isManualInv": $('input[name="manual-inv"]:checked').get().map(t => $(t).val()),
				"cusID": $('#cusID').val(),
				"paymentFor": $('#paymentFor').val(),
				"userId": $('#userid').val(),
				"byCancel": parseInt($('input[name=by-cancel]:checked').val())
			};

			$.ajax({
				url: "<?= site_url(md5('Report') . '/' . md5('rptDraftWithoutInv')); ?>",
				dataType: 'json',
				data: formData,
				type: 'POST',
				success: function(data) {
					btn.button("reset");

					if (data.deny) {
						toastr["error"](data.deny);
						return;
					}

					var rows = [];

					//, d.PAYMENT_STATUS, d.ModifiedBy, d.update_time, d.REMARK

					if (data.draftdetails && data.draftdetails.length > 0) {
						$('#exportdata').val(JSON.stringify(data.draftdetails));
						$.each(data.draftdetails, function(i, item) {
							var r = [];
							$.each(_colDraftDetail, function(idx, colname) {
								var val = "";
								switch (colname) {
									case "STT":
										val = i + 1;
										break;
									case "CancelBy":
									case "CancelReason":
										val = item['PAYMENT_STATUS'] == 'C' ? item[colname] : '';
										break;
									case "CancelTime":
										val = item['PAYMENT_STATUS'] == 'C' ? getDateTime(item[colname]) : '';
										break;
									default:
										val = item[colname] ? item[colname] : "";
										break;
								}
								r.push(val);
							});
							rows.push(r);
						});
					}

					tblDraftDetail.dataTable().fnClearTable();
					if (rows.length > 0) {
						tblDraftDetail.dataTable().fnAddData(rows);
					}
				},
				error: function(err) {
					tblDraftDetail.dataTable().fnClearTable();
					btn.button("reset");
					$('.toast').remove();
					toastr['error']("Có lỗi xảy ra! <br/>  Vui lòng liên hệ với bộ phận kỹ thuật! ");
					console.log(err);
				}
			});
		}

		function load_payer() {
			tblPayer.waitingLoad();

			$.ajax({
				url: "<?= site_url(md5('Report') . '/' . md5('rptDraftWithoutInv')); ?>",
				dataType: 'json',
				data: {
					'action': 'view',
					'act': 'load_payer'
				},
				type: 'POST',
				success: function(data) {

					if (data.deny) {
						toastr["error"](data.deny);
						return;
					}

					var rows = [];

					if (data.payers && data.payers.length > 0) {
						_payers = data.payers;
						var i = 0;
						$.each(_payers, function(index, rData) {
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

		function fillPayer() {
			var py = _payers.filter(p => p.VAT_CD == $('#taxcode').val() && p.CusID == $("#cusID").val());

			if (py.length > 0) { //fa-check-square
				$('#payer-name').text(py[0].CusName);
				$('#payer-addr').text(py[0].Address);

				if (py[0].Email) {
					$("#inv-payer-email").val(py[0].Email);
				}

				if (py[0].EMAIL_DD && py[0].EMAIL_DD != py[0].Email) {
					$("#inv-payer-email").val($("#mail").val() + ',' + py[0].EMAIL_DD);
				}

				$("#taxcode").removeClass("error");
			}
		}

		function clearPayer() {
			$('#taxcode').val("");
			$("#cusID").val("");
			$('#payer-name').text(" [Tên đối tượng thanh toán]");
			$('#payer-addr').text(" [Địa chỉ]");
		}

	});
</script>

<script src="<?= base_url('assets/vendors/moment/min/moment.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/bootstrap-select/dist/js/bootstrap-select.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/jquery-confirm/jquery-confirm.min.js'); ?>"></script>
<!--format number-->
<script src="<?= base_url('assets/js/jshashtable-2.1.js'); ?>"></script>
<script src="<?= base_url('assets/js/jquery.numberformatter-1.2.3.min.js'); ?>"></script>

<script src="<?= base_url('assets/vendors/dataTables/datatables.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/dataTables/dataTables.buttons.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/dataTables/extensions/jszip.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/dataTables/extensions/buttons.html5.min.js'); ?>"></script>