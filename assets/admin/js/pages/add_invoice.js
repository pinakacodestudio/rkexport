$(document).ready(function () {

    $('#invoicedate').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        todayBtn: "linked",
    });
    /****CHANNEL CHANGE EVENT****/
    $('#channelid').on('change', function (e) {
        getmember();
        changeextrachargesamount();
        overallextracharges();
        netamounttotal();
    });
    /****MEMBER CHANGE EVENT****/
    $('#memberid').on('change', function (e) {
        getMemberSalesOrders();
        getbillingaddress();
        changeextrachargesamount();
        overallextracharges();
        netamounttotal();
    });
    /****ORDERID CHANGE EVENT****/
    $('#orderid').on('change', function (e) {
        getTransactionProducts();
        getbillingaddress();
        changeextrachargesamount();
        overallextracharges();
        netamounttotal();
    });
    /****BILLING ADDRESS CHANGE EVENT****/
    $('#billingaddressid').on('change', function (e) {
        $('#billingaddress').val($('#billingaddressid option:selected').text());
    });
    /****SHIPPING ADDRESS CHANGE EVENT****/
    $('#shippingaddressid').on('change', function (e) {
        $('#shippingaddress').val($('#shippingaddressid option:selected').text());
    });

    $(".countcharges0 .add_charges_btn").hide();
    $(".countcharges0 .add_charges_btn:last").show();

    if (ACTION == 1 /*  && OrderId!='' && OrderId!=null */ ) {
        getmember();
        // getMemberSalesOrders();
        getbillingaddress();
        getTransactionProducts();
        netamounttotal();


        $(".countcharges0 .add_charges_btn").hide();
        $(".countcharges0 .add_charges_btn:last").show();
        $(".countcharges0 .remove_charges_btn").show();
        if ($(".countcharges0 .remove_charges_btn:visible").length == 1) {
            $(".countcharges0 .remove_charges_btn").hide();
        }

    }
    $('body').on('keyup', '.qty', function () {
        var divid = $(this).attr("id").match(/(\d+)/g);
        var orderid = $("#orderidarr" + divid).val();
        if (parseFloat(this.value) > parseFloat($("#orderqty" + divid).val())) {
            if (MANAGE_DECIMAL_QTY == 1) {
                $(this).val(parseFloat($("#orderqty" + divid).val()).toFixed(2));
            } else {
                $(this).val(parseInt($("#orderqty" + divid).val()));
            }
        }
        totalproductamount(orderid, divid);
    });
    $('body').on('change', '.qty', function () {
        var divid = $(this).attr("id").match(/(\d+)/g);
        var orderid = $("#orderidarr" + divid).val();
        if (parseFloat(this.value) > parseFloat($("#orderqty" + divid).val())) {
            if (MANAGE_DECIMAL_QTY == 1) {
                $(this).val(parseFloat($("#orderqty" + divid).val()).toFixed(2));
            } else {
                $(this).val(parseInt($("#orderqty" + divid).val()));
            }
        }
        totalproductamount(orderid, divid);
    });

});
$(document).on('keyup', '#cashbackpercent', function (e) {

    if (parseFloat(this.value) >= 100) {
        $("#cashbackpercent").val("100");
    }
    calculatecashbackdiscount();
});
$(document).on('keyup', '#cashbackamount', function (e) {
    calculatecashbackdiscountmount($(this).val());
});

function calculatecashbackdiscount() {
    var discountpercentage = $("#cashbackpercent").val();
    discountpercentage = (discountpercentage != '' && discountpercentage != 0) ? discountpercentage : 0;
    var payableamount = $("#inputtotalpayableamount").val();
    payableamount = (payableamount != '' && payableamount != 0) ? payableamount : 0;

    if (payableamount != 0 && discountpercentage != 0) {
        var discountamount = (parseFloat(payableamount) * parseFloat(discountpercentage) / 100);

        $("#cashbackamount").val(parseFloat(discountamount).toFixed(2));
    } else {
        $("#cashbackamount").val('');
    }
}

function calculatecashbackdiscountmount(discountamount) {

    var discountpercentage = 0;
    var payableamount = $("#inputtotalpayableamount").val();
    payableamount = (payableamount != 0) ? payableamount : 0;

    if (discountamount != undefined && discountamount != '') {

        if (parseFloat(discountamount) > parseFloat(payableamount)) {
            discountamount = parseFloat(payableamount);
            $("#cashbackamount").val(parseFloat(discountamount).toFixed(2));
        }

        if (parseFloat(payableamount) != 0) {
            var discountpercentage = ((parseFloat(discountamount) * 100) / parseFloat(payableamount));
        }

        $("#cashbackpercent").val(parseFloat(discountpercentage).toFixed(2));
    } else {
        $("#cashbackamount").val('');
        $("#cashbackpercent").val("");
    }
}

function getmember() {
    $('#memberid')
        .find('option')
        .remove()
        .end()
        .append('<option value="0">Select ' + Member_label + '</option>')
        .val('0');
    if (ACTION == 0) {

        $('#orderid')
            .find('option')
            .remove()
            .end()
            .append()
            .val('0');
    }
    $('#billingaddressid')
        .find('option,optgroup')
        .remove()
        .end()
        .val('0');
    $('#shippingaddressid')
        .find('option,optgroup')
        .remove()
        .end()
        .val('0');

    $('#memberid').selectpicker('refresh');
    $('#orderid').selectpicker('refresh');
    $('#billingaddressid,#shippingaddressid').selectpicker('refresh');

    var channelid = (ChannelId > 0) ? ChannelId : $("#channelid").val();

    if (channelid != 0) {
        var uurl = SITE_URL + "invoice/getOrderMemberByChannel";

        $.ajax({
            url: uurl,
            type: 'POST',
            data: {
                channelid: String(channelid)
            },
            dataType: 'json',
            async: false,
            beforeSend: function () {
                $('.mask').show();
                $('#loader').show();
            },
            success: function (response) {

                for (var i = 0; i < response.length; i++) {

                    $('#memberid').append($('<option>', {
                        value: response[i]['id'],
                        text: response[i]['name']
                    }));

                    if (MemberId != 0) {
                        $('#memberid').val(MemberId);
                    }
                }
            },
            error: function (xhr) {
                //alert(xhr.responseText);
            },
            complete: function () {
                $('.mask').hide();
                $('#loader').hide();
            },
        });
    } else {
        $("#invoiceproducttable tbody").html("<tr><td colspan='9' class='text-center'>No data available in table.</td></tr>");
    }
    $('#memberid').selectpicker('refresh');
}

function getMemberSalesOrders() {
    $('#orderid')
        .find('option')
        .remove()
        .end()
        .append()
        .val('0');

    $('#orderid').selectpicker('refresh');

    var memberid = (MemberId > 0) ? MemberId : $("#memberid").val();

    if (memberid != 0) {
        var uurl = SITE_URL + "member/getMemberSalesOrders";

        $.ajax({
            url: uurl,
            type: 'POST',
            data: {
                memberid: String(memberid),
                from: 'invoice'
            },
            dataType: 'json',
            //async: false,
            beforeSend: function () {
                $('.mask').show();
                $('#loader').show();
            },
            beforeSend: function () {
                $('.mask').show();
                $('#loader').show();
            },
            success: function (response) {

                for (var i = 0; i < response.length; i++) {

                    if (ACTION == 1) {
                        if (OrderId != null || OrderId != '') {

                            OrderId = OrderId.toString().split(',');

                            if (OrderId.includes(response[i]['id'])) {
                                $('#orderid').append($('<option>', {
                                    value: response[i]['id'],
                                    selected: "selected",
                                    text: ucwords(response[i]['orderid']),
                                    "data-billingid": response[i]['billingid'],
                                    "data-shippingid": response[i]['shippingid']
                                }));
                            } else {
                                $('#orderid').append($('<option>', {
                                    value: response[i]['id'],
                                    text: ucwords(response[i]['orderid']),
                                    "data-billingid": response[i]['billingid'],
                                    "data-shippingid": response[i]['shippingid']
                                }));
                            }
                        }
                    } else {
                        $('#orderid').append($('<option>', {
                            value: response[i]['id'],
                            text: ucwords(response[i]['orderid']),
                            "data-billingid": response[i]['billingid'],
                            "data-shippingid": response[i]['shippingid']
                        }));
                    }
                }
                $('#orderid').selectpicker('refresh');
            },
            error: function (xhr) {
                //alert(xhr.responseText);
            },
            complete: function () {
                $('.mask').hide();
                $('#loader').hide();
            },
        });
    } else {
        $("#invoiceproducttable tbody").html("<tr><td colspan='9' class='text-center'>No data available in table.</td></tr>");
    }


}

