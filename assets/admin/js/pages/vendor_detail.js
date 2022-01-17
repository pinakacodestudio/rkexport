var isvalidfiletext = 0;
$(document).ready(function() {
    $('.yesno input[type="checkbox"]').bootstrapToggle({
      on: 'On',
      off: 'Off',
      onstyle: 'primary',
      offstyle: 'danger',
      size: 'mini'
    });
    billingaddresstableTable = $('#billingaddresstable').dataTable
    ({
      "language": {
        "lengthMenu": "_MENU_"
      },
      "pageLength": 10,
      "columnDefs": [{
        'orderable': false,
        'targets': [0,-1,-2]
      }],
      "order": [], //Initial no order.
      'serverSide': true,//Feature control DataTables' server-side processing mode.
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": SITE_URL+"vendor/billingaddresslisting",
        "type": "POST",
        "data" :function ( data ) {
          data.vendorid = $('#vendorid').val();
        },
        beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
        },
        error: function(xhr) {
          //alert(xhr.responseText);
        },
        complete: function(){
          $('.mask').hide();
          $('#loader').hide();
        },
      },
    });

    //DOM Manipulation to move datatable elements integrate to panel
    $('#billingaddresstable_filter input').attr('placeholder','Search...');
    $('#billingaddress .panel-ctrls').append($('#billingaddresstable_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('#billingaddress .panel-ctrls.panel-tbl').append("<i class='separator'></i>");
    $('#billingaddress .panel-ctrls').append($('#billingaddresstable_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");
    $('#billingaddress .panel-footer').append($(".dataTable+.row"));
    $('.dataTables_paginate>ul.pagination').addClass("pull-right pagination-md");
    
    $('.discountdaterangepicker').datepicker({
        // todayHighlight: true,
        format: 'dd/mm/yyyy',
        autoclose: true,
        startDate: new Date(),
    });
    var todayDate = new Date().getDate();
    $('#pointsdate').datetimepicker({
        format: 'dd/mm/yyyy hh:ii:ss',
        todayBtn: "linked",
        autoclose: true,
        clearBtn: true,
        viewMode: 'days',
        endDate : new Date(),
    });
   
    $('#BillingAddressModal').on('hidden.bs.modal', function () {
      $("#billingaddressid").val("");
      $("#BillingAddressModal .modal-title").text("Add Address");
      $("#billingaddressbtn").val("ADD");
      $("#baname,#baemail,#baddress,#batown,#bapostalcode,#bamobileno").val("");
      $("#bayes").prop("checked",true);
      
      $('#countryid').val(DEFAULT_COUNTRY_ID);
      getprovince(DEFAULT_COUNTRY_ID);
      $('#countryid,#provinceid,#cityid').selectpicker('refresh');
      $("#baname_div,#baemail_div,#baddress_div,#batown_div,#bapostalcode_div,#bamobileno_div").removeClass("has-error is-focused");

    });
    $('input[name="discountonbill"]').click(function(){
      if ($(this).is(':checked')){
        if($(this).val() == 1){               
          $('#discountonbilldiv').show();
        }else{          
          $('#discountonbilldiv').hide();
        }
      }
    });

    $('#datepicker-range1').datepicker({
      // todayHighlight: true,
      format: 'dd/mm/yyyy',
      autoclose: true,
      startDate: new Date(),
    });
    
    orderTable = $('#ordertable').dataTable
    ({
      "language": {
        "lengthMenu": "_MENU_"
      },
      "pageLength": 10,
      "columnDefs": [{
        'orderable': false,
        'targets': [0]
      },{targets: [3],className: "text-center"}],
      "order": [], //Initial no order.
      'serverSide': true,//Feature control DataTables' server-side processing mode.
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": SITE_URL+"vendor/purchaseorderlisting",
        "type": "POST",
        "data" :function ( data ) {
          data.vendorid = $('#vendorid').val();
          data.startdate = $('#orderstartdate').val();
          data.enddate = $('#orderenddate').val();
          data.status = $('#orderstatus').val();
          data.displaytype = 0;
        },
        beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
        },
        error: function(xhr) {
          //alert(xhr.responseText);
        },
        complete: function(){
          $('.mask').hide();
          $('#loader').hide();
        },
      },
    });
   
    //DOM Manipulation to move datatable elements integrate to panel
    $('#ordertable_filter input').attr('placeholder','Search...');
    $('#purchaseorder .panel-ctrls').append($('#ordertable_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('#purchaseorder .panel-ctrls.panel-tbl').append("<i class='separator'></i>");
    $('#purchaseorder .panel-ctrls').append($('#ordertable_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");
    $('#purchaseorder .panel-footer').append($(".dataTable+.row"));
    
    $('.memberdaterangepicker').datepicker({
      todayHighlight: true,
      format: 'dd/mm/yyyy',
      autoclose: true,
      todayBtn:"linked"
    });

    producttable = $('#producttable').DataTable
    ({
      "language": {
        "lengthMenu": "_MENU_"
      },
      "pageLength": 10,
      "columnDefs": [{
        'orderable': false,
        'targets': [0,-1]
      }],
      "order": [], //Initial no order.
      'serverSide': true,//Feature control DataTables' server-side processing mode.
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": SITE_URL+"vendor/productlisting",
        "type": "POST",
        "data" :function ( data ) {
              data.vendorid = $("#vendorid").val();
          },
          beforeSend: function(e){
            $('.mask').show();
            $('#loader').show();
          },
          error: function(xhr) {
            //alert(xhr.responseText);
          },
          complete: function(){
            $('.mask').hide();
            $('#loader').hide();
            loadpopover();
          },
      },
    });
     
    //DOM Manipulation to move datatable elements integrate to panel
    $('#producttable_filter input').attr('placeholder','Search...');
    $('#memberproduct .panel-ctrls').append($('#producttable_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('#memberproduct .panel-ctrls.panel-tbl').append("<i class='separator'></i>");
    $('#memberproduct .panel-ctrls').append($('#producttable_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");
    $('#memberproduct .panel-footer').append($(".dataTable+.row"));  

    $('#identityprooftable').DataTable({
      "language": {
          "lengthMenu": "_MENU_"
      },
      "columnDefs": [ {
        "targets": [0,-1,-2],
        "orderable": false
      } ],
      "order": [],
      responsive: true,
    });

    $('#identityprooftable_filter input').attr('placeholder','Search...');
    $('#identityproof .panel-ctrls').append($('#identityprooftable_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('#identityproof .panel-ctrls.panel-tbl').append("<i class='separator'></i>");
    $('#identityproof .panel-ctrls').append($('#identityprooftable_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");
    $('#identityproof .panel-footer').append($(".dataTable+.row"));

    quotationTable = $('#quotationtable').DataTable({
      "language": {
        "lengthMenu": "_MENU_"
      },
      "pageLength": 10,
      "columnDefs": [{
        'orderable': false,
        'targets': [0]
      },{targets: [3],className: "text-center"}],
      "order": [], //Initial no order.
      'serverSide': true,//Feature control DataTables' server-side processing mode.
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": SITE_URL+"vendor/quotationlisting",
        "type": "POST",
        "data" :function ( data ) {
          data.vendorid = $('#vendorid').val();
          data.startdate = $('#quotationstartdate').val();
          data.enddate = $('#quotationenddate').val();
          data.status = $('#quotationstatus').val();
          data.displaytype = 0;
        },
        beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
        },
        error: function(xhr) {
          //alert(xhr.responseText);
        },
        complete: function(){
          $('.mask').hide();
          $('#loader').hide();
        },
      },
    });
    $('#quotationtable_filter input').attr('placeholder','Search...');
    $('#quotationtablediv .panel-ctrls').append($('#quotationtable_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('#quotationtablediv .panel-ctrls.panel-tbl').append("<i class='separator'></i>");
    $('#quotationtablediv .panel-ctrls').append($('#quotationtable_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");
    $('#quotationtablediv .panel-footer').append($(".dataTable+.row"));

    $('#paymentdetailtable').DataTable({
        "language": {
            "lengthMenu": "_MENU_"
        },
        "columnDefs": [ {
          "targets": [0],
          "orderable": false
        } ],
        responsive: true,
    });
    
    $('#codeexpireddate').datepicker({
      language:  'fr',
      weekStart: 1,
      autoclose: 1,
      startDate: new Date(),
      startView: 3,
      forceParse: 0,
      format: "dd/mm/yyyy",
      clearBtn : true,
      todayHighlight: 1
    });
  
     $('#codecleardatebtn').click(function(){
        $("#codestartdate").val("");
        $("#codeenddate").val("");
     })
    //  $('#vouchercodeform').hide();
  
    $('input[name="isuniversal"]').click(function(){
      $("#codevouchercode_div").removeClass("has-error is-focused");
      $("#codenoofcustomerused_div").removeClass("has-error is-focused");
      $("#codequantity_div").removeClass("has-error is-focused");
      if ($(this).is(':checked')){
  
        if($(this).val() == 1){
          $('#codevouchercode_div').show();
          $('#codenoofcustomerused_div').show();
          $('#codequantity_div').hide();
        }else{                          
          $('#codevouchercode_div').hide();
          $('#codenoofcustomerused_div').hide();
          $('#codequantity_div').show();
        }
      }
    });
    $('input[name="codediscounttype"]').click(function(){
      $("#codeamount_div").removeClass("has-error is-focused");
      $("#codepercentageval_div").removeClass("has-error is-focused");
      if ($(this).is(':checked')){
  
        if($(this).val() == 1){               
          $('#codeamount_div').hide();
          $('#codepercentageval_div').show();
        }else{          
          $('#codeamount_div').show();
          $('#codepercentageval_div').hide();
        }
      }
    });
  
    if(ACTION==1){
      if($('input[name="codediscounttype"]:checked').val() == 1){
        $('#codeamount_div').hide();
        $('#codepercentageval_div').show();
      }else{          
        $('#codeamount_div').show();
        $('#codepercentageval_div').hide();
      }
    }
    $("#codepercentageval").keyup(function(e){
      
      if($(this).val()>100){
        $(this).val('100.00');  
      }
    });

    $("input[name=amount]").keyup(function(e){
      var minamount = $('#discountonbillminamount').val().trim();
      var amount = $(this).val().trim();
      if(parseFloat(minamount)!=''){
        if(parseFloat(amount) > parseFloat(minamount)){
          $(this).val(parseFloat(minamount));  
        }
      }else{
        $(this).val('');
      }
    });
    $("input[name=discountonbillminamount]").keyup(function(e){
      var amount = $('#amount').val().trim();
      var minamount = $(this).val().trim();
      
      if(parseFloat(minamount)!=''){
        if(parseFloat(amount) > parseFloat(minamount)){
          $('#amount').val(parseFloat(minamount));  
        }
      }else{
        $('#amount').val('');
      }
    });

    $('input[name="discountonbilltype"]').click(function(){
      $("#amount_div").removeClass("has-error is-focused");
      $("#percentageval_div").removeClass("has-error is-focused");
      if ($(this).is(':checked')){
  
        if($(this).val() == 1){               
          $('#amount_div').hide();
          $('#percentageval_div').show();
        }else{          
          $('#amount_div').show();
          $('#percentageval_div').hide();
        }
      }
    });
   if($('input[name="discountonbilltype"]:checked').val() == 1){
      $('#amount_div').hide();
      $('#percentageval_div').show();
    }else{          
      $('#amount_div').show();
      $('#percentageval_div').hide();
    }
    $('#cleardatebtn').click(function(){
        $("#startdate").val("");
        $("#enddate").val("");
     })
    $("#percentageval").keyup(function(e){
      if($(this).val()>100){
        $(this).val('100.00');  
      }
    });

    $(function () {
      $('.panel-heading.filter-panel').click(function() {
          $(this).find('.button-icon span').html($(this).find('.button-icon span').text() == 'keyboard_arrow_down' ? 'keyboard_arrow_up' : 'keyboard_arrow_down');
          //$(this).children().toggleClass(" ");
          $(this).next().slideToggle({duration: 200});
          $(this).toggleClass('panel-collapsed');
          return false;
      });
    });

    /****COUNTRY CHANGE EVENT****/
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
      $('#provinceid').selectpicker('refresh');
      $('#cityid').selectpicker('refresh');
      getprovince(this.value);
    });
    /****PROVINCE CHANGE EVENT****/
    $('#provinceid').on('change', function (e) {
      
      $('#cityid')
        .find('option')
        .remove()
        .end()
        .append('<option value="">Select City</option>')
        .val('0')
      ;
      $('#cityid').selectpicker('refresh');
      getcity(this.value);
    });

});

function applyFilterOrder(){
  orderTable.fnDraw();
//   salesorderTable.fnDraw();
}

function applyFilterQuotation(){
  quotationTable.ajax.reload();
//   salesquotationTable.fnDraw();
}
function loadprovinceorcity(){
  getprovince($('#countryid').val());
  getcity($('#provinceid').val());
}
function getproductdetail(productid,memberid) {
  if(productid!=""){
      var uurl = SITE_URL+"member/geteditproductdetail";
      memberid = $("#memberid").val();
      $.ajax({
        url: uurl,
        type: 'POST',
        data: { productid : productid, memberid : memberid },
        dataType:"JSON",
        async: false,
        beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
        },
        success: function(response){
          // console.log(response);
          if(response['productdata'].isuniversal!=undefined){
            variantshtml="";
            if(response['productdata'].isuniversal=="0"){
              pricescnt=0;
              $.each(response['productcombination'], function( key, value ) {
                  if(value.length>0){    
                    variantshtml+="<div class='load_variantsdiv'>";
                    variantshtml+='<div class="row"><div class="col-md-5">';
                    variantshtml+='<div class="form-group" for="price" id="price_div'+pricescnt+'">Price<input type="text" id="price'+pricescnt+'" onkeypress="return decimal(event,this.value)" class="form-control prices" disabled placeholder="Price" name="price['+response['productcombination'][key][0].priceid+']" value="'+response['productcombination'][key][0].price+'"></div>';
                    variantshtml+='</div><div class="col-md-5 col-md-offset-1">';
                    variantshtml+='<div class="form-group" for="price" id="memberprice_div'+pricescnt+'">'+Member_label+' Price<input type="text" id="memberprice'+pricescnt+'" onkeypress="return decimal(event,this.value)" class="form-control prices" placeholder="'+Member_label+' Price" name="memberprice['+response['productcombination'][key][0].membervariantid+']" value="'+response['productcombination'][key][0].memberprice+'"></div>';
                    variantshtml+='</div></div>';
                    
                    variantshtml+="<table class='table'>";
                    $.each(value, function( key1, value1 ) {
                      variantshtml+= "<tr><td>"+value1.attributename+"</td>";
                      variantshtml+= "<td>"+value1.variantname+"</td></tr>";
                    })
                    variantshtml+="</table></div>";
                  }
                  pricescnt++;
              })
            }else{
              variantshtml+='<div class="row"><div class="col-md-5">';
              variantshtml+='<div class="form-group" for="universalprice" id="universalprice_div">Price<input type="text" id="universalprice" onkeypress="return decimal(event,this.value)" class="form-control prices" disabled placeholder="Price" name="universalprice" value="'+response['productdata'].price+'"></div>';
              variantshtml+='</div><div class="col-md-5 col-md-offset-1">';
              variantshtml+='<div class="form-group" for="memberuniversalprice" id="memberuniversalprice_div">'+Member_label+' Price<input type="text" id="memberuniversalprice" onkeypress="return decimal(event,this.value)" class="form-control prices" placeholder="'+Member_label+' Price" name="memberuniversalprice" value="'+response['productdata'].memberprice+'"></div>';
              variantshtml+='</div></div>';
            }
            $("#load_variants").html(variantshtml);
          }else{
            $("#load_variants").html("Product not found");
          }
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
        complete: function(){
          $('.mask').hide();
          $('#loader').hide();
        },
        /*cache: false,
        contentType: false,
        processData: false*/
      });
  }else{
    $("#load_variants").html("");
  }
}

function checkvalidation() {

    var debitlimit = $("#debitlimit").val();
    
    var isvaliddebitlimit = 0;
    if(debitlimit ==''){
        $("#debitlimit_div").addClass("has-error is-focused");
        new PNotify({title: "Please enter debit limit !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvaliddebitlimit = 0;
    }else{
        isvaliddebitlimit = 1;
    }
    
    if(isvaliddebitlimit == 1){

    var formData = new FormData($('#debitlimitform')[0]);
    var uurl = SITE_URL+"vendor/edit-debit-limit";
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
            new PNotify({title: "Debit limit successfully updated !",styling: 'fontawesome',delay: '3000',type: 'success'});
            location.reload();
          }else{
            new PNotify({title: "Debit limit not updated !",styling: 'fontawesome',delay: '3000',type: 'error'});
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
function validfile(obj){
  var val = obj.val();
  var id = obj.attr('id').match(/\d+/);
  var filename = obj.val().replace(/C:\\fakepath\\/i, '');

  switch(val.substring(val.lastIndexOf('.') + 1).toLowerCase()){
    case 'pdf' : case 'gif': case 'bmp': case 'jpg': case 'jpeg': case 'png':
      
      $("#textfile").val(filename);
      isvalidfiletext = 1;
      $("#identityproof_div").removeClass("has-error is-focused");
      break;
    default:
      $("#identityproof").val("");
      $("#textfile").val("");
      isvalidfiletext = 0;
      $("#identityproof_div").addClass("has-error is-focused");
      new PNotify({title: 'Please upload valid file !',styling: 'fontawesome',delay: '3000',type: 'error'});
      break;
  }
}
function identityproofcheckvalidation() {
  var idproof = $('#textfile').val().trim();
  var documenttitle = $('#titledocument').val().trim();

  var isvalididproof = isvaliddocumenttitle = 0;
  PNotify.removeAll();
  if(documenttitle==''){
    $("#titledocument_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter document title !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }else{
    if(documenttitle.length<3){
      $("#titledocument_div").addClass("has-error is-focused");
      new PNotify({title: 'Document title required minimum 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }else{
      isvaliddocumenttitle = 1;
    }
  }

  if(idproof==''){
    $("#identityproof_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select document !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalididproof = 0;
  }else{
    if(ACTION==1){
        isvalididproof = 1;
    }else{
      if(isvalidfiletext==0){
        $("#identityproof_div").addClass("has-error is-focused");
        new PNotify({title: 'Please upload valid file !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalididproof = 0;
      }else{
        isvalididproof = 1;
      }
    }
  }
  
  if(isvalididproof == 1 && isvaliddocumenttitle == 1){

    var formData = new FormData($('#idproofform')[0]);
    if(ACTION == 0){
      var uurl = SITE_URL+"vendor/add-identity-proof";
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
            new PNotify({title: "Vendor document successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
            setTimeout(function() { location.reload(); }, 1500);
          }else if(response==2){
            new PNotify({title: "File Type is not valid !",styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(response==3){
            new PNotify({title: "Vendor document not upload !",styling: 'fontawesome',delay: '3000',type: 'error'});
          }else{
            new PNotify({title: "Vendor document not added !",styling: 'fontawesome',delay: '3000',type: 'error'});
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
    var uurl = SITE_URL+"vendor/update-identity-proof";
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
            new PNotify({title: "Vendor document successfully updated.",styling: 'fontawesome',delay: '3000',type: 'success'});
            setTimeout(function() { location.reload(); }, 1500);
          }else if(response==2){
            new PNotify({title: "File Type is not valid !",styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(response==3){
            new PNotify({title: "Vendor document not upload !",styling: 'fontawesome',delay: '3000',type: 'error'});
          }else{
            new PNotify({title: "Vendor document not updated !",styling: 'fontawesome',delay: '3000',type: 'error'});
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
function checkdiscountonbillvalidation() {

  var percentageval = $("#percentageval").val().trim();
  var amount = $("#amount").val().trim();
  var discounttype = $("input[name='discountonbilltype']:checked").val();
  var discountonbill = $("input[name='discountonbill']:checked").val();
  var discountonbillminamount = $("input[name='discountonbillminamount']").val().trim();

  var isvalidpercentageval = isvalidamount = isvaliddiscountonbillminamount = 1;

  if(discountonbill==1){  
    if(discounttype==1){
      if(percentageval == 0){
        $("#percentageval_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter discount percentage !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidpercentageval = 0;
      }
    }else{
      if(amount == 0){
        $("#amount_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter discount amount !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidamount = 0;
      }
    }
    if(discountonbillminamount == "" || discountonbillminamount == 0){
      $("#discountonbillminvalue_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter minimum bill amount !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvaliddiscountonbillminamount = 0;
    }
  }
  if(isvalidpercentageval == 1 && isvalidamount == 1 && isvaliddiscountonbillminamount == 1){
    var uurl = SITE_URL+"vendor/savediscountonbill";
    var formData = new FormData($('#discountform')[0]);
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
            new PNotify({title: 'Discount changes successfully updated!',styling: 'fontawesome',delay: '3000',type: 'success'});
            
            // setTimeout(function() { location.reload(); }, 1500);
        }
        else{
          new PNotify({title: 'Discount changes not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
function getbillingaddressdetail(billingaddressid){
  var uurl = SITE_URL+"member/getBillingAddressDataById";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: { billingaddressid : billingaddressid},
    dataType:"JSON",
    async: false,
    beforeSend: function(){
      $('.mask').show();
      $('#loader').show();
    },
    success: function(response){
      $("#baname").val(response.name);
      $("#baemail").val(response.email);
      $("#baddress").val(response.address);
      $("#batown").val(response.town);
      $("#bapostalcode").val(response.postalcode);
      $("#bamobileno").val(response.mobileno);

      if(response.status==1){
        $("#bayes").prop("checked",true);
      }else{
        $("#bano").prop("checked",true);
      }
      if(response.countryid!=null){

        $("#countryid").val(response.countryid);
        getprovince(response.countryid);
        $("#provinceid").val(response.provinceid);
        getcity(response.provinceid);
        $("#cityid").val(response.cityid);
        $("#countryid,#provinceid,#cityid").selectpicker("refresh");
      }else{
        $("#countryid").val(DEFAULT_COUNTRY_ID);
        getprovince(DEFAULT_COUNTRY_ID);
      }
      $("#billingaddressid").val(response.id);
      $("#BillingAddressModal .modal-title").html("Edit Address");
      $("#billingaddressbtn").val("UPDATE");
    
      $('#BillingAddressModal').modal('show');
    },
    error: function(xhr) {
    //alert(xhr.responseText);
    },
    complete: function(){
      $('.mask').hide();
      $('#loader').hide();
    },
    /*cache: false,
    contentType: false,
    processData: false*/
  });
}
function openIDProofmodal(){

  //if($("#btntext input[name=submit]").val()=="ADD"){
    
    $("#btntext input[name=submit]").val('ADD');
    $("#memberidproofid").val('');
    $("#oldIDproof").val('');

    $('#identityproof').val('');
    $('#textfile').val('');
    $("#titledocument").val('');
    $('#documenttitle').html('Add Vendor Document');
  
    $('#identityproof_div').removeClass('has-error is-focused');
    $('#titledocument_div').removeClass('has-error is-focused');  
  //}
  $('#identityproof_div').removeClass('has-error is-focused');
  $('#titledocument_div').removeClass('has-error is-focused'); 
  $("#titledocument").focus();
  ACTION=0;
}
function getIdentityProofDataById(id){

  $("#filetext").val("");
  $('#identityproof_div').removeClass('has-error is-focused');
  $('#titledocument_div').removeClass('has-error is-focused');

  ACTION=1;
  var uurl = SITE_URL+"vendor/getIdentityProofDataById";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: { id : id},
    dataType:"JSON",
    async: false,
    beforeSend: function(){
      $('.mask').show();
      $('#loader').show();
    },
    success: function(response){
      $("#btntext input[name=submit]").val('UPDATE');
      $('#documenttitle').html('Edit Vendor Document');
      $("#titledocument").val(response['title']);
      if(response['idproof'] != undefined){
        var idproof = response['idproof'].trim();

        $("#memberidproofid").val(id);
        $("#oldIDproof").val(idproof);
        
        $('#identityproof').val(idproof);
        $('#textfile').val(idproof);
        
      }
    },
    error: function(xhr) {
    //alert(xhr.responseText);
    },
    complete: function(){
      $('.mask').hide();
      $('#loader').hide();
        
    },
    /*cache: false,
    contentType: false,
    processData: false*/
  });
}

function chageapprovestatusonmemberidproof(status, id){
  var uurl = SITE_URL+"vendor/update-vendor-identity-proof-status";
  if(id!=''){
    swal({    title: "Are you sure to change status?",
      type: "warning",   
      showCancelButton: true,   
      confirmButtonColor: "#DD6B55",   
      confirmButtonText: "Yes, change it!",   
      closeOnConfirm: false }, 
      function(isConfirm){   
        if (isConfirm) {   
          $.ajax({
              url: uurl,
              type: 'POST',
              data: {status:status,id:id},
              
              success: function(response){
                if(response==1){
                    location.reload();
                 }
               },
              error: function(xhr) {
              //alert(xhr.responseText);
              }
          });  
        }
      });

  }           
}
  
function billingaddressresetdata(){

  $("#baname_div").removeClass("has-error is-focused");
  $("#baemail_div").removeClass("has-error is-focused");
  $("#baddress_div").removeClass("has-error is-focused");
  $("#batown_div").removeClass("has-error is-focused");
  $("#bapostalcode_div").removeClass("has-error is-focused");
  $('#bamobileno_div').removeClass("has-error is-focused");
  $('#country_div').removeClass("has-error is-focused");
  $('#province_div').removeClass("has-error is-focused");
  $('#city_div').removeClass("has-error is-focused");
 
  if(ACTION==0){
    $('#baname').val('');
    $('#baemail').val('');
    $('#baddress').val(1);
    $('#batown').val('');
    $('#bapostalcode').val('');
    $('#bamobileno').val('');
    $('#countryid').val(DEFAULT_COUNTRY_ID);
    $('#provinceid').val("0");
    $('#cityid').val("0");
    getprovince(DEFAULT_COUNTRY_ID);

  }

  $('#bayes').prop("checked", true);
  $('#baname').focus();
  
  $('.selectpicker').selectpicker('refresh');
  $('html, body').animate({scrollTop:0},'slow');
}

function billingaddresscheckvalidation(){
  
  var name = $("#baname").val().trim();
  var email = $("#baemail").val().trim();
  var billingaddress = $("#baddress").val().trim();
  var postalcode = $("#bapostalcode").val().trim();
  var mobileno = $("#bamobileno").val().trim();
 
  var isvalidname = isvalidemail = isvalidbillingaddress = isvalidpostalcode = isvalidmobileno = 0;
  
  PNotify.removeAll();
  if(name == ''){
    $("#baname_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter name !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidname = 0;
  }else { 
    if(name.length<2){
      $("#baname_div").addClass("has-error is-focused");
      new PNotify({title: 'Name require minmum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidname = 0;
    }else{
      isvalidname = 1;
    }
  }
  if(email == ''){
    $("#baemail_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter email !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidemail = 0;
  }else { 
    if(!ValidateEmail(email)){
      $("#baemail_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter valid email !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidemail = 0;
    }else { 
      isvalidemail = 1;
    }
  }

  if(billingaddress == ''){
    $("#baddress_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter billing address !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidbillingaddress = 0;
  }else { 
    if(billingaddress.length<3){
      $("#baddress_div").addClass("has-error is-focused");
      new PNotify({title: 'Billing address required minimum 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidbillingaddress = 0;
    }else { 
      isvalidbillingaddress = 1;
    }
  }
  if(postalcode == ''){
    $("#bapostalcode_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter postal code !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidpostalcode = 0;
  }else { 
    if(isNaN(postalcode)){
      $("#bapostalcode_div").addClass("has-error is-focused");
      new PNotify({title: 'Postal code allow only numbers !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidpostalcode = 0;
    }else { 
      isvalidpostalcode = 1;
    }
  }
  if(mobileno == ''){
    $("#bamobileno_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter mobile no. !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidmobileno = 0;
  }else { 
    if(isNaN(mobileno)){
      $("#bamobileno_div").addClass("has-error is-focused");
      new PNotify({title: 'Mobile no. allow only numbers !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidmobileno = 0;
    }else if(mobileno.length<10){
      $("#bamobileno_div").addClass("has-error is-focused");
      new PNotify({title: 'Mobile no. required minimum 10 digits !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidmobileno = 0;
    }else { 
      isvalidmobileno = 1;
    }
  }
  
  if(isvalidname == 1 && isvalidemail == 1 && isvalidbillingaddress == 1 && isvalidmobileno == 1 && isvalidpostalcode == 1){

    var formData = new FormData($('#billingaddressform')[0]);
    billingaddressid = $("#billingaddressid").val();

    if(billingaddressid==""){
        var uurl = SITE_URL+"vendor/add-billing-address";
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
              new PNotify({title: "Billing Address successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
              
              billingaddresstableTable.fnDraw();
              $('#billingaddressform').trigger("reset");
              $('#BillingAddressModal').modal('hide');
              billingaddressresetdata();
              
            }else if(response==2){
              new PNotify({title: 'Billing Address already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else{
              new PNotify({title: 'Billing Address not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
      var uurl = SITE_URL+"vendor/update-billing-address";
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
            new PNotify({title: "Billing Address successfully updated !",styling: 'fontawesome',delay: '3000',type: 'success'});
            
            billingaddresstableTable.fnDraw();
            $('#billingaddressform').trigger("reset");
            $('#BillingAddressModal').modal('hide');
            billingaddressresetdata();
            
          }else if(response==2){
              new PNotify({title: 'Billing Address already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else{
            new PNotify({title: 'Billing Address not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
function exportproduct(memberid){
  memberid = memberid || 0;
  var totalRecords =$("#producttable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    window.location= SITE_URL+"product/exportproduct?memberid="+memberid;
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
}

function importproduct(){
  PNotify.removeAll();

  $("#attachment_div").removeClass("has-error is-focused");
  $("#Filetext").val("");
  $('.selectpicker').selectpicker('refresh');  
  $('#myDetailModal').modal('show');
}
function checkimportproductvalidation(){

  var filetext = $("#Filetext").val();

  var isvalidfiletext = 0;
  
  PNotify.removeAll();
  
  //CHECK FILE
  if(filetext==''){
    $("#attachment_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select excel file !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }else{
      $("#attachment_div").removeClass("has-error is-focused");
      isvalidfiletext = 1;
  }
  if(isvalidfiletext==1){
    $("#productpriceimportform").append($("<input>").attr("type", "hidden").attr("name", "memberid").val($("#memberid").val()));
    
    var formData = new FormData($('#productpriceimportform')[0]);

    var uurl = SITE_URL+"product/importproductprice";
    
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
            new PNotify({title: "Product price successfully imported.",styling: 'fontawesome',delay: '3000',type: 'success'});
             setTimeout(function() { window.location.reload(); }, 1500);
        }else if(response=='2'){
          new PNotify({title: "Uploaded file is not an excel file !",styling: 'fontawesome',delay: '3000',type: 'error'});
        }else if(response=='3'){
          new PNotify({title: "Excel file not uploaded !",styling: 'fontawesome',delay: '3000',type: 'error'});
        }else if(response=='4'){
          new PNotify({title: "Some field name are not match !",styling: 'fontawesome',delay: '3000',type: 'error'});
        }else if(response=='5'){
          new PNotify({title: "Please enter at least one product price detail !",styling: 'fontawesome',delay: '3000',type: 'error'});
        }else{
          new PNotify({title: response,styling: 'fontawesome',delay: '3000',type: 'error'});
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