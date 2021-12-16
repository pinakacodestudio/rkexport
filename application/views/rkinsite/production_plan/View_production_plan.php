<script>
    var OrderId = '<?php if(isset($productionplandata)){ echo $productionplandata['orderid']; } ?>';
</script>
<style>
    .productvariantdiv {
        box-shadow: 0px 1px 6px #333 !important;
        margin-bottom: 20px;
    }
</style>
<div class="page-content">
    <div class="page-heading">            
        <h1>View <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active">View <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
        </small>
    </div>
    <div class="container-fluid">
                                    
      <div data-widget-group="group1">
        <div class="row">
            <form class="form-horizontal" id="production-plan-form">
                <input type="hidden" name="productionplanid" id="productionplanid" value="<?php if(isset($productionplandata)){ echo $productionplandata['id']; } ?>">
                <div class="col-md-12">
                    <div class="panel panel-default border-panel">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-7">
                                   <div class="form-group" id="orderid_div">
                                        <label class="col-md-2 pl-n pr-n control-label" for="orderid">Select Order</label>
                                        <div class="col-md-6">
                                            <select id="orderid" name="orderid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                <option value="0">Select Order</option>
                                                <?php foreach($orderdata as $order){ ?>
                                                    <option data-productionplanid="<?php echo $order['productionplanid']; ?>" value="<?php echo $order['id']; ?>" <?php if(isset($productionplandata) && $productionplandata['orderid'] == $order['id']){ echo "selected"; } ?>><?php echo $order['ordernumber']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5 pull-right text-right">
                                    <a class="<?=back_class;?>" href="<?=ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>" title=<?=back_title?>><?=back_text?></a>
                                    <!-- <a id="editBtn" class="<?=editbtn_class;?>" href="<?=ADMIN_URL.$this->session->userdata(base_url().'submenuurl').'/product-recepie-edit/'.$productrecepiedata['id']?>" title=<?=editbtn_title?>><?=editbtn_text?></a> -->
                                </div>
                                <div class="col-md-12"><hr></div>
                                <div class="col-md-12" id="readytostartpanel"></div> 
                                <div class="col-md-12" id="missingmaterialpanel"></div> 
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
      </div>

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->