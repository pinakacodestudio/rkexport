var mappingcount = 0;
$(document).ready(function(){

  if(ACTION==1){
    var productid = $("#productid").val();
    getcarmodel(productid);
    getproductpricedata(productid,carmodelid);
    $('option:not(:selected)').attr('disabled', true);
  }
  
});
$('#productid').change(function(){
  var productid = $("#productid").val();
  $('#carmodelid')
      .find('option')
      .remove()
      .end()
      .append('<option value="0">Select Car Model</option>')
      .val('whatever')
  ;
  $('#mappingdata').html('');
       
  if(productid!=0){
    getcarmodel(productid);
    var carmodelid = $("#carmodelid").val();
    if($("#carmodelid option").length==1 && carmodelid==0){
      getproductpricedata(this.value);
    }
  }
  $('#carmodelid').selectpicker('refresh');
});
$('#carmodelid').change(function(){
  var productid = $("#productid").val();
  var carmodelid = $("#carmodelid").val();
  getproductpricedata(productid,carmodelid);
  //$('option:not(:selected)').attr('disabled', true);
});
function getcarmodel(productid){
  var succeed = 0;
  var uurl = SITE_URL+"carmodel/getActiveProductCarmodel";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {productid:productid},
    dataType: 'json',
    async: false,
    success: function(response){
      $('#carmodelid')
      .find('option')
      .remove()
      .end()
      .append('<option value="0">Select Car Model</option>')
      .val('whatever')
      ;
      
      for(var i = 0; i < response.length; i++) {

        $('#carmodelid').append($('<option>', { 
          value: response[i]['id'],
          text : response[i]['name']
        }));

      }
      $('#carmodelid').val(carmodelid);
      $('#carmodelid').selectpicker('refresh');
    },
    complete: function(){
      succeed = 1;
    },
    error: function(xhr) {
          //alert(xhr.responseText);
          succeed = 0;
        },
      });
  return succeed;
}
/*$('#productid').change(function(){
  getproductpricedata(this.value);
});*/
function getproductpricedata(productid,carmodelid=''){
  if(productid!=0){
    var uurl = SITE_URL+"Product/getProductPriceData";
      
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {productid:productid,carmodelid:carmodelid},
      datatype:'json',
      //async: false,
      beforeSend: function(){
        $('.mask').show();
        $('#loader').show();
      },
      success: function(response){
        var response = JSON.parse(response);
        if(response['productdata'].length>0){
          
          $.html = '<input type="hidden" value="" id="removeproductmappingid" name="removeproductmappingid">';
          var productdata = response['productdata'];

          mappingcount = response['productdata'].length;
          for (var i = 0; i < productdata.length; i++) {

            $.html += '<input type="hidden" value="'+productdata[i]['carmodelid']+'" name="carmodelid[]">';
            $.html += '<input type="hidden" value="'+productdata[i]['price']+'" name="carmodelprice[]">';
            
            mappingdata = response['mappingdata'][productdata[i]['productmappingid']];

            if(mappingdata.length!=0){

              for (var j = 0; j < mappingdata.length; j++) {
                $.html += '<div id="mappingcount'+(j+1)+'"><input type="hidden" value="'+mappingdata[j]['id']+'" id="productmappingid'+(j+1)+productdata[i]['carmodelid']+'" name="productmappingid'+productdata[i]['carmodelid']+'[]">';
                if(productdata[i]['carmodel']!=''){
                  $.html += '<div class="col-sm-12">';
                  $.html +=  '<div class="col-md-3">';
                                if(j==0){
                                  $.html +='<div class="form-group"> \
                                    <div class="col-md-12"> \
                                      <label class="control-label">Car Model</label> \
                                      <input type="text" class="form-control" disabled value="'+productdata[i]['carmodel']+'"> \
                                      <span class="mandatoryfield">Note: Product Price : '+productdata[i]['price']+'</span> \
                                    </div> \
                                  </div>';
                                }
                                
                              $.html += '</div>';
                }else{
                  $.html += '<div class="col-sm-11 col-md-offset-1">';
                }
                $.html += '<div class="col-md-'+(productdata[i]['carmodel']!=''?3:3)+'"> \
                            <div class="form-group" id="mappingname'+productdata[i]['carmodelid']+(j+1)+'_div"> \
                              <div class="col-md-12"> \
                                <label class="control-label">Mapping Name <span class="mandatoryfield">*</span></label> \
                                <input type="text" class="form-control" name="mappingname'+productdata[i]['carmodelid']+'[]" id="mappingname'+productdata[i]['carmodelid']+'" value="'+mappingdata[j]['mappingname']+'">';
                                if(j==0 && productdata[i]['carmodel']==''){
                                  $.html += '<span class="mandatoryfield">Note: Product Price : '+productdata[i]['price']+'</span>';
                                }
                              $.html += '</div> \
                            </div> \
                          </div> \
                          <div class="col-md-'+(productdata[i]['carmodel']!=''?3:3)+'"> \
                            <div class="form-group" id="barcode'+productdata[i]['carmodelid']+(j+1)+'_div"> \
                              <div class="col-md-12"> \
                                <label class="control-label">Bar Code <span class="mandatoryfield">*</span></label> \
                                <input type="text" class="form-control" name="barcode'+productdata[i]['carmodelid']+'[]" id="barcode'+productdata[i]['carmodelid']+'" maxlength="25" value="'+mappingdata[j]['barcode']+'"> \
                              </div> \
                            </div> \
                          </div> \
                          <div class="col-md-'+(productdata[i]['carmodel']!=''?2:3)+'"> \
                            <div class="form-group" id="price'+productdata[i]['carmodelid']+(j+1)+'_div"> \
                              <div class="col-md-12"> \
                                <label class="control-label">Price <span class="mandatoryfield">*</span></label> \
                                <input type="text" class="form-control" name="price'+productdata[i]['carmodelid']+'[]" id="price'+productdata[i]['carmodelid']+'" onkeypress="return decimal_number_validation(event,this.value,8,3)" maxlength="25" value="'+mappingdata[j]['price']+'"> \
                              </div> \
                            </div> \
                          </div><div class="col-md-1">';
                          if(j==0){
                            $.html += '<button type="button" class="btn btn-default btn-raised" id="btn'+(j+1)+'" onclick="addnewproductmapping(\''+productdata[i]['carmodel']+'\','+productdata[i]['carmodelid']+',1)" style="margin-top: 35px;"><i class="material-icons">plus_one</i><div class="ripple-container"></div></button>';
                          }else if(j!=0) {
                            $.html += '<button type="button" class="btn btn-default btn-raised" onclick="removeproductmapping('+(j+1)+','+productdata[i]['carmodelid']+')" style="margin-top: 35px;"><i class="fa fa-minus"></i><div class="ripple-container"></div></button>';
                          }
                          $.html += '</div></div></div> \
                          <div id="moremapping'+productdata[i]['carmodelid']+'_div"></div>';
              }
            }else{
              if(productdata[i]['carmodel']!=''){

                  $.html += '<div class="col-sm-12">';
                  $.html +=  '<div class="col-md-3"> \
                                <div class="form-group"> \
                                  <div class="col-md-12"> \
                                    <label class="control-label">Car Model</label> \
                                    <input type="text" class="form-control" disabled value="'+productdata[i]['carmodel']+'"> \
                                    <span class="mandatoryfield">Note: Product Price : '+productdata[i]['price']+'</span> \
                                  </div> \
                                </div> \
                              </div>';
                }else{
                  $.html += '<div class="col-sm-11 col-md-offset-1">';
                }
                $.html += '<div class="col-md-'+(productdata[i]['carmodel']!=''?3:3)+'"> \
                            <div class="form-group" id="mappingname'+productdata[i]['carmodelid']+'1_div"> \
                              <div class="col-md-12"> \
                                <label class="control-label">Mapping Name <span class="mandatoryfield">*</span></label> \
                                <input type="text" class="form-control" name="mappingname'+productdata[i]['carmodelid']+'[]" id="mappingname'+productdata[i]['carmodelid']+'" value="">';
                                if(productdata[i]['carmodel']==''){
                                  $.html += '<span class="mandatoryfield">Note: Product Price : '+productdata[i]['price']+'</span>';
                                }
                              $.html += '</div> \
                            </div> \
                          </div> \
                          <div class="col-md-'+(productdata[i]['carmodel']!=''?3:3)+'"> \
                            <div class="form-group" id="barcode'+productdata[i]['carmodelid']+'1_div"> \
                              <div class="col-md-12"> \
                                <label class="control-label">Bar Code <span class="mandatoryfield">*</span></label> \
                                <input type="text" class="form-control" name="barcode'+productdata[i]['carmodelid']+'[]" id="barcode'+productdata[i]['carmodelid']+'" maxlength="25" value=""> \
                              </div> \
                            </div> \
                          </div> \
                          <div class="col-md-'+(productdata[i]['carmodel']!=''?2:3)+'"> \
                            <div class="form-group" id="price'+productdata[i]['carmodelid']+'1_div"> \
                              <div class="col-md-12"> \
                                <label class="control-label">Price <span class="mandatoryfield">*</span></label> \
                                <input type="text" class="form-control" name="price'+productdata[i]['carmodelid']+'[]" id="price'+productdata[i]['carmodelid']+'" onkeypress="return decimal_number_validation(event,this.value,8,3)" maxlength="25" value="'+productdata[i]['price']+'"> \
                              </div> \
                            </div> \
                          </div> \
                          <button type="button" class="btn btn-default btn-raised" onclick="addnewproductmapping(\''+productdata[i]['carmodel']+'\','+productdata[i]['carmodelid']+')" style="margin-top: 35px;"><i class="material-icons">plus_one</i><div class="ripple-container"></div></button> \
                          </div> \
                          <div id="moremapping'+productdata[i]['carmodelid']+'_div"></div>';
            }
            
            
          }
          $('#mappingdata').html($.html);
        }else{
          new PNotify({title: "Product data not get!",styling: 'fontawesome',delay: '3000',type: 'error'});
          $("#product_div").addClass("has-error is-focused");
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
}
function addnewproductmapping(carmodel,carmodelid,current){
    
  var mapping = $("input[name='mappingname"+carmodelid+"[]']").map(function(){return $(this).val();}).get();
  mappingcount = ++mapping.length;
  //$('#btn'+current).html('<i class="fa fa-minus"></i><div class="ripple-container"></div>');
  //$("#btn"+current).attr("onclick","removeproductmapping("+current+","+carmodelid+")");
  $.html = '<div id="mappingcount'+mappingcount+'">';
              //$.html += '<input type="hidden" value="'+carmodelid+'" name="carmodelid[]">';
            if(carmodel!=''){
              $.html += '<div class="col-sm-12">';
              $.html +=  '<div class="col-md-3"> \
                          </div>';
            }else{
              $.html += '<div class="col-sm-11 col-md-offset-1">';
            }
            $.html += '<div class="col-md-'+(carmodel!=''?3:3)+'"> \
                        <div class="form-group" id="mappingname'+carmodelid+mappingcount+'_div"> \
                          <div class="col-md-12"> \
                            <label class="control-label">Mapping Name <span class="mandatoryfield">*</span></label> \
                            <input type="text" class="form-control" name="mappingname'+carmodelid+'[]" id="mappingname'+carmodel+'" value=""> \
                          </div> \
                        </div> \
                      </div> \
                      <div class="col-md-'+(carmodel!=''?3:3)+'"> \
                        <div class="form-group" id="barcode'+carmodelid+mappingcount+'_div"> \
                          <div class="col-md-12"> \
                            <label class="control-label">Bar Code <span class="mandatoryfield">*</span></label> \
                            <input type="text" class="form-control" name="barcode'+carmodelid+'[]" id="barcode'+carmodel+'" maxlength="25" value=""> \
                          </div> \
                        </div> \
                      </div> \
                      <div class="col-md-'+(carmodel!=''?2:3)+'"> \
                        <div class="form-group" id="price'+carmodelid+mappingcount+'_div"> \
                          <div class="col-md-12"> \
                            <label class="control-label">Price <span class="mandatoryfield">*</span></label> \
                            <input type="text" class="form-control" name="price'+carmodelid+'[]" id="price'+carmodel+'" onkeypress="return decimal_number_validation(event,this.value,8,3)" maxlength="25" value=""> \
                          </div> \
                        </div> \
                      </div> \
                      <div class="col-md-1"> \
                        <button type="button" class="btn btn-default btn-raised" onclick="removeproductmapping('+mappingcount+','+carmodelid+')" style="margin-top: 35px;"><i class="fa fa-minus"></i><div class="ripple-container"></div></button> \
                      </div> \
                      </div> \
            </div>';
  $('#moremapping'+carmodelid+'_div').append($.html);

}
function removeproductmapping(rowid,carmodelid){
    
    if($("input[name='mappingname"+carmodelid+"[]']").length!=1 && $('#productmappingid'+rowid+carmodelid).val()!=null){

      var removeproductmappingid = $('#removeproductmappingid').val();
      $('#removeproductmappingid').val(removeproductmappingid+','+$('#productmappingid'+rowid+carmodelid).val());
    }
    $('#mappingcount'+rowid).remove();
  }
function checkvalidation(){
  var productid = $('#productid').val();
  var carmodelid = $("input[name='carmodelid[]']").map(function(){return $(this).val();}).get();
  var carmodelprice = $("input[name='carmodelprice[]']").map(function(){return $(this).val();}).get();
  
  var isvalidproductid = 0;
  var isvalidmappingname = isvalidbarcode = isvalidprice = 1;
  
  PNotify.removeAll();
  if(productid==0){
    $("#product_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter productid !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidproductid = 0;
  }else{
    isvalidproductid = 1;
  }
  for (var i = 0; i < carmodelid.length; i++) {
    var mappingname = $("input[name='mappingname"+carmodelid[i]+"[]']").map(function(){return $(this).val();}).get();
    var barcode = $("input[name='barcode"+carmodelid[i]+"[]']").map(function(){return $(this).val();}).get();
    var price = $("input[name='price"+carmodelid[i]+"[]']").map(function(){return $(this).val();}).get();

    for (var j = 0; j < mappingname.length; j++) {
      if(mappingname[j] == ''){
        $("#mappingname"+carmodelid[i]+(j+1)+"_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter '+(j+1)+' mapping name !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidmappingname = 0;
      }else if(mappingname[j].length<3){
        $("#mappingname"+carmodelid[i]+(j+1)+"_div").addClass("has-error is-focused");
        new PNotify({title: 'Mapping name require minimum 3 characters for '+(j+1)+' product !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidmappingname = 0;
      }else{
        $("#mappingname"+carmodelid[i]+(j+1)+"_div").removeClass("has-error is-focused");
      }

      if(barcode[j] == ''){
        $("#barcode"+carmodelid[i]+(j+1)+"_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter '+(j+1)+' barcode number !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidbarcode = 0;
      }else if(barcode[j].length<4){
        $("#barcode"+carmodelid[i]+(j+1)+"_div").addClass("has-error is-focused");
        new PNotify({title: 'Bar code require minimum 4 characters for '+(j+1)+' product !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidbarcode = 0;
      }else{
        $("#barcode"+carmodelid[i]+(j+1)+"_div").removeClass("has-error is-focused");
      }

      if(price[j] == 0){
        $("#price"+carmodelid[i]+(j+1)+"_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter '+(j+1)+' price number !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidprice = 0;
      }else{
        $("#price"+carmodelid[i]+(j+1)+"_div").removeClass("has-error is-focused");
      }
    }
    
  }

  if(isvalidprice==1){
    for (var i = 0; i < carmodelid.length; i++) {
      var total = 0;
      $("input[name='price"+carmodelid[i]+"[]']").each(function (index, element) {
        total = total + parseFloat($(element).val());
      });
      if(carmodelprice[i]!=total){
        $("#price"+carmodelid[i]+"1_div").addClass("has-error is-focused");
        new PNotify({title: (i+1)+' product mapping price different from actual product price !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidprice = 0;
      }else{
        $("#price"+carmodelid[i]+"1_div").removeClass("has-error is-focused");
      }
    }
  }
  if(isvalidproductid == 1 && isvalidmappingname == 1 && isvalidbarcode == 1 && isvalidprice == 1){
    $('#productid').removeAttr('disabled');
    var formData = new FormData($('#productmappingform')[0]);
    var uurl = SITE_URL+"productmapping/addproductmapping";
    
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
          new PNotify({title: "Product successfully mapping.",styling: 'fontawesome',delay: '3000',type: 'success'});
          setTimeout(function() { window.location=SITE_URL+"productmapping"; }, 1500);
        }else{
          new PNotify({title: 'Product not mapping !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

