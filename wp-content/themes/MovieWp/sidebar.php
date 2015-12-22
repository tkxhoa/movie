<?php global $theme; ?>

<div id="sidebar-primary">

    <?php
        if(!dynamic_sidebar('sidebar_primary')) {
            /**
            * The primary sidebar widget area. Manage the widgets from: wp-admin -> Appearance -> Widgets 
            */
            $theme->hook('sidebar_primary');
        }
    ?>
    
</div><!-- #sidebar-primary -->


<div id="sidebar-secondary">

    <?php
        if(!dynamic_sidebar('sidebar_secondary')) {
            /**
            * The secondary sidebar widget area. Manage the widgets from: wp-admin -> Appearance -> Widgets 
            */
            $theme->hook('sidebar_secondary');
        }
    ?>
    
</div><!-- #sidebar-secondary -->