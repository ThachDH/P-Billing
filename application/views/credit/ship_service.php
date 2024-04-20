<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<link href="<?= base_url('assets/vendors/bootstrap-select/dist/css/bootstrap-select.min.css'); ?>" rel="stylesheet" />
<link href="<?= base_url('assets/vendors/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css'); ?>" rel="stylesheet" />
<style>
	@media (max-width: 767px) {
		.f-text-right {
			text-align: right;
		}
	}

	.no-pointer {
		pointer-events: none;
	}

	span.col-form-label {
		width: 100%;
		border-bottom: dotted 1px #ccc;
		display: inline-block;
		word-wrap: break-word;
	}

	#INV_DRAFT_TOTAL span.col-form-label {
		width: 64%;
		border-bottom: dotted 1px;
		display: inline-block;
		word-wrap: break-word;
	}

	.form-group {
		margin-bottom: 10px;
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

	.nav>.tooltip-addon {
		flex-grow: 1;
		display: flex;
		flex-wrap: wrap;
		justify-content: flex-end;
	}

	.tooltip-addon>i {
		margin-top: auto;
		margin-bottom: auto;
		margin-right: 10px;
	}

	.nav-link.publish-opt:not(.active) {
		text-decoration: underline;
	}
</style>
<div class="row">
	<div class="col-xl-12">
		<div class="ibox collapsible-box">
			<i class="la la-angle-double-up dock-right"></i>
			<div class="ibox-head">
				<div class="ibox-title" id="panel-title">DỊCH VỤ TÀU</div>
				<div class="button-bar-group mr-3">
					<button id="remove" class="btn btn-outline-danger btn-sm">
						<span class="btn-icon"><i class="fa fa-trash"></i>Xóa dòng</span>
					</button>
					<button id="pay-confirm" class="btn btn-outline-primary btn-sm" data-loading-text="<i class='la la-spinner spinner'></i>Đang xử lý" title="Xác nhận thanh toán">
						<span class="btn-icon"><i class="fa fa-id-card"></i> Xác nhận thanh toán</span>
					</button>
					<button type="button" id="view-draft-inv" title="Xem HĐ nháp" data-loading-text="<i class='la la-spinner spinner'></i>Đang tạo" class="btn btn-sm btn-outline-secondary mr-1">
						<i class="fa fa-eye"></i>
						Xem HĐ nháp
					</button>
				</div>
			</div>
			<div class="ibox-body pt-3 pb-2 bg-f9 border-e">
				<div class="row">
					<div class="col-6 pl-0">
						<div class="ibox mb-0 border-e p-4">
							<div class="form-group pb-1">
								<h5 class="text-primary" style="border-bottom: 1px solid #eee">Thông tin chung</h5>
							</div>
							<div class="row form-group">
								<label class="col-sm-1 col-form-label">Tàu</label>
								<div class="col-sm-5 input-group">
									<input class="form-control form-control-sm input-required " id="shipid" placeholder="Tàu/chuyến" type="text" readonly>
									<span class="input-group-addon bg-white btn mobile-hiden text-warning" style="padding: 0 .5rem" title="chọn tàu" data-toggle="modal" data-target="#ship-modal">
										<i class="ti-search"></i>
									</span>
								</div>
							</div>
							<div class="row form-group">
								<label class="col-sm-1 col-form-label" title="Shifting" style="overflow: visible;">Shifting</label>
								<div class="col-sm-7 input-group input-group-sm">
									<div class="input-group">
										<span class="col-form-label " id="shifing" style="font-size: 11px!important;font-family:Tahoma;">&nbsp;</span>
										<!-- <input class="form-control form-control-sm" id="" type="text" placeholder="Từ ngày"> -->
									</div>
								</div>
								<label class="col-sm-1 col-form-label">LOA</label>
								<div class="col-md-2 input-group input-group-sm">
									<div class="input-group">
										<span class="col-form-label " id="loa" style="font-size: 11px!important;font-family:Tahoma;">&nbsp;</span>
										<!-- <input class="form-control form-control-sm" id="FE" type="text" placeholder="Chiều dài"> -->
									</div>
								</div>
							</div>
							<div class="row form-group">
								<label class="col-sm-1 col-form-label" title="Quốc tịch" style="overflow: visible;">Quốc tịch</label>
								<div class="col-md-3 input-group input-group-sm">
									<span class="col-form-label " id="nation" style="font-size: 11px!important;font-family:Tahoma;">&nbsp;</span>
								</div>
								<label class="col-sm-1 col-form-label" title="Hãng khai thác">Hãng</label>
								<div class="col-md-3 input-group input-group-sm">
									<div class="input-group">
										<span class="col-form-label " id="agency" style="font-size: 11px!important;font-family:Tahoma;">&nbsp;</span>
									</div>
								</div>
								<label class="col-sm-1 col-form-label" title="Trọng tải đăng kiểm">GRT</label>
								<div class="col-md-2 input-group input-group-sm">
									<div class="input-group">
										<span class="col-form-label " id="grt" style="font-size: 11px!important;font-family:Tahoma;">&nbsp;</span>
										<!-- <input class="form-control form-control-sm" id="FE" type="text" placeholder="Trọng tải đăng kiểm"> -->
									</div>
								</div>
							</div>
							<div class="row form-group">
								<label class="col-sm-1 col-form-label" title="Ngày tàu cập">ATB</label>
								<div class="col-sm-3 input-group input-group-sm">
									<div class="input-group">
										<span class="col-form-label " id="ATB" style="font-size: 11px!important;font-family:Tahoma;">&nbsp;</span>
										<!-- <input class="form-control form-control-sm" id="ATB" type="text" placeholder="Ngày tàu cập"> -->
									</div>
								</div>
								<label class="col-sm-1 col-form-label" title="Ngày tàu rời">ATD</label>
								<div class="col-sm-3 input-group input-group-sm">
									<div class="input-group">
										<span class="col-form-label " id="ATD" style="font-size: 11px!important;font-family:Tahoma;">&nbsp;</span>
										<!-- <input class="form-control form-control-sm" id="ATD" type="text" placeholder="Ngày tàu rời"> -->
									</div>
								</div>
								<label class="col-sm-1 col-form-label" title="Trọng tải toàn phần">DWT</label>
								<div class="col-md-2 input-group input-group-sm">
									<div class="input-group">
										<span class="col-form-label " id="dwt" style="font-size: 11px!important;font-family:Tahoma;">&nbsp;</span>
										<!-- <input class="form-control form-control-sm" id="FE" type="text" placeholder="Trọng tải toàn phần"> -->
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-6 my-box table-responsive p-3">
						<div class="form-group row">
							<div class="col-sm-10">
								<h5 class="text-primary" style="border-bottom: 1px solid #eee">Dịch vụ</h5>
							</div>
						</div>
						<table id="tb-srv" class="table table-striped display nowrap single-row-select" cellspacing="0" style="width: 99%">
							<thead>
								<tr>
									<th class="editor-cancel data-type-checkbox" style="max-width: 30px">Chọn</th>
									<th col-name="CJMode_CD">Mã phương án</th>
									<th col-name="CJModeName">Tên phương án</th>
									<th col-name="Cont_Count">Số lượng</th>
								</tr>
							</thead>

							<tbody>
							</tbody>
						</table>
					</div>
				</div>
				<div class="row mt-2 pt-2 my-box">
					<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="row">
							<div class="col-6 mb-0 p-3 pl-0">
								<div class="form-group pb-1">
									<h5 class="text-primary" style="border-bottom: 1px solid #eee">Thanh toán</h5>
								</div>
								<div class="row form-group">
									<label class="col-sm-1 col-form-label" title="Đối tượng thanh toán">ĐTTT*</label>
									<div class="col-sm-3">
										<div class="input-group">
											<input class="form-control form-control-sm input-required" id="taxcode" placeholder="Đang nạp ..." type="text">
											<span class="input-group-addon bg-white btn mobile-hiden text-warning" style="padding: 0 .5rem" title="Chọn đối tượng thanh toán" data-toggle="modal" data-target="#payer-modal">
												<i class="ti-search"></i>
											</span>
										</div>
									</div>
									<div class="col-md-8 input-group input-group-sm">
										<i class="fa fa-id-card" style="font-size: 15px!important;"></i>-<span id="payer-name">
											[Tên đối tượng thanh toán]</span>&emsp;
									</div>
									<input class="hiden-input" id="cusID" readonly>
								</div>

								<div class="row form-group">
									<label style="overflow: visible;" class="col-sm-1 col-form-label" title="Hình thức thanh toán">Loại tiền</label>
									<div class="col-sm-3">
										<select id="inv-temp-currency" class="selectpicker input-required" data-style="btn-default btn-sm" data-width="100%">
											<option value="VND" selected=""> VNĐ</option>
											<option value="USD"> USD</option>
										</select>
									</div>
									<div class="col-md-8 input-group input-group-sm">
										<i class="fa fa-home" style="font-size: 15px!important;"></i>-<span id="payer-addr">
											[Địa chỉ]</span>&emsp;
									</div>
								</div>
								<div class="row form-group">
									<label style="overflow: visible;" class="col-sm-1 col-form-label" title="Mẫu cước">Mẫu cước</label>
									<div class="col-sm-3">
										<select id="inv-temp" class="selectpicker input-required" data-style="btn-default btn-sm" data-live-search="true" data-width="100%">
											<option value="" selected="">-- Chọn Mẫu cước --</option>
											<?php if (isset($invTemps) && count($invTemps) > 0) {
												foreach ($invTemps as $item) { ?>
													<option value="<?= $item['TPLT_NM'] ?>">
														<?= $item['TPLT_NM'] . " : " . $item['TPLT_DESC'] ?>
													</option>
											<?php }
											} ?>
										</select>
									</div>
								</div>
							</div>
							<div class="col-6 mb-0 pl-0 ">
								<div class="row">

									<div class="col-xl-7 col-lg-7 col-md-7 col-sm-7 col-xs-7 mt-3">
										<div class="row" id="INV_DRAFT_TOTAL">
											<div class="col-sm-12">
												<div class="form-group pb-1">
													<h5 class="text-primary" style="border-bottom: 1px solid #eee">Tổng cước phí</h5>
												</div>
												<div class="row form-group">
													<label class="col-sm-3 col-form-label">Thành tiền</label>
													<span class="col-form-label text-right font-bold text-blue" id="AMOUNT">0</span>
													&ensp;
													<div class="currency-unit col-form-label text-right font-bold text-blue">VND</div>
												</div>
												<div class="row form-group hiden-input">
													<label class="col-sm-3 col-form-label">Giảm trừ</label>
													<span class="col-form-label text-right font-bold text-blue" id="DIS_AMT">0</span>
													&ensp;
													<div class="currency-unit col-form-label text-right font-bold text-blue">VND</div>
												</div>
												<div class="row form-group">
													<label class="col-sm-3 col-form-label">Tiền thuế</label>
													<span class="col-form-label text-right font-bold text-blue" id="VAT">0</span>
													&ensp;
													<div class="currency-unit col-form-label text-right font-bold text-blue">VND</div>
												</div>
												<div class="row form-group">
													<label class="col-sm-3 col-form-label">Tổng tiền</label>
													<span class="col-form-label text-right font-bold text-danger" id="TAMOUNT">0</span>
													&ensp;
													<div class="currency-unit col-form-label text-right font-bold text-danger">VND</div>
												</div>
											</div>
										</div>
										<div class="row mt-3">
											<div class="col-sm-12 publish-type">
												<div class="form-group">
													<textarea class="form-control form-control-sm" id="remark" placeholder="GHI CHÚ HÓA ĐƠN " style="height: 44px"></textarea>
												</div>
											</div>
										</div>
									</div>
									<div class="col-xl-5 col-lg-5 col-md-5 col-sm-5 col-xs-5 mt-3">
										<div class="col-sm-12 publish-type">
											<div class="form-group">
												<h5 class="text-primary" style="border-bottom: 1px solid #eee">Loại phát hành</h5>
											</div>
											<div class="form-group">
												<ul class="nav nav-pills nav-pills-rounded nav-pills-air nav-pills-primary nav-justified pb-1">
													<li class="nav-item ">
														<a class="nav-link active publish-opt" href="#tab-12-1" data-value="dft" data-toggle="tab">Phiếu thu</a>
													</li>
													<li class="nav-item ">
														<a class="nav-link publish-opt" href="#tab-12-3" data-value="e-inv" data-toggle="tab">HĐ điện tử</a>
													</li>
												</ul>
											</div>
										</div>
										<div class="form-group hiden-input" id="inv-type-container">
											<div class="form-group row">
												<label class="col-form-label col-sm-3" title="Loại hóa đơn">Loại HĐ</label>
												<select id="inv-type" class="selectpicker col-sm-9 pl-0 pt-0" data-style="btn-default btn-sm" data-width="100%">
													<option value="VND" selected=""> Hóa đơn VND </option>
													<option value="USD"> Hóa đơn USD </option>
												</select>
											</div>
											<div class="form-group row">
												<label class="col-form-label col-sm-3" title="Tỉ giá">Tỉ giá</label>
												<div class="col-sm-9 pl-0 pt-0" data-width="100%">
													<input id="ExchangeRate" class="form-control form-control-sm text-right" value="1" placeholder="Tỉ giá" type="text">
												</div>
											</div>
											<div id="m-inv-date" class="form-group" style="display:block">
												<label class="col-form-label" title="Ngày Hoá đơn">Ngày HĐ</label>
												<input id="inv-date" class="form-control form-control-sm" placeholder="Ngày HĐ" type="text">
											</div>
											<div class="form-group mb-0">
												<div id="m-inv-container" class="form-group mb-0 hiden-input">
													<label class="col-form-label">Số HĐ kế tiếp</label>
													<div class="col-form-label text-danger font-bold">
														<span id="ss-invNo">
															chưa khai báo
														</span>
														<button id="change-ssinvno" class="btn btn-outline-primary btn-sm" style="height: 20px; width:28px" title="Khai báo/ thay đổi số hóa đơn sử dụng tiếp theo">
															<span class="btn-icon"><i class="fa fa-pencil"></i></span>
														</button>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="row mt-2 pt-2 my-box">
				<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<table id="tbl-inv" class="table table-striped display nowrap" cellspacing="0">
						<thead>
							<tr>
								<th col-name="rowguid">rowguid</th>
								<th col-name="STT">STT</th>
								<th col-name="TRF_CODE">Mã BC</th>
								<th col-name="TRF_DESC" max-length=250>Tên biểu cước</th>
								<th col-name="Remark" max-length=100>Ghi chú hóa đơn</th>
								<th col-name="INV_UNIT" max-length=3>ĐVT</th>
								<th col-name="IX_CD" class="autocomplete" default-value="*">Hướng</th>
								<th col-name="CARGO_TYPE" class="autocomplete" default-value="*">Loại hàng</th>
								<th col-name="SZ" class="autocomplete" default-value="*">SIZE</th>
								<th col-name="FE" class="autocomplete" default-value="*">F/E</th>
								<th col-name="IsLocal" default-value="*">Nội/Ngoại (L/F)</th>
								<th col-name="QTY" class="data-type-numeric" float-nums=<?= json_encode($this->config->item('ROUND_NUM_QTY_UNIT')); ?>>Số Lượng</th>
								<th col-name="GRT" class="data-type-numeric">Trọng tải</th>
								<th col-name="Shifting" class="data-type-numeric">Số giờ</th>
								<th col-name="standard_rate" class="data-type-numeric">Đơn giá gồm thuế</th>
								<th col-name="UNIT_RATE" class="data-type-numeric" float-nums=4>Đơn giá chưa thuế</th>
								<th col-name="AMOUNT" class="data-type-numeric">Thành tiền</th>
								<th col-name="VAT_RATE" class="data-type-numeric">Thuế (%)</th>
								<th col-name="VAT" class="data-type-numeric">Tiền thuế</th>
								<th col-name="TAMOUNT" class="data-type-numeric">Tổng tiền</th>
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
								<input name="shipArrStatus" type="radio" value="1">
								<span class="input-span"></span>
								Đến cảng
							</label>
							<label class="radio radio-outline-primary">
								<input name="shipArrStatus" value="2" type="radio" checked>
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
										<option value="2023">2023</option>
										<option value="2021">2021</option>
										<option value="2022">2022</option>
										<option value="2023">2023</option>
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
								<th>ShipYear</th>
								<th>ShipVoy</th>
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

<select id="status" style="display: none">
	<option value="0">Ngưng hoạt động</option>
	<option value="1">Hoạt động</option>
</select>

<select id="httt" style="display: none">
	<option value="0">Thu ngay</option>
	<option value="1">Thu sau</option>
</select>

<script type="text/javascript">
	$(document).ready(function() {
		var _colsPayment = ["rowguid", "STT", "TRF_CODE", "TRF_DESC", "Remark", "INV_UNIT", "IX_CD", "CARGO_TYPE", "SZ", "FE", "IsLocal", "QTY", 'GRT', 'Shifting', "standard_rate", "UNIT_RATE", "AMOUNT", "VAT_RATE", "VAT", "TAMOUNT"];

		var _colPayer = ["STT", "CusID", "VAT_CD", "CusName", "Address", "CusType"],
			_colInfo = ["rowguid", "STT", "CntrNo", "LocalSZPT", "Status", "congviec"],
			_colServices = ["Select", "CJMode_CD", "CJModeName", "CJMode_SL"];

		var _useTplts = [],
			services = [],
			_listTariff = [],
			tblInv = $("#tbl-inv"),
			tblSrv = $('#tb-srv'),
			calUnit = {},
			_selectShipKey;

		var _cargoTypes = <?= json_encode($cargoTypes) ?>;
		_cargoTypes.map(x => x.Description = x.Description.toUpperCase());
		var invTemps = <?= json_encode($invTemps); ?>;
		var _roundNumQty_Unit = <?= json_encode($this->config->item('ROUND_NUM_QTY_UNIT')); ?>; //lam tron so luong+don gia theo yeu cau KT
		var _roundNums = <?= json_encode($this->config->item('ROUND_NUM')); ?>; //them moi lam tron so
		var _cntrClass = <?= json_encode($cntrClass) ?>;


		if (_cargoTypes.filter(p => p.Code == '*').length == 0) {
			_cargoTypes.push({
				Code: '*',
				Description: '*'
			});
		}
		if (_cntrClass.filter(p => p.CLASS_Code == '*').length == 0) {
			_cntrClass.push({
				CLASS_Code: '*',
				CLASS_Name: '*'
			});
		}

		var _localForeign = [{
					"Code": "*",
					"Name": "*"
				},
				{
					"Code": "L",
					"Name": "Nội"
				},
				{
					"Code": "F",
					"Name": "Ngoại"
				}
			],
			_status = [{
					"Code": "*",
					"Name": "*"
				}, {
					"Code": "F",
					"Name": "F"
				},
				{
					"Code": "E",
					"Name": "E"
				}
			],
			_size = [{
					"Code": "*",
					"Name": "*"
				},
				{
					"Code": "20",
					"Name": "20"
				},
				{
					"Code": "40",
					"Name": "40"
				},
				{
					"Code": "45",
					"Name": "45"
				}
			];

		//define table cont
		var onChangeSZ_FEtblInv = $('#tbl-inv');
		tblInv.DataTable({
			info: false,
			paging: false,
			searching: false,
			// buttons: [{
			// 	text: '<i class="fa fa-upload"></i> Nạp file Excel',
			// 	titleAttr: 'Nạp file Excel',
			// 	action: function() {
			// 		$("input#input-file").trigger("click");
			// 	}
			// }, {
			// 	text: '<i class="fa fa-download"></i> Tải tệp mẫu',
			// 	action: function(e, dt, node, config) {
			// 		location.href = "<?= site_url(md5('Tools') . '/' . md5('downloadManualInvTemp')); ?>";
			// 	}
			// }],
			scrollY: '33vh',
			columnDefs: [{
					targets: _colsPayment.indexOf("rowguid"),
					className: "hiden-input"
				},
				{
					targets: _colsPayment.getIndexs(["STT"]),
					className: "text-center"
				},
				{
					targets: _colsPayment.getIndexs(["TRF_CODE", "INV_UNIT"]),
					className: "input-required"
				},
				{
					targets: _colsPayment.getIndexs(["QTY", 'Shifting', 'GRT']),
					className: "text-right",
					render: function(data, type, full, meta) {
						return $.fn.dataTable.render.number(',', '.', _roundNumQty_Unit).display(data); //lam tron so luong+don gia theo yeu cau KT
					}
				},
				{
					targets: _colsPayment.getIndexs(["UNIT_RATE"]),
					className: "text-right",
					render: function(data, type, full, meta) {
						return $.fn.dataTable.render.number(',', '.', 4).display(data); //lam tron so luong+don gia theo yeu cau KT
					}
				},
				{
					targets: _colsPayment.getIndexs(["standard_rate", "AMOUNT", "VAT_RATE", "VAT", "TAMOUNT"]),
					className: "text-right",
					render: function(data, type, full, meta) {
						return $.fn.dataTable.render.number(',', '.', _roundNums[$('#inv-temp-currency').val()]).display(data); //them moi lam tron so
					}
				},
				{
					className: "input-required",
					render: function(data, type, full, meta) {
						return "<div class='wrap-text width-300'>" + data + "</div>";
					},
					width: 200,
					targets: _colsPayment.getIndexs(["TRF_DESC", "Remark"])
				},
				{
					render: function(data, type, full, meta) {
						var temp = Array.isArray(data) ? data[0] : data;
						var temp1 = _status.filter(p => p.Code?.trim()?.toUpperCase() === temp?.trim()?.toUpperCase());
						return temp1.length > 0 ? temp1[0].Name : '';
					},
					className: "text-center show-dropdown input-required",
					targets: _colsPayment.indexOf('FE')
				},
				{
					render: function(data, type, full, meta) {
						var temp = Array.isArray(data) ? data[0] : data;
						var temp1 = _size.filter(p => p.Code?.trim()?.toUpperCase() === temp?.trim()?.toUpperCase());
						return temp1.length > 0 ? temp1[0].Name : '';
					},
					className: "text-center show-dropdown input-required",
					targets: _colsPayment.indexOf('SZ')
				},
				{
					render: function(data, type, full, meta) {
						var temp = Array.isArray(data) ? data[0] : data;
						var temp1 = _localForeign.filter(p => p.Code?.trim()?.toUpperCase() === temp?.trim()?.toUpperCase());
						return temp1.length > 0 ? temp1[0].Name : '';
					},
					className: "text-center show-dropdown input-required",
					targets: _colsPayment.indexOf('IsLocal')
				},
				{
					render: function(data, type, full, meta) {
						var temp = Array.isArray(data) ? data[0] : data;
						var temp1 = _cntrClass.filter(p => p.CLASS_Code?.trim()?.toUpperCase() === temp?.trim()?.toUpperCase());
						return temp1.length > 0 ? temp1[0].CLASS_Name : '';
					},
					className: "text-center show-dropdown input-required",
					targets: _colsPayment.getIndexs(["IX_CD"])
				},
				{
					render: function(data, type, full, meta) {
						var temp = Array.isArray(data) ? data[0] : data;
						var temp1 = _cargoTypes.filter(p => p.Code?.trim()?.toUpperCase() === temp?.trim()?.toUpperCase());
						return temp1.length > 0 ? temp1[0].Description : '';
					},
					className: "text-center show-dropdown input-required",
					targets: _colsPayment.getIndexs(["CARGO_TYPE"])
				}
			],
			order: [],
			buttons: [],
			keys: true,
			autoFill: {
				focus: 'focus',
				columns: _colsPayment.getIndexs(["TRF_DESC", "Remark", "INV_UNIT", "IX_CD", "CARGO_TYPE", "SZ", "FE", "IsLocal", "QTY", "standard_rate", "UNIT_RATE", "VAT_RATE"])
			},
			select: true,
			rowReorder: false
		});

		///////// SEARCH SHIP
		search_ship();

		$(document).on('click', '#search-ship tbody tr', function() {
			$('.m-row-selected').removeClass('m-row-selected');
			$(this).addClass('m-row-selected');
		});

		$('#search-ship-name').on('keypress', function(e) {
			if (e.which == 13) {
				search_ship();
			}
		});

		$('#search-ship').on('dblclick', 'tbody tr td', function() {
			var r = $(this).parent();
			$('#shipid').val($(r).find('td:eq(2)').text() + "/" + $(r).find('td:eq(3)').text() + "/" + $(r).find('td:eq(4)').text());
			$('#shipid').removeClass('error');

			_selectShipKey = $(r).find('td:eq(6)').text();
			loadData(_selectShipKey);
			$('#ship-modal').modal("toggle");
		});

		$('#select-ship').on('click', function() {
			var r = $('#search-ship tbody').find('tr.m-row-selected').first();
			$('#shipid').val($(r).find('td:eq(2)').text() + "/" + $(r).find('td:eq(3)').text() + "/" + $(r).find('td:eq(4)').text());
			$('#shipid').removeClass('error');

			_selectShipKey = $(r).find('td:eq(6)').text();
			loadData(_selectShipKey);
			$('#ship-modal').modal("toggle");
		});

		$('#reload-ship').on("click", function() {
			$('#search-ship-name').val("");
			search_ship();
		})

		$("#remove-ship-selected").on("click", function() {
			_selectShipKey = "";
			$("#shipid").val("");
		});

		$('#ship-modal, #payer-modal, #payment-modal').on('shown.bs.modal', function(e) {
			$($.fn.dataTable.tables(true)).DataTable()
				.columns
				.adjust();
		});
		/////// END SEARCH SHIP

		tblSrv.on("change", "input[name='select-draft']", function(e) {
			var chk = $(e.target);
			var isChecked = chk.is(":checked");

			// Remove các dịch vụ trên lưới tính cước ở đây
			tblInv.find('tbody tr').each(function(index, element) {
				if (services.map(e => e.CJMode_CD).includes($(element).find('td:eq(' + _colsPayment.indexOf('TRF_CODE') + ')').html())) {
					tblInv.DataTable().row(element).remove();
				}
			})
			if (!$('#taxcode').val()) {
				$('#taxcode').addClass('error');
				toastr["error"]('Vui lòng chọn đối tượng thanh toán!');

				if (chk.is(":checked")) {
					chk.removeAttr("checked");
					chk.val("");
				} else {
					chk.attr("checked", "");
					chk.val(1);
				}
				return;
			}

			if (isChecked) {
				chk.attr("checked", "");
				chk.val("1");
				tblSrv.DataTable().rows(chk.closest("tr")).select();
			} else {
				chk.removeAttr("checked");
				chk.val("0");
				tblSrv.DataTable().rows(chk.closest("tr")).deselect();
			}
			if (!tblSrv.getSelectedRows().length) {
				
			}
			tblInv.waitingLoad();
			var cjModeCdSelected = tblSrv.getSelectedRows().data().toArray().map(p => p[_colServices.indexOf('CJMode_CD')]);
			var serviceSelected = services.filter(item => cjModeCdSelected.includes(item.CJMode_CD)).map(row => {
				row.currency = $('#inv-temp-currency').val();
				row.ShipKey = _selectShipKey;
				return row;
			});

			var formdata = {
				'action': 'view',
				'act': 'load_payment',
				'list': serviceSelected,
				'cusID': $('#taxcode').val()
			}
			$.ajax({
				url: "<?= site_url(md5('Credit') . '/' . md5('creShipService')); ?>",
				dataType: 'json',
				data: formdata,
				type: 'POST',
				success: function(data) {
					if (data.no_payer) {
						$(".toast").remove();
						toastr["error"](data.no_payer);

						tblInv.dataTable().fnClearTable();
						return;
					}
					if (data.error && data.error.length > 0) {
						$(".toast").remove();
						$.each(data.error, function(idx, err) {
							toastr["error"](err);
						});

						tblInv.dataTable().fnClearTable();
						return;
					}
					var rows = [];
					if (data.results && data.results.length > 0) {
						var lst = data.results,
							stt = tblInv.DataTable().rows().count() + 1;
						for (i = 0; i < lst.length; i++) {
							rows.push([
								"", (stt++), lst[i].TariffCode, lst[i].TariffDescription, "", lst[i].Unit, lst[i].IX_CD, lst[i].Cargotype, "", "", lst[i].IsLocal, lst[i].Quantity, "", "", lst[i].StandardTariff, 0, lst[i].Amount, lst[i].VatRate ? parseFloat(lst[i].VatRate) : '', lst[i].VATAmount, lst[i].SubAmount
							]);
						}
					}
					if (rows.length > 0) {
						tblInv.dataTable().fnAddData(rows);
						calcTotal();
					}
				},
				error: function(err) {
					console.log(err);
					toastr['error']("Có lỗi xảy ra! <br/>  Vui lòng liên hệ với bộ phận kỹ thuật! ");
				}
			});
		})
		///////// SEARCH PAYER
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

		//define table info
		var tblInfo = $('#tableInfo');
		var dataTblInfo = tblInfo.newDataTable({
			scrollY: '25vh',
			order: [
				[_colInfo.indexOf('STT'), 'asc']
			],
			paging: false,
			keys: true,
			info: false,
			buttons: [],
			searching: false,
			autoFill: {
				focus: 'focus'
			},
			select: true,
			rowReorder: false
		});

		//------USING MANUAL INVOICE

		$("a.publish-opt").on("click", function(e) {
			if ($(e.target).attr('data-value') == $('a.publish-opt.active').attr('data-value')) {
				return;
			}

			if ($(e.target).attr('data-value') == 'dft') {
				$("#inv-type-container").addClass("hiden-input");
				$("#pay-confirm").prop("disabled", false);
				return;
			}

			$("#inv-type-container").removeClass("hiden-input");

			if ($(e.target).attr('data-value') == "m-inv") {
				$("#m-inv-container").removeClass("hiden-input");
				if ($("#paymentType").val() == "CRE") {
					$("#m-inv-date").removeClass("hiden-input");
				} else {
					$("#m-inv-date").addClass("hiden-input");
				}

				$("#pay-confirm").prop("disabled", _isDup || !_ssInvInfo || !_ssInvInfo[$('#paymentType').val()]);
			} else {
				$("#m-inv-container, #m-inv-date").addClass("hiden-input");
				$("#pay-confirm").prop("disabled", false);
			}
		});

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
			searching: false
		});

		$("#inv-type").on("change", function(e) {
			$('.currency-unit').text($(this).val());
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
		load_payer();
		loadInvTemp();


		tblSrv.DataTable({
			paging: false,
			columnDefs: [{
				className: 'text-center',
				orderDataType: 'dom-text',
				type: 'string',
				targets: _colServices.indexOf("Select")
			}],
			order: [],
			buttons: [],
			info: false,
			searching: false,
			scrollY: '14vh'
		});

		//END INIT TABLES
		var unitRateChanged = false;
		tblInv.on('change', 'td', function(e) {
			var colidx = $(this).index();

			if (colidx == _colsPayment.indexOf("SZ")) {
				onChangeSZ_FE($(e.target))
			}

			if (colidx == _colsPayment.indexOf("FE")) {
				onChangeSZ_FE($(e.target))
			}

			if (colidx == _colsPayment.indexOf("standard_rate")) {
				onChangeStandardRate($(e.target));
			}

			if (colidx == _colsPayment.indexOf("UNIT_RATE")) {
				onChangeUnitRate($(e.target));
			}

			if (colidx == _colsPayment.indexOf("QTY")) {
				onChangeQty($(e.target));
			}

			if (colidx == _colsPayment.indexOf("VAT_RATE")) {
				onChangeVatRate($(e.target));
			}

			if (colidx == _colsPayment.indexOf("AMOUNT") || colidx == _colsPayment.indexOf("TAMOUNT")) {
				calcTotal();
			}
		})

		//bieu cuoc
		$("#inv-temp-currency").on("change", function() {
			tblInv.dataTable().fnClearTable();
			_useTplts = _listTariff = [];
			loadInvTemp();
		});

		$("#inv-temp").on("change", function(e) {
			if (!$('#inv-temp').val()) {
				toastr["error"]("Chọn mẫu cước!");
				return;
			}
			loadTariffSTD($('#inv-temp').val());
		});

		$("#remove").on("click", function() {
			if (tblInv.DataTable().rows().count() == 0) {
				return;
			}

			if (tblInv.DataTable().rows('.selected').count() == 0) {
				toastr["error"]("Chọn ít nhất 1 dòng dữ liệu để xoá!");
				return;
			}

			$.confirm({
				title: 'Thông báo!',
				type: 'orange',
				icon: 'fa fa-warning',
				content: 'Các dòng dữ liệu được chọn sẽ được xóa?',
				buttons: {
					ok: {
						text: 'Chấp nhận',
						btnClass: 'btn-warning',
						keys: ['Enter'],
						action: function() {
							//remove all row to recalculate
							tblInv.DataTable().rows('.selected').remove().draw();
							tblInv.updateSTT(_colsPayment.indexOf("STT"));
						}
					},
					cancel: {
						text: 'Hủy bỏ',
						btnClass: 'btn-default',
						keys: ['ESC']
					}
				}
			});
		});

		function search_ship() {
			var tblSearchShip = $("#search-ship");

			tblSearchShip.dataTable().fnClearTable();
			tblSearchShip.waitingLoad();
			var formdata = {
				'action': 'view',
				'act': 'search_ship',
				'arrStatus': $('input[name="shipArrStatus"]:checked').val(),
				'shipyear': $('#cb-searh-year').val(),
				'shipname': $('#search-ship-name').val()
			};

			$.ajax({
				url: "<?= site_url(md5('Credit') . '/' . md5('creShipService')); ?>",
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

					tblSearchShip.dataTable().fnClearTable();
					if (rows.length > 0) {
						tblSearchShip.dataTable().fnAddData(rows);
					}
				},
				error: function(err) {
					tblSearchShip.dataTable().fnClearTable();
					console.log(err);
					toastr['error']("Có lỗi xảy ra! <br/>  Vui lòng liên hệ với bộ phận kỹ thuật! ");
				}
			});
		};

		tblInv.DataTable().on('autoFill', function(e, datatable, cells) {
			var startRowIndex = cells[0][0].index.row;

			var fillQty = cells[0].filter(p => p.index.column == _colsPayment.indexOf("QTY"));
			var notFirstRow = cells.filter(p => cells.indexOf(p) != 0);
			if (fillQty && fillQty.length > 0) {
				$.each(notFirstRow, function(idx, item) {
					var qtyCell = tblInv.DataTable().cell(item[0].index.row, _colsPayment.indexOf("QTY")).nodes().to$();
					onChangeQty(qtyCell);
				});
			}

			var fillSZ = cells[0].filter(p => p.index.column == _colsPayment.indexOf("SZ"));
			if (fillSZ && fillSZ.length > 0) {
				$.each(notFirstRow, function(idx, item) {
					var szCell = tblInv.DataTable().cell(item[0].index.row, _colsPayment.indexOf("SZ")).nodes().to$();
					onChangeSZ_FE(szCell);
				});
			}

			var fillFE = cells[0].filter(p => p.index.column == _colsPayment.indexOf("FE"));
			if (fillFE && fillFE.length > 0) {
				$.each(notFirstRow, function(idx, item) {
					var szFE = tblInv.DataTable().cell(item[0].index.row, _colsPayment.indexOf("FE")).nodes().to$();
					onChangeSZ_FE(szFE);
				});
			}

			var fillstandardRate = cells[0].filter(p => p.index.column == _colsPayment.indexOf("standard_rate"));
			if (fillstandardRate && fillstandardRate.length > 0) {
				$.each(notFirstRow, function(idx, item) {
					var szstandardRate = tblInv.DataTable().cell(item[0].index.row, _colsPayment.indexOf("standard_rate")).nodes().to$();
					onChangeStandardRate(szstandardRate);
				});
			}

			var fillunitRate = cells[0].filter(p => p.index.column == _colsPayment.indexOf("UNIT_RATE"));
			if (fillunitRate && fillunitRate.length > 0) {
				$.each(notFirstRow, function(idx, item) {
					var stdCell = tblInv.DataTable().cell(item[0].index.row, _colsPayment.indexOf("UNIT_RATE")).nodes().to$();
					onChangeUnitRate(stdCell);
				});
			}

			var fillvatRate = cells[0].filter(p => p.index.column == _colsPayment.indexOf("VAT_RATE"));
			if (fillunitRate && fillunitRate.length > 0) {
				$.each(notFirstRow, function(idx, item) {
					var vatRateCell = tblInv.DataTable().cell(item[0].index.row, _colsPayment.indexOf("VAT_RATE")).nodes().to$();
					onChangeVatRate(vatRateCell);
				});
			}
		});

		function loadData($shipKey) {
			// tblPayer.waitingLoad();
			$('.ibox.collapsible-box').blockUI();
			$.ajax({
				url: "<?= site_url(md5('Credit') . '/' . md5('creShipService')); ?>",
				dataType: 'json',
				data: {
					'action': 'view',
					'act': 'load_data',
					'args': $shipKey
				},
				type: 'POST',
				success: function(data) {
					var rows = [];
					services = [];
					if (Object.keys(data.results.info).length) {
						//dich vu tau
						const dateOneObj = (new Date(data.results.info.fShifting)).getTime();
						const dateTwoObj = (new Date(data.results.info.tShifting)).getTime();
						const milliseconds = Math.abs(dateTwoObj - dateOneObj);
						const hours = milliseconds / 36e5;
						calUnit = {
							GRT: data.results.info.Grt,
							Shifting: hours
						};

						$('#shifing').text(getDateTime(data.results.info.fShifting) + ' ➔ ' + getDateTime(data.results.info.tShifting));
						$('#nation').text(data.results.info.NationName);
						$('#ATB').text(getDateTime(data.results.info.ETA));
						$('#ATD').text(getDateTime(data.results.info.ETB));
						$('#loa').text(data.results.info.Loa);
						$('#grt').text(data.results.info.Grt);
						$('#agency').text(data.results.info.Agency);
						$('#dwt').text(data.results.info.Dwt);
					}
					if (data.results.service.length) {
						services = data.results.service;
						var i = 0;
						$.each(services, function(index, rData) {
							var r = [];
							$.each(_colServices, function(idx, colname) {
								var val = "";
								switch (colname) {
									case "STT":
										val = i + 1;
										break;
									case "Select":
										val = '<label class="checkbox checkbox-outline-ebony">' +
											'<input type="checkbox" name="select-draft" value="0" style="display: none;">' +
											'<span class="input-span"></span>'; +
										'</label>';
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
					tblSrv.dataTable().fnClearTable();
					if (rows.length > 0) {
						tblSrv.dataTable().fnAddData(rows);
					}
					$('.ibox.collapsible-box').unblock();
				},
				error: function(err) {
					$('.ibox.collapsible-box').unblock();
					tblSrv.dataTable().fnClearTable();
					console.log(err);
					toastr["error"]("Có lỗi xảy ra! Vui lòng liên hệ với kỹ thuật viên! <br/>Cảm ơn!");
				}
			});
		}

		function fillPayer() {
			var py = $("#cusID").val() ? payers.filter(p => p.VAT_CD == $('#taxcode').val() && p.CusID == $("#cusID").val()) :
				payers.filter(p => p.VAT_CD == $('#taxcode').val());

			if (py.length > 0) { //fa-check-square
				$('#payer-name, #p-payername').text(py[0].CusName);
				$('#payer-addr, #p-payer-addr').text(py[0].Address);

				$("#p-money-credit").removeClass("hiden-input").find("span").text(py[0].CusType == "M" ? "THU NGAY" : "THU SAU");

				$("#taxcode").removeClass("error");
			}

			return py.length > 0;
		}

		function loadTariffSTD(inv_temp) {

			var formdata = {
				'action': 'view',
				'act': 'load_tariff',
				'invTemp': inv_temp
			};

			_useTplts = _useTplts.concat(inv_temp);

			$(".row.ibox-footer").blockUI();
			$('#inv-temp').parent().blockUI();

			$.ajax({
				url: "<?= site_url(md5('Credit') . '/' . md5('creShipService')); ?>",
				dataType: 'json',
				data: formdata,
				type: 'POST',
				success: function(data) {
					$(".row.ibox-footer").unblock();
					$('#inv-temp').parent().unblock();
					if (data.deny) {
						toastr["error"](data.deny);
						return;
					}

					if (data.no_payer) {
						$(".toast").remove();
						toastr["error"](data.no_payer);

						tblInv.dataTable().fnClearTable();
						return;
					}

					var rows = [];
					if (data.results && data.results.length > 0) {
						data = data.results.map(item => Object.assign(item, calUnit));
						_listTariff = _listTariff.concat(data);
						var lst = data;
						var stt = tblInv.DataTable().rows().count() + 1;
						for (i = 0; i < lst.length; i++) {
							var status = lst[i].Status == "F" ? "Hàng" : "Rỗng";
							var isLocal = lst[i].IsLocal == "F" ? "Ngoại" : (lst[i].IsLocal == "L" ? "Nội" : "");
							rows.push([
								lst[i].rowguid, (stt++), lst[i].TRF_CODE, lst[i].TRF_STD_DESC, '' //them remark
								, lst[i].INV_UNIT, lst[i].IX_CD, lst[i].CARGO_TYPE, '*' //iso size
								, '*' //FULL - EMPTY
								, lst[i].IsLocal, 0 //lst[i].Quantity
								, calUnit.GRT, calUnit.Shifting, 0 //lst[i].StandardTariff
								, 0 //lst[i].Unit_rate
								, 0 //lst[i].Amount
								, lst[i].VAT ? parseFloat(lst[i].VAT) : '', 0 //lst[i].VATAmount
								, 0 //lst[i].SubAmount
							]);
						}
					}
					if (rows.length > 0) {
						tblInv.dataTable().fnAddData(rows);
					}

					extendSelectOnGrid();
				},
				error: function(err) {
					$(".toast").remove();
					toastr["error"]("ERROR!");
					$(".row.ibox-footer").unblock();
					$('#inv-temp').parent().unblock();
					tblInv.dataTable().fnClearTable();

					console.log(err);
				}
			});
		}

		function extendSelectOnGrid() {
			//------SET AUTOCOMPLETE
			var tblHeader = tblInv.parent().prev().find('table');
			tblHeader.find(' th:eq(' + _colsPayment.indexOf('IX_CD') + ') ').setSelectSource(_cntrClass.map(p => p.CLASS_Name));
			tblHeader.find(' th:eq(' + _colsPayment.indexOf('FE') + ') ').setSelectSource(_status.map(p => p.Name));
			tblHeader.find(' th:eq(' + _colsPayment.indexOf('CARGO_TYPE') + ') ').setSelectSource(_cargoTypes.map(p => p.Description));
			tblHeader.find(' th:eq(' + _colsPayment.indexOf('IsLocal') + ') ').setSelectSource(_localForeign.map(p => p.Name));
			tblHeader.find(' th:eq(' + _colsPayment.indexOf('SZ') + ') ').setSelectSource(_size.map(p => p.Name));
			//------SET AUTOCOMPLETE

			//------SET DROPDOWN BUTTON FOR COLUMN
			tblInv.columnDropdownButton({
				data: [{
						colIndex: _colsPayment.indexOf("IX_CD"),
						source: _cntrClass
					}, {
						colIndex: _colsPayment.indexOf("IsLocal"),
						source: _localForeign
					},
					{
						colIndex: _colsPayment.indexOf("FE"),
						source: _status
					},
					{
						colIndex: _colsPayment.indexOf("CARGO_TYPE"),
						source: _cargoTypes
					},
					{
						colIndex: _colsPayment.indexOf("SZ"),
						source: _size
					}
				],
				onSelected: function(cell, itemSelected) {
					tblInv.DataTable().cell(cell).data(itemSelected.attr('code')).draw(false);
					tblInv.DataTable().cell(cell.parent().index(), cell.next()).focus();

					if (cell.index() == _colsPayment.indexOf("SZ")) {
						onChangeSZ_FE(cell);
					}

					if (cell.index() == _colsPayment.indexOf("FE")) {
						onChangeSZ_FE(cell);
					}
				}
			});
			//------SET DROPDOWN BUTTON FOR COLUMN

			tblInv.editableTableWidget();
		}

		function onChangeSZ_FE(cell) {
			var dtTbl = tblInv.DataTable();
			var rowIdx = dtTbl.cell(cell).index().row;
			var rowguid = dtTbl.cell(rowIdx, _colsPayment.indexOf("rowguid")).data();
			var sz = dtTbl.cell(rowIdx, _colsPayment.indexOf("SZ")).data();
			var fe = dtTbl.cell(rowIdx, _colsPayment.indexOf("FE")).data();
			var qty = dtTbl.cell(rowIdx, _colsPayment.indexOf("QTY")).data();
			var grt = dtTbl.cell(rowIdx, _colsPayment.indexOf('GRT')).data();
			var shifting = dtTbl.cell(rowIdx, _colsPayment.indexOf('Shifting')).data();

			var tempTRF = _listTariff.filter(p => p.rowguid == rowguid)[0];

			var colForUnitPrice = sz == "*" ? "AMT_NCNTR" : "AMT_" + fe + sz;
			//giá theo biểu cước
			var tempPrice = tempTRF ? parseFloat(tempTRF[colForUnitPrice]) : undefined;

			if (isNaN(tempPrice) || !fe) {
				dtTbl.cell(rowIdx, _colsPayment.indexOf("standard_rate")).data(0);
				dtTbl.cell(rowIdx, _colsPayment.indexOf("UNIT_RATE")).data(0);
				dtTbl.cell(rowIdx, _colsPayment.indexOf("AMOUNT")).data(0);
				dtTbl.cell(rowIdx, _colsPayment.indexOf("VAT")).data(0);
				dtTbl.cell(rowIdx, _colsPayment.indexOf("TAMOUNT")).data(0);

				//thay đổi đơn giá theo biểu cước -> đánh dấu "thay đổi đơn giá" = false;
				unitRateChanged = false;
				calcTotal();
				return;
			}

			var isIncludeVat = tempTRF['INCLUDE_VAT'] === '1';
			var vatRate = parseFloat(tempTRF['VAT'] || 0) / 100;

			//don gia chua thue
			var unitPrice = isIncludeVat ? (tempPrice / (1 + vatRate)) : tempPrice;

			//đơn giá gồm thuế
			var standard_rate = isIncludeVat ? tempPrice : (tempPrice * (1 + vatRate));

			var roundNum = _roundNums[$('#inv-type').val()];
			//thành tiền
			var amt = parseFloat(qty) * unitPrice;
			grt && shifting ? amt = amt * parseFloat(calUnit.GRT) * parseFloat(calUnit.Shifting) : "";
			amt = parseFloat(amt.toFixed(roundNum));

			//tiền thuê
			var vatAM = amt * vatRate;
			vatAM = parseFloat(vatAM.toFixed(roundNum));

			//tổng tiền
			var tAmount = amt + vatAM;

			dtTbl.cell(rowIdx, _colsPayment.indexOf("standard_rate")).data(standard_rate);
			dtTbl.cell(rowIdx, _colsPayment.indexOf("UNIT_RATE")).data(unitPrice);
			dtTbl.cell(rowIdx, _colsPayment.indexOf("AMOUNT")).data(amt);
			dtTbl.cell(rowIdx, _colsPayment.indexOf("VAT")).data(vatAM);
			dtTbl.cell(rowIdx, _colsPayment.indexOf("TAMOUNT")).data(tAmount);

			//thay đổi đơn giá theo biểu cước -> đánh dấu "thay đổi đơn giá" = false;
			unitRateChanged = false;
			calcTotal();
		}

		function onChangeQty(cell) {
			var dtTbl = tblInv.DataTable();
			var rowIdx = dtTbl.cell(cell).index().row;
			var qty = dtTbl.cell(rowIdx, _colsPayment.indexOf("QTY")).data();
			var vat_rate = dtTbl.cell(rowIdx, _colsPayment.indexOf("VAT_RATE")).data() || 0;
			var standard_rate = dtTbl.cell(rowIdx, _colsPayment.indexOf("standard_rate")).data();
			var roundNum = _roundNums[$('#inv-type').val()];
			var grt = dtTbl.cell(rowIdx, _colsPayment.indexOf('GRT')).data();
			var shifting = dtTbl.cell(rowIdx, _colsPayment.indexOf('Shifting')).data();

			//thành tiền
			var unitRate = parseFloat(standard_rate) / ((parseFloat(vat_rate) / 100) + 1);
			var amt = parseFloat(qty * unitRate);
			grt && shifting ? amt = amt * parseFloat(calUnit.GRT) * parseFloat(calUnit.Shifting) : "";
			var last_amt = parseFloat(amt.toFixed(roundNum));

			if (!unitRateChanged) {
				var rowguid = dtTbl.cell(rowIdx, _colsPayment.indexOf("rowguid")).data();
				var sz = dtTbl.cell(rowIdx, _colsPayment.indexOf("SZ")).data();
				var fe = dtTbl.cell(rowIdx, _colsPayment.indexOf("FE")).data();
				var tempTRF = _listTariff.filter(p => p.rowguid == rowguid)[0];

				var colForUnitPrice = sz == "*" ? "AMT_NCNTR" : "AMT_" + fe + sz;
				//giá theo biểu cước
				var tempPrice = tempTRF ? parseFloat(tempTRF[colForUnitPrice]) : undefined;

				if (isNaN(tempPrice)) {
					dtTbl.cell(rowIdx, _colsPayment.indexOf("AMOUNT")).data(0);
					dtTbl.cell(rowIdx, _colsPayment.indexOf("VAT")).data(0);
					dtTbl.cell(rowIdx, _colsPayment.indexOf("TAMOUNT")).data(0);

					calcTotal();
					return;
				}

				//đơn giá có thuế
				var unitPriceIncludeVat = tempPrice / ((parseFloat(tempTRF['VAT']) / 100) + 1);

				//thành tiền
				amt = parseFloat(qty) * (tempTRF['INCLUDE_VAT'] == "1" ? unitPriceIncludeVat : tempPrice);
				grt && shifting ? amt = amt * parseFloat(calUnit.GRT) * parseFloat(calUnit.Shifting) : "";
				last_amt = parseFloat(amt.toFixed(roundNum));
			}

			//tiền thuế
			var vat_amt = (parseFloat(vat_rate) / 100) * amt;
			var last_vat_amt = parseFloat(vat_amt.toFixed(roundNum));

			//tổng tiền
			var tamount = last_amt + last_vat_amt;

			dtTbl.cell(rowIdx, _colsPayment.indexOf("AMOUNT")).data(last_amt);
			dtTbl.cell(rowIdx, _colsPayment.indexOf("VAT")).data(last_vat_amt);
			dtTbl.cell(rowIdx, _colsPayment.indexOf("TAMOUNT")).data(tamount);

			calcTotal();
		}

		function onChangeVatRate(cell) {
			var dtTbl = tblInv.DataTable();
			var rowIdx = dtTbl.cell(cell).index().row;
			var unitRate = dtTbl.cell(rowIdx, _colsPayment.indexOf("UNIT_RATE")).data();
			var qty = dtTbl.cell(rowIdx, _colsPayment.indexOf("QTY")).data();
			var amt = dtTbl.cell(rowIdx, _colsPayment.indexOf("AMOUNT")).data();
			var vat_rate = dtTbl.cell(rowIdx, _colsPayment.indexOf("VAT_RATE")).data() || 0;
			var roundNum = _roundNums[$('#inv-type').val()];

			//tiền thuế
			var vat_amt = (parseFloat(vat_rate) / 100) * parseFloat(amt);
			var last_vat_amt = parseFloat(vat_amt.toFixed(roundNum));

			//tổng tiền
			var tamount = parseFloat(amt) + last_vat_amt;

			dtTbl.cell(rowIdx, _colsPayment.indexOf("VAT")).data(last_vat_amt);
			dtTbl.cell(rowIdx, _colsPayment.indexOf("TAMOUNT")).data(tamount);

			calcTotal();
		}

		function onChangeUnitRate(cell) {
			var dtTbl = tblInv.DataTable();
			var rowIdx = dtTbl.cell(cell).index().row;
			var unitRate = dtTbl.cell(rowIdx, _colsPayment.indexOf("UNIT_RATE")).data();
			var qty = dtTbl.cell(rowIdx, _colsPayment.indexOf("QTY")).data();
			var vat_rate = dtTbl.cell(rowIdx, _colsPayment.indexOf("VAT_RATE")).data() || 0;
			var roundNum = _roundNums[$('#inv-type').val()];
			var grt = dtTbl.cell(rowIdx, _colsPayment.indexOf('GRT')).data();
			var shifting = dtTbl.cell(rowIdx, _colsPayment.indexOf('Shifting')).data();

			//thành tiền gồm thuế
			var amtIncludeVat = parseFloat(unitRate) * ((parseFloat(vat_rate) / 100) + 1);
			var last_amtIncludeVat = amtIncludeVat;

			//thành tiền
			var amt = parseFloat(qty) * parseFloat(unitRate);
			grt && shifting ? amt = amt * parseFloat(calUnit.GRT) * parseFloat(calUnit.Shifting) : "";
			var last_amt = parseFloat(amt.toFixed(roundNum));

			//tiền thuế
			var vat_amt = (parseFloat(vat_rate) / 100) * amt;
			var last_vat_amt = parseFloat(vat_amt.toFixed(roundNum));

			//tổng tiền
			var tamount = last_amt + last_vat_amt;

			dtTbl.cell(rowIdx, _colsPayment.indexOf("standard_rate")).data(last_amtIncludeVat);
			dtTbl.cell(rowIdx, _colsPayment.indexOf("AMOUNT")).data(last_amt);
			dtTbl.cell(rowIdx, _colsPayment.indexOf("VAT")).data(last_vat_amt);
			dtTbl.cell(rowIdx, _colsPayment.indexOf("TAMOUNT")).data(tamount);

			//user thay đổi đơn giá trên lưới -> đanh dấu "Thany đổi đơn gía" = true
			unitRateChanged = true;
			calcTotal();
		}

		function onChangeStandardRate(cell) {
			var dtTbl = tblInv.DataTable();
			var rowIdx = dtTbl.cell(cell).index().row;
			var standard_rate = dtTbl.cell(rowIdx, _colsPayment.indexOf("standard_rate")).data();
			var qty = dtTbl.cell(rowIdx, _colsPayment.indexOf("QTY")).data();
			var vat_rate = dtTbl.cell(rowIdx, _colsPayment.indexOf("VAT_RATE")).data() || 0;
			var roundNum = _roundNums[$('#inv-type').val()];
			var grt = dtTbl.cell(rowIdx, _colsPayment.indexOf('GRT')).data();
			var shifting = dtTbl.cell(rowIdx, _colsPayment.indexOf('Shifting')).data();

			//thành tiền chưa thuế
			var amtExcludeVat = parseFloat(standard_rate) / ((parseFloat(vat_rate) / 100) + 1);
			var last_amtExcludeVat = amtExcludeVat;

			//thành tiền
			var amt = parseFloat(qty) * (parseFloat(standard_rate) / ((parseFloat(vat_rate) / 100) + 1));
			grt && shifting ? amt = amt * parseFloat(calUnit.GRT) * parseFloat(calUnit.Shifting) : "";
			var last_amt = parseFloat(amt.toFixed(roundNum));

			//tiền thuế
			var vat_amt = (parseFloat(vat_rate) / 100) * amt;
			var last_vat_amt = parseFloat(vat_amt.toFixed(roundNum));

			//tổng tiền
			var tamount = last_amt + last_vat_amt;

			dtTbl.cell(rowIdx, _colsPayment.indexOf("UNIT_RATE")).data(last_amtExcludeVat);
			dtTbl.cell(rowIdx, _colsPayment.indexOf("AMOUNT")).data(last_amt);
			dtTbl.cell(rowIdx, _colsPayment.indexOf("VAT")).data(last_vat_amt);
			dtTbl.cell(rowIdx, _colsPayment.indexOf("TAMOUNT")).data(tamount);

			//user thay đổi đơn giá trên lưới -> đanh dấu "Thany đổi đơn gía" = true
			unitRateChanged = true;
			calcTotal();
		}

		function calcTotal() {

			var formatNum = '#,###'; //them moi lam tron so
			var roundNum = _roundNums[$('#inv-type').val()];
			if (roundNum > 0) {
				formatNum += "." + ("0000000000".slice(-roundNum));
			}

			var exchange_rate = parseFloat($('#ExchangeRate').val().replace(',', ''));
			//ko quy doi khi 2 loai hoa don giong nhau
			if ($('#inv-temp-currency').val() == $('#inv-type').val()) {
				exchange_rate = 1;
			}


			var amount = tblInv.DataTable()
				.column(_colsPayment.indexOf("AMOUNT"), {
					page: 'current'
				})
				.data()
				.reduce(function(a, b) {
					return parseFloat(a) + parseFloat(b);
				}, 0);
			var totalVAT = tblInv.DataTable()
				.column(_colsPayment.indexOf("VAT"), {
					page: 'current'
				})
				.data()
				.reduce(function(a, b) {
					return parseFloat(a || 0) + parseFloat(b || 0);
				}, 0);

			amount = parseFloat((amount * exchange_rate).toFixed(roundNum));
			totalVAT = parseFloat((totalVAT * exchange_rate).toFixed(roundNum));
			totalAmount = amount + totalVAT;

			$('#AMOUNT').text(amount == 0 ? '0' : $.formatNumber(amount, {
				format: formatNum,
				locale: "us"
			}));
			$('#VAT').text(totalVAT == 0 ? '0' : $.formatNumber(totalVAT, {
				format: formatNum,
				locale: "us"
			}));
			$('#TAMOUNT').text(totalAmount == 0 ? '0' : $.formatNumber(totalAmount, {
				format: formatNum,
				locale: "us"
			}));

			return {
				AMOUNT: amount,
				VAT: totalVAT,
				TAMOUNT: totalAmount,
				DIS_AMT: 0
			};
		}

		function loadInvTemp() {
			var currency = $("#inv-temp-currency").val();
			var html = '<option value="" selected="">-- Chọn Mẫu cước --</option>';
			$.each(invTemps.filter(p => p.CURRENCYID == currency), function(i, v) {
				html += '<option data="' + currency + '" value="' + v.TPLT_NM + '">' + v.TPLT_NM + ' : ' + v.TPLT_DESC + '</option>';
			});

			$("#inv-temp").html(html).selectpicker("refresh");
			$('#inv-type').val(currency).selectpicker('refresh').trigger('change');
			$('#AMOUNT, #VAT, #TAMOUNT').text('0');
		}

		function load_payer() {
			var tblPayer = $('#search-payer');
			tblPayer.waitingLoad();

			$.ajax({
				url: "<?= site_url(md5('Credit') . '/' . md5('creShipService')); ?>",
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

		// define popover tooltip
		var tips = '<b>LOA</b> : Chiều dài toàn bộ</br>' +
			'<b>GRT</b> : Trọng tải đăng kiểm</br>' +
			'<b>DWT</b> : Trọng tải toàn phần</br>' +
			'<b>ATA</b> : Ngày tàu đến</br>' +
			'<b>ATB</b> : Ngày tàu cập</br>' +
			'<b>ATWD</b> : Ngày bắt đầu làm hàng nhập</br>' +
			'<b>ATCD</b> : Ngày kết thúc làm hàng nhập</br>' +
			'<b>ATWL</b> : Ngày bắt đầu làm hàng xuất</br>' +
			'<b>ATC</b> : Ngày kết thúc làm hàng xuất</br>' +
			'<b>ATD</b> : Ngày tàu rời';
		$('#explain').popover({
			container: '#explain',
			content: tips,
			html: true
		});

	});
</script>
<script src="<?= base_url('assets/js/jshashtable-2.1.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/bootstrap-select/dist/js/bootstrap-select.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/moment/min/moment.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/jquery.numberformatter-1.2.3.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'); ?>"></script>