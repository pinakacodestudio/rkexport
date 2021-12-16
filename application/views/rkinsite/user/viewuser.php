<script type="text/javascript">
	var profileimgpath = '<?php echo PROFILE;?>';
	var defaultprofileimgpath = '<?php echo DEFAULT_PROFILE;?>';
</script>
<ol class="breadcrumb">                        
  <li class="breadcrumb-item"><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
  <li class="breadcrumb-item"><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
  <li class="breadcrumb-item"><a href="<?php echo base_url(); ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
  <li class="breadcrumb-item active">View <?=$this->session->userdata(base_url().'submenuname')?></li>
</ol> 
<style>
.datepicker1 {
    text-align: left !important;
    border-radius: 3px !important;
}
</style>
<div class="container">
  <div class="animated fadeIn"> 
    <div class="row">
      <div class="col-md-2">
      <?php 
                          if($userdata['image']==''){
                            
                              $profileimg = '<img src="'.DEFAULT_PROFILE.'Male-Avatar.png" class="thumbwidth img-circle" style="width: 100px;height: 100px">';
                          }else{
                            $profileimg = '<img src="'.PROFILE.$userdata['image'].'" class="thumbwidth img-circle" style="width: 100px;height: 100px">';  
                          }
                          echo $profileimg;
                        ?>
      </div>
      <div class="col-md-10">
        <h5><b><?php echo ucfirst($userdata['ename']); ?></b></h5>
        <h6><b><?php echo ucfirst($userdata['technology'])." - ".ucfirst($userdata['desname']); ?></b></h6>
        <h6><b><?php echo $userdata['email']; ?></b></h6>
      </div>
    </div>
    </br>
 
    <ul class="nav nav-tabs border-panel" role="tablist">
      <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#home" role="tab" aria-controls="home">Employee Detail</a>
      </li>
      <li class="nav-item">
          <a class="nav-link" data-toggle="tab" href="#leave" role="tab" aria-controls="leave">Leave</a>
      </li>
      <li class="nav-item">
          <a class="nav-link" data-toggle="tab" href="#expense" role="tab" aria-controls="expense">Expense</a>
      </li>
      <li class="nav-item">
          <a class="nav-link" data-toggle="tab" href="#designation" role="tab" aria-controls="designation">Designation History</a>
      </li>
      <li class="nav-item">
          <a class="nav-link" data-toggle="tab" href="#salary" role="tab" aria-controls="salary">Salary History</a>
      </li>
      <li class="nav-item">
          <a class="nav-link" data-toggle="tab" href="#task" role="tab" aria-controls="task">Task</a>
      </li>                  
      <li class="nav-item">
          <a class="nav-link" data-toggle="tab" href="#bankdetails" role="tab" aria-controls="bankdetails">Bank Details</a>
      </li>                  
    </ul>
    
    <div class="tab-content">
      <div class="tab-pane active" id="home" role="tabpanel">
        <div class="row">
          <div class="col-md-6">
            <table class="table table-striped table-responsive-sm" width="100%">              
              <tr>
                <th>Employee Name</th>
                <td><?php echo ucfirst($userdata['ename']); ?></td>
              </tr>
              <tr>
                <th>Email</th>
                <td><?php echo '<a class="a-without-link" href="mailto:'.$userdata['email'].'">'.$userdata['email'].'</a>'; ?></td>
              </tr>              
              <tr>
                <th>Mobile No</th>
                <td><?php echo '<a class="a-without-link" href="https://api.whatsapp.com/send?phone='.$userdata['code'].$userdata['mobileno'].'&text=hi '.$userdata['ename'].'" target="_blank">'.$userdata['countrycode'].$userdata['mobileno'].'</a>'; ?></td>
              </tr>
              <tr>
                <th>Address</th>
                <td><?php if(!empty($userdata['address'])){
                            echo ucfirst($userdata['address']);
                          }else{                            
                            echo ucfirst($userdata['cname'])." (".ucfirst($userdata['sname']).",".ucfirst($userdata['coname']).")";
                          }
                    ?>
                </td>                
              </tr>
              <tr>
                <th>Location</th>
                <td><?php                          
                      echo ucfirst($userdata['cname'])." (".ucfirst($userdata['sname']).",".ucfirst($userdata['coname']).")";
                    ?>
                </td>
              </tr>
              <tr>
                <th>Zone</th>
                <td><?php echo ucfirst($userdata['zonename']);?></td>
              </tr>
              <tr>
                <th>Reporting To</th>
                <td><?php 
                    $reportingtodata=$this->User->getUserDataByID($userdata['reportingto']);
                    if($reportingtodata!=0)
                    { 
                        echo ucfirst($reportingtodata['name']);
                    }
                    ?>
                </td>
              </tr>
              <tr>
                <th>Status</th>
                <td>
                    <?php 
            			if($userdata['status']==1){
            				echo "<span class='badge badge-success'>Active</span>";
            			}else{
            				echo "<span class='badge badge-danger'>Inactive</span>";
                        }
                    ?>
                </td>
              </tr> 
              <?php  
                if(isset($userdata['secondarymobileno']) && $userdata['secondarymobileno']  != ''){   			  
                  echo "<tr><th>Secondary Mobile</th><td>".$userdata['secondarymobileno']."</td></tr>";
                }
              ?>
              <tr><th>Personal Email</th><td><?=$userdata['personalemail']?></td></tr>   			  
            </table>
          </div>
          <div class="col-md-6">
            <table class="table table-striped table-responsive-sm" width="100%">
            <tr>
                <th>Role</th>
                <td><?php echo ucfirst($userdata['profileid']); ?></td>
              </tr>
              <tr>
                <th>Department</th>
                <td><?php echo ucfirst($userdata['depname']); ?></td>
              </tr>
              <tr>
                <th>Designation</th>
                <td><?php echo ucfirst($userdata['desname']); ?></td>
              </tr>
              <tr>
                <th>Qualification</th>
                <td><?php echo ucfirst($userdata['qualification']); ?></td>
              </tr>
              <tr>
                <th>Technology</th>
                <td><?php echo ucfirst($userdata['technology']); ?></td>
              </tr>
              <tr>
                <th>Employee Code</th>
                <td><?php echo $userdata['code']; ?></td>
              </tr>
              <tr>
                <th>Joining Date</th>
                <td><?php if($userdata['joindate'] != "0000-00-00"){echo $this->general_model->displaydate($userdata['joindate']); }?></td>
              </tr>
              <tr>
                <th>Checkin Time</th>
                <td><?php echo $userdata['checkintime']." AM"; ?></td>
              </tr>
              <tr>
                <th>Checkout Time</th>
                <td><?php echo $userdata['checkouttime']." PM"; ?></td>
              </tr>
               <tr>
                <th>Birth Date</th>
                <td><?php if($userdata['birtdate'] == 0){echo $this->general_model->displaydate($userdata['birthdate']);}  ?></td>
              </tr>
              <tr>
                <th>Marital Status</th>
                <td><?php if($userdata['maritalstatus'] == 0){echo "Single";}elseif($userdata['maritalstatus'] == 1){echo "Married";}else{echo "Commited";}  ?></td>
              </tr>
              <?php if($userdata['maritalstatus'] == 1){ ?>
                <tr>
                  <th>Anniversary Date</th>
                  <td><?php if(isset($userdata['anniversarydate'])){echo $userdata['anniversarydate'];} ?></td>
                </tr>
              <?php } ?>
                  
            </table>
          </div>
        </div>
      </div>

      <div class="tab-pane" id="leave" role="tabpanel">
        <h3>Leave List</h3><br>
        <div class="table-responsive">
          <table id="leavetbl" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th width="1%">No.</th>
                <th>From Date</th>
                <th>To Date</th>
                <th>No. of Days</th>
                <th>Remarks</th>                          
              </tr>
            </thead>
            <tbody>
              <?php $i=0;
                foreach($leavedata as $ld){
                    $diff = "";
                    if($ld['leavetype'] == 1){

        				if ($ld['leavetype'] == 1 && $ld['fromdate'] != "0000-00-00" && $ld['todate'] != "0000-00-00") {
        					if ($ld['fromdate'] != $ld['todate']) {
        						$date1 = strtotime($ld['fromdate']);
        						$date2 = strtotime($ld['todate']);
        						$datediff = $date2 - $date1;
        						$diff = floor($datediff/(60*60*24));
        						$diff = $diff." "."days";
        					} else {
        						$diff = "1 days";
        					}
        				}
        			} else {
        				$diff = $ld['halfleave']." "."half";
        			}
              ?>
              <tr>
                <td width="1%"><?php echo ++$i;?></td>
                <td><?php echo $this->general_model->displaydate($ld['fromdate']);?></td>
                <td><?php if($ld['todate'] != "0000-00-00"){
                echo $this->general_model->displaydate($ld['todate']);
                }else{
                    echo $this->general_model->displaydate($ld['fromdate']);
                }
                ?></td> 
                <td><?php echo $diff; ?></td>
                <td><?php echo $ld['remarks'];?></td>                
              </tr>
              <?php
                }
              ?>
            </tbody>
          </table>
        </div>                           
      </div>

      <div class="tab-pane" id="expense" role="tabpanel">
        <h3>Expense List</h3><br>
        <div class="table-responsive">
          <table id="expensetbl" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th width="1%">No.</th>
                <th>Date</th>
                <th>Expense</th>
                <th>Amount</th>                           
                <th>Receipt</th> 
                <th>Action</th>                                                      
              </tr>
            </thead>
            <tbody>
              <?php $i=0;
                foreach($expensedata as $ed){                              
              ?>
              <tr>
                <td width="1%"><?php echo ++$i;?></td>
                <td><?php echo $this->general_model->displaydate($ed['date']);?></td>
                <td><?php echo $ed['expensename'];?></td> 
                <td><?php echo $ed['amount'];?></td>
                <td> <a class="a-without-link" href="<?php echo RECEIPT.$ed['receipt']; ?>" download="<?php echo $ed['receipt'];?>">Invoice</a> </td>
                <td><?php if(strpos($submenuvisibility['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                        <a class="<?=edit_class;?>" href="<?=ADMIN_URL?>expense/expenseedit/<?php echo $ed['id']; ?>"><?=edit_text;?></a>
                    <?php } ?>
                    <?php if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>                              
                        <a class="<?=delete_class?>" href="javascript:void(0)"
                                              onclick=deleterow("<?=$ed['id']?>","<?=ADMIN_URL?>expense/checkexpenseuse","expense","<?=ADMIN_URL?>expense/deletemulexpense")><?=delete_text?></a>
                    <?php } ?>

                </td>               
              </tr>
              <?php
                }
              ?>
            </tbody>
          </table>
        </div> 
      </div>

      <div class="tab-pane" id="designation" role="tabpanel">
        <div class="pull-right">
          <?php 
              if (strpos($submenuvisibility['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
            ?>
            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#add_data_Modal" type="button"><i class="fa fa-plus"></i> ADD</button>
            <?php
            }
            ?>
          </div>   

        <h3>Designation Details</h3><br>
        <div class="table-responsive">
          <table id="designationtbl" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th width="1%">No.</th>
                <th>Designation</th> 
                <th>From Date</th>                                                     
                <th>To Date</th>  
                <th>Action</th>                             
              </tr>
            </thead>
            <tbody> 
            <?php $i=0;
                foreach($designationdata as $dd){                              
              ?>
              <tr>
                <td width="1%"><?php echo ++$i;?></td>
                <td><?php echo $dd['designation'];?></td>
                <td><?php echo $this->general_model->displaydate($dd['fromdate']);?></td>                            
                <td><?php if($dd['todate'] == "0000-00-00"){
                        echo "Present";
                    }else{
                        echo $this->general_model->displaydate($dd['todate']);
                    }?></td>
                <td>
                    <?php if(strpos($submenuvisibility['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ 
                            if($dd['todate'] == "0000-00-00"){
                    ?>
                        <a class="<?=edit_class;?> edit_data text-white" id="<?php echo $dd["dhid"]; ?>"><?=edit_text;?></a>
                    <?php } }?>
                    <?php  if($dd['todate'] == "0000-00-00"){
                        if ($dd['status']==1) { ?>
                        <span id="span<?=$dd['dhid']; ?>">
                              <a href="javascript:void(0)" onclick="enabledisable(0,<?=$dd['dhid']; ?>,'<?=ADMIN_URL; ?>designation/designationhistoryenabledisable','<?=disable_title?>','<?=disable_class?>','<?=enable_class?>','<?=disable_title?>','<?=enable_title?>','<?=disable_text?>','<?=enable_text?>')" class="<?=disable_class?>"><?=stripslashes(disable_text)?></a></span>
                    <?php } else { ?>
                        <span id="span<?=$dd['dhid']; ?>">
                              <a href="javascript:void(0)" onclick="enabledisable(1,<?=$dd['dhid']; ?>,'<?=ADMIN_URL; ?>designation/designationhistoryenabledisable','<?=enable_title?>','<?=disable_class?>','<?=enable_class?>','<?=disable_title?>','<?=enable_title?>','<?=disable_text?>','<?=enable_text?>')" class="<?=enable_class?>"><?=stripslashes(enable_text)?></a></span>
                    <?php }
                    } ?>

                </td>               
              </tr>
              <?php
                }
              ?>         
            </tbody>
          </table>
        </div>             
      </div>

      <div class="tab-pane" id="salary" role="tabpanel">
        <div class="pull-right">
        <?php 
            if (strpos($submenuvisibility['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
          ?>
           <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#add_salary_Modal" type="button"><i class="fa fa-plus"></i> ADD</button>
          <?php
          }
          ?>
        </div>   

        <h3>Salary History</h3><br>
        <div class="table-responsive">
          <table id="salarytbl" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th width="1%">No.</th>
                <th>Amount</th>
                <th>From date</th>
                <th>To Date</th>                           
                <th>Status</th>                                      
              </tr>
            </thead>
            <tbody>
              <?php $i=0;
                foreach($salarydata as $sd){                              
              ?>
              <tr>
                <td width="1%"><?php echo ++$i;?></td>
                <td><?php echo $sd['salaryamount'];?></td>       
                <td><?php echo $this->general_model->displaydate($sd['fromdate']);?></td>
                <td><?php if($sd['todate'] == "0000-00-00"){
                        echo "Present";
                    }else{
                        echo $this->general_model->displaydate($sd['todate']);
                    }?></td> 
                <td> <?php if(strpos($submenuvisibility['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ 
                            if($sd['todate'] == "0000-00-00"){
                    ?>
                        <a class="<?=edit_class;?> edit_salary text-white" id="<?php echo $sd["shid"]; ?>"><?=edit_text;?></a>
                    <?php } }?>
                    <?php  if($sd['todate'] == "0000-00-00"){
                        if ($sd['status']==1) { ?>
                        <span id="span<?=$sd['shid']; ?>">
                              <a href="javascript:void(0)" onclick="enabledisable(0,<?=$sd['shid']; ?>,'<?=ADMIN_URL; ?>user/salaryhistoryenabledisable','<?=disable_title?>','<?=disable_class?>','<?=enable_class?>','<?=disable_title?>','<?=enable_title?>','<?=disable_text?>','<?=enable_text?>')" class="<?=disable_class?>"><?=stripslashes(disable_text)?></a></span>
                    <?php } else { ?>
                        <span id="span<?=$sd['shid']; ?>">
                              <a href="javascript:void(0)" onclick="enabledisable(1,<?=$sd['shid']; ?>,'<?=ADMIN_URL; ?>user/salaryhistoryenabledisable','<?=enable_title?>','<?=disable_class?>','<?=enable_class?>','<?=disable_title?>','<?=enable_title?>','<?=disable_text?>','<?=enable_text?>')" class="<?=enable_class?>"><?=stripslashes(enable_text)?></a></span>
                    <?php }
                    } ?></td>              
              </tr>
              <?php
                }
              ?>
            </tbody>
          </table>
        </div> 
      </div>

      <div class="tab-pane" id="task" role="tabpanel"> 
      <div class="pull-right">
                        <?php  if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){      ?>
                        &nbsp;<a class="<?=deletebtn_class;?> delete-btn-align" href="javascript:void(0)"
                            onclick="checkmultipledelete('<?php echo ADMIN_URL; ?>task/checktaskuse','Task','<?php echo ADMIN_URL; ?>task/deletemultask')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                        <?php } ?>
                    </div> 
                    <div class="pull-right">
                       <?php   if (strpos($submenuvisibility['submenuadd'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){?>
                              <a class="<?=addbtn_class;?> pull-right" href="<?php echo ADMIN_URL; ?>task/taskadd/<?=$projectdata['id'];?>" target="_blank" title=<?=addbtn_title?>><?=addbtn_text;?></a>
                              <?php } ?>
                    </div>
                     <h3>Task Details</h3></br>
                     <div class="table-responsive">
          <table id="tasktbl" class="table table-striped table-bordered table-responsive-sm" cellspacing="0"
                width="100%">
                <thead>
                    <tr>
                        <th width="1%">No.</th>
                        <th width="15%">Date</th> 
                        <th width="30%">Title</th>                    
                        <th>Estimated Date</th>
                        <th>Estimated Time</th>
                        <th>Priority</th>
                        <th>Task</th>
                        <th width="60%">Task Time</th>                       
                        <th>Employee</th>                                   
                        <th width="10%">Action</th>
                        <?php  if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){      ?>
                         <th width="5%">
                            <div class="checkbox table-checkbox">
                                <input id="deletecheckall" onchange="allchecked()" type="checkbox" value="all">
                                <label for="deletecheckall"></label>
                            </div>
                        </th> 
                            <?php } ?>
                    </tr>
                </thead>
                <tbody>
                <?php if(!empty($taskdata)){
                $i=0;

                $taskids = array();
                $taskids = array_column(json_decode(json_encode($taskdata), true),'taskid');
                
                $transferhistoryarr=array();
                if(count($taskids)>0){
                  $this->db->select("(select name from ".tbl_user." where id=transferfrom)as transferfromemployee,(select name from ".tbl_user." where id=transferto)as transfertoemployee,taskid,DATE(createdate)as date");
                  $this->db->from(tbl_tasktransferhistory." as its");
                  $this->db->where(array("taskid in(".implode(",",$taskids).")"=>null,"transferto!="=>0));
                  $this->db->where("its.transferfrom!=its.transferto");
                  $this->db->order_by("taskid asc,id asc",null);
                  $query = $this->db->get();
                  $tasktransferhistory = $query->result_array();
                  $i=1;
                  foreach($tasktransferhistory as $k=>$ith){
                    if(isset($transferhistoryarr[$ith['taskid']])){
                      $transferhistoryarr[$ith['taskid']]= $transferhistoryarr[$ith['taskid']]."<br>".(++$i).") ".$this->general_model->displaydate($ith['date'])." - ".$ith['transfertoemployee'];

                    }else{
                      $i=1;
                      $transferhistoryarr[$ith['taskid']]=($i).") ".$this->general_model->displaydate($ith['date'])." - ".$ith['transferfromemployee']."<br>".(++$i).") ".$this->general_model->displaydate($ith['date'])." - ".$ith['transfertoemployee'];
                    }
                  }
                }
               
                foreach($taskdata as $td){  
                  $PanelName=array();	
                  if($td['panelid']!=""){
                      $pid = explode(',',$td['panelid']);
                      for($p=0;$p<count($pid);$p++){
                          $panid=  $pid[$p];
                          $this->db->select("name");
                          $this->db->from(tbl_panel);
                          $this->db->where("id = '".$panid."'");
                          $result = $this->db->get()->row_array();
                          $PanelName[]=$result['name'];
                      }
                  }

                  $ModuleName=array();	
                  if($td['moduleid']!=""){
                      $mid = explode(',',$td['moduleid']);
                      for($m=0;$m<count($mid);$m++){
                          $modid=  $mid[$m];
                          $this->db->select("name");
                          $this->db->from(tbl_module);
                          $this->db->where("id = '".$modid."'");
                          $result = $this->db->get()->row_array();
                          $ModuleName[]=$result['name'];
                      }
                  }  
                  
                  $content = '';  
                           
                  if(!empty($td['technology'])){
                    $content .= '<b>Technology : </b>'.$td['technology'].'<br>';
                  }
                  if(!empty($td['panelid'])){
                    $content .= '<b>Panel : </b>'.implode(',', $PanelName).'<br>';
                  }
                  if(!empty($td['moduleid'])){
                    $content .= '<b>Module : </b>'.implode(',', $ModuleName).'<br>';
                  }
                  if(!empty($td['type'])){
                    $content .= '<b>Type : </b>'.$td['type'].'<br>';
                  }
                  if(!empty($td['redevelopmentcount'])){
                    $content = '<b>Redevelopment Count : </b>'.$td['redevelopmentcount'].'<br>';
                  }else{
                    $content = '<b>Redevelopment Count : </b> 0 <br>';
                  }
                  if(!empty($td['addedby'])){
                    $content .= '<b>Added By : </b>'.$td['addedby'].'<br>';
                  }
                  $content = htmlspecialchars($content);

                  $buttons="";
            
                  if ($td['statusname'] == "Open" || $td['statusname'] == "Restart") {
                    $buttons .=  '<button type="button" style="margin-right: 30px;text-transform: uppercase;
                    color: #ffffff !important;background-color: '.$td['taskcolor'].';border-color: '.$td['taskcolor'].';" class="btn btn-sm pull-right" id="start'.$td['taskid'].'" onclick="starttask('.$td['taskid'].','.$td['employeeid'].',\''.$td['statusname'].'\',2)">Start</button>';
                  }else if($td['statusname'] == "In Progress"){
                    $buttons .=  '<button style="margin-bottom:5px;margin-right: 30px;text-transform: uppercase;
                    color: #ffffff !important;background-color: '.$td['taskcolor'].';border-color: '.$td['taskcolor'].';" type="button" class="btn btn-sm pull-right" id="stop'.$td['taskid'].'" onclick="stoptask('.$td['tasktimeid'].','.$td['taskid'].','.$td['employeeid'].',\'Closed\')">Stop</button>';
                    $buttons .=  '<button type="button" style="margin-right: 20px;text-transform: uppercase;
                    color: #ffffff !important;background-color: #d1ce2c;border-color: #d1ce2c;" class="btn btn-sm pull-right" id="onhold'.$td['taskid'].'" onclick="onholdtask('.$td['taskid'].','.$td['employeeid'].',\'On Hold\')">On Hold</button>';
                  }else if($td['statusname'] == "On Hold"){
                    $buttons .=  '<button type="button" style="margin-right: 25px;text-transform: uppercase;
                    color: #ffffff !important;background-color: '.$td['taskcolor'].';border-color: '.$td['taskcolor'].';" class="btn btn-sm pull-right" id="restart'.$td['taskid'].'" onclick="restarttask('.$td['taskid'].','.$td['employeeid'].',\'On Hold\',2)">Restart</button>';
                  }else if($td['statusname']=="Closed"){
                    if(!empty($td['srprojecthead'])){
                      if(in_array($this->session->userdata(base_url().'ADMINID'),explode(",",$td['srprojecthead']))){
                        $buttons .=  '<button type="button" style="margin-right: 25px;text-transform: uppercase;
                        color: #ffffff !important;background-color: '.$td['taskcolor'].';border-color: '.$td['taskcolor'].';" onclick="reviewtask('.$td['taskid'].','.$td['employeeid'].',,\'Review\')" class="btn btn-sm pull-right" id="review'.$td['taskid'].'">Review</button>';
                      }else{
                        $buttons .=  '<button type="button" style="margin-right: 25px;text-transform: uppercase;
                        color: #ffffff !important;background-color: '.$td['taskcolor'].';border-color: '.$td['taskcolor'].';" class="btn btn-sm pull-right" id="review'.$td['taskid'].'">Review</button>';
                      }
                    }else{
                      $buttons .=  '<button type="button" style="margin-right: 25px;text-transform: uppercase;
                      color: #ffffff !important;background-color: '.$td['taskcolor'].';border-color: '.$td['taskcolor'].';" class="btn btn-sm pull-right" id="review'.$td['taskid'].'">Review</button>';
                    }
                  }else if($td['statusname']=="Completed"){
                    $buttons .=  '<button type="button" style="margin-right: 15px;text-transform: uppercase;
                    color: #ffffff !important;background-color: '.$td['taskcolor'].';border-color: '.$td['taskcolor'].';" class="btn btn-sm pull-right" id="completed'.$td['taskid'].'" onclick="">Completed</button>';
                  }else if($td['statusname']=="Cancelled"){
                    $buttons .=  '<button type="button" style="margin-right: 15px;text-transform: uppercase;
                    color: #ffffff !important;background-color: '.$td['taskcolor'].';border-color: '.$td['taskcolor'].';" class="btn btn-sm pull-right" id="cancelled'.$td['taskid'].'" onclick="">Cancelled</button>';
                  }  

                  //Time Calculation
                  if(empty($td['totaltime'])){
                    $TotalTime = '00:00:00';              
                  }else{
                    $TotalTime = $td['totaltime'];
                  }
      
                  if(empty($td['totalholdtime'])){
                    $HoldTime = '00:00:00';              
                  }else{
                    $HoldTime = $td['totalholdtime'];
                  }
      
                  if(empty($td['withoutholdtime'])){
                    $WithoutHoldTime = '00:00:00';              
                  }else{
                    $WithoutHoldTime = $td['withoutholdtime'];
                  }
      
                  sscanf($HoldTime, "%d:%d:%d", $hours, $minutes, $seconds);
                  $Hold_time_seconds = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;
                  $TotalHoldDays = number_format($Hold_time_seconds/60/60/8,2);
      
                  sscanf($TotalTime, "%d:%d:%d", $hours, $minutes, $seconds);
                  $Total_time_seconds = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;         
                  $TotalTimeDays = number_format($Total_time_seconds/60/60/8,2);
      
                  sscanf($WithoutHoldTime, "%d:%d:%d", $hours, $minutes, $seconds);
                  $Total_without_hold_seconds = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;
                  $TotalWithoutHoldDays = number_format($Total_without_hold_seconds/60/60/8,2);
                                                    
                  
                  sscanf($td['totalhours'], "%d:%d:%d", $hours, $minutes, $seconds);
                  $taskexpectedtimesec = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;
                  $taskexpectedtime = number_format($taskexpectedtimesec/60/60/8,2);
      
                  sscanf($WithoutHoldTime, "%d:%d:%d", $hours, $minutes, $seconds);
                  $taskwithoutholdtimesec = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;         
                  $taskwithoutholdtime = number_format($taskwithoutholdtimesec/60/60/8,2);
                                
                  $finaltime = ''; 
      
                  if($TotalHoldDays > 1){
                    $finaltime .= '<b>Hold Time : </b>'.$HoldTime.' ('.$TotalHoldDays.' Days)<br>';
                  }else{
                    $finaltime .= '<b>Hold Time : </b>'.$HoldTime.'<br>';
                  }
                      
                  if($TotalTimeDays > 1){
                    $finaltime .= '<b>Total Time : </b>'.$TotalTime.' ('.$TotalTimeDays.' Days)<br>';
                  }else{
                    $finaltime .= '<b>Total Time : </b>'.$TotalTime.'<br>';
                  }
                                
                  if($taskwithoutholdtime > $taskexpectedtime){ 
                    if($TotalWithoutHoldDays > 1){
                      $finaltime .= '<b>Without Hold Time : </b><span style="color:red;">'.$WithoutHoldTime.' ('.$TotalWithoutHoldDays.' Days)</span><br>';
                    }else{
                      $finaltime .= '<b>Without Hold Time : </b><span style="color:red;">'.$WithoutHoldTime.'</span><br>';
                    }              
                  }else{
                    if($TotalWithoutHoldDays > 1){
                      $finaltime .= '<b>Without Hold Time : </b>'.$WithoutHoldTime.' ('.$TotalWithoutHoldDays.' Days)<br>';
                    }else{
                      $finaltime .= '<b>Without Hold Time : </b>'.$WithoutHoldTime.'<br>';
                    }               
                  } 
                  $Taskfile = $this->Task->getFileDataByTaskId($td['taskid']);

                  ?>
                  <tr>
                        <td><?php echo ++$i;?></td>
                        <td><?php echo $this->general_model->displaydate($td['createddate']);?></td>
                        <td><?php echo '<a href="#" class="popoverButton a-without-link" title="Task Information" data-toggle="popover" data-trigger="hover" data-content="'.$content.'" style="word-break: break-word !important;" onclick="viewtaskdetails('.$td['taskid'].')">'.$td['taskname'].'</a></br></br><b><a href="'.ADMIN_URL.'project/viewproject/'.$td['projectid'].'" class="a-without-link">'.ucfirst($td['project']).'</a></b>';?></td>                      
                        <td><?=$this->general_model->displaydate($td['expecteddate'])?></td>                                         
                        <td><?=$this->general_model->displaydatetime($td['totalhours'],'H:i')?></td>                         
                        <td><?=ucfirst($td['priority'])?></td> 
                        <td><?=$buttons?></td> 
                        <td><?=$finaltime?></td>   
                        <td>
                       <?php 
                       $transferhistorystr="";
                       if(isset($transferhistoryarr[$td['taskid']])){
                         $transferhistorystr=$transferhistoryarr[$td['taskid']];
                       }
                       
                       if($transferhistorystr!=""){?>
                        <a class="popoverButton a-without-link" style="color:#20a8d8;word-break: break-word !important;" title="Transfer History" data-toggle="popover" data-trigger="hover" onclick="loadtask_modal('<?=$td['taskid']?>')" data-content="<?php echo $transferhistorystr;?><br/>"><?=$td['employee']?></a>
                        <?php }else{?>
                        <a class="a-without-link" onclick="loadtask_modal('<?=$td['taskid']?>')" style="color:#20a8d8;word-break: break-word !important;"><?=$td['employee']?></a>
                        <?php } ?>
                        </td>                                          
                        <td>
                       <?php if(strpos($this->viewData['submenuvisibility']['submenuvisible'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){?>
                            <a class="<?=view_class?> btn-tooltip" href="<?=ADMIN_URL?>task/viewtask/<?=$td['taskid'];?>" data-toggle="tooltip" target="_blank" title=<?=view_title?>><?=view_text?></a>
                           <?php } ?>
                                      <?php if(strpos($submenuvisibility['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                                     <a class="<?=edit_class;?> btn-tooltip" href="<?php echo ADMIN_URL; ?>task/taskedit/<?=$td['taskid'];?>" traget="_blank" title=<?=edit_title?>><?=edit_text;?></a>
                                      <?php }if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                                      <a class="<?=delete_class;?> btn-tooltip" href="javascript:void(0)" title=<?=delete_title?> onclick="deleterow('<?=$td['taskid'];?>','<?=ADMIN_URL.'task/checktaskuse';?>','task','<?=ADMIN_URL.'task/deletemultask';?>')"><?=delete_text;?></a>
                                      
                                      <?php } ?>
                                       <a class="<?=clone_class;?> btn-tooltip" href="<?php echo ADMIN_URL?>task/taskclone/<?=$td['taskid']?>" target="_blank" title=<?=clone_title?>><?=clone_text;?></a> 
                                      <?php if(count($Taskfile)>0){?>
                                      <a href="<?=DOCUMENT?><?=$Taskfile[0]['file']?>" class="btn btn-primary btn-sm btn-tooltip" title="Download File" download="<?=$Taskfile[0]['file']?>" style="margin-top:5px;"><i class="fa fa-download" aria-hidden="true"></i></a>
                                      <?php } ?>
                                    </td>  
                         <td>
                            <div class="checkbox table-checkbox">
                                <input id="deletecheck<?=$td['taskid'];?>" onchange="singlecheck(this.id)" type="checkbox" value="<?=$td['taskid'];?>" name="deletecheck<?=$td['taskid'];?>" class="checkradios">
                                <label for="deletecheck<?=$td['taskid'];?>"></label>
                            </div>
                        </td> 
                    </tr> 
                    <?php }} ?>
                </tbody>
            </table>
            </div>
      </div>

      <div class="tab-pane active" id="bankdetails" role="tabpanel">
        <div class="row">
          <div class="col-md-6">
            <table class="table table-striped table-responsive-sm" width="100%">              
              <tr>
                <th>Bank Name</th>
                <td><?=$bankdetails["bankname"]?></td>
              </tr>
              <tr>
                <th>Account No</th>
                <td><?=$bankdetails["accountnumber"]?></td>
              </tr>              
              <tr>
                <th>Account Type</th>
                <td><?=($bankdetails["accounttypeid"]==2)?"Saving Account":"Current Account"?></td>
              </tr>  			  
            </table>
          </div>
          <div class="col-md-6">
            <table class="table table-striped table-responsive-sm" width="100%">
            <tr>
                <th>Account Holder Name</th>
                <td><?=$bankdetails["holdername"]?></td>
              </tr>
              <tr>
                <th>IFSC Code</th>
                <td><?=$bankdetails["ifsccode"]?></td>
              </tr>
              <tr>
                <th>Branch Name</th>
                <td><?=$bankdetails["branchname"]?></td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="add_data_Modal" class="modal fade">  
      <div class="modal-dialog">  
           <div class="modal-content">  
                <div class="modal-header"> 
                    <h4 class="modal-title" style="float:left;">Add Designation</h4>  
                     <button type="button" class="close" data-dismiss="modal">&times;</button>  
                </div>  
                <div class="modal-body">  
                     <form method="post" id="insert_form"> 
                        <input type="hidden" name="employeeid" id="employeeid" value="<?php echo $userdata['eid'];?>"/>
                        <div class="form-group" id="designation_div">
                            <label class="col-form-label" for="designationid">Designation </label>
                            <div class="input-group">
                                <select id="designationid" name="designationid" class="selectpicker form-control" data-style="form-control"  data-live-search="true" data-size="4" >
                                    <option value="0">Select Designation</option>
                                    <?php foreach($designation as $designationrow){ ?>
                                    <option value="<?php echo $designationrow['id']; ?>" <?php if(isset($userdata)){ if(isset($userdata['designationid'])){ if($designationrow['id']==$userdata['designationid']){ echo 'selected';} } } ?>><?php echo $designationrow['name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>  
                        <div class="form-group" id="fromdate_div">
                            <label class="col-form-label">From Date </label>
                            <input type="text" class="input-small form-control datepicker1" name="fromdate" id="fromdate" placeholder="From Date" value="<?php if(!empty($plandata)){ echo $this->general_model->displaydate($plandata['startdate']); } ?>" readonly/>
                        </div>
                        <div class="form-group" id="todate_div">
                            <input type="checkbox" name="box" id="box" value="1">
                            <label class="col-form-label">To Date </label>
                            <input type="text" class="input-small form-control datepicker1" name="todate" id="todate" placeholder="To Date"  value="<?php if(!empty($plandata)){ echo $this->general_model->displaydate($plandata['startdate']); } ?>" disabled readonly/>
                        </div>  
                        <input type="hidden" name="did" id="did"/>  
                        <input type="submit" name="insert" id="insert" value="Insert" class="btn btn-success" />  
                     </form>  
                </div>    
           </div>  
      </div>  
 </div>  
 <div id="add_salary_Modal" class="modal fade">  
      <div class="modal-dialog">  
           <div class="modal-content">  
                <div class="modal-header"> 
                    <h4 class="modal-title" style="float:left;">Add Salary</h4>  
                     <button type="button" class="close" data-dismiss="modal">&times;</button>  
                </div>  
                <div class="modal-body">  
                     <form method="post" id="insert_salary_form"> 
                        <input type="hidden" name="eid" id="eid" value="<?php echo $userdata['eid'];?>"/>
                        <div class="form-group" id="salary_div">
                            <label class="col-form-label">Salary Amount </label>
                            <input type="text" class="input-small form-control" name="salary1" id="salary1" placeholder="Salary Amount"/>
                        </div>
                        <div class="form-group" id="fromdate_div1">
                            <label class="col-form-label">From Date </label>
                            <input type="text" class="input-small form-control datepicker1" name="fromdate1" id="fromdate1" placeholder="From Date" readonly/>
                        </div>
                        <div class="form-group" id="todate_div1">
                            <input type="checkbox" name="todatebox" id="todatebox" value="1">
                            <label class="col-form-label">To Date </label>
                            <input type="text" class="input-small form-control datepicker1" name="todate1" id="todate1" placeholder="To Date" disabled readonly/>
                        </div>  
                        <input type="hidden" name="sid" id="sid"/>  
                        <input type="submit" name="insertsalary" id="insertsalary" value="Insert" class="btn btn-success" />  
                     </form>  
                </div>    
           </div>  
      </div>  
 </div>  

 <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="emailsubject"></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="prostock" class="form-horizontal" >
                <table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <div class="col-md-12">
                    
                    </div>
                  </thead>
                  <tbody>
                    <div id="emailbody"></div>
                      
                  </tbody>
                </table>
              </form>
      </div>
    </div>
  </div>
</div>

<div id="add_remarks_Modal" class="modal fade">  
      <div class="modal-dialog">  
           <div class="modal-content">  
                <div class="modal-header"> 
                    <h4 class="modal-title" style="float:left;" id="title">Task Put On Hold</h4>  
                     <button type="button" class="close" data-dismiss="modal">&times;</button>  
                </div>  
                <div class="modal-body">  
                     <form method="post" id="insert_remarks_form"> 
                        <input type="hidden" name="ttimeid" id="ttimeid" value=""/>
                        <input type="hidden" name="tid" id="tid" value=""/>
                        <input type="hidden" name="eid" id="eid" value=""/>      
                        <input type="hidden" name="sname" id="sname" value=""/>                   
                        <div class="form-group" id="taskstatus_div">
                            <label class="col-form-label">Status <span class="mandatoryfield">*</span></label>
                            <select id="taskstatus" name="taskstatus" class="selectpicker form-control" data-style="form-control"
                                data-live-search="true" data-size="5" tabindex="1">
                            </select>
                        </div>
                        <div class="form-group" id="remarks_div">
                            <label class="col-form-label">Remarks <span class="mandatoryfield">*</span></label>
                            <textarea class="input-small form-control" name="remarks" id="remarks" rows="5" placeholder=""/></textarea>
                        </div>
                        <input type="submit" name="insertremarks" id="insertremarks" value="Submit" class="<?=addbtn_class?>" />  
                     </form>  
                </div>    
           </div>  
      </div>  
</div> 

<div class="modal" id="myModal2" tabindex='-1'>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="post_title">Transfer</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form action="#" id="taskform" class="form-horizontal">
                        <input type="hidden" name="taskid" id="task2" value="">
                        <input type="hidden" name="oldassignto" id="oldassignto" value="">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row" id="inquiryassignto_div">
                                    <label class="col-md-3 col-form-label" for="inquiryassignto">Assign To</label>
                                    <div class="col-md-8">
                                        <select class="form-control selectpicker" id="taskassignto" name="employee" data-live-search="true" data-size="5" data-style="form-control" onchange="showreason()">
                                            <option value="0">Select Employee</option>
                                            <?php foreach($Userdatas as $ud){ ?>
                                                <option value="<?php echo $ud['id'];?>"><?php echo $ud['name'];?></option>
                                           <?php } ?>

                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row" id="reason1_div" style="display:none;">
                                    <label class="col-md-3 col-form-label" for="reason1">Reason <span class="mandatoryfield">*</span></label>
                                    <div class="col-md-8">
                                        <textarea name="reason" id="reason1" class="form-control" rows="5" value="" placeholder="Reason"></textarea>
                                    </div>
                                </div>                             
                                <div class="form-group row">
                                    <label class="col-md-3 control-label"></label>
                                    <div class="col-md-8">
                                        <input type="button" id="submit" onclick="checkvalidation1()" name="submit" value="SUBMIT" class="btn btn-success btn-raised">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
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
                     url:"<?php echo site_url('rkinsite/designation/adddesignationhistory')?>",  
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
                            setTimeout(function() { window.location=SITE_URL+"user/viewuser/"+<?php echo $userdata['eid'];?>; }, 1500);
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
                url:"<?php echo site_url('rkinsite/designation/getdesignationhistory')?>",  
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
                     url:"<?php echo site_url('rkinsite/user/addsalaryhistory')?>",  
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
                            setTimeout(function() { window.location=SITE_URL+"user/viewuser/"+<?php echo $userdata['eid'];?>; }, 1500);
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
                url:"<?php echo site_url('rkinsite/user/getsalaryhistory')?>",  
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
