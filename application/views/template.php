<!DOCTYPE html>
<html lang="en">
    <?php $headerData = $this->frontend_headerlib->data(); ?>
    <head>
        <meta charset="utf-8"/>
        <!-- <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge"> -->
        <title><?php echo $title ?><?=($title!=""?" - ".COMPANY_NAME:COMPANY_NAME)?></title>
        <?php echo $headerData['meta_tags']; ?>

        <?php echo $headerData['favicon']; ?>
        <?php echo $headerData['stylesheets']; ?>
        <?php echo $headerData['plugins']; ?>
        <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900i%7CRoboto:300,400,500,700,900" rel="stylesheet"> 
        <script src="https://kit.fontawesome.com/bc6fe17638.js"></script>
        <script type="text/javascript">
            var SITE_URL = '<?php echo FRONT_URL ?>';
            var ACTION = '<?php if(isset($action)){ echo 1; }else{ echo 0; } ?>';
            var PRODUCT = '<?=PRODUCT?>';
            var CURRENCY_CODE = '<?=CURRENCY_CODE?>';
            var CATEGORY_SLUG = '<?=CATEGORY_SLUG?>';
            var loginsession = '<?php if(isset($this->session->userdata[base_url().'MEMBER_ID'])) { echo 1; }else { echo 0; }?>';
            var GST_PRICE = '<?=PRICE?>';
            var NOOFUSERINCHANNEL = '<?=NOOFUSERINCHANNEL?>'; 
            var Member_label= '<?=Member_label?>';
            var member_label= '<?=member_label?>';
            var MANAGE_DECIMAL_QTY='<?=MANAGE_DECIMAL_QTY?>';
        </script>
    </head>
    <?php 
    $headerclass="";
    if($page=="Success" || $page=="Failure" || $page=="no_result_found" || $page=="not_found"){ $headerclass="maintain-page"; }?>
    <body class="<?=$headerclass?>">
        <div class="mask">
            <div id="loader"></div>
        </div>
        <a href="https://wa.me/+91<?=explode(",",COMPANY_MOBILENO)[0]?>" target="_blank">
            <div class="phonecall">
                <i class="fab fa-whatsapp faa-tada animated "></i>
            </div>
        </a>
        <!-- END PAGE LOADER -->
        <!-- BEGIN HEADER -->
        <?php $this->load->view('includes/header');?>
        <!-- END HEADER -->  

        <?php $this->load->view($module);?>

        <!-- footer start here -->
        <?php $this->load->view('includes/footer');?>
        <!-- footer end here -->
                
        <?php echo $headerData['top_javascripts']; ?>
        <?php echo $headerData['javascript']; ?>
        <?php echo $headerData['javascript_plugins']; ?>
        <?php echo $headerData['bottom_javascripts']; ?>

    </body>
</html>
