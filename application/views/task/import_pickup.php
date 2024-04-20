<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<link href="<?= base_url('assets/vendors/bootstrap-select/dist/css/bootstrap-select.min.css'); ?>" rel="stylesheet" />
<link href="<?= base_url('assets/vendors/dataTables/extensions/select.dataTables.min.css'); ?>" rel="stylesheet" />

<style>
	.form-group {
		margin-bottom: .5rem !important;
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

	#payer-modal .dataTables_filter,
	#bill-modal .dataTables_filter {
		padding-left: 10px !important;
	}

	table.dataTable tr.selected td.select-checkbox::after {
		color: black !important;
		margin-top: -28px !important;
	}
</style>
<div class="row" style="font-size: 12px!important;">
	<div class="col-xl-12">
		<div class="ibox collapsible-box">
			<i class="la la-angle-double-up dock-right"></i>
			<div class="ibox-head">
				<div class="ibox-title">LỆNH CẤP CONTAINER HÀNG</div>
			</div>
			<div class="ibox-body pt-3 pb-2 bg-f9 border-e">
				<div class="row">
					<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<h5 class="text-primary">Thông tin lệnh</h5>
					</div>
				</div>
				<div class="row my-box pb-1 ">
					<div class="col-xl-7 col-lg-7 col-md-12 col-sm-12 col-xs-12 mt-3">
						<div class="row" id="row-transfer-left">
							<div id="static-cont-left" class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-xs-12">
								<div class="row form-group" hidden>
									<label class="col-sm-4 col-form-label">Phương án</label>
									<div class="col-sm-8">
										<select id="cjmode" class="selectpicker" data-style="btn-default btn-sm" data-width="100%">
											<option value="LAYN" selected>Lấy Nguyên</option>
										</select>
									</div>
								</div>
								<div class="row form-group">
									<label class="col-sm-4 col-form-label">Ngày lệnh</label>
									<div class="col-sm-8 input-group input-group-sm">
										<div class="input-group">
											<input class="form-control form-control-sm input-required" id="ref-date" type="text" placeholder="Ngày lệnh" readonly>
										</div>
									</div>
								</div>
								<div class="row form-group">
									<label class="col-sm-4 col-form-label">Hạn lệnh *</label>
									<div class="col-sm-8 input-group input-group-sm">
										<div class="input-group">
											<input class="form-control form-control-sm input-required" id="ref-exp-date" type="text" placeholder="Hạn lệnh" autocomplete="off">
											<span class="input-group-addon bg-white btn text-danger" title="Bỏ chọn ngày" style="padding: 0 .5rem"><i class="fa fa-times"></i></span>
										</div>
									</div>
								</div>
								<div class="row form-group">
									<label class="col-sm-4 col-form-label">Hạn lưu cont *</label>
									<div class="col-sm-8 input-group input-group-sm">
										<div class="input-group">
											<input class="form-control form-control-sm input-required" id="opr-exp-date" type="text" placeholder="Hạn lưu cont" autocomplete="off">
											<span class="input-group-addon bg-white btn text-danger" title="Bỏ chọn ngày" style="padding: 0 .5rem"><i class="fa fa-times"></i></span>
										</div>
									</div>
								</div>

								<div id="MT-return-content" class="row form-group">
									<div class="col-sm-4 col-form-label pr-0">
										<label class="checkbox checkbox-warning text-warning">
											<input type="checkbox" name="chkMT-return" id="chkMT-return" value="0">
											<span class="input-span"></span>
											Lệnh trả rỗng
										</label>
									</div>
									<div class="col-sm-8">
										<div class="input-group">
											<input class="form-control form-control-sm MT-change-required" id="MT-retlocation" type="text" placeholder="Nơi trả rỗng" maxlength="199">
										</div>
									</div>
								</div>

								<div class="row form-group MT-toggle" style="display: none;">
									<label class="col-sm-4 col-form-label">Hạn trả</label>
									<div class="col-sm-8 input-group input-group-sm">
										<div class="input-group">
											<input class="form-control form-control-sm" id="MT-exp-date" type="text" placeholder="Hạn trả">
										</div>
									</div>
								</div>
								<div class="row form-group MT-toggle" style="display: none;">
									<label class="col-sm-4 col-form-label">Ghi chú</label>
									<div class="col-sm-8 input-group input-group-sm">
										<input class="form-control form-control-sm" id="MT-remark" type="text" placeholder="Ghi chú trả rỗng">
									</div>
								</div>

							</div>
							<div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-xs-12">
								<div class="row form-group" style="border-bottom: 1px solid #eee">
									<div class="col-12 col-form-label">
										<label class="checkbox checkbox-blue">
											<input type="checkbox" name="isGiaoCntrXuat" id="isGiaoCntrXuat">
											<span class="input-span"></span>
											Giao container hàng xuất
										</label>
									</div>
								</div>
								<div class="row form-group">
									<label class="col-sm-4 col-form-label">D/O</label>
									<div class="col-sm-8 input-group input-group-sm input-required">
										<input class="form-control form-control-sm " id="do" type="text" placeholder="D/O">
									</div>
								</div>

								<div id="billno-content" class="row form-group">
									<label id="billno-label" class="col-sm-4 col-form-label pr-0">Số vận đơn</label>
									<div class="col-sm-8 input-group input-group-sm">
										<div class="input-group">
											<input class="form-control form-control-sm" id="billno" type="text" placeholder="Số vận đơn" style="text-transform: uppercase">
										</div>
									</div>
								</div>

								<div class="row form-group">
									<label class="col-sm-4 col-form-label">Số container</label>
									<div class="col-sm-8 input-group input-group-sm">
										<div class="input-group">
											<input class="form-control form-control-sm" id="cntrno" type="text" placeholder="Container No." style="text-transform: uppercase">
											<span class="input-group-addon bg-white btn text-warning" title="Chọn" data-toggle="modal" data-target="" style="padding: 0 .5rem"><i class="fa fa-search"></i></span>
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
										<div id="barge-ischecked" class="input-group unchecked-Salan">
											<input class="form-control form-control-sm" id="barge-info" type="text" placeholder="Mã/Năm/Chuyến" readonly>
											<span class="input-group-addon bg-white btn text-warning" id="btn-search-barge" data-toggle="modal" data-target="#barge-modal" title="Chọn" style="padding: 0 .5rem"><i class="fa fa-search"></i></span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-5 col-lg-5 col-md-12 col-sm-12 col-xs-12 mt-3">
						<div class="row">
							<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<div class="row form-group" style="border-bottom: 1px solid #eee">
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
							<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12" id="col-transfer">
								<div class="row form-group">
									<label class="col-sm-3 col-form-label" title="Chủ hàng">Chủ hàng *</label>
									<div class="col-sm-9">
										<input class="form-control form-control-sm input-required" id="shipper-name" type="text" placeholder="Chủ hàng">
									</div>
								</div>
								<div class="row form-group">
									<label class="col-sm-3 col-form-label">Người đại diện</label>
									<div class="col-sm-9 input-group">
										<input class="form-control form-control-sm mr-1" id="cmnd" user-input=0 type="text" placeholder="Số CMND /Số ĐT" maxlength="20">
										<input class="form-control form-control-sm mr-1" id="personal-name" user-input=0 type="text" placeholder="Tên người đại diện" maxlength="50">
										<input class="form-control form-control-sm" id="mail" user-input=0 type="text" placeholder="Địa chỉ Email" style="width: 20%" maxlength="100">
									</div>
								</div>
								<div class="row form-group">
									<label class="col-sm-3 col-form-label">Ghi chú</label>
									<div class="col-sm-9 input-group input-group-sm">
										<input class="form-control form-control-sm" id="remark" type="text" placeholder="Ghi chú">
									</div>
								</div>
								<div class="row form-group">
									<label class="col-sm-3 col-form-label pr-0">Chuyển tải | Cảng GN</label>
									<div class="col-sm-9 input-group input-group-sm">
										<select id="transist" class="selectpicker mr-1" data-style="btn-default btn-sm" data-width="45%">
											<option value="" selected>--[Chuyển tải]--</option>
											<?php if (isset($transists) && count($transists) > 0) {
												foreach ($transists as $item) { ?>
													<option value="<?= $item['Transit_CD'] ?>"><?= $item['Transit_CD'] . ' : ' . $item['Transit_Name'] ?></option>
											<?php }
											} ?>
										</select>
										<input class="form-control form-control-sm" id="terminal-cd" type="text" placeholder="Cảng giao nhận" maxlength="199">
									</div>
								</div>
							</div>

						</div>
					</div>
				</div>

				<div class="row mt-2 pt-2 my-box">
					<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="row">
							<div class="col-sm-4">
								<div class="row form-group">
									<label class="col-sm-4 col-form-label" title="Đối tượng thanh toán">Đối tượng TT *</label>
									<div class="col-sm-7" style="margin-left: calc(2px - 1.25vw);">
										<div class="input-group">
											<input class="form-control form-control-sm input-required" id="taxcode" placeholder="Đang nạp ..." type="text">
											<span class="input-group-addon bg-white btn mobile-hiden text-warning" style="padding: 0 .5rem" title="Chọn đối tượng thanh toán" data-toggle="modal" data-target="#payer-modal">
												<i class="ti-search"></i>
											</span>
										</div>
									</div>
									<input class="hiden-input" id="cusID" readonly>
								</div>
							</div>
							<div class="col-sm-8 col-form-label mt-1">
								<i class="fa fa-id-card" style="font-size: 15px!important; margin-left: calc(-3vw - 6px);"></i>-<span id="payer-name"> [Tên đối tượng thanh toán]</span>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-4">
								<div class="row form-group">
									<label class="col-sm-4 col-form-label" title="Hình thức thanh toán">Hình thức TT *</label>
									<div class="col-sm-7" style="margin-left: calc(2px - 1.25vw);">
										<select id="payment-type" class="selectpicker" data-style="btn-default btn-sm" data-width="100%">
											<option value="M">THU NGAY</option>
											<option value="C">THU SAU</option>
										</select>
									</div>
								</div>
							</div>
							<div class="col-sm-8 col-form-label mt-1">
								<i class="fa fa-home" style="font-size: 15px!important;margin-left: calc(-3vw - 6px);"></i>-<span id="payer-addr"> [Địa chỉ]</span>
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
							<button id="show-ps-notify" class="btn btn-outline-secondary btn-sm hiden-input" data-toggle="modal" data-target="#notify-modal">
								<span class="btn-icon"><i class="fa fa-info"></i>Chi tiết cước</span>
							</button>
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
					<table id="tbl-conts" class="table table-striped display nowrap" cellspacing="0">
						<thead>
							<tr>
								<th>STT</th>
								<th>Số Container</th>
								<th>BL / BK</th>
								<th>Hướng</th>
								<th>Hãng khai thác</th>
								<th>Kích cỡ nội bộ</th>
								<th>Kích cỡ ISO</th>
								<th>Hàng/Rỗng</th>
								<th>Số chì</th>
								<th>Nội/ngoại</th>
								<th>Trọng lượng</th>
								<th>Loại hàng</th>
								<th>Nhiệt độ</th>
								<th>Mã nguy hiểm</th>
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
								<th>Hàng/rỗng </th>
								<th>Nội/ngoại</th>
								<th>Số lượng</th>
								<th>Đơn giá</th>
								<th>CK (%)</th>
								<th>Đơn giá CK</th>
								<th>Đơn giá sau CK</th>
								<th>Thành tiền</th>
								<th>VAT (%)</th>
								<th>Tiền VAT</th>
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
		</div>
	</div>
