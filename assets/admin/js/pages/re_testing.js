$(document).ready(function() {  
  
    /* $('#testdate').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        todayBtn:"linked",
    }); */
    
    $('.qualitycheckfile').change(function(){
        validfile($(this),this);
    });
    
    
    $('#processid').on('change', function (e) {
        getbatchno();
    });
    $('#batchid').on('change', function (e) {
        getProducts();
        getproductDetailsByBatchId();
    });
    
    $(".countcharges0 .add_charges_btn").hide();
    $(".countcharges0 .add_charges_btn:last").show();
  
    if(ACTION==1 || RETESTING==1){
        getbatchno();
        getProducts();
        getproductDetails(TestingId);
    }
  
});
  
function calculateretestingpendingqty(id){
    var qty =$('#retestingquantity'+id).val();
    
    var mechanicledefectqty = $('#retestingmechanicledefectqty'+id).val();
    var electricallydefectqty = $('#retestingelectricallydefectqty'+id).val();
    var visuallydefectqty = $('#retestingvisuallydefectqty'+id).val();
    var pendingqty = qty;
    // alert(qty);

    if(!isNaN(mechanicledefectqty)  && parseInt(mechanicledefectqty)<=parseInt(pendingqty)){
        var pendingqty = parseInt(pendingqty)-parseInt(mechanicledefectqty);
    }
    
    if(!isNaN(visuallydefectqty)  && parseInt(visuallydefectqty)<=parseInt(pendingqty)){
        var pendingqty = parseInt(pendingqty)-parseInt(visuallydefectqty);
    }
    
    if(!isNaN(electricallydefectqty)  && parseInt(electricallydefectqty)<=parseInt(pendingqty)){
        var pendingqty = parseInt(pendingqty)-parseInt(electricallydefectqty);
    }
    
    if(parseInt(pendingqty) < 0 ){
        $('#retestingpendingqty'+id).html(qty);
    }else{
        $('#retestingpendingqty'+id).html(pendingqty);
    }    
}
function calculatenewretestingpendingqty(id){
    var qty =$('#newretestingquantity'+id).val();
    
    var mechanicledefectqty = $('#newretestingmechanicledefectqty'+id).val()!=''?$('#newretestingmechanicledefectqty'+id).val():0;
    var electricallydefectqty = $('#newretestingelectricallydefectqty'+id).val()!=''?$('#newretestingelectricallydefectqty'+id).val():0;
    var visuallydefectqty = $('#newretestingvisuallydefectqty'+id).val()!=''?$('#newretestingvisuallydefectqty'+id).val():0;
    var pendingqty = qty;
    // alert(qty);

    if(!isNaN(mechanicledefectqty)  && parseInt(mechanicledefectqty)<=parseInt(pendingqty)){
        var pendingqty = parseInt(pendingqty)-parseInt(mechanicledefectqty);
    }
    
    if(!isNaN(visuallydefectqty)  && parseInt(visuallydefectqty)<=parseInt(pendingqty)){
        var pendingqty = parseInt(pendingqty)-parseInt(visuallydefectqty);
    }
    
    if(!isNaN(electricallydefectqty)  && parseInt(electricallydefectqty)<=parseInt(pendingqty)){
        var pendingqty = parseInt(pendingqty)-parseInt(electricallydefectqty);
    }
    
    if(parseInt(pendingqty) < 0 ){
        $('#newretestingpendingqty'+id).html(qty);
    }else{
        $('#newretestingpendingqty'+id).html(pendingqty);
    }    
}
function calculatependingqty(id){
    var qty =$('#quantity'+id).val();
    // alert(id);
    var mechanicledefectqty = $('#mechanicledefectqty'+id).val();
    var electricallydefectqty = $('#electricallydefectqty'+id).val();
    var visuallydefectqty = $('#visuallydefectqty'+id).val();
    var pendingqty = qty;
    // alert(mechanicledefectqty);

    if(!isNaN(mechanicledefectqty)  && parseInt(mechanicledefectqty)<=parseInt(pendingqty)){
        var pendingqty = parseInt(pendingqty)-parseInt(mechanicledefectqty);
    }
    
    if(!isNaN(visuallydefectqty)  && parseInt(visuallydefectqty)<=parseInt(pendingqty)){
        var pendingqty = parseInt(pendingqty)-parseInt(visuallydefectqty);
    }
    
    if(!isNaN(electricallydefectqty)  && parseInt(electricallydefectqty)<=parseInt(pendingqty)){
        var pendingqty = parseInt(pendingqty)-parseInt(electricallydefectqty);
    }
    
    if(parseInt(pendingqty) < 0 ){
        $('#pendingqty'+id).html(qty);
    }else{
        $('#pendingqty'+id).html(pendingqty);
    }    
}
  
function validnewretestingimageorpdffile(obj){
  // console.log(element);
  var val = obj.val();
  var id = obj.attr('id').match(/\d+/);
  
  var filename = obj.val().replace(/C:\\fakepath\\/i, '');
  // alert(filename);
  
  switch(val.substring(val.lastIndexOf('.') + 1).toLowerCase()){
    case 'pdf': case 'gif': case 'bmp': case 'jpg': case 'jpeg': case 'png':
      $("#newretestingFiletext"+id).val(filename);
      $("#newretestingfileupload"+id).removeClass("has-error is-focused");
      // $("#isvalid"+element).val('1');
      break;
    default:
      $("#newretestingtestingfile"+id).val("");
      $("#newretestingFiletext"+id).val("");
      // $("#isvalid"+element).val('0');
      $("#newretestingfileupload"+id).addClass("has-error is-focused");
      new PNotify({title: 'Please upload valid testing file '+id+' !',styling: 'fontawesome',delay: '3000',type: 'error'});
      break;
  }
}
function validretestingimageorpdffile(obj){
  // console.log(element);
  var val = obj.val();
  var id = obj.attr('id').match(/\d+/);
  
  var filename = obj.val().replace(/C:\\fakepath\\/i, '');
  // alert(filename);
  
  switch(val.substring(val.lastIndexOf('.') + 1).toLowerCase()){
    case 'pdf': case 'gif': case 'bmp': case 'jpg': case 'jpeg': case 'png':
      $("#retestingFiletext"+id).val(filename);
      $("#retestingfileupload"+id).removeClass("has-error is-focused");
      // $("#isvalid"+element).val('1');
      break;
    default:
      $("#retestingtestingfile"+id).val("");
      $("#retestingFiletext"+id).val("");
      // $("#isvalid"+element).val('0');
      $("#retestingfileupload"+id).addClass("has-error is-focused");
      new PNotify({title: 'Please upload valid testingfile '+id+' !',styling: 'fontawesome',delay: '3000',type: 'error'});
      break;
  }
}
function validimageorpdffile(obj){
  // console.log(element);
  var val = obj.val();
  var id = obj.attr('id').match(/\d+/);
  
  var filename = obj.val().replace(/C:\\fakepath\\/i, '');
  // alert(filename);
  
  switch(val.substring(val.lastIndexOf('.') + 1).toLowerCase()){
    case 'pdf': case 'gif': case 'bmp': case 'jpg': case 'jpeg': case 'png':
      $("#Filetext"+id).val(filename);
      $("#fileupload"+id).removeClass("has-error is-focused");
      // $("#isvalid"+element).val('1');
      break;
    default:
      $("#testingfile"+id).val("");
      $("#Filetext"+id).val("");
      // $("#isvalid"+element).val('0');
      $("#fileupload"+id).addClass("has-error is-focused");
      new PNotify({title: 'Please upload valid testingfile '+id+' !',styling: 'fontawesome',delay: '3000',type: 'error'});
      break;
  }
}

