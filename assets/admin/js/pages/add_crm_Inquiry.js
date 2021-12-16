var memberid = "";
$(document).ready(function() {
  $("#contacts").val($("#contacts option:eq(1)").val());
  $("#contacts").selectpicker("refresh");
 
  $("#existingmemberid").change(function(){
    memberid = $("#existingmemberid").val();
    getcontacts(memberid);
    if(ACTION == 0){
      addcontactbtn = $("#addcontactbtn").prop("disabled",false);
      $("#contacts").val($("#contacts option:eq(1)").val());
    }
    $("#contacts").selectpicker("refresh");
  });

  $('#leadsource').change(function(){
    $('#inquiryleadsource').val(this.value);
    $("#inquiryleadsource").selectpicker("refresh");
  });

  /* $('.quotationfile').change(function() {
    validfile($(this), this);
  }); */
  
  if(ACTION == 1){
    inquiryassignto = $("#oldinquiryassignto").val();
    inquiryassignname = $("#oldinquiryassignname").val();
    if($("#inquiryemployee").val()==0 && inquiryassignto!=0){
      
      $("#inquiryemployee").append("<option value='"+inquiryassignto+"' class='newoptions'>"+inquiryassignname+"<option>");
      $("#inquiryemployee").val(inquiryassignto);
      $("#inquiryemployee").prop("disabled",true);
    }else{
      // $("#inquiryemployee").prop("disabled",false);
    }
    $(".selectpicker").selectpicker("refresh");
    totalnetamount();
  }

  $('form').on('reset', function(e){
    setTimeout(function() {resetdata();});
  });
  getprovince($('#countryid').val());
  getcity($('#provinceid').val());
  getarea($('#cityid').val());

  $('#countryid').on('change', function (e) {
        
    $('#provinceid')
      .find('option')
      .remove()
      .end()
      .append('<option value="">Select State</option>')
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
      .append('<option value="">Select Area</option>')
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
    .append('<option value="">Select Area</option>')
    .val('0')
  ;
  $('#cityid').selectpicker('refresh');
    $('#areaid').selectpicker('refresh');    
    getcity(this.value);
  });

  $('#cityid').on('change', function (e) {
      
    $('#areaid')
      .find('option')
      .remove()
      .end()
      .append('<option value="">Select City</option>')
      .val('0')
    ;
    $('#areaid').selectpicker('refresh');
    getarea(this.value);
  });

  $('.app-body').on('click', '.modal .close', function () {
    $(this).closest('.modal').modal('hide');
  });

  var date = new Date();
  date.setDate(date.getDate());
  displaydate = date.getDate()+"/"+(date.getMonth()+1)+"/"+date.getFullYear();
  
  $('.followupdate').daterangepicker({
    singleDatePicker: true,
    showDropdowns: true,
    autoUpdateInput: true,
    timePicker: true,
    /*timePicker24Hour: true,*/
    //minYear: 1901,
    minDate: displaydate,
    locale: {
      format: 'DD/MM/YYYY HH:mm'
    },
  });

  $('.quotationdate').datepicker({
      todayHighlight: true,
      format: 'dd/mm/yyyy',
      orientation: 'top',
      autoclose: true,
      todayBtn: "linked"
  });

  $('.js-data-example-ajax').select2({
    placeholder: "Select "+Member_label,
    // minimumInputLength: 3,
    ajax: {
      url: SITE_URL+'crm-inquiry/getmembers',
      dataType: 'json',
      quietMillis: 100,
      type:"post",
      data: function (term, page) { // page is the one-based page number tracked by Select2
        return {
            term: term, //search term
            page_limit: 25, // page size
            page: page, // page number
        };
      },
      results: function (data, page) {
        var more = (page * 25) < data.total; // whether or not there are more results available
        result = data.results;
        // notice we return the value of more so Select2 knows if more results can be loaded
        return {results: result, more: more};
      }
    },
    initSelection: function(element, callback) {
      if($("#existingmemberid").val()!=""){
        callback({id: $("#existingmemberid").val(), text: $("#existingmemberid").attr("data-text") });
        getcontacts($("#existingmemberid").val());
        $("#contacts").val($("#contacts option:eq(1)").val());
        $("#contacts").selectpicker("refresh");
      }
    },
    formatResult: styleFormatResult, // omitted for brevity, see the source of this page
    dropdownCssClass: "bigdrop", // apply css that makes the dropdown taller
    escapeMarkup: function (m) { return m; } // we do not want to escape markup since we are displaying html in results
});

  if(ACTION == 0){
    $("#installmentsetting_div").hide();
    $("#emidate").val("");
    $("#emiduration").val("");
    $("#installmentmaindivheading").hide();
  }

  $('#emidate').datepicker({
    todayHighlight: true,
    format: 'dd/mm/yyyy',
    autoclose: true,
    todayBtn:"linked",
    orientation:"bottom"
  });

  if(ACTION == 1){ 
      $('.installmentdate').datepicker({
            todayHighlight: true,
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayBtn:"linked",
            orientation:"bottom"
        });
      $('.paymentdate').datepicker({
            todayHighlight: true,
            format: 'dd/mm/yyyy',
            autoclose: true,
            endDate:new Date(),
            todayBtn:"linked",
            orientation:"bottom"
        });
      if($("#noofinstallment").val()==""){
        $("#installmentsetting_div").hide();
        $("#installmentmaindivheading").hide(); 
        /* $("#noofinstallment").prop("disabled",true);
        $("#generateinstallment").prop("disabled",true);
             */
      }
  }


  $("input[name=installmentstatus]").click(function(){
      console.log($("input[name=installmentstatus]:checked").val());
      if($("input[name=installmentstatus]:checked").val()==1){
        $("#installmentsetting_div").show();
        // $("#installmentmaindivheading").show();
        
        //$('#paymentdate_div').hide();
      }else{
        $("#installmentsetting_div").hide();
        //$('#paymentdate_div').show();
        $("#noofinstallment").val("");
        $("#emidate").val("");
        $("#emiduration").val("");
        $("#installmentmaindivheading").hide();
        $("#installmentdivs").html("");
      }
      changestatus();
  })

  $(document).on('change', '.percentage', function() {
    if($(this).val()==""){
      $(this).val(0);
      changeinstallmentamount();
    }
    totalnetamount();
  })

  $(document).on('keyup', '.percentage', function() {
      totalpercentage = 0;
      $(".percentage").each(function(value,index){
        if($(this).val()!=""){
          totalpercentage += parseFloat($(this).val());
        }
      })
      if(totalpercentage > 100){
        $(this).val(0);
        new PNotify({title: "Total installment can not be more than 100.",styling: 'fontawesome',delay: '3000',type: 'error'});
      }
      changeinstallmentamount();
      totalnetamount();
  });

  $("#generateinstallment").click(function(){
    $("#installmentdivs").html("");
    noofinstallmentval = $("#noofinstallment").val();
    noofinstallmentdiv = $(".noofinstallmentdiv").length;
    emidate = $("#emidate").val();
    emiduration = $("#emiduration").val();
    
    if(noofinstallmentval=="" || emidate=="" || emiduration==""){
      return false;
    }

    totalvalue=0;
    $('.productsnetamount').each(function (index, value) {
      if($(this).val()!=""){
        totalvalue = totalvalue+parseFloat($(this).val());
      }
    })
    installmentamount = (parseFloat(totalvalue)/parseFloat(noofinstallmentval)).toFixed(2);
    installmentpercentage = (100/parseFloat(noofinstallmentval)).toFixed(2);

    $("#installmentmaindivheading").show();
    /* console.log(dd+"/"+mm+"/"+yy);
    console.log(emidate);
    console.log(datearray[2]+"-"+datearray[1]+"-"+datearray[0]); */
    /* console.log(tomorrow.getDate());
    console.log(tomorrow.getMonth()+ 1);return false; */
    
    // console.log(dd+"/"+mm+"/"+yy);
    // console.log(installmentamount);
    /*if(noofinstallmentdiv==0){
      noofinstallmentdiv=1;
    }*/
    var datearray = emidate.split("/");
    var emidate = new Date(datearray[2]+"-"+datearray[1]+"-"+datearray[0]);
    emidurationval=0;
    percentagetotal=0;
    amounttotal=0;
    $('#installmentmaindiv').find(".noofinstallmentdiv").slice( noofinstallmentval,noofinstallmentdiv).remove();
    for (var i = 0; i <= noofinstallmentval-1; i++) {

      if(emidurationval==0){
        emidate.setDate(emidate.getDate());
      }else{
        emidate.setDate(emidate.getDate()+parseInt(emiduration));
      }
      if(i == noofinstallmentval-1){
        installmentpercentage = (100-parseFloat(percentagetotal)).toFixed(2);
        installmentamount = (parseFloat(totalvalue)-parseFloat(amounttotal)).toFixed(2);
      }
      percentagetotal = parseFloat(percentagetotal)+parseFloat(installmentpercentage);
      amounttotal = parseFloat(amounttotal)+parseFloat(installmentamount);
      emidurationval=1;
      var dd = emidate.getDate();
      var mm = emidate.getMonth()+ 1;
      var yy = emidate.getFullYear();
      installmentdate = dd+"/"+mm+"/"+yy;

      $("#installmentdivs").append('<div class="row noofinstallmentdiv">\
          <div class="col-md-2 text-center">'+(i+1)+'</div>\
          <div class="col-md-2 text-center">\
            <div class="col-md-12 pl-n">\
              <div class="form-group mt-n">\
                <input type="text" id="percentage'+(i+1)+'" value="'+installmentpercentage+'" name="percentage[]" class="form-control text-right percentage"  div-id="'+(i+1)+'" maxlength="5" onkeyup="return onlypercentage(this.id)" onkeypress="return decimal(event,this.id)">\
              </div>\
            </div>\
          </div>\
          <div class="col-md-2 text-center">\
            <div class="col-md-12 pl-n">\
              <div class="form-group mt-n">\
                <input type="text" id="installmentamount'+(i+1)+'" value="'+installmentamount+'" name="installmentamount[]" class="form-control text-right installmentamount" div-id="'+(i+1)+'" maxlength="5" onkeypress="return decimal(event,this.id);" readonly>\
                </div>\
            </div>\
          </div>\
          <div class="col-md-2 text-center">\
            <div class="col-md-12 pl-n">\
              <div class="form-group mt-n">\
                <input type="text" id="installmentdate'+(i+1)+'" value="'+installmentdate+'" name="installmentdate[]" class="form-control" div-id="'+(i+1)+'" maxlength="5">\
              </div>\
            </div>\
          </div>\
          <div class="col-md-2 text-center">\
            <div class="col-md-12 pl-n">\
              <div class="form-group mt-n">\
                <input type="text" id="paymentdate'+(i+1)+'" value="" name="paymentdate[]" class="form-control" div-id="'+(i+1)+'" maxlength="5">\
              </div>\
            </div>\
          </div>\
          <div class="col-md-2 text-center">\
            <div class="checkbox">\
                <input id="installmentstatus'+(i+1)+'" type="checkbox" value="1" name="installmentstatus'+(i+1)+'" div-id="'+(i+1)+'" class="checkradios">\
                <label for="installmentstatus'+(i+1)+'"></label>\
            </div>\
          </div>\
        </div>');

      $('#installmentdate'+(i+1)).datepicker({
          todayHighlight: true,
          format: 'dd/mm/yyyy',
          autoclose: true,
          todayBtn:"linked",
          orientation:"bottom"
      });
      $('#paymentdate'+(i+1)).datepicker({
          todayHighlight: true,
          format: 'dd/mm/yyyy',
          autoclose: true,
          endDate:new Date(),
          todayBtn:"linked",
          orientation:"bottom"
      });

    }
  })
  $('.datepicker1').datepicker({
      todayHighlight: true,
      format: 'dd/mm/yyyy',
      autoclose: true,
      todayBtn:"linked",
      clearBtn: true
      /*orientation:"bottom"*/
  });
  $('#date').datepicker({
      todayHighlight: true,
      format: 'dd/mm/yyyy',
      autoclose: true,
      todayBtn:"linked",
      orientation:"bottom"
  });

  if(member_id!="0"){
    $("#existingmember_div").show();
    $("#existingmemberid").val(member_id);
    $("#existingmemberid").selectpicker("refresh");
    $("#memberdetail").hide();
    $("#existingmembercontactdiv").show();
    $("#contacts").selectpicker("refresh");
  }else{
    $("#existingmember_div").hide();
    if(ACTION==1){
      $("#existingmembercontactdiv").show();
    }else{
      $("#existingmembercontactdiv").hide();
    }
  }

  $("#new_existing_memberid").change(function() {
    if($("#new_existing_memberid").val()=="new")
    {
      $("#memberdetail").show('1000');
      $("#existingmember_div").hide('1000');
      $("#existingmembercontactdiv").hide('1000');
    }
    if($("#new_existing_memberid").val()=="existing")
    {
      $("#memberdetail").hide('1000');
      $("#existingmember_div").show('1000');
      $("#existingmembercontactdiv").show('1000');
    }
  })
  
  $("#companyname").change(function(){
    if($("#companyname").val()!=""){      
      checkduplicate("companyname",$("#companyname").val(),memberid,$("#name").val());
    }else{
      $("#companynameduplicatemessage").hide();
    }
  });

  $(".mobileno:first").change(function(){
    if($(".mobileno:first").val()!=""){  
      checkduplicate("mobileno",$(".mobileno:first").val(),memberid);
    }else{
      $("#mobilenoduplicatemessage").hide();
    }
  });


  $(".email:first").change(function(){
    if($(".email:first").val()!=""){  
      checkduplicate("email",$(".email:first").val(),memberid);
    }else{
      $("#emailduplicatemessage").hide();
    }
  });

  $(".newcontactmobileno:first").change(function(){
    if($(".newcontactmobileno:first").val()!=""){  
      checkduplicate("mobileno",$(".newcontactmobileno:first").val(),memberid);
    }else{
      $("#mobilenoduplicatemessage").hide();
    }
  });

  $(".newcontactemail:first").change(function(){
    if($(".newcontactemail:first").val()!=""){  
      checkduplicate("email",$(".newcontactemail:first").val(),memberid);
    }else{
      $("#emailduplicatemessage").hide();
    }
  });

  if(ACTION==1){
    $("#inquiryemployee").change(function()
    {
      if($("#oldinquiryassignto").val()==$("#inquiryemployee").val())
      {
        $("#reason_div").hide();
      }else{
        $("#reason_div").show();
      }
    })
  }
  $(document).on('change', 'select.productcategory', function() { 
    var divid = $(this).attr("prow");
    
    $('#qty'+divid).val(1);
    $('#productrate'+divid+",#discountpercent"+divid+",#discount"+divid+",#amount"+divid+",#tax"+divid+",#taxvalue"+divid+",#netamount"+divid).val('');

    getproduct(divid);
    totalnetamount();
  });

  $(document).on('change', 'select.product', function() { 
    var divid = $(this).attr("product-select-id");

    $('#qty'+divid).val(1);
    $('#productrate'+divid+",#discountpercent"+divid+",#discount"+divid+",#amount"+divid+",#tax"+divid+",#taxvalue"+divid+",#netamount"+divid).val('');

    getproductprice(divid);
    changeinstallmentamount();
    totalnetamount();
  });

  $(document).on('change', 'select.priceid', function() { 
    var eleid = $(this).attr("variant-select-id");

    if(this.value != 0){
      var price = $("#priceid"+eleid+" option:selected").attr("data-price");
      var discount = $("#product"+eleid+" option:selected").attr("data-discount");
      var tax = $("#product"+eleid+" option:selected").attr("data-tax");

      if(PRODUCT_DISCOUNT==0){
        $("#discountpercent"+eleid).val('');
      }else{
        $("#discountpercent"+eleid).val(parseFloat(discount).toFixed(2));
      }
      $("#tax"+eleid).val(parseFloat(tax).toFixed(2));     
      $("#productrate"+eleid).val(parseFloat(price).toFixed(2));     
      $("#qty"+eleid).val(1);
    }else{
      $("#productrate"+eleid).val('');
      $("#discountpercent"+eleid).val('');
      $("#tax"+eleid).val('');  
    }
    
    changeamount(eleid);
    changeinstallmentamount();
    totalnetamount();
  });

  $(".qty").TouchSpin(touchspinoptions);

  $(".add_btn_product").hide();
  $(".add_btn_product:last").show();

  $(".file_add_btn").hide();
  $(".file_add_btn:last").show();
});

