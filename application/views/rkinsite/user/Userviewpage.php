<script type="text/javascript">
	var profileimgpath = '<?php echo PROFILE; ?>';
	var defaultprofileimgpath = '<?php echo DEFAULT_PROFILE; ?>';
</script>
<ol class="breadcrumb">
  <li class="breadcrumb-item"><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
  <li class="breadcrumb-item"><a href="javascript:void(0)"><?=$this->session->userdata(base_url() . 'mainmenuname')?></a></li>
  <li class="breadcrumb-item"><a href="<?php echo base_url(); ?><?=$this->session->userdata(base_url() . 'submenuurl')?>"><?=$this->session->userdata(base_url() . 'submenuname')?></a></li>
  <li class="breadcrumb-item active">View <?=$this->session->userdata(base_url() . 'submenuname')?></li>
</ol>
<style>
.datepicker1 {
    text-align: left !important;
    border-radius: 3px !important;
}

.br2{
  border-right: 2px solid #d9d9c8;
}
</style>
<div class="page-content">
    <div class="page-heading">     
        <?php $this->load->view(ADMINFOLDER.'includes/menu_header');?>
    </div>

     
   <?php print_r($list); ?>
      <div class="container-fluid">
        <div data-widget-group="group1">
   
              <div class="panel panel-default border-panel">
                <div class="panel-heading">
                <div class="row">
                  <div class="col-md-12">
                    <div class="col-md-3">
                    Name
                    </div>
                    <div class="col-md-3 br2">
                    <?=$list['name']?>
                    </div>
                    <div class="col-md-3">
                    Mobil Number
                    </div>
                    <div class="col-md-3">
                    <?=$list['mobileno']?>
                    </div>
                </div>
              </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="col-md-3">
                    Gender
                    </div>
                    <div class="col-md-3 br2">
                    <?php if($list['cityid']!=1){ echo "Male"; }else{ echo "Female";} ?>
                    </div>
                    <div class="col-md-3">
                    Email Id
                    </div>
                    <div class="col-md-3">
                    <?=$list['email']?>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="col-md-3">
                    Designation
                    </div>
                    <div class="col-md-3 br2">
                   <?=$list['designationname']?>
                    </div>
                    <div class="col-md-3">
                    Address
                    </div>
                    <div class="col-md-3">
                    <?=$list['address']?>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="col-md-3">
                    Department
                    </div>
                    <div class="col-md-3 br2">
                    &nbsp;
                    </div>
                    <div class="col-md-3">
                    City
                    </div>
                    <div class="col-md-3">
                    <?=$list['cityname']?>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="col-md-3">
                    Username
                    </div>
                    <div class="col-md-3 br2">
                    &nbsp;
                    </div>
                    <div class="col-md-3">
                    State


                    </div>
                    <div class="col-md-3">
                    &nbsp;
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="col-md-3">
                    Password
                    </div>
                    <div class="col-md-3 br2">
                    &nbsp;
                    </div>
                    <div class="col-md-3">
                    Country

                    </div>
                    <div class="col-md-3">
                    &nbsp;
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="col-md-3">
                    Party Cord
                    </div>
                    <div class="col-md-3 br2">
                    <?=$list['partycord'] ?>
                    </div>
                    <div class="col-md-3">
                    Join Date

                    </div>
                    <div class="col-md-3">
                    &nbsp;
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="col-md-3">
                    Branch Name
                    </div>
                    <div class="col-md-3 br2">
                    &nbsp;
                    </div>
                    <div class="col-md-3">
                    Birth Date
                    </div>
                    <div class="col-md-3">
                    &nbsp;
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="col-md-3">&nbsp;
                    </div>
                    <div class="col-md-3 br2">&nbsp;
                    </div>
                    <div class="col-md-3">
                    Anniversary Date
                    </div>
                    <div class="col-md-3">
                    &nbsp;
                    </div>
                  </div>
                </div>
            </div>
          </div>
        </div>
      </div>
      </div>


     


     







<script language="javascript">
function viewtaskdetails(id){

      var uurl = SITE_URL+"task/gettaskdeatilbyid";
        $.ajax({
          url: uurl,
          type: 'POST',
          data: {id:String(id)},
          // dataType: 'json',
          async: false,
          success: function(response){
            var JSONObject = JSON.parse(response);

            var str = JSONObject['description'];
            str = str.replace(" ", "&nbsp;");
            $('#emailsubject').html(JSONObject['name']);
            $('#emailbody').html(str);
            $('#myModal').modal('show');
            $('.popoverButton').popover('hide');
          },
          error: function(xhr) {
          //alert(xhr.responseText);
          },
        });
    }


