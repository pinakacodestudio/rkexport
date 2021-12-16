function savefullscreenmode(type=""){

  if ((document.fullScreenElement !== undefined && document.fullScreenElement === null) || (document.msFullscreenElement !== undefined && document.msFullscreenElement === null) || (document.mozFullScreen !== undefined && !document.mozFullScreen) || (document.webkitIsFullScreen !== undefined && !document.webkitIsFullScreen)) {
    var isfullscreen = 1;     
  }else{
    var isfullscreen = 0;
  }
  $.ajax({
    url: SITE_URL+"process/windowFullScreenSave",
    type: 'POST',
    data: {type: type, isfullscreen: isfullscreen},
    success: function(data){
      
      /* if(data==1){
        var elem = document.documentElement;
        if (elem.requestFullScreen) {
            elem.requestFullScreen();
        } else if (elem.mozRequestFullScreen) {
            elem.mozRequestFullScreen();
        } else if (elem.webkitRequestFullScreen) {
            elem.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
        } else if (elem.msRequestFullscreen) {
            elem.msRequestFullscreen();
        }
      } */
    },
    error: function(xhr) {
      //alert(xhr.responseText);
    },
  });
}
$('form').on('reset', function(e){
  setTimeout(function() {resetdata();});
});

$(document).on('mouseover','table', function(e){
  if($('.popoverButton').length>1){
    $('.popoverButton').popover('hide');
    if($(e.target).hasClass('popoverButton')){
      $(e.target).popover('toggle');
    }
  }
});
$('#head_channelid').on('change', function (e) {
  e.preventDefault();
  
  var channelid = $(this).val();
  if(channelid==null){
    channelid="";
  }
  $.ajax({
      url: SITE_URL+"process/update-user-channel",
      type: 'POST',
      data: {channelid:channelid},
      success: function(data){
        location.reload();
      },
      error: function(xhr) {
      },
   });
   
});

