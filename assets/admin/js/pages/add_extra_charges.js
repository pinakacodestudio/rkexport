$(document).ready(function() {
    $("#defaultamount").on('keyup',function(e){
        if ($("#percentage").is(":checked")) {
            if(this.value > 100){
                $(this).val("100.00");
            }
        }
    });
    $(function() {
      $('input:radio[name="amounttype"]').change(function() {
          if ($(this).val() == '0') {
              $("#defaultamount").val("");
          }
      });
    });
    $("#channelid").change(function(){
        getmembers();
        gethsncode();
    });
    if(ACTION==1 && (CHANNELID!=0 || CHANNELID!="")){
        getmembers();
    }
    if(ACTION==1 && (CHANNELID==0 || MEMBERID!=0)){
      gethsncode();
    }
    $("#memberid").change(function(){
      gethsncode();
  });
});
function getmembers(){
    
    var channelid = $("#channelid").val();
  
    $('#memberid')
        .find('option')
        .remove()
        .end()
        .append('<option value="0">Select '+Member_label+'</option>')
        .val('whatever')
        ;
    if(channelid!=""){
        var uurl = SITE_URL+"member/getmembers";
        $.ajax({
            url: uurl,
            type: 'POST',
            data: {channelid:channelid,vendor:1},
            dataType: 'json',
            async: false,
            success: function(response){
        
                for(var i = 0; i < response.length; i++) {
            
                    $('#memberid').append($('<option>', { 
                    value: response[i]['id'],
                    text : ucwords(response[i]['name'])
                    }));
            
                }
                if(MEMBERID!=0){
                    $('#memberid').val(MEMBERID);
                }
            },
            error: function(xhr) {
            //alert(xhr.responseText);
            },
        });
    }
    $('#memberid').selectpicker('refresh');
}
function gethsncode(){
    
  $('#hsncodeid')
    .find('option')
    .remove()
    .end()
    .append('<option value="0">Select HSN Code</option>')
    .val('0')
  ;
  $('#hsncodeid').selectpicker('refresh');

  var channelid = $("#channelid").val();
  var memberid = $("#memberid").val();
  
  if(channelid==0 || memberid!=0){
    var uurl = SITE_URL+"hsn-code/getHsnCodeByChannelOrMemberID";
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {channelid:channelid,memberid:memberid},
      dataType: 'json',
      async: false,
      success: function(response){

        for(var i = 0; i < response.length; i++) {
    
          $('#hsncodeid').append($('<option>', { 
            value: response[i]['id'],
            text : ucwords(response[i]['hsncode'])
          }));
        }
        if(HSNCODEID!=0){
          $('#hsncodeid').val(HSNCODEID);
        }
      },
      error: function(xhr) {
        //alert(xhr.responseText);
      },
    });
  }
  $('#hsncodeid').selectpicker('refresh');
}
function resetdata(){

  $("#name_div").removeClass("has-error is-focused");
  $("#defaultamount_div").removeClass("has-error is-focused");
  $("#member_div").removeClass("has-error is-focused");

  if(ACTION==0){
      
    $('#name').val('');
    $('#defaultamount').val('');
    $('#channelid').val('');
    $('#memberid').val('0');
    $('#hsncodeid').val('0');
    $('#amount').prop("checked", true);
    $('#defaultamount').val('');
    $('#yes').prop("checked", true);
    $('#name').focus();
    
    $('.selectpicker').selectpicker('refresh');
  }
  $('html, body').animate({scrollTop:0},'slow');
}
function checkvalidation(){
  
  var name = $("#name").val().trim();
  var defaultamount = $("#defaultamount").val();
  var channelid = $("#channelid").val();
  var memberid = $("#memberid").val();
  var type = $('input:radio[name="amounttype"]:checked').val();
 
  var isvalidname = isvaliddefaultamount = isvalidmemberid = 1;
  
  PNotify.removeAll();
  if(name == ''){
    $("#name_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter name !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidname = 0;
  }else { 
    if(name.length<2){
      $("#name_div").addClass("has-error is-focused");
      new PNotify({title: 'Name require minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidname = 0;
    }else{
        $("#name_div").removeClass("has-error is-focused");
    }
  }
  if(defaultamount == '' || (defaultamount <= 0 && type==0)){
    $("#defaultamount_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter default amount !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvaliddefaultamount = 0;
  }else{
    $("#defaultamount_div").removeClass("has-error is-focused");
  }
  if(channelid!="" && channelid!="0"){
    if(memberid == 0){
      $("#member_div").addClass("has-error is-focused");
      new PNotify({title: 'Please select '+member_label+' !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidmemberid = 0;
    }else{
      $("#member_div").removeClass("has-error is-focused");
    }
  }else{
    $("#member_div").removeClass("has-error is-focused");
  }
  
  if(isvalidname == 1 && isvaliddefaultamount == 1 && isvalidmemberid == 1){

    var formData = new FormData($('#extrachargesform')[0]);
    if(ACTION==0){
      var uurl = SITE_URL+"extra-charges/add-extra-charges";
      
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
                new PNotify({title: "Extra charges successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
                resetdata();
            }else if(data['error']==2){
                new PNotify({title: 'Extra charges already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else if(data['error']==3){
                new PNotify({title: data['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
            }else{
                new PNotify({title: 'Extra charges not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
      var uurl = SITE_URL+"extra-charges/update-extra-charges";
      
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
              new PNotify({title: "Extra charges successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"extra-charges"; }, 1500);
            }else if(data['error']==2){
                new PNotify({title: 'Extra charges already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else if(data['error']==3){
                new PNotify({title: data['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
            }else{
                new PNotify({title: 'Extra charges not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