function getbillingaddress(loadtype = 0) {
    $('#billingaddressid')
        .find('option,optgroup')
        .remove()
        .end()
        .val('0');
    $('#shippingaddressid')
        .find('option,optgroup')
        .remove()
        .end()
        .val('0');
    $('#billingaddressid,#shippingaddressid').selectpicker('refresh');

    var memberid = (MemberId > 0) ? MemberId : $("#memberid").val();
    var BillingAddressID = (addressid != 0) ? addressid : $("#orderid option:selected:last").attr("data-billingid");
    var ShippingAddressID = (shippingaddressid != 0) ? shippingaddressid : $("#orderid option:selected:last").attr("data-shippingid");

    var ordertype = 0;
    if (memberid != 0) {
        var uurl = SITE_URL + "order/getBillingAddresstByMemberId";
        if (loadtype == 0) {
            passdata = {
                memberid: String(memberid),
                loadtype: 0,
                ordertype: ordertype
            };
        } else {
            passdata = {
                memberid: String(memberid),
                loadtype: 1,
                ordertype: ordertype
            };
        }

        $.ajax({
            url: uurl,
            type: 'POST',
            data: passdata,
            //dataType: 'json',
            async: false,
            beforeSend: function () {
                $('.mask').show();
                $('#loader').show();
            },
            success: function (response) {
                var obj = JSON.parse(response);
                if (!jQuery.isEmptyObject(obj['billingaddress'])) {
                    for (var i = 0; i < obj['billingaddress'].length; i++) {

                        if (BillingAddressID != 0 && BillingAddressID == obj['billingaddress'][i]['id']) {
                            if ($('#billingaddressid option').length == 0) {
                                $('#billingaddressid').append('<optgroup label="Order Billing Address"></optgroup>');
                            } else {
                                $('#billingaddressid').prepend('<optgroup label="Order Billing Address"></optgroup>');
                            }
                            //$('#billingaddressid option:first').after('<optgroup label="Order Billing Address"></optgroup>');
                            $('#billingaddressid optgroup').html($('<option>', {
                                value: obj['billingaddress'][i]['id'],
                                text: ucwords(obj['billingaddress'][i]['address'])
                            }));
                        } else {
                            $('#billingaddressid').append($('<option>', {
                                value: obj['billingaddress'][i]['id'],
                                text: ucwords(obj['billingaddress'][i]['address'])
                            }));
                        }
                        if (ShippingAddressID != 0 && ShippingAddressID == obj['billingaddress'][i]['id'] && ShippingAddressID != "undefined") {
                            if ($('#shippingaddressid option').length == 0) {
                                $('#shippingaddressid').append('<optgroup label="Order Shipping Address"></optgroup>');
                            } else {
                                $('#shippingaddressid').prepend('<optgroup label="Order Shipping Address"></optgroup>');
                            }
                            // $('#shippingaddressid option:first').after('<optgroup label="Order Shipping Address"></optgroup>');
                            $('#shippingaddressid optgroup').html($('<option>', {
                                value: obj['billingaddress'][i]['id'],
                                text: ucwords(obj['billingaddress'][i]['address'])
                            }));
                        } else {
                            $('#shippingaddressid').append($('<option>', {
                                value: obj['billingaddress'][i]['id'],
                                text: ucwords(obj['billingaddress'][i]['address'])
                            }));
                        }
                    }
                    if (BillingAddressID != 0) {
                        $('#billingaddressid').val(BillingAddressID);
                    }
                    if (ShippingAddressID != 0) {
                        $('#shippingaddressid').val(ShippingAddressID);
                    }
                }
                /* if (!jQuery.isEmptyObject(obj['countrewards'])) {
                    $('#redeempointsforbuyer').val(obj['countrewards']['rewardpoint']);
                } */
            },
            error: function (xhr) {
                //alert(xhr.responseText);
            },
            complete: function () {
                $('.mask').hide();
                $('#loader').hide();
            },
        });
    }
    $('#billingaddressid,#shippingaddressid').selectpicker('refresh');
    if ($('#billingaddressid').val() != 0) {
        $('#billingaddress').val($('#billingaddressid option:selected').text());
    } else {
        $('#billingaddress').val('');
    }
    if ($('#shippingaddressid').val() != 0) {
        $('#shippingaddress').val($('#shippingaddressid option:selected').text());
    } else {
        $('#shippingaddress').val('');
    }
}

