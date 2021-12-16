<?php
if(count($myleavedata)>0 && !empty($myleavediff)){
?>
<table style='border-collapse: collapse;border: 1px solid #d6d6d6;'>
    <tr style='height:40px;border: 1px solid #d6d6d6;'>
        <td style='color:#6B6666;border: 1px solid #d6d6d6;padding:0px 28px 0px 25px;'>Name</td>
        <td style='color:#6B6666;border: 1px solid #d6d6d6;padding:0px 28px 0px 25px;'><?=$myleavedata['name']?></td>
    </tr>
    <tr style='height:40px;border: 1px solid #d6d6d6;'>
        <td style='color:#6B6666;border: 1px solid #d6d6d6;padding:0px 28px 0px 25px;'>No of Days</td>
        <td style='color:#6B6666;border: 1px solid #d6d6d6;padding:0px 28px 0px 25px;'><?=$myleavediff?></td>
    </tr>
    <tr style='height:40px;border: 1px solid #d6d6d6;'>
        <td style='color:#6B6666;border: 1px solid #d6d6d6;padding:0px 28px 0px 25px;'>From Date</td>
        <td style='color:#6B6666;border: 1px solid #d6d6d6;padding:0px 28px 0px 25px;'><?=$this->general_model->displaydate($myleavedata['fromdate'])?></td>
    </tr>
    <tr style='height:40px;border: 1px solid #d6d6d6;'>
        <td style='color:#6B6666;border: 1px solid #d6d6d6;padding:0px 28px 0px 25px;'>To Date</td>
        <td style='color:#6B6666;border: 1px solid #d6d6d6;padding:0px 28px 0px 25px;'><?php if($myleavedata['todate'] != "0000-00-00"){echo $this->general_model->displaydate($myleavedata['todate']);}else{echo "-";}?></td>
    </tr>
    <tr style='height:40px;border: 1px solid #d6d6d6;'>
        <td style='color:#6B6666;border: 1px solid #d6d6d6;padding:0px 28px 0px 25px;'>Reason</td>
        <td style='color:#6B6666;border: 1px solid #d6d6d6;padding:0px 28px 0px 25px;'><?=$myleavedata['reason']?></td>
    </tr>
    <tr style='height:40px;border: 1px solid #d6d6d6;'>
        <td style='color:#6B6666;border: 1px solid #d6d6d6;padding:0px 28px 0px 25px;'>Request Date</td>
        <td style='color:#6B6666;border: 1px solid #d6d6d6;padding:0px 28px 0px 25px;'><?=$this->general_model->displaydatetime($myleavedata['createddate'])?></td>
    </tr>
</table>
<br/>
<a href="<?=base_url().ADMINFOLDER?>leave" target='_blank'><button style='cursor:pointer;background-color:#6947A9;border-color:#6947A9;color:#f2f2f2;border-radius:3px;padding:2px 5px;border:1px solid transparent'>Approve</button></a>
<?php
}
?>