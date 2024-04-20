<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<link href="<?= base_url('assets/vendors/bootstrap-select/dist/css/bootstrap-select.min.css'); ?>" rel="stylesheet" />
<link href="<?= base_url('assets/vendors/jquery-confirm/jquery-confirm.min.css'); ?>" rel="stylesheet" />
<link href="<?= base_url('assets/css//ebilling.css'); ?>" rel="stylesheet" />

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

	#payment-success-modal ul {
		list-style-image: url("<?= base_url('assets/img/icons/sqpurple.gif'); ?>");
	}

	#payment-success-modal ul li {
		padding-bottom: 10px;
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
</style>

<div class="row" style="font-size: 12px!important;">
	<div class="col-xl-12">
		<div class="ibox collapsible-box">
			<i class="la la-angle-double-up dock-right"></i>
			<div class="ibox-head">
				<div class="ibox-title">PHÁT HÀNH HÓA ĐƠN</div>
				<div class="button-bar-group mr-3">
					<!-- <label class="checkbox checkbox-blue mr-3">
						<input type="checkbox" name="eport-inv">
						<span class="input-span"></span>
						Hoá đơn EPORT
					</label> -->
					<button type="button" id="load-data" title="Nạp dữ liệu" data-loading-text="<i class='la la-spinner spinner'></i>Đang nạp" class="btn btn-sm btn-outline-primary mr-1">
						<i class="fa fa-refresh"></i>
						Nạp dữ liệu
					</button>
					<!-- <button type="button" id="m-inv" title="Xuất HĐ giấy" data-loading-text="<i class='la la-spinner spinner'></i>Đang xuất" class="btn btn-sm btn-outline-primary mr-1">
						<i class="fa fa-edit"></i>
						Xuất HĐ giấy
					</button> -->
					<button type="button" id="e-inv" title="Xuất HĐ điện tử" data-loading-text="<i class='la la-spinner spinner'></i>Đang xuất" class="btn btn-sm btn-outline-primary mr-1">
						<i class="fa fa-internet-explorer"></i>
						Xuất HĐ điện tử
					</button>
					<button type="button" id="print-draft" title="In phiếu tính cước" data-loading-text="<i class='la la-spinner spinner'></i>Đang in" class="btn btn-sm btn-outline-primary mr-1">
						<i class="fa fa-print"></i>
						In phiếu tính cước
					</button>
					<button type="button" id="view-draft-inv" title="Xem HĐ nháp" data-loading-text="<i class='la la-spinner spinner'></i>Đang tạo" class="btn btn-sm btn-outline-secondary mr-1">
						<i class="fa fa-eye"></i>
						Xem HĐ nháp
					</button>
				</div>
			</div>
			<div class="ibox-body px-3 pt-2 pb-0 bg-f9 border-e">
				<div class="row">
					<div class="col-3 ibox border-e pt-2 pb-0 mb-2 filter-group">
						<div class="row form-group">
							<label class="col-sm-4 col-form-label">Khoảng ngày</label>
							<div class="col-sm-8 input-group input-group-sm">
								<input class="form-control form-control-sm" id="fromDate" type="text" placeholder="Từ ngày">
								<span>&ensp;</span>
								<input class="form-control form-control-sm" id="toDate" type="text" placeholder="Đến ngày">
							</div>
						</div>
						<div class="row form-group">
							<label class="col-sm-4 col-form-label">Hình thức</label>
							<div class="col-sm-8">
								<select id="paymentType" class="selectpicker" data-style="btn-default btn-sm" data-width="100%" title="Chọn hình thức thanh toán">
									<option value="CAS">Thu ngay</option>
									<option value="CRE">Thu sau</option>
								</select>
							</div>
						</div>
						<div class="row form-group">
							<label class="col-sm-4 col-form-label">Loại tiền</label>
							<div class="col-sm-8 input-group input-group-sm">
								<select id="moneyType" class="selectpicker" data-style="btn-default btn-sm" data-width="100%" title="Chọn loại tiền">
									<option value="VND">VND</option>
									<option value="USD">USD</option>
								</select>
							</div>
						</div>
						<div class="row form-group">
							<label class="col-sm-4 col-form-label">Người tạo</label>
							<div class="col-sm-8 input-group input-group-sm">
								<input id="createdBy" name="CreatedBy" class="form-control form-control-sm" type="text" placeholder="Người lập phiếu" autocomplete="on">
							</div>
						</div>
					</div>

					<!-- ///////////////////////////////// -->

					<div class="col-9 ibox border-e pt-3 mb-2 filter-group">
						<div class="ml-3">
							<div class="row">
								<!-- <div class="col-sm-4">
									<div class="row form-group">
										<label class="col-sm-4 col-form-label">Hóa đơn giấy</label>
										<div class="col-sm-8">
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
													<button id="change-ssinvno" class="btn btn-outline-secondary btn-icon-only btn-sm" data-toggle="modal" data-target="#change-ssinv-modal" title="Thay đổi hóa đơn sử dụng tiếp theo" style="width: 45px; height: 18px">
														<i class="fa fa-pencil"></i>
													</button>
												<?php } else { ?>
													<span id="ss-invNo">
														Chưa khai báo!
													</span>
													&ensp;
													<button id="change-ssinvno" class="btn btn-outline-secondary btn-icon-only btn-sm" data-toggle="modal" data-target="#change-ssinv-modal" title="Khai báo số hóa đơn sử dụng tiếp theo" style="width: 45px; height: 18px">
														<i class="fa fa-pencil"></i>
													</button>
												<?php } ?>
											</div>
										</div>
									</div>
								</div> -->

								<div class="col-sm-4">
									<div class="row form-group">
										<label class="col-sm-4 col-form-label">Đổi từ phiếu thu</label>
										<div class="col-sm-8 input-group input-group-sm">
											<label class="checkbox checkbox-primary text-primary">
												<input type="checkbox" name="isDFT_to_INV" id="isDFT_to_INV" value="2" checked>
												<span class="input-span"></span>
											</label>
										</div>
									</div>
								</div>

								<div class="col-sm-4">
									<div class="row form-group">
										<label class="col-sm-4 col-form-label">Phương thức</label>
										<div class="col-sm-8 input-group input-group-sm">
											<select id="paymentMethod" class="selectpicker input-required" data-style="btn-default btn-sm" data-width="100%" title="Chọn phương thức">
											</select>
										</div>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="row form-group">
										<label class="col-sm-4 col-form-label">Loại hóa đơn</label>
										<div class="col-sm-8 input-group input-group-sm">
											<select id="invType" class="selectpicker input-required" data-style="btn-default btn-sm" data-width="100%" title="Chọn loại hóa đơn">
												<option value="VND">Tiền VND</option>
												<option value="USD">Tiền USD</option>
											</select>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-4">
									<div class="row form-group">
										<label class="col-sm-4 col-form-label">Ngày lập HĐ</label>
										<div class="col-sm-8 input-group input-group-sm">
											<input class="form-control form-control-sm input-required" id="invDate" type="text" placeholder="Ngày lập" disabled>
										</div>
									</div>
								</div>
								<div class="col-sm-8">
									<div class="row form-group">
										<label class="col-sm-2 col-form-label">Diễn giải</label>
										<div class="col-sm-10 input-group input-group-sm">
											<textarea class="form-control" rows="1" id="remark" style="height: 28px"></textarea>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-4">
									<div class="row form-group">
										<label class="col-sm-4 col-form-label" title="Đối tượng thanh toán">ĐTTT *</label>
										<div class="col-sm-8 input-group">
											<input class="form-control form-control-sm input-required" id="taxcode" placeholder="ĐTTT" type="text" readonly>
											<span class="input-group-addon bg-white btn mobile-hiden text-warning" style="padding: 0 .5rem" title="Chọn đối tượng thanh toán" data-toggle="modal" data-target="#payer-modal">
												<i class="ti-search"></i>
											</span>
										</div>
										<input class="hiden-input" id="cusID" readonly>
									</div>
								</div>
								<div class="col-sm-8">
									<div class="row form-group">
										<div class="col-sm-12 col-form-label" style="font-size:10px">
											<i class="fa fa-id-card" style="font-size: 15px!important;"></i>-<span id="payer-name"> [Tên đối tượng thanh toán]</span>&emsp;
											<i class="fa fa-home" style="font-size: 15px!important;"></i>-<span id="payer-addr"> [Địa chỉ]</span>&emsp;
											<i class="fa fa-tags" style="font-size: 15px!important;"></i>-<span id="payment-type" data-value="C" style="text-transform: uppercase; font-weight: bold;"> [Hình thức thanh toán]</span>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-4">
									<div class="row form-group" id='publishRow'>
										<label class="col-sm-4 col-form-label" title="Đơn vị phát hành">ĐVPH*</label>
										<div class="col-sm-8 input-group">
											<select id="publishby" class="selectpicker" data-style="btn-default btn-sm" data-width="100%" title="Chọn đơn vị phát hành">
												<option value="HAP" selected>HAP</option>
												<option value="HATS">HATS</option>
											</select>
										</div>
									</div>
								</div>
							</div>
						</div>

					</div>
				</div>
				<div class="row">
					<div class="col-12 ibox mb-0 border-e pb-1 pt-3 mb-3">
						<table id="tbl-draft" class="table table-striped display nowrap" cellspacing="0" style="width: 99.8%">
							<thead>
								<tr>
									<th>STT</th>
									<th>
										<label class="checkbox checkbox-outline-ebony">
											<input type="checkbox" name="select-all-draft" value="*" style="display: none;">
											<span class="input-span"></span>
										</label>
									</th>
									<th>Số Phiếu Tính Cước</th>
									<th>Ngày Lập Phiếu</th>
									<th>Số Lệnh</th>
									<th>Mã ĐTTT</th>
									<th>Tên ĐTTT</th>
									<th>Thành Tiền</th>
									<th>Tiền Thuế</th>
									<th>Tổng Tiền</th>
									<th>Loại Tiền</th>
									<th>Người tạo</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
				<div class="row">
					<div class="col-12 ibox mb-0 border-e pb-1 pt-3">
						<table id="tbl-draft-details" class="table table-striped display nowrap" cellspacing="0" style="width: 99.8%">
							<thead>
								<tr>
									<th>STT</th>
									<th>Số phiếu tính cước</th>
									<th>Mã biểu cước</th>
									<th>Diễn Giải</th>
									<th>ĐVT</th>
									<th>Loại hàng</th>
									<th>Kích cỡ</th>
									<th>Hàng/rỗng </th>
									<th>Nội/ngoại</th>
									<th>Số lượng</th>
									<th>Đơn giá</th>
									<th>CK (%)</th>
									<th>Đơn giá CK</th>
									<th>Đơn giá sau CK</th>
									<th>Thành tiền</th>
									<th>VAT (%)</th>
									<th>Tiền Thuế</th>
									<th>Tổng tiền</th>
									<th>Ghi Chú</th>
									<th>Chi tiết</th>
									<th>Loại tiền</th>
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
<!-- view-inv-draft modal -->
<div class="modal fade" id="view-inv-draft-modal" tabindex="-1" role="dialog" aria-labelledby="groups-modalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document" style="display: flex;justify-content: center;align-items: center;">
		<div class="modal-content" style="border-radius: 10px!important;">
			<div class="modal-body">
				<div id="view-inv-draft-content"></div>
			</div>
			<div class="modal-footer p-2">
				<button type="button" id="print-inv-draft" data-loading-text="<i class='la la-spinner spinner'></i>Đang in" class="btn btn-sm btn-outline-primary">
					<i class="fa fa-print"></i>
					In
				</button>
				<button type="button" class="btn btn-sm btn-outline-secondary" data-dismiss="modal">
					<i class="fa fa-close"></i>
					Đóng lại
				</button>
			</div>
		</div>
	</div>
