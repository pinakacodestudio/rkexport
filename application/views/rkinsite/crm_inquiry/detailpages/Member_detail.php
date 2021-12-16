<div class="col-md-6">
<?php if(count($memberdata)>0) { ?>
    <table class="table table-striped table-responsive-sm" width="100%">
        <tr>
            <th>Company Name</th>
            <td><?php echo $memberdata['companyname']; ?></td>
        </tr>
        <tr>
            <th>Name</th>
            <td><?php echo ucwords($memberdata['name']); ?>
            </td>
        </tr>
        <tr>
            <th>Lead Source</th>
            <td><?php echo ($memberdata['leadsource']!=""?$memberdata['leadsource']:"-"); ?></td>
        </tr>
        <tr>
            <th>Industry</th>
            <td><?php echo ($memberdata['industry']!=""?$memberdata['industry']:"-"); ?></td>
        </tr>
        <tr>
            <th>Rating</th>
            <td>
                <div id="rate" class="rate"></div>
            </td>
        </tr>
        <tr>
            <th>Assign To</th>
            <td><?php echo $assignedemp; ?></td>
        </tr>
        <tr>
            <th>Type</th>
            <td><?php echo isset($this->Membertype[$memberdata['type']])?$this->Membertype[$memberdata['type']]:"-"; ?></td>
        </tr>
        <tr>
            <th>Website</th>
            <td><?php echo ($memberdata['website']!=""?$memberdata['website']:"-"); ?></td>
        </tr>
        <tr>
            <th>Status</th>
            <td>
                <?php echo ($memberdata['mstatus'] == 1) ? '<span class="label label-warning text-white">Suspect</span>':'';
                echo ($memberdata['mstatus'] == 2) ? '<span class="label label-info text-white">Dead Lead</span>':'';
                echo ($memberdata['mstatus'] == 3) ? '<span class="label label-info text-white">Prospect</span>':'';
                echo ($memberdata['mstatus'] == 4) ? '<span class="label label-success text-white">Archived</span>':'';
                echo ($memberdata['mstatus'] == 5) ? '<span class="label label-danger text-white">Closed</span>':'';
                ?>
            </td>
        </tr>
    </table>
    <?php
}
?>
</div>
<div class="col-md-6">
    <?php if(count($memberdata)>0) { ?>
    <table class="table table-striped table-responsive-sm" width="100%">
        <tr>
            <th>Address</th>
            <td><?php 
                    if($memberdata['address']!=""){
                        echo $memberdata['address']." ".$memberdata['city']."(".$memberdata['state'].",".$memberdata['country'].")";
                    }else{
                        echo $memberdata['city']." (".$memberdata['state'].",".$memberdata['country'].")";
                    }
                ?>
            </td>
        </tr>
        <tr>
            <th>Area</th>
            <td><?php echo ($memberdata['area']!=""?$memberdata['area']:"-"); ?></td>
        </tr>
        <tr>
            <th>Pin Code</th>
            <td><?php echo ($memberdata['pincode']!=""?$memberdata['pincode']:"-"); ?></td>
        </tr>
        <tr>
            <th>Latitude</th>
            <td><?php echo ($memberdata['latitude']!=""?$memberdata['latitude']:"-"); ?></td>
        </tr>
        <tr>
            <th>Longitude</th>
            <td><?php echo ($memberdata['longitude']!=""?$memberdata['longitude']:"-"); ?></td>
        </tr>
        <tr>
            <th>Zone</th>
            <td><?php echo ($memberdata['zone']!=""?$memberdata['zone']:"-"); ?></td>
        </tr>
        <tr>
            <th>Remarks</th>
            <td><?php echo ($memberdata['remarks']!=""?$memberdata['remarks']:"-"); ?></td>
        </tr>
        <tr>
            <th>Requirement</th>
            <td><?php echo ($memberdata['requirement']!=""?$memberdata['requirement']:"-"); ?></td>
        </tr>
    </table>
    <?php } ?>
</div>