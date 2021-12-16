function resetdata(){
  
  $("#productid_div").removeClass("has-error is-focused");
  $("#question_div").removeClass("has-error is-focused");
  $("#answer_div").removeClass("has-error is-focused");
  $('#cke_question').css({"border":"1px solid #b6b6b6"});
  $('#cke_answer').css({"border":"1px solid #b6b6b6"});

  if(ACTION==1){
   
  }else{
    $('#productid').val('');
    $('#question').val('');
    $('#cke_answer').css({"background-color":"#FFF","border":"1px solid #D2D2D2"});
    CKEDITOR.instances['answer'].setData("");
  }
  $('#productid').selectpicker('refresh');
  $('html, body').animate({scrollTop:0},'slow');  

} 

function checkvalidation() {

  var productid = $("#productid").val() == undefined ? '' : $("#productid").val();
  var question = $("#question").val()
  //question = encodeURIComponent(question);
  
  var answer = CKEDITOR.instances['answer'].getData();
  //answer = encodeURIComponent(answer);
  CKEDITOR.instances['answer'].updateElement();

  var isvalidquestion = isvalidanswer = 0;
  var isvalidproductid = 1;
 
  /*if(productid == 0 || productid == ''){
    $("#productid_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter product !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidproductid = 0;
  }else { 
    isvalidproductid = 1;
  }*/
 
  if(question.trim() ==''){
    $("#question_div").addClass("has-error is-focused");
    $('#cke_question').css({"background-color":"#FFECED","border":"1px solid #e51c23"});
    new PNotify({title: "Please enter question !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidquestion = 0;
  }else{
    isvalidquestion = 1;
    $("#question_div").removeClass("has-error is-focused");
    $('#cke_question').css({"border":"1px solid #b6b6b6"});
  }

  if(answer.trim() ==''){
    $("#answer_div").addClass("has-error is-focused");
    $('#cke_answer').css({"background-color":"#FFECED","border":"1px solid #e51c23"});
    new PNotify({title: "Please enter answer !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidanswer = 0;
  }else{
    isvalidanswer = 1;
    $("#answer_div").removeClass("has-error is-focused");
    $('#cke_answer').css({"border":"1px solid #b6b6b6"});
  }
  
  if(isvalidproductid == 1 && isvalidquestion ==1 && isvalidanswer == 1){

    var formData = new FormData($('#faqform')[0]);
    if(ACTION == 0){    
      var uurl = SITE_URL+"faq/addfaq";
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
            new PNotify({title: "FAQ successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
            resetdata();
          }else{
            new PNotify({title: "FAQ not added !",styling: 'fontawesome',delay: '3000',type: 'error'});
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
      var uurl = SITE_URL+"faq/updatefaq";
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
            new PNotify({title: "FAQ successfully updated.",styling: 'fontawesome',delay: '3000',type: 'success'});
            setTimeout(function() { window.location=SITE_URL+"faq"; }, 1500);
          }else{
            new PNotify({title: "FAQ not updated !",styling: 'fontawesome',delay: '3000',type: 'error'});
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

