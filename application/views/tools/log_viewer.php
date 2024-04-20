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
</style>
<div class="row" style="font-size: 12px!important;">
	<div class="col-xl-12">
		<div class="ibox collapsible-box">
			<i class="la la-angle-double-up dock-right"></i>
			<div class="ibox-head">
				<div class="ibox-title">LỊCH SỬ THAY ĐỔI DỮ LIỆU</div>
			</div>
			<div class="ibox-body pt-3 pb-2 bg-f9 border-e">
				<div class="row bg-white border-e pb-1 pt-3">
					<div class="col-12">
						<div class="row">
							<div class="col-xl-5 col-lg-5 col-md-6 col-sm-12 col-xs-12" style="border-right: 1px solid #eee">

								<div class="row form-group">
									<label class="col-sm-3 col-form-label">
										Ngày thay đổi
									</label>
									<div class="col-sm-8 input-group input-group-sm">
										<input class="form-control form-control-sm text-center border-right-0" id="changedDateFrom" type="text" placeholder="Từ ngày">
										<input class="form-control form-control-sm text-center" id="changedDateTo" type="text" placeholder="Đến ngày">
										<span class="input-group-btn">
											<button id="clear-date" class="btn btn-outline-default">
												<span class="text-danger">X</span>
											</button>
										</span>
									</div>
								</div>
								<div class="row form-group">
									<label class="col-sm-3 col-form-label" for="tableName">Dữ liệu</label>
									<div class="col-sm-8 input-group input-group-sm">
										<select id="tableName" name="tableName" class="selectpicker" data-width="100%" data-style="btn-default btn-sm" data-live-search="true">
										</select>
									</div>
								</div>
								<div class="row form-group">
									<label class="col-sm-3 col-form-label" for="changedType">Loại thay đổi</label>
									<div class="col-sm-8 input-group input-group-sm">
										<select id="changedType" name="changedType" class="selectpicker" data-width="100%" data-style="btn-default btn-sm">
										</select>
									</div>
								</div>
								<div class="row form-group">
									<label class="col-sm-3 col-form-label" for="changedBy">Người thay đổi</label>
									<div class="col-sm-8 input-group input-group-sm">
										<input class="form-control form-control-sm" id="changedBy" name="changedBy" type="text" placeholder="Người thay đổi">
									</div>
								</div>
							</div>

							<div class="col-xl-7 col-lg-7 col-md-6 col-sm-12 col-xs-12">
								<div class="row" style="border-bottom: 1px solid #eee">
									<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<div class="row form-group">
											<label class="col-sm-4 col-lg-2 col-form-label">Tác nghiệp</label>
											<div class="col-sm-8 col-lg-10 col-form-label">
												<label class="radio radio-ebony pr-4">
													<input type="radio" name="typeof" value="" checked>
													<span class="input-span"></span>
													TẤT CẢ
												</label>
												<label class="radio radio-ebony pr-4">
													<input type="radio" name="typeof" value="nh">
													<span class="input-span"></span>
													NÂNG HẠ
												</label>
												<label class="radio radio-ebony pr-4">
													<input type="radio" name="typeof" value="dr">
													<span class="input-span"></span>
													ĐÓNG RÚT
												</label>
												<label class="radio radio-ebony">
													<input type="radio" name="typeof" value="dv">
													<span class="input-span"></span>
													DỊCH VỤ
												</label>
											</div>
										</div>
										<div class="row form-group">
											<label class="col-sm-4 col-lg-2 col-form-label">Tìm trong</label>
											<div class="col-sm-8 col-lg-10 col-form-label">
												<label class="radio radio-ebony pr-4">
													<input type="radio" name="Content" value="" checked>
													<span class="input-span"></span>
													TẤT CẢ
												</label>
												<label class="radio radio-ebony pr-4">
													<input type="radio" name="Content" value="Old">
													<span class="input-span"></span>
													DỮ LIỆU CŨ
												</label>
												<label class="radio radio-ebony pr-4">
													<input type="radio" name="Content" value="New">
													<span class="input-span"></span>
													DỮ LIỆU MỚI
												</label>
											</div>
										</div>
										<div class="row form-group">
											<label class="col-sm-2 col-form-label" for="searchVal">Tìm kiếm</label>
											<div class="col-sm-10 input-group input-group-sm">
												<input class="tagsinput form-control form-control-sm" id="searchVal" name="searchVal" type="text" placeholder="Số lệnh / Số Container / Số PTC / Số Hoá đơn / ..." />
											</div>
										</div>
									</div>
								</div>

								<div class="row pt-2">
									<div class="col-sm-12">
										<div class="row form-group">
											<button type="button" id="loadData" data-loading-text="<i class='la la-spinner spinner'></i>Đang nạp" class="btn btn-sm btn-primary ml-2">
												<i class="fa fa-refresh"></i>
												Nạp dữ liệu
											</button>
										</div>
									</div>

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
							<thead>
								<tr>
									<th col-name="STT">STT</th>
									<th col-name="ID">ID</th>
									<th col-name="ChangedTime">Thời gian</th>
									<th col-name="TableName">Dữ liệu</th>
									<th col-name="OrderNo">Số lệnh</th>
									<th col-name="CntrNo">Số Container</th>
									<th col-name="FeautureName">Tác nghiệp</th>
									<th col-name="ChangedType">Loại</th>
									<th col-name="ChangedBy">User</th>
									<th col-name="ChangedIPAddress">Địa chỉ IP</th>
									<th col-name="OldContent">Dữ liệu cũ</th>
									<th col-name="NewContent">Dữ liệu mới</th>
									<th col-name="CJMode_CD">Mã CV</th>
									<th col-name="BLNo">Số BL</th>
									<th col-name="BookingNo">Số Booking</th>
									<th col-name="DRAFT_INV_NO">Số PTC</th>
									<th col-name="InvNo">Số hoá đơn</th>
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
	$(document).ready(function() {
		var table_log = {
			"TRF_CODES": "MÃ BIỂU CƯỚC",
			"TRF_DIS": "BIỂU CƯỚC GIẢM GIÁ",
			"TRF_SERVICE": "BIỂU CƯỚC DỊCH VỤ",
			"TRF_STD": "BIỂU CƯỚC CHUẨN",
			"TRF_STORAGE": "BIỂU CƯỚC LƯU BÃI",
			"UNIT_CODES": "ĐƠN VỊ TÍNH",
			"ACCOUNTS": "HÌNH THỨC THANH TOÁN",
			"ALLJOB_TYPE": "CÔNG VIỆC",
			"CARGO_TYPE": "LOẠI HÀNG",
			"CLASS_MODE": "HƯỚNG",
			"CNTR_DETAILS": "CONTAINER",
			"CNTR_SZTP": "KÍCH CỠ",
			"CNTR_SZTP_MAP": "MAP KÍCH CỠ",
			"CUSTOMERS": "KHÁCH HÀNG",
			"DELIVERY_METHODS": "PHƯƠNG THỨC VẬN CHUYỂN",
			"DELIVERY_MODE": "PHƯƠNG ÁN",
			"EIR": "LỆNH NÂNG HẠ",
			"EMP_BOOK": "BOOKING",
			"EXCHANGE_RATE": "TỈ GIÁ",
			"GATE_MONITOR": "CỔNG",
			"INV_DFT": "DRAFT",
			"INV_DFT_DTL": "CHI TIẾT DRAFT",
			"RF_CONFIG": "THIẾT LẬP GIỜ KIỂM TRA CONT LẠNH",
			"INV_VAT": "HOÁ ĐƠN",
			"MAPPING_CODE": "MAP CODE",
			"MENTHOD_MODE": "PHƯƠNG ÁN THEO NHẬP/XUẤT",
			"ORD_TPLT": "MẪU BIỂU CƯỚC",
			"RF_ONOFF": "CẮM RÚT LẠNH",
			"RF_TPLT": "CẤU HÌNH ĐIỆN LẠNH",
			"SRV_ODR": "LỆNH DỊCH VỤ / ĐÓNG / RÚT",
			"SRVMORE": "DỊCH VỤ ĐÍNH KÈM"
		};
		var changedType = {
			"I": 'INSERT',
			"U": 'UPDATE',
			"D": 'DELETE',
			"T": 'EXCHANGE INV'
		}

		var _colDetails = ["STT", "ID", "ChangedTime", "TableName", "OrderNo", "CntrNo", "FeautureName", "ChangedType", "ChangedBy", "ChangedIPAddress", "OldContent", "NewContent", "CJMode_CD", "BLNo", "BookingNo", "DRAFT_INV_NO", "InvNo"];
		var tblDetail = $("#tbl-ord-detail");
		var dtDetails = tblDetail.DataTable({
			order: [
				[0, 'asc']
			],
			paging: true,
			infor: false,
			scrollY: '40vh',
			buttons: [],
			rowGroup: {
				dataSrc: [4]
			},
			columnDefs: [{
					type: "num",
					className: "text-center",
					targets: _colDetails.indexOf('STT')
				},
				{
					visible: false,
					targets: _colDetails.indexOf('ID')
				},
				{
					className: "text-center",
					targets: _colDetails.getIndexs(['OldContent', 'NewContent']),
					render: function(data, type, full, meta) {
						return `<button class="btn btn-sm btn-default active view-detail"
										data-loading-text="<i class='la la-spinner spinner'></i>Chi tiết"
										data-col="` + _colDetails[meta.col] + `" data-id="` + full[_colDetails.indexOf('ID')] + `">
										Chi tiết
									</button>`;
					}
				},
				{
					className: "text-center",
					targets: _colDetails.indexOf('TableName'),
					render: function(data, type, full, meta) {
						return table_log[data] ? table_log[data] : data;
					}
				},
				{
					className: "text-center",
					targets: _colDetails.indexOf('ChangedType'),
					render: function(data, type, full, meta) {
						return changedType[data] ? changedType[data] : data;
					}
				},
				{
					className: "text-center",
					targets: _colDetails.indexOf('ChangedTime'),
					render: function(data, type, full, meta) {
						return moment(data).format('DD/MM/YYYY HH:mm:ss');
					}
				}
			],
			scroller: {
				displayBuffer: 9,
				boundaryScale: 0.5
			},
			select: {
				style: 'single',
				info: false
			},
			buttons: []
		});

		var usid = <?= json_encode($userIds) ?>;
		$("#changedBy").autocomplete({
			source: usid.map(p => p.UserID),
			minLength: 2
		});

		var cbTableHTML = '<option value="" selected>Tất cả</option>'
		Object.keys(table_log).forEach(function(v) {
			cbTableHTML += '<option value="' + v + '">' + table_log[v] + '</option>'
		});
		$('#tableName').html(cbTableHTML).selectpicker('refresh');

		var cbChangeType = '<option value="" selected>Tất cả</option>'
		Object.keys(changedType).forEach(function(v) {
			cbChangeType += '<option value="' + v + '">' + changedType[v] + '</option>'
		});
		$('#changedType').html(cbChangeType).selectpicker('refresh');

		var changedDateFrom = $('#changedDateFrom');
		var changedDateTo = $('#changedDateTo');
		setDateTimeRange(changedDateFrom, changedDateTo);
		autoLoadYearCombo('cb-searh-year');

		changedDateFrom.val(moment().subtract(1, 'day').format('DD/MM/YYYY 00:00'));
		changedDateTo.val(moment().format('DD/MM/YYYY 23:59'));

		$('.tagsinput').tagsinput({
			tagClass: 'label label-primary',
			maxTags: 10
		});

		$("#clear-date").on('click', function() {
			$('#changedDateFrom, #changedDateTo').val('');
		});

		$(document).on("click", 'button.view-detail', function() {
			var colName = $(this).data('col');
			var id = $(this).data('id');
			// $(this).closest('tbody').find('tr.selected')
			viewDetail(colName, id);
		});

		$("#loadData").on("click", function() {
			$(this).button("loading");
			search_log();
		});

		function search_log() {
			tblDetail.waitingLoad();

			var orderType = $("input[name='typeof']:checked").val(),
				fromDate = $("#changedDateFrom").val(),
				toDate = $("#changedDateTo").val(),
				changedBy = $("#changedBy").val(),
				changedType = $("#changedType").val(),
				tableName = $("#tableName").val(),
				findIn = $("input[name='Content']:checked").val(),
				searchVal = $("#searchVal").val();

			var formData = {
				action: "view",
				act: "search_value",
				args: {
					orderType: orderType,
					changedDateFrom: fromDate.trim(),
					changedDateTo: toDate.trim(),
					searchValue: searchVal,
					changedBy: changedBy.trim(),
					changedType: changedType.trim(),
					tableName: tableName.trim(),
					findIn: findIn.trim() ? findIn.trim() + 'Content' : "",
				}
			};

			$.ajax({
				url: "<?= site_url(md5('Tools') . '/' . md5('tlViewLogging')); ?>",
				dataType: 'json',
				data: formData,
				type: 'POST',
				success: function(data) {
					$("#loadData").button("reset");
					var rows = [];

					if (data.results && data.results.length > 0) {
						$.each(data.results, function(i, item) {
							var r = [];
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
								r.push(val);
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

		function viewDetail(colName, id) {
			var btn = $('button.view-detail[data-col="' + colName + '"][data-id="' + id + '"]');
			btn.button('loading');
			var formData = {
				"action": "view",
				"act": "view_detail",
				"id": id,
			};

			$.ajax({
				url: "<?= site_url(md5('Tools') . '/' . md5('tlViewLogging')); ?>",
				dataType: 'json',
				data: formData,
				type: 'POST',
				success: function(data) {
					btn.button('reset');
					if (data.deny) {
						toastr['error'](data.deny);
					}

					if (data) {
						let obj = JSON.parse(data[colName]);
						let strObj = JSON.stringify(obj, null, 4);
						let n = data.ChangedType == 'U' ? compareObj(JSON.parse(data.OldContent) || {}, JSON.parse(data.NewContent) || {}) : [];
						if (n.length > 0) {
							n.map(p => {
								strObj = strObj.replaceAll(p, `<span class="font-bold text-success">` + p + `</span>`);
							})
						}

						$('#notify-modal .modal-body').html('').html(strObj);
						$('#notify-modal').modal('show');
					} else {
						toastr['infor']('Không có chi tiết');
					}
				},
				error: function(err) {
					tblDetail.dataTable().fnClearTable();
					btn.button('reset');
					$('.toast').remove();
					toastr['error']("Có lỗi xảy ra! <br/>  Vui lòng liên hệ với bộ phận kỹ thuật! ");
					console.log(err);
				}
			});
		}
	});

	function compareObj(obj1, obj2) {
		var result = [];
		for (let i = 0; i < Object.keys(obj2).length; i++) {
			let key = Object.keys(obj2)[i];
			let val1 = String(obj1[key] || '').replaceAll('UNICODE', '');
			let val2 = String(obj2[key] || '').replaceAll('UNICODE', '');
			if (!obj1.hasOwnProperty(key) || val1 != val2) {
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

<script src="<?= base_url('assets/vendors/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js'); ?>"></script>