function getTransactionProducts() {

    var memberid = (MemberId > 0) ? MemberId : $("#memberid").val();
    var orderid = (ACTION == 1) ? $("#oldorderid").val() : $("#orderid").val();
    var invoiceid = $("#invoiceid").val();

    $('.disccol,.cgstcol,.sgstcol,.igstcol').show();

    if (orderid != '' && orderid != null) {
        var uurl = SITE_URL + "invoice/getTransactionProducts";

        $.ajax({
            url: uurl,
            type: 'POST',
            data: {
                memberid: String(memberid),
                orderid: String(orderid),
                invoiceid: String(invoiceid)
            },
            dataType: 'json',
            async: false,
            beforeSend: function () {
                $('.mask').show();
                $('#loader').show();
            },
            success: function (response) {
                if (response != "") {
                    var orderproducts = response['orderproducts'];
                    var orderamountdata = response['orderamountdata'];
                    var gstprice = response['gstprice'];

                    var htmldata = discolumn = "";
                    var gstcolumn = [];

                    var headerdata = '<tr>\
                                  <th rowspan="2" class="width5">Sr. No.</th>\
                                  <th rowspan="2">Product Name</th>\
                                  <th rowspan="2">Remarks</th>\
                                  <th rowspan="2" class="width12">Qty.</th>\
                                  <th rowspan="2" class="text-right">Rate (Excl. Tax)</th>';
                    if (gstprice == 1) {
                        headerdata += '<th class="text-right width8 disccol">Dis.(%)</th>\
                                  <th class="text-right width8 sgstcol">SGST (%)</th>\
                                  <th class="text-right width8 cgstcol">CGST (%)</th>\
                                  <th class="text-right width8 igstcol">IGST (%)</th>';
                    } else {
                        headerdata += '<th class="text-right width8 sgstcol">SGST (%)</th>\
                                  <th class="text-right width8 cgstcol">CGST (%)</th>\
                                  <th class="text-right width8 igstcol">IGST (%)</th>\
                                  <th class="text-right width8 disccol">Dis.(%)</th>';
                    }
                    headerdata += '<th rowspan="2" class="text-right">Amount (' + CURRENCY_CODE + ')</th>\
                              </tr>\
                              <tr>';
                    if (gstprice == 1) {
                        headerdata += '<th class="text-right width8 disccol">Amt. (' + CURRENCY_CODE + ')</th>\
                                  <th class="text-right width8 sgstcol">Amt. (' + CURRENCY_CODE + ')</th>\
                                  <th class="text-right width8 cgstcol">Amt. (' + CURRENCY_CODE + ')</th>\
                                  <th class="text-right width8 igstcol">Amt. (' + CURRENCY_CODE + ')</th>';
                    } else {
                        headerdata += '<th class="text-right width8 sgstcol">Amt. (' + CURRENCY_CODE + ')</th>\
                                  <th class="text-right width8 cgstcol">Amt. (' + CURRENCY_CODE + ')</th>\
                                  <th class="text-right width8 igstcol">Amt. (' + CURRENCY_CODE + ')</th>\
                                  <th class="text-right width8 disccol">Amt. (' + CURRENCY_CODE + ')</th>';
                    }
                    headerdata += '</tr>';

                    if (orderproducts != null && orderproducts != "") {
                        if (orderproducts.length > 0) {
                            for (var i = 0; i < orderproducts.length; i++) {

                                if (invoiceid != '') {
                                    var qty = parseFloat(orderproducts[i]['editquantity']);
                                    var orderqty = parseFloat(orderproducts[i]['quantity']) - parseFloat(orderproducts[i]['invoiceqty']);
                                } else {
                                    var qty = parseFloat(orderproducts[i]['quantity']) - parseFloat(orderproducts[i]['invoiceqty']);
                                    var orderqty = qty;
                                }

                                gstcolumn.push(orderproducts[i]['igst']);
                                // var qty = parseInt(orderproducts[i]['quantity']);
                                var tax = parseFloat(orderproducts[i]['tax']);
                                var amount = parseFloat(orderproducts[i]['amount']);
                                var originalprice = parseFloat(orderproducts[i]['originalprice']);
                                /* if(GST_PRICE == 1){
                                  var amount = parseFloat(orderproducts[i]['originalprice']);
                                } */
                                var discount = parseFloat(orderproducts[i]['discount']);
                                discolumn += parseFloat(discount);

                                var discountamount = ((parseFloat(originalprice) * parseFloat(qty)) * parseFloat(discount) / 100);

                                var totalprice = (parseFloat(amount) * parseFloat(qty));
                                var taxvalue = parseFloat(parseFloat(amount) * parseFloat(qty) * parseFloat(tax) / 100);
                                var total = parseFloat(totalprice) + parseFloat(taxvalue);

                                var orderid = orderproducts[i]['orderid'];
                                if (parseFloat(orderproducts[i]['quantity']) == parseFloat(orderproducts[i]['invoiceqty'])) {
                                    var orderid = "";
                                }

                                htmldata += "<tr class='countproducts' id='" + orderproducts[i]['orderproductsid'] + "'>";
                                htmldata += "<td rowspan='2'>" + (i + 1);
                                htmldata += '<input type="hidden" name="orderproductsid[]" value="' + orderproducts[i]['orderproductsid'] + '">';
                                htmldata += '<input type="hidden" name="transactionproductid[]" value="' + orderproducts[i]['transactionproductid'] + '">';
                                htmldata += '<input type="hidden" id="price' + orderproducts[i]['orderproductsid'] + '" value="' + parseFloat(amount) + '">';
                                htmldata += '<input type="hidden" id="actualprice' + orderproducts[i]['orderproductsid'] + '" value="' + parseFloat(originalprice) + '">';
                                htmldata += '<input type="hidden" id="tax' + orderproducts[i]['orderproductsid'] + '" value="' + parseFloat(tax) + '">';
                                htmldata += '<input type="hidden" id="taxtype' + orderproducts[i]['orderproductsid'] + '" value="' + orderproducts[i]['igst'] + '">';
                                htmldata += '<input type="hidden" id="taxvalue' + orderproducts[i]['orderproductsid'] + '" class="taxvalue" value="' + parseFloat(taxvalue).toFixed(2) + '">';
                                htmldata += '<input type="hidden" id="producttotal' + orderproducts[i]['orderproductsid'] + '" class="producttotal" value="' + parseFloat(parseFloat(amount) * parseFloat(qty)).toFixed(2) + '">';
                                htmldata += '<input type="hidden" id="discount' + orderproducts[i]['orderproductsid'] + '" class="discount" value="' + parseFloat(discount).toFixed(2) + '">';
                                htmldata += '<input type="hidden" name="orderidarr[]" id="orderidarr' + orderproducts[i]['orderproductsid'] + '" value="' + orderid + '">';
                                htmldata += '<input type="hidden" id="orderquantity' + orderproducts[i]['orderproductsid'] + '" value="' + parseFloat(orderproducts[i]['quantity']).toFixed(2) + '" class="orderquantity' + orderid + '">';

                                htmldata += "</td>";

                                htmldata += "<td rowspan='2'>" + ucwords(orderproducts[i]['productname']) + "<br><br><b>OrderID: </b>" + orderproducts[i]['ordernumber'] + "</td>";

                                htmldata += '<td rowspan="2"><div class="col-md-12 pl pr"><div class="form-group" id="productremarks' + orderproducts[i]['orderproductsid'] + '_div"><textarea name="productremarks[]" id="productremarks' + orderproducts[i]['orderproductsid'] + '" class="form-control productremarks">' + orderproducts[i]['remarks'] + '</textarea>\
                      </div></td>';

                                htmldata += '<td rowspan="2" class="width8"><div class="col-md-12 pl pr"><div class="form-group" id="quantity' + orderproducts[i]['orderproductsid'] + '_div"><input type="text" name="quantity[]" id="quantity' + orderproducts[i]['orderproductsid'] + '" class="form-control qty" value="' + parseFloat(qty).toFixed(2) + '" onkeypress="' + (MANAGE_DECIMAL_QTY == 1 ? "return decimal_number_validation(event, this.value,8);" : "return isNumber(event);") + '">\
                      <input type="hidden" name="orderqty" id="orderqty' + orderproducts[i]['orderproductsid'] + '" value="' + parseFloat(orderqty).toFixed(2) + '"></td>';

                                htmldata += "<td rowspan='2' class='text-right'>" + parseFloat(amount).toFixed(2) + "<br><br><p><b>Total Invoice Qty.: </b>" + parseFloat(orderproducts[i]['invoiceqty']).toFixed(2) + "</p></td>";

                                if (gstprice == 1) {

                                    if (parseFloat(discount) > 0) {
                                        htmldata += "<td class='text-right disccol'>" + parseFloat(discount).toFixed(2) + "</td>";
                                    } else {
                                        htmldata += "<td class='text-right disccol'>-</td>";
                                    }
                                    if (orderproducts[i]['igst'] == 1) {
                                        htmldata += "<td class='text-right sgstcol'>" + parseFloat((parseFloat(tax) / 2)).toFixed(2) + "</td>";
                                        htmldata += "<td class='text-right cgstcol'>" + parseFloat((parseFloat(tax) / 2)).toFixed(2) + "</td>";
                                    } else {
                                        htmldata += "<td class='text-right igstcol'>" + parseFloat(tax).toFixed(2) + "</td>";
                                    }
                                } else {
                                    if (orderproducts[i]['igst'] == 1) {
                                        htmldata += "<td class='text-right sgstcol'>" + parseFloat((parseFloat(tax) / 2)).toFixed(2) + "</td>";
                                        htmldata += "<td class='text-right cgstcol'>" + parseFloat((parseFloat(tax) / 2)).toFixed(2) + "</td>";
                                    } else {
                                        htmldata += "<td class='text-right igstcol'>" + parseFloat(tax).toFixed(2) + "</td>";
                                    }
                                    if (parseFloat(discount) > 0) {
                                        htmldata += "<td class='text-right disccol'>" + parseFloat(discount).toFixed(2) + "</td>";
                                    } else {
                                        htmldata += "<td class='text-right disccol'>-</td>";
                                    }
                                }

                                htmldata += "<td rowspan='2' class='text-right netamount' id='productnetprice" + orderproducts[i]['orderproductsid'] + "'>" + parseFloat(total).toFixed(2) + "</td>";
                                htmldata += "</tr>";

                                htmldata += "<tr>";
                                if (gstprice == 1) {
                                    if (parseFloat(discount) > 0) {
                                        htmldata += "<td class='text-right disccol' id='discountamount" + orderproducts[i]['orderproductsid'] + "'>" + parseFloat(discountamount).toFixed(2) + "</td>";
                                    } else {
                                        htmldata += "<td class='text-right disccol'>-</td>";
                                    }
                                    if (orderproducts[i]['igst'] == 1) {
                                        htmldata += "<td class='text-right sgstcol' id='sgst" + orderproducts[i]['orderproductsid'] + "'>" + parseFloat((taxvalue / 2)).toFixed(2) + "</td>";
                                        htmldata += "<td class='text-right cgstcol' id='cgst" + orderproducts[i]['orderproductsid'] + "'>" + parseFloat((taxvalue / 2)).toFixed(2) + "</td>";
                                    } else {
                                        htmldata += "<td class='text-right igstcol' id='igst" + orderproducts[i]['orderproductsid'] + "'>" + parseFloat(taxvalue).toFixed(2) + "</td>";
                                    }
                                } else {
                                    if (orderproducts[i]['igst'] == 1) {
                                        htmldata += "<td class='text-right sgstcol' id='sgst" + orderproducts[i]['orderproductsid'] + "'>" + parseFloat((taxvalue / 2)).toFixed(2) + "</td>";
                                        htmldata += "<td class='text-right cgstcol' id='cgst" + orderproducts[i]['orderproductsid'] + "'>" + parseFloat((taxvalue / 2)).toFixed(2) + "</td>";
                                    } else {
                                        htmldata += "<td class='text-right igstcol' id='igst" + orderproducts[i]['orderproductsid'] + "'>" + parseFloat(taxvalue).toFixed(2) + "</td>";
                                    }
                                    if (parseFloat(discount) > 0) {
                                        htmldata += "<td class='text-right disccol' id='discountamount" + orderproducts[i]['orderproductsid'] + "'>" + parseFloat(discountamount).toFixed(2) + "</td>";
                                    } else {
                                        htmldata += "<td class='text-right disccol'>-</td>";
                                    }
                                }
                                htmldata += "</tr>";
                            }
                        } else {
                            $("#invoiceproducttable tbody").html("<tr><td colspan='9' class='text-center'>No data available in table.</td></tr>");
                        }
                    }

                    $("#invoiceproducttable thead").html(headerdata);
                    $("#invoiceproducttable tbody").html(htmldata);
                    if (discolumn > 0) {
                        $('.disccol').show();
                    } else {
                        $('.disccol').hide();
                    }
                    if (gstcolumn.includes("1")) {
                        $('.igstcol').hide();
                        $('.cgstcol,.sgstcol').show();
                    } else {
                        $('.igstcol').show();
                        $('.cgstcol,.sgstcol').hide();
                    }
                    $(".qty").TouchSpin(touchspinoptions);
                    var html = extrachargespanel = '';
                    if (orderamountdata != null && orderamountdata != "") {
                        if (orderamountdata.length > 0) {
                            var orderextracharge = [];
                            var orderidArr = [];
                            for (var i = 0; i < orderamountdata.length; i++) {
                                var redeemamountrows = extrachargesrows = extrachargeshtml = '';
                                var orderid = orderamountdata[i]['id'];
                                if (orderamountdata[i]['redeempoints'] > 0) {
                                    redeemamountrows = '<tr>\
                                            <td>Redeem Amount (' + orderamountdata[i]['redeempoints'] + '*' + orderamountdata[i]['redeemrate'] + ')</td>\
                                            <th> : </th>\
                                            <td class="text-right">' + parseFloat(orderamountdata[i]['redeemamount']).toFixed(2) + '</td>\
                                          </tr>';
                                }
                                var extracharges = orderamountdata[i]['extracharges'];
                                var totalextracharges = 0;
                                var extracharge = [];
                                if (extracharges.length > 0) {
                                    for (var j = 0; j < extracharges.length; j++) {
                                        extrachargesrows += '<tr>\
                                              <td>' + extracharges[j]['extrachargesname'] + '</td>\
                                              <th> : </th>\
                                              <td class="text-right">' + parseFloat(extracharges[j]['amount']).toFixed(2) + '</td>\
                                            </tr>';

                                        totalextracharges += parseFloat(extracharges[j]['amount']);

                                        extracharge.push(extracharges[j]['extrachargesid']);

                                        extrachargeshtml += '<div class="col-md-6 p-n countcharges' + orderid + '" id="countcharges_' + orderid + '_' + (j + 1) + '">\
                                              <div class="col-sm-7 pr-xs">\
                                                  <div class="form-group" id="extracharges_' + orderid + '_' + (j + 1) + '_div">\
                                                      <div class="col-sm-12">\
                                                          <select id="orderextrachargesid_' + orderid + '_' + (j + 1) + '" name="orderextrachargesid[' + orderid + '][]" class="selectpicker form-control orderextrachargesid" data-live-search="true" data-select-on-tab="true" data-size="5" data-live-search="true">\
                                                            <option value="0">Select Extra Charges</option>\
                                                            ' + extrachargeoptionhtml + '\
                                                          </select>\
                                                          <input type="hidden" name="transactionextrachargesid[' + orderid + '][]" id="transactionextrachargesid_' + orderid + '_' + (j + 1) + '" class="transactionextrachargesid" value="' + extracharges[j]['transactionextrachargesid'] + '">\
                                                          <input type="hidden" name="orderextrachargesmappingid[' + orderid + '][]" id="orderextrachargesmappingid_' + orderid + '_' + (j + 1) + '" class="orderextrachargesmappingid" value="' + extracharges[j]['id'] + '">\
                                                          <input type="hidden" name="orderextrachargestax[' + orderid + '][]" id="orderextrachargestax_' + orderid + '_' + (j + 1) + '" class="orderextrachargestax" value="' + extracharges[j]['taxamount'] + '">\
                                                          <input type="hidden" name="orderextrachargesname[' + orderid + '][]" id="orderextrachargesname_' + orderid + '_' + (j + 1) + '" class="orderextrachargesname" value="' + extracharges[j]['extrachargesname'] + '">\
                                                          <input type="hidden" name="orderextrachargepercentage[' + orderid + '][]" id="orderextrachargepercentage_' + orderid + '_' + (j + 1) + '" class="orderextrachargepercentage" value="' + extracharges[j]['extrachargepercentage'] + '">\
                                                      </div>\
                                                  </div>\
                                              </div>\
                                              <div class="col-sm-3 pl-xs pr-xs">\
                                                  <div class="form-group p-n" id="orderextrachargeamount_' + orderid + '_' + (j + 1) + '_div">\
                                                      <div class="col-sm-12">\
                                                          <input type="text" id="orderextrachargeamount_' + orderid + '_' + (j + 1) + '" name="orderextrachargeamount[' + orderid + '][]" class="form-control text-right orderextrachargeamount" placeholder="Charge" onkeypress="return decimal_number_validation(event, this.value)" value="' + parseFloat(extracharges[j]['amount']).toFixed(2) + '">\
                                                      </div>\
                                                  </div>\
                                              </div>\
                                              <div class="col-md-2 text-right pt-md">\
                                                <button type="button" class="btn btn-default btn-raised remove_charges_btn" onclick="removecharge(' + orderid + ',' + (j + 1) + ')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>\
                                              </div>\
                                          </div>';
                                    }
                                }
                                orderextracharge[String(orderid)] = extracharge;

                                var orderdiscountpercent = (parseFloat(orderamountdata[i]['discountamount']) * 100 / (parseFloat(orderamountdata[i]['orderamount']) + parseFloat(orderamountdata[i]['taxamount'])));
                                var discount_text = redeem_text = coupon_text = coupon_label = '';
                                if (parseFloat(orderdiscountpercent) > 0) {
                                    discount_text += '<div class="col-md-2 pr-sm">\
                                          <div class="form-group p-n text-right" id="orderdiscountpercent' + orderid + '_div">\
                                            <div class="col-sm-12">\
                                              <label class="control-label" for="orderdiscountpercent' + orderid + '">Discount (%)</label>\
                                              <input type="text" id="orderdiscountpercent' + orderid + '" name="orderdiscountpercent[' + orderid + ']" class="form-control text-right orderdiscountpercent" placeholder="" onkeypress="return decimal_number_validation(event, this.value)" value="' + parseFloat(orderdiscountpercent).toFixed(2) + '">\
                                            </div>\
                                          </div>\
                                        </div>\
                                        <div class="col-md-3 pl-sm pr-sm">\
                                          <div class="form-group p-n text-right" id="orderdiscountamount' + orderid + '_div">\
                                            <div class="col-sm-12">\
                                              <label class="control-label" for="orderdiscountamount' + orderid + '">Discount Amount</label>\
                                              <input type="text" id="orderdiscountamount' + orderid + '" name="orderdiscountamount[' + orderid + ']" class="form-control text-right orderdiscountamount" placeholder="" onkeypress="return decimal_number_validation(event, this.value)" value="' + parseFloat(orderamountdata[i]['discountamount']).toFixed(2) + '">\
                                              <label class="control-label p-n m-n mb-xs">Max : ' + CURRENCY_CODE + ' <span id="applymaxdisc' + orderid + '"></span></label>\
                                              <input type="hidden" id="invoicediscamnt' + orderid + '" value="' + orderamountdata[i]['discountamount'] + '">\
                                              <input type="hidden" id="orderdiscamnt' + orderid + '" value="' + orderamountdata[i]['discountamount'] + '">\
                                            </div>\
                                          </div>\
                                        </div>';
                                }
                                if (orderamountdata[i]['redeempoints'] > 0) {
                                    redeem_text = '<div class="col-md-3 pl-sm pr-sm">\
                                      <div class="form-group p-n text-right" id="orderredeempoint' + orderid + '_div">\
                                        <div class="col-sm-12">\
                                          <label class="control-label" for="orderredeempoint' + orderid + '">Redeem Point (Rate:' + orderamountdata[i]['redeemrate'] + ')</label>\
                                          <input type="text" id="orderredeempoint' + orderid + '" name="orderredeempoint[' + orderid + ']" class="form-control text-right orderredeempoint" placeholder="" onkeypress="return isNumber(event)" value="' + orderamountdata[i]['redeempoints'] + '" maxlength="4">\
                                          <label class="control-label p-n m-n mb-xs">Max : <span id="applymaxrp' + orderid + '"></span></label>\
                                          <input type="hidden" id="redeempoint' + orderid + '" value="' + orderamountdata[i]['redeempoints'] + '">\
                                          <input type="hidden" id="invoiceredeempoint_' + orderid + '" value="' + orderamountdata[i]['redeempoints'] + '">\
                                          <input type="hidden" id="redeemrate' + orderid + '" name="redeemrate[' + orderid + ']" value="' + orderamountdata[i]['redeemrate'] + '">\
                                          <input type="hidden" id="redeemamount' + orderid + '" name="redeemamount[' + orderid + ']" value="' + orderamountdata[i]['redeemamount'] + '">\
                                        </div>\
                                      </div>\
                                    </div>';
                                }
                                if (orderamountdata[i]['couponcode'] != "" && orderamountdata[i]['couponamount'] > 0) {
                                    coupon_text = '<div class="col-md-3 pl-sm pr-sm">\
                                      <div class="form-group p-n text-right" id="ordercouponamount' + orderid + '_div">\
                                        <div class="col-sm-12">\
                                          <label class="control-label" for="ordercouponamount' + orderid + '">Coupon Amount</label>\
                                          <input type="text" id="ordercouponamount' + orderid + '" name="ordercouponamount[' + orderid + ']" class="form-control text-right ordercouponamount" placeholder="" onkeypress="return decimal_number_validation(event, this.value)" value="' + orderamountdata[i]['couponamount'] + '">\
                                          <label class="control-label p-n m-n mb-xs">Max : ' + CURRENCY_CODE + ' <span id="applymaxca' + orderid + '"></span></label>\
                                          <input type="hidden" id="couponamount' + orderid + '" value="' + orderamountdata[i]['couponamount'] + '">\
                                          <input type="hidden" id="invoicecouponamount_' + orderid + '" value="' + orderamountdata[i]['couponamount'] + '">\
                                        </div>\
                                      </div>\
                                    </div>';

                                    coupon_label = '<h2><b>Coupon Applied : </b>' + orderamountdata[i]['couponcode'] + '</h2>';
                                }
                                if (extrachargeshtml != "" || discount_text != "" || redeem_text != "" || coupon_text != "") {

                                    var ordergrossamount = parseFloat(orderamountdata[i]['orderamount']) + parseFloat(orderamountdata[i]['taxamount']);

                                    //ordergrossamount = (parseInt($("#quantity"+orderid).val()) * parseFloat(ordergrossamount) / parseInt($("#orderquantity"+orderid).val()));

                                    extrachargespanel += '<div class="panel countorders" id="' + orderid + '">\
                                              <div class="panel-heading">\
                                                <h2 style="width: 35%;"><b>OrderID :</b> ' + orderamountdata[i]['ordernumber'] + '</h2>\
                                                <h2 style="width: 33%;"><b>Product Total : </b><span id="displayproducttotal' + orderid + '">0.00</span></h2>\
                                                ' + coupon_label + '\
                                              </div>\
                                              <div class="panel-body no-padding">\
                                                <div class="row m-n">\
                                                ' + extrachargeshtml + '\
                                                </div>\
                                              <input type="hidden" name="ordergrossamount[]" id="ordergrossamount_' + orderid + '" class="ordergrossamount" value="' + parseFloat(ordergrossamount).toFixed(2) + '">\
                                              <input type="hidden" name="invoiceorderamount[]" id="invoiceorderamount_' + orderid + '" class="invoiceorderamount" value="' + parseFloat(ordergrossamount).toFixed(2) + '">\
                                                <div class="row m-n">\
                                                  ' + discount_text + '\
                                                  ' + redeem_text + '\
                                                  ' + coupon_text + '\
                                                </div>\
                                              </div>\
                                            </div>\
                                          </div>';
                                }

                                var doscountrows = couponrows = '';
                                if (parseFloat(orderamountdata[i]['discountamount']) > 0) {
                                    doscountrows = '<tr>\
                                        <td>Discount Amount</td>\
                                        <th> : </th>\
                                        <td class="text-right">' + parseFloat(orderamountdata[i]['discountamount']).toFixed(2) + '</td>\
                                      </tr>';
                                }
                                if (parseFloat(orderamountdata[i]['couponamount']) > 0) {
                                    couponrows = '<tr>\
                                      <td>Coupon Amount</td>\
                                      <th> : </th>\
                                      <td class="text-right">' + parseFloat(orderamountdata[i]['couponamount']).toFixed(2) + '</td>\
                                    </tr>';
                                }
                                var netamount = (parseFloat(orderamountdata[i]['netamount']) - parseFloat(orderamountdata[i]['discountamount']) - parseFloat(orderamountdata[i]['couponamount']) - orderamountdata[i]['redeemamount'] + parseFloat(totalextracharges));
                                if (parseFloat(netamount) < 0) {
                                    netamount = 0;
                                }
                                html += '<div class="col-sm-4 pl-sm pr-sm" style="margin-bottom:10px;min-height: 200px;">\
                              <table class="table m-n orderamounttable" style="border: 5px solid #e8e8e8;">\
                                <tr>\
                                  <th>Order No.</th>\
                                  <th> : </th>\
                                  <td><a href="' + SITE_URL + 'order/view-order/' + orderamountdata[i]['id'] + '" target="_blank">' + orderamountdata[i]['ordernumber'] + '</a></td>\
                                </tr>\
                                <tr style="border-bottom: 2px solid #E8E8E8;">\
                                  <th>Order Date</th>\
                                  <th> : </th>\
                                  <td>' + orderamountdata[i]['orderdate'] + '</td>\
                                </tr>\
                                <tr>\
                                  <td>Order Amount</td>\
                                  <th> : </th>\
                                  <td class="text-right">' + parseFloat(orderamountdata[i]['orderamount']).toFixed(2) + '</td>\
                                </tr>\
                                <tr>\
                                  <td>Tax Amount</td>\
                                  <th> : </th>\
                                  <td class="text-right">' + parseFloat(orderamountdata[i]['taxamount']).toFixed(2) + '</td>\
                                </tr>\
                                ' + doscountrows + '\
                                ' + couponrows + '\
                                ' + redeemamountrows + '\
                                ' + extrachargesrows + '\
                                <tr>\
                                  <th>Net Amount</th>\
                                  <th> : </th>\
                                  <th class="text-right">' + format.format(netamount) + '</th>\
                                </tr>\
                              </table>\
                            </div>';
                            }

                            $('#extracharges_div').html(extrachargespanel);
                            $('.orderextrachargesid').selectpicker("refresh");
                            $('#orderamountdiv').html(html);

                            if (orderamountdata.length > 0) {
                                for (var k = 0; k < orderamountdata.length; k++) {
                                    var OrderID = orderamountdata[k]['id'];

                                    for (var l = 0; l < orderextracharge[OrderID].length; l++) {

                                        var extrachargesid = orderextracharge[OrderID][l];
                                        $("#orderextrachargesid_" + OrderID + "_" + (l + 1)).val(extrachargesid);
                                        $("#orderextrachargesid_" + OrderID + "_" + (l + 1)).selectpicker('refresh');
                                        $("#orderextrachargesid_" + OrderID + "_" + (l + 1) + " option:not(:selected)").remove();
                                        $("#orderextrachargesid_" + OrderID + "_" + (l + 1)).selectpicker('refresh');
                                        changechargespercentage(orderid, (l+1));
                                    }
                                    // calculateorderamount(OrderID);
                                }
                            }
                            /****EXTRA CHARGE CHANGE EVENT****/
                            $('body').on('change', 'select.orderextrachargesid', function () {
                                var rowid = $(this).attr("id").split('_');
                                var orderid = rowid[1];
                                var divid = rowid[2];
                                calculateextracharges(orderid, divid);
                                changechargespercentage(orderid, divid);
                                overallextracharges();
                                netamounttotal();
                            });
                            $('body').on('keyup', '.orderextrachargeamount', function () {
                                var rowid = $(this).attr("id").split('_');
                                var orderid = rowid[1];
                                var divid = rowid[2];

                                var grossamount = $("#invoiceorderamount_" + orderid).val();
                                var inputgrossamount = $("#inputgrossamount").val();

                                if (orderid == 0) {
                                    grossamount = parseFloat(inputgrossamount);
                                }

                                var chargestaxamount = chargespercent = 0;
                                var tax = $("#orderextrachargesid_" + orderid + "_" + divid + " option:selected").attr("data-tax");
                                var type = $("#orderextrachargesid_" + orderid + "_" + divid + " option:selected").attr("data-type");

                                if (this.value != '') {
                                    if (parseFloat(this.value) > parseFloat(grossamount)) {
                                        $(this).val(parseFloat(grossamount).toFixed(2));
                                    }
                                    if (tax > 0) {
                                        chargestaxamount = parseFloat(this.value) * parseFloat(tax) / (100 + parseFloat(tax));
                                    }
                                    if (type == 0) {
                                        chargespercent = parseFloat(this.value) * 100 / parseFloat(grossamount);
                                    }
                                }
                                $("#orderextrachargestax_" + orderid + "_" + divid).val(parseFloat(chargestaxamount).toFixed(2));
                                $("#orderextrachargepercentage_" + orderid + "_" + divid).val(parseFloat(chargespercent).toFixed(2));
                                changechargespercentage(orderid, divid);
                                overallextracharges();
                                netamounttotal();
                            });
                            $('.orderdiscountpercent').on('keyup', function () {
                                var orderid = $(this).attr("id").match(/\d+/);
                                var discountpercentage = $(this).val();
                                var invoicediscamnt = $("#invoicediscamnt" + orderid).val();
                                var inputgrossamount = $("#inputgrossamount").val();

                                var grossamount = parseFloat(invoicediscamnt);
                                if (orderid == 0) {
                                    grossamount = parseFloat(inputgrossamount);
                                }

                                if (discountpercentage != undefined && discountpercentage != '') {
                                    if (parseFloat(discountpercentage) > 100) {
                                        $(this).val("100");
                                        discountpercentage = 100;
                                    }

                                    if (grossamount != '') {
                                        var discountamount = (parseFloat(grossamount) * parseFloat(discountpercentage) / 100);

                                        $("#orderdiscountamount" + orderid).val(parseFloat(discountamount).toFixed(2));

                                        $("#ovdiscper").html(parseFloat(discountpercentage).toFixed(2));
                                        $("#ovdiscamnt").html(parseFloat(discountamount).toFixed(2));
                                        $("#inputovdiscamnt").val(parseFloat(discountamount).toFixed(2));

                                        var discount = 0;
                                        $('.orderdiscountamount').each(function (index) {
                                            var id = $(this).attr("id").match(/\d+/);
                                            if (id != "") {
                                                discount += parseFloat($(this).val());
                                            }
                                        });
                                        if (orderid == 0) {
                                            if (parseFloat(discount) > parseFloat(grossamount)) {
                                                new PNotify({
                                                    title: "Discount amount apply less than product total amount !",
                                                    styling: 'fontawesome',
                                                    delay: '3000',
                                                    type: 'error'
                                                });
                                                $(this).val('');
                                                $("#orderdiscountamount0").val('');
                                            }
                                        }
                                        overallextracharges();
                                        netamounttotal();
                                    }
                                } else {
                                    $(this).val('');
                                    $("#orderdiscountamount" + orderid).val('');
                                    $("#ovdiscper").html("0");
                                    $("#ovdiscamnt").html("0.00");
                                    $("#inputovdiscamnt").val('0.00');
                                    overallextracharges();
                                    netamounttotal();
                                }
                            });
                            $('.orderdiscountamount').on('keyup', function () {
                                var orderid = $(this).attr("id").match(/\d+/);
                                var discountamount = $(this).val();
                                var discountpercentage = $("#ovdiscper").html();
                                var invoicediscamnt = $("#invoicediscamnt" + orderid).val();
                                var inputgrossamount = $("#inputgrossamount").val();

                                var grossamount = parseFloat(invoicediscamnt);
                                if (orderid == 0) {
                                    grossamount = parseFloat(inputgrossamount);
                                }

                                if (discountamount != undefined && discountamount != '') {
                                    if (orderid != 0) {
                                        if (parseFloat(discountamount) > parseFloat(grossamount)) {
                                            grossamount = (parseFloat(grossamount) > 0) ? parseFloat(grossamount) : 0;
                                            $(this).val(parseFloat(grossamount));
                                            discountamount = parseFloat(grossamount);
                                        }
                                    }
                                    if (parseFloat(grossamount) != '') {
                                        var discountpercentage = ((parseFloat(discountamount) * 100) / parseFloat(grossamount));
                                        if (parseFloat(discountpercentage) == 0) {
                                            $("#orderdiscountpercent" + orderid).val(0);
                                        } else {
                                            $("#orderdiscountpercent" + orderid).val(parseFloat(discountpercentage).toFixed(2));
                                        }

                                        $("#ovdiscper").html(parseFloat(discountpercentage).toFixed(2));
                                        $("#ovdiscamnt").html(parseFloat(discountamount).toFixed(2));
                                        $("#inputovdiscamnt").val(parseFloat(discountamount).toFixed(2));
                                        if (parseFloat(discountpercentage) > 100) {
                                            $("#orderdiscountpercent" + orderid).val("100");
                                        }
                                        var discount = 0;
                                        $('.orderdiscountamount').each(function (index) {
                                            var id = $(this).attr("id").match(/\d+/);
                                            if (id != "") {
                                                discount += parseFloat($(this).val());
                                            }
                                        });
                                        if (orderid == 0) {
                                            if (parseFloat(discount) > parseFloat(grossamount)) {
                                                new PNotify({
                                                    title: "Discount amount apply less than product total amount !",
                                                    styling: 'fontawesome',
                                                    delay: '3000',
                                                    type: 'error'
                                                });
                                                $(this).val('');
                                                $("#orderdiscountpercent0").val('');
                                            }
                                        }
                                        overallextracharges();
                                        netamounttotal();
                                    }
                                } else {
                                    $(this).val('');
                                    $("#orderdiscountpercent" + orderid).val('');
                                    $("#ovdiscper").html("0");
                                    $("#ovdiscamnt").html("0.00");
                                    $("#inputovdiscamnt").val('0.00');
                                    overallextracharges();
                                    netamounttotal();
                                }
                            });
                            $('.orderredeempoint').on('keyup', function () {
                                var orderid = $(this).attr("id").match(/\d+/);
                                var redeemrate = $("#redeemrate" + orderid).val();
                                var inputgrossamount = $("#inputgrossamount").val();

                                if (this.value != '') {
                                    if (parseInt(this.value) > parseInt($("#invoiceredeempoint_" + orderid).val())) {
                                        $(this).val($("#invoiceredeempoint_" + orderid).val());
                                    }
                                    $("#redeemamount" + orderid).val(parseFloat(parseInt(this.value) * parseInt(redeemrate)).toFixed(2));
                                } else {
                                    $("#redeemamount" + orderid).val(parseFloat(0).toFixed(2));
                                }
                                overallextracharges();
                                netamounttotal();
                            });
                            $('.ordercouponamount').on('keyup', function () {
                                var orderid = $(this).attr("id").match(/\d+/);
                                var invoicecouponamount = $("#invoicecouponamount_" + orderid).val();

                                if (this.value != '') {

                                    if (parseInt(this.value) > parseInt(invoicecouponamount)) {
                                        $(this).val($("#invoicecouponamount_" + orderid).val());
                                    }
                                }
                                overallextracharges();
                                netamounttotal();
                            });
                        }
                    } else {
                        $('#orderamountdiv').html("");
                        $('#extracharges_div').html("");
                    }

                    overallextracharges();
                    netamounttotal();
                }
            },
            error: function (xhr) {
                //alert(xhr.responseText);
            },
            complete: function () {
                $('.mask').hide();
                $('#loader').hide();
            },
        });
    } else {
        $("#invoiceproducttable tbody").html("<tr><td colspan='9' class='text-center'>No data available in table.</td></tr>");
        $('#orderamountdiv').html("");
        $('#extracharges_div').html("");
        $('#billingaddress').val('');
        $('#shippingaddress').val('');
    }

}

