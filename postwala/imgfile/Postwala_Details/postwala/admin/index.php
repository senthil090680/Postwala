<?php
require_once('access.php');
require_once('header.php');
?>
<h2><?php _e("Administration");?></h2>
<div class="form-tab"><?php _e("Quick View");?></div>
<div class="clear"></div>
<div class="dashboard">
    <ul>
    <li><?php _e("Version");?>: <?php echo VERSION;?>
        <?php _e("Language");?>: <?php echo LANGUAGE;?>
        <?php _e("Theme");?>: <?php echo THEME;?></li>
        <br/>
    <li><?php echo T_("Total Ads").': '.totalAds();?>
    <li><?php echo T_("Total Views").': '.totalViews();?></li>
    </ul>
</div>

<?php
require_once('footer.php');
?>