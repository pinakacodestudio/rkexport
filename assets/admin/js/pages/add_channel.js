$(document).ready(function(){
  $("#color").minicolors({
    control: $(this).attr('data-control') || 'hue',
    defaultValue: $(this).attr('data-defaultValue') || '',
    format: $(this).attr('data-format') || 'hex',
    keywords: $(this).attr('data-keywords') || '',
    inline: $(this).attr('data-inline') === 'true',
    letterCase: $(this).attr('data-letterCase') || 'lowercase',
    opacity: $(this).attr('data-opacity'),
    position: $(this).attr('data-position') || 'bottom',
    swatches: $(this).attr('data-swatches') ? $(this).attr('data-swatches').split('|') : [],
    change: function(value, opacity) {
      if( !value ) return;
      if( opacity ) value += ', ' + opacity;
      if( typeof console === 'object' ) {
      }
    },
    theme: 'bootstrap'
  });
  $(".selectcolor").minicolors({
    control: $(this).attr('data-control') || 'hue',
    defaultValue: $(this).attr('data-defaultValue') || '',
    format: $(this).attr('data-format') || 'hex',
    keywords: $(this).attr('data-keywords') || '',
    inline: $(this).attr('data-inline') === 'true',
    letterCase: $(this).attr('data-letterCase') || 'lowercase',
    opacity: $(this).attr('data-opacity'),
    position: $(this).attr('data-position') || 'bottom',
    swatches: $(this).attr('data-swatches') ? $(this).attr('data-swatches').split('|') : [],
    change: function(value, opacity) {
      if( !value ) return;
      if( opacity ) value += ', ' + opacity;
      if( typeof console === 'object' ) {
      }
    },
    theme: 'bootstrap'
  });
  $('.yesno input[type="checkbox"]').bootstrapToggle({
    on: 'Yes',
    off: 'No',
    onstyle: 'primary',
    offstyle: 'danger'
  });
  $('.generalmember input[type="checkbox"]').bootstrapToggle({
    on: Member_label,
    off: 'General',
    onstyle: 'primary',
    offstyle: 'danger'
  });
  $('.listing input[type="checkbox"]').bootstrapToggle({
    on: 'Scroll',
    off: 'Pagination',
    onstyle: 'primary',
    offstyle: 'danger'
  });
  $('.websitetype input[type="checkbox"]').bootstrapToggle({
    on: 'Online Store',
    off: 'Informative',
    onstyle: 'primary',
    offstyle: 'danger'
  });

  $('input[name="productwisepoints"]').change(function() {
    if($(this).prop("checked") == false){
      $('input[name="productwisepointsmultiplywithqty"]').bootstrapToggle('off');
      $('input[name="productwisepointsforseller"]').bootstrapToggle('off');
      $('input[name="productwisepointsforbuyer"]').bootstrapToggle('off');
    }
  });

  $('input[name="offermodule"]').change(function() {
    if($(this).prop("checked") == false){
      // $('input[name="offerdisplayonly"]').bootstrapToggle('off');
    }
  });


  /* DISCOUNT SETTING START*/
  $('input[name="discountonbill"]').click(function(){
    if ($(this).is(':checked')){
      if($(this).val() == 1){               
        $('#discountonbilldiv').show();
      }else{          
        $('#discountonbilldiv').hide();
      }
    }
  });

  $('.discountdaterangepicker').datepicker({
    // todayHighlight: true,
    format: 'dd/mm/yyyy',
    autoclose: true,
    startDate: new Date(),
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
  /* DISCOUNT SETTING END*/
})
function resetdata(){  
  
    $("#channel_div").removeClass("has-error is-focused");
    $("#priority_div").removeClass("has-error is-focused");
    $("#rewardforrefferedby_div").removeClass("has-error is-focused");
    $("#rewardfornewregister_div").removeClass("has-error is-focused");
    $("#conversationrate_div").removeClass("has-error is-focused");
    if(ACTION==0){
      $('#name').val('');
      $('#priority').val('');
      $('#rewardforrefferedby').val('');
      $('#rewardfornewregister').val('');
      $('#conversationrate').val('');
    }
    
    $('html, body').animate({scrollTop:0},'slow');
}
function checkvalidation(){
  
  var name = $("#name").val().trim();
  var color = $("#color").val().trim();
  var priority = $("#priority").val().trim();

  //color validation
  var themecolor = $("#themecolor").val();
  var fontcolor = $("#fontcolor").val();
  var sidebarbgcolor = $("#sidebarbgcolor").val();
  var sidebarmenuactivecolor = $("#sidebarmenuactivecolor").val();
  var sidebarsubmenubgcolor = $("#sidebarsubmenubgcolor").val();
  var sidebarsubmenuactivecolor = $("#sidebarsubmenuactivecolor").val();
  var footerbgcolor = $("#footerbgcolor").val();
  var linkcolor = $("#linkcolor").val();
  var tableheadercolor = $("#tableheadercolor").val();

  //Discount validation
  var percentageval = $("#percentageval").val().trim();
  var amount = $("#amount").val().trim();
  var discounttype = $("input[name='discountonbilltype']:checked").val();
  var discountonbill = $("input[name='discountonbill']:checked").val();
  var discountonbillminamount = $("input[name='discountonbillminamount']").val().trim();
  
  var isvalidname = isvalidpriority = isvalidthemecolor = isvalidfontcolor = isvalidsidebarbgcolor = isvalidsidebarmenuactivecolor = isvalidsidebarsubmenubgcolor = isvalidsidebarsubmenuactivecolor = isvalidfooterbgcolor = isvalidlinkcolor = isvalidtableheadercolor = 0 ;

  var isvalidnoofchannel = isvalidpercentageval = isvalidamount = isvaliddiscountonbillminamount = 1;
  
  
  PNotify.removeAll();

  if(parseInt(ChannelRecords)>=parseInt(NOOFCHANNEL)){
      new PNotify({title: 'Maximum '+NOOFCHANNEL+' channel allowed  !',styling: 'fontawesome',delay: '3000',type: 'error'});
      
      isvalidnoofchannel = 0;
  }


  if(isvalidnoofchannel==1){


    if(name == ''){
      $("#channel_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter name !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidname = 0;
    }else {
      if(name.length<3){
        $("#channel_div").addClass("has-error is-focused");
        new PNotify({title: 'Name require minimum 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidname = 0;
      }else{
        isvalidname = 1;
      }
    }

    if(color == ''){
      $("#color_div").addClass("has-error is-focused");
      new PNotify({title: 'Please select color !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidcolor = 0;
    }else {
        isvalidcolor = 1;
    }

    if(priority  == ''){
      $("#priority_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter priority !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidpriority  = 0;
    }else {
      isvalidpriority  = 1;
    }

    if(themecolor == ""){
      $("#themecolor_div").addClass("has-error is-focused");
      new PNotify({title: 'Please select theme color !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidthemecolor = 0;
    }
    else{
      isvalidthemecolor = 1;
    }

    if(fontcolor == ""){
      $("#fontcolor_div").addClass("has-error is-focused");
      new PNotify({title: 'Please Select Font Color !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidfontcolor = 0;
    }
    else{
      isvalidfontcolor = 1;    
    }

    if(sidebarbgcolor == ""){
      $("#sidebarbgcolor_div").addClass("has-error is-focused");
      new PNotify({title: 'Please Select Sidebar Background Color !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidsidebarbgcolor = 0;
    }
    else{
      isvalidsidebarbgcolor = 1;
    }

    if(sidebarmenuactivecolor == ""){
      $("#sidebarmenuactivecolor_div").addClass("has-error is-focused");
      new PNotify({title: 'Please Select Sidebar Menu Active Color !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidsidebarmenuactivecolor = 0;
    }
    else{
      isvalidsidebarmenuactivecolor = 1;
    }

    if(sidebarsubmenubgcolor == ""){
      $("#sidebarsubmenubgcolor_div").addClass("has-error is-focused");
      new PNotify({title: 'Please Select Sidebar Submenu Background Color !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidsidebarsubmenubgcolor = 0;
    }
    else{
      isvalidsidebarsubmenubgcolor = 1;
    }

    if(sidebarsubmenuactivecolor	 == ""){
      $("#sidebarsubmenuactivecolor	_div").addClass("has-error is-focused");
      new PNotify({title: 'Please Select Sidebar Submenu Active Color !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidsidebarsubmenuactivecolor	 = 0;
    }
    else{
      isvalidsidebarsubmenuactivecolor	 = 1;
    }

    if(footerbgcolor == ""){
      $("#footerbgcolor_div").addClass("has-error is-focused");
      new PNotify({title: 'Please Select Footer Color !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidfooterbgcolor = 0;
    }
    else{
      isvalidfooterbgcolor = 1;
    }

    if(linkcolor == ""){
      $("#linkcolor_div").addClass("has-error is-focused");
      new PNotify({title: 'Please Select Link Color !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidlinkcolor = 0;
    }
    else{
      isvalidlinkcolor = 1;
    }

    if(tableheadercolor == ""){
      $("#tableheadercolor_div").addClass("has-error is-focused");
      new PNotify({title: 'Please Select Table Header Color !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidtableheadercolor = 0;
    }
    else{
      isvalidtableheadercolor = 1;
    }

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


    if(isvalidname == 1 && isvalidpriority == 1 && isvalidcolor==1 && isvalidthemecolor == 1 && isvalidfontcolor == 1  && isvalidsidebarbgcolor == 1 && isvalidsidebarmenuactivecolor == 1 && isvalidsidebarsubmenubgcolor == 1 && isvalidsidebarsubmenuactivecolor == 1 && isvalidfooterbgcolor == 1 && isvalidlinkcolor == 1 && isvalidtableheadercolor == 1 && isvalidpercentageval == 1 && isvalidamount == 1 && isvaliddiscountonbillminamount == 1){

      var formData = new FormData($('#channelform')[0]);
      if(ACTION==0){
        var uurl = SITE_URL+"channel/add-channel";
        
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
              new PNotify({title: "Channel successfully added !",styling: 'fontawesome',delay: '3000',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"channel"; }, 1500);
            }else if(response==2){
              new PNotify({title: "Channel name or priority already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
              $("#channel_div").addClass("has-error is-focused");
              $("#priority_div").addClass("has-error is-focused");
            }else{
              new PNotify({title: 'Channel not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
        var uurl = SITE_URL+"channel/update-channel";
        
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
                new PNotify({title: "Channel successfully updated !",styling: 'fontawesome',delay: '1500',type: 'success'});
                setTimeout(function() { window.location=SITE_URL+"channel"; }, 1500);
            }else if(response==2){
              new PNotify({title: "Channel already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
              $("#channel_div").addClass("has-error is-focused");
            }else{
                new PNotify({title: 'Channel not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
}
  
  