</div>

<!--payment success modal-->
<div class="modal fade" id="payment-success-modal" tabindex="-1" role="dialog" aria-labelledby="groups-modalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-dialog-mw" role="document" style="min-width: 550px">
		<div class="modal-content" style="border-radius: 20px!important;">
			<div class="modal-body" style="padding: 50px 50px 10px">
				<h1 class="text-center font-bold mb-5">HOÀN TẤT !</h1>
				<div class="text-center">
					<span class="success-head-icon"><i class="fa fa-check"></i></span>
				</div>
				<h5 class="mb-5 text-center">Hóa đơn đã được phát hành thành công!</h5>

				<ul class="ml-5">
					<li>
						<h5>Số lệnh: <span id="inv-order-no" class="font-bold" style="font-size: 20px; color: #c5285e"></span></h5>
					</li>
					<li>
						<h5>Số PIN: <span id="inv-pin-code" class="font-bold" style="font-size: 20px; color: #c5285e"></span></h5>
					</li>
					<li>
						<h5>Số hóa đơn: <span id="inv-no" class="font-bold" style="font-size: 20px; color: #c5285e"></span></h5>
					</li>

					<li>
						<h5>Số tiền: <span id="inv-tamount" class="font-bold" style="font-size: 20px; color: #c5285e"></span></h5>
					</li>
					<li>
						<h5>Người đại diện: <span id="inv-payer-name" class="font-bold" style="font-size: 20px;"></span></h5>
					</li>
					<li>
						<div class="input-group">
							<label class="col-form-label font-normal" style="font-size: 20px">Email: &ensp;</label>
							<input id="inv-payer-email" class="form-control form-control-sm" type="text">
						</div>

					</li>
				</ul>

			</div>
			<div class="modal-footer" style="display: block!important">
				<div class="row">
					<div class="col-sm-4">
						<a class="btn btn-lg btn-default btn-rounded btn-block" data-dismiss="modal">Đóng lại</a>
					</div>
					<div class="col-sm-4">
						<button class="btn btn-lg btn-outline-primary btn-rounded btn-block" id="view-inv">In hóa đơn</button>
					</div>
					<div class="col-sm-4">
						<!-- <a class="btn btn-lg btn-outline-warning btn-rounded btn-block" id="send-email">Gởi email</a> -->
						<button type="button" class="btn btn-lg btn-outline-warning btn-rounded btn-block" id="send-email" data-loading-text="<i class='la la-spinner spinner'></i>Đang gởi">
							Gởi email
						</button>
					</div>
				</div>
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

