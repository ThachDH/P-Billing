<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<link href="<?= base_url('assets/vendors/bootstrap-select/dist/css/bootstrap-select.min.css'); ?>" rel="stylesheet" />
<link href="<?= base_url('assets/vendors/jquery-confirm/jquery-confirm.min.css'); ?>" rel="stylesheet" />
<link href="<?= base_url('assets/vendors/dataTables/extensions/buttons.dataTables.min.css'); ?>" rel="stylesheet" />
<link href="<?= base_url('assets/vendors/bootstrap-tagsinput/dist/bootstrap-tagsinput.css'); ?>" rel="stylesheet" />
<style>
	.wrapok {
		white-space: normal !important;
	}

	.bootstrap-tagsinput input {
		width: inherit !important;
	}

	.modal-dialog-mw-py {
		position: fixed;
		top: 20%;
		margin: 0;
		width: 100%;
		height: 100%;
		padding: 0;
		max-width: 100% !important;
	}

	.modal-dialog-mw-py .modal-body {
		width: 90% !important;
		margin: auto;
	}

	.form-group {
		margin-bottom: .5rem !important;
	}

	.grid-hidden {
		display: none;
	}

	.unchecked-Salan {
		pointer-events: none;
	}

	@media (min-width: 1024px) {
		.modal-dialog-mw {
			min-width: 960px !important;
		}
	}

	@media (min-width: 960px) and (max-width: 1024px) {
		.modal-dialog-mw {
			min-width: 720px !important;
		}
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

	.link-cell:hover {
		text-decoration: underline;
	}

	.width-500 {
		width: 500px !important;
	}

	.row.stretch label.radio {
		min-width: 93px;
		margin-bottom: .5rem !important
	}

	.font-weight-400 {
		font-weight: 400 !important;
	}

	#payer-modal .dataTables_filter {
		padding-left: 10px !important;
	}
