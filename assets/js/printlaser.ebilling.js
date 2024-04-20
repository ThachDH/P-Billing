/**
 * Created by levuh on 10/10/2023
 */
function createContainer(id) {
    let container = document.createElement("div");
    container.setAttribute("id", id);
    container.setAttribute("class", "m-hidden");
    document.body.appendChild(container);
    return $(`#${id}`);
}

function printConfirm() {
    return new Promise((resolve, reject) => {
        resolve('single');
        return;
        var contents = `<div class="pt-3 pl-2 pr-4">
                                <div class="form-group">
                                    <label class="radio radio-outline-primary" style="padding-right: 20px">
                                        <input name="print-type" type="radio" value="single" checked>
                                        <span class="input-span"></span>
                                        IN TỪNG LỆNH
                                    </label>
                                    <label class="radio radio-outline-primary" style="padding-right: 20px">
                                        <input name="print-type" type="radio" value="list">
                                        <span class="input-span"></span>
                                        IN DANH SÁCH
                                    </label>
                                </div>
                            </div>`;

        $.confirm({
            columnClass: 'col-md-4 col-md-offset-4',
            title: 'Chọn hình thức in',
            type: 'blue',
            content: contents,
            buttons: {
                ok: {
                    text: 'OK',
                    btnClass: 'btn-sm btn-primary btn-confirm',
                    keys: ['Enter'],
                    action: function () {
                        var type = this.$content.find("input[name='print-type']:checked").val();
                        resolve(type);
                    }
                },
                later: {
                    text: 'Huỷ',
                    btnClass: 'btn-sm',
                    keys: ['ESC'],
                    action: function () {
                        reject()
                    }
                }
            }
        });
    })
}

