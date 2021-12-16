$(document).ready(function() {


  getprovince($('#countryid').val());
  getcity($('#provinceid').val());
  

    $('#countryid').on('change', function (e) {
          
      $('#provinceid')
        .find('option')
        .remove()
        .end()
        .append('<option value="">Select Province</option>')
        .val('0')
      ;
      $('#cityid')
        .find('option')
        .remove()
        .end()
        .append('<option value="">Select City</option>')
        .val('0')
      ;
      $('#areaid')
          .find('option')
          .remove()
          .end()
          .append('<option value="0">Select Area</option>')
          .val('0')
      ;
      $('#provinceid').selectpicker('refresh');
      $('#cityid').selectpicker('refresh');
      $('#areaid').selectpicker('refresh');
      getprovince(this.value);
    });
    $('#provinceid').on('change', function (e) {
          
        $('#cityid')
          .find('option')
          .remove()
          .end()
          .append('<option value="">Select City</option>')
          .val('0')
        ;
        $('#areaid')
          .find('option')
          .remove()
          .end()
          .append('<option value="0">Select Area</option>')
          .val('0')
        ;
        $('#cityid').selectpicker('refresh');
        $('#areaid').selectpicker('refresh');
        getcity(this.value);
      });


    });




$("[data-provide='city']").each(function () {
   var $element = $(this);
  
  $element.select2({
    allowClear: true,
    minimumInputLength: 3,    
    placeholder: $element.attr("placeholder"),            
   
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
                        id: item.id
                    }
                })
            };
        }
    },
    initSelection: function (element, callback) {
        var id = $(element).val(); 

        if (id !== "" && id!=='0') {
            $.ajax($element.data("url"), {
                data: {
                    ids: id,
                },
                type: "POST",
                dataType: "json",
            }).done(function (data) {
                callback(data);    
            });
        }else{
          $("#cityid").select2("data", { id: 0, text: "Select City" });
        }
    }
    });

});
$("[data-provide='reference']").each(function () {
   var $element = $(this);
  
  $element.select2({
    allowClear: true,
    minimumInputLength: 3,
    tokenSeparators: [','],
    width: '100%',
    multiple:true,
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
        data: [],
        tags:true,
        initSelection: function (element, callback) {
            var id = $(element).val();        
            if (id !== "") {
                data=[];
                var result = id.split(',');
                for (var prop in result) {

                    keyword = {};
                    keyword['id'] =result[prop]
                    keyword['text'] =result[prop];
                    data.push(keyword);
                }
                callback(data);
            }
        }
    
    
    });

});
if(ACTION==1 && $('#oldlogo').val()!=''){
  var $imageupload = $('.imageupload');
  $imageupload.imageupload({
    url: SITE_URL,
    type: '1',
    allowedFormats: [ 'jpg', 'jpeg', 'png','ico', 'gif']
  });
}else{
  var $imageupload = $('.imageupload');
  $imageupload.imageupload({
    url: SITE_URL,
    type: '0',
    allowedFormats: [ 'jpg', 'jpeg', 'png','ico', 'gif']
  });  
}
$('#gstno').keyup(function() {
    if (this.value != this.value.replace(/[^A-Za-z0-9 \-\']/g, '')) {
        this.value = this.value.replace(/[^A-Za-z0-9 \-\']/g, '');
    }
    this.value = this.value.toUpperCase();
});

function resetdata(){
  
  $("#businessname_div").removeClass("has-error is-focused");
  $("#businessaddress_div").removeClass("has-error is-focused");
  $("#email_div").removeClass("has-error is-focused");
  $("#image_div").removeClass("has-error is-focused");
  $("#gstno_div").removeClass("has-error is-focused");
  $('.cke_inner').css({"border":"none"});
  $("#cityid_div").removeClass("has-error is-focused");
  $('#s2id_cityid > a').css({"background-color":"#FFF","border":"#D2D2D2"});

  $("#countryid").val(countryid);
  $("#provinceid").val(0);
  $("#cityid").val(0);

  getprovince(countryid);

  var $imageupload = $('.imageupload');
  if($('#oldlogo').val()!=''){
    $('#companylogo img').attr('src',MAIN_LOGO_IMAGE_URL+$('#oldlogo').val());
    $imageupload.imageupload({
      url: SITE_URL,
      type: '1'
    });  
  }else{
    $imageupload.imageupload({
      url: SITE_URL,
      type: '0',
      allowedFormats: [ 'jpg', 'jpeg', 'png','ico']
    });
  }
 
  $('#companylogo img').css({"border":"1px solid #f1f1f1"});
  $('html, body').animate({scrollTop:0},'slow');  

} 

function checkvalidation() {

  var businessname = $("#businessname").val().trim();
  var businessaddress = $("#businessaddress").val().trim();
  var email = $("#email").val().trim();
  var logobtn = $("#logobtn").html();
  var gstno = $("#gstno").val().trim();
  var invoicenotes = CKEDITOR.instances['invoicenotes'].getData();
  invoicenotes = encodeURIComponent(invoicenotes);
  CKEDITOR.instances['invoicenotes'].updateElement();
  var countryid = $("#countryid").val();
  var provinceid = $("#provinceid").val();
  var cityid = $("#cityid").val();
  var postcode = $("#postcode").val();
  
  var isvalidbusinessname = isvalidbusinessaddress = isvalidemail =  isvalidlogobtn = isvalidcityid = isvalidcountryid = isvalidprovinceid = isvalidcityid = 0;
  var isvalidinvoicenotes = isvalidgstno = 1;
  
  var regexp = /^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]+)$/;

  if(businessname == ''){
    $("#businessname_div").addClass("has-error is-focused");
    new PNotify({title: "Please enter business name !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidbusinessname = 0;
  }else { 
    if(businessname.length<3){
      $("#businessname_div").addClass("has-error is-focused");
      new PNotify({title: 'Business name require minimum 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidbusinessname = 0;
    }else { 
      isvalidbusinessname = 1;
    }
  }
  if(businessaddress == ''){
    $("#businessaddress_div").addClass("has-error is-focused");
    new PNotify({title: "Please enter business address !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidbusinessaddress = 0;
  }else { 
    if(businessaddress.length<3){
      $("#businessname_div").addClass("has-error is-focused");
      new PNotify({title: 'Business address require minimum 3 characters!',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidbusinessaddress = 0;
    }else { 
      isvalidbusinessaddress = 1;
    }
  }
  if(email == ''){
    $("#email_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter email address !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidemail = 0;
  }
  else if(ValidateEmail(email) == false){
    $("#email_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter valid email address!',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidemail = 0;
  }else { 
    isvalidemail = 1;
  }
  if(logobtn.trim() == 'Select Image'){
    $('#companylogo img').css({"border":"1px solid #FFB9BD"});
    $("#image_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select company logo!',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidlogobtn = 0;
  }else { 
    $("#image_div").removeClass("has-error is-focused");
    isvalidlogobtn = 1;
  }
  if(gstno != ''){
    
    if(gstno.length!=15){
      $("#gstno_div").addClass("has-error is-focused");
      new PNotify({title: 'GST number must be 15 characters!',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidgstno = 0;
    }else if(!regexp.test(gstno)){
      $("#gstno_div").addClass("has-error is-focused");
      new PNotify({title: 'GST number should have at least 1 alphabet and 1 digit!',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidgstno = 0;
    }else { 
      isvalidgstno = 1;
    }
  }
  if(invoicenotes != ''){
    if(invoicenotes.length<3){
      $("#invoicenotes_div").addClass("has-error is-focused");
      $('.cke_inner').css({"background-color":"#FFECED","border":"1px solid #e51c23"});
      new PNotify({title: 'Invoice notes require minimum 3 characters!',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidinvoicenotes = 0;
    }else { 
      $('.cke_inner').css({"border":"none"});
      isvalidinvoicenotes = 1;
    }
  }
  
  if(countryid==0) {
    $("#country_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select country !',styling: 'fontawesome',delay: '3000',type: 'error'});
} else {
    $("#country_div").removeClass("has-error is-focused");
    isvalidcountryid = 1;
}

if(provinceid==0) {
    $("#province_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select province !',styling: 'fontawesome',delay: '3000',type: 'error'});
} else {
    $("#province_div").removeClass("has-error is-focused");
    isvalidprovinceid = 1;
}

if(cityid==0) {
    $("#city_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select city !',styling: 'fontawesome',delay: '3000',type: 'error'});
} else {
    $("#city_div").removeClass("has-error is-focused");
    isvalidcityid = 1;
}

  if(postcode == ''){
      $('#postcode_div').addClass("has-error is-focused");
      new PNotify({title: 'Please enter postcode !',styling: 'fontawesome',delay: '3000',type: 'error'});  
      isvalidpostcode = 0;
  }else if(postcode < 4){
      $('#postcode_div').addClass("has-error is-focused");
      new PNotify({title: 'Please enter minimum 4 digit post number !',styling: 'fontawesome',delay: '3000',type: 'error'});  
      isvalidpostcode = 0;
  }else{
      isvalidpostcode = 1;
  }

  if(isvalidbusinessname == 1 && isvalidbusinessaddress ==1 && isvalidemail == 1 && isvalidgstno ==1 && isvalidlogobtn == 1 && isvalidinvoicenotes == 1 && isvalidcountryid==1 && isvalidprovinceid==1 && isvalidcityid==1 && isvalidpostcode == 1)
  {
    var uurl = SITE_URL+"invoicesetting/updateinvoicesetting";
    var formData = new FormData($('#invoicesettingform')[0]);
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
        // var a = $.parseJSON(response);
          if(response==1){
            new PNotify({title: 'Invoice Settings successfully updated!',styling: 'fontawesome',delay: '3000',type: 'success'});
            setTimeout(function() { window.location.href = SITE_URL+"invoicesetting"; }, 1500);
        }else if(response==2){
          new PNotify({title: 'Uploaded File is not an Image!',styling: 'fontawesome',delay: '3000',type: 'error'});
        }
        else{
          new PNotify({title: 'Invoice Settings not updated!',styling: 'fontawesome',delay: '3000',type: 'error'});
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