function validquotationfile(obj,element,elethis){
  var val = obj.val();
  var id = element.match(/\d+/);
  var filename = obj.val().replace(/C:\\fakepath\\/i, '');
  
  if(elethis.files[0].size <= UPLOAD_MAX_FILE_SIZE){

      switch(val.substring(val.lastIndexOf('.') + 1).toLowerCase()){
        case 'jpe': case 'pbm': case 'jpg': case 'jpeg': case 'png': case 'pdf': case 'doc': case 'docx':
         
          isvalidquotationfile = 1;
          $("#Filetext"+id).val(filename);
          $("#"+element+"_div").removeClass("has-error is-focused");
          break;
        default:
          isvalidquotationfile = 0;
          $("#"+element).val("");
          $("#Filetext"+id).val("");
          $("#"+element+"_div").addClass("has-error is-focused");
          new PNotify({title: 'File type does not valid !',styling: 'fontawesome',delay: '3000',type: 'error'});
          break;
      }
  }else{
    isvalidquotationfile = 0;
      $("#"+element).val("");
      $("#Filetext"+id).val("");
      $("#"+element+"_div").addClass("has-error is-focused");
      new PNotify({title: 'File is too large (max size ' + formatBytes(UPLOAD_MAX_FILE_SIZE)+') !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
}

function getproduct(divid){

  $('#product'+divid).find('option')
      .remove()
      .end()
      .append('<option value="">Select Product</option>')
      .val('0')
  ;
  $('#priceid'+divid).find('option')
      .remove()
      .end()
      .append('<option value="">Select Variant</option>')
      .val('0')
  ;
  var productcategory = $("#productcategory"+divid).val();
  
  if(productcategory!='' && productcategory!=0){
    var uurl = SITE_URL+"crm-inquiry/getProduct";
    
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {productcategory:productcategory},
      dataType: 'json',
      async: false,
      success: function(response){
          for(var i = 0; i < response.length; i++) {
            
            var productname = response[i]['name'].replace("'","&apos;");
            if(DROPDOWN_PRODUCT_LIST==1){
                
                $('#product'+divid).append($('<option>', { 
                    value: response[i]['id'],
                    text : productname,
                    "data-tax" : response[i]['tax'],
                    "data-discount" : response[i]['discount']
                }));
            }else{
                
              $('#product'+divid).append($('<option>', { 
                value: response[i]['id'],
                //text : ucwords(response[i]['name'])
                "data-tax" : response[i]['tax'],
                "data-discount" : response[i]['discount'],
                "data-content" :'<img src="'+PRODUCT_PATH+response[i]['image']+'" style="width:40px">  ' + response[i]['name']
              }));
            }
          }

          if(productid[divid-1]!=0){
            $('#product'+divid).val(productid[divid-1]);
          }
      },
      error: function(xhr) {
      //alert(xhr.responseText);
      },
    });
  }
  $('#product'+divid).selectpicker('refresh');
  $('#priceid'+divid).selectpicker('refresh');
}

function getproductprice(divid){

  $('#priceid'+divid).find('option')
      .remove()
      .end()
      .append('<option value="">Select Variant</option>')
      .val('0')
  ;
  
  $('.selectpicker').selectpicker('refresh');
  var productid = $("#product"+divid).val();
  
  if(productid!='' && productid!=0){
    var uurl = SITE_URL+"crm-inquiry/getVariant";
    
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {productid:productid},
      dataType: 'json',
      async: false,
      success: function(response){

          for(var i = 0; i < response.length; i++) {
            $('#priceid'+divid).append($('<option>', { 
              value: response[i]['id'],
              text : response[i]['memberprice'],
              "data-price" : response[i]['price']
            }));
          }
          if(priceid[divid-1]!=0){
            $('#priceid'+divid).val(priceid[divid-1]);
          }
      },
      error: function(xhr) {
      //alert(xhr.responseText);
      },
    });
  }
  $('#priceid'+divid).selectpicker('refresh');
}