function calculateorderamount(OrderID) {
    var orderqty = invoiceqty = 0;
    $(".orderquantity" + OrderID).each(function (index) {
        var orderproductid = $(this).attr("id").match(/(\d+)/g);
        if ($(this).val() != "") {
            orderqty += parseFloat($(this).val());
        }
        if ($("#quantity" + orderproductid).val() != "") {
            invoiceqty += parseFloat($("#quantity" + orderproductid).val());
        }
    });
    var ordergrossamount = (parseFloat(invoiceqty) * parseFloat($("#ordergrossamount_" + OrderID).val()) / parseFloat(orderqty));
    $("#invoiceorderamount_" + OrderID).val(parseFloat(ordergrossamount).toFixed(2));
    $("#displayproducttotal" + OrderID).html(parseFloat(ordergrossamount).toFixed(2));
    changeextrachargesamount();

    var invoiceredeempoint = (parseFloat(invoiceqty) * parseFloat($("#redeempoint" + OrderID).val()) / parseFloat(orderqty));
    $("#invoiceredeempoint_" + OrderID).val(parseInt(invoiceredeempoint));
    $("#orderredeempoint" + OrderID).val(parseInt(invoiceredeempoint));
    $("#redeemamount" + OrderID).val(parseFloat(parseInt(invoiceredeempoint) * parseInt($("#redeemrate" + OrderID).val())).toFixed(2));
    $("#applymaxrp" + OrderID).html(parseInt(invoiceredeempoint));

    var invoicediscamnt = (parseFloat(invoiceqty) * parseFloat($("#orderdiscamnt" + OrderID).val()) / parseFloat(orderqty));
    var invoicediscper = (parseFloat(invoicediscamnt) * 100 / parseFloat(invoicediscamnt));
    $("#orderdiscountamount" + OrderID).val(parseFloat(invoicediscamnt).toFixed(2));
    $("#invoicediscamnt" + OrderID).val(parseFloat(invoicediscamnt).toFixed(2));
    $("#applymaxdisc" + OrderID).html(parseFloat(invoicediscamnt).toFixed(2));
    $("#orderdiscountpercent" + OrderID).val(parseFloat(invoicediscper).toFixed(2));

    var invoicecouponamnt = (parseFloat(invoiceqty) * parseFloat($("#couponamount" + OrderID).val()) / parseFloat(orderqty));
    $("#ordercouponamount" + OrderID).val(parseFloat(invoicecouponamnt).toFixed(2));
    $("#invoicecouponamount_" + OrderID).val(parseFloat(invoicecouponamnt).toFixed(2));
    $("#applymaxca" + OrderID).html(parseFloat(invoicecouponamnt).toFixed(2));

    var producttotal = productgstamount = 0;
    $(".producttotal").each(function (index) {
        var divid = $(this).attr("id").match(/(\d+)/g);
        if ($(this).val() != "" && $("#quantity" + divid).val() > 0) {
            producttotal += parseFloat($(this).val());
        }
    });
    $(".taxvalue").each(function (index) {
        var divid = $(this).attr("id").match(/(\d+)/g);
        if ($(this).val() != "" && $("#quantity" + divid).val() > 0) {
            productgstamount += parseFloat($(this).val());
        }
    });
    var grossamount = parseFloat(producttotal) + parseFloat(productgstamount);
    $("#applymaxdisc0").html(parseFloat(grossamount).toFixed(2));
    var discamnt = $("#orderdiscountamount0").val();
    if (discamnt != '') {
        if (parseFloat(discamnt) > parseFloat(grossamount)) {
            $("#orderdiscountamount0").val(parseFloat(grossamount).toFixed(2));
            $("#orderdiscountpercent0").val(parseFloat(100).toFixed(2));
        } else {
            var invoicediscper = (parseFloat(discamnt) * 100 / parseFloat(grossamount));

            $("#orderdiscountpercent0").val(parseFloat(invoicediscper).toFixed(2));
        }

    }
}

