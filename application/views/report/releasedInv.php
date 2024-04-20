<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<link href="<?= base_url('assets/vendors/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css'); ?>" rel="stylesheet" />
<link href="<?= base_url('assets/vendors/bootstrap-select/dist/css/bootstrap-select.min.css'); ?>" rel="stylesheet" />
<link href="<?= base_url('assets/vendors/jquery-ui-month-year-picker/MonthPicker.css'); ?>" rel="stylesheet" />
<link href="<?= base_url('assets/vendors/dataTables/extensions/buttons.dataTables.min.css'); ?>" rel="stylesheet" />
<style>
	.ui-icon {
		margin-top: -0.9em !important;
		margin-left: -8px !important;
	}

	tfoot tr th {
		font-size: 12px !important;
		padding: 10px 8px !important;
	}
</style>
<div class="row">
	<div class="col-xl-12">
		<div class="ibox collapsible-box">
			<i class="la la-angle-double-up dock-right"></i>
			<div class="ibox-head">
				<div class="ibox-title">BÁO CÁO PHÁT HÀNH HÓA ĐƠN</div>
				<div class="button-bar-group mr-3">
					<button type="button" id="search" title="Nạp dữ liệu" data-loading-text="<i class='la la-spinner spinner'></i>Đang nạp" class="btn btn-sm btn-outline-primary mr-1">
						<i class="fa fa-refresh"></i>
						Nạp dữ liệu
					</button>
				</div>
			</div>
			<div class="ibox-body pt-3 pb-3 bg-f9 border-e">
				<form id="frmdata_export" method="post" action="<?= site_url(md5('Report') . '/' . md5('export_releaseInv')); ?>">
					<div class="row border-e bg-white pb-1 pt-3">
						<div class="col-xs-12 col-md-3 col-lg-3 col-xl-2">
							<div class="form-group" id="divDistance">
								<label class="radio radio-outline-primary" style="padding-right: 20px">
									<input name="distance" type="radio" value="1" checked>
									<span class="input-span"></span>
									Tháng
								</label>
								<label class="radio radio-outline-primary" style="padding-right: 20px">
									<input name="distance" type="radio" value="2">
									<span class="input-span"></span>
									Quý
								</label>
								<label class="radio radio-outline-primary">
									<input name="distance" type="radio" value="3">
									<span class="input-span"></span>
									Năm
								</label>
							</div>
							<div class="form-group">
								<input id="onlyMonth" name="onlyMonth" class="form-control form-control-sm" type="text">
							</div>
							<div class="form-group hiden-input">
								<div class="input-group">
									<select id="cb-period-month" class="selectpicker" data-style="btn-default bg-white btn-sm" data-width="40%">
										<option value="1" selected>1</option>
										<option value="2">2</option>
										<option value="3">3</option>
										<option value="4">4</option>
									</select>
									<select id="cb-period-year" class="selectpicker ml-1" data-style="btn-default bg-white btn-sm" data-width="60%">
									</select>
								</div>
							</div>
							<div class="form-group hiden-input">
								<select id="only-year" class="selectpicker" data-style="btn-default bg-white btn-sm" data-width="100%">
								</select>
							</div>
						</div>
						<div class="col-xs-12 col-md-3 col-lg-3 col-xl-2">
							<div class="form-group">
								<label class="mb-0">Loại tác nghiệp</label>
							</div>
							<div class="form-group">
								<select id="jmode" name="jmode" class="selectpicker" data-style="btn-default btn-sm bg-white" data-width="100%">
									<option value="*" selected>* (Tất cả)</option>
									<option value="LAYN">Lấy nguyên</option>
									<option value="HBAI">Hạ bãi</option>
									<option value="CAPR">Cấp rỗng</option>
									<option value="TRAR">Trả rỗng</option>
								</select>
							</div>
						</div>
						<div class="col-xs-12 col-md-3 col-lg-3 col-xl-2">
							<div class="form-group">
								<label class="mb-0">Loại thanh toán</label>
							</div>
							<div class="form-group">
								<select id="payment-type" name="payment-type" class="selectpicker" data-style="btn-default btn-sm bg-white" data-width="100%">
									<option value="*" selected>* (Tất cả)</option>
									<option value="CAS">Thu ngay</option>
									<option value="CRE">Thu sau</option>
								</select>
							</div>
						</div>

						<div class="col-xs-12 col-md-3 col-lg-3 col-xl-2">
							<div class="form-group">
								<label class="mb-0">Loại tiền</label>
							</div>
							<div class="form-group">
								<select id="currency" name="currency" class="selectpicker" data-style="btn-default btn-sm bg-white" data-width="100%">
									<option value="*" selected>* (Tất cả)</option>
									<option value="VND">VND</option>
									<option value="USD">USD</option>
								</select>
							</div>
						</div>
						<div class="col-xs-12 col-md-3 col-lg-3 col-xl-2">
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
						<div class="col-xs-12 col-md-3 col-lg-3 col-xl-2">
							<div class="form-group">
								<label class="mb-0">Hệ thống</label>
							</div>
							<div class="form-group">
								<select id="sys" class="selectpicker" data-style="btn-default btn-sm bg-white" data-width="100%">
									<option value="" selected>* (Tất cả)</option>
									<option value="TOS">Billing</option>
									<option value="VSL">VSL</option>
								</select>
							</div>
						</div>
					</div>
					<!-- <div class="row border-e bg-white pb-1 hidden-input">
						<div class="col-xs-3 col-md-3 col-lg-3 col-xl-3 mt-3">
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
					</div> -->
					<input id="exportdata" name="exportdata" type="text" style="display: none">
					<input id="fromdate" name="fromdate" type="text" style="display: none">
					<input id="todate" name="todate" type="text" style="display: none">
				</form>
			</div>
			<div class="row ibox-footer border-top-0">
				<div class="col-md-12 col-sm-12 col-xs-12 table-responsive">
					<div id="tablecontent">
						<table id="contenttable" class="table table-striped display nowrap" cellspacing="0" style="width: 99.9%">
							<thead>
								<tr>
									<th>STT</th>
									<th>Số PTC</th>
									<th>Ngày PTC</th>
									<th>Quyển HĐ</th>
									<th>Số HĐ</th>
									<th>Ngày HĐ</th>
									<th>Thành Tiền</th>
									<th>Thuế VAT</th>
									<th>Tổng Tiền</th>
									<th>Loại Hóa Đơn</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
							<tfoot>
								<tr>
									<th colspan="6" style="text-align:center;font-weight: bold;">TỔNG CỘNG</th>
									<th class="text-right"></th>
									<th class="text-right"></th>
									<th class="text-right"></th>
									<th></th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$('#contenttable').DataTable({
			scrollY: '50vh',
			columnDefs: [{
					className: "text-center",
					targets: [0, 2, 3, 5, 9]
				},
				{
					className: "text-right",
					targets: [6, 7, 8],
					render: $.fn.dataTable.render.number(',', '.', 0)
				}
			],
			order: [
				[0, 'asc']
			],
			paging: true,
			scroller: {
				displayBuffer: 9,
				boundaryScale: 0.95
			},
			buttons: [{
				text: '<i class="fa fa-files-o"></i> Xuất Excel',
				titleAttr: 'Xuất Excel',
				action: function() {
					if (!$("#exportdata").val()) {
						toastr.warning('Không có dữ liệu!');
						return;
					}
					$('#frmdata_export').submit();
				}
			}],
			footerCallback: function(row, data, start, end, display) {
				var api = this.api();
				var data = api.rows({
					search: 'applied'
				}).data().toArray();

				var amt = 0,
					vat = 0,
					tamt = 0;
				if (data.length > 0) {
					amt = data.map(p => p[6] || 0).reduce(function(a, b) {
						return parseFloat(a) + parseFloat(b);
					}, 0);
					vat = data.map(p => p[7] || 0).reduce(function(a, b) {
						return parseFloat(a) + parseFloat(b);
					}, 0);
					tamt = data.map(p => p[8] || 0).reduce(function(a, b) {
						return parseFloat(a) + parseFloat(b);
					}, 0);
				}

				// Update footer
				$(api.column(6).footer()).html($.formatNumber(amt, {
					format: "#,###",
					locale: "us"
				}));
				$(api.column(7).footer()).html($.formatNumber(vat, {
					format: "#,###",
					locale: "us"
				}));
				$(api.column(8).footer()).html($.formatNumber(tamt, {
					format: "#,###",
					locale: "us"
				}));
			}
		});

		$('#onlyMonth').MonthPicker({
			Button: function() {
				return $(this).next('.button');
			}
		});
		var crrentmonth = (new Date()).getMonth() + 1;
		$('#onlyMonth').val((crrentmonth < 10 ? "0" + crrentmonth : crrentmonth) + "/" + (new Date()).getFullYear());
		$('#cb-period-year, #only-year').autoYearPicker();

		$('#divDistance input[type="radio"]').on('change', function() {
			var checkval = $("#divDistance input[type='radio']:checked").attr('value');
			$('#divDistance').parent().find('div.form-group:not(:first-child)').addClass('hiden-input');
			$('#divDistance').parent().find('div.form-group:eq(' + checkval + ')').first().removeClass('hiden-input');
		});

		$('#search').on('click', function() {
			$("#contenttable").waitingLoad();

			var jmode = $('#jmode').val();
			var jdate = getFilterDate();
			var formData = {
				'action': 'view',
				'fromdate': jdate[0],
				'todate': jdate[1],
				'jmode': jmode,
				'paymentType': $("#payment-type").val(),
				'currency': $("#currency").val(),
				"sys": $("#sys").val(),
				'adjust_type': $("#adjust-type").val(),
			};

			$.ajax({
				url: "<?= site_url(md5('Report') . '/' . md5('rptReleasedInv')); ?>",
				dataType: 'json',
				data: formData,
				type: 'POST',
				success: function(data) {
					var rows = [];
					if (data.results.length > 0) {
						for (i = 0; i < data.results.length; i++) {
							let adjustName = '';
							switch (parseInt(data.results[i].AdjustType)) {
								case 1:
									adjustName = 'Thay thế';
									break;
								case 2:
									adjustName = 'Điều chỉnh tăng';
									break;
								case 3:
									adjustName = 'Điều chỉnh giảm';
									break;
								case 4:
									adjustName = 'Điều chỉnh thông tin';
									break;
								default:
									adjustName = 'HĐ gốc';
									break;
							}
							rows.push([
								(i + 1), data.results[i].DRAFT_INV_NO, getDateTime(data.results[i].DRAFT_INV_DATE), data.results[i].INV_PREFIX, data.results[i].INV_NO, getDateTime(data.results[i].INV_DATE), data.results[i].AMOUNT, data.results[i].VAT, data.results[i].TAMOUNT, adjustName
							]);
						}

						$("#exportdata").val(JSON.stringify(data.results));
						$("#fromdate").val(formData.fromdate);
						$("#todate").val(formData.todate);
					}

					$('#contenttable').dataTable().fnClearTable();
					if (rows.length > 0) {
						$('#contenttable').dataTable().fnAddData(rows);
					}
				},
				error: function(err) {
					$('#contenttable').dataTable().fnClearTable();
					console.log(err);
				}
			});
		});

		function getFilterDate() {
			var result = [];
			var td, frdate;
			var selected = $('#divDistance input[type="radio"]:checked').val();
			if (selected == 1) {
				if (!$('#onlyMonth').val()) {
					frdate = td = "";
				} else {
					frdate = "01/" + $('#onlyMonth').val();
					var daysinmonth1 = daysInMonth(parseInt($('#onlyMonth').val().split('/')[0]), parseInt($('#onlyMonth').val().split('/')[1]));
					td = (daysinmonth1 < 10 ? "0" + daysinmonth1 : daysinmonth1) + "/" + $('#onlyMonth').val();
				}
			}
			if (selected == 2) {
				var frmonth = $('#cb-period-month').val() != 4 ? "0" + ($('#cb-period-month').val() * 3 - 2) : $('#cb-period-month').val() * 3 - 2;
				frdate = "01/" + frmonth + "/" + $('#cb-period-year').val();
				var daysinmonth2 = daysInMonth(parseInt($('#cb-period-month').val() * 3), parseInt($('#cb-period-year').val()));
				td = (daysinmonth2 < 10 ? "0" + daysinmonth2 : daysinmonth2) + "/" + ($('#cb-period-month').val() != 4 ? "0" + ($('#cb-period-month').val() * 3) : $('#cb-period-month').val() * 3) + "/" + $('#cb-period-year').val();
			}
			if (selected == 3) {
				frdate = "01/01/" + $('#only-year').val();
				td = "31/12/" + $('#only-year').val();
			}
			result.push(frdate);
			result.push(td);
			return result;
		}
	});
</script>

<script src="<?= base_url('assets/vendors/moment/min/moment.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/bootstrap-select/dist/js/bootstrap-select.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/jquery-ui-month-year-picker/MonthPicker.js'); ?>"></script>
<!--format number-->
<script src="<?= base_url('assets/js/jshashtable-2.1.js'); ?>"></script>
<script src="<?= base_url('assets/js/jquery.numberformatter-1.2.3.min.js'); ?>"></script>