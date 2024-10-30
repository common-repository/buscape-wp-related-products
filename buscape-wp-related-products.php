<?php
/*
Plugin Name: BuscaPé WP Related Products
Plugin URI: http://developer.buscape.com/blog/aplicativos/buscape-wp-related-products/
Description: BuscaPé Gets related products on posts.
Author: Apiki
Version: 0.1
Author URI: http://apiki.com/
*/
include_once WP_PLUGIN_DIR . '/buscape-wp-related-products/includes/Apiki_Buscape_API.php';

class Buscape_Wp_Related_Products
{
    function Buscape_Wp_Related_Products()
    {
        $bwprp_settings = $this->get_settings();

        add_action( 'activate_buscape-wp-related-products/buscape-wp-related-products.php', array( &$this ,'install' ) );
        add_action( 'admin_menu', array( &$this, 'menu' ) );
        add_action( 'init', array( &$this, 'bwprp_addbuttons' ) );
        add_action( 'init', array( &$this, 'textdomain') );
        add_action( 'admin_notices', array( &$this, 'alert_required_settings' ) );
        add_shortcode( 'bwprp', array( &$this,'bwprp_shortcode') );
        
        if ( ( $bwprp_settings['form'] == 'Automatic' ) && ( !empty($bwprp_settings['application_id']) ))
            add_filter('the_content', array(&$this, 'get_tags') );
    }

    function textdomain()
    {
        load_plugin_textdomain( 'BWPRP', false , 'buscape-wp-related-products/languages' );
    }

    function install()
    {
       $role = get_role( 'administrator' );
       if( !$role->has_cap( 'mananger_bwprp' ) ) :
            $role->add_cap( 'mananger_bwprp' );
        endif;

        $bwprp_options = array('country' => 'BR', 'application_id' => '', 'form' => 'Automatic');
        add_option('bwprp_options',$bwprp_options);
    }

    function menu()
    {
        if ( function_exists('add_menu_page') )
            add_menu_page(__('BuscaPé WP Related Products','BWPRP'),__('BuscaPé WP Related Products','BWPRP'), 'mananger_bwprp', 'buscape-wp-related-products/bwprp-settings.php' );
    }

    function get_countries()
    {
        $country = array('BR' => __('Brazil','BWPRP'), 'AR' => __('Argentina','BWPRP'), 'CL' => __('Chile','BWPRP'), 'CO' => __('Colombia','BWPRP'),
                         'MX' => __('Mexico','BWPRP'), 'PE' => __('Peru','BWPRP'),      'VL' => __('Venezuela','BWPRP'));
        return $country;
    }

    function alert_required_settings()
    {
        $settings_page = admin_url( 'admin.php?page=buscape-wp-related-products/bwprp-settings.php' );
        $bwprp_settings = $this->get_settings();
        if ( empty( $bwprp_settings['application_id'] ) and strpos(esc_url($_SERVER['REQUEST_URI']), 'bwprp-settings') === false )
            printf( '<div class="updated"><p>%s</p></div>', sprintf( __( 'BuscaPé WP Related Products requires settings. Go to <a href="%s">Settings page</a> to configure it.', 'BWPRP' ), $settings_page ) );
    }

    function update_settings()
    {
        extract($_POST, EXTR_SKIP);

        $bwprp_options = array('country' => $add_country, 'application_id' => $add_application_id, 'form' => $add_form);
        update_option('bwprp_options', $bwprp_options);

        return true;
    }

    function get_settings ()
    {
        return $bwprp_options = get_option('bwprp_options');
    }


    function get_tags ($content)
    {
        $tags = get_the_tags( get_the_ID() );

        if ( !empty($tags) ) :
            $products_show = '';

            $bwprp_settings = $this->get_settings();
            $objApikiBuscapeApi = new Apiki_Buscape_API( $bwprp_settings['application_id'] , $bwprp_settings['country'], 'json',  true );

            $i = 0;
            foreach ($tags as $tag) :
                if ($i == 0)
                    $array_products['keyword'] = $tag->name;
                else
                    $array_products['keyword'] .= ',' . $tag->name;
                $i++;
            endforeach;
            $return_buscape = json_decode($objApikiBuscapeApi->findProductList($array_products),True);

            foreach ((array)$return_buscape['product'] as  $return_buscape_nivel2) :
                foreach ((array)$return_buscape_nivel2 as $return_buscape_nivel3) :
                    foreach ((array)$return_buscape_nivel3 as $key => $value) :
                        if ($key == 'thumbnail')
                            $thumbnail = $value['url'];
                        else if ($key == 'productname')
                          $name_product = $value;
                        else if ($key == 'links')
                            $link = $value[0]['link']['url'];

                        if ( (! empty($thumbnail)) && (! empty ($name_product)) && (! empty($link) ) ) :
                            $products_return[] = array('thumbnail' => $thumbnail,
                                                       'link'      => $link,
                                                       'name'      => $name_product);
                            $thumbnail    = '';
                            $name_product = '';
                            $link         = '';
                        endif;
                    endforeach;
                endforeach;
            endforeach;
            
            $products_show = '<div id="product_list">';

            $_products_return = array_slice( $products_return, 0, 3 );

            foreach ($_products_return as $key => $product )
                $products_show .= '<a target="_blank" href="' . $product['link'] .'" ><img src="' . $product['thumbnail'] . '"></a>';

            $products_show .= '</div>';

            return $content . '<br/>' . $products_show;
        else :
            return $content;
        endif;


    }