function changechargespercentage(orderid, divid) {
    // alert(divid)
    var type = $("#orderextrachargesid_" + orderid + "_" + divid + " option:selected").attr("data-type");
    var optiontext = $("#orderextrachargesid_" + orderid + "_" + divid + " option:selected").text();
    var grossamount = $("#invoiceorderamount_" + orderid).val();
    var amount = $("#orderextrachargeamount_" + orderid + "_" + divid).val();
    var chargespercent = 0;
    var inputgrossamount = $("#inputgrossamount").val();

    if (orderid == 0) {
        grossamount = parseFloat(inputgrossamount);
    }
    if (type == 0) {
        if (parseFloat(amount) > 0) {
            chargespercent = parseFloat(amount) * 100 / parseFloat(grossamount);
        }
        optiontext = optiontext.split("(");
        $("#orderextrachargesid_" + orderid + "_" + divid + " option:selected").text(optiontext[0].trim() + " (" + parseFloat(chargespercent).toFixed(2) + "%)");
        $("#orderextrachargesid_" + orderid + "_" + divid).selectpicker("refresh");
        $("#orderextrachargesname_" + orderid + "_" + divid).val(optiontext[0].trim() + " (" + parseFloat(chargespercent).toFixed(2) + "%)");
    }
}

function totalproductamount(orderid, divid) {
    var quantity = $("#quantity" + divid).val();
    var taxtype = $("#taxtype" + divid).val();
    var tax = $("#tax" + divid).val();
    var price = $("#price" + divid).val();
    var actualprice = $("#actualprice" + divid).val();
    var discount = $("#discount" + divid).val();

    var discountamount = ((parseFloat(actualprice) * parseFloat(quantity)) * parseFloat(discount) / 100);
    var totalprice = (parseFloat(price) * parseFloat(quantity));
    var taxvalue = parseFloat(parseFloat(price) * parseFloat(quantity) * parseFloat(tax) / 100);
    var total = parseFloat(totalprice) + parseFloat(taxvalue);

    if (taxtype == 1) {
        $("#sgst" + divid).html(parseFloat(taxvalue / 2).toFixed(2));
        $("#cgst" + divid).html(parseFloat(taxvalue / 2).toFixed(2));
    } else {
        $("#igst" + divid).html(parseFloat(taxvalue).toFixed(2));
    }
    $("#discountamount" + divid).html(parseFloat(discountamount).toFixed(2));
    $("#productnetprice" + divid).html(parseFloat(total).toFixed(2));
    $("#taxvalue" + divid).val(parseFloat(taxvalue).toFixed(2));
    $("#producttotal" + divid).val(parseFloat(parseFloat(totalprice)).toFixed(2));
    calculateorderamount(orderid);
    changeextrachargesamount();
    overallextracharges();
    netamounttotal();
}

