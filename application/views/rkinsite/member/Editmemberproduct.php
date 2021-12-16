<style type="text/css">
	.load_variantsdiv{
		/*border:1px solid lightgray;*/
		padding: 5px 25px;
		/*box-shadow: 0px 1px 1px black;*/
		margin-bottom: 10px;
	}
	.variant_div{
		box-shadow: 0px 2px 9px #333;
		padding: 5px;
	}
</style>
<div class="page-content">
    <div class="page-heading">            
        <h1>Edit <?=$this->session->userdata(base_url().'submenuname')?> Product</h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url(); ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active">Edit <?=$this->session->userdata(base_url().'submenuname')?> Product</li>
            </ol>
		</small>
    </div>

    <div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default">
		        <div class="panel-body">
		        	<div class="col-sm-12 col-md-12 col-lg-12">
						<form class="form-horizontal" id="memberproductform" name="memberproductform">
							<input type="hidden" name="memberid" value="<?=$memberid?>" id="memberid">
							<input type="hidden" name="productid" value="<?=$productid?>" id="productid">
							<div class="form-group row" for="category" id="categoryid_div">
                           		<h4><span class="text-muted">Product Name : </span><?php echo $productdata['name'];?></h4>
                           		<h4><span class="text-muted">Category : </span><?php echo $productdata['categoryname'];?></h4>
                          	</div>

							
							<div id="load_variants row">
								<!--  -->
								<?php
								if($productdata['isuniversal']==0){
									$pricescnt=0;
									foreach($productcombination as $key => $value) {
										if(count($value)>0){  
									
								?>
									<div class="load_variantsdiv col-md-4">
										<div class="row variant_div">
											<div class="col-md-5">
												<div class="form-group" for="price" id="price_div<?=$pricescnt?>">Price<input type="text" id="price<?=$pricescnt?>" onkeypress="return decimal(event,this.value)" class="form-control prices" disabled="" placeholder="Price" name="price[9]" value="<?=$productcombination[$key][0]['price']?>">
												</div>
											</div>
											<div class="col-md-5 col-md-offset-1">
												<div class="form-group" for="price" id="memberprice_div<?=$pricescnt?>"><?=Member_label?> Price
												<?php
												if($productcombination[$key][0]['membervariantid']!=""){
												?>
												<input type="text" id="memberprice<?=$pricescnt?>" onkeypress="return decimal(event,this.value)" class="form-control prices" placeholder="<?=Member_label?> Price" name="memberprice[<?=$productcombination[$key][0]['membervariantid']?>]" value="<?=$productcombination[$key][0]['memberprice']?>">
												<?php
												}else{
												?>
												<input type="text" id="memberprice<?=$pricescnt?>" onkeypress="return decimal(event,this.value)" class="form-control prices" placeholder="<?=Member_label?> Price" name="newmemberprice[<?=$productcombination[$key][0]['priceid']?>]">
												<?php
												}
												?>
													
												</div>
											</div>
											<div class="col-md-12">
												<table class="table">
													<?php
													foreach ($value as $key1=>$value1) {
													?>
														<tr>
															<td><?=$value1['attributename']?></td>
															<td><?=$value1['variantname']?></td>
														</tr>
													<?php } ?>
												</table>
											</div>
										</div>
										
									</div>
								<?php
										}
									}
								}else{
								?>
								<div class="row">
									<div class="col-md-5">
										<div class="form-group" for="universalprice" id="universalprice_div">Price
											<input type="text" id="universalprice" onkeypress="return decimal(event,this.value)" class="form-control prices" disabled="" placeholder="Price" name="universalprice" value="<?=$productdata['price'];?>">
										</div>
									</div>
									<div class="col-md-5 col-md-offset-1">	<div class="form-group" for="memberuniversalprice" id="memberuniversalprice_div"><?=Member_label?> Price <input type="text" id="memberuniversalprice" onkeypress="return decimal(event,this.value)" class="form-control prices" placeholder="<?=Member_label?> Price" name="memberuniversalprice" value="<?=$productdata['memberprice'];?>">
										</div>
									</div>
								</div>
								<?php
								}
								?>
								
							<!--  -->
							</div>
							<div class="row">
								<!-- <label for="focusedinput" class="col-sm-3 control-label"></label> -->
								<div class="col-sm-8">
								  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="EDIT" class="btn btn-primary btn-raised">
								  <input type="reset" id="resetbtn" name="reset" value="RESET" class="btn btn-info btn-raised">
									<a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>member" title=<?=cancellink_title?>><?=cancellink_text?></a>
								</div>
							</div>
						</form>
					</div>
				</div>
		      </div>
		    </div>
		  </div>
		</div>

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->