<div class="row mb-xl">
    <div class="col-md-12">
        
        <?php if(!empty($invoicesettingdata)) { ?>
        <div class="pull-left" style="width: 32%">                            
            <?php if(file_exists(SETTINGS_PATH.$invoicesettingdata['logo'])){ ?>
            <img src="<?php echo MAIN_LOGO_IMAGE_URL.$invoicesettingdata['logo']; ?>" alt="<?=$invoicesettingdata['logo']?>" style="width: auto;max-width: 100%;max-height: 120px;">
            <?php } ?>
            <address class="mt-md mb-md">
              <?php if($invoicesettingdata['businessaddressfull']!=''){ ?>
                <?=$invoicesettingdata['businessaddressfull']?><br>
              <?php } ?>
              <?=$invoicesettingdata['cityname']." - ".$invoicesettingdata['postcode']." (".$invoicesettingdata['provincename'].") ".$invoicesettingdata['countryname'];?><br>
              <?php if($invoicesettingdata['invoiceemail']!=''){ ?>
                <b>Email : </b> <?=$invoicesettingdata['invoiceemail']?><br>
              <?php } ?>
              <?php if($invoicesettingdata['gstno']!=''){ ?>
              <b>GST No. : </b> <?=$invoicesettingdata['gstno']?>
              <?php } ?>
            </address>
        </div>
        <?php } ?>
        <div class="pull-right" style="width: 65%">
            <div class="pull-left" style="width: 50%">
                <?php if(!empty($transactiondata['transactiondetail']['billingaddressid'])){ ?>
                <h4 style="font-size: 15px;"><b>Billing Address</b></h4>
                <address>
                    <?php if(!empty($transactiondata['transactiondetail'])){ echo ucwords($transactiondata['transactiondetail']['membername']); } ?><br>
                    <?php if(!empty($transactiondata['transactiondetail'])){ echo ucwords($transactiondata['transactiondetail']['billingaddress']); } ?><br/>
                    <b>Tel/Mobile : </b> <?php if(!empty($transactiondata['transactiondetail'])){ echo ucwords($transactiondata['transactiondetail']['mobileno']); } ?><br >
                    <b>Email : </b> <?php if(!empty($transactiondata['transactiondetail'])){ echo $transactiondata['transactiondetail']['email']; } ?><br >
                    <b>GST No. : </b> <?php if(!empty($transactiondata['transactiondetail']) && $transactiondata['transactiondetail']['gstno']!=''){ echo $transactiondata['transactiondetail']['gstno']; }else{ echo "&nbsp;&nbsp;-"; } ?>
                </address>
                <?php } ?>
            </div>
            <div class="pull-right" style="width: 50%">
                <?php if(!empty($transactiondata['transactiondetail']['shippingaddressid'])){ ?>
                    <h4 style="font-size: 15px;"><b>Shipping Address</b></h4>
                    <address>
                        <?php if(!empty($transactiondata['transactiondetail'])){ echo ucwords($transactiondata['transactiondetail']['shippingmembername']); } ?><br>
                        <?php if(!empty($transactiondata['transactiondetail'])){ echo ucwords($transactiondata['transactiondetail']['shippingaddress']); } ?><br/>
                        <b>Tel/Mobile : </b> <?php if(!empty($transactiondata['transactiondetail'])){ echo ucwords($transactiondata['transactiondetail']['shippingmobileno']); } ?><br >
                        <b>Email : </b> <?php if(!empty($transactiondata['transactiondetail'])){ echo $transactiondata['transactiondetail']['shippingemail']; } ?><br >
                    </address>
                <?php } ?>
                <ul class="text-left list-unstyled">
                    <?php if(isset($printtype) && $printtype=='order'){ ?>
                        <?php if($transactiondata['transactiondetail']['quotationid'] != '') { ?>
                        <li><b>Quotation No. : </b> 
                            <?php if(isset($transactiondata['transactiondetail']['quotationid'])){
                                echo $transactiondata['transactiondetail']['quotationid'];
                            }?>
                        </li>
                        <?php }?>
                        <li><b>Order No. : </b> <?=$transactiondata['transactiondetail']['orderid']?></li>
                        <li><b>Order Date : </b> <?=$transactiondata['transactiondetail']['createddate']?></li>
                        <li><b>Payment Method : </b>
                        <?php
                        if($transactiondata['transactiondetail']['paymenttype']==1){
                            echo "COD";
                        }else if($transactiondata['transactiondetail']['paymenttype']==2){
                            echo isset($this->Paymentgatewaytype[$transactiondata['transactiondetail']['paymentgetwayid']]) ? ucwords($this->Paymentgatewaytype[$transactiondata['transactiondetail']['paymentgetwayid']]) : '-';
                        }else if($transactiondata['transactiondetail']['paymenttype']==3){
                            echo "Advance Payment";
                        }else if($transactiondata['transactiondetail']['paymenttype']==4){
                            echo "Partial Payment";
                        }else if($transactiondata['transactiondetail']['paymenttype']==5){
                            echo "Debit";
                        }
                        ?>
                        </li>
                    <?php }else if(isset($printtype) && $printtype=='quotation'){ ?>
                        <li><b>Quotation No. : </b> <?=$transactiondata['transactiondetail']['quotationid']?></li>
                        <li><b>Quotation Date : </b> <?=$transactiondata['transactiondetail']['createddate']?></li>
                        <li><b>Payment Method : </b>
                        <?php
                        if($transactiondata['transactiondetail']['paymenttype']==1){
                            echo "COD";
                        }else if($transactiondata['transactiondetail']['paymenttype']==2){
                            echo "Online Payment";
                        }else if($transactiondata['transactiondetail']['paymenttype']==3){
                            echo "Advance Payment";
                        }else if($transactiondata['transactiondetail']['paymenttype']==4){
                            echo "Partial Payment";
                        }
                        ?>
                        </li>
                    <?php }else if(isset($printtype) && ($printtype=='invoice' || $printtype=='purchase-invoice')){ 
                            
                        $uniquearr = (array_unique(array_column($transactiondata['transactionproduct'],'orderid')));   
                        if(count($uniquearr)==1 && $printtype=='purchase-invoice'){ ?>
                        <li><b>GRN No. : </b> 
                            <?php echo $uniquearr[0]; ?>
                        </li>
                        
                        <?php  } ?>
                        <li><b>Invoice No. : </b> 
                            <?php if(isset($transactiondata['transactiondetail']['invoiceno'])){
                                echo $transactiondata['transactiondetail']['invoiceno'];
                            }?></li>
                        <li><b>Invoice Date : </b>                             
                            <?php if(isset($transactiondata['transactiondetail']['invoicedate'])){
                                echo $transactiondata['transactiondetail']['invoicedate'];
                            }?>
                        </li>
                    <?php }else if(isset($printtype) && $printtype=='creditnote'){ ?>
                        <li><b>Credit Note No. : </b> 
                            <?php if(isset($transactiondata['transactiondetail']['creditnoteno'])){
                                echo $transactiondata['transactiondetail']['creditnoteno'];
                            }?></li>
                        <li><b>Credit Note Date : </b>                             
                            <?php if(isset($transactiondata['transactiondetail']['creditnotedate'])){
                                echo $transactiondata['transactiondetail']['creditnotedate'];
                            }?>
                        </li>
                    <?php }else if(isset($printtype) && ($printtype=='goods_received_notes')){ 
                        
                        $uniquearr = (array_unique(array_column($transactiondata['transactionproduct'],'orderid')));   
                        if(count($uniquearr)==1){ ?>
                        <li><b>Order No. : </b> 
                            <?php echo $uniquearr[0]; ?>
                        </li>
                        
                        <?php  } ?>
                        <li><b>GRN No. : </b> 
                            <?php if(isset($transactiondata['transactiondetail']['grnnumber'])){
                                echo $transactiondata['transactiondetail']['grnnumber'];
                            }?></li>
                        <li><b>Received Date : </b>                             
                            <?php if(isset($transactiondata['transactiondetail']['receiveddate'])){
                                echo $transactiondata['transactiondetail']['receiveddate'];
                            }?>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
</div>