function printLaser(data, tempEirLaser, tempSrvLaser) {
    $("#Print-NH, #Print-DR, #Print-DV").html('');
    if (!data || data.length == 0) {
        toastr["warning"]("Không thể in!<br> Vui lòng kiểm tra lại!");
        return;
    }

    //set data for LOLO
    var loloServiceList = data.filter(p => p.OrderType == 'NH');
    if (loloServiceList.length > 0) {
        var loloPrintContent = $("#Print-NH");
        if (loloPrintContent.length == 0) {
            toastr["warning"]("Không tìm thấy container Print-NH!");
            return;
        }

        $.each(loloServiceList, function (idx, it) {
            var template = tempEirLaser;

            $.each(Object.keys(it), function (idx, key) {
                var item = Object.assign({}, it);
                if (['IssueDate', 'BerthDate', 'ETA', 'ETD','DateIn', 'ExpDate', 'ExpPluginDate', 'OprExpDate'].indexOf(key) != -1) {
                    //neu co ngay dien lanh + co nhiet do -> clear ngay han lenh
                    if (key == 'ExpDate' || key == 'ExpPluginDate') {
                        if (item['ExpPluginDate'] && item['Temperature']) {
                            item['ExpDate'] = '';
                        } else {
                            item['ExpPluginDate'] = '';
                        }
                    }

                    let tmex = item[key] ? getDateTime(item[key]) : '';
                    if (!tmex) {
                        item[key] = '';
                    } else {
                        let ttime = tmex.split(' ')[1];
                        let tt = ttime.split(':')[0] + ':' + ttime.split(':')[1];
                        item[key] = tmex.split(' ')[0] + ' ' + tt;
                    }

                    if (item[key].includes('1900')) {
                        item[key] = '';
                    }
                }

                if (key == 'IsLocal') {
                    item[key] = item[key] == 'F' ? 'Ngoại' : (item[key] == 'L' ? 'Nội' : '');
                }

                if (key == 'cTLHQ' && item['CJMode_CD'] == 'LAYN'  && item['IsLocal'] != 'L') {
                    let tempTLhq = '';
                    if (item['cTLHQ'] == '1') { 
                        tempTLhq = `<div style='padding:9px;border: 2px solid #0d4cb3;color: #111;text-align:center;font-weight: bold;font-family: "Times New Roman","serif";'>
                                            <div style="padding-bottom:5px;font-size:16px">ĐỦ ĐIỀU KIỆN</div>
                                            QUA KHU VỰC GIÁM SÁT
                                        </div>`;
                    } else {
                        tempTLhq = `<div style='padding:9px;border: 2px solid #0d4cb3;color: red;text-align:center;font-weight: bold;font-family: "Times New Roman","serif";'>
                                            <div style="padding-bottom:5px;font-size:16px">CHƯA ĐỦ ĐIỀU KIỆN</div>
                                            QUA KHU VỰC GIÁM SÁT
                                        </div>`;
                    }

                    template = template.replaceAll("[" + key + "]", tempTLhq);
                } else if (key == 'QrData') {
                    var div = document.createElement('div');
                    $(div).html(template);
                    $(div).find('#qr-img').attr('src', item[key]);
                    template = $(div).html();
                }
                //add info in cago cell
                else if (key == 'CARGO_TYPE') {
                    var sReefer = ["Temperature", "Vent", "Vent_Unit"];
                    var sDangerous = ["UNNO", "CLASS"];
                    var sOOG = ["OOG_TOP", "OOG_LEFT", "OOG_RIGHT", "OOG_BACK", "OOG_FRONT"];
                    var sDaR = sReefer.concat(sDangerous);
                    var sOaD = sOOG.concat(sDangerous);

                    var moreInfor = [];
                    var strMoreInfo = '';
                    switch (item[key]) {
                        case 'RF':
                            moreInfor = sReefer.map(k => item[k] || '');
                            break;
                        case 'DG':
                            moreInfor = sDangerous.map(k => item[k] || '');
                            break;
                        case 'OG':
                            moreInfor = sOOG.map(k => item[k] || '');
                            break;
                        case 'DR':
                            moreInfor = sDaR.map(k => item[k] || '');
                            break;
                        case 'OD':
                            moreInfor = sOaD.map(k => item[k] || '');
                            break;
                    }

                    if (moreInfor.filter(p => p).length > 0) {
                        strMoreInfo = "(" + moreInfor.map(p => p || '-').join('|') + ")";
                    }

                    template = template.replaceAll("[CARGO_ADD_INFO]", strMoreInfo);
                }
                //add info in cago cell
                else {
                    template = template.replaceAll("[" + key + "]", item[key] || '');
                }
            });

            loloPrintContent.append(template);
        });

        let loadImgSuccess = 0;
        loloPrintContent.find('#qr-img').on('load', function () {
            loadImgSuccess += 1;
            if (loadImgSuccess >= 2) {
                loloPrintContent.print();
                loloPrintContent.html('');
                loadImgSuccess = 0;
            }
        })

        loloPrintContent.find('#img-logo').on('load', function () {
            loadImgSuccess += 1;
            if (loadImgSuccess >= 2) {
                loloPrintContent.print();
                loloPrintContent.html('');
                loadImgSuccess = 0;
            }
        })

        // var win = window.open("", "_blank");
        // $(win.document.body).append(loloPrintContent);
    }

    //set data for STUFF - UNSTUFF
    var stuffList = data.filter(p => p.OrderType == 'DR' || p.OrderType == 'DV');
    if (stuffList.length > 0) {
        var printContent = $("#Print-DR");
        if (printContent.length == 0) {
            toastr["warning"]("Không tìm thấy container Print-NH!");
            return;
        }

        $.each(stuffList, function (idx, it) {
            var template = tempSrvLaser;
            $.each(Object.keys(it), function (idx, key) {

                var item = Object.assign({}, it);
                if (['IssueDate', 'BerthDate', 'ETA', 'ETD', 'DateIn', 'ExpDate', 'ExpPluginDate', 'OprExpDate'].indexOf(key) != -1) {
                    //neu co ngay dien lanh + co nhiet do -> clear ngay han lenh
                    if (key == 'ExpDate' || key == 'ExpPluginDate') {
                        if (item['ExpPluginDate'] && item['Temperature']) {
                            item['ExpDate'] = '';
                        } else {
                            item['ExpPluginDate'] = '';
                        }
                    }

                    let tmex = item[key] ? getDateTime(item[key]) : '';
                    if (!tmex) {
                        item[key] = '';
                    } else {
                        let ttime = tmex.split(' ')[1];
                        let tt = ttime.split(':')[0] + ':' + ttime.split(':')[1];
                        item[key] = tmex.split(' ')[0] + ' ' + tt;
                    }

                    if (item[key].includes('1900')) {
                        item[key] = '';
                    }
                }
                if (key == 'IsLocal') {
                    item[key] = item[key] == 'F' ? 'Ngoại' : (item[key] == 'L' ? 'Nội' : '');
                }
                if (key == 'QrData') {
                    var div = document.createElement('div');
                    $(div).html(template);
                    $(div).find('#qr-img').attr('src', item[key]);
                    template = $(div).html();
                }
                //add info in cago cell
                else if (key == 'CARGO_TYPE') {
                    var sReefer = ["Temperature", "Vent", "Vent_Unit"];
                    var sDangerous = ["UNNO", "CLASS"];
                    var sOOG = ["OOG_TOP", "OOG_LEFT", "OOG_RIGHT", "OOG_BACK", "OOG_FRONT"];
                    var sDaR = sReefer.concat(sDangerous);
                    var sOaD = sOOG.concat(sDangerous);

                    var moreInfor = [];
                    var strMoreInfo = '';
                    switch (item[key]) {
                        case 'RF':
                            moreInfor = sReefer.map(k => item[k] || '');
                            break;
                        case 'DG':
                            moreInfor = sDangerous.map(k => item[k] || '');
                            break;
                        case 'OG':
                            moreInfor = sOOG.map(k => item[k] || '');
                            break;
                        case 'DR':
                            moreInfor = sDaR.map(k => item[k] || '');
                            break;
                        case 'OD':
                            moreInfor = sOaD.map(k => item[k] || '');
                            break;
                    }

                    if (moreInfor.filter(p => p).length > 0) {
                        strMoreInfo = "(" + moreInfor.map(p => p || '-').join('|') + ")";
                    }

                    template = template.replaceAll("[CARGO_ADD_INFO]", strMoreInfo);
                }
                //add info in cago cell
                else {
                    template = template.replaceAll("[" + key + "]", item[key] || '');
                }
            });

            printContent.append(template);
        });

        let loadImgSuccess = 0;
        printContent.find('#qr-img').on('load', function () {
            loadImgSuccess += 1;
            if (loadImgSuccess >= 2) {
                printContent.print();
                printContent.html('');
                loadImgSuccess = 0;
            }
        })

        printContent.find('#img-logo').on('load', function () {
            loadImgSuccess += 1;
            if (loadImgSuccess >= 2) {
                printContent.print();
                printContent.html('');
                loadImgSuccess = 0;
            }
        })
    }
}