function getbatchno(){
  
  var processid = $("#processid").val();

    $('#batchid')
        .find('option')
        .remove()
        .end()
        .append('<option value="0">Select Batch No.</option>')
        .val('0')
    ;
  
  if(processid!='' && processid!=null){
    var uurl = SITE_URL+"testing-and-rd/getBatchNoOfINProductProcess";
    
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {processid:processid},
      dataType: 'json',
      async: false,
      success: function(response){
        if(response!=""){

          for(var i = 0; i < response.length; i++) {

              $('#batchid').append($('<option>', { 
                value: response[i]['id'],
                text : response[i]['batchno'],
            }));

            if(BatchNo!=0){
              $('#batchid').val(BatchNo);
            }
          }
        }
      },
      error: function(xhr) {
      //alert(xhr.responseText);
      },
    });
  }

  $('#batchid').selectpicker('refresh');
  
}

function getProducts(){
  
  var batchno = $("#batchid").val();
  
  if(batchno!='' && batchno!=null){
    var uurl = SITE_URL+"testing-and-rd/getProductByBatchnoForRetesting";
    
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {batchno:batchno},
      dataType: 'json',
      async: false,
      success: function(response){
        if(response!=""){
              var productdata = response['productdata'];
              var retestingproductdata = response['retestingproductdata'];
              var remaintetproductdata = response['remaintetproductdata'];
              var htmldata = discolumn = retesthtmldata = "";
              var headerdata = '<tr>\
                                  <th class="width5">Sr. No.</th>\
                                  <th>Output Product Name</th>\
                                  <th>Qty.</th>\
                                  <th>Mechanicle Checked</th>\
                                  <th>Electrically Checked</th>\
                                  <th>Visually Checked</th>\
                                  <th>Approve Qty.</th>\
                                  <th>Upload Report</th>\
                                </tr>';

              if(productdata!=null && productdata!=""){
                // alert();
                if(productdata.length>0){
                  for(var i=0; i<productdata.length; i++){
                    var pendingqty = productdata[i]['quantity'];

                    htmldata += "<tr class='countproducts' div-id='"+(i+1)+"' id='"+productdata[i]['id']+"'>";
                      htmldata += "<td>"+(i+1);
                      htmldata += "</td>";

                      htmldata += "<td>"+ucwords(productdata[i]['productname'])+"</td>";
                      
                      htmldata += '<td class="width8 text-left pl-n"><div class="col-md-12">'+parseInt(productdata[i]['quantity'])+'<input type="hidden" name="quantity[]" id="quantity'+(i+1)+'" class="form-control qty" value="'+parseInt(productdata[i]['quantity'])+'" onkeypress="'+(MANAGE_DECIMAL_QTY==1?"return decimal_number_validation(event, this.value,8);":"return isNumber(event);")+'"></td>';
                      
                      htmldata += "<td><div class='checkbox'><input type='checkbox' disabled class='text-left mechaniclecheck' name='mechaniclecheck"+(i+1)+"'  id='mechaniclecheck"+(i+1)+"' value=''><label for='mechaniclecheck"+(i+1)+"'></label></div>\
                                    <div class='text-left form-group mechanicledefectdiv"+(i+1)+"'><div class='col-md-12'><label class='control-label' for='defectqty'>Defect Qty.</label><br><input type='text' class='text-left form-control defectedqty' name='mechanicledefectqty[]' id='mechanicledefectqty"+(i+1)+"' value='0' onkeypress='return isNumber(event)' readonly></div></div>\
                                    </td>";
                        
                      htmldata += "<td><div class='checkbox'><input type='checkbox' disabled class='text-left electricallycheck' name='electricallycheck"+(i+1)+"'  id='electricallycheck"+(i+1)+"' value=''><label for='electricallycheck"+(i+1)+"'></label></div>\
                                    <div class='text-left form-group electricallydefectdiv"+(i+1)+"'><div class='col-md-12'><label class='control-label' for='defectqty'>Defect Qty.</label><br><input type='text' class='text-left form-control defectedqty' name='electricallydefectqty[]' id='electricallydefectqty"+(i+1)+"' value='0' onkeypress='return isNumber(event)' readonly></div></div>\
                                    </td>";

                      htmldata += "<td><div class='checkbox'><input type='checkbox' disabled class='text-left visuallycheck' name='visuallycheck"+(i+1)+"'  id='visuallycheck"+(i+1)+"' value=''><label for='visuallycheck"+(i+1)+"'></label></div>\
                                    <div class='text-left form-group visuallydefectdiv"+(i+1)+"'><div class='col-md-12'><label class='control-label' for='defectqty'>Defect Qty.</label><br><input type='text' class='text-left form-control defectedqty' name='visuallydefectqty[]' id='visuallydefectqty"+(i+1)+"' value='0' onkeypress='return isNumber(event)' readonly></div></div>\
                                    </td>";
                      htmldata +="<td id='pendingqty"+(i+1)+"'>"+parseInt(pendingqty)+"</td>";
                      htmldata +="<td class='pr-n'>\
                                  <div class='form-group col-md-12 pr-n' id='Filetext"+(i+1)+"_div'>\
                                  <!-- <input type='text' style='margin-top: 1px!important;' readonly='' id='Filetext"+(i+1)+"' class='form-control ' name='Filetext[]' value=''> -->\
                                  <a href='"+TESTING_IMAGE+productdata[i]['filename']+"' class='"+view_class+"' target='_blank' >"+view_text+"</a>\
                                  </div>\
                                  </div>\
                                  </td>";

                    htmldata += "</tr>";


                  }
                  for (let j = 0; j < retestingproductdata.length; j++) {

                    // console.log(retestingproductdata[j]['testdate'])
                      var date = retestingproductdata[j]['testdate'].split('-');
                      var remarks = retestingproductdata[j]['remarks'];
                      var newdate = new Date(date[0],date[1]-1,date[2]);
                      var month = newdate.getMonth();
                      // console.log(month);
                      var day = newdate.getDate();
                      var year = newdate.getFullYear();

                      FinalDate = day+'/'+month+'/'+year

                      
                      
                      retesthtmlproductdata = '';
                      for (let p = 0; p < retestingproductdata[j]['retestingdata'].length; p++) {
                        var productprocessdetailsid = parseInt(retestingproductdata[j]['retestingdata'][p]['id']);
                        var quantity = parseInt(retestingproductdata[j]['retestingdata'][p]['quantity']);
                        var retestqty = parseInt(retestingproductdata[j]['retestingdata'][p]['retestqty']);
                        var filename = retestingproductdata[j]['retestingdata'][p]['filename'];
                        var productname = retestingproductdata[j]['retestingdata'][p]['productname'];
                        var mechaniclecheck = retestingproductdata[j]['retestingdata'][p]['mechaniclecheck'];
                        var electricallycheck = retestingproductdata[j]['retestingdata'][p]['electricallycheck'];
                        var visuallycheck = retestingproductdata[j]['retestingdata'][p]['visuallycheck'];
                        var mechanicledefectqty = retestingproductdata[j]['retestingdata'][p]['mechanicledefectqty'];
                        var electricallydefectqty = retestingproductdata[j]['retestingdata'][p]['electricallydefectqty'];
                        var visuallydefectqty = retestingproductdata[j]['retestingdata'][p]['visuallydefectqty'];
                        
                        FinalCheckId = productprocessdetailsid;
                       /*  FinalCheckQty = quantity;
                        FinalCheckretestqty = retestqty
                        FinalCheckfilename = filename
                        FinalCheckproductname = productname
                        FinalCheckmechaniclecheck = mechaniclecheck
                        FinalCheckelectricallycheck = electricallycheck
                        FinalCheckvisuallycheck = visuallycheck
                        FinalCheckmechanicledefectqty = mechanicledefectqty
                        FinalCheckelectricallydefectqty = electricallydefectqty
                        FinalCheckvisuallydefectqty = visuallydefectqty */
                        FinalCheck = parseInt(mechanicledefectqty)+parseInt(electricallydefectqty)+parseInt(visuallydefectqty);



                          retesthtmlproductdata += "<tr class='countproducts' div-id='"+(p+1)+"' id='"+productprocessdetailsid+"'>";
                          retesthtmlproductdata += "<td>"+(p+1);
                          retesthtmlproductdata += "</td>";

                          retesthtmlproductdata += "<td>"+productname+"</td>";
                
                          retesthtmlproductdata += '<td class="width8 text-left pl-n"><div class="col-md-12">'+retestqty+'<input type="hidden" name="retestingquantity[]" id="retestingquantity'+(p+1)+'" class="form-control retestingqty" value="'+retestqty+'" onkeypress="'+(MANAGE_DECIMAL_QTY==1?"return decimal_number_validation(event, this.value,8);":"return isNumber(event);")+'">';
                          retesthtmlproductdata += '<input type="hidden" name="retestingtransactionproductsid[]" value="'+productprocessdetailsid+'">';
                          retesthtmlproductdata += "<input type='hidden' id='retestingoldtestingfiletext"+(p+1)+"' class='form-control' name='retestingoldFiletext[]' value=''>";
                          retesthtmlproductdata += "<input type='hidden' name='retestingmappingid[]' id= 'retestingmappingid"+(p+1)+"'class='form-control'  value=''>";
                          retesthtmlproductdata += '</td>';
                          
                          retesthtmlproductdata += "<td><div class='checkbox'><input type='checkbox' class='text-left retestingmechaniclecheck' name='retestingmechaniclecheck"+(p+1)+"' "+(mechaniclecheck==1?"checked":"")+" id='retestingmechaniclecheck"+(p+1)+"' value='' disabled><label for='retestingmechaniclecheck"+(p+1)+"'></label></div>\
                                      <div class='text-left form-group retestingmechanicledefectdiv"+(p+1)+"'><div class='col-md-12'><label class='control-label' for='retestingdefectqty'>Defect Qty.</label><br><input type='text' class='text-left form-control retestingdefectedqty' name='retestingmechanicledefectqty[]' value="+mechanicledefectqty+" id='retestingmechanicledefectqty"+(p+1)+"' value='0' onkeypress='return isNumber(event)' readonly></div></div>\
                                      </td>";
                          
                          retesthtmlproductdata += "<td><div class='checkbox'><input type='checkbox' class='text-left retestingelectricallycheck' name='retestingelectricallycheck"+(p+1)+"'  "+(electricallycheck==1?"checked":"")+" id='retestingelectricallycheck"+(p+1)+"' disabled value=''><label for='retestingelectricallycheck"+(p+1)+"'></label></div>\
                                      <div class='text-left form-group retestingelectricallydefectdiv"+(p+1)+"'><div class='col-md-12'><label class='control-label' for='retestingdefectqty'>Defect Qty.</label><br><input type='text' class='text-left form-control retestingdefectedqty' name='retestingelectricallydefectqty[]' value="+electricallydefectqty+" id='retestingelectricallydefectqty"+(p+1)+"' value='0' onkeypress='return isNumber(event)' readonly></div></div>\
                                      </td>";
  
                          retesthtmlproductdata += "<td><div class='checkbox'><input type='checkbox' class='text-left retestingvisuallycheck' name='retestingvisuallycheck"+(p+1)+"'  "+(visuallycheck==1?"checked":"")+" id='retestingvisuallycheck"+(p+1)+"' disabled value=''><label for='retestingvisuallycheck"+(p+1)+"'></label></div>\
                                      <div class='text-left form-group visuallydefectdiv"+(p+1)+"'><div class='col-md-12'><label class='control-label' for='retestingdefectqty'>Defect Qty.</label><br><input type='text' class='text-left form-control retestingdefectedqty' name='retestingvisuallydefectqty[]' id='retestingvisuallydefectqty"+(p+1)+"' value="+visuallydefectqty+" onkeypress='return isNumber(event)' readonly></div></div>\
                                      </td>";
                          retesthtmlproductdata += "<td id='retestingpendingqty"+(p+1)+"'>"+retestqty+"</td>";
                          retesthtmlproductdata += "<td class='pr-n'>\
                                      <div class='form-group col-md-12 pr-n' id='retestingFiletext"+(p+1)+"_div'>\
                                      <!-- <div class='input-group ' id='retestingfileupload1'>\
                                      <span class='input-group-btn' style='padding: 0 0px 0px 0px;'>\
                                      <span class='btn btn-primary btn-raised btn-file'>\
                                      <i class='fa fa-upload'></i>\
                                      <input type='file' name='retestingtestingfile"+(p+1)+"' class='retestingtestingfile' id='retestingtestingfile"+(p+1)+"' accept='.docx,.pdf,.bmp,.bm,.gif,.ico,.jfif,.jfif-tbnl,.jpe,.jpeg,.jpg,.pbm,.png,.svf,.tif,.tiff,.wbmp,.x-png' onchange='validretestingimageorpdffile($(this))'>\
                                      </span>\
                                      </span>\
                                      <input type='text' style='margin-top: 1px!important;' readonly='' id='retestingFiletext"+(p+1)+"' class='form-control ' name='Filetext[]' value=''>\
                                      </div> -->";  
                                      if(filename!=''){
                                        retesthtmlproductdata += "<a href='"+TESTING_IMAGE+filename+"' class='"+view_class+"' target='_blank' >"+view_text+"</a>";
                                      }
                                      retesthtmlproductdata += "</div>\
                                      </td>";
                          retesthtmlproductdata += '</tr>';
                      }

                          // var retestqty = 0;
                          if(retestqty!=0){
                            retesthtmldata += '<div class="row">\
                                                  <div class="col-md-12 p-n">\
                                                    <hr>\
                                                  </div>\
                                                </div>\
                                                <div class="row">\
                                                  <div class="col-md-12 p-n">\
                                                    <div class="col-sm-3">\
                                                      <div class="form-group" id="testdate_div">\
                                                          <div class="col-md-12 pl-xs">\
                                                              <label for="testdate" class="control-label">Date <span class="mandatoryfield">*</span></label>\
                                                              <div class="input-group">\
                                                                  <input id="testdate" type="text" value="'+FinalDate +'" class="form-control" readonly>\
                                                                  <span class="btn btn-default datepicker_calendar_button" title="Date"><i class="fa fa-calendar fa-lg"></i></span>\
                                                              </div>\
                                                          </div>\
                                                      </div>\
                                                  </div>\
                                                  </div>\
                                                </div>\
                                              <div class="row">\
                                                <div class="col-md-12 p-n">\
                                                    <div class="panel">\
                                                        <div class="panel-heading">\
                                                            <h4 class="text-center">Re Testing And R&D '+(j+1)+'</h4>\
                                                            <hr>\
                                                        </div>\
                                                        <div class="panel-body no-padding">\
                                                            <div class="table-responsive">\
                                                                <table id="retesttestingproducttable'+(j+1)+'" class="table table-hover table-bordered m-n">\
                                                                    <thead>\
                                                                      <tr>\
                                                                        <th class="width5">Sr. No.</th>\
                                                                        <th>Output Product Name</th>\
                                                                        <th>Qty.</th>\
                                                                        <th>Mechanicle Checked</th>\
                                                                        <th>Electrically Checked</th>\
                                                                        <th>Visually Checked</th>\
                                                                        <th>Approve Qty.</th>\
                                                                        <th>Upload Report</th>\
                                                                      </tr>\
                                                                    </thead>\
                                                                    <tbody>';
                                                                    retesthtmldata += retesthtmlproductdata;  
                                                                retesthtmldata += '</tbody>\
                                                                </table>\
                                                            </div>\
                                                        </div>\
                                                    </div>\
                                                </div>\
                                            </div>\
                                            <div class="row">\
                                            <div class="col-md-12">\
                                                <div class="form-group" id="remarks_div">\
                                                    <div class="col-md-12 p-n">\
                                                        <label for="testingremarks" class="control-label">Remarks</label>\
                                                        <div class="input-group">\
                                                            <textarea id="testingremarks" name="testingremarks" class="form-control" readonly>'+remarks+'</textarea>\
                                                        </div>\
                                                    </div>\
                                                </div>\
                                            </div>\
                                        </div>';
                      
                    }
                  }

                  // console.log(FinalCheck);
                  remainretesthtmlproductdata = '';
                  for(var i=0; i<remaintetproductdata.length; i++){
                        // console.log(remaintetproductdata[i]['testingid'])

                        var testingid = parseInt(remaintetproductdata[i]['testingid']);
                        var productprocessdetailsid = parseInt(remaintetproductdata[i]['id']);
                        var retestqty = parseInt(remaintetproductdata[i]['retestqty']);
                        var filename = remaintetproductdata[i]['filename'];
                        var productname = remaintetproductdata[i]['productname'];
                        
                        var mechaniclecheck = remaintetproductdata[i]['mechaniclecheck'];
                        var electricallycheck = remaintetproductdata[i]['electricallycheck'];
                        var visuallycheck = remaintetproductdata[i]['visuallycheck'];
                        var mechanicledefectqty = remaintetproductdata[i]['mechanicledefectqty'];
                        var electricallydefectqty = remaintetproductdata[i]['electricallydefectqty'];
                        var visuallydefectqty = remaintetproductdata[i]['visuallydefectqty'];


                      remainretesthtmlproductdata += "<tr class='newcountproducts' div-id='"+(i+1)+"' id='"+productprocessdetailsid+"'>";
                      remainretesthtmlproductdata += "<td>"+(i+1)+"</td>";
                      remainretesthtmlproductdata += "<td>"+productname+"</td>";
            
                      remainretesthtmlproductdata += '<td class="width8 text-left pl-n"><div class="col-md-12">'+retestqty+'<input type="hidden" name="newretestingquantity[]" id="newretestingquantity'+(i+1)+'" class="form-control newretestingqty" value="'+retestqty+'" onkeypress="'+(MANAGE_DECIMAL_QTY==1?"return decimal_number_validation(event, this.value,8);":"return isNumber(event);")+'">';
                      remainretesthtmlproductdata += '<input type="hidden" name="newretestingtransactionproductsid[]" value="'+productprocessdetailsid+'">';
                      remainretesthtmlproductdata += "<input type='hidden' id='newretestingoldtestingfiletext"+(i+1)+"' class='form-control' name='newretestingoldFiletext[]' value='"+filename+"'>";
                      remainretesthtmlproductdata += "<input type='hidden' name='newretestingmappingid[]' id= 'newretestingmappingid"+(i+1)+"' class='form-control'  value=''>";
                      remainretesthtmlproductdata += '</td>';
                      
                      remainretesthtmlproductdata += "<td><div class='checkbox'><input type='checkbox' class='text-left newretestingmechaniclecheck' name='newretestingmechaniclecheck"+(i+1)+"' id='newretestingmechaniclecheck"+(i+1)+"' value=''><label for='newretestingmechaniclecheck"+(i+1)+"'></label></div>\
                                  <div class='text-left form-group newretestingmechanicledefectdiv'><div class='col-md-12'><label class='control-label' for='newretestingdefectqty"+(i+1)+"'>Defect Qty.</label><br><input type='text' class='text-left form-control newretestingdefectedqty' name='newretestingmechanicledefectqty[]' value='' id='newretestingmechanicledefectqty"+(i+1)+"' value='0' onkeypress='return isNumber(event)' readonly></div></div>\
                                  </td>";
                      
                      remainretesthtmlproductdata += "<td><div class='checkbox'><input type='checkbox' class='text-left newretestingelectricallycheck' name='newretestingelectricallycheck"+(i+1)+"' id='newretestingelectricallycheck"+(i+1)+"' value=''><label for='newretestingelectricallycheck"+(i+1)+"'></label></div>\
                                  <div class='text-left form-group newretestingelectricallydefectdiv'><div class='col-md-12'><label class='control-label' for='newretestingdefectqty"+(i+1)+"'>Defect Qty.</label><br><input type='text' class='text-left form-control newretestingdefectedqty' name='newretestingelectricallydefectqty[]' value='' id='newretestingelectricallydefectqty"+(i+1)+"' value='0' onkeypress='return isNumber(event)' readonly></div></div>\
                                  </td>";

                      remainretesthtmlproductdata += "<td><div class='checkbox'><input type='checkbox' class='text-left newretestingvisuallycheck' name='newretestingvisuallycheck"+(i+1)+"' id='newretestingvisuallycheck"+(i+1)+"' value=''><label for='newretestingvisuallycheck"+(i+1)+"'></label></div>\
                                  <div class='text-left form-group newvisuallydefectdiv'><div class='col-md-12'><label class='control-label' for='newretestingdefectqty"+(i+1)+"'>Defect Qty.</label><br><input type='text' class='text-left form-control newretestingdefectedqty' name='newretestingvisuallydefectqty[]' id='newretestingvisuallydefectqty"+(i+1)+"' value='' onkeypress='return isNumber(event)' readonly></div></div>\
                                  </td>";
                      remainretesthtmlproductdata += "<td id='newretestingpendingqty"+(i+1)+"'>"+retestqty+"</td>";
                      remainretesthtmlproductdata += "<td class='pr-n'>\
                                            <div class='form-group col-md-12 pr-n' id='newretestingFiletext"+(i+1)+"_div'>\
                                              <div class='input-group ' id='newretestingfileupload"+(i+1)+"'>\
                                                <span class='input-group-btn' style='padding: 0 0px 0px 0px;'>\
                                                  <span class='btn btn-primary btn-raised btn-file'>\
                                                      <i class='fa fa-upload'></i>\
                                                      <input type='file' name='newretestingtestingfile"+(i+1)+"' class='newretestingtestingfile' id='newretestingtestingfile"+(i+1)+"' accept='.docx,.pdf,.bmp,.bm,.gif,.ico,.jfif,.jfif-tbnl,.jpe,.jpeg,.jpg,.pbm,.png,.svf,.tif,.tiff,.wbmp,.x-png' onchange='validnewretestingimageorpdffile($(this))'>\
                                                  </span>\
                                                </span>\
                                              <input type='text' style='margin-top: 1px!important;' readonly='' id='newretestingFiletext"+(i+1)+"' class='form-control ' name='Filetext[]' value=''>\
                                              </div>\
                                            </div>\
                                          </td>";
                      remainretesthtmlproductdata += '</tr>';

                  }
                  
                  if(remainretesthtmlproductdata!=''){
                      var today = new Date();
                      var month = today.getMonth()+1;
                      var day = today.getDate();
                      var year = today.getFullYear();
                      
                      $("#parenttestingid").val(testingid);


                      FinalDate = day+'/'+month+'/'+year
                      retesthtmldata += '<div class="row">\
                                          <div class="col-md-12 p-n">\
                                            <hr>\
                                          </div>\
                                        </div>\
                                        <div class="row">\
                                          <div class="col-md-12 p-n">\
                                            <div class="col-sm-3">\
                                              <div class="form-group" id="newtestdate_div">\
                                                  <div class="col-md-12 pl-xs">\
                                                      <label for="newtestdate" class="control-label">Date <span class="mandatoryfield">*</span></label>\
                                                      <div class="input-group">\
                                                          <input id="newtestdate" type="text" name="newtestdate" value="'+FinalDate +'" class="form-control" readonly>\
                                                          <span class="btn btn-default datepicker_calendar_button" title="Date"><i class="fa fa-calendar fa-lg"></i></span>\
                                                      </div>\
                                                  </div>\
                                              </div>\
                                          </div>\
                                          </div>\
                                        </div>\
                                      <div class="row">\
                                        <div class="col-md-12 p-n">\
                                            <div class="panel">\
                                                <div class="panel-heading">\
                                                    <h4 class="text-center">Re Testing And R&D </h4>\
                                                    <hr>\
                                                </div>\
                                                <div class="panel-body no-padding">\
                                                    <div class="table-responsive">\
                                                        <table id="newretesttestingproducttable1" class="table table-hover table-bordered m-n">\
                                                            <thead>\
                                                              <tr>\
                                                                <th class="width5">Sr. No.</th>\
                                                                <th>Output Product Name</th>\
                                                                <th>Qty.</th>\
                                                                <th>Mechanicle Checked</th>\
                                                                <th>Electrically Checked</th>\
                                                                <th>Visually Checked</th>\
                                                                <th>Approve Qty.</th>\
                                                                <th>Upload Report</th>\
                                                              </tr>\
                                                            </thead>\
                                                            <tbody>\
                                                            '+remainretesthtmlproductdata+'\
                                                            </tbody>\
                                                        </table>\
                                                    </div>\
                                                </div>\
                                            </div>\
                                        </div>\
                                    </div>\
                                    <div class="row">\
                                    <div class="col-md-12">\
                                        <div class="form-group" id="newremarks_div">\
                                            <div class="col-md-12 p-n">\
                                                <label for="newtestingremarks" class="control-label">Remarks</label>\
                                                <div class="input-group">\
                                                    <textarea id="newtestingremarks" name="newtestingremarks" class="form-control"></textarea>\
                                                </div>\
                                            </div>\
                                        </div>\
                                    </div>\
                                </div>';
                  }

                  $("#testingproducttable thead").html(headerdata);
                  $("#testingproducttable tbody").html(htmldata);

                  $("#retestingtables").append(retesthtmldata);


                  for (let r = 0; r < retestingproductdata.length; r++) {
                    calculateretestingpendingqty(r+1);
                  }
                  // $("#retesttestingproducttable tbody").html(retesthtmldata);
                }else{
                  $("#testingproducttable tbody").html("<tr><td colspan='9' class='text-center'>No data available in table.</td></tr>");
                }
              }else{
                $("#testingproducttable tbody").html("<tr><td colspan='9' class='text-center'>No data available in table.</td></tr>");
              }
              // $('.visuallycheck').each(function(){
              //   var visdivid =$(this).attr("id").match(/\d+/g);
                // $('.visuallycheckdepdiv'+visdivid).add();
              // });

              $('.retestingmechaniclecheck').click(function(){
                var id= $(this).attr("id").match(/\d+/g);
                
                if($('#retestingmechaniclecheck'+id).prop('checked')==true){
                $('#retestingmechanicledefectqty'+id).prop("readonly",false);
                }
                else{
                  $('#retestingmechanicledefectqty'+id).prop("readonly",true);
                }
              });

              // $('.dimensioncheck').each(function(){
              //   var dimdivid =$(this).attr("id").match(/\d+/g);
              //   $('.dimensioncheckdepdiv'+dimdivid).hide();
              // });

              $('.retestingelectricallycheck').click(function(){
                var id= $(this).attr("id").match(/\d+/g);
                
                if($('#retestingelectricallycheck'+id).prop('checked')==true){
                  $('#retestingelectricallydefectqty'+id).prop("readonly",false);
                }
                else{
                  $('#retestingelectricallydefectqty'+id).prop("readonly",true);
                }
              });

              $('.retestingvisuallycheck').click(function(){
                var id= $(this).attr("id").match(/\d+/g);
                
                if($('#retestingvisuallycheck'+id).prop('checked')==true){
                  $('#retestingvisuallydefectqty'+id).prop("readonly",false);
                }
                else{
                  $('#retestingvisuallydefectqty'+id).prop("readonly",true);
                }
              });

              $('.newretestingmechaniclecheck').click(function(){
                var id= $(this).attr("id").match(/\d+/g);
                if($('#newretestingmechaniclecheck'+id).prop('checked')==true){
                  $('#newretestingmechanicledefectqty'+id).prop("readonly",false);
                }
                else{
                  $('#newretestingmechanicledefectqty'+id).prop("readonly",true);
                }
              });

              $('.newretestingelectricallycheck').click(function(){
               
                var id= $(this).attr("id").match(/\d+/g);
                
                if($('#newretestingelectricallycheck'+id).prop('checked')==true){
                  $('#newretestingelectricallydefectqty'+id).prop("readonly",false);
                }
                else{
                  $('#newretestingelectricallydefectqty'+id).prop("readonly",true);
                }
              });

              $('.newretestingvisuallycheck').click(function(){
                var id= $(this).attr("id").match(/\d+/g); 
                if($('#newretestingvisuallycheck'+id).prop('checked')==true){
                  $('#newretestingvisuallydefectqty'+id).prop("readonly",false);
                }
                else{
                  $('#newretestingvisuallydefectqty'+id).prop("readonly",true);
                }
              });
        }
      },
      error: function(xhr) {
      //alert(xhr.responseText);
      },
    });
  }else{
    $("#testingproducttable tbody").html("<tr><td colspan='9' class='text-center'>No data available in table.</td></tr>");
    $('#orderamountdiv').html("");
    $('#extracharges_div').html("");
    $('#billingaddress').val('');
    $('#shippingaddress').val('');
  }
  
}

