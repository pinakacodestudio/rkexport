$(document).ready(function()
{
    $('#commissiontype').on('change',function(){

        $("#commissiondiv").html("");
        if(this.value > 0){
            addnewcommission();
        }
    });
    if(ACTION==1){
        $(".add_btn").hide();
        $(".add_btn:last").show();
    }
});
$(document).on("click",".checkGST", function() {
    var id = $(this).attr("id").match(/\d+/);

    if($(this).val() == 1){
        $("#gst"+id).val('1');
    }else{
        $("#gst"+id).val('0');
    }
});
function addnewcommission(){

    var commissiontype = $('#commissiontype').val();

    var HTML = "";
    if(commissiontype > 0){
        if(commissiontype == 1){
            //Flat Commission
            HTML += '<div class="col-md-11 col-md-offset-1">\
                        <input type="hidden" name="salescommissiondetailid" value="">\
                        <div class="form-group" id="flatcommission_div">\
                            <label for="flatcommission" class="col-sm-4 control-label">Commission (%) <span class="mandatoryfield">*</span></label>\
                            <div class="col-sm-4">\
                                <input id="flatcommission" type="text" name="flatcommission" value="" class="form-control" onkeypress="return decimal_number_validation(event, this.value,3,2)" onkeyup="onlypercentage(&quot;flatcommission&quot;)">\
                            </div>\
                        </div>\
                        <div class="form-group">\
                            <label for="focusedinput" class="col-sm-4 control-label">GST</label>\
                            <div class="col-sm-4">\
                                <div class="col-sm-5 col-xs-6 pl-n">\
                                    <div class="radio">\
                                        <input type="radio" name="flatcommissiongst" id="flatcommissionwithgst" value="1" checked>\
                                        <label for="flatcommissionwithgst">With GST</label>\
                                    </div>\
                                </div>\
                                <div class="col-sm-6 col-xs-6 pl-n">\
                                    <div class="radio">\
                                        <input type="radio" name="flatcommissiongst" id="flatcommissionwithoutgst" value="0">\
                                        <label for="flatcommissionwithoutgst">Without GST</label>\
                                    </div>\
                                </div>\
                            </div>\
                        </div>\
                    </div>';

            $("#commissiondiv").html(HTML);

        }
        else if(commissiontype == 2){
            //Product Base Commission
            var rowcount = $(".countproducts").length>0?(parseInt($(".countproducts:last").attr("id").match(/\d+/))+1):1;
            var hideBtn = "";
            if($(".countproducts").length==0){

                hideBtn = "display:none;";
                HTML += '<div class="col-sm-12"><hr></div>\
                        <div class="col-sm-12">\
                            <div class="col-sm-4">\
                                <div class="form-group">\
                                    <div class="col-sm-12 pl-sm pr-sm">\
                                        <label class="control-label">Select Product <span class="mandatoryfield">*</span></label>\
                                    </div>\
                                </div>\
                            </div>\
                            <div class="col-sm-2">\
                                <div class="form-group">\
                                    <div class="col-sm-12 pl-sm pr-sm">\
                                        <label class="control-label">Comm. (%) <span class="mandatoryfield">*</span></label>\
                                    </div>\
                                </div>\
                            </div>\
                            <div class="col-sm-5">\
                                <div class="form-group">\
                                    <div class="col-sm-12">\
                                        <label class="control-label">GST</label>\
                                    </div>\
                                </div>\
                            </div>\
                        </div>';
            }
            HTML += '<div class="col-sm-12 countproducts" id="countproducts'+rowcount+'">\
                        <input type="hidden" name="salescommissiondetailid[]" value="">\
                        <input type="hidden" name="salescommissionmappingid[]" value="">\
                        <div class="col-sm-4">\
                            <div class="form-group" id="product'+rowcount+'_div">\
                                <div class="col-sm-12 pl-sm pr-sm">\
                                    <select id="productid'+rowcount+'" name="productid[]" class="selectpicker form-control productid" data-live-search="true" data-select-on-tab="true" data-size="8" data-id="'+rowcount+'">\
                                        <option value="0">Select Product</option>\
                                        '+productoptionhtml+'\
                                    </select>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-sm-2">\
                            <div class="form-group" id="productcommission'+rowcount+'_div">\
                                <div class="col-sm-12 pl-sm pr-sm">\
                                    <input id="productcommission'+rowcount+'" type="text" name="productcommission[]" value="" class="form-control" onkeypress="return decimal_number_validation(event, this.value,3,2)" onkeyup="onlypercentage(&quot;productcommission'+rowcount+'&quot;)">\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-sm-4">\
                            <div class="form-group mt-sm">\
                                <div class="col-sm-12 pl-sm pr-sm">\
                                    <input type="hidden" id="gst'+rowcount+'" name="productgst[]" value="1">\
                                    <div class="col-sm-6 col-xs-8 pl-xs">\
                                        <div class="radio">\
                                            <input type="radio" class="checkGST" name="productgst'+rowcount+'" id="productwithgst'+rowcount+'" value="1" checked>\
                                            <label for="productwithgst'+rowcount+'">With GST</label>\
                                        </div>\
                                    </div>\
                                    <div class="col-sm-6 col-xs-8 pl-n">\
                                        <div class="radio">\
                                            <input type="radio" class="checkGST" name="productgst'+rowcount+'" id="productwithoutgst'+rowcount+'" value="0">\
                                            <label for="productwithoutgst'+rowcount+'">Without GST</label>\
                                        </div>\
                                    </div>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-sm-2 mt-sm">\
                            <button type="button" class="btn btn-default btn-raised remove_btn" onclick="removecommission(&quot;countproducts'+rowcount+'&quot;,'+rowcount+')"  style="padding: 5px 10px;margin-top: 0px;'+hideBtn+'"><i class="fa fa-minus"></i><div class="ripple-container"></div></button>\
                            <button type="button"class="btn btn-default btn-raised add_btn" onclick="addnewcommission()" style="padding: 5px 10px;margin-top: 0px;"> <i class="fa fa-plus"></i> <div class="ripple-container"></div></button>\
                        </div>\
                    </div>';

            $(".remove_btn:first").show();
            $(".add_btn:last").hide();
            if($(".countproducts").length>0){
                $("#countproducts"+(rowcount-1)).after(HTML);
            }else{
                $("#commissiondiv").html(HTML);
                $(".add_btn").hide();
                $(".add_btn:last").show();
            }
            $(".selectpicker").selectpicker('refresh');
        }
        else if(commissiontype == 3){
            //Member Base Commission
            var rowcount = $(".countmembers").length>0?(parseInt($(".countmembers:last").attr("id").match(/\d+/))+1):1;
            var hideBtn = "";
            if($(".countmembers").length==0){

                hideBtn = "display:none;";
                HTML += '<div class="col-sm-12"><hr></div>\
                        <div class="col-sm-12">\
                            <div class="col-sm-4">\
                                <div class="form-group">\
                                    <div class="col-sm-12 pl-sm pr-sm">\
                                        <label class="control-label">Select '+Member_label+' <span class="mandatoryfield">*</span></label>\
                                    </div>\
                                </div>\
                            </div>\
                            <div class="col-sm-2">\
                                <div class="form-group">\
                                    <div class="col-sm-12 pl-sm pr-sm">\
                                        <label class="control-label">Comm. (%) <span class="mandatoryfield">*</span></label>\
                                    </div>\
                                </div>\
                            </div>\
                            <div class="col-sm-5">\
                                <div class="form-group">\
                                    <div class="col-sm-12">\
                                        <label class="control-label">GST</label>\
                                    </div>\
                                </div>\
                            </div>\
                        </div>';
            }
            HTML += '<div class="col-sm-12 countmembers" id="countmembers'+rowcount+'">\
                        <input type="hidden" name="salescommissiondetailid[]" value="">\
                        <input type="hidden" name="salescommissionmappingid[]" value="">\
                        <div class="col-sm-4">\
                            <div class="form-group" id="member'+rowcount+'_div">\
                                <div class="col-sm-12 pl-sm pr-sm">\
                                    <select id="memberid'+rowcount+'" name="memberid[]" class="selectpicker form-control memberid" data-live-search="true" data-select-on-tab="true" data-size="8" data-id="'+rowcount+'">\
                                        <option value="0">Select '+Member_label+'</option>\
                                        '+memberoptionhtml+'\
                                    </select>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-sm-2">\
                            <div class="form-group" id="membercommission'+rowcount+'_div">\
                                <div class="col-sm-12 pl-sm pr-sm">\
                                    <input id="membercommission'+rowcount+'" type="text" name="membercommission[]" value="" class="form-control" onkeypress="return decimal_number_validation(event, this.value,3,2)"onkeyup="onlypercentage(&quot;membercommission'+rowcount+'&quot;)">\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-sm-4">\
                            <div class="form-group mt-sm">\
                                <div class="col-sm-12 pl-sm pr-sm">\
                                    <input type="hidden" id="gst'+rowcount+'" name="membergst[]" value="1">\
                                    <div class="col-sm-6 col-xs-8 pl-xs">\
                                        <div class="radio">\
                                            <input type="radio" class="checkGST" name="memberbasegst'+rowcount+'" id="memberbasewithgst'+rowcount+'" value="1" checked>\
                                            <label for="memberbasewithgst'+rowcount+'">With GST</label>\
                                        </div>\
                                    </div>\
                                    <div class="col-sm-6 col-xs-8 pl-n">\
                                        <div class="radio">\
                                            <input type="radio" class="checkGST" name="memberbasegst'+rowcount+'" id="memberbasewithoutgst'+rowcount+'" value="0">\
                                            <label for="memberbasewithoutgst'+rowcount+'">Without GST</label>\
                                        </div>\
                                    </div>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-sm-2 mt-sm">\
                            <button type="button" class="btn btn-default btn-raised remove_btn" onclick="removecommission(&quot;countmembers'+rowcount+'&quot;,'+rowcount+')"  style="padding: 5px 10px;margin-top: 0px;'+hideBtn+'"><i class="fa fa-minus"></i><div class="ripple-container"></div></button>\
                            <button type="button"class="btn btn-default btn-raised add_btn" onclick="addnewcommission()" style="padding: 5px 10px;margin-top: 0px;"> <i class="fa fa-plus"></i> <div class="ripple-container"></div></button>\
                        </div>\
                    </div>';

            $(".remove_btn:first").show();
            $(".add_btn:last").hide();
            if($(".countmembers").length>0){
                $("#countmembers"+(rowcount-1)).after(HTML);
            }else{
                $("#commissiondiv").html(HTML);
                $(".add_btn").hide();
                $(".add_btn:last").show();
            }
            $(".selectpicker").selectpicker('refresh');

        }
        else if(commissiontype == 4){
            //Tiered Commission

            var rowcount = $(".counttiered").length>0?(parseInt($(".counttiered:last").attr("id").match(/\d+/))+1):1;
            var hideBtn = "";
            if($(".counttiered").length==0){

                hideBtn = "display:none;";
                HTML += '<div class="col-sm-12"><hr></div>\
                        <div class="col-sm-12">\
                            <div class="col-sm-4">\
                                <div class="form-group">\
                                    <div class="col-sm-12 pl-sm pr-sm">\
                                        <label class="control-label">Range <span class="mandatoryfield">*</span></label>\
                                    </div>\
                                </div>\
                            </div>\
                            <div class="col-sm-2">\
                                <div class="form-group">\
                                    <div class="col-sm-12 pl-sm pr-sm">\
                                        <label class="control-label">Comm. (%) <span class="mandatoryfield">*</span></label>\
                                    </div>\
                                </div>\
                            </div>\
                            <div class="col-sm-5">\
                                <div class="form-group">\
                                    <div class="col-sm-12">\
                                        <label class="control-label">GST</label>\
                                    </div>\
                                </div>\
                            </div>\
                        </div>';
            }
            HTML += '<div class="col-sm-12 counttiered" id="counttiered'+rowcount+'">\
                        <input type="hidden" name="salescommissiondetailid[]" value="">\
                        <input type="hidden" name="salescommissionmappingid[]" value="">\
                        <div class="col-sm-4">\
                            <div class="col-sm-12 pl-sm pr-sm">\
                                <div class="col-md-6 pr-sm pl-n">\
                                    <div class="form-group pr-md" id="rangestart'+rowcount+'_div">\
                                        <input id="rangestart'+rowcount+'" type="text" name="rangestart[]" value="" class="form-control tiered" placeholder="Start" onkeypress="return isNumber(event)">\
                                    </div>\
                                </div>\
                                <div class="col-md-6 pl-sm pr-n">\
                                    <div class="form-group pl-md" id="rangeend'+rowcount+'_div">\
                                        <input id="rangeend'+rowcount+'" type="text" name="rangeend[]" value="" class="form-control" placeholder="End" onkeypress="return isNumber(event)"> \
                                    </div>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-sm-2">\
                            <div class="form-group" id="tieredcommission'+rowcount+'_div">\
                                <div class="col-sm-12 pl-sm pr-sm">\
                                    <input id="tieredcommission'+rowcount+'" type="text" name="tieredcommission[]" value="" class="form-control" onkeypress="return decimal_number_validation(event, this.value,3,2)"onkeyup="onlypercentage(&quot;tieredcommission'+rowcount+'&quot;)">\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-sm-4">\
                            <div class="form-group mt-sm">\
                                <div class="col-sm-12 pl-sm pr-sm">\
                                    <input type="hidden" id="gst'+rowcount+'" name="tieredgst[]" value="1">\
                                    <div class="col-sm-6 col-xs-8 pl-xs">\
                                        <div class="radio">\
                                            <input type="radio" class="checkGST" name="tieredbasegst'+rowcount+'" id="tieredbasewithgst'+rowcount+'" value="1" checked>\
                                            <label for="tieredbasewithgst'+rowcount+'">With GST</label>\
                                        </div>\
                                    </div>\
                                    <div class="col-sm-6 col-xs-8 pl-n">\
                                        <div class="radio">\
                                            <input type="radio" class="checkGST" name="tieredbasegst'+rowcount+'" id="tieredbasewithoutgst'+rowcount+'" value="0">\
                                            <label for="tieredbasewithoutgst'+rowcount+'">Without GST</label>\
                                        </div>\
                                    </div>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-sm-2 mt-sm">\
                            <button type="button" class="btn btn-default btn-raised remove_btn" onclick="removecommission(&quot;counttiered'+rowcount+'&quot;,'+rowcount+')"  style="padding: 5px 10px;margin-top: 0px;'+hideBtn+'"><i class="fa fa-minus"></i><div class="ripple-container"></div></button>\
                            <button type="button"class="btn btn-default btn-raised add_btn" onclick="addnewcommission()" style="padding: 5px 10px;margin-top: 0px;"> <i class="fa fa-plus"></i> <div class="ripple-container"></div></button>\
                        </div>\
                    </div>';

            $(".remove_btn:first").show();
            $(".add_btn:last").hide();
            if($(".counttiered").length>0){
                $("#counttiered"+(rowcount-1)).after(HTML);
            }else{
                $("#commissiondiv").html(HTML);
                $(".add_btn").hide();
                $(".add_btn:last").show();
            }
        }
    }else{
        $("#commissiondiv").html("");
    }     
}

