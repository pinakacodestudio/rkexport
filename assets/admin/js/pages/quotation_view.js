function changeinstallmentstatus(status, installmentid){
    var uurl = SITE_URL+"quotation/update-installment-status";
        if(installmentid!=''){
              swal({    title: "Are you sure to change status?",
                type: "warning",   
                showCancelButton: true,   
                confirmButtonColor: "#DD6B55",   
                confirmButtonText: "Yes, change it!",   
                closeOnConfirm: true }, 
                function(isConfirm){   
                  if (isConfirm) {   
                    $.ajax({
                        url: uurl,
                        type: 'POST',
                        data: {status:status,installmentid:installmentid},
                        
                        success: function(response){
                          if(response==1){
                            if(status==1){
                              $("#btndropdown"+installmentid).removeClass("btn-warning");
                              $("#btndropdown"+installmentid).addClass("btn-success");
                              $("#btndropdown"+installmentid).html("Paid");
                            }else{
                              $("#btndropdown"+installmentid).removeClass("btn-success");
                              $("#btndropdown"+installmentid).addClass("btn-warning");
                              $("#btndropdown"+installmentid).html("Pending <span class='caret'></span>");
                            }
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

function printquotationinvoice(id){

  var uurl = SITE_URL + "quotation/printQuotationInvoice";
  $.ajax({
      url: uurl,
      type: 'POST',
      data: {
          id:id
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
          //alert(xhr.responseText);
      },
      complete: function() {
          $('.mask').hide();
          $('#loader').hide();
      },
  });

}