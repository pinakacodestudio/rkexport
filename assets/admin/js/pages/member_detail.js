var isvalidfiletext = 0;
$(document).ready(function() {
    contactdetailtable = $('#contactdetailtable').DataTable ({
      "language": {
        "lengthMenu": "_MENU_"
      },
      "pageLength": 10,
      "columnDefs": [{
        'orderable': false,
        'targets': []
      },{width: 150, targets: 0}],
      "order": [], //Initial no order.
      responsive: true,
    });

    //DOM Manipulation to move datatable elements integrate to panel
    $('#contactdetailtable_filter input').attr('placeholder','Search...');
    $('#contactdetail .panel-ctrls.panel-tbl').append($('#contactdetailtable_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('#contactdetail .panel-ctrls.panel-tbl').append("<i class='separator'></i>");
    $('#contactdetail .panel-ctrls.panel-tbl').append($('#contactdetailtable_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");
    $('#contactdetail .panel-footer').append($(".dataTable+.row"));
    $('.dataTables_paginate>ul.pagination').addClass("pull-right pagination-md");
  
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
        "url": SITE_URL+"member/memberbillingaddresslisting",
        "type": "POST",
        "data" :function ( data ) {
          data.memberid = $('#memberid').val();
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
    $('#billingaddress .panel-ctrls.panel-tbl').append($('#billingaddresstable_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('#billingaddress .panel-ctrls.panel-tbl').append("<i class='separator'></i>");
    $('#billingaddress .panel-ctrls.panel-tbl').append($('#billingaddresstable_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");
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
   
    $('#VoucherModal').on('hidden.bs.modal', function () {
      $("#memberchannelid").val("0");
      $("#memberchannelid").selectpicker("refresh");
      $("#voucherid").val("");
      $("#VoucherModal .modal-title").text("Add Voucher Code");
      $("#vouchercodebtn").val("ADD");
      $("#codename,#codemaximumusage,#codevouchercode,#codepercentageval,#codeamount,#codeminbillamount,#codestartdate,#codeenddate").val("");
      $("#yes").prop("checked",true);
      $("#codepercentage").prop("checked",true);
      $("#codeamount_div").hide();
      $("#codepercentageval_div").show();
      $("#codename_div,#codevouchercode_div,#codemaximumusage_div,#datepicker-range1,#codepercentageval_div,#codeamount_div,#codeminbillamount_div").removeClass("has-error is-focused");
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
      "pageLength": 50,
      "columnDefs": [{
        'orderable': false,
        'targets': [0]
      }],
      "order": [], //Initial no order.
      'serverSide': true,//Feature control DataTables' server-side processing mode.
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": SITE_URL+"member/memberorderlisting",
        "type": "POST",
        "data" :function ( data ) {
          data.memberid = $('#memberid').val();
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
    $('#purchaseorder .panel-ctrls.panel-tbl').append($('#ordertable_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('#purchaseorder .panel-ctrls.panel-tbl').append("<i class='separator'></i>");
    $('#purchaseorder .panel-ctrls.panel-tbl').append($('#ordertable_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");
    $('#purchaseorder .panel-footer').append($(".dataTable+.row"));
    
    $('.memberdaterangepicker').datepicker({
      todayHighlight: true,
      format: 'dd/mm/yyyy',
      autoclose: true,
      todayBtn:"linked"
    });

    salesorderTable = $('#salesordertable').dataTable
    ({
      "language": {
        "lengthMenu": "_MENU_"
      },
      "pageLength": 50,
      "columnDefs": [{
        'orderable': false,
        'targets': [0]
      }],
      "order": [], //Initial no order.
      'serverSide': true,//Feature control DataTables' server-side processing mode.
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": SITE_URL+"member/membersalesorderlisting",
        "type": "POST",
        "data" :function ( data ) {
          data.memberid = $('#memberid').val();
          data.startdate = $('#orderstartdate').val();
          data.enddate = $('#orderenddate').val();
          data.status = $('#orderstatus').val();
          data.displaytype = 1;
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
    $('#salesordertable_filter input').attr('placeholder','Search...');
    $('#salesorder .panel-ctrls.panel-tbl').append($('#salesordertable_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('#salesorder .panel-ctrls.panel-tbl').append("<i class='separator'></i>");
    $('#salesorder .panel-ctrls.panel-tbl').append($('#salesordertable_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");
    $('#salesorder .panel-footer').append($(".dataTable+.row"));

    producttable = $('#producttable').DataTable
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
        "url": SITE_URL+"member/memberproductlisting",
        "type": "POST",
        "data" :function ( data ) {
            data.memberid = $("#memberid").val();
            data.brandid = $("#brandid").val();
            data.categoryid = $("#categoryid").val();
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
    $('#memberproduct .panel-ctrls.panel-tbl').append($('#producttable_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('#memberproduct .panel-ctrls.panel-tbl').append("<i class='separator'></i>");
    $('#memberproduct .panel-ctrls.panel-tbl').append($('#producttable_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");
    $('#memberproduct .panel-footer').append($(".dataTable+.row"));  

    cartTable = $('#carttable').DataTable({
      "language": {
          "lengthMenu": "_MENU_"
      },
      "columnDefs": [{
        "targets": [0,-2],
        "orderable": false
      }],
      "order": [], //Initial no order.
      'serverSide': true,//Feature control DataTables' server-side processing mode.
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": SITE_URL+"member/cartlisting",
        "type": "POST",
        "data" :function ( data ) {
          data.memberid = $('#memberid').val();
          data.startdate = $('#cartstartdate').val();
          data.enddate = $('#cartenddate').val();
          data.status = $('#cartstatus').val();
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
          loadpopover();
        },
      },
      responsive: true,
    });

    $('#carttable_filter input').attr('placeholder','Search...');
    $('#cartlisting .panel-ctrls').append($('#carttable_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('#cartlisting .panel-ctrls.panel-tbl').append("<i class='separator'></i>");
    $('#cartlisting .panel-ctrls').append($('#carttable_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");
    $('#cartlisting .panel-footer').append($(".dataTable+.row"));

    vouchercodetable = $('#discountcoupontable').DataTable({
        "processing": true,
        "language": {
            "lengthMenu": "_MENU_"
        },
        "columnDefs": [{
          "targets": [0,-1,-2],
          "orderable": false
        }],
        "order": [], //Initial no order.
        'serverSide': true,//Feature control DataTables' server-side processing mode.
        // Load data for the table's content from an Ajax source
        "ajax": {
          "url": SITE_URL+"member/membervouchercodelisting",
          "type": "POST",
          "data" :function ( data ) {
                data.memberid = $("#memberid").val();
                data.channelid = $("#channelid").val();
            },
        },
        responsive: true,
    });

    $('#discountcoupontable_filter input').attr('placeholder','Search...');
    $('#discountcoupontab .panel-ctrls').append($('#discountcoupontable_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('#discountcoupontab .panel-ctrls.panel-tbl').append("<i class='separator'></i>");
    $('#discountcoupontab .panel-ctrls').append($('#discountcoupontable_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");
    $('#discountcoupontab .panel-footer').append($(".dataTable+.row"));

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
      "pageLength": 50,
      "columnDefs": [{
        'orderable': false,
        'targets': [0]
      }],
      "order": [], //Initial no order.
      'serverSide': true,//Feature control DataTables' server-side processing mode.
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": SITE_URL+"member/memberquotationlisting",
        "type": "POST",
        "data" :function ( data ) {
          data.memberid = $('#memberid').val();
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

    salesquotationTable = $('#salesquotationtable').dataTable
    ({
      "language": {
        "lengthMenu": "_MENU_"
      },
      "pageLength": 50,
      "columnDefs": [{
        'orderable': false,
        'targets': [0]
      }],
      "order": [], //Initial no order.
      'serverSide': true,//Feature control DataTables' server-side processing mode.
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": SITE_URL+"member/membersalesquotationlisting",
        "type": "POST",
        "data" :function ( data ) {  
          data.memberid = $('#memberid').val();
          data.startdate = $('#quotationstartdate').val();
          data.enddate = $('#quotationenddate').val();
          data.status = $('#quotationstatus').val();
          data.displaytype = 1;
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
    $('#salesquotationtable_filter input').attr('placeholder','Search...');
    $('#salesquotationtablediv .panel-ctrls').append($('#salesquotationtable_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('#salesquotationtablediv .panel-ctrls.panel-tbl').append("<i class='separator'></i>");
    $('#salesquotationtablediv .panel-ctrls').append($('#salesquotationtable_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");
    $('#salesquotationtablediv .panel-footer').append($(".dataTable+.row"));

    pointhistorytable = $('#pointhistorytable').DataTable({
      "language": {
            "lengthMenu": "_MENU_"
        },
        "pageLength": 50,
        "columnDefs": [{
          "targets": [0,-2],
          "orderable": false
        }],
        "order": [], //Initial no order.
        'serverSide': true,//Feature control DataTables' server-side processing mode.
        // Load data for the table's content from an Ajax source
        "ajax": {
          "url": SITE_URL+"member/pointhistorylisting",
          "type": "POST",
          "data" :function ( data ) {
            data.memberid = $('#memberid').val();
            data.startdate = $('#pointhistorystartdate').val();
            data.enddate = $('#pointhistoryenddate').val();
            data.type = $('#type').val();
            data.transactiontype = $('#transactiontype').val();
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
        responsive: true,
    });
    $('#pointhistorytable_filter input').attr('placeholder','Search...');
    $('#pointlisting .panel-ctrls').append($('#pointhistorytable_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('#pointlisting .panel-ctrls.panel-tbl').append("<i class='separator'></i>");
    $('#pointlisting .panel-ctrls').append($('#pointhistorytable_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");
    $('#pointlisting .panel-footer').append($(".dataTable+.row"));

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

    
    //$('.dataTables_filter input').attr('placeholder','Search...');


    //DOM Manipulation to move datatable elements integrate to panel
    /* $('.panel-ctrls.panel-tbl').append($('.dataTables_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('.panel-ctrls.panel-tbl').append("<i class='separator'></i>");
    $('.panel-ctrls.panel-tbl').append($('.dataTables_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

    $('.panel-footer').append($(".dataTable+.row"));
    $('.dataTables_paginate>ul.pagination').addClass("pull-right pagination-md"); */
});

function applyFilterOrder(){
  orderTable.fnDraw();
  salesorderTable.fnDraw();
}
function applyFilterProduct(){
  producttable.ajax.reload();
}
function applyFilterQuotation(){
  quotationTable.ajax.reload();
  salesquotationTable.fnDraw();
}

function applyFilterCart(){
  cartTable.ajax.reload();
}
function applyFilterPointsHistory(){
  pointhistorytable.ajax.reload();
}
/* function applyFilterVoucherCode(){
  vouchercodetable.ajax.reload();
} */
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
  //question = encodeURIComponent(question);

  var isvaliddebitlimit = 0;
 
  /*if(productid == 0 || productid == ''){
    $("#productid_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter product !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidproductid = 0;
  }else { 
    isvalidproductid = 1;
  }*/
 
  if(debitlimit ==''){
    $("#debitlimit_div").addClass("has-error is-focused");
    new PNotify({title: "Please enter debit limit !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvaliddebitlimit = 0;
  }else{
    isvaliddebitlimit = 1;
  }
  
  if(isvaliddebitlimit == 1){

    var formData = new FormData($('#debitlimitform')[0]);
    // if(ACTION == 0){    
      var uurl = SITE_URL+"member/edit-debit-limit";
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
    /*}else{
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
*/    
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
      var uurl = SITE_URL+"member/add-identity-proof";
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
            new PNotify({title: Member_label+" document successfully added !",styling: 'fontawesome',delay: '3000',type: 'success'});
            setTimeout(function() { location.reload(); }, 1500);
          }else if(response==2){
            new PNotify({title: "File Type is not valid !",styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(response==3){
            new PNotify({title: Member_label+" document not upload !",styling: 'fontawesome',delay: '3000',type: 'error'});
          }else{
            new PNotify({title: Member_label+" document not added !",styling: 'fontawesome',delay: '3000',type: 'error'});
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
    var uurl = SITE_URL+"member/update-identity-proof";
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
            new PNotify({title: Member_label+" document successfully updated !",styling: 'fontawesome',delay: '3000',type: 'success'});
            setTimeout(function() { location.reload(); }, 1500);
          }else if(response==2){
            new PNotify({title: "File Type is not valid !",styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(response==3){
            new PNotify({title: Member_label+" document not upload !",styling: 'fontawesome',delay: '3000',type: 'error'});
          }else{
            new PNotify({title: Member_label+" document not updated !",styling: 'fontawesome',delay: '3000',type: 'error'});
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
    var uurl = SITE_URL+"member/savediscountonbill";
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

function vouchercoderesetdata(){

  $("#memberchannelid_div").removeClass("has-error is-focused");
  $("#codename_div").removeClass("has-error is-focused");
  $("#codevouchercode_div").removeClass("has-error is-focused");
  $("#codemaximumusage_div").removeClass("has-error is-focused");
  $("#codepercentageval_div").removeClass("has-error is-focused");
  $("#codeamount_div").removeClass("has-error is-focused");
  // $("#codenoofcustomerused_div").removeClass("has-error is-focused");

  $('#codevouchercode_div').show();
  // $('#codenoofcustomerused_div').show();
  $('#codeamount_div').hide();
  $('#codepercentageval_div').show();

  $('#codename').val('');
  $('#codevouchercode').val('');
  $('#codemaximumusage').val(1);
  $('#codepercentageval').val('');
  // $('#codenoofcustomerused').val(1);
  $('#codeexpireddate').val('');
  // $("#codeproductid").val('');
  $('#codepercentage').prop("checked", true);
  $('#codename').focus();
  $('#memberchannelid').selectpicker('refresh');
  // $('#productid').selectpicker('refresh');
  $('html, body').animate({scrollTop:0},'slow');
}
function vouchercodecheckvalidation(){
  
  var channelid = $("#memberchannelid").val();
  var name = $("#codename").val().trim();
  var maximumusage = $("#codemaximumusage").val().trim();
  var vouchercode = $("#codevouchercode").val().trim();
  // var noofcustomerused = $("#noofcustomerused").val().trim();
  var percentageval = $("#codepercentageval").val().trim();
  var amount = $("#codeamount").val().trim();

  var discounttype = $("input[name='codediscounttype']:checked").val();

  var isvalidname = isvalidmaximumusage = isvalidchannelid = 0;
  var isvalidvouchercode = /* isvalidnoofcustomerused = */ isvalidpercentageval = isvalidamount = 1;
  
  PNotify.removeAll();
  if(channelid == 0){
    $("#memberchannelid_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select channel !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidchannelid = 0;
  }else { 
    isvalidchannelid = 1;
  }
  if(name == ''){
    $("#codename_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter name !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidname = 0;
  }else { 
    if(name.length<2){
      $("#codename_div").addClass("has-error is-focused");
      new PNotify({title: Member_label+' name require minmum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidname = 0;
    }else{
      isvalidname = 1;
    }
  }
  if(maximumusage == 0){
    $("#codemaximumusage_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter maximum usage by customer !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidmaximumusage = 0;
  }else { 
    isvalidmaximumusage = 1;
  }

  if(vouchercode == ''){
      $("#codevouchercode_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter voucher code !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidvouchercode = 0;
    }else { 
      if(vouchercode.length<3){
        $("#codevouchercode_div").addClass("has-error is-focused");
        new PNotify({title: 'Voucher code require minmum 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidvouchercode = 0;
      }
    }

  /*   if(noofcustomerused == 0){
      $("#noofcustomerused_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter no of customer used !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidnoofcustomerused = 0;
    } */

  if(discounttype==1){
    if(percentageval == 0){
      $("#codepercentageval_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter discount percentage !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidpercentageval = 0;
    }
  }else{
    if(amount == 0){
      $("#codeamount_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter discount amount !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidamount = 0;
    }
  }
  
  if(isvalidchannelid == 1 && isvalidname == 1 && isvalidmaximumusage == 1 && isvalidvouchercode == 1 && /* isvalidnoofcustomerused == 1  && */ isvalidpercentageval == 1 && isvalidamount == 1){

    var formData = new FormData($('#vouchercodeform')[0]);
    voucherid = $("#voucherid").val();
    if(voucherid==""){
        var uurl = SITE_URL+"member/add-voucher-code";
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
              new PNotify({title: "Coupon code successfully added !",styling: 'fontawesome',delay: '3000',type: 'success'});
              vouchercodetable.ajax.reload(null,false);
              // $('#vouchercodeform').trigger("reset");
              $("#codename,#codemaximumusage,#codevouchercode,#codepercentageval,#codeamount,#codeminbillamount").val("");
              
              $('#VoucherModal').modal('hide');
              // $('#vouchercodeform')[0].reset();
              // setTimeout(function() { window.location=SITE_URL+"vouchercode"; }, 1500);
            }else if(response==2){
              new PNotify({title: 'Coupon code already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else{
              new PNotify({title: 'Coupon code not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
      var uurl = SITE_URL+"member/update-voucher-code";
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
            new PNotify({title: "Coupon code successfully updated !",styling: 'fontawesome',delay: '3000',type: 'success'});
            vouchercodetable.ajax.reload(null,false);
            $('#vouchercodeform').trigger("reset");
            $('#VoucherModal').modal('hide');
            $("#codename,#codemaximumusage,#codevouchercode,#codepercentageval,#codeamount,#codeminbillamount").val("");
            // $('#vouchercodeform')[0].reset();
            // setTimeout(function() { window.location=SITE_URL+"vouchercode"; }, 1500);
          }else if(response==2){
              new PNotify({title: 'Coupon code already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else{
            new PNotify({title: 'Coupon code not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

function getvouchercodedetail(voucherid){
    var uurl = SITE_URL+"member/getvouchercodedetail";
    $.ajax({
      url: uurl,
      type: 'POST',
      data: { voucherid : voucherid},
      dataType:"JSON",
      async: false,
      beforeSend: function(){
        $('.mask').show();
        $('#loader').show();
      },
      success: function(response){
        $("#memberchannelid").val(response.channelid);
        $("#codename").val(response.name);
        $("#codemaximumusage").val(response.maximumusage);
        $("#codevouchercode").val(response.vouchercode);
        // var noofcustomerused = $("#noofcustomerused").val().trim();
        if(response.discounttype==1){
          $("#codepercentageval").val(response.discountvalue);
          $("#codepercentageval_div").show();
          $("#codeamount_div").hide();
        }else{
          $("#codeamount").val(response.discountvalue);
          $("#codeamount_div").show();
          $("#codepercentageval_div").hide();
        }

        if(response.status==1){
          $("#yes").prop("checked",true);
        }else{
          $("#no").prop("checked",true);
        }

        if(response.startdate != "00/00/0000"){
          $("#codestartdate").val(response.startdate);
        }
        if(response.enddate != "00/00/0000"){
          $("#codeenddate").val(response.enddate);
        }
        if(response.discounttype==1){
          $("#codepercentage").prop("checked",true);
        }else{
          $("#codeamounttype").prop("checked",true);
        }
        
        $("#codeminbillamount").val(response.minbillamount);
        $("#voucherid").val(response.id);
        $("#VoucherModal .modal-title").html("Edit Voucher Code");
        $("#vouchercodebtn").val("UPDATE");
        $('#memberchannelid').selectpicker('refresh');
        $('#VoucherModal').modal('show');
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
    $('#documenttitle').html('Add '+Member_label+' Document');
  
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
  var uurl = SITE_URL+"member/getIdentityProofDataById";
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
      $('#documenttitle').html('Edit '+Member_label+' Document');
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
  var uurl = SITE_URL+"member/update-member-identity-proof-status";
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
  /* function chageorderstatus(status, orderId, ordernumber, membername=''){
  var uurl = SITE_URL+"member/update-member-order-status";
      if(orderId!=''){
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
                data: {status:status,orderId:orderId, ordernumber:ordernumber, membername:membername},
                
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
function chagequotationstatus(status, quotationId, quotationnumber, membername=''){
  var uurl = SITE_URL+"member/update-member-quotation-status";
if(quotationId!=''){
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
            data: {status:status,quotationId:quotationId, quotationnumber:quotationnumber, membername:membername},
            
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
} */
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
  /* var countryid = $("#countryid").val().trim();
  var provinceid = $("#provinceid").val().trim();
  var cityid = $("#cityid").val().trim(); */

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
        var uurl = SITE_URL+"member/add-billing-address";
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
      var uurl = SITE_URL+"member/update-billing-address";
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

function pointshistoryresetdata(){

  $("#point_div").removeClass("has-error is-focused");
  $("#rate_div").removeClass("has-error is-focused");
  $("#detail_div").removeClass("has-error is-focused");
  $("#pointsdate_div").removeClass("has-error is-focused");
 
  if(ACTION==0){
    $('#point').val('');
    $('#rate').val('');
    $('#detail').val('');
    $('#pointsdate').val('');
  }

  $('#creditpoints').prop("checked", true);
  $('#point').focus();
  
}

function pointshistorycheckvalidation(){
  
  var point = $("#point").val();
  var rate = $("#rate").val();
  var detail = $("#detail").val().trim();
  var date = $("#pointsdate").val();
  
  var isvalidpoint = isvalidrate = isvaliddetail = isvaliddate =1;
  
  PNotify.removeAll();
  if(point == ''){
    $("#point_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter points !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidpoint = 0;
  }else { 
    if(isNaN(point)){
      $("#point_div").addClass("has-error is-focused");
      new PNotify({title: 'Point allow only numbers !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidpoint = 0;
    }
  }
  if(rate == ''){
    $("#rate_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter rate !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidrate = 0;
  }else { 
    if(isNaN(rate)){
      $("#rate_div").addClass("has-error is-focused");
      new PNotify({title: 'Rate allow only numbers !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidrate = 0;
    }
  }

  if(date == ''){
    $("#pointsdate_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select date !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvaliddate = 0;
  }

  if(detail == ''){
    $("#detail_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter points detail !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvaliddetail = 0;
  }else { 
    if(detail.length<3){
      $("#detail_div").addClass("has-error is-focused");
      new PNotify({title: 'Details required minimum 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvaliddetail = 0;
    }
  }
  
  if(isvalidpoint == 1 && isvalidrate == 1 && isvaliddetail == 1 && isvaliddate == 1){

    var formData = new FormData($('#pointshistoryform')[0]);
   
    var uurl = SITE_URL+"member/add-points-history";
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
          new PNotify({title: "Points history successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
          
          pointhistorytable.ajax.reload();
          $('#pointshistoryform').trigger("reset");
          $('#pointsmodal').modal('hide');
          pointshistoryresetdata();
          
        }else{
          new PNotify({title: 'Points history not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

function exportpointshistoryreport(){
  
  var startdate = $('#pointhistorystartdate').val();
  var enddate = $('#pointhistoryenddate').val();
  var memberid = $('#memberid').val();
  var type = $('#type').val();
  var transactiontype = $('#transactiontype').val();
  
  var totalRecords =$("#pointhistorytable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    window.location= SITE_URL+"points-history-report/exportpointshistoryreport?startdate="+startdate+"&enddate="+enddate+"&memberid="+memberid+"&type="+type+"&transactiontype="+transactiontype;
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  
}

function exportproduct(memberid){
  memberid = memberid || 0;
  var categoryid = ($("#categoryid").val()!=null?$("#categoryid").val().join(','):'');
  var brandid = $("#brandid").val();

  var totalRecords =$("#producttable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    window.location= SITE_URL+"product/exportproduct?memberid="+memberid+"&brandid="+brandid+"&categoryid="+categoryid;
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
function assignbrand(){
  PNotify.removeAll();

  $("#productbrand_div").removeClass("has-error is-focused");
  $("#productbrandchannelid").val(Channel_ID);
  $("#productbrandid").val(0);
  
  $('.selectpicker').selectpicker('refresh');
  
  $('#assignbrandModal').modal('show');
}
function checkvalidationassignbrand(){

  var channelid = $("#productbrandchannelid").val();
  var brandid = $("#productbrandid").val();

  var isvalidchannelid = isvalidbrandid = 0;
  
  PNotify.removeAll();
  
  //CHECK FILE
  if(brandid==0){
    $("#productbrand_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select brand !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }else{
      $("#productbrand_div").removeClass("has-error is-focused");
      isvalidbrandid = 1;
  }
  if(isvalidbrandid==1){
    $("#assignbrandform").append($("<input>").attr("type", "hidden").attr("name", "memberid").val($("#memberid").val()));
    $("#assignbrandform").append($("<input>").attr("type", "hidden").attr("name", "channelid").val($("#channelid").val()));

    var formData = new FormData($('#assignbrandform')[0]);
    var uurl = SITE_URL+"member/assignBrandProductForMember";
    
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
            new PNotify({title: "Brand products successfully assigned.",styling: 'fontawesome',delay: '3000',type: 'success'});
             setTimeout(function() { window.location.reload(); }, 1500);
        }else if(response==2){
          new PNotify({title: "Products not available in selected brand !",styling: 'fontawesome',delay: '3000',type: 'error'});
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