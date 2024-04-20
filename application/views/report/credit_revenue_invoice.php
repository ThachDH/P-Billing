<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<link href="<?= base_url('assets/vendors/bootstrap-select/dist/css/bootstrap-select.min.css'); ?>" rel="stylesheet" />
<link href="<?= base_url('assets/vendors/dataTables/extensions/buttons.dataTables.min.css'); ?>" rel="stylesheet" />

<style>
    .ui-icon {
        margin-top: -0.9em !important;
        margin-left: -8px !important;
    }

    tfoot tr th {
        font-size: 13px !important;
        padding: 10px 8px !important;
    }

    #payer-modal .dataTables_filter {
        padding-left: 10px !important;
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

<div class="row">
    <div class="col-xl-12">
        <div class="ibox collapsible-box">
            <i class="la la-angle-double-up dock-right"></i>
            <div class="ibox-head">
                <div class="ibox-title">THỐNG KÊ HÓA ĐƠN THU SAU</div>
                <div class="button-bar-group mr-3">
                    <button type="button" id="search" title="Nạp dữ liệu" data-loading-text="<i class='la la-spinner spinner'></i>Đang nạp" class="btn btn-sm btn-outline-primary mr-1">
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
                <form id="frmdata_export" method="post" action="<?= site_url(md5('Report') . '/' . md5('export_credit')); ?>">
                    <div class="row border-e bg-white pb-1">
                        <div class="col-xs-3 col-md-3 col-lg-2 col-xl-2 mt-3">
                            <div class="form-group">
                                <label class="mb-0">Ngày hoá đơn</label>
                            </div>
                            <div class="form-group input-group">
                                <input class="form-control form-control-sm text-center mr-2" id="fromDate" name="fromDate" type="text" placeholder="Từ ngày">
                                <input id="toDate" name="toDate" class="form-control form-control-sm text-center" type="text" placeholder="Đến ">
                            </div>
                        </div>
                        <div class="col-xs-3 col-md-3 col-lg-2 col-xl-2 mt-3">
                            <div class="form-group">
                                <label class="mb-0">Tàu chuyến</label>
                            </div>
                            <div class="form-group input-group">
                                <input class="form-control form-control-sm input-required" id="shipid" placeholder="Tàu/chuyến" type="text" readonly>
                                <span class="input-group-addon bg-white btn mobile-hiden text-warning" style="padding: 0 .5rem" title="chọn tàu" data-toggle="modal" data-target="#ship-modal">
                                    <i class="ti-search"></i>
                                </span>
                            </div>
                        </div>

                        <div class="col-xs-2 col-md-2 col-lg-2 col-xl-2 mt-3">
                            <div class="form-group">
                                <label class="mb-0">Loại tiền</label>
                            </div>
                            <div class="form-group">
                                <select id="currencyid" name="currencyid" class="selectpicker" data-style="btn-default btn-sm" data-width="100%">
                                    <option value="" selected>Tất cả</option>
                                    <option value="VND" >VND</option>
                                    <option value="USD">USD</option>
                                </select>
                            </div>
                        </div>


                        <div class="col-xs-1 col-md-1 col-lg-2 col-xl-2 mt-3">
                            <div class="form-group">
                                <label class="mb-0">ĐVPH</label>
                            </div>
                            <div class="form-group">
                                <select id="publishBy" class="selectpicker" data-style="btn-default btn-sm" data-width="100%" title="Chọn đơn vị phát hành">
                                    <option value="HAP" selected>HAP</option>
                                    <option value="HATS">HATS</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <input id="exportdata" name="exportdata" type="text" style="display: none">
                </form>
            </div>
            <div class="row ibox-footer" style="border-top: 0">
                <div class="col-md-12 col-sm-12 col-xs-12 table-responsive">
                    <div id="tablecontent">
                        <table id="contenttable" class="table table-striped display nowrap" cellspacing="0" style="width: 99.9%">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Số HĐ</th>
                                    <th>Ngày Hóa đơn</th>
                                    <th>ĐVPH</th>
                                    <th>Doanh Thu(USD)</th>
                                    <th>Thuế(USD)</th>
                                    <th>Doanh Thu VNĐ</th>
                                    <th>Thuế(VNĐ)</th>
                                    <th>Tàu Chuyến</th>
                                    <th>Số Tiền(USD)</th>
                                    <th>Tỷ Giá</th>
                                    <th>Số Tiền(VNĐ)</th>
                                    <th>Lập bởi</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <!-- <tfoot>
                                <tr style="color:red; font-size:13px">
                                    <th colspan="6"></th>
                                    <th style="font-weight: bold;">TỔNG CỘNG</th>
                                    <th></th>
                                    <th></th>
                                    <th class="text-right">0</th>
                                    <th class="text-right">0</th>
                                    <th class="text-right">0</th>
                                    <th class="text-right"></th>
                                    <th class="text-right">0</th>
                                    <th class="text-right">0</th>
                                    <th colspan="7"></th>
                                </tr>
                            </tfoot> -->
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
                                <th>Ship Year</th>
                                <th>Ship Voy</th>
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


