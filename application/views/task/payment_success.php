<?php
defined('BASEPATH') or exit('');
?>
<style>
    body {
        background-repeat: no-repeat;
        background-size: cover;
    }

    .cover {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: rgba(117, 54, 230, .1);
    }

    .notify-content {
        max-width: 800px;
        margin: auto;
        height: 700px;
        vertical-align: middle;
        padding-top: 60px !important;
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
        top: 8px;
        right: 12vw;
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

    .dropdown-item {
        padding: .95rem 3.5rem !important;
    }

    .btn.dropdown-arrow:after {
        left: .7rem !important;
    }

    .m-hidden {
        display: none;
    }

    #service-list table tbody tr td {
        text-align: center;
    }
</style>

<div class="cover"></div>
<div class="ibox ibox-fullheight">
    <div class="ibox-body notify-content">
        <h1 class="text-center font-bold mb-5">COMPLETE !</h1>
        <div class="text-center">
            <span class="success-head-icon"><i class="fa fa-check"></i></span>
        </div>
        <h5 class="text-center mb-4">Giao dịch đã được thực hiện thành công!</h5>
        <div class="form-group text-center">
            <img style="width: 120px; height: 120px; border: 0; background-color: transparent;" src="<?= $qr; ?>"></img>
        </div>

        <?php if (isset($invInfo)) { ?>
            <h5 class="text-center">Mã giao dịch: <span class="font-bold"><?= $invInfo["fkey"]; ?></span></h5>
            <h5 class="text-center mb-5">Số hóa đơn: <span class="font-bold"><?= $invInfo["serial"] . $invInfo['invno']; ?></span></h5>
        <?php } ?>

        <div class="ibox-footer row">
            <div class="col-sm-4">
                <a class="btn btn-dark btn-rounded btn-block text-white" onclick="goBack()">LÀM LỆNH MỚI</a>
            </div>
            <div class="col-sm-4">
                <?php if ($this->config->item('IS_LASER_PRINT') == '1') { ?>
                    <button id="print-laser-order" class="btn btn-warning btn-rounded btn-block">IN LỆNH</button>
                <?php } else { ?>
                    <button id="print-order" class="btn btn-warning btn-rounded btn-block">IN LỆNH</button>
                <?php } ?>
            </div>
            <div class="col-sm-4">
                <?php if (isset($invInfo)) { ?>
                    <button id="show-inv" class="btn btn-blue btn-rounded btn-block">IN HÓA ĐƠN</button>
                <?php } ?>
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
        <i class="la la-close" style="font-size: 21px;" title="Đóng"></i>
    </div>
</div>
<div id="Print-NH" class="m-hidden">

</div>
<div id="Print-DR" class="m-hidden">

</div>
<div id="Print-DV" class="m-hidden">

</div>
<div id="Print-INV" class="m-hidden">

</div>
<script src="<?= base_url('assets/js/jsprint.js'); ?>"></script>
<script src="<?= base_url('assets/js/printlaser.ebilling.js'); ?>"></script>