function addnewproduct(){

  if(PRODUCT_DISCOUNT==0){
      discount = "display:none;";
  }else{ 
      discount = "display:block;"; 
  }
  var readonly = "readonly";
  if(EDITTAXRATE_SYSTEM==1){
      readonly = "";
  }

  divcount = parseInt($(".countproducts:last").attr("id").match(/\d+/))+1;
  producthtml = '<div id="productrow'+divcount+'" class="countproducts"><hr style="height: 1px;background: lightblue;">';
  producthtml += '<div class="row ml3 mr4">\
                    <div class="col-md-4">\
                      <div class="form-group productcategorydiv" id="productcategory'+divcount+'_div">\
                        <label class="control-label" for="productcategory'+divcount+'">Product Category <span class="mandatoryfield">*</span></label>\
                        <div class="col-md-12 pl-n">\
                          <select prow="'+divcount+'" id="productcategory'+divcount+'" name="productcategory[]" class="selectpicker form-control productcategory" data-live-search="true" data-size="8">\
                            <option value="0">Select Product Category</option>\
                            '+categoryoptionhtml+'\
                          </select>\
                        </div>\
                      </div>\
                    </div>\
                    <div class="col-md-4">\
                      <div class="form-group productdiv" id="product'+divcount+'_div">\
                          <label class="control-label" for="product'+divcount+'">Product <span class="mandatoryfield">*</span></label>\
                          <div class="col-md-12 pl-n">\
                              <select product-select-id="'+divcount+'" id="product'+divcount+'" name="product[]" class="selectpicker form-control product" data-live-search="true" data-size="8">\
                                  <option value="0">Select Product</option>\
                              </select>\
                          </div>\
                      </div>\
                    </div>\
                    <div class="col-md-4">\
                      <div class="form-group pricediv" id="price'+divcount+'_div">\
                          <label class="control-label" for="priceid'+divcount+'">Variant <span class="mandatoryfield">*</span></label>\
                          <div class="col-md-12 pl-n">\
                              <select variant-select-id="'+divcount+'" id="priceid'+divcount+'" name="priceid[]" class="selectpicker form-control priceid" data-live-search="true" data-size="8">\
                                  <option value="0">Select Variant</option>\
                              </select>\
                          </div>\
                      </div>\
                  </div>\
              </div>';
            
  producthtml += '<div class="row ml3 mr4">\
                    <div class="col-md-1">\
                      <div class="form-group qtydiv" id="qty'+divcount+'_div">\
                        <div class="col-md-12 pl-n">\
                          <label class="control-label" for="qty'+divcount+'">Qty. <span class="mandatoryfield">*</span></label>\
                          <input type="text" id="qty'+divcount+'" value="" name="qty[]" class="qty form-control text-right" onkeypress="'+(MANAGE_DECIMAL_QTY==1?"return decimal_number_validation(event, this.value,8);":"return isNumber(event);")+'" onchange="changeamount('+divcount+')">\
                        </div>\
                      </div>\
                  </div>\
                  <div class="col-md-2">\
                      <div class="form-group ratediv" id="productrate'+divcount+'_div">\
                        <div class="col-md-12 pl-n text-right">\
                          <label class="control-label" for="productrate'+divcount+'">Rate ('+CURRENCY_CODE+')<span class="mandatoryfield">*</span></label>\
                          <input type="text" id="productrate'+divcount+'" value="" name="productrate[]" class="form-control text-right productrate" onkeypress="return decimal(event,this.id);" onchange="changeamount('+divcount+')">\
                        </div>\
                      </div>\
                  </div>\
                <div class="col-md-1" style="'+discount+'">\
                    <div class="form-group discountpercentdiv" id="discountpercent'+divcount+'_div">\
                      <div class="col-md-12 pl-n text-right">\
                        <label class="control-label" for="discountpercent'+divcount+'">Dis. (%) </label>\
                        <input type="text" id="discountpercent'+divcount+'" value="" name="discountpercent[]" class="form-control text-right"  onkeyup="return onlypercentage(this.id)" onchange="changediscount('+divcount+')" onkeypress="return decimal(event,this.id);">\
                      </div>\
                    </div>\
                </div>\
                <div class="col-md-1" style="'+discount+'">\
                    <div class="form-group discountdiv" id="discount'+divcount+'_div">\
                      <div class="col-md-12 pl-n text-right">\
                        <label class="control-label" for="discount'+divcount+'">Dis. ('+CURRENCY_CODE+')</label>\
                        <input type="text" id="discount'+divcount+'" value="" name="discount[]" class="form-control text-right discount" onchange="changepercentage('+divcount+')" onkeypress="return decimal(event,this.id);" div-id="'+divcount+'">\
                      </div>\
                    </div>\
                </div>\
                <div class="col-md-2">\
                    <div class="form-group amountdiv" id="amount'+divcount+'_div">\
                      <div class="col-md-12 pl-n text-right">\
                        <label class="control-label" for="amount'+divcount+'">Amount ('+CURRENCY_CODE+')<span class="mandatoryfield">*</span></label>\
                        <input type="text" id="amount'+divcount+'" value="" name="amount[]" class="form-control text-right productsamount" readonly onkeypress="return decimal(event,this.id);">\
                      </div>\
                    </div>\
                </div>\
                <div class="col-md-1">\
                    <div class="form-group taxdiv" id="tax'+divcount+'_div">\
                      <div class="col-md-12 pl-n text-right">\
                        <label class="control-label" for="tax'+divcount+'">Tax (%)<span id="displaytax"></span></label>\
                        <input type="hidden" class="taxvalue" name="taxvalue[]" id="taxvalue'+divcount+'">\
                        <input type="text" id="tax'+divcount+'" value="" name="tax[]" class="form-control text-right tax" maxlength="5" onkeypress="return decimal(event,this.id);" onchange="changeamount('+divcount+')" '+readonly+'>\
                      </div>\
                    </div>\
                </div>\
                <div class="col-md-2">\
                    <div class="form-group netamountdiv" id="netamount'+divcount+'_div">\
                      <div class="col-md-12 pl-n text-right">\
                        <label class="control-label" for="netamount'+divcount+'">Net Amount ('+CURRENCY_CODE+')<span class="mandatoryfield">*</span></label>\
                        <input type="text" id="netamount'+divcount+'" value="" name="netamount[]" class="form-control text-right productsnetamount" readonly onkeypress="return decimal(event,this.id);">\
                      </div>\
                    </div>\
                </div>\
                <div class="col-md-2 pt-xxl">\
                    <button type="button" class="btn btn-danger btn-raised remove_btn_product" onclick="removeproduct('+divcount+')" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>\
                    <button type="button" class="btn btn-primary btn-raised add_btn_product" onclick="addnewproduct()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>\
                </div>\
            </div>\
        </div>';

  $(".remove_btn_product:first").show();
  $(".add_btn_product:last").hide();
  $("#productrow"+(divcount-1)).after(producthtml);

  $("#qty"+divcount).TouchSpin(touchspinoptions);

  $(".selectpicker").selectpicker("refresh");
  totalnetamount();
}
function removeproduct(divid){

  /* if($('select[name="productcategory[]"]').length!=1 && ACTION==1 && $('#orderproductsid'+divid).val()!=null){
      var removeorderproductid = $('#removeorderproductid').val();
      $('#removeorderproductid').val(removeorderproductid+','+$('#orderproductsid'+divid).val());
  } */
  $("#productrow"+divid).remove();

  $(".add_btn_product:last").show();
  if ($(".remove_btn_product:visible").length == 1) {
      $(".remove_btn_product:first").hide();
  }
  changeinstallmentamount();
  totalnetamount();
}

function checkduplicate(duplicatetype,fieldvalue,memberid,membername,id) {
  membername = membername || "";
  id = id || "";
  $.ajax({
      url: SITE_URL+"member/checkduplicate",
      type: 'POST',
      data: {"type":duplicatetype,"value":fieldvalue,"memberid":memberid,"membername":membername},
      beforeSend: function(){
      },
      success: function(response){
        /* if(parseInt(response)>0){
          $("#"+duplicatetype+"duplicatemessage").html("DUPLICATE");
        }else{
          $("#"+duplicatetype+"duplicatemessage").html("");
        } */

        if(memberid==""){
          memberid=0;
        }
        if(parseInt(response)>0){
          var msg = "";
          if(duplicatetype=="mobileno"){
            msg = "Mobile number already exist !";
          }else if(duplicatetype=="email"){
            msg = "Email already exist !";
          }else if(duplicatetype=="companyname"){
            msg = "Company name already exist !";
          }
          $("#"+duplicatetype+"duplicatemessage"+id).html(msg);
          $("#"+duplicatetype+"duplicatemessage"+id).show();
        }else{
          $("#"+duplicatetype+"duplicatemessage"+id).html("");
        }

      },
      error: function(xhr) {
      },
      complete: function(){
      },
    });
}
function styleFormatResult(style) {
  var markup = style.text;
  return markup;
}

function fill_product_dropdown(product_id,edit_fill="",my_product_edit="")
{
    var prow=product_id;
    var productcategory = $("#productcategory"+product_id).val();
    if(my_product_edit!="")
    {
      prow=my_product_edit;
    }
    if(product_id==1 && my_product_edit!="")
    {
        var productcategory = $("#productcategory").val();
    }
    getproduct(prow);
}

function changediscount(selectid) {
  
  product_rate =  parseFloat($("#productrate"+selectid).val())*parseFloat($("#qty"+selectid).val());
  discountpercent = parseFloat($("#discountpercent"+selectid).val());
  $("#discount"+selectid).val(((product_rate*discountpercent)/100));
  
  changeamount(selectid);
  totalnetamount();
}

function changepercentage(selectid) {
  product_rate =  parseFloat($("#productrate"+selectid).val())*parseFloat($("#qty"+selectid).val());
  discount = parseFloat($("#discount"+selectid).val());

  if(isNaN(discount)){
    discount = parseFloat(0);
  }
  discountpercent = $("#discountpercent"+selectid).val((parseFloat(discount)*100/parseFloat(product_rate)).toFixed(2));
  
  changeamount(selectid);
  totalnetamount();
}

