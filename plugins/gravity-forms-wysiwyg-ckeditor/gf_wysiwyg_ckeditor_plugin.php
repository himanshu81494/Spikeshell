<?php
/*
Plugin Name: CKEditor WYSIWYG for Gravity Forms
Description: Use the CKEditor WYSIWYG in your Gravity Forms
Version: 1.8.7
Author: Adrian Gordon
Author URI: http://www.itsupportguides.com 
License: GPL2
Text Domain: gravity-forms-wysiwyg-ckeditor

------------------------------------------------------------------------
Copyright 2015 Adrian Gordon

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA

*/

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

load_plugin_textdomain( 'gravity-forms-wysiwyg-ckeditor', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );

// include upload handler for image upload feature -- extends default classes
if ( !class_exists( 'UploadHandler' ) && version_compare( phpversion(), '5.4', '>=' ) ) {
	require_once( plugin_dir_path( __FILE__ ).'UploadHandler.php' );
	
	class ITSG_GFCKEDITOR_UploadHandler extends UploadHandler {
		public function post( $print_response = true ) {
			$upload = $this->get_upload_data( $this->options['param_name'] );
			// Parse the Content-Disposition header, if available:
			$content_disposition_header = $this->get_server_var( 'HTTP_CONTENT_DISPOSITION' );
			$file_name = $content_disposition_header ?
			rawurldecode(preg_replace(
			'/(^[^"]+")|("$)/',
			'',
			$content_disposition_header
			)) : null;
			// Parse the Content-Range header, which has the following form:
			// Content-Range: bytes 0-524287/2000000
			$content_range_header = $this->get_server_var( 'HTTP_CONTENT_RANGE' );
			$content_range = $content_range_header ?
			preg_split('/[^0-9]+/', $content_range_header ) : null;
			$size =  $content_range ? $content_range[3] : null;
			$files = array();
			if ( $upload ) {
				if ( is_array( $upload['tmp_name'] ) ) {
					// param_name is an array identifier like "files[]",
					// $upload is a multi-dimensional array:
					foreach ( $upload['tmp_name'] as $index => $value ) {
						$files[] = $this->handle_file_upload (
							$upload['tmp_name'][$index],
							$file_name ? $file_name : $upload['name'][$index],
							$size ? $size : $upload['size'][$index],
							$upload['type'][$index],
							$upload['error'][$index],
							$index,
							$content_range
						);
					}
				} else {
					// param_name is a single object identifier like "file",
					// $upload is a one-dimensional array:
					$files[] = $this->handle_file_upload(
						isset( $upload['tmp_name'] ) ? $upload['tmp_name'] : null,
						$file_name ? $file_name : ( isset($upload['name'] ) ?
								$upload['name'] : null ),
						$size ? $size : (isset($upload['size']) ?
								$upload['size'] : $this->get_server_var( 'CONTENT_LENGTH' ) ),
						isset( $upload['type'] ) ?
								$upload['type'] : $this->get_server_var( 'CONTENT_TYPE' ),
						isset( $upload['error'] )  ? $upload['error'] : null,
						null,
						$content_range
					);
				}
			}
			$CKEditorFuncNum = $this->options['CKEditorFuncNum'];
			$download_url = $files[0]->url;
			$response = "<script>
var l = '".$download_url."';
window.parent.CKEDITOR.tools.callFunction(
'" . $CKEditorFuncNum . "',
l,
'".  $files[0]->error ."'
);
</script>";
			return $this->generate_response( $response, $print_response );
		}

		public function generate_response( $content, $print_response = true ) {
			$this->response = $content;
			$this->body( $content );
			return $content;
		}
		
		protected function trim_file_name( $file_path, $name, $size, $type, $error, $index, $content_range ) {
			$name = apply_filters( 'itsg_gf_ckeditor_filename', $name, $file_path, $size, $type, $error, $index, $content_range );

			$exclude_characters = array( 
				'\\',
				'/',
				':',
				';',
				'*',
				'?',
				'!',
				'"',
				'`',
				"'",
				'<',
				'>',
				'{',
				'}',
				'[',
				']',
				',',
				'|' 
				);
			$exclude_characters = (array)apply_filters( 'itsg_gf_ckeditor_filename_exclude_characters', $exclude_characters );
			$replace_character = (string)apply_filters( 'itsg_gf_ckeditor_filename_replace_characters', '' );
			$name = str_replace( $exclude_characters, $replace_character, $name );
			
			return $name;
		}
	} // END ITSG_GFCKEDITOR_UploadHandler
}

add_action( 'admin_notices', array( 'ITSG_GF_WYSIWYG_CKEditor', 'admin_warnings' ), 20 );
load_plugin_textdomain( 'itsg_gf_wysiwyg_ckeditor', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );

require_once( plugin_dir_path( __FILE__ ).'gf_wysiwyg_ckeditor_settings.php' );