document.getElementById('box').onchange = function() {
    document.getElementById('todate').disabled = !this.checked;
};
document.getElementById('todatebox').onchange = function() {
    document.getElementById('todate1').disabled = !this.checked;
};

      $('#insert_form').on("submit", function(event){
           event.preventDefault();
           if($('#designationid').val() == 0 || $('#designationid').val() == ""){
                $("#designation_div").addClass("has-error is-focused");
                new PNotify({title: "Please enter designation",styling: 'fontawesome',delay: '3000',type: 'error'});

           }else if($('#fromdate').val() == ""){
                $("#fromdate_div").addClass("has-error is-focused");
                new PNotify({title: "Please enter from date",styling: 'fontawesome',delay: '3000',type: 'error'});

            }else{
                $.ajax({
                     url:"<?php echo site_url('rkinsite/designation/adddesignationhistory') ?>",
                     method:"POST",
                     data:$('#insert_form').serialize(),
                     beforeSend:function(){
                          $('#insert').val("Inserting");
                     },
                     success:function(data){
                          //alert(data);
                          $('#add_data_Modal').modal('hide');
                          if(data==1){
                            new PNotify({title: "Designation successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
                            setTimeout(function() { window.location=SITE_URL+"user/viewuser/"+<?php echo $userdata['eid']; ?>; }, 1500);
                        }else{
                            new PNotify({title: "Designation not added !",styling: 'fontawesome',delay: '3000',type: 'error'});
                        }
                     }
                });
            }
    });

    $(document).on('click', '.edit_data', function(){
           var did = $(this).attr("id");
           $.ajax({
                url:"<?php echo site_url('rkinsite/designation/getdesignationhistory') ?>",
                method:"POST",
                data:{did:did},
                dataType:"json",
                success:function(data){
                    $('#employeeid').val(data.employeeid);
                    $("#designationid").val(data.designationid).change();
                    $('#fromdate').val(data.fromdate);
                    $('#todate').val(data.todate);
                    $('#did').val(data.id);
                    $('#insert').val("Update");
                    $('.modal-title').html("Update Details");
                    $('#add_data_Modal').modal('show');
                }
           });
      });

    $('#insert_salary_form').on("submit", function(event){
           event.preventDefault();
           if($('#salary1').val() == 0 || $('#salary1').val() == ""){
                $("#salary_div").addClass("has-error is-focused");
                new PNotify({title: "Please enter salary amount",styling: 'fontawesome',delay: '3000',type: 'error'});

           }else if($('#fromdate1').val() == ""){
                $("#fromdate_div1").addClass("has-error is-focused");
                new PNotify({title: "Please enter from date",styling: 'fontawesome',delay: '3000',type: 'error'});

            }else{
                $.ajax({
                     url:"<?php echo site_url('rkinsite/user/addsalaryhistory') ?>",
                     method:"POST",
                     data:$('#insert_salary_form').serialize(),
                     beforeSend:function(){
                          $('#insertsalary').val("Inserting");
                     },
                     success:function(data){
                          //alert(data);
                          $('#add_salary_Modal').modal('hide');
                          if(data==1){
                            new PNotify({title: "Salary successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
                            setTimeout(function() { window.location=SITE_URL+"user/viewuser/"+<?php echo $userdata['eid']; ?>; }, 1500);
                        }else{
                            new PNotify({title: "Salary not added !",styling: 'fontawesome',delay: '3000',type: 'error'});
                        }
                     }
                });
            }
    });

    $(document).on('click', '.edit_salary', function(){
           var sid = $(this).attr("id");
           $.ajax({
                url:"<?php echo site_url('rkinsite/user/getsalaryhistory') ?>",
                method:"POST",
                data:{sid:sid},
                dataType:"json",
                success:function(response){
                    $('#eid').val(response.employeeid);
                    $('#salary1').val(response.salaryamount);
                    $('#fromdate1').val(response.fromdate);
                    $('#todate1').val(response.todate);
                    $('#sid').val(response.id);
                    $('#insertsalary').val("Update");
                    $('.modal-title').html("Update Details");
                    $('#add_salary_Modal').modal('show');
                }
           });
      });


</script>