<script type="text/javascript">
    $(document).ready(function() {
        var _selectShipKey,
            _colContent = ['STT', 'INV_NO', 'INV_DATE', 'REF_NO', 'AMUSD', 'VATUSD', 'AMVND', 'VATVND', 'TauChuyen', 'TMUSD', 'RATE', 'TMVND', 'CreatedBy']

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
                // customize: function(xlsx) {
                // 	var sheet = xlsx.xl.worksheets['sheet1.xml'];
                // 	$('row c[r^="K"], row c[r^="M"], row c[r^="N"]', sheet).attr('s', 63);
                // }
            }],
            paging: true,
            searching: true,
            scrollY: '42vh',
            columnDefs: [{
                    targets: _colContent.getIndexs(['STT', 'CURRENCYID', 'INV_NO', 'INV_DATE', 'REF_NO', 'TauChuyen', 'CreatedBy']),
                    className: ""
                },
                {
                    targets: _colContent.getIndexs(["TMUSD", "VATUSD", "TMVND", "VATVND", "AMUSD", "RATE", "AMVND"]),
                    className: "text-center",
                    render: $.fn.dataTable.render.number(',', '.', 2)
                }
            ],
            order: [
                [0, 'asc']
            ],
            scroller: {
                displayBuffer: 20,
                boundaryScale: 0.5,
                loadingIndicator: true
            }
        });

        $('#search-ship').DataTable({
            scrollY: '35vh',
            paging: false,
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


        //set from date, to date
        var fromDate = $('#fromDate');
        var toDate = $('#toDate');
        setDateTimeRange(fromDate, toDate); //, 'yy-mm-dd', 'HH:mm:ss'

        fromDate.val(moment().subtract(1, 'day').format('DD/MM/YYYY 00:00'));
        toDate.val(moment().format('DD/MM/YYYY 23:59'));

        ///////// SEARCH SHIP
        autoLoadYearCombo('cb-searh-year');

        search_ship();

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

            $('#shipid').val($(r).find('td:eq(0)').text() + "/" + $(r).find('td:eq(3)').text() + "/" + $(r).find('td:eq(4)').text());
            $('#shipid').removeClass('error');

            _selectShipKey = $(r).find('td:eq(6)').text();
        });

        $('#search-ship').on('dblclick', 'tbody tr td', function() {
            var r = $(this).parent();

            $('#shipid').val($(r).find('td:eq(0)').text() + "/" + $(r).find('td:eq(3)').text() + "/" + $(r).find('td:eq(4)').text());
            $('#shipid').removeClass('error');

            _selectShipKey = $(r).find('td:eq(6)').text();

            $('#ship-modal').modal("hide");
        });

        $('#unselect-ship').on('click', function() {
            $('#shipid').val('');
        });

        $('#reload-ship').on("click", function() {
            $('#search-ship-name').val("");
            search_ship();
        })
        ///////// END SEARCH SHIP


        $('#ship-modal').on('shown.bs.modal', function(e) {
            $($.fn.dataTable.tables(true)).DataTable()
                .columns
                .adjust();
        });

        $('#search').on('click', function() {
            $("#contenttable").waitingLoad();

            // var jmode = $('#jmode').val();

            // var jdate = getFilterDate();
            var formData = {
                'action': 'view',
                'fromDate': $("#fromDate").val(),
                'toDate': $("#toDate").val(),
                'shipKey': _selectShipKey,
                'currencyId': $("#currencyid").val(),
                'publishBy': $("#publishBy").val(),
            };

            $.ajax({
                url: "<?= site_url(md5('Report') . '/' . md5('rptCreditRevenueInv')); ?>",
                dataType: 'json',
                data: formData,
                type: 'POST',
                success: function(data) {
                    var rows = [];
                    console.log('asd', data)
                    if (data.results && data.results.length > 0) {
                        results = data.results;
                        var i = 0;
                        $.each(results, function(index, rData) {
                            var r = [];
                            $.each(_colContent, function(idx, colname) {
                                var val = "";
                                switch (colname) {
                                    case "STT":
                                        val = i + 1;
                                        break;
                                    case "REF_NO":
                                        if (!rData[colname]) {
                                            val = 'HAP'
                                        } else {
                                            val = rData[colname] ? rData[colname] : "";
                                        }
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

                    $('#contenttable').dataTable().fnClearTable();
                    if (rows.length > 0) {
                        console.log(rows)
                        $('#contenttable').dataTable().fnAddData(rows);
                    }
                },
                error: function(err) {
                    console.log(err);
                }
            });
        });


        function search_ship() {
            $("#search-ship").waitingLoad();
            var formdata = {
                'action': 'view',
                'act': 'searh_ship',
                'arrStatus': $('input[name="shipArrStatus"]:checked').val(),
                'shipyear': $('#cb-searh-year').val(),
                'shipname': $('#search-ship-name').val()
            };
            $.ajax({
                url: "<?= site_url(md5('Report') . '/' . md5('rptCreditRevenueInv')); ?>",
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
                        searching: false,
                        data: rows
                    });
                },
                error: function(err) {
                    console.log(err);
                }
            });
        }
    });
</script>

<script src="<?= base_url('assets/vendors/moment/min/moment.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/bootstrap-select/dist/js/bootstrap-select.min.js'); ?>"></script>
<!--format number-->
<script src="<?= base_url('assets/js/jshashtable-2.1.js'); ?>"></script>
<script src="<?= base_url('assets/js/jquery.numberformatter-1.2.3.min.js'); ?>"></script>

<script src="<?= base_url('assets/vendors/dataTables/datatables.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/dataTables/dataTables.buttons.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/dataTables/extensions/jszip.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/dataTables/extensions/buttons.html5.min.js'); ?>"></script>