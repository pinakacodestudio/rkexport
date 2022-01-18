
$(document).ready(function() {
    getpricelistdata();
    
    $("input[name=producttype]").click(function(){
      getpricelistdata();
    });
    $("#categoryid").change(function(){
      getpricelistdata();
    });
});
/* 
$(document).on('keyup', '.price', function(e) {
  var elementid = e.target.id;
  
  elementid = elementid.split('_');
  var productid = elementid[1];
  var productpriceid = elementid[2];
  var channelid = elementid[3];
  var element = productid+"_"+productpriceid+"_"+channelid;

  calculatediscount(element);
});
$(document).on('keyup', '.discper', function(e) {
  var elementid = e.target.id;
  
  elementid = elementid.split('_');
  var productid = elementid[1];
  var productpriceid = elementid[2];
  var channelid = elementid[3];
  var element = productid+"_"+productpriceid+"_"+channelid;

  if(parseFloat(this.value)>=100){
    $("#discper_"+element).val("100");
  }
  calculatediscount(element);
});
$(document).on('keyup', '.discamnt', function(e) {
  var elementid = e.target.id;
  
  elementid = elementid.split('_');
  var productid = elementid[1];
  var productpriceid = elementid[2];
  var channelid = elementid[3];
  var element = productid+"_"+productpriceid+"_"+channelid;
  calculatediscountmount(element,$(this).val());
});

function calculatediscount(elementid){
  var discountpercentage = $("#discper_"+elementid).val(); 
  discountpercentage = (discountpercentage!='' && discountpercentage!=0)?discountpercentage:0;
  var price = $("#price_"+elementid).val();
  price = (price!='' && price!=0)?price:0;
  
  if(price!=0 && discountpercentage!=0){
      var discountamount = (parseFloat(price)*parseFloat(discountpercentage)/100);
      
      $("#discamnt_"+elementid).val(parseFloat(discountamount).toFixed(2));
  }else{
      $("#discamnt_"+elementid).val('');
  }
}
function calculatediscountmount(elementid,discountamount){

  var discountpercentage = 0;
  var price = $("#price_"+elementid).val();
  price = (price!=0)?price:0;

  if(discountamount!=undefined && discountamount!=''){
    
    if(parseFloat(discountamount)>parseFloat(price)){
        discountamount = parseFloat(price);
        $("#discamnt_"+elementid).val(parseFloat(discountamount).toFixed(2));
    }
    
    if(parseFloat(price)!=0){
        var discountpercentage = ((parseFloat(discountamount)*100) / parseFloat(price));
    }
    
    $("#discper_"+elementid).val(parseFloat(discountpercentage).toFixed(2)); 
  }else{
      $("#discamnt_"+elementid).val('');
      $("#discper_"+elementid).val(""); 
  }
} */
function getpricelistdata(){

  var uurl = SITE_URL+"price-list/getpricelistdata";
  var formData = new FormData($('#pricelistform')[0]);
  $.ajax({
    url: uurl,
    type: 'POST',
    data: formData,
    //dataType: "json",
    //async: false,
    beforeSend: function(){
      $('.mask').show();
      $('#loader').show();
    },
    success: function(response){
      
      var dataObject = $.parseJSON(response);

      if ( $.fn.DataTable.isDataTable('#producttable') ) {
        $('#producttable').DataTable().destroy();
      }
      $('#producttable').empty();
           
      // if(!$.isEmptyObject(dataObject.DATA) && dataObject.DATA!=undefined){
   
      table =   $('#producttable').DataTable({
        "data": dataObject.DATA,
        "columns": dataObject.COLUMNS,
        "language": {
          "lengthMenu": "_MENU_"
        },
        drawCallback: function () {
          loadpopover();
        },
        "destroy": true,
        "pageLength": 10,
        "columnDefs": [{
          'orderable': false,
          'targets': []
        }],
        "order": [], //Initial no order.
        
      });
      $('.dataTables_filter input').attr('placeholder','Search...');

      $('.panel-ctrls').html('');
      $('.panel-footer').html('');
      $('.dataTables_filter input').attr('placeholder','Search...');

      //DOM Manipulation to move datatable elements integrate to panel
      $('.panel-ctrls').append($('.dataTables_filter').addClass("pull-right form-group")).find("label").addClass("panel-ctrls-center");
      $('.panel-ctrls').append("<i class='separator'></i>");
      $('.panel-ctrls').append($('.dataTables_length').addClass("pull-left form-group")).find("label").addClass("panel-ctrls-center");

      $('.panel-footer').append($(".dataTable+.row"));
      $('.dataTables_paginate>ul.pagination').addClass("pull-right pagination-md");

      // loadpopover();
        
      /* if(!$.isEmptyObject(dataObject.DATA) && dataObject.DATA!=undefined){
        if(dataObject.DATA.length == 0){
            $('#editbtnproduct').hide();
        }else{
            $('#editbtnproduct').show();
        }
      } */
      //}
        
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
function displaydescription(id){
    var description = $('#description'+id).html();
    $('.modal-body').html(description.replace(/&nbsp;/g, ' '));
}

function updateproductbasicprice(){
  var price = $("input[name='price[]']").map(function(){return $(this).val();}).get();
  var pricearray = $("input[name='price[]']").map(function(){return $(this).attr('id');}).get();
  var productpriceid = $("input[name='productpriceid[]']").map(function(){return $(this).val();}).get();
  var productid = $("input[name='productid[]']").map(function(){return $(this).val();}).get();
  var productallow = $("input[name^='allowcheck']").map(function(){ if($(this).prop("checked")==true){ return 1; }else{ return 0; }}).get();

  var isvalidprice = 1;
  PNotify.removeAll();

  if(isvalidprice==1){
      var formData = new FormData($('#pricelistform')[0]);
      var uurl = SITE_URL+"price-list/updateproductbasicprice";
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
                new PNotify({title: "Product sales price successfully updated.",styling: 'fontawesome',delay: '3000',type: 'success'});
            }else{
                new PNotify({title: "Product sales price not updated !",styling: 'fontawesome',delay: '3000',type: 'error'});
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

function importproduct(){
  PNotify.removeAll();

  $("#attachment_div").removeClass("has-error is-focused");

  $("#Filetext").val("");
          
  $('.selectpicker').selectpicker('refresh');
  
  $('#myDetailModal').modal('show');
}

function importchannelproduct(){
  PNotify.removeAll();

  $("#importattachment_div").removeClass("has-error is-focused");

  $("#importFiletext").val("");
          
  $('.selectpicker').selectpicker('refresh');
  
  $('#importpriceModal').modal('show');
}

function exportproduct(){
  var producttype = $("input[name=producttype]:checked").val();
  var categoryid = ($("#categoryid").val()!=null?$("#categoryid").val().join(','):'');
  
  var totalRecords =$("#producttable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    window.location= SITE_URL+"price-list/exportproduct?producttype="+producttype+"&categoryid="+categoryid;
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
}

function downloadExcelFile(){

  var producttype = $("input[name=producttype]:checked").val();
  window.location= SITE_URL+"price-list/download-excel-file?producttype="+producttype;
}

/* function checkvalidation(){

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
    
    var formData = new FormData($('#productpriceimportform')[0]);

    var uurl = SITE_URL+"price-list/importproductprice";
    
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
} */

function checkvalidationimport(){

  var filetext = $("#importFiletext").val();
  var producttype = $("input[name=producttype]:checked").val();

  var isvalidfiletext = 0;
  
  PNotify.removeAll();

  //CHECK FILE
  if(filetext==''){
    $("#importattachment_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select excel file !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }else{
      $("#importattachment_div").removeClass("has-error is-focused");
      isvalidfiletext = 1;
  }
  if(isvalidfiletext==1){
    
    var formData = new FormData($('#channelproductpriceimportform')[0]);
    formData.append("producttype",producttype);
    var uurl = SITE_URL+"price-list/importchannelproductprice";
    
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