function changeextrachargesamount() {

    $(".orderextrachargeamount").each(function (index) {
        var element = $(this).attr("id").split('_');
        var orderid = element[1];
        var divid = element[2];
        calculateextracharges(orderid, divid);
    });
}

function calculateextracharges(orderid, rowid) {
    var extracharges = $("#orderextrachargesid_" + orderid + "_" + rowid).val();
    var type = $("#orderextrachargesid_" + orderid + "_" + rowid + " option:selected").attr("data-type");
    var amount = $("#orderextrachargesid_" + orderid + "_" + rowid + " option:selected").attr("data-amount");
    var tax = $("#orderextrachargesid_" + orderid + "_" + rowid + " option:selected").attr("data-tax");


    var totalgrossamount = $("#invoiceorderamount_" + orderid).val();
    var inputgrossamount = $("#inputgrossamount").val();
    // alert(inputgrossamount)

    if (orderid == 0) {
        totalgrossamount = parseFloat(inputgrossamount);
    }
    /* var discount = $("#discountamount").html();
    var couponamount = $("#coupondiscountamount").html(); */

    var chargesamount = chargestaxamount = 0;
    // alert(totalgrossamount)
    if (parseFloat(totalgrossamount) > 0 && parseFloat(extracharges) > 0) {
        if (type == 0) {
            chargesamount = parseFloat(totalgrossamount) * parseFloat(amount) / 100;
        } else {
            chargesamount = parseFloat(amount);
        }

        chargestaxamount = parseFloat(chargesamount) * parseFloat(tax) / (100 + parseFloat(tax));

        $("#orderextrachargestax_" + orderid + "_" + rowid).val(parseFloat(chargestaxamount).toFixed(2));
        $("#orderextrachargeamount_" + orderid + "_" + rowid).val(parseFloat(chargesamount).toFixed(2));
    } else {
        $("#orderextrachargestax_" + orderid + "_" + rowid).val(parseFloat(0).toFixed(2));
        $("#orderextrachargeamount_" + orderid + "_" + rowid).val(parseFloat(0).toFixed(2));
    }
    var chargesname = $("#orderextrachargesid_" + orderid + "_" + rowid + " option:selected").text();
    $("#orderextrachargesname_" + orderid + "_" + rowid).val(chargesname.trim());
    var chargespercent = 0;
    if (type == 0) {
        chargespercent = parseFloat(amount);
    }
    $("#orderextrachargepercentage_" + orderid + "_" + rowid).val(parseFloat(chargespercent).toFixed(2));
    netamounttotal();
}

function overallextracharges() {

    /********* CALCULATE EXTRA CHARGES START *********/
    var extrachargesrow = '';
    var CHARGES_ARR = [];
    var extrachargesamnt = [];
    $(".tr_extracharges").remove();
    $("select.orderextrachargesid").each(function (index) {
        var element = $(this).attr("id").split('_');
        var orderid = element[1];
        var divid = element[2];
        var extrachargesname = $("#orderextrachargesname_" + orderid + "_" + divid).val();
        var extrachargeamount = $("#orderextrachargeamount_" + orderid + "_" + divid).val();
        var extrachargestax = $("#orderextrachargestax_" + orderid + "_" + divid).val();
        var extrachargepercentage = $("#orderextrachargepercentage_" + orderid + "_" + divid).val();
        var extrachargesdatatype = $("#orderextrachargesid_" + orderid + "_" + divid + " option:selected").attr("data-type");
        var extrachargesid = $(this).val();

        extrachargeamount = (parseFloat(extrachargeamount) > 0) ? parseFloat(extrachargeamount) : 0;

        if (extrachargesid != 0) {

            if (!CHARGES_ARR.includes(extrachargesid)) {

                extrachargesrow += "<tr class='tr_extracharges' id='tr_extracharges_" + extrachargesid + "'>";
                extrachargesrow += "<td>" + extrachargesname + "</td>";
                extrachargesrow += "<td class='text-right'><span id='extrachargeamount" + extrachargesid + "'>" + parseFloat(extrachargeamount).toFixed(2) + "</span>";

                extrachargesrow += '<input type="hidden" name="extrachargesid[]" id="extrachargesid' + extrachargesid + '" value="' + extrachargesid + '">';

                extrachargesrow += '<input type="hidden" name="extrachargeamount[]" id="inputextrachargeamount' + extrachargesid + '" value="' + parseFloat(extrachargeamount).toFixed(2) + '">';

                extrachargesrow += '<input type="hidden" id="extrachargestax' + extrachargesid + '" name="extrachargestax[]" value="' + parseFloat(extrachargestax).toFixed(2) + '">';

                extrachargesrow += '<input type="hidden" name="extrachargesname[]" id="extrachargesname' + extrachargesid + '" value="' + extrachargesname + '">';

                extrachargesrow += '<input type="hidden" name="extrachargepercentage[]" id="extrachargepercentage' + extrachargesid + '" value="' + parseFloat(extrachargepercentage).toFixed(2) + '">';

                extrachargesrow += '<input type="hidden" name="extrachargesdatatype[]" id="extrachargesdatatype' + extrachargesid + '" value="' + parseInt(extrachargesdatatype) + '">';

                extrachargesrow += "</td>";
                extrachargesrow += "</tr>";

                CHARGES_ARR.push(extrachargesid);

            } else {

                var sumamount = sumtax = type = 0;
                $("select.orderextrachargesid").each(function (index) {
                    var elementid = $(this).attr("id").split('_');
                    var OrderId = elementid[1];
                    var Id = elementid[2];
                    var thisid = $(this).val();
                    var sumchargeamount = $("#orderextrachargeamount_" + OrderId + "_" + Id).val();
                    var sumchargetax = $("#orderextrachargestax_" + OrderId + "_" + Id).val();
                    var thisid = $(this).val();
                    var thistype = $("#orderextrachargesid_" + OrderId + "_" + Id + " option:selected").attr("data-type");
                    sumchargeamount = (parseFloat(sumchargeamount) > 0) ? parseFloat(sumchargeamount) : 0;
                    sumchargetax = (parseFloat(sumchargetax) > 0) ? parseFloat(sumchargetax) : 0;

                    if (thisid == extrachargesid) {
                        sumamount += parseFloat(sumchargeamount);
                        sumtax += parseFloat(sumchargetax);
                        type = thistype;
                    }
                });
                extrachargesamnt.push(extrachargesid + '_' + parseFloat(sumamount).toFixed(2) + '_' + parseFloat(sumtax).toFixed(2) + '_' + type);
            }
        }
    });

    $("#redeempointrow").after(extrachargesrow);
    var inputgrossamount = $("#inputgrossamount").val();
    if (extrachargesamnt.length > 0) {
        for (var i = 0; i < extrachargesamnt.length; i++) {

            var id = extrachargesamnt[i].split('_');
            var chargesid = id[0];
            var amount = id[1];
            var tax = id[2];
            var type = id[3];
            var chargespercent = 0;
            if (type == 0) {
                if (parseFloat(amount) > 0) {
                    chargespercent = parseFloat(amount) * 100 / parseFloat(inputgrossamount);
                }
                var optiontext = $("#extrachargesname" + chargesid).val();

                optiontext = optiontext.split("(");
                optiontext = optiontext[0].trim() + " (" + parseFloat(chargespercent).toFixed(2) + "%)";
                $("#tr_extracharges_" + chargesid + " td:first").text(optiontext);
                $("#extrachargesname" + chargesid).val(optiontext);
            }

            $("#extrachargeamount" + chargesid).html(parseFloat(amount).toFixed(2));
            $("#inputextrachargeamount" + chargesid).val(parseFloat(amount).toFixed(2));
            $("#extrachargestax" + chargesid).val(parseFloat(tax).toFixed(2));
            $("#extrachargesdatatype" + chargesid).val(parseInt(type));
            $("#extrachargepercentage" + chargesid).val(parseFloat(chargespercent).toFixed(2));
        }
    }
    /********* CALCULATE EXTRA CHARGES END *********/

    /********* CHANGE DISCOUNT START *********/
    var discountamount = orderdiscountpercent = 0;
    $(".orderdiscountamount").each(function (index) {
        //var divid = $(this).attr("id").match(/(\d+)/g);
        //var percent = $("#orderdiscountpercent"+divid).val();
        if (this.value > 0) {
            discountamount += parseFloat(this.value);
            //orderdiscountpercent += parseFloat(percent);
        }
    });

    // netamounttotal();
    if (parseFloat(discountamount) > 0) {
        var grossamount = $("#inputgrossamount").val();
        $("#ovdiscper").html(parseFloat((parseFloat(discountamount) * 100 / parseFloat(grossamount))).toFixed(2));
        $("#ovdiscamnt").html(parseFloat(discountamount).toFixed(2));
        $("#inputovdiscamnt").val(parseFloat(discountamount).toFixed(2));
        $("#totaldiscounts").show();
    } else {
        $("#ovdiscamnt").html('0.00');
        $("#inputovdiscamnt").val('0.00');
        $("#totaldiscounts").hide();
    }
    /********* CHANGE DISCOUNT END *********/

    /********* CALCULATE REDEEM POINT START *********/
    var points = redeemamount = redeemrate = 0;
    $(".orderredeempoint").each(function (index) {
        var orderid = $(this).attr("id").match(/(\d+)/g);
        redeemrate = $("#redeemrate" + orderid).val();
        if (this.value > 0) {
            points += parseInt(this.value);
            redeemamount += (parseFloat(this.value) * parseFloat(redeemrate));
        }
    });
    if (parseFloat(redeemamount) > 0) {
        //$("#conversationrate").html(parseInt(points)+"*"+parseInt(redeemrate));
        $("#conversationrateamount").html(parseFloat(redeemamount).toFixed(2));
        $("#redeempointrow").show();
    } else {
        //$("#conversationrate").html('0');
        $("#conversationrateamount").html('0.00');
        $("#redeempointrow").hide();
    }
    /********* CALCULATE REDEEM POINT END *********/

    /********* CALCULATE COUPON START *********/
    var couponamount = 0;
    $(".ordercouponamount").each(function (index) {
        //var divid = $(this).attr("id").match(/(\d+)/g);
        //var percent = $("#orderdiscountpercent"+divid).val();
        if (this.value > 0) {
            couponamount += parseFloat(this.value);
            //orderdiscountpercent += parseFloat(percent);
        }
    });

    if (parseFloat(couponamount) > 0) {
        $("#couponamount").html(parseFloat(couponamount).toFixed(2));
        $("#inputcouponamount").val(parseFloat(couponamount).toFixed(2));
        $("#couponamountrow").show();
    } else {
        $("#couponamount").html('0.00');
        $("#inputcouponamount").val('0.00');
        $("#couponamountrow").hide();
    }
    /********* CALCULATE COUPON POINT END *********/
}