function changeamount(selectid) {
  
  qty = $("#qty"+selectid).val()=="" ? $("#qty"+selectid).val(0) : parseFloat($("#qty"+selectid).val());
  product_rate = $("#productrate"+selectid).val()=="" ? $("#productrate"+selectid).val(0) :  parseFloat($("#productrate"+selectid).val());
  discount = $("#discount"+selectid).val()=="" || isNaN($("#discount"+selectid).val()) ? $("#discount"+selectid).val(0) : parseFloat($("#discount"+selectid).val());
  tax = $("#tax"+selectid).val()=="" ? $("#tax"+selectid).val(0) : parseFloat($("#tax"+selectid).val());
  taxvalue = $("#taxvalue"+selectid).val()=="" ? $("#taxvalue"+selectid).val(0) : parseFloat($("#taxvalue"+selectid).val());
  discountpercent = $("#discountpercent"+selectid).val()=="" ? $("#discountpercent"+selectid).val(0) : parseFloat($("#discountpercent"+selectid).val());
  amount = $("#amount"+selectid).val()=="" || isNaN($("#amount"+selectid).val()) ? $("#amount"+selectid).val(0) : parseFloat($("#amount"+selectid).val());
  netamount = $("#netamount"+selectid).val()=="" ? $("#netamount"+selectid).val(0) : parseFloat($("#netamount"+selectid).val());

  if(isNaN(discount)){
    discount = parseFloat(0);
  }
  if(isNaN(discountpercent)){
    discountpercent = parseFloat(0);
  }
  if(isNaN(qty)){
    qty = parseFloat(0);
  }
  if(isNaN(product_rate)){
    product_rate = parseFloat(0);
  }
  if(isNaN(tax)){
    tax = parseFloat(0);
  }
  if(product_rate==0){
    $('#productrate'+selectid+",#discountpercent"+selectid+",#discount"+selectid+",#amount"+selectid+",#tax"+selectid+",#taxvalue"+selectid+",#netamount"+selectid).val('');
  }else{
    product_rate =  parseFloat($("#productrate"+selectid).val());
    discount = parseFloat($("#discount"+selectid).val());

    if(isNaN(discount)){
      $("#discount"+selectid).val(0);
      $("#discountpercent"+selectid).val(0);
    }else{
      if(PRODUCT_DISCOUNT==0){
        $("#discount"+selectid).val(0);
        $("#discountpercent"+selectid).val(0);
      }else{
        amount = product_rate*qty;
        if(!isNaN(((discount*100)/amount))){
          $("#discount"+selectid).val(((amount*discountpercent)/100).toFixed(2));
        }else{
          $("#discountpercent"+selectid).val(0);
        } 
      }
    }
    discount = $("#discount"+selectid).val();
    totalamount = (qty*product_rate)-discount;

    if(GST_PRICE == 1){
      taxvalue = ((parseFloat(totalamount)*parseFloat(tax))/100).toFixed(2);
      totalnetamountval = parseFloat(totalamount) + parseFloat(taxvalue);
    }else{
      taxvalue = ((parseFloat(totalamount)*parseFloat(tax))/(100+parseFloat(tax))).toFixed(2);
      totalnetamountval = parseFloat(totalamount);
    }
    
    !isNaN(taxvalue) ? $("#taxvalue"+selectid).val(parseFloat(taxvalue).toFixed(2)) : parseFloat($("#taxvalue"+selectid).val(0));
    !isNaN(totalnetamountval) ? $("#netamount"+selectid).val(parseFloat(totalnetamountval).toFixed(2)) : $("#netamount"+selectid).val(0);
    $("#amount"+selectid).val(parseFloat(totalamount).toFixed(2));
    $("#netamount"+selectid).val(parseFloat(totalnetamountval).toFixed(2));
  }
  changeinstallmentamount();
  totalnetamount();
}

function onlypercentage(val){
    fieldval = $("#"+val).val();
    if (parseInt(fieldval) < 0) $("#"+val).val(0);
    if (parseInt(fieldval) > 100) $("#"+val).val(100);
    totalnetamount();
}

function changeinstallmentamount() {  
  totalamount = 0;
  if($(".installmentamount").length > 0 && $(".productsnetamount").length > 0){
    $(".productsnetamount").each(function(value,index){
      if($(this).val()!=""){
        totalamount += parseFloat($(this).val());
      }
    })
    $(".installmentamount").each(function(value,index){
      divid = $(this).attr("div-id");
      if($("#percentage"+divid).val()!=""){      
        percentage = parseFloat($("#percentage"+divid).val());
        $(this).val(((totalamount*percentage)/100).toFixed(2));
      }
    })
  }
  totalnetamount();
}

function getmembersdetail(duplicatetype,fieldvalue,memberid,membername=""){
  if(duplicatetype=="companyname"){
    fieldvalue = $("#companyname").val();
  }/*else if(duplicatetype=="mobileno"){
    fieldvalue = $(".mobileno:first").val();
  }else{
    fieldvalue = $(".email:first").val();
  }*/
  $.ajax({
    url: SITE_URL+"member/duplicatemember",
    type: 'POST',
    dataType:"json",
    data: {"type":duplicatetype,"value":fieldvalue,"memberid":memberid,"membername":membername},
    beforeSend: function(){
    },
    success: function(response){

      if(duplicatetype=='companyname'){
        $("#duplicate_title").html(Member_label+" With Same Company");
      }else if(duplicatetype=='mobileno'){
        $("#duplicate_title").html(Member_label+" With Same Mobile No");
      }else if(duplicatetype=='email'){
        $("#duplicate_title").html(Member_label+" With Same Email");
      }
      
      if(response.length>0){

        var memberhtml='<tr>\
          <th>S.No</th>\
          <th>Company Name</th>\
          <th>'+Member_label+' Name</th>\
          <th>Mobile No</th>\
          <th>Email</th>\
          <th>City</th>\
          <th>Date</th>\
          <th>Assigned To</th>\
          <th>Remarks</th>\
          <th>'+Inquiry_label+' Notes</th>\
        </tr>';
        $.each(response, function(i, item) {
          if(item.assigntoname==null){
            item.assigntoname="-";
          }
          if(item.memberremark==null){
            item.memberremark="-";
          }
          inquirynotesstr="";
          if(item.inquirynotes==null){
            item.inquirynotes="-";
          }else{
            inquirynotes = (item.inquirynotes).split("|");
            jQuery.each(inquirynotes, function() {
              inquirynotesstr += this + ",<br>";
            });
          }
          if(item.city==null){
            item.city="-";
          }
          memberhtml += '<tr>\
                          <td>'+(i+1)+'</td>\
                          <td>'+item.companyname+'</td>\
                          <td>'+item.name+'</td>\
                          <td>'+item.mobileno+'</td>\
                          <td>'+item.email+'</td>\
                          <td>'+item.city+'</td>\
                          <td>'+item.createddate+'</td>\
                          <td>'+item.assigntoname+'</td>\
                          <td>'+item.memberremark+'</td>\
                          <td>'+inquirynotesstr+'</td>\
                        </tr>';
        })
        $("#duplicatemembertable").html(memberhtml);
        $('#duplicatemembermodal').modal('toggle');
      }else{
        $("#duplicatemembertable").html("");
        $("#"+duplicatetype+"duplicatemessage").html("");
      }
    },
    error: function(xhr) {
    },
    complete: function(){
    },
  });
}

function addnewcontact(){
  contactcount = $(".contactdiv:last").attr("div-id");
  contactheading = $(".contactheading:last").attr("heading-id");
  
  divid = parseInt(contactcount)+1;
  
  contacthtml = '<div class="contactdiv" id="contactdiv'+divid+'" div-id="'+divid+'">\
        <div class="row ml3 mr4">\
        <div class="col-md-6">\
          <div class="radio radio1">\
            <input type="radio" name="inquirycontact" id="inquirycontact'+divid+'" class="inquirycontact" value="'+(parseInt(contactheading)+1)+'">\
          <label for="inquirycontact'+divid+'" div-id="'+divid+'" class="contactheading" heading-id="'+(parseInt(contactheading)+1)+'">Contact '+(parseInt(contactheading)+1)+'</label>\
          </div>\
        </div>\
        <div class="col-md-6 text-right">\
          <span class=" mr12" style="color:#800080">Note : Either Mobile or Email is Required</span> \
          <button type="button" class="btn btn-primary btn-raised btn-label btn-sm pull-right" id="contactdivbtn'+(parseInt(contactheading)+1)+'" onclick="addnewcontact();"><i class="fa fa-plus"></i> ADD</button>\
          <button type="button" class="btn btn-danger btn-raised btnremove btn-sm pull-right mr-7" id="contactdivbtn'+divid+'" onclick="removecontact('+divid+')"><i class="fa fa-remove"></i> REMOVE</button>\
        </div>\
      </div>\
      <div class="row ml3 mr4">\
      <div class="col-md-3">\
          <div class="form-group" id="firstname_div'+divid+'">\
            <div class="col-md-12 pl-n">\
              <label class="control-label" for="firstname'+divid+'">First Name </label>\
              <input type="text" id="firstname'+divid+'" name="firstname[]" class=" fromgroup form-control"  onkeypress="return onlyAlphabets(event)">\
            </div>\
          </div>\
      </div>\
      <div class="col-md-3">\
          <div class="form-group" id="lastname_div'+divid+'">\
            <div class="col-md-12 pl-n">\
              <label class="control-label" for="lastname'+divid+'">Last Name </label>\
              <input type="text" id="lastname'+divid+'" name="lastname[]" class="form-control fromgroup"  onkeypress="return onlyAlphabets(event)">\
            </div>\
          </div>\
      </div>\
      <div class="col-md-3">\
          <div class="form-group" id="mobile_div'+divid+'">\
            <div class="col-md-12 pl-n">\
              <label class="control-label" for="mobileno'+divid+'">Mobile No <span class="mandatoryfield" style="color:#800080">*</span></label>\
              <input id="mobileno'+divid+'" type="text" name="mobileno[]" class="form-control fromgroup mobileno number" maxlength="10" div-id="'+divid+'" onkeypress="return isNumber(event)">\
              <label class="col-form-label text-danger" id="mobilenoduplicatemessage'+divid+'" div-id="1"></label>\
            </div>\
          </div>\
      </div>\
      <div class="col-md-3">\
          <div class="form-group" id="email_div'+divid+'">\
            <div class="col-md-12 pl-n">\
              <label class="control-label" for="email'+divid+'">Email <span class="mandatoryfield" style="color:#800080"> *</span></label>\
              <input id="email'+divid+'" type="text" name="email[]" class="form-control email fromgroup"  div-id="'+divid+'">\
              <label class="col-form-label text-danger" id="emailduplicatemessage'+divid+'" div-id="'+divid+'"></label> \
            </div>\
          </div>\
      </div>\
      </div>\
      <div class="row ml3 mr4">\
      <div class="col-md-3">\
          <div class="form-group" id="designation_div'+divid+'">\
            <div class="col-md-12 pl-n">\
              <label class="control-label" for="designation'+divid+'">Designation </label>\
              <input type="text" id="designation'+divid+'" name="designation[]" class="form-control fromgroup" >\
            </div>\
          </div>\
      </div>\
      <div class="col-md-3">\
          <div class="form-group" id="department_div'+divid+'">\
            <div class="col-md-12 pl-n">\
              <label class="control-label" for="department'+divid+'">Department </label>\
              <input type="text" id="department'+divid+'" name="department[]" class="form-control fromgroup" >\
            </div>\
          </div>\
      </div>\
      <div class="col-md-3">\
          <div class="form-group" id="birthdate_div'+divid+'">\
            <div class="col-md-12 pl-n">\
              <label class="control-label" for="birthdate'+divid+'">Birth Date </label>\
              <input id="birthdate'+divid+'" type="text" name="birthdate[]" class="form-control fromgroup datepicker1"  readonly>\
            </div>\
          </div>\
      </div>\
      <div class="col-md-3">\
          <div class="form-group" id="annidate_div'+divid+'">\
            <div class="col-md-12 pl-n">\
              <label class="control-label" for="annidate'+divid+'">Anniversary Date </label>\
              <input id="annidate'+divid+'" type="text" name="annidate[]" class="form-control datepicker1"  readonly>\
            </div>\
          </div>\
      </div>\
      </div>\
    </div>';
  $("#contactdivs").append(contacthtml);
  $('#birthdate'+divid).datepicker({
    todayHighlight: true,
    format: 'dd/mm/yyyy',
    endDate: new Date(),
    autoclose: true,
    todayBtn:"linked",
    orientation:"bottom"
  });
  $('#annidate'+divid).datepicker({
      todayHighlight: true,
      format: 'dd/mm/yyyy',
      autoclose: true,
      todayBtn:"linked",
      orientation:"bottom"
  });
  $(".mobileno").change(function(){
    if(this.value!=""){  
      checkduplicate("mobileno",this.value,memberid,'',$(this).attr("div-id"));
    }else{
      $("#mobilenoduplicatemessage"+$(this).attr("div-id")).hide();
    }
  });

  $(".email").change(function(){
    if(this.value!=""){  
      checkduplicate("email",this.value,memberid,'',$(this).attr("div-id"));
    }else{
      $("#emailduplicatemessage"+$(this).attr("div-id")).hide();
    }
  });
  $('.number').on('keypress', function (evt) {
    evt = (evt) ? evt : window.event;
      var charCode = (evt.which) ? evt.which : evt.keyCode;
      if (charCode > 31 && (charCode < 48 || charCode > 57)) {
       return false;
     }
     return true;
  }).on('focusout', function (e) {
    var $this = $(this);
    $this.val($this.val().replace(/[^0-9]/g, ''));
  }).on('paste', function (e) {
    var $this = $(this);
    setTimeout(function () {
        $this.val($this.val().replace(/[^0-9]/g, ''));
    }, 5);
  });
}