<div class="m-show-modal">
	<div class="m-modal-background">

	</div>
	<div class="m-modal-content">
		<iframe id="file-show-content" width="100%" height="100%" type="application/pdf" style="border:none"></iframe>
	</div>
	<div class="m-close-modal" style="display: none;">
		<i class="la la-close" style="font-size: 21px; font-weight: bolder" title="Đóng"></i>
	</div>
</div>

<div id="Print-INV" class="m-hidden">
</div>

<script src="<?= base_url('assets/js/jsprint.js'); ?>"></script>
<script type="text/javascript">
	var tempINV = `<div class="INV-content" style="height:578px;margin-left:41.58px;position:relative;font-size:1em;font-family:'Arial','Sans-serif';page-break-after:always">
        <span style="position: absolute;z-index: 1;top: 132.25px; left: 302.4px;" class="INV_DAY"></span>
        <span style="position: absolute;z-index: 1;top: 132.25px; left: 359.1px;" class="INV_MONTH"></span>
        <span style="position: absolute;z-index: 1;top: 132.25px; left: 415.8px;" class="INV_YEAR"></span>

        <span style="position: absolute;z-index: 1;top: 170px; left: 94px;" class="CusName"></span>

        <span style="position: absolute;z-index: 1;top: 188.5px; left: 94px;" class="PAYER"></span>
        <span style="position: absolute;z-index: 1;top: 188.5px; left: 586px;" class="HTTT">TM</span>
        <span style="position: absolute;z-index: 1;top: 188.5px; left: 652px;" class="CURRENCYID">VND</span>

        <span style="position: absolute;z-index: 1;top: 205px; left: 57px; font-size: 0.9em!important" class="Address"></span>

        <span style="position: absolute;z-index: 1;top: 221px; left: 95px;" class="SO_TK"></span>
        <span style="position: absolute;z-index: 1;top: 221px; left: 529px;" class="BAI_KHO"></span>

        <div id="inv-list" style="text-align: center;position: absolute;z-index: 1;top: 283.5px; left:9.45px">
            <table>
                <tbody>
                    
                </tbody>
            </table>
        </div>

        <span style="position: absolute;z-index: 1;top: 387.5px; left: 510px; width: 207px; text-align: right;" class="SUB_AMOUNT"></span>
        <span style="position: absolute;z-index: 1;top: 415.8px; left: 113.4px;" class="VAT_RATE"></span>
        <span style="position: absolute;z-index: 1;top: 415.8px; left: 510px; width: 207px;text-align: right;" class="VAT"></span>
        <span style="position: absolute;z-index: 1;top: 431px; left: 510px; width: 207px;text-align: right;" class="TAMOUNT"></span>
        <span style="position: absolute;z-index: 1;top: 445px; left: 133px;" class="AmountInWords">hai trăm ngàn đồng</span>
        <span style="position: absolute;z-index: 1;top: 539px; left: 539px;" class="UserName"></span>
    </div>`;

	var tempRowINV = `<tr style="border: 1px solid #ddd">
        <td style="width: 31px;height: 19px; text-align: center;" class="STT"></td>
        <td style="width: 323.2px;height: 19px; text-align: left;" class="TRF_DESC"></td>
        <td style="width: 48px;height: 19px; text-align: center;" class="UNIT_NM"></td>
        <td style="width: 66px;height: 19px; text-align: right;" class="QTY"></td>
        <td style="width: 94.5px;height: 19px; text-align: right;" class="UNIT_RATE"></td>
        <td style="width: 132.3px;height: 19px; text-align: right;" class="AMOUNT"></td>
    </tr>`;
</script>