function getproductDetails(TestingId){
  // alert("getingpd");
  var uurl = SITE_URL+"testing-and-rd/getProductdatabytestingID";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {TestingId:String(TestingId)},
    dataType: 'json',
    async: false,
    success: function(response){
      if(response!=""){
        var productData = response;
        // console.log(productData);

        if(productData!=null && productData!=""){
          if(productData.length>0){
            for(var i=0; i<productData.length; i++){

              // updateData
              $('.countproducts').each(function(){
                var transactionproductsid = parseInt($(this).attr('id'));
                var div_id = parseInt($(this).attr('div-id'));

                if(transactionproductsid == productData[i]['transactionproductsid']){

                  $('#mechaniclecheck'+div_id).prop('checked',productData[i]['mechaniclecheck']==1?true:false);
                  $('#electricallycheck'+div_id).prop('checked',productData[i]['electricallycheck']==1?true:false);
                  $('#visuallycheck'+div_id).prop('checked',productData[i]['visuallychecked']==1?true:false);

                  $('#mechanicledefectqty'+div_id).prop("readonly",true);
                  $('#electricallydefectqty'+div_id).prop("readonly",true);
                  $('#visuallydefectqty'+div_id).prop("readonly",true);

                  $('#mechanicledefectqty'+div_id).val(parseInt(productData[i]['mechanicledefectqty']));
                  $('#electricallydefectqty'+div_id).val(parseInt(productData[i]['electricallydefectqty']));
                  $('#visuallydefectqty'+div_id).val(parseInt(productData[i]['visuallydefectqty']));
                  $('#Filetext'+div_id).val(productData[i]['filename']);
                  $('#oldtestingfiletext'+div_id).val(productData[i]['filename']);
                  $('#mappingid'+div_id).val(productData[i]['mappingid']);
                  calculatependingqty(div_id);
                }
              });
            }
          }
        }
      }
    },
    error: function(xhr) {
    //alert(xhr.responseText);
    },
  });
}
function getproductDetailsByBatchId(){
  
  var batchno = $("#batchid").val();

  var uurl = SITE_URL+"testing-and-rd/getproductDetailsByBatchId";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {batchno:String(batchno)},
    dataType: 'json',
    async: false,
    success: function(response){
      if(response!=""){
        var productData = response['productData'];
        var TestingId = response['TestingId'];
        // var productData = response['productData'];
        // console.log(productData);

        if(productData!=null && productData!=""){
          if(productData.length>0){
            ACTION = 1;
            $('#testingid').val(TestingId);
            for(var i=0; i<productData.length; i++){

              // updateData
              $('.countproducts').each(function(){
                var transactionproductsid = parseInt($(this).attr('id'));
                var div_id = parseInt($(this).attr('div-id'));

                if(transactionproductsid == productData[i]['transactionproductsid']){

                  $('#mechaniclecheck'+div_id).prop('checked',productData[i]['mechaniclecheck']==1?true:false);
                  $('#electricallycheck'+div_id).prop('checked',productData[i]['electricallycheck']==1?true:false);
                  $('#visuallycheck'+div_id).prop('checked',productData[i]['visuallychecked']==1?true:false);

                  $('#mechanicledefectqty'+div_id).prop("readonly",true);
                  $('#electricallydefectqty'+div_id).prop("readonly",true);
                  $('#visuallydefectqty'+div_id).prop("readonly",true);

                  $('#mechanicledefectqty'+div_id).val(parseInt(productData[i]['mechanicledefectqty']));
                  $('#electricallydefectqty'+div_id).val(parseInt(productData[i]['electricallydefectqty']));
                  $('#visuallydefectqty'+div_id).val(parseInt(productData[i]['visuallydefectqty']));
                  $('#Filetext'+div_id).val(productData[i]['filename']);
                  $('#oldtestingfiletext'+div_id).val(productData[i]['filename']);
                  $('#mappingid'+div_id).val(productData[i]['mappingid']);
                  calculatependingqty(div_id);
                }
              });
            }
          }
        }
      }
    },
    error: function(xhr) {
    //alert(xhr.responseText);
    },
  });
}

