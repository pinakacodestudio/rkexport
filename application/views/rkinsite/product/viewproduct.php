<div class="page-content">
    <div class="page-heading">            
        <h1><?=$this->session->userdata(base_url().'submenuname')?> Detail</h1>            
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url(); ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?=$this->session->userdata(base_url().'submenuname')?> Detail</li>
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
              	<div class="row">
              		<div class="col-md-5">

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
						  	<?php for ($i=0; $i < count($productfile); $i++) { ?>
						    <div class="item <?php if($i==0){ echo "active"; } ?>">
						      <img src="<?=PRODUCT.$productfile[$i]['filename']?>" alt="Image">
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
              			<table class="table table-striped">
              				<tr>
              					<td><b>Name</b></td>
              					<td><?=wordwrap($productdata['name'],50,"<br>\n",TRUE);?></td>
              				</tr>
              				<tr>
              					<td><b>Price</b></td>
              					<td><?php
              					if($productdata['isuniversal']==1){
              						echo $productdata['price'];
              					}else{
              						if(count($productprices)>0){
              							echo min($productprices)." - ".max($productprices);
              						}
              					}
              					?></td>
              				</tr>
                      <tr>
                        <td><b>Discount</b></td>
                        <td><?=$productdata['discount']?> %</td>
                      </tr>
              				<tr>
              					<td><b>Stock</b></td>
              					<td><?php
              						if($productdata['isuniversal']==1){
              							echo $productdata['universalstock'];
              						}else{
              							echo "-";
              						}
              					?></td>
              				</tr>
              				<tr>
              					<td><b>Tax</b></td>
              					<td><?=$productdata['tax']?></td>
              				</tr>
              				<tr>
              					<td><b>Hsn code</b></td>
              					<td><?=$productdata['hsncode']?></td>
              				</tr>
              				<tr>
              					<td><b>Priority</b></td>
              					<td><?=$productdata['priority']?></td>
              				</tr>
              				<tr>
              					<td><b>Created Date</b></td>
              					<td><?php echo $this->general_model->displaydatetime($productdata['createddate']); ?></td>
              				</tr>
              				<tr>
              					<td><b>Status</b></td>
              					<td><?php 
              					if($productdata['status']==1){
              						echo "<label class='badge badge-success'>Active</label>";
              					}else{
              						echo "<label class='badge badge-success'>Inactive</label>";
              					} ?></td>
              				</tr>
              			</table>
              		</div>
              	</div>

              	<div class="row">
              		<div class="col-md-12">
              			<hr/><h4>Description : </h4>
              			<p><?=$productdata['description']?></p>
              		</div>
              	</div><hr/>
              	<?php
              	if($productdata['isuniversal']==0){
              	?>
              	<div class="row">
              			<?php
              				foreach ($productcombination as $pckey => $pc) {
              			?>
              			<div class="col-md-6">
              				<hr/><h4>Price : <?=$pc['price']?>  ( Stock : <?=$pc['stock']?> )</h4>
              					<table class="table table-striped">
              					<?php
              					foreach ($pc['variants'] as $vv) {
              					?>
              						<tr>
	              						<td width="50%"><b><?=$vv['variantname']?></b></td>
	              						<td><?=$vv['variantvalue']?></td>
              						</tr>
              					<?php
              					}
              					?>
              					</table><hr/>
              			</div>
              			<?php	
              				}
              			?>
              	</div>
              	<?php
              	}
              	?>
              </div>
        	</div>
          </div>
        </div>
      </div>
    </div>

    </div> <!-- .container-fluid -->
