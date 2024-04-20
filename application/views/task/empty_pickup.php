<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<link href="<?= base_url('assets/vendors/bootstrap-select/dist/css/bootstrap-select.min.css'); ?>" rel="stylesheet" />
<link href="<?= base_url('assets/vendors/dataTables/extensions/select.dataTables.min.css'); ?>" rel="stylesheet" />
<style>
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

	.un-pointer {
		pointer-events: none;
	}

	.form-group {
		margin-bottom: .5rem !important;
	}

	.grid-hidden {
		display: none;
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

	.add-payer {
		flex: 1;
		/* shorthand for: flex-grow: 1, flex-shrink: 1, flex-basis: 0 */
		display: flex;
		justify-content: flex-start;
		align-items: center;
	}

	.add-payer-container {
		transform: scaleX(0);
		position: absolute;
		width: 100%;
		height: 100%;
		top: 0;
		left: 0;
		background: -webkit-linear-gradient(to right, #2c3e50, #3498db);
		/* Chrome 10-25, Safari 5.1-6 */
		background: linear-gradient(to right, #2c3e50, #3498db);
		color: white;
		background: #8e9eab;
		/* fallback for old browsers */
		background: -webkit-linear-gradient(to right, #8e9eab, #eef2f3);
		/* Chrome 10-25, Safari 5.1-6 */
		background: linear-gradient(to right, #8e9eab, #eef2f3);
		-webkit-transition: transform 1s linear;
		/* For Safari 3.1 to 6.0 */
		transition: transform 1s linear;
		transform-origin: left center;
		z-index: 1;
		padding: 7px 0 7px 20px;
	}

	.payer-show {
		transform: scaleX(1);
	}

	#payer-modal .dataTables_filter {
		padding-left: 10px !important;
	}

	table.dataTable tr.selected td.select-checkbox::after {
		color: black !important;
		margin-top: -28px !important;
	}

	#FCL-attach-modal {
		z-index: 1045 !important;
	}
</style>
<div class="row" style="font-size: 12px!important;">
	<div class="col-xl-12">
		<div class="ibox collapsible-box" id="parent-loading">
			<i class="la la-angle-double-up dock-right"></i>
			<div class="ibox-head">
				<div class="ibox-title">LỆNH CẤP CONTAINER RỖNG</div>
			</div>
			<div class="ibox-body pt-3 pb-3 bg-f9 border-e">
				<div class="row">
					<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<h5 class="text-primary">Thông tin lệnh</h5>
					</div>
				</div>
				<div class="row my-box pb-1">
					<div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-xs-12 mt-3">
						<div class="row" id="row-transfer-left">
							<div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-xs-12">
								<div class="row form-group">
									<label class="col-sm-4 col-form-label">Ngày lệnh</label>
									<div class="col-sm-8 input-group input-group-sm">
										<div class="input-group">
											<input class="form-control form-control-sm" id="ref-date" type="text" placeholder="Ngày lệnh">
										</div>
									</div>
								</div>
								<div class="row form-group">
									<label class="col-sm-4 col-form-label">Hạn lệnh *</label>
									<div class="col-sm-8 input-group input-group-sm">
										<div class="input-group">
											<input class="form-control form-control-sm input-required" id="ref-exp-date" type="text" placeholder="Hạn lệnh">
											<span class="input-group-addon bg-white btn text-danger" title="Bỏ chọn ngày" style="padding: 0 .5rem"><i class="fa fa-times"></i></span>
										</div>
									</div>
								</div>
								<div class="row form-group">
									<div class="col-sm-4 col-form-label">
										<label class="checkbox checkbox-blue">
											<input type="checkbox" name="chkSalan" id="chkSalan">
											<span class="input-span"></span>
											Sà lan
										</label>
									</div>
									<div class="col-sm-8 input-group input-group-sm">
										<div id="barge-ischecked" class="input-group un-pointer">
											<input class="form-control form-control-sm" id="barge-info" type="text" placeholder="Mã/Năm/Chuyến" readonly>
											<span class="input-group-addon bg-white btn text-warning" id="btn-search-barge" data-toggle="modal" data-target="#barge-modal" title="Chọn" style="padding: 0 .5rem"><i class="fa fa-search"></i></span>
										</div>
									</div>
								</div>

								<div id="MT-return-content" class="row form-group">
									<div class="col-sm-4 col-form-label pr-0">
										<label class="checkbox checkbox-warning text-warning">
											<input type="checkbox" name="chkFCL-attach" id="chkFCL-attach" value="0">
											<span class="input-span"></span>
											Lệnh hạ hàng
										</label>
									</div>
								</div>

								<div class="row form-group hiden-input">
									<label class="col-sm-4 col-form-label">Tàu/chuyến</label>
									<div class="col-sm-8 input-group">
										<input class="form-control form-control-sm" id="shipid" placeholder="Tàu/chuyến" type="text" readonly>
										<span class="input-group-addon bg-white btn mobile-hiden text-warning open-ship-modal" style="padding: 0 .5rem" title="chọn tàu" data-toggle="modal" data-id="shipid">
											<i class="ti-search"></i>
										</span>
									</div>
								</div>
							</div>
							<div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-xs-12">
								<div class="row form-group">
									<label class="col-sm-4 col-form-label">D/O</label>
									<div class="col-sm-8 input-group input-group-sm">
										<input class="form-control form-control-sm" id="do" type="text" placeholder="D/O">
									</div>
								</div>
								<div class="row form-group">
									<label class="col-sm-4 col-form-label pr-0">Số booking *</label>
									<div class="col-sm-8 input-group input-group-sm">
										<div class="input-group">
											<input class="form-control form-control-sm input-required" id="bookingno" type="text" placeholder="Số booking">
											<span class="input-group-addon bg-white btn text-warning hiden-input" title="Tìm booking" style="padding: 0 .5rem"><i class="fa fa-search"></i></span>
										</div>
									</div>
								</div>
								<div class="row form-group show-non-cont hiden-input">
									<label class="col-sm-4 col-form-label">OPR/SzType</label>
									<div class="col-sm-8">
										<div class="input-group">
											<input class="form-control form-control-sm" id="opr" type="text" placeholder="OPR" readonly>
											<select id="sizetype" class="selectpicker pl-1" data-style="btn-default btn-sm" data-width="50%">
												<option value="" selected>--</option>
											</select>
										</div>
									</div>
								</div>
								<div class="row form-group hide-non-cont">
									<label class="col-sm-4 col-form-label">Số container</label>
									<div class="col-sm-8 input-group input-group-sm">
										<div class="input-group">
											<input class="form-control form-control-sm" id="cntrno" type="text" placeholder="Container No.">
											<span class="input-group-addon bg-white btn text-warning" data-toggle="modal" id="cntrno-search" data-target="" title="Chọn" style="padding: 0 .5rem"><i class="fa fa-search"></i></span>
										</div>
									</div>
								</div>
								<div class="row form-group hiden-input show-non-cont">
									<label class="col-sm-4 col-form-label">Số lượng cont</label>
									<div class="col-sm-8 input-group input-group-sm">
										<div class="input-group">
											<input class="form-control form-control-sm" id="noncont" type="number" placeholder="Số lượng" value="0" min="0" max="999">
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-xs-12 mt-3">
						<div class="row">
							<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<div id="attach-srv-chk-container" class="row form-group hiden-input" style="border-bottom: 1px solid #eee">
									<div class="col-12 col-form-label">
										<label class="checkbox checkbox-blue">
											<input type="checkbox" name="chkServiceAttach" id="chkServiceAttach">
											<span class="input-span"></span>
											Đính kèm dịch vụ
										</label>
									</div>
								</div>
							</div>
							<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12" id="col-attach-service" style="display: none;">
								<table id="tb-attach-srv" class="table table-striped display nowrap single-row-select" cellspacing="0" style="width: 99.8%">
									<thead>
										<tr>
											<th class="editor-cancel data-type-checkbox" style="max-width: 30px">Chọn</th>
											<th col-name="CJMode_CD">Mã phương án</th>
											<th col-name="CJModeName">Tên phương án</th>
											<th col-name="Cont_Count">Số lượng Cont</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
							</div>
						</div>

						<div class="row" id="row-transfer-right">
							<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12 mt-1" id="col-transfer">
								<div class="row form-group">
									<label class="col-sm-2 col-form-label" title="Chủ hàng">Chủ hàng *</label>
									<div class="col-sm-10">
										<input class="form-control form-control-sm input-required" id="shipper-name" type="text" placeholder="Chủ hàng">
									</div>
								</div>
								<div class="row form-group">
									<label class="col-sm-2 col-form-label">Người đại diện</label>

									<div class="col-sm-10 input-group">
										<input class="form-control form-control-sm mr-2" id="cmnd" user-input=0 type="text" placeholder="Số CMND /Số ĐT" maxlength="20">
										<input class="form-control form-control-sm mr-2" id="personal-name" user-input=0 type="text" style="width: 100px" placeholder="Tên người đại diện" maxlength="50">
										<input class="form-control form-control-sm" id="mail" user-input=0 type="text" placeholder="Địa chỉ Email" style="width: 140px" maxlength="100">
									</div>
								</div>
								<div class="row form-group">
									<label class="col-sm-2 col-form-label">Ghi chú</label>
									<div class="col-sm-10 input-group input-group-sm">
										<input class="form-control form-control-sm" id="remark" type="text" placeholder="Ghi chú">
									</div>
								</div>
								<div class="row form-group">
									<label class="col-sm-2 col-form-label">Chuyển cảng</label>
									<div class="col-sm-6 input-group input-group-sm">
										<select id="transist" class="selectpicker" data-style="btn-default btn-sm" data-width="100%">
											<option value="" selected>--</option>
											<?php if (isset($transists) && count($transists) > 0) {
												foreach ($transists as $item) { ?>
													<option value="<?= $item['Transit_CD'] ?>"><?= $item['Transit_CD'] . ' : ' . $item['Transit_Name'] ?></option>
											<?php }
											} ?>
										</select>
									</div>
								</div>
							</div>

						</div>
					</div>
				</div>
				<div class="row mt-2 pt-2 my-box">
					<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="row">
							<div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-xs-3">
								<div class="row form-group">
									<label class="col-sm-4 col-form-label" title="Đối tượng thanh toán">ĐTTT *</label>
									<div class="col-sm-8 ">
										<div class="input-group">
											<input class="form-control form-control-sm input-required" id="taxcode" placeholder="ĐTTT" type="text">
											<span class="input-group-addon bg-white btn mobile-hiden text-warning" style="padding: 0 .5rem" title="Chọn đối tượng thanh toán" data-toggle="modal" data-target="#payer-modal">
												<i class="ti-search"></i>
											</span>
										</div>
									</div>
									<input class="hiden-input" id="cusID" readonly>
								</div>
							</div>
							<div class="col-xl-9 col-lg-9 col-md-9 col-sm-9 col-xs-9 col-form-label mt-1">
								<i class="fa fa-id-card" style="font-size: 15px!important;"></i>-<span id="payer-name"> [Tên đối tượng thanh toán]</span>&emsp;
							</div>
						</div>
						<div class="row">
							<div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-xs-3">
								<div class="row form-group">
									<label class="col-sm-4 col-form-label" title="Hình thức thanh toán">HTTT *</label>
									<div class="col-sm-8">
										<select id="payment-type" class="selectpicker" data-style="btn-default btn-sm" data-width="100%">
											<option value="M">THU NGAY</option>
											<option value="C">THU SAU</option>
										</select>
									</div>
								</div>
							</div>
							<div class="col-xl-9 col-lg-9 col-md-9 col-sm-9 col-xs-9 col-form-label mt-1">
								<i class="fa fa-home" style="font-size: 15px!important;"></i>-<span id="payer-addr"> [Địa chỉ]</span>&emsp;
							</div>
						</div>
					</div>
				</div>
				<div class="row mt-2 pt-2 my-box">
					<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">
						<div class="row form-group ml-auto">
							<button id="remove" class="btn btn-outline-danger btn-sm mr-1" title="Xóa những dòng đang chọn">
								<span class="btn-icon"><i class="fa fa-trash"></i>Xóa dòng</span>
							</button>
							<a class="col-form-label text-primary btn btn-outline-primary btn-sm" href="<?= site_url(md5('Task') . '/' . md5('tskBooking')); ?>" style="padding-left: 10px;" target="_blank">
								<span class="btn-icon"><i class="fa fa-plus-circle"></i>Tạo Booking</span>
							</a>
						</div>
					</div>
					<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
						<div class="row form-group" style="display: inline-block; margin: 0 auto">
							<label class="radio radio-outline-success pr-4">
								<input name="view-opt" type="radio" id="chk-view-cont" value="cont" checked>
								<span class="input-span"></span>
								Danh sách container
							</label>
							<label class="radio radio-outline-success pr-4">
								<input name="view-opt" id="chk-view-inv" value="inv" type="radio">
								<span class="input-span"></span>
								Tính cước
							</label>
							<button id="show-payment-modal" class="btn btn-warning btn-sm" title="Thông tin thanh toán" data-toggle="modal">
								<i class="fa fa-print"></i>
								Thanh toán
							</button>
						</div>
					</div>
				</div>
			</div>
			<div class="row grid-toggle" style="padding: 10px 12px; margin-top: -4px">
				<div class="col-md-12 col-sm-12 col-xs-12 table-responsive">
					<table id="tbl-cont" class="table table-striped display nowrap" cellspacing="0" style="min-width: 99.4%">
						<thead>
							<tr>
								<th>STT</th>
								<th>Số cont</th>
								<th>Số booking</th>
								<th>Hướng</th>
								<th>Hãng khai thác</th>
								<th>Kích cỡ nội bộ</th>
								<th>Kích cỡ ISO</th>
								<th>Hàng/Rỗng</th>
								<th>Số chì</th>
								<th>Nội/ngoại</th>
								<th>Trọng lượng</th>
								<th>Loại hàng</th>
								<th>Ghi chú</th>
								<th>TLHQ</th>
							</tr>
						</thead>

						<tbody>
						</tbody>
					</table>
				</div>
				<div class="col-md-12 col-sm-12 col-xs-12 table-responsive grid-hidden">
					<table id="tbl-inv" class="table table-striped display nowrap" cellspacing="0">
						<thead>
							<tr>
								<th>STT</th>
								<th>Số phiếu tính cước</th>
								<th>Số lệnh</th>
								<th>Mã biểu cước</th>
								<th>Tên biểu cước</th>
								<th>ĐVT</th>
								<th>Loại công việc</th>
								<th>PTGN</th>
								<th>Loại hàng</th>
								<th>Kích cỡ ISO</th>
								<th>Hàng/rỗng</th>
								<th>Nội/ngoại</th>
								<th>Số lượng</th>
								<th>Đơn giá</th>
								<th>Chiết khấu (%)</th>
								<th>Đơn giá CK</th>
								<th>Đơn giá sau CK</th>
								<th>Thành tiền</th>
								<th>Thuế (%)</th>
								<th>Tiền thuế</th>
								<th>Tổng tiền</th>
								<th>Loại tiền</th>
								<th>IX_CD</th>
								<th>CNTR_JOB_TYPE</th>
								<th>VAT_CHK</th>
								<th>Remark</th>
								<th>TRF_DESC_MORE</th>
							</tr>
						</thead>

						<tbody>
						</tbody>
					</table>
				</div>
			</div>
			<div class="row ibox-footer">

			</div>
		</div>
	</div>
</div>
<!--select barge-->
<div class="modal fade" id="barge-modal" tabindex="-1" role="dialog" aria-labelledby="groups-modalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-mw" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="groups-modalLabel">Chọn sà lan</h5>
			</div>
			<div class="modal-body pt-0">
				<div class="table-responsive">
					<table id="search-barge" class="table table-striped display nowrap table-popup single-row-select" cellspacing="0" style="width: 99.8%">
						<thead>
							<tr>
								<th style="max-width: 15px">STT</th>
								<th>Mã xà lan</th>
								<th>Tên xà lan</th>
								<th>Năm</th>
								<th>Chuyến</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="select-barge" class="btn btn-success" data-dismiss="modal">Chọn</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
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
								<th>ShipYear</th>
								<th>ShipVoy</th>
								<th>YARD_CLOSE</th>
								<th>LaneID</th>
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
				<button type="button" id="select-ship" class="btn btn-sm btn-outline-primary">
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

<!--payment modal-->
<div class="modal fade" id="payment-modal" tabindex="-1" role="dialog" aria-labelledby="groups-modalLabel" aria-hidden="true" data-whatever="id" data-backdrop="static">
	<div class="modal-dialog modal-dialog-mw-py" role="document">
		<div class="modal-content p-3">
			<button type="button" class="close text-right" data-dismiss="modal">&times;</button>
			<div class="modal-body px-5">
				<div class="row">
					<div class="col-xl-8 col-lg-8 col-md-8 col-sm-8 col-xs-8">
						<div class="form-group pb-1">
							<h5 class="text-primary" style="border-bottom: 1px solid #eee">Thông tin thanh toán</h5>
						</div>
						<div class="row form-group">
							<label class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-form-label" title="Mã KH/ MST">Mã KH/ MST</label>
							<span class="col-form-label" id="p-taxcode"></span>
						</div>
						<div class="row form-group">
							<label class="col-sm-3 col-form-label">Tên</label>
							<span class="col-form-label" id="p-payername"></span>
						</div>
						<div class="row form-group">
							<label class="col-sm-3 col-form-label">Địa chỉ</label>
							<span class="col-form-label" id="p-payer-addr"></span>
						</div>

						<div class="row form-group">
							<label class="col-sm-3 col-form-label">Thanh toán</label>
							<div class="col-sm-9 pl-0">
								<!-- add payment method -->
								<select id="paymentMethod" class="selectpicker" data-style="btn-default btn-sm" title="Chọn phương thức">
									<?php if (isset($paymentMethod) && count($paymentMethod) > 0) {
										foreach ($paymentMethod as $item) { ?>
											<option value="<?= $item['ACC_CD'] ?>"><?= $item['ACC_NO']; ?></option>
									<?php }
									} ?>
								</select>
							</div>
						</div>

						<div class="row form-group mt-3" id="publish-type">
							<div class="col-9 ml-sm-auto">
								<div class="row input-group">
									<label class="col-form-label radio radio-outline-blue text-blue mr-4 mx-auto">
										<input name="publish-opt" type="radio" value="dft">
										<span class="input-span" style="margin-top: calc(.5rem - 1px * 2);"></span> PHIẾU TẠM THU
									</label>
									<!-- <label class="col-form-label radio radio-outline-danger text-danger mr-4 mx-auto">
										<input name="publish-opt" value="m-inv" type="radio">
										<span class="input-span" style="margin-top: calc(.5rem - 1px * 2);"></span>
										HÓA ĐƠN GIẤY
									</label> -->
									<label class="col-form-label radio radio-outline-warning text-warning mx-auto">
										<input name="publish-opt" value="e-inv" type="radio" checked>
										<span class="input-span" style="margin-top: calc(.5rem - 1px * 2);"></span>
										HÓA ĐƠN ĐIỆN TỬ
									</label>
								</div>
							</div>
						</div>

						<div id="m-inv-container" class="row form-group hiden-input">
							<label class="col-sm-3 col-form-label">Số HĐ kế tiếp</label>
							<div class="col-form-label text-danger font-bold">
								<?php if (isset($ssInvInfo) && count($ssInvInfo) > 0) { ?>
									<span id="ss-invNo">
										<?= $ssInvInfo['serial'] . $ssInvInfo['invno'] ?>
										<?php if ($isDup) { ?>
											&ensp;
											[BỊ TRÙNG]
										<?php } ?>
									</span>
									&ensp;
									<button id="change-ssinvno" class="btn btn-outline-secondary btn-sm mr-1" data-toggle="modal" data-target="#change-ssinv-modal" title="Thay đổi hóa đơn sử dụng tiếp theo">
										<span class="btn-icon"><i class="fa fa-pencil"></i>Thay đổi</span>
									</button>
								<?php } else { ?>
									<span id="ss-invNo">
										Chưa khai báo hóa đơn tiếp theo!
									</span>
									&ensp;
									<button id="change-ssinvno" class="btn btn-outline-primary btn-sm mr-1" data-toggle="modal" data-target="#change-ssinv-modal" title="Khai báo số hóa đơn sử dụng tiếp theo">
										<span class="btn-icon"><i class="fa fa-pencil"></i>Khai báo</span>
									</button>
								<?php } ?>
							</div>
						</div>

					</div>

					<div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-xs-4" id="INV_DRAFT_TOTAL">
						<div class="form-group pb-1">
							<h5 class="text-primary" style="border-bottom: 1px solid #eee">Tổng tiền thanh toán</h5>
						</div>
						<div class="row form-group">
							<label class="col-sm-4 col-form-label">Thành tiền</label>
							<span class="col-form-label text-right font-bold text-blue" id="AMOUNT"></span>
						</div>
						<div class="row form-group hiden-input">
							<label class="col-sm-4 col-form-label">Giảm trừ</label>
							<span class="col-form-label text-right font-bold text-blue" id="DIS_AMT"></span>
						</div>
						<div class="row form-group">
							<label class="col-sm-4 col-form-label">Tiền thuế</label>
							<span class="col-form-label text-right font-bold text-blue" id="VAT"></span>
						</div>
						<div class="row form-group">
							<label class="col-sm-4 col-form-label">Tổng tiền</label>
							<span class="col-form-label text-right font-bold text-danger" id="TAMOUNT"></span>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<div id="dv-cash" style="margin: 0 auto">
					<button class="btn btn-rounded btn-gradient-purple" id="pay-atm">
						<span class="btn-icon"><i class="fa fa-id-card"></i> Xác nhận thanh toán</span>
					</button>
					<button class="btn btn-rounded btn-rounded btn-gradient-lime hiden-input">
						<span class="btn-icon"><i class="fa fa-id-card"></i> Thanh toán bằng thẻ MASTER, VISA</span>
					</button>
				</div>
				<div id="dv-credit" class="hiden-input" style="margin: 0 auto">
					<button id="save-credit" class="btn btn-rounded btn-rounded btn-gradient-lime btn-fix">
						<span class="btn-icon"><i class="fa fa-save"></i> Lưu dữ liệu </span>
					</button>
				</div>
			</div>
		</div>
	</div>
</div>

<!--booking modal-->
<div class="modal fade" id="booking-modal" tabindex="-1" role="dialog" aria-labelledby="groups-modalLabel" aria-hidden="true" data-whatever="id" style="padding-left: 14%">
	<div class="modal-dialog" role="document" style="min-width: 750px!important">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title text-primary" id="groups-modalLabel">Chi tiết booking</h5>
			</div>
			<div class="modal-body">
				<div class="table-responsive">
					<table id="booking-detail" class="table table-striped display nowrap table-popup" cellspacing="0" style="width: 99.5%">
						<thead>
							<tr>
								<th style="max-width: 10px!important;">Chọn</th>
								<th>Số container</th>
								<th>Hãng tàu</th>
								<th>Kích cỡ</th>
								<th>Vị trí bãi</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<div style="margin: 0 auto!important;">
					<button class="btn btn-gradient-blue btn-labeled btn-labeled-left btn-icon" id="apply-booking" data-dismiss="modal">
						<span class="btn-label"><i class="ti-check"></i></span>Chuyển tính tiền</button>
					<button class="btn btn-gradient-peach btn-labeled btn-labeled-left btn-icon" data-dismiss="modal">
						<span class="btn-label"><i class="ti-close"></i></span>Đóng</button>
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
					<table id="search-payer" class="table table-striped display nowrap table-popup single-row-select" cellspacing="0" style="width: 99.9%">
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
				<div class="add-payer-container">
					<div class="row">
						<div class="col-sm-11 col-xs-11">
							<div class="row">
								<div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-xs-4">
									<div class="row form-group">
										<label class="col-sm-3 col-form-label" title="Mã số thuế">MST</label>
										<div class="col-sm-9">
											<input class="form-control form-control-sm" id="add-payer-taxcode" type="text" placeholder="Mã số thuế">
										</div>
									</div>
								</div>

								<div class="col-xl-8 col-lg-8 col-md-8 col-sm-8 col-xs-8">
									<div class="row form-group">
										<label class="col-sm-2 col-form-label" title="Tên đối tượng thanh toán">Tên</label>
										<div class="col-sm-10">
											<input class="form-control form-control-sm" id="add-payer-name" type="text" placeholder="Tên">
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-sm-12 col-xs-12">
									<div class="row form-group">
										<label class="col-sm-1 col-form-label" title="Địa chỉ">Địa chỉ</label>
										<div class="col-sm-11">
											<input class="form-control form-control-sm" id="add-payer-address" type="text" placeholder="Địa chỉ">
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-1 col-xs-1" style="margin: auto 0;">
							<div class="row">
								<div class="col-sm-12 col-xs-12">
									<div class="row form-group">
										<a id="save-payer" class="btn btn-sm text-primary" title="Lưu" style="padding: 14px; font-size: 1.2rem">
											<span class="btn-icon"><i class="fa fa-save"></i></span>
										</a>
									</div>
									<div class="row form-group">
										<a id="close-payer-content" class="btn btn-sm text-danger" title="Đóng lại" style="padding: 14px; font-size: 1.3rem">
											<span class="btn-icon"><i class="fa fa-close"></i></span>
										</a>
									</div>
								</div>
							</div>
						</div>
					</div>

				</div>
				<div class="add-payer">
					<button id="b-add-payer" class="btn btn-outline-success" title="Thêm khách hàng">
						<i class="fa fa-plus"></i>
						Thêm khách hàng
					</button>
				</div>

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

<div class="modal fade" id="change-ssinv-modal" tabindex="-1" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 300px">
		<div class="modal-content" style="border-radius: 5px">
			<div class="modal-header" style="border-radius: 5px;background-color: #fdf0cd;">
				<h4 class="modal-title text-primary font-bold" id="groups-modalLabel">Khai báo số hóa đơn</h4>
				<i class="btn fa fa-times text-primary" data-dismiss="modal"></i>
			</div>
			<div class="modal-body" style="margin:3px;border-radius: 5px;overflow-y: auto;max-height: 90vh">
				<div class="form-group pb-3">
					<label class="col-form-label">Mẫu hóa đơn</label>
					<input class="form-control form-control-sm" id="inv-prefix" type="text" placeholder="Mẫu hóa đơn">
				</div>
				<div class="form-group pb-3">
					<label class="col-form-label">Từ số - đến số</label>
					<div class="input-group">
						<input class="form-control form-control-sm" id="inv-no-from" maxlength="7" type="text" placeholder="Từ số">
						<input class="form-control form-control-sm ml-2" id="inv-no-to" maxlength="7" type="text" placeholder="Đến số">
					</div>
				</div>
				<div class="form-group">
					<p class="text-muted m-b-20">Số hóa đơn kế tiếp sẽ được sử dụng là giá trị <br> [Từ số] được nhập vào ở trên!</p>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="confirm-ssInvInfo" class="btn btn-sm btn-outline-warning">
					<i class="fa fa-check"></i>
					Xác nhận
				</button>
				<button type="button" class="btn btn-sm btn-outline-secondary" data-dismiss="modal">
					<i class="fa fa-close"></i>
					Hủy bỏ
				</button>
			</div>
		</div>
	</div>
</div>

<!--container additional info-->
<div class="modal fade" id="FCL-attach-modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="groups-modalLabel" aria-hidden="true" data-whatever="">

	<div class="modal-dialog" role="document">
		<div class="modal-content p-2">
			<div class="modal-body px-2">
				<div class="row">
					<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="form-group pb-1">
							<h5 id="cntrAdd-cargoType" class="text-primary" style="border-bottom: 1px solid #eee">THÔNG TIN LỆNH HẠ HÀNG</h5>
						</div>
						<div class="row form-group">
							<label class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-form-label" title="Loại hàng">Loại hàng</label>
							<div class="col-sm-8 input-group input-group-sm">
								<select id="FCL-cargotype" class="selectpicker FCL-change-required" data-style="btn-default btn-sm" data-width="100%">
									<option value="" selected>[Loại hàng *]</option>
									<?php if (isset($cargoTypes) && count($cargoTypes) > 0) {
										foreach ($cargoTypes as $item) { ?>
											<option value="<?= $item['Code'] ?>"><?= $item['Code'] . ' : ' . $item['Description'] ?></option>
									<?php }
									} ?>
								</select>
							</div>
						</div>
						<div class="row form-group">
							<label class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-form-label" title="Hạn hạ hàng">Hạn hạ hàng</label>
							<div class="col-sm-8 input-group input-group-sm">
								<input class="form-control form-control-sm" id="FCL-exp-date" type="text" placeholder="Hạn hạ hàng" value="" maxlength="21">
							</div>
						</div>
						<div class="row form-group">
							<label class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-form-label" title="Tàu chuyến">Tàu chuyến</label>
							<div class="col-sm-8 input-group input-group-sm">
								<input class="form-control form-control-sm FCL-change-required" id="FCL-shipid" placeholder="Tàu/chuyến" type="text" readonly>
								<span class="input-group-addon bg-white btn mobile-hiden text-warning open-ship-modal" style="padding: 0 .5rem" title="chọn tàu" data-toggle="modal" data-id="FCL-shipid">
									<i class="ti-search"></i>
								</span>
							</div>
						</div>
						<div class="row form-group">
							<label class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-form-label" title="Cảng dỡ">Cảng dỡ</label>
							<div class="col-sm-8 input-group input-group-sm">
								<select id="FCL-pod" class="selectpicker" data-style="btn-default btn-sm" data-width="100%">
									<option value="" selected>[Cảng dỡ]</option>
								</select>
							</div>
						</div>
						<div class="row form-group">
							<label class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-form-label" title="Nội/Ngoại">Nội/Ngoại</label>
							<div class="col-sm-8 input-group input-group-sm">
								<select id="FCL-islocal" class="selectpicker" data-style="btn-default btn-sm" data-width="100%">
									<option value="" selected>Nội/Ngoại</option>
									<option value="L">Nội</option>
									<option value="F">Ngoại</option>
								</select>
							</div>
						</div>
						<div class="row form-group">
							<label class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-form-label" title="Trọng lượng">Trọng lượng</label>
							<div class="col-sm-8 input-group input-group-sm">
								<input class="form-control form-control-sm" id="FCL-cmdweight" type="number" placeholder="Trọng lượng" value="" maxlength="500">
							</div>
						</div>
						<div class="row form-group">
							<label class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-form-label" title="Ghi chú">Ghi chú</label>
							<div class="col-sm-8 input-group input-group-sm">
								<input class="form-control form-control-sm" id="FCL-note" type="text" placeholder="Ghi chú" value="" maxlength="500">
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<div style="margin: 0 auto">
					<button type="button" id="FCL-apply-order" class="btn btn-success btn-labeled btn-labeled-right btn-icon btn-sm">
						<span class="btn-label"><i class="ti-check"></i></span>Xác nhận</button>
					<button type="button" id="FCL-cancel-order" class="btn btn-danger btn-labeled btn-labeled-right btn-icon btn-sm">
						<span class="btn-label"><i class="ti-close"></i></span>Hủy bỏ</button>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	var _listShip = [];
	$(document).ready(function() {
		moment.tz.setDefault('Asia/Ho_Chi_Minh');
		var _colsPayment = ["STT", "DRAFT_INV_NO", "REF_NO", "TRF_CODE", "TRF_DESC", "INV_UNIT", "JobMode", "DMETHOD_CD", "CARGO_TYPE", "ISO_SZTP", "FE", "IsLocal", "QTY", "standard_rate", "DIS_RATE", "extra_rate", "UNIT_RATE", "AMOUNT", "VAT_RATE", "VAT", "TAMOUNT", "CURRENCYID", "IX_CD", "CNTR_JOB_TYPE", "VAT_CHK", "Remark", "TRF_DESC_MORE"],
			_colsAttachServices = ["Select", "CjMode_CD", "CJModeName", "Cont_Count"],
			_colCont = ["STT", "CntrNo", "BookingNo", "CntrClass", "OprID", "LocalSZPT", "ISO_SZTP", "Status", "SealNo", "IsLocal", "CMDWeight", "CARGO_TYPE", "Note", "cTLHQ"],
			_colPayer = ["STT", "CusID", "VAT_CD", "CusName", "Address", "CusType"],
			_ports = [];

		var _bookingList = [],
			_bookingFiltered = [],
			selected_cont = [],
			_lstEir = [],
			_localForeign = [{
					"Code": "L",
					"Name": "Nội"
				},
				{
					"Code": "F",
					"Name": "Ngoại"
				}
			];
		var tblCont = $("#tbl-cont"),
			tblInv = $("#tbl-inv"),
			tblAttach = $('#tb-attach-srv');

		var payers = [],
			_attachServicesChecker = [],
			_lstAttachService = [];

		<?php if (isset($payers) && count($payers) > 0) { ?>
			payers = <?= json_encode($payers); ?>;
		<?php } ?>

		var maxContNum = 0;

		$('#search-ship').DataTable({
			scrollY: '35vh',
			paging: false,
			order: [
				[1, 'asc']
			],
			columnDefs: [{
					className: "input-hidden",
					targets: [0, 6, 7, 10, 11]
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

		$('#booking-detail').DataTable({
			info: true,
			paging: false,
			ordering: false,
			searching: true,
			scrollY: '51vh',
			columnDefs: [{
				orderable: false,
				className: 'select-checkbox',
				targets: 0
			}, {
				className: 'text-center',
				targets: [2, 3]
			}],
			select: {
				style: 'multi+shift',
				selector: 'td:first-child'
			},
			order: [
				[1, 'asc']
			],
		});

		tblCont.DataTable({
			columnDefs: [{
				render: function(data, type, full, meta) {
					var temp = Array.isArray(data) ? data[0] : data;
					if (temp) {
						tblCont.DataTable().cell(meta.row, meta.col).nodes().to$().removeClass('error')
						return temp.trim().toUpperCase() == "L" ? "Nội" : "Ngoại";
					} else return temp;
				},
				className: "text-center show-dropdown input-required",
				targets: _colCont.indexOf("IsLocal")
			}],
			info: false,
			paging: false,
			searching: true,
			select: true,
			buttons: [],
			scrollY: '25vh'
		});

		tblInv.DataTable({
			info: false,
			paging: false,
			searching: true,
			buttons: [],
			columnDefs: [{
					targets: _colsPayment.getIndexs(['STT', 'CURRENCYID']),
					className: "text-center"
				},
				{
					targets: _colsPayment.indexOf('QTY'),
					className: "text-right"
				},
				{
					targets: _colsPayment.getIndexs(["standard_rate", "DIS_RATE", "extra_rate", "UNIT_RATE", "AMOUNT", "VAT_RATE", "VAT", "TAMOUNT"]),
					className: "text-right",
					render: $.fn.dataTable.render.number(',', '.', 2)
				},
				{
					targets: _colsPayment.getIndexs(["IX_CD", "CNTR_JOB_TYPE", "VAT_CHK", "Remark", "TRF_DESC_MORE"]),
					className: "hiden-input"
				}
			],
			scrollY: '30vh',
			createdRow: function(row, data, dataIndex) {
				if (!data[_colsPayment.indexOf('TRF_CODE')]) {
					$(row).addClass('row-total');

					$('td:eq(0)', row).attr('colspan', 17);
					$('td:eq(0)', row).addClass('text-center');
					for (var i = 1; i <= 16; i++) {
						$('td:eq(' + i + ')', row).css('display', 'none');
					}

					this.api().cell($('td:eq(0)', row)).data('TỔNG CỘNG');
				}
			}
		});

		tblAttach.DataTable({
			paging: false,
			columnDefs: [{
				className: 'text-center',
				orderDataType: 'dom-text',
				type: 'string',
				targets: _colsAttachServices.indexOf("Select")
			}],
			order: [],
			buttons: [],
			info: false,
			searching: false,
			scrollY: '16vh'
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

		autoLoadYearCombo('cb-searh-year');
		load_payer();

		//------SET DROPDOWN BUTTON FOR COLUMN
		tblCont.columnDropdownButton({
			data: [{
				colIndex: _colCont.indexOf("IsLocal"),
				source: _localForeign
			}],
			onSelected: function(cell, itemSelected) {
				let val = _localForeign.filter(p => p.Name == itemSelected.text())[0].Code;
				let dCell = tblCont.DataTable().cell(cell);
				let oldVal = dCell.data();
				let rIndex = dCell.index().row;
				_bookingFiltered[rIndex].IsLocal = val;
				tblCont.DataTable().cell(cell).data(val).draw(false);
				if (val != oldVal) {
					tblInv.dataTable().fnClearTable();
				}
			}
		});
		//------SET DROPDOWN BUTTON FOR COLUMN

		$('#ref-date').val(moment().format('DD/MM/YYYY HH:mm:ss'));
		$('#ref-exp-date').datetimepicker({
			dateFormat: 'dd/mm/yy',
			timeFormat: 'HH:mm:ss',
			todayHighlight: true,
			oneLine: true,
			minDate: moment().format('DD/MM/YYYY HH:mm:ss'),
			controlType: 'select',
			autoclose: true,
			timeInput: true
		});
		$('#FCL-exp-date').datetimepicker({
			dateFormat: 'dd/mm/yy',
			timeFormat: 'HH:mm:ss',
			todayHighlight: true,
			oneLine: true,
			minDate: moment().format('DD/MM/YYYY HH:mm:ss'),
			controlType: 'select',
			autoclose: true,
			timeInput: true,
			beforeShow: function(el) {
				setTimeout(function() {
					$('.ui-datepicker').css('z-index', 1046);
				}, 0);
			}
		});

		$('#ref-exp-date').val(moment().format('DD/MM/YYYY 23:59:59'));
		$('#ref-exp-date + span').on('click', function() {
			$('#ref-exp-date').val('');
		});

		$('#barge-modal, #booking-modal, #payer-modal, #ship-modal').on('shown.bs.modal', function(e) {
			$($.fn.dataTable.tables(true)).DataTable().columns.adjust();
		});

		//THU SAU
		$('#payment-modal').on('hide.bs.modal', function(e) {
			$('#show-payment-modal').attr("data-target", "");
		});

		$('#payment-type').on('change', function() {
			if ($(this).val() == "M") {
				$('#chk-view-inv').closest('label').show(); //show option tinh cuoc
				$('#show-payment-modal')
					.removeClass('btn-primary')
					.addClass('btn-warning')
					.html('<i class="fa fa-print"></i>&ensp;Thanh toán'); //doi title button

				$('#p-money i').removeClass('fa-square').addClass('fa-check-square');
				$('#p-credit i').removeClass('fa-check-square').addClass('fa-square');
			} else {
				$('#chk-view-cont').trigger('click'); //chuyen qua tab list cont
				$('#chk-view-inv').closest('label').hide(); //an option tinh cuoc
				$('#show-payment-modal')
					.addClass('btn-primary')
					.removeClass('btn-warning')
					.html('<i class="fa fa-save"></i>&ensp;Lưu thu sau'); //doi title button

				$('#p-money i').removeClass('fa-check-square').addClass('fa-square');
				$('#p-credit i').removeClass('fa-square').addClass('fa-check-square');
			}
		});
		//THU SAU

		$('#show-payment-modal').on("click", function(e) {
			if (!$("#taxcode").val()) {
				$('#taxcode').addClass("error");
				toastr["warning"]("Chưa chọn đối tượng thanh toán!");
				e.preventDefault();
				return;
			}

			var paymentType = $('#payment-type').val();
			if (paymentType == "M") {
				if (tblInv.DataTable().rows().data().toArray().length == 0) {
					toastr["warning"]("Không có gì để thanh toán!");
					e.preventDefault();
					return;
				}

				$("#dv-cash, #publish-type").removeClass("hiden-input");
				$("#dv-credit").addClass("hiden-input");

				if (!$("input[name='publish-opt']").is(":checked")) {
					$("input[name='publish-opt'][value='e-inv']").prop("checked", true);
					$("#m-inv-container").addClass("hiden-input");
				}
				$(this).attr("data-target", "#payment-modal");

			} else {

				if (tblCont.DataTable().rows().data().toArray().length == 0) {
					toastr["warning"]("Không có gì để lưu!");
					e.preventDefault();
					return;
				}

				//kiem tra cac truong bat buoc truoc khi luu thu sau
				if ($('.input-required').has_required()) {
					$('.toast').remove();
					toastr['error']('Các trường bắt buộc (*) không được để trống!');
					return;
				}

				$("#dv-cash, #publish-type").addClass("hiden-input");
				$("#dv-credit").removeClass("hiden-input");
				$("input[name='publish-opt']").prop("checked", false);

				var tdrequired = $('#tbl-cont tbody').find('td.input-required');
				if (tdrequired.has_required()) {
					$('.toast').remove();
					toastr['error']('Vui lòng nhập đầy đủ thông tin!');
					tblInv.dataTable().fnClearTable();
					$('#chk-view-cont').trigger('click');
					return;
				}

				_lstEir = [];

				if (_bookingFiltered.length > 0 && selected_cont.length > 0) {
					for (i = 0; i < _bookingFiltered.length; i++) {
						if (selected_cont.indexOf(_bookingFiltered[i].CntrNo) == '-1') continue;
						addCntrToEir(_bookingFiltered[i]);
					}
				}

				$.confirm({
					title: 'Cảnh báo!',
					type: 'orange',
					icon: 'fa fa-warning',
					content: 'Xác nhận lưu thu sau?',
					buttons: {
						ok: {
							text: 'Xác nhận',
							btnClass: 'btn-warning',
							keys: ['Enter'],
							action: function() {
								$('body').blockUI();
								saveData();
							}
						},
						cancel: {
							text: 'Hủy bỏ',
							btnClass: 'btn-default',
							keys: ['ESC']
						}
					}
				});
			}
		});

		$('#b-add-payer').on("click", function() {
			$('.add-payer-container').addClass("payer-show");
		});

		$('#close-payer-content').on("click", function() {
			$('.add-payer-container').removeClass("payer-show");
		});

		$('input[name="view-opt"]').bind('change', function(e) {
			$('.grid-toggle').find('div.table-responsive').toggleClass('grid-hidden');
			tblCont.DataTable().columns.adjust();
			tblInv.DataTable().columns.adjust();
			if ($('#chk-view-inv').is(':checked') && tblInv.DataTable().rows().count() == 0) {
				var tdrequired = $('#tbl-cont tbody').find('td.input-required');
				if (tdrequired.has_required()) {
					$('.toast').remove();
					toastr['error']('Vui lòng nhập đầy đủ thông tin!');
					tblInv.dataTable().fnClearTable();
					$('#chk-view-cont').trigger('click');
					return;
				}

				_lstEir = [];

				if (_bookingFiltered.length > 0 && selected_cont.length > 0) {
					for (i = 0; i < _bookingFiltered.length; i++) {
						if (selected_cont.indexOf(_bookingFiltered[i].CntrNo) == '-1') continue;
						addCntrToEir(_bookingFiltered[i]);
					}
				}

				loadpayment();
			}
		});

		$('#chkFCL-attach').on('change', function() {

			if ($(this).is(":checked")) {
				if (tblCont.DataTable().data().toArray().length == 0) {
					$('.toast').remove();
					toastr.warning('Vui lòng nhập thông tin lệnh Cấp rỗng trước!');
					$('#chkFCL-attach').prop('checked', false);
					return false;
				}

				if (selected_cont.filter(p => p !== '*').length == 0) {
					$('.toast').remove();
					toastr.warning('Booking không được chỉ định số container!');
					$('#chkFCL-attach').prop('checked', false);
					return false;
				}

				$('#FCL-attach-modal').modal('show');
				$('.FCL-change-required').addClass('input-required');
				if ($("#search-ship").DataTable().data().toArray().length == 0) {
					search_ship()
				}

			} else {
				$('#FCL-attach-modal').modal('hide');
				$('.FCL-change-required').removeClass('input-required');
				_lstEir = _lstEir.filter(item => item.CJMode_CD != "HBAI");
			}
		});

		$('#FCL-apply-order').on('click', function() {
			var hasRequired = $('#FCL-attach-modal').find('.input-required').has_required();
			if (hasRequired) {
				$('.toast').remove();
				toastr['error']('Các trường bắt buộc (*) không được để trống!');
				return;
			}

			$('#FCL-attach-modal').modal('hide');
		})
		$('#FCL-cancel-order').on('click', function() {
			$('#chkFCL-attach').prop('checked', false);
			$('#FCL-attach-modal').modal('hide');
		})

		$('input[name="chkSalan"]').on('change', function() {
			$('#barge-ischecked').toggleClass('un-pointer');
			$('#barge-info').toggleClass('input-required');

			var ischecked = $(this).is(':checked');
			if (!ischecked) {
				$('#barge-info').val('');
				$('#barge-info').trigger('change');
			}

			var bargeInfo = $('#barge-info').val();

			$.each(_lstEir, function(idx, item) {
				item.DMETHOD_CD = ischecked ? "BAI-SALAN" : "BAI-XE";
				item.IsTruckBarge = ischecked ? "B" : "T";
				item.BARGE_CODE = bargeInfo ? bargeInfo.split('/')[0] : "";
				item.BARGE_YEAR = bargeInfo ? bargeInfo.split('/')[1] : "";
				item.BARGE_CALL_SEQ = bargeInfo ? bargeInfo.split('/')[2] : "";
			});

			if ($('#chk-view-inv').is(':checked')) {
				loadpayment();
			}
		});

		// $(document).on('click', '#booking-detail tbody tr td', function() {
		// 	$(this).parent().find('td:eq(0)').first().toggleClass('ti-check');
		// 	$(this).parent().toggleClass('m-row-selected');
		// });

		$('#booking-detail').on('click', 'tbody tr td:not(:nth-child(1))', function(e) {
			var tr = $(e.target).closest('tr');
			if (tr.hasClass('selected')) {
				$('#booking-detail').DataTable().rows(tr).deselect();
			} else {
				$('#booking-detail').DataTable().rows(tr).select();
			}
		});

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

		///////// SEARCH BARGE
		$('#btn-search-barge').on('click', function() {
			search_barge();
		});
		$(document).on('click', '#search-barge tbody tr', function() {
			$('.m-row-selected').removeClass('m-row-selected');
			$(this).addClass('m-row-selected');
		});
		$('#select-barge').on('click', function() {
			var r = $('#search-barge tbody').find('tr.m-row-selected').first();
			$('#barge-info').val($(r).find('td:eq(1)').text() + "/" + $(r).find('td:eq(3)').text() + "/" + $(r).find('td:eq(4)').text());
			$('#barge-info').trigger('change');
			$('#ship-modal').modal("toggle");
			getLane($(r).find('td:eq(6)').text());

		});
		$('#search-barge').on('dblclick', 'tbody tr td', function() {
			var r = $(this).parent();
			$('#barge-info').val($(r).find('td:eq(1)').text() + "/" + $(r).find('td:eq(3)').text() + "/" + $(r).find('td:eq(4)').text());
			$('#barge-modal').modal("toggle");
			$('#barge-info').trigger('change');
		});
		///////// END SEARCH BARGE

		///////// SEARCH SHIP
		$('.open-ship-modal').on('click', function(e) {
			$('#ship-modal').data('target-input', $(this).data('id'));
			$(this).attr('data-target', '#ship-modal');
			$('#ship-modal').modal("show");
			return;
		})
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
			var id = $('#ship-modal').data('target-input');
			$(`#${id}`).val($(r).find('td:eq(2)').text() + "/" + $(r).find('td:eq(3)').text() + "/" + $(r).find('td:eq(4)').text());

			$(`#${id}`).removeClass('error');
			$(`#${id}`).data('shipkey', $(r).find('td:eq(6)').text());
			$('#ship-modal').modal("toggle");
			getLane($(r).find('td:eq(6)').text());
		});
		$('#unselect-ship').on('click', function() {
			var id = $('#ship-modal').data('target-input');
			$(`#${id}`).val('');
			$(`#${id}`).data('shipkey', '');
		});
		$('#search-ship').on('dblclick', 'tbody tr td', function() {
			var r = $(this).parent();
			var id = $('#ship-modal').data('target-input');
			$(`#${id}`).val($(r).find('td:eq(2)').text() + "/" + $(r).find('td:eq(3)').text() + "/" + $(r).find('td:eq(4)').text());
			$(`#${id}`).removeClass('error');
			$(`#${id}`).data('shipkey', $(r).find('td:eq(6)').text());

			$('#ship-modal').modal("toggle");
			getLane($(r).find('td:eq(6)').text());

		});
		///////// END SEARCH SHIP

		//------USING MANUAL INVOICE

		$("input[name='publish-opt']").on("change", function(e) {
			if ($(e.target).val() == "m-inv") {
				$("#m-inv-container").removeClass("hiden-input");
				$("#pay-atm").prop("disabled", <?= $isDup || !isset($ssInvInfo) || count($ssInvInfo) == 0; ?>);
			} else {
				$("#m-inv-container").addClass("hiden-input");
				$("#pay-atm").prop("disabled", false);
			}
		});

		$("#confirm-ssInvInfo").on("click", function() {
			if (!$("#inv-prefix").val()) {
				toastr["error"]("Vui lòng nhập mẫu hóa đơn!");
				return;
			}

			if (!$("#inv-no-from").val()) {
				toastr["error"]("Vui lòng nhập số hóa đơn [Từ số]!");
				return;
			}

			if (!$("#inv-no-to").val()) {
				toastr["error"]("Vui lòng nhập số hóa đơn [Đến số]!");
				return;
			}

			$.confirm({
				columnClass: 'col-md-4 col-md-offset-4 mx-auto',
				titleClass: 'font-size-17',
				title: 'Xác nhận',
				content: 'Xác nhận thông tin khai báo hóa đơn này!?',
				buttons: {
					ok: {
						text: 'OK',
						btnClass: 'btn-sm btn-primary btn-confirm',
						keys: ['Enter'],
						action: function() {
							var data = {
								invno: $("#inv-no-from").val(),
								serial: $("#inv-prefix").val(),
								fromNo: $("#inv-no-from").val(),
								toNo: $("#inv-no-to").val()
							};

							var formData = {
								'action': 'save',
								'act': 'use_manual_Inv',
								'useInvData': data
							};

							$("#change-ssinv-modal .modal-content").blockUI();

							$.ajax({
								url: "<?= site_url(md5('Task') . '/' . md5('tskEmptyPickup')); ?>",
								dataType: 'json',
								data: formData,
								type: 'POST',
								success: function(data) {

									$("#change-ssinv-modal .modal-content").unblock();

									if (data.deny) {
										toastr["error"](data.deny);
										return;
									}

									var invNo = formData.useInvData.serial + formData.useInvData.invno;

									if (data.isDup) {
										toastr["error"]("Số hóa đơn bắt đầu [" + invNo + "] đã tồn tại trong hệ thống!");
										return;
									}

									$("#change-ssinv-modal").modal('hide');
									toastr["success"]("Xác nhận sử dụng Số HĐ [" + invNo + "] thành công!");
									$("#ss-invNo").text(invNo);
									$("#change-ssinvno").attr("title", "Thay đổi hóa đơn sử dụng tiếp theo")
										.html('<span class="btn-icon"><i class="fa fa-pencil"></i>Thay đổi');

									$("#pay-atm").prop("disabled", false);
								},
								error: function(err) {
									$("#change-ssinv-modal .modal-content").unblock();
									toastr["error"]("Server Error at [confirm-ssInvInfo]!");
									console.log(err);
								}
							});
						}
					},
					cancel: {
						text: 'Hủy',
						btnClass: 'btn-sm',
						keys: ['ESC'],
						action: function() {

						}
					}
				}
			});
		});

		//------USING MANUAL INVOICE

		var _ktype = "";
		$('#bookingno').on('keypress', function(e) {
			if (!$(this).val()) return;
			if (e.which == 13) {
				_ktype = "enter";
				$('#cntrno-search').trigger('click');
			}
		});

		var _ktypecntr = "";
		$('#cntrno').on('change keypress', function(e) {
			if ((e.which == 13 || e.type == "change") && _ktypecntr == "") {
				load_booking(e);
				_ktypecntr = e.type;
				return;
			}
			_ktypecntr = "";
		});

		$('#cntrno-search').on('click', function(e) {
			var rl = $('#booking-detail').DataTable().rows().to$();
			if (rl.length == 1 && rl[0].length > 0) {
				$(this).attr('data-target', '#booking-modal');
			} else {
				load_booking(e);
			}
		});

		$('#apply-booking').on('click', function() {
			var selectedRows = $('#booking-detail').DataTable().rows('.selected').data().toArray();
			selectedRows.map((v, k) => {
				var cntrNo = v[1];
				if (selected_cont.indexOf(selected_cont) < 0) {
					selected_cont.push(cntrNo);
				}
			});

			apply_booking();
		});

		$("#sizetype").on("change", function() {

			var temp = _bookingList.filter(p => p.LocalSZPT == $("#sizetype").val())[0];
			if (new Date(temp.ExpDate) < new Date()) {
				$('.toast').remove();
				toastr["info"]("Booking / Kích cỡ [" + temp.BookingNo + " / " + temp.LocalSZPT + "] đã hết hạn!");
				return;
			}

			var bookAmt = temp.BookAmount ? parseInt(temp.BookAmount) : 0;
			var stackAmt = temp.StackingAmount ? parseInt(temp.StackingAmount) : 0;
			if (bookAmt <= stackAmt) {
				$('.toast').remove();
				toastr["info"]("Booking / Kích cỡ [" + temp.BookingNo + " / " + temp.LocalSZPT + "] đã hết số lượng đặt chỗ!");
				return;
			}


			var countrowBySize = tblCont.DataTable().rows(function(idx, data, node) {
				return data[_colCont.indexOf("LocalSZPT")] === $("#sizetype").val();
			}).count();

			$('#noncont').data("old", countrowBySize);
			$('#noncont').val(countrowBySize);

			maxContNum = bookAmt - stackAmt;

		});

		$('#noncont').data("old", 0);
		$('#noncont').on("click", function() {
			if (parseInt($(this).data("old")) != parseInt($(this).val())) {
				$(this).trigger("change");
			}
		});
		$('#noncont').on('change', function() {

			if (!$("#sizetype").val()) {
				$('#noncont').val(0);
				$('.toast').remove();
				toastr["error"]("Chưa chọn kích cỡ!");
				return;
			}

			var currentContInput = parseInt($(this).val());

			if (currentContInput > maxContNum) {
				$(this).val($(this).data("old"));
				$('.toast').remove();
				toastr["error"]("Quá số lượng đặt chỗ!");
				return;
			}

			$(this).data("old", $(this).val());

			//loại những cont cũ theo size type ra, để add cont mới, ví dụ: lúc trước nhập 3 cont, sau đó nhập 2 cont
			// thì xóa 3 cont cũ. add 2 cont mới
			_bookingFiltered = _bookingFiltered.filter(p => p.LocalSZPT !== $("#sizetype").val());

			selected_cont = ['*'];
			var temp = _bookingList.filter(p => p.LocalSZPT == $("#sizetype").val())[0];
			temp.CntrNo = "*";

			if (temp) {
				for (i = 1; i <= parseInt($(this).val()); i++) {
					_bookingFiltered.push($.extend({}, temp));
				}
			}

			apply_booking();
		});

		$('#noncont').on('keydown', function(e) {
			if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
				((e.keyCode == 65 || e.keyCode == 86 || e.keyCode == 67) && (e.ctrlKey === true || e.metaKey === true)) ||
				(e.keyCode >= 35 && e.keyCode <= 40) || e.keyCode >= 112) {
				return;
			}
			// Ensure that it is a number and stop the keypress
			if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
				e.preventDefault();
			}
		});

		//for number ,space, / and :
		$('#ref-exp-date').on('keydown', function(e) {
			if ($.inArray(e.keyCode, [32, 46, 8, 9, 27, 13, 191]) !== -1 ||
				((e.keyCode == 65 || e.keyCode == 86 || e.keyCode == 67) && (e.ctrlKey === true || e.metaKey === true)) ||
				(e.keyCode >= 35 && e.keyCode <= 40) || (e.keyCode >= 112 && e.keyCode <= 123) || (e.shiftKey && e.keyCode == 59)) {
				return;
			}
			// Ensure that it is a number and stop the keypress
			if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
				e.preventDefault();
			}
		});

		// ----- FOR ATTACH SERVICES
		load_attach_srv();
		$("#chkServiceAttach").on("change", function() {
			var content = $("#col-transfer")[0];

			$("#col-transfer").remove();

			if ($(this).is(":checked")) {
				$("#row-transfer-left").append(content);
			} else {
				$("#row-transfer-right").append(content);
			}

			$("#col-attach-service").toggle(800, function() {
				$($.fn.dataTable.tables(true)).DataTable()
					.columns
					.adjust();
			});

		});

		//setup before functions
		var typingTimer;
		var doneInterval = 500;

		tblCont.DataTable().on("select deselect", function(e, dt, type, indexes) {
			clearTimeout(typingTimer);
			typingTimer = setTimeout(loadAttachData(indexes), doneInterval);
		});

		tblAttach.on('change', 'tbody tr td input[type="checkbox"]', function(e) {

			var inp = $(e.target);

			if (tblCont.DataTable().rows('.selected').data().length == 0) {

				$(".toastr").remove();
				toastr["error"]("Vui lòng chọn một container trước!");

				if (inp.is(":checked")) {
					inp.removeAttr("checked");
					inp.val("");
				} else {
					inp.attr("checked", "");
					inp.val(1);
				}

				tblAttach.DataTable().cell(inp.closest("td")).data(inp.closest("td").html()).draw(false);

				return;
			}

			if (inp.is(":checked")) {
				inp.attr("checked", "");
				inp.val(1);
			} else {
				inp.removeAttr("checked");
				inp.val("");
			}

			if (inp.closest("td").index() == _colsAttachServices.indexOf("Select")) {
				var currentTD = inp.closest("td");

				var selectedConts = tblCont.DataTable()
					.rows('.selected')
					.data().toArray()
					.map(x => x[_colCont.indexOf("CntrNo")]);

				var currentCjMode = inp.closest("tr").find("td:eq(" + _colsAttachServices.indexOf("CjMode_CD") + ")").text();

				if (_attachServicesChecker.length > 0) {
					var contHasThisServices = _attachServicesChecker.filter(p => selectedConts.indexOf(p.CntrNo) != -1 && p.CJMode_CD == currentCjMode)
						.map(x => x.CntrNo);

					_attachServicesChecker.filter(p => contHasThisServices.indexOf(p.CntrNo) != -1 && p.CJMode_CD == currentCjMode)
						.map(x => x.Select = (inp.is(":checked") ? 1 : 0));

					var contNonService = selectedConts.filter(p => contHasThisServices.indexOf(p) == -1);
					$.each(contNonService, function(idx, iContNo) {
						_attachServicesChecker.push({
							Select: inp.is(":checked") ? 1 : 0,
							CntrNo: iContNo,
							CJMode_CD: currentCjMode
						});
					});
				} else {
					$.each(selectedConts, function(idx, iContNo) {
						_attachServicesChecker.push({
							Select: inp.is(":checked") ? 1 : 0,
							CntrNo: iContNo,
							CJMode_CD: currentCjMode
						});
					});
				}

				//thay đổi số lượng chọn khi check/uncheck
				var plusOrSubtract = inp.is(":checked") ? 1 : -1;

				var oldNumCell = currentTD.closest("tr").find("td:eq(" + _colsAttachServices.indexOf("Cont_Count") + ")");
				var oldNum = tblAttach.DataTable().cell(oldNumCell).data();
				var newNum = (oldNum ? parseInt(oldNum) : 0) + (selectedConts.length * plusOrSubtract);

				tblAttach.DataTable().cell(oldNumCell).data(newNum > 0 ? newNum : "");

				if ($('#chk-view-inv').is(':checked')) {
					loadpayment();
				} else {
					$('#tbl-inv').dataTable().fnClearTable();
				}
			}

			var crCell = inp.closest('td');
			var crRow = inp.closest('tr');
			var eTable = tblAttach.DataTable();

			eTable.cell(crCell).data(crCell.html()).draw(false);
			eTable.row(crRow).nodes().to$().addClass("editing");
		});

		// ----- FOR ATTACH SERVICES

		$('#remove').on('click', function() {
			if ($('#chk-view-inv').is(':checked')) return;
			if (tblCont.DataTable().rows().count() == 0) {
				return;
			}

			if (tblCont.DataTable().rows('.selected').count() == 0) {
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
							var removedRows = tblCont.DataTable().rows('.selected').data().toArray();
							var removedCont = removedRows.map(x => x[_colCont.indexOf("CntrNo")]);
							selected_cont = selected_cont.filter(p => p == "*" || removedCont.indexOf(p) == "-1");

							var temp = [];
							$.each(removedRows, function(i, c) {
								var x = _bookingFiltered.filter(p => p.CntrNo == c[_colCont.indexOf("CntrNo")] &&
									p.BookingNo == c[_colCont.indexOf("BookingNo")] &&
									p.LocalSZPT == c[_colCont.indexOf("LocalSZPT")]);
								if (x.length > 0) {
									temp.push(_bookingFiltered.indexOf(x[0]));
								}
							});

							$.each(temp, function() {
								_bookingFiltered.splice(this, 1);
							});

							tblCont.DataTable().rows(".selected").remove().draw(false);
							tblCont.updateSTT();
							$('#booking-detail').DataTable().rows().every(function(rowIdx, tableLoop, rowLoop) {
								var cntrNo = this.data()[1];
								if (removedCont.indexOf(cntrNo) >= 0) {
									$('#booking-detail').DataTable().rows(rowIdx).deselect();
								}
							});

							//remove all row to recalculate
							tblInv.dataTable().fnClearTable();
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

		$("#save-payer").on("click", function() {
			var addTaxCode = $("#add-payer-taxcode").val();
			var addPayerName = $("#add-payer-name").val();
			var address = $("#add-payer-address").val();

			if (!addTaxCode) {
				$("#add-payer-taxcode").addClass("error");
				$(".toast").remove();
				toastr["error"]("Vui lòng nhập thông tin [Mã số thuế]!");
				return;
			}

			var checkTaxCode = addTaxCode;
			checkTaxCode = checkTaxCode.replace("-", "");

			if ([10, 13].indexOf(checkTaxCode.length) == "-1" || isNaN(checkTaxCode)) {
				$("#add-payer-taxcode").addClass("error");
				$(".toast").remove();
				toastr["error"]("Vui lòng nhập đúng định dạng [Mã số thuế]!");
				return;
			}

			if (!addPayerName) {
				$("#add-payer-name").addClass("error");
				$(".toast").remove();
				toastr["error"]("Vui lòng nhập thông tin [Tên]!");
				return;
			}

			var formData = {
				'action': 'save',
				'act': 'save_new_payer',
				'taxCode': addTaxCode,
				'cusName': addPayerName,
				'address': address
			};

			save_new_payer(formData);
		});

		$('#pay-atm').on('click', function() {
			//add payment method
			var publishType = $("input[name='publish-opt']:checked").val();

			if (!$('#paymentMethod').val() && publishType != 'dft') {
				toastr.warning('Chưa chọn phương thức thanh toán!');
				$('#paymentMethod').selectpicker('toggle');
				return;
			}

			if (publishType == "e-inv") {
				publishInv();
			} else {
				saveData();
			}
		});

		$('#save-credit').on("click", function() {
			//kiem tra cac truong bat buoc truoc khi luu thu sau
			if ($('.input-required').has_required()) {
				$('.toast').remove();
				toastr['error']('Các trường bắt buộc (*) không được để trống!');
				return;
			}
			saveData();
		});

		///////// INPUT TAX_CODE DIRECTLY
		$("#taxcode").on("keypress", function(e) {
			if (e.keyCode == 13) {
				$(e.target).trigger('change');
			}
		});
		///////// INPUT TAX_CODE DIRECTLY

		var iptimee;
		$('.input-required:not(#taxcode)').on('input', function(e) {
			clearTimeout(iptimee);
			iptimee = window.setTimeout(function() {
				$(e.target).blur();
			}, 1500);
		});

		var typingTimer;

		$(document).on('change', 'input, select', function(e) {
			clearTimeout(typingTimer);
			var cr = e.target;
			if ($(cr).val()) {
				$(cr).removeClass('error');
				$(cr).parent().removeClass('error');
			}

			if ($(cr).attr('id') == 'taxcode') {
				var taxcode = $(cr).val();
				if (!taxcode) {
					clearPayer();
					return;
				}

				var cusID = "";
				var ccc = $("#cusID").val();

				if (payers.length == 0 && !cusID) {
					findPayer(taxcode);
					return;
				}

				var tempSearchCus = payers.filter(p => p.VAT_CD.toString().includes(taxcode));
				if (ccc) {
					cusID = ccc;
				} else {
					if (tempSearchCus.length == 0) {
						$(".toast").remove();
						toastr.options.timeOut = "10000";
						toastr["warning"]("Đối tượng thanh toán này không tồn tại trong hệ thống! <br/> Vui lòng Thêm mới/ Chọn đối tượng khác!");
						toastr.options.timeOut = "5000";
						return;
					} else if (tempSearchCus.length > 1) {
						clearPayer();
						$('#search-payer').DataTable().search(taxcode).draw(false)
						$('#payer-modal').modal("show");
						return;
					}
					cusID = tempSearchCus[0].CusID;
				}

				$("#cusID").val(cusID);
				var pytype = getPayerType(cusID);
				$.each(_lstEir, function(k, v) {
					_lstEir[k].CusID = cusID;
					_lstEir[k].PAYER_TYPE = pytype;
				});

				var checkPayerInput = fillPayer();

				if (!checkPayerInput) {
					clearPayer();

					$(".toast").remove();
					toastr.options.timeOut = "10000";
					toastr["warning"]("Đối tượng thanh toán này không tồn tại trong hệ thống! <br/> Vui lòng Thêm mới/ Chọn đối tượng khác!");
					toastr.options.timeOut = "5000";
					return;
				}
			}

			if ($(cr).attr('id') == "bookingno") {
				$('#cntrno-search').attr('data-target', '');
				if (e.type == 'change' && _ktype == "") {
					$('#cntrno-search').trigger('click');
				}
				//reset list eir
				_lstEir = [];
				tblCont.dataTable().fnClearTable();
				tblInv.dataTable().fnClearTable();
				$('#booking-detail').dataTable().fnClearTable();

				return;
			}

			if (_lstEir.length > 0) {
				$.each(_lstEir, function(idx, item) {
					if (item.CJMode_CD == 'CAPR') {
						eir_base(item);
					} else {
						FCL_eir_base(item);
					}
				});
			}

			typingTimer = window.setTimeout(function() {
				//reset list eir
				// _lstEir = [];
				if ($('.input-required.error').length == 0 &&
					($(cr).attr('id') == "taxcode" || $(cr).attr('id') == "cntrclass" || $(cr).attr('id') == 'barge-info' || $(cr).attr('id') == 'chkFCL-attach') &&
					($(cr).val() || $(cr).attr('id') == 'barge-info') &&
					$('#chk-view-inv').is(':checked')) {
					loadpayment();
				}

			}, 1000);
		});

		function getLane(shipkey) {
			$('.modal-dialog').blockUI();

			var formdata = {
				'action': 'view',
				'act': 'getLane',
				'shipkey': shipkey
			};
			$.ajax({
				url: "<?= site_url(md5('Task') . '/' . md5('tskStuffingOrder')); ?>",
				dataType: 'json',
				data: formdata,
				type: 'POST',
				success: function(data) {
					$('.modal-dialog').unblock();

					if (data.deny) {
						toastr["error"](data.deny);
						return;
					}

					if (!data.ports || data.ports.length == 0) {
						toastr["warning"]("Không có thông tin cảng dỡ / đích theo hành trình tàu [" + $("#shipid").val() + "]!");
						$("#shipid").val("");
						return;
					}
					_ports = data.ports;
					var tempPort = "";
					_ports.map(item => {
						tempPort += `<option value = '${item.Port_CD}'>${item.Port_Name}</option>`
					})
					console.log('tempPort', tempPort);

					$('#FCL-pod').append(tempPort);
					$('#FCL-pod').selectpicker('refresh');
				},
				error: function(err) {
					console.log(err);
				}
			});
		}

		//function
		function search_barge() {
			$("#search-barge").waitingLoad();
			var formdata = {
				'action': 'view',
				'act': 'search_barge'
			};

			$.ajax({
				url: "<?= site_url(md5('Task') . '/' . md5('tskEmptyPickup')); ?>",
				dataType: 'json',
				data: formdata,
				type: 'POST',
				success: function(data) {
					var rows = [];
					if (data.barges.length > 0) {
						for (i = 0; i < data.barges.length; i++) {
							rows.push([
								(i + 1), data.barges[i].ShipID, data.barges[i].ShipName, data.barges[i].ShipYear, data.barges[i].ShipVoy
							]);
						}
					}
					$('#search-barge').DataTable({
						paging: false,
						searching: false,
						infor: false,
						scrollY: '30vh',
						buttons: [],
						data: rows
					});
				},
				error: function(err) {
					console.log(err);
				}
			});
		}

		function load_booking(e) {
			// neu tim kim bang so cont
			if ($(e.target).attr('id') == 'cntrno') {

				$("#cntrno").parent().blockUI();
				//loc so cont trong list _bookinglist
				filtercontainer();
				// neu tim duoc so cont trong bookinglist, apply so cont nay va return
				if (_bookingFiltered.length > 0) {
					$("#cntrno").parent().unblock();
					apply_container(true);
					return;
				}
			} else {
				$("#bookingno").parent().blockUI();
			}

			var formdata = {
				'action': 'view',
				'act': 'load_booking',
				'bkno': $('#bookingno').val().trim(),
				'cntrno': String($('#cntrno').val()).trim().toUpperCase()
			};

			$.ajax({
				url: "<?= site_url(md5('Task') . '/' . md5('tskEmptyPickup')); ?>",
				dataType: 'json',
				data: formdata,
				type: 'POST',
				success: function(data) {
					$("#bookingno, #cntrno").parent().unblock();
					if (data.bookinglist.error) {
						toastr["error"](data.bookinglist.error);
						return;
					}

					_bookingList = data.bookinglist;

					if ($(e.target).attr('id') == 'cntrno-search') {
						_ktype = "";
						check_booking();
					}

					if ($(e.target).attr('id') == 'cntrno') {
						apply_container(false);
					}
				},
				error: function(err) {
					console.log(err);
					$("#bookingno, #cntrno").parent().unblock();
					toastr["error"]("Server error at [load_booking]");
				}
			});
		}

		function check_booking() {
			$('#opr').val('');
			$('#sizetype option:not(:eq(0))').remove();
			$('#sizetype option:selected').prop('selected', false);
			$('#sizetype').selectpicker('refresh');
			$('#cntrno-search').attr('data-target', '');

			//add 05-04-2019
			$("#noncont").val(0);
			var bkNo = $('#bookingno').val();
			if (!bkNo) {
				return;
			}

			if (_bookingList.length == 0) {
				$('.toast').remove();
				toastr['info']('Số booking này không đúng!\nVui lòng kiểm tra lại!');
				return;
			}

			if (_bookingList.filter(p => p.Ter_Hold_CHK != '1').length == 0) {
				$.confirm({
					title: 'Cảnh báo!',
					type: 'orange',
					icon: 'fa fa-warning',
					content: 'Tất cả container thuộc booking [' + bkNo + '] đang được giữ tại Cảng!',
					buttons: {
						ok: {
							text: 'Ok',
							btnClass: 'btn-primary',
							keys: ['Enter'],
							action: function() {}
						}
					}
				});
				return;
			}

			//check số lượng booking : các số booking giống nhau, đều hết số lượng đặt chỗ
			if (_bookingList.filter(p => p.BookAmount - p.StackingAmount == 0).length == _bookingList.length) {
				toastr['info']('Booking này đã hết số lượng đặt chỗ!');
				return;
			}

			//check hạn booking, nếu mọi booking giống nhau, nhưng khác size type đều hết hạn
			if (_bookingList.filter(p => new Date(p.ExpDate) < new Date()).length == _bookingList.length) {
				toastr['info']('Booking này đã hết hạn!');
				return;
			}

			if (_bookingList.filter(p => p.BOOK_STATUS == 'C').length >= 1) {
				toastr['info']('Booking này đã huỷ!');
				return;
			}

			$('#opr').val(_bookingList[0].OprID);

			var lcSize = $.unique(_bookingList.map(p => p.LocalSZPT));

			$.each(lcSize, function(idx, val) {
				$('#sizetype').append($("<option></option>").attr("value", val).text(val));
			});

			$('#sizetype option:eq(0)').prop('selected', true);
			$('#sizetype').selectpicker('refresh');

			//CHECK NON CONT///
			if (_bookingList.filter(item => item.CntrNo).length > 0) {
				_bookingFiltered = _bookingList;
				//if is not non cont -> show input cont /hide input noncont
				$('.show-non-cont').addClass('hiden-input');
				$('.hide-non-cont').removeClass('hiden-input');

				//if not non cont -> show check attach service
				$("#attach-srv-chk-container").removeClass("hiden-input");

				$('#cntrno-search').attr('data-target', '#booking-modal');
				$('#booking-detail').waitingLoad();
				var rows = [];
				$.each(_bookingList, function(idx, item) {
					//					if(item.LocalSZPT != $('#sizetype').val()) return;

					//CHECK NẾU TỒN TẠI LỆNH ĐÓNG RÚT
					if (item.CJMode_OUT_CD && ["1", "2", "3"].indexOf(item.ischkCFS) != "-1") {
						return;
					}

					//CHECK NẾU TỒN TẠI LỆNH NÂNG HẠ
					if (item.EIRNo && item.bXNVC != '1') {
						return;
					}

					//check nếu container bị giữ tại cảng
					if (item.Ter_Hold_CHK == '1') {
						return;
					}

					rows.push([
						'', item.CntrNo, item.OprID, item.LocalSZPT, item.cTier ? (item.cBlock + "-" + item.cBay + "-" + item.cRow + "-" + item.cTier) : item.cArea
					]);
				});

				var applied_cntr = tblCont.DataTable().columns(1).data().to$()[0];

				$('#booking-detail').dataTable().fnClearTable();
				if (rows.length > 0) {
					$('#booking-detail').dataTable().fnAddData(rows);
					if (applied_cntr.length > 0) {
						$('#booking-detail').DataTable()
							.rows(function(idx, data, node) {
								if (applied_cntr.indexOf(data[1]) >= 0) {
									return node;
								}
								return false;
							}).select()

					} else {
						$('#booking-detail').DataTable().rows().select();
					}
				}

				$('#booking-modal').modal("show");
			} else {
				// $('#noncont').attr('max', _bookingFiltered[0].BookAmount - _bookingFiltered[0].StackingAmount);
				maxContNum = _bookingList[0].BookAmount - _bookingList[0].StackingAmount;
				//if is non cont -> show input noncont /hide input cont
				$('.show-non-cont').removeClass('hiden-input');
				$('.hide-non-cont').addClass('hiden-input');

				//if is non cont -> hide checkbox attach service
				$("#attach-srv-chk-container").addClass("hiden-input");
				$("#chkServiceAttach").prop("checked", false);
			}
		}

		function filtercontainer() {
			var cntrNo = String($('#cntrno').val()).trim().toUpperCase();
			if (_bookingFiltered.length > 0) {
				if (_bookingFiltered.filter(item => item.CntrNo == cntrNo).length == 0) {
					var temp = _bookingList.filter(item => item.CntrNo == cntrNo && item.BookingNo == _bookingFiltered[0].BookingNo);
					if (temp.length > 0) {
						$.each(temp, function(m, n) {
							_bookingFiltered.push(n);
						});
					} else {
						_bookingFiltered = _bookingList.filter(item => item.CntrNo == cntrNo);
					}
				}
			} else {
				_bookingFiltered = _bookingList.filter(item => item.CntrNo == cntrNo);
			}
		}

		function apply_container(isfiltered) {
			$('#bookingno').val('');
			$('#opr').val('');
			$('#sizetype option:not(:eq(0))').remove();
			$('#sizetype option:selected').prop('selected', false);

			var cntrNo = String($('#cntrno').val()).trim().toUpperCase();
			if (!cntrNo) return;

			if (_bookingList.length == 0) {
				$('.toast').remove();
				toastr['warning']('Số container chưa được đăng ký booking!');
				return;
			}

			if (!isfiltered) {
				filtercontainer();
			}

			if (_bookingFiltered.length == 0) {
				$('.toast').remove();
				toastr['warning']('Số container chưa được đăng ký booking!');
				return;
			}

			if (_bookingFiltered.filter(p => p.CntrNo == cntrNo)[0].Ter_Hold_CHK == '1') {
				$.confirm({
					title: 'Cảnh báo!',
					type: 'orange',
					icon: 'fa fa-warning',
					content: 'Container [' + cntrNo + '] đang bị giữ tại Cảng!',
					buttons: {
						ok: {
							text: 'Ok',
							btnClass: 'btn-primary',
							keys: ['Enter'],
							action: function() {
								_bookingFiltered = _bookingFiltered.filter(p => p.CntrNo != cntrNo);
							}
						}
					}
				});
				return;
			}

			if (_bookingFiltered[0].BookAmount - _bookingFiltered[0].StackingAmount == 0) {
				$('.toast').remove();
				toastr['warning']('Booking này đã hết số lượng đặt chỗ!');
				return;
			}

			var item = _bookingList.filter(item => item.CntrNo == cntrNo)[0];
			if (item.CJMode_OUT_CD && ["1", "2", "3"].indexOf(item.ischkCFS) != "-1") {
				$('.toast').remove();
				toastr['error']('Container đã được cấp lệnh đóng/rút/sang cont số [' + item.SSOderNo + ']');
				return;
			}

			if (item.EIRNo && item.bXNVC != '1') {
				$('.toast').remove();
				toastr['error']('Container đã được cấp lệnh nâng/hạ số [' + item.EIRNo + ']');
				return;
			}

			$('#sizetype').append($("<option></option>").attr("value", item.LocalSZPT)
				.prop("selected", item.CntrNo == cntrNo)
				.text(item.LocalSZPT));

			$('#bookingno').val(_bookingFiltered[0].BookingNo);
			$('#opr').val(_bookingFiltered[0].OprID);
			$('#sizetype').selectpicker('refresh');

			if ($.inArray(cntrNo, selected_cont) == "-1") {
				selected_cont.push(cntrNo);
			}

			apply_booking();
		}

		function apply_booking() {
			var hasrequired = false;
			if ($('.input-required.error').length > 0) {
				hasrequired = true;
			} else {
				hasrequired = $('.input-required').has_required();
				if (hasrequired) {
					$('.toast').remove();
					toastr['error']('Các trường bắt buộc (*) không được để trống!');
				}
			}

			tblCont.waitingLoad();
			var rows = [];
			if (_bookingFiltered.length > 0 && selected_cont.length > 0) {
				var stt = 1;
				//reset list eir
				_lstEir = [];
				for (i = 0; i < _bookingFiltered.length; i++) {
					var item = _bookingFiltered[i];
					if (selected_cont.indexOf(item.CntrNo) == '-1') continue;

					//add item cntr_details to _lst;
					if ($('.input-required.error').length == 0) {
						if (!hasrequired) {
							addCntrToEir(item);
						}
					}
					var status = item.Status == "F" ? "Hàng" : "Rỗng";
					var isLocal = item.IsLocal ? (item.IsLocal == "F" ? "Ngoại" : "Nội") : "";
					rows.push([
						(stt++), item.CntrNo ? item.CntrNo : "", item.BookingNo ? item.BookingNo : "", "Empty Storage", item.OprID ? item.OprID : "", item.LocalSZPT ? item.LocalSZPT : "", item.ISO_SZTP ? item.ISO_SZTP : "", status, item.SealNo ? item.SealNo : "", isLocal, item.CMDWeight ? item.CMDWeight : "", item.CARGO_TYPE ? item.CARGO_TYPE : "", item.Note ? item.Note : "", item.cTLHQ ? item.cTLHQ : ""
					]);
				}
			}
			$('#chk-view-cont').trigger('click');

			tblCont.dataTable().fnClearTable();
			if (rows.length > 0) {
				tblCont.dataTable().fnAddData(rows);
			}

			tblInv.dataTable().fnClearTable();
		}

		function eir_base(item) {
			item['IssueDate'] = $('#ref-date').val(); //*
			item['ExpDate'] = $('#ref-exp-date').val(); //*
			item['NameDD'] = $('#personal-name').val();

			item['IsTruckBarge'] = $('input[name="chkSalan"]').is(':checked') ? "B" : "T";
			item['BARGE_CODE'] = $('#barge-info').val() ? $('#barge-info').val().split('/')[0] : "";
			item['BARGE_YEAR'] = $('#barge-info').val() ? $('#barge-info').val().split('/')[1] : "";
			item['BARGE_CALL_SEQ'] = $('#barge-info').val() ? $('#barge-info').val().split('/')[2] : "";

			item['DMETHOD_CD'] = $('input[name="chkSalan"]').is(':checked') ? "BAI-SALAN" : "BAI-XE";
			item['TruckNo'] = '';

			item['PersonalID'] = $('#cmnd').val();
			item['Note'] = $('#remark').val();
			item['SHIPPER_NAME'] = $('#shipper-name').val(); //*

			if ($('#mail').val()) {
				item['Mail'] = $('#mail').val();
			}

			if ($("#transist").val()) {
				item["Transist"] = $("#transist").val();
			}

			item['PAYER_TYPE'] = getPayerType($('#cusID').val());
			item['CusID'] = $('#cusID').val(); //*

			item['PAYMENT_TYPE'] = $('#payment-type').val();
			//item['PAYMENT_CHK'] = item['PAYMENT_TYPE'] == "C" ? "0" : "1";
			item['PAYMENT_CHK'] = "0"

			item['DELIVERYORDER'] = $("#do").val(); //*
			item['CJMode_CD'] = 'CAPR'; //*
			item['CJModeName'] = 'Cấp rỗng'; //*
			item['Status'] = 'E'; //*

			if (!item['ShipKey']) {
				item['ShipKey'] = 'STORE';
				item['ShipID'] = 'STORAGE';
				item['ShipYear'] = '0000';
				item['ShipVoy'] = '0000';
			}

			if (!item['CARGO_TYPE']) {
				item['CARGO_TYPE'] = item["ISO_SZTP"].charAt(2) == "R" ? "ER" : "MT";
			}

			if (!item['CntrClass']) {
				item['CntrClass'] = "2";
			}

			if (!item['IsLocal']) {
				item['IsLocal'] = "*";
			}

			if (item.EIR_SEQ == 0) {
				item['EIR_SEQ'] = 1;
			}
		}

		function addCntrToEir(inputItem) {

			var item = $.extend({}, inputItem);

			eir_base(item);

			deleteItemInArray(item, ["ContCondition", "isAssignCntr", "EIRNo", "RowguidCntrDetails", "ischkCFS", "CJMode_OUT_CD", "SSOderNo", "DateOut", "TERMINAL_CD", "BookingDate", "BookAmount", "StackingAmount", "bXNVC", "BLNo"]);

			_lstEir.push(item);

			var temp = $.extend({}, item);
			if ($('#chkFCL-attach').is(':checked')) {
				FCL_eir_base(temp);

				//udpate 2018-12-17
				temp["cBlock"] = temp["cBay"] = temp["cRow"] = temp["cTier"] = temp["BLNo"] = null;

				_lstEir.push(temp);
			}
		}

		function FCL_eir_base(item) {
			item['ShipKey'] = $('#FCL-shipid').data('shipkey');
			var ship = _listShip.filter(p => p.ShipKey == item['ShipKey'])[0];
			item['ShipID'] = ship.ShipID;
			item['ImVoy'] = ship.ImVoy;
			item['ExVoy'] = ship.ExVoy;
			item['ShipYear'] = ship.ShipYear;
			item['ShipVoy'] = ship.ShipVoy;
			item['BerthDate'] = ship.BerthDate;
			item['LaneID'] = ship.LaneID;
			item['BerthDate'] = ship.BerthDate;
			item['CARGO_TYPE'] = $('#FCL-cargotype').val();
			item['ExpDate'] = $('#FCL-exp-date').val();
			item['Note'] = $('#FCL-note').val().replace(/'|"/g, "");
			item['POD'] = $('#FCL-pod').val();
			item['IsLocal'] = $('#FCL-islocal').val();
			item['CMDWeight'] = $('#FCL-cmdweight').val();

			item['DMETHOD_CD'] = 'BAI-XE';
			item['TruckNo'] = '';

			item['CntrClass'] = '3'; //*
			item['Status'] = 'F'; //*
			item['CJMode_CD'] = 'HBAI';
			item['CJModeName'] = 'Hạ bãi';
			deleteItemInArray(item, ["Rowguid_CntrDetails"]);
		}

		function getPayerType(id) {
			if (payers.length == 0) return "";
			var py = payers.filter(p => p.CusID == id);
			if (py.length == 0) return "";
			if (py[0].IsOpr == "1") return "SHP";
			if (py[0].IsAgency == "1") return "SHA";
			if (py[0].IsOwner == "1") return "CNS";
			if (py[0].IsLogis == "1") return "FWD";
			if (py[0].IsTrans == "1") return "TRK";
			if (py[0].IsOther == "1") return "DIF";
			return "";
		}

		function loadpayment() {
			if (_lstEir.length == 0) {
				tblInv.dataTable().fnClearTable();
				return;
			}
			if ($('.input-required.error').length > 0) {
				tblInv.dataTable().fnClearTable();
				return;
			}
			if ($('.input-required').has_required()) {
				tblInv.dataTable().fnClearTable();
				$('.toast').remove();
				toastr['error']('Các trường bắt buộc (*) không được để trống!');
				return;
			}

			var formdata = {
				'action': 'view',
				'act': 'load_payment',
				'cusID': $('#taxcode').val(),
				'list': JSON.stringify(_lstEir)
			};

			if ($("#chkServiceAttach").is(":checked")) {
				addCntrToAttachSRV();

				var nonAttach = _lstAttachService.filter(p => p.CJMode_CD != "SDD" && p.CJMode_CD != "LBC");
				var sdd = _lstAttachService.filter(p => p.CJMode_CD == "SDD")[0];
				var lbc = _lstAttachService.filter(p => p.CJMode_CD == "LBC");

				if (nonAttach && nonAttach.length > 0) {
					formdata['nonAttach'] = JSON.stringify(nonAttach);
				}

				if (sdd && sdd.length > 0) {
					formdata['sdd'] = JSON.stringify(sdd);
				}

				if (lbc && lbc.length > 0) {
					formdata['lbc'] = JSON.stringify(lbc);
				}
			}

			tblInv.waitingLoad();

			$.ajax({
				url: "<?= site_url(md5('Task') . '/' . md5('tskEmptyPickup')); ?>",
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

					if (data.no_tariff_end) {
						$(".toast").remove();
						toastr["error"](data.no_tariff_end);
						tblInv.dataTable().fnClearTable();
						return;
					}

					if (data.no_tariff) {
						$(".toast").remove();
						toastr["warning"](data.no_tariff);
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
							stt = 1;
						for (i = 0; i < lst.length; i++) {
							var cntrclass = lst[i].CntrClass == 1 ? "Nhập" : (lst[i].CntrClass == 4 ? "Nhập chuyển cảng" : "");
							var status = lst[i].Status == "F" ? "Hàng" : "Rỗng";
							var isLocal = lst[i].IsLocal == "F" ? "Ngoại" : (lst[i].IsLocal == "L" ? "Nội" : "");
							rows.push([
								(stt++), lst[i].DraftInvoice, lst[i].OrderNo ? lst[i].OrderNo : "", lst[i].TariffCode, lst[i].TariffDescription, lst[i].Unit, lst[i].JobMode, lst[i].DeliveryMethod, lst[i].Cargotype, lst[i].ISO_SZTP, lst[i].FE, lst[i].IsLocal, lst[i].Quantity, lst[i].StandardTariff, 0, lst[i].DiscountTariff, lst[i].DiscountedTariff, lst[i].Amount, lst[i].VatRate, lst[i].VATAmount, lst[i].SubAmount, lst[i].Currency, lst[i].IX_CD, lst[i].CNTR_JOB_TYPE, lst[i].VAT_CHK, lst[i].Remark || '', lst[i].TRF_DESC_MORE || ''
							]);
						}
					}
					if (rows.length > 0) {
						var n = rows.length;
						rows.push([
							n, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', data.SUM_AMT, '', data.SUM_VAT_AMT, data.SUM_SUB_AMT, '', '', '', '', '', ''
						]);
						$('#AMOUNT').text($.formatNumber(data.SUM_AMT, {
							format: "#,###",
							locale: "us"
						}));
						$('#DIS_AMT').text($.formatNumber(data.SUM_DIS_AMT, {
							format: "#,###",
							locale: "us"
						}));
						$('#VAT').text($.formatNumber(data.SUM_VAT_AMT, {
							format: "#,###",
							locale: "us"
						}));
						$('#TAMOUNT').text($.formatNumber(data.SUM_SUB_AMT, {
							format: "#,###",
							locale: "us"
						}));
					}

					tblInv.dataTable().fnClearTable();
					if (rows.length > 0) {
						tblInv.dataTable().fnAddData(rows);
					}
				},
				error: function(err) {
					$(".toast").remove();
					toastr["error"]("ERROR!");

					tblInv.dataTable().fnClearTable();
					console.log(err);
				}
			});
		}

		function load_payer() {
			var tblPayer = $('#search-payer');
			tblPayer.waitingLoad();

			$.ajax({
				url: "<?= site_url(md5('Task') . '/' . md5('tskEmptyPickup')); ?>",
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

		function clearPayer() {
			$("#cusID").val('');
			$('#taxcode').val('');

			$("#payer-name").text(" [Tên đối tượng thanh toán]");
			$("#payer-addr").text(" [Địa chỉ]");
			$("#payment-type").val('M').selectpicker('refresh').trigger('change');

			$('#p-taxcode, #p-payername, #p-payer-addr').text('');
		}

		$("#cmnd, #personal-name, #mail").on('input', function(e) {
			$(e.target).attr('user-input', $(e.target).val() ? 1 : 0);
		});

		function fillPayer() {
			var py = payers.filter(p => p.VAT_CD == $('#taxcode').val() && p.CusID == $("#cusID").val());
			if (py.length > 0) { //fa-check-square
				$('#p-taxcode').text($('#taxcode').val());
				$('#payer-name, #p-payername').text(py[0].CusName);
				$('#payer-addr, #p-payer-addr').text(py[0].Address);
				$('#payment-type').val(py[0].CusType).selectpicker('refresh').trigger('change');
				if (py[0].CusType == "M") {
					$('#p-money i').removeClass('fa-square').addClass('fa-check-square');
					$('#p-credit i').removeClass('fa-check-square').addClass('fa-square');
				} else {
					$('#p-money i').removeClass('fa-check-square').addClass('fa-square');
					$('#p-credit i').removeClass('fa-square').addClass('fa-check-square');
				}

				$("#cmnd[user-input=0]").val(py[0].PersonalID);
				$("#personal-name[user-input=0]").val(py[0].NameDD);
				if (py[0].Email) {
					$("#mail[user-input=0]").val(py[0].Email);
				}

				if (py[0].EMAIL_DD && py[0].EMAIL_DD != py[0].Email) {
					$("#mail").val($("#mail").val() + ',' + py[0].EMAIL_DD);
				}

				$("#taxcode").removeClass("error");
			}

			return py.length > 0;
		}

		function findPayer(str) {
			$('#taxcode').parent().blockUI();
			$.ajax({
				url: "<?= site_url(md5('Task') . '/' . md5('tskEmptyPickup')); ?>",
				dataType: 'json',
				data: {
					action: 'view',
					act: 'search_cus_by_tax',
					taxCode: str
				},
				type: 'POST',
				success: function(data) {
					$('#taxcode').parent().unblock();
					if (data.deny) {
						$('#payment-modal').find('.modal-content').unblock();
						toastr["error"](data.deny);
						return;
					}

					if (data.error) {
						$(".toast").remove();
						toastr["error"](data.error);
						return;
					}

					if (!data['cus']) {
						$(".toast").remove();
						toastr["error"]('Mã số thuế này không tồn tại trong hệ thống! Vui lòng nhập lại hoặc tạo mới');
						return;
					}

					if (payers.length == 0) {
						payers.push(data.cus);
					}

					$('#taxcode').val(data.cus.VAT_CD);
					$('#cusID').val(data.cus.CusID);
					$('#taxcode').trigger('change');

				},
				error: function(err) {
					$('#taxcode').parent().unblock();
					$(".toast").remove();
					toastr["error"]('Xảy ra lỗi');
					console.log(err);
				}
			});
		}

		//PUBLISH INV
		function publishInv() {
			$('#payment-modal').find('.modal-content').blockUI();

			var datas = getInvDraftDetail();
			var formData = {
				cusTaxCode: $('#p-taxcode').text(),
				cusAddr: $('#p-payer-addr').text(),
				cusName: $('#p-payername').text(),
				cusEmail: $('#mail').val(),
				sum_amount: $('#AMOUNT').text(),
				vat_amount: $('#VAT').text(),
				total_amount: $('#TAMOUNT').text(),
				paymentMethod: $('#paymentMethod').val(), //add payment method
				shipKey: _lstEir[0]['ShipKey'],
				datas: datas
			};

			$.ajax({
				url: "<?= site_url(md5('InvoiceManagement') . '/' . md5('importAndPublish')); ?>",
				dataType: 'json',
				data: formData,
				type: 'POST',
				success: function(data) {

					if (data.error) {
						$(".toast").remove();
						toastr["error"](data.error);
						return;
					}

					saveData(data);
				},
				error: function(err) {
					$('#payment-modal').find('.modal-content').unblock();
					console.log(err);
				}
			});
		}
		//SAVE DATA
		function saveData(invInfo) {
			var drDetail = getInvDraftDetail();
			var drTotal = {};
			$.each($('#INV_DRAFT_TOTAL').find('span'), function(idx, item) {
				drTotal[$(item).attr('id')] = $(item).text();
			});

			var countBySize = {};
			tblCont.DataTable().rows(function(idx, data, node) {
				return countBySize[data[_colCont.indexOf("LocalSZPT")]] = countBySize[data[_colCont.indexOf("LocalSZPT")]] ?
					countBySize[data[_colCont.indexOf("LocalSZPT")]] + 1 :
					1;
			});

			$.each(Object.keys(countBySize), function(idx, sz) {
				countBySize[sz] += parseInt(_bookingList.filter(p => p.LocalSZPT == sz).map(x => x.StackingAmount)[0]);
			});

			_lstEir.map(x => x.Note = $('#remark').val());
			_lstEir.map(x => x.SHIPPER_NAME = $('#shipper-name').val());
			_lstEir.map(x => x.PersonalID = $('#cmnd').val());
			_lstEir.map(x => x.NameDD = $('#personal-name').val());
			_lstEir.map(x => x.Mail = $('#mail').val());

			var publish_opt_checked = $("input[name='publish-opt']:checked").val();
			var formData = {
				'action': 'save',
				'data': {
					'pubType': publish_opt_checked ? publish_opt_checked : "credit",
					'stackingAmount': countBySize,
					'eir': _lstEir,
					'draft_detail': drDetail,
					'draft_total': drTotal
				}
			};

			//add payment method: to draft Total
			formData.data.draft_total['ACC_CD'] = formData.data.pubType == 'credit' ? 'TM/CK' : $('#paymentMethod').val();

			if (formData.data.pubType != 'credit' && (!drDetail || drDetail.length == 0)) {
				$('#payment-modal').find('.modal-content').unblock();
				$('.toast').remove();
				toastr['warning']('Chưa có thông tin tính cước!');
				return;
			}

			//get attach service for save
			if (_lstAttachService.length > 0) {
				formData['data']['odr'] = _lstAttachService; //JSON.stringify();
			}
			//get attach service for save

			if (typeof invInfo !== "undefined" && invInfo !== null) {
				formData.data["invInfo"] = invInfo;
			} else {
				//trg hop không phải xuất hóa đơn điện tử, block popup ở đây
				$('#payment-modal').find('.modal-content').blockUI();
			}

			$.ajax({
				url: "<?= site_url(md5('Task') . '/' . md5('tskEmptyPickup')); ?>",
				dataType: 'json',
				data: formData,
				type: 'POST',
				success: function(data) {

					if (data.deny) {
						$('#payment-modal').find('.modal-content').unblock();
						toastr["error"](data.deny);
						return;
					}

					if (data.non_invInfo) {
						$('#payment-modal').find('.modal-content').unblock();
						toastr["error"](data.non_invInfo);
						return;
					}

					if (data.isDup) {
						$('#payment-modal').find('.modal-content').unblock();
						toastr["error"]("Hóa đơn hiện tại đã tồn tại trong hệ thống! Kiểm tra lại!");
						return;
					}

					if (data.invInfo) {
						var form = document.createElement("form");
						form.setAttribute("method", "post");
						form.setAttribute("action", "<?= site_url(md5('Task') . '/' . md5('payment_success')); ?>");

						var input = document.createElement('input');
						input.type = 'hidden';
						input.name = "invInfo";
						input.value = JSON.stringify(data.invInfo);
						form.appendChild(input);

						document.body.appendChild(form);
						form.submit();
						document.body.removeChild(form);
					} else if (data.dftInfo) {
						var form = document.createElement("form");
						form.setAttribute("method", "post");
						form.setAttribute("action", "<?= site_url(md5('Task') . '/' . md5('draft_success')); ?>");

						var input = document.createElement('input');
						input.type = 'hidden';
						input.name = "dftInfo";
						input.value = JSON.stringify(data.dftInfo);
						form.appendChild(input);

						document.body.appendChild(form);
						form.submit();
						document.body.removeChild(form);
					} else {
						toastr["success"]("Lưu dữ liệu thành công!");
						location.reload(true);
					}
				},
				error: function(xhr, status, error) {
					console.log(xhr);
					$('.toast').remove();
					$('#payment-modal').find('.modal-content').unblock();
					toastr['error'](error);
				}
			});
		}

		function getInvDraftDetail() {
			var rows = [];
			tblInv.find('tbody tr:not(.row-total)').each(function() {
				var nrows = [];
				var ntds = $(this).find('td:not(.dataTables_empty)');
				if (ntds.length > 0) {
					ntds.each(function(td) {
						nrows.push($(this).text() == "null" ? "" : $(this).text());
					});
					rows.push(nrows);
				}
			});

			var drd = [];
			if (rows.length == 0) return [];
			$.each(rows, function(idx, item) {
				var temp = {};
				for (var i = 1; i <= _colsPayment.length - 1; i++) {
					temp[_colsPayment[i]] = item[i];
				}
				// temp['Remark'] = selected_cont.toString();
				drd.push(temp);
			});
			return drd;
		}

		function addCntrToAttachSRV() {
			_lstAttachService = [];
			var attachSrvSelected = _attachServicesChecker.filter(p => p.Select == 1);

			if (attachSrvSelected.length > 0) {
				$.each(attachSrvSelected, function(index, elem) {
					var finds = _lstEir.filter(p => p.CntrNo == elem["CntrNo"])[0];

					var item = $.extend({}, finds);

					item['CJMode_CD'] = elem["CJMode_CD"];

					item['PTI_Hour'] = 0;

					item['cBlock1'] = item['cBlock'];
					item['cBay1'] = item['cBay'];
					item['cRow1'] = item['cRow'];
					item['cTier1'] = item['cTier'];

					deleteItemInArray(item, ["cBlock", "cBay", "cRow", "cTier", "CJModeName", "CLASS", "ContCondition", "isAssignCntr", "EIRNo", "DateOut", "UNNO", "TERMINAL_CD", "IsTruckBarge", "TruckNo", "BookingDate", "BookAmount", "StackingAmount"]);

					_lstAttachService.push(item);
				});
			}
		}

		function deleteItemInArray(item, arrColName) {
			$.each(arrColName, function(idx, colname) {
				delete item[colname];
			});
		}

		function load_attach_srv() {
			$('#col-attach-service').blockUI();
			tblAttach.waitingLoad();
			var formdata = {
				'action': 'view',
				'act': 'load_attach_srv',
				'order_type': 'CAPR'
			};

			$.ajax({
				url: "<?= site_url(md5('Task') . '/' . md5('tskEmptyPickup')); ?>",
				dataType: 'json',
				data: formdata,
				type: 'POST',
				success: function(data) {
					$('#col-attach-service').unblock();

					var rows = [];
					if (data.lists && data.lists.length > 0) {
						for (i = 0; i < data.lists.length; i++) {
							var r = [];

							$.each(_colsAttachServices, function(indx, colname) {
								if (colname == "Select") {
									var xxx = '<label class="checkbox checkbox-primary"><input type="checkbox" value="0"><span class="input-span"></span></label>';
									r.push(xxx);
								} else if (colname == "Checker") {
									r.push(0);
								} else {
									r.push(data.lists[i][colname] ? data.lists[i][colname] : "");
								}
							});
							rows.push(r);
						}
					}

					tblAttach.dataTable().fnClearTable();
					if (rows.length > 0) {
						tblAttach.dataTable().fnAddData(rows);
					}

				},
				error: function(err) {
					$('#col-attach-service').unblock();
					tblAttach.dataTable().fnClearTable();
					console.log(err);
				}
			});
		}

		function loadAttachData(rowIndexes) {

			var cellCheked = tblAttach.find("tbody tr")
				.find('input[type="checkbox"]:checked').closest("td");

			$.each(cellCheked, function(idx, cell) {
				tblAttach.DataTable().cell(cell).data('<label class="checkbox checkbox-primary"><input type="checkbox" value="0"><span class="input-span"></span></label>');
			});

			tblAttach.DataTable().draw(false);

			if (!rowIndexes || rowIndexes.length == 0) {
				return;
			}

			var allCntrNoSelected = tblCont.DataTable().rows(rowIndexes).data().toArray().map(p => p[_colCont.indexOf("CntrNo")]);

			if (_attachServicesChecker.length > 0) {
				// var indexCells = [];
				$.each(tblAttach.find("tbody tr"), function() {
					var cjmode = $(this).find("td:eq(" + _colsAttachServices.indexOf("CjMode_CD") + ")").text();

					var itemChecked = _attachServicesChecker.filter(p => allCntrNoSelected.indexOf(p.CntrNo) != -1 && p.CJMode_CD == cjmode);
					if (itemChecked && itemChecked.length == allCntrNoSelected.length) {
						var cellSelect = $(this).find('td:eq(' + _colsAttachServices.indexOf("Select") + ')');

						cellSelect.find('input[type="checkbox"]')
							.val(itemChecked[0].Select)
							.prop('checked', itemChecked[0].Select == 1 ? true : false);
					}
				});
			}
		}

	});

	function search_ship() {
		_listShip.length = 0;
		$("#search-ship").dataTable().fnClearTable();
		$("#search-ship").waitingLoad();
		var formdata = {
			'action': 'view',
			'act': 'searh_ship',
			'arrStatus': $('input[name="shipArrStatus"]:checked').val(),
			'shipyear': $('#cb-searh-year').val(),
			'shipname': $('#search-ship-name').val()
		};
		$.ajax({
			url: "<?= site_url(md5('Task') . '/' . md5('tskEmptyPickup')); ?>",
			dataType: 'json',
			data: formdata,
			type: 'POST',
			success: function(data) {
				var rows = [];
				$("#search-ship").dataTable().fnClearTable();
				if (data.vsls.length > 0) {
					_listShip = data.vsls;
					for (i = 0; i < data.vsls.length; i++) {
						rows.push([
							data.vsls[i].ShipID, (i + 1), data.vsls[i].ShipName, data.vsls[i].ImVoy, data.vsls[i].ExVoy, getDateTime(data.vsls[i].ETB), data.vsls[i].ShipKey,
							getDateTime(data.vsls[i].BerthDate), data.vsls[i].ShipYear, data.vsls[i].ShipVoy, data.vsls[i].YARD_CLOSE ? data.vsls[i].YARD_CLOSE : "", data.vsls[i].LaneID
						]);
					}
				}

				$('#search-ship').dataTable().fnClearTable();
				if (rows.length > 0) {
					$('#search-ship').dataTable().fnAddData(rows);
				}
			},
			error: function(err) {
				_listShip.length = 0;
				$("#search-ship").dataTable().fnClearTable();
				console.log(err);
			}
		});
	}

	function save_new_payer(formData) {
		$(".add-payer-container").blockUI();
		$.ajax({
			url: "<?= site_url(md5('Task') . '/' . md5('tskEmptyPickup')); ?>",
			dataType: 'json',
			data: formData,
			type: 'POST',
			success: function(data) {

				$(".add-payer-container").unblock();

				if (data.deny) {
					toastr["error"](data.deny);
					return;
				}

				if (data.error) {
					toastr["error"](data.error);
					return;
				}

				toastr["success"]("Thêm mới thành công!");
				$(".add-payer-container").find("input").val("");

				var tblPayer = $('#search-payer');
				var dtTblPayer = tblPayer.DataTable();
				if (data.saveType == "add") {
					toastr["success"]("Thêm mới thành công!");

					var rowcount = dtTblPayer.rows().count();
					var row = [
						rowcount + 1, formData.taxCode, formData.taxCode, formData.cusName, formData.address, 'Thu ngay'
					];

					tblPayer.dataTable().fnAddData(row);

					dtTblPayer.page("last").draw("page");

					var lastRow = dtTblPayer.row(':last', {
						order: 'applied'
					});

					dtTblPayer.rows('.m-row-selected').nodes().to$().removeClass("m-row-selected");

					$(lastRow.node()).addClass("m-row-selected");

					dtTblPayer.search(formData.taxCode).draw(false);

					payers.push({
						Address: formData.address,
						CusID: formData.taxCode,
						CusName: formData.cusName,
						CusType: "M",
						IsAgency: "0",
						IsLogis: "0",
						IsOpr: "0",
						IsOther: "0",
						IsOwner: "1",
						IsTrans: "0",
						VAT_CD: formData.taxCode
					});
				}

				if (data.saveType == "edit") {
					toastr["success"]("Cập nhật thành công!");
					var indx = payers.findIndex(x => x.VAT_CD == formData.taxCode && x.CusType == 'M');
					payers[indx]["CusName"] = formData.cusName;
					payers[indx]["Address"] = formData.address;

					var indexes = dtTblPayer.rows().eq(0).filter(function(rowIdx) {
						return dtTblPayer.cell(rowIdx, 2).data() === formData.taxCode &&
							dtTblPayer.cell(rowIdx, 5).data() === 'Thu ngay';
					});

					if (indexes.toArray().length > 0) {
						var firstIdx = indexes.toArray()[0];

						dtTblPayer.rows('.m-row-selected').nodes().to$().removeClass("m-row-selected");
						dtTblPayer.rows(firstIdx).nodes().to$().addClass("m-row-selected");

						dtTblPayer.cell(firstIdx, 3).data(formData.cusName);
						dtTblPayer.cell(firstIdx, 4).data(formData.address);
						dtTblPayer.search(formData.taxCode);
						dtTblPayer.draw(false);
					}
				}

			},
			error: function(xhr, status, error) {
				console.log(xhr);

				$(".add-payer-container").unblock();
				$('.toast').remove();
				toastr['error']("Có lỗi xảy ra khi lưu dữ liệu! Vui lòng liên hệ KTV! ");
			}
		});
	}
</script>

<script src="<?= base_url('assets/vendors/dataTables/extensions/select.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/bootstrap-select/dist/js/bootstrap-select.min.js'); ?>"></script>
<!--format number-->
<script src="<?= base_url('assets/js/jshashtable-2.1.js'); ?>"></script>
<script src="<?= base_url('assets/js/jquery.numberformatter-1.2.3.min.js'); ?>"></script>