<script type="text/javascript">
	moment.tz.setDefault('Asia/Ho_Chi_Minh');
	$(document).ready(function() {
		$("#m-inv").prop("disabled", <?= !isset($isDup) || $isDup || !isset($ssInvInfo) || count($ssInvInfo) == 0; ?>);

		var tblDraft = $("#tbl-draft"),
			tblDraftDetail = $("#tbl-draft-details"),
			tblPayer = $("#search-payer"),
			_colDraft = ["STT", "Select", "DRAFT_INV_NO", "DRAFT_INV_DATE", "REF_NO", "PAYER", "CusName", "AMOUNT", "VAT", "TAMOUNT", "CURRENCYID", "CreatedBy"],
			_colDraftDetail = ["STT", "DRAFT_INV_NO", "TRF_CODE", "TRF_DESC", "INV_UNIT", "CARGO_NAME", "SZ", "FE", "IsLocal", "QTY", "standard_rate", "DIS_RATE", "extra_rate", "UNIT_RATE", "AMOUNT", "VAT_RATE", "VAT", "TAMOUNT", "Remark", "TRF_DESC_MORE", "CURRENCYID", "CARGO_TYPE"],
			_colPayer = ["STT", "CusID", "VAT_CD", "CusName", "Address", "CusType"];

		var _draftDetails = [],
			_drafts = [],
			_payers = [];

		var _invData = [],
			_invInfo = null,
			_amtwords,
			_formatNum = '#,###', //them moi lam tron so
			_formatNumQty_Unit = '#,###'; //lam tron so luong+don gia theo yeu cau KT

		var _paymentMethods = [];
		<?php if (isset($paymentMethod) && count($paymentMethod) > 0) { ?>
			_paymentMethods = <?= json_encode($paymentMethod); ?>;
		<?php } ?>

		//---------datepicker modified---------
		$('#dateStart, #dateEnd').datepicker({
			format: "dd/mm/yyyy",
			startDate: moment().format('DD/MM/YYYY'),
			todayHighlight: true,
			autoclose: true
		});

		$('#dateStart + span').on('click', function() {
			$('#dateStart').val("*");
		});

		var dtPayer = tblPayer.DataTable({
			info: false,
			paging: false,
			searching: false,
			buttons: [],
			scrollY: '25vh'
		});

		var dtDraft = tblDraft.DataTable({
			scrollY: '20vh',
			columnDefs: [{
					type: "num",
					className: "text-center",
					targets: _colDraft.indexOf('STT')
				},
				{
					orderable: false,
					className: "text-center",
					targets: _colDraft.indexOf('Select')
				},
				{
					className: "text-center",
					targets: _colDraft.getIndexs(['DRAFT_INV_NO', 'DRAFT_INV_DATE', 'PAYER', 'CURRENCYID'])
				},
				{
					className: "text-right",
					targets: _colDraft.getIndexs(["AMOUNT", "VAT", "TAMOUNT"]),
					render: $.fn.dataTable.render.number(',', '.', 2)
				},
				{
					render: function(data, type, full, meta) {
						return "<div class='wrap-text width-350'>" + data + "</div>";
					},
					targets: _colDraft.indexOf("CusName")
				}
			],
			order: [
				[_colDraft.indexOf('STT'), 'asc']
			],
			paging: false,
			rowReorder: false,
			buttons: [],
			select: {
				style: 'api'
			}
		});

		var dtDraftDetail = tblDraftDetail.DataTable({
			scrollY: '20vh',
			columnDefs: [{
					type: "num",
					className: "text-center",
					targets: _colDraftDetail.indexOf('STT')
				},
				{
					visible: false,
					targets: _colDraftDetail.indexOf('CURRENCYID')
				},
				{
					className: "text-right",
					targets: _colDraftDetail.getIndexs(["QTY", "standard_rate", "DIS_RATE", "extra_rate", "UNIT_RATE", "AMOUNT", "VAT_RATE", "VAT", "TAMOUNT"]),
					render: $.fn.dataTable.render.number(',', '.', 2)
				}
			],
			searching: false,
			info: false,
			order: [
				[_colDraft.indexOf('STT'), 'asc']
			],
			paging: false,
			rowReorder: false,
			buttons: []
		});

		tblPayer.DataTable({
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

		fromDate.datepicker({
			controlType: 'select',
			oneLine: true,
			// minDate: _maxDateDateIn,
			dateFormat: 'dd/mm/yy',
			timeInput: true,
			onClose: function(dateText, inst) {
				if (toDate.val() != '') {
					var testStartDate = fromDate.datetimepicker('getDate');
					var testEndDate = toDate.datetimepicker('getDate');
					if (testStartDate > testEndDate)
						toDate.datetimepicker('setDate', testStartDate);
				} else {
					toDate.val(dateText);
				}
			},
			onSelect: function(selectedDateTime) {
				toDate.datetimepicker('option', 'minDate', fromDate.datetimepicker('getDate'));
			}
		});

		toDate.datepicker({
			controlType: 'select',
			oneLine: true,
			// minDate: _maxDateDateIn,
			dateFormat: 'dd/mm/yy',
			timeInput: true,
			onClose: function(dateText, inst) {
				if (fromDate.val() != '') {
					var testStartDate = fromDate.datetimepicker('getDate');
					var testEndDate = toDate.datetimepicker('getDate');
					if (testStartDate > testEndDate)
						fromDate.datetimepicker('setDate', testEndDate);
				} else {
					fromDate.val(dateText);
				}
			},
			onSelect: function(selectedDateTime) {
				fromDate.datetimepicker('option', 'maxDate', toDate.datetimepicker('getDate'));
			}
		});

		fromDate.val(moment().subtract('month', 1).format('DD/MM/YYYY'));
		toDate.val(moment().format('DD/MM/YYYY'));
		//end set fromdate, todate

		$("#invDate").datetimepicker({
			controlType: 'select',
			oneLine: true,
			dateFormat: 'dd/mm/yy',
			timeFormat: 'HH:mm:00',
			timeInput: true
		});

		$("#invDate").val(moment().format('DD/MM/YYYY HH:mm:ss'));

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

		//------USING MANUAL INVOICE

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
								toNo: $("#inv-no-to").val(),
								paymentType: 'CAS' //them moi hd thu sau
							}

							var formData = {
								'action': 'edit',
								'act': 'use_manual_Inv',
								'useInvData': data
							};

							$("#change-ssinv-modal .modal-content").blockUI();

							$.ajax({
								url: "<?= site_url(md5('Invoice') . '/' . md5('invPublishInvoice')); ?>",
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

										$("#m-inv").prop("disabled", true);

										toastr["error"]("Số hóa đơn bắt đầu [" + invNo + "] đã tồn tại trong hệ thống!");
										return;
									}

									$("#change-ssinv-modal").modal('hide');
									toastr["success"]("Xác nhận sử dụng Số HĐ [" + invNo + "] thành công!");
									$("#ss-invNo").text(invNo);
									$("#change-ssinvno").attr("title", "Thay đổi hóa đơn sử dụng tiếp theo");
									$("#m-inv").prop("disabled", false);
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

		$("#load-data").on("click", function() {
			loadDraft();
		});

		$('#payer-modal').on('shown.bs.modal', function(e) {
			$($.fn.dataTable.tables(true)).DataTable()
				.columns
				.adjust();
		});

		$(".input-required").on("input change", function(e) {
			$(e.target).removeClass("error");
			$(e.target).parent().removeClass("error");
		});

		$("#e-inv").on("click", function() {
			if ($(".input-required").has_required()) {
				toastr["error"]("Các thông tin bắt buộc không được để trống!");
				return;
			}

			if (tblDraft.find("input[name='select-draft']:checked").length == 0) {
				toastr["error"]("Chưa có phiếu tính cước nào được chọn!");
				return;
			}

			// $.confirm({
			// 	columnClass: 'col-md-5 col-md-offset-5',
			// 	title: 'Lý do hủy lệnh',
			// 	type: 'orange',
			//     icon: 'fa fa-warning',
			//     content: 'Xác nhận xuất hóa đơn điện tử?',
			// 	content: '<div class="form-group"><span class="text-primary">Địa chỉ email (dùng dấu phẩy <,> để phân cách các mail)</span></div>'
			// 			+'<div class="form-group">'
			// 				+'<input autofocus class="form-control form-control-sm font-size-14" id="mail" placeholder="Nhập địa chỉ mail nhận HĐĐT" rows=5></input>'
			// 			+'</div>',
			// 	buttons: {
			// 		ok: {
			// 			text: 'Tiếp tục',
			// 			btnClass: 'btn-sm btn-primary btn-confirm',
			// 			keys: ['Enter'],
			// 			action: function(){
			// 				var input = this.$content.find('input#mail');
			// 				var errorText = this.$content.find('.text-danger');
			// 				if(!input.val().trim()){
			// 					$.alert({
			// 						title: "Thông báo",
			// 						content: "Vui lòng nhập địa chỉ mail nhận HĐĐT!.",
			// 						type: 'red'
			// 					});
			// 					return false;
			// 				}else{
			// 					publishInv();
			// 				}
			// 			}
			// 		},
			//         cancel: {
			//             text: 'Hủy bỏ',
			//             btnClass: 'btn-default',
			//             keys: ['ESC']
			//         }
			// 	}
			// });

			$.confirm({
				title: 'Thông báo!',
				type: 'orange',
				icon: 'fa fa-warning',
				content: 'Xác nhận xuất hóa đơn điện tử?',
				buttons: {
					ok: {
						text: 'Tiếp tục',
						btnClass: 'btn-warning',
						keys: ['Enter'],
						action: function() {
							publishInv();
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

		$("#view-draft-inv").on("click", function() {
			if ($(".input-required").has_required()) {
				toastr["error"]("Các thông tin bắt buộc không được để trống!");
				return;
			}

			if (tblDraft.find("input[name='select-draft']:checked").length == 0) {
				toastr["error"]("Chưa có phiếu tính cước nào được chọn!");
				return;
			}

			// if (tblDraft.find("input[name='select-draft']:checked").length > 1) {
			// 	toastr["error"]("Chỉ được xem 1 hóa đơn cùng lúc!");
			// 	return;
			// }

			publishInv(true);
		});

		$('#print-inv-draft').on('click', function() {
			$('#view-inv-draft-content').print();
		})

		$("#m-inv").on("click", function() {
			if ($(".input-required").has_required()) {
				toastr["error"]("Các thông tin bắt buộc không được để trống!");
				return;
			}

			$.confirm({
				title: 'Thông báo!',
				type: 'orange',
				icon: 'fa fa-warning',
				content: 'Xác nhận xuất hóa đơn giấy?',
				buttons: {
					ok: {
						text: 'Tiếp tục',
						btnClass: 'btn-warning',
						keys: ['Enter'],
						action: function() {
							var invInfo = {
								INV_DATE: moment().format('DD/MM/YYYY HH:mm:ss'),
								REMARK: $("#remark").val()
							};

							$(".ibox").first().blockUI();

							saveData(invInfo, 'm-inv');
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

		$(document).on("click", "#view-inv", function() {
			if (_invData && _invData.length > 0) {
				printInv(_invData, _amtwords); //them moi lam tron so
				return;
			}
			var src = '';
			if (_invInfo) {
				src = '<?= site_url(md5("InvoiceManagement") . '/' . md5("getInvView") . "?"); ?>' + (new URLSearchParams(_invInfo)).toString();
			}

			$('#file-show-content').attr('src', src);

			$('#file-show-content').on('load', function() {
				document.getElementById("file-show-content").contentWindow.print();
			})
			// $('.m-show-modal').show('fade', function() {
			// 	window.setTimeout(function() {
			// 		$(".m-close-modal").show("slide", {
			// 			direction: "up"
			// 		}, 300);
			// 	}, 3000);
			// });
		});
		$('#publishRow').addClass('hiden-input');

		$("#send-email").on("click", function() {
			sendMail();
		});

		$("#print-draft").on("click", function() {
			var draftNos = tblDraft.find("input[name='select-draft']:checked")
				.closest("tr")
				.find("td:eq(" + _colDraft.indexOf("DRAFT_INV_NO") + ")")
				.map(function() {
					return $(this).text();
				})
				.get();

			// var draftDetails = _draftDetails
			// 	.filter(p => draftNos.indexOf(p.DRAFT_INV_NO) != -1)
			// 	.map(function(item) {
			// 		var cusID = _drafts.filter(x => x.DRAFT_INV_NO == item["DRAFT_INV_NO"])
			// 			.map(c => c.PAYER)[0];
			// 		var taxCode = _payers.filter(p => p.CusID == cusID)
			// 			.map(c => c.VAT_CD)[0];
			// 		var payerName = _payers.filter(p => p.CusID == cusID && p.VAT_CD == taxCode)
			// 			.map(c => c.CusName)[0];

			// 		return {
			// 			"DRAFT_INV_NO": item["DRAFT_INV_NO"],
			// 			"DRAFT_INV_DATE": _drafts.filter(x => x.DRAFT_INV_NO == item["DRAFT_INV_NO"])
			// 				.map(c => c.DRAFT_INV_DATE)[0],
			// 			"TRF_DESC": item["TRF_DESC"],
			// 			"INV_UNIT": item["INV_UNIT"],
			// 			"QTY": item["QTY"],
			// 			"standard_rate": item["standard_rate"],
			// 			"UNIT_RATE": item['UNIT_RATE'],
			// 			"VAT": item["VAT"],
			// 			"TAMOUNT": item["TAMOUNT"],
			// 			"TAX_CODE": taxCode,
			// 			"PAYER_NAME": payerName,
			// 			"CURRENCYID": _drafts.filter(x => x.DRAFT_INV_NO == item["DRAFT_INV_NO"])
			// 				.map(c => c.CURRENCYID)[0] //doc tien usd
			// 		};
			// 	});

			printDraft("<?= site_url(md5('ExportRPT') . '/' . md5('viewDraftPDF')); ?>", draftNos, $(this));
		});

		tblDraft.on("change", "input[name='select-draft']", function(e) {
			var chk = $(e.target);
			var isChecked = chk.is(":checked");
			var dtDetails = tblDraftDetail.DataTable();

			var invDraftNo = chk.closest('tr').find('td:eq(' + _colDraft.indexOf("DRAFT_INV_NO") + ')').text();

			if (isChecked) {

				chk.attr("checked", "");
				chk.val("1");
				tblDraft.DataTable().rows(chk.closest("tr")).select();

				var drDetails = _draftDetails.filter(p => p["DRAFT_INV_NO"] == invDraftNo);
				var currencyId = _drafts.filter(p => p.DRAFT_INV_NO == invDraftNo).map(x => x.CURRENCYID)[0];
				drDetails.map(p => p['CURRENCYID'] = currencyId);

				addRowToDraftDetail(drDetails);
			} else {

				chk.removeAttr("checked");
				chk.val("0");
				tblDraft.DataTable().rows(chk.closest("tr")).deselect();

				var delrowIdxes = dtDetails.rows(function(idx, data, node) {
						return data[_colDraftDetail.indexOf("DRAFT_INV_NO")] == invDraftNo ?
							true : false;
					})
					.indexes().toArray();
				dtDetails.rows(delrowIdxes).remove().draw();
				tblDraftDetail.updateSTT();
			}

			checkDraft = tblDraft.find("input[name='select-draft']:checked");
			if (checkDraft.length == 1) {
				var cusid = checkDraft.closest("tr").find("td:eq(" + _colDraft.indexOf("PAYER") + ")").text();
				var payerSelected = _payers.filter(p => p.CusID == cusid);

				$('#taxcode').val(payerSelected.map(x => x.VAT_CD));
				$("#cusID").val(cusid);
				$("#paymentMethod").find('option').remove();

				var draftNo = checkDraft.closest("tr").find("td:eq(" + _colDraft.indexOf("DRAFT_INV_NO") + ")").text();
				var draftItem = _drafts.filter(p => p.DRAFT_INV_NO == draftNo);
				var currencyId = draftItem.map(x => x.CURRENCYID)[0];
				var invType = draftItem.map(p => p.INV_TYPE)[0];

				if (invType === 'CRE') {
					$('#publishRow').removeClass('hiden-input');
				} else {
					$('#publishRow').addClass('hiden-input');
					$('#publishby').val('');

				}

				var methods = _paymentMethods.filter(p => p.ACC_TYPE == invType);
				methods.map((p, i) => {
					$("#paymentMethod").append(`<option value="${p.ACC_CD}"  ${i == 0 && methods.length == 1 ? "selected" : ""}>${p.ACC_NO}</option>`);
				});

				$("#invType").val(currencyId);

				fillPayer();
			} else {
				if ($('#taxcode').val()) {
					clearPayer();
				}

				$("#invType").val("")
			}

			$("#paymentMethod, #invType, #publishby").selectpicker("refresh");

			var crCell = chk.closest('td');
			tblDraft.DataTable().cell(crCell).data(crCell.html());
		});

		function loadDraft() {
			tblDraft.dataTable().fnClearTable();
			tblDraftDetail.dataTable().fnClearTable();
			tblDraft.waitingLoad();

			var btn = $("#load-data");
			btn.button("loading");

			var formData = {
				"action": "view",
				"act": "search_draft",
				"fromDate": $("#fromDate").val(),
				"toDate": $("#toDate").val(),
				"paymentType": $("#paymentType").val(),
				"currency": $("#moneyType").val(),
				"createdBy": $("#createdBy").val()
			};

			$.ajax({
				url: "<?= site_url(md5('Invoice') . '/' . md5('invPublishInvoice')); ?>",
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
					_draftDetails = [];
					_drafts = [];

					if (data.drafts && data.drafts.length > 0) {
						_drafts = data.drafts;
						_draftDetails = data.draftdetails;
						// var drafts = data.drafts.sort((a,b) => (a.OrderNo > b.OrderNo) ? 1 : ((b.OrderNo > a.OrderNo) ? -1 : 0));

						$.each(data.drafts, function(i, item) {
							var r = [];
							$.each(_colDraft, function(idx, colname) {
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
									case "DRAFT_INV_DATE":
										val = getDateTime(item[colname]);
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

					tblDraft.dataTable().fnClearTable();
					if (rows.length > 0) {
						tblDraft.dataTable().fnAddData(rows);
					}
				},
				error: function(err) {
					tblDraft.dataTable().fnClearTable();
					btn.button("reset");
					$('.toast').remove();
					toastr['error']("Có lỗi xảy ra! <br/>  Vui lòng liên hệ với bộ phận kỹ thuật! ");
					console.log(err);
				}
			});
		}

		function addRowToDraftDetail(item) {
			var rows = [];
			var currentSTT = tblDraftDetail.DataTable().rows().count();
			$.each(item, function(i, item) {
				var r = [];
				$.each(_colDraftDetail, function(idx, colname) {
					var val = "";
					switch (colname) {
						case "STT":
							val = currentSTT + i + 1;
							break;
						case "FE":
							val = "<input class='hiden-input' value='" + item[colname] + "'>" +
								(item[colname] == "F" ? "Hàng" : (item[colname] == "E" ? "Rỗng" : item[colname]));
							break;
						case "IsLocal":
							val = "<input class='hiden-input' value='" + item[colname] + "'>" +
								(item[colname] == "F" ? "Ngoại" : (item[colname] == "L" ? "Nội" : item[colname]));
							break;
						default:
							val = item[colname] ? item[colname] : "";
							break;
					}
					r.push(val);
				});

				rows.push(r);

			});

			if (rows.length > 0) {
				tblDraftDetail.dataTable().fnAddData(rows);
			}
		}

		function sendMail() {
			$("#send-email").button("loading");
			var formData = {
				"action": "view",
				"act": "send_mail",
				"args": {
					"inv": $("#inv-no").text(),
					"orderNo": $("#inv-order-no").text(),
					"pinCode": $("#inv-pin-code").text(),
					"amount": $("#inv-tamount").text(),
					"mailTo": $("#inv-payer-email").val(),
					"reservationCode": _invInfo['reservationCode'] || ''
				}
			};

			$.ajax({
				url: "<?= site_url(md5('Invoice') . '/' . md5('invPublishInvoice')); ?>",
				dataType: 'json',
				data: formData,
				type: 'POST',
				success: function(data) {
					$("#send-email").button("reset");
					if (data.deny) {
						toastr["error"](data.deny);
						return;
					}

					$(".toast").remove();
					if (data.result == "sent") {
						toastr["success"]("Mail đã được gởi thành công!");
					} else {
						toastr["error"](data.result);
					}
				},
				error: function(err) {
					$("#send-email").button("reset");
					console.log(err);
					toastr["error"]("Có lỗi xảy ra! Vui lòng liên hệ với kỹ thuật viên! <br/>Cảm ơn!");
				}
			});
		}

		function load_payer() {
			tblPayer.waitingLoad();

			$.ajax({
				url: "<?= site_url(md5('Invoice') . '/' . md5('invPublishInvoice')); ?>",
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
				$('#payment-type').attr('data', py[0].CusType);
				$('#payment-type').text(py[0].CusType == 'M' ? "Thu ngay" : "Thu sau");

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
			$('#payment-type').attr('data', "");
			$('#payment-type').text(" [Hình thức thanh toán]");
		}

		function getPayerType(py) {
			if (py.IsOpr == "1") return "SHP";
			if (py.IsAgency == "1") return "SHA";
			if (py.IsOwner == "1") return "CNS";
			if (py.IsLogis == "1") return "FWD";
			if (py.IsTrans == "1") return "TRK";
			if (py.IsOther == "1") return "DIF";
			return "";
		}

		function publishInv(isViewDraft = false) {
			var draftNos = tblDraft.getData().filter(p => p[_colDraft.indexOf("Select")] == "1").map(x => x[_colDraft.indexOf("DRAFT_INV_NO")]);
			var datas = tblDraftDetail.getDataByColumns(_colDraftDetail);

			if (draftNos.length == 0 || datas.length == 0) {
				$(".ibox").first().unblock();
				$('.toast').remove();
				toastr['warning']('Chọn phiếu tính cước để phát hành hóa đơn!');
				return;
			}

			_invInfo = null;
			var selectDraft = _drafts.filter(p => p.DRAFT_INV_NO == draftNos[0])[0];
			var invType = selectDraft['INV_TYPE'];
			var shipKey = selectDraft['ShipKey'];
			var pinCode = selectDraft['PinCode'] ? selectDraft['PinCode'] : '';
			var formData = {
				cusTaxCode: $('#taxcode').val(),
				cusAddr: $('#payer-addr').text(),
				cusName: $('#payer-name').text(),
				cusEmail: $('#inv-payer-email').val(),
				sum_amount: datas.map(x => x.AMOUNT).reduce((a, b) => parseFloat(a) + parseFloat(b)),
				vat_amount: datas.map(x => x.VAT).reduce((a, b) => parseFloat(a) + parseFloat(b)),
				total_amount: datas.map(x => x.TAMOUNT).reduce((a, b) => parseFloat(a) + parseFloat(b)),
				is_eport: $("input[name='eport-inv']").is(":checked") ? "1" : "0",
				inv_type: $('#invType').val(),
				paymentMethod: $("#paymentMethod").val(),
				isCredit: String(invType).trim().toUpperCase() === 'CRE' ? "1" : "0",
				note: $('#remark').val(),
				shipKey: shipKey,
				pinCode : pinCode,
				datas: datas
			};
			$("#publishby").val() ? formData['publishBy'] = $("#publishby").val() : '';
			$(".ibox").first().blockUI();

			let url = isViewDraft ?
				"<?= site_url(md5('InvoiceManagement') . '/' . md5('viewDraftInv')); ?>" :
				"<?= site_url(md5('InvoiceManagement') . '/' . md5('importAndPublish')); ?>"

			$.ajax({
				url: url,
				dataType: 'json',
				data: formData,
				type: 'POST',
				success: function(data) {
					if (data.deny || data.error) {
						$(".ibox").first().unblock();
						toastr["error"](data.deny || data.error);
						return;
					}

					if (isViewDraft) {
						$(".ibox").first().unblock();
						if (!data.success) {
							toastr["error"](data.message || data.error);
							return;
						}

						if (data.pdfData) {
							// data should be your response data in base64 format
							const blob = dataURItoBlob(data.pdfData);
							const url = URL.createObjectURL(blob);
							// to open the PDF in a new window
							window.open(url, '_blank');
							return;
						}

						if (data.html) {
							$('#view-inv-draft-content').html(data.html);
							$('#view-inv-draft-modal').modal('show');
							return;
						}
					}

					if (data.error) {
						$(".ibox").first().unblock();
						$(".toast").remove();
						toastr["error"](data.error);
						return;
					}

					// data["INV_DATE"] = moment().format('DD/MM/YYYY HH:mm:ss');
					data["REMARK"] = $("#remark").val();
					_invInfo = data;

					saveData(data, 'e-inv');
				},
				error: function(err) {
					$(".ibox").first().unblock();
					console.log(err);
				}
			});
		}

		function saveData(invInfo, pubType) {
			var draftNos = tblDraft.getData().filter(p => p[_colDraft.indexOf("Select")] == "1").map(x => x[_colDraft.indexOf("DRAFT_INV_NO")]);

			if (draftNos.length == 0) {
				$(".ibox").first().unblock();
				$('.toast').remove();
				toastr['warning']('Chọn phiếu tính cước để phát hành hóa đơn!');
				return;
			}

			var draftData = _drafts.filter(p => draftNos.indexOf(p.DRAFT_INV_NO) != "-1")
				.map(function(item) {
					return {
						"DRAFT_INV_NO": item["DRAFT_INV_NO"],
						"REF_NO": item["REF_NO"],
						"ShipKey": item["ShipKey"],
						"ShipID": item["ShipID"],
						"ShipYear": item["ShipYear"],
						"ShipVoy": item["ShipVoy"],
						"OPR": item["OPR"],
						"PAYER_TYPE": item["PAYER_TYPE"],
						"INV_TYPE": item["INV_TYPE"],
						"ACC_CD": $("#paymentMethod").val()
					};
				});

			var allDraftDetail = _draftDetails.filter(p => draftNos.indexOf(p.DRAFT_INV_NO) != -1);

			if (allDraftDetail.length == 0) {
				$(".ibox").first().unblock();
				$('.toast').remove();
				toastr['warning']('Không thể xuất hoá đơn cho các Phiếu tính cước này!');
				return;
			}

			var draftTotal = {
				AMOUNT: allDraftDetail.map(x => x.AMOUNT).reduce((a, b) => parseFloat(a) + parseFloat(b)),
				VAT: allDraftDetail.map(x => x.VAT).reduce((a, b) => parseFloat(a) + parseFloat(b)),
				DIS_AMT: allDraftDetail.map(x => x.DIS_AMT).reduce((a, b) => parseFloat(a) + parseFloat(b)),
				TAMOUNT: allDraftDetail.map(x => x.TAMOUNT).reduce((a, b) => parseFloat(a) + parseFloat(b))
			};
			$('#publishby').val() ? draftTotal['PUBLISH_BY'] = $('#publishby').val() : '';

			var formData = {
				'action': 'add',
				'data': {
					'pubType': pubType,
					'invInfo': invInfo,
					'draftData': draftData,
					'draftTotal': draftTotal,
					'payer': $("#cusID").val(),
					'currencyId': $("#invType").val(),
					'isDFT_to_INV': $("input[name='isDFT_to_INV']:checked").val() || 1,
					'paymentType': 'CAS' //them moi hd thu sau
				}
			};

			//them moi hd thu sau
			_invData.length = 0; //reset de ko in nham hddt -> hdgiay

			$.ajax({
				url: "<?= site_url(md5('Invoice') . '/' . md5('invPublishInvoice')); ?>",
				dataType: 'json',
				data: formData,
				type: 'POST',
				success: function(data) {
					$(".ibox").first().unblock();

					if (data.error) {
						$(".toast").remove();
						toastr["error"](data.error);
						return;
					}

					if (data.isDup) {
						toastr["error"]("Hóa đơn hiện tại đã tồn tại trong hệ thống! Kiểm tra lại!");
						return;
					}

					if (data.outInfo) {
						$("#inv-order-no").text(data.outInfo.OrderNo);
						$("#inv-payer-name").text(data.outInfo.NameDD);
						$("#inv-payer-email").val(data.outInfo.Mail);
					}

					if (data.ssInvInfo) {
						$("#ss-invNo").text(data.ssInvInfo.serial + data.ssInvInfo.invno);
						if (data.hasDup) {
							$("#ss-invNo").text($("#ss-invNo").text() + " [BỊ TRÙNG]");
							$("#m-inv").prop("disabled", data.hasDup);
						}
					} else {
						$("#ss-invNo").text("Chưa khai báo!");
						$("#change-ssinvno").attr("title", "Khai báo số hóa đơn sử dụng tiếp theo");
						$("#m-inv").prop("disabled", true);
					}

					if (data.invInfo) {

						//them moi lam tron so
						if (data.invdata && data.invdata.length > 0) {
							_invData = data.invdata;
							_amtwords = data.amtwords;
							_formatNum = data.formatNum; //them moi lam tron so
							_formatNumQty_Unit = data.formatNumQty_Unit; //lam tron so luong+don gia theo yeu cau KT
						}

						// $( "#inv-prefix" ).text( data.invInfo.serial );
						$("#inv-no").text(data.invInfo.serial + data.invInfo.invno);
						$("#inv-pin-code").text(data.invInfo.fkey);
						$("#inv-tamount").text($.formatNumber(formData.data.draftTotal.TAMOUNT, {
								format: _formatNum || "#,###", //them moi lam tron so
								locale: "us"
							}) +
							" " + $("#invType").val());

						//clear selected row on draft table
						var selectedRows = tblDraft.find("input[name='select-draft']:checked")
							.closest("tr");
						tblDraft.DataTable().rows(selectedRows).remove().draw(false);
						tblDraft.updateSTT();

						//clear table draft details
						tblDraftDetail.dataTable().fnClearTable();

						$("#payment-success-modal").modal("show");

					} else {
						toastr["success"]("Lưu dữ liệu thành công!");
						location.reload(true);
					}
				},
				error: function(xhr, status, error) {
					console.log(xhr);
					$('.toast').remove();
					$(".ibox").first().unblock();
					toastr['error']("Có lỗi xảy ra khi lưu dữ liệu! Vui lòng liên hệ KTV! ");
				}
			});
		}

		function dataURItoBlob(dataURI) {
			const byteString = window.atob(dataURI);
			const arrayBuffer = new ArrayBuffer(byteString.length);
			const int8Array = new Uint8Array(arrayBuffer);
			for (let i = 0; i < byteString.length; i++) {
				int8Array[i] = byteString.charCodeAt(i);
			}
			const blob = new Blob([int8Array], {
				type: 'application/pdf'
			});
			return blob;
		}

		function printInv(data, amtwords) { //them moi lam tron so
			if (data && data.length > 0) {
				var invContent = $("#Print-INV");
				invContent.html(tempINV);
				if (localStorage.getItem("margin_hd")) {
					invContent.find('.INV-content:last').css("margin-top", localStorage.getItem("margin_hd"));
				}
				//set data for header
				var headerData = data[0];
				$.each(Object.keys(headerData), function(idx, key) {
					if (['INV_DATE'].indexOf(key) != -1) {
						var d = new Date(headerData[key]);
						var dd = d.getDate();
						var mm = d.getMonth() + 1;

						invContent.find('.INV-content:last').find('span.INV_DAY').text(dd > 9 ? dd : "0" + dd);
						invContent.find('.INV-content:last').find('span.INV_MONTH').text(mm > 9 ? mm : "0" + mm);
						invContent.find('.INV-content:last').find('span.INV_YEAR').text(d.getFullYear());
					} else if (['VAT_RATE', 'VAT'].indexOf(key) != -1) {
						let n = "";
						let checkVal = headerData['VAT_RATE'];
						if (!checkVal) {
							n = "\\";
						} else {
							if (parseFloat(headerData[key]) == 0) {
								n = "0";
							} else {
								n = $.formatNumber(headerData[key], {
									format: _formatNum, //them moi lam tron so
									locale: "us" //them moi lam tron so
								});
							}
						}

						invContent.find('.INV-content:last').find('span.' + key).text(n);
					} else if (['SUB_AMOUNT', 'TAMOUNT'].indexOf(key) != -1) {
						let n = "";
						if (parseFloat(headerData[key]) == 0) {
							n = "0";
						} else {
							n = $.formatNumber(headerData[key], {
								format: _formatNum, //them moi lam tron so
								locale: "us" //them moi lam tron so
							});
						}

						invContent.find('.INV-content:last').find('span.' + key).text(n);
					}
					//check format taxcode for show to invoice
					else if (['PAYER'].indexOf(key) != -1) {
						var taxCode = headerData[key];
						var checkTaxCode = headerData[key].replace("-", "");
						if ([10, 13].indexOf(checkTaxCode.length) == "-1" || isNaN(checkTaxCode)) {
							taxCode = '';
						}
						invContent.find('.INV-content:last').find('span.' + key).text(taxCode);
					}
					//cac truong hop khac
					else {
						invContent.find('.INV-content:last').find('span.' + key).text(headerData[key]);
					}
				});

				//tiền = chữ
				invContent.find('.INV-content:last').find('span.AmountInWords').text(amtwords ? amtwords.toUpperCase() : ""); //doc tien usd

				//set data for each row and append to table
				var i = 1;
				$.each(data, function(idx, item) {
					invContent.find('.INV-content:last').find("table tbody").append(tempRowINV);
					var lastRow = invContent.find("table tbody tr:last");
					lastRow.find('td.STT').text(i++);
					$.each(Object.keys(item), function(ix, key) {
						if (['AMOUNT'].indexOf(key) != -1) {
							var n = $.formatNumber(item[key], {
								format: _formatNum, //them moi lam tron so
								locale: "us"
							});
							lastRow.find('td.' + key).text(n);
						} else if (['QTY', 'UNIT_RATE'].indexOf(key) != -1) { //lam tron so luong+don gia theo yeu cau KT
							var n = $.formatNumber(item[key], {
								format: _formatNumQty_Unit, //lam tron so luong+don gia theo yeu cau KT
								locale: "us"
							});
							lastRow.find('td.' + key).text(n);
						} else if (key == 'TRF_DESC') {
							let conts = item['Remark'] || '';
							let blbk = item['TRF_DESC_MORE'] || '';
							let desc = item[key];
							if (conts && conts.split(',').length <= 5) {
								desc += ` ${conts}`;
							} else if (blbk) {
								desc += ` ${blbk}`;
							}

							lastRow.find('td.' + key).text(desc);
						} else {
							lastRow.find('td.' + key).text(item[key]);
						}
					});
				});

				invContent.print();
				invContent.html('');
				// var win = window.open("", "_blank");
				//     $(win.document.body).append(invContent);
			} else {
				toastr["warning"]("Không thể in!<br> Vui lòng kiểm tra lại!")
			}
		}

		$('.m-modal-background').click(function() {
			$('.m-show-modal').hide('fade');
		});

		$('.m-close-modal').click(function() {
			$(this).hide();
			$('.m-show-modal').hide('fade');
		});

		$(document).on("keydown", function(e) {
			if (e.keyCode == 27) {
				$('.m-close-modal').trigger("click");;
			}
		});

		window.setInterval(function() {
			$("#invDate").val(moment().format('DD/MM/YYYY HH:mm:ss'));
		}, 1000);
	});
</script>

<script src="<?= base_url('assets/js/printlaser.ebilling.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/moment/min/moment.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/bootstrap-select/dist/js/bootstrap-select.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/jquery-confirm/jquery-confirm.min.js'); ?>"></script>
<!--format number-->
<script src="<?= base_url('assets/js/jshashtable-2.1.js'); ?>"></script>
<script src="<?= base_url('assets/js/jquery.numberformatter-1.2.3.min.js'); ?>"></script>