function printInvoice(id){

  var uurl = SITE_URL + "purchase-invoice/printPurchaseInvoice";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {id:id},
    //dataType: 'json',
    async: false,
    beforeSend: function() {
        $('.mask').show();
        $('#loader').show();
    },
    success: function(response) {
        
      var data = JSON.parse(response);
      var html = data['content'];
    
      var frame1 = document.createElement("iframe");
      frame1.name = "frame1";
      frame1.style.position = "absolute";
      frame1.style.top = "-1000000px";
      document.body.appendChild(frame1);
      var frameDoc = frame1.contentWindow ? frame1.contentWindow : frame1.contentDocument.document ? frame1.contentDocument.document : frame1.contentDocument;
      frameDoc.document.open();
      frameDoc.document.write(html);
      frameDoc.document.close();
      setTimeout(function () {
        window.frames["frame1"].focus();
        window.frames["frame1"].print();
        document.body.removeChild(frame1);
      }, 500);
    },
    error: function(xhr) {
        //alert(xhr.responseText);
    },
    complete: function() {
        $('.mask').hide();
        $('#loader').hide();
    },
  });
}

function resetdata(){  
  
  $("#process_div").removeClass("has-error is-focused");
  $("#batchid_div").removeClass("has-error is-focused");
  
  
  if(ACTION==0){
      if(VendorId==0){
        $('#vendorid,#grnid').val('0');
        $('#billingaddressid,#shippingaddressid').val('');
        $('#billingaddress').val('');
        $('#shippingaddress').val('');
        $('#grnid')
          .find('option')
          .remove()
          .end()
          .append()
          .val('0')
      ;
      
      $('#billingaddressid,#shippingaddressid')
          .find('option')
          .remove()
          .end()
          .val('whatever')
        ;
      $('#grnid,#billingaddressid,#shippingaddressid').selectpicker('refresh');
      }else{
        $('#vendorid').val(VendorId);
        $('#grnid').val(GRNId);
      }
      $('#remarks').val("");
      $('#invoicedate').val(new Date().toLocaleDateString());
      $('.selectpicker').selectpicker('refresh');
      if(VendorId!=0){
        getProducts();
      }else{
        $("#testingproducttable tbody").html("<tr><td colspan='9' class='text-center'>No data available in table.</td></tr>");
        $('#orderamountdiv').html("");
        $('#extracharges_div').html("");
      }
      // overallextracharges();
      // netamounttotal();
  }

  $('html, body').animate({scrollTop:0},'slow');
}