function netamounttotal() {
    var producttotal = productgstamount = grossamount = extrachargesamount = extrachargestax = chargesassesbaleamount = 0;

    $(".producttotal").each(function (index) {
        var divid = $(this).attr("id").match(/(\d+)/g);
        if ($(this).val() != "" && $("#quantity" + divid).val() > 0) {
            producttotal += parseFloat($(this).val());
        }
    });
    $(".taxvalue").each(function (index) {
        var divid = $(this).attr("id").match(/(\d+)/g);
        if ($(this).val() != "" && $("#quantity" + divid).val() > 0) {
            productgstamount += parseFloat($(this).val());
        }
    });
    $("#producttotal").html(parseFloat(producttotal).toFixed(2));
    $("#inputproducttotal").val(parseFloat(producttotal).toFixed(2));
    $("#gsttotal").html(parseFloat(productgstamount).toFixed(2));
    $("#inputgsttotal").val(parseFloat(productgstamount).toFixed(2));

    if ($("select.orderextrachargesid").length > 0) {
        $(".tr_extracharges").each(function (index) {
            if ($(this).attr("id") != "default") {
                var orderid = $(this).attr("id").match(/(\d+)/g);
                var exchrgamnt = $("#extrachargeamount" + orderid).html();
                var exchrgtax = $("#extrachargestax" + orderid).val();
                if (parseFloat(exchrgamnt) > 0) {
                    extrachargesamount += parseFloat(exchrgamnt);
                    extrachargestax += parseFloat(exchrgtax);
                }
            }
        });
    }
    chargesassesbaleamount = parseFloat(extrachargesamount) - parseFloat(extrachargestax);
    var producttotalassesbaleamount = parseFloat(producttotal) + parseFloat(chargesassesbaleamount);
    var producttotalgstamount = parseFloat(productgstamount) + parseFloat(extrachargestax);

    $("#chargestotalassesbaleamount").html(format.format(parseFloat(chargesassesbaleamount).toFixed(2)));
    $("#chargestotalgstamount").html(format.format(parseFloat(extrachargestax).toFixed(2)));
    $("#producttotalassesbaleamount").html(format.format(parseFloat(producttotalassesbaleamount).toFixed(2)));
    $("#producttotalgstamount").html(format.format(parseFloat(producttotalgstamount).toFixed(2)));

    grossamount = parseFloat(producttotal) + parseFloat(productgstamount);
    // alert(grossamount)
    $("#grossamount").html(parseFloat(grossamount).toFixed(2));
    $("#inputgrossamount").val(parseFloat(grossamount).toFixed(2));

    var discount = $("#ovdiscamnt").html();
    var reddemamount = $("#conversationrateamount").html();
    var couponamount = $("#inputcouponamount").val();
    var finalamount = parseFloat(grossamount) - parseFloat(discount) - parseFloat(couponamount) - parseFloat(reddemamount) + parseFloat(extrachargesamount);

    if (finalamount < 0) {
        finalamount = 0;
    }
    var roundoff = Math.round(parseFloat(finalamount).toFixed(2)) - parseFloat(finalamount);
    finalamount = Math.round(parseFloat(finalamount).toFixed(2));
    $("#roundoff").html(format.format(roundoff));
    $("#totalpayableamount").html(format.format(finalamount));
    $("#inputtotalpayableamount").val(parseFloat(finalamount).toFixed(2));

    calculatecashbackdiscount();
}

function printInvoice(id) {

    var uurl = SITE_URL + "invoice/printInvoice";
    $.ajax({
        url: uurl,
        type: 'POST',
        data: {
            id: id
        },
        //dataType: 'json',
        async: false,
        beforeSend: function () {
            $('.mask').show();
            $('#loader').show();
        },
        success: function (response) {

            var data = JSON.parse(response);
            var html = data['content'];

            var frame1 = document.createElement("iframe");
            frame1.name = "frame1";
            frame1.style.position = "absolute";
            frame1.style.top = "-1000000px";
            document.body.appendChild(frame1);
            var frameDoc = frame1.contentWindow ? frame1.contentWindow : frame1.contentDocument.document ? frame1.contentDocument.document : frame1.contentDocument;
            frameDoc.document.open();
            frameDoc.document.write(html);
            frameDoc.document.close();
            setTimeout(function () {
                window.frames["frame1"].focus();
                window.frames["frame1"].print();
                document.body.removeChild(frame1);
            }, 500);
        },
        error: function (xhr) {
            //alert(xhr.responseText);
        },
        complete: function () {
            $('.mask').hide();
            $('#loader').hide();
        },
    });
}

function addnewcharge() {

    var lastid = $(".countcharges0:last").attr("id").split("_");
    var rowcount = parseInt(lastid[2]) + 1;
    var datahtml = ' <div class="col-md-6 p-n countcharges0" id="countcharges_0_' + rowcount + '">\
                      <div class="col-sm-6 pr-xs">\
                          <div class="form-group p-n" id="extracharges_0_' + rowcount + '_div">\
                              <div class="col-sm-12">\
                                  <select id="orderextrachargesid_0_' + rowcount + '" name="orderextrachargesid[0][]" class="selectpicker form-control orderextrachargesid" data-live-search="true" data-select-on-tab="true" data-size="5">\
                                      <option value="0">Select Extra Charges</option>\
                                          ' + extrachargeoptionhtml + '\
                                  </select>\
                                  <input type="hidden" name="orderextrachargesmappingid[0][]" id="orderextrachargesmappingid_0_' + rowcount + '" class="orderextrachargesmappingid" value="">\
                                  <input type="hidden" name="orderextrachargestax[0][]" id="orderextrachargestax_0_' + rowcount + '" class="orderextrachargestax" value="">\
                                  <input type="hidden" name="orderextrachargesname[0][]" id="orderextrachargesname_0_' + rowcount + '" class="orderextrachargesname" value="">\
                                  <input type="hidden" name="orderextrachargepercentage[0][]" id="orderextrachargepercentage_0_' + rowcount + '" class="orderextrachargepercentage" value="">\
                              </div>\
                          </div>\
                      </div>\
                      <div class="col-sm-3 pl-xs pr-xs">\
                        <div class="form-group p-n" id="orderextrachargeamount_0_' + rowcount + '_div">\
                            <div class="col-sm-12">\
                                <input type="text" id="orderextrachargeamount_0_' + rowcount + '" name="orderextrachargeamount[0][]" class="form-control text-right orderextrachargeamount" placeholder="Charge" onkeypress="return decimal_number_validation(event, this.value)">\
                            </div>\
                        </div>\
                      </div>\
                      <div class="col-md-3 text-right pt-md">\
                          <button type="button" class="btn btn-default btn-raised remove_charges_btn m-n" onclick="removecharge(0,' + rowcount + ')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>\
                          <button type="button" class="btn btn-default btn-raised add_charges_btn m-n" onclick="addnewcharge()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>\
                      </div>\
                    </div>';

    $(".countcharges0 .remove_charges_btn:first").show();
    $(".countcharges0 .add_charges_btn:last").hide();
    $("#countcharges_0_" + (rowcount - 1)).after(datahtml);

    $("#orderextrachargesid_0_" + rowcount).selectpicker("refresh");
}

function removecharge(orderid, rowid) {

    /* if($('select[name="extrachargesid[]"]').length!=1 && ACTION==1 && $('#extrachargemappingid'+rowid).val()!=null){
        var removeextrachargemappingid = $('#removeextrachargemappingid').val();
        $('#removeextrachargemappingid').val(removeextrachargemappingid+','+$('#extrachargemappingid'+rowid).val());
    } */
    $("#countcharges_" + orderid + "_" + rowid).remove();
    overallextracharges();
    if (orderid == 0) {
        $(".countcharges" + orderid + " .add_charges_btn:last").show();
        if ($(".countcharges" + orderid + " .remove_charges_btn:visible").length == 1) {
            $(".countcharges" + orderid + " .remove_charges_btn:first").hide();
        }
    }

    netamounttotal();
}