    function get_categories() 
    {
        $bwprp_settings     = $this->get_settings();
        $objApikiBuscapeApi = new Apiki_Buscape_API( $bwprp_settings['application_id'] , $bwprp_settings['country'], 'json',  true );
        $return_categories  = json_decode($objApikiBuscapeApi->findCategoryList( array( 'categoryId' => 0 ) ),True);

        foreach ($return_categories['subcategory'] as $return_categories_nivel1)
            foreach ($return_categories_nivel1 as $return_categories_nivel2)
                foreach ($return_categories_nivel2 as $key => $value) :
                    if ($key == 'id')
                        $id = $value;
                    elseif ($key == 'name')
                        $name = $value;

                    if ( ( !empty($id) ) && ( !empty($name) ) ) :
                        $lista_categorias[] = array('id'   => $id,
                                                    'name' => $name);
                        $id   = '';
                        $name = '';
                    endif;
                endforeach;
        return $lista_categorias;

    }

    function bwprp_shortcode($atts)
    {
        extract($atts);

        if ( ( !empty($cat) ) || ( !empty($keywords) ) )
            return $this->get_embedded( $cat, $keywords);
        else
            return '';
    }

    function get_embedded($cat, $keywords)
    {
        $products_show = '';

        $bwprp_settings = $this->get_settings();
        $objApikiBuscapeApi = new Apiki_Buscape_API( $bwprp_settings['application_id'] , $bwprp_settings['country'], 'json',  true );

        $array_products = array('categoryId' => $cat,
                                'keyword'    => $keywords);

        $return_buscape = json_decode($objApikiBuscapeApi->findProductList($array_products),True);

        if ( !empty($return_buscape['product']) ) :
            foreach ((array)$return_buscape['product'] as  $return_buscape_nivel2) :
                foreach ((array)$return_buscape_nivel2 as $return_buscape_nivel3) :
                    foreach ((array)$return_buscape_nivel3 as $key => $value) :
                        if ($key == 'thumbnail')
                            $thumbnail = $value['url'];
                        else if ($key == 'productname')
                          $name_product = $value;
                        else if ($key == 'links')
                            $link = $value[0]['link']['url'];

                        if ( (! empty($thumbnail)) && (! empty ($name_product)) && (! empty($link) ) ) :
                            $products_return[] = array('thumbnail' => $thumbnail,
                                                       'link'      => $link,
                                                       'name'      => $name_product);
                            $thumbnail    = '';
                            $name_product = '';
                            $link         = '';
                        endif;
                    endforeach;
                endforeach;
            endforeach;
        elseif (! empty($return_buscape['category']) ) :
            foreach ((array)$return_buscape['category'] as  $key => $value) :
                if ($key == 'thumbnail')
                    $thumbnail = $value['url'];
                else if ($key == 'name')
                    $name_product = $value;
                else if ($key == 'links')
                    $link = $value[0]['link']['url'];

                if ( (! empty($thumbnail)) && (! empty ($name_product)) && (! empty($link) ) ) :
                    $products_return[] = array('thumbnail' => $thumbnail,
                                               'link'      => $link,
                                               'name'      => $name_product);
                    $thumbnail    = '';
                    $name_product = '';
                    $link         = '';
                endif;
            endforeach;
        endif;

        if (! empty ($products_return)) :

            $products_show = '<div id="product_list">';
            $_products_return = array_slice( $products_return, 0, 3 );

            foreach ($_products_return as $key => $product )
                $products_show .= '<a target="_blank" href="' . $product['link'] .'" ><img src="' . $product['thumbnail'] . '"></a>';

            $products_show .= '</div>';

            return $products_show;
        endif;
    }


    function bwprp_addbuttons()
    {
        add_filter('mce_external_plugins', array( &$this, 'add_bwprp_tinymce_plugin'), 5);
	add_filter('mce_buttons', array( &$this, 'register_bwprp_button') , 5);
    }

    function register_bwprp_button($buttons)
    {
        array_push($buttons, "separator", "bwprp");
        return $buttons;
    }

    function add_bwprp_tinymce_plugin($plugin_array)
    {
        $plugin_array['bwprp'] = get_option('siteurl').'/wp-content/plugins/buscape-wp-related-products/assets/js/editor_plugin.js';
        return $plugin_array;
    }
}

$bwprp = New Buscape_Wp_Related_Products();
?>