$('body').on('keyup', '.defectedqty',function (){
  
    var id = $(this).attr('id').match(/\d+/g);
    // alert(id);
    var mechanicledefectqty = ($('#mechanicledefectqty'+id).val()!=''?$('#mechanicledefectqty'+id).val():0);
    var electricallydefectqty = ($('#electricallydefectqty'+id).val()!=''?$('#electricallydefectqty'+id).val():0);
    var visuallydefectqty = ($('#visuallydefectqty'+id).val()!=''?$('#visuallydefectqty'+id).val():0);
    var quantity = $('#quantity'+id).val();
    var pendingqty = $('#pendingqty'+id).text();
    // alert(pendingqty)
    if((parseInt(quantity)-(parseInt(mechanicledefectqty)+parseInt(electricallydefectqty)+parseInt(visuallydefectqty))) < 0){
      // $('#mechanicledefectqty'+id).val('');
      $(this).val('');
      new PNotify({title: "Total of defect qty."+id+" can't greater than qty."+id+" !",styling: 'fontawesome',delay: '3000',type: 'error'});
    }
    calculatependingqty(id);
});

$('body').on('keyup', '.retestingdefectedqty',function (){
  
    var id = $(this).attr('id').match(/\d+/g);
    var mechanicledefectqty = ($('#retestingmechanicledefectqty'+id).val()!=''?$('#retestingmechanicledefectqty'+id).val():0);
    var electricallydefectqty = ($('#retestingelectricallydefectqty'+id).val()!=''?$('#retestingelectricallydefectqty'+id).val():0);
    var visuallydefectqty = ($('#retestingvisuallydefectqty'+id).val()!=''?$('#retestingvisuallydefectqty'+id).val():0);
    var quantity = $('#retestingquantity'+id).val();
    var pendingqty = $('#retestingpendingqty'+id).text();
    
    if((parseInt(quantity)-(parseInt(mechanicledefectqty)+parseInt(electricallydefectqty)+parseInt(visuallydefectqty))) < 0){
      $(this).val('');
      new PNotify({title: "Total of defect qty."+id+" can't greater than qty."+id+" !",styling: 'fontawesome',delay: '3000',type: 'error'});
    }
    calculateretestingpendingqty(id);
});
$('body').on('keyup', '.newretestingdefectedqty',function (){
  
    var id = $(this).attr('id').match(/\d+/g);
    var mechanicledefectqty = ($('#newretestingmechanicledefectqty'+id).val()!=''?$('#newretestingmechanicledefectqty'+id).val():0);
    var electricallydefectqty = ($('#newretestingelectricallydefectqty'+id).val()!=''?$('#newretestingelectricallydefectqty'+id).val():0);
    var visuallydefectqty = ($('#newretestingvisuallydefectqty'+id).val()!=''?$('#newretestingvisuallydefectqty'+id).val():0);
    var quantity = $('#newretestingquantity'+id).val();
    var pendingqty = $('#newretestingpendingqty'+id).text();
    
    if((parseInt(quantity)-(parseInt(mechanicledefectqty)+parseInt(electricallydefectqty)+parseInt(visuallydefectqty))) < 0){
      $(this).val('');
      new PNotify({title: "Total of defect qty."+id+" can't greater than qty."+id+" !",styling: 'fontawesome',delay: '3000',type: 'error'});
    }
    calculatenewretestingpendingqty(id);
});

