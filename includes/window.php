<?php
    $wpconfig = realpath("../../../../wp-config.php");
    if (!file_exists($wpconfig))  {
        echo "Could not found wp-config.php. Error in path :\n\n".$wpconfig ;
        die;
    }
    require_once($wpconfig);
    //require_once(ABSPATH.'/wp-admin/admin.php');
    global $wpdb;
    global $bwprp;
?>

<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>BuscaPé WP Related Products</title>
        <!-- 	<meta http-equiv="Content-Type" content="<?php// bloginfo('html_type'); ?>; charset=<?php //echo get_option('blog_charset'); ?>" /> -->
        <script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
        <script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
        <script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-content/plugins/buscape-wp-related-products/assets/js/tinymce.js"></script>
        <base target="_self" />
    </head>
    <body id="link" onload="tinyMCEPopup.executeOnLoad('init();');document.body.style.display='';" style="display: none">
        <!-- <form onsubmit="insertAction();return false;" action="#" autocomplete="off">-->
        <form name="bwprp" action="#">
            <table border="0" cellpadding="4" cellspacing="0">
                <tr>
                    <td nowrap="nowrap"><label for="bwprp_main"><?php _e("Select Category", 'BWPRP'); ?></label></td>
                    <td>
                        <select id="bwprp_categories" name="bwprp_main" style="width: 200px">
                            <?php 
                                $categories = $bwprp->get_categories();
                                if( $categories ) :
                                    foreach( $categories as $category ) : ?>
                                        <option value="<?php echo $category['id']; ?>"><?php echo $category['name'];  ?></option>
                                    <?php endforeach; ?>
                                <?php else :?>
                                    <option value=""><?php _e('No category until the moment.', 'BWPRP'); ?></option>
                                <?php endif; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                   <td nowrap="nowrap"><label for="bwprp_keyword"><?php _e("Keyword by commas", 'BWPRP'); ?></label></td>
                   <td>
                        <input type="text" id="bwprp_keyword" name="bwprp_keyword" style="width: 200px"/>
                   </td>
                </tr>
            </table>
            <div class="mceActionPanel">
            <p>
                <div style="float: left">
                    <input type="button" id="cancel" name="cancel" value="<?php _e("Cancel", 'BWPRP'); ?>" onclick="tinyMCEPopup.close();" />
                </div>
                <div style="float: right">
                    <input type="submit" id="insert" name="insert" value="<?php _e("Insert", 'BWPRP'); ?>" onclick="insertBWPRPcode();" />
                </div>
            </p>
            </div>
        </form>
    </body>
</html>