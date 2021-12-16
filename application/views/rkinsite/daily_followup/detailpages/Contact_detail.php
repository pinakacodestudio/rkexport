<div class="col-md-12">
<?php foreach($contactdetail as $k=>$cd){  ?>
  <h4>Contact <?=$k+1?></h4>
  <div class="row">
    <div class="col-md-6">
      <table class="table table-striped table-responsive-sm" width="100%">
        <tr>
          <th>First Name</th>
          <td><?php echo ucwords($cd['firstname']); ?></td>
        </tr>
        <tr>
          <th>Last Name</th>
          <td><?php echo ucwords($cd['lastname']); ?></td>
        </tr>
        <tr>
          <th>Email</th>
          <td><?php echo $cd['email']; ?></td>
        </tr>
        <tr>
          <th>Mobile NO</th>
          <td><?php echo str_replace(' ', '',$cd['countrycode'].$cd['mobileno']);?></td>
        </tr>
      </table>
    </div>
    <div class="col-md-6">
      <table class="table table-striped table-responsive-sm" width="100%">
        <tr>
          <th>Birth Date</th>
          <td><?php 
              if($cd['birthdate']!="0000-00-00") {
                echo $this->general_model->displaydate($cd['birthdate']);
              }
          ?></td>
        </tr>
        <tr>
          <th>Anniversary Date</th>
          <td>
            <?php 
              if($cd['annidate']!="0000-00-00") {
                echo $this->general_model->displaydate($cd['annidate']);
              } ?>
          </td>
        </tr>
        <tr>
          <th>Designation</th>
          <td><?php echo $cd['designation']; ?></td>
        </tr>
        <tr>
          <th>Department</th>
          <td><?php echo $cd['department']; ?></td>
        </tr>
      </table>
    </div>
  </div>
<?php } ?>
</div>