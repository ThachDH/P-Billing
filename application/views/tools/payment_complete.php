<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<link href="<?= base_url('assets/vendors/bootstrap-select/dist/css/bootstrap-select.min.css'); ?>" rel="stylesheet" />
<link href="<?= base_url('assets/vendors/jquery-confirm/jquery-confirm.min.css'); ?>" rel="stylesheet" />
<link href="<?= base_url('assets/css//ebilling.css'); ?>" rel="stylesheet" />

<style>
  .m-row-selected {
    background: violet;
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

  #payer-modal .dataTables_filter {
    padding-left: 10px !important;
  }

  .font-size-14 {
    font-size: 14px !important;
  }

  .box-group {
    border: 1px solid #ccc !important;
    margin-left: -10px;
    padding-top: 10px !important;
    border-radius: 3px !important;
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
    top: 2px;
    right: 11vw;
    color: #fff;
    cursor: pointer;
  }

  .m-close-modal i {
    padding: 5px;
    border-radius: 50%;
  }

  .m-close-modal i:hover {
    background-color: rgba(255, 255, 255, 0.1);
  }

  .m-hidden {
    display: none;
  }
</style>

<div class="row" style="font-size: 12px!important;">
  <div class="col-xl-12">
    <div class="ibox collapsible-box">
      <i class="la la-angle-double-up dock-right"></i>
      <div class="ibox-head">
        <div class="ibox-title">XÁC NHẬN THANH TOÁN</div>
        <div class="button-bar-group mr-3">
          <button type="button" id="load-data" title="Nạp dữ liệu" data-loading-text="<i class='la la-spinner spinner'></i>Đang nạp" class="btn btn-sm btn-primary mr-1">
            <i class="fa fa-refresh"></i>
            Nạp dữ liệu
          </button>
          <button type="button" id="confirm-payment" title="Thanh toán" data-loading-text="<i class='la la-spinner spinner'></i>Đang xác thực" class="btn btn-warning btn-sm mr-1">
            <i class="fa fa-print"></i>
            Thanh toán
          </button>
        </div>
      </div>
      <div class="ibox-body pt-3 pb-3 bg-f9 border-e">
        <div class="row my-box pb-1">
          <div class="col-sm-12 col-xs-12 mt-3">
            <div class="row form-group">
              <div class="col-sm-8">
                <div class="row">
                  <div class="col-sm-6">
                    <div class="row form-group">
                      <label class="col-sm-4 col-form-label" title="Từ ngày">Từ ngày</label>
                      <div class="col-sm-8">
                        <input class="form-control form-control-sm" id="issueDateFrom" type="text" placeholder="Từ ngày">
                      </div>
                    </div>
                    <div class="row form-group">
                      <label class="col-sm-4 col-form-label" title="Số lệnh">Số lệnh</label>
                      <div class="col-sm-8">
                        <input class="form-control form-control-sm" id="ord-no" type="text" placeholder="Số lệnh">
                      </div>
                    </div>
                    <div class="row form-group">
                      <label class="col-sm-4 col-form-label" title="Số container">Số container</label>
                      <div class="col-sm-8">
                        <input class="form-control form-control-sm" id="cntr-no" type="text" placeholder="Số container">
                      </div>
                    </div>
                    <div class="row form-group">
                      <label class="col-sm-4 col-form-label" title="Phương thức thanh toán">Phương thức thanh toán</label>
                      <div class="col-sm-8">
                        <select id="AccCd" class="selectpicker form-control" multiple="" title="-- [Phương thức] --">
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="row form-group">
                      <label class="col-sm-4 col-form-label" title="Đến ngày">Đến ngày</label>
                      <div class="col-sm-8">
                        <input class="form-control form-control-sm" id="issueDateTo" type="text" placeholder="Đến ngày">
                      </div>
                    </div>
                    <div class="row form-group">
                      <label class="col-sm-4 col-form-label" title="Số PIN">Số PIN</label>
                      <div class="col-sm-8">
                        <input class="form-control form-control-sm" id="pincode" type="text" placeholder="Số PIN">
                      </div>
                    </div>
                    <div class="row form-group">
                      <label class="col-sm-4 col-form-label" title="Số Hoá đơn">Số Hoá đơn</label>
                      <div class="col-sm-8">
                        <input class="form-control form-control-sm" id="invNo" type="text" placeholder="Số Hoá đơn">
                      </div>
                    </div>
                    <div class="row form-group">
                      <label class="col-sm-4 col-form-label" title="Trạng thái HĐ">Trạng thái thanh toán</label>
                      <div class="col-sm-8">
                        <div class="mt-2">
                          <label class="checkbox checkbox-inline">
                            <input type="checkbox" name="payment-status" value="0" checked="">
                            <span class="input-span"></span>Chưa thanh toán</label>
                          <label class="checkbox checkbox-inline">
                            <input type="checkbox" name="payment-status" value="1" checked="">
                            <span class="input-span"></span>Đã thanh toán</label>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-sm-2">
                <div class="row form-group">
                  <label class="radio radio-ebony pr-4">
                    <input type="radio" name="ord-type" value="NH" checked>
                    <span class="input-span col-form-label"></span>
                    Nâng Hạ
                  </label>
                </div>
                <div class="row form-group">
                  <label class="radio radio-ebony">
                    <input type="radio" name="ord-type" value="DV">
                    <span class="input-span col-form-label"></span>
                    Dịch Vụ
                  </label>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-12 col-xs-12">
            <div class="row form-group">
              <div class="col-sm-8">
                <div class="row">
                  <div class="col-sm-12">
                    <div class="row form-group">
                      <!-- <label class="col-sm-2 col-form-label" title="Phương thức thanh toán">Phương thức thanh toán</label>
                      <div class="col-sm-4">
                        <select id="cjMode" class="selectpicker form-control" multiple="" title="-- [Phương án] --">
                          <option value="LAYN">Lấy nguyên</option>
                          <option value="CAPR">Cấp rỗng</option>
                          <option value="HBAI">Hạ bãi</option>
                          <option value="TRAR">Trả rỗng</option>
                        </select>
                      </div> -->
                      <!-- <label class="col-sm-2 col-form-label" title="Trạng thái HĐ">Trạng thái thanh toán</label>
                      <div class="col-sm-4">
                        <div class="mt-2">
                          <label class="checkbox checkbox-inline">
                            <input type="checkbox" name="payment-status" value="0" checked="">
                            <span class="input-span"></span>Chưa thanh toán</label>
                          <label class="checkbox checkbox-inline">
                            <input type="checkbox" name="payment-status" value="1" checked="">
                            <span class="input-span"></span>Đã thanh toán</label>
                        </div>
                      </div> -->
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row mt-2 pt-2">
          <div class="col-12 ibox mb-0 border-e pb-1 pt-3">
            <table id="tbl-inv" class="table table-striped display nowrap" cellspacing="0" style="width: 99.8%">
              <thead>
                <tr>
                  <!-- <th>Rowguid</th> -->
                  <th>STT</th>
                  <th>Chọn</th>
                  <th>TT thanh toán</th>
                  <th>Số HĐ</th>
                  <th>Số PTC</th>
                  <th>PTTT</th>
                  <th>Tổng Tiền</th>
                  <th>Số lệnh</th>
                  <th>Ngày lệnh</th>
                  <th>Hãng KT</th>
                  <th>Mã KH</th>
                  <th>Tên KH</th>
                  <th>Thành Tiền</th>
                  <th>Tiền Thuế</th>
                  <th>Loại Tiền</th>
                  <th>Người tạo</th>
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

