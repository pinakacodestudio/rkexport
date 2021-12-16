$(document).ready(function() {
    
    oTable = $('#targetoffertable').DataTable
    ({
      "language": {
        "lengthMenu": "_MENU_"
      },
      "pageLength": 25,
      "columnDefs": [{
        'orderable': false,
        'targets': [0,-1,-4]
      },{'targets': [4,5], className: "text-right"}],
      "order": [], //Initial no order.
      'serverSide': true,//Feature control DataTables' server-side processing mode.
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": SITE_URL+"target-offer/listing",
        "type": "POST",
        "data": function ( data ) {
          data.startdate = $('#startdate').val();
          data.enddate = $('#enddate').val();
          data.channelid = $('#channelid').val();
          data.memberid = $('#memberid').val();
          data.offerid = $('#offerid').val();
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

    $('.dataTables_filter input').attr('placeholder','Search...');

    //DOM Manipulation to move datatable elements integrate to panel
    $('.panel-ctrls.panel-tbl').append($('.dataTables_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('.panel-ctrls.panel-tbl').append("<i class='separator'></i>");
    $('.panel-ctrls.panel-tbl').append($('.dataTables_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

    $('.panel-footer').append($(".dataTable+.row"));
    $('.dataTables_paginate>ul.pagination').addClass("pull-right pagination-md");

    $('#datepicker-range').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayBtn:"linked"
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

    $("#channelid").change(function(){
        getmembers();
    });
});

$(document).on('change', '.qty', function() {
  var id = $(this).attr("id").match(/(\d+)/g);
  var remainqty = $("#remainqty"+id).val();

  if(parseFloat(this.value) > parseFloat(remainqty)){
    $(this).val(parseFloat(remainqty).toFixed(2));
  }
});
$(document).on('keyup', '#redeempoints', function() {
  var memberredeempoint = $("#memberredeempoint").html();

  if(parseInt(this.value) > parseInt(memberredeempoint)){
    $(this).val(parseInt(memberredeempoint));
  }
});
function applyFilter(){
  oTable.ajax.reload(null, false);
}
function getmembers(type=0){
  
  var memberelement = $("#memberid");
  var channelelement = $("#channelid");

  if(type==1){
    memberelement = $("#sellermemberid");
    channelelement = $("#sellerchannelid");
  }
  memberelement.find('option')
              .remove()
              .end()
              .val('0')
              .append('<option value="0">All '+Member_label+'</option>')
            ;
  memberelement.selectpicker('refresh');
  var channelid = channelelement.val();

  if(channelid!='' && channelid!=0){
    var uurl = SITE_URL+"member/getmembers";
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {channelid:channelid},
      dataType: 'json',
      async: false,
      success: function(response){

        for(var i = 0; i < response.length; i++) {

          memberelement.append($('<option>', { 
            value: response[i]['id'],
            text : ucwords(response[i]['namewithcodeormobile'])
          }));

        }
        memberelement.selectpicker('refresh');
      },
      error: function(xhr) {
        //alert(xhr.responseText);
      },
    });
  }
}
function getgiftproduct(offerid,memberid){

  if(offerid!=""){
    var uurl = SITE_URL+"target-offer/getGiftProductByOfferId";
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {offerid:offerid,memberid:memberid},
      dataType: 'json',
      async: false,
      success: function(response){

        var pointsdata = response['pointsdata'];
        var productdata = response['productdata'];

        var HTML = "";
        if(productdata.length > 0){
          for(var i = 0; i < productdata.length; i++) {
            HTML += '<tr>';
            HTML += '<td>'+productdata[i]['productname']+'\
                    <input type="hidden" name="assigngiftproductid[]" value="'+productdata[i]['assigngiftproductid']+'">\
                    <input type="hidden" name="offerproductid[]" value="'+productdata[i]['id']+'">\
                    <input type="hidden" name="productvariantid[]" value="'+productdata[i]['productvariantid']+'">\
                    <input type="hidden" id="remainqty'+productdata[i]['id']+'" name="remainqty[]" value="'+productdata[i]['quantity']+'">\
                    </td>';
            HTML += '<td class="text-right">'+productdata[i]['currentstock']+'</td>';
            HTML += '<td>\
                      <div class="form-group mt-n">\
                        <div class="col-md-12">\
                          <input type="text" name="quantity[]" id="quantity'+productdata[i]['id']+'" value="'+productdata[i]['quantity']+'" class="form-control qty m-n">\
                        </div>\
                      </div>\
                    </td>';
            HTML += '<td><div class="checkbox m-n"><input onchange="singlecheck(this.id)" type="checkbox" value="'+productdata[i]['id']+'" name="deletecheck'+productdata[i]['id']+'" id="deletecheck'+productdata[i]['id']+'" class="checkradios"><label for="deletecheck'+productdata[i]['id']+'"></label></div></td>';
            HTML += '</tr>';
          }
          $("#giftofferid").val(offerid);
          $("#giftmemberid").val(memberid);
          $("#giftproductdata").html(HTML);
          $("#GiftModal").modal('show');

          $(".qty").TouchSpin({
            initval: 0,
            min: 1,
            max: 9999,
            decimals: 2,
            forcestepdivisibility: 'none',
            boostat: 5,
            maxboostedstep: 10,
            verticalbuttons: true,
            verticalupclass: 'glyphicon glyphicon-plus',
            verticaldownclass: 'glyphicon glyphicon-minus'
          });

          $("#redeempoints").val(pointsdata['sellerpoint']);
          $("#redeempointsrate").val(pointsdata['rate']);
          $("#memberredeempoint").html(pointsdata['points']);
        }else{
          $("#giftproductdata").html("");
          new PNotify({title: "Gift already assigned !",styling: 'fontawesome',delay: '3000',type: 'error'});
        }
      },
      error: function(xhr) {
        //alert(xhr.responseText);
      },
    });
  }
}

function viewgiftproduct(offerid,memberid){

  if(offerid!=""){
    var uurl = SITE_URL+"target-offer/getAssignGiftProductByOfferId";
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {offerid:offerid,memberid:memberid},
      dataType: 'json',
      async: false,
      success: function(response){

        var HTML = "";
        if(response.length > 0){
          for(var i = 0; i < response.length; i++) {
            HTML += '<tr>';
            HTML += '<td>'+response[i]['productname']+'</td>';
            HTML += '<td>'+response[i]['quantity']+'</td>';
            HTML += '</tr>';
          }

          $("#assigngiftproductdata").html(HTML);
          $("#ViewGiftModal").modal('show');
        }else{
          HTML += '<tr>';
          HTML += '<td colspan="2" class="text-center">No data available in table.</td>';
          HTML += '</tr>';
          new PNotify({title: "Not assign any gift !",styling: 'fontawesome',delay: '3000',type: 'error'});
        }
        

      },
      error: function(xhr) {
        //alert(xhr.responseText);
      },
    });
  }
}
function displayCNError(){
  new PNotify({title: 'Credit note already generated !',styling: 'fontawesome',delay: '3000',type: 'error'});
}
function assigngift(){
  var productchecked = $('#GiftModal .checkradios:checked').length;
  var quantity = $("input[name='quantity[]']").map(function(){return $(this).val();}).get();

  var isvalidproductchecked = isvalidquantity = 1;
  
  PNotify.removeAll();
  if(quantity.length > 0){
      var countqty = 0;
      for(var i=0; i<quantity.length; i++){
          if(quantity[i]=="" || parseFloat(quantity[i]) == "0"){
              countqty++;
          }
      }
      if(countqty == quantity.length){
          isvalidquantity = 0;
          new PNotify({title: 'Please enter atleast one assign quantity !',styling: 'fontawesome',delay: '3000',type: 'error'});
      }
  }
  if(productchecked == 0) {
    new PNotify({title: 'Please tick atleast one checkbox !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidproductchecked = 0;
  }
  
  if(isvalidproductchecked == 1 && isvalidquantity == 1){

      var formData = new FormData($('#assigngiftform')[0]);
      var baseurl = SITE_URL + 'target-offer/assignGift';
      $.ajax({
          
          url: baseurl,
          type: 'POST',
          data: formData,
          //async: false,
          beforeSend: function(){
              $('.mask').show();
              $('#loader').show();
          },
          success: function(response){
              
              if(response==1){
                new PNotify({title: 'Gift successfully assign !',styling: 'fontawesome',delay: '3000',type: 'success'});
                setTimeout(function() { window.location.reload(); }, 1500);
              }else{
                  new PNotify({title: 'Gift not assign !',styling: 'fontawesome',delay: '3000',type: 'error'});
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