<script>
    var GENERATE_QRCODE_SRC = '<?=GENERATE_QRCODE_SRC?>';
    var CURRENCY_CODE = '<?=CURRENCY_CODE?>';
</script>
<div class="page-content">
    <div class="page-heading">    
        <div class="btn-group dropdown dropdown-l dropdown-breadcrumbs">
            <a class="dropdown-toggle dropdown-toggle-style" data-toggle="dropdown" aria-expanded="false"><span>
                <i class="material-icons" style="font-size: 26px;">menu</i>
            </span> </a>
            <ul class="dropdown-menu dropdown-tl" role="menu">
            <label class="mt-sm ml-sm mb-n">Menu</label>
            <?php
                $subid = $this->session->userdata(base_url().'submenuid');
                foreach($subnavtabsmenu as $row){
                if($subid == $row['id']){ ?>
                    
                    <li class="active"><a href="javascript:void(0);"><i class="fa fa-caret-right mr-sm"></i> <?=$row['name']; ?></a></li>
                
                <?php }else{ ?>
                    <li><a href="<?=base_url().ADMINFOLDER.$row['url']; ?>"><i class="fa fa-caret-right mr-sm"></i> <?=$row['name']; ?></a></li>
                <?php } 
                } ?>
            </ul>
        </div>         
        <h1><?=$title?></h1>            
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?=$title?></li>
            </ol>
    </small>
    </div>

    <div class="container-fluid">
                                    
        <div data-widget-group="group1">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default border-panel">
                        <div class="panel-heading">
                            <div class="col-md-8 pl-n">
                                <h4><b>Cashback Offer : </b><?=$cashbackofferdata['name']?></h4>
                            </div>
                            <div class="col-md-4 text-right p-n">
                                <a class="<?=back_class;?>" href="<?=ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>" title=<?=back_title?>><?=back_text?></a>
                                <?php if (in_array("print",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                                <a class="<?=printbtn_class;?>" href="javascript:void(0)" onclick="printQRCode()" title=<?=printbtn_title?>><?=printbtn_text?></a>
                                <?php } ?>
                            </div>
                            <div class="col-md-6">
                                <div class="panel-ctrls panel-tbl"></div>
                            </div>
                        </div>
                        <div class="panel-body">
                            <form class="form-horizontal" id="printqrcodeform">
                                <input type="hidden" name="cashbackofferid" id="cashbackofferid" value="<?php if(isset($cashbackofferdata)){ echo $cashbackofferdata['id']; }?>">
                                <div class="row">
                                    <div class="col-md-12 p-n" id="productdetailsdiv">
                                        <table id="producttable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>            
                                                    <th class="width8">Sr. No.</th>
                                                    <th>Product Name</th>  
                                                    <th>Earn Points</th>  
                                                    <th class="width15">Print No. of Copies </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                                if(!empty($cashbackofferproductdata)){
                                                    foreach($cashbackofferproductdata as $i=>$row){?>
                                                        <tr>
                                                            <td><?=($i+1)?>
                                                                <input type="hidden" name="priceid[]" id="priceid<?=($i+1)?>" value="<?php echo $cashbackofferproductdata[$i]['priceid']; ?>">
                                                            </td>
                                                            <td><?=$row['productname']?></td>
                                                            <td><?=$row['earnpoints']?></td>
                                                            <td>
                                                                <div class="form-group">
                                                                    <div class="col-md-12">
                                                                        <input type="text" name="printnoofcopies[]" id="printnoofcopies<?=($i+1)?>" class="form-control m-n" value="" maxlength="3" onkeypress="return isNumber(event)">
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                <?php }
                                                } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- .container-fluid -->