function removecommission(elementid, rowcount){
    $("#"+elementid).remove();

    $(".add_btn:last").show();
    if ($(".remove_btn:visible").length == 1) {
        $(".remove_btn:first").hide();
    }
}

function onlypercentage(val){
    fieldval = $("#"+val).val();
    if (parseInt(fieldval) < 0) $("#"+val).val(0);
    if (parseInt(fieldval) > 100) $("#"+val).val(100);
}

function resetdata(){  
  
    $("#employee_div").removeClass("has-error is-focused");
    $("#commissiontype_div").removeClass("has-error is-focused");
    $("#flatcommission_div").removeClass("has-error is-focused");

    if(ACTION==0){
        $('#employeeid').val('0');
        $('#commissiontype').val('0');
        $("#commissiondiv").html("");
    }else{
        $('#employeeid').val(EmployeeId);
        $('#commissiontype').val(CommissionType);
    }
    $('.selectpicker').selectpicker('refresh');
    $('html, body').animate({scrollTop:0},'slow');
}

function checkvalidation(submittype='0'){
    
    var employeeid = $("#employeeid").val();
    var commissiontype = $("#commissiontype").val();
    
    PNotify.removeAll();
    var isvalidemployeeid = isvalidcommissiontype = isvalidflatcommission = isvalidproductid = isvalidproductcommission = isvalidduniqueproduct = isvalidmemberid = isvalidmembercommission = isvalidduniquemember = isvalidrangestart = isvalidrangeend = isvalidtieredcommission = isvaliduniquerange = 1;

    if(employeeid == 0){
        $("#employee_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select employee !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidemployeeid = 0;
    }else {
        $("#employee_div").removeClass("has-error is-focused");
    }
    if(commissiontype == 0){
        $("#commissiontype_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select commission type !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidcommissiontype = 0;
    }else {
        $("#commissiontype_div").removeClass("has-error is-focused");
        if(commissiontype==1){
            var flatcommission = $("#flatcommission").val();
            if(flatcommission == ""){
                $("#flatcommission_div").addClass("has-error is-focused");
                new PNotify({title: 'Please enter commission !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidflatcommission = 0;
            }
        }else if(commissiontype==2){
            var productidarr = $("select[name='productid[]']").map(function(){return $(this).val();}).get();
            var productelementarr = $("select[name='productid[]']").map(function(){return $(this).attr("id");}).get();
            var productcommissionarr = $("input[name='productcommission[]']").map(function(){return $(this).val();}).get();
            var productcommissionelearr = $("input[name='productcommission[]']").map(function(){return $(this).attr("id");}).get();

            if(productidarr.length > 0){
                for(var i=0; i<productidarr.length; i++){
                    var id = productelementarr[i].match(/\d+/);
                    if($("#"+productelementarr[i]).val() == 0){
                        $("#product"+id+"_div").addClass("has-error is-focused");
                        new PNotify({title: 'Please select '+(i+1)+' product !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        isvalidproductid = 0;
                    }else {
                        $("#product"+id+"_div").removeClass("has-error is-focused");
                    }
                    if($("#"+productcommissionelearr[i]).val() == ""){
                        $("#productcommission"+id+"_div").addClass("has-error is-focused");
                        new PNotify({title: 'Please enter '+(i+1)+' commission !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        isvalidproductcommission = 0;
                    }else {
                        $("#productcommission"+id+"_div").removeClass("has-error is-focused");
                    }
                }

            }
            var selects_product = $('select[name="productid[]"]');
            var values = [];
            for(j=0;j<selects_product.length;j++) {
                var selectsproduct = selects_product[j];
                var id = selectsproduct.id.match(/\d+/);
                
                if(selectsproduct.value!=0){
                    if(values.indexOf(selectsproduct.value)>-1) {
                        $("#product"+id[0]+"_div").addClass("has-error is-focused");
                        new PNotify({title: 'Please select '+(j+1)+' is different product !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        isvalidduniqueproduct = 0;
                    }
                    else{ 
                        values.push(selectsproduct.value);
                        if($("#productid"+id[0]).val()!=0){
                            $("#product"+id[0]+"_div").removeClass("has-error is-focused");
                        }
                    }
                }
            }
        }else if(commissiontype==3){
            var memberidarr = $("select[name='memberid[]']").map(function(){return $(this).val();}).get();
            var memberelementarr = $("select[name='memberid[]']").map(function(){return $(this).attr("id");}).get();
            var membercommissionarr = $("input[name='membercommission[]']").map(function(){return $(this).val();}).get();
            var membercommissionelearr = $("input[name='membercommission[]']").map(function(){return $(this).attr("id");}).get();

            if(memberidarr.length > 0){
                for(var i=0; i<memberidarr.length; i++){
                    var id = memberelementarr[i].match(/\d+/);
                    if($("#"+memberelementarr[i]).val() == 0){
                        $("#member"+id+"_div").addClass("has-error is-focused");
                        new PNotify({title: 'Please select '+(i+1)+' '+member_label+' !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        isvalidmemberid = 0;
                    }else {
                        $("#member"+id+"_div").removeClass("has-error is-focused");
                    }
                    if($("#"+membercommissionelearr[i]).val() == ""){
                        $("#membercommission"+id+"_div").addClass("has-error is-focused");
                        new PNotify({title: 'Please enter '+(i+1)+' commission !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        isvalidmembercommission = 0;
                    }else {
                        $("#membercommission"+id+"_div").removeClass("has-error is-focused");
                    }
                }
            }
            var selects_member = $('select[name="memberid[]"]');
            var values = [];
            for(j=0;j<selects_member.length;j++) {
                var selectsmember = selects_member[j];
                var id = selectsmember.id.match(/\d+/);
                
                if(selectsmember.value!=0){
                    if(values.indexOf(selectsmember.value)>-1) {
                        $("#member"+id[0]+"_div").addClass("has-error is-focused");
                        new PNotify({title: 'Please select '+(j+1)+' is different '+member_label+' !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        isvalidduniquemember = 0;
                    }
                    else{ 
                        values.push(selectsmember.value);
                        if($("#memberid"+id[0]).val()!=0){
                            $("#member"+id[0]+"_div").removeClass("has-error is-focused");
                        }
                    }
                }
            }
        }else if(commissiontype==4){
            var rangestartarr = $("input[name='rangestart[]']").map(function(){return $(this).val();}).get();
            var rangestartelementarr = $("input[name='rangestart[]']").map(function(){return $(this).attr("id");}).get();
            var rangeendarr = $("input[name='rangeend[]']").map(function(){return $(this).val();}).get();
            var rangeendelementarr = $("input[name='rangeend[]']").map(function(){return $(this).attr("id");}).get();
            var tieredcommissionarr = $("input[name='tieredcommission[]']").map(function(){return $(this).val();}).get();
            var tieredcommissionelementarr = $("input[name='tieredcommission[]']").map(function(){return $(this).attr("id");}).get();

            if(rangestartarr.length > 0){
                for(var i=0; i<rangestartarr.length; i++){
                    var id = rangestartelementarr[i].match(/\d+/);
                    if($("#"+rangestartelementarr[i]).val() == ""){
                        $("#rangestart"+id+"_div").addClass("has-error is-focused");
                        new PNotify({title: 'Please enter '+(i+1)+' start value of range !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        isvalidrangestart = 0;
                    }else{
                        $("#rangestart"+id+"_div").removeClass("has-error is-focused");
                    }
                    if($("#"+rangeendelementarr[i]).val() == ""){
                        $("#rangeend"+id+"_div").addClass("has-error is-focused");
                        new PNotify({title: 'Please select '+(i+1)+' end value of range !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        isvalidrangeend = 0;
                    }else {
                        if(parseInt($("#"+rangeendelementarr[i]).val()) <= parseInt($("#"+rangestartelementarr[i]).val())){
                            $("#rangeend"+id+"_div").addClass("has-error is-focused");
                            new PNotify({title: (i+1)+' range end value required greater than start value !',styling: 'fontawesome',delay: '3000',type: 'error'});
                            isvalidrangeend = 0;
                        }else{
                            $("#rangeend"+id+"_div").removeClass("has-error is-focused");
                        }
                    }
                    if($("#"+tieredcommissionelementarr[i]).val() == ""){
                        $("#tieredcommission"+id+"_div").addClass("has-error is-focused");
                        new PNotify({title: 'Please enter '+(i+1)+' commission !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        isvalidtieredcommission = 0;
                    }else {
                        $("#tieredcommission"+id+"_div").removeClass("has-error is-focused");
                    }
                }
            }
            if(isvalidrangestart == 1 && isvalidrangeend == 1 && rangestartarr.length > 1){
                for(var j=0; j<rangestartarr.length; j++){
                    var jstart = $("#"+rangestartelementarr[j]).val();
                    var jend = $("#"+rangeendelementarr[j]).val();
                    
                    for(var k=0; k<rangestartarr.length; k++){
                        
                        // $("#rangestart"+(k+1)+"_div,#rangeend"+(k+1)+"_div").removeClass("has-error is-focused");
                        if(k != j){
                            var id = rangestartelementarr[k].match(/\d+/);
                            var kstart = $("#"+rangestartelementarr[k]).val();
                            var kend = $("#"+rangeendelementarr[k]).val();
                          
                            if(isvaliduniquerange==1){
                                if(parseInt(kstart) == parseInt(jstart) && parseInt(kend) == parseInt(jend)){
                                    $("#rangestart"+id+"_div,#rangeend"+id+"_div").addClass("has-error is-focused");
                                    new PNotify({title: 'Please enter '+(k+1)+' different range !',styling: 'fontawesome',delay: '3000',type: 'error'});
                                    isvaliduniquerange = 0;
                                }else{
                                    if(parseInt(kstart) >= parseInt(jstart) && parseInt(kstart) <= parseInt(jend)){
                                        $("#rangestart"+id+"_div,#rangeend"+id+"_div").addClass("has-error is-focused");
                                        new PNotify({title: 'Please enter '+(k+1)+' different start range !',styling: 'fontawesome',delay: '3000',type: 'error'});
                                        isvaliduniquerange = 0;
                                    }
                                    if(parseInt(kend) <= parseInt(jend) && parseInt(kend) >= parseInt(jstart)){
                                        $("#rangestart"+id+"_div,#rangeend"+id+"_div").addClass("has-error is-focused");
                                        new PNotify({title: 'Please enter '+(k+1)+' different end range !',styling: 'fontawesome',delay: '3000',type: 'error'});
                                        isvaliduniquerange = 0;
                                    }
                                }
                            }
                            if(isvaliduniquerange==1){
                                $("#rangestart"+id+"_div,#rangeend"+id+"_div").removeClass("has-error is-focused");
                            }
                        }
                    }
                    /* if(isvaliduniquestartrange==0){
                        $("#rangestart"+(j+1)+"_div").addClass("has-error is-focused");
                        new PNotify({title: 'Please enter '+(j+1)+' different start range !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else if(isvaliduniqueendrange == 0){
                        $("#rangeend"+(j+1)+"_div").addClass("has-error is-focused");
                        new PNotify({title: 'Please enter '+(j+1)+' different end range !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else{
                        $("#rangestart"+(j+1)+"_div,#rangeend"+(j+1)+"_div").removeClass("has-error is-focused");
                    } */
                    // isvaliduniquerange = 0;
                }
            }
        }
    }
  
    if(isvalidemployeeid == 1 && isvalidcommissiontype == 1 && isvalidflatcommission == 1 && isvalidproductid == 1 && isvalidproductcommission == 1 && isvalidduniqueproduct == 1 && isvalidmemberid == 1 && isvalidmembercommission == 1 && isvalidduniquemember == 1 && isvalidrangestart == 1 && isvalidrangeend == 1 && isvalidtieredcommission == 1 && isvaliduniquerange == 1){
        var formData = new FormData($('#salescommissionform')[0]);
        if(ACTION==0){
            var uurl = SITE_URL+"sales-commission/add-sales-commission";
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
                    var data = JSON.parse(response);
                    if(data['error']==1){
                        new PNotify({title: "Sales commission successfully added !",styling: 'fontawesome',delay: '3000',type: 'success'});
                        if(submittype==1){
                            resetdata();
                        }else{
                            setTimeout(function() { window.location=SITE_URL+"sales-commission"; }, 1500);
                        }
                    }else if(data['error']==2){
                        new PNotify({title: "Sales commission already exist !",styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else{
                        new PNotify({title: 'Sales commission not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
            var uurl = SITE_URL+"sales-commission/update-sales-commission";
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
                var data = JSON.parse(response);
                if(data['error']==1){
                    new PNotify({title: "Sales commission successfully updated.",styling: 'fontawesome',delay: '3000',type: 'success'});
                    setTimeout(function() { window.location=SITE_URL+"sales-commission"; }, 1500);
                }else if(data['error']==2){
                    new PNotify({title: "Sales commission already exist !",styling: 'fontawesome',delay: '3000',type: 'error'});
                }else{
                    new PNotify({title: 'Sales commission not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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