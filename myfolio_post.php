<?php
if(!class_exists('MyFolioPost')) {
	class MyFolioPost{
	
		
		function set_up(){
			add_action( 'init', array(&$this, 'set_up_posts'));
			add_action( 'wp_enqueue_scripts', array(&$this, 'set_up_styles'));
			add_action('wp_head', array(&$this, 'set_up_custom_css'));
			add_action( 'wp_enqueue_scripts', array(&$this, 'set_up_js'));
			add_action('do_meta_boxes', array(&$this, 'change_image_box'));
		}
		
		/**
		 * Set up posts
		 */
		public function set_up_posts(){
			$supports = array( 'title', 'editor', 'revisions', 'thumbnail' );
			register_post_type( 'myfolio',
				array(
					'labels' => array(
						'name' => __( 'Folio' ),
						'singular_name' => __( 'Folio' ),
						'add_new' => __('Add New Folio'),
						'add_new_item' => __('Create New Folio'),
						'edit_item' => __('Edit Folios'),
						'edit' => __('Edit Folio'),
						'new_item' => __('New Folio'),
						'view_item' => __('View Folio'),
						'search_items' => __('Search Folios'),
						'not_found' => __('No Folios Found'),
						'not_found_in_trash' => __('No Folios found in Trash'),
						'view' => __('View Folio')
					),
					'description' => __('Portfolio for your webste.'),
					'menu_icon' => MYFOLIO_PLUGIN_URL . '/myfolio.png',
					'public' => true,
					'publicly_queryable' => true,
					'has_archive' => true,
					'show_ui' => true,
					'show_in_menu' => true, 
					'query_var' => true,
					'hierarchical' => false,
					'capability_type' => 'post',
					'supports' => $supports
				)
			);
		}
		
		/**
		 * Change meta box
		 */
		function change_image_box(){
			remove_meta_box( 'postimagediv', 'myfolio', 'side' );
			add_meta_box('postimagediv', __('Portfolio Cover'), 'post_thumbnail_meta_box', 'myfolio', 'side', 'high');
		}

		
		/**
		 * Load up styles
		 */
		function set_up_styles(){
			if(!is_admin()){
				wp_enqueue_style('myfolio_style', MYFOLIO_PLUGIN_URL.'/css/style.css');
			}
		}
		
		/**
		 * Custom CSS
		 */
		function set_up_custom_css(){
			$myfolio_settings = get_option('myfolio_settings');
			$color = $myfolio_settings['color'];
			$overcolor = $myfolio_settings['overcolor'];
			$type = $myfolio_settings['type'];
			if(empty($type)){
				$type = 'circle';
			}
			?>
			<style type="text/css">
			.ec-circle{
				<?php if($type === 'circle'){?>
				-webkit-border-radius: 210px;
				-moz-border-radius: 210px;
				border-radius: 50%;
				<?php } ?>
				-webkit-box-shadow: 
					inset 0 0 1px 230px rgba(0,0,0,0.6),
					inset 0 0 0 7px #<?php echo $color; ?>;
				-moz-box-shadow: 
					inset 0 0 1px 230px rgba(0,0,0,0.6),
					inset 0 0 0 7px #<?php echo $color; ?>;
				box-shadow: 
					inset 0 0 1px 230px rgba(0,0,0,0.6),
					inset 0 0 0 7px #<?php echo $color; ?>;
			}
			
			.ec-circle-hover{
				-webkit-box-shadow: 
					inset 0 0 0 0 rgba(0,0,0,0.6),
					inset 0 0 0 20px #<?php echo $overcolor; ?>,
					0 0 10px rgba(0,0,0,0.3);
				-moz-box-shadow: 
					inset 0 0 0 0 rgba(0,0,0,0.6),
					inset 0 0 0 20px #<?php echo $overcolor; ?>,
					0 0 10px rgba(0,0,0,0.3);
				box-shadow: 
					inset 0 0 0 0 rgba(0,0,0,0.6),
					inset 0 0 0 20px #<?php echo $overcolor; ?>,
					0 0 10px rgba(0,0,0,0.3);
			}
			</style>
			<?php
		}
		
		/**
		 * Load up js
		 */
		function set_up_js(){
			if(!is_admin()){
				wp_enqueue_script('modernizr', MYFOLIO_PLUGIN_URL.'/js/modernizr.custom.72835.js', array('jquery'), '', false);
				wp_enqueue_script('jquery_circlemouse', MYFOLIO_PLUGIN_URL.'/js/jquery.circlemouse.js', array('jquery'), '', false);
				wp_enqueue_script("modernizr");
				wp_enqueue_script("jquery_circlemouse");
			}
		}
		
		
		/**
		 * Resize image
		 */
		function resize_image($attach_id = null, $img_url = null, $width, $height, $crop = true){
			$org_img = @getimagesize($img_url);
			if($org_img){
				if(empty($width)){
					$width = $org_img[0];
				}
				if(empty($height)){
					$height = $org_img[1];
				}
				if($attach_id){
					// this is an attachment, so we have the ID
					$image_src = wp_get_attachment_image_src($attach_id, 'full');
					$file_path = get_attached_file($attach_id);
				} elseif($img_url){
					// this is not an attachment, let's use the image url
					$file_path = parse_url($img_url);
					$file_path = $_SERVER['DOCUMENT_ROOT'].$file_path['path'];
					// Look for Multisite Path
					if(file_exists($file_path) === false){
						global $blog_id;
						$file_path = parse_url($img_url);
						if(preg_match('/files/', $file_path['path'])){
							$path = explode('/', $file_path['path']);
							foreach($path as $k => $v){
								if($v == 'files'){
									$path[$k-1] = 'wp-content/blogs.dir/'.$blog_id;
								}
							}
							$path = implode('/', $path);
						}
						$file_path = $_SERVER['DOCUMENT_ROOT'].$path;
					}
					//$file_path = ltrim( $file_path['path'], '/' );
					//$file_path = rtrim( ABSPATH, '/' ).$file_path['path'];
					$orig_size = getimagesize($file_path);
					$image_src[0] = $img_url;
					$image_src[1] = $orig_size[0];
					$image_src[2] = $orig_size[1];
				}
				$file_info = pathinfo($file_path);
				// check if file exists
				$base_file = $file_info['dirname'].'/'.$file_info['filename'].'.'.$file_info['extension'];
				if(!file_exists($base_file))
				return;
				$extension = '.'. $file_info['extension'];
				// the image path without the extension
				$no_ext_path = $file_info['dirname'].'/'.$file_info['filename'];
				$cropped_img_path = $no_ext_path.'-'.$width.'x'.$height.$extension;
				
				//remove old files older than 2 days to keep things fresh incase an image of the same name is changed
				if(file_exists($cropped_img_path))
					if(time() - @filemtime(utf8_decode($cropped_img_path)) >= 2*24*60*60){
						unlink($cropped_img_path);
					}
				// checking if the file size is larger than the target size
				// if it is smaller or the same size, stop right here and return
				if($image_src[1] > $width){
					// the file is larger, check if the resized version already exists (for $crop = true but will also work for $crop = false if the sizes match)
					if(file_exists($cropped_img_path)){
						$cropped_img_url = str_replace(basename($image_src[0]), basename($cropped_img_path), $image_src[0]);
						$resized_image = array(
							'url'   => $cropped_img_url,
							'width' => $width,
							'height'    => $height
						);
						return $resized_image['url'];
					}
					// $crop = false or no height set
					if($crop == false OR !$height){
						// calculate the size proportionaly
						$proportional_size = wp_constrain_dimensions($image_src[1], $image_src[2], $width, $height);
						$resized_img_path = $no_ext_path.'-'.$proportional_size[0].'x'.$proportional_size[1].$extension;
						// checking if the file already exists
						if(file_exists($resized_img_path)){
							$resized_img_url = str_replace(basename($image_src[0]), basename($resized_img_path), $image_src[0]);
							$resized_image = array(
								'url'   => $resized_img_url,
								'width' => $proportional_size[0],
								'height'    => $proportional_size[1]
							);
							return $resized_image['url'];
						}
					}
					// check if image width is smaller than set width
					$img_size = getimagesize($file_path);
					if($img_size[0] <= $width) $width = $img_size[0];
						// Check if GD Library installed
						if(!function_exists('imagecreatetruecolor')){
							echo 'GD Library Error: imagecreatetruecolor does not exist - please contact your webhost and ask them to install the GD library';
							return;
						}
						// no cache files - let's finally resize it
						$new_img_path = image_resize($file_path, $width.'px', $height.'px', $crop);
						$new_img_size = getimagesize($new_img_path);
						$new_img = str_replace(basename($image_src[0]), basename($new_img_path), $image_src[0]);
						// resized output
						$resized_image = array(
							'url'   => $new_img,
							'width' => $new_img_size[0],
							'height'    => $new_img_size[1]
						);
						return $resized_image['url'];
				}
				// default output - without resizing
				$resized_image = array(
					'url'   => $image_src[0],
					'width' => $width,
					'height'    => $height
				);
				return $resized_image['url'];
			}else{
				return $img_url;
			}
		}
		
		
		function show_portfolio($top_row, $image_width = '200', $image_height= '200'){
			$content = '';
			$args['post_type'] = 'myfolio';
			$get_posts = new WP_Query;
			$folios = $get_posts->query($args);
			if (is_array($folios) && count($folios) > 0) {
				$content .= '<div class="myfolio">';
				$content .= '<table class="myfolio-item">';
				$content .= '<tr>';
				$attachment_images = '';
				$count = 1;
				$total = 0;
				foreach ($folios as $folio) {
					$attachment_images = '';
					$attachment_images = &get_children('post_type=attachment&post_status=inherit&post_mime_type=image&post_parent=' . $folio->ID);
					$main_image = '';
					foreach ($attachment_images as $image) {
						$main_image = $image->guid;
						break;
					}
					if (empty($main_image)){
						$main_image = wp_get_attachment_image_src( get_post_thumbnail_id( $folio->ID ), 'single-post-thumbnail' );
						$main_image =  $main_image[0];
					}
					
					if (empty($main_image)) {
						$main_image = MYFOLIO_PLUGIN_URL.'/images/portfolio.png';
					}
					$permalink = get_permalink($folio->ID);
					$margin_top = intval($image_width)/2;
					$content .= '<td>';
					$content .= '<a href="'.$permalink.'" id="myfolio_'.$count.'" class="ec-circle" style="background-image:url('.$this->resize_image(null,$main_image,$image_width,$image_height).'); width:'.$image_width.'px ; height:'.$image_height.'px">';
					$content .= '<h3 style="margin-top:'.$margin_top.'px">'.$folio->post_title.'</h3>';
					$content .= '</a>';
					$content .= '<script type="text/javascript">';
					$content .= 'jQuery(function() {';
					$content .= 'jQuery("#myfolio_'.$count.'").circlemouse({';
					$content .= 'onMouseEnter: function( el ) {';
					$content .= 'el.addClass("ec-circle-hover");';
					$content .= '},';
					$content .= 'onMouseLeave: function( el ) {';
					$content .= 'el.removeClass("ec-circle-hover");';
					$content .= '}';
					$content .= '});';
					$content .= '});';
					$content .= '</script>';
					$content .= '</td>';
					if($total === intval($top_row)){
						$content .= '</tr><tr>';
						$total = 0;
					}
					$total++;
					$count++;
				}
				$content .= '</tr>';
				$content .= '</table>';
				$content .= '</div>';
			}
			return $content;
		}
	}
}
?>