function printOrderList(data, tempOrderList) {
    if (!data || data.length == 0) {
        toastr["warning"]("Không thể in!<br> Vui lòng kiểm tra lại!")
        return;
    }

    //set data for LOLO
    var printContents = [];
    var loloServiceList = data.filter(p => p.OrderType == 'NH');
    if (loloServiceList.length > 0) {
        let container = createContainer("Print-order-list-NH");

        let pr = printOrderListByOrderType(loloServiceList, 'NÂNG HẠ', tempOrderList);
        container.append(pr)
        printContents.push(container);
    }

    var stuffList = data.filter(p => p.OrderType == 'DR' || p.OrderType == 'DV');
    if (stuffList.length > 0) {
        let container = createContainer("Print-order-list-DV");
        let pr = printOrderListByOrderType(stuffList, 'DỊCH VỤ', tempOrderList);
        container.append(pr)
        printContents.push(container);
    }

    if (printContents.length == 0) {
        toastr["warning"]("Không thể in!<br> Vui lòng kiểm tra lại!")
        return;
    }

    printQueue(printContents);
}

function printOrderListByOrderType(printData, title, tempOrderList) {
    var template = tempOrderList;
    var rowDetailMatch = tempOrderList.match(/\<ROW_DETAIL\>((.|\r|\n|\t|\s)*?)\<\/ROW_DETAIL\>/i);
    var rowDetail = rowDetailMatch[1];
    var detailReplace = rowDetailMatch[0]; //include prefix [ROW_DETAIL]
    template = template.replace(/\<ROW_DETAIL\>((.|\r|\n|\t|\s)*?)\<\/ROW_DETAIL\>/i, '[ROW_DETAIL]');

    var it = printData[0];
    var item = Object.assign({
        TITLE: title
    }, it);

    for (var idx = 0; idx < Object.keys(item).length; idx++) {
        var key = Object.keys(item)[idx];
        if (['IssueDate', 'BerthDate', 'ETA', 'ETD', 'ExpDate', 'ExpPluginDate', 'OprExpDate', 'BerthDate', 'YARD_CLOSE'].indexOf(key) != -1) {
            //neu co ngay dien lanh + co nhiet do -> clear ngay han lenh
            if (key == 'ExpDate' || key == 'ExpPluginDate') {
                if (item['ExpPluginDate'] && item['Temperature']) {
                    item['ExpDate'] = '';
                } else {
                    item['ExpPluginDate'] = '';
                }
            }

            let tmex = item[key] ? getDateTime(item[key]) : '';
            if (!tmex) {
                item[key] = '';
            } else {
                let ttime = tmex.split(' ')[1];
                let tt = ttime.split(':')[0] + ':' + ttime.split(':')[1];
                item[key] = tmex.split(' ')[0] + ' ' + tt;
            }

            if (item[key].includes('1900')) {
                item[key] = '';
            }
        }
        if (key == 'IsLocal') {
            item[key] = item[key] == 'F' ? 'Ngoại' : (item[key] == 'L' ? 'Nội' : '');
        }
        if (key == 'TruckOrBarge') {
            item[key] = item[key] == 'T' ? 'XE' : (item[key] == 'B' ? 'SÀ LAN' : '');
        }
        if (key == 'PAYMENT_TYPE') {
            item[key] = item[key] == 'M' ? 'CASH' : (item[key] == 'C' ? 'CREDIT' : '');
        }

        template = template.replaceAll("[" + key + "]", item[key] || '');
    }

    var rowDetailContent = '';
    var objContainerSumary = {
        C20_R: 0,
        C20: 0,
        C20HC_R: 0,
        C20HC: 0,
        C40_R: 0,
        C40: 0,
        C40HC_R: 0,
        C40HC: 0,
        C45_R: 0,
        C45: 0,
        C48_R: 0,
        C48: 0
    };

    for (var idx = 0; idx < printData.length; idx++) {
        let item = Object.assign({}, printData[idx]);
        let content = rowDetail;
        for (var n = 0; n < Object.keys(item).length; n++) {
            let key = Object.keys(item)[n];
            if (key == 'CMDWeight') {
                item[key] = item[key] ? parseFloat(item[key]) * 1000 : '';
            }

            if (key == 'DateIn') {
                item['StorageDay'] = item['DateIn'] ? (moment(item['IssueDate']).diff(moment(item['DateIn']), 'days') + 1) : '';
            }

            if (key == 'ISO_SZTP') {
                let prefixStatus = item['Status'] === 'E' ? '_R' : '';
                let firstChar = String(item[key]).substring(0, 1);
                let secondChar = String(item[key]).substring(1, 2);
                let contSize = '';
                switch (firstChar) {
                    case "2":
                        contSize = secondChar === "5" ? "20HC" : "20";
                        break;
                    case "4":
                        contSize = secondChar === "5" ? "40HC" : (secondChar === "8" ? "48" : "40");
                        break;
                    case "L":
                    case "M":
                    case "9":
                        contSize = "45";
                        break;
                    default:
                        break;
                }
                objContainerSumary[`C${contSize}${prefixStatus}`] += 1;
            }

            content = content.replaceAll("[" + key + "]", item[key] || '');
        }
        rowDetailContent += content;
    }

    for (var ix = 0; ix < Object.keys(objContainerSumary).length; ix++) {
        let key = Object.keys(objContainerSumary)[ix];
        template = template.replaceAll("[" + key + "]", objContainerSumary[key] || '0');
    }

    template = template.replace('[ROW_DETAIL]', rowDetailContent)
    return template;
}

