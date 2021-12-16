<style>
    .bottom-top i{
        position: absolute;
        top: 34%;
        left: 44%;
    }
	.chromescroll{
		overflow-y: auto;
    	max-height: 248px;
	}
	
	.chromescroll::-webkit-scrollbar {
		width: 5px;
	}
	
	.chromescroll::-webkit-scrollbar-thumb {
		background: #ddd;
	}

	.chromescroll::-webkit-scrollbar-track {
		background: #fff;
	}
</style>
<script>
	var MAXPRICE = '<?=$maxprice?>';
	var IS_CATEGORY = '0';
	var IS_TAGS = '0';
	var CATEGORY_SLUG = '';
	var PER_PAGE_OUR_PRODUCTS = '<?=PER_PAGE_OUR_PRODUCTS?>';
	var LISTING = '<?=LISTING?>';
</script>
<!-- slider start here -->
<div class="process-bg" style="<?php if(!empty($coverimage)){ echo  "background-image: url(".FRONTMENU_COVER_IMAGE.$coverimage.");"; }else{ echo "background-color:".DEFAULT_COVER_IMAGE_COLOR.";"; } ?>" aria-label="<?=$coverimage?>">
	<div class="container">
		<div class="row">
			<div class="col-sm-12 col-xs-12">
				<h1><?=$covertitle?></h1>
				<ul class="breadcrumbs list-inline">
					<li><a href="<?=FRONT_URL?>">Home</a></li>
					<li>Product</li>
				</ul>
			</div>
		</div>
	</div>
</div>
<!-- slider end here -->

<!-- shop code start here -->
<div class="shop">
	<div class="container">
		<div class="row">

			<div class="col-sm-3 rightbar"> <!-- hidden-xs --> 
				<?php $this->load->view('products-sidebar');?>
			</div>

			<div class="col-sm-9 col-md-9 col-xs-12">
				<div class="alert alert-dismissible fade in" id="alert" style="display:none;"></div>
				<div class="col-md-12 sort">
					<div class="row">
						<div class="col-md-7 col-sm-7 col-xs-12">
							<div class="row">
								<div class="col-md-6 col-xs-12 paddright">
									<div class="form-group">
										<label>Sort By:</label>
										<select id="orderbyprice" class="form-control selectpicker">
											<option value="2" selected="selected">Popularity</option>
											<option value="0">Price -- Low to High</option>
											<option value="1">Price -- High to Low</option>
										</select>
									</div>
								</div>
								<!-- <div class="col-md-6 col-xs-12">
									<div class="form-group">
										<label>Show:</label>
										<select class="form-control selectpicker">
											<option value="" selected="selected">10 items/ Page</option>
											<option value="">20 items/ Page</option>
										</select>
									</div>
								</div> -->
							</div>
						</div>
						<div class="col-md-5 col-sm-5 hidden-xs">
							<div class="btn-group btn-group-sm pull-right">
								<label>View as:</label>
								<button type="button" id="grid-view" class="btn btn-default listtype" data-toggle="tooltip"
										title="{{ button_grid }}"><i class="fa fa-th-large"></i></button>
								<button type="button" id="list-view" class="btn btn-default listtype" data-toggle="tooltip"
									title="{{ button_list }}"><i class="fa fa-th-list"></i></button>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12 p-n" id="productdata">
						<?php $this->load->view('products-ajax-data');?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- shop end here -->
