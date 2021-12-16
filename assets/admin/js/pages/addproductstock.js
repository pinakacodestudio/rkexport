$(document).ready(function(e){
  if(ACTION==1){
    var productid = $("#productid").val();
    getproductstockdata(productid);  
  }
});
$('#productid').change(function(){
  var productid = $("#productid").val();
       
  if(productid!=0){
    getproductstockdata(productid); 
  }else{
    $('#stock_div').html('');
    $('#actionbtn').hide();
  }
  
});
function getproductstockdata(productid){

  var uurl = SITE_URL+"productstock/getproductstock";
      
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {productid:productid},
    dataType: 'json',
    //async: false,
    beforeSend: function(){
      $('.mask').show();
      $('#loader').show();
    },
    success: function(response){
      $.html = '';
      if(response.length>0){
        if(response[0]['universal']==1){
          $('#universalprice').val(1);
          $.html = '<label class="col-sm-3 control-label">Stock <span class="mandatoryfield">*</span></label> \
                      <div class="col-md-8"> \
                        <div class="col-md-12 p-n"> \
                          <input id="productstockbycarmodelid1" type="hidden" name="productstockbycarmodelid[]" class="form-control" readonly value="'+response[0]['productstockbycarmodelid']+'"> \
                          <input id="carmodelid1" type="hidden" name="carmodelid[]" class="form-control" readonly value="'+response[0]['carmodelid']+'"> \
                          <div class="col-sm-4 pl-n"> \
                            <div class="form-group"> \
                              <div class="checkbox col-sm-12 col-xs-6"> \
                                <input type="checkbox" name="isuniversal" id="isuniversal" checked disabled> \
                                <label for="isuniversal" style="font-size: 14px;">Universal</label> \
                              </div> \
                            </div> \
                          </div> \
                          <div class="col-md-4 pl-n"> \
                            <div class="form-group" id="minimumstock1_div">\
                              <div class="col-sm-12"> \
                                <input id="minimumstock1" type="text" name="minimumstock[]" class="form-control" onkeypress="return isNumber(event)" placeholder="Minimum Stock" value="'+response[0]['minimumstock']+'" maxlength="10"> \
                              </div> \
                            </div> \
                          </div> \
                          <div class="col-md-4 pl-n"> \
                            <div class="form-group" id="currentstock1_div"> \
                              <div class="col-sm-12"> \
                                <input id="currentstock1" type="text" name="currentstock[]" class="form-control" onkeypress="return isNumber(event)" placeholder="Current Stock" value="'+response[0]['currentstock']+'" maxlength="10"> \
                              </div> \
                            </div> \
                          </div> \
                        </div> \
                      </div>';
          $('#stock_div').html($.html);
        }else{
          $('#universalprice').val(0);
          $.html += '<label class="col-sm-3 control-label">Stock <span class="mandatoryfield">*</span></label> \
                          <div class="col-md-8">';
          for(var i = 0; i < response.length; i++) {
              $.html += '<div class="col-md-12 p-n"> \
                            <input id="productstockbycarmodelid'+(i+1)+'" type="hidden" name="productstockbycarmodelid[]" class="form-control" readonly value="'+response[i]['productstockbycarmodelid']+'"> \
                            <input id="carmodelid'+(i+1)+'" type="hidden" name="carmodelid[]" class="form-control" readonly value="'+response[i]['carmodelid']+'"> \
                            <div class="col-sm-4 pl-n"> \
                              <div class="form-group" id="carmodelid'+(i+1)+'_div"> \
                                <div class="col-sm-12"> \
                                  <input type="text" class="form-control" readonly value="'+response[i]['carmodel']+'"> \
                                </div> \
                              </div> \
                            </div> \
                            <div class="col-md-4 pl-n"> \
                              <div class="form-group" id="minimumstock'+(i+1)+'_div">\
                                <div class="col-sm-12"> \
                                  <input id="minimumstock'+(i+1)+'" type="text" name="minimumstock[]" class="form-control" onkeypress="return isNumber(event)" placeholder="Minimum Stock" value="'+response[i]['minimumstock']+'" maxlength="10"> \
                                </div> \
                              </div> \
                            </div> \
                            <div class="col-md-4 pl-n"> \
                              <div class="form-group" id="currentstock'+(i+1)+'_div"> \
                                <div class="col-sm-12"> \
                                  <input id="currentstock'+(i+1)+'" type="text" name="currentstock[]" class="form-control" onkeypress="return isNumber(event)" placeholder="Current Stock" value="'+response[i]['currentstock']+'" maxlength="10"> \
                                </div> \
                              </div> \
                            </div> \
                          </div>';
          }
          $.html += '</div></div>';
          $('#stock_div').html($.html);
        }
        $('#actionbtn').show();
      }else{
        $('#stock_div').html('');
        $('#actionbtn').hide();
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

function resetdata(){

  $("#productid_div").removeClass("has-error is-focused");

  if(ACTION==1){
    
  }else{
    $('#productid').val('0');
    $('#stock_div').html('');
    $('#actionbtn').hide();
  }
  $('.selectpicker').selectpicker('refresh');  
  $('html, body').animate({scrollTop:0},'slow');
  
}
function checkvalidation(){
  
  var productid = $("#productid").val();
  var minimumstock = $("input[name='minimumstock[]']").map(function(){return $(this).val();}).get();
  var currentstock = $("input[name='currentstock[]']").map(function(){return $(this).val();}).get();
  
  var isvalidproductid = 0;
  var isvalidminimumstock = isvalidcurrentstock = 1 ;
  
  PNotify.removeAll();
  if(productid == 0){
    $("#productid_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select product !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidproductid = 0;
  }else{ 
    $("#productid_div").removeClass("has-error is-focused");
    isvalidproductid = 1;
  }

  for (var i = 0; i < minimumstock.length; i++) {
    if(minimumstock[i]==''){
      $("#minimumstock"+(i+1)+"_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter '+(i+1)+' minimum stock !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidminimumstock = 0;
    }else{
      $("#minimumstock"+(i+1)+"_div").removeClass("has-error is-focused");
      
    }
    if(currentstock[i]==''){
      $("#currentstock"+(i+1)+"_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter '+(i+1)+' current stock !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidcurrentstock = 0;
    }else{
      $("#currentstock"+(i+1)+"_div").removeClass("has-error is-focused");
      
    }
  }

  if(isvalidproductid == 1 && isvalidminimumstock == 1 && isvalidcurrentstock == 1){

    var formData = new FormData($('#productstockform')[0]);
    if(ACTION==0){
      var uurl = SITE_URL+"productstock/addproductstock";
      
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
            new PNotify({title: "Product stock successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
            resetdata();
          }else{
            new PNotify({title: 'Product stock not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
      var uurl = SITE_URL+"productstock/addproductstock";
      
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
              new PNotify({title: "Product stock successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"productstock"; }, 1500);
          }else{
              new PNotify({title: 'Product stock not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