<script>
    function goBack() {
        window.location.replace(document.referrer);
    };

    var tempNH = `<div class="NH-content" style="height:578.268px; position: relative;margin-top: 53px; left: 120.945px; font-weight: bold; font-size: 1.1em; font-family: 'Arial', 'Sans-serif'; page-break-after: always">
        <span style="position: absolute;z-index: 1;top: 0; left: 0;" class="OrderNo"></span>
        <span style="position: absolute;z-index: 1;top: 0; left: 272px;" class="InvNo"></span>
        <span style="position: absolute;z-index: 1;top: 0; left: 575px;" class="ExpDate"></span>
        <span style="position: absolute;z-index: 1;top: 27px; left: 90px;" class="CJModeName"></span>
        <span style="position: absolute;z-index: 1;top: 30px; left: 472px;">Hạn điện:</span>
        <span style="position: absolute;z-index: 1;top: 27px; left: 575px;" class="ExpPluginDate"></span>
        <span style="position: absolute;z-index: 1;top: 68px; left: 20px; font-size: 0.9em;" class="SHIPPER_NAME"></span>
        <span style="position: absolute;z-index: 1;top: 97px; left: 45px;" class="BL_BKNo"></span>
        <span style="position: absolute;z-index: 1;top: 97px; left: 495px;" class="BerthDate"></span>
        <span style="position: absolute;z-index: 1;top: 128px; left: 20px;">
            <span class="ShipName"></span>/<span class="ImVoy"></span>/<span class="ExVoy"></span>
        </span>
        <span style="position: absolute;z-index: 1;top: 128px; left: 538px;">
            <span class="POL"></span>/<span class="POD"></span>/<span class="FPOD"></span>
        </span>
        <span style="position: absolute;z-index: 1;top: 178px; left: 27px; font-size: 1.2em;" class="CntrNo"></span>
        <span style="position: absolute;z-index: 1;top: 178px; left: 215px; font-size: 0.9em; font-weight: normal">
            <span class="SealNo"></span> + <span class="SealNo1"></span>
        </span>
        <span style="position: absolute;z-index: 1;top: 178px; left: 435px;" class="OprID"></span>
        <span style="position: absolute;z-index: 1;top: 178px; left: 640px;" class="Status"></span>
        <span style="position: absolute;z-index: 1;top: 213px; left: 26px;" class="CMDWeight"></span>
        <span style="position: absolute;z-index: 1;top: 213px; left: 234px;" class="ISO_SZTP"></span>
        <span style="position: absolute;z-index: 1;top: 213px; left: 440px;" class="CARGO_TYPE_NAME"></span>
        <span style="position: absolute;z-index: 1;top: 250px; left: 45px;" class="Temperature"></span>
        <span style="position: absolute;z-index: 1;top: 250px; left: 250px;" class="Vent"></span>
        <span style="position: absolute;z-index: 1;top: 250px; left: 393px;" class="UNNO"></span>
        <span style="position: absolute;z-index: 1;top: 250px; left: 601px;" class="OOG"></span>
        <span style="position: absolute;z-index: 1;top: 321px; left: 0;" class="YardPos"></span>
        <span style="position: absolute;z-index: 1;top: 321px; left: 226px; max-width: 395px; word-wrap: break-word; font-weight: normal;" class="Note"></span>
        <span style="position: absolute;z-index: 1;top: 350px; left: 270px; max-width: 395px; word-wrap: break-word; font-weight: bold; font-size: 1.3em; border: 0px solid #212529" class="isTLHQ"></span>
        <img style="width:90px; height: 90px;background-color:transparent;border: 0;position:absolute;z-index:1;top:425px;left:0" src="<?= $qr; ?>"></img>
        <span style="position: absolute;z-index: 1;top: 487px; left: 90px;" class="UserName"></span>
    </div>`;

    var tempDR = `<div class="DR-content" style="height:578.268px; position: relative;margin-top: 76px; left: 120.945px; font-weight: bold; font-size: 0.9em; font-family: 'Arial', 'Sans-serif'; page-break-after: always">
        <span style="position: absolute;z-index: 1;top: 0; left: 0; font-size: 1.1em;" class="OrderNo"></span>
        <span style="position: absolute;z-index: 1;top: 0; left: 272px; font-size: 1.1em;" class="InvNo"></span>
        <span style="position: absolute;z-index: 1;top: 0; left: 575px; font-size: 1.1em;" class="ExpDate"></span>
        <span style="position: absolute;z-index: 1;top: 29px; left: 90px; font-size: 1.2em;" class="CJModeName"></span>
        <span style="position: absolute;z-index: 1;top: 35px; left: 472px;">Hạn điện:</span>
        <span style="position: absolute;z-index: 1;top: 30px; left: 575px;" class="ExpPluginDate"></span>
        <span style="position: absolute;z-index: 1;top: 68px; left: 20px;" class="SHIPPER_NAME"></span>
        <span style="position: absolute;z-index: 1;top: 97px; left: 45px;" class="BL_BKNo"></span>
        <span style="position: absolute;z-index: 1;top: 102px; left: 438px;" class="Quantity"></span>
        <span style="position: absolute;z-index: 1;top: 100px; left: 595px;" class="BerthDate"></span>
        <span style="position: absolute;z-index: 1;top: 128px; left: 20px;">
            <span class="ShipName"></span>/<span class="ImVoy"></span>/<span class="ExVoy"></span>
        </span>
        <span style="position: absolute;z-index: 1;top: 130px; left: 490px;">
            <span class="POL"></span>/<span class="POD"></span>/<span class="FPOD"></span>
        </span>
        <span style="position: absolute;z-index: 1;top: 178px; left: 27px; font-size: 1.4em;" class="CntrNo"></span>
        <span style="position: absolute;z-index: 1;top: 180px; left: 215px; font-size: 0.9em; font-weight: normal">
            <span class="SealNo"></span> + <span class="SealNo1"></span>
        </span>
        <span style="position: absolute;z-index: 1;top: 180px; left: 435px; font-size: 1.4em;" class="OprID"></span>
        <span style="position: absolute;z-index: 1;top: 182px; left: 640px;" class="Status"></span>
        <span style="position: absolute;z-index: 1;top: 215px; left: 26px;" class="CMDWeight"></span>
        <span style="position: absolute;z-index: 1;top: 215px; left: 234px; font-size: 1.4em;" class="ISO_SZTP"></span>
        <span style="position: absolute;z-index: 1;top: 217px; left: 440px;" class="CARGO_TYPE_NAME"></span>
        <span style="position: absolute;z-index: 1;top: 252px; left: 45px;" class="Temperature"></span>
        <span style="position: absolute;z-index: 1;top: 252px; left: 250px;" class="Vent">0.00</span>
        <span style="position: absolute;z-index: 1;top: 252px; left: 393px;" class="UNNO"></span>
        <span style="position: absolute;z-index: 1;top: 252px; left: 601px;" class="OOG">0.0/0.0/0.0</span>
        <span style="position: absolute;z-index: 1;top: 321px; left: 0;" class="YardPos">B2-04-03-2</span>
        <span style="position: absolute;z-index: 1;top: 321px; left: 226px; max-width: 395px; word-wrap: break-word;" class="Note"></span>
        <img style="width:90px; height: 90px;background-color:transparent;border: 0;position:absolute;z-index:1;top:410px;left:0" src="<?= $qr; ?>"></img>
        <span style="position: absolute;z-index: 1;top: 476px; left: 90px;" class="UserName"></span>
    </div>`;

    var tempDV = `<div class="DV-content" style="height:574px; position: relative;margin-top: 85px; left: 120.945px; font-weight: bold; font-size: 1.1em; font-family: 'Arial', 'Sans-serif'; page-break-after: always">
        <span style="position: absolute;z-index: 1;top: 0; left: 0;" class="OrderNo">1234567789</span>
        <span style="position: absolute;z-index: 1;top: 0; left: 345px;" class="InvNo"></span>
        <span style="position: absolute;z-index: 1;top: 0; left: 575px;" class="ExpDate"></span>
        <span style="position: absolute;z-index: 1;top: 30px; left: 90px;" class="CJModeName"></span>
        <span style="position: absolute;z-index: 1;top: 69px; left: 20px; font-size: 0.9em;" class="SHIPPER_NAME"></span>
        <span style="position: absolute;z-index: 1;top: 97px; left: 45px;" class="BL_BKNo"></span>
        <span style="position: absolute;z-index: 1;top: 97px; left: 375px;" class="NameDD"></span>
        <span style="position: absolute;z-index: 1;top: 97px; left: 605px;" class="PersonalID"></span>
        <!-- <span style="position: absolute;z-index: 1;top: 33.5vh; left: 60vw;" class="Quantity">40</span> -->
        <span style="position: absolute;z-index: 1;top: 128px; left: 20px;">
            <span class="ShipName"></span>/<span class="ImVoy"></span>/<span class="ExVoy"></span>
        </span>
		<span style="position: absolute;z-index: 1;top: 128px; left: 545px;" class="BerthDate"></span>
        <span style="position: absolute;z-index: 1;top: 180px; left: 0; font-size: 0.9em; font-weight: normal" class="Note"></span>
        <div id="service-list" style="text-align: center;position: absolute;z-index: 1;top: 231px; left: -80px">
            <table>
                <tbody style="font-size: 0.8em;">
                    
                </tbody>
            </table>
        </div>
        <span style="position: absolute;z-index: 1;top: 357px; left: 0;" class="IssueDate"></span>
        <span style="position: absolute;z-index: 1;top: 357px; left: 235px;" class="startDate"></span>
        <span style="position: absolute;z-index: 1;top: 357px; left: 500px;" class="endDate"></span>
        <img style="width:90px; height: 90px;background-color:transparent;border: 0;position:absolute;z-index:1;top:390px;left:0" src="<?= $qr; ?>"></img>
        <span style="position: absolute;z-index: 1;top: 455px; left: 90px;" class="UserName"></span>
    </div>`;

    var tempRowDV = `<tr style="border: 1px solid">
        <td style="width: 27px;height: 16px; text-align: center;" class="STT"></td>
        <td style="width: 135px;height: 16px; text-align: center; font-weight: bold;" class="CntrNo"></td>
        <td style="width: 80px;height: 16px; text-align: center;" class="OprID"></td>
        <td style="width: 46px;height: 16px; text-align: center;" class="ISO_SZTP"></td>
        <td style="width: 52px;height: 16px; text-align: center;" class="Status"></td>
        <td style="width: 80px;height: 16px; text-align: center;" class="CMDWeight"></td>
        <td style="width: 88px;height: 16px; text-align: center;" class="SealNo"></td>
        <td style="width: 88px;height: 16px; text-align: center;" class="YardPos"></td>
        <td style="width: 125px;height: 16px; text-align: center;" class="Remark"></td>
    </tr>`;

    var tempINV = `<div class="INV-content" style="height:572px;margin-left:25px;position:relative;font-family:'Arial','Sans-serif';font-size:9px;page-break-after:always">
        <span style="position: absolute;z-index: 1;top: 140.25px; left: 307.4px;" class="INV_DAY"></span>
        <span style="position: absolute;z-index: 1;top: 140.25px; left: 364.1px;" class="INV_MONTH"></span>
        <span style="position: absolute;z-index: 1;top: 140.25px; left: 423.8px;" class="INV_YEAR"></span>
        <span style="position: absolute;z-index: 1;top: 178px; left: 94px;" class="CusName"></span>
        <span style="position: absolute;z-index: 1;top: 196.5px; left: 94px;" class="PAYER"></span>
        <span style="position: absolute;z-index: 1;top: 196.5px; left: 586px;" class="HTTT">TM</span>
        <span style="position: absolute;z-index: 1;top: 196.5px; left: 652px;" class="CURRENCYID">VND</span>
        <span style="position: absolute;z-index: 1;top: 213px; left: 57px;" class="Address"></span>
        <span style="position: absolute;z-index: 1;top: 229px; left: 95px;" class="SO_TK"></span>
        <span style="position: absolute;z-index: 1;top: 229px; left: 529px;" class="BAI_KHO"></span>
        <div id="inv-list" style="text-align: center;position: absolute;z-index: 1;top: 286px; left:13.45px">
            <table style="font-size:1em;">
                <tbody>
                    
                </tbody>
            </table>
        </div>

        <span style="position: absolute;z-index: 1;top: 403.5px; left: 510px; width: 169px; text-align: right;font-size:1.5em" class="SUB_AMOUNT"></span>
        <span style="position: absolute;z-index: 1;top: 423.8px; left: 123px;font-size:1.5em" class="VAT_RATE"></span>
        <span style="position: absolute;z-index: 1;top: 423.8px; left: 510px; width: 169px;text-align: right;font-size:1.5em" class="VAT"></span>
        <span style="position: absolute;z-index: 1;top: 439px; left: 510px; width: 169px;text-align: right;font-size:1.5em" class="TAMOUNT"></span>
        <span style="position: absolute;z-index: 1;top: 465px; left: 133px;" class="AmountInWords">hai trăm ngàn đồng</span>
        <span style="position: absolute;z-index: 1;top: 550px; left: 539px;" class="UserName"></span>
    </div>`;

    var tempINV_CRE = `<div class="INV-content" style="height:1057px;margin-left:25px;position:relative;font-family:'Arial','Sans-serif';font-size:11px;page-break-after:always">
        <span style="position: absolute;z-index: 1;top: 171px; left: 299.8px;" class="INV_DAY"></span>
        <span style="position: absolute;z-index: 1;top: 171px; left: 398.3px;" class="INV_MONTH"></span>
        <span style="position: absolute;z-index: 1;top: 171px; left: 480.5px;" class="INV_YEAR"></span>


        <span style="position: absolute;z-index: 1;top: 226px; left: 150px;" class="CusName"></span>
        <span style="position: absolute;z-index: 1;top: 248px; left: 132px;" class="Address"></span>
        <span style="position: absolute;z-index: 1;top: 269px; left: 168px;" class="PAYER"></span>
        <span style="position: absolute;z-index: 1;top: 269px; left: 586px;" class="HTTT">TM</span>
        <span style="position: absolute;z-index: 1;top: 269px; left: 652px;" class="CURRENCYID">VND</span>
        <span style="position: absolute;z-index: 1;top: 294px; left: 178px;" class="SO_TK"></span>
        <span style="position: absolute;z-index: 1;top: 316px; left: 307.4px;" class="VESSEL_INFO">TAU_CHUYEN</span>
        <div id="inv-list" style="text-align: center;position: absolute;z-index: 1;top: 404px; left:22px">
            <table style="font-size:1em;">
                <tbody>
                    
                </tbody>
            </table>
        </div>

        <span style="position: absolute;z-index: 1;top: 794.5px; left: 548px; width: 150px; text-align: right;font-size:1.5em" class="SUB_AMOUNT"></span>
        <span style="position: absolute;z-index: 1;top: 818px; left: 198px;font-size:1.5em" class="VAT_RATE"></span>
        <span style="position: absolute;z-index: 1;top: 818px; left: 548px; width: 150px;text-align: right;font-size:1.5em" class="VAT"></span>
        <span style="position: absolute;z-index: 1;top: 837px; left: 548px; width: 150px;text-align: right;font-size:1.5em" class="TAMOUNT"></span>
        <span style="position: absolute;z-index: 1;top: 883px; left: 150px;" class="AmountInWords">hai trăm ngàn đồng</span>
        <span style="position: absolute;z-index: 1;top: 1000px; left: 521px;font-size:1.5em" class="UserName"></span>
    </div>`;

    var tempRowINV = `<tr style="vertical-align: top;">
        <td style="width: 31px;height: 19px; text-align: center;" class="STT"></td>
        <td style="width: 311px;height: 19px; text-align: left;" class="TRF_DESC"></td>
        <td style="width: 40px;height: 19px; text-align: center;" class="UNIT_NM"></td>
        <td style="width: 56px;height: 19px; text-align: right;" class="QTY"></td>
        <td style="width: 94.5px;height: 19px; text-align: right;" class="UNIT_RATE"></td>
        <td style="width: 124px;height: 19px; text-align: right;" class="AMOUNT"></td>
    </tr>`;

    var tempEirLaser = `<?= $eir_laser ?>`;
    var tempSrvLaser = `<?= $srv_laser ?>`;
    var tempOrderList = `<?= $order_list_laser ?>`;
    var data = <?= json_encode($print_data); ?>;
