$(document).ready(function(){

    $('#purchasedate').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        todayBtn:"linked",
        clearBtn: true,
        endDate: new Date()
    });

    $("[data-provide='companyname']").each(function () {
        var $element = $(this);
       
        $element.select2({    
            allowClear: true,
            minimumInputLength: 1,     
            width: '100%',  
            placeholder: $element.attr("placeholder"),         
            createSearchChoice: function(term, data) {
            if ($(data).filter(function() {
                return this.text.localeCompare(term) === 0;
                }).length === 0) {
                    return {
                        id: term,
                        text: term
                    };
                }
            },
            ajax: {
            url: $element.data("url"),
            dataType: 'json',
            type: "POST",
            quietMillis: 50,
            data: function (term) {
                return {
                    term: term,
                };
            },
            results: function (data) {            
                return {
                results: $.map(data, function (item) {
                    return {
                        text: item.text,                        
                        id: item.text
                    }
                })
                };
            }
            },
            initSelection: function (element, callback) {
            var id = $(element).val();        
            if (id !== "" && id != 0) {
                /* $.ajax($element.data("url"), {
                    data: {
                        ids: id,
                    },
                    type: "POST",
                    dataType: "json",
                }).done(function (data) {                
                    callback(data);
                }); */
                $("#companyname").select2("data", { id: id, text: id });
            }else{
                $("#companyname").select2("data", { id: 0, text: "Enter Company Name" });
            }
            }
        });
    });
});
function resetdata() {
    $("#companyname_div").removeClass("has-error is-focused");
    $("#machinename_div").removeClass("has-error is-focused");
    $("#modelno_div").removeClass("has-error is-focused");
    $("#unitconsumption_div").removeClass("has-error is-focused");
    $("#minimumcapacity").removeClass("has-error is-focused");
    $("#maximumcapacity_div").removeClass("has-error is-focused");
    $('#s2id_companyname > a').css({"background-color":"#fcfcfc","border":"1px solid #e3e3e3"});

    if(ACTION==0){
        $('#machinename,#modelno,#unitconsumption,#noofhoursused,#minimumcapacity,#maximumcapacity').val("");
        
        $("#companyname").select2("val", "");

        $('#yes').prop("checked", true);
    }
    $('html, body').animate({scrollTop:0},'slow');  
}
function checkvalidation(addtype=0) {
   
    var companyname = $('#companyname').val().trim();
    var machinename = $('#machinename').val().trim();
    var modelno = $('#modelno').val().trim();
    var unitconsumption = $('#unitconsumption').val().trim();
    var minimumcapacity = $('#minimumcapacity').val().trim();
    var maximumcapacity = $('#maximumcapacity').val().trim();

    var isvalidcompanyname = isvalidmachinename = isvalidmodelno = isvalidunitconsumption = isvalidminimumcapacity = isvalidmaximumcapacity = 1;
    
    PNotify.removeAll();
    if(companyname.trim()==0) {
        $("#companyname_div").addClass("has-error is-focused");
        $('#s2id_companyname > a').css({"background-color":"#FFECED","border":"1px solid #FFB9BD"});
        new PNotify({title: 'Please enter company name !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidcompanyname = 0;
    } else if(companyname.length < 3){
        $("#companyname_div").addClass("has-error is-focused");
        $('#s2id_companyname > a').css({"background-color":"#FFECED","border":"1px solid #FFB9BD"});
        new PNotify({title: 'Company name required minimum 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidcompanyname = 0;
    } else {
        $("#companyname_div").removeClass("has-error is-focused");
        $('#s2id_companyname > a').css({"background-color":"#fcfcfc","border":"1px solid #e3e3e3"});
    }
    if(machinename=="") {
        $("#machinename_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter machine name !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidmachinename = 0;
    } else if(machinename.length < 3){
        $("#machinename_div").addClass("has-error is-focused");
        new PNotify({title: 'Machine name required minimum 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidmachinename = 0;
    } else {
        $("#machinename_div").removeClass("has-error is-focused");
    }
    if(modelno=="") {
        $("#modelno_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter model no. !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidmodelno = 0;
    } else {
        $("#modelno_div").removeClass("has-error is-focused");
    }    
    if(unitconsumption=="") {
        $("#unitconsumption_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter power consumption in units !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidunitconsumption = 0;
    } else {
        $("#unitconsumption_div").removeClass("has-error is-focused");
    }  
    if(minimumcapacity=="") {
        $("#minimumcapacity_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter minimum value of production capacity !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidminimumcapacity = 0;
    } else {
        $("#minimumcapacity_div").removeClass("has-error is-focused");
    }  
    if(maximumcapacity=="") {
        $("#maximumcapacity_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter maximum value of production capacity !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidmaximumcapacity = 0;
    } else if (parseInt(maximumcapacity) < parseInt(minimumcapacity)){
        $("#maximumcapacity_div").addClass("has-error is-focused");
        new PNotify({title: 'Maximum value is not less than minimum value !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidmaximumcapacity = 0;
    }  else {
        $("#maximumcapacity_div").removeClass("has-error is-focused");
    }

    if(isvalidcompanyname == 1 && isvalidmachinename == 1 && isvalidmodelno == 1 && isvalidunitconsumption == 1 && isvalidminimumcapacity == 1 && isvalidmaximumcapacity == 1){
        var formData = new FormData($('#machine-form')[0]);
        if(ACTION == 0){ // INSERT
            var baseurl = SITE_URL + 'machine/machine-add';
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
                        new PNotify({title: 'Machine successfully added.',styling: 'fontawesome',delay: '3000',type: 'success'});
                        if(addtype==1){
                            resetdata();
                        }else{
                            setTimeout(function() { window.location = SITE_URL + "machine";}, 500);
                        }
                    }else if(data['error']==2){
                        new PNotify({title: 'Machine already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else if(data['error']==3){
                        new PNotify({title: data['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else{
                        new PNotify({title: 'Machine not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
        } else {
                 // MODIFY
            var baseurl = SITE_URL + 'machine/update-machine';
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
                    if(data['error']==1){
                        new PNotify({title: 'Machine successfully updated.',styling: 'fontawesome',delay: '3000',type: 'success'});
                    
                        setTimeout(function() { window.location = SITE_URL + "machine";}, 500);
                    }else if(data['error']==2){
                        new PNotify({title: 'Machine already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else if(data['error']==3){
                        new PNotify({title: data['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else{
                        new PNotify({title: 'Machine not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
