
<div class="col-md-12">
    <div class="panel panel-default border-panel quotationfiles" style="padding-top: 15px;">      
      <div class="l-box l-spaced-bottom">
        <div class="l-box-body l-spaced">
          <div class="col-md-6 pr-xs mb16">
              <p class="col-md-4"><b>Company Name</b>	</p>
              <p class="col-md-1"><b>:</b></p>
              <p class="col-md-5">Rk</p>
              
            </div>
            <div class="col-md-6 pr-xs mb16">
              <p class="col-md-4"><b>Customer Name</b></p>
              <p class="col-md-1"><b>:</b></p>
              <p class="col-md-5">	Rk</p>
              
            </div>
        
          <div class="col-md-6 pr-xs mb16">
              <p class="col-md-4"><b>Mobile</b>	</p>
              <p class="col-md-1"><b>:</b></p>
              <p class="col-md-5">	+919999999998</p>
              
            </div>
          <div class="col-md-6 pr-xs mb16">
             <p class="col-md-4"><b>Email</b>	</p>
             <p class="col-md-1"><b>:</b></p>
             <p class="col-md-5"><a href = "mailto: mitul@rkinfoyechindia.com">mitul@rkinfoyechindia.com</a></p>
            
          </div>
        
          
          <div class="col-md-12 pr-xs">
               
               <div class="panel panel-default mb-n">
                   <div class="panel-heading">
                       <div class="col-md-6 p-n">
                             <div class="panel-ctrls quotationfilestabel "></div>
                       </div>
                       
                   </div>
                   <div class="panel-body no-padding">
                         <table class="table table-striped table-bordered" id="quotationfilestabel">
                              <thead>
                                 <tr>
                                     <th>Sr. No.</th>
                                      <th>Description</th>
                                      <th>File</th>
                                      <th>Date</th>
                                      <th>Entry Date</th>
                                      <th>Added By</th>
                                       <th>Action</th>
                                   </tr>
                                </thead>
                                 <tbody>
                                   <tr>
                                   <td>1</td>
                                      <td>test</td>
                                      <td>-</td>
                                      <td>-</td>
                                      <td>- </td>
                                      <td>Vimal</td>
                                       <td></td>
                                  </tr>
                                 </tbody>
                          </table>
                   </div>
                   <div class="panel-footer quotationfilestabel "></div>
              </div>
         </div>
       </div>                
      </div>
    </div>
</div>   
<script>
  




$(document).ready(function() {
    quotationfilestabel=$('#quotationfilestabel').DataTable({
        "language": {
            "lengthMenu": "_MENU_"
        },
        "columnDefs": [ {
          "targets": [-1,-2,-3,-4],
          "orderable": false
        } ],
        responsive: true,
    });
    $('.dataTables_filter input').attr('placeholder','Search...');


    //DOM Manipulation to move datatable elements integrate to panel
    $('.panel-ctrls.quotationfilestabel').append($('.dataTables_filter').addClass("pull-left")).find("label").addClass("panel-ctrls");
    $('.panel-ctrls.quotationfilestabel').append("<i class='separator'></i>");
    $('.panel-ctrls.quotationfilestabel').append($('.dataTables_length').addClass("pull-right ")).find("label").addClass("panel-ctrls");

    $('.panel-footer.transfertabel').append($(".dataTable+.row"));
    $('.dataTables_paginate>ul.pagination').addClass("pull-right pagination-md");
});
</script>