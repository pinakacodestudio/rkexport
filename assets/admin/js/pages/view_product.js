$(document).ready(function(){
  $('#productid').on('change', function (e) {
    
    filterproducts();
  });
  $('.filtersection input[type=checkbox]').on('change', function (e) {
    
    filterproducts();
  });
  filterproducts();
});
function printProductDetails(){

  var productid = $('#productid').val();
  var productname = $('#productname').is(":checked")?1:0;
  var sku = $('#sku').is(":checked")?1:0;
  var productprice = $('#productprice').is(":checked")?1:0;
  var variant = $('#variant').is(":checked")?1:0;

  if(productid!=null){
    var uurl = SITE_URL + "product/printProductDetails";
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {productid:productid,productname:productname,sku:sku,productprice:productprice,variant:variant},
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
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
}
 
function exportToPDFQRCode(){

  var productid = $('#productid').val();
  var productname = $('#productname').is(":checked")?1:0;
  var sku = $('#sku').is(":checked")?1:0;
  var productprice = $('#productprice').is(":checked")?1:0;
  var variant = $('#variant').is(":checked")?1:0;

  if(productid!=null){

    window.location= SITE_URL+"product/exportToPDFQRCode?productid="+productid+"&productname="+productname+"&sku="+sku+"&productprice="+productprice+"&variant="+variant;
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
}
function filterproducts(){

  var productid = $('#productid').val();
  var productname = $('#productname').is(":checked");
  var sku = $('#sku').is(":checked");
  var productprice = $('#productprice').is(":checked");
  var variant = $('#variant').is(":checked");

  if(productid!=null){
    var datastr = 'productid='+productid;
    var baseurl = SITE_URL+'product/getProductsByIDs';
    $.ajax({
        url: baseurl,
        type: 'POST',
        data: datastr,
        datatype:'json',
        beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
        },
        success: function(response){
            var obj = JSON.parse(response);
            
            $.html = '';
            if(!$.isEmptyObject(obj)){
              
              for(var i = 0; i < obj.length; i++) {
                if(obj[i]['isuniversal']==0){
                  var variantproduct = obj[i]['variant'];
                  
                  var displayproductname = '';
                  if(productname){
                    displayproductname = '<b>'+obj[i]['name']+'</b><br>';
                  }
                  
                  $.each( variantproduct, function( key, value ) {
                  
                    var id = obj[i]['id']+','+key;
                    if(variantproduct[key]['sku']!=""){
                      var qrtext = "SKU:"+variantproduct[key]['sku']+"|"+obj[i]['id']+"|"+key;
                      var src = GENERATE_QRCODE_SRC.replace(/{encodeurlstring}/g, qrtext); 
                    }else{
                      var src = DEFAULT_IMG_PATH+"qrcodenotavailable.jpg"; 
                    }
                    var displaysku = displayproductprice = displayvariant = '';
                    if(sku){
                      displaysku = variantproduct[key]['sku']!=''?'<b>SKU : </b>'+variantproduct[key]['sku']+'<br>':'';
                    }
                    if(productprice){
                      displayproductprice = '<div class="text-center"><b>'+CURRENCY_CODE+'</b> '+variantproduct[key]['price']+'</div>';
                    }
                    if(variant){
                      var variants_html = '';
                      var variants = variantproduct[key]['variants'];
                      for(var j = 0; j < variants.length; j++) {
                        variants_html += '<b>'+variants[j]['variantname']+' : </b>'+variants[j]['variantvalue']+'<br>';
                      }
                      displayvariant = variants_html;
                    }
                    $.html += '<div class="col-md-4 pl-xs pr-xs mb-sm">\
                                <div class="col-md-12 pl-xs pr-xs" style="border: 3px solid #e8e8e8;">\
                                  <div class="col-md-4 pull-left p-n">\
                                    <img style="width: 100%;" src="'+src+'" class="">\
                                    '+displayproductprice+'\
                                  </div>\
                                  <div class="col-md-8 p-n mt-md">\
                                    <p>'+displayproductname+displaysku+displayvariant+'</p>\
                                  </div>\
                                </div>\
                              </div>';
                  });
                }else{
                  if(obj[i]['sku']!=""){
                    var qrtext = "SKU:"+obj[i]['sku']+"|"+obj[i]['id']+"|"+obj[i]['priceid'];
                    var src = GENERATE_QRCODE_SRC.replace(/{encodeurlstring}/g, qrtext); 
                  }else{
                    var src = DEFAULT_IMG_PATH+"qrcodenotavailable.jpg"; 
                  }
                  var displayproductname = displaysku = displayproductprice = '';
                  if(productname){
                    displayproductname = '<b>'+obj[i]['name']+'</b><br>';
                  }
                  if(sku){
                    displaysku = obj[i]['sku']!=''?'<b>SKU : </b>'+obj[i]['sku']:'';
                  }
                  if(productprice){
                    displayproductprice = '<div class="text-center"><b>'+CURRENCY_CODE+'</b> '+obj[i]['price']+'</div>';
                  }
                  $.html += '<div class="col-md-4 pl-xs pr-xs mb-sm">\
                              <div class="col-md-12 pl-xs pr-xs" style="border: 3px solid #e8e8e8;">\
                                <div class="col-md-4 pull-left p-n">\
                                  <img style="width: 100%;" src="'+src+'" class="">\
                                  '+displayproductprice+'\
                                </div>\
                                <div class="col-md-8 p-n mt-md">\
                                  <p>'+displayproductname+displaysku+'</p>\
                                </div>\
                              </div>\
                            </div>';
                }
              }

            }else{
              
            }
            
            $('#productdetailsdiv').html($.html);
        },
        complete: function(){
          $('.mask').hide();
          $('#loader').hide();
        },
    });
  }else{
    $.html = '';
    $.html += '<div class="col-md-12 pl-xs pr-xs text-center" style="border: 3px solid #e8e8e8;">\
                  <h4>No data available.</h4>\
              </div>';
    $('#productdetailsdiv').html($.html);
  }
}