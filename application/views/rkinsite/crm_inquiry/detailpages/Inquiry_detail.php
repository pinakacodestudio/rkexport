  <div class="col-md-12 p-n">
    <div class="col-md-3">
      <p class="fs14"><b>ID</b> : <?php if(isset($inquirydata[0]['identifier'])){ echo $inquirydata[0]['identifier']; } ?></p>
    </div>
    <div class="col-md-3">
      <p class="fs14"><b>Assign To </b> : <?php if(isset($inquiryassignto)){ echo ucwords($inquiryassignto); } ?></p>
    </div>
    <div class="col-md-3">
      <p class="fs14"><b>Status</b> : <?php if(isset($inquirystatus)){ echo $inquirystatus; } ?></p>
    </div>
    <div class="col-md-3">
      <p class="fs14"><b>Inquiry Lead Source</b> : <?php if(isset($leadsourcename)){ echo $leadsourcename; } ?></p>
    </div>
  </div>

  <div class="col-md-12">
    <p class="fs14"><b><?=Inquiry?> Note</b> : <?php if(isset($notes)){ echo ucfirst($notes); } ?></p>
  </div>
  <div class="col-md-12"><hr/></div>
  <div class="col-md-12">
    <table id="viewinquirytbl" class="table table-striped table-bordered table-responsive-sm"
                            cellspacing="0" width="100%">
      <thead>
          <tr>
              <th width="5%">Sr. No.</th>
              <th>Product Name</th>
              <th>Product Category</th>
              <th class="text-right">Quantity</th>
              <th class="text-right">Rate (<?=CURRENCY_CODE?>)</th>
              <th class="text-right">Discount (<?=CURRENCY_CODE?>)</th>
              <th class="text-right">Amount (<?=CURRENCY_CODE?>)</th>
              <th class="text-right">Tax (<?=CURRENCY_CODE?>)</th>                                   
          </tr>
      </thead>
      <tbody>
          <?php $i=0; $totaltax = 0; $totalamount = 0; $totalnetamount = 0; 
            if(!empty($inquirydata)){                  
              foreach($inquirydata as $id){ 
                $amount = ($id['rate'] * $id['qty'] - $id['discount']);
                $taxamount = ($amount * $id['tax'] / 100);
                ?>
                <tr>
                    <td width="5%"><?php echo ++$i;?></td>
                    <td><?php echo $id['productname'];?></td>
                    <td><?php echo $id['pcname'];?></td> 
                    <td class="text-right"><?php echo $id['qty'];?></td>                                    
                    <td class="text-right"><?php echo numberFormat($id['rate'],2,',');?></td>
                    <td class="text-right"><?php echo numberFormat($id['discount'],2,',');?></td>
                    <td class="text-right"><?php echo numberFormat($amount,2,',');?></td>
                    <td class="text-right"><?php echo numberFormat($taxamount,2,',');?></td>                                   
                </tr>
                <?php
                $totaltax += $taxamount;
                $totalamount += $amount;
                $totalnetamount += $amount + $taxamount;
              }
            }else{ ?>
             <!--  <tr>
                <td colspan="8" class="text-center">No data available in table.</td>
              </tr> -->
            <?php }
          ?>
      </tbody>
      <tfoot>
        <tr> 
          <th colspan="7" style="text-align:right"><p>Gross Amount (<?=CURRENCY_CODE?>)</p><p>Tax Amount (<?=CURRENCY_CODE?>)</p><p>Net Amount (<?=CURRENCY_CODE?>)</th>
          <th id="total" style="text-align:right"></th>                                                 
        </tr>
      </tfoot>
    </table>  
  </div> 
<?php if(isset($inquirydata) && isset($noofinstallment) && $noofinstallment>0 && isset($installment) && count($installment)>0){ ?>
        <div class="col-md-12">
          <h3>Installment</h3><hr/>
          <div id="installmentmaindiv">
            <table class="table table-bordered table-striped table-responsive-sm" width="100%">
              <tr>
                <th class="width8 text-center">Sr. No.</th>
                <th class="text-right">Installment (%)</th>
                <th class="text-right">Amount (<?=CURRENCY_CODE?>)</th>
                <th class="text-center">Installment Date</th>
                <th class="text-center">Payment Date</th>
                <th class="text-center">Received Status</th>
              </tr>
              <?php
                foreach ($installment as $k=>$inmt) {
                  $div = $k+1; ?>
                  <tr>
                    <td class="text-center"><?=$div?></td>
                    <td class="text-right"><?=$inmt['percentage']?></td>
                    <td class="text-right"><?=numberFormat($inmt['amount'],2,',')?></td>
                    <td class="text-center"><?php
                      if($inmt['date']!="0000-00-00"){ echo $this->general_model->displaydate($inmt['date']);  } else{ echo "-"; }  ?></td>
                    <td class="text-center"><?php
                    if($inmt['paymentdate']!="0000-00-00"){ echo $this->general_model->displaydate($inmt['paymentdate']);  } else{ echo "-"; } ?></td>
                    <td class="text-center">
                      <?php if($inmt['status']==1){ 
                        echo "<span class='label label-success'>Received</span>";
                      }else{
                        echo "<span class='label label-danger'>Not Received</span>";
                      } ?>
                    </td>
                  </tr>
                <?php } ?>
            </table>
          </div>
        </div>
<?php } ?>