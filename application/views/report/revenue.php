<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<link href="<?= base_url('assets/vendors/bootstrap-select/dist/css/bootstrap-select.min.css'); ?>" rel="stylesheet" />
<link href="<?= base_url('assets/vendors/jquery-ui-month-year-picker/MonthPicker.css'); ?>" rel="stylesheet" />
<link href="<?= base_url('assets/vendors/dataTables/extensions/buttons.dataTables.min.css'); ?>" rel="stylesheet" />

<style>
	table.dataTable thead {
		font-weight: bold;
		color: #000060 !important;
		background: rgb(222, 239, 255) !important;
		/* Old browsers */
		background: -moz-linear-gradient(top, rgba(222, 239, 255, 1) 0%, rgba(170, 199, 224, 1) 100%) !important;
		/* FF3.6-15 */
		background: -webkit-linear-gradient(top, rgba(222, 239, 255, 1) 0%, rgba(170, 199, 224, 1) 100%) !important;
		/* Chrome10-25,Safari5.1-6 */
		background: linear-gradient(to bottom, rgba(222, 239, 255, 1) 0%, rgba(170, 199, 224, 1) 100%) !important;
		/* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
		filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#deefff', endColorstr='#98bede', GradientType=0) !important;
		/* IE6-9 */
		/*background-color: #bddcef;*/
		text-align: center !important;
		vertical-align: middle !important;
	}

	table.dataTable thead tr {
		color: none !important;
		background: none !important;
		filter: none !important;
		/*background-color: #bddcef;*/
		text-align: center !important;
	}

	table.dataTable thead tr th {
		text-align: center !important;
	}

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
				<div class="ibox-title">BÁO CÁO TỔNG HỢP DOANH THU</div>
				<div class="button-bar-group mr-3">
					<button type="button" id="search" title="Nạp dữ liệu" data-loading-text="<i class='la la-spinner spinner'></i>Đang nạp" class="btn btn-sm btn-outline-primary mr-1">
						<i class="fa fa-refresh"></i>
						Nạp dữ liệu
					</button>
					<button type="button" id="report-excel" title="Xuất báo cáo" data-loading-text="<i class='la la-spinner spinner'></i>Đang xuất" class="btn btn-sm btn-outline-secondary mr-1">
						<i class="la la-file-excel-o"></i>
						Xuất báo cáo
					</button>
				</div>
			</div>
			<div class="ibox-body pt-3 pb-3 bg-f9 border-e">
				<form id="frmdata_export" method="post" action="<?= site_url(md5('Report') . '/' . md5('export_revenue')); ?>">
					<div class="row border-e bg-white pb-1 pt-3">
						<div class="col-xs-6 col-md-5 col-lg-4 col-xl-3">
							<div class="form-group" id="divDistance">
								<label class="radio radio-outline-primary" style="padding-right: 20px">
									<input name="distance" type="radio" value="1" checked>
									<span class="input-span"></span>
									Tuỳ chọn
								</label>
								<label class="radio radio-outline-primary" style="padding-right: 20px">
									<input name="distance" type="radio" value="2">
									<span class="input-span"></span>
									Tháng
								</label>
								<label class="radio radio-outline-primary" style="padding-right: 20px">
									<input name="distance" type="radio" value="3">
									<span class="input-span"></span>
									Quý
								</label>
								<label class="radio radio-outline-primary">
									<input name="distance" type="radio" value="4">
									<span class="input-span"></span>
									Năm
								</label>
							</div>
							<div class="form-group">
								<div class="input-group">
									<input class="form-control form-control-sm mr-2" id="issueFrom" type="text" placeholder="Từ ngày">
									<input class="form-control form-control-sm" id="issueTo" type="text" placeholder="Đến ngày">
								</div>
							</div>
							<div class="form-group hiden-input">
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
										<option value="2015">2015</option>
										<option value="2016">2016</option>
										<option value="2017">2017</option>
										<option value="2018" selected>2018</option>
										<option value="2019">2019</option>
										<option value="2020">2020</option>
										<option value="2021">2021</option>
										<option value="2022">2022</option>
										<option value="2023">2023</option>
										<option value="2024">2024</option>
										<option value="2025">2025</option>
									</select>
								</div>
							</div>
							<div class="form-group hiden-input">
								<select id="only-year" class="selectpicker" data-style="btn-default bg-white btn-sm" data-width="100%">
									<option value="2015">2015</option>
									<option value="2016">2016</option>
									<option value="2017">2017</option>
									<option value="2018" selected>2018</option>
									<option value="2019">2019</option>
									<option value="2020">2020</option>
									<option value="2021">2021</option>
									<option value="2022">2022</option>
									<option value="2023">2023</option>
									<option value="2024">2024</option>
									<option value="2025">2025</option>
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
									<option value="NH">Nâng hạ</option>
									<option value="DH">Đóng hàng</option>
									<option value="RH">Rút hàng</option>
									<option value="CC">Sang container</option>
									<option value="DVB">Dịch vụ bãi</option>
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
									<option value="VND" selected>VND</option>
									<option value="USD">USD</option>
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

						<!-- <div class="col-xs-3 col-md-3 col-lg-2 col-xl-2 mt-3 hidden-input">
							<div class="form-group col-form-label">
								<label class="radio radio-inline">
									<input type="radio" name="sys" value="BL" checked>
									<span class="input-span"></span>BILLING</label>
							</div>
							<div class="form-group">
								<label class="radio radio-inline">
									<input type="radio" name="sys" value="EP">
									<span class="input-span"></span>EPORT</label>
							</div>
						</div> -->
					</div>
					<input id="exportdata" name="exportdata" type="text" style="display: none">
					<input id="fromdate" name="fromdate" type="text" style="display: none">
					<input id="todate" name="todate" type="text" style="display: none">
					<input id="cjmodeName" name="cjmodeName" type="text" style="display: none">
					<input id="paymentType" name="paymentType" type="text" style="display: none">
				</form>
			</div>
			<div class="row ibox-footer">
				<div class="col-md-12 col-sm-12 col-xs-12 table-responsive">
					<div id="tablecontent">
						<table id="contenttable" class="table table-striped display nowrap" cellspacing="0" style="width: 99.9%">
							<thead>
								<tr>
									<th rowspan="2">STT</th>
									<th rowspan="2">DOANH THU LÀM THỦ TỤC DỊCH VỤ</th>
									<th colspan="3">LOẠI CONTAINER</th>
									<th rowspan="2">THÀNH TIỀN</th>
									<th rowspan="2">TIỀN THUẾ</th>
									<th rowspan="2">TỔNG TIỀN</th>
								</tr>
								<tr>
									<th>20'</th>
									<th>40'</th>
									<th>45'</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
							<tfoot>
								<tr class="text-danger">
									<th></th>
									<th style="text-align:center;font-weight: bold;">TỔNG CỘNG</th>
									<th class="text-right"></th>
									<th class="text-right"></th>
									<th class="text-right"></th>
									<th class="text-right"></th>
									<th class="text-right"></th>
									<th class="text-right"></th>
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
				customize: function(xlsx) {
					var sheet = xlsx.xl.worksheets['sheet1.xml'];
					$('row c[r^="K"], row c[r^="M"], row c[r^="N"]', sheet).attr('s', 63);
				}
			}],
			paging: true,
			searching: true,
			scrollY: '45vh',
			columnDefs: [{
				className: "text-center",
				targets: 0
			}, {
				className: "text-right",
				targets: [2, 3, 4],
				render: $.fn.dataTable.render.number(',', '.', 0)
			}, {
				className: "text-right",
				targets: [5, 6, 7],
				render: function(data, type, full, meta) {
					return $.fn.dataTable.render.number(',', '.', $("#currency").val() == 'USD' ? 2 : 0).display(data);
				}
			}],
			order: [
				[0, 'asc']
			],
			scroller: {
				displayBuffer: 9,
				boundaryScale: 0.95
			},
			footerCallback: function(row, data, start, end, display) {
				var api = this.api();
				var data = api.rows({
					search: 'applied'
				}).data().toArray();

				if (data.length > 0) {
					let $frmat = $('#currency').val() == 'USD' ? "#,###.00" : "#,###";
					let sum20 = data.map(p => p[2] || 0).reduce(function(a, b) {
						return parseFloat(a) + parseFloat(b);
					}, 0);
					let sum40 = data.map(p => p[3] || 0).reduce(function(a, b) {
						return parseFloat(a) + parseFloat(b);
					}, 0);
					let sum45 = data.map(p => p[4] || 0).reduce(function(a, b) {
						return parseFloat(a) + parseFloat(b);
					}, 0);

					let sumAmt = data.map(p => p[5] || 0).reduce(function(a, b) {
						return parseFloat(a) + parseFloat(b);
					}, 0);
					let sumVat = data.map(p => p[6] || 0).reduce(function(a, b) {
						return parseFloat(a) + parseFloat(b);
					}, 0);
					let sumTamount = data.map(p => p[7] || 0).reduce(function(a, b) {
						return parseFloat(a) + parseFloat(b);
					}, 0);

					$(api.column(2).footer()).html($.formatNumber(sum20, {
						format: "#,###",
						locale: "us"
					}));
					$(api.column(3).footer()).html($.formatNumber(sum40, {
						format: "#,###",
						locale: "us"
					}));
					$(api.column(4).footer()).html($.formatNumber(sum45, {
						format: "#,###",
						locale: "us"
					}));

					$(api.column(5).footer()).html($.formatNumber(sumAmt, {
						format: $frmat,
						locale: "us"
					}));
					$(api.column(6).footer()).html($.formatNumber(sumVat, {
						format: $frmat,
						locale: "us"
					}));
					$(api.column(7).footer()).html($.formatNumber(sumTamount, {
						format: $frmat,
						locale: "us"
					}));

				} else {
					$(api.column(2).footer()).html(0);
					$(api.column(3).footer()).html(0);
					$(api.column(4).footer()).html(0);
					$(api.column(5).footer()).html(0);
					$(api.column(6).footer()).html(0);
					$(api.column(7).footer()).html(0);
				}
				// var api = this.api();
				// // Update footer
				// $(api.column(2).footer()).html($.fn.dataTable.render.number(',', '.', 0).display(s20));
				// $(api.column(3).footer()).html($.fn.dataTable.render.number(',', '.', 0).display(s40));
				// $(api.column(4).footer()).html($.fn.dataTable.render.number(',', '.', 0).display(s45));
				// $(api.column(5).footer()).html($.fn.dataTable.render.number(',', '.', 0).display(tamt));

			}
		});

		var issueFrom = $('#issueFrom');
		var issueTo = $('#issueTo');
		setDateTimeRange(issueFrom, issueTo); //, 'yy-mm-dd', 'HH:mm:ss'

		issueFrom.val(moment().startOf('month').format('DD/MM/YYYY 00:00'));
		issueTo.val(moment().format('DD/MM/YYYY 23:59'));

		$('#onlyMonth').MonthPicker({
			Button: function() {
				return $(this).next('.button');
			}
		});
		var crrentmonth = (new Date()).getMonth() + 1;
		$('#onlyMonth').val((crrentmonth < 10 ? "0" + crrentmonth : crrentmonth) + "/" + (new Date()).getFullYear());

		$('#divDistance input[type="radio"]').on('change', function() {
			var checkval = $("#divDistance input[type='radio']:checked").attr('value');
			$('#divDistance').parent().find('div.form-group:not(:first-child)').addClass('hiden-input');
			$('#divDistance').parent().find('div.form-group:eq(' + checkval + ')').first().removeClass('hiden-input');
		});

		$('#search').on('click', function() {
			$("#contenttable").waitingLoad(8);

			var jmode = $('#jmode').val();

			var jdate = getFilterDate();
			var formData = {
				action: 'view',
				args: {
					'fromdate': jdate[0],
					'todate': jdate[1],
					'jmode': jmode,
					'payment-type': $('#payment-type').val(),
					'currency': $('#currency').val(),
					"sys": $("#sys").val()
				}
			};

			$('#exportdata').val("");
			$('#fromdate').val(formData.args.fromdate);
			$('#todate').val(formData.args.todate);
			$('#cjmodeName').val($(`#jmode option[value="${formData.args.jmode}"]`).text());
			$('#paymentType').val($(`#payment-type option[value="${formData.args['payment-type']}"]`).text());

			$.ajax({
				url: "<?= site_url(md5('Report') . '/' . md5('rptRevenue')); ?>",
				dataType: 'json',
				data: formData,
				type: 'POST',
				success: function(data) {
					var rows = [];
					if (data.results.length > 0) {
						$('#exportdata').val(JSON.stringify(data.results));
						for (i = 0; i < data.results.length; i++) {
							rows.push([
								(i + 1), data.results[i].TRF_CODE + " - " + data.results[i].TRF_DESC, data.results[i]["20"] != "0" ? data.results[i]["20"] : "", data.results[i]["40"] != "0" ? data.results[i]["40"] : "", data.results[i]["45"] != "0" ? data.results[i]["45"] : "", data.results[i].SUMAMOUNT, data.results[i].SUMVAT, data.results[i].SUMTAMOUNT
							]);
						}
					}

					$('#contenttable').dataTable().fnClearTable();
					if (rows.length > 0) {
						$('#contenttable').dataTable().fnAddData(rows);
					}
				},
				error: function(err) {
					$('#contenttable').dataTable().fnClearTable();
					toastr.error(err?.message || 'Lỗi nạp dữ liệu!');
					console.log(err);
				}
			});
		});
	});

	function getFilterDate() {
		var result = [];
		var td, frdate;
		var selected = $('#divDistance input[type="radio"]:checked').val();

		if (selected == 1) {
			frdate = $('#issueFrom').val();
			td = $('#issueTo').val();
		}

		if (selected == 2) {
			if (!$('#onlyMonth').val()) {
				frdate = td = "";
			} else {
				frdate = "01/" + $('#onlyMonth').val();
				var daysinmonth1 = daysInMonth(parseInt($('#onlyMonth').val().split('/')[0]), parseInt($('#onlyMonth').val().split('/')[1]));
				td = (daysinmonth1 < 10 ? "0" + daysinmonth1 : daysinmonth1) + "/" + $('#onlyMonth').val();
			}
		}
		if (selected == 3) {
			var frmonth = $('#cb-period-month').val() != 4 ? "0" + ($('#cb-period-month').val() * 3 - 2) : $('#cb-period-month').val() * 3 - 2;
			frdate = "01/" + frmonth + "/" + $('#cb-period-year').val();
			var daysinmonth2 = daysInMonth(parseInt($('#cb-period-month').val() * 3), parseInt($('#cb-period-year').val()));
			td = (daysinmonth2 < 10 ? "0" + daysinmonth2 : daysinmonth2) + "/" + ($('#cb-period-month').val() != 4 ? "0" + ($('#cb-period-month').val() * 3) : $('#cb-period-month').val() * 3) + "/" + $('#cb-period-year').val();
		}

		if (selected == 4) {
			frdate = "01/01/" + $('#only-year').val();
			td = "31/12/" + $('#only-year').val();
		}

		result.push(frdate);
		result.push(td);
		return result;
	}
</script>

<script src="<?= base_url('assets/vendors/moment/min/moment.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/bootstrap-select/dist/js/bootstrap-select.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/jquery-ui-month-year-picker/MonthPicker.js'); ?>"></script>
<!--format number-->
<script src="<?= base_url('assets/js/jshashtable-2.1.js'); ?>"></script>
<script src="<?= base_url('assets/js/jquery.numberformatter-1.2.3.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/dataTables/datatables.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/dataTables/dataTables.buttons.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/dataTables/extensions/jszip.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/dataTables/extensions/buttons.html5.min.js'); ?>"></script>