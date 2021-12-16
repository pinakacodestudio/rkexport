$(document).ready(function()
{
    $('#datepicker-range').datepicker({
        //todayHighlight: true,
        format: 'dd/mm/yyyy',
        autoclose: true,
        startDate: new Date(),
        // orientation:"bottom"
    });

    if(ACTION==1)
    {
        $("#targettypeid_div").show();
        gettypedata();
        $("#typeid").val(referenceid);
        $('#typeid').selectpicker('refresh');
        if(startdate!=0)
        {
            $("#startdate_div").show();
            $("#datecheckbox").prop("checked",true);
            $("#duration").prop("disabled", true);
            $("#duration").val("0");
            $('#duration').selectpicker('refresh');
        }
        else
        {
            $("#startdate_div").hide(); 
            $("#duration").prop("disabled", false);
            $('#duration').selectpicker('refresh');
        }
    }
    else
    {   
        
        $("#targettypeid_div").hide();
        $("#startdate_div").hide();
    }

    $("#datecheckbox").click(function()
    {
        if($("#datecheckbox").prop("checked"))
        {
            $("#startdate_div").show();
            $("#duration").prop("disabled", true);
            $("#duration").val("0");
            $('#duration').selectpicker('refresh');
        }
        else
        {
            $("#startdate_div").hide();
            $("#duration").prop("disabled", false);
            $('#duration').selectpicker('refresh');
        }
    });
});

function gettypedata()
{

    /*Employee*/
    if($("#type").val()==1)
    {
      $("#targettypeid_div").show();
      $("#targettype_heading").html("Employee <span class='mandatoryfield'>*</span>");
      $("#targettype_select_heading").html("Select Employee");

      var uurl = SITE_URL+"Target/getEmployee";
      $.ajax({
        url: uurl,
        type: 'POST',
        dataType: 'json',
        async: false,
        success: function(response){
          $('#typeid')
          .find('option')
          .remove()
          .end()
          .append('<option value="0">Select Employee</option>')
          .val('whatever')
          ;

          for(var i = 0; i < response.length; i++) {

            $('#typeid').append($('<option>', { 
              value: response[i]['id'],
              text : response[i]['username']
            }));

          }
          $('#typeid').val(referenceid);
          $('#typeid').selectpicker('refresh');
        },
        error: function(xhr) {
              //alert(xhr.responseText);
            },
          });
    }
    /*Zone*/
    else if($("#type").val()==2){
      $("#targettypeid_div").show();
      $("#targettype_heading").html("Zone <span class='mandatoryfield'>*</span>");
      $("#targettype_select_heading").html("Select Zone");

      var uurl = SITE_URL+"Target/getZone";
      $.ajax({
        url: uurl,
        type: 'POST',
        dataType: 'json',
        async: false,
        success: function(response){
          $('#typeid')
          .find('option')
          .remove()
          .end()
          .append('<option value="0">Select Zone</option>')
          .val('whatever')
          ;

          for(var i = 0; i < response.length; i++) {
            $('#typeid').append($('<option>', { 
              value: response[i]['id'],
              text : response[i]['zonename']
            }));
          }
          //console.log(referenceid);
          //$('#typeid').val('2');
          $('#typeid').selectpicker('refresh');
        },
        error: function(xhr) {
            },
      });
    }
    /*Product*/
    else if($("#type").val()==3){
      $("#targettypeid_div").show();
      $("#targettype_heading").html("Product <span class='mandatoryfield'>*</span>");
      $("#targettype_select_heading").html("Select Product");

      var uurl = SITE_URL+"Target/getProduct";
      $.ajax({
        url: uurl,
        type: 'POST',
        dataType: 'json',
        async: false,
        success: function(response){
          $('#typeid')
          .find('option')
          .remove()
          .end()
          .append('<option value="0">Select Product</option>')
          .val('whatever')
          ;

          for(var i = 0; i < response.length; i++) {

            var productname = response[i]['name'].replace("'","&apos;");
            if(DROPDOWN_PRODUCT_LIST==0){
                
                $('#typeid').append($('<option>', { 
                    value: response[i]['id'],
                    text : productname
                }));
            }else{
                
              $('#typeid').append($('<option>', { 
                value: response[i]['id'],
                //text : ucwords(response[i]['name'])
                "data-content" :'<img src="'+PRODUCT_PATH+response[i]['image']+'" style="width:40px">  ' + productname
              }));
            }

          }
          $('#typeid').val(referenceid);
          $('#typeid').selectpicker('refresh');
        },
        error: function(xhr) {
            },
      });
    } else {
      $('#type')
          .find('option')
          .remove()
          .end()
          .append('<option value="0"></option>')
          .val('whatever')
          ;
          $('#typeid').selectpicker('refresh');
          $("#targettype_heading").html("");
          $("#targettype_select_heading").html("");
          $("#targettypeid_div").hide();      
    } 
}

