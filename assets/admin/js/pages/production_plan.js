$(document).ready(function() {
    
    oTable = $('#productionplantable').DataTable({
        "language": {
            "lengthMenu": "_MENU_"
        },
        "pageLength": 10,
        "columnDefs": [{
            'orderable': false,
            'targets': [0,3,4,-1,-2]
        },{className: "text-center", "targets": 5}],
        "order": [], //Initial no order.
        'serverSide': true,//Feature control DataTables' server-side processing mode.
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": SITE_URL+"production-plan/listing",
            "type": "POST",
            "data": function ( data ) {
               data.type = $("#type").val();
            },
            beforeSend: function(){
                $('.mask').show();
                $('#loader').show();
            },
            error: function(xhr) {
            //alert(xhr.responseText);
            },
            complete: function(e){
                $('.mask').hide();
                $('#loader').hide();
            },
        },
    });

    $('.dataTables_filter input').attr('placeholder','Search...');

    //DOM Manipulation to move datatable elements integrate to panel
    $('.panel-ctrls.panel-tbl').append($('.dataTables_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('.panel-ctrls.panel-tbl').append("<i class='separator'></i>");
    $('.panel-ctrls.panel-tbl').append($('.dataTables_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

    $('.panel-footer').append($(".dataTable+.row"));
    $('.dataTables_paginate>ul.pagination').addClass("pull-right pagination-md");

    $(function () {
        $('.panel-heading.filter-panel').click(function() {
            $(this).find('.button-icon span').html($(this).find('.button-icon span').text() == 'keyboard_arrow_down' ? 'keyboard_arrow_up' : 'keyboard_arrow_down');
            //$(this).children().toggleClass(" ");
            $(this).next().slideToggle({duration: 200});
            $(this).toggleClass('panel-collapsed');
            return false;
        });
    });
});

$(document).on('keyup','.productionqty', function (e) {
    var divid = $(this).attr('id').match(/\d+/);
    var qty = $(this).val();
    var maxqty = $("#productionqty"+divid).attr("data-maxqty");
    
    if(qty!="" && parseInt(qty) > parseInt(maxqty)){
        $(this).val(parseInt(maxqty));
    }

});
function applyFilter(){
    oTable.ajax.reload(null, false);
}
function openstartprocesspopup(productionplanid,orderid){

    getStrartProcessProducts(productionplanid);
    $("#startProcessModal").modal("show");
    $("#orderid").val(orderid);
    $("#productionplanid").val(productionplanid);
}
function getStrartProcessProducts(productionplanid){

    var uurl = SITE_URL+"production-plan/getStrartProcessProducts";
    $.ajax({
        url: uurl,
        type: 'POST',
        data: {productionplanid:String(productionplanid)},
        dataType: 'json',
        async: false,
        beforeSend: function(){
            $('.mask').show();
            $('#loader').show();
        },
        success: function(response){

            if(response.length > 0){
                var TABLE_ROWS = '';
                for(var i=0; i<response.length; i++){
                    var productname = response[i]['name'];
                    if(response[i]['available']==0){
                        productname = '<a href="javascript:void(0)" onclick="openrawmaterialpopup('+response[i]['id']+')" style="color:red;">'+response[i]['name']+'</a>';
                    }
                    var PROCESS_GROUP_OPTION = "";
                    for(var j=0; j<response[i]['processgroup'].length; j++){
                        PROCESS_GROUP_OPTION += '<option value="'+response[i]['processgroup'][j]['processgroupid']+'" '+(j==0?"selected":"")+'>'+response[i]['processgroup'][j]['name']+'</option>';
                    }
                    TABLE_ROWS += '<tr>\
                                        <td>'+productname+'\
                                            <input id="productionplandetailid" name="productionplandetailid[]" type="hidden" value="'+response[i]['id']+'">\
                                            <input name="productid[]" type="hidden" value="'+response[i]['productid']+'">\
                                            <input name="priceid[]" type="hidden" value="'+response[i]['priceid']+'">\
                                        </td>\
                                        <td>'+response[i]['variantname']+'</td>\
                                        <td class="text-right">'+response[i]['orderqty']+'</td>\
                                        <td>\
                                            <div class="col-md-12 p-n">\
                                                <div class="form-group mt-n" id="processgroup_div'+response[i]['id']+'">\
                                                    <div class="col-md-12">\
                                                        <select id="processgroupid'+response[i]['id']+'" name="processgroupid[]" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8">\
                                                            <option value="0">Select Process Group</option>\
                                                            '+PROCESS_GROUP_OPTION+'\
                                                        </select>\
                                                    </div>\
                                                </div>\
                                            </div>\
                                        </td>\
                                        <td class="text-right">\
                                            <div class="form-group pb-n mt-n">\
                                                <div class="col-md-12">\
                                                    <input id="productionqty'+response[i]['id']+'" name="productionqty[]" type="text" class="form-control productionqty text-right mb-n" value="'+(response[i]['remainqty']>0?response[i]['remainqty']:"")+'" data-maxqty="'+response[i]['remainqty']+'" '+(response[i]['remainqty']<=0?"disabled":"")+'>\
                                                </div>\
                                            </div>\
                                        </td>\
                                        <td>\
                                            <div class="checkbox">\
                                                <input value="'+response[i]['id']+'" type="checkbox" class="checkradios" name="productcheck'+response[i]['id']+'" id="productcheck'+response[i]['id']+'" onchange="singlecheck_product(this.id)" '+(response[i]['remainqty']<=0?"disabled":"")+'>\
                                                <label for="productcheck'+response[i]['id']+'"></label>\
                                            </div>';
                    
                                TABLE_ROWS += "<input id='rawmaterial"+response[i]['id']+"' type='hidden' value='"+response[i]['rawmaterials']+"'>";
                    TABLE_ROWS += '</td>\
                                </tr>';
                }
                var processproducts = '<table class="table table-bordered">\
                                        <thead>\
                                            <th>Product Name</th>\
                                            <th>Variant</th>\
                                            <th class="text-right">Order Quantity</th>\
                                            <th>Process Group</th>\
                                            <th class="text-right" width="20%">Production Quantity</th>\
                                            <th>\
                                                <div class="checkbox">\
                                                    <input id="productcheckall" onchange="allproductchecked()" type="checkbox" value="all">\
                                                    <label for="productcheckall"></label>\
                                                </div>\
                                            </th>\
                                        </thead>\
                                        <tbody>\
                                            '+TABLE_ROWS+'\
                                        </tbody>\
                                    </table>';
                $("#startprocessdata").html(processproducts);
                $("#refreshproduct").attr("onclick","refreshproductionprocess("+productionplanid+")");
                $(".selectpicker").selectpicker("refresh");
            }
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
        complete: function(){
            $('.mask').hide();
            $('#loader').hide();
        },
    });
}
function openrawmaterialpopup(id){

    var rawmaterial = $("#rawmaterial"+id).val();
    var obj = JSON.parse(rawmaterial);
    var PRODUCT_HTML = "";
    if(obj.length > 0){
        for(var i=0; i<obj.length; i++){
            var requiredtostartproduction = remainingstock = "";
            if(obj[i]['requiredtostartproduction'] != ""){
                requiredtostartproduction = parseFloat(parseFloat(obj[i]['requiredtostartproduction'])).toFixed(2)+" "+obj[i]['unit'];
            }
            remainingstock = parseFloat(obj[i]['remainingstock']).toFixed(2)+" "+obj[i]['unit'];
            PRODUCT_HTML += '<tr>\
                                    <td>'+obj[i]['productname']+'</td>\
                                    <td>'+parseFloat(obj[i]['stock']).toFixed(2)+" "+obj[i]['unit']+'</td>\
                                    <td>'+parseFloat(obj[i]['requiredstock']).toFixed(2)+" "+obj[i]['unit']+'</td>\
                                    <td>'+requiredtostartproduction+'</td>\
                                    <td>'+remainingstock+'</td>\
                                </tr>';
        }
    }else{
        PRODUCT_HTML += '<tr>\
                            <td colspan="5" class="text-center">No data available in table.</td>\
                        </tr>';
    }
    var material = '<table class="table table-striped">\
                        <thead>\
                            <tr>\
                                <th width="25%">Raw Material</th>\
                                <th>Stock</th>\
                                <th>Required Stock</th>\
                                <th>Required to Start Production</th>\
                                <th>Remaining Stock</th>\
                            </tr>\
                        </thead>\
                        <tbody>\
                            '+PRODUCT_HTML+'\
                        </tbody>\
                    </table>';

    $("#rawmaterialdata").html(material);
    $("#rawMaterialModal").modal("show");
}
function startprocess(){
    var productchecked = $('#startProcessModal .checkradios:checked').length;
    var quantity = $("input[name='productionqty[]']:not(:disabled)").map(function(){return $(this).val();}).get();
    var qtyid = $("input[name='productionqty[]']:not(:disabled)").map(function(){return $(this).attr("id");}).get();
    var orderid = $("#orderid").val();

    var isvalidproductchecked = isvalidquantity = isvalidgroup = 1;
    PNotify.removeAll();
    if(quantity.length > 0){
        var countqty = 0;
        for(var i=0; i<quantity.length; i++){
            var divid = qtyid[i].match(/\d+/);
            if(quantity[i]=="" || parseFloat(quantity[i]) == "0"){
                countqty++;
            }

            if($("#processgroupid"+divid).val() == 0 && $("#productcheck"+divid).is(":checked")== true){
                isvalidgroup = 0;
                $("#processgroup_div"+divid).addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(i+1)+' process group !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else{
                $("#processgroup_div"+divid).removeClass("has-error is-focused");
            }
        }
        if(countqty == quantity.length){
            isvalidquantity = 0;
            new PNotify({title: 'Please enter atleast one production quantity !',styling: 'fontawesome',delay: '3000',type: 'error'});
        }
    }
    if(productchecked == 0) {
        new PNotify({title: 'Please tick atleast one checkbox !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidproductchecked = 0;
    }
    
    if(isvalidproductchecked == 1 && isvalidquantity == 1 && isvalidgroup == 1){

        var formData = new FormData($('#start-process-form')[0]);
        var baseurl = SITE_URL + 'production-plan/start-production-plan-process';
        $.ajax({
            
            url: baseurl,
            type: 'POST',
            data: formData,
            //async: false,
            beforeSend: function(){
                $('.mask').show();
                $('#loader').show();
            },
            success: function(response){
                
                var data = JSON.parse(response);
                if(data['error']==1){
                    // window.location = SITE_URL + "product-process/start-new-process/"+data['processgroupid']+"/"+$("#productionplanid").val()+"/"+orderid;
                    $("#startProcessModal").modal("hide");
                    window.open( 
                        SITE_URL + "product-process/start-new-process/"+data['processgroupid']+"/"+$("#productionplanid").val()+"/"+orderid, "_blank"); 
                }else{
                    new PNotify({title: 'Process group not exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                }
            },
            error: function(xhr) {
            },
            complete: function(){
                $('.mask').hide();
                $('#loader').hide();
            },
            cache: false,
            contentType: false,
            processData: false
        });
    }
}
function refreshproductionprocess(id){
    var formData = new FormData($('#start-process-form')[0]);
    var baseurl = SITE_URL + 'production-plan/refresh-production-process';
    $.ajax({
        
        url: baseurl,
        type: 'POST',
        data: formData,
        //async: false,
        beforeSend: function(){
            $('.mask').show();
            $('#loader').show();
        },
        success: function(response){
            getStrartProcessProducts(id);
        },
        error: function(xhr) {
        },
        complete: function(){
            $('.mask').hide();
            $('#loader').hide();
        },
        cache: false,
        contentType: false,
        processData: false
    });
}

var productcurrentdids = [];
var productposition = 0;
var productinputs = $("#startProcessModal input[type='checkbox']");
function singlecheck_product(id){
    var productinputs = $("#startProcessModal input[type='checkbox']");
    var isallproductchecked = 1,isallproductdechecked = 1;
      
    if ($('#'+id).prop('checked')==true){
        productcurrentdids[productposition] = $('#'+id).val();                      
        productposition++;
        for(var i = 1; i<productinputs.length; i++){
          if($('#'+productinputs[i].id).prop('checked') == true){
            isallproductchecked = 1;
          }else{
            isallproductchecked = 0;
            break;
          }
        }
        if(isallproductchecked == 1){
          $('#productcheckall').prop('checked', true);
        }
    }else{
        productcurrentdids.splice($.inArray($('#'+id).val(), productcurrentdids),1);
        for(var i = 1; i<productinputs.length; i++){
          if($('#'+productinputs[i].id).prop('checked') == false){
            $('#productcheckall').prop('checked', false);
            break;
          }
        }
        productposition--;
    }
}

function allproductchecked(){
    var productinputs = $("#startProcessModal input[type='checkbox']");
      if ($('#productcheckall').prop('checked')==true){
        for(var i = 1; i<productinputs.length; i++){
            if($('#'+productinputs[i].id).prop('disabled') === false){
                $('#'+productinputs[i].id).prop('checked', true);
            }
            if($('#'+productinputs[i].id).prop('checked') == true){
                if(jQuery.inArray($('#'+productinputs[i].id).val(),productcurrentdids) == -1){
                    productcurrentdids[productposition] = $('#'+productinputs[i].id).val();
                    productposition++;
                }
            }
        }
      }
      else{ 
        for(var i = 1; i<productinputs.length; i++){
            productcurrentdids.splice($.inArray($('#'+productinputs[i].id).val(), productcurrentdids),1);
            $('#'+productinputs[i].id).prop('checked', false);
            productposition--;
        }
      }
}