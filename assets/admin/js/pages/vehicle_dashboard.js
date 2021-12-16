$(document).ready(function(){
    applyFilter();
    if(size>STORAGESPACE){
        getSizemodal();
    }
  });
  
  function getSizemodal(){
      //alert("dfjnod");
      swal({    title: "Storage Space limit reached 100%.",
      text: "Kindly contact our sales team for renewal",
      type: "warning",   
      showCancelButton: false,   
      confirmButtonColor: "#DD6B55",   
      confirmButtonText: "Ok",   
      closeOnConfirm: true });
  }
  
  $(document).ready(function() {
  });
  
  function applyFilter(){
    var days = $('#days').val();
    uurl = SITE_URL+'vehicle-dashboard/listing';
    $.ajax({
      url: uurl,
      type: 'POST',
      dataType: 'json',
      data: 'days='+days,
      // async: false,
      success: function (response) {
        var documentdata = response['documentdata'];
        var insurancedata = response['insurancedata'];
        var partsdata = response['partsdata'];
        var vehicleregistrationdata = response['vehicleregistrationdata'];
        var emidata = response['emidata'];
  
        documnettable = '';
        insurancetable = '';
        servicepartstable = '';
        vehicleregistrationtable = '';
        emiremindertable = '';
  
        if(vehicleregistrationdata.length>0){
          for(var i = 0; i < vehicleregistrationdata.length; i++) {
  
            var DueDate = vehicleregistrationdata[i]["duedateofregistration"];
            DueDate = new Date(DueDate);
            var dd = String(DueDate.getDate()).padStart(2, '0');
            var mm = String(DueDate.getMonth() + 1).padStart(2, '0'); //January is 0!
            var yyyy = DueDate.getFullYear();
            DueDate = dd+"/"+mm+"/"+yyyy;
            
            vehicleregistrationtable += '<tr>\
                              <td><a href="'+SITE_URL+'vehicle/view-vehicle/'+vehicleregistrationdata[i]['vehicleid']+'#vehicledetails" target="_balnk">'+vehicleregistrationdata[i]['vehiclename']+'</a></td>\
                              <td>'+vehicleregistrationdata[i]["vehicleno"]+'</td>\
                              <td>'+vehicleregistrationdata[i]["vehicletypename"]+'</td>\
                              <td><a href="'+SITE_URL+'party/view-party/'+vehicleregistrationdata[i]['partyid']+'#assignedvehicledetails" target="_balnk">'+vehicleregistrationdata[i]['partyname']+'</a></td>\
                              <td>'+vehicleregistrationdata[i]["ownercontactno"]+'</td>\
                              <td>'+DueDate+'</td>\
                              <td>'+vehicleregistrationdata[i]["days"]+'</td>\
                            </tr>';
          }
        }else{
          vehicleregistrationtable = '<tr><td colspan="7" class="text-center">No records found</td></tr>';
        }
  
        if(documentdata.length>0){
          for(var i = 0; i < documentdata.length; i++) {
  
            var DueDate = documentdata[i]["duedate"];
            DueDate = new Date(DueDate);
            var dd = String(DueDate.getDate()).padStart(2, '0');
            var mm = String(DueDate.getMonth() + 1).padStart(2, '0'); //January is 0!
            var yyyy = DueDate.getFullYear();
            DueDate = dd+"/"+mm+"/"+yyyy;
  
            documnettable += '<tr>\
                              <td><a href="'+SITE_URL+'vehicle/view-vehicle/'+documentdata[i]['vehicleid']+'#documenttab" target="_balnk">'+documentdata[i]['vehiclename']+'</a></td>\
                              <td><a href="'+SITE_URL+'party/view-party/'+documentdata[i]['partyid']+'#documentdetails" target="_balnk">'+documentdata[i]['partyname']+'</a></td>\
                              <td>'+documentdata[i]["documentnumber"]+'</td>\
                              <td>'+documentdata[i]["documenttype"]+'</td>\
                              <td>'+DueDate+'</td>\
                              <td>'+documentdata[i]["days"]+'</td>\
                            </tr>';
          }
        }else{
          documnettable = '<tr><td colspan="6" class="text-center">No records found</td></tr>';
        }
  
        if(insurancedata.length>0){
          for(var i = 0; i < insurancedata.length; i++) {
  
              var DueDate = insurancedata[i]["todate"];
              DueDate = new Date(DueDate);
              var dd = String(DueDate.getDate()).padStart(2, '0');
              var mm = String(DueDate.getMonth() + 1).padStart(2, '0'); //January is 0!
              var yyyy = DueDate.getFullYear();
              DueDate = dd+"/"+mm+"/"+yyyy;
              
            insurancetable += '<tr>\
                              <td><a href="'+SITE_URL+'vehicle/view-vehicle/'+insurancedata[i]['vehicleid']+'#insurancetab" target="_balnk">'+insurancedata[i]['vehiclename']+'</a></td>\
                              <td>'+insurancedata[i]["companyname"]+'</td>\
                              <td>'+insurancedata[i]["policyno"]+'</td>\
                              <td>'+DueDate+'</td>\
                              <td class="text-right">'+format.format(parseFloat(insurancedata[i]["amount"]).toFixed(2))+'</td>\
                              <td>'+insurancedata[i]["days"]+'</td>\
                            </tr>';
          }
        }else{
          insurancetable = '<tr><td colspan="6" class="text-center">No records found</td></tr>';
        }
  
        if(partsdata.length>0){
          for(var i = 0; i < partsdata.length; i++) {
  
              var DueDate = partsdata[i]["duedate"];
              DueDate = new Date(DueDate);
              var dd = String(DueDate.getDate()).padStart(2, '0');
              var mm = String(DueDate.getMonth() + 1).padStart(2, '0'); //January is 0!
              var yyyy = DueDate.getFullYear();
              DueDate = dd+"/"+mm+"/"+yyyy;
  
              var WarrantyDate = partsdata[i]["warrantyenddate"];
              WarrantyDate = new Date(WarrantyDate);
              var dd = String(WarrantyDate.getDate()).padStart(2, '0');
              var mm = String(WarrantyDate.getMonth() + 1).padStart(2, '0'); //January is 0!
              var yyyy = WarrantyDate.getFullYear();
              WarrantyDate = dd+"/"+mm+"/"+yyyy;
  
            servicepartstable += '<tr>\
                                  <td><a href="'+SITE_URL+'vehicle/view-vehicle/'+partsdata[i]['vehicleid']+'#servicetab" target="_balnk">'+partsdata[i]['vehiclename']+'</a></td>\
                                  <td>'+partsdata[i]["partname"]+'</td>\
                                  <td>'+partsdata[i]["serialnumber"]+'</td>\
                                  <td>'+WarrantyDate+'</td>\
                                  <td>'+DueDate+'</td>\
                                  <td class="text-right">'+format.format(parseFloat(partsdata[i]["amount"]).toFixed(2))+'</td>\
                                  <td>'+partsdata[i]["days"]+'</td>\
                                </tr>';
          }
        }else{
          servicepartstable = '<tr><td colspan="7" class="text-center">No records found</td></tr>';
        }
  
        if(emidata.length>0){
          for(var i = 0; i < emidata.length; i++) {
  
            var DueDate = emidata[i]["installmentdate"];
              DueDate = new Date(DueDate);
              var dd = String(DueDate.getDate()).padStart(2, '0');
              var mm = String(DueDate.getMonth() + 1).padStart(2, '0'); //January is 0!
              var yyyy = DueDate.getFullYear();
              DueDate = dd+"/"+mm+"/"+yyyy;
  
            emiremindertable += '<tr>\
                              <td><a href="'+SITE_URL+'vehicle/view-vehicle/'+emidata[i]['vehicleid']+'#emiremindertab" target="_balnk">'+emidata[i]['vehiclename']+'</a></td>\
                              <td class="text-right">'+emidata[i]["installmentamount"]+'</td>\
                              <td>'+DueDate+'</td>\
                              <td>'+emidata[i]["days"]+'</td>\
                            </tr>';
          }
        }else{
          emiremindertable = '<tr><td colspan="4" class="text-center">No records found</td></tr>';
        }
  
          $('#documenttable tbody').html(documnettable);
          $('#insurancetable tbody').html(insurancetable);
          $('#vehicleregistrationtable tbody').html(vehicleregistrationtable);
          $('#servicepartstable tbody').html(servicepartstable);
          $('#emiremindertable tbody').html(emiremindertable);
      },
      error: function (xhr) {
      // alert(xhr.responseText);
      },
  });
  }
  
  function printvehicleregistration(){
    var days = $('#days').val();
  
  var totalRecords =$("#vehicleregistrationtable tbody tr td").html();
  $.skylo('end');
  if(totalRecords != 'No records found'){
      var uurl = SITE_URL + "vehicle-dashboard/printexpiredvehicleregistration";
      $.ajax({
          url: uurl,
          type: 'POST',
          data: {
            days:days
          },
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
              // alert(xhr.responseText);
          },
          complete: function() {
              $('.mask').hide();
              $('#loader').hide();
          },
      });
    }
    else{
        new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }
  }
  function printexpireinsurance(){
    var days = $('#days').val();
  
  var totalRecords =$("#insurancetable tbody tr td").html();
  $.skylo('end');
  if(totalRecords != 'No records found'){
      var uurl = SITE_URL + "vehicle-dashboard/printexpiredinsurance";
      $.ajax({
          url: uurl,
          type: 'POST',
          data: {
            days:days
          },
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
              // alert(xhr.responseText);
          },
          complete: function() {
              $('.mask').hide();
              $('#loader').hide();
          },
      });
    }
    else{
        new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }
  }
  function printexpireddocument(){
    var days = $('#days').val();
  
  var totalRecords =$("#documenttable tbody tr td").html();
  $.skylo('end');
  if(totalRecords != 'No records found'){
      var uurl = SITE_URL + "vehicle-dashboard/printexpireddocument";
      $.ajax({
          url: uurl,
          type: 'POST',
          data: {
            days:days
          },
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
              // alert(xhr.responseText);
          },
          complete: function() {
              $('.mask').hide();
              $('#loader').hide();
          },
      });
    }
    else{
        new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }
  }
  function printservicepartdata(){
    var days = $('#days').val();
  
  var totalRecords =$("#servicepartstable tbody tr td").html();
  $.skylo('end');
  if(totalRecords != 'No records found'){
      var uurl = SITE_URL + "vehicle-dashboard/printservicepartdata";
      $.ajax({
          url: uurl,
          type: 'POST',
          data: {
            days:days
          },
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
              // alert(xhr.responseText);
          },
          complete: function() {
              $('.mask').hide();
              $('#loader').hide();
          },
      });
    }
    else{
        new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }
  }
  function printservicepartalertdata(){
    var days = $('#days').val();
  
  var totalRecords =$("#servicepartalerttable tbody tr td").html();
  $.skylo('end');
  if(totalRecords != 'No records found'){
      var uurl = SITE_URL + "vehicle-dashboard/printservicepartalertdata";
      $.ajax({
          url: uurl,
          type: 'POST',
          data: {
            days:days
          },
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
              // alert(xhr.responseText);
          },
          complete: function() {
              $('.mask').hide();
              $('#loader').hide();
          },
      });
    }
    else{
        new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }
  }
  function printemireminderdata(){
    var days = $('#days').val();
  
  var totalRecords =$("#emiremindertable tbody tr td").html();
  $.skylo('end');
  if(totalRecords != 'No records found'){
      var uurl = SITE_URL + "vehicle-dashboard/printemireminderdata";
      $.ajax({
          url: uurl,
          type: 'POST',
          data: {
            days:days
          },
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
              // alert(xhr.responseText);
          },
          complete: function() {
              $('.mask').hide();
              $('#loader').hide();
          },
      });
    }
    else{
        new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }
  }