</style>
<div class="row" style="font-size: 12px!important;">
	<div class="col-xl-12">
		<div class="ibox collapsible-box">
			<div class="ibox-head">
				<div class="ibox-title">QUẢN LÝ TÍCH HỢP HỆ THỐNG KẾ TOÁN</div>
			</div>
			<div class="ibox-body pt-3 pb-2 bg-f9 border-e">
				<div class="row bg-white border-e pb-1 pt-3">
					<div class="col-12">
						<div class="row">
							<div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12" style="border-right: 1px solid #eee">
								<div class="row form-group">
									<label class="col-lg-3 col-sm-12 col-form-label">
										Ngày phát hành
									</label>
									<div class="col-lg-9 col-sm-12 input-group input-group-sm">
										<input class="form-control form-control-sm text-center border-right-0" id="issueFromDate" type="text" placeholder="Từ ngày">
										<input class="form-control form-control-sm text-center" id="issueToDate" type="text" placeholder="Đến ngày">
										<span class="input-group-btn">
											<button id="clear-date" class="btn btn-outline-default">
												<span class="text-danger">X</span>
											</button>
										</span>
									</div>
								</div>
								<div class="row form-group">
									<label class="col-lg-3 col-sm-12 col-form-label" for="taxcode">Khách hàng</label>
									<div class="col-lg-9 col-sm-12 input-group">
										<input class="form-control form-control-sm" id="taxcode" placeholder="Đang nạp ..." type="text" readonly="">
										<span class="input-group-addon bg-white btn mobile-hiden text-warning" style="padding: 0 .5rem" title="Chọn đối tượng thanh toán" data-toggle="modal" data-target="#payer-modal">
											<i class="ti-search"></i>
										</span>
									</div>
									<input class="hiden-input" id="cusID" readonly>
								</div>
								<div class="row form-group">
									<label class="col-lg-3 col-sm-12 col-form-label" for="searchVal">Tìm kiếm</label>
									<div class="col-lg-9 col-sm-12 input-group input-group-sm">
										<input class="form-control form-control-sm" id="searchVal" name="searchVal" type="text" placeholder="Số PinCode/ Số lệnh/ Số hoá đơn">
									</div>
								</div>
							</div>

							<div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12" style="border-right: 1px solid #eee">
								<div class="row stretch">
									<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12 pr-0">
										<div class="row">
											<label class="col-sm-4 col-lg-2 col-form-label">Hình thức</label>
											<div class="col-sm-12 col-lg-10 col-form-label">
												<label class="radio radio-blue">
													<input type="radio" name="type" value="CAS" checked>
													<span class="input-span"></span>
													Thu ngay
												</label>
												<label class="radio radio-blue">
													<input type="radio" name="type" value="CRE">
													<span class="input-span"></span>
													Thu sau
												</label>
											</div>
										</div>
										<div class="row">
											<label class="col-sm-4 col-lg-2 col-form-label">Trạng thái</label>
											<div class="col-sm-12 col-lg-10 col-form-label">
												<label class="radio radio-blue">
													<input type="radio" name="status" value="" checked>
													<span class="input-span"></span>
													Tất cả
												</label>
												<label class="radio radio-blue">
													<input type="radio" name="status" value="YU">
													<span class="input-span"></span>
													Phát hành
												</label>
												<label class="radio radio-blue">
													<input type="radio" name="status" value="C">
													<span class="input-span"></span>
													Huỷ bỏ
												</label>
											</div>
										</div>
										<div class="row">
											<label class="col-sm-4 col-lg-2 col-form-label">Chuyển/ lỗi</label>
											<div class="col-sm-12 col-lg-10 col-form-label">
												<label class="radio radio-blue">
													<input type="radio" name="isPosted" value="" checked>
													<span class="input-span"></span>
													Tất cả
												</label>
												<label class="radio radio-blue">
													<input type="radio" name="isPosted" value="1">
													<span class="input-span"></span>
													Đã chuyển
												</label>
												<label class="radio radio-blue">
													<input type="radio" name="isPosted" value="0">
													<span class="input-span"></span>
													Chưa chuyển
												</label>
												<label class="radio radio-blue">
													<input type="radio" name="isPosted" value="2">
													<span class="input-span"></span>
													Chuyển lỗi
												</label>
											</div>
										</div>
									</div>
								</div>

							</div>
							<div class="col-md-2 col-sm-12 col-xs-12 ml-sm-auto pl-0">
								<div class="form-group">
									<button type="button" id="loadData" class="btn btn-sm btn-blue btn-block ml-2" data-loading-text="<i class='la la-spinner spinner'></i>Đang nạp">
										<i class="fa fa-refresh"></i>
										Nạp dữ liệu
									</button>
								</div>
								<div class="form-group">
									<button type="button" id="transfer-inv" class="btn btn-sm btn-default btn-block ml-2" data-loading-text="<i class='la la-spinner spinner'></i>Đang chuyển" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="" data-html="true">
										<i class="fa fa-send"></i>
										Chuyển hoá đơn
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="ibox-footer border-top-0 mt-3">
				<div class="row">
					<div class="col-sm-12 detail-expand">
						<table id="tbl-ord-detail" class="table table-striped display nowrap tableDetails" cellspacing="0">
							<tfoot id="tbl-footer">
								<tr style="color:red; font-size:13px"></tr>
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
					<table id="search-payer" class="table table-striped display nowrap table-popup single-row-select" cellspacing="0" style="width: 99%">
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
		</div>
	</div>
</div>

<!--notify modal-->
<div class="modal fade" id="notify-modal" tabindex="-1" role="dialog" aria-labelledby="groups-modalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content" style="border-radius: 5px">
			<div class="modal-header" style="border-radius: 5px;background-color: #cdfde0;">
				<h4 class="modal-title text-primary font-bold" id="groups-modalLabel">Chi tiết dữ liệu</h4>
				<i class="btn fa fa-times text-primary" data-dismiss="modal"></i>
			</div>
			<div class="modal-body" style="border: 2px outset #ccc;margin:3px;border-radius: 5px;overflow-y: auto;max-height: 75vh; white-space: pre;"></div>
		</div>
	</div>
</div>