function removecontact(divid){
  $("#contactdiv"+divid).remove();
  $('.contactheading').each(function (index, value) {
    $(this).html("Contact "+(index+1));
    $(this).attr("heading-id",(index+1));
    divid = $(this).attr("div-id");
    $('#inquirycontact'+divid).val(index+1);
  })
}

function totalnetamount(){
  totalnetamountval=0;
  $(".productsnetamount").each(function(value,index){
    if($(this).val()!=""){
      totalnetamountval += parseFloat($(this).val());
    }
  })
  totalproductamountval=0;
  $(".productsamount").each(function(value,index){
    if($(this).val()!=""){
      totalproductamountval += parseFloat($(this).val());
    }
  })
  totaltaxamountval = 0;
  totaltaxes = 0;
  $(".taxvalue").each(function(value,index){
    if($(this).val()!=""){
      totaltaxes++;
      totaltaxamountval += parseFloat($(this).val());
    }
  })

  $("#totaltaxamount").html((totaltaxamountval).toFixed(2));
  $("#totalgrossamount").html((totalproductamountval).toFixed(2));  
  $("#totalnetamount").html((totalnetamountval).toFixed(2));
}

function getcontacts(memberid) {
  var uurl = SITE_URL+"crm-inquiry/getcontactdata";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {memberid:memberid},
    dataType: 'json',
    async: false,
    success: function(response){
      $('#contacts')
      .find('option')
      .remove()
      .end()
      .append('<option value="0">Select Contact</option>')
      .val('0')
      ;

      for(var i = 0; i < response.length; i++) {

        $('#contacts').append($('<option>', { 
          value: response[i]['id'],
          text : "Name : "+response[i]['firstname']+" "+response[i]['lastname']+" | Email : "+response[i]['email']+" | Mobile No. : "+response[i]['countrycode']+response[i]['mobileno']
        }));
      }
      $("#countrycode").val((response.length==0?'+91':response[0]['countrycode']));
      $("#newcontactmemberid").val(memberid);
      // $('#product'+prow).val(areaid);
      $('#contacts').selectpicker('refresh');
    },
    error: function(xhr) {
          //alert(xhr.responseText);
        },
  });
  // alert(product_id);
}

