<style type="text/css">
  .productvariantdiv{
    box-shadow: 0px 1px 6px #333;
    padding: 10px;
    margin-bottom: 20px;
	/* margin-right: 15px;
	width: 30%; */
	float: left;
  }

  .bordertable td{
	border-top: 1px solid #d0d0d0 !important;
  }
  .scroll-product-detail{
  	position: sticky;
    top: 70px;
    bottom: 0;
  }
</style>
<div class="page-content">
    <div class="page-heading">            
        <h1><?=$this->session->userdata(base_url().'submenuname')?> Detail</h1>            
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?=$this->session->userdata(base_url().'submenuname')?> Detail</li>
            </ol>
    	</small>
    </div>
	<?php $productsections='';?>
    <div class="container-fluid">
                                    
    <div data-widget-group="group1">
      <div class="row">
	  	<div class="col-md-12">
			<div class="panel panel-default border-panel">
				<div class="panel-heading">
					<div class="col-md-12 col-xs-9 col-sm-6 p-n">
						<h2 style="font-size: 13px;"><b>Product Name : </b><?=ucwords($productdata['name'])." | ".$productdata['category'].($productdata['brandname']!=""?(" (".$productdata['brandname'].")"):"");?></h2>
					</div>
					<!-- <div class="col-md-6 col-sm-6 col-xs-3 p-n text-right">
					</div> -->
				</div>
			</div>
		</div>
        <div class="col-md-12">
          <div class="panel panel-default border-panel">
			<div class="panel-body p-n pt-1">
				<div class="tab-container tab-default m-n">
					<ul class="nav nav-tabs">
						<li class="dropdown pull-right tabdrop hide">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-angle-down"></i> </a><ul class="dropdown-menu"></ul>
						</li>
						<li class="active">
							<a id="firsttab" href="#productdetailtab" data-toggle="tab" aria-expanded="false">Product Details<div class="ripple-container"></div></a>
						</li>
						<?php if($productdata['isuniversal']==0){ ?>
						<li class="">
							<a href="#varianttab" data-toggle="tab" aria-expanded="false">Variant Details<div class="ripple-container"></div></a>
						</li>
						<?php } ?>
						<?php
						$policytab=0;
						if(!empty($productdata['returnpolicytitle']) || !empty($productdata['replacementpolicytitle']) || !empty($productdata['returnpolicydescription']) || !empty($productdata['replacementpolicydescription'])){
							$policytab=1;
						}
						if($policytab==1){?>
						<li class="">
							<a href="#policytab" data-toggle="tab" aria-expanded="false">Policy<div class="ripple-container"></div></a>
						</li>
						<?php } ?>
					</ul>
					<div class="tab-content pb-n pl-sm pr-sm">
						<div class="tab-pane active" id="productdetailtab">
							<div class="row">
								<div class="col-md-5 scroll-product-detail">

									<div id="myCarousel" class="carousel slide" data-ride="carousel">
									<!-- Indicators -->
									<ol class="carousel-indicators">
									<?php
									for ($i=0; $i < count($productfile); $i++) {
									?>
										<li data-target="#myCarousel" data-slide-to="<?=$i?>" class="<?php if($i==0){ echo "active"; } ?>"></li>
									<?php
									}
									?>
									</ol>

									<!-- Wrapper for slides -->
									<div class="carousel-inner">
										<?php for ($i=0; $i < count($productfile); $i++) { 
											if (!file_exists(PRODUCT_PATH.$productfile[$i]['filename'])) {
												$img = PRODUCT.PRODUCTDEFAULTIMAGE;
												$alt = PRODUCTDEFAULTIMAGE;
											}else{
												$img = PRODUCT.$productfile[$i]['filename'];
												$alt = $productfile[$i]['filename'];
											}
										?>
										<div class="item <?php if($i==0){ echo "active"; } ?>">
										<img src="<?=$img?>" alt="<?=$alt?>">
										</div>
										<?php } ?>
									</div>

									<!-- Left and right controls -->
									<a class="left carousel-control" href="#myCarousel" data-slide="prev">
										<span class="glyphicon glyphicon-chevron-left"></span>
										<span class="sr-only">Previous</span>
									</a>
									<a class="right carousel-control" href="#myCarousel" data-slide="next">
										<span class="glyphicon glyphicon-chevron-right"></span>
										<span class="sr-only">Next</span>
									</a>
									</div>
									<div>          
								</div>
								</div>
								<div class="col-md-7">
									<table class="table table-striped bordertable">
										<tr>
											<td width="30%"><b>Name</b></td>
											<td><?=wordwrap(ucwords($productdata['name']),50,"<br>\n",TRUE);?></td>
										</tr>
										<tr>
											<td><b>Category</b></td>
											<td><?=ucwords($productdata['category']);?></td>
										</tr>
										<tr>
											<td><b>Brand</b></td>
											<td><?=($productdata['brandname']!=""?$productdata['brandname']:"-")?></td>
										</tr>
										<tr>
											<td><b>Unit</b></td>
											<td><?=($productdata['unitname']!=""?$productdata['unitname']:"-")?></td>
										</tr>
										<tr>
											<td><b>Quantity Type</b></td>
											<td><?php
												if($productdata['quantitytype']==0){
													echo "Range Base";
												}else{
													echo "Multiplication";
												}
											?></td>
										</tr>
										<tr>
											<td><b>Price</b></td>
											<td><?php
											if(number_format($productpricesdata['minprice'],2,'.','') == number_format($productpricesdata['maxprice'],2,'.','')){
												echo CURRENCY_CODE.number_format($productpricesdata['minprice'], 2, '.', ',');
											}else{
												echo CURRENCY_CODE.number_format($productpricesdata['minprice'], 2, '.', ',')." - ".number_format($productpricesdata['maxprice'], 2, '.', ',');
											}

											if($productdata['isuniversal']==1 && !empty($productcombination) && $productcombination[$productdata['priceid']]['pricetype']==1 && !empty($productcombination[$productdata['priceid']]['multipleprice'])){ ?>
												<br><br>
												<?php foreach($productcombination[$productdata['priceid']]['multipleprice'] as $ik=>$pmp){ ?>
														<p style="font-size: 13px;"><?='- '.$pmp['quantity'].($productdata['quantitytype']==0?'+':'')." Qty = "?>
														<?php 
														$price = $pmp['price'];
														if($pmp['discount'] > 0){
															$price = $pmp['price'] - ($pmp['price'] * $pmp['discount'] / 100);
															$discount = ((int)$pmp['discount'] == $pmp['discount'])?(int)$pmp['discount']:$pmp['discount'];
														}
														echo '<span style="font-weight:bold;">'.CURRENCY_CODE.numberFormat($price,2,',').'</span>';
														
														if($pmp['discount'] > 0){
															if($pmp['discount']<100){
																$save = $discount.'% Off';
															}else{
																$save = 'Free';
															}
															echo " <span style='text-decoration: line-through;font-weight:bold;color:#777171;'>".CURRENCY_CODE.$pmp['price']."</span>";
															echo " <span style='color:green;'>".$save."</span>";
														} ?>
														</p>
												<?php } ?>
											<?php }


											/* if($productdata['isuniversal']==1){
												echo implode(',',$productprices);
											}else{
												if(count($productprices)>0){
													if(number_format(min($productprices),2,'.','') == number_format(max($productprices),2,'.','')){
														echo number_format(min($productprices), 2, '.', ',');
													}else{
														echo number_format(min($productprices), 2, '.', ',')." - ".number_format(max($productprices), 2, '.', ',');
													}
												}
											} */
											?></td>
										</tr>
										<?php if($productdata['pricetype']==0 && $productdata['isuniversal']==1) { ?>
										<tr>
											<td><b>Discount (%)</b></td>
											<td><?=number_format($productdata['discount'],2,'.','')?></td>
										</tr>
										<?php } ?>
										<tr>
											<td><b>Stock</b></td>
											<td><?php
												if($productdata['isuniversal']==1){
													echo $productdata['universalstock'];
												}else{
													echo $productdata['universalstock'];
												}
											?></td>
										</tr>
										<tr>
											<td><b>Tax</b></td>
											<td><?=($productdata['tax']!=""?$productdata['tax']:"-")?></td>
										</tr>
										<tr>
											<td><b>HSN code</b></td>
											<td><?=($productdata['hsncode']!=""?$productdata['hsncode']:"-")?></td>
										</tr>
										<tr>
											<td><b>Priority</b></td>
											<td><?=$productdata['priority']?></td>
										</tr>
										<tr>
											<td><b>Product Section</b></td>
											<td><?=($productsections!=""?$productsections:"-")?></td>
										</tr>
										<tr>
											<td><b>Product Type</b></td>
											<td><?php
												if($productdata['producttype']==1){
													echo "Offer Product";
												}else if($productdata['producttype']==2){
													echo "Raw Product";
												}else if($productdata['producttype']==3){
													echo "Semi-Finish Product";
												}else{
													echo "Regular Product";
												}
											?></td>
										</tr>
										<?php if(REWARDSPOINTS==1) { 
											if($productdata['isuniversal']==0){ ?>
											<tr>
												<td><b>Points Priority</b></td>
												<td><?=($productdata['pointspriority']==0?'Universal Point':"Variant Point")?></td>
											</tr>
											<?php } ?>
											<tr>
												<td><b>Points for Seller</b></td>
												<td><?=$productdata['pointsforseller']?></td>
											</tr>
											<tr>
												<td><b>Points for Buyer</b></td>
												<td><?=$productdata['pointsforbuyer']?></td>
											</tr>
										<?php } ?>
										<?php if($productdata['isuniversal']==1){ ?>
										<tr>
											<td><b>SKU</b></td>
											<td><?=($productdata['sku']!=""?$productdata['sku']:"-")?></td>
										</tr>
										<tr>
											<td><b>Weight (kg)</b></td>
											<td><?=($productdata['weight']!=""?$productdata['weight']:"-")?></td>
										</tr>
										<?php } ?>
										<tr>
											<td><b>Tag</b></td>
											<td><?=($productdata['tagsname']!=""?$productdata['tagsname']:"-")?></td>
										</tr>
										<tr>
											<td><b>Related Products</b></td>
											<td><?=($relatedproducts!=""?$relatedproducts:"-")?></td>
										</tr>
										<tr>
											<td><b>Product Display on Front</b></td>
											<td><?=($productdata['productdisplayonfront']==1?'Yes':"No")?></td>
										</tr>
										<tr>
											<td><b>Product Catalog</b></td>
											<td>
											<?php if($productdata['catalogfile']!=""){ ?>

												<a class="<?=downloadlblbtn_class?>" href="<?=CATALOG_IMAGE.$productdata['catalogfile']?>" title="<?=downloadlblbtn_title?>" download><?=downloadlblbtn_text?></a>
											<?php }else{ 
													echo "-";
											}?>
											</td>
										</tr>
										<tr>
											<td><b>Status</b></td>
											<td><?php 
											if($productdata['status']==1){
												echo "<label class='label label-success'>Active</label>";
											}else{
												echo "<label class='label label-success'>Inactive</label>";
											} ?></td>
										</tr>
										<tr>
											<td><b>Created Date</b></td>
											<td><?php echo $this->general_model->displaydatetime($productdata['createddate']); ?></td>
										</tr>
									</table>
								</div>
							</div>
							<div class="row mb-md">
								<div class="col-md-12">
									<h4><b>Short Description</b></h4>
									<hr/>
									<div class="col-md-12">
										<p><?=ucfirst($productdata['shortdescription'])?></p>
									</div>
								</div>
							</div>
							<div class="row mb-md">
								<div class="col-md-12">
									<h4><b>Description</b></h4><hr/>
									<div class="col-md-12">
										<p><?=ucfirst($productdata['description'])?></p>
									</div>
								</div>
							</div>
							<?php if($productdata['isuniversal']==1){ ?>
							<div class="row mb-md">
								<div class="col-md-12"><hr/></div>
								<div class="col-md-3 text-center">
									<?php if($productdata['sku']!=''){ 
										$qrtext = "SKU:".$productdata['sku']."|".$productdata['id']."|".$productdata['priceid'];
										?>
										<?php echo "<img style='width: 150px;' src='".str_replace("{encodeurlstring}",$qrtext,GENERATE_QRCODE_SRC)."' class=''>"; ?>
									<?php }else{ ?>
										<label class="label label-danger p-xs">QR Code Not Available.</label>
									<?php } ?>
								</div>
								<?php if($productdata['barcode']!=''){ ?>
									<div class="col-md-6 pt-xl">
										<img src="<?=ADMIN_URL.'product/set-barcode/'.$productdata['barcode']?>" style="max-width:100%;">
									</div>
								<?php }else{ ?>
									<div class="col-md-6">
										<label class="label label-danger p-xs">Barcode Not Available.</label>
									</div>
								<?php } ?>
							</div>
							<?php } ?>
						</div>
						<?php if($productdata['isuniversal']==0){ ?>
						<div class="tab-pane" id="varianttab">
							<?php if($productdata['isuniversal']==0){ ?>
								<div class="row m-n">
									<?php 
									$in=0;
									foreach ($productcombination as $pckey => $pc) { 
										if($in%3==0){
											echo '<div class="col-md-12 p-n">';
										}
										?>

										<div class="col-md-4 pl-sm pr-sm">
											<div class="productvariantdiv border-panel">
												<h4 style="font-size: 15px;"><b>Price (<?=CURRENCY_CODE?>) : </b><?=$pc['price']?>  (Stock : <?=$pc['stock']?>)</h4>
												<?php if($pc['pricetype']==1){ ?>
												<table class="table table-striped mb-n" style="border-top: 2px solid #ddd;">
													<tr>
														<td>
														<p style="font-weight: bold;">Price</p>
															<?php foreach ($pc['multipleprice'] as $mp) { ?>
																<p style="font-size: 13px;"><?='- '.$mp['quantity'].($productdata['quantitytype']==0?'+':'')." Qty = "?>
																	<?php 
																	$price = $mp['price'];
																	if($mp['discount'] > 0){
																		$price = $mp['price'] - ($mp['price'] * $mp['discount'] / 100);
																		$discount = ((int)$mp['discount'] == $mp['discount'])?(int)$mp['discount']:$mp['discount'];
																	}
																	echo '<span style="font-weight:bold;">'.CURRENCY_CODE.numberFormat($price,2,',').'</span>';
																	
																	if($mp['discount'] > 0){
																		if($mp['discount']<100){
																			$save = $discount.'% Off';
																		}else{
																			$save = 'Free';
																		}
																		echo " <span style='text-decoration: line-through;font-weight:bold;color:#777171;'>".CURRENCY_CODE.$mp['price']."</span>";
																		echo " <span style='color:green;'>".$save."</span>";
																	} ?>
																</p>
															<?php } ?>
														</td>
													</tr>
												</table>
												<?php } ?>
												<table class="table table-striped" style="border-top: 2px solid #ddd;border-bottom: 2px solid #ddd;">
												<?php foreach ($pc['variants'] as $vv) { ?>
													<tr>
														<td width="50%" ><b><?=$vv['variantname']?></b></td>
														<td ><?=$vv['variantvalue']?></td>
													</tr>
													
												<?php } ?>
												</table>
												<?php if(REWARDSPOINTS==1) { ?>
												<div class="col-md-6 p-n">
													<span style="width: 75%;float: left;"><b>Points for Seller : </b></span>
													<span><?=$pc['pointsforseller']?></span>
												</div>
												<div class="col-md-6 p-n">
													<span style="width: 75%;float: left;"><b>Points for Buyer : </b></span>
													<span><?=$pc['pointsforbuyer']?></span>
												</div>
												<?php } ?>
												<?php if($pc['sku']!=''){ ?>
												<div class="col-md-12 p-n">
													<hr>
													<span style="width: 31%;float: left;"><b>SKU</b></span>
													<span style="width: 7%;float: left;"><b>&nbsp;:&nbsp;</b></span>
													<span><?=$pc['sku']?></span>
												</div>
												<?php } ?>
												<?php if($pc['weight']!=0){ ?>
												<div class="col-md-12 p-n">
													<hr>
													<span style="width: 31%;float: left;"><b>Weight (kg)</b></span>
													<span style="width: 7%;float: left;"><b>&nbsp;:&nbsp;</b></span>
													<span><?=$pc['weight']?></span>
												</div>
												<?php } ?>
												<div class="col-md-12 text-center p-n">
													<hr>
													<?php if($pc['sku']!=''){ 
															$qrtext = "SKU:".$pc['sku']."|".$productdata['id']."|".$pckey;
															echo "<img style='width: 120px;' src='".str_replace("{encodeurlstring}",$qrtext,GENERATE_QRCODE_SRC)."' class=''>"; ?>
													<?php }else{ ?>
														<label class="label label-danger p-xs">QR Code Not Available.</label>
													<?php } ?>
												</div>
												<div class="col-md-12 text-center p-n">
													<hr>
													<?php if($pc['barcode']!=''){ ?>
														<img src="<?=ADMIN_URL.'product/set-barcode/'.$pc['barcode']?>" style="max-width:100%;">
													<?php }else{ ?>
														<label class="label label-danger p-xs">Barcode Not Available.</label>
													<?php } ?>
												</div>
											</div>
										</div>
										<?php 
										if($in%3==2 || $in==count($productcombination)-1){
											echo '</div>';
										}
										$in++;
									} ?>
								</div>
							<?php } ?>
						</div>
						<?php } ?>
						<?php if($policytab==1){?>
						<div class="tab-pane" id="policytab">
							<div class="row mb-md">
								<div class="col-md-12">
									<h4><?=ucfirst($productdata['returnpolicytitle'])?></h4>
									<hr/>
									<div class="col-md-12">
										<p><?=ucfirst($productdata['returnpolicydescription'])?></p>
									</div>
								</div>
							</div>
							<div class="row mb-md">
								<div class="col-md-12">
									<h4><?=ucfirst($productdata['replacementpolicytitle'])?></h4>
									<hr/>
									<div class="col-md-12">
										<p><?=ucfirst($productdata['replacementpolicydescription'])?></p>
									</div>
								</div>
							</div>
						</div>
						<?php } ?>
					</div>
				</div>
         	</div>
          </div>
        </div>
      </div>
    </div>

    </div> <!-- .container-fluid -->
