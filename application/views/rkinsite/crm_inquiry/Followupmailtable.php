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
if(!empty($followupdata)){
?>
	<table width="100%" class="table-design">
		<tr class="table-design">
			<th class="table-design">Company</th>
			<th class="table-design">Employee</th>
			<th class="table-design">Type</th>
			<th class="table-design">Date</th>
			<th class="table-design">Notes</th>
			<th class="table-design">Status</th>
		</tr>
		<tr class="table-design">
			<td width="20%" class="table-design"><?=$followupdata['companyname']?></td>
			<td width="20%" class="table-design"><?=$followupdata['employeename']?></td>
			<td width="10%" class="table-design"><?=$followupdata['followuptypename']?></td>
			<td width="10%" class="table-design"><?=$this->general_model->displaydate($followupdata['date'])?></td>
			<td width="20%" class="table-design"><?=$followupdata['notes']?></td>
			<td width="20%" class="statusbg table-design"><?=$followupdata['statusname']?></td>
		</tr>
	</table>
<?php
}
?>