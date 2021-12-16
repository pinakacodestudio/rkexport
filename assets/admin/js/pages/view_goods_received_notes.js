function printGoodsReceivedNotes(id){

    var uurl = SITE_URL + "goods-received-notes/printGoodsReceivedNotes";
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
        
            printdocument(html);
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