function checkvalidation(){
  
  var processid = $('#processid').val();
  var batchid =$('#batchid').val();
  var isvalidfile = 1; 
  var isvalidprocessid =  isvalidbatchid = isvalidmechanicledefect = isvalidelectricaldefect = isvalidvisuallydefect = 1;
  PNotify.removeAll();

  if(processid == 0){
    $("#process_div").addClass("has-error is-focused");
    new PNotify({title: "Please select process !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidprocessid = 0;
  }else{
    $("#process_div").removeClass("has-error is-focused");
    isvalidprocessid =1;
  }

  if(batchid == 0){
    $("#batchid_div").addClass("has-error is-focused");
    new PNotify({title: "Please select batch no !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidbatchid = 0;
  }else{
    $("#batchid_div").removeClass("has-error is-focused");
    isvalidbatchid =1;
  }
  
  $('.mechaniclecheck').each(function(){
    
    var id = $(this).attr('id').match(/\d+/g);
    var medefected = $('#mechanicledefectqty'+id).val();
    var file = $('#Filetext'+id).val();

    if($(this).prop('checked')==true || $('#electricallycheck'+id).prop('checked')==true || $('#visuallycheck'+id).prop('checked')==true){
      if(file == ''){
        $('#Filetext'+id+'_div').addClass('has-error is-focused');
        new PNotify({title: "Please upload report "+id+" !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidfile = 0;
      }else{
        $('#Filetext'+id+'_div').removeClass('has-error is-focused');
        //isvalidfile = 1;
      }
    }
    /* if($(".mechaniclecheck").prop('checked')==true){
      if(medefected == ''){
        $('.mechanicledefectdiv'+id).addClass('has-error is-focused');
        new PNotify({title: "Please enter  mechanicle defect quantity "+id+" !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidmechanicledefect = 0;
      }else{
        $('.mechanicledefectdiv'+id).removeClass('has-error is-focused');
        isvalidmechanicledefect =1;
      }
    } */
  });

  /* $('.electricallycheck').each(function(){
    
    var id = $(this).attr('id').match(/\d+/g);
    var eldefected = $('#electricallydefectqty'+id).val();
    
    if($(".electricallycheck").prop('checked')==true){
      if(eldefected == ''){
        $('.electricallydefectdiv'+id).addClass('has-error is-focused');
        new PNotify({title: "Please enter  electrically check quantity "+id+" !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidelectricaldefect = 0;
      }else{
        $('.electricallydefectdiv'+id).removeClass('has-error is-focused');
        isvalidelectricaldefect=1;
      }
    }
  }); */

  /* $('.visuallycheck').each(function(){
    
    var id = $(this).attr('id').match(/\d+/g);
    var videfected = $('#visuallydefectqty'+id).val();
    
    if($(".visuallycheck").prop('checked')==true){
      if(videfected == ''){
        $('.visuallydefectdiv'+id).addClass('has-error is-focused');
        new PNotify({title: "Please enter  visually defect quantity "+id+" !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidvisuallydefect = 0;
      }else{
        $('.visuallydefectdiv'+id).removeClass('has-error is-focused');
        isvalidvisuallydefect=1;
      }
    }
  }); */

  var isvalidproducts = 0;
  $('.countproducts').each(function(){
    var id = $(this).attr('div-id');
    
    if($('#mechaniclecheck'+id).prop('checked')==true || $('#electricallycheck'+id).prop('checked')==true || $('#visuallycheck'+id).prop('checked')==true){
      isvalidproducts = 1;
    }
  });
  if(isvalidproducts == 0){
    new PNotify({title: "Please check any one product !",styling: 'fontawesome',delay: '3000',type: 'error'});
  }

if(isvalidprocessid == 1 && isvalidfile == 1 && isvalidbatchid ==1 && isvalidmechanicledefect ==1 && isvalidelectricaldefect == 1 && isvalidvisuallydefect==1 && isvalidproducts==1){
    
    var formData = new FormData($('#testingform')[0]);
      if(ACTION==1){
        var uurl = SITE_URL+"testing-and-rd/re-testing-and-rd-edit";
      }else{
        var uurl = SITE_URL+"testing-and-rd/re-testing-and-rd-add";
      }
      $.ajax({
        url: uurl,
        type: 'POST',
        data:formData,
        //async: false,
        beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
        },
        success: function(response){
          var obj = JSON.parse(response);
          if(obj==1){
            new PNotify({title: "Testing And R&D successfully added !",styling: 'fontawesome',delay: '3000',type: 'success'});
            setTimeout(function() { window.location = SITE_URL+"testing-and-rd"; }, 1500);
          }else if(obj==2){
            new PNotify({title: "Testing And R&D successfully updated !",styling: 'fontawesome',delay: '3000',type: 'success'});
            setTimeout(function() { window.location = SITE_URL+"testing-and-rd"; }, 1500);
          }else if(obj==3){
            new PNotify({title: "File not uploaded !",styling: 'fontawesome',delay: '3000',type: 'error'});
            // setTimeout(function() { window.location = SITE_URL+"testing-and-rd"; }, 1500);
          }else if(obj==4){
            new PNotify({title: "Invalid File!",styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(obj==5){
            new PNotify({title: "Testing And R&D not updated !",styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(obj==0){
            new PNotify({title: "Testing And R&D not added !",styling: 'fontawesome',delay: '3000',type: 'error'});
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