function checknewcontactvalidation(){
   
  var mobileno = $("#newcontactmobileno").val();
  var email = $("#newcontactemail").val();
   
  var isvalidmobileno = isvalidemail = 1 ;
  if(email == '' && mobileno == ''){
    // mobilenoerror += "Contact "+(index+1)+" : Please enter mobile number";
    new PNotify({title: "Please enter mobile number !",styling: 'fontawesome',delay: '3000',type: 'error'});
    new PNotify({title: "Please enter email !",styling: 'fontawesome',delay: '3000',type: 'error'});
    $("#newcontactmobile_div,#newcontactemail_div").addClass("has-error is-focused");
    isvalidmobileno=0;
    isvalidemail=0;
  }
  invalidemailerror='';
  invalidmobilenoerror='';
  if(email != ''){
    if(validemail.test(email) == false){
        $("#newcontactemail_div").addClass("has-error is-focused");
        invalidemailerror += "Please enter valid email address !";
        isvalidemail = 0;
        $('html, body').animate({scrollTop:0},'slow');
    }else{
        $("#newcontactemail_div").removeClass("has-error is-focused");
    }
  }
  if(mobileno != ''){
    if(mobileno.length != 10){
      $("#newcontactmobile_div").addClass("has-error is-focused");
      invalidmobilenoerror += "Please enter minimum 10 digit mobile number !";
      isvalidmobileno = 0;
      $('html, body').animate({scrollTop:0},'slow');
    }else{
      $("#newcontactmobile_div").removeClass("has-error is-focused");
    }
  }
  
  if(invalidmobilenoerror!=""){
    new PNotify({title: invalidmobilenoerror,styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  if(invalidemailerror!=""){
    new PNotify({title: invalidemailerror,styling: 'fontawesome',delay: '3000',type: 'error'});
  }

   if(isvalidmobileno == 1 && isvalidemail == 1)
   {
     var formData = new FormData($('#newcontactform')[0]);
    //  if(ACTION==0){
 
       var uurl = SITE_URL+"crm-inquiry/add-new-contact";
       
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
           if(response=='-3'){
             new PNotify({title: "Mobile Number already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
             $("#mobile_div").addClass("has-error is-focused");
           }else if(response=='-4'){
             new PNotify({title: "Email already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
             $("#email_div").addClass("has-error is-focused");
           }else if(response=='0'){
            new PNotify({title: 'Contact not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else{
             new PNotify({title: 'Contact added successfully !',styling: 'fontawesome',delay: '3000',type: 'success'});
             getcontacts($("#newcontactmemberid").val());
              $("#addcontactmodal").modal("hide");
              $("#newcontactfirstname").val("");
              $("#newcontactlastname").val("");
              $("#newcontactmobileno").val("");
              $("#newcontactemail").val("");
              $("#newcontactdesignation").val("");
              $("#newcontactdepartment").val("");
              $("#newcontactbirthdate").val("");
              $("#newcontactannidate").val("");
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
     /* }else{
       var uurl = SITE_URL+"member/updatemember";
       
       // console.log($('#employee option'));
       // return false;
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
               new PNotify({title: "member successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
               setTimeout(function() { window.location=SITE_URL+"member"; }, 1500);
           }else if(response==2){
             new PNotify({title: "member name already exists.",styling: 'fontawesome',delay: '3000',type: 'error'});
             $("#name_div").addClass("has-error is-focused");
           }else if(response==3){
             new PNotify({title: "Mobile Number already exists.",styling: 'fontawesome',delay: '3000',type: 'error'});
             $("#mobile_div").addClass("has-error is-focused");
           }else if(response==4){
             new PNotify({title: "Email already exists.",styling: 'fontawesome',delay: '3000',type: 'error'});
             $("#email_div").addClass("has-error is-focused");
           }else{
               new PNotify({title: 'member not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
     } */
   }
}

function addnewquotationfile() {

    if ($('input[name="Filetext[]"]').length < 10) {
        quotationfilecount = ++quotationfilecount;
        var element = 'quotationfile'+quotationfilecount;
        $.html = '<div id="quotationfilecount'+quotationfilecount+'"><div class="row ml3 mr4"> \
                    <div class="col-md-4"> \
                        <div class="form-group" id="quotationfile'+quotationfilecount+'_div"> \
                          <div class="col-md-12 pl-n pr-sm">\
                              <div class="input-group"> \
                                  <span class="input-group-btn" style="padding: 0 0px 0px 0px;"> \
                                      <span class="btn btn-primary btn-raised btn-file">Browse... \
                                      <input type="file" class="quotationfile" name="quotationfile'+quotationfilecount+'" id="quotationfile'+quotationfilecount+'" accept=".jpe,.jpeg,.jpg,.pbm,.png,.pdf,.doc,.docx" onchange="validquotationfile($(this),&apos;'+element+'&apos;,this)"> \
                                  </span> \
                                  </span> \
                                  <input type="text" readonly="" id="Filetext'+quotationfilecount+'" name="Filetext[]" class="form-control" placeholder="File"> \
                              </div> \
                            </div> \
                        </div> \
                    </div> \
                    <div class="col-md-4"> \
                        <div class="form-group" id="quotationdescription_div"> \
                          <div class="col-md-12 pl-sm pr-sm">\
                              <input type="text" id="quotationdescription'+quotationfilecount+'" name="quotationdescription'+quotationfilecount+'" class="form-control" placeholder="Description"> \
                          </div>\
                        </div> \
                    </div> \
                    <div class="col-md-2"> \
                        <div class="form-group" id="quotationdate'+quotationfilecount+'_div"> \
                          <div class="col-md-12 pl-sm pr-sm">\
                              <input id="quotationdate'+quotationfilecount+'" type="text" name="quotationdate'+quotationfilecount+'" class="form-control quotationdate" placeholder="Quotation Date" readonly> \
                              </div> \
                        </div> \
                    </div> \
                    <div class="col-md-2"> \
                        <button type = "button" class = "btn btn-danger btn-raised file_remove_btn" id = "p'+quotationfilecount+'" onclick = "removequotationfile('+quotationfilecount+')" style = "padding: 5px 10px;margin-top: 18px;"> <i class = "fa fa-minus"> </i><div class="ripple-container"></div></button> \
                        <button type="button" class="btn btn-primary btn-raised file_add_btn" id="'+quotationfilecount+'" onclick="addnewquotationfile('+quotationfilecount+')" style="padding: 5px 10px;margin-top: 18px;"><i class="fa fa-plus"></i><div class="ripple-container"></div></button> \
                    </div> \
                </div> \
              </div>';


        $(".file_remove_btn:first").show();
        $(".file_add_btn:last").hide();

        $('#quotationfiledata_div').append($.html);
        
        $('.quotationdate').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            showButtonPanel: true,
            drops:'up',
            autoUpdateInput: true,
            autoApply: true,
            locale: {
                format: 'DD/MM/YYYY',
                cancelLabel: 'Clear'
            },
        });
    } else {
        PNotify.removeAll();
        new PNotify({
            title: 'Maximum 10 quotation files allowed !',
            styling: 'fontawesome',
            delay: '3000',
            type: 'error'
        });
    }
}

function removequotationfile(rowid) {
  if (ACTION == 1 && $('#quotationfileid' + rowid).val() != null) {
      var removequotationfileid = $('#removequotationfileid').val();
      $('#removequotationfileid').val(removequotationfileid + ',' + $('#quotationfileid' + rowid).val());
  }
  $('#quotationfilecount' + rowid).remove();
  
  $(".file_add_btn:last").show();
  if ($(".file_remove_btn:visible").length == 1) {
      $(".file_remove_btn:first").hide();
  }
}

function resetdata(){
  
  $("#name_div").removeClass("has-error is-focused");
  $("#companyname_div").removeClass("has-error is-focused");
  $("#firstname_div").removeClass("has-error is-focused");
  $("#lastname_div").removeClass("has-error is-focused");
  $("#mobile_div").removeClass("has-error is-focused");
  $("#email_div").removeClass("has-error is-focused");
  $("#designation_div").removeClass("has-error is-focused");
  $("#area_div").removeClass("has-error is-focused");
  $("#address_div").removeClass("has-error is-focused");
  $("#pincode_div").removeClass("has-error is-focused");
  $("#leadsource_div").removeClass("has-error is-focused");
  $("#zoneid_div").removeClass("has-error is-focused");
  $("#industrycategory_div").removeClass("has-error is-focused");
  $("#types_div").removeClass("has-error is-focused");
  $("#status_div").removeClass("has-error is-focused");
  $("#inquiryleadsource_div").removeClass("has-error is-focused");
  $("#remarks_div").removeClass("has-error is-focused");
  $("#country_div").removeClass("has-error is-focused");
  $("#province_div").removeClass("has-error is-focused");
  $("#city_div").removeClass("has-error is-focused");
  $("#employee_div").removeClass("has-error is-focused");
  $("#website_div").removeClass("has-error is-focused");
  $("#membercode_div").removeClass("has-error is-focused");
  $("#mobilenumber_div").removeClass("has-error is-focused");
  $("#memberemail_div").removeClass("has-error is-focused");
  $("#password_div").removeClass("has-error is-focused");
  
  $("#inquiryemployee_div").removeClass("has-error is-focused");
  $("#follow_up_type_div").removeClass("has-error is-focused");
  $(".productdiv").removeClass("has-error is-focused");
  $(".productcategorydiv").removeClass("has-error is-focused");
  $(".pricediv").removeClass("has-error is-focused");
  $(".qtydiv").removeClass("has-error is-focused");
  $(".ratediv").removeClass("has-error is-focused");
  $(".discountpercentdiv").removeClass("has-error is-focused");
  $(".discountdiv").removeClass("has-error is-focused");
  $(".amountdiv").removeClass("has-error is-focused");
  $(".netamountdiv").removeClass("has-error is-focused");

  if(ACTION==0){
    $("#name").val("");
    $("#companyname").val("");
    $("#firstname").val("");
    $("#lastname").val("");
    $("#mobileno").val("");
    $("#email").val("");
    $("#designation").val("");
    $("#areaid").val(0);
    $("#address").val("");
    $("#pincode").val("");
    $("#leadsource").val(0);
    $("#zoneid").val(0);
    $("#industrycategory").val(0);
    $("#types").val(0);
    $("#status").val(inquirydefaultstatus);
    $("#inquiryleadsource").val(0);
    $("#remarks").val("");
    $("#countryid").val("101");
    getprovince(101);
    $("#provinceid").val(0);
    $("#cityid").val(0);
    $("#employee").val(assignto);
    $("#website").val("");
    $("#notes").val("");
    $("#inquiryemployee").val(assignto);
    $("#follow_up_type").val(defaultfollowuptype);
    $("#membercode").val("");
    $("#mobilenumber").val("");
    $("#countrycodeid").val('+91');
    $("#memberemail").val("");
    $("#password").val("");
    
    $("select.productcategory").each(function(i){
      $(this).val("0");
      $(this).selectpicker('refresh');
    })
    $("select.product").each(function(i){
      $(this).val("0");
      $(this).selectpicker('refresh');
    })
    $("select.priceid").each(function(i){
      $(this).val("0");
      $(this).selectpicker('refresh');
    })
    $('.countproducts').not(':first').remove();
    $('.contactdiv').not('div:first').remove();
    $('#mobilenoduplicatemessage').html('');
    $('#emailduplicatemessage').html('');

    $("#installmentsetting_div").hide();
    $("#noofinstallment").val("");
    $("#emidate").val("");
    $("#emiduration").val("");
    $("#installmentmaindivheading").hide();
    $("#installmentdivs").html("");
    $('#rating').val("");

    $("#addnewfollowup").prop("checked", true);
    $("#followuplatitude").val("");
    $("#followuplongitude").val("");

    $(".add_btn_product:last").show();
    if ($(".remove_btn_product:visible").length == 1) {
        $(".remove_btn_product:first").hide();
    }
    
    Date.prototype.addDays = function(days) {
      var date = new Date(this.valueOf());
      date.setDate(date.getDate() + days);
      return date;
  }
  
    var date = new Date();
    date = date.addDays(parseInt(defaultfollowupdate));
    day = (date.getDate() < 10 ? '0' : '') + date.getDate();
    month = ((date.getMonth()+1) < 10 ? '0' : '') + (date.getMonth()+1);
    year = date.getFullYear();
    hours = (date.getHours() < 10 ? '0' : '') + date.getHours();
    minutes = (date.getMinutes() < 10 ? '0' : '') + date.getMinutes();
    displaydate = day+"/"+month+"/"+year+" "+hours+":"+minutes;
    
    $("#followupdate").val(displaydate);
    $( "#followupdate" ).data('daterangepicker').setStartDate(displaydate);
    $( "#followupdate" ).data('daterangepicker').setEndDate(displaydate);
    
    $('#contacts')
      .find('option')
      .remove()
      .end()
      .append('<option value="0">Select Contact</option>')
      .val('0');

    $('select.product')
          .find('option')
          .remove()
          .end()
          .append('<option value="0">Select Product</option>')
          .val('0');
    
    $("#existingmemberid").select2("val", "");

    $("#memberdetail").show();
    $("#existingmember_div").hide();
    $("#existingmembercontactdiv").hide();
    $('.selectpicker').selectpicker('refresh');
    $("#rate").find(".rate-select-layer").remove();
    totalnetamount();
  }

  $('html, body').animate({scrollTop:0},'slow');
  
}

function checkvalidation(submittype=0){

  checkdisable = 0;
  if($("#inquiryemployee").prop("disabled")==true){
    checkdisable = 1;
  }

  $("#inquiryemployee").prop("disabled",false);
  var contacts = $("#contacts").val();
  var inquiryleadsource = $("#inquiryleadsource").val();

  if(ACTION==1){
    var productcategory =$("#productcategory").val(); 
    var product =$("#product1").val(); 
    var qty =$("#qty").val(); 
    var rate =$("#product_rate").val(); 
    var amount =$("#amount").val(); 
    var inquiryemployee =$("#inquiryemployee").val(); 
    var status =$("#status").val(); 
    var memberstatus =$("#memberstatus").val(); 
  }else {
    var name = $("#name").val();
    var companyname = $("#companyname").val();
    var website = $("#website").val();
    var mobileno = $("#mobileno").val();
    var email = $("#email").val();
    var cityid = $("#cityid").val();
    var country =$("#countryid").val(); 
    var province =$("#provinceid").val(); 
    var pincode = $("#pincode").val();
    var leadsource = $("#leadsource").val();
    var employee =$("#employee").val();
    var membercode = $("#membercode").val();
    var mobilenumber = $("#mobilenumber").val().trim();
    var countrycodeid = $("#countrycodeid").val();    
    var password = $("#password").val();  
    var memberemail = $("#memberemail").val();

    var members = $("#existingmemberid").val();
    var productcategory =$("#productcategory").val(); 
    var product =$("#product1").val(); 
    var qty =$("#qty").val(); 
    var rate =$("#product_rate").val(); 
    var amount =$("#amount").val(); 
    var inquiryemployee =$("#inquiryemployee").val(); 
    var status =$("#status").val(); 
    var memberstatus =$("#memberstatus").val(); 

    var follow_up_type =$("#follow_up_type").val();
    var followupdate =$("#followupdate").val();
  }
  var reason = $("#reason").val();
  isvalidreason = 0;
  /*New*/
  isvalidinquiryemployee = isvalidinquiryleadsource = 0;
  isvalidfollow_up_type = isvalidfollowupdate = 1;
  
  if($("#new_existing_memberid").val()=="new") {
    var isvalidname = isvalidcompanyname = isvalidmobileno = isvalidemail = isvaliddesignation =  isvalidareaid = isvalidcityid =isvalidprovince = isvalidcountry = isvalidpincode = isvalidleadsource = isvalidproductcategory = isvalidproduct = isvalidqty = isvalidrate =  isvalidamount = isvaliddate = isvalidremarks = isvalidemployee = isvalidmembers = isvalidstatus = isvalidmemberstatus = isvalidcontacts = isvalidmembercode = isvalidmobilenumber = isvalidcountrycodeid = isvalidpassword = isvalidmemberemail = 1 ;
    isvalidwebsite = isvalidtypes = 1;
  }
  
  if($("#new_existing_memberid").val()=="existing" || ACTION==1){ 
    var isvalidstatus = isvalidcontacts = 0 ;
    var isvalidproductcategory = isvalidproduct = isvalidqty = isvalidrate =  isvalidamount = isvalidname = isvalidcompanyname = isvalidmobileno = isvalidemail = isvaliddesignation = isvalidprovince = isvalidcountry = isvalidcityid = isvalidareaid = isvalidpincode = isvalidleadsource = isvalidremarks = isvalidemployee = isvalidwebsite = isvalidtypes = isvalidmemberstatus = isvalidmembercode = isvalidmobilenumber = isvalidcountrycodeid = isvalidpassword = isvalidmemberemail = 1;
  }
  isvalidpercentage = 1; 
  totalpercentage = 0;
  
  $(".percentage").each(function(value,index){
    if($(this).val()!=""){
      totalpercentage = (parseFloat(totalpercentage)+parseFloat($(this).val())).toFixed(2);
    }
  })

  if($("#noofinstallment").val()!="" && parseInt($("#noofinstallment").val())>0){
    if(totalpercentage!=100){
      isvalidpercentage = 0;
      new PNotify({title: "Total installment percentage must be 100.",styling: 'fontawesome',delay: '3000',type: 'error'});
    }
  }

  if(reason == '' && $("#reason_div").is(":visible")){
    $("#reason_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter reason !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidreason = 0;
  }else {
    isvalidreason = 1;
  }

  /*New*/
  if($("#new_existing_memberid").val()=="new") {
    isvalidmembers = 1;
    PNotify.removeAll();
    
    if(memberstatus == '0'){
      $("#memberstatus_div").addClass("has-error is-focused");
      new PNotify({title: 'Please select '+member_label+' status !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidmemberstatus = 0;
    }else {
        isvalidmemberstatus= 1;
    }

    if(companyname == ''){
      $("#companyname_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter company name !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidcompanyname = 0;
    }else {
      if(companyname.length<3){
        $("#companyname_div").addClass("has-error is-focused");
        new PNotify({title: 'Company name require minimum 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidcompanyname = 0;
      }else{
        isvalidcompanyname = 1;
      }
    }

    if(name == ''){
      $("#name_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter '+member_label+' name !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidname = 0;
    }else {
      if(name.length<3){
        $("#name_div").addClass("has-error is-focused");
        new PNotify({title: Member_label+' name require minimum 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidname = 0;
      }else{
        isvalidname = 1;
      }
    }

    if(website.trim() != ""){
      if(!isUrl(website)){
        $("#website_div").addClass("has-error is-focused");
        new PNotify({title: "Please enter valid website",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidwebsite = 0;
      }else{
        isvalidwebsite = 1;  
      }
    }

    if(membercode==""){
      $("#membercode_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter '+member_label+' code !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
      if(membercode.length<6){
        $("#membercode_div").addClass("has-error is-focused");
        new PNotify({title: Member_label+' code required minimum 6 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      }else{
        $("#membercode_div").removeClass("has-error is-focused");
        isvalidmembercode = 1;
      }
    }
  
    if(password=="") {
        $("#password_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter password !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidpassword = 0;
    } else {
      if(CheckPassword(password)==false){
        $("#password_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter password between 6 to 20 characters which contain at least one alphabetic, numeric & special character !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidpassword = 0;
      }else { 
        $("#password_div").removeClass("has-error is-focused");
        isvalidpassword = 1;
      }
    }
  
    if(memberemail == ''){
      $("#memberemail_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter email !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidmemberemail = 0;
    }else{
      if(!ValidateEmail(memberemail)){
          $("#memberemail_div").addClass("has-error is-focused");
          new PNotify({title: 'Please enter valid Email !',styling: 'fontawesome',delay: '3000',type: 'error'});
          isvalidmemberemail = 0;
      }else{
          $("#memberemail_div").removeClass("has-error is-focused");
          isvalidmemberemail = 1;
      }
    }

    if(countrycodeid=="" || countrycodeid==0) {
      $("#countrycodeid_div").addClass("has-error is-focused");
      new PNotify({title: 'Please select country code !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidcountrycodeid = 0;
    } else {
      $("#countrycodeid_div").removeClass("has-error is-focused");
      isvalidcountrycodeid = 1;
    }

    if(mobilenumber=="") {
        $("#mobilenumber_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter mobile number !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidmobilenumber = 0;
    } else {
      if(mobilenumber.length!=10){
        $("#mobilenumber_div").addClass("has-error is-focused");
        new PNotify({title: 'Mobile number allow only 10 digits !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidmobilenumber = 0;
      }else{
        $("#mobilenumber_div").removeClass("has-error is-focused");
        isvalidmobilenumber = 1;
      }
    }

    isvalidemail = 1;
    emailerror = invalidemailerror = duplicateemailerror = "";
    emailarr = [];

    $('.email').each(function (index, value) {
      email = $(this).val();
      divid = $(this).attr("div-id");
      emailindex = $('.email').index(this);
      mobileno = $("#mobileno"+(parseInt(emailindex)+1)).val();
      if(email!=""){
        if(emailarr.includes($(this).val())){
          duplicateemailerror = "Enter different email !";
        }
        emailarr.push(email);
      }
      if( email == '' && mobileno==""){
        $("#email_div"+divid).addClass("has-error is-focused");
        emailerror += "Contact "+(index+1)+" : Please enter Email !<br>";
        isvalidemail = 0;
        $('html, body').animate({scrollTop:0},'slow');
      }else {
        $("#email_div"+divid).removeClass("has-error is-focused");
        if(email != ''){
          if(validemail.test(email) == false){
              $("#email_div"+divid).addClass("has-error is-focused");
              invalidemailerror += "Contact "+(index+1)+" : Please enter valid email address !<br>";
              isvalidemail = 0;
              $('html, body').animate({scrollTop:0},'slow');
          }else{
              $("#email_div"+divid).removeClass("has-error is-focused");
          }
        }
      }
    });

    mobilenoerror = invalidmobilenoerror = duplicatemobilerror = "";
    isvalidmobileno = 1;
    mobilenoarr = [];
    
    $('.mobileno').each(function (index, value) {
      mobileno = $(this).val();
      divid = $(this).attr("div-id");
      
      mobilenoindex = $('.mobileno').index(this);
      email = $("#email"+(parseInt(mobilenoindex)+1)).val();
      if(mobileno!=""){
        if(mobilenoarr.includes($(this).val())){
          duplicatemobilerror = "Enter different mobile number !";
        }
        mobilenoarr.push(mobileno);
      }
      if(mobileno == '' && email==""){
        $("#mobile_div"+divid).addClass("has-error is-focused");
        mobilenoerror += "Contact "+(index+1)+" : Please enter mobile number ! <br>";
        isvalidmobileno = 0;
        $('html, body').animate({scrollTop:0},'slow');
      }else {
        $("#mobile_div"+divid).removeClass("has-error is-focused");
      
        if(mobileno != ''){
          if(mobileno.length < 10){
            $("#mobile_div"+divid).addClass("has-error is-focused");
            invalidmobilenoerror += "Contact "+(index+1)+" : Please enter minimum 10 digit mobile number ! <br>";
            isvalidmobileno = 0;
            $('html, body').animate({scrollTop:0},'slow');
          }else{
            $("#mobile_div"+divid).removeClass("has-error is-focused");
          }
        }
      }
    })

    if(emailerror!="" && isvalidmobileno==1){
    
      $('.mobileno').each(function (index, value) {
        divid = $(this).attr("div-id");
        $("#mobile_div"+divid).removeClass("has-error is-focused");
      })
      $('.email').each(function (index, value) {
        divid = $(this).attr("div-id");
        $("#email_div"+divid).removeClass("has-error is-focused");
      })
    }
    
    if(mobilenoerror!="" && isvalidemail==1){
    
      $('.mobileno').each(function (index, value) {
        divid = $(this).attr("div-id");
        $("#mobile_div"+divid).removeClass("has-error is-focused");
      })
      $('.email').each(function (index, value) {
        divid = $(this).attr("div-id");
        $("#email_div"+divid).removeClass("has-error is-focused");
      })
    }

    if(isvalidmobileno==0 && isvalidemail==0){
      if(emailerror!=""){
        new PNotify({title: emailerror,styling: 'fontawesome',delay: '3000',type: 'error'});
      }
      if(mobilenoerror!=""){
        new PNotify({title: mobilenoerror,styling: 'fontawesome',delay: '3000',type: 'error'});
      }
    }else if(isvalidemail==0 && isvalidmobileno==1){
      if(emailerror!=""){
        new PNotify({title: emailerror,styling: 'fontawesome',delay: '3000',type: 'error'});
      }
    }else if(isvalidmobileno==0 && isvalidemail==1){
      if(mobilenoerror!=""){
        new PNotify({title: mobilenoerror,styling: 'fontawesome',delay: '3000',type: 'error'});
      }
    }

    if(invalidmobilenoerror!=""){
      new PNotify({title: invalidmobilenoerror,styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidmobileno=0;
    }

    if(invalidemailerror!=""){
      new PNotify({title: invalidemailerror,styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidemail=0;
    }
    
    if(duplicatemobilerror!=""){
      new PNotify({title: duplicatemobilerror,styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidmobileno=0;
    }

    if(duplicateemailerror!=""){
      new PNotify({title: duplicateemailerror,styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidemail=0;
    }

    isvaliddesignation = 1;

    isvalidareaid = 1;

    if(country == 0 || country==null){
      $("#country_div").addClass("has-error is-focused");
      new PNotify({title: "Please Select country !",styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidcountry = 0;
    }else {
        isvalidcountry = 1;
    }
    
    if(province == 0 || province=='' || province==null){
      $("#province_div").addClass("has-error is-focused");
      new PNotify({title: "Please Select state !",styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidprovince = 0;
    }else {
      isvalidprovince = 1;
    }

    if(cityid == 0 || cityid==null || cityid==""){
      $("#city_div").addClass("has-error is-focused");
      new PNotify({title: "Please Select city !",styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidcityid = 0;
    }else {
      isvalidcityid = 1;
    }

    if(pincode != '' && pincode.length!=6){
      $("#pincode_div").addClass("has-error is-focused");
      new PNotify({title: 'Pincoide allow only 6 digits !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidpincode = 0;
    }else{
      isvalidpincode = 1;
    }

    if(leadsource == 0 || leadsource==null){
      $("#leadsource_div").addClass("has-error is-focused");
      new PNotify({title: "Please select "+member_label+" lead source !",styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidleadsource = 0;
    }else {
      isvalidleadsource = 1;
    }

    if(inquirywithproduct==1){
      if(productcategory == '0'){
        $("#productcategory1_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select productcategory !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidproductcategory = 0;
      }else {
          isvalidproductcategory= 1;
      }

      if(product == '0'){
        $("#product1_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select product !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidproduct = 0;
      }else {
          isvalidproduct= 1;
      }

      if(qty == ''){

        $("#qty_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter quantity !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidqty = 0;
      }else {
          isvalidqty = 1;
      }

    if(rate == ''){
        $("#rate_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter rate !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidrate = 0;
      }else {
            isvalidrate = 1;
      }

      if(amount == ''){

        $("#amount_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter amount !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidamount = 0;
      }else {
          isvalidamount = 1;
      }
      
      if(employee.length==0){
        $("#employee_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select employee !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidemployee = 0;
      }else {
          isvalidemployee= 1;
      }
    }
    isvalidremarks = 1;
  }

  /*Existing*/
  if($("#new_existing_memberid").val()=="existing" || ACTION==1){ 

    if(members == ''){
      $("#existingmember_div").addClass("has-error is-focused");
      new PNotify({title: 'Please select '+member_label+' !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidmembers = 0;
    }else {
      isvalidmembers= 1;
    }

    if(contacts == '0' || contacts == ""){
      $("#contacts_div").addClass("has-error is-focused");
      new PNotify({title: 'Please select contact !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidcontacts = 0;
    }else {
        isvalidcontacts= 1;
    }

    if(inquirywithproduct==1){
      if(productcategory == '0'){
        $("#productcategory1_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select productcategory !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidproductcategory = 0;
      }else {
          isvalidproductcategory= 1;
      }

      if(product == '0'){
        $("#product1_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select product !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidproduct = 0;
      }else {
          isvalidproduct= 1;
      }

      if(qty == ''){

        $("#qty_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter quantity !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidqty = 0;
      }else {
          isvalidqty = 1;
      }

      if(rate == ''){
        $("#rate_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter rate !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidrate = 0;
      }else {
            isvalidrate = 1;
      }

      if(amount == ''){

        $("#amount_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter amount !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidamount = 0;
      }else {
          isvalidamount = 1;
      }
    }
  }

  if(ACTION==0){
    if($('#addnewfollowup').prop("checked") == true){
      if(follow_up_type == '0'){
        $("#follow_up_type_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select '+follow_up_label+' type !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidfollow_up_type = 0;
      }else{
        $("#follow_up_type_div").removeClass("has-error is-focused");
      }
      if(followupdate == ''){
        $("#followupdate_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select follow date !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidfollowupdate = 0;
      }else{
        $("#followupdate_div").removeClass("has-error is-focused");
      }
    }else{
      $("#follow_up_type_div").removeClass("has-error is-focused");
      $("#followupdate_div").removeClass("has-error is-focused");
    }
  }

  if(inquiryleadsource == 0 || inquiryleadsource==null){
    $("#inquiryleadsource_div").addClass("has-error is-focused");
    new PNotify({title: "Please select inquiry lead source !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidinquiryleadsource = 0;
  }else {
    isvalidinquiryleadsource = 1;
  }

  if(inquiryemployee == '0'){
    $("#inquiryemployee_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select '+inquiry_label+' assign to employee !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidinquiryemployee = 0;
  }else {
      isvalidinquiryemployee= 1;
  }

  /*Multi Product validate*/
  var validproducts=-1;
  var cnt=1;

  var c=1;
  var firstId = $('.countproducts:first').attr('id').match(/\d+/); 
  $('.countproducts').each(function(){
      var id = $(this).attr('id').match(/\d+/);
     
      if($("#productcategory"+id).val() > 0 || $("#product"+id).val() > 0 || $("#priceid"+id).val() > 0 || $("#productrate"+id).val() != "" || $("#qty"+id).val() == 0 || $("#amount"+id).val() != "" || parseInt(id)==parseInt(firstId)){
          
          if($("#productcategory"+id).val() == 0){
            $("#productcategory"+id+"_div").addClass("has-error is-focused");
            new PNotify({title: 'Please select '+(c)+' product category !',styling: 'fontawesome',delay: '3000',type: 'error'});
            validproducts = 0;
          }else {
            $("#productcategory"+id+"_div").removeClass("has-error is-focused");
          }
          if($("#product"+id).val() == 0){
            $("#product"+id+"_div").addClass("has-error is-focused");
            new PNotify({title: 'Please select '+(c)+' product !',styling: 'fontawesome',delay: '3000',type: 'error'});
            validproducts = 0;
          }else {
            $("#product"+id+"_div").removeClass("has-error is-focused");
          }
          if($("#priceid"+id).val() == ""){
            $("#price"+id+"_div").addClass("has-error is-focused");
            new PNotify({title: 'Please select '+(c)+' price !',styling: 'fontawesome',delay: '3000',type: 'error'});
            validproducts = 0;
          }else {
            $("#price"+id+"_div").removeClass("has-error is-focused");
          }
          if($("#qty"+id).val() == 0){
            $("#qty"+id+"_div").addClass("has-error is-focused");
            new PNotify({title: 'Please select '+(c)+' quantity !',styling: 'fontawesome',delay: '3000',type: 'error'});
            validproducts = 0;
          }else{
            $("#qty"+id+"_div").removeClass("has-error is-focused");
          }
          if($("#productrate"+id).val() == ""){
            $("#productrate"+id+"_div").addClass("has-error is-focused");
            new PNotify({title: 'Please enter '+(c)+' product rate !',styling: 'fontawesome',delay: '3000',type: 'error'});
            validproducts = 0;
          }else {
            $("#productrate"+id+"_div").removeClass("has-error is-focused");
          }
          if($("#amount"+id).val() == 0){
            $("#amount"+id+"_div").addClass("has-error is-focused");
            new PNotify({title: 'Please enter '+(c)+' amount !',styling: 'fontawesome',delay: '3000',type: 'error'});
            validproducts = 0;
          }else {
            $("#amount"+id+"_div").removeClass("has-error is-focused");
          }
      } else{
          $("#productcategory"+id+"_div").removeClass("has-error is-focused");
          $("#product"+id+"_div").removeClass("has-error is-focused");
          $("#price"+id+"_div").removeClass("has-error is-focused");
          $("#productrate"+id+"_div").removeClass("has-error is-focused");
          $("#qty"+id+"_div").removeClass("has-error is-focused");
          $("#amount"+id+"_div").removeClass("has-error is-focused");
      }
      c++;
  });

  var variant = $('select[name="priceid[]"]');
  var values = [];
  for(j=0;j<variant.length;j++) {
      var uniqueproducts = variant[j];
      var id = uniqueproducts.id.match(/\d+/);
      
      if(uniqueproducts.value!="" && ($("#priceid"+id[0]).val()!="" && $("#priceid"+id[0]+" option:selected").text()!="Select Variant")){
          if(values.indexOf(uniqueproducts.value)>-1) {
              $("#price"+id[0]+"_div").addClass("has-error is-focused");
              new PNotify({title: 'Please select '+(j+1)+' is different variant !',styling: 'fontawesome',delay: '3000',type: 'error'});
              validproducts = 0;
          }
          else{ 
              values.push(uniqueproducts.value);
              if(($("#priceid"+id[0]).val()!="" && $("#priceid"+id[0]+" option:selected").text()!="Select Variant")){
                  $("#price"+id[0]+"_div").removeClass("has-error is-focused");
              }
          }
      }
  }
  if(validproducts==-1) {
    validproducts = 1;
  }
  
  /*Multi Product validate wnd*/
  if(status == '0'){
    $("#status_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select status !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidstatus = 0;
  }else {
    isvalidstatus= 1;
  }
  
  if(isvalidname == 1 && isvalidcompanyname == 1 && isvalidmobileno == 1 && isvalidemail == 1 && isvaliddesignation == 1 && isvalidareaid == 1 && isvalidcityid==1 && isvalidpincode == 1 && isvalidleadsource == 1 && isvalidproductcategory == 1 && isvalidproduct == 1 && isvalidqty == 1 && isvalidrate == 1 && isvalidamount == 1 && isvalidremarks == 1 && isvalidemployee == 1 && validproducts == 1 && isvalidinquiryemployee==1 &&
    isvalidfollow_up_type==1 && isvalidfollowupdate==1 && isvalidwebsite == 1 && isvalidpercentage==1 && isvalidtypes==1 && isvalidcountry==1 && isvalidprovince==1 && isvalidstatus==1 && isvalidreason==1 && isvalidmemberstatus==1 && isvalidcontacts==1 && isvalidmembers==1 && isvalidinquiryleadsource==1 && isvalidmembercode == 1 && isvalidmobilenumber == 1 && isvalidcountrycodeid == 1 && isvalidpassword == 1 && isvalidmemberemail == 1) {
    var formData = new FormData($('#crminquiryform')[0]);
    if(ACTION==0){
      var uurl = SITE_URL+"crm-inquiry/crm-inquiry-add";
      
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
            new PNotify({title: Inquiry_label+" successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});

            if(submittype==1){
              $('#reset').click();
            }else if(submittype==2){
              $('.modal').modal('hide');
            }else{
              setTimeout(function() { window.location=SITE_URL+"crm-inquiry"; }, 1500);
            }
          }else if(response==2){
            new PNotify({title: Inquiry_label+" already exists.",styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#name_div").addClass("has-error is-focused");
          }else if(response==3){
            new PNotify({title: "Mobile Number already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#mobile_div").addClass("has-error is-focused");
          }else if(response==4){
            new PNotify({title: "Email already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#email_div").addClass("has-error is-focused");
          }else if(response==5){
            new PNotify({title: "Contact mobile Number already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#mobile_div").addClass("has-error is-focused");
          }else if(response==6){
            new PNotify({title: "Contact email already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#email_div").addClass("has-error is-focused");
          }else if(response==7){
            new PNotify({title: "Quotation file type does not valid !",styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#email_div").addClass("has-error is-focused");
          }else if(response==8){
            new PNotify({title: "Quotation file not upload !",styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#email_div").addClass("has-error is-focused");
          }else{
            new PNotify({title: Inquiry_label+' not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
      var uurl = SITE_URL+"crm-inquiry/update-crm-inquiry";
      
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
              new PNotify({title: Inquiry_label+" successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"crm-inquiry"; }, 1500);
          }else if(response==2){
            new PNotify({title: Inquiry_label+" already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#name_div").addClass("has-error is-focused");
          }else if(response==7){
            new PNotify({title: "Quotation file type does not valid !",styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#email_div").addClass("has-error is-focused");
          }else if(response==8){
            new PNotify({title: "Quotation file not upload !",styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#email_div").addClass("has-error is-focused");
          }else{
              new PNotify({title: Inquiry_label+' not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
  }else{
    if(checkdisable==1){
      $("#inquiryemployee").prop("disabled",true);
    }
  }
}
