<?php
/**
 * Plugin Name: Clk.im Integrator
 * Plugin URI: clk.im
 * Description: Clk.im Link Shortner And Interstitial Adserver Integration Plugin
 * Version: 1.6
 * Author: Clk.im
 * Author URI: clk.im
 * License: GPL2
 */

# Define text domain
define('CLK_TEXTDOMAIN','clk-im-generator');

# Register Clk.im menu in WP
add_action('admin_menu', 'clk_create_menu') ;

/**
 * Plugin Activation
 */
function clk_activate() {

	update_option( 'selector', 'a' );
	$options = get_option( 'type' );
	$options['site'] = 1;
	update_option('type', $options);

}
register_activation_hook( __FILE__, 'clk_activate' );


/**
 * Load Textdomain
 * 
 * @return void
 */
function clk_load_textdomain() {
    load_plugin_textdomain(CLK_TEXTDOMAIN, FALSE, dirname( plugin_basename(__FILE__) ) . '/lang/' );
}
add_action('plugins_loaded', 'clk_load_textdomain');



/**
 * Set Admin menu
 * 
 * @return void
 */
function clk_create_menu() {
	add_menu_page( __('Clk.im Integration Plugin Settings',CLK_TEXTDOMAIN), __('Clk.im Settings',CLK_TEXTDOMAIN), 'administrator', __FILE__, 'clk_settings_page',plugins_url('link.png', __FILE__));
	add_action( 'admin_init', 'register_clksettings' );
}


/**
 * Register Settings
 * 
 * @return void
 */
function register_clksettings() {

	register_setting( 'clk-settings-group', 'api_key' );
    register_setting( 'clk-settings-group', 'branded_domain' );
    register_setting( 'clk-settings-group', 'selector' );
	register_setting( 'clk-settings-group', 'type' );
    register_setting( 'clk-settings-group', 'clkim_links_type' );
    register_setting( 'clk-settings-group', 'clkim_specific_domains' );
    register_setting( 'clk-settings-group', 'clkim_shorten_social' );
    register_setting( 'clk-settings-group', 'clkim_exclude_domains' );

}

