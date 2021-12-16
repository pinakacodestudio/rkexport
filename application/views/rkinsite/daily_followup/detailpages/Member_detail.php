<div class="col-md-6">
<?php if(count($followupdata)>0) { ?>
<table class="table table-striped table-responsive-sm" width="100%">
    <tr>
        <th>Company Name</th>
        <td><?php echo $followupdata['companyname']; ?></td>
    </tr>
    <tr>
        <th>Name</th>
        <td><?php echo ucwords($followupdata['name']); ?>
    </td>
    </tr>
    <tr>
        <th>Lead Source</th>
        <td><?php echo $followupdata['leadsource']; ?></td>
    </tr>
    <tr>
        <th>Industry</th>
        <td><?php echo $followupdata['industry']; ?></td>
    </tr>
    <tr>
        <th>Rating</th>
        <td><div id="rate" class="rate"></div></td>
    </tr>
    <tr>
        <th>Assign To</th>
        <td><?php echo $assignedemp; ?></td>
    </tr>
    <tr>
    <th>Type</th>
        <td><?php echo $followupdata['type']!=0?$this->Membertype[$followupdata['type']]:"-"; ?></td>
    </tr>
    <tr>
        <th>Website</th>
        <td><?php echo $followupdata['website']; ?></td>
    </tr>
    <tr>
        <th>Status</th>
        <td>
            <?php echo ($followupdata['status'] == 1) ? '<span class="label label-warning text-white">Suspect</span>':'';
            echo ($followupdata['status'] == 2) ? '<span class="label label-info text-white">Dead Lead</span>':'';
            echo ($followupdata['status'] == 3) ? '<span class="label label-info text-white">Prospect</span>':'';
            echo ($followupdata['status'] == 4) ? '<span class="label label-success text-white">Archived</span>':'';
            echo ($followupdata['status'] == 5) ? '<span class="label label-danger text-white">Closed</span>':'';
            ?>
        </td>
    </tr>
</table>
<?php } ?>
</div>
<div class="col-md-6">
<?php if(count($followupdata)>0) { ?>
<table class="table table-striped table-responsive-sm" width="100%">
    <tr>
        <th>Address</th>
        <td><?php 
        if($followupdata['address']!=""){
            echo $followupdata['address']." ".$followupdata['city']."(".$followupdata['state'].",".$followupdata['country'].")";
        }else{
            echo $followupdata['address'].",";
        }
        ?></td>
    </tr>
    <tr>
        <th>Area</th>
        <td><?php echo $followupdata['area']; ?></td>
    </tr>
    <tr>
        <th>Pin Code</th>
        <td><?php echo $followupdata['pincode']; ?></td>
    </tr>
    <tr>
        <th>Latitude</th>
        <td><?php echo $followupdata['latitude']; ?></td>
    </tr>
    <tr>
        <th>Longitude</th>
        <td><?php echo $followupdata['longitude']; ?></td>
    </tr>
    <tr>
        <th>Zone</th>
        <td><?php echo $followupdata['zone']; ?></td>
    </tr>
    <tr>
        <th>Remarks</th>
        <td><?php echo $followupdata['remarks']; ?></td>
    </tr>
    <tr>
        <th>Requirement</th>
        <td><?php echo $followupdata['requirement']; ?></td>
    </tr>
</table>
<?php } ?>
</div>