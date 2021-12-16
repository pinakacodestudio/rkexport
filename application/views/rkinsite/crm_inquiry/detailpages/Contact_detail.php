<div class="col-md-12">
  <table id="contactinquirytbl" class="table table-striped table-bordered table-responsive-sm"
    cellspacing="0" width="100%">
    <thead>
        <tr>
            <th width="1%">No.</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Mobile No</th>
            <th>Birth Date</th>
            <th>Anniversary Date</th>
            <th>Designation</th>
            <th>Departmant</th>                                   
        </tr>
    </thead>
    <tbody>
        <?php $i=0;                   
            foreach($contactdetail as $cd){                              
        ?>
        <tr>
            <td width="1%"><?php echo ++$i;?></td>
            <td><?php echo ucwords($cd['firstname']);?></td>
            <td><?php echo ucwords($cd['lastname']);?></td> 
            <td><?php echo '<a class="a-without-link" href="mailto:'.$cd['email'].'" target="_blank">'.$cd['email'].'</a>';?></td>                                    
            <td><a href="<?php echo 'https://api.whatsapp.com/send?phone='.$cd['code'].$cd['mobileno'].'&text=hi '.$cd['firstname'];?>" target="_blank"><?php echo str_replace(' ', '', $cd['countrycode'].$cd['mobileno']);?></a></td>
            <td><?php 
              if($cd['birthdate']!="0000-00-00") {
                echo $this->general_model->displaydate($cd['birthdate']);
              }
            ?></td>
            <td><?php if($cd['annidate']!="0000-00-00") {
              echo $this->general_model->displaydate($cd['annidate']);
            } ?></td>
            <td><?php echo ucwords($cd['designation']);?></td>   
            <td><?php echo ucwords($cd['department']);?></td>                                       
        </tr>
        <?php
            }
        ?>
    </tbody>                            
  </table>
</div>