function printDraft(url, draftNos, btn) {
    btn && btn.button("loading");
    var drafts = Array.isArray(draftNos) ? draftNos.join(" ") : draftNos
    var formData = {
        "draftNo": drafts
    };

    $.ajax({
        url: url,
        dataType: 'json',
        data: formData,
        type: 'POST',
        success: function (data) {
            btn && btn.button("reset");

            if (data.deny) {
                toastr["error"](data.deny);
                return;
            }
            if (!data.result || data.result.length == 0) {
                toastr["error"]('Không thể tải dữ liệu in');
                return;
            }
            if (!data.tempDraftLaser) {
                toastr["error"]('Không thể tải mẫu in');
                return;
            }
            printDraftLaser(data.result, data.tempDraftLaser);
        },
        error: function (err) {
            btn && btn.button("reset");
            $('.toast').remove();
            toastr['error']("Có lỗi xảy ra! <br/>  Vui lòng liên hệ với bộ phận kỹ thuật! ");
            console.log(err);
        }
    });
}

function printDraftLaser(printData, tempDraftLaser) {
    var container = createContainer('Print-Draft-Container');
    for (let it of printData) {
        var template = tempDraftLaser;
        var rowDetailMatch = tempDraftLaser.match(/\<ROW_DETAIL\>((.|\r|\n|\t|\s)*?)\<\/ROW_DETAIL\>/i);
        var rowDetail = rowDetailMatch[1];
        var detailReplace = rowDetailMatch[0]; //include prefix [ROW_DETAIL]
        template = template.replace(/\<ROW_DETAIL\>((.|\r|\n|\t|\s)*?)\<\/ROW_DETAIL\>/i, '[ROW_DETAIL]');

        for (var i = 0; i < Object.keys(it).length; i++) {
            var key = Object.keys(it)[i];
            if (key == 'ROW_DETAIL') {
                var rowDetailContent = '';
                var details = it[key];
                for (var idx = 0; idx < details.length; idx++) {
                    let item = Object.assign({}, details[idx]);
                    let content = rowDetail;
                    for (var n = 0; n < Object.keys(item).length; n++) {
                        let key = Object.keys(item)[n];
                        content = content.replaceAll("[" + key + "]", item[key] || '');
                    }
                    rowDetailContent += content;
                }

                template = template.replace('[ROW_DETAIL]', rowDetailContent)
            } else {
                template = template.replaceAll("[" + key + "]", it[key] || '');
            }
        }

        container.append(template);
    }

    var maxTableBodyHeight = 120 * 0.75; //pt = 3/4px
    var totalRowHeight = 18 * 0.75;
    var maxEachRowDetailHeight = (maxTableBodyHeight - totalRowHeight) / container.find('.resize-font').length;

    for (var i = 0; i < container.find('.resize-font').length; i++) {
        var $this = container.find('.resize-font:eq(' + i + ')');
        var tdSize = $this.closest('td').getSize();
        if ((tdSize.height * 0.75) > maxEachRowDetailHeight) {
            var area = (tdSize.width * 0.75) * maxEachRowDetailHeight;
            var contentLength = $this.text().length;
            $this[0].style.fontSize = Math.sqrt(area / contentLength) + 'pt';
        }
    }

    var loadImgSuccess = 0;
    var countLogos = container.find('#img-logo').length;
    container.find('#img-logo').each(function () {
        $(this).on('load', function () {
            loadImgSuccess += 1;
            if (loadImgSuccess >= countLogos) {
                container.print();
                container.remove();
                loadImgSuccess = 0;
            }
        })
    })
}

function printQueue(printContents) {
    for (let printContent of printContents) {
        let loadImgSuccess = 0;
        printContent.find('#img-logo').on('load', function () {
            loadImgSuccess += 1;
            if (loadImgSuccess >= 1) {
                printContent.print();
                printContent.remove();
                loadImgSuccess = 0;
            }
        })
    }
}