<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Sort_Me_This
 * @subpackage Sort_Me_This/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Sort_Me_This
 * @subpackage Sort_Me_This/admin
 * @author     Algaweb
 */
class Sort_Me_This_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Sort_Me_This_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Sort_Me_This_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/sort-me-this-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'selectize-css', plugin_dir_url( __FILE__ ) . 'selectize/dist/css/selectize.css' );
		wp_enqueue_style( 'jquery-ui-css-smet', plugin_dir_url( __FILE__ ) . 'css/jquery-ui.css');
		wp_enqueue_style( 'jquery-ui' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 *
		 * The Sort_Me_This_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/sort-me-this-admin.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'selectize-js', plugin_dir_url( __FILE__ ) . 'selectize/dist/js/standalone/selectize.min.js', array( 'jquery' ) );
		wp_enqueue_script( 'jquery-ui-datepicker' );
		// Ajax localize  
		wp_localize_script( $this->plugin_name, 'smet_retrieve_info' , array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		wp_localize_script( $this->plugin_name, 'smet_edit_metadata' , array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		wp_localize_script( $this->plugin_name, 'smet_save_cat_only' , array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		wp_localize_script( $this->plugin_name, 'smet_show_filtered_media' , array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		wp_localize_script( $this->plugin_name, 'smet_save_new_media_category' , array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		wp_localize_script( $this->plugin_name, 'smet_delete_media_category' , array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		wp_localize_script( $this->plugin_name, 'smet_edit_existent_media_category' , array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		wp_localize_script( $this->plugin_name, 'smet_delete_media' , array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

	}

	/**
	 * Add Menu page for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function add_menu_page() {
		
		/**
		 *
		 * The Sort_Me_This_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		add_menu_page( $this->plugin_name, __('SortMeThis', ''),  'manage_options', $this->plugin_name, '', plugin_dir_url( __FILE__ ).'partials/img/sortmethis_icon.png', 60);
		add_submenu_page($this->plugin_name, 'Media', 'Media', 'manage_options', $this->plugin_name, function(){ require_once(plugin_dir_path( __FILE__ ) .'partials/sort-me-this-admin-display-media.php');});
		add_submenu_page($this->plugin_name, 'Media Categories', 'Media Categories', 'manage_options', $this->plugin_name.'-media-categories', function(){ require_once(plugin_dir_path( __FILE__ ) .'partials/sort-me-this-admin-display-categories.php'); });
	}

	/**
	 * Async calls to show filtered media list.
	 *
	 * @since    1.0.0
	 */
	public function smet_show_filtered_media() {
		
		/**
		 *
		 * The Sort_Me_This_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

		$page = sanitize_text_field($_POST['smt-curr-page']);
        $cur_page = $page;
        $page -= 1;
        // Set the number of results to display
        $per_page = 25;
        $previous_btn = true;
        $next_btn = true;
        $first_btn = true;
        $last_btn = true;
		$start = $page * $per_page;

		$media_cat = array();
		if(!empty($_POST['media-categories'])) {
			foreach ( $_POST['media-categories'] as $key => $val ) {
				$key = sanitize_text_field( $key );
				$media_cat[ $key ] = sanitize_text_field( $val );
			}
		}
		$m_type = sanitize_text_field($_POST['m-type']);
		$from_date = sanitize_text_field($_POST['from-date']);
		$to_date = sanitize_text_field($_POST['to-date']);
		if(isset($from_date) && $from_date != '') {
			$from_date =  date("Y-m-d", strtotime(sanitize_text_field($_POST['from-date'])));
		}
		if(isset($to_date) && $to_date != '') {
			$to_date = date("Y-m-d", strtotime(sanitize_text_field($_POST['to-date'])));
		}
		
		

		if(empty($media_cat)) {
			$media_query = new WP_Query(
				array(
					'post_type' => 'attachment',
					'post_status' => 'inherit',
					'posts_per_page'    => $per_page,
					'offset'            => $start,
					'post_mime_type' => $m_type == 'all' ? '' : $m_type,
					'fields' => 'ids',
					'date_query' => array(
						array(
							'after'     => $from_date,
							'before'    => $to_date,
							'inclusive' => true,
						),
					),
				)
			);
		}
		else {
			$meta_query = array();
			$meta_query['relation'] = 'AND';
			foreach ($media_cat as $cat) {
				$meta_query[] = array(
					'key' => 'smet_attachment_media_categories',
					'value' => $cat,
					'compare' => 'LIKE'
				);
			}
			$media_query = new WP_Query(
				array(
					'post_type' => 'attachment',
					'post_status' => 'inherit',
					'fields' => 'ids',
					'posts_per_page'    => $per_page,
					'offset'            => $start,
					'post_mime_type' => $m_type == 'all' ? '' : $m_type,
					'date_query' => array(
						array(
							'after'     => $from_date,
							'before'    => $to_date,
							'inclusive' => true,
						),
					),
					'meta_query' => array(
						'relation' => 'OR',
						$meta_query
					)
				)
			);
		}


		if ( !$media_query ) {
			$response_array['status'] = 'ko';
			$response_array['description'] = __('Cannot show medias.', 'sort-me-this');
			wp_send_json_error( $response_array );
			die();
		}
		
		$count = $media_query->found_posts;
		foreach ($media_query->posts as $id) {
			$type = get_post_mime_type( $id );
			$file_icon_class = ''; 
			if(	$type != 'image/jpeg' && $type != 'image/png' && $type != 'image/gif'){
				$file_icon_class = 'smt-img-wrapper-files-icon'; 
			}
		?>
			<li data-id="<?php echo $id; ?>" class="smt-media-list-li" onclick="smet_image_click(<?php echo $id; ?>, this);">
				<div class="smt-media-img-wrapper <?php echo $file_icon_class; ?>">
					<?php 
						echo wp_get_attachment_image($id, 'thumbnail', true);
						if(	$type != 'image/jpeg' && $type != 'image/png' && $type != 'image/gif'){
							echo '<div>'. get_the_title($id) .'</div>';
						}
					?>
				</div>                 
			</li>
		<?php
		}

		$no_of_paginations = ceil($count / $per_page);

		// Navigation buttons   
		$pag_container .= "
		<ul>";

		if ($first_btn && $cur_page > 1) {
			$pag_container .= "<li p='1' class='active' onclick='smet_nav_btn_click(this);'>". __('First', 'sort-me-this') ."</li>";
		} else if ($first_btn) {
			$pag_container .= "<li p='1' class='inactive'>First</li>";
		}

		if ($previous_btn && $cur_page > 1) {
			$pre = $cur_page - 1;
			$pag_container .= "<li p='$pre' class='active' onclick='smet_nav_btn_click(this);'>". __('Previous', 'sort-me-this') ."</li>";
		} else if ($previous_btn) {
			$pag_container .= "<li class='inactive'>". __('Previous', 'sort-me-this') ."</li>";
		}

		$pag_container .= "<li class='smt-curr-page'>". $cur_page . __(' of ', 'sort-me-this') . $no_of_paginations ."</li>";
		
		if ($next_btn && $cur_page < $no_of_paginations) {
			$nex = $cur_page + 1;
			$pag_container .= "<li p='$nex' class='active' onclick='smet_nav_btn_click(this);'>". __('Next', 'sort-me-this') ."</li>";
		} else if ($next_btn) {
			$pag_container .= "<li class='inactive'>". __('Next', 'sort-me-this') ."</li>";
		}

		if ($last_btn && $cur_page < $no_of_paginations) {
			$pag_container .= "<li p='$no_of_paginations' class='active' onclick='smet_nav_btn_click(this);'>". __('Last', 'sort-me-this') ."</li>";
		} else if ($last_btn) {
			$pag_container .= "<li p='$no_of_paginations' class='inactive'>". __('Last', 'sort-me-this') ."</li>";
		}

		$pag_container = $pag_container . "
		</ul>";
		echo '<div class = "smt-pagination-nav">' . $pag_container . '</div>';
		die();
	}


	/**
	 * Async calls to return attachment info.
	 *
	 * @since    1.0.0
	 */
	public function smet_retrieve_info() {
		
		/**
		 *
		 * The Sort_Me_This_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		$post = get_post( sanitize_text_field($_POST['attachemnt-id'] ));
		if ( is_wp_error( $post ) ) {
			$response_array['status'] = 'ko';
			$response_array['description'] = __('Something went wrong during the retrieve of this attachment.', 'sort-me-this');    
			wp_send_json_error( $response_array );
			die();
		}

		$attachment = wp_get_attachment_metadata($post->ID);
		$author_id = get_post_field ('post_author', $post->ID);
		$display_name = get_the_author_meta( 'display_name' , $author_id );
		$selected_media_categories = get_post_meta($post->ID, 'smet_attachment_media_categories', true);

		
		if (! $selected_media_categories) {
			$selected_media_categories = [];
		}
		
		?>
		<div class="smt-file-image">
			<?php
			$type = get_post_mime_type( $post->ID );
			$file_icon_class = ''; 
			if(	$type != 'image/jpeg' && $type != 'image/png' && $type != 'image/gif'){
				echo wp_get_attachment_image($post->ID, 'thumbnail', true, array( "class" => "smt-full-margin" ));
			}
			else {
				echo wp_get_attachment_image($post->ID, 'large'); 
			}
	 
			?>	
		</div>
		<div class="smt-file-data">
			<div class="smt-data-collection">
				<div class="smt-filename"><strong><?php _e('File name', 'sort-me-this'); ?>:</strong> <?php echo get_the_title($post->ID) ?> </div>
				<div class="smt-file-type"><strong><?php _e('File type', 'sort-me-this'); ?>:</strong> <?php echo get_post_mime_type($post->ID); ?> </div>
				<div class="smt-uploaded"><strong><?php _e('Uploaded', 'sort-me-this'); ?>:</strong> <?php echo get_the_date('', $post->ID); ?> </div>
				<div class="smt-file-size"><strong><?php _e('File size', 'sort-me-this'); ?>:</strong> <?php echo size_format(filesize( get_attached_file( $post->ID ) )); ?></div>
				<div class="smt-dimension"><strong><?php _e('Dimensions', 'sort-me-this'); ?>:</strong> <?php echo $attachment['width']. ' x '.$attachment['height']; ?></div>
			</div>
			<div class="smt-settings">
				<span class="smt-setting">
					<label for="smt-attachment-details-alt-text"><?php _e('Alt text', 'sort-me-this'); ?></label>
					<input type="text" id="smt-attachment-details-alt-text" value="<?php echo get_post_meta( $post->ID, '_wp_attachment_image_alt', true ); ?>">
				</span>
				<span class="smt-setting">
					<label for="smt-attachment-details-title"><?php _e('Title', 'sort-me-this'); ?></label>
					<input type="text" id="smt-attachment-details-title" value="<?php echo $post->post_title; ?>">
				</span>
				<span class="smt-setting">
					<label for="smt-attachment-details-caption"><?php _e('Caption', 'sort-me-this'); ?></label>
					<textarea id="smt-attachment-details-caption"><?php echo $post->post_excerpt; ?></textarea>
				</span>
				<span class="smt-setting">
					<label for="smt-attachment-details-description"><?php _e('Description', 'sort-me-this'); ?></label>
					<textarea id="smt-attachment-details-description"><?php echo $post->post_content; ?></textarea>
				</span>
				<span class="smt-setting">
					<label for="smt-attachment-details-uploaded-by"><?php _e('Uploaded by', 'sort-me-this'); ?></label>
					<span id="smt-attachment-details-uploaded-by"><?php echo $display_name; ?></span>
				</span>
				<span class="smt-setting">
					<label for="smt-attachment-details-file-url"><?php _e('File URL', 'sort-me-this'); ?></label>
					<span id="smt-attachment-details-file-url"><?php echo wp_get_attachment_url( $post->ID ); ?></span>
				</span>
				<span class="smt-setting">
					<label for="smt-attachment-details-media-categories"><?php _e('Media categories', 'sort-me-this'); ?></label>
					<select id="smt-attachment-details-media-categories" multiple="multiple"  class="smt-selectize-multiple-details">
					<?php
						$media_cat = get_option('smt-media-categories');
						foreach ($media_cat as $key => $value) {
							$selected = '';
							if(in_array($key, $selected_media_categories)) {
								$selected = 'selected="selected"';
							}
							echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
						}
					?>
					</select>
				</span>
				
			</div>

			<div class="smt-edit-btn-container">
				<button id="smt-edit-media-btn" class="smt-button-primary" type="button" onclick="smet_edit_media( this, <?php echo $post->ID; ?>);"><?php _e('Edit', 'sort-me-this'); ?></button>
			</div>

		</div>
		<?php
		die();
	}

	/**
	 * Async calls to edit attachment metadata.
	 *
	 * @since    1.0.0
	 */
	public function smet_edit_metadata() {
		
		/**
		 *
		 * The Sort_Me_This_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		$post = get_post( sanitize_text_field($_POST['attachemnt-id']) );
		$alt = sanitize_text_field($_POST['alt-text']);
		$title = sanitize_text_field($_POST['title']);
		$caption = sanitize_text_field($_POST['caption']);
		$description = sanitize_text_field( $_POST['description']);
		$media_cat = array();
		foreach ( $_POST['media-categories'] as $key => $val ) {
			$key = sanitize_text_field( $key );
			$media_cat[ $key ] = sanitize_text_field( $val );
		}

		$attachment_post = array(
			'ID'           => $post->ID,
			'post_title'   => $title,
			'post_content' => $description,
			'post_excerpt' => $caption,
		);
		$pid = wp_update_post( $attachment_post );
		if ( is_wp_error( $pid ) ) {
			$response_array['status'] = 'ko';
			$response_array['description'] = __('Something went wrong during the update of this attachment.', 'sort-me-this');
			wp_send_json_error( $response_array );
			die();
		}
		
		update_post_meta($post->ID, '_wp_attachment_image_alt', $alt);
		update_post_meta($post->ID, 'smet_attachment_media_categories', $media_cat);

		$response_array['status'] = 'ok';
		$response_array['description'] = __('Media data edited', 'sort-me-this');
		wp_send_json_success($response_array);

		die();
	}

	/**
	 * Async calls to save only attachment media categories.
	 *
	 * @since    1.0.0
	 */
	public function smet_save_cat_only() {
		
		/**
		 *
		 * The Sort_Me_This_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		$media_ids = array();
		foreach ( $_POST['media_ids'] as $key => $val ) {
			$media_ids[ $key ] = sanitize_text_field( $val );
		}
		
		$media_cat = array();
		foreach ( $_POST['media-categories'] as $key => $val ) {
			$key = sanitize_text_field( $key );
			$media_cat[ $key ] = sanitize_text_field( $val );
		}
		
		foreach ($media_ids as $id) {
			$res = update_post_meta($id, 'smet_attachment_media_categories', $media_cat);
			if ( !$res ) {
				$response_array['status'] = 'ko';
				$response_array['description'] = __('Something went wrong.', 'sort-me-this');
				wp_send_json_error( $response_array );
				die();
			}
		}

		$response_array['status'] = 'ok';
		$response_array['description'] = __('Category saved!', 'sort-me-this');
		wp_send_json_success($response_array);
		die();
	}

	/**
	 * Async calls to save new Media Categories (Media Categories page).
	 *
	 * @since    1.0.0
	 */
	public function smet_save_new_media_category() {
		
		/**
		 *
		 * The Sort_Me_This_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		$media_cat = sanitize_text_field($_POST['new-media-category']);
		$media_cat_slug = sanitize_title($_POST['new-media-category']);

		if ( $media_cat_slug == '' ) {
			$response_array['status'] = 'ko';
			$response_array['description'] = __('Media Category cannot be empty', 'sort-me-this');    
			wp_send_json_error( $response_array );
			die();
		}
		
		$option_val = get_option('smt-media-categories');

		if ( in_array($media_cat, $option_val) ) {
			$response_array['status'] = 'ko';
			$response_array['description'] = __('This Media Category already exist!', 'sort-me-this');
			wp_send_json_error( $response_array );
			die();
		}

		$new_id = md5(uniqid($media_cat_slug));
		$option_val[$new_id] = $media_cat;
		$check = get_option('cmc-fv');
		if($check <= 5) {
			update_option('smt-media-categories', $option_val);

			$response_array['status'] = 'ok';
			$response_array['html'] = '<tr id="row-id-'.$new_id.'">
				<td class="smt-table-name">'.$media_cat.'</td>
				<td>'.$new_id.'</td>
				<td>0</td>
				<td></td>
				<td><button class="smt-edit-existent-media-cat" type="button" value="'.$new_id.'" onclick="smet_show_edit_media_cat_modal(this);">'. __('Edit', 'sort-me-this') .'</button></td>
				<td><button type="button" class="smt-button-warning" onclick="smet_delete_media_cat(this, \''.$new_id.'\')">'. __('Delete', 'sort-me-this') .'</button></td>
			</tr>';
			update_option('cmc-fv', $check + 1);
			wp_send_json_success($response_array);
		}
		else {
			$response_array['status'] = 'ko';
			$response_array['description'] = __('You have reached the max avaiable categories fo FREE version', 'sort-me-this');
			wp_send_json_error( $response_array );
			die();
		}
		die();
	}

	/**
	 * Async calls to delete Media or group of media.
	 *
	 * @since    1.0.0
	 */
	public function smet_delete_media() {
		
		/**
		 *
		 * The Sort_Me_This_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		$ids_to_delete = explode(',', sanitize_text_field($_POST['ids-to-delete']));

		foreach ($ids_to_delete as $id) {
			$res = wp_delete_post($id);
			if ( is_wp_error( $res ) ) {
				$response_array['status'] = 'ko';
				$response_array['description'] = __('Something went wrong during the deleting of this attachment: ID->'.$id, 'sort-me-this');
				wp_send_json_error( $response_array );
				die();
			}
		}


		$response_array['status'] = 'ok';
		$response_array['description'] = __('Media correctly removed.', 'sort-me-this');
		wp_send_json_success($response_array);

		die();
	}

	/**
	 * Async calls to delete Media Categories (Media Categories page).
	 *
	 * @since    1.0.0
	 */
	public function smet_delete_media_category() {
		
		/**
		 *
		 * The Sort_Me_This_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		$media_cat_id_to_remove = sanitize_text_field($_POST['media-category-id']);
		
		$option_val = get_option('smt-media-categories');

		if (array_key_exists($media_cat_id_to_remove, $option_val)) {
			unset($option_val[$media_cat_id_to_remove]);
			update_option('smt-media-categories', $option_val);
			$response_array['status'] = 'ok';
			$response_array['description'] = __('Media category deleted!', 'sort-me-this');
			if($check > 0) {
				update_option('cmc-fv', $check - 1);
			}
			wp_send_json_success($response_array);
			die();
		}

		$response_array['status'] = 'ko';
		$response_array['description'] = __('Something went wrong during the removing of this Media Category.', 'sort-me-this');
		wp_send_json_error( $response_array );
		die();	
	}

	/**
	 * Async calls to Edit an already existent Media Category.
	 *
	 * @since    1.0.0
	 */
	public function smet_edit_existent_media_category() {
		
		/**
		 *
		 * The Sort_Me_This_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		$cat_id =  sanitize_text_field($_POST['cat-id']);
		$new_name_cat = sanitize_text_field($_POST['new-name-category']);	
		$media_cat_slug = sanitize_title($_POST['new-name-category']);
		
		$option_val = get_option('smt-media-categories');
		
		if ( $media_cat_slug == '' ) {
			$response_array['status'] = 'ko';
			$response_array['description'] = __('Media Category cannot be empty', 'sort-me-this'); 
			wp_send_json_error( $response_array );
			die();
		}

		if ( in_array($new_name_cat, $option_val) ) {
			$response_array['status'] = 'ko';
			$response_array['description'] =  __('This Media Category already exist!', 'sort-me-this');   
			wp_send_json_error( $response_array );
			die();
		}

		if (array_key_exists($cat_id, $option_val)) {
			$option_val[$cat_id] = $new_name_cat;
			update_option('smt-media-categories', $option_val);
			$response_array['status'] = 'ok';
			$response_array['new_name'] = $new_name_cat;
			$response_array['row_id'] = 'row-id-'.$cat_id;
			wp_send_json_success($response_array);
			die();
		}

		$response_array['status'] = 'ko';
		$response_array['description'] = __('Something went wrong during the edit of this Media Category.', 'sort-me-this');
		wp_send_json_error( $response_array );
		die();
	}

}