</script>

<script>
    $(document).ready(function() {
        ctrlDown = false,
            ctrlKey = 17,
            cmdKey = 91,
            rKey = 219,

            $(document).keydown(function(e) {
                if (e.keyCode == ctrlKey || e.keyCode == cmdKey) ctrlDown = true;
            }).keyup(function(e) {
                if (e.keyCode == ctrlKey || e.keyCode == cmdKey) ctrlDown = false;
            });

        localStorage.removeItem("margin_nh");
        localStorage.removeItem("margin_dr");
        localStorage.removeItem("margin_dv");
        localStorage.removeItem("margin_hd");

        $(document).keydown(function(e) {
            if (ctrlDown && e.keyCode == rKey) {
                $.confirm({
                    columnClass: 'col-md-3 col-md-offset-3',
                    titleClass: 'font-size-17',
                    title: 'Margin Top NH;DR;DV;HD',
                    content: '<div class="input-group-icon input-group-icon-left">' +
                        '<span class="input-icon input-icon-left"><i class="fa fa-plus" style="color: green"></i></span>' +
                        '<input autofocus class="form-control form-control-sm" id="num-row" value="1">' +
                        '</div>',
                    buttons: {
                        ok: {
                            text: 'OK',
                            btnClass: 'btn-sm btn-primary btn-confirm',
                            keys: ['Enter'],
                            action: function() {
                                var input = this.$content.find('input#num-row');
                                var errorText = this.$content.find('.text-danger');
                                if (!input.val().trim()) {
                                    return false;
                                } else {
                                    var dt = input.val().split(";");
                                    localStorage.setItem("margin_nh", dt[0] ? dt[0] : "53px");
                                    localStorage.setItem("margin_dr", dt[1] ? dt[1] : "76px");
                                    localStorage.setItem("margin_dv", dt[2] ? dt[2] : "85px");
                                    localStorage.setItem("margin_hd", dt[3] ? dt[3] : "85px");
                                }
                            }
                        },
                        later: {
                            text: 'Hủy',
                            btnClass: 'btn-sm',
                            keys: ['ESC']
                        }
                    }
                });
                return false;
            }
        });

        $('#show-inv').on('click', function() {
            <?php if (isset($m_inv_data)) { ?>
                var data = <?= json_encode($m_inv_data); ?>;
                var type = '<?= $type; ?>'; //them moi hd thu sau
                var formatNum = '<?= $formatNum; ?>'; //them moi lam tron so
                var formatNumQty_Unit = '<?= $formatNumQty_Unit; ?>'; //lam tron so luong+don gia theo yeu cau KT
                printInv(data, type, formatNum, formatNumQty_Unit); //them moi lam tron so //lam tron so luong+don gia theo yeu cau KT
                return;
            <?php } ?>

            $('#file-show-content').attr('src', '<?= count($invInfo) > 0 ? site_url(md5("InvoiceManagement") . '/' . md5("getInvView") . "?" . http_build_query($invInfo)) : ""; ?>');
            $('#file-show-content').on('load', function() {
                var a5 = $("#file-show-content").contents().find("body").find('style').text().includes('size: A5') || $("#file-show-content").contents().find("body").find('style').text().includes('size:A5');
                if (a5) {
                    var n = $('<style type="text/css" >').html(`@page { size: 215mm 157mm !important; margin: 5mm 5mm 0mm 5mm; !important; }`);
                    $("#file-show-content").contents().find("body").prepend(n);
                }
                document.getElementById("file-show-content").contentWindow.print();
            })

            // $('.m-show-modal').show('fade', function() {
            //     window.setTimeout(function() {
            //         $(".m-close-modal").show("slide", {
            //             direction: "up"
            //         }, 300);
            //     }, 3000);
            // });
        });

        $("#print-order").on("click", function() {
            var data = <?= json_encode($print_data); ?>;
            $("#Print-NH, #Print-DR, #Print-DV").html('');

            if (data && data.length > 0) {
                //set data for LOLO
                var loloServiceList = data.filter(p => p.OrderType == 'NH');
                if (loloServiceList.length > 0) {
                    var loloPrintContent = $("#Print-NH");
                    $.each(loloServiceList, function(idx, item) {
                        loloPrintContent.append(tempNH);

                        if (localStorage.getItem("margin_nh")) {
                            loloPrintContent.find('.NH-content:last').css("margin-top", localStorage.getItem("margin_nh"));
                        }

                        $.each(Object.keys(item), function(idx, key) {
                            if (['IssueDate', 'ExpDate', 'ExpPluginDate', 'BerthDate'].indexOf(key) != -1) {
                                item[key] = getDateTime(item[key]);
                            }

                            if (key == 'cTLHQ' && item['CJMode_CD'] == 'LAYN') {
                                var txtTLHQ = item['cTLHQ'] == '1' ? 'ĐÃ THANH LÝ HQ' : 'CHƯA THANH LÝ HQ';
                                loloPrintContent.find('.NH-content:last').find('span.isTLHQ').css("border", "3px solid #212529");
                                loloPrintContent.find('.NH-content:last').find('span.isTLHQ').text(txtTLHQ);
                            } else {
                                loloPrintContent.find('.NH-content:last').find('span.' + key).text(item[key]);
                            }
                        });
                    });
                    loloPrintContent.print();
                    loloPrintContent.html('');
                }

                //set data for STUFF - UNSTUFF
                var stuffList = data.filter(p => p.OrderType == 'DR');
                if (stuffList.length > 0) {
                    var stuffPrintContent = $("#Print-DR");
                    $.each(stuffList, function(idx, item) {
                        stuffPrintContent.append(tempDR);
                        if (localStorage.getItem("margin_dr")) {
                            stuffPrintContent.find('.DR-content:last').css("margin-top", localStorage.getItem("margin_dr"));
                        }

                        $.each(Object.keys(item), function(idx, key) {
                            if (['IssueDate', 'ExpDate', 'ExpPluginDate', 'BerthDate'].indexOf(key) != -1) {
                                item[key] = getDateTime(item[key]);
                            }
                            stuffPrintContent.find('.DR-content:last').find('span.' + key).text(item[key]);
                        });
                    });
                    stuffPrintContent.print();
                    stuffPrintContent.html('');
                }

                //set data for service
                var serviceList = data.filter(p => p.OrderType == 'DV');

                if (serviceList.length > 0) {
                    var servicePrintContent = $("#Print-DV");
                    var groupByCjMode = serviceList.reduce(function(r, a) {
                        r[a.CJMode_CD] = r[a.CJMode_CD] || [];
                        r[a.CJMode_CD].push(a);
                        return r;
                    }, Object.create(null));

                    var arrays = [],
                        numOfRow = 5;


                    $.each(groupByCjMode, function(cjmode, items) {

                        while (items.length > 0) {
                            arrays.push(items.splice(0, numOfRow));
                        }

                        var i = 1;
                        $.each(arrays, function(idx, serviceItem) {
                            if (serviceItem.length == 0) {
                                return;
                            }
                            servicePrintContent.append(tempDV);
                            if (localStorage.getItem("margin_dv")) {
                                servicePrintContent.find('.DV-content:last').css("margin-top", localStorage.getItem("margin_dv"));
                            }
                            //set data for header
                            var headerData = serviceItem[0];
                            $.each(Object.keys(headerData), function(idx, key) {
                                if (['IssueDate', 'ExpDate', 'ExpPluginDate', 'BerthDate'].indexOf(key) != -1) {
                                    headerData[key] = getDateTime(headerData[key]);
                                }

                                servicePrintContent.find('.DV-content:last').find('span.' + key).text(headerData[key]);
                            });

                            //set data for each row and append to table
                            $.each(serviceItem, function(idx, item) {
                                servicePrintContent.find('.DV-content:last').find("table tbody").append(tempRowDV);
                                var lastRow = servicePrintContent.find("table tbody tr:last");
                                lastRow.find('td.STT').text(i++);
                                $.each(Object.keys(item), function(ix, key) {
                                    lastRow.find('td.' + key).text(item[key]);
                                });
                            });
                        });
                    });

                    servicePrintContent.print();
                    servicePrintContent.html('');
                    //var win = window.open("", "_blank");
                    //$(win.document.body).append(servicePrintContent);
                }

                // $('.m-show-modal').print();
                // PrintElem( "Print-content" );
                // var bppd = document.getElementById("Print-content");bppd.focus();bppd.contentWindow.print();
            } else {
                toastr["warning"]("Không thể in!<br> Vui lòng kiểm tra lại!")
            }
        });

        $("#print-laser-order").on("click", async function() {
            try {
                var type = await printConfirm();
                if (!type) {
                    $.alert({
                        title: "Thông báo",
                        content: "Vui lòng chọn hình thức in!",
                        type: 'red'
                    });
                    return;
                }

                switch (type) {
                    case 'single':
                        printLaser(data, tempEirLaser, tempSrvLaser);
                        break;
                    case 'list':
                        printOrderList(data, tempOrderList);
                        break;
                    default:
                        toastr["error"]("Vui lòng chọn lại kiểu in!")
                        break;
                }

            } catch (error) {
                return;
            }
        });

        //lam tron so luong+don gia theo yeu cau KT
        function printInv(data, type = 'CAS', formatNum = '#,###', formatNumQty_Unit = '#,###') { //them moi hd thu sau //them moi lam tron so
            if (data && data.length > 0) {
                var invContent = $("#Print-INV");
                invContent.html(type == 'CAS' ? tempINV : tempINV_CRE); //them moi hd thu sau
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
                        invContent.find('.INV-content:last').find('span.INV_YEAR').text(d.getFullYear().toString().substring(2));
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
                                    format: formatNum, //them moi lam tron so
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
                                format: formatNum, //them moi lam tron so
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
                var amoutInWords = '<?= isset($AmountInWords) ? $AmountInWords : ""; ?>';
                invContent.find('.INV-content:last').find('span.AmountInWords').text(amoutInWords ? amoutInWords.toUpperCase() : ""); //doc tien usd

                //set data for each row and append to table
                var i = 1;
                $.each(data, function(idx, item) {
                    invContent.find('.INV-content:last').find("table tbody").append(tempRowINV);
                    var lastRow = invContent.find("table tbody tr:last");
                    lastRow.find('td.STT').text(i++);
                    $.each(Object.keys(item), function(ix, key) {
                        if (['AMOUNT'].indexOf(key) != -1) {
                            var n = $.formatNumber(item[key], {
                                format: formatNum, //them moi lam tron so
                                locale: "us"
                            });
                            lastRow.find('td.' + key).text(n);
                        } else if (['QTY', 'UNIT_RATE'].indexOf(key) != -1) { //lam tron so luong+don gia theo yeu cau KT
                            var n = $.formatNumber(item[key], {
                                format: formatNumQty_Unit, //lam tron so luong+don gia theo yeu cau KT
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
    });
</script>

<!--format number-->
<script src="<?= base_url('assets/js/jshashtable-2.1.js'); ?>"></script>
<script src="<?= base_url('assets/js/jquery.numberformatter-1.2.3.min.js'); ?>"></script>