if ( !class_exists( 'ITSG_GF_WYSIWYG_CKEditor' ) ) {
    class ITSG_GF_WYSIWYG_CKEditor {
	private static $name = 'CKEditor WYSIWYG for Gravity Forms';
    private static $slug = 'gravity-forms-wysiwyg-ckeditor';
	
        /*
         * Construct the plugin object
         */
        public function __construct() {	// register plugin functions through 'plugins_loaded' -
			// this delays the registration until all plugins have been loaded, ensuring it does not run before Gravity Forms is available.
            add_action( 'plugins_loaded', array( &$this, 'register_actions' ) );
		} // END __construct
		
		/*
         * Register plugin functions
         */
		function register_actions() {
		// register actions
            if ( self::is_gravityforms_installed() ) {
				$ckeditor_settings = self::get_options();
				
				//start plug in

				add_action( 'wp_ajax_itsg_gf_wysiwyg_ckeditor_upload', array( &$this, 'itsg_gf_wysiwyg_ckeditor_upload' ) );
				add_action( 'wp_ajax_nopriv_itsg_gf_wysiwyg_ckeditor_upload', array( &$this, 'itsg_gf_wysiwyg_ckeditor_upload' ) );

				add_action( 'gform_enqueue_scripts', array( &$this, 'enqueue_scripts' ), 10, 2 );
				add_filter( 'gform_save_field_value', array( &$this, 'save_field_value' ), 10, 4 );
				add_action( 'gform_field_standard_settings', array( &$this, 'ckeditor_field_settings' ), 10, 2 );
				add_filter( 'gform_tooltips', array( &$this, 'ckeditor_field_tooltips' ) );
				add_action( 'gform_editor_js', array( &$this, 'ckeditor_editor_js' ) );
				add_action( 'gform_field_css_class', array( &$this, 'ckeditor_field_css_class' ), 10, 3 );
				add_filter( 'gform_field_content',  array( &$this, 'ckeditor_field_content' ), 10, 5 );
				add_filter( 'gform_counter_script', array( &$this, 'ckeditor_counter_script_js' ), 10, 4 );
				add_filter( 'gform_merge_tag_filter', array( &$this, 'decode_wysiwyg_frontend_confirmation' ), 10, 5 );
				add_filter( 'gform_entry_field_value', array( &$this, 'decode_wysiwyg_backend_and_gravitypdf' ), 10, 4 );
				add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array( &$this, 'plugin_action_links' ) );
				
				if ( self::is_minimum_php_version() ) {
					require_once( plugin_dir_path( __FILE__ ).'gravitypdf/gravitypdf.php' );
				}

				// enqueue in entry editor and form editor
				if( ( 'gf_entries' == RGForms::get('page') && 'edit' === RGForms::post('screen_mode') ) || ( 'gf_edit_forms' == RGForms::get('page') && RGForms::get('id') && !RGForms::get('view') && 'on' == $ckeditor_settings['enable_in_form_editor'] ) ) {
					if ( get_option( 'gform_enable_noconflict' ) && 'on' == $ckeditor_settings['enable_in_form_editor'] && 'on' !== $ckeditor_settings['enable_override_gfnoconflict'] ) {
						add_action( 'admin_notices', array( &$this, 'admin_warnings_noconflict'), 20 ); // admin warning if ckeditor enabled in form editor and GF no conflict enabled
					} else {
						if ( 'on' == $ckeditor_settings['enable_override_gfnoconflict'] ) {
							add_filter( 'gform_noconflict_scripts', array( &$this, 'register_noconflictscript' ) );
						}
						add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_js' ) ); // enqueue JavaScript files
						add_action( 'admin_footer', array( &$this, 'ckeditor_script_js' ) ); // enqueue inline JavaScript
					}
				} elseif ( 'gf_settings' == RGForms::get('page') ) {
					// add settings page
					RGForms::add_settings_page( 'WYSIWYG CKEditor', array( 'ITSG_GF_WYSIWYG_ckeditor_settings_page', 'settings_page' ), self::get_base_url() . '/images/user-registration-icon-32.png' );

					if ( ( 'WYSIWYG+CKEditor' == RGForms::get('subview') || 'WYSIWYG CKEditor' == RGForms::get('subview') ) && !self::is_minimum_php_version() ) {
						add_action( 'admin_notices', array( &$this, 'admin_warnings_minimum_php_version'), 20 );
					}
					
				}
				
				if ( $ckeditor_settings['enable_upload_image'] && self::is_minimum_php_version() ) {
					// handles the change upload path settings
					add_filter( 'gform_upload_path', array( &$this, 'change_upload_path' ), 10, 2 );
				}
			}
		} // END register_actions
		
		function itsg_gf_wysiwyg_ckeditor_upload() {
			$CKEditorFuncNum = isset( $_GET['CKEditorFuncNum'] ) ? $_GET['CKEditorFuncNum'] : null;
			if ( is_null( $CKEditorFuncNum ) ) {
				die( "<script>
				window.parent.CKEDITOR.tools.callFunction(
				'',
				'',
				'ERROR: Failed to pass CKEditorFuncNum');
				</script>" );
			}
			
			$form_id = isset( $_GET['form_id'] ) ? $_GET['form_id'] : null;	

			if ( is_null( $form_id ) ) {
				die( "<script>
				window.parent.CKEDITOR.tools.callFunction('',
				'',
				'ERROR: Failed to get form_id');
				</script>" );
			}
			
			// get target path - also responsible for creating directories if path doesnt exist
			$target = GFFormsModel::get_file_upload_path( $form_id, null );
			$target_path = pathinfo( $target['path'] );
			$target_url = pathinfo( $target['url'] );
			
			// get Ajax Upload options
			$ckeditor_settings = self::get_options();
			
			// calculate file size in KB from MB
			$file_size = $ckeditor_settings['setting_upload_filesize'];
			$file_size_kb = $file_size * 1024 * 1024;
			
			// push options to upload handler
			$options = array(
				'paramName' => 'upload',
				'param_name' => 'upload',
				'CKEditorFuncNum' => $CKEditorFuncNum,
				'upload_dir' => $target_path['dirname'].'/',
				'upload_url' => $target_url['dirname'].'/',
				'image_versions' => array(
					'' => array(
					'max_width' => empty( $ckeditor_settings['setting_upload_filewidth'] ) ? null : $ckeditor_settings['setting_upload_filewidth'],
					'max_height' => empty( $ckeditor_settings['setting_upload_fileheight'] ) ? null : $ckeditor_settings['setting_upload_fileheight'],
					'jpeg_quality' => empty( $ckeditor_settings['setting_upload_filejpegquality'] ) ? null : $ckeditor_settings['setting_upload_filejpegquality']
					)
				),
				'accept_file_types' => empty( $ckeditor_settings['setting_upload_filetype'] ) ? '/(\.|\/)(png|tif|jpeg|jpg|gif)$/i' : '/(\.|\/)('.$ckeditor_settings['setting_upload_filetype'].')$/i',
				'max_file_size' => empty( $file_size_kb ) ? null : $file_size_kb
			);
			
			if ( class_exists( 'ITSG_GFCKEDITOR_UploadHandler' ) ) {
				// initialise the upload handler and pass the options
				$upload_handler = new ITSG_GFCKEDITOR_UploadHandler( $options );
			}
			
			// terminate the function
			die(); 
		} // END itsg_gf_wysiwyg_ckeditor_upload
		
		/* 
		 *   Changes the upload path for Gravity Form uploads.
		 *   Changes made by this function will be seen when the Gravity Forms function  GFFormsModel::get_file_upload_path() is called.
		 *   The default upload path applied by this function matches the default for Gravity forms:
		 *   /gravity_forms/{form_id}-{hashed_form_id}/{month}/{year}/
		 */
		function change_upload_path( $path_info, $form_id ) {
			$ckeditor_settings = self::get_options();
			$file_dir = $ckeditor_settings['setting_upload_filedir'];
			
			if ( 0 != strlen( $file_dir ) ) {
				// Generate the yearly and monthly dirs
				$time            = current_time( 'mysql' );
				$y               = substr( $time, 0, 4 );
				$m               = substr( $time, 5, 2 );
				
				// removing leading forward slash, if present
				if( '/' == $file_dir[0] ) {
					$file_dir = ltrim( $file_dir, '/' );
				}
				
				// remove leading forward slash, if present
				if( '/' == substr( $file_dir, -1 ) ) {
					$file_dir = rtrim( $file_dir, '/' );
				}
				
				// if {form_id} keyword used, replace with current form id
				if ( false !== strpos( $file_dir, '{form_id}' ) ) {
					$file_dir = str_replace( '{form_id}', $form_id, $file_dir );
				}
				
				// if {hashed_form_id} keyword used, replace with hashed current form id
				if ( false !== strpos( $file_dir, '{hashed_form_id}' ) ) {
					$file_dir = str_replace( '{hashed_form_id}', wp_hash( $form_id), $file_dir );
				}
				
				// if {year} keyword used, replace with current year
				if ( false !== strpos($file_dir,'{year}') ) {
					$file_dir = str_replace( '{year}', $y, $file_dir );
				}
				
				// if {month} keyword used, replace with current month
				if ( false !== strpos( $file_dir, '{month}' ) ) {
					$file_dir = str_replace( '{month}', $m, $file_dir );
				}
				
				// if {user_id} keyword used, replace with current user id
				if ( false !== strpos( $file_dir, '{user_id}' ) ) {
					if ( isset( $_POST['entry_user_id'] ) ) {
						$entry_user_id = $_POST['entry_user_id'];
						$file_dir = str_replace( '{user_id}', $entry_user_id, $file_dir );
					} else {
						$user_id = get_current_user_id() ? get_current_user_id() : '0';
						$file_dir = str_replace( '{user_id}', $user_id, $file_dir );
					}
				}
				
				// if {hashed_user_id} keyword used, replace with hashed current user id
				if ( false !== strpos( $file_dir, '{hashed_user_id}' ) ) {
					if ( isset( $_POST['entry_user_id'] ) ) {
						$entry_user_id = $_POST['entry_user_id'];
						$hashed_entry_user_id = wp_hash( $entry_user_id );
						$file_dir = str_replace( '{hashed_user_id}', $hashed_entry_user_id, $file_dir );
					} else {
						$hashed_user_id = wp_hash( is_user_logged_in() ? get_current_user_id() : '0' );
						$file_dir = str_replace( '{hashed_user_id}', $hashed_user_id, $file_dir );
					}
				}
				
				$upload_dir = wp_upload_dir(); // get WordPress upload directory information - returns an array
				
				$path_info['path']	= $upload_dir['basedir'].'/'.$file_dir.'/';  // set the upload path
				$path_info['url']	= $upload_dir['baseurl'].'/'.$file_dir.'/';  // set the upload URL
			}
			return $path_info;
		} // END change_upload_path
		
		/* 
		 *   Converts php.ini memory limit string to bytes.
		 *   For example, 2MB would convert to 2097152
		 */
		public static function return_bytes( $val ) {
			$val = trim( $val );
			$last = strtolower( $val[ strlen( $val ) -1 ] );
			
			switch( $last ) {
				case 'g':
					$val *= 1024;
				case 'm':
					$val *= 1024;
				case 'k':
					$val *= 1024;
			}
			return $val;
		} // END return_bytes
		
		/* 
		 *   Determines the maximum upload file size.
		 *   Retrieves three values from php.ini and returns the smallest.
		 */
		public static function max_file_upload_in_bytes() {
			//select maximum upload size
			$max_upload = self::return_bytes( ini_get( 'upload_max_filesize' ) );
			//select post limit
			$max_post = self::return_bytes( ini_get( 'post_max_size' ) );
			//select memory limit
			$memory_limit = self::return_bytes( ini_get( 'memory_limit' ) );
			// return the smallest of them, this defines the real limit
			return min( $max_upload, $max_post, $memory_limit );
		} // END max_file_upload_in_bytes
		
		function register_noconflictscript( $scripts ) {
			$scripts[] = 'ITSG_gf_wysiwyg_ckeditor_js';
			$scripts[] = 'ITSG_gf_wysiwyg_ckeditor_jquery_adapter';
			return $scripts;
		} // END register_noconflictscript
		
		function enqueue_js() {
			wp_enqueue_script( 'ITSG_gf_wysiwyg_ckeditor_js', plugin_dir_url( __FILE__ ) . 'ckeditor/ckeditor.js' );
			wp_enqueue_script( 'ITSG_gf_wysiwyg_ckeditor_jquery_adapter', plugin_dir_url( __FILE__ ) . 'ckeditor/adapters/jquery.js' );
		} // END enqueue_js
		
		/*
         * Add 'Settings' link to plugin in WordPress installed plugins page
         */
		function plugin_action_links( $links ) {

			$action_links = array(
				'settings' => '<a href="' . admin_url( 'admin.php?page=gf_settings&subview=WYSIWYG+CKEditor' ) . '" >' . __( 'Settings', 'gravity-forms-wysiwyg-ckeditor' ) . '</a>',
			);

			return array_merge( $action_links, $links );
		} // END plugin_action_links
		
		/* 
		 *   Handles the plugin options.
		 *   Default values are stored in an array.
		 */ 
		public static function get_options() {
			$defaults = array(
				'enable_in_form_editor' => 'on',
				'enable_bold' => 'on',
				'enable_italic' => 'on',
				'enable_underline' => 'on',
				'enable_pastetext' => 'on',
				'enable_pastefromword' => 'on',
				'enable_numberedlist' => 'on',
				'enable_bulletedlist' => 'on',
				'enable_outdent' => 'on',
				'enable_indent' => 'on',
				'enable_link' => 'on',
				'enable_unlink' => 'on',
				'enable_format' => 'on',
				'enable_font' => 'on',
				'enable_fontsize' => 'on',
				'setting_upload_filesize' => '2',
				'setting_upload_filetype' => 'png|tif|jpeg|jpg|gif',
				'setting_upload_filedir' => '/gravity_forms/{form_id}-{hashed_form_id}/{month}/{year}/',
				'setting_upload_filejpegquality' => '75',
				'setting_upload_filewidth' => '786',
				'setting_upload_fileheight' => '786',
				'enable_override_gfnoconflict' => 'off',
				'enable_upload_image' => 'off'
			);
			$options = wp_parse_args( get_option( 'ITSG_gf_wysiwyg_ckeditor_settings' ), $defaults );
			return $options;
		} // END get_options	
		
		/*
         * Place ckeditor JavaScript in footer, applies ckeditor to 'textarea' fields 
         */
		public function enqueue_scripts( $form, $is_ajax ) {
			if ( is_array( $form['fields'] ) || is_object( $form['fields'] ) ) {
				foreach ( $form['fields'] as $field ) {
					if ( $this->is_wysiwyg_ckeditor($field) ) {
						add_action( 'wp_footer', array( &$this, 'enqueue_js' ) ); // enqueue JavaScript files
						add_action( 'wp_footer', array( &$this, 'ckeditor_script_js' ) );  // enqueue inline JavaScript
						
						wp_deregister_script( 'gform_textarea_counter' ); // deregistered default textarea count script - the default script counts spaces
						wp_register_script( 'gform_textarea_counter', plugins_url( 'js/jquery.textareaCounter.plugin.js', __FILE__ ), array( 'jquery' ) ); // this script does not count spaces
					}
				}
			}
		} // END enqueue_scripts
		
		/*
         * Main JavaScript function
         */
		public function ckeditor_script_js() {
			$ckeditor_settings = self::get_options();
				?>
				<script>
				
				function itsg_gf_wysiwyg_ckeditor_function(self){
					<?php if ( 'gf_edit_forms' == RGForms::get( 'page' ) ) 
					// if in Gravity Forms form editor
					// destroy any active CKeditor instances first
					{ ?>
					for(i in CKEDITOR.instances) {
						CKEDITOR.instances[i].destroy();
					}
					<?php } ?>
					
				(function( $ ) {
					"use strict";
					$(function(){
						$('.gform_wrapper .gform_wysiwyg_ckeditor:not(.wysiwyg_exclude) textarea:not([disabled="disabled"],.wysiwyg_exclude), .gform_wrapper .gform_page:not([style="display:none;"],.wysiwyg_exclude) .gform_wysiwyg_ckeditor:not(.wysiwyg_exclude) textarea:not([disabled=disabled],.wysiwyg_exclude), #field_settings textarea:not([disabled=disabled],#gfield_bulk_add_input,#field_calculation_formula,.wysiwyg_exclude,#field_placeholder_textarea), .gf_entry_wrap .postbox .gform_wysiwyg_ckeditor:not(.wysiwyg_exclude) textarea:not([disabled=disabled],.wysiwyg_exclude)').each(function() {
							$(this).ckeditor(CKEDITOR.tools.extend( {<?php if ( ( 'gf_edit_forms'  !== RGForms::get('page') && 'gf_entries' !== RGForms::get( 'page' ) ) || rgar( $ckeditor_settings, 'enable_oembed' ) ) { ?>extraPlugins : '<?php if ( 'gf_edit_forms' !== RGForms::get( 'page' ) && 'gf_entries' !== RGForms::get( 'page' ) )echo 'wordcount,notification' ?><?php if ( rgar( $ckeditor_settings, 'enable_oembed' ) ) 
								echo ',oembed,widget,dialog' ?>', 
							<?php if ( 'gf_edit_forms' !== RGForms::get( 'page' ) && 'gf_entries' !== RGForms::get( 'page' ) ) {?>
							wordcount : {
								showParagraphs : false,
								showWordCount: false,
								showCharCount: true,
								maxCharCount: $(this).attr('data-maxlen'),
								hardLimit: true 
							}, 
							<?php } ?>
							<?php } ?>
							toolbar: [
								<?php   /* SOURCE */
								if ( rgar( $ckeditor_settings, 'enable_source' ) ) { echo "{ name: 'source', items: [ 'Source' ] },";} ?>
								{ name: 'basicstyles', items: [ <?php  /* BASIC STYLES */ 
								if ( rgar( $ckeditor_settings, 'enable_bold' ) ) { echo "'Bold',";} 
								if ( rgar( $ckeditor_settings, 'enable_italic' ) ) { echo "'Italic',";} 
								if ( rgar( $ckeditor_settings, 'enable_underline' ) ) { echo "'Underline',";} 
								if ( rgar( $ckeditor_settings, 'enable_strike' ) ) { echo "'Strike',";} 
								if ( rgar( $ckeditor_settings, 'enable_subscript' ) ) { echo "'Subscript',";}   
								if ( rgar( $ckeditor_settings, 'enable_superscript' ) ) { echo "'Superscript',";}   
								if ( rgar( $ckeditor_settings, 'enable_removeformat' ) ) { echo "'-', 'RemoveFormat'";} ?> 
								] },
								{ name: 'clipboard',  items: [ <?php  /* CLIPBOARD */
								if ( rgar( $ckeditor_settings, 'enable_cut' ) ) { echo "'Cut',";} 
								if ( rgar( $ckeditor_settings, 'enable_copy' ) ) { echo "'Copy',";} 
								if ( rgar( $ckeditor_settings, 'enable_paste' ) ) { echo "'Paste',";} 
								if ( rgar( $ckeditor_settings, 'enable_pastetext' ) ) { echo "'PasteText',";} 
								if ( rgar( $ckeditor_settings, 'enable_pastefromword' ) ) { echo "'PasteFromWord',";}   
								if ( rgar( $ckeditor_settings, 'enable_undo' ) ) { echo "'-', 'Undo',";}   
								if ( rgar( $ckeditor_settings, 'enable_redo' ) ) { echo "'Redo'";} ?> 
								] },
								{ name: 'paragraph', items: [ <?php  /* PARAGRAPH */
								if ( rgar( $ckeditor_settings, 'enable_numberedlist' ) ) { echo "'NumberedList',";} 
								if ( rgar( $ckeditor_settings, 'enable_bulletedlist' ) ) { echo "'BulletedList',";} 
								if ( rgar( $ckeditor_settings, 'enable_outdent' ) ) { echo "'-', 'Outdent',";} 
								if ( rgar( $ckeditor_settings, 'enable_indent' ) ) { echo "'Indent',";} 
								if ( rgar( $ckeditor_settings, 'enable_blockquote' ) ) { echo "'-', 'Blockquote',";} 
								if ( rgar( $ckeditor_settings, 'enable_creatediv' ) ) { echo "'CreateDiv',";} 
								if ( rgar( $ckeditor_settings, 'enable_justifyleft' ) ) { echo "'-', 'JustifyLeft',";} 
								if ( rgar( $ckeditor_settings, 'enable_justifycenter' ) ) { echo "'JustifyCenter',";} 
								if ( rgar( $ckeditor_settings, 'enable_justifyright' ) ) { echo "'JustifyRight',";} 
								if ( rgar( $ckeditor_settings, 'enable_justifyblock' ) ) { echo "'JustifyBlock',";} 
								if ( rgar( $ckeditor_settings, 'enable_bidiltr' ) ) { echo "'-','BidiLtr',";}   
								if ( rgar( $ckeditor_settings, 'enable_bidirtl' ) ) { echo "'BidiRtl',";}   
								if ( rgar( $ckeditor_settings, 'enable_language' ) ) { echo "'Language'";} ?> 
								] },
								{ name: 'links', items: [ <?php  /* LINKS */
								if ( rgar( $ckeditor_settings, 'enable_link' ) ) { echo "'Link',";}   
								if ( rgar( $ckeditor_settings, 'enable_unlink' ) ) { echo "'Unlink',";}   

								if ( rgar( $ckeditor_settings, 'enable_anchor' ) ) { echo "'Anchor'";}
								if ( rgar( $ckeditor_settings, 'enable_oembed' ) ) { echo "'oembed'";} ?> 
								] },
								{ name: 'document', items: [ <?php /* DOCUMENT */
								if ( rgar( $ckeditor_settings, 'enable_preview' ) ) { echo "'Preview',";}
								if ( rgar( $ckeditor_settings, 'enable_print' ) ) { echo "'Print',";} ?> 
								] },
								{ name: 'editing', items: [ <?php   /* EDITING */
								if ( rgar( $ckeditor_settings, 'enable_find' ) ) { echo "'Find',";}
								if ( rgar( $ckeditor_settings, 'enable_replace' ) ) { echo "'Replace',";}
								if ( rgar( $ckeditor_settings, 'enable_selectall' ) ) { echo "'-', 'SelectAll',";}
								if ( rgar( $ckeditor_settings, 'enable_scayt' ) ) { echo "'-', 'Scayt'";} ?>
								] },
								{ name: 'insert', items: [ <?php  /* INSERT */
								if ( rgar( $ckeditor_settings, 'enable_image' ) || ( rgar( $ckeditor_settings, 'enable_upload_image' ) && self::is_minimum_php_version() ) ) { echo "'Image',";}
								if ( rgar( $ckeditor_settings, 'enable_flash' ) ) { echo "'Flash',";}
								if ( rgar( $ckeditor_settings, 'enable_table' ) ) { echo "'Table',";}
								if ( rgar( $ckeditor_settings, 'enable_horizontalrule' ) ) { echo "'HorizontalRule',";}
								if ( rgar( $ckeditor_settings, 'enable_smiley' ) ) { echo "'Smiley',";}
								if ( rgar( $ckeditor_settings, 'enable_specialchar' ) ) { echo "'SpecialChar',";}
								if ( rgar( $ckeditor_settings, 'enable_pagebreak' ) ) { echo "'PageBreak',";}
								if ( rgar( $ckeditor_settings, 'enable_iframe' ) ) { echo "'Iframe'";} ?>
								] },
								'/',
								{ name: 'styles', items: [ <?php  /* STYLES */
								if ( rgar( $ckeditor_settings, 'enable_styles' ) ) { echo "'Styles',";}
								if ( rgar( $ckeditor_settings, 'enable_format' ) ) { echo "'Format',";}
								if ( rgar( $ckeditor_settings, 'enable_font' ) ) { echo "'Font',";}
								if ( rgar( $ckeditor_settings, 'enable_fontsize' ) ) { echo "'FontSize'";} ?>
								] },
								{ name: 'colors', items: [ <?php  /* COLOURS */
								if ( rgar( $ckeditor_settings, 'enable_textcolor' ) ) { echo "'TextColor',";}
								if ( rgar( $ckeditor_settings, 'enable_bgcolor' ) ) { echo "'BGColor'";} ?>
								] },
								{ name: 'tools', items: [ <?php  /* TOOLS */
								if ( rgar( $ckeditor_settings, 'enable_maximize' ) ) { echo "'Maximize',";}
								if ( rgar( $ckeditor_settings, 'enable_showblocks' ) ) { echo "'ShowBlocks'";} ?>
								] },
								{ name: 'about', items: [ <?php  /* ABOUT */
								if ( rgar( $ckeditor_settings, 'enable_about' ) ) { echo "'About'";} ?>
								] }],
							allowedContent: true
							 <?php if ( rgar( $ckeditor_settings, 'enable_upload_image') && self::is_minimum_php_version() ) {?>, filebrowserImageUploadUrl  : '<?php echo admin_url('admin-ajax.php'); ?>?action=itsg_gf_wysiwyg_ckeditor_upload&form_id=<?php 
								$is_entry_detail = GFCommon::is_entry_detail();
								if ( $is_entry_detail ) {
									$form_id = $_GET['id'];
									echo $form_id."'";
								} else {
									echo "'+jQuery('input[name=gform_submit]').val()";
								}
								?> <?php } ?>
							}));
						
							CKEDITOR.on( 'dialogDefinition', function( event ) {
								var dialogName = event.data.name;
								var dialogDefinition = event.data.definition;
								event.data.definition.resizable = CKEDITOR.DIALOG_RESIZE_NONE;

								if ( dialogName == 'link' ) {
								 var infoTab = dialogDefinition.getContents( 'info' );
								 infoTab.remove( 'protocol' );
								 dialogDefinition.removeContents( 'target' );
								 dialogDefinition.removeContents( 'advanced' );
								}
								
								if ( dialogName == 'image' ) {
									dialogDefinition.removeContents( 'advanced' );
									dialogDefinition.removeContents( 'Link' );
									var infoTab = dialogDefinition.getContents( 'info' );
									infoTab.remove( 'txtBorder' );
									infoTab.remove( 'txtHSpace' );
									infoTab.remove( 'txtVSpace' );
									infoTab.remove( 'txtWidth' );
									infoTab.remove( 'txtHeight' );
									infoTab.remove( 'ratioLock' );
									// infoTab.remove( 'htmlPreview' ); -- currently disabled, causes 'TypeError: this.preview is null' error
									<?php if ( rgar( $ckeditor_settings, 'enable_upload_image') && self::is_minimum_php_version() ) {?>
									// handle default tab
									var dialog = event.data.definition;
									var oldOnShow = dialog.onShow;
									dialog.onShow = function() {
										 oldOnShow.apply(this, arguments);
										if ( this.imageEditMode === false ) { 
											this.selectPage('Upload'); 
										}
									};
									<?php } ?>
							  }
							});
							
							<?php if ( 'gf_edit_forms' == RGForms::get( 'page' ) ) { ?>
							for (var i in CKEDITOR.instances) {
								// wrap in half second timeout provides performance improvement by stopping 'change' event from firing multiple times
								setTimeout(function(){
									CKEDITOR.instances[i].on('change', function(event) {
										if (event.sender.name == 'field_description') {
											SetFieldDescription(this.getData());
										} else if (event.sender.name  == 'field_content') {
											SetFieldProperty('content', this.getData());
										} else if (event.sender.name  == 'field_default_value_textarea') {
											SetFieldProperty('defaultValue', this.getData());
										} else if (event.sender.name  == 'infobox_more_info_field') {
											SetFieldProperty('infobox_more_info_field', this.getData());
										} else {
											CKEDITOR.instances[i].updateElement();  
										}
										
									});
								},500);
								CKEDITOR.instances[i].on('loaded', function(event) {
									if (event.sender.name == 'field_description') {
										SetFieldDescription(this.getData());
									} else if (event.sender.name  == 'field_content') {
										SetFieldProperty('content', this.getData());
									} else if (event.sender.name  == 'field_default_value_textarea') {
										SetFieldProperty('defaultValue', this.getData());
									} else if (event.sender.name  == 'infobox_more_info_field') {
										SetFieldProperty('infobox_more_info_field', this.getData());
									} else {
										CKEDITOR.instances[i].updateElement();  
									}
									this.setData(this.getData())
								});
							}
							<?php } ?>
							
							<?php if ( self::is_dpr_installed() && !is_admin() ) { ?>
							var changed;
							for (var i in CKEDITOR.instances) {
								CKEDITOR.instances[i].on('change', function() {
									CKEDITOR.instances[i].updateElement();    
									changed = true;
								});
							}
							<?php } ?>
						});

					});
				}(jQuery));
				
				}
				
				<?php if ( 'gf_edit_forms' == RGForms::get( 'page' ) ) { ?>
				
				// runs the main function when field settings have been opened in the form editor
				
				jQuery(document).bind('gform_load_field_settings', function($) {
					// wrap in half second timeout provides perceived performance improvement by delaying the CKeditor load until the field settings has loaded
					// currently commented out due to issues during initial testing
					//setTimeout(function(){	
						itsg_gf_wysiwyg_ckeditor_function(jQuery(this));
					//},500);
					});
					
				// destroy all existing CKEditor instances when field is deleted in form editor - hooks into existing StartDeleteField function.
				// backup original StartDeleteField
				var StartDeleteFieldoldCK = StartDeleteField;
				StartDeleteField = function(field) {
					// destroy all CKEditor instances
					for(name in CKEDITOR.instances) {
						CKEDITOR.instances[name].destroy(true);
					}
					// call original StartDeleteField
					StartDeleteFieldoldCK(field);
				};
				
				// destroy all existing CKEditor instances when fileupload field is switched between single and multi upload in form editor
				jQuery('input#field_multiple_files').on('change', function(event) {
					for(name in CKEDITOR.instances) {
						CKEDITOR.instances[name].destroy(true);
					}
				});
				
				// destroy all existing CKEditor instances when product field is switched between field type in form editor - hooks into existing StartChangeProductType function.
				// backup original StartChangeProductType
				var StartChangeProductTypeCK = StartChangeProductType;
				StartChangeProductType = function(field) {
					// destroy all CKEditor instances
					for(name in CKEDITOR.instances) {
						CKEDITOR.instances[name].destroy(true);
					}
					// call original StartChangeProductType
					StartChangeProductTypeCK(field);
				};
				
				// destroy all existing CKEditor instances when shipping field is switched between field type in form editor - hooks into existing StartChangeShippingType function.
				// backup original StartChangeShippingType
				var StartChangeShippingTypeCK = StartChangeShippingType;
				StartChangeShippingType = function(field) {
					// destroy all CKEditor instances
					for(name in CKEDITOR.instances) {
						CKEDITOR.instances[name].destroy(true);
					}
					// call original StartChangeShippingType
					StartChangeShippingTypeCK(field);
				};
				
				// destroy all existing CKEditor instances when option field is switched between field type in form editor - hooks into existing StartChangeInputType function.
				// backup original StartChangeInputType
				var StartChangeInputTypeCK = StartChangeInputType;
				StartChangeInputType = function(field) {
					// destroy all CKEditor instances
					for(name in CKEDITOR.instances) {
						CKEDITOR.instances[name].destroy(true);
					}
					// call original StartChangeInputType
					StartChangeInputTypeCK(field);
				};
				
				<?php } else { 
					if ( 'gf_entries' == RGForms::get( 'page' ) ) { ?>
					
					// runs the main function when the page loads -- entry editor

					jQuery(document).ready(function($) {itsg_gf_wysiwyg_ckeditor_function(jQuery(this));  }); // In version 1.6.5 this was changed so it only loaded in the entries editor.
					
					<?php } else { ?>
					
					// runs the main function when the page loads -- front end forms

					jQuery(document).bind('gform_post_render', function($) {itsg_gf_wysiwyg_ckeditor_function(jQuery(this));  });

					<?php }  ?>


				<?php }  ?>
				</script>
				<?php
		} // END ckeditor_script_js
		
		/*
         * Customises 'Paragraph Text' field output to 
		 *  1. apply 'gform_wysiwyg_ckeditor' class to ckeditor fields in the wp-admin
		 *  2. include character limit details and CSS class for admin area
         */
		public function ckeditor_field_content( $content, $field, $value, $lead_id, $form_id ){
			if ( $this->is_wysiwyg_ckeditor( $field ) ) {
				if ( is_admin() ){
					$content = str_replace( "class='", "class='gform_wysiwyg_ckeditor ", $content );
				} else {
					$label = rgar( $field, 'label' );
					$limit = ( '' == rgar( $field, 'maxLength' ) ? 'unlimited' : rgar( $field, 'maxLength' ) );
					$content = str_replace( "<textarea ", "<textarea data-maxlen='".$limit."' ", $content);
				}	
			}
			return $content;
		} // END ckeditor_field_content
				
		/*
         * Customises character limit count down for NON-CKEditor fields to match what CKEditor provides 
		 * - note that these fields DO count spaces, where as CKEDitor does NOT count spaces.
		 * Output: Characters: [number of characters in field]/[limit on field]
         */
		public function ckeditor_counter_script_js( $script, $form_id, $input_id, $max_length ){
			$field_id_number = substr( $input_id, strrpos($input_id, '_') + 1);
			$form = GFFormsModel::get_form_meta( $form_id );
			$field = GFFormsModel::get_field( $form, $field_id_number );
			if ( $this->is_wysiwyg_ckeditor( $field ) ) {
				return ""; 
			} else {
				$script = "jQuery('#{$input_id}').textareaCount(" .
							"    {" .
							"    'maxCharacterSize': {$max_length}," .
							"    'originalStyle': 'ginput_counter'," .
							"    'displayFormat' : '" .  esc_js( __( 'Characters', 'gravity-forms-wysiwyg-ckeditor' ) ) . ": #input/$max_length'" .
							"    });";	   
				return $script;
			}
		} // END ckeditor_counter_script_js
			
		/*
         * Applies CSS class to 'Paragraph text' fields when CKEditor is enabled
         */
		public function ckeditor_field_css_class( $classes, $field, $form ) {
			if ( $this->is_wysiwyg_ckeditor( $field ) ) {
				 $classes .= ' gform_wysiwyg_ckeditor';
			}
            return $classes;
        } // END ckeditor_field_css_class
		
		/*
         * Applies 'Enable WYSIWYG CKEditor' option to 'Paragraph Text' field
         */
		public function ckeditor_field_settings($position, $form_id) {
			if ( 25 == $position ) {
				?>
				<li class="wysiwyg_field_setting_wysiwyg_ckeditor field_setting" style="display:list-item;">
					<input type="checkbox" id="field_enable_wysiwyg_ckeditor"/>
					<label for="field_enable_wysiwyg_ckeditor" class="inline">
						<?php _e( 'Enable WYSIWYG (CKEditor)', 'gravity-forms-wysiwyg-ckeditor' ); ?>
					</label>
					<?php gform_tooltip( 'form_field_enable_wysiwyg_ckeditor' ) ?><br/>
				</li>
			<?php
			}
		} // END ckeditor_field_settings
		
		/*
         * JavaScript for form editor
         */
		public function ckeditor_editor_js() {
			?>
			<script>
				// handles when the field is opened in the form editor
				jQuery(document).bind("gform_load_field_settings", function (event, field, form) {
					var field_type = field['type'];
					if ('post_content' == field_type  || 'textarea' == field_type || ('post_custom_field' == field_type  && 'textarea' == field['inputType'])) {

						var $wysiwyg_container = jQuery(".wysiwyg_field_setting_wysiwyg_ckeditor");

						$wysiwyg_container.show();

						var enable_wysiwyg_ckeditor = ( typeof field['enable_wysiwyg_ckeditor'] != 'undefined' && field['enable_wysiwyg_ckeditor'] != '' ) ? field['enable_wysiwyg_ckeditor'] : false;

						if ( enable_wysiwyg_ckeditor != false ) {
							//check the checkbox if previously checked
							$wysiwyg_container.find("input:checkbox").attr("checked", "checked");
						} else {
							$wysiwyg_container.find("input:checkbox").removeAttr("checked");
						}
					}
				});
				
				// handles when the 'Enable WYSIWYG (CKEditor)' tick box is used in a field in the form editor

				jQuery(".wysiwyg_field_setting_wysiwyg_ckeditor input").click(function () {
					if (jQuery(this).is(":checked")) {
						SetFieldProperty('enable_wysiwyg_ckeditor', 'true');
					} else {
						SetFieldProperty('enable_wysiwyg_ckeditor', '');
					}
				});
			</script>
		<?php
		} // END ckeditor_editor_js
		
		/*
         * Tooltip for field in form editor
         */
		public function ckeditor_field_tooltips( $tooltips ){
			$tooltips['form_field_enable_wysiwyg_ckeditor'] = '<h6>'.__( 'Enable WYSIWYG', 'gravity-forms-wysiwyg-ckeditor' ).'</h6>'.__( 'Check this box to turn this field into a WYSIWYG editor, using CKEditor.', 'gravity-forms-wysiwyg-ckeditor' );
			return $tooltips;
		} // END ckeditor_field_tooltips

		/*
         * Checks if field is CKEditor enabled
         */
		public function is_wysiwyg_ckeditor( $field ) {
			$field_type = self::get_type( $field );
			if ( 'post_content' == $field_type ||
				'textarea' == $field_type ||
				( 'post_custom_field' == $field_type && 'textarea' == $field['inputType'] ) ) {
				if ( isset( $field['enable_wysiwyg_ckeditor'] ) ) {
					return $field['enable_wysiwyg_ckeditor'] == 'true';
				}
			}
			return false;
		} // END is_wysiwyg_ckeditor
		
		/*
         * Get field type
         */
		private static function get_type( $field ) {
			$type = '';
			if ( isset( $field['type'] ) ) {
				$type = $field['type'];
				if ( 'post_custom_field' == $type ) {
					if ( isset( $field['inputType'] ) ) {
						$type = $field['inputType'];
					}
				}
				return $type;
			}
		} // END get_type
	
		/*
         * Modifies the value before saved to the database - removes line spaces
         */
		public function save_field_value( $value, $lead, $field, $form ) {
			if ( $this->is_wysiwyg_ckeditor( $field ) ) {
				$value = rgpost( "input_{$field['id']}" );
				$value = preg_replace( "/\r|\n/", "", $value );
			}
			return $value;
		} // END save_field_value
		
		/*
         * Warning message if Gravity Forms is installed and enabled
         */
		public static function admin_warnings() {
			if ( !self::is_gravityforms_installed() ) {
				printf(
					'<div class="error"><h3>%s</h3><p>%s</p><p>%s</p></div>',
						__( 'Warning', 'gravity-forms-wysiwyg-ckeditor' ),
						sprintf ( __( 'The plugin %s requires Gravity Forms to be installed.', 'gravity-forms-wysiwyg-ckeditor' ), '<strong>'.self::$name.'</strong>' ),
						sprintf ( esc_html__( 'Please %sdownload the latest version of Gravity Forms%s and try again.', 'gravity-forms-wysiwyg-ckeditor' ), '<a href="https://www.e-junkie.com/ecom/gb.php?cl=54585&c=ib&aff=299380" target="_blank" >', '</a>' )
				);
			}
		} // END admin_warnings
		
		/*
         * Warning message if Gravity Forms no conflict mode enabled
         */
		public static function admin_warnings_noconflict() {
			printf(
				'<div class="error"><h3>%s</h3><p>%s</p><p>%s</p><p>%s</p>%s</div>',
					__( 'Warning', 'gravity-forms-wysiwyg-ckeditor' ),
					sprintf ( __( 'The plugin %s has detected a configuration conflict.', 'gravity-forms-wysiwyg-ckeditor' ), '<strong>'.self::$name.'</strong>' ),
					sprintf ( esc_html__( "No-Conflict Mode is currently 'On' in the %sGravity Forms Settings%s page.", 'gravity-forms-wysiwyg-ckeditor' ), '<a href="' . admin_url( 'admin.php?page=gf_settings' ) . '" >', '</a>' ),
					__( "The no-conflict option allows Gravity Form users to disable all third party scripts from running in Gravity Form administration pages - <strong>this will stop CKEditor WYSIWYG for Gravity Forms from working in administration pages</strong>.", 'gravity-forms-wysiwyg-ckeditor' ),
					sprintf ( "%s:<ol><li style='list-style-type: decimal'>%s</li><li style='list-style-type: decimal'>%s</li><li style='list-style-type: decimal'>%s</li></ol>", 
					__( 'There are three options to resolve this issue:', 'gravity-forms-wysiwyg-ckeditor' ),
					sprintf ( esc_html__( "Set the 'No-Conflict Mode' option to 'Off' in the %sGravity Forms Settings%s page.", 'gravity-forms-wysiwyg-ckeditor' ), '<a href="' . admin_url( 'admin.php?page=gf_settings' ) . '" >', '</a>' ),
					sprintf ( esc_html__( "Disable the 'Enable in form editor' option in the %sCKEditor Settings%s page.", 'gravity-forms-wysiwyg-ckeditor' ), '<a href="' . admin_url( 'admin.php?page=gf_settings&subview=WYSIWYG_ckeditor' ) . '" >', '</a>' ),
					sprintf ( esc_html__( "Enable the 'Override Gravity Forms No-Conflict Mode' option in the %sCKEditor Settings%s page.", 'gravity-forms-wysiwyg-ckeditor' ), '<a href="' . admin_url( 'admin.php?page=gf_settings&subview=WYSIWYG_ckeditor' ) . '" >', '</a>' )
				)
			);
		} // END admin_warnings_noconflict
		
		/*
         * Warning message if Gravity Forms is installed and enabled
         */
		public static function admin_warnings_minimum_php_version() {
				printf(
					'<div class="error"><h3>%s</h3><p>%s</p><p>%s</p></div>',
						__( 'Warning', 'gravity-forms-wysiwyg-ckeditor' ),
						sprintf( __( 'The <strong>image upload</strong> feature requires a minimum of PHP version 5.4.', 'gravity-forms-wysiwyg-ckeditor' ) ),
						sprintf( __( 'You are running an PHP version %s. Contact your web hosting provider to update.', 'gravity-forms-wysiwyg-ckeditor' ), phpversion() )
				);
		} // END admin_warnings_minimum_php_version
		
		/*
         * Check if GF is installed
         */
        private static function is_gravityforms_installed() {
			if ( !function_exists( 'is_plugin_active' ) || !function_exists( 'is_plugin_active_for_network' ) ) {
				require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
			}
			if (is_multisite()) {
				return ( is_plugin_active_for_network('gravityforms/gravityforms.php' ) || is_plugin_active( 'gravityforms/gravityforms.php' ) );
			} else {
				return is_plugin_active( 'gravityforms/gravityforms.php' );
			}
        } // END is_gravityforms_installed
		
		/*
         * Check if PHP version is at least 5.4
         */
        private static function is_minimum_php_version() {
			return version_compare( phpversion(), '5.4', '>=' );
        } // END is_minimum_php_version
		
		/*
         * Check if Gravity Forms - Data Persistence Reloaded is installed
         */
        private function is_dpr_installed() {
            return function_exists( 'ri_gfdp_ajax' );
        } // END is_dpr_installed
		
		/*
         * Get plugin url
         */
		 private function get_base_url(){
			return plugins_url( null, __FILE__ );
		} // END get_base_url
		
		/*
         * decodes the value before being displayed in the front end confirmation - for gravity wiz better pre-confirmation 
         */ 
		public function decode_wysiwyg_frontend_confirmation( $value, $merge_tag, $modifier, $field, $raw_value ) {
			if ( $this->is_wysiwyg_ckeditor( $field ) ) {
				return $raw_value;
			}
			return $value;
		} // END decode_wysiwyg_frontend_confirmation
		
		/*
         * decodes the value before being displayed in the entry editor and Gravity PDF 3.x
         */ 
		public function decode_wysiwyg_backend_and_gravitypdf( $value, $field, $lead, $form ) {
			if ( $this->is_wysiwyg_ckeditor( $field ) ) {
				return htmlspecialchars_decode( $value );
			}
			return $value;
		} // END decode_wysiwyg_backend_and_gravitypdf

    }
    $ITSG_GF_WYSIWYG_CKEditor = new ITSG_GF_WYSIWYG_CKEditor();
}