</div>
<!--select barge-->
<div class="modal fade" id="barge-modal" tabindex="-1" role="dialog" aria-labelledby="groups-modalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-mw" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="groups-modalLabel">Chọn xà lan</h5>
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

<!--payment modal-->
<div class="modal fade" id="payment-modal" tabindex="-1" data-whatever="id" data-backdrop="static">
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
									<button id="change-ssinvno" class="btn btn-outline-secondary btn-sm mr-1" data-toggle="modal" data-target="#change-ssinv-modal" title="Khai báo số hóa đơn sử dụng tiếp theo">
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
<!--bill modal-->
<div class="modal fade" id="bill-modal" tabindex="-1" role="dialog" aria-labelledby="groups-modalLabel" aria-hidden="true" data-whatever="id">
	<div class="modal-dialog" role="document" style="min-width: 750px!important">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title text-primary" id="groups-modalLabel">Thông tin vận đơn</h5>
			</div>
			<div class="modal-body px-0">
				<div class="table-responsive">
					<table id="bill-detail" class="table table-striped display nowrap table-popup" cellspacing="0" style="width: 99.8%">
						<thead>
							<tr>
								<th style="max-width: 10px!important;">Chọn</th>
								<th>Số container</th>
								<th>Hãng tàu</th>
								<th>Kích cỡ</th>
								<th>Vị trí bãi</th>
								<th>Thanh lý HQ</th>
								<th>Nội/Ngoại</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<div style="margin: 0 auto!important;">
					<button class="btn btn-gradient-blue btn-labeled btn-labeled-left btn-icon" id="apply-bill">
						<span class="btn-label"><i class="ti-check"></i></span>Chuyển tính tiền</button>
					<button class="btn btn-gradient-peach btn-labeled btn-labeled-left btn-icon" data-dismiss="modal">
						<span class="btn-label"><i class="ti-close"></i></span>Đóng</button>
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
				<h4 class="modal-title text-primary font-bold" id="groups-modalLabel">Chi tiết Lưu Bãi/ Điện Lạnh</h4>
				<i class="btn fa fa-times text-primary" data-dismiss="modal"></i>
			</div>
			<div class="modal-body" style="border: 2px outset #ccc;margin:3px;border-radius: 5px;overflow-y: auto;max-height: 90vh">
				<h4>

				</h4>
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

