<?php echo'<?xml version="1.0" encoding="UTF-8" ?>' ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc><?php echo base_url();?></loc>
        <priority>1.0</priority>
        <changefreq>Daily</changefreq>
    </url>


    <!-- Sitemap -->
    <?php foreach($sitemapdata as $sitemap) { ?>
    <url>
        <loc><?php echo FRONT_URL.$sitemap['slug']?></loc>
        <priority>
        <?php 
            if($sitemap['priority']==0){
                echo "0.0";
            }elseif($sitemap['priority']==1){
                echo "0.1";
            }elseif($sitemap['priority']==2){
                echo "0.2";
            }elseif($sitemap['priority']==3){
                echo "0.3";
            }elseif($sitemap['priority']==4){
                echo "0.4";
            }elseif($sitemap['priority']==5){
                echo "0.5";
            }elseif($sitemap['priority']==6){
                echo "0.6";
            }elseif($sitemap['priority']==7){
                echo "0.7";
            }elseif($sitemap['priority']==8){
                echo "0.8";
            }elseif($sitemap['priority']==9){
                echo "0.9";
            }elseif($sitemap['priority']==10){
                echo "1.0";
            }
        ?>
        </priority>
        <changefreq>
        <?php 
            if($sitemap['changefrequency']==0){
                echo "Always";
            }elseif($sitemap['changefrequency']==1){
                echo "Hourly";
            }elseif($sitemap['changefrequency']==2){
                echo "Daily";
            }elseif($sitemap['changefrequency']==3){
                echo "Weekly";
            }elseif($sitemap['changefrequency']==4){
                echo "Monthly";
            }elseif($sitemap['changefrequency']==5){
                echo "Yearly";
            }elseif($sitemap['changefrequency']==6){
                echo "Never";
            }
        ?>
        </changefreq>
    </url>
    <?php } ?>


</urlset>