<script type="text/javascript">
  $(document).ready(function() {
    var tblInv = $("#tbl-inv"),
      // _colInv = ["Rowguid", "STT", "Action", 'ORD_NO', "ISSUE_DATE", "PAYMENT_CHK", "DRAFT_INV_NO", "INV_NO", "OPR", "PAYER", "CusName", "AMOUNT", "VAT", "TAMOUNT", "CURRENCYID", "CreatedBy"],
      _colInv = ["STT", "Action","PAYMENT_CHK","INV_NO", "DRAFT_INV_NO", "ACC_CD", "TAMOUNT", 'ORD_NO', "ISSUE_DATE", "OPR", "PAYER", "CusName", "AMOUNT", "VAT", "CURRENCYID", "CreatedBy"],
      _colPayer = ["STT", "CusID", "VAT_CD", "CusName", "Address", "CusType"];
    var _draftDetails = [],
      _invs = [],
      _listPayment = [],
      _currentTbl = ''

    var _paymentMethod = [];
    <?php if (isset($paymentMethod) && count($paymentMethod) > 0) { ?>
      _paymentMethod = <?= json_encode($paymentMethod) ?>;
    <?php } ?>

    for (var i = 0; i < _paymentMethod.length; i++) {
      $("#AccCd").append('<option value="' + _paymentMethod[i].ACC_CD + '">' + _paymentMethod[i].ACC_NAME + '</option>');
    }
    $("#AccCd").selectpicker('refresh');

    var dtInv = tblInv.DataTable({
      scrollY: '37vh',
      columnDefs: [{
          type: "num",
          className: "text-center",
          targets: _colInv.indexOf('STT')
        },
        {
          orderable: false,
          className: "text-center",
          targets: _colInv.indexOf('Action')
        },
        // {
        //   className: "hiden-input",
        //   targets: _colInv.indexOf('Rowguid')
        // },
        {
          className: "text-center",
          targets: _colInv.getIndexs(['DRAFT_INV_NO', 'INV_NO', "OPR", 'CURRENCYID'])
        },
        {
          className: "text-right",
          targets: _colInv.getIndexs(["AMOUNT", "VAT", "TAMOUNT"]),
          render: $.fn.dataTable.render.number(',', '.', 2)
        },
        {
          render: function(data, type, full, meta) {
            return "<div class='wrap-text width-250'>" + data + "</div>";
          },
          targets: _colInv.getIndexs(["CusName"])
        },
        {
          className: "text-center",
          targets: _colInv.indexOf('ORD_NO')
        }
      ],
      order: [
        [_colInv.indexOf('STT'), 'asc']
      ],
      paging: true,
      scroller: {
        displayBuffer: 9,
        boundaryScale: 0.95
      },
      rowReorder: false,
      buttons: []
    });
    //set from date, to date
    var issueDateFrom = $('#issueDateFrom');
    var issueDateTo = $('#issueDateTo');
    setDateTimeRange(issueDateFrom, issueDateTo); //, 'yy-mm-dd', 'HH:mm:ss'

    issueDateFrom.val(moment().subtract(1, 'day').format('DD/MM/YYYY 00:00'));
    issueDateTo.val(moment().format('DD/MM/YYYY 23:59'));
    //end set fromdate, todate

    $("#load-data").on("click", function() {
      loadData();
    });

    tblInv.on('change', 'input[name ="select-inv"]', function(e) {
      var idPaymentStatus = $(this).closest('tr').find(`td:eq(${_colInv.indexOf('PAYMENT_CHK')}) span`).attr('id');
      var chk = $(e.target);
      var isChecked = chk.is(":checked");
      if (idPaymentStatus) {
        if (isChecked) {
          // chk.removeAttr("checked");
          chk.val("1");
          tblInv.DataTable().rows(chk.closest("tr")).deselect();
        } else {
          // chk.attr("checked", "");
          chk.val("0");
          tblInv.DataTable().rows(chk.closest("tr")).select();
        }
      } else {
        if (isChecked) {
          // chk.attr("checked", "");
          chk.val("1");
          tblInv.DataTable().rows(chk.closest("tr")).select();
        } else {
          // chk.removeAttr("checked");
          chk.val("0");
          tblInv.DataTable().rows(chk.closest("tr")).deselect();
        }
      }
    });
    $('#confirm-payment').on('click', function() {
      if ($(".input-required").has_required()) {
        toastr["error"]("Các thông tin bắt buộc không được để trống!");
        return;
      }

      if (tblInv.find(".selected").length == 0) {
        toastr["error"]("Chưa có phiếu tính cước nào được chọn!");
        return;
      }
      $.confirm({
        title: 'Cảnh báo!',
        type: 'orange',
        icon: 'fa fa-warning',
        content: 'Xác nhận thanh toán, bạn có muốn tiếp tục ?',
        buttons: {
          ok: {
            text: 'Tiếp tục',
            btnClass: 'btn-primary',
            action: function() {
              var dataPayment = tblInv.DataTable().rows('.selected').nodes();
              dataUpdate(dataPayment);
              confirmPayment(_listPayment);
            }
          },
          cancel: {
            text: 'Hủy bỏ',
            btnClass: 'btn-default',
            keys: ['ESC'],
            action: function() {
              $('#ship-modal').modal("show");
            }
          }
        }
      });

    })

    function loadData() {
      tblInv.dataTable().fnClearTable();
      tblInv.waitingLoad();

      var btn = $("#load-data");
      btn.button("loading");

      var formData = {
        "action": "view",
        "act": "search_inv",
        "AccCd": $("#AccCd").val(),
        "fromDate": $("#issueDateFrom").val(),
        "toDate": $("#issueDateTo").val(),
        "ordNo": $("#ord-no").val(),
        "pinCode": $("#pincode").val(),
        "cntrNo": $("#cntr-no").val(),
        "invNo": $("#invNo").val(),
        "typeOfDate": $("input[name='ord-type']:checked").val(),
        "paymentStatus": $("input[name='payment-status']:checked").map(function(_, el) {
          return $(el).val();
        }).get(),
      };
      $.ajax({
        url: "<?= site_url(md5('Tools') . '/' . md5('tlPaymentComplete')); ?>",
        dataType: 'json',
        data: formData,
        type: 'POST',
        success: function(data) {
          _currentTbl = $("input[name='ord-type']:checked").val();
          btn.button("reset");

          if (data.deny) {
            toastr["error"](data.deny);
            return;
          }
          var rows = [];
          if (data.invs && data.invs.length > 0) {
            _invs = data.invs;

            $.each(data.invs, function(i, item) {
              var r = [];
              $.each(_colInv, function(idx, colname) {
                var val = "";
                switch (colname) {
                  case "STT":
                    val = i + 1;
                    break;
                  case "Action":
                    if (item['PAYMENT_CHK'] == '1') {
                      val = '<label class="checkbox checkbox-outline-ebony">' +
                        '<input type="checkbox" name="select-inv" value="1" checked="" >' +
                        '<span class="input-span"></span>'; +
                      '</label>';
                    } else {
                      val = '<label class="checkbox checkbox-outline-ebony">' +
                        '<input type="checkbox" name="select-inv" value="0" style="display: none;">' +
                        '<span class="input-span"></span>'; +
                      '</label>';
                    }

                    break;
                  case "PAYMENT_CHK":
                    val = item[colname] == 1 ?
                      '<span id="completed" style="color : green" >Đã thanh toán<span>' : '<span style = "color :red; font-weight: bold">Chưa thanh toán</span>'
                    break;
                  case "INV_DATE":
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

          tblInv.dataTable().fnClearTable();
          if (rows.length > 0) {
            tblInv.dataTable().fnAddData(rows);
          }
        },
        error: function(err) {
          toastr["error"]("Error!");
          btn.button('reset');
          $('.ibox.collapsible-box').unblock();
          console.log(err);
        }
      });
    }

    function confirmPayment(dataPayment) {
      var btn = $("#confirm-payment");
      btn.button("loading");

      var formData = {
        "action": "save",
        "act": "update",
        'tbl': _currentTbl,
        "data": dataPayment
      };
      $.ajax({
        url: "<?= site_url(md5('Tools') . '/' . md5('tlPaymentComplete')); ?>",
        dataType: 'json',
        data: formData,
        type: 'POST',
        success: function(data) {
          btn.button("reset");

          if (data.deny) {
            toastr["error"](data.deny);
            return;
          }

          toastr["success"]("Cập nhật thành công!");
          tblInv.find(".selected").each((i, v) => {
            var ischecked = $(v).find('input[name ="select-inv"]').attr('checked');
            console.log('ischecked', ischecked);
            if (ischecked) {
              $(v).find('td:eq(' + _colInv.indexOf('Action') + ')').html('<label class="checkbox checkbox-outline-ebony">' +
                '<input type="checkbox" name="select-inv" value="0">' +
                '<span class="input-span"></span></label>');
              $(v).find('td:eq(' + _colInv.indexOf('PAYMENT_CHK') + ')').html('<span style = "color :red; font-weight: bold">Chưa thanh toán</span>')
            } else {
              $(v).find('td:eq(' + _colInv.indexOf('Action') + ')').html('<label class="checkbox checkbox-outline-ebony">' +
                '<input type="checkbox" name="select-inv" value="1" checked="" >' +
                '<span class="input-span"></span></label>');
              $(v).find('td:eq(' + _colInv.indexOf('PAYMENT_CHK') + ')').html('<span id="completed" style="color : green" >Đã thanh toán<span>')
            }
          })
          tblInv.DataTable().rows('.selected').nodes().to$().removeClass("selected")
        },
        error: function(err) {
          toastr["error"]("Error!");
          btn.button('reset');
          $('.ibox.collapsible-box').unblock();
          console.log(err);
        }
      });
    }

    function dataUpdate(data) {
      _listPayment = [];
      $.each(data, function(index, value) {
        var item = {};
        // item['Rowguid'] = value[`${_colInv.indexOf('Rowguid')}`];
        item['PAYMENT_CHK'] = $(this).find('input[name="select-inv"]').attr('value');
        item['ORD_NO'] = $(this).find('td:eq(' + _colInv.indexOf('ORD_NO') + ')').html()
        _listPayment.push(item);
      })
    }


  });
</script>

<script src="<?= base_url('assets/vendors/moment/min/moment.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/bootstrap-select/dist/js/bootstrap-select.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/jquery-confirm/jquery-confirm.min.js'); ?>"></script>
<!--format number-->
<script src="<?= base_url('assets/js/jshashtable-2.1.js'); ?>"></script>
<script src="<?= base_url('assets/js/jquery.numberformatter-1.2.3.min.js'); ?>"></script>