<script type="text/javascript">
	moment.tz.setDefault('Asia/Ho_Chi_Minh');
	$(document).ready(function() {
		var _colsPayment = ["STT", "DRAFT_INV_NO", "REF_NO", "TRF_CODE", "TRF_DESC", "INV_UNIT", "JobMode", "DMETHOD_CD", "CARGO_TYPE", "ISO_SZTP", "FE", "IsLocal", "QTY", "standard_rate", "DIS_RATE", "extra_rate", "UNIT_RATE", "AMOUNT", "VAT_RATE", "VAT", "TAMOUNT", "CURRENCYID", "IX_CD", "CNTR_JOB_TYPE", "VAT_CHK", "Remark", "TRF_DESC_MORE"],
			_colPayer = ["STT", "CusID", "VAT_CD", "CusName", "Address", "CusType"],
			_colCont = ["STT", "CntrNo", "BLNo", "CntrClass", "OprID", "LocalSZPT", "ISO_SZTP", "Status", "SealNo", "IsLocal", "CMDWeight", "CARGO_TYPE", "CLASS_UNNO", "Note", "cTLHQ"],
			_colsAttachServices = ["Select", "CjMode_CD", "CJModeName", "Cont_Count"];

		var _result = [],
			_lstEir = [];
		var selected_cont = [],
			tblConts = $("#tbl-conts"),
			tblBillDetail = $('#bill-detail'),
			tblInv = $("#tbl-inv"),
			tblAttach = $('#tb-attach-srv');

		var payers = [],
			_attachServicesChecker = [],
			_lstAttachService = [];

		$('#search-barge').DataTable({
			paging: false,
			searching: false,
			infor: false,
			scrollY: '25vh'
		});

		tblConts.DataTable({
			info: false,
			paging: false,
			searching: false,
			columnDefs: [{
				className: 'text-center',
				targets: [0]
			}],
			select: true,
			buttons: [],
			scrollY: '30vh'
		});

		tblInv.DataTable({
			info: false,
			paging: false,
			searching: false,
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

		tblBillDetail.DataTable({
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
				targets: 6,
				render: function(data, type, full, meta) {
					return data == 'F' ? 'Ngoại' : 'Nội';
				},
			}],
			select: {
				style: 'multi+shift',
				selector: 'td:first-child'
			},
			order: [
				[1, 'asc']
			],
		});

		tblAttach.DataTable({
			paging: false,
			columnDefs: [{
					className: 'text-center',
					orderDataType: 'dom-text',
					type: 'string',
					targets: _colsAttachServices.indexOf("Select")
				},
				{
					className: 'text-center',
					targets: _colsAttachServices.indexOf("Cont_Count")
				}
			],
			order: [],
			buttons: [],
			info: false,
			searching: false,
			scrollY: '20vh'
		});

		$('#search-payer').DataTable({
			paging: true,
			scroller: {
				displayBuffer: 9,
				boundaryScale: 0.95
			},
			columnDefs: [{
					type: "num",
					className: 'text-center',
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

		$('#ref-date').val(moment().format('DD/MM/YYYY HH:mm:ss'));
		$('#ref-exp-date, #MT-exp-date, #opr-exp-date').datetimepicker({
			dateFormat: 'dd/mm/yy',
			timeFormat: 'HH:mm:ss',
			todayHighlight: true,
			oneLine: true,
			minDate: moment().format('DD/MM/YYYY HH:mm:ss'),
			controlType: 'select',
			autoclose: true,
			timeInput: true,
			hour: 23,
			minute: 59,
			second: 59
		});

		$('#ref-exp-date').val(moment().format('DD/MM/YYYY 23:59:59'));
		$('#ref-exp-date + span').on('click', function() {
			$('#ref-exp-date').val('');
		});
		$('#opr-exp-date + span').on('click', function() {
			$('#opr-exp-date').val('');
		});

		var relocation = <?= json_encode($relocation) ?>;
		$("#terminal-cd").autocomplete({
			source: relocation.map(p => p.GNRL_CODE + ' : ' + p.GNRL_NM), //GNRL_CODE
			minLength: 0
		});

		// $("#MT-retlocation, #terminal-cd").mousedown(function() {
		// 	if (document.activeElement == this) return;
		// 	$(this).focus();
		// });

		<?php if (isset($cntrFromEDO) && count($cntrFromEDO) > 0) { ?>
			_result = <?= json_encode($cntrFromEDO); ?>;
			selected_cont = _result.map(x => x.CntrNo);
			apply_bill();
		<?php } ?>

		load_payer();

		$('#chkMT-return').on('change', function() {
			if ($(this).is(":checked")) {
				$('.MT-toggle').css('display', '');
				$('#MT-retlocation').val('HAP : HAI AN PORT');
			} else {
				$('.MT-toggle').css('display', 'none');
				$('#MT-retlocation').val('');
			}
			$('.MT-change-required').toggleClass('input-required');
			if (!$(this).is(':checked')) {
				_lstEir = _lstEir.filter(item => item.CJMode_CD != "TRAR");
			}
		});

		$('#b-add-payer').on("click", function() {
			$('.add-payer-container').addClass("payer-show");
		});

		$('#close-payer-content').on("click", function() {
			$('.add-payer-container').removeClass("payer-show");
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

		///////// INPUT TAX_CODE DIRECTLY
		$("#taxcode").on("keypress", function(e) {
			if (e.keyCode == 13) {
				$(this).trigger('change');
			}
		});
		///////// INPUT TAX_CODE DIRECTLY

		$('input[name="view-opt"]').bind('change', function(e) {
			$('.grid-toggle').find('div.table-responsive').toggleClass('grid-hidden');
			if ($('#chk-view-inv').is(':checked') && $('#tbl-inv tbody').find('tr').length <= 1) {
				//kiem tra cac truong bat buoc
				if ($('.input-required').has_required()) {
					$('.toast').remove();
					toastr['error']('Các trường bắt buộc (*) không được để trống!');
					tblInv.dataTable().fnClearTable();
					$('#chk-view-cont').trigger('click');
					return;
				}

				var hasRefeerCont = tblConts.DataTable().column(_colCont.indexOf("ISO_SZTP"))
					.data().toArray()
					.filter(p => p.substr(2, 1) == "R").length > 0;
				var checkCargoType = _lstEir.map(e => e?.CARGO_TYPE).filter(item => item != 'GP' && item != 'MT').toString();
				var hasAttachSDD = _attachServicesChecker.filter(p => p.CJMode_CD == 'SDD' && p.Select == 1).length > 0;
				if (hasRefeerCont && !hasAttachSDD) {
					$.confirm({
						title: 'Cảnh báo!',
						type: 'orange',
						icon: 'fa fa-warning',
						content: 'Có container lạnh chưa tính cước!',
						buttons: {
							ok: {
								text: 'Tiếp tục',
								btnClass: 'btn-primary',
								keys: ['Enter'],
								action: function() {
									loadpayment();
								}
							},
							cancel: {
								text: 'Hủy bỏ',
								btnClass: 'btn-default',
								keys: ['ESC'],
								action: function() {
									$('#chk-view-cont').trigger('click');
								}
							}
						}
					});
				} else {
					if (checkCargoType) {
						$.confirm({
							title: 'Cảnh báo!',
							type: 'orange',
							icon: 'fa fa-warning',
							content: `Lệnh giao nhận có loại hàng ${checkCargoType} !`,
							buttons: {
								ok: {
									text: 'Tiếp tục',
									btnClass: 'btn-primary',
									keys: ['Enter'],
									action: function() {
										loadpayment();
									}
								},
								cancel: {
									text: 'Hủy bỏ',
									btnClass: 'btn-default',
									keys: ['ESC'],
									action: function() {
										$('#chk-view-cont').trigger('click');
									}
								}
							}
						});
					} else {
						loadpayment();
					}
				}
			}

			if ($(this).val() == "inv") {
				tblInv.DataTable().columns.adjust();
			} else {
				tblConts.DataTable().columns.adjust();
			}
		});

		$('input[name="chkSalan"]').on('change', function() {
			$('#barge-ischecked').toggleClass('unchecked-Salan');
			$('#barge-info').toggleClass('input-required');

			var ischecked = $(this).is(':checked');
			if (!ischecked) {
				$('#barge-info').val('');
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

		$('input[name="chkMT-return"]').on('change', function() {
			$('.unchecked-Salan').toggleClass('unchecked-Salan');
		});

		$('#barge-modal, #bill-modal, #payer-modal').on('shown.bs.modal', function(e) {
			$($.fn.dataTable.tables(true)).DataTable()
				.columns
				.adjust();
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

			//refill payment type
			_lstEir.map(item => {
				item['PAYMENT_TYPE'] = $('#payment-type').val();
				//item['PAYMENT_CHK'] = item['PAYMENT_TYPE'] == "C" ? "0" : "1";
				item['PAYMENT_CHK'] = "0"
			});
		});
		//THU SAU

		$('#show-payment-modal').on("click", function(e) {
			if (!$("#taxcode").val() || !$("#cusID").val()) {
				$('#taxcode').addClass("error");
				toastr["warning"]("Chưa chọn đối tượng thanh toán!");
				e.preventDefault();
				return;
			}

			var paymentType = $('#payment-type').val();
			if (paymentType == "M") {

				if (_lstEir.length == 0 || $('.input-required').has_required()) {
					tblInv.dataTable().fnClearTable();
					e.preventDefault();
					return;
				}

				if (tblInv.DataTable().rows().count() == 0) {
					loadpayment();
					e.preventDefault();
					return;
				}

				$("#dv-cash, #publish-type").removeClass("hiden-input");
				$("#dv-credit").addClass("hiden-input");

				if (!$("input[name='publish-opt']").prop("checked")) {
					$("input[name='publish-opt'][value='e-inv']").prop("checked", true);
					$("#m-inv-container").addClass("hiden-input");
				}
				$(this).attr("data-target", "#payment-modal");

			} else {
				if (tblConts.DataTable().rows().data().toArray().length == 0) {
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
								url: "<?= site_url(md5('Task') . '/' . md5('tskImportPickup')); ?>",
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

		$('#isGiaoCntrXuat').on('change', function(e) {
			$('#billno-label').text($(this).is(':checked') ? 'Số booking' : 'Số vận đơn');
			$('#billno').attr("placeholder", $(this).is(':checked') ? 'Số booking' : 'Số vận đơn');
		});

		var _ktype = "";
		$('#billno').on('keypress', function(e) {
			if (!$(this).val()) return;
			if (e.keyCode == 13) {
				_ktype = "enter";
				search_bill($(this).val(), '');
			}
		});

		$('#cntrno + span').on('click', function() {
			var rl = tblBillDetail.DataTable().rows().to$();
			if (rl.length == 1 && rl[0].length > 0) {
				$(this).attr('data-target', '#bill-modal');
			} else {
				$('.toast').remove();
				toastr['warning']('Chưa có thông tin vận đơn!');
				$(this).attr('data-target', '');
			}
		});

		var _tp = "";
		$('#cntrno').on('change keypress', function(e) {
			if ((e.type == 'change' || e.which == 13) && _tp == "") {
				apply_cont();
				_tp = e.type;
				return;
			}
			_tp = "";
		});

		tblBillDetail.on('click', 'tbody tr td:not(:nth-child(1))', function(e) {
			var tr = $(e.target).closest('tr');
			if (tr.hasClass('selected')) {
				tblBillDetail.DataTable().rows(tr).deselect();
			} else {
				tblBillDetail.DataTable().rows(tr).select();
			}
		});
		//select barge
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
		});

		$('#search-barge').on('dblclick', 'tbody tr td', function() {
			var r = $(this).parent();
			$('#barge-info').val($(r).find('td:eq(1)').text() + "/" + $(r).find('td:eq(3)').text() + "/" + $(r).find('td:eq(4)').text());
			$('#barge-modal').modal("toggle");
			$('#barge-info').trigger('change');
		});

		$('#apply-bill').on('click', function() {
			//ISSUE : Bỏ ràng buộc với cont chưa TLHQ 1/6/2024
			var btn = $(this);
			var chkTLHQ_cntr = [];
			// selected_cont = [];
			var selectedRows = tblBillDetail.DataTable().rows('.selected').data().toArray();
			selectedRows.map((v, k) => {
				var isTLHQ = v[5].includes("input") ? $(v[5]).val() : v[5];
				var applyCntrNo = v[1];
				var isLocal = v[6];

				if (isTLHQ != "1" && isLocal != "L") { //ko check thanh ly voi cont noi(L)
					if ($.inArray(applyCntrNo, chkTLHQ_cntr) == "-1") {
						chkTLHQ_cntr.push(applyCntrNo);
					}
				}
				// else {
				if ($.inArray(applyCntrNo, selected_cont) == "-1") {
					selected_cont.push(applyCntrNo);
				}
				// }
			});

			// if (chkTLHQ_cntr.length > 0) {
			// 	$('#bill-modal').attr("data-keyboard", "false");
			// 	var confirmBtn = {
			// 		cancel: {
			// 			text: 'Hủy bỏ',
			// 			btnClass: 'btn-default btn-sm lower-text',
			// 			keys: ['ESC'],
			// 			action: function() {
			// 				selected_cont.length = 0;
			// 			}
			// 		}
			// 	};

			// 	/** [ISSUE 99] issue canh bao va ko cho lam lenh doi voi nhung cont chua thanh ly **/
			// 	// confirmBtn["ok"] = {
			// 	// 		text: 'Tiếp tục với những cont đã chọn',
			// 	// 		btnClass: 'btn-primary btn-sm lower-text',
			// 	// 		action: function() {
			// 	// 			selected_cont = selected_cont.concat(chkTLHQ_cntr);

			// 	// 			$('#bill-modal').modal("hide");

			// 	// 			apply_bill();
			// 	// 		}
			// 	// 	};

			// 	if (selected_cont.length > 0) {
			// 		confirmBtn["need"] = {
			// 			text: 'Chỉ chọn cont đã thanh lý',
			// 			btnClass: 'btn-warning btn-sm lower-text',
			// 			action: function() {
			// 				tblBillDetail.find("tbody tr").each(function(k, v) {
			// 					var cntrNoChk = $(v).find("td:eq(1)").text();
			// 					if (chkTLHQ_cntr.indexOf(cntrNoChk) != "-1") {
			// 						tblBillDetail.DataTable().rows($(v)).deselect();
			// 					}
			// 				});

			// 				$('#bill-modal').modal("hide");
			// 				apply_bill();
			// 			}
			// 		}
			// 	}
			// 	/** [ISSUE 99] issue canh bao va ko cho lam lenh doi voi nhung cont chua thanh ly **/
			// 	// else {
			// 	// 	confirmBtn.ok.text = "Tiếp tục";
			// 	// 	confirmBtn.ok["keys"] = ["Enter"];
			// 	// }

			// 	$.confirm({
			// 		title: 'Cảnh báo!',
			// 		type: 'orange',
			// 		icon: 'fa fa-warning',
			// 		content: 'Có container chưa được thanh lý HQ!',
			// 		buttons: confirmBtn
			// 	});
			// } else {
			if (chkTLHQ_cntr.length > 0) {
				$.confirm({
					title: 'Cảnh báo!',
					type: 'orange',
					icon: 'fa fa-warning',
					content: 'Container chưa được thanh lý HQ! <br/>Tiếp tục làm lệnh ?',
					buttons: {
						ok: {
							text: 'Tiếp tục',
							btnClass: 'btn-primary',
							keys: ['Enter'],
							action: function() {

								tblBillDetail.find("tbody tr").each(function(k, v) {
									var cntrNoChk = $(v).find("td:eq(1)").text();
									if (chkTLHQ_cntr.indexOf(cntrNoChk) != "-1") {
										tblBillDetail.DataTable().rows($(v)).deselect();
									}
								});

								$('#bill-modal').modal("hide");

								apply_bill();
							}
						},
						cancel: {
							text: 'Hủy bỏ',
							btnClass: 'btn-default',
							keys: ['ESC']
						}
					}
				});
			} else {
				tblBillDetail.find("tbody tr").each(function(k, v) {
					var cntrNoChk = $(v).find("td:eq(1)").text();
					if (chkTLHQ_cntr.indexOf(cntrNoChk) != "-1") {
						tblBillDetail.DataTable().rows($(v)).deselect();
					}
				});

				$('#bill-modal').modal("hide");

				apply_bill();
			}
			// }
		});

		// ----- FOR ATTACH SERVICES
		load_attach_srv();
		$("#chkServiceAttach").on("change", function() {
			var content = $("#col-transfer");

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

		tblConts.DataTable().on("select deselect", function(e, dt, type, indexes) {
			clearTimeout(typingTimer);
			typingTimer = setTimeout(loadAttachData(indexes), doneInterval);
		});

		var storedOption = {
			applyAll: 0,
			data: ''
		};

		var specialIndx = 0;

		function confirmServices(currentTD, selectedConts, currentCjMode) {
			var iContNo = selectedConts[specialIndx];

			if (!iContNo) {
				storedOption.applyAll = 0;
				storedOption.data = '';
				specialIndx = 0;
				return;
			}

			if (storedOption.applyAll == 1) {
				var setItem = {
					Select: 1,
					CntrNo: iContNo,
					CJMode_CD: currentCjMode
				};

				setItem[currentCjMode == 'SDD' ? "ExpPluginDate" : "ExpDate"] = storedOption.data;

				if (_attachServicesChecker.length > 0) {
					var hasItemIdx = _attachServicesChecker.filter(p => p.CntrNo == iContNo).map(x => _attachServicesChecker.indexOf(x));
					if (hasItemIdx.length > 0) {
						_attachServicesChecker[hasItemIdx[0]].Select = 1;

						_attachServicesChecker[hasItemIdx[0]][currentCjMode == 'SDD' ? "ExpPluginDate" : "ExpDate"] = storedOption.data;
					} else {
						_attachServicesChecker.push(setItem);
					}
				} else {
					_attachServicesChecker.push(setItem);
				}

				if (iContNo == selectedConts[selectedConts.length - 1]) {
					if ($('#chk-view-inv').is(':checked')) {
						loadpayment();
					} else {
						$('#tbl-inv').dataTable().fnClearTable();
					}
				}

				specialIndx++;

				var oldNumCell = currentTD.closest("tr").find("td:eq(" + _colsAttachServices.indexOf("Cont_Count") + ")");
				var oldNum = tblAttach.DataTable().cell(oldNumCell).data();

				tblAttach.DataTable().cell(oldNumCell).data((oldNum ? parseInt(oldNum) : 0) + 1);

				confirmServices(currentTD, selectedConts, currentCjMode);
			} else {
				$.confirm({
					columnClass: 'col-md-4 col-md-offset-4 mx-auto',
					titleClass: 'font-size-17',
					title: 'Chọn hạn tính ' + (currentCjMode == "SDD" ? 'Điện Lạnh' : 'Lưu bãi') + ' container [' + iContNo + ']',
					content: '<div class="input-group-icon input-group-icon-left">' +
						'<span class="input-icon input-icon-left"><i class="fa fa-calendar" style="color: blue"></i></span>' +
						'<input class="form-control form-control-sm" id="select-datetime" type="text" placeholder="Chọn thời gian">' +
						'</div>' +
						'<div class="form-inline" >' +
						'<div id="calendar-inline" style="margin: auto">' +
						'</div>',
					onContentReady: function() {
						$('#calendar-inline').datetimepicker({
							dateFormat: 'dd/mm/yy',
							timeFormat: 'HH:mm',
							controlType: 'select',
							altField: "#select-datetime",
							minDate: new Date(_result.filter(p => p.CntrNo == iContNo).map(x => x.DateIn)[0]),
							maxDate: new Date(convertDateTimeFormat($("#ref-exp-date").val(), 'y-m-d')),
							altFieldTimeOnly: false
						});

						$('#select-datetime').val($('#ref-exp-date').val());
					},
					buttons: {
						allApply: {
							text: 'Áp dụng hết',
							btnClass: 'btn-sm btn-warning btn-confirm',
							keys: ['Enter'],
							action: function() {
								var input = this.$content.find('input#select-datetime');
								var errorText = this.$content.find('.text-danger');
								if (!input.val().trim()) {
									$.alert({
										title: "Thông báo",
										content: "Vui lòng chọn thời gian!.",
										type: 'red'
									});
									return false;
								} else {
									storedOption.applyAll = 1;
									storedOption.data = input.val();
									confirmServices(currentTD, selectedConts, currentCjMode);
								}
							}
						},
						ok: {
							text: 'Xác nhận',
							btnClass: 'btn-sm btn-primary btn-confirm',
							keys: ['Enter'],
							action: function() {
								var input = this.$content.find('input#select-datetime');
								var errorText = this.$content.find('.text-danger');
								if (!input.val().trim()) {
									$.alert({
										title: "Thông báo",
										content: "Vui lòng chọn thời gian!.",
										type: 'red'
									});
									return false;
								} else {
									if (_attachServicesChecker.length > 0) {
										var findIdx = _attachServicesChecker.findIndex(p => p.CntrNo == iContNo &&
											p.CJMode_CD == currentCjMode);
										if (findIdx > -1) {
											_attachServicesChecker[findIdx].Select = 1;
										} else {
											var temp3 = {
												Select: 1,
												CntrNo: iContNo,
												CJMode_CD: currentCjMode
											};

											temp3[currentCjMode == 'SDD' ? "ExpPluginDate" : "ExpDate"] = input.val();

											_attachServicesChecker.push(temp3);
										}
									} else {
										var temp4 = {
											Select: 1,
											CntrNo: iContNo,
											CJMode_CD: currentCjMode
										};

										temp4[currentCjMode == 'SDD' ? "ExpPluginDate" : "ExpDate"] = input.val();

										_attachServicesChecker.push(temp4);
									}

									storedOption.applyAll = 0;
									storedOption.data = '';

									var oldNumCell = currentTD.closest("tr").find("td:eq(" + _colsAttachServices.indexOf("Cont_Count") + ")");
									var oldNum = tblAttach.DataTable().cell(oldNumCell).data();

									tblAttach.DataTable().cell(oldNumCell).data((oldNum ? parseInt(oldNum) : 0) + 1);

									// if( $('#chk-view-inv').is(':checked') ){
									// 	loadpayment();
									// }else{
									// 	$('#tbl-inv').dataTable().fnClearTable();
									// }

									if (iContNo == selectedConts[selectedConts.length - 1]) {
										if ($('#chk-view-inv').is(':checked')) {
											loadpayment();
										} else {
											$('#tbl-inv').dataTable().fnClearTable();
										}
									}

									specialIndx++;
									confirmServices(currentTD, selectedConts, currentCjMode);
								}
							}
						},
						cancel: {
							text: 'Hủy',
							btnClass: 'btn-sm',
							keys: ['ESC'],
							action: function() {
								storedOption.applyAll = 0;
								storedOption.data = '';

								currentTD.find("input:first").removeAttr("checked").val("");
								specialIndx = 0;

								tblAttach.DataTable().cell(currentTD).data(currentTD.html()).draw(false);
							}
						}
					}
				});
			}
		}

		tblAttach.on('change', 'tbody tr td input[type="checkbox"]', function(e) {

			var inp = $(e.target);

			if (tblConts.DataTable().rows('.selected').data().length == 0) {

				$(".toastr").remove();
				toastr["error"]("Vui lòng chọn ít nhất một container để đính kèm dịch vụ!");

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

			if (inp.closest("td").index() == _colsAttachServices.indexOf("Select")) {

				var currentTD = inp.closest("td");

				var selectedConts = tblConts.DataTable()
					.rows('.selected')
					.data().toArray()
					.map(x => x[_colCont.indexOf("CntrNo")]);

				var currentCjMode = inp.closest("tr").find("td:eq(" + _colsAttachServices.indexOf("CjMode_CD") + ")").text();

				if (currentCjMode == 'SDD' || currentCjMode == 'LBC') {
					storedOption.applyAll = 0;
					storedOption.data = '';
					specialIndx = 0;

					if (!inp.is(":checked")) {
						_attachServicesChecker = _attachServicesChecker.filter(p => selectedConts.indexOf(p.CntrNo) != -1 && p.CJMode_CD != currentCjMode);

						//giảm số lượng chọn khi uncheck
						var oldNumCell = currentTD.closest("tr").find("td:eq(" + _colsAttachServices.indexOf("Cont_Count") + ")");
						var oldNum = tblAttach.DataTable().cell(oldNumCell).data();
						var newNum = (oldNum ? parseInt(oldNum) : 0) - selectedConts.length

						tblAttach.DataTable().cell(oldNumCell).data(newNum > 0 ? newNum : 0);

						if ($('#chk-view-inv').is(':checked')) {
							loadpayment();
						} else {
							$('#tbl-inv').dataTable().fnClearTable();
						}
					} else {
						confirmServices(currentTD, selectedConts, currentCjMode);
					}
				} else {
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
			}

			if (inp.is(":checked")) {
				inp.attr("checked", "");
				inp.val(1);
			} else {
				inp.removeAttr("checked");
				inp.val("");
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

			if (tblConts.DataTable().rows().count() == 0) {
				return;
			}

			if (tblConts.DataTable().rows('.selected').count() == 0) {
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

							var selectContNos = tblConts.DataTable()
								.rows('.selected')
								.data().toArray()
								.map(p => p[_colCont.indexOf("CntrNo")]);
							// var selectContNos = tblConts.find('tr.selected').find('td:eq('+ 1 +')').map( function(){ return $(this).text(); }  ).get();
							tblConts.DataTable().rows(".selected").remove().draw(false);
							tblConts.updateSTT();

							selected_cont = selected_cont.filter(p => selectContNos.indexOf(p) == "-1");
							_lstEir = _lstEir.filter(p => selectContNos.indexOf(p.CntrNo) == "-1");

							//remove cont in attach services
							_attachServicesChecker = _attachServicesChecker.filter(p => selectContNos.indexOf(p.CntrNo) == "-1");

							$.each($('#booking-detail tbody ').find('tr').find('td:eq(1)'), function(idx, td) {
								if (selectContNos.indexOf($(td).text()) != "-1") {
									$(td).parent().removeClass('m-row-selected');
									$(td).parent().find('td:eq(0)').removeClass('ti-check');
								}
							});

							//remove all row to recalculate
							tblInv.DataTable().clear().draw();
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

		$("#add-payer-taxcode, #add-payer-name").on("input", function() {
			$(this).removeClass("error");
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

		var iptimee;
		$('.input-required:not(#taxcode)').on('input', function(e) {
			clearTimeout(iptimee);
			iptimee = window.setTimeout(function() {
				$(e.target).blur();
			}, 1500);
		});

		var typingTimer;
		$(document).on('change', '.input-required, #chkMT-return', function(e) {
			clearTimeout(typingTimer);
			if ($(e.target).val()) {
				$(e.target).removeClass('error');
				$(e.target).parent().removeClass('error');
			}

			if ($(e.target).attr('id') == 'taxcode') {
				var taxcode = $(e.target).val();
				if (!taxcode) {
					clearPayer();
					return;
				}

				var cusID = "";
				var ccc = $("#cusID").val();

				if (payers.length == 0 && !ccc) {
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

			if ($(e.target).attr('id') == "billno") {
				if (e.type == 'change' && _ktype == "") {
					search_bill($('#billno').val(), '');
				}

				// //reset list eir
				_lstEir = [];
				// if(tblConts.find('tr').length > 1){
				// 	tblConts.DataTable().clear().draw();
				// }

				tblInv.dataTable().fnClearTable();

				// if(tblInv.find('tr').length > 1){
				// }

				return;
			}

			typingTimer = window.setTimeout(function() {
				//reset list eir
				_lstEir = [];

				if ($('.input-required.error').length == 0) {
					if (_result.length > 0 && selected_cont.length > 0) {

						for (i = 0; i < _result.length; i++) {
							if (selected_cont.indexOf(_result[i].CntrNo) == '-1') continue;
							addCntrToEir(_result[i]);
						}
					}
					if ($('#chk-view-inv').is(':checked') &&
						$.inArray($(e.target).attr('id'), ['barge-ischecked', "barge-info", 'taxcode', 'chkMT-return']) != "-1") {
						loadpayment();
					}
				}
			}, 1000);
		});

		// function
		function apply_cont() {
			var cntrno = $('#cntrno').val().trim();
			if (!cntrno) return;

			if (_result.length == 0 || _result.filter(p => p.CntrNo == cntrno).length == 0 || tblBillDetail.DataTable().rows().to$().length == 0) {
				search_bill('', cntrno);
				return;
			}

			if ($.inArray(cntrno, selected_cont) == "-1") {
				selected_cont.push(cntrno);
				tblBillDetail.DataTable().rows().deselect();
				tblBillDetail.DataTable()
					.rows(function(idx, data, node) {
						if (data[1] === cntrno) {
							return node
						}
						return false;
					}).select()

				apply_bill();
			}
		}

		function apply_bill() {
			$('#bill-modal').attr("data-keyboard", "true");

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

			$("#tbl-conts").waitingLoad();
			var rows = [];

			// tblConts.DataTable().column(_colcont.indexOf("CntrNo")).data().toArray();

			if (_result.length > 0 && selected_cont.length > 0) {
				var stt = 1;
				//reset list eir
				_lstEir = [];
				var shipperName = '';
				for (i = 0; i < _result.length; i++) {
					if (selected_cont.indexOf(_result[i].CntrNo) == '-1') continue;

					//add item cntr_details to _lst;
					if ($('.input-required.error').length == 0) {
						if (!hasrequired) {
							addCntrToEir(_result[i]);
						}
					}

					if (_result[i].ShipperName) {
						shipperName = _result[i].ShipperName;
					}

					var cntrclass = "";
					switch (_result[i].CntrClass) {
						case "1":
							cntrclass = "Nhập";
							break;
						case "3":
							cntrclass = "Xuất";
							break;
						case "4":
							cntrclass = "Nhập chuyển cảng";
							break;
						case "5":
							cntrclass = "Xuất chuyển cảng";
							break;
						default:
							_result[i].CntrClass;
							break;
					}

					var status = _result[i].Status == "F" ? "Hàng" : "Rỗng";
					var isLocal = _result[i].IsLocal == "F" ? "Ngoại" : (_result[i].IsLocal == "L" ? "Nội" : "");
					rows.push([
						(stt++), _result[i].CntrNo, _result[i].CntrClass == "3" ? _result[i].BookingNo : _result[i].BLNo, cntrclass, _result[i].OprID, _result[i].LocalSZPT, _result[i].ISO_SZTP, status, _result[i].SealNo, isLocal, _result[i].CMDWeight, '<input class="hiden-input" value="' + _result[i].CARGO_TYPE + '"/>' + _result[i].Description, _result[i].Temperature, (_result[i].CLASS ? _result[i].CLASS : "") + "/" + (_result[i].UNNO ? _result[i].UNNO : ""), _result[i].Note, _result[i].cTLHQ == 1 ? "Đã thanh lý" : "Chưa thanh lý"
					]);
				}
			}

			if (shipperName) {
				$('#shipper-name').val(shipperName).removeClass('error');
			}

			$('#chk-view-cont').trigger('click');
			tblConts.dataTable().fnClearTable();
			if (rows.length > 0) {
				tblConts.dataTable().fnAddData(rows);
			}

			tblInv.dataTable().fnClearTable();
		}

		function search_bill(billno, cntrNo) {
			billno = billno.toUpperCase();
			cntrNo = cntrNo.toUpperCase();
			var formData = {
				'action': 'view',
				'act': 'search_bill',
				'billNo': billno,
				'cntrNo': cntrNo,
				'isGiaoCntrXuat': $('#isGiaoCntrXuat').is(':checked') ? 1 : 0
			};

			tblBillDetail.waitingLoad();

			if (formData.billNo != '') {
				$("#billno").parent().blockUI();
			}
			if (formData.cntrNo != '') {
				$("#cntrno").parent().blockUI();
			}

			$.ajax({
				url: "<?= site_url(md5('Task') . '/' . md5('tskImportPickup')); ?>",
				dataType: 'json',
				data: formData,
				type: 'POST',
				success: function(data) {

					if (formData.billNo != '') {
						$("#billno").parent().unblock()
					};
					if (formData.cntrNo != '') {
						$("#cntrno").parent().unblock()
					};

					if (data.deny) {
						toastr["error"](data.deny);
						return;
					}

					if (data.error) {
						toastr["error"](data.error);
						return;
					}

					var rows = [];
					var blbkNo = '';

					if (!data.list || data.list.length == 0) {
						tblBillDetail.dataTable().fnClearTable();
						$('.toast').remove();

						if (formData.billNo != '') {
							toastr['info']('Số vận đơn [' + formData.billNo + '] không đủ điều kiện làm lệnh!\nVui lòng kiểm tra lại!');
						} else {
							toastr['info']('Số container [' + formData.cntrNo + '] không đủ điều kiện làm lệnh!\nVui lòng kiểm tra lại!');
						}

						return;
					} else if (data.list.filter(p => p.Ter_Hold_CHK != '1').length == 0) {
						tblBillDetail.dataTable().fnClearTable();
						$('.toast').remove();
						if (formData.billNo != '') {
							toastr['warning']('Toàn bộ container thuộc số vận đơn [' + formData.billNo + '] đang bị giữ tại Cảng!');
						} else {
							if (data.list.filter(p => p.Ter_Hold_CHK == '1' && p.CntrNo == formData.cntrNo).length > 0) {
								toastr['warning']('Số container [' + formData.cntrNo + '] đang bị giữ tại Cảng!');
							} else {
								toastr['warning']('Số container [' + formData.cntrNo + '] không đủ điều kiện làm lệnh!');
							}
						}

						return;
					} else {
						var avaiCont = _result.map(x => x.CntrNo);
						var avaiShipKey = _result.map(x => x.ShipKey);
						var colBLBK = formData.isGiaoCntrXuat === 1 ? "BookingNo" : 'BLNo';
						var avaiBLBKNo = _result.map(x => x[colBLBK]);

						data.list.filter(p => avaiCont.indexOf(p.CntrNo) < 0 &&
								((!p[colBLBK] || avaiBLBKNo.length == 0) ? 1 : avaiBLBKNo.indexOf(p[colBLBK])) >= 0 &&
								p.Ter_Hold_CHK != '1')
							.map(item => _result.push(item));

						blbkNo = _result[0][colBLBK];

						for (i = 0; i < data.list.length; i++) {
							if (data.list[i].Ter_Hold_CHK != '1') {
								rows.push([
									'', data.list[i].CntrNo, data.list[i].OprID, data.list[i].ISO_SZTP, data.list[i].cBlock ? (data.list[i].cBlock + "-" + data.list[i].cBay + "-" + data.list[i].cRow + "-" + data.list[i].cTier) :
									data.list[i].cArea, "<input type='text' class='hiden-input' value='" + data.list[i].cTLHQ + "'> " +
									(data.list[i].cTLHQ == "1" ? "Đã thanh lý" : "Chưa thanh lý"), data.list[i].IsLocal
								]);
							}
						}

						tblBillDetail.dataTable().fnClearTable();
						if (rows.length > 0) {
							tblBillDetail.dataTable().fnAddData(rows);
							if (formData.cntrNo != '') {
								tblBillDetail.DataTable()
									.rows(function(idx, data, node) {
										if (data[1] === formData.cntrNo) {
											return node;
										}
										return false;
									}).select()

							} else {
								tblBillDetail.DataTable().rows().select();
							}

						}

						// $('#bill-detail').DataTable({
						// 	data: rows,
						// 	createdRow: function(row, data, dataIndex) {
						// 		if (formData.cntrNo != '') {
						// 			if (data[1] != formData.cntrNo) {
						// 				$('td:eq(0)', row).addClass("ti-check");
						// 				$(row).removeClass('selected');
						// 			}
						// 		}
						// 	}
						// });
					}

					if (formData.cntrNo != '' && blbkNo != '') {
						var cntrsa = data.list.filter(p => p.CntrNo == formData.cntrNo);
						if (cntrsa.length == 0) {
							toastr['info']('Số container [' + formData.cntrNo + '] không đủ điều kiện làm lệnh!\nVui lòng kiểm tra lại!');
							return;
						};

						if (cntrsa[0].Ter_Hold_CHK == '1') {
							$.alert({
								title: 'Cảnh báo!',
								content: 'Container [' + formData.cntrNo + '] đang bị giữ tại Cảng!',
								type: 'red'
							});
						} else if (cntrsa[0].cTLHQ != '1' && cntrsa[0].IsLocal != 'L') { //khong bat thanh ly voi cont noi(L)
							/** [ISSUE 99] issue canh bao va ko cho lam lenh doi voi nhung cont chua thanh ly **/

							// $.alert({
							// 	title: 'Cảnh báo!',
							// 	content: 'Container chưa được thanh lý HQ!',
							// 	type: 'red'
							// });

							$.confirm({
								title: 'Cảnh báo!',
								type: 'orange',
								icon: 'fa fa-warning',
								content: 'Container chưa được thanh lý HQ! <br/>Tiếp tục làm lệnh ?',
								buttons: {
									ok: {
										text: 'Tiếp tục',
										btnClass: 'btn-primary',
										keys: ['Enter'],
										action: function() {

											$('#cntrno').val('');
											$('#billno').val(blbkNo);

											// selected_cont = [cntrNo];
											if ($.inArray(cntrNo, selected_cont) == "-1") {
												selected_cont.push(cntrNo);
											}

											apply_bill();
										}
									},
									cancel: {
										text: 'Hủy bỏ',
										btnClass: 'btn-default',
										keys: ['ESC']
									}
								}
							});

						} else {
							$('#cntrno').val('');
							$('#billno').val(blbkNo);

							// selected_cont = [cntrNo];
							if ($.inArray(cntrNo, selected_cont) == "-1") {
								selected_cont.push(cntrNo);
							}

							apply_bill();
						}
					} else {
						$('#cntrno + span').trigger('click');
					}

					_ktype = "";
				},
				error: function(err) {
					toastr["error"]("Internal ERROR !");
					if (formData.billNo != '') {
						$("#billno").parent().unblock()
					};
					if (formData.cntrNo != '') {
						$("#cntrno").parent().unblock()
					};
					console.log(err);
				}
			});
		}

		function search_barge() {
			$("#search-barge").waitingLoad();
			var formdata = {
				'action': 'view',
				'act': 'search_barge'
			};

			$.ajax({
				url: "<?= site_url(md5('Task') . '/' . md5('tskImportPickup')); ?>",
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
						scrollY: '25vh',
						data: rows
					});
				},
				error: function(err) {
					console.log(err);
				}
			});
		}

		function load_payer() {
			var tblPayer = $('#search-payer');
			tblPayer.waitingLoad();

			$.ajax({
				url: "<?= site_url(md5('Task') . '/' . md5('tskImportPickup')); ?>",
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

		function addCntrToEir(item) {
			// item['EIRNo'] =  $('#ref-no').val();
			//			item['RetLocation'] =  "";
			item['IssueDate'] = $('#ref-date').val(); //*
			item['ExpDate'] = $('#ref-exp-date').val(); //*
			item['OprExpDate'] = $('#opr-exp-date').val(); //*
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

			item['PAYER_TYPE'] = getPayerType($('#cusID').val());
			item['CusID'] = $('#cusID').val(); //*

			item['PAYMENT_TYPE'] = $('#payment-type').val();
			//item['PAYMENT_CHK'] = item['PAYMENT_TYPE'] == "C" ? "0" : "1";
			item['PAYMENT_CHK'] = "0"

			item['DELIVERYORDER'] = $("#do").val(); //*
			item['Mail'] = $("#mail").val();

			item['CJMode_CD'] = 'LAYN'; //*
			item['CJModeName'] = 'Lấy nguyên'; //*

			/**ISSUE 88 : Yêu cầu bổ sung thông tin để nhập liệu lúc đăng ký lệnh:
							1. Ô "Cảng giao nhận"  -> Sử dụng vận chuyển nội bộ của cảng. table [EIR].[TERMINAL_CD]
							2. Ô "Nơi trả rỗng" -> Sử dụng cho trường hợp cont trả rỗng khách hàng. [EIR].[RetLocation] */
			item['RetLocation'] = $('#MT-retlocation').val();
			item['TERMINAL_CD'] = $('#terminal-cd').val();

			if (item.EIR_SEQ == 0) {
				item['EIR_SEQ'] = 1;
			}

			if ($("#transist").val()) {
				item["Transist"] = $("#transist").val();
			}

			_lstEir.push(item);

			var temp = $.extend({}, item);
			if ($('#chkMT-return').is(':checked')) {
				// temp['EIRNo'] = _eirforMTReturn;
				temp['ShipKey'] = 'STORE';
				temp['CntrClass'] = '2';
				temp['ShipID'] = 'STORAGE';
				temp['ShipVoy'] = '0000';
				temp['ShipYear'] = '0000';

				temp['ImVoy'] = null;

				temp['ExpDate'] = $('#MT-exp-date').val();
				temp['Note'] = $('#MT-remark').val();
				temp['RetLocation'] = $('#MT-retlocation').val();

				temp['SealNo'] = '';
				temp['SealNo1'] = '';
				temp['CJMode_CD'] = 'TRAR';
				temp['CJModeName'] = 'Trả rỗng';
				temp['Status'] = 'E';
				temp['IsLocal'] = 'L';

				var emptyCargoType = "";
				switch (temp['ISO_SZTP'].substring(2, 3)) {
					case "R":
						emptyCargoType = "ER";
						break;
					case "U":
						emptyCargoType = "EO";
						break;
					case "P":
						emptyCargoType = "EF";
						break;
					case "T":
						emptyCargoType = "ET";
						break;
					case "G":
					default:
						emptyCargoType = "MT";
						break;
				}
				temp['CARGO_TYPE'] = emptyCargoType;
				temp['CmdID'] = '';

				//udpate 2018-12-17
				temp["cBlock"] = temp["cBay"] = temp["cRow"] = temp["cTier"] = temp["BLNo"] = null;
				temp["ImVoy"] = "CN";
				temp["ExVoy"] = "CX";

				//update 02092021
				temp["POD"] = null;

				//update 15092021
				temp["BookingNo"] = null;

				temp["Port_CD"] = null;

				// issue: trọng lượng vỏ cont đang bị default = 0 (Default cont 20 là 2T, cont 40 là 3.5T)
				// temp["CMDWeight"] = 0;
				switch (temp['ISO_SZTP'].substr(0, 1)) {
					case "2":
						temp["CMDWeight"] = "2.00";
					default:
						temp["CMDWeight"] = "3.50";
				}

				//udpate 2021-08-05
				deleteItemInArray(temp, ['Temperature', 'CLASS', 'UNNO', 'OOG_TOP', 'OOG_LEFT', 'OOG_BACK', 'OOG_FRONT']);

				_lstEir.push(temp);
			}
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

			if (_lstEir.length == 0 || $('.input-required').has_required()) {
				tblInv.dataTable().fnClearTable();
				return;
			}

			var cusID = $('#cusID').val();
			var formdata = {
				'action': 'view',
				'act': 'load_payment',
				'cusID': cusID,
				'list': _lstEir
			};

			if ($("#chkServiceAttach").is(":checked")) {
				addCntrToAttachSRV();

				var nonAttach = _lstAttachService.filter(p => p.CJMode_CD != "SDD" && p.CJMode_CD != "LBC");
				var sdd = _lstAttachService.filter(p => p.CJMode_CD == "SDD");
				var lbc = _lstAttachService.filter(p => p.CJMode_CD == "LBC");

				if (nonAttach && nonAttach.length > 0) {
					formdata['nonAttach'] = nonAttach;
				}

				if (sdd && sdd.length > 0) {
					formdata['sdd'] = sdd;
				}

				if (lbc && lbc.length > 0) {
					formdata['lbc'] = lbc;
				}
			}

			tblInv.waitingLoad();

			$.ajax({
				url: "<?= site_url(md5('Task') . '/' . md5('tskImportPickup')); ?>",
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
					}

					if (data.error_plugin && data.error_plugin.length > 0) {
						$(".toast").remove();
						$.each(data.error_plugin, function() {
							toastr["error"](this);
						});

						tblInv.dataTable().fnClearTable();
						// return;
					}

					if (data.error && data.error.length > 0) {
						$(".toast").remove();
						$.each(data.error, function(idx, err) {
							toastr["error"](err);
						});

						tblInv.dataTable().fnClearTable();
						return;
					}

					if (data.ps_notify) {
						$("#notify-modal").find(".modal-body h4").html(data.ps_notify);
						$("#show-ps-notify").removeClass("hiden-input");
					} else {
						$("#notify-modal").find(".modal-body h4").html("");
						$("#show-ps-notify").addClass("hiden-input");
					}

					if (data.freeContInYard) {
						_lstAttachService = _lstAttachService.filter(p => data.freeContInYard.indexOf(p.CntrNo) == -1 && p.CJMode_CD == "LBC");
						toastr["warning"]("Container [" + data.freeContInYard.join(", ") + "] được miễn phí lưu bãi!");
					}

					var rows = [];
					if (data.results && data.results.length > 0) {
						var lst = data.results,
							stt = 1;
						for (i = 0; i < lst.length; i++) {

							var status = lst[i].Status == "F" ? "Hàng" : "Rỗng";
							var isLocal = lst[i].IsLocal == "F" ? "Ngoại" : (lst[i].IsLocal == "L" ? "Nội" : "");
							rows.push([
								(stt++), lst[i].DraftInvoice, lst[i].OrderNo ? lst[i].OrderNo : "", lst[i].TariffCode, lst[i].TariffDescription, lst[i].Unit, lst[i].JobMode == 'GO' ? "Nâng container" : (lst[i].JobMode == 'GF' ? "Hạ container" : lst[i].JobMode), lst[i].DeliveryMethod, lst[i].Cargotype, lst[i].ISO_SZTP, lst[i].FE, lst[i].IsLocal, lst[i].Quantity, lst[i].StandardTariff, 0, lst[i].DiscountTariff, lst[i].DiscountedTariff, lst[i].Amount, lst[i].VatRate, lst[i].VATAmount, lst[i].SubAmount, lst[i].Currency, lst[i].IX_CD, lst[i].CNTR_JOB_TYPE, lst[i].VAT_CHK, lst[i].Remark || '', lst[i].TRF_DESC_MORE || ''
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

		$("#cmnd, #personal-name, #mail").on('input', function(e) {
			$(e.target).attr('user-input', $(e.target).val() ? 1 : 0);
		});

		function fillPayer() {
			var py = $("#cusID").val() ?
				payers.filter(p => p.VAT_CD == $('#taxcode').val() && p.CusID == $("#cusID").val()) :
				payers.filter(p => p.VAT_CD == $('#taxcode').val());

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

		function clearPayer() {
			$("#cusID").val('');
			$('#taxcode').val('');

			$("#payer-name").text(" [Tên đối tượng thanh toán]");
			$("#payer-addr").text(" [Địa chỉ]");
			$("#payment-type").val('M').selectpicker('refresh').trigger('change');

			$('#p-taxcode, #p-payername, #p-payer-addr').text('');
		}

		function findPayer(str) {
			$('#taxcode').parent().blockUI();
			$.ajax({
				url: "<?= site_url(md5('Task') . '/' . md5('tskImportPickup')); ?>",
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
						$('#payment-modal').find('.modal-content').unblock();
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

		function saveData(invInfo) {
			var drDetail = getInvDraftDetail();
			var drTotal = {};
			$.each($('#INV_DRAFT_TOTAL').find('span'), function(idx, item) {
				drTotal[$(item).attr('id')] = $(item).text();
			});

			var publish_opt_checked = $("input[name='publish-opt']:checked").val();

			_lstEir.map(x => x.Note = $('#remark').val());
			_lstEir.map(x => x.SHIPPER_NAME = $('#shipper-name').val());
			_lstEir.map(x => x.PersonalID = $('#cmnd').val());
			_lstEir.map(x => x.NameDD = $('#personal-name').val());
			_lstEir.map(x => x.Mail = $('#mail').val());

			var formData = {
				'action': 'save',
				'data': {
					'pubType': publish_opt_checked ? publish_opt_checked : "credit",
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
				//trg hop không phải xuất hóa đơn điện tử, block popup thanh toán ở đây
				$('#payment-modal').find('.modal-content').blockUI();
			}

			$.ajax({
				url: "<?= site_url(md5('Task') . '/' . md5('tskImportPickup')); ?>",
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
					toastr['error']("Server Error at [saveData]! ");
				}
			});
		}

		function getInvDraftDetail() {
			var rows = [];
			var tmprow = tblInv.find('tbody tr:not(.row-total)');
			$.each(tmprow, function() {
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

		function save_new_payer(formData) {
			$(".add-payer-container").blockUI();
			$.ajax({
				url: "<?= site_url(md5('Task') . '/' . md5('tskImportPickup')); ?>",
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

		function addCntrToAttachSRV() {
			_lstAttachService = [];
			var attachSrvSelected = _attachServicesChecker.filter(p => p.Select == 1);

			if (attachSrvSelected.length > 0) {
				$.each(attachSrvSelected, function(index, elem) {
					var finds = _lstEir.filter(p => p.CntrNo == elem["CntrNo"])[0];

					var item = $.extend({}, finds);

					item['CJMode_CD'] = elem["CJMode_CD"];
					item['DMETHOD_CD'] = '*';

					item['PTI_Hour'] = 0;

					if (elem["ExpPluginDate"]) {
						item["ExpPluginDate"] = elem["ExpPluginDate"];
						finds["ExpPluginDate"] = elem["ExpPluginDate"];
					}

					if (elem["ExpDate"]) {
						item["ExpDate"] = elem["ExpDate"];
					}

					//nếu elem có ExpDate, tức là cont điện lạnh, gán lại ExpDate
					// if( elem["ExpDate"] ){
					// 	item["ExpDate"] = elem["ExpDate"];
					// }

					item['cBlock1'] = item['cBlock'];
					item['cBay1'] = item['cBay'];
					item['cRow1'] = item['cRow'];
					item['cTier1'] = item['cTier'];

					deleteItemInArray(item, ["cBlock", "cBay", "cRow", "cTier", "CJModeName", "CLASS", "UNNO", "TERMINAL_CD", "IsTruckBarge", "TruckNo"]);

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
				'order_type': $("#cjmode").val()
			};

			$.ajax({
				url: "<?= site_url(md5('Task') . '/' . md5('tskImportPickup')); ?>",
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

			var allCntrNoSelected = tblConts.DataTable().rows(rowIndexes).data().toArray().map(p => p[_colCont.indexOf("CntrNo")]);

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

						// indexCells.push( cellSelect.index() );
					}
				});
			}
		}
	});
</script>

<script src="<?= base_url('assets/vendors/dataTables/extensions/select.min.js'); ?>"></script>

<script src="<?= base_url('assets/vendors/moment/min/moment.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/bootstrap-select/dist/js/bootstrap-select.min.js'); ?>"></script>
<!--format number-->
<script src="<?= base_url('assets/js/jshashtable-2.1.js'); ?>"></script>
<script src="<?= base_url('assets/js/jquery.numberformatter-1.2.3.min.js'); ?>"></script>