function resetdata() {

    $("#member_div").removeClass("has-error is-focused");
    $("#orderid_div").removeClass("has-error is-focused");
    $("#billingaddress_div").removeClass("has-error is-focused");
    $("#shippingaddress_div").removeClass("has-error is-focused");
    $("#invoicedate_div").removeClass("has-error is-focused");

    if (ACTION == 0) {
        if (MemberId == 0) {
            $('#memberid,#orderid').val('0');
            $('#billingaddressid,#shippingaddressid').val('0');
            $('#billingaddress').val('');
            $('#shippingaddress').val('');
            $('#paymentdays,#cashbackpercent,#cashbackamount').val('');
            $('#orderid')
                .find('option')
                .remove()
                .end()
                .append()
                .val('0');

            $('#billingaddressid,#shippingaddressid')
                .find('option')
                .remove()
                .end();
            $('#orderid,#billingaddressid,#shippingaddressid').selectpicker('refresh');
        } else {
            $('#memberid').val(MemberId);
            $('#orderid').val(OrderId);
        }
        $('#remarks').val("");
        $('#invoicedate').val(new Date().toLocaleDateString());
        $('.selectpicker').selectpicker('refresh');
        if (MemberId != 0) {
            getTransactionProducts();
        } else {
            $("#invoiceproducttable tbody").html("<tr><td colspan='9' class='text-center'>No data available in table.</td></tr>");
            $('#orderamountdiv').html("");
            $('#extracharges_div').html("");
        }
        overallextracharges();
        netamounttotal();
    }

    $('html, body').animate({
        scrollTop: 0
    }, 'slow');
}

function checkvalidation(btntype = '') {

    var invoiceid = $('#invoiceid').val();
    var channelid = $('#channelid').val();
    var memberid = $('#memberid').val();
    var orderid = $('#orderid').val();
    var billingaddressid = $('#billingaddressid').val();
    var shippingaddressid = $('#shippingaddressid').val();
    var invoicedate = $('#invoicedate').val();

    var isvalidchannelid = isvalidmemberid = isvalidorderid = isvalidproductcount = isvalidbillingaddressid = isvalidshippingaddressid = isvalidinvoicedate = isvalidextrachargesid = isvalidextrachargeamount = isvalidduplicatecharges = 1;
    PNotify.removeAll();

    if (channelid == 0) {
        $("#channel_div").addClass("has-error is-focused");
        new PNotify({
            title: "Please select channel !",
            styling: 'fontawesome',
            delay: '3000',
            type: 'error'
        });
        isvalidchannelid = 0;
    } else {
        $("#channel_div").removeClass("has-error is-focused");
    }
    if (memberid == 0) {
        $("#member_div").addClass("has-error is-focused");
        new PNotify({
            title: "Please select " + member_label + " !",
            styling: 'fontawesome',
            delay: '3000',
            type: 'error'
        });
        isvalidmemberid = 0;
    } else {
        $("#member_div").removeClass("has-error is-focused");
    }
    if (orderid == 0 || orderid == null) {
        $("#orderid_div").addClass("has-error is-focused");
        new PNotify({
            title: "Please select orders !",
            styling: 'fontawesome',
            delay: '3000',
            type: 'error'
        });
        isvalidorderid = 0;
    } else {
        $("#orderid_div").removeClass("has-error is-focused");
    }
    /* if(billingaddressid == "" || billingaddressid == null){
      $("#billingaddress_div").addClass("has-error is-focused");
      new PNotify({title: "Please select billing address !",styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidbillingaddressid = 0;
    }else{
      $("#billingaddress_div").removeClass("has-error is-focused");
    }
    if(shippingaddressid == "" || shippingaddressid == null){
      $("#shippingaddress_div").addClass("has-error is-focused");
      new PNotify({title: "Please select shipping address !",styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidshippingaddressid = 0;
    }else{
      $("#shippingaddress_div").removeClass("has-error is-focused");
    } */
    if (invoicedate == "") {
        $("#invoicedate_div").addClass("has-error is-focused");
        new PNotify({
            title: "Please select invoice date !",
            styling: 'fontawesome',
            delay: '3000',
            type: 'error'
        });
        isvalidinvoicedate = 0;
    } else {
        $("#invoicedate_div").removeClass("has-error is-focused");
    }

    if ($('.countproducts').length == 0) {
        isvalidproductcount == 0;
        new PNotify({
            title: "Please add at least one product !",
            styling: 'fontawesome',
            delay: '3000',
            type: 'error'
        });
    }
    var i = 1;
    $('.countorders').each(function () {
        var orderid = $(this).attr('id');
        $('.countcharges' + orderid).each(function () {
            var elementid = $(this).attr('id').split('_');
            var divid = elementid[2];

            if ($("#orderextrachargesid_" + orderid + "_" + divid).val() > 0 || $("#orderextrachargeamount_" + orderid + "_" + divid).val() > 0) {

                if ($("#orderextrachargesid_" + orderid + "_" + divid).val() == 0) {
                    $("#extracharges_" + orderid + "_" + divid + "_div").addClass("has-error is-focused");
                    new PNotify({
                        title: 'Please select ' + divid + ' extra charge !',
                        styling: 'fontawesome',
                        delay: '3000',
                        type: 'error'
                    });
                    isvalidextrachargesid = 0;
                } else {
                    $("#extracharges_" + orderid + "_" + divid + "_div").removeClass("has-error is-focused");
                }
                if ($("#orderextrachargeamount_" + orderid + "_" + divid).val() == '' || $("#orderextrachargeamount_" + orderid + "_" + divid).val() == 0) {
                    $("#orderextrachargeamount_" + orderid + "_" + divid + "_div").addClass("has-error is-focused");
                    var msg = (orderid == 0) ? "other charges" : (i) + " order";
                    new PNotify({
                        title: 'Please enter ' + divid + ' extra charge amount on ' + msg + ' !',
                        styling: 'fontawesome',
                        delay: '3000',
                        type: 'error'
                    });
                    isvalidextrachargeamount = 0;
                } else {
                    $("#orderextrachargeamount_" + orderid + "_" + divid + "_div").removeClass("has-error is-focused");
                }
            } else {
                $("#extracharges_" + orderid + "_" + divid + "_div").removeClass("has-error is-focused");
                $("#orderextrachargeamount_" + orderid + "_" + divid + "_div").removeClass("has-error is-focused");
            }

        });
        i++;
    });
    var k = 1;
    $('.countorders').each(function () {
        var orderid = $(this).attr('id');

        var selects_charges = $('select[name="orderextrachargesid[' + orderid + '][]"]');
        var values = [];
        for (j = 0; j < selects_charges.length; j++) {
            var selectscharges = selects_charges[j];
            var id = selectscharges.id.split("_");
            var divid = id[2];

            if (selectscharges.value != 0) {
                if (values.indexOf(selectscharges.value) > -1) {
                    $("#extracharges_" + orderid + "_" + divid + "_div").addClass("has-error is-focused");
                    new PNotify({
                        title: 'Please select ' + (j + 1) + ' is different extra charge !',
                        styling: 'fontawesome',
                        delay: '3000',
                        type: 'error'
                    });
                    isvalidduplicatecharges = 0;
                } else {
                    values.push(selectscharges.value);
                    if ($("#orderextrachargesid_" + orderid + "_" + divid).val() != 0) {
                        $("#extracharges_" + orderid + "_" + divid + "_div").removeClass("has-error is-focused");
                    }
                }
            }
        }
        k++;
    });
    if (isvalidchannelid == 1 && isvalidmemberid == 1 && isvalidorderid == 1 && isvalidproductcount == 1 && isvalidbillingaddressid == 1 && isvalidshippingaddressid == 1 && isvalidinvoicedate == 1 && isvalidextrachargesid == 1 && isvalidextrachargeamount == 1 && isvalidduplicatecharges == 1) {

        var formData = new FormData($('#invoiceform')[0]);
        if (ACTION == 0) {
            if (invoiceid == '' || invoiceid == 0) {
                var uurl = SITE_URL + "invoice/add-invoice";
                $.ajax({
                    url: uurl,
                    type: 'POST',
                    data: formData,
                    //async: false,
                    beforeSend: function () {
                        $('.mask').show();
                        $('#loader').show();
                    },
                    success: function (response) {
                        var obj = JSON.parse(response);
                        if (obj['error'] == 1) {
                            new PNotify({
                                title: "Invoice successfully generated !",
                                styling: 'fontawesome',
                                delay: '3000',
                                type: 'success'
                            });
                            if (btntype == 'print') {
                                printInvoice(obj['invoiceid']);
                                setTimeout(function () {
                                    window.location = SITE_URL + "invoice";
                                }, 1500);
                            } else {
                                resetdata();
                                if (MemberId != 0) {
                                    getTransactionProducts();
                                }
                            }
                        } else if (obj['error'] == 2) {
                            new PNotify({
                                title: "Invoice number aleady exists !",
                                styling: 'fontawesome',
                                delay: '3000',
                                type: 'error'
                            });
                        } else {
                            new PNotify({
                                title: "Invoice not generate !",
                                styling: 'fontawesome',
                                delay: '3000',
                                type: 'error'
                            });
                        }
                    },
                    error: function (xhr) {
                        //alert(xhr.responseText);
                    },
                    complete: function () {
                        $('.mask').hide();
                        $('#loader').hide();
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                });
            }
        } else {
            var uurl = SITE_URL + "invoice/update-invoice";
            $.ajax({
                url: uurl,
                type: 'POST',
                data: formData,
                //async: false,
                beforeSend: function () {
                    $('.mask').show();
                    $('#loader').show();
                },
                success: function (response) {
                    var obj = JSON.parse(response);
                    if (obj['error'] == 1) {
                        new PNotify({
                            title: "Invoice successfully updated !",
                            styling: 'fontawesome',
                            delay: '3000',
                            type: 'success'
                        });
                        if (btntype == 'print') {
                            printInvoice(obj['invoiceid']);
                            setTimeout(function () {
                                window.location = SITE_URL + "invoice";
                            }, 1500);
                        } else {
                            setTimeout(function () {
                                window.location = SITE_URL + "invoice/invoice-add";
                            }, 1500);
                        }
                    } else if (obj['error'] == 2) {
                        new PNotify({
                            title: "Invoice number aleady exists !",
                            styling: 'fontawesome',
                            delay: '3000',
                            type: 'error'
                        });
                    } else {
                        new PNotify({
                            title: "Invoice not updated !",
                            styling: 'fontawesome',
                            delay: '3000',
                            type: 'error'
                        });
                    }
                },
                error: function (xhr) {
                    //alert(xhr.responseText);
                },
                complete: function () {
                    $('.mask').hide();
                    $('#loader').hide();
                },
                cache: false,
                contentType: false,
                processData: false
            });
        }
    }
}