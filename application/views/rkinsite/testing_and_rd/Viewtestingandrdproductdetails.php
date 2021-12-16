<?php
    $style = 'style="padding: 5px;border: 1px solid #666;font-size: 12px;"';

/*if(isset($printtype) && $printtype=='creditnote' && $transactiondata['transactiondetail']['creditnotetype']==1){*/ ?>
    <table class="table table-hover table-bordered m-n invoice" width="100%" style="color: #000">
    <thead>
        <tr>
          <th class="width5">Sr. No.</th>
          <th >Product Name</th>
          <th class="text-right">Qty</th>
          <th class='text-right'>Mechanicle Checked (Qty)</th>
          <th class='text-right'>Electrically Checked (Qty)</th>
          <th class='text-right'>Visually Checked (Qty)</th>
          <th  class= "text-right"> Approve Qty</th>
          <?php if(!isset($hideonprint)){ ?>
          <th >View Report</th>
          <?php }?>
        </tr>
    </thead>
    <tbody>
      <?php foreach($productdetails as $pd =>$value){ 
        
        ?>
        <tr>
          <td ><?=$pd+1?></td>
          <td ><?=$productdetails[$pd]['productname']?></td>
          <td class='text-right'><?=numberFormat($productdetails[$pd]['qty'],2)?></td>
          <td class='text-right'>
               <?=numberFormat($productdetails[$pd]['mechanicledefectqty'],2)?>
          </td>
          <td class='text-right'>
               <?=numberFormat($productdetails[$pd]['electricallydefectqty'],2)?>
          </td>
          <td class='text-right'>
               <?=numberFormat($productdetails[$pd]['visuallydefectqty'],2)?>
          </td>
          <td class='text-right'><?=($productdetails[$pd]['qty']-$productdetails[$pd]['mechanicledefectqty']-$productdetails[$pd]['electricallydefectqty']-$productdetails[$pd]['visuallydefectqty'])?></td>
          <?php if(!isset($hideonprint)){ ?>
          <td><a href="<?= TESTING_IMAGE.$productdetails[$pd]['filename']?>" class="<?=view_class?>" target="_blank" ><?=view_text?></td>
          <?php } ?>
        </tr>
    <?php
      
    } ?>  
    </tbody>
</table>


<?php ?>