function getfrontendsubmenu(mainmenuid){
  var succeed = 0;
  var uurl = SITE_URL+"frontend-sub-menu/getFrontendSubmenuList";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {mainmenuid:mainmenuid},
    dataType: 'json',
    async: false,
    success: function(response){
      $('#frontendsubmenuid')
      .find('option')
      .remove()
      .end()
      .append('<option value="0">Select Sub Menu</option>')
      .val('whatever')
      ;
      for(var i = 0; i < response.length; i++) {

        $('#frontendsubmenuid').append($('<option>', { 
          value: response[i]['id'],
          text : response[i]['name']
        }));

      }
      $('#frontendsubmenuid').val(frontendsubmenuid);
      $('#frontendsubmenuid').selectpicker('refresh');
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

var format = new Intl.NumberFormat('en-IN', { 
  /* currency: 'INR',  */
  minimumFractionDigits: 2, 
});
if(MANAGE_DECIMAL_QTY==1){
  var touchspinoptions = {
      initval: 0,
      min: 0,
      max: 999999,
      step: 1,
      decimals: 2,
      forcestepdivisibility: 'none',
      boostat: 5,
      maxboostedstep: 10,
      verticalbuttons: true,
      verticalupclass: 'glyphicon glyphicon-plus',
      verticaldownclass: 'glyphicon glyphicon-minus'
  };
}else{
  var touchspinoptions = {
      initval: 0,
      min: 1,
      max: 999999,
      step: 1,
      verticalbuttons: true,
      verticalupclass: 'glyphicon glyphicon-plus',
      verticaldownclass: 'glyphicon glyphicon-minus'
  };
}
/*
    runBtnBlur
    ========================================================================== */
    function runBtnBlur(morrisChart){

      $(".btn").mouseup(function(e){
        e.preventDefault();
        $(this).blur();
      });

    }
    function isUrl(s) {
     var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
     return regexp.test(s);
   }
   function isUrlValid(url) {
      return /^(https?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(url);
  }
   function isValidWebsite(url) {
      var urlregex = new RegExp(
          "^(http:\/\/www.|http:\/\/[0-9A-Za-z].|https:\/\/www.|ftp:\/\/www.|www.){1}([0-9A-Za-z]+\.)");
      return urlregex.test(url);
      //return /^(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(url);
    }
   function validateYouTubeUrl(url) {

    if (url != undefined || url != '') {
      var regExp = /^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/;
      var match = url.match(regExp);
      if (match && match[1].length == 11) {
          // Do anything for being valid
          // if need to change the url to embed url then use below line
          return match[1];
        }
        else {
          // Do anything for not being valid
        }
      }
    }
    
  function decimal_number_validation(event, value, len=5, lastdigitslen=2) {
      
      var present=0;
      var count=0;
     
      if((event.which < 45 || event.which > 58 || event.which == 47) && (event.which!=8 && event.which!=0)) {
        return false;
        event.preventDefault();
      
      } // prevent if not number/dot

      if(event.which == 46 && value.indexOf('.') != -1) {
          return false;
          event.preventDefault();
      } // prevent if already dot

      if(event.which == 45 && value.indexOf('-') != -1) {
          return false;
          event.preventDefault();
      } // prevent if already dot

      if(event.which == 45 && value.length>0) {
        event.preventDefault();
      } // prevent if already -
      
      var valuelength = value.length;
      var lastdigitslength = lastdigitslen;
      if(value.indexOf('.') != -1){
        valuelength = value.indexOf('.');
        lastdigitslength = value.substring(value.indexOf(".")+1,value.length);
      }
      // console.log(event.target.selectionEnd);
      if((valuelength>=len || lastdigitslength.length>=lastdigitslen) && (event.which!=8 && event.which!=0)){

        if(value.indexOf(".") == -1){ //without decimal
          if(valuelength>=len){
            if(event.keyCode != 46)
            return false;
          }
        }else{ // //with decimal
          if((valuelength+1)>len && lastdigitslength.length>lastdigitslen){
            if(event.keyCode != 46)
            return false;
          }
        }
        /* if((value.indexOf(".") == -1 && valuelength==len) || (value.indexOf(".") != -1 && lastdigitslength.length>=lastdigitslen)){
          if(event.keyCode != 46)
          return false;
        } *//* else if (lastdigitslength.length==lastdigitslen && valuelength==len){
          if(event.keyCode != 46)
          return false;
        } */
      }
      do{
       present=value.indexOf(".",present);

       if(present!=-1)
        {
         count++;
         present++;
        }
       }while(present!=-1);
       
      if(count==1 && (event.which!=8 && event.which!=0)) {
        var lastdigits=value.substring(value.indexOf(".")+1,value.length);
        if(lastdigits.length>=lastdigitslen){
          //alert("Two decimal places only allowed");
          if(value.indexOf(".") != -1 && value.indexOf(".")==len){
            event.keyCode=0;
            return false;
          }
        }
      }
      return true;
    }
    function float_validation(event, value, len=5, lastdigitslen=2){
      var present=0;
      var count=0;
      
      if((event.which < 45 || event.which > 58 || event.which == 47) && (event.which!=8 && event.which!=0)) {
        return false;
          event.preventDefault();
      
      } // prevent if not number/dot

      if(event.which == 46 && value.indexOf('.') != -1) {
          return false;
          event.preventDefault();
      } // prevent if already dot

      if(event.which == 45 && value.indexOf('-') != -1) {
              return false;
          event.preventDefault();
      } // prevent if already dot

      if(event.which == 45 && value.length>0) {
          event.preventDefault();
      } // prevent if already -
      if(value.length==len && (event.which!=8 && event.which!=0)){
        if(event.keyCode != 46)
        return false;
      }
      do{
       present=value.indexOf(".",present);

       if(present!=-1)
        {
         count++;
         present++;
         }
       }while(present!=-1);
       
      if(count==1 && (event.which!=8 && event.which!=0)) {
        var lastdigits=value.substring(value.indexOf(".")+1,value.length);
        if(lastdigits.length>=lastdigitslen){
          //alert("Two decimal places only allowed");
          event.keyCode=0;
          return false;
        }
      }
      return true;
    };

/*  ==========================================================================
    Function Calls
    ========================================================================== */

    $(function(){

	   	// Variables

		// === Checkers ===

		// === Setters ===

		// === Executions ===
    runBtnBlur();

  });
    var pattern = /(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g;
    var validemail = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    var passw = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,20}$/;
/*  ==========================================================================
    Functions
    ========================================================================== */
    function isNumber(evt) {
      evt = (evt) ? evt : window.event;
      var charCode = (evt.which) ? evt.which : evt.keyCode;
      if (charCode > 31 && (charCode < 48 || charCode > 57)) {
       return false;
     }
     return true;
   }
   function isPhonecode(evt) {
      evt = (evt) ? evt : window.event;
      var charCode = (evt.which) ? evt.which : evt.keyCode;
      if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode!=43) {
       return false;
     }
     return true;
   }
   function ValidateEmail(mail){  
    //var res = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;

    var res = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    if (res.test(mail)) {  
      return true;
    }  
    return false;  
  } 
  function CheckPassword(inputtxt) 
  { 
    // var passw = /^(?=.*[!@#$%_''""/=(){}\^\&*-.\?])[a-zA-Z0-9!@#$%_''""/=(){}\^\&*-.\?]{6,20}$/;
    var passw = /^(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[!@#$%_''""/=(){}\^\&*-.`\?]).{6,20}$/;
    if(inputtxt.match(passw)){ 
      return true;
    }else{ 
      return false;
    }
  }
  function alphanumeric(e){ 
    var regex = new RegExp("^[a-zA-Z0-9]+$");
    var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
    if (regex.test(str)) {
        return true;
    }

    e.preventDefault();
    return false;
  }
  function alphanumericspaces(e){ 
    var regex = new RegExp("^[a-zA-Z0-9 ]+$");
    var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
    if (regex.test(str)) {
        return true;
    }

    e.preventDefault();
    return false;
  }
  function validslug(e){  
    var regex = new RegExp("^[a-z0-9-]+$");
    var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
    if (regex.test(str)) {
        return true;
    }

    e.preventDefault();
    return false;
  } 
  function decimal(event,val){
    if (((event.which != 46 || (event.which == 46 && val == '')) ||
            val.indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
        event.preventDefault();
    }
  }
  function onlyAlphabets(evt) {
    evt = (evt) ? evt : window.event;
    var inputValue = evt.charCode;
    
    if(!(inputValue >= 65 && inputValue <= 122) && (inputValue != 32 && inputValue != 0)){
        evt.preventDefault();
    }
//    var charCode = (evt.which) ? evt.which : evt.keyCode;
//    
//    if ((charCode > 64 && charCode < 91) || (charCode > 96 && charCode < 123)){
//        
//      return true;
//    }
//     return false;
   }
   function ordinal_suffix_of(i) {
      var j = i % 10,
          k = i % 100;
      if (j == 1 && k != 11) {
          return i + "st";
      }
      if (j == 2 && k != 12) {
          return i + "nd";
      }
      if (j == 3 && k != 13) {
          return i + "rd";
      }
      return i + "th";
  }
   function qualification(e){  
    var regex = new RegExp("^[a-zA-Z0-9,.-]+$");
    var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
    if (regex.test(str)) {
        return true;
    }

    e.preventDefault();
    return false;
  }
    // Generate a password string
    function randString(len=8){

      var possible = '';
      possible += 'abcdefghijklmnopqrstuvwxyz';
      possible += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
      possible += '0123456789';
      possible += '!{}()%&*$#^@';
      
      var text = '';
      for(var i=0; i < len; i++) {
        text += possible.charAt(Math.floor(Math.random() * possible.length));
      }
      return text;
    }
    /*==========================================================================
    Here is a function provides you more options to set min of special chars, min of upper chars, min of lower chars and min of number (min = 0)
    ========================================================================== */
    function randomPassword(len = 8, minUpper = 1, minLower = 1, minNumber = 1, minSpecial = 1) {
      let chars = String.fromCharCode(...Array(127).keys()).slice(33),//chars
          A2Z = String.fromCharCode(...Array(91).keys()).slice(65),//A-Z
          a2z = String.fromCharCode(...Array(123).keys()).slice(97),//a-z
          zero2nine = String.fromCharCode(...Array(58).keys()).slice(48),//0-9
          specials = chars.replace(/\w/g, '')
      if (minSpecial < 0) chars = zero2nine + A2Z + a2z
      if (minNumber < 0) chars = chars.replace(zero2nine, '')
      let minRequired = minSpecial + minUpper + minLower + minNumber
      let rs = [].concat(
          Array.from({length: minSpecial ? minSpecial : 0}, () => specials[Math.floor(Math.random() * specials.length)]),
          Array.from({length: minUpper ? minUpper : 0}, () => A2Z[Math.floor(Math.random() * A2Z.length)]),
          Array.from({length: minLower ? minLower : 0}, () => a2z[Math.floor(Math.random() * a2z.length)]),
          Array.from({length: minNumber ? minNumber : 0}, () => zero2nine[Math.floor(Math.random() * zero2nine.length)]),
          Array.from({length: Math.max(len, minRequired) - (minRequired ? minRequired : 0)}, () => chars[Math.floor(Math.random() * chars.length)]),
      )
      return rs.sort(() => Math.random() > Math.random()).join('')
    }
/*  ==========================================================================
    Uppercase first letter
    ========================================================================== */
    function ucwords(str){
      str = str.toLowerCase().replace(/\b[a-z]/g, function(letter) {
        return letter.toUpperCase();
      });
      return str;
    }
/*  ==========================================================================
    Enable Disable
    ========================================================================== */
    function enabledisable(val,id,uurl,title,disable_class,enable_class,disable_title,enable_title,disable_test,enable_text){
      if(val==0){
        swal({    title: 'Are you sure want to '+title+'?',
          type: "warning",   
          showCancelButton: true,   
          confirmButtonColor: "#DD6B55",   
          confirmButtonText: "Yes, "+title+" it!",
          timer: 2000,   
          closeOnConfirm: false }, 
          function(isConfirm){
            if (isConfirm) {   
              enabledisableconfirm(val,id,uurl,title,disable_class,enable_class,disable_title,enable_title,disable_test,enable_text);
              
            }
          });
      }else{
        swal({    title: 'Are you sure want to '+title+'?',
          type: "warning",   
          showCancelButton: true,   
          confirmButtonColor: "#DD6B55",   
          confirmButtonText: "Yes, "+title+" it!",
          timer: 2000,   
          closeOnConfirm: false }, 
          function(isConfirm){
            if (isConfirm) {   
              enabledisableconfirm(val,id,uurl,title,disable_class,enable_class,disable_title,enable_title,disable_test,enable_text);
            }
          });
      }
    }
    function enabledisableconfirm(val,id,uurl,title,disable_class,enable_class,disable_title,enable_title,disable_text,enable_text)
    {
      var datastr = 'id='+id+'&val='+val;
      $.ajax({
        url: uurl,
        type: 'POST',
        data: datastr,
        beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
          },
        success: function (data) {
          if(data == id){

            if(uurl == SITE_URL+"sales-commission/sales-commission-enable-disable"){
              location.reload();
            }else{
              var enablehtml = "<a href='#' class='btn-floating btn-flat waves-effect waves-light white-text "+disable_class+"' onclick=\"enabledisable(0,"+id+",'"+uurl+"','"+disable_title+"','"+disable_class+"','"+enable_class+"','"+disable_title+"','"+enable_title+"','"+disable_text.replace(/'/g, "\\'")+"','"+enable_text.replace(/'/g, "\\'")+"')\" title='"+disable_title+"'>"+disable_text+"</a>";
              var disablehtml = "<a href='#' class='btn-floating btn-flat waves-effect waves-light white-text "+enable_class+"' onclick=\"enabledisable(1,"+id+",'"+uurl+"','"+enable_title+"','"+disable_class+"','"+enable_class+"','"+disable_title+"','"+enable_title+"','"+disable_text.replace(/'/g, "\\'")+"','"+enable_text.replace(/'/g, "\\'")+"')\" title='"+enable_title+"'>"+enable_text+"</a>";
              swal.close();
              if(val == 0){
                $("#span"+id).html(disablehtml);
              }else{
                $("#span"+id).html(enablehtml);
              }
            }
          }else if(data==0 && uurl == SITE_URL+"sms-gateway/sms-gateway-enable-disable"){
            swal("Failed", "Another SMS Gateway was Enabled !", "error");
          }else if(data==0 && uurl == SITE_URL+"member/member-enable-disable"){
            swal.close();
            new PNotify({title: 'Maximum '+member_label+' limit exceeded in this channel !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }
        },
        complete: function(){
          $('.mask').hide();
          $('#loader').hide();
        },
      });
    }

/*  ==========================================================================
    Multiple Delete
    ========================================================================== */
    var currentdids = [];
    var position = 0;
    var inputs = $("input[type='checkbox']");
    function singlecheck(id){
      var inputs = $("input[type='checkbox']");
      var isallchecked = 1,isalldechecked = 1;
      
      if ($('#'+id).prop('checked')==true){
        currentdids[position] = $('#'+id).val();                      
        position++;
        for(var i = 1; i<inputs.length; i++){
          if($('#'+inputs[i].id).prop('checked') == true){
            isallchecked = 1;
          }else{
            isallchecked = 0;
            break;
          }
        }
        if(isallchecked == 1){
          $('#deletecheckall').prop('checked', true);
        }
      }else{
        currentdids.splice($.inArray($('#'+id).val(), currentdids),1);
        for(var i = 1; i<inputs.length; i++){
          if($('#'+inputs[i].id).prop('checked') == false){
            $('#deletecheckall').prop('checked', false);
            break;
          }
        }
        position--;
      }
    }

    function allchecked(){
      var inputs = $("input[type='checkbox']");
      if ($('#deletecheckall').prop('checked')==true){
        for(var i = 1; i<inputs.length; i++){
          $('#'+inputs[i].id).prop('checked', true);
          if($('#'+inputs[i].id).prop('checked') == true){
            if(jQuery.inArray($('#'+inputs[i].id).val(),currentdids) == -1){
              currentdids[position] = $('#'+inputs[i].id).val();
              position++;
            }
          }
        }
      }
      else{ 
        for(var i = 1; i<inputs.length; i++){
          currentdids.splice($.inArray($('#'+inputs[i].id).val(), currentdids),1);
          $('#'+inputs[i].id).prop('checked', false);
          position--;
        }
      }
    }

    function deleterow(id,url,name,deleteurl,tablename='',productcount=''){

      if(url!=''){
        currentdids = id; 
        var uurl = url;
        $.ajax({
          url: uurl,
          type: 'POST',
          data: {ids:id},

          success: function(data){
            if(data==0){
              swal({    title: "Are you sure want to delete?",
                type: "warning",   
                showCancelButton: true,   
                confirmButtonColor: "#DD6B55",   
                confirmButtonText: "Yes, delete it!",   
                closeOnConfirm: false }, 
                function(isConfirm){   
                  if (isConfirm) {   
                    multipledelete(deleteurl,tablename,productcount);
                  }else{
                    if($('#deletecheckall').prop('checked') == true){
                      $('#deletecheckall').prop('checked', false);
                    }
                    for(var i=1;i<inputs.length;i++){
                      if($('#'+inputs[i].id).prop('checked') == true){
                        $('#'+inputs[i].id).prop('checked', false);
                      }
                    }
                    currentdids = [];
                    position = 0;
                  }
                });

            }else{
              swal("Cancelled",ucwords(name)+' is already used. So, delete is not allowed!', "error");
              currentdids = [];
              position = 0;
            }
          },
          error: function(xhr) {
                //alert(xhr.responseText);
              },
            });
      }else{
        currentdids = id;
        swal({    title: "Are you sure want to delete?",
          type: "warning",   
          showCancelButton: true,   
          confirmButtonColor: "#DD6B55",   
          confirmButtonText: "Yes, delete it!",   
          closeOnConfirm: false }, 
          function(isConfirm){   
            if (isConfirm) {   
              multipledelete(deleteurl,tablename,productcount);
            }else{
              if($('#deletecheckall').prop('checked') == true){
                $('#deletecheckall').prop('checked', false);
              }
              for(var i=1;i<inputs.length;i++){
                if($('#'+inputs[i].id).prop('checked') == true){
                  $('#'+inputs[i].id).prop('checked', false);
                }
              }
              currentdids = [];
              position = 0;
            }
          });
      }
    }

  function multipledelete(url,tablename='',productcount=''){ 
    var datastr = 'ids='+currentdids;
    var baseurl = url;
    $.ajax({
      url: baseurl,
      type: 'POST',
      data: datastr,
      success: function(data){
        if(tablename!="" && (productcount=="" || productcount > 1)){
          $('#'+tablename).DataTable().ajax.reload();
          swal.close();
        }else{
          location.reload();
        }
      }
    });
  }

  function checkmultipledelete(url,name,deleteurl){
    var inputs = $("input[type='checkbox']");
    if(currentdids == ""){
      swal("Cancelled", 'Please select '+name+' !', "error");
    }else{
      if(url!=''){
        var datastr = 'ids='+currentdids;
        var baseurl = url;
        $.ajax({
          url: baseurl,
          type: 'POST',
          data: datastr,
          success: function(data){
            if(data == 0){
              swal({    title: 'Are you sure to delete '+name+'?',
                type: "warning",   
                showCancelButton: true,   
                confirmButtonColor: "#DD6B55",   
                confirmButtonText: "Yes, delete it!",   
                closeOnConfirm: false }, 
                function(isConfirm){
                  if (isConfirm) {   
                    multipledelete(deleteurl);
                  }else{
                    if($('#deletecheckall').prop('checked') == true){
                      $('#deletecheckall').prop('checked', false);
                    }
                    for(var i=1;i<inputs.length;i++){
                      if($('#'+inputs[i].id).prop('checked') == true){
                        $('#'+inputs[i].id).prop('checked', false);
                      }
                    }
                    currentdids = [];
                    position = 0;
                  }
                });
            }else{
              if(data == currentdids.length){
                swal("Cancelled", "All "+ucwords(name)+" are used in other. So, you can't delete them!", "error");

                if($('#deletecheckall').prop('checked') == true){
                  $('#deletecheckall').prop('checked', false);
                }
                for(var i=1;i<inputs.length;i++){
                  if($('#'+inputs[i].id).prop('checked') == true){
                    $('#'+inputs[i].id).prop('checked', false);
                  }
                }
                currentdids = [];
                position = 0;
              }

              else if(data == 1){
                swal({    title: data+' '+ucwords(name)+' is used in other. So, you can not delete it. Still you want to delete remaining '+name+'?',
                  type: "warning",   
                  showCancelButton: true,   
                  confirmButtonColor: "#DD6B55",   
                  confirmButtonText: "Yes, delete it!",   
                  closeOnConfirm: false }, 
                  function(isConfirm){
                    if (isConfirm) {   
                      multipledelete(deleteurl);
                    }else{
                      if($('#deletecheckall').prop('checked') == true){
                        $('#deletecheckall').prop('checked', false);
                      }
                      for(var i=1;i<inputs.length;i++){
                        if($('#'+inputs[i].id).prop('checked') == true){
                          $('#'+inputs[i].id).prop('checked', false);
                        }
                      }
                      currentdids = [];
                      position = 0;
                    }
                  });
              }

              else{
                swal({    title: data+' '+ucwords(name)+' are used in other. So, you can not delete it. Still you want to delete remaining '+name+'?',
                  type: "warning",   
                  showCancelButton: true,   
                  confirmButtonColor: "#DD6B55",   
                  confirmButtonText: "Yes, delete it!",   
                  closeOnConfirm: false }, 
                  function(isConfirm){
                    if (isConfirm) {   
                      multipledelete(deleteurl);
                    }else{
                      if($('#deletecheckall').prop('checked') == true){
                        $('#deletecheckall').prop('checked', false);
                      }
                      for(var i=1;i<inputs.length;i++){
                        if($('#'+inputs[i].id).prop('checked') == true){
                          $('#'+inputs[i].id).prop('checked', false);
                        }
                      }
                      currentdids = [];
                      position = 0;
                    }
                  });
              }
            }
          }
        });
      }else{
        swal({    title: 'Are you sure to delete '+name+'?',
          type: "warning",   
          showCancelButton: true,   
          confirmButtonColor: "#DD6B55",   
          confirmButtonText: "Yes, delete it!",   
          closeOnConfirm: false }, 
          function(isConfirm){
            if (isConfirm) {   
              multipledelete(deleteurl);
            }else{
              if($('#deletecheckall').prop('checked') == true){
                $('#deletecheckall').prop('checked', false);
              }
              for(var i=1;i<inputs.length;i++){
                if($('#'+inputs[i].id).prop('checked') == true){
                  $('#'+inputs[i].id).prop('checked', false);
                }
              }
              currentdids = [];
              position = 0;
            }
          });
      }

    }
  }
  function getprovince(countryid, elementid = "provinceid"){
    var succeed = 0;
    $('#'+elementid)
        .find('option')
        .remove()
        .end()
        .append('<option value="0">Select Province</option>')
        .val('0')
    ;
    if(countryid!=0 && countryid!=""){
      var uurl = SITE_URL+"province/getProvinceList";
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {countryid:countryid},
        dataType: 'json',
        async: false,
        beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
        },
        success: function(response){
          
          for(var i = 0; i < response.length; i++) {
            $('#'+elementid).append($('<option>', { 
              value: response[i]['id'],
              text : response[i]['name']
            }));
          }
          
          if(provinceid!=null && provinceid!=0 && $.isNumeric(provinceid)){
            $('#'+elementid).val(provinceid);
          }
        },
        complete: function(){
          $('.mask').hide();
          $('#loader').hide();
          succeed = 1;
        },
        error: function(xhr) {
          //alert(xhr.responseText);
          succeed = 0;
        },
      });
    }
    $('#'+elementid).selectpicker('refresh');
    return succeed;
  }
  
  function getcity(provinceid, elementid = "cityid"){

    $('#'+elementid)
      .find('option')
      .remove()
      .end()
      .append('<option value="0">Select City</option>')
      .val('0')
    ;

    if(provinceid!=0 && provinceid!=""){
      var uurl = SITE_URL+"city/getCityList";
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {provinceid:provinceid},
        dataType: 'json',
        async: false,
        beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
        },
        success: function(response){
          
          for(var i = 0; i < response.length; i++) {

            $('#'+elementid).append($('<option>', { 
              value: response[i]['id'],
              text : response[i]['name']
            }));

          }
          if(cityid!=null && cityid!=0 && $.isNumeric(cityid)){
            $('#'+elementid).val(cityid);
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
    $('#'+elementid).selectpicker('refresh');
  }
  function getarea(cityid){

    var uurl = SITE_URL+"area/getAreaList";
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {cityid:cityid},
      dataType: 'json',
      async: false,
      success: function(response){
        $('#areaid')
          .find('option')
          .remove()
          .end()
          .append('<option value="0">Select Area</option>')
          .val('whatever')
        ;

        for(var i = 0; i < response.length; i++) {

          $('#areaid').append($('<option>', { 
            value: response[i]['id'],
            text : response[i]['areaname']
          }));

        }
        $('#areaid').val(areaid);
        $('#areaid').selectpicker('refresh');
      },
      error: function(xhr) {
        //alert(xhr.responseText);
      },
    });
  }
  function list(div,response_url,order,pageLength=10){
    var oTable = $('#'+div).dataTable
    ({
      "processing": true,//Feature control the processing indicator.
      "language": {
        "lengthMenu": "_MENU_"
      },
      
      "pageLength": pageLength,
      "columnDefs": [{
        'orderable': false,
        'targets': order
      }],
      "order": [], //Initial no order.
      'serverSide': true,//Feature control DataTables' server-side processing mode.
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": SITE_URL+response_url,
        "type": "POST",
        beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
        },
        complete: function(){
          $('.mask').hide();
          $('#loader').hide();
        },
      },
    });
  }
    
  function setorder(uurl,tablename='table'){
    
    if($('#btntype').text()=="Set Priority"){
      
      $('#btntype').html('Save');
      $(tablename+' > tbody').sortable({
          disabled:false,
          connectWith:'tbody',
          helper: 'clone',
          clone: true,
          axis: 'y',
          cursor: 'move',
          opacity: 0.7,
          scroll: false,
        
          helper: function(e, tr)
          {
            var $originals = tr.children();
            var $helper = tr.clone();
            $helper.children().each(function(index)
            {
              $(this).width($originals.eq(index).width());
                
            });
            startElement = $(tablename +' > tbody > tr > td:last-child > span').html();
            
            return $helper;
          },
          stop: function() {
            var i=(startElement==0)?1:startElement;

            var srno=1;
            $(tablename+' > tbody > tr').each(function(){
              $(this).find('td:first-child').html(srno);
              $(this).find('td:last-child > span').html(i);
              
              if(i!=0){
                /*if(tablename=='#premiumprofiletable'){  */
                  i++;
                /*  }else{
                  i--;  
                } */
              }
              srno++;
            });
          },
          beforeStop: function(e, tr) {

            
          },
      });
      
    }else{
      $('#btntype').html('Set Priority');
      $(tablename+" > tbody").sortable("disable");

      var parent = [];//top holder
      $(tablename+' > tbody > tr').each(function(){
      var child1 = {};
        
        sequenceno = $(this).find('td:last-child > span').text();
        // console.log(sequenceno);
        child1["sequenceno"] = sequenceno;
        child1["id"] = $(this).attr('id');
        parent.push(child1);
        /*$.ajax({
          url: uurl,
          type: 'POST',
          data: {sequenceno:sequenceno,id:$(this).attr('id')},
          success: function(response){
          },
          error: function(xhr) {
          },
          
        });*/
      }); 
      if(parent.length>0){
        $.ajax({
          url: uurl,
          type: 'POST',
          data: {sequencearray:parent},
          beforeSend: function(){
            $('.mask').show();
            $('#loader').show();
          },
          success: function(response){
          },
          error: function(xhr) {
          },
          complete: function(){
            $('.mask').hide();
            $('#loader').hide();
          },
        });
      }
      
    }
  }

  function readURL(input,name) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();

      reader.onload = function(e) {
        $('#'+name).attr('src', e.target.result);
      }

      reader.readAsDataURL(input.files[0]);
    }
    
  }   
  function resetpassword(userid){
    swal({title: "Are you sure want to reset password?",
      type: "warning",   
      showCancelButton: true,   
      confirmButtonColor: "#DD6B55",   
      confirmButtonText: "Yes, do it!",   
      closeOnConfirm: false }, 
      function(isConfirm){   
        if (isConfirm) {
          
          $.ajax({
            url: SITE_URL+"reset-password/member-reset-password",
            type: 'POST',
            data: {userid:userid},
            success: function(data){
              
              if(data==1){
                swal("Success!", "We will send new password to your email address!", "success");
              }else if(data==2){
                swal("Failed", "We can\'t send new password because email address not available!", "error");
              }else{
                swal("Failed", "We can\'t send new password to your email address !", "error");
              }
            },
            error: function(xhr) {
              swal("Failed", "We can\'t send new password to your email address !", "error");
              //alert(xhr.responseText);
            },
          });
        }else{
          
        }
      });
  }
  function setsidebarcollapsed(){

    var sessionclass = "";
    if ($("body").hasClass("sidebar-collapsed")) {
      sessionclass = "sidebar-scroll";
    }else{
      sessionclass = "sidebar-collapsed";
    }
    $.ajax({
      url: SITE_URL+"process/setsidebarcollapsed",
      type: 'POST',
      data: {sessionclass: String(sessionclass)},
      success: function(response){
        if ($("body").hasClass("sidebar-collapsed")) {
          $("body").removeClass("sidebar-scroll");
          $(".static-sidebar").removeClass("scroll-pane has-scrollbar");
          $(".sidebar").removeClass("scroll-content");
        }else{
          $("body").addClass("sidebar-scroll");
          $(".static-sidebar").addClass("scroll-pane has-scrollbar");
          $(".sidebar").addClass("scroll-content");
        }
      },
      error: function(xhr) {
        //alert(xhr.responseText);
      },
    });
  }
  function formatBytes(bytes) {
      if(bytes < 1024) return bytes + "Bytes";
      else if(bytes < 1048576) return parseInt(bytes / 1024) + "KB";
      else if(bytes < 1073741824) return parseInt(bytes / 1048576) + "MB";
      else return parseInt(bytes / 1073741824) + "GB";
  }
  function verifyemail(email) {
    swal({  title: 'Are you sure to send verification mail?',
          type: "warning",   
          showCancelButton: true,   
          confirmButtonColor: "#DD6B55",   
          confirmButtonText: "Yes, Send it!",   
          closeOnConfirm: false }, 
          function(isConfirm){
            if (isConfirm) {   
              $.ajax({
                url: SITE_URL+"member/verifyemail",
                type: 'POST',
                data: {email:email},
                beforeSend: function(){
                  $('.mask').show();
                  $('#loader').show();
                },
                success: function(response){
                  if(response==1){
                    swal("Success!", "We will send verification link to your email address!", "success");
                  }else{
                    swal("Failed", "We can\'t send verification link to your email address !", "error");
                  }
                },
                error: function(xhr) {
                  swal("Failed", "We can\'t send verification link to your email address !", "error");
                },
                complete: function(){
                  $('.mask').hide();
                  $('#loader').hide();
                },
              });
            }
          });
  }

  function copyelementtext(element,copyText){

    $('#copycontent').html(copyText);
    var contentHolder = document.getElementById("copycontent");

    // We will need a range object and a selection.
    var range = document.createRange(),
    selection = window.getSelection();

    // Clear selection from any previous data.
    selection.removeAllRanges();

    // Make the range select the entire content of the contentHolder paragraph.
    range.selectNodeContents(contentHolder);

    // Add that range to the selection.
    selection.addRange(range);

    // Copy the selection to clipboard.
    document.execCommand('copy');

    // Clear selection if you want to.
    selection.removeAllRanges();
    $('#copycontent').html('');
    $('#'+element).attr('data-original-title', 'Copied').tooltip('show');
  }

  function resettooltiptitle(element,title) {
    $('#'+element).attr('data-original-title', title);
  }

  function loadpopover(align='right'){
    
    $('.popoverButton').popover({
        "html": true,
        trigger: 'manual',
        placement: align,
        "content": function () {
            return "";
        }
    });
  }

  function generateTabIndex(){
    $(function() {
      var tabindex = 1;
      $('input,select,textarea,a,button').each(function() {
          if (this.type != "hidden") {
            var $input = $(this);
            $input.attr("tabindex", tabindex);
            tabindex++;
          }
      });
    });
  }

  function sendtransactionpdf(transactionid,transactiontype=0,sendtype=0,transaction="sales") {

    var trns_label = 'order';
    if(transactiontype==1){
      trns_label = 'quotation';
    }else if(transactiontype==2){
      trns_label = 'invoice';
    }else if(transactiontype==3){
      trns_label = 'credit note';
    }
    var label = (sendtype==0)?"mail":"whatsapp";
    var label2 = (sendtype==0)?"mail address":"whatsapp";
    var receiver = "buyer";
    var uurl = SITE_URL+"order/sendtransactionpdf";
    if(transaction=="purchase"){
      uurl = SITE_URL+"purchase-order/sendtransactionpdf";
      receiver = "vendor";
    }
    swal({  
      title: 'Are you sure to send pdf on '+label+' ?',
      type: "warning",   
      showCancelButton: true,   
      confirmButtonColor: "#DD6B55",   
      confirmButtonText: "Yes, Send it!",   
      closeOnConfirm: false }, 
      function(isConfirm){
        if (isConfirm) {   
          $.ajax({
            url: uurl,
            type: 'POST',
            data: {transactionid:transactionid,transactiontype:transactiontype,sendtype:sendtype},
            beforeSend: function(){
              $('.mask').show();
              $('#loader').show();
            },
            success: function(response){
              if(response==1){
                swal("Success!", "We will send "+trns_label+" pdf on "+receiver+" "+label2+" !", "success");
              }else if(response==-1){
                swal("Failed", "Mobile number can not set as a whatsapp number !", "error");
              }else{
                swal("Failed", "We can\'t send "+trns_label+" pdf on "+receiver+" "+label2+" !", "error");
              }
            },
            error: function(xhr) {
              swal("Failed", "We can\'t send "+trns_label+" pdf on "+receiver+" "+label2+" !", "error");
            },
            complete: function(){
              $('.mask').hide();
              $('#loader').hide();
            },
          });
        }
      });
  }

  function printdocument(html){
  
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

  }