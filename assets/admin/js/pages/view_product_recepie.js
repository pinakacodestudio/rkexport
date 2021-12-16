$(document).ready(function(){

    $('#productid').on('change', function (e) {

        var productrecepieid = $('#productid option:selected').attr("data-id");
        // getproductrecepiedetails(productrecepieid);

        $("#editBtn").attr("href",SITE_URL+"product-recepie/product-recepie-edit/"+productrecepieid);
        oTable.ajax.reload(null, false);

        var isuniversal = $("#productid option:selected").attr("data-isuniversal");
        if(isuniversal==0){
            getVariantProductCombination(productrecepieid);
        }else{
            $("#variantwisepanel").hide(); 
        }
    });

    var productrecepieid = $('#productid option:selected').attr("data-id");
    var isuniversal = $("#productid option:selected").attr("data-isuniversal");
    if(isuniversal==0){
        getVariantProductCombination(productrecepieid);
    }else{
        $("#variantwisepanel").hide(); 
    }
    
    oTable = $('#commonmaterialtable').DataTable({
        "language": {
            "lengthMenu": "_MENU_"
        },
        "pageLength": 10,
        "columnDefs": [{
            'orderable': false,
            'targets': [0,-1]
        },{'targets': [-2], className: "text-right"},{'targets': [-1], className: "text-center"}],
        "order": [], //Initial no order.
        'serverSide': true,//Feature control DataTables' server-side processing mode.
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": SITE_URL+"product-recepie/common-material-listing",
            "type": "POST",
            "data": function ( data ) {
                data.productrecepieid = $('#productid option:selected').attr("data-id");
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
    $('#commonmaterialtable_filter input').attr('placeholder','Search...');


    //DOM Manipulation to move datatable elements integrate to panel
    $('#commonpanel .panel-ctrls').append($('#commonmaterialtable_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('#commonpanel .panel-ctrls').append("<i class='separator'></i>");
    $('#commonpanel .panel-ctrls').append($('#commonmaterialtable_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

    $('#commonpanel .panel-footer').append($(".dataTable+.row"));
    $('#commonmaterialtable_paginate>ul.pagination').addClass("pull-right pagination-md");
    
});
/****PRODUCT CHANGE EVENT****/
$(document).on('change', '#editproductid', function (e) {

    getproductprice(this.value);
});
function getVariantProductCombination(productrecepieid){
    var productid = $("#productid").val();
    
    var uurl = SITE_URL+"product-recepie/getVariantProductCombination";
    $.ajax({
        url: uurl,
        type: 'POST',
        data: {productid:String(productid),productrecepieid:String(productrecepieid)},
        dataType: 'json',
        async: false,
        beforeSend: function(){
            $('.mask').show();
            $('#loader').show();
        },
        success: function(response){

            var prices = response['prices'];
            var combination = response['combination'];
            if(prices.length > 0){

                var VARIANT_HTML = '';
                for(var i=0; i<prices.length; i++){

                    if(prices[i]['isrecepievariant']==1){

                        var variant = '';
                        if(!$.isEmptyObject(combination)){
                            var priceid = combination[prices[i]['id']];
                            if(priceid.length > 0){
                                variant = '<table class="table table-striped table-bordered" style="box-shadow: 0 1px 6px 0 rgba(0, 0, 0, 0.12), 0px 1px 1px 0 rgba(0, 0, 0, 0.12) !important;">\
                                                <tr>\
                                                    <th width="40%">Attribute</th>\
                                                    <th width="60%">Variant</th>\
                                                </tr>';
                                for(var j=0; j<priceid.length; j++){

                                    variant += '<tr>\
                                                    <th>'+priceid[j]['attribute']+'</th>\
                                                    <td>'+priceid[j]['variantvalue']+'</td>\
                                                </tr>';
                                }
                                variant += '</table>';
                            }
                        }
                        var productdata = "";
                        if(prices[i]['recepievariantdata'].length > 0){
                            var recepievariantdata = prices[i]['recepievariantdata'];
                            for(var v=0; v<recepievariantdata.length; v++){
                                var Action = '';
                                if(response['isedit']==1){
                                    Action += '<a onclick="editMaterial('+recepievariantdata[v]['id']+',&apos;variant&apos;)" class="btn btn-success btn-raised btn-sm" href="javascript:void(0)" title="EDIT"><i class="fa fa-pencil"></i></a>\
                                    <input type="hidden" id="variantproductid'+recepievariantdata[v]['id']+'" value="'+recepievariantdata[v]['productid']+'">\
                                    <input type="hidden" id="variantpriceid'+recepievariantdata[v]['id']+'" value="'+recepievariantdata[v]['rawpriceid']+'">\
                                    <input type="hidden" id="variantunitid'+recepievariantdata[v]['id']+'" value="'+recepievariantdata[v]['unitid']+'">\
                                    <input type="hidden" id="variantvalue'+recepievariantdata[v]['id']+'" value="'+parseFloat(recepievariantdata[v]['value']).toFixed(2)+'">\
                                    <input type="hidden" id="priceid'+recepievariantdata[v]['id']+'" value="'+prices[i]['id']+'">'; 
                                }
                                productdata += '<tr>\
                                                    <td>'+(v+1)+'</td>\
                                                    <td>'+recepievariantdata[v]['namewithvariant']+'</td>\
                                                    <td>'+recepievariantdata[v]['unitname']+'</td>\
                                                    <td class="text-right">'+parseFloat(recepievariantdata[v]['value']).toFixed(2)+'</td>\
                                                    <td>'+Action+'</td>\
                                                </tr>';
                                                
                            }
                        }
                        
                        VARIANT_HTML += '<div class="panel panel-default productvariantdiv" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="" style="visibility: visible; opacity: 1; display: block; transform: translateY(0px);">\
                                        <div class="panel-body no-padding pt-md">\
                                            <div class="col-md-12">\
                                                <div class="col-md-4 pl-xs pr-xs">\
                                                    '+variant+'\
                                                </div>\
                                                <div class="col-md-8 pull-right pl-xs pr-xs">\
                                                    <table class="table table-striped table-bordered" style="box-shadow: 0 1px 6px 0 rgba(0, 0, 0, 0.12), 0px 1px 1px 0 rgba(0, 0, 0, 0.12) !important;">\
                                                        <thead>\
                                                            <tr>\
                                                                <th class="width8">Sr. No.</th>\
                                                                <th>Product Name</th>\
                                                                <th>Unit</th>\
                                                                <th class="text-right">Quantity</th>\
                                                                <th class="width8 text-center">Action</th>\
                                                            </tr>\
                                                        </thead>\
                                                        <tbody>\
                                                            '+productdata+'\
                                                        </tbody>\
                                                    </table>\
                                                </div>\
                                            </div>\
                                        </div>\
                                    </div>';
                    }
                }
            }
            
            if(VARIANT_HTML!=""){
                $("#variantmaterialdata").html(VARIANT_HTML);
                $("#variantwisepanel").show(); 
            }else{
                $("#variantwisepanel").hide(); 
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
function editMaterial(materialid,type='common',mode=0){

    if(type=='common'){
        $("#MaterialModal .modal-title").html("Edit Common Raw Material");
    }else{
        $("#MaterialModal .modal-title").html("Edit Variant Wise Material");
    }
    if(mode==0){
        //OPEN POPUP
        if(type=='common'){
            var productid = $("#commonproductid"+materialid).val();
            var priceid = $("#commonpriceid"+materialid).val();
            var unitid = $("#commonunitid"+materialid).val();
            var value = $("#commonvalue"+materialid).val();
        }else{
            var productid = $("#variantproductid"+materialid).val();
            var priceid = $("#variantpriceid"+materialid).val();
            var unitid = $("#variantunitid"+materialid).val();
            var value = $("#variantvalue"+materialid).val();
        }
        $("#editproductid_div,#editpriceid_div,#editunitid_div,#editvalue_div").removeClass("has-error is-focused");
        $("#editproductid").val(productid);
        getproductprice(productid);
        $("#editpriceid").val(priceid);
        $("#editunitid").val(unitid);
        $("#editvalue").val(parseFloat(value).toFixed(2));
        $(".btnSubmitMaterial").attr("onclick","editMaterial("+materialid+",'"+type+"',1)");
        $(".selectpicker").selectpicker("refresh");
        $("#MaterialModal").modal("show");
    }else if(mode==1){
        //UPDATE VARIANT MATERIAL DETAILS

        var isvalidproductid = isvalidpriceid = isvalidunitid = isvalidvalue = 1;
        PNotify.removeAll();

        if($("#editproductid").val() == 0){
            $("#editproductid_div").addClass("has-error is-focused");
            new PNotify({title: 'Please select product !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidproductid = 0;
        }else {
            $("#editproductid_div").removeClass("has-error is-focused");
        }
        if($("#editpriceid").val() == 0){
            $("#editpriceid_div").addClass("has-error is-focused");
            new PNotify({title: 'Please select variant !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidpriceid = 0;
        }else {
            $("#editpriceid_div").removeClass("has-error is-focused");
        }
        if($("#editunitid").val() == 0){
            $("#editunitid_div").addClass("has-error is-focused");
            new PNotify({title: 'Please select unit !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidunitid = 0;
        }else {
            $("#editunitid_div").removeClass("has-error is-focused");
        }
        if($("#editvalue").val() == "" || parseFloat($("#editvalue").val()) <= "0"){
            $("#editvalue_div").addClass("has-error is-focused");
            new PNotify({title: 'Please enter value !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidvalue = 0;
        }else {
            $("#editvalue_div").removeClass("has-error is-focused");
        }

        if(isvalidproductid == 1 && isvalidpriceid == 1 && isvalidunitid == 1 && isvalidvalue == 1){
            var formData = new FormData($('#edit-material-form')[0]);
            formData.append("materialid",materialid);
            formData.append("type",type);
            if(type!='common'){
            formData.append("priceid",$("#priceid"+materialid).val());
            }
            formData.append("productrecepieid", $('#productid option:selected').attr("data-id"));
            
            var baseurl = SITE_URL + 'product-recepie/update-product-recepie-material';
            $.ajax({
                url: baseurl,
                type: 'POST',
                data: formData,
                beforeSend: function(){
                    $('.mask').show();
                    $('#loader').show();
                },
                success: function(response){
                    var data = JSON.parse(response);
                    $("#editproductid_div,#editunitid_div").removeClass("has-error is-focused");
                    if(data['error']==1){
                        new PNotify({title: 'Material successfully updated.',styling: 'fontawesome',delay: '3000',type: 'success'});
                        if(type=='common'){
                            oTable.ajax.reload(null, false);
                        }else{
                            location.reload();
                        }
                        setTimeout(function() { $("#MaterialModal").modal("hide"); }, 500);
                    }else if(data['error']==2){
                        new PNotify({title: 'Product variant & unit already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        $("#editpriceid_div,#editunitid_div").addClass("has-error is-focused");
                    }else{
                        new PNotify({title: 'Material not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
}
function getproductprice(productid){
    
    $("#editpriceid").find('option')
        .remove()
        .end()
        .append('<option value="0">Select Variant</option>')
        .val('0')
    ;
    $("#editpriceid").selectpicker('refresh');
    
    if(productid!=0){
      var uurl = SITE_URL+"product-recepie/getVariantByProductId";
      
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {productid:String(productid)},
        dataType: 'json',
        async: false,
        success: function(response){
  
            for(var i = 0; i < response.length; i++) {
                if(ACTION==1){
                    if(typeof(response[i]['universal'])!='undefined'){
                        $("#editpriceid").append($('<option>', { 
                          value: response[i]['id'],
                          text : response[i]['memberprice']
                        }));
                        $("#editpriceid").val(response[i]['id']).selectpicker("refresh");
                    }else{
                        $("#editpriceid").append($('<option>', { 
                            value: response[i]['id'],
                            text : response[i]['memberprice']
                        }));
                    }
                }else{
                    $("#editpriceid").append($('<option>', { 
                        value: response[i]['id'],
                        text : response[i]['memberprice']
                    }));
                    if(response[i]['universal']!='undefined'){
                        $("#editpriceid").val(response[i]['id']);
                    }
                }
            }
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
      });
    }
    $("#editpriceid").selectpicker('refresh');
}