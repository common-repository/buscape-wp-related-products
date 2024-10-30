<?php
$base_name = plugin_basename('buscape-wp-related-products/bwprp-settings.php');
$base_page = 'admin.php?page='.$base_name;

if ( isset( $_POST['action'] ) )
    $update_settings = $bwprp->update_settings( $_POST );
else
    $update_settings = '';

$bwprp_option = get_option('bwprp_options');
?>

<div class="wrap">
    <h2><?php _e('BuscaPé WP Related Products - Settings','BWPRP') ?></h2>

    <?php if ( $update_settings ) : ?>
        <div class="updated fade" id="message">
            <p> <?php _e('Settings updated successfully!','BWPRP') ?> </p>
        </div>
    <?php endif; ?>

    <form action="<?php echo $base_page; ?>" method="post" enctype="multipart/form-data">
        <table class="form-table" >
            <tbody>
                <?php if (! empty($bwprp_options) ) ?>
                        <tr valign="top" >
                            <th scope="row">
                                <label for="add_country"><?php _e('Country','BWPRP') ?></label>
                            </th>
                            <td>
                                <select name="add_country" id="add_country">
                                <?php
                                    $countries = $bwprp->get_countries();
                                    asort($countries);
                                    foreach ($countries as $key => $country) :
                                        if ($bwprp_option['country'] == $key)
                                            echo '<option value="' . $key . '" selected>' . $country . '</option>';
                                        else
                                            echo '<option value="' . $key . '">' . $country . '</option>';
                                    endforeach;
                                ?>
                                </select>
                            </td>
                        </tr>
                        <tr valign="top" >
                            <th scope="row">
                                <label for="add_application_id"><?php _e('Application ID','BWPRP') ?></label>
                            </th>
                            <td>
                                <input type="text"  class="regular-text" id="add_application_id" name="add_application_id"  value="<?php echo ( ! empty( $bwprp_option['application_id'] ) ) ? $bwprp_option['application_id'] : '' ?>" />  <span class="description"><?php _e('<a target="_blank" href="http://developer.buscape.com/admin/registration.html?lang=en">Get your Application ID</a>','BWPRP') ?></span>
                            </td>
                        </tr>
                        <tr valign="top" >
                            <th scope="row">
                                <label for="add_form"><?php _e('Display','BWPRP') ?></label>
                            </th>
                            <td>
                                <select name="add_form" id="add_form">
                                <?php
                                    if ($bwprp_option['form'] == __('Automatic','BWPRP') ) :
                                        echo '<option value="' . __('Automatic','BWPRP') . '" selected>' . __('Automatically','BWPRP') . '</option>';
                                        echo '<option value="' . __('Manually','BWPRP') . '">' . __('Manually','BWPRP') . '</option>';
                                    elseif ($bwprp_option['form'] == __('Manually','BWPRP') ) :
                                        echo '<option value="' . __('Manually','BWPRP') . '" selected>' . __('Manually','BWPRP') . '</option>';
                                        echo '<option value="' . __('Automatic','BWPRP') . '">' . __('Automatically','BWPRP') . '</option>';
                                    endif;
                                ?>
                                </select>
                            </td>
                        </tr>
            </tbody>
        </table>
        <p class="submit">
            <input type="submit" value="<?php _e('Save','BWPRP') ?> " class="button-primary" name="action" />
        </p>
    </form>
</div>