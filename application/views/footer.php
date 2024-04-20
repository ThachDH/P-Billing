<?php
defined('BASEPATH') or exit('');
?>
</div>
<!-- END PAGE CONTENT-->
<footer class="page-footer">
    <div class="font-13" style="width: 100%; text-align:right">2021 © <b>CEH Software</b></div>
    <div class="to-top"><i class="fa fa-angle-double-up"></i></div>
</footer>
</div>
</div>
<input style="display: none" id="editor-input" />
<!-- BEGIN PAGA BACKDROPS-->
<div class="sidenav-backdrop backdrop"></div>
<div class="preloader-backdrop">
    <div class="page-preloader">Loading</div>
</div>
<!-- END PAGA BACKDROPS-->

<!-- CORE SCRIPTS-->
<script src="<?= base_url('assets/js/app.min.js'); ?>"></script>

<script>
    var resizefunc = [];

    $.extend(true, $.fn.dataTable.defaults, {
        language: {
            info: "Số dòng: _TOTAL_",
            emptyTable: "------------ Không có dữ liệu hiển thị ------------",
            infoFiltered: "(trên _MAX_ dòng)",
            infoEmpty: "Số dòng: 0",
            search: '<span>Tìm:</span> _INPUT_',
            zeroRecords: "------------ Không có dữ liệu được tìm thấy ------------",
            sThousands: ",",
            sDecimal: ".",
            select: {
                rows: {
                    _: "Đã chọn %d dòng",
                    0: ""
                }
            }
        },
        search: {
            regex: true
        },
        info: true,
        orderClasses: false,
        paging: false,
        scrollY: 419,
        scrollX: true,
        lengthChange: false,
        scrollCollapse: false,
        deferRender: true,
        processing: true,
        autoWidth: true,
        dom: '<"datatable-header"fl<"datatable-info-right"Bip>><"datatable-scroll-wrap"t>',
        buttons: [{
                extend: 'selectAll',
                text: '<i class="fa fa-check-circle"></i>&ensp;Chọn tất cả',
                className: 'btn btn-sm btn-outline-secondary'
            },
            {
                extend: 'selectNone',
                text: '<i class="fa fa-ban"></i>&ensp;Bỏ chọn',
                className: 'btn btn-sm btn-outline-secondary'
            }
        ],
        destroy: true
    });

    $.fn.dataTable.ext.order['dom-checkbox'] = function(settings, col) {
        return this.api().column(col, {
            order: 'index'
        }).nodes().map(function(td, i) {
            if( $(td).find('input[type="checkbox"]').length > 0 ){
                return $('input[type="checkbox"]', td).prop('checked') ? '1' : '0';
            }
            else {
                return $(td).closest('tr').hasClass('selected') ? '1' : '0';
            }
        });
    };
</script>
<script>

    // var timeblur = new Date().getTime();
    // window.onblur = function() {
    //     timeblur = new Date().getTime();
    // };

    // window.onfocus = function() { 
    //     if(new Date().getTime() - timeblur >= 5*(60*1000)){ //15p
    //         location.reload();
    //     }
    // };
    
    $(document).ready(function() {
        $("a[href='" + location.href + "']").addClass("active");
        $("a[href='" + location.href + "']").parent().parent().parent().addClass("active");
        $("a[href='" + location.href + "']").closest("ul.nav-2-level").addClass("collapse in");
        $("#_myDrawerSidebar").prop('checked', !$('body').hasClass('drawer-sidebar'));

        $('#sidebar-collapse').slimScroll({
            height: "100%",
            railOpacity: "0.9",
            color: '#fff'
        });

        $("#_myDrawerSidebar").on('change', function() {
            if (!isMobile.any()) {
                $("#_drawerSidebar").prop('checked', !$(this).is(':checked')).trigger('change');
                ($("body").hasClass("has-backdrop") && $("#sidebar").backdrop())
                setTimeout(function() {
                    $($.fn.dataTable.tables(true)).DataTable()
                        .columns
                        .adjust();
                }, 220);
            }
        });

        window.addEventListener('beforeunload', function(e) {
            localStorage.setItem('bodyClass', $('body').attr('class').replace('has-backdrop', ''));
        });

        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "1000",
            "hideDuration": "1000",
            "timeOut": "10000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "swing",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        $('a.nav-link.sidebar-toggler.js-sidebar-toggler').on('click', function() {
            $("body").hasClass("sidebar-mini") ? $('#sidebar-collapse').slimScroll({
                destroy: true
            }).css({
                overflow: "visible",
                height: "auto"
            }) : $('#sidebar-collapse').slimScroll({
                height: "100%",
                railOpacity: "0.9",
                color: '#fff'
            });;
            setTimeout(function() {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns
                    .adjust();
            }, 220);
        });

        //remove class error when change value
        $(document).on('input', '.error input', function() {
            $(this).parent().removeClass('error');
        });

        $('[data-action="reloadUI"]').on('click', function(e) {
            var block = $(this).attr('data-reload-target');
            $(block).block({
                message: '<i class="la la-spinner spinner"></i>',
                overlayCSS: {
                    backgroundColor: '#fff',
                    opacity: 0.8,
                    cursor: 'wait',
                    'box-shadow': '0 0 0 1px #ddd'
                },
                css: {
                    border: 0,
                    padding: 0,
                    backgroundColor: 'none'
                }
            });
        });
    });

    function autoLoadYearCombo(id) {
        $("#" + id).find('option').remove();
        $("#" + id).selectpicker('refresh');
        var currentYear = (new Date()).getFullYear();
        for (let y = currentYear - 2; y <= currentYear + 4; y++) {
            $("#" + id).append('<option value="' + y + '">' + y + '</option>');
        }
        $("#" + id).val(currentYear);
        $("#" + id).selectpicker("refresh");
    }

    function logout() {
        $.confirm({
            title: 'Thông báo!',
            type: 'orange',
            icon: 'fa fa-warning',
            content: 'Bạn có chắc chắn muốn đăng xuất ?',
            buttons: {
                ok: {
                    text: 'Đăng xuất',
                    btnClass: 'btn-warning',
                    keys: ['Enter'],
                    action: function() {
                        window.location.href = '<?= site_url(md5('user') . '/' . md5('logout')); ?>';
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
</script>

<script>
    $.blockUI.defaults.fadeIn = 0;
    $.blockUI.defaults.onBlock = function() {
        let blockEl = $('.blockUI:first').parent();
        if(blockEl && blockEl.find('button').length > 0) {
            blockEl.find('button').prop('disabled', true);
        }
    }

    $.blockUI.defaults.onUnblock = function(element, options) {
        if(element && $(element).find('button').length > 0) {
            $(element).find('button').prop('disabled', false);
        }
    }
    
</script>

</body>

</html>