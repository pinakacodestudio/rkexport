$(document).ready(function(){

  /* $('#todolist').on('change',function(){
    // alert();
    alert($(this).val());
    // // $('#s2id_todolist > input').val(null);
    // $(this).val(null);
  }) */
  
  

  $("[data-provide='todolist']").each(function () {
    var $element = $(this);
  
    $element.select2({    
      allowClear: true,
      minimumInputLength: 3,     
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
        if (id !== "" && id != 0) {
          $.ajax($element.data("url"), {
              data: {
                  ids: id,
              },
              type: "POST",
              dataType: "json",
          }).done(function (data) {                
              callback(data);
          });
        }
      }
    });
});
});
$(function() {
  var tabindex = 1;
  $('input,select,textarea,a,button,radio,checkbox').each(function() {
      if (this.type != "hidden") {
        var $input = $(this);
        $input.attr("tabindex", tabindex);
        tabindex++;
      }
  });
});

function resetdata(){  
  $("#employee_div").removeClass("has-error is-focused");    
  $("#todolist_div").removeClass("has-error is-focused");
  // $("#employeeid").val(0);
  $('#s2id_todolist > ul').css({"background-color":"#FFF","border":"1px solid #cccccc"});
  if(ACTION==0){
      $("#employeeid").val("0");       
      $("#todolist").val("");
      $('.selectpicker').selectpicker('refresh');
  }   
  $('html, body').animate({scrollTop:0},'slow');
}

function checkvalidation(){



  // alert($("#todolist").val());
  var employeeid=$("#employeeid").val();
  var todolist=$("#todolist").val();
  var isvalidemployeeid = isvalidtodolist = 0 ;
  PNotify.removeAll();
  if(employeeid == 0){
      $("#employee_div").addClass("has-error is-focused");
      new PNotify({title: 'Please select employee !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidemployeeid = 0;
  }else {
      isvalidemployeeid = 1;
  }
 
  if(todolist == ''){
      $("#todolist_div").addClass("has-error is-focused");
      $('#s2id_todolist > ul').css({"background-color":"#FFECED","border":"1px solid #FFB9BD"});
      new PNotify({title: 'Please enter to do list !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidtodolist = 0;
  }else {
    $('#s2id_todolist > ul').css({"background-color":"#FFF","border":"1px solid #cccccc"});
      isvalidtodolist = 1;
  }

  if(isvalidemployeeid==1 && isvalidtodolist==1)
  {  

      var formData = new FormData($('#todolistform')[0]);
      if(ACTION==0){
          var uurl = SITE_URL+"todo-list/add-todo-list";
          
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
              new PNotify({title: "To Do List successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"todo-list"; }, 1500);
              }else{
              new PNotify({title: 'To Do List not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
          var uurl = SITE_URL+"todo-list/update-todo-list";
          
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
                  new PNotify({title: "To Do List successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
                  setTimeout(function() { window.location=SITE_URL+"todo-list"; }, 1500);
              }else{
                  new PNotify({title: 'To Do List not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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