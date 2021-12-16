$(document).ready(function(){

    $('#orderid').on('change', function (e) {

        if(this.value==0 && OrderId==0){
            var productionplanid = $("#productionplanid").val();
        }else{
            var productionplanid = $('#orderid option:selected').attr("data-productionplanid");
        }
        getProductionPlanMaterial(productionplanid);
    });

    if($("#orderid").val()==0 && OrderId==0){
        var productionplanid = $("#productionplanid").val();
    }else{
        var productionplanid = $('#orderid option:selected').attr("data-productionplanid");
    }
    getProductionPlanMaterial(productionplanid);
});  

function getProductionPlanMaterial(productionplanid){
    var orderid = $("#orderid").val();
    
    var uurl = SITE_URL+"production-plan/getProductionPlanMaterial";
    $.ajax({
        url: uurl,
        type: 'POST',
        data: {orderid:String(orderid),productionplanid:String(productionplanid)},
        dataType: 'json',
        async: false,
        beforeSend: function(){
            $('.mask').show();
            $('#loader').show();
        },
        success: function(response){

            var READYTOSTART_HTML = MISSINGRAWMATERIAL_HTML = '';
            if(!$.isEmptyObject(response)){
                var readytostart = response['readytostart'];
                var missingrawmaterial = response['missingrawmaterial'];
            
                if(readytostart.length > 0){

                    var TABLE_ROWS = '';
                    for(var i=0; i<readytostart.length; i++){
                        TABLE_ROWS += '<tr>\
                                            <td>'+readytostart[i]['name']+'</td>\
                                            <td>'+readytostart[i]['variantname']+'</td>\
                                            <td class="text-right">'+readytostart[i]['quantity']+'</td>\
                                            <td>'+readytostart[i]['date']+'</td>\
                                       </tr>';
                    }
                    READYTOSTART_HTML = '<div class="panel-heading p-n">\
                                                <h2 style="font-weight:600;">Ready to Start</h2>\
                                            </div>\
                                            <div class="panel panel-default productvariantdiv">\
                                            <div class="panel-body no-padding">\
                                                <div class="col-md-12 p-n">\
                                                    <table class="table table-striped table-bordered mb-n">\
                                                        <thead>\
                                                            <tr>\
                                                                <th>Product Name</th>\
                                                                <th>Variant</th>\
                                                                <th class="text-right">Quantity</th>\
                                                                <th>Date</th>\
                                                            </tr>\
                                                        </thead>\
                                                        <tbody>\
                                                            '+TABLE_ROWS+'\
                                                        </tbody>\
                                                    </table>\
                                                </div>\
                                            </div>\
                                        </div>';
                }
                if(missingrawmaterial.length > 0){

                    var TABLE_ROWS = '';
                    for(var i=0; i<missingrawmaterial.length; i++){
                        TABLE_ROWS += '<tr>\
                                            <td>'+missingrawmaterial[i]['name']+'</td>\
                                            <td>'+missingrawmaterial[i]['variantname']+'</td>\
                                            <td class="text-right">'+missingrawmaterial[i]['quantity']+'</td>\
                                            <td>'+missingrawmaterial[i]['date']+'</td>\
                                       </tr>';
                    }
                    var MISSINGRAWMATERIAL_HTML = '<div class="panel-heading p-n">\
                                                <h2 style="font-weight:600;">Missing Raw Material</h2>\
                                                <a href="'+SITE_URL+'raw-material-request/add-raw-material-request/'+productionplanid+'" class="btn btn-primary btn-raised pull-right">Request</a>\
                                            </div>\
                                            <div class="panel panel-default productvariantdiv">\
                                            <div class="panel-body no-padding">\
                                                <div class="col-md-12 p-n">\
                                                    <table class="table table-striped table-bordered mb-n">\
                                                        <thead>\
                                                            <tr>\
                                                                <th>Product Name</th>\
                                                                <th>Variant</th>\
                                                                <th class="text-right">Quantity</th>\
                                                                <th>Date</th>\
                                                            </tr>\
                                                        </thead>\
                                                        <tbody>\
                                                            '+TABLE_ROWS+'\
                                                        </tbody>\
                                                    </table>\
                                                </div>\
                                            </div>\
                                        </div>';
                }
            }

            if(READYTOSTART_HTML!=""){
                $("#readytostartpanel").html(READYTOSTART_HTML).show();
            }else{
                $("#readytostartpanel").html('').hide(); 
            }
            if(MISSINGRAWMATERIAL_HTML!=""){
                $("#missingmaterialpanel").html(MISSINGRAWMATERIAL_HTML).show();
            }else{
                $("#missingmaterialpanel").html('').hide(); 
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