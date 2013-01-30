<?php

/*
Plugin Name: Background Plugin
Plugin URI: wwww.http://charleslouisolivier.blogspot.ca/
Description: Allows to setup a custom background using Scott Robbin Backstretch - v2.0.0 js script.
Author: Charles Olivier + Scott Robbin
Version: beta
Author URI: http://wwww.charleslouisolivier.blogspot.ca/
Copyright (c) 2012 Scott Robbin; Licensed MIT
License: Personal use only. 
*/

// ---------------------------------------------------------------------------------------- Activation //

function my_plugin_activate() {
error_log("plugin activated");

// create database //
global $wpdb;
$table_name = $wpdb->prefix . "background";
$sql = "CREATE TABLE $table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  bgimage tinytext NOT NULL,
  UNIQUE KEY id (id)
);";
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$id = "1"; $bgimage="01.jpg";
$rows_affected = $wpdb->insert( $table_name, array( 'id' => $id, 'bgimage' => $bgimage ) );

}

register_activation_hook(__FILE__,"my_plugin_activate");




// ---------------------------------------------------------------------------------------- De-activation //



function my_plugin_deactivate() {
error_log("plugin deactivated");

//  remove database //
global $wpdb;
$table_name = $wpdb->prefix . "background";
$sql = "DROP TABLE". $table_name;
$wpdb->query($sql);
}
register_deactivation_hook(__FILE__,"my_plugin_deactivate");



// ---------------------------------------------------------------------------------------- Call css + script //



function my_plugin_admin_init() {
wp_register_style( 'background_stylesheet', plugins_url('bg.css', __FILE__) );
wp_register_script( 'google-script', 'http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js' );
wp_register_script( 'my-plugin-script', plugins_url('bg.js', __FILE__) );
}
function my_plugin_admin_styles() { wp_enqueue_style( 'background_stylesheet' ); }
function my_plugin_admin_scripts() { wp_enqueue_script( 'google-script' ) ; wp_enqueue_script( 'my-plugin-script' );}
add_action( 'admin_init', 'my_plugin_admin_init' );



// ---------------------------------------------------------------------------------------- Plugin-Menu //



function custom_bg() {
$page = add_theme_page( 'Custom Background', 'Background', 'manage_options', 'background-plugin','custom_bg_nav');
add_action('admin_print_styles-' . $page, 'my_plugin_admin_styles');
add_action('admin_print_scripts-' . $page, 'my_plugin_admin_scripts');
}
add_action('admin_menu','custom_bg');



// ---------------------------------------------------------------------------------------- Interface //



function custom_bg_nav() {


// on-thb-click update wp_background database //
if (isset($_POST['a'])) {
$b= $_POST['a'];
global $wpdb;
$wpdb->update( wp_background, array( 'bgimage' => ''.$b.''), array( 'id' => 1 ) );
echo "<div id='message' class='updated'><p>Background is updated.</p></div>";
}


// gather background thb //
global $wpdb;
$mybgimage = $wpdb->get_var(" SELECT bgimage FROM wp_background WHERE id = '1' ");

foreach(glob('../wp-content/plugins/Wordpress-background-plugin-master/bg/*.jpg', GLOB_BRACE) as $image) {
$image_name = basename($image);
$image_path = '../wp-content/plugins/Wordpress-background-plugin-master/bg/'.$image_name.'';


// retrieve background thb and show active thb //
if ($mybgimage == $image_name) {
$thb .='<li class="active"><span><img src="'.$image_path.'" alt="'.$image_name.'" /></span><input type="submit" name="a" value="'.$image_name.'" /></li>'; 
}
else { 
$thb .='<li><span><img src="'.$image_path.'" alt="'.$image_name.'" /></span><input type="submit" name="a" value="'.$image_name.'" /></li>'; 
}                         


}

	
?>

<div class="wrap">
<?php screen_icon(); ?>
<h2>Custom Background</h2>
<p>Select a background from the list below</p>
<form action="" method ="post" id="image_background_menu" >
<ul class="thb clearfix"><?php echo $thb; ?></ul>
</form>
</div>

<?php

}

// ---------------------------------------------------------------------------------------- Load Script //


function background_setup() { 

global $wpdb;$mybgimage = $wpdb->get_var(" SELECT bgimage FROM wp_background WHERE id = '1' ");
echo '<script type="text/javascript" src="/wp-content/plugins/Wordpress-background-plugin-master/backstretch.js"></script>
<script>$.backstretch(["/wp-content/plugins/Wordpress-background-plugin-master/bg/'.$mybgimage.'"]);</script>';
	
	}
add_action('wp_footer', 'background_setup');


