<?php

/*
Plugin Name: Custom Meta Box Template
Plugin URI: http://themefoundation.com/
Description: Provides a starting point for creating custom meta boxes.
Author: Theme Foundation
Version: 1.0
Author URI: http://themefoundation.com/
*/


/**
 * Adds a meta box to the post editing screen
 */
add_action( 'add_meta_boxes', 'prfx_custom_meta' );
function prfx_custom_meta() {
	add_meta_box( 'prfx_meta', __( 'Meta Box Title', 'prfx-textdomain' ), 'prfx_meta_callback', 'post' );
}

/**
 * Outputs the content of the meta box
 */
function prfx_meta_callback( $post ) {
	$who = 'prfx_custom_meta';
	$what = 'update';
	$which = $post->ID;
	wp_nonce_field( $who.$what.$which, 'prfx_nonce' );
	$prfx_stored_meta = get_post_meta( $post->ID );
	$default_keys = array( 	'meta_text'=>'', 'meta_checkbox'=>'', 'meta_checkbox_two'=>'', 'meta_radio'=>'radio-one', 'meta_select'=>'select-one', 'meta_textarea'=>'', 'meta_color'=>'#FFFFFF', 'meta_image'=>'' );
	foreach( $default_keys as $key => $def )
		$$key = isset( $prfx_stored_meta[$key][0] ) ? $prfx_stored_meta[$key][0] : $def;
	?>

	<p>
		<label for="meta-text" class="prfx-row-title"><?php _e( 'Example Text Input', 'prfx-textdomain' )?></label>
		<input type="text" id="meta-text" name="_metas[meta_text]" id="meta-text" value="<?php echo esc_attr( $meta_text ); ?>" />
	</p>

	<p>
		<span class="prfx-row-title"><?php _e( 'Example Checkbox Input', 'prfx-textdomain' )?></span>
		<div class="prfx-row-content">
			<label for="meta-checkbox">
				<input type="checkbox" name="_metas[meta_checkbox]" id="meta-checkbox" value="yes" <?php checked( $meta_checkbox, 'yes' ); ?> />
				<?php _e( 'Checkbox label', 'prfx-textdomain' )?>
			</label>
			<label for="meta-checkbox-two">
				<input type="checkbox" name="_metas[meta_checkbox_two]" id="meta-checkbox-two" value="yes" <?php checked( $meta_checkbox_two, 'yes' ); ?> />
				<?php _e( 'Another checkbox', 'prfx-textdomain' )?>
			</label>
		</div>
	</p>

	<p>
		<span class="prfx-row-title"><?php _e( 'Example Radio Buttons', 'prfx-textdomain' )?></span>
		<div class="prfx-row-content">
			<label for="meta-radio-one">
				<input type="radio" name="_metas[meta_radio]" id="meta-radio-one" value="radio-one" <?php checked( $meta_radio, 'radio-one' ); ?>>
				<?php _e( 'Radio Option #1', 'prfx-textdomain' )?>
			</label>
			<label for="meta-radio-two">
				<input type="radio" name="_metas[meta_radio]" id="meta-radio-two" value="radio-two" <?php checked( $meta_radio, 'radio-two' ); ?>>
				<?php _e( 'Radio Option #2', 'prfx-textdomain' )?>
			</label>
		</div>
	</p>

	<p>
		<label for="meta-select" class="prfx-row-title"><?php _e( 'Example Select Input', 'prfx-textdomain' )?></label>
		<select name="_metas[meta_select]" id="meta-select">
			<option value="select-one" <?php selected( $meta_select, 'select-one' ); ?>><?php _e( 'One', 'prfx-textdomain' )?></option>';
			<option value="select-two" <?php selected( $meta_select, 'select-two' ); ?>><?php _e( 'Two', 'prfx-textdomain' )?></option>';
		</select>
	</p>

	<p>
		<label for="meta-textarea" class="prfx-row-title"><?php _e( 'Example Textarea Input', 'prfx-textdomain' )?></label>
		<textarea name="_metas[meta_textarea]" id="meta-textarea"><?php echo esc_textarea( $meta_textarea ); ?></textarea>
	</p>

	<p>
		<label for="meta-color" class="prfx-row-title"><?php _e( 'Color Picker', 'prfx-textdomain' )?></label>
		<input name="_metas[meta_color]" type="text" value="<?php echo esc_attr( $meta_color ); ?>" class="meta-color" />
	</p>

	<p>
		<label for="meta-image" class="prfx-row-title"><?php _e( 'Example File Upload', 'prfx-textdomain' )?></label>
		<input type="text" name="_metas[meta_image]" id="meta-image" value="<?php echo esc_attr( $meta_image ); ?>" />
		<input type="button" id="meta-image-button" class="button" value="<?php _e( 'Choose or Upload an Image', 'prfx-textdomain' )?>" />
	</p>
 

	<?php
}



/**
 * Saves the custom meta input
 */
add_action( 'save_post', 'prfx_meta_save' );
function prfx_meta_save() {
 
	// Checks save status
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE || !isset( $_POST['_metas'] ) )
		return;
	$post_id = $_POST['post_ID'];

	$who = 'prfx_custom_meta';
	$what = 'update';
	$which = $post_id;
	check_admin_referer( $who.$what.$which, 'prfx_nonce' );

	$allowed_keys = array_flip( array( 'meta_text', 'meta_checkbox', 'meta_checkbox_two', 'meta_radio', 'meta_select', 'meta_textarea', 'meta_color', 'meta_image' ) );

	$final_keys = array_intersect_key( $_POST['_metas'], array_flip( $allowed_keys ) );
	foreach( $allowed_keys as $key=>$value )
		if( isset( $_POST['_metas'][$key] ) )
			update_post_meta( $post_id, $key, $_POST['_metas'][$key] );
		else
			delete_post_meta( $post_id, $key );
}


/**
 * Adds the meta box stylesheet when appropriate
 */
add_action( 'admin_print_styles', 'prfx_admin_styles' );
function prfx_admin_styles(){
	global $typenow;
	if( $typenow == 'post' ) {
		wp_enqueue_style( 'prfx_meta_box_styles', plugin_dir_url( __FILE__ ) . 'meta-box-styles.css' );
	}
}


/**
 * Loads the color picker javascript
 */
add_action( 'admin_enqueue_scripts', 'prfx_color_enqueue' );
function prfx_color_enqueue() {
	global $typenow;
	if( $typenow == 'post' ) {
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'meta-box-color-js', plugin_dir_url( __FILE__ ) . 'meta-box-color.js', array( 'wp-color-picker' ) );
	}
}

/**
 * Loads the image management javascript
 */
add_action( 'admin_enqueue_scripts', 'prfx_image_enqueue' );
function prfx_image_enqueue() {
	global $typenow;
	if( $typenow == 'post' ) {
		wp_enqueue_media();
 
		// Registers and enqueues the required javascript.
		wp_register_script( 'meta-box-image', plugin_dir_url( __FILE__ ) . 'meta-box-image.js', array( 'jquery' ) );
		wp_localize_script( 'meta-box-image', 'meta_image',
			array(
				'title' => __( 'Choose or Upload an Image', 'prfx-textdomain' ),
				'button' => __( 'Use this image', 'prfx-textdomain' ),
			)
		);
		wp_enqueue_script( 'meta-box-image' );
	}
}