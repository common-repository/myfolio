<?php
/*
Plugin Name: MyFolio
Description: A simple WordPress portfolio plugin. Create a simple portfolio using custom post types
Version: 1.1
Author: rixeo
Author URI: http://thebunch.co.ke/
Plugin URI: http://thebunch.co.ke/
*/

define('MYFOLIO_PLUGIN_URL', WP_PLUGIN_URL.'/'.dirname(plugin_basename(__FILE__)));
define('MYFOLIO_PLUGIN_DIR', WP_PLUGIN_DIR.'/'.dirname(plugin_basename(__FILE__)));

require_once(MYFOLIO_PLUGIN_DIR.'/myfolio_post.php');

//Initialise MyFolioPost
global $myfoliopost;
if(!isset($myfoliopost)){
	$myfoliopost = new MyFolioPost();
}

$myfoliopost->set_up();

/**
 * Shortcode
 */
add_shortcode('my_folio', 'my_folio_shortcode');
function my_folio_shortcode($atts){
	global $myfoliopost;
	extract(shortcode_atts(array(
                'image_width' => '200',
				'image_height' => '200',
				'top_row' => '4'), $atts));
	return $myfoliopost->show_portfolio($top_row, $image_width, $image_height);
}


/**
 * Add settings page
 */
add_action('admin_menu', 'my_folio_create_admin_menu');
function my_folio_create_admin_menu(){
	add_options_page('MyFolio','MyFolio Settings','manage_options','myfolio-settings','myfolio_settings_setup');
}


function myfolio_settings_setup(){
	if(@$_POST['myfolio_settings']){
		$color = $_POST['myfolio_color'];
		$overcolor = $_POST['myfolio_overcolor'];
		$type = $_POST['myfolio_type'];
		$opts = array('color' => $color,
					  'overcolor' => $overcolor,
					  'type' => $type);
		update_option('myfolio_settings', $opts);
	}
	$myfolio_settings = get_option('myfolio_settings');
	$myfolio_folio_views = array('circle'=>'Circle',
								 'square' => 'Square');
	?>
	<div class="wrap">
		<h2><?php _e("MyFolio Settings"); ?></h2>
		<form method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><label for="myfolio_color"><?php _e("Border Color"); ?>: </label></th>
						<td><input id="myfolio_color" name="myfolio_color" type="text" class="regular-text color" value="<?php echo $myfolio_settings['color']; ?>" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="myfolio_overcolor"><?php _e("Border mouse over color"); ?>: </label></th>
						<td><input id="myfolio_overcolor" name="myfolio_overcolor" type="text" class="regular-text color" value="<?php echo $myfolio_settings['overcolor']; ?>" /></td>
					</tr>
					<tr valign="top">
							<th scope="row"><label for="myfolio_type"><?php _e("View Type"); ?>: </label></th>
							<td>
								<select name="myfolio_type">
									<?php 
									foreach ( $myfolio_folio_views as $myfolio_folio_view => $view) {
										$cont_selected = '';
										if ($myfolio_settings['type'] === $myfolio_folio_view) {
											$cont_selected = 'selected="selected"';
										}
										$option = '<option value="' .$myfolio_folio_view. '" '.$cont_selected.'>';
										$option .= $view;
										$option .= '</option>';
										echo $option;
									}
									?>
								</select>
							</td>
						</tr>
				</tbody>
			</table>
			<script type="text/javascript" src="<?php echo MYFOLIO_PLUGIN_URL; ?>/js/jscolor.js"></script>
			<p class="submit">
				<input class='button button-primary' type='submit' name='myfolio_settings' value='<?php _e('Save Options'); ?>' id='submitbutton' />
			</p>
		</form>
	</div>
	<?php
}

?>