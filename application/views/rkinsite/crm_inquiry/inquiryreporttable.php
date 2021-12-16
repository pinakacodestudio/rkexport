<style type="text/css">
	.table-design {
	  border: 1px solid black;text-align: center;padding: 6px 0px;
	  border-collapse: collapse;
	}
	.statusbg{
		background: lightgray
	}
</style>
<?php
if(!is_null($inquirydata)){
?>
	<table cellpadding="10" cellspacing="0" width="100%" class="table-design" style="text-align:left;">
		<tbody>
			<tr>
            	<td style="width:30%;border: 1px solid #ddd;">Company</td>
            	<td style="width:70%;border: 1px solid #ddd;"><?=$inquirydata['companyname']?></td>
			</tr>
			<tr>
            	<td style="width:30%;border: 1px solid #ddd;">Email</td>
            	<td style="width:70%;border: 1px solid #ddd;"><?=$inquirydata['memberemail']?></td>
			</tr>
			<tr>
            	<td style="width:30%;border: 1px solid #ddd;">Mobile No</td>
            	<td style="width:70%;border: 1px solid #ddd;"><?=$inquirydata['membermobileno']?></td>
			</tr>
			<tr>
            	<td style="width:30%;border: 1px solid #ddd;">Assigned To</td>
            	<td style="width:70%;border: 1px solid #ddd;"><?=$inquirydata['employeename']?></td>
			</tr>
			<tr>
            	<td style="width:30%;border: 1px solid #ddd;">Date</td>
            	<td style="width:70%;border: 1px solid #ddd;"><?=$this->general_model->displaydate($inquirydata['date'])?></td>
			</tr>
			<tr>
            	<td style="width:30%;border: 1px solid #ddd;">Notes</td>
            	<td style="width:70%;border: 1px solid #ddd;"><?=$inquirydata['notes']?></td>
			</tr>
			<tr>
            	<td style="width:30%;border: 1px solid #ddd;">Status</td>
            	<td style="width:70%;border: 1px solid #ddd;"><?=$inquirydata['statusname']?></td>
			</tr>
		</tbody>
	</table>
<?php
}
?>