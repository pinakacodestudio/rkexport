<?php if(!empty($productprocessdata)){ 
    $panelborderclass = "border-panel";
    $panelborderstyle = "";
    if(isset($printtype) && $printtype==1){
        $panelborderclass = ""; 
        $panelborderstyle = "border: 1px solid #ddd;";        
    }
    $style = $padding0 = '';
    $bordercolor = "#ddd";
    if(isset($printtype) && $printtype==1){
        $style = 'style="padding: 5px;border: 1px solid #666;font-size: 12px;"';
        $padding0 = "p-n";
        $bordercolor = "#666";
    }
    if(!isset($section)){
        foreach($productprocessdata['processes'] as $process){

            require(APPPATH."views/".ADMINFOLDER.'product_process/Processwiseproductdetails.php');
        } 
    }
    if(count($producttotal['totaloutproducts'])>0 || count($producttotal['totalinproducts'])>0){
        if(!isset($section) || (isset($section) && $section=="total")){
            if(isset($printtype) && $printtype==1){ ?>
                <div class="col-md-12 mb-sm" style="padding: 0px 5px;border: 1px solid #666;">
                    <h5><b>Total Amount of In/OUT Product</b></h5>
            <?php }else{ ?>
                <div class="panel panel-default <?=$panelborderclass?>" style="<?=$panelborderstyle?>">
            
                    <div class="panel-heading">
                        <div class="col-md-6 p-n">
                            <h2>Total Amount of In/OUT Product</h2>
                        </div>
                        <div class="col-md-6 p-n text-right">
                            <?php if (in_array("print",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                            <a class="<?=printbtn_class;?>" href="javascript:void(0)" onclick="printallprocessdetail(<?=$productprocessdata['processes'][0]['processgroupid']?>,<?=$productprocessdata['processes'][0]['productprocessid']?>,'total')" title=<?=printbtn_title?>><?=printbtn_text?></a>
                            <?php } if (in_array("export-to-pdf",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                            <a class="<?=exportpdfbtn_class;?>" href="javascript:void(0);" title="<?=exportpdfbtn_title?>" onclick="exporttopdfallprocessdetail(<?=$productprocessdata['processes'][0]['processgroupid']?>,<?=$productprocessdata['processes'][0]['productprocessid']?>,'total')"><?=exportpdfbtn_text;?></a>
                            <?php } ?>				
                        </div>
                    </div>
                    <div class="panel-body">
            <?php } 
                $this->load->view(ADMINFOLDER.'product_process/Totalamountdetails');
            if(isset($printtype) && $printtype==1){ ?>
                </div> 
            <?php }else{ ?>
                    </div>
                </div>
            <?php } 
        }
    }
 } 
 ?>