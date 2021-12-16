<div id="productreview" style="display: grid;">
  <?php foreach ($productreviews as $row) { ?>
    <div>
        <div class="col-md-12 padd0">
            <div class="col-md-9">
                <h4 style="color:#454545"><b><?=ucwords($row['membername'])?></b></h4>
            </div>
            <div class="col-md-3 text-right">
                <h4 class="font-italic" style="display: contents;line-height: 1.6;">
                <b><?=$this->general_model->displaydate($row['createddate'])?></b>
                </h4>
                <div class="rating star-ico" style="float: right;margin-left: 10px;">
                    <img src="<?=FRONT_URL?>assets/images/star-<?=($row['rating']>=1?"on":"off")?>.png" alt="">
                    <img src="<?=FRONT_URL?>assets/images/star-<?=($row['rating']>=2?"on":"off")?>.png" alt="">
                    <img src="<?=FRONT_URL?>assets/images/star-<?=($row['rating']>=3?"on":"off")?>.png" alt="">
                    <img src="<?=FRONT_URL?>assets/images/star-<?=($row['rating']>=4?"on":"off")?>.png" alt="">
                    <img src="<?=FRONT_URL?>assets/images/star-<?=($row['rating']>=5?"on":"off")?>.png" alt="">
                </div>
            </div>
        </div>
        <div class="col-md-12 padd0">
            <div class="col-md-12">
                <p class="text-justify font-italic"><?=$row['message']?></p>
                <hr>
            </div>
        </div>
    </div>
    <? } ?>
    <div class="col-md-12 blog-box padd0">
        <div class="col-md-12 text-right">
        <?=$link;?>
        </div>
    </div>
</div>  