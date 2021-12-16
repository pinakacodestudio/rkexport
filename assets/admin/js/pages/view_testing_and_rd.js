function printTesting(id){

  var uurl = SITE_URL + "testing-and-rd/printTestingandrdDetails";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {id:id},
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
