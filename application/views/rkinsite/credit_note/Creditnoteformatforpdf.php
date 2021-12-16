<?php
$floatformat = '.';
$decimalformat = ',';
?>
<!DOCTYPE html>
<html>
<head>
    <title></title>
    <style type="text/css">
        table>thead>tr>th{
            padding: 8px;
        }
    </style>
</head>
<body style="background-color: #FFF;">

<div class="row mb-sm">
    <div class="col-md-12">
        <div class="">
            <div class="panel-body no-padding">
                <div class="table-responsive">
                    <p class="text-center" style="font-size: 16px;color: #000"><u><b>Sales Return</b></u></p>
                    <?php require_once(APPPATH."views/".ADMINFOLDER.'credit_note/Creditnoteproductdetails.php');?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <?php require_once(APPPATH."views/".ADMINFOLDER.'credit_note/Creditnotesummarydetails.php');?>
    </div>
</div>
</body>
</html>