<script type="text/javascript">
	moment.tz.setDefault('Asia/Ho_Chi_Minh');
	var _columnDefs = [{
			name: "STT",
			data: "STT",
			title: "STT",
			width: "20px",
			className: "text-center",
			targets: 0
		},
		{
			name: "IsChecked",
			data: "IsChecked",
			className: "text-center",
			orderDataType: 'dom-checkbox',
			title: 'Chọn',
			targets: 1,
			render: function(data, type, row, meta) {
				var value = data ? parseInt(data) : 0;
				return `<label class="checkbox checkbox-primary">
									<input type="checkbox" disabled value="` + value + `" ` + (value == 1 ? "checked" : "") + `>
									<span class="input-span"></span>
								</label>`;
			}
		},
		{
			name: "isPosted",
			data: "isPosted",
			title: "<div class='wrap-text' style='width:65px'>Trạng thái chuyển HĐ</div>",
			targets: 2,
			render: function(data) {
				let n = data ? parseInt(data) : '';
				switch (n) {
					case 1: //chuyen moi ok
					case 6: // chuyen huy ok
						return '<span class="badge badge-success font-weight-400">Đã chuyển</span>';
						break;
					case 2: //chuyen moi bi loi
					case 7: //chuyen huy bi loi
						return '<span class="badge badge-danger font-weight-400">Lỗi</span>';
						break;
					default:
						return '<span class="badge badge-default font-weight-400">Chưa chuyển</span>';
				}
			}
		},
		{
			name: "PAYMENT_STATUS",
			data: "PAYMENT_STATUS",
			title: "Loại hoá đơn",
			targets: 3,
			render: function(data) {
				return data != 'C' ? 'Hoá đơn mới' : 'Hoá đơn huỷ';
			}
		},
		{
			name: "INV_TYPE",
			data: "INV_TYPE",
			title: "Hình thức",
			targets: 4,
			render: function(data) {
				return data == 'CRE' ? 'Thu sau' : 'Thu ngay';
			}
		},
		{
			name: "INV_NO",
			data: "INV_NO",
			className: "text-center",
			title: "Số hóa đơn",
			targets: 5
		},
		{
			name: "INV_DATE",
			data: "INV_DATE",
			className: "text-center",
			title: "Ngày lập hóa đơn",
			targets: 6,
			render: function(data) {
				return data ? getDateTime(data) : ''
			}
		},
		{
			name: "PinCode",
			data: "PinCode",
			title: "Số PIN",
			className: "text-center",
			targets: 7
		},
		{
			name: "REF_NO",
			data: "REF_NO",
			className: "text-center",
			title: "Số lệnh",
			targets: 8,
			render: function(data) {
				return data || '';
			}
		},
		// {
		// 	name: "DRAFT_INV_NO",
		// 	data: "DRAFT_INV_NO",
		// 	className: "text-center",
		// 	title: "Số PTC",
		// 	targets: 9,
		// 	render: function(data) {
		// 		return data || '';
		// 	}
		// },
		{
			name: "CusName",
			data: "CusName",
			title: "Đối tượng thanh toán",
			targets: 9,
			render: function(data) {
				return '<div class="wrap-text width-250">' + data + '</div>';
			}
		},
		{
			name: "VAT_CD",
			data: "VAT_CD",
			title: "Mã số thuế",
			targets: 10
		},
		{
			name: "AMOUNT",
			data: "AMOUNT",
			title: "Thành tiền",
			targets: 11,
			className: "text-right",
			render: $.fn.dataTable.render.number(',', '.', 2),
		},
		{
			name: "VAT",
			data: "VAT",
			title: "Tiền Thuế",
			targets: 12,
			className: "text-right",
			render: $.fn.dataTable.render.number(',', '.', 2),
		},
		{
			name: "TAMOUNT",
			data: "TAMOUNT",
			title: "Tổng cộng",
			targets: 13,
			className: "text-right",
			render: $.fn.dataTable.render.number(',', '.', 2),
		},
		{
			name: "RATE",
			data: "RATE",
			className: "text-right",
			title: "Tỷ giá",
			targets: 14
		},
		{
			name: "CURRENCYID",
			data: "CURRENCYID",
			className: "text-center",
			title: "Loại tiền",
			targets: 15
		},
		{
			name: "CreatedBy",
			data: "CreatedBy",
			title: "Lập bởi",
			targets: 16
		},
		{
			name: "CancelDate",
			data: "CancelDate",
			title: "Ngày huỷ",
			targets: 17,
			render: function(data) {
				return data ? moment(data).format('DD/MM/YYYY HH:mm:ss') : ''
			}
		},
		{
			name: "CancelBy",
			data: "CancelBy",
			title: "Người hủy",
			targets: 18
		},
		{
			name: "CancelRemark",
			data: "CancelRemark",
			title: "Lý do huỷ",
			targets: 19
		},
		{
			name: "INV_NO_PRE",
			data: "INV_NO_PRE",
			targets: 20,
			visible: false
		},
		{
			name: "TRF_DESC",
			data: "TRF_DESC",
			targets: 21,
			visible: false
		},
		{
			name: "INV_PREFIX",
			data: "INV_PREFIX",
			targets: 22,
			visible: false
		},
		{
			name: "PAYER",
			data: "PAYER",
			targets: 23,
			visible: false
		}, {
			name: "ShipKey",
			data: "ShipKey",
			targets: 24,
			visible: false
		}, {
			name: "BerthDate",
			data: "BerthDate",
			targets: 25,
			visible: false
		}, {
			name: "CusID",
			data: "CusID",
			targets: 26,
			visible: false
		}, {
			name: "ShipID",
			data: "ShipID",
			targets: 27,
			visible: false
		}, {
			name: "ShipVoy",
			data: "ShipVoy",
			targets: 28,
			visible: false
		}, {
			name: "ShipYear",
			data: "ShipYear",
			targets: 29,
			visible: false
		}, {
			name: "ACC_CD",
			data: "ShipYear",
			targets: 30,
			visible: false
		}
	];

	var _colPayer = ["STT", "CusID", "VAT_CD", "CusName", "Address", "CusType"];
	var _colDetails = _columnDefs.map(p => p.name);
	_colDetails.map(p => $('#tbl-footer tr').append('<th>'));

	$(document).ready(function() {
		var tblDetail = $("#tbl-ord-detail");
		var dtDetails = tblDetail.DataTable({
			order: [
				[0, 'asc']
			],
			paging: true,
			infor: false,
			scrollY: '41vh',
			rowGroup: {
				dataSrc: [4]
			},
			columnDefs: _columnDefs,
			scroller: {
				displayBuffer: 9,
				boundaryScale: 0.5
			},
			select: {
				style: 'multi+shift',
				selector: 'td:nth-child(' + (_colDetails.indexOf('IsChecked') + 1) + ')',
			},
			footerCallback: function(row, datas, start, end, display) {
				var api = this.api();
				var data = api.rows({
					search: 'applied'
				}).data().toArray();

				$(api.column(_colDetails.indexOf('CusName')).footer()).html('TỔNG CỘNG');
				// console.log(data);
				if (data.length > 0) {
					var sumAmt = data.map(p => p.AMOUNT).reduce(function(a, b) {
						return parseFloat(a || 0) + parseFloat(b || 0);
					}, 0);
					var sumVat = data.map(p => p.VAT).reduce(function(a, b) {
						return parseFloat(a || 0) + parseFloat(b || 0);
					}, 0);
					var sumTamount = data.map(p => p.TAMOUNT).reduce(function(a, b) {
						return parseFloat(a || 0) + parseFloat(b || 0);
					}, 0);

					$(api.column(_colDetails.indexOf('AMOUNT')).footer()).html($.formatNumber(sumAmt, {
						format: "#,###",
						locale: "us"
					}));
					$(api.column(_colDetails.indexOf('VAT')).footer()).html($.formatNumber(sumVat, {
						format: "#,###",
						locale: "us"
					}));
					$(api.column(_colDetails.indexOf('TAMOUNT')).footer()).html($.formatNumber(sumTamount, {
						format: "#,###",
						locale: "us"
					}));
				} else {
					$(api.column(_colDetails.indexOf('AMOUNT')).footer()).html(0);
					$(api.column(_colDetails.indexOf('VAT')).footer()).html(0);
					$(api.column(_colDetails.indexOf('TAMOUNT')).footer()).html(0);
				}
			}
		});
		$('#search-payer').DataTable({
			paging: true,
			scroller: {
				displayBuffer: 9,
				boundaryScale: 0.95
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

		var issueFromDate = $('#issueFromDate');
		var issueToDate = $('#issueToDate');
		setDateTimeRange(issueFromDate, issueToDate);
		autoLoadYearCombo('cb-searh-year');

		issueFromDate.val(moment().subtract(1, 'day').format('DD/MM/YYYY 00:00'));
		issueToDate.val(moment().format('DD/MM/YYYY 23:59'));

		$("#clear-date").on('click', function() {
			$('#issueFromDate, #issueToDate').val('');
		});

		///////// SEARCH PAYER
		// load_payer();
		$(document).on('click', '#search-payer tbody tr', function() {
			$('.m-row-selected').removeClass('m-row-selected');
			$(this).addClass('m-row-selected');
		});
		$('#select-payer').on('click', function() {
			var r = $('#search-payer tbody').find('tr.m-row-selected').first();

			$('#taxcode').val($(r).find('td:eq(' + _colPayer.indexOf("VAT_CD") + ')').text());
			$('#cusID').val($(r).find('td:eq(' + _colPayer.indexOf("CusID") + ')').text());

			$('#taxcode').trigger("change");
		});
		$('#search-payer').on('dblclick', 'tbody tr td', function() {
			var r = $(this).parent();

			$('#taxcode').val($(r).find('td:eq(' + _colPayer.indexOf("VAT_CD") + ')').text());
			$('#cusID').val($(r).find('td:eq(' + _colPayer.indexOf("CusID") + ')').text());

			$('#payer-modal').modal("toggle");
			$('#taxcode').trigger("change");
		});

		$('#payer-modal').on('shown.bs.modal', function(e) {
			$($.fn.dataTable.tables(true)).DataTable()
				.columns
				.adjust();
		});
		///////// END SEARCH PAYER

		///////// INPUT TAX_CODE DIRECTLY
		$("#taxcode").on("keypress", function(e) {
			if (e.keyCode == 13) {
				$(this).blur();
			}
		});
		///////// INPUT TAX_CODE DIRECTLY

		dtDetails.on('select', function(e, dt, type, indexes) {
			indexes.map(ii => {
				dt.cell(ii, _colDetails.indexOf('IsChecked')).data(1);
			})
		});

		dtDetails.on('deselect', function(e, dt, type, indexes) {
			indexes.map(ii => {
				dt.cell(ii, _colDetails.indexOf('IsChecked')).data(0);
			})
		});

		$("#loadData").on("click", function() {
			$(this).button("loading");
			search_log();
		});

		$('#transfer-inv').on("click", function() {
			var checkedIndexes = dtDetails.rows().eq(0).filter(function(rowIdx) {
				return dtDetails.cell(rowIdx, _columnDefs.filter(p => p.name == 'IsChecked')[0].targets).data() === 1;
			}).toArray();

			if (checkedIndexes.length == 0) {
				toastr.warning('Chưa chọn dữ liệu chuyển!');
				return;
			}

			$.confirm({
				title: 'Cảnh báo!',
				type: 'orange',
				icon: 'fa fa-warning',
				content: 'Chuyển những HOÁ ĐƠN đã chọn?<br/>' + $('#transfer-inv').data('content'),
				buttons: {
					ok: {
						text: 'Tiếp tục',
						btnClass: 'btn-primary',
						keys: ['Enter'],
						action: function() {
							postToTransfer(checkedIndexes, $('#transfer-inv'));
						}
					},
					cancel: {
						text: 'Hủy bỏ',
						btnClass: 'btn-default',
						keys: ['ESC'],
						action: function() {}
					}
				}
			});
		});

		function postToTransfer(checkedIndexes, btn) {
			var selectedData = dtDetails.rows(checkedIndexes).data().toArray();
			var datapost = {
				action: 'add',
				act: 'send',
				data: selectedData
			};
			btn.button('loading');

			$.ajax({
				url: "<?= site_url(md5('Tools') . '/' . md5('tlInvoice2Oracle')); ?>",
				dataType: 'json',
				data: datapost,
				type: 'POST',
				success: function(data) {
					btn.button('reset');
					if (data.deny) {
						toastr.error(data.deny);
						return;
					}

					if (data.results) {
						if (data.results['success']) {
							toastr.success('Chuyển dữ liệu thành công');
							checkedIndexes.map(i => {
								let isCancel = dtDetails.cell(i, _columnDefs.filter(p => p.name == 'PAYMENT_STATUS')[0].targets).data() == 'C';
								dtDetails.cell(i, _columnDefs.filter(p => p.name == 'isPosted')[0].targets).data(!isCancel ? 1 : 6);
							});
							dtDetails.rows(checkedIndexes).deselect();
							return;
						}

						var errorIndexInv = dtDetails.rows(checkedIndexes).eq(0).filter(function(rowIdx) {
							let colIndx = _columnDefs.filter(p => p.name == 'INV_NO')[0].targets;
							return data.results.filter(p => p['invoiceNo'] == dtDetails.cell(rowIdx, colIndx).data()).length > 0;
						}).toArray();

						if (errorIndexInv.length > 0) {
							errorIndexInv.map(i => {
								let isCancel = dtDetails.cell(i, _columnDefs.filter(p => p.name == 'PAYMENT_STATUS')[0].targets).data() == 'C';
								dtDetails.cell(i, _columnDefs.filter(p => p.name == 'isPosted')[0].targets).data(!isCancel ? 2 : 7)
							});
							dtDetails.rows(dtDetails).select();
						}

						let strErrs = data.results.map(p => `[${p['invoiceNo'] || p['draftNo'] || ''}] ${p['message'] || ''}`);
						let err = strErrs.join('').length > 0 ? strErrs.join('<br>') : 'Chuyển dữ liệu không thành công';
						toastr.error(err);
					}
				},
				error: function(err) {
					btn.button('reset');
					$('.toast').remove();
					toastr['error']("Có lỗi xảy ra! <br/>  Vui lòng liên hệ với bộ phận kỹ thuật! ");
					console.log(err);
				}
			});
		}

		function search_log() {
			tblDetail.waitingLoad();

			var fromDate = $("#issueFromDate").val(),
				toDate = $("#issueToDate").val(),
				cusID = $("#cusID").val(),
				searchVal = $("#searchVal").val(),
				paymentType = $("input[name='type']:checked").val(),
				status = $("input[name='status']:checked").val(),
				isPosted = $("input[name='isPosted']:checked").val();

			var formData = {
				action: "view",
				act: "search_value",
				args: {
					issueFromDate: fromDate.trim(),
					issueToDate: toDate.trim(),
					cusID: cusID.trim(),
					searchVal: searchVal.trim(),
					paymentType: paymentType,
					status: status,
					isPosted: isPosted,
				}
			};

			$.ajax({
				url: "<?= site_url(md5('Tools') . '/' . md5('tlInvoice2Oracle')); ?>",
				dataType: 'json',
				data: formData,
				type: 'POST',
				success: function(data) {
					$("#loadData").button("reset");
					var rows = [];

					if (data.results && data.results.length > 0) {
						$.each(data.results, function(i, item) {
							var r = {};
							$.each(_colDetails, function(idx, colname) {
								var val = "";
								switch (colname) {
									case "STT":
										val = i + 1;
										break;
									default:
										val = item[colname] ? item[colname] : "";
										break;
								}
								r[colname] = val;
							});
							rows.push(r);
						});
					}

					tblDetail.dataTable().fnClearTable();
					if (rows.length > 0) {
						tblDetail.dataTable().fnAddData(rows);
					}
				},
				error: function(err) {
					tblDetail.dataTable().fnClearTable();
					$("#loadData").button("reset");
					$('.toast').remove();
					toastr['error']("Có lỗi xảy ra! <br/>  Vui lòng liên hệ với bộ phận kỹ thuật! ");
					console.log(err);
				}
			});
		}

		function load_payer() {
			var tblPayer = $('#search-payer');
			tblPayer.waitingLoad();

			$.ajax({
				url: "<?= site_url(md5('Tools') . '/' . md5('tlInvoice2Oracle')); ?>",
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

					$("#taxcode").prop("readonly", false);
					$("#taxcode").prop("placeholder", "ĐT thanh toán");
				},
				error: function(err) {
					tblPayer.dataTable().fnClearTable();
					console.log(err);
					toastr["error"]("Có lỗi xảy ra! Vui lòng liên hệ với kỹ thuật viên! <br/>Cảm ơn!");
				}
			});
		};

		function clearPayer() {
			$("#cusID").val('');
			$('#taxcode').val('');
		}
	});

	function compareObj(obj1, obj2) {
		var result = [];
		for (let i = 0; i < Object.keys(obj2).length; i++) {
			let key = Object.keys(obj2)[i];
			if (!obj1[key] || obj1[key] != obj2[key]) {
				result.push(key);
			}
		}

		return result;
	}

	function getContSize(sztype) {
		switch (sztype.substring(0, 1)) {
			case "2":
				return 20;
			case "4":
				return 40;
			case "L":
			case "M":
			case "9":
				return 45;
		}
		return "0";
	}
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
<script src="<?= base_url('assets/vendors/dataTables/extensions/dataTables.rowsGroup.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/dataTables/extensions/select.min.js'); ?>"></script>

<script src="<?= base_url('assets/vendors/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js'); ?>"></script>