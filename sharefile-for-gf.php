<?php
/**
 * @package Post-Link-Preview`
 */
/*
Plugin Name: ShareFile for Gravity Forms
Plugin URI: http://holisticnetworking.net/sharefile-for-gravity-forms/
Description: Plugin creates form fields for uploading to ShareFile
Version: 0.1b
Author: Thomas J Belknap
Author URI: http://holisticnetworking.net
License: GPLv2 or later
*/
if (class_exists("GFForms")) {
	GFForms::include_addon_framework();
    class ShareFile extends GFAddOn {
        protected $_version = "0.1b"; 
        protected $_min_gravityforms_version = "1.9.1.1";
        protected $_slug = "sharefile";
        protected $_path = "sharefile-for-gf/sharefile-for-gf.php";
        protected $_full_path = __FILE__;
        protected $_title = "ShareFile Integration for Gravity Forms";
        protected $_short_title = "ShareFile Integration";
        
        
        function add_post_field($field_groups){
			foreach($field_groups as &$group){
				if($group["name"] == "standard_fields"){
					$group["fields"][] = array( "type" => "button", "class" => "button", "value" => __("Upload to sharefile", "gravityforms"), "onclick" => "StartAddField('sharefile');");
					break;
				}
			}
			return $field_groups;
		}
		
		function title( $title, $field_type ) {
			if( $field_type == "sharefile" ) :
				return "Upload to ShareFile";
			endif;
		}
		
		
		function upload_input($input, $field, $value, $lead_id, $form_id){
			if($field["type"] == "sharefile"){
				$input	= '<div class="upload_container">';
				$input	.= '<input type="file" name="sharefile[File]" id="sharefile-file" />';
				$input	.= '<input type="hidden" name="sharefile[Order]" id="sharefile-order" />';
				$input	.= '</div>';
			}
			return $input;
		}
		
		function editor_js(){
			?>
			<script type='text/javascript'>
				jQuery(document).ready(function($) {
					// Add all textarea settings to the "TOS" field plus custom "tos_setting"
					// fieldSettings["tos"] = fieldSettings["textarea"] + ", .tos_setting"; // this will show all fields that Paragraph Text field shows plus my custom setting
					// from forms.js; can add custom "tos_setting" as well
					fieldSettings["sharefile"] = ".label_setting, .description_setting, .admin_label_setting, .size_setting, .default_value_textarea_setting, .error_message_setting, .css_class_setting, .visibility_setting, .sharefile_setting";
			
					//binding to the load field settings event to initialize the checkbox
					$(document).bind("gform_load_field_settings", function(event, field, form){
						jQuery("#field_sharefile").attr("checked", field["field_sharefile"] == true);
						$("#field_sharefile_value").val(field["sharefile"]);
					});
				});
			</script>
			<?php
		}
		
		function frontend_scripts( $form ) {
			echo '<script type="text/javascript">jQuery(document).ready(function($){ $( "input", ".sgl-readonly" ).attr( "readonly", true ); });</script>';
			/* foreach( $form['fields'] as &$field ) :
				if( $field["type"] == 'sharefile' ) :
			 		print_r($field);
			 		//
			 	endif;
			endforeach; */
			return $form;
		}
		
		/**
		 * Override the plugin_settings_fields() function and return the configuration for the Add-On Settings.
		 * Field Rending and Updating is handled by the Framework.
		 *
		 * @return array
		 */
		public function plugin_settings_fields() {
			return array(
				array(
					"title"  => __("ShareFile Configuration", "gravityformsaggregator"),
					"description" => "These settings configure the system to send data to ShareFile though the use of it's API.",
					"fields" => array(
						array(
							"name"        => "hostname",
							"tooltip"     => __("The ShareFile site to which you want to send data.", "gravityformsaggregator"),
							"label"       => __("Host Name", "gravityformsaggregator"),
							"type"        => "text",
							"class"       => "medium",
							"placeholder" => "https://example.sharefile.com",
						),
						array(
							"name"    => "client_id",
							"tooltip" => __("The Client ID assigned by ShareFile.", "gravityformsaggregator"),
							"label"   => __("Client ID", "gravityformsaggregator"),
							"type"    => "text"
						),
						array(
							"name"    => "client_secret",
							"tooltip" => __("The Client Secret provided by ShareFile.", "gravityformsaggregator"),
							"label"   => __("Client Secret", "gravityformsaggregator"),
							"type"    => "text"
						),
						array(
							"name"    => "username",
							"tooltip" => __("The Sharefile username with which to access this host.", "gravityformsaggregator"),
							"label"   => __("User Name", "gravityformsaggregator"),
							"type"    => "text"
						),
						array(
							"name"    => "password",
							"tooltip" => __("The password associated with this account", "gravityformsaggregator"),
							"label"   => __("Password", "gravityformsaggregator"),
							"type"    => "text"
						)
					)
				),
				array(
					"title"  => __("Local Site Configuration", "gravityformsaggregator"),
					"fields" => array(
						array(
							"name"    => "enableResults",
							"tooltip" => __("Activate this setting on the central server to enable the results page."),
							"label"   => __("Enable Results Page", "gravitycontacts"),
							"type"    => "checkbox",
							"choices" => array(
								array(
									"label" => __("Enable Results", "gravityformsaggregator"),
									"name"  => "resultsEnabled"
								)
							)
						),
						array(
							"name"    => "identifier",
							"tooltip" => __("Specifies from which site the form was submitted (optional)"),
							"label"   => __("Site Identifier", "gravitycontacts"),
							"type"    => "text",
						)
					)
				)
			);
		}


		public function get_results_page_config() {
			$settings = $this->get_plugin_settings();
			return $settings["resultsEnabled"] ? array("title" => "Aggregation Results") : array();
		}
		
		public function get_entry_meta($entry_meta, $form_id) {
			$entry_meta['gf_aggregator_id'] = array(
				'label'                      => 'Site ID',
				'is_numeric'                 => false,
				'update_entry_meta_callback' => array($this, 'update_entry_meta'),
				'is_default_column'          => true, // default column on the entry list
				'filter'                     => array(
					'operators' => array("is", "isnot", "contains")
				)
			);
			return $entry_meta;
		}

		public function update_entry_meta($key, $entry, $form) {
			if ($key === "gf_aggregator_id") {
				$add_on_settings = $this->get_plugin_setting('identifier');
				return empty($add_on_settings) ? get_bloginfo() : $add_on_settings;
			}
		}
		
		/*
		// Showtime. Now we upload
		*/
		public function after_submission( $entry, $form ) {
			// Require the ShareFile API Class:
			require_once( plugin_dir_path( __FILE__ ) . 'sharefile.class.php' );
			// Our API settings:
			$addon_settings = $this->get_plugin_settings();
			if (!$addon_settings) {
				return;
			}
			// Initialize the ShareFile Object:
			$sf		= new ShareFile(
				$addon_settings['hostname'],  
				$addon_settings['client_id'], 
				$addon_settings['client_secret'], 
				$addon_settings['username'], 
				$addon_settings['password']
			);	
		}
		
		function init() {
			parent::init();
			add_filter( "gform_add_field_buttons", array( $this, "add_post_field" ) );
			add_filter( "gform_field_type_title", array( $this, "title" ), 10, 2 );
			add_filter( "gform_field_input", array( $this, "upload_input" ), 10, 5 );
			add_action( "gform_editor_js", array( $this, "editor_js" ) );
			add_filter( "gform_pre_render", array( $this, "frontend_scripts" ) );
		}
		
		function init_frontend() {
			
		}
    }
    $fs	= new ShareFile;
}
?>