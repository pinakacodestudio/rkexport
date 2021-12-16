$(document).ready(function(){

  if(ACTION==1){
    var automailid = $("#automailid").val();
    getautomaildata(automailid);
    //$('option:not(:selected)').attr('disabled', true);
  }
  
});

$('#automailid').change(function(){
  if(this.value!=0){
    getautomaildata(this.value);  
  }else{
    $('#daydetaildata').html('');
  }
  
});
function resetdata(){
  $("#automailid_div").removeClass("has-error is-focused");
}
function getautomaildata(automailid,carmodelid=''){
  if(automailid!=0){
    var uurl = SITE_URL+"automail/getAutomailData";
      
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {automailid:automailid},
      datatype:'json',
      //async: false,
      beforeSend: function(){
        $('.mask').show();
        $('#loader').show();
      },
      success: function(response){
        var response = JSON.parse(response);
        $.html = '<input type="hidden" type="text" name="removeautomailtableid" id="removeautomailtableid">';
        if(response.length>0){
          
          for (var i = 0; i < response.length; i++) {
            dayscount = response.length;
            $.html += '<div class="col-md-12"> \
                        <div id="automailcount'+i+'"> \
                          <input type="hidden" name="automailtableid[]" value="'+response[i]['id']+'" id="automailtableid'+i+'"> \
                          <div class="col-md-12 p-n"> \
                            <div class="col-md-8 col-md-offset-3"> \
                              <div class="form-group" id="day'+i+'_div"> \
                                <div class="col-sm-8"> \
                                  <input id="day'+i+'" type="text" name="day[]" class="form-control" value="'+response[i]['day']+'" onkeypress="return isNumber(event)" maxlength="3"> \
                                </div> \
                                 <div class="col-md-2">';
                                  if(i==0){
                                    $.html += '<button type="button" class="btn btn-default btn-raised" onclick="addnewdaydetail()"><i class="material-icons">plus_one</i><div class="ripple-container"></div></button>';
                                  }else{
                                    $.html += '<button type="button" class="btn btn-default btn-raised" onclick="removedaydetail('+i+')"><i class="fa fa-minus"></i><div class="ripple-container"></div></button>';
                                  }
                          $.html += '</div> \
                                  </div> \
                                </div> \
                              </div> \
                            </div> \
                          </div>';
            
          }
          $('#daydetaildata').html($.html);
          
        }else{
          dayscount = 1;
          $.html = '<div class="col-md-12"> \
                        <div id="carmodelcount1"> \
                          <div class="col-md-12 p-n"> \
                            <div class="col-md-8 col-md-offset-3"> \
                              <div class="form-group" id="day1_div"> \
                                <div class="col-sm-8"> \
                                  <input id="day1" type="text" name="day[]" class="form-control" value="" onkeypress="return isNumber(event)" maxlength="3"> \
                                </div> \
                                <div class="col-md-2"> \
                                  <button type="button" class="btn btn-default btn-raised" onclick="addnewdaydetail()"><i class="material-icons">plus_one</i><div class="ripple-container"></div></button> \
                                </div> \
                              </div> \
                            </div> \
                          </div> \
                        </div> \
                      </div>';

          $('#daydetaildata').html($.html);
          
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
function addnewdaydetail(){
    
  if($('input[name="day[]"]').length<5){
    dayscount = ++dayscount;
    $.html = '<div class="col-md-12"> \
                <div id="automailcount'+dayscount+'"> \
                  <div class="col-md-12 p-n"> \
                    <div class="col-md-8 col-md-offset-3"> \
                      <div class="form-group" id="day'+dayscount+'_div"> \
                        <div class="col-sm-8"> \
                          <input id="day'+dayscount+'" type="text" name="day[]" class="form-control" value="" onkeypress="return isNumber(event)" maxlength="3"> \
                        </div> \
                         <div class="col-md-2"> \
                          <button type="button" class="btn btn-default btn-raised" onclick="removedaydetail('+dayscount+')"><i class="fa fa-minus"></i><div class="ripple-container"></div></button> \
                        </div> \
                          </div> \
                        </div> \
                      </div> \
                    </div> \
                  </div>';

      $('#daydetaildata').append($.html);
      
  }else{
    PNotify.removeAll();
    new PNotify({title: 'Maximum 5 days details allowed !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
}
function removedaydetail(rowid){
  if($('input[name="automailtableid[]"]').length!=1 && $('#automailtableid'+rowid).val()!=null){
    var removeautomailtableid = $('#removeautomailtableid').val();
    $('#removeautomailtableid').val(removeautomailtableid+','+$('#automailtableid'+rowid).val());
  }
  $('#automailcount'+rowid).remove();
}
function checkvalidation(){
  
  var automailid = $('#automailid').val();
  var day = $("input[name='day[]']").map(function(){return $(this).val();}).get();
  
  var isvalidautomailid = isvalidday = 1;
  
  PNotify.removeAll();
  if(automailid == 0){
    $("#automailid_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select auto mail type !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidautomailid = 0;
  }else{
    isvalidautomailid = 1;  
  }

  for (var i = 0; i < day.length; i++) {
    if(day[i] == 0){
      $("#day"+day[i]+"_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter '+(i+1)+' day number !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidday = 0;
    }
  }

  if(isvalidautomailid == 1 && isvalidday == 1){

    var formData = new FormData($('#automailform')[0]);
    var uurl = SITE_URL+"automail/addautomail";
    
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
          new PNotify({title: "Auto mail successfully set.",styling: 'fontawesome',delay: '3000',type: 'success'});
          setTimeout(function() { window.location=SITE_URL+"automail"; }, 1500);
        }else{
          new PNotify({title: 'Auto mail not set !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