function checkvalidation(){
  
  var type = $("#type").val();
  var typeid = $("#typeid").val();
  var revenue = $("#revenue").val().trim();
  var orders = $("#orders").val().trim();
  var leads = $("#leads").val().trim();
  var meetings = $("#meetings").val().trim();
  var duration = $("#duration").val().trim();
  var fromdate = $("#startdate").val().trim();
  var todate = $("#enddate").val().trim();

  var isvalidtype = isvalidtypeid = isvalidrevenue = isvalidorders = isvalidleads = isvalidmeetings = isvalidduration = isvalidfromdate = isvalidtodate = 0 ;
  
  PNotify.removeAll();

  if(type == 0 || type==null){
    $("#targettype_div").addClass("has-error is-focused");
    new PNotify({title: "Please select target type !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidtype = 0;
  }else {
      isvalidtype = 1;
  }

  if(typeid == 0 || typeid==null){
    var type_heading="";

    if(type==1)
    {type_heading="employee";}
    if(type==2)
    {type_heading="zone";}
    if(type==3)
    {type_heading="product";}

    if(type_heading!="")
    {
      $("#typeid_div").addClass("has-error is-focused");
      new PNotify({title: "Please select "+type_heading+"!",styling: 'fontawesome',delay: '3000',type: 'error'});
    }
    isvalidtypeid = 0;
  }else {
      isvalidtypeid = 1;
  }

  if(revenue == ''){
    $("#revenue_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter revenue!',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidrevenue = 0;
  }else {
      isvalidrevenue = 1;
  }

  if(leads == ''){
    $("#leads_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter leads!',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidleads = 0;
  }else {
      isvalidleads = 1;
  }

  if(orders == ''){
    $("#orders_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter orders!',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidorders = 0;
  }else {
      isvalidorders = 1;
  }

   if(meetings == ''){
    $("#meetings_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter meetings!',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidmeetings = 0;
  }else {
      isvalidmeetings = 1;
  }

  if($("#datecheckbox").prop("checked")==false)
  {
    if(duration == 0 || duration==null){
      $("#duration_div").addClass("has-error is-focused");
      new PNotify({title: "Please select duration !",styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidduration = 0;
    }else {
        isvalidduration = 1;
    }
  }else{
    $("#duration_div").removeClass("has-error is-focused");
      isvalidduration = 1;
  }

  if($("#datecheckbox").prop("checked"))
  {
    if(fromdate == ''){
        $("#startdate_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select start date!',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidfromdate = 0;
      }else {
          isvalidfromdate = 1;
      }
      if(todate == ''){
        $("#startdate_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select end date!',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidtodate = 0;
      }else {
          isvalidtodate = 1;
      }
  }
  else
  {
    isvalidfromdate=1;
    isvalidtodate=1;
  }

  if(isvalidtype == 1 && isvalidtypeid == 1 && isvalidrevenue == 1 && isvalidorders == 1 && isvalidleads == 1 && isvalidmeetings == 1 && isvalidduration == 1 && isvalidfromdate == 1 && isvalidtodate == 1)
  {

    var formData = new FormData($('#targetform')[0]);
    if(ACTION==0){
      var uurl = SITE_URL+"Target/add-target";
      
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
            new PNotify({title: "Target successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
            setTimeout(function() { window.location=SITE_URL+"target"; }, 1500);
         }else{
            new PNotify({title: 'Target not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
      var uurl = SITE_URL+"Target/update-target";
      
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
              new PNotify({title: "Target successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"target"; }, 1500);
          }else{
              new PNotify({title: 'Target not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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