/**
* Admin Settings Page
*/
function clk_settings_page() {
?>
<div class="wrap">
<h2>Clk.im Integration Plugin Settings</h2>

<form method="post" action="options.php">

<script>

jQuery(document).ready(function($){
    $('select[name="clkim_links_type"]').on('change',function(e){
       if ( $(this).val() == 'specific' ) {
           $('tr#tr-specific-domains').show();
       } else {
           $('tr#tr-specific-domains').hide();
       }
    });
});

function toggle(source) {
  checkboxes = document.getElementsByTagName("input");
  for(var i=0, n=checkboxes.length;i<n;i++) {
    checkboxes[i].checked = source.checked;
  }
}
</script>
    <?php settings_fields( 'clk-settings-group' ); ?>
    <?php do_settings_sections( 'clk-settings-group' ); ?>
    <table class="form-table">

        <tr valign="top">
            <th scope="row"><?php echo __('API Key',CLK_TEXTDOMAIN);?> </th>
            <td><input type="text" name="api_key" value="<?php echo esc_attr( get_option('api_key') ); ?>" /><?php echo __('Get this from',CLK_TEXTDOMAIN);?>  <a href="http://clk.im/user">http://clk.im/user</a></td>
        </tr>

        <tr valign="top">
            <th scope="row"><?php echo __('Branded Domain',CLK_TEXTDOMAIN);?> </th>
            <td><input type="text" name="branded_domain" value="<?php echo esc_attr( get_option('branded_domain') ); ?>" /><?php echo __('Your own private domain which is hosted on Clk.im',CLK_TEXTDOMAIN);?></td>
        </tr>

        <tr valign="top">
            <th scope="row"><?php echo __('Selector',CLK_TEXTDOMAIN);?></th>
            <td><input type="text" name="selector" value="<?php echo esc_attr( get_option('selector') ); ?>" /> <?php echo __("JQuery Selector to use. Default is 'a' which is all links. Leave as defualt to shorten and track all links.",CLK_TEXTDOMAIN);?>
            </td>
        </tr>

        <?php $options = get_option( 'type' ); ?>
        <tr valign="top">
            <th scope="row"><?php echo __('Types of page to use plugin on',CLK_TEXTDOMAIN);?>:</th>
            <td>
                <table>
                    <tr>
                        <td><input type="checkbox" onClick="toggle(this)" name="type[site]" value="1"<?php checked( isset( $options['site'] ) ); ?> /></td>
                        <td><?php echo __('Entire Site',CLK_TEXTDOMAIN);?></td>
                    </tr>
                    <tr>
                        <td><input type="checkbox" name="type[home]" value="1"<?php checked( isset( $options['home'] ) ); ?> />
                        <td><?php echo __('Home Page',CLK_TEXTDOMAIN);?> </td>
                    </tr>
                    <tr>
                        <td><input type="checkbox" name="type[page]" value="1"<?php checked( isset( $options['page'] ) ); ?> /></td>
                        <td><?php echo __('Pages',CLK_TEXTDOMAIN);?></td>
                    </tr>
                    </tr>
                    <tr>
                        <td><input type="checkbox" name="type[posts]" value="1"<?php checked( isset( $options['posts'] ) ); ?> /> </td>
                        <td><?php echo __('Posts',CLK_TEXTDOMAIN);?> </td>
                    </tr>
                    <tr>
                        <td><input type="checkbox" name="type[category]" value="1"<?php checked( isset( $options['category'] ) ); ?> /></td>
                        <td><?php echo __('Category Pages',CLK_TEXTDOMAIN);?> </td>
                    </tr>
                    <tr>
                        <td> <input type="checkbox" name="type[blog]" value="1"<?php checked( isset( $options['blog'] ) ); ?> /></td>
                        <td><?php echo __('Blog Page',CLK_TEXTDOMAIN);?></td>
                    </tr>
                    <tr>
                        <td> <input type="checkbox" name="type[tag]" value="1"<?php checked( isset( $options['tag'] ) ); ?> /></td>
                        <td><?php echo __('Tag Page',CLK_TEXTDOMAIN);?></td>
                    </tr>
                </table>
            </td>

        </tr>

        <?php $links = get_option( 'clkim_links_type' ); ?>
        <tr valign="top">
            <th scope="row"><?php echo __('Shorten Links',CLK_TEXTDOMAIN);?></th>
            <td valign="top">
                <table>
                    <tr>
                        <td style="padding:0">
                            <select name="clkim_links_type" id="">
                                <option value="all"<?php selected( isset( $links ) && $links == 'all' ); ?>><?php echo __('All Links',CLK_TEXTDOMAIN);?></option>
                                <option value="external"<?php selected( isset( $links ) && $links == 'external' ); ?>><?php echo __('External Links',CLK_TEXTDOMAIN);?></option>
                                <option value="internal"<?php selected( isset( $links ) && $links == 'internal' ); ?>><?php echo __('Internal Links',CLK_TEXTDOMAIN);?></option>
                                <option value="specific"<?php selected( isset( $links ) && $links == 'specific' ); ?>><?php echo __('Specific Domain Links (comma seperated)',CLK_TEXTDOMAIN);?></option>
                            </select>
                        </td>
                    </tr>
                    <tr id="tr-specific-domains" style="<?= ( isset($links) AND $links == 'specific' ) ? '' : 'display:none;' ?>">
                        <td colspan="2">
                            <textarea name="clkim_specific_domains" id="" cols="30" rows="10" placeholder="example.com,example2.com"><?php echo get_option('clkim_specific_domains') ?></textarea>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <?php $shorten_social = get_option( 'clkim_shorten_social' ); ?>
        <tr valign="top">
            <th scope="row"><?php echo __('Shorten Social Links',CLK_TEXTDOMAIN);?></th>
            <td valign="top">
                <table>
                    <tr>
                        <td style="padding:0">
                            <select name="clkim_shorten_social" id="">
                                <option value="0"<?php selected( isset( $shorten_social ) && $shorten_social == '0' ); ?>><?php echo __('No',CLK_TEXTDOMAIN);?></option>
                                <option value="1"<?php selected( isset( $shorten_social ) && $shorten_social == '1' ); ?>><?php echo __('Yes',CLK_TEXTDOMAIN);?></option>
                            </select>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <?php $exclude_domains = get_option( 'clkim_exclude_domains' ); ?>
        <tr valign="top">
            <th scope="row"><?php echo __('Exclude Domains:',CLK_TEXTDOMAIN);?></th>
            <td valign="top">
                <table>
                    <tr>
                        <td colspan="2">
                            <textarea name="clkim_exclude_domains" id="" cols="30" rows="10" placeholder="example.com,example2.com"><?php echo $exclude_domains ?></textarea>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

    </table>
    
    <?php submit_button(); ?>

</form>

</div>
<?php }


/**
 * Footer code
 * 
 * @return void
 */
function clk_footer() {

    $options = get_option('type');

    $show_code = FALSE;

    if( isset($options['site']) ) {

        # Global
        $show_code = TRUE;

    }
    elseif ( isset($options['home']) && is_home() ) {
        $show_code = TRUE;
    }
    elseif ( isset($options['page']) && is_page() ) {
        $show_code = TRUE;
    }
    elseif ( isset($options['posts']) && is_single() ) {
        $show_code = TRUE;
    }
    elseif ( isset($options['category']) && is_category() ) {
        $show_code = TRUE;
    }
    elseif ( isset($options['blog']) && is_front_page() && is_home() ) {
        $show_code = TRUE;
    }
    elseif( isset($options['tag']) && is_tag() ) {
        $show_code = TRUE;
    }


    if ( $show_code ) {

        $file = plugins_url('/js/clkim.js',__FILE__);

        $selector = get_option('selector');
        $api_key = get_option('api_key');
        $branded_domain = get_option('branded_domain');
        $links_type = get_option('clkim_links_type');
        $specific_domains = get_option('clkim_specific_domains');

        $shorten_social = get_option('clkim_shorten_social');
        $shorten_social = ( empty( $shorten_social ) ) ? 'false' : 'true';

        $exclude_domains = get_option('clkim_exclude_domains');

        echo <<<HTML

            <!-- Clk.im Shortner -->
<script type='text/javascript'>
        var clkim = {
            selector: '{$selector}',
            api: '{$api_key}',
            branded_domain: '{$branded_domain}',
            links_type: '{$links_type}',
            links_domains: '{$specific_domains}',
            shorten_social: {$shorten_social},
            exclude_domains: '{$exclude_domains}'
        }
    </script>
            <script src='{$file}'></script>
            <!-- end Clk.im Shortner -->

HTML;

    }
}

add_action('wp_footer', 'clk_footer');
