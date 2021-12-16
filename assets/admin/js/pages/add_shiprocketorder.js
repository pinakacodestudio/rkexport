$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
    $('#buttonids').hide();

    $('#pickuplocation').change(function(){
        //alert("1");
        $('#pickupaddress').val($( "#pickuplocation option:selected" ).text());
        //alert($( "#pickuplocation option:selected" ).text());
    });

    $('#invoiceid').change(function(){
        getWeight();
    });
    
});



function companydivclicked(id){
        
    if(document.getElementById('companyid'+id).checked){
        $('#companyid'+id).prop('checked',false);

    }else{
        $('#companyid'+id).prop('checked','checked');
    }

    $('#name').val($('#name'+id).val());
    $('#rtocharges').val($('#rtocharges'+id).val());
    $('#trackingservice').val($('#trackingservice'+id).val());
    $('#etd').val($('#etd'+id).val());
    $('#totalrate').val($('#totalrate'+id).val());
    $('#freightcharge').val($('#freightcharge'+id).val());
    $('#codcharges').val($('#codcharges'+id).val());
    $('#couriercompanyid').val($('#companyid'+id).val());

    //alert($("input[name='companyid']").val());
        
}

function searchcourier(){
    var length = $('#length').val();
    var breath = $('#breath').val();
    var weight = $('#weight').val();
    var height = $('#height').val();
    var pickuplocation = $('#pickuplocation').val();
    //alert(pickuplocation);
    //console.log(pickuplocation);

    var isvalidlength = isvalidbreath = isvalidheight = isvalidweight = isvalidpickuplocation = 0;

    if(length == ''){
        $("#length_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter length !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidlength = 0;
    }else {
        $("#length_div").removeClass("has-error is-focused");
        isvalidlength = 1;
    }

    if(breath == ''){
        $("#breath_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter breath !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidbreath = 0;
    }else {
        $("#breath_div").removeClass("has-error is-focused");
        isvalidbreath = 1;
    }

    if(height == ''){
        $("#height_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter height !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidheight = 0;
    }else {
        $("#height_div").removeClass("has-error is-focused");
        isvalidheight = 1;
    }

    if(weight == ''){
        $("#weight_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter weight !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidweight = 0;
    }else {
        $("#weight_div").removeClass("has-error is-focused");
        isvalidweight = 1;
    }

    if(pickuplocation == 0){
        $("#pickuplocation_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select pickup address !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidpickuplocation = 0;
    }else {
        $("#pickuplocation_div").removeClass("has-error is-focused");
        isvalidpickuplocation = 1;
    }

    if(isvalidlength==1 && isvalidbreath==1 && isvalidheight==1 && isvalidweight==1 && isvalidpickuplocation==1){

        var uurl = SITE_URL+"Shiprocket-order/getCouriercompany";
        var htmldata = '';
        var i=1,j=0;
        var recommenddata = weightclass = pickupperformanceclass = deliveryperformanceclass = trackingperformanceclass = rtoperformanceclass = ratingclass = '';

        

        $.ajax({
            
            url: uurl,
            type: 'POST',
            data: {length:length,breath:breath,weight:weight,height:height,pickuplocation:pickuplocation},
            dataType: 'json',
            //async: false,
            beforeSend: function(){
                $('.mask').show();
                $('#loader').show();
            },
            success: function(response){
                var JSONObject = JSON.parse(response);
                //console.log(JSONObject);
                jQuery.each(JSONObject['data']['available_courier_companies'], function(index, itemData) {

                    if((itemData['weight_cases']*20) <= 30){
                        weightclass = 'progress-bar progress-bar-danger';
                    }else if((itemData['weight_cases']*20)>30 &&  (itemData['weight_cases']*20)<=70){
                        weightclass = 'progress-bar progress-bar-warning';
                    }else if((itemData['weight_cases']*20) > 70 ){
                        weightclass = 'progress-bar progress-bar-success';
                    }

                    if((itemData['pickup_performance']*20) <= 30){
                        pickupperformanceclass = 'progress-bar progress-bar-danger';
                    }else if((itemData['pickup_performance']*20)>30 &&  (itemData['pickup_performance']*20)<=70){
                        pickupperformanceclass = 'progress-bar progress-bar-warning';
                    }else if((itemData['pickup_performance']*20) > 70 ){
                        pickupperformanceclass = 'progress-bar progress-bar-success';
                    }

                    if((itemData['delivery_performance']*20) <= 30){
                        deliveryperformanceclass = 'progress-bar progress-bar-danger';
                    }else if((itemData['delivery_performance']*20)>30 &&  (itemData['delivery_performance']*20)<=70){
                        deliveryperformanceclass = 'progress-bar progress-bar-warning';
                    }else if((itemData['delivery_performance']*20) > 70 ){
                        deliveryperformanceclass = 'progress-bar progress-bar-success';
                    }

                    if((itemData['tracking_performance']*20) <= 30){
                        trackingperformanceclass = 'progress-bar progress-bar-danger';
                    }else if((itemData['tracking_performance']*20)>30 &&  (itemData['tracking_performance']*20)<=70){
                        trackingperformanceclass = 'progress-bar progress-bar-warning';
                    }else if((itemData['tracking_performance']*20) > 70 ){
                        trackingperformanceclass = 'progress-bar progress-bar-success';
                    }

                    if((itemData['rto_performance']*20) <= 30){
                        rtoperformanceclass = 'progress-bar progress-bar-danger';
                    }else if((itemData['rto_performance']*20)>30 &&  (itemData['rto_performance']*20)<=70){
                        rtoperformanceclass = 'progress-bar progress-bar-warning';
                    }else if((itemData['rto_performance']*20) > 70 ){
                        rtoperformanceclass = 'progress-bar progress-bar-success';
                    }

                    if((itemData['rating']*20) <= 30){
                        ratingclass = 'progress-bar progress-bar-danger';
                    }else if((itemData['rating']*20)>30 &&  (itemData['rating']*20)<=70){
                        ratingclass = 'progress-bar progress-bar-warning';
                    }else if((itemData['rating']*20) > 70 ){
                        ratingclass = 'progress-bar progress-bar-success';
                    }
                    
                    htmldata += '<div class="row" id="box'+i+'" ">\
                    <div class="col-md-12">\
                        <div class="panel panel-transparent ">\
                           \
                            <div id="recommendid'+i+'" style="position:absolute;" ></div>\
                            <div class="panel-body shadowfaxforwarddiv  "  onclick="companydivclicked('+i+')" id="panel'+i+'" style="">\
                                        \
                                    <div class="col-md-12 input-group">\
                                        <div class="radio input-group-addon" style="align:center;">\
                                                <input type="radio" name="companyid" id="companyid'+i+'" value="'+itemData['courier_company_id']+'" >\
                                                <label ></label>\
                                        </div>\
                                        <div class="col-md-3">\
                                            <div class="col-md-12 p-n">\
                                                <input type=hidden id="name'+i+'" value="'+itemData['courier_name']+'">\
                                                <h4>'+itemData['courier_name']+'</h4>\
                                            </div>\
                                            <div class="col-md-12 p-n">\
                                                    <h5 style="color:#424242;"><b>Min Weight : </b>'+itemData['min_weight']+' Kg</h5>\
                                            </div>\
                                            <div class="col-md-12 p-n">\
                                                    <input type=hidden id="rtocharges'+i+'" value="'+itemData['rto_charges']+'">\
                                                    <input type="button" class="<?=addbtn_class?>" value="RTO Charges : '+itemData['rto_charges']+'">\
                                            </div>\
                                        </div>\
                                        <div class="col-md-2" style="padding-top:7px;">\
                                            <div class="col-md-12 p-n">\
                                                <p>Pickup Performance</p>\
                                                <div class="progress">\
                                                    <div class="'+pickupperformanceclass+'" style="width: '+(itemData['pickup_performance']*20)+'%"></div>\
                                                </div>\
                                            </div>\
                                            <div class="col-md-12 p-n">\
                                                <p>RTO Performance</p>\
                                                <div class="progress">\
                                                    <div class="'+rtoperformanceclass+'" style="width: '+(itemData['rto_performance']*20)+'%"></div>\
                                                </div>\
                                            </div>\
                                        </div>\
                                        <div class="col-md-2" style="padding-top:7px;">\
                                            <div class="col-md-12 p-n">\
                                                <p>Delivery Performance</p>\
                                                <div class="progress">\
                                                    <div class="'+deliveryperformanceclass+'" style="width: '+(itemData['delivery_performance']*20)+'%"></div>\
                                                </div>\
                                            </div>\
                                            <div class="col-md-12 p-n">\
                                                <p>Weight Cases</p>\
                                                <div class="progress">\
                                                    \
                                                    <div class="'+weightclass+'" style="width: '+(itemData['weight_cases']*20)+'%"></div>\
                                                </div>\
                                            </div>\
                                        </div>\
                                        <div class="col-md-2" style="padding-top:7px;">\
                                            <div class="col-md-12 p-n">\
                                                <p>NDR Performance</p>\
                                                <div class="progress">\
                                                    <div class="'+trackingperformanceclass+'" style="width: '+(itemData['tracking_performance']*20)+'%"></div>\
                                                </div>\
                                            </div>\
                                            <div class="col-md-12 p-n">\
                                                <p>Overall Rating</p>\
                                                <div class="progress">\
                                                    <div class="'+ratingclass+'" style="width: '+(itemData['rating']*20)+'%"></div>\
                                                </div>\
                                            </div>\
                                        </div>\
                                        <div class="col-md-3">\
                                            <div class="col-md-12 p-n">\
                                                <input type="hidden" id="trackingservice'+i+'" value="'+itemData['realtime_tracking']+'">\
                                                <h5><b>Tracking Service : </b>'+itemData['realtime_tracking']+'</h5>\
                                            </div>\
                                            <div class="col-md-12 p-n">\
                                                <h5><b>Pickup Available : </b>Tomorrow </h5>\
                                            </div>\
                                            <div class="col-md-12 p-n">\
                                                <input type="hidden" id="etd'+i+'" value="'+itemData['etd']+'">\
                                                <h5><b>Estimated Delivery : </b> '+itemData['etd']+'</h5>\
                                            </div>\
                                        </div>\
                                        <div class="input-group-addon " >\
                                                <input type="hidden" id="totalrate'+i+'" value="'+itemData['rate']+'">\
                                                <input type="hidden" id="freightcharge'+i+'" value="'+itemData['freight_charge']+'">\
                                                <input type="hidden" id="codcharges'+i+'" value="'+itemData['cod_charges']+'">\
                                                <h4>'+CURRENCY_CODE+' '+itemData['rate']+' \
                                                    <span class=" tooltips" data-toggle="tooltips" data-placement="left" title="Frieght Charges : '+itemData['freight_charge']+' + COD Charges : '+itemData['cod_charges']+'">\
                                                        <i class="fa fa-question-circle"></i>\
                                                    </span>\
                                                </h4>\
                                           \
                                        </div>\
                                    </div>\
                            </div>\
                        </div>\
                    </div>\
                </div>';

                //console.log(itemData['courier_company_id']);
                if(itemData['courier_company_id']==JSONObject['data']['shiprocket_recommended_courier_id']){
                    
                    recommenddata = '<span class="courier_card ">RECOMMENDED</span>';
                     j=i;
                    
                }
                     
                    i++;
                });

                $('#couriercompany').html(htmldata);
                $('#recommendid'+j).html(recommenddata);
                
                $('#buttonids').show();
                companydivclicked(j);
                $('#pickuplocation').change(function(){
                    //alert("1");
                    $('#pickupaddress').val($( "#pickuplocation option:selected" ).text());
                    //alert($( "#pickuplocation option:selected" ).text());
                });
                
                
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
}

function getWeight(){

    var invoiceid = $('#invoiceid').val();
    var uurl = SITE_URL+"Shiprocket-order/getWeight";
     
        $.ajax({
            
            url: uurl,
            type: 'POST',
            data: {invoiceid:invoiceid},
            dataType: 'json',
            //async: false,
            beforeSend: function(){
                $('.mask').show();
                $('#loader').show();
            },
            success: function(response){
                $('#weight').val(response['totalweight']);
                
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

function checkvalidation(){
    
    var length = $('#length').val();
    var breath = $('#breath').val();
    var weight = $('#weight').val();
    var height = $('#height').val();
    var pickuplocation = $('#pickuplocation').val();
    var invoiceid = $('#invoiceid').val();
    var couriercompanyid = $('#couriercompanyid').val();
    

    var isvalidlength = isvalidbreath = isvalidheight = isvalidweight = isvalidpickuplocation = isvalidinvoice = isvalidcompanyid = 0;

    if(length == ''){
        $("#length_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter length !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidlength = 0;
    }else {
        $("#length_div").removeClass("has-error is-focused");
        isvalidlength = 1;
    }

    if(breath == ''){
        $("#breath_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter breath !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidbreath = 0;
    }else {
        $("#breath_div").removeClass("has-error is-focused");
        isvalidbreath = 1;
    }

    if(height == ''){
        $("#height_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter height !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidheight = 0;
    }else {
        $("#height_div").removeClass("has-error is-focused");
        isvalidheight = 1;
    }

    if(weight == ''){
        $("#weight_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter weight !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidweight = 0;
    }else {
        $("#weight_div").removeClass("has-error is-focused");
        isvalidweight = 1;
    }

    if(pickuplocation == 0){
        $("#pickuplocation_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select pickup address !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidpickuplocation = 0;
    }else {
        $("#pickuplocation_div").removeClass("has-error is-focused");
        isvalidpickuplocation = 1;
    }

    if(invoiceid == 0){
        $("#invoice_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select invoice !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidinvoice = 0;
    }else {
        $("#invoice_div").removeClass("has-error is-focused");
        isvalidinvoice = 1;
    }

    if(couriercompanyid == ''){
        //$("#length_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select courier company !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidcompanyid = 0;
    }else {
        //$("#length_div").removeClass("has-error is-focused");
        isvalidcompanyid = 1;
    }

    if(isvalidlength==1 && isvalidbreath==1 && isvalidheight==1 && isvalidweight==1 && isvalidpickuplocation==1 && isvalidinvoice==1 && isvalidcompanyid==1){

    
   

    PNotify.removeAll();
    
   
        var formData = new FormData($('#shiprocketorderform')[0]);
        if(ACTION==0){
            var uurl = SITE_URL+"Shiprocket_order/insert-order";
            $.ajax({
                url: uurl,
                type: 'POST',
                data: formData,
                //async: false,
                beforeSend: function(){
                $('.mask').show();
                $('#loader').show();
                },
                success: function(response){
                    if(response==1){
                        new PNotify({title: "Order successfully added !",styling: 'fontawesome',delay: '3000',type: 'success'});
                        
                            setTimeout(function() { window.location=SITE_URL+"Shiprocket-order"; }, 1500);
                        
                    }else if(response==0){
                        new PNotify({title: 'Order not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else{
                        new PNotify({title:response,styling: 'fontawesome',delay: '3000',type: 'error'});
                    }
                },
                error: function(xhr) {
                //alert(xhr.responseText);
                },
                complete: function(){
                $('.mask').hide();
                $('#loader').hide();
                },
                cache: false,
                contentType: false,
                processData: false
            });
        }else{
            var uurl = SITE_URL+"order/update-order";
            $.ajax({
              url: uurl,
              type: 'POST',
              data: formData,
              //async: false,
              beforeSend: function(){
                $('.mask').show();
                $('#loader').show();
              },
              success: function(response){
                if(response==1){
                    new PNotify({title: "Order successfully updated.",styling: 'fontawesome',delay: '3000',type: 'success'});
                    if(ordertype=='1'){
                        setTimeout(function() { window.location=SITE_URL+"purchase-order"; }, 1500);
                    }else{
                        setTimeout(function() { window.location=SITE_URL+"order"; }, 1500);
                    }
                }else if(response==2){
                    new PNotify({title: "Order already exist !",styling: 'fontawesome',delay: '3000',type: 'error'});
                }else if(response==3){
                    new PNotify({title: "Quantity greater than stock quantity !",styling: 'fontawesome',delay: '3000',type: 'error'});
                }else if(response==0){
                    new PNotify({title: 'Order not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
                }else{
                    new PNotify({title: response,styling: 'fontawesome',delay: '3000',type: 'error'});
                }
              },
              error: function(xhr) {
              //alert(xhr.responseText);
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
