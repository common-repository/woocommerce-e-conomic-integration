<?php
/**
 * Plugin Name: WooCommerce e-conomic Integration
 * Plugin URI: http://plugins.svn.wordpress.org/woocommerce-e-conomic-integration/
 * Description: An e-conomic API Interface. Synchronizes products, orders, Customers and more to e-conomic.
 * Also fetches inventory from e-conomic and updates WooCommerce
 * Version: 1.9.25
 * Author: wooconomics
 * Text Domain: woocommerce-e-conomic-integration
 * Author URI: www.wooconomics.com
 * License: GPL2
 */
 if ( ! defined( 'ABSPATH' ) ) exit;
if(!defined('TESTING')){
    define('TESTING',true);
}

if(!defined('AUTOMATED_TESTING')){
    define('AUTOMATED_TESTING', true);
}

if ( ! function_exists( 'logthis' ) ) {
    function logthis($msg) {
        if(TESTING){
			$filePath = dirname(__FILE__).'/logfile.log';
			$archivedFilePath = dirname(__FILE__).'/logfile_archived.log';
			if(file_exists($filePath) && ceil(filesize($filePath)/(1024*1024)) > 2){
				rename($filePath, $archivedFilePath);
			}
            if(!file_exists($filePath)){
                $fileobject = fopen($filePath, 'a');
                chmod($filePath, 0666);
            }
            else{
                $fileobject = fopen($filePath, 'a');
            }
            if(is_array($msg) || is_object($msg)){
                fwrite($fileobject,print_r($msg, true));
            }
            else{
                fwrite($fileobject,date("Y-m-d H:i:s"). ":" . $msg . "\n");
            }
        }
        else{
            error_log($msg);
        }
    }
}

// Makes sure the plugin is defined before trying to use it
if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
}

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || is_plugin_active_for_network( 'woocommerce/woocommerce.php' )) {

    if ( ! class_exists( 'WC_Economic' ) ) {
		
		//WooConomis developer tools
		add_action( 'wp_ajax_deletelog', 'economic_delete_log' );
		function economic_delete_log(){
			$filePath = dirname(__FILE__).'/logfile.log';
			unlink($filePath);
			logthis('Log file removed by economic_delete_log!');
		}
		
		
		//Add e-conomic payment class
		include_once("economic-payment.php");

        // in javascript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
        function economic_enqueue(){
            wp_enqueue_script('jquery');
            wp_register_script( 'economic-script', plugins_url( '/js/economic.js', __FILE__ ) );
            wp_enqueue_script( 'economic-script' );
        }

        add_action( 'admin_enqueue_scripts', 'economic_enqueue' );
		
		
		add_action('economic_product_sync_cron', 'economic_sync_products_callback');
        add_action( 'wp_ajax_sync_products', 'economic_sync_products_callback' );
        function economic_sync_products_callback() {
			//echo json_encode(array('status' => 'test', 'msg'=>'testing ajax')); exit; die();
            global $wpdb; // this is how you get access to the database
			include_once("class-economic-api.php");
            $wce_api = new WCE_API();
			$wce = new WC_Economic();
			if($wce->is_license_key_valid() != "Active" || !$wce_api->create_API_validation_request()){
			  logthis("economic_sync_products_callback exiting because license key validation not passed.");
			  return false;
			}			
			$log_msg = '';		
			$sync_log = $wce_api->sync_products();
			foreach(array_slice($sync_log, 1) as $key => $value){
				$log_msg .= __('<br>Sync status: ', 'woocommerce-e-conomic-integration'). $value['status'].'<br>';
				$log_msg .= __('Product SKU: ', 'woocommerce-e-conomic-integration'). $value['sku'].'<br>';
				$log_msg .= __('Product Name: ', 'woocommerce-e-conomic-integration'). $value['name'].'<br>';
				$log_msg .= __('Sync message: ', 'woocommerce-e-conomic-integration'). $value['msg'].'<br>';
			}
            if($sync_log[0]){
				$log = array('status' => __('Products are synchronized without problems.', 'woocommerce-e-conomic-integration'), 'msg' => $log_msg);
				//logthis(json_encode($log));
				echo json_encode($log);
            }
            else{
				$log = array('status' => __('Something went wrong.', 'woocommerce-e-conomic-integration'), 'msg' => $log_msg);
				echo json_encode($log);
            }
            die(); // this is required to return a proper result
        }
		
		add_action( 'wp_ajax_sync_products_ew', 'economic_sync_products_ew_callback' );
		//add_action( 'wp_ajax_nopriv_sync_products_ew', 'economic_sync_products_ew_callback' );
        function economic_sync_products_ew_callback() {
			//echo json_encode(array('status' => 'test', 'msg'=>'testing ajax')); exit; die();
            global $wpdb; // this is how you get access to the database
			include_once("class-economic-api.php");
            $wce_api = new WCE_API();
			$wce = new WC_Economic();
			if($wce->is_license_key_valid() != "Active" || !$wce_api->create_API_validation_request()){
			  logthis("economic_sync_products_callback exiting because license key validation not passed.");
			  return false;
			}			
			$log_msg = '';		
			$sync_log = $wce_api->sync_products_ew();
			foreach(array_slice($sync_log, 1) as $key => $value){
				$log_msg .= __('<br>Sync status: ', 'woocommerce-e-conomic-integration'). $value['status'].'<br>';
				$log_msg .= __('Product SKU: ', 'woocommerce-e-conomic-integration'). $value['sku'].'<br>';
				$log_msg .= __('Product Name: ', 'woocommerce-e-conomic-integration'). $value['name'].'<br>';
				$log_msg .= __('Sync message: ', 'woocommerce-e-conomic-integration'). $value['msg'].'<br>';
			}
            if($sync_log[0]){
				$log = array('status' => __('Products are synchronized without problems.', 'woocommerce-e-conomic-integration'), 'msg' => $log_msg);
				//logthis(json_encode($log));
				echo json_encode($log);
            }
            else{
				$log = array('status' => __('Something went wrong.', 'woocommerce-e-conomic-integration'), 'msg' => $log_msg);
				echo json_encode($log);
            }
            die(); // this is required to return a proper result
        }
		
		
		//Added for version 1.9.9.12, edited on 1.9.9.14
		add_action( 'wp_ajax_nopriv_sync_products_ew_webhook', 'economic_sync_products_ew_webhook_callback' );
		function economic_sync_products_ew_webhook_callback(){
			include_once("class-economic-api.php");
            $wce_api = new WCE_API();
			$wce = new WC_Economic();
			if($wce->is_license_key_valid() != "Active" || !$wce_api->create_API_validation_request()){
			  logthis("economic_sync_products_ew_webhook_callback exiting because license key validation not passed.");
			  return false;
			}	
			logthis('wp_ajax_nopriv_sync_products_ew_webhook executed');
			$number = $_GET['number'];
			$log_msg = '';	
			$sync_log = $wce_api->sync_products_ew($number);
			/*
			foreach(array_slice($sync_log, 1) as $key => $value){
				$log_msg .= __('<br>Sync status: ', 'woocommerce-e-conomic-integration'). $value['status'].'<br>';
				$log_msg .= __('Product SKU: ', 'woocommerce-e-conomic-integration'). $value['sku'].'<br>';
				$log_msg .= __('Product Name: ', 'woocommerce-e-conomic-integration'). $value['name'].'<br>';
				$log_msg .= __('Sync message: ', 'woocommerce-e-conomic-integration'). $value['msg'].'<br>';
			}
			
            if($sync_log[0]){
				$log = array('status' => __('Products are synchronized without problems.', 'woocommerce-e-conomic-integration'), 'msg' => $log_msg);
				//logthis(json_encode($log));
				echo json_encode($log);
            }
            else{
				$log = array('status' => __('Something went wrong.', 'woocommerce-e-conomic-integration'), 'msg' => $log_msg);
				echo json_encode($log);
            }
			*/
			if($sync_log[0]){
				logthis('wp_ajax_nopriv_sync_products_ew_webhook web hook "Product inventory updated" for e-conomic product number:'.$number.' successfully!');
			}else{
				logthis('wp_ajax_nopriv_sync_products_ew_webhook web hook "Product inventory updated" for e-conomic product number:'.$number.' failed!');
			}
			
            die(); // this is required to return a proper result	
		}
		//Added for version 1.9.9.12

        add_action( 'wp_ajax_sync_orders', 'economic_sync_orders_callback' );
        function economic_sync_orders_callback() {
            global $wpdb; // this is how you get access to the database
			include_once("class-economic-api.php");
            $wce_api = new WCE_API();
			$wce = new WC_Economic();
			if($wce->is_license_key_valid() != "Active" || !$wce_api->create_API_validation_request()){
			  logthis("economic_sync_products_callback existing because licensen key validation not passed.");
			  return false;
			}
			$log_msg = '';
			$sync_log = $wce_api->sync_orders();
            foreach(array_slice($sync_log, 1) as $key => $value){
				$log_msg .= __('<br>Sync status: ', 'woocommerce-e-conomic-integration'). $value['status'].'<br>';
				isset($value['order_id']) ? $log_msg .= 'Order ID: '. $value['order_id'].'<br>' : '';
				$log_msg .= __('Sync message: ', 'woocommerce-e-conomic-integration'). $value['msg'].'<br>';
			}
            if($sync_log[0]){
				$log = array('status' => __('Orders are synchronized without problems.', 'woocommerce-e-conomic-integration'), 'msg' => $log_msg);
				echo json_encode($log);
            }
            else{
				$log = array('status' => __('Something went wrong.', 'woocommerce-e-conomic-integration'), 'msg' => $log_msg);
				echo json_encode($log);
            }
            die(); // this is required to return a proper result
        }
		

        add_action( 'wp_ajax_sync_contacts', 'economic_sync_contacts_callback' );
        function economic_sync_contacts_callback() {
            global $wpdb; // this is how you get access to the database
			include_once("class-economic-api.php");
            $wce_api = new WCE_API();
			$wce = new WC_Economic();
			if($wce->is_license_key_valid() != "Active" || !$wce_api->create_API_validation_request()){
			  logthis("economic_sync_products_callback existing because licensen key validation not passed.");
			  return false;
			}
			$log_msg = '';
			$sync_log = $wce_api->sync_contacts();
            foreach(array_slice($sync_log, 1) as $key => $value){
				$log_msg .= __('<br>Sync status: ', 'woocommerce-e-conomic-integration'). $value['status'].'<br>';
				isset($value['user_id']) ? $log_msg .= 'Contact ID: '. $value['user_id'].'<br>' : '';
				$log_msg .= __('Sync message: ', 'woocommerce-e-conomic-integration'). $value['msg'].'<br>';
			}
            if($sync_log[0]){
				$log = array('status' => __('Contacts synchronized without problems.', 'woocommerce-e-conomic-integration'), 'msg' => $log_msg);
				echo json_encode($log);
            }
            else{
				$log = array('status' => __('Something went wrong.', 'woocommerce-e-conomic-integration'), 'msg' => $log_msg);
				echo json_encode($log);
            }
            die(); // this is required to return a proper result
        }
		
		//Sync function added for e-conomic to woocommerce sync.
		
		add_action( 'wp_ajax_sync_contacts_ew', 'economic_sync_contacts_ew_callback' );
        function economic_sync_contacts_ew_callback() {
            global $wpdb; // this is how you get access to the database
			include_once("class-economic-api.php");
            $wce_api = new WCE_API();
			$wce = new WC_Economic();
			if($wce->is_license_key_valid() != "Active" || !$wce_api->create_API_validation_request()){
			  logthis("economic_sync_contacts_ew_callback existing because licensen key validation not passed.");
			  return false;
			}
			$log_msg = '';
			$sync_log = $wce_api->sync_contacts_ew();
            foreach(array_slice($sync_log, 1) as $key => $value){
				$log_msg .= __('<br>Sync status: ', 'woocommerce-e-conomic-integration'). $value['status'].'<br>';
				isset($value['user_id']) ? $log_msg .= 'User ID: '. $value['user_id'].'<br>' : '';
				$log_msg .= __('Sync message: ', 'woocommerce-e-conomic-integration'). $value['msg'].'<br>';
			}
            if($sync_log[0]){
				$log = array('status' => __('Contacts synchronized without problems.', 'woocommerce-e-conomic-integration'), 'msg' => $log_msg);
				echo json_encode($log);
            }
            else{
				$log = array('status' => __('Something went wrong.', 'woocommerce-e-conomic-integration'), 'msg' => $log_msg);
				echo json_encode($log);
            }
            die(); // this is required to return a proper result
        }
		
		
		add_action( 'wp_ajax_sync_shippings', 'economic_sync_shippings_callback' );
        function economic_sync_shippings_callback() {
            global $wpdb; // this is how you get access to the database
			include_once("class-economic-api.php");
            $wce_api = new WCE_API();
			$wce = new WC_Economic();
			if($wce->is_license_key_valid() != "Active" || !$wce_api->create_API_validation_request()){
			  logthis("economic_sync_products_callback existing because licensen key validation not passed.");
			  return false;
			}
			$log_msg = '';
			$sync_log = $wce_api->sync_shippings();
            foreach(array_slice($sync_log, 1) as $key => $value){
				$log_msg .= __('<br>Sync status: ', 'woocommerce-e-conomic-integration'). $value['status'].'<br>';
				$log_msg .= __('Shipping type: ', 'woocommerce-e-conomic-integration'). $value['name'].'<br>';
				$log_msg .= __('Sync message: ', 'woocommerce-e-conomic-integration'). $value['msg'].'<br>';
			}
            if($sync_log[0]){
				$log = array('status' => __('Delivery synchronized without problems.', 'woocommerce-e-conomic-integration'), 'msg' => $log_msg);
				echo json_encode($log);
            }
            else{
				$log = array('status' => __('Something went wrong.', 'woocommerce-e-conomic-integration'), 'msg' => $log_msg);
				echo json_encode($log);
            }
            die(); // this is required to return a proper result
        }
		
		add_action( 'wp_ajax_sync_coupons', 'economic_sync_coupons_callback' );
        function economic_sync_coupons_callback() {
            global $wpdb; // this is how you get access to the database
			include_once("class-economic-api.php");
            $wce_api = new WCE_API();
			$wce = new WC_Economic();
			if($wce->is_license_key_valid() != "Active" || !$wce_api->create_API_validation_request()){
			  logthis("economic_sync_products_callback existing because licensen key validation not passed.");
			  return false;
			}
			$log_msg = '';
			$sync_log = $wce_api->sync_coupons();
            foreach(array_slice($sync_log, 1) as $key => $value){
				$log_msg .= __('<br>Sync status: ', 'woocommerce-e-conomic-integration'). $value['status'].'<br>';
				$log_msg .= __('Coupon code: ', 'woocommerce-e-conomic-integration'). $value['name'].'<br>';
				$log_msg .= __('Sync message: ', 'woocommerce-e-conomic-integration'). $value['msg'].'<br>';
			}
            if($sync_log[0]){
				$log = array('status' => __('Coupon codes synchronized without problems.', 'woocommerce-e-conomic-integration'), 'msg' => $log_msg);
				echo json_encode($log);
            }
            else{
				$log = array('status' => __('Something went wrong.', 'woocommerce-e-conomic-integration'), 'msg' => $log_msg);
				echo json_encode($log);
            }
            die(); // this is required to return a proper result
        }

        add_action( 'wp_ajax_send_support_mail', 'economic_send_support_mail_callback' );
		
		add_action( 'add_meta_boxes_product', 'economic_product_group_metabox' );
		
		function economic_product_group_metabox(){
			include_once("class-economic-api.php");
			$wce_api = new WCE_API();
			$wce = new WC_Economic();
			if($wce->is_license_key_valid() != "Active" || !$wce_api->create_API_validation_request()){
			  logthis("economic_sync_products_callback existing because licensen key validation not passed.");
			  return false;
			}
			add_meta_box( 'productGroup', 'e-conomic product group', 'economic_product_group', 'product', 'side', 'high' );
		}
		
		function economic_product_group( $post ) {
			include_once("class-economic-api.php");
			// Add a nonce field so we can check for it later.
			wp_nonce_field( 'economic_productGroup_save_meta_box_data', 'economic_productGroup_meta_box_nonce' );
			$wce_api = new WCE_API();
			$client = $wce_api->woo_economic_client();	
			$options = get_option('woocommerce_economic_general_settings');
			$productGroup = get_post_meta( $post->ID, 'productGroup', true );
			
			if($productGroup == '' || $productGroup == NULL){
				$productGroup = $options['product-group'];
			}
			$groups = $client->ProductGroup_GetAll()->ProductGroup_GetAllResult->ProductGroupHandle;
			
			echo __('Product group', 'woocommerce-e-conomic-integration').': ';
			echo '<select name="productGroup">';
			if(is_array($groups)){
				foreach($groups as $group){
					$groupnames[$group->Number] = $client->ProductGroup_GetName(array('productGroupHandle' => $group))->ProductGroup_GetNameResult;
					
					if($productGroup == $group->Number){
						echo '<option selected value='.$group->Number.'>'.$group->Number.'-'.$groupnames[$group->Number].'</option>';
					}else{
						echo '<option value='.$group->Number.'>'.$group->Number.'-'.$groupnames[$group->Number].'</option>';
					}
				}
			}else{
				$groupnames[$groups->Number] = $client->ProductGroup_GetName(array('productGroupHandle' => $groups))->ProductGroup_GetNameResult;
				echo '<option selected value='.$groups->Number.'>'.$groups->Number.'-'.$groupnames[$groups->Number].'</option>';
			}
			echo '</select>';
		}
		
		/**
		 * When the post is saved, saves our custom data.
		 *
		 * @param int $post_id The ID of the post being saved.
		 */
		function economic_productGroup_save_meta_box_data( $post_id ) {
			/*
			 * We need to verify this came from our screen and with proper authorization,
			 * because the save_post action can be triggered at other times.
			 */
		
			// Check if our nonce is set.
			if ( ! isset( $_POST['economic_productGroup_meta_box_nonce'] ) ) {
				return;
			}
		
			// Verify that the nonce is valid.
			if ( ! wp_verify_nonce( $_POST['economic_productGroup_meta_box_nonce'], 'economic_productGroup_save_meta_box_data' ) ) {
				return;
			}
		
			// If this is an autosave, our form has not been submitted, so we don't want to do anything.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
		
			// Check the user's permissions.
			if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
		
				if ( ! current_user_can( 'edit_page', $post_id ) ) {
					return;
				}
		
			} else {
		
				if ( ! current_user_can( 'edit_post', $post_id ) ) {
					return;
				}
			}
		
			/* OK, it's safe for us to save the data now. */
			
			// Make sure that it is set.
			if ( ! isset( $_POST['productGroup'] ) ) {
				return;
			}
		
			// Sanitize user input.
			$productGroup = sanitize_text_field( $_POST['productGroup'] );
		
			// Update the meta field in the database.
			logthis('economic_productGroup_save_meta_box_data adding productGroup for product: '.$post_id);
			update_post_meta( $post_id, 'productGroup', $productGroup );
			
			$args = array(
				'post_parent' => $post_id,
				'post_type'   => 'product_variation', 
				'numberposts' => -1,
				'post_status' => 'publish' 
			);
			
			$children_array = get_children( $args, OBJECT );
			if(!empty($children_array)){
				foreach ($children_array as $id => $childProduct){
					// Update the meta field in the database.
					logthis('economic_productGroup_save_meta_box_data adding productGroup for product: '.$childProduct->ID);
					update_post_meta( $childProduct->ID, 'productGroup', $productGroup );
				}
			}
		}
		add_action( 'save_post', 'economic_productGroup_save_meta_box_data', 1 );

        function economic_send_support_mail_callback() {

            //$message = 'Kontakta ' . $_POST['name'] . ' <br>på ' . $_POST['company'] . ' <br>antingen på ' .$_POST['telephone'] .' <br>eller ' . $_POST['email'] . ' <br>gällande: <br>' . $_POST['subject'];
			$message = '<html><body><table rules="all" style="border-color: #91B9F6; width:70%; font-family:Calibri, Arial, sans-serif;" cellpadding="10">';
			if(isset($_POST['supportForm']) && $_POST['supportForm'] ==  "support"){
				$message .= '<tr><td align="right">Type: </td><td align="left" colspan="1"><strong>Support</strong></td></tr>';
			}else{
				$message .= '<tr><td align="right">Type: </td><td align="left" colspan="1"><strong>Installationssupport</strong></td></tr>';
			}
			$message .= '<tr><td align="right">Företag: </td><td align="left">'.$_POST['company'].'</td></tr>';
			$message .= '<tr><td align="right">Namn: </td><td align="left">'.$_POST['name'].'</td></tr>';
			$message .= '<tr><td align="right">Telefon: </td><td align="left">'.$_POST['telephone'].'</td></tr>';
			$message .= '<tr><td align="right">Email: </td><td align="left">'.$_POST['email'].'</td></tr>';
			$message .= '<tr><td align="right">Ärende: </td><td align="left">'.$_POST['subject'].'</td></tr>';
			
			if(isset($_POST['supportForm']) && $_POST['supportForm'] ==  "support"){
				$options = get_option('woocommerce_economic_general_settings');
				//echo array_key_exists('activate-oldordersync', $options)? 'key exist' : 'key doesnt exist';
				$order_options = get_option('woocommerce_economic_order_settings');
				$message .= '<tr><td align="right" colspan="1"><strong>Allmänna inställningar</strong></td></tr>';
				if(array_key_exists('token', $options)){
					//$message .= '<tr><td align="right">Token ID: </td><td align="left">'.$options['token'].'</td></tr>';
				}
				if(array_key_exists('license-key', $options)){
					$message .= '<tr><td align="right">License Nyckel: </td><td align="left">'.$options['license-key'].'</td></tr>';
				}
				if(array_key_exists('other-checkout', $options)){
					$message .= '<tr><td align="right">Other checkout: </td><td align="left">'.$options['other-checkout'].'</td></tr>';
				}
				if(array_key_exists('economic-checkout', $options)){
					$message .= '<tr><td align="right">e-conomic checkout: </td><td align="left">'.$options['economic-checkout'].'</td></tr>';
				}				
				if(array_key_exists('activate-oldordersync', $options)){
					$message .= '<tr><td align="right">Activate old orders sync: </td><td align="left">'.$options['activate-oldordersync'].'</td></tr>';
				}
				if(array_key_exists('product-sync', $options)){
					$message .= '<tr><td align="right">Activate product sync: </td><td align="left">'.$options['product-sync'].'</td></tr>';
				}
				if(array_key_exists('scheduled-product-sync', $options)){
					$message .= '<tr><td align="right">Run scheduled product stock sync: </td><td align="left">'.$options['scheduled-product-sync'].'</td></tr>';
				}
				if(array_key_exists('product-group', $options)){
					$message .= '<tr><td align="right">Product group: </td><td align="left">'.$options['product-group'].'</td></tr>';
				}
				if(array_key_exists('product-prefix', $options)){
					$message .= '<tr><td align="right">Product prefix: </td><td align="left">'.$options['product-prefix'].'</td></tr>';
				}
				if(array_key_exists('customer-group', $options)){
					$message .= '<tr><td align="right">Customer group: </td><td align="left">'.$options['customer-group'].'</td></tr>';
				}
				if(array_key_exists('shipping-group', $options)){
					$message .= '<tr><td align="right">Shipping group: </td><td align="left">'.$options['shipping-group'].'</td></tr>';
				}
				if(array_key_exists('coupon-group', $options)){
					$message .= '<tr><td align="right">Coupon group: </td><td align="left">'.$options['coupon-group'].'</td></tr>';
				}
				if(array_key_exists('order-reference-prefix', $options)){
					$message .= '<tr><td align="right">Order reference prefix: </td><td align="left">'.$options['order-reference-prefix'].'</td></tr>';
				}
			}
			$message .= '<tr><td align="right">Plugin version: </td><td align="left">'.get_option('economic_version').'</td></tr>';
			$message .= '</table></html></body>';			
			$headers = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=utf-8 \r\n";
			//$headers .= "From:".get_option('admin_email')."\r\n";
			
            echo wp_mail( 'support@wooconomics.com', 'e-conomic Support', $message , $headers) ? "success" : "error";
            die(); // this is required to return a proper result
        }
		
		
		
		
		//Test the connection
		
		function economic_test_connection_callback() {
			include_once("class-economic-api.php");
			$wce = new WC_Economic();
			$wce_api = new WCE_API();
			if( $wce->is_license_key_valid() != "Active" ){
				_e('License Key is Invalid!', 'woocommerce-e-conomic-integration');
				die(); // this is required to return a proper result
			}else{
				$data = $wce_api->create_API_validation_request();
				if( $data ){
					_e('Your integration works fine!', 'woocommerce-e-conomic-integration');
					die(); // this is required to return a proper result
				}else{
					_e('Your e-conomic Token ID or License Key is not valid!', 'woocommerce-e-conomic-integration');
					die(); // this is required to return a proper result
				}
			}
			_e('Something went wrong, please try again later!', 'woocommerce-e-conomic-integration');
			die(); // this is required to return a proper result
        }
		
		//Connection testing ends

        add_action( 'wp_ajax_test_connection', 'economic_test_connection_callback' );
		
		
		//License key invalid warning message. todo change the license purchase link
		
		function license_key_invalid() {
			$options = get_option('woocommerce_economic_general_settings');
			$wce = new WC_Economic();
			$key_status = $wce->is_license_key_valid();
			if(!isset($options['license-key']) || $options['license-key'] == '' || $key_status!='Active'){
			?>
                <div class="error">
                    <p><?php echo __('WooCommerce e-conomic Integration: License Key Invalid!', 'woocommerce-e-conomic-integration'); ?> <button type="button button-primary" class="button button-primary" title="" style="margin:5px" onclick="window.open('http://whmcs.onlineforce.net/cart.php?a=add&pid=56&carttpl=flex-web20cart&language=English','_blank');"><?php echo __('Get license Key', 'woocommerce-e-conomic-integration'); ?></button></p>
                </div>
			<?php
			}
		}
		
		add_action( 'admin_notices', 'license_key_invalid' );
		//License key invalid warning message ends.


		//Section for wordpress pointers
		
		function economic_wp_pointer_hide_callback(){
			update_option('economic-tour', false);
		}
		add_action( 'wp_ajax_wp_pointer_hide', 'economic_wp_pointer_hide_callback' );
		
		$economic_tour = get_option('economic-tour');
		
		if(isset($economic_tour) && $economic_tour){
			// Register the pointer styles and scripts
			add_action( 'admin_enqueue_scripts', 'enqueue_scripts' );
			
			// Add pointer javascript
			add_action( 'admin_print_footer_scripts', 'add_pointer_scripts' );
		}
		
		// enqueue javascripts and styles
		function enqueue_scripts()
		{
			wp_enqueue_style( 'wp-pointer' );
			wp_enqueue_script( 'wp-pointer' );	
		}
		
		// Add the pointer javascript
		function add_pointer_scripts()
		{
			$content = __('<h3>WooCommerce e-conomic Integration</h3>', 'woocommerce-e-conomic-integration');
			$content .= __('<p>You’ve just installed WooCommerce e-conomic Integration by wooconomics. Please use the plugin options page to setup your integration.</p>', 'woocommerce-e-conomic-integration');
		
			?>
			
            <script type="text/javascript">
				jQuery(document).ready( function($) {
					$("#toplevel_page_woocommerce_economic_options").pointer({
						content: '<?php echo $content; ?>',
						position: {
							edge: 'left',
							align: 'center'
						},
						close: function() {
							// what to do after the object is closed
							var data = {
								action: 'wp_pointer_hide'
							};
	
							jQuery.post(ajaxurl, data);
						}
					}).pointer('open');
				});
			</script>
		   
		<?php
		}
		
		//Section for wordpress pointers ends.
		
		
		/***********************************************************************************************************
		* e-conomic FUNCTIONS
		***********************************************************************************************************/
		
		
		function get_current_user_role() {
			require_once(ABSPATH . 'wp-includes/functions.php');
			require_once(ABSPATH . 'wp-includes/pluggable.php');
		
			global $wp_roles;
			global $current_user;
			get_currentuserinfo();
			$roles = $current_user->roles;
			$role = array_shift($roles);
			return isset($wp_roles->role_names[$role]) ? translate_user_role($wp_roles->role_names[$role] ) : false;
		}
		
		
		//Save product to economic from woocommerce.
		add_action('save_post', 'woo_save_object_to_economic', 2, 2);
		function woo_save_object_to_economic( $post_id, $post) {
			//logthis($post);
			global $wpdb;
			if(!get_option('woo_save_object_to_economic')){
				logthis("woo_save_object_to_economic existing because disabled!");
				return;
			}
			include_once("class-economic-api.php");
			$wce = new WC_Economic();
			$wce_api = new WCE_API();
			if($wce->is_license_key_valid() != "Active" || !$wce_api->create_API_validation_request()){
				logthis("woo_save_object_to_economic existing because licensen key validation not passed.");
				return false;
			}
			logthis("woo_save_object_to_economic called by post_id: " . $post_id . " posttype: " . $post->post_type);
			if ( !$post ) return $post_id;		  
			if ( wp_is_post_revision( $post_id )) {
				logthis('woo_save_object_to_economic exit on wp_is_post_revision'); 
				return;
			}
			
			if( wp_is_post_autosave( $post_id ) || $post->post_status == 'auto-draft' ) {
				logthis('woo_save_object_to_economic exit on wp_is_post_autosave'); 
				return;
			}
			
			if($post->post_status == 'trash' ) {
				logthis('woo_save_object_to_economic exit on post status trash.'); 
				return;
			}

			if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
				logthis('woo_save_object_to_economic exit on wp_is_post_autosave'); 
				return $post_id;
			}
			
			
			if($post->post_type == 'shop_order' && $post->post_status != 'wc-cancelled'){
				$order = new WC_Order($post_id);
				$options = get_option('woocommerce_economic_general_settings');
				if(($options['economic-checkout'] == 'invoice' || $options['other-checkout'] == 'invoice') && $wpdb->query ("SELECT * FROM ".$wpdb->prefix."wce_orders WHERE order_id=".$post_id." AND synced=1")){
					logthis('woo_save_customer_to_economic exiting, because an already invoiced order is being saved');
					return;
				}
				if($order->billing_first_name == ''){
					logthis('woo_save_customer_to_economic exiting, because order data is empty.');
					return;
				}
				
				if(get_current_user_role() != 'Administrator'){
					logthis('woo_save_customer_to_economic exiting, because save_customer is not called by administrator.');
					return;
				}
				
				$post_date = new DateTime($post->post_date);
				$post_modified = new DateTime($post->post_modified);
				$interval = $post_date->diff($post_modified);
				$diff = (int) $interval->format('%i');
				
				if($diff == 0){
					logthis('woo_save_customer_to_economic exiting, because the order is being saved just after woocommerce_checkout_order_processed actionhook');
					return;
				}
				
				
				do_action('woo_save_'.$post->post_type.'_to_economic', $post_id, NULL, str_replace("wc-", "", $post->post_status), NULL);
				return;
			}
			
			if($post->post_type == 'product'){
				logthis("woo_save_object_to_economic calling woo_save_".$post->post_type."_to_economic");
				do_action('woo_save_'.$post->post_type.'_to_economic', $post_id, $post);
				
				//Added for 1.9.9.17 update by Alvin
				$args = array(
					'post_parent' => $post_id,
					'post_type'   => 'product_variation', 
					'numberposts' => -1,
					'post_status' => 'publish' 
				); 
				$children_array = get_children( $args );
				foreach ($children_array as $variation_product) {
					do_action('woo_save_'.$post->post_type.'_to_economic', $variation_product->ID, $variation_product);
				}
				
				return;
			}
			
			
			if ($post->post_type != 'product' || $post->post_status != 'publish') {
				logthis('woo_save_object_to_economic exit on post_type: '.$post->post_type.' and post_status: '.$post->post_status); 
				return;
			}
		  
		}
				
		add_action('woo_save_product_to_economic', 'woo_save_product_to_economic', 1,2);
		add_action('woo_save_product_variation_to_economic', 'woo_save_product_to_economic', 1,2);
		add_action( 'wp_ajax_save_product', 'woo_save_product_to_economic', 1,2 );
		function woo_save_product_to_economic($post_id, $post) {
		  global $wpdb;
		  include_once("class-economic-api.php");
		  $wce = new WC_Economic();
		  $wce_api = new WCE_API();
		  //should be handled for syncing products from economic to woocommerce.
		  /*if ($woo_economic_product_lock) {
			logthis("woo_save_product_to_economic cancel save product, product is locked");
			return;
		  }*/
		  logthis("woo_save_product_to_economic product post id: " . $post_id);
		  $product = new WC_Product($post->ID);
		  $client = $wce_api->woo_economic_client();
		  logthis("saving product: " . $product->get_title() . " id: " . $product->id . " sku: " . $product->sku);
		  //$wce_api->save_product_to_economic($product, $client);
		  //Added for WooConomic 1.9.19
		  if($client){
		  	$wce_api->save_product_to_economic($product, $client);
		  }else{
			logthis("Product sync on save exiting, because e-conomic client connection issue!");
		  }
		  //Added for WooConomic 1.9.19
		}
		//Save product to economic from woocommerce ends.
		
		
		//Save orders to economic from woocommerce.
		/*
		* Action to create invoice/order/quotation
		* This function is broken and diabled.
		*/
		//add_action('woocommerce_order_status_completed', 'woo_save_invoice_order_to_economic', 10, 4);
		function woo_save_invoice_order_to_economic($order_id) {
			try {
				global $wpdb;
				if($wpdb->query ("SELECT * FROM ".$wpdb->prefix."wce_orders WHERE order_id=".$order_id." AND synced=1;")){
					logthis("woo_save_invoice_to_economic: order_id: ".$order_id." is already synced during the checkout");
					return true;
				}
				include_once("class-economic-api.php");
				$options = get_option('woocommerce_economic_general_settings');
				$wce = new WC_Economic();
				$wce_api = new WCE_API();
				if($wce->is_license_key_valid() != "Active" || !$wce_api->create_API_validation_request()){
					logthis('Exiting on API license failure!');
					if($wpdb->query ("SELECT * FROM ".$wpdb->prefix."wce_orders WHERE order_id=".$order_id.";")){
						$wpdb->update ($wpdb->prefix."wce_orders", array('synced' => 0), array('order_id' => $order->id), array('%d'), array('%d'));
					}else{
						$wpdb->insert ($wpdb->prefix."wce_orders", array('order_id' => $order_id, 'synced' => 0), array('%d', '%d'));
					}
					return false;
				}
				logthis("woo_save_invoice_order_to_economic: order_id: ".$order_id);
				$order = new WC_Order($order_id);
				if($order->customer_user != 0){
					$user = new WP_User($order->customer_user);
				}else{
					$user = NULL;
				}
				if($order->payment_method != 'economic-invoice'){
					if($options['other-checkout'] == "do nothing"){
						if($wpdb->query ("SELECT * FROM ".$wpdb->prefix."wce_orders WHERE order_id=".$order->id." AND synced=0;")){
							return false;
						}else{
							$wpdb->insert ($wpdb->prefix."wce_orders", array('order_id' => $order->id, 'synced' => 0), array('%d', '%d'));
							return false;
						}
					}
				}else{
					if($options['economic-checkout'] == "do nothing"){
						if($wpdb->query ("SELECT * FROM ".$wpdb->prefix."wce_orders WHERE order_id=".$order->id." AND synced=0;")){
							return false;
						}else{
							$wpdb->insert ($wpdb->prefix."wce_orders", array('order_id' => $order->id, 'synced' => 0), array('%d', '%d'));
							return false;
						}
					}
				}
				$client = $wce_api->woo_economic_client();
				if($options['economic-checkout'] == 'draft invoice' || $order->payment_method == 'economic-invoice'){
					if($wce_api->save_invoice_to_economic($client, $user, $order, false)){
						logthis("woo_save_invoice_to_economic order: " . $order_id . " is synced with economic");
					}
					else{
						logthis("woo_save_invoice_to_economic order: " . $order_id . " sync failed, please try again after sometime!");
					}
				}else{
					if($wce_api->save_order_to_economic($client, $user, $order, false)){
						logthis("woo_save_order_to_economic order: " . $order_id . " is synced with economic");
					}
					else{
						logthis("woo_save_order_to_economic order: " . $order_id . " sync failed, please try again after sometime!");
					}
				}
				/**
				* if create auto debtor payment - create it
				
				$auto_create_debtor = $options['activate-cashbook'];
				if (isset($auto_create_debtor) && $auto_create_debtor == 'on') {
					woo_economic_create_debtor_payment($user, $order);
				}*/
			}catch (Exception $exception) {
				logthis("woocommerce_order_status_completed could not sync: " . $exception->getMessage());
				$wce_api->debug_client($client);
				logthis($exception->getMessage);
				if($wpdb->query ("SELECT * FROM ".$wpdb->prefix."wce_orders WHERE order_id=".$order_id." AND synced=0;")){
					return false;
				}else{
					$wpdb->insert ($wpdb->prefix."wce_orders", array('order_id' => $order_id, 'synced' => 0), array('%d', '%d'));
					return false;
				}
				return false;
			}
		}
		
		/*
		* Action to create invoice/order/quotation
		*
		add_action('woocommerce_order_status_refunded', 'woo_refund_order_to_economic', 10, 4);
		function woo_refund_order_to_economic($order_id) {
			include_once("class-economic-api.php");
			$wce_api = new WCE_API();
			logthis("woo_economic_refund_invoice: order_id: ".$order_id);
			$order = new WC_Order($order_id);
			$user = new WP_User($order->user_id);
			$client = $wce_api->woo_economic_client();
			$wce_api->save_invoice_to_economic($user, $order, $client, $order_id . " refunded", true);
		}*/
		
		//Save orders to economic from woocommerce ends.


		
		//Save customers to economic from woocommerce ends.
		
		//Add action to schedules payment for subscriptions
		add_action('woocommerce_scheduled_subscription_payment_economic-invoice', 'scheduled_subscription_payment_economic', 10, 2);
		function scheduled_subscription_payment_economic($amount_to_charge, $order){
			logthis("scheduled_subscription_payment_economic run for order: ".$order->id);
			if(woo_save_customer_to_economic($order->id, NULL, NULL, true)){
				logthis("scheduled_subscription_payment_economic created order/invoice/draft invoice at e-conomoic!");
			}else{
				logthis("scheduled_subscription_payment_economic creating order/invoice/draft invoice at e-conomoic failed!");
			}
		}
		
		add_action( 'wp_ajax_capture_payment', 'economic_capture_payment' );
		add_action( 'wp_ajax_nopriv_capture_payment', 'economic_capture_payment' );
		function economic_capture_payment(){
			logthis('economic_capture_payment is run by Webhook Day book or Entries Booked hook');
			include_once("class-economic-api.php");
			include_once("restclient.php");
			$tono = isset($_GET['tono'])? $_GET['tono'] : '';
			$fromno = isset($_GET['fromno'])? $_GET['fromno'] : '';
			$daybookno = isset($_GET['daybookno'])? $_GET['daybookno'] : '';
			$wce_api = new WCE_API();
			
			$client = $wce_api->woo_economic_client();
			
			$api = new RestClient(array(
				'base_url' => "https://restapi.e-conomic.com", 
				'format' => "json", 
				'headers' => array('X-AppSecretToken' => $wce_api->appToken, 'X-AgreementGrantToken' => $wce_api->token, 'Content-Type' => 'application/json'), 
			));
			
			$entry = json_decode($api->get("entries/".$tono)->response);
			$voucherNo = $entry->voucherNumber;
			$invoiceNo = json_decode($api->get("vouchers/booked/".$voucherNo."/".$entry->date)->response);
			
			$InvoiceNumber = $invoiceNo->lines[1]->invoiceNumber;
			
			if(empty($InvoiceNumber)) {
				try{
					//logthis("Geting Cashbook entry!");
					//$entryTypes = array('DebtorPayment', 'DebtorInvoice', 'CreditorInvoice', 'CreditorPayment', 'JournalEntry', 'Reminder', 'OpeningEntry', 'TransferredOpeningEntry', 'SystemEntry', 'ManualDebtorInvoice');
					$DebtorEntry_FindBySerialNumber  = $client->DebtorEntry_FindBySerialNumber   (array(
						'from' => $fromno,
						'to' => $tono,
					))->DebtorEntry_FindBySerialNumberResult;
					//logthis('DebtorEntry_FindBySerialNumber  :');
					//logthis($DebtorEntry_FindBySerialNumber  );
					
					if(is_array($DebtorEntry_FindBySerialNumber->DebtorEntryHandle)){
						$DebtorEntryHandle = $DebtorEntry_FindBySerialNumber->DebtorEntryHandle[0];
					}else{
						$DebtorEntryHandle = $DebtorEntry_FindBySerialNumber->DebtorEntryHandle;
					}
					
					$DebtorEntry_GetData = $client->DebtorEntry_GetData(array(
						'entityHandle' => $DebtorEntryHandle
					))->DebtorEntry_GetDataResult;
					
					//logthis('DebtorEntry_GetData: ');
					//logthis($DebtorEntry_GetData);
					
					$InvoiceNumber = $DebtorEntry_GetData->InvoiceNumber;
					//logthis("InvoiceNo: ".$InvoiceNumber);
				}
				catch (Exception $exception) {
					$wce_api->debug_client($client);
					logthis($exception->getMessage);
				}
			}
			
			try{
				$invoiceHandle = $client->Invoice_FindByNumber(array(
					'number' => $InvoiceNumber
				))->Invoice_FindByNumberResult;
				
				$invoiceOtherReference = $client->Invoice_GetOtherReference(array(
					'invoiceHandle' => $invoiceHandle
				))->Invoice_GetOtherReferenceResult;
				
				logthis("Invoice other reference: ".$invoiceOtherReference);
			}
			catch (Exception $exception) {
				$wce_api->debug_client($client);
				logthis($exception->getMessage);
			}
			
			//Condition for checking if the booked entry is a payment entry in the next release 1.9.9.18 if
			//if($invoiceOtherReference)?
			
			if($wce_api->order_reference_prefix != ''){
				$orderId = str_replace($wce_api->order_reference_prefix,"", $invoiceOtherReference);
			}else{
				$orderId = $invoiceOtherReference;
			}
			logthis("orderID: ");
			logthis($orderId);
			$webhookURL = admin_url('admin-ajax.php').'?action=capture_payment&tono='.$tono.'&fromno='.$fromno;
			if(!empty($orderId)){
				$order = new WC_Order($orderId);
				if($order->payment_method == 'economic-invoice'){
					$order->add_order_note( 'Payment for e-conomic invoice "'.$InvoiceNumber.'" is recorded on entry No: '.$tono );
				}
				//Should verify if the payment is already paid before marking it as paid.
				//Payment already marked as processing is handled correctly by payment_completed 
				$order->payment_complete();
				$wooSubscription = 'woocommerce-subscriptions/woocommerce-subscriptions.php';
				if(is_plugin_active($wooSubscription)){
					if(WC_Subscriptions_Order::order_contains_subscription( $orderId )){
						WC_Subscriptions_Manager::process_subscription_payments_on_order( $order );
					}
				}
				logthis('Capturing payment successful for WC order ID: '.$$orderId.', e-conomic Invoice no: '.$InvoiceNumber.'!');
				echo 'If you want to run the webhook manually again for this entry then use this URL <a href="'.$webhookURL.'">'.$webhookURL.'</a>!';
			}else{
				logthis('Capturing payment failed for serial: '.$fromno.' - '.$tono);
				echo 'Somthing went wrong, please visit this URL <a href="'.$webhookURL.'">'.$webhookURL.'</a> to run the webhook again manually.';
			}
		}
		
		add_action( 'profile_update', 'economic_customer_update', 1000, 2 );
		
		function economic_customer_update($user_id, $old_user_data){
			global $wpdb;
			$user = new WP_User($user_id);
			$wce_api = new WCE_API();
			$client = $wce_api->woo_economic_client();
            if($client){
                if($wce_api->save_customer_to_economic($client, $user) != true){
    				if($wpdb->query ("SELECT * FROM ".$wpdb->prefix."wce_customers WHERE email=".$email." AND synced=0;")){
    					return false;
    				}else{
    					$wpdb->insert ($wpdb->prefix."wce_customers", array('user_id' => $user->ID, 'customer_number' => '0', 'email' => $email, 'synced' => 0), array('%d', '%s', '%s', '%d'));
    					return false;
    				}
    			}
            }
            else{
                logthis('economic_customer_update existing because client creation failed!');
            }
			
		}
		
		
		/*
		 * Create new customer at economic with minimial required data.
		 */
		add_action('woocommerce_checkout_order_processed', 'woo_save_customer_to_economic');
		add_action('woo_save_shop_order_to_economic', 'woo_save_customer_to_economic', 10, 4);
		add_action('woocommerce_order_status_changed', 'woo_save_customer_to_economic', 10, 4);
		
		function woo_save_customer_to_economic($order_id, $old_status = NULL, $new_status = NULL, $subscription_renewal = NULL) {
			try{
				global $wpdb;
				$options = get_option('woocommerce_economic_general_settings');
				$order = new WC_Order($order_id);
				$sync = array( 'sync' => true, 'type' => '');
				if($old_status != NULL && $new_status != NULL){
					$sync['type'] = 'status';
				}elseif($old_status == NULL && $new_status != NULL){
					$sync['type'] = 'save';
				}else{
					$sync['type'] = 'event';
					//Added to sync Subscription renewal order as event based sync.
					//Modified for 1.9.16 because the status driven sync was overridden by this bug.
					if($subscription_renewal){
						$options['initiate-order'] = 'event_based';
					}
				}
				if($options['initiate-order'] == 'status_based'){					
					logthis('woo_save_customer_to_economic: Order sync initiated for the '.$options['initiate-order'].' option.');
					if($new_status != NULL){
						if($options['initiate-order-status-'.$new_status] == 'on'){
							if($old_status != NULL){
								logthis('woo_save_customer_to_economic: Order sync initiated for the status change: '.$new_status);
							}else{
								logthis('woo_save_customer_to_economic: Order sync initiated for the order save with status: '.$new_status);
							}
						}else{
							$sync['sync'] = false;
						}
					}else{
						$sync['sync'] = false;
					}
				}
				
				if($options['initiate-order'] == 'event_based'){
					logthis('woo_save_customer_to_economic: Order sync initiated for the '.$options['initiate-order'].' option.');
					if((current_filter() == 'woocommerce_checkout_order_processed' && $options['initiate-order-event'] == 'checkout_order_processed')){
						logthis('woo_save_customer_to_economic: Order sync initiated by event: '.current_filter());
					}else{
						$sync['sync'] = false;
					}
					if(current_filter() == 'woocommerce_scheduled_subscription_payment_'.$order->payment_method){
						$sync['sync'] = true;
					}
				}
				
				if(!$sync['sync']){
					if($wpdb->query ("SELECT * FROM ".$wpdb->prefix."wce_orders WHERE order_id=".$order_id." AND synced=1")){
						if($options['initiate-order'] == 'status_based' && $sync['type'] == 'status'){
							logthis('woo_save_customer_to_economic: Order sync initiated by status update is exiting because status: '.$new_status.' is not selected for active sync and the order is already synced on valid status or event!');
						}elseif($options['initiate-order'] == 'event_based' && $sync['type'] == 'event'){
							logthis('woo_save_customer_to_economic: Order sync initiated by event is exiting because event: '.current_filter().' is not selected for active sync and the order is already synced on valid status or event!');
						}else{
							logthis('woo_save_customer_to_economic: Order sync initiated by save event is exiting because the event or status is not selected for sync and the order is already synced on valid status or event!');
						}
						return false;
					}else{
						if($options['initiate-order'] == 'status_based' && $sync['type'] == 'status'){
							logthis('woo_save_customer_to_economic: Order sync initiated by status update is exiting because status: '.$new_status.' is not selected for active sync and this is the initial order sync!');
						}elseif($options['initiate-order'] == 'event_based' && $sync['type'] == 'event'){
							logthis('woo_save_customer_to_economic: Order sync initiated by event is exiting because event: '.current_filter().' is not selected for active sync and this is the initial order sync!');
						}else{
							logthis('woo_save_customer_to_economic: Order sync initiated by save event is exiting because the event or status is not selected for sync');						
						}
						
						if($wpdb->query ("SELECT * FROM ".$wpdb->prefix."wce_orders WHERE order_id=".$order_id." AND synced=0")){
							return false;
						}else{
							$wpdb->insert ($wpdb->prefix."wce_orders", array('order_id' => $order_id, 'synced' => 0), array('%d', '%d'));
							return false;
						}
						
					}
				}				
				
				
				if($wpdb->query ("SELECT * FROM ".$wpdb->prefix."wce_orders WHERE order_id=".$order_id." AND synced=1")){
					logthis('syncing order for update.');
				}elseif($wpdb->query ("SELECT * FROM ".$wpdb->prefix."wce_orders WHERE order_id=".$order_id." AND synced=0")){
					logthis('syncing order failed previously');
				}else{
					$wpdb->insert ($wpdb->prefix."wce_orders", array('order_id' => $order_id, 'synced' => 0), array('%d', '%d'));
				}
				
				include_once("class-economic-api.php");			
				
				$wce = new WC_Economic();
				$wce_api = new WCE_API();
				if($order->customer_user != 0){
					$user = new WP_User($order->customer_user);
				}else{
					$user = NULL;
				}
				if($wce->is_license_key_valid() != "Active" || !$wce_api->create_API_validation_request()){
					if($wpdb->query ("SELECT * FROM ".$wpdb->prefix."wce_customers WHERE email=".$order->billing_email.";")){
						$wpdb->update ($wpdb->prefix."wce_customers", array('synced' => 0), array('email' => $order->billing_email), array('%d'), array('%s'));
					}else{
						$wpdb->insert ($wpdb->prefix."wce_customers", array('user_id' => $user->ID, 'customer_number' => 0, 'email' => $order->billing_email, 'synced' => 0), array('%d', '%s', '%s', '%d'));
					}
					return false;
				}
				logthis("woo_save_customer_to_economic for user: " . $order->billing_first_name);
			
				if (woo_is_economic_customer($user)) {
					$client = $wce_api->woo_economic_client();
					logthis("woo_save_customer_to_economic user: " . $order->billing_first_name . " is being synced with economic.");
					if($wce_api->save_customer_to_economic($client, $user, $order)){
						logthis("woo_save_customer_to_economic user: " . $order->billing_first_name . " is synced with economic.");
						if($order->payment_method == 'economic-invoice'){
							logthis("woo_save_customer_to_economic syncing WC order for e-conomic payment.");
							
							if($options['economic-checkout'] == 'order'){
								if($wce_api->save_order_to_economic($client, $user, $order, false)){
									logthis("woo_save_order_to_economic order: " . $order_id . " is synced with economic as order.");
								}
								else{
									logthis("woo_save_order_to_economic order: " . $order_id . " order sync failed, please try again after sometime!");
								}
							}
							
							if($options['economic-checkout'] == 'draft invoice' || $options['economic-checkout'] == 'invoice'){
								if($wce_api->save_invoice_to_economic($client, $user, $order, false)){
									logthis("woo_save_invoice_to_economic order: " . $order_id . " is synced with economic as draft invoice.");
								}
								else{
									logthis("woo_save_invoice_to_economic order: " . $order_id . " draft invoice sync failed, please try again after sometime!");
								}
								
								if($options['economic-checkout'] == 'invoice'){
									if($wce_api->send_invoice_economic($client, $order)){
										logthis("woo_save_invoice_to_economic invoice for order: " . $order_id . " is sent to customer.");
									}else{
										logthis("woo_save_invoice_to_economic invoice for order: " . $order_id . " sending failed!");
									}
								}
							}
								
						}else{
							logthis("woo_save_customer_to_economic syncing WC order for payment method except e-conomic.");
							if($options['other-checkout'] == 'do nothing'){
								logthis("woo_save_order_to_economic order: " . $order_id . " is not synced synced with economic because do nothing is selected for e-conomic payment.");
							}
							
							if($options['other-checkout'] == 'order'){
								if($wce_api->save_order_to_economic($client, $user, $order, false)){
									logthis("woo_save_order_to_economic order: " . $order_id . " is synced with economic as draft invoice.");
								}
								else{
									logthis("woo_save_order_to_economic order: " . $order_id . " order sync failed, please try again after sometime!");
								}
							}
							
							if($options['other-checkout'] == 'draft invoice' || $options['other-checkout'] == 'invoice'){
								if($wce_api->save_invoice_to_economic($client, $user, $order, false)){
									logthis("woo_save_invoice_to_economic order: " . $order_id . " is synced with economic as invoice.");
								}
								else{
									logthis("woo_save_invoice_to_economic order: " . $order_id . " invoice sync failed, please try again after sometime!");
								}
								
								if($options['other-checkout'] == 'invoice'){
									if($wce_api->send_invoice_economic($client, $order)){
										logthis("woo_save_invoice_to_economic invoice for order: " . $order_id . " is sent to customer.");
									}else{
										logthis("woo_save_invoice_to_economic invoice for order: " . $order_id . " sending failed!");
									}
								}
							}
								
						}
						//do_action( 'woocommerce_payment_complete', $order_id );
					}
					else{
						logthis("woo_save_customer_to_economic user: " . $user->ID . "sync failed, please manual sync after sometime!");
					}
				}
			}catch (Exception $exception) {
				logthis("woo_save_customer_to_economic could not sync user/order: " . $exception->getMessage());
				$wce_api->debug_client($client);
				logthis($exception->getMessage);
				if($wpdb->query ("SELECT * FROM ".$wpdb->prefix."wce_orders WHERE order_id=".$order_id." AND synced=0;")){
					return false;
				}else{
					$wpdb->insert ($wpdb->prefix."wce_orders", array('order_id' => $order_id, 'synced' => 0), array('%d', '%d'));
					return false;
				}
				if($wpdb->query ("SELECT * FROM ".$wpdb->prefix."wce_customers WHERE email=".$order->billing_email." AND synced=0;")){
					return false;
				}else{
					$wpdb->insert ($wpdb->prefix."wce_customers", array('user_id' => $user->ID, 'customer_number' => 0, 'email' => $order->billing_email, 'synced' => 0), array('%d', '%s', '%s', '%d'));
					return false;
				}
				return false;
			}
		}
		
		// add the action for payment completed to add note to e-conomic order/invoice about the payment type and date. 
		add_action( 'woocommerce_payment_complete', 'woo_update_order_payment_to_economic', 10, 1 ); 
		function woo_update_order_payment_to_economic($order_id){
			logthis('woo_update_order_payment_to_economic: Called by woocommerce_payment_complete hook.');
			include_once("class-economic-api.php");
			$order = new WC_Order($order_id);
			$wce_api = new WCE_API();
			$client = $wce_api->woo_economic_client();
			if($client){
				$wce_api->woo_update_order_payment_to_economic($client, $order);
			}else{
				logthis('woo_update_order_payment_to_economic: failed, because client not created.');
			}
		}
		
		//add the action for show user profile and update user profile added by Alvin for 1.9.9.8 release.
		add_action( 'show_user_profile', 'woo_add_customer_meta_fields', 10, 1 );
		add_action( 'edit_user_profile', 'woo_add_customer_meta_fields', 10, 1 );
		add_action( 'personal_options_update', 'woo_save_customer_meta_fields', 10, 1 );
		add_action( 'edit_user_profile_update', 'woo_save_customer_meta_fields', 10, 1 );
		
		function woo_add_customer_meta_fields($user){
			?>
            <h3>e-conomic</h3>
			<table class="form-table">
					<tr>
						<th><label for="customerno">e-conomic customer number</label></th>
						<td>
							<input type="text" name="debtor_number" id="customerno" value="<?php echo esc_attr( get_user_meta( $user->ID, 'debtor_number', true ) ); ?>" class="regular-text" />
							<br/>
							<span class="description"><?php echo wp_kses_post( 'e-conomic customer number' ); ?></span>
						</td>
					</tr>
			</table>
            <?php
		}
		
		
		function woo_save_customer_meta_fields($user_id){
			if ( isset( $_POST['debtor_number'] ) ) {
				update_user_meta( $user_id, 'debtor_number', wc_clean( $_POST['debtor_number'] ) );
			}		
		}
		
		function woo_is_economic_customer($user) {
		  //$is_customer = false; changed for accepting all customers.
		  $is_customer = true;
		  return $is_customer;
		  foreach ($user->roles as $role) {
			logthis("user role: " . $role);
			if ($role == 'customer') {
			  $is_customer = true;
			  break;
			}
		  }
		  return $is_customer;
		}
		
		/*
		 * Save additional user data to economic
		 */
		add_action('update_user_meta', 'woo_update_user_meta_to_economic', 10, 4);
		function woo_update_user_meta_to_economic($meta_id, $object_id, $meta_key, $_meta_value) {
			if(!get_option('woo_save_object_to_economic')){
				logthis("woo_update_user_meta_to_economic existing because disabled!");
				return;
			}
		  global $wpdb;
		  include_once("class-economic-api.php");
		  $wce = new WC_Economic();
		  $wce_api = new WCE_API();
		  $user = new WP_User($object_id);
		  if(in_array($meta_key, $wce_api->user_fields)){
			  logthis("woo_update_user_meta_to_economic: meta_id: ".$meta_id." object_id: ".$object_id." meta_key: ".$meta_key." meta_value: ".$_meta_value);
			  if($wce->is_license_key_valid() != "Active" || !$wce_api->create_API_validation_request()){
				  if($wpdb->query ("SELECT * FROM ".$wpdb->prefix."wce_customers WHERE user_id=".$object_id.";")){
					 $wpdb->update ($wpdb->prefix."wce_customers", array('email' => $user->get('billing_email'), 'synced' => 0), array('user_id' => $object_id), array('%s', '%d'), array('%d'));
				  }else{
					 $wpdb->insert ($wpdb->prefix."wce_customers", array('user_id' => $object_id, 'customer_number' => 0, 'email' => $user->get('billing_email'), 'synced' => 0), array('%d', '%s', '%s', '%d'));
				  }
				  return false;
			  }
			  if (woo_is_economic_customer($user)) {
				$client = $wce_api->woo_economic_client();
				
				$debtorHandle = $wce_api->woo_get_debtor_handle_from_economic($client, $user);
				$debtor_delivery_location_handle = $wce_api->woo_get_debtor_delivery_location_handles_from_economic($client, $debtorHandle);
				
				if($wce_api->woo_save_customer_meta_data_to_economic($client, $meta_key, $_meta_value, $debtorHandle, $debtor_delivery_location_handle, $user)){
					$wpdb->update ($wpdb->prefix."wce_customers", array('synced' => 1), array('user_id' => $user->ID), array('%d'), array('%d'));
					logthis("woo_update_user_meta_to_economic user: " . $user->ID . " additional data is synced with economic");
				}
				else{
					$wpdb->update ($wpdb->prefix."wce_customers", array('synced' => 0), array('user_id' => $user->ID), array('%d'), array('%d'));
					logthis("woo_update_user_meta_to_economic user: " . $user->ID . " additional data sync failed, please try again after sometime!");
				}
			  }
		  }else{
			  logthis("woo_update_user_meta_to_economic: Not selected for sync, skipping meta_id: ".$meta_id." object_id: ".$object_id);
		  }
		}
		
		//Save customers to economic from woocommerce ends.


		//Section for Plugin installation and activation
		/**
		 * Creates tables for WooCommerce Economic
		 *
		 * @access public
		 * @param void
		 * @return bool
		 */
		function economic_install(){
			add_option('economic-tour', true);
			global $wpdb;
			$wce_orders = $wpdb->prefix."wce_orders";
			$wce_customers = $wpdb->prefix."wce_customers";
			
			$sql = "CREATE TABLE IF NOT EXISTS ".$wce_orders."( id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
					order_id MEDIUMINT(9) NOT NULL,
					synced TINYINT(1) DEFAULT FALSE NOT NULL,
					UNIQUE KEY id (id)
			);";
			
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql );
			
			$sql = "CREATE TABLE IF NOT EXISTS ".$wce_customers."( id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
					user_id MEDIUMINT(9) NOT NULL,
					customer_number MEDIUMINT(9) NOT NULL,
					email VARCHAR(320) DEFAULT NULL,
					synced TINYINT(1) DEFAULT FALSE NOT NULL,
					UNIQUE KEY user_id (id)
			);";
			
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql );
			
			//1.9.9.16 new feature
			$options = get_option('woocommerce_economic_general_settings');
			$options['initiate-order'] = 'event_based';
			$options['initiate-order-event'] = 'checkout_order_processed';
			update_option('woocommerce_economic_general_settings', $options);
			//1.9.9.16 new feature
			
			update_option('economic_version', 19.25);
			update_option('woo_save_object_to_economic', true);
		}
		
		/**
		 * Drops tables for WooCommerce Economic
		 *
		 * @access public
		 * @param void
		 * @return bool
		 */
		function economic_uninstall(){
			global $wpdb;				
			$wce_orders = $wpdb->prefix."wce_orders";
			$wce_customers = $wpdb->prefix."wce_customers";
			$wpdb->query ("DROP TABLE ".$wce_orders.";");
			$wpdb->query ("DROP TABLE ".$wce_customers.";");
			delete_option('economic-tour');	
			delete_option('economic_version');
			delete_option('woocommerce_economic_general_settings');	
			delete_option('local_key_economic_plugin');
			delete_option('woocommerce_economic_order_settings');
			wp_clear_scheduled_hook('economic_product_sync_cron');		
		}
		
		/**
		 *
		 *Functon for plugin update
		*/
		function economic_update(){
			global $wpdb;
			$wce_orders = "wce_orders";
			$wce_customers = "wce_customers";
			$economic_version = get_option('economic_version');
			if(floatval($economic_version) < 1.7 ){
				$wpdb->query("ALTER TABLE ".$wce_customers." ADD email VARCHAR(320) DEFAULT NULL AFTER customer_number");
			}
			if(floatval($economic_version) < 1.999 ){
				$options = get_option('woocommerce_economic_general_settings');
				if(isset($options['activate-allsync'])){
					if($options['sync-order-invoice'] == 'invoice'){
						$options['other-checkout'] = 'draft invoice';
						$options['economic-checkout'] = 'draft invoice';
					}else{
						$options['other-checkout'] = 'order';
						$options['economic-checkout'] = 'order';
					}
				}else{
					$options['other-checkout'] = 'do nothing';
					if($options['sync-order-invoice'] == 'invoice'){
						$options['economic-checkout'] = 'draft invoice';
					}else{
						$options['economic-checkout'] = 'order';
					}
				}
				update_option('woocommerce_economic_general_settings', $options);
			}
			if(floatval($economic_version) < 1.9999 ){
				$options = get_option('woocommerce_economic_general_settings');
				$options['initiate-order'] = 'event_based';
				$options['initiate-order-event'] = 'checkout_order_processed';
				update_option('woocommerce_economic_general_settings', $options);
			}
			if(floatval($economic_version) < 19.14){
				$wce_orders_migrated = $wpdb->prefix."wce_orders";
				$wce_customers_migrated = $wpdb->prefix."wce_customers";
				$sql = "CREATE TABLE IF NOT EXISTS ".$wce_orders_migrated."( id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
					order_id MEDIUMINT(9) NOT NULL,
					synced TINYINT(1) DEFAULT FALSE NOT NULL,
					UNIQUE KEY id (id)
				);";
				
				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql );
				
				$sql = "CREATE TABLE IF NOT EXISTS ".$wce_customers_migrated."( id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
						user_id MEDIUMINT(9) NOT NULL,
						customer_number MEDIUMINT(9) NOT NULL,
						email VARCHAR(320) DEFAULT NULL,
						synced TINYINT(1) DEFAULT FALSE NOT NULL,
						UNIQUE KEY user_id (id)
				);";
				
				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql );
				
				$sql = "INSERT INTO ".$wce_orders_migrated." SELECT * FROM ".$wce_orders;
				
				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql );
				
				$sql = "INSERT INTO ".$wce_customers_migrated." SELECT * FROM ".$wce_customers;
				
				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql );
				
				
			}

			update_option('economic_version', 19.25);
			update_option('woo_save_object_to_economic', true);
		}
		
		add_action( 'plugins_loaded', 'economic_update' );
		
		// install necessary tables
		register_activation_hook( __FILE__, 'economic_install');
		register_uninstall_hook( __FILE__, 'economic_uninstall');
		//Section for plugin installation and activation ends


        /**
         * Localisation
         **/
		 
		 /**
		 * Return the locale to en_GB
		 */ 		
		add_action('plugins_loaded', 'economic_load_textdomain');
		function economic_load_textdomain() {
			load_plugin_textdomain( 'woocommerce-e-conomic-integration', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}
				

        class WC_Economic {

            private $general_settings_key = 'woocommerce_economic_general_settings';
            private $order_settings_key = 'woocommerce_economic_order_settings';
            private $support_key = 'woocommerce_economic_support';
            private $manual_action_key = 'woocommerce_economic_manual_action';
            private $start_action_key = 'woocommerce_economic_start_action';
            private $general_settings;
            private $accounting_settings;
            private $plugin_options_key = 'woocommerce_economic_options';
            private $plugin_settings_tabs = array();
			

            public function __construct() {

                //call register settings function
                add_action( 'init', array( &$this, 'load_settings' ) );
                add_action( 'admin_init', array( &$this, 'register_woocommerce_economic_start_action' ));
                add_action( 'admin_init', array( &$this, 'register_woocommerce_economic_general_settings' ));
                add_action( 'admin_init', array( &$this, 'register_woocommerce_economic_manual_action' ));
                add_action( 'admin_init', array( &$this, 'register_woocommerce_economic_support' ));
                add_action( 'admin_menu', array( &$this, 'add_admin_menus' ) );


                // install necessary tables
                //register_activation_hook( __FILE__, array(&$this, 'install'));
                //register_deactivation_hook( __FILE__, array(&$this, 'uninstall'));
            }

            /***********************************************************************************************************
             * ADMIN SETUP
             ***********************************************************************************************************/

            /**
             * Adds admin menu
             *
             * @access public
             * @param void
             * @return void
             */
            function add_admin_menus() {
				add_menu_page( 'WooCommerce e-conomic Integration', 'e-conomic', 'manage_options', $this->plugin_options_key, array( &$this, 'woocommerce_economic_options_page' ) );
            }

            /**
             * Generates html for textfield for given settings params
             *
             * @access public
             * @param void
             * @return void
             */
            function field_gateway($args) {
                $options = get_option($args['tab_key']);?>

                <input type="hidden" name="<?php echo $args['tab_key']; ?>[<?php echo $args['key']; ?>]" value="<?php echo $args['key']; ?>" />

                <select name="<?php echo $args['tab_key']; ?>[<?php echo $args['key'] . "_payment_method"; ?>]" >';
                    <option value=""<?php if(isset($options[$args['key'] . "_payment_method"]) && $options[$args['key'] . "_payment_method"] == ''){echo 'selected="selected"';}?>>Välj nedan</option>
                    <option value="CARD"<?php if(isset($options[$args['key'] . "_payment_method"]) && $options[$args['key'] . "_payment_method"] == 'CARD'){echo 'selected="selected"';}?>>Kortbetalning</option>
                    <option value="BANK"<?php if(isset($options[$args['key'] . "_payment_method"]) && $options[$args['key'] . "_payment_method"] == 'BANK'){echo 'selected="selected"';}?>>Bankgiro/Postgiro</option>
                </select>
                <?php
                $str = '';
                if(isset($options[$args['key'] . "_book_keep"])){
                    if($options[$args['key'] . "_book_keep"] == 'on'){
                        $str = 'checked = checked';
                    }
                }
                ?>
                <span>Bokför automatiskt:  </span>
                <input type="checkbox" name="<?php echo $args['tab_key']; ?>[<?php echo $args['key'] . "_book_keep"; ?>]" <?php echo $str; ?> />

            <?php
            }

            /**
             * Generates html for textfield for given settings params
             *
             * @access public
             * @param void
             * @return void
             */
            function field_option_text($args) {
                $options = get_option($args['tab_key']);
                $val = '';
                if(isset($options[$args['key']])){
                    $val = esc_attr( $options[$args['key']] );
                }
				if($args['key'] == 'token' &&  (!isset($options[$args['key']]) || $options[$args['key']]=='')){
					if(isset($_GET['token'])){
						$val = $_GET['token'];
						if((!isset($options[$args['key']]) || $options[$args['key']]=='')){
							$args['desc'] .= __(' Please save the settings before leaving this page!', 'woocommerce-e-conomic-integration');
						}
					}else{
						$args['desc'] .= '<a href="https://secure.e-conomic.com/secure/api1/requestaccess.aspx?appId=LS4emZLGHCD_itL9OvgLbp1CvGCeeh0kE7f_v3L7fdU1&redirectUrl='.urlencode(admin_url().'admin.php?page=woocommerce_economic_options&tab=woocommerce_economic_general_settings').'" class="button button-primary" title="" style="margin-left:5px">'.__(' Click here to generate token access ID', 'woocommerce-e-conomic-integration').'</a>';
					}
					
				}
                ?>
                <input <?php echo isset($args['id'])? 'id="'.$args['id'].'"': ''; ?> type="text" name="<?php echo $args['tab_key']; ?>[<?php echo $args['key']; ?>]" value="<?php echo $val; ?>" />
                <span><i><?php echo $args['desc']; ?></i></span>
            <?php
            }
            
            /**
             * Generates html for dropdown for given settings of sandbox params
             *
             * @access public
             * @param void
             * @return void
             */
            function field_mode_dropdown($args) {
                $options = get_option($args['tab_key']);
                $str = '';
                $str2 = '';
                if(isset($options[$args['key']])){
                    if($options[$args['key']] == 'Live'){
                        $str = 'selected';
                    }
                    else
                    {
                        $str2 = 'selected';
                    }
                }

                ?>
                <select <?php echo isset($args['id'])? 'id="'.$args['id'].'"': ''; ?> name="<?php echo $args['tab_key']; ?>[<?php echo $args['key']; ?>]">
                    <option <?php echo $str; ?>>Live</option>
                    <option <?php echo $str2; ?>>Sandbox</option>
                </select>
                <span id="sandbox-mode"><i><?php echo $args['desc']; ?></i></span>
            <?php
            }
			
			/**
             * Generates html for dropdown for given settings params
             *
             * @access public
             * @param void
             * @return void
             */
            function field_option_schedule($args) {
                $options = get_option($args['tab_key']);
                $hourly = '';
                $twicedaily = '';
				$daily = '';
				$disabled = '';
				$webhook = '';
                if(isset($options[$args['key']])){
                    if($options[$args['key']] == 'hourly'){
                        $hourly = 'selected';
                    }
					elseif($options[$args['key']] == 'webhook'){
						$webhook = 'selected';
					}
					elseif($options[$args['key']] == 'twicedaily'){
						$twicedaily = 'selected';
					}
					elseif($options[$args['key']] == 'twicedaily'){
						$daily = 'selected';
					}
					else{
						$disabled = 'selected';
					}
                }
				
				wp_clear_scheduled_hook('economic_product_sync_cron');
				if(isset($options[$args['key']]) && $options[$args['key']] != 'disabled' && $options[$args['key']] != '' && $options[$args['key']] != 'webhook'){
					wp_schedule_event(time(), $options[$args['key']], 'economic_product_sync_cron');
				}
                ?>
                <select <?php echo isset($args['id'])? 'id="'.$args['id'].'"':''; ?> name="<?php echo $args['tab_key']; ?>[<?php echo $args['key']; ?>]">
                	<option <?php echo $disabled; ?> value='disabled'><?php _e('Disabled', 'woocommerce-e-conomic-integration'); ?></option>
                    <option <?php echo $webhook; ?> value='webhook'><?php _e('Web hook', 'woocommerce-e-conomic-integration'); ?></option>
                    <option <?php echo $hourly; ?> value='hourly'><?php _e('Hourly', 'woocommerce-e-conomic-integration'); ?></option>
                    <option <?php echo $twicedaily; ?> value='twicedaily'><?php _e('Twice Daily', 'woocommerce-e-conomic-integration'); ?></option>
                    <option <?php echo $daily; ?> value='daily'><?php _e('Daily', 'woocommerce-e-conomic-integration'); ?></option>
                </select>
                <span><i><?php echo $args['desc']; ?></i><?php if($webhook == 'selected'){ echo '<br><i style="margin-left:25px; color: #F00; font-weight: bold;">Note: </i> <i>Add a web hook to your e-conomic account for “Product inventory updated” type, using URL: <b>'.admin_url('admin-ajax.php').'?action=sync_products_ew_webhook&number=[NUMBER]</b></i>'; }?></span>
            <?php
            }
			
			
            
            /**
             * Generates html for dropdown for given settings params
             *
             * @access public
             * @param void
             * @return void
             */
            function field_option_dropdown($args) {
                $options = get_option($args['tab_key']);
                $str1 = '';
                $str2 = '';
				$str3 = '';
				$str4 = '';
                if(isset($options[$args['key']])){
                    if($options[$args['key']] == 'do nothing'){
                        $str1 = 'selected';
                    }
					elseif($options[$args['key']] == 'order'){
						$str2 = 'selected';
					}
					elseif($options[$args['key']] == 'draft invoice'){
						$str3 = 'selected';
					}
					elseif($options[$args['key']] == 'invoice'){
						$str4 = 'selected';
					}
                }

                ?>
                <select <?php echo isset($args['id'])? 'id="'.$args['id'].'"':''; ?> name="<?php echo $args['tab_key']; ?>[<?php echo $args['key']; ?>]">
                	<?php if($args['key'] == 'other-checkout'){ ?>
                    <option <?php echo $str1; ?> value='do nothing'><?php _e('Do nothing', 'woocommerce-e-conomic-integration'); ?></option>
                    <?php } ?>
                    <option <?php echo $str2; ?> value='order'><?php _e('Create order', 'woocommerce-e-conomic-integration'); ?></option>
                    <option <?php echo $str3; ?> value='draft invoice'><?php _e('Create draft invoice', 'woocommerce-e-conomic-integration'); ?></option>
                    <option <?php echo $str4; ?> value='invoice'><?php _e('Create invoice', 'woocommerce-e-conomic-integration'); ?></option>
                </select>
                <span><i><?php echo $args['desc']; ?></i></span>
            <?php
            }
			
			
			
			/**
             * Generates html for dropdown for order initiate option.
             *
             * @access public
             * @param void
             * @return void
             */
			function field_option_order_sync_dropdown($args) {
				$options = get_option($args['tab_key']);
                $str1 = '';
                $str2 = '';
                if(isset($options[$args['key']])){
                    if($options[$args['key']] == 'event_based'){
                        $str1 = 'selected';
                    }
					if($options[$args['key']] == 'status_based'){
						$str2 = 'selected';
					}
                }

                ?>
                <select id="initiate_order" <?php echo isset($args['id'])? 'id="'.$args['id'].'"':''; ?> name="<?php echo $args['tab_key']; ?>[<?php echo $args['key']; ?>]">
                    <option <?php echo $str1; ?> value='event_based'><?php _e('Based on an Event', 'woocommerce-e-conomic-integration'); ?></option>
                    <option <?php echo $str2; ?> value='status_based'><?php _e('Based on Order status', 'woocommerce-e-conomic-integration'); ?></option>  
                </select>
                <span><i><?php echo $args['desc']; ?></i></span>
            <?php
			}
			
			
			/**
             * Generates html for radio buttons for order initiate event option.
             *
             * @access public
             * @param void
             * @return void
             */
			function field_option_event_radio($args) {
				$options = get_option($args['tab_key']);
				//echo $options['initiate-order'];
				//print_r($options); echo "<br>";
                ?><span><i><?php echo $args['desc']; ?></i></span>
                <div id="event_based_options">
                	<input type="radio" name="<?php echo $args['tab_key']; ?>[<?php echo $args['key']; ?>]" value="checkout_order_processed" id="initiate-order-event" <?php echo $options[$args['key']] == 'checkout_order_processed' ? 'checked="checked"' : ''; ?>/><label for="initiate-order-event">Checkout Order Processed</label>  
                </div>
                
            <?php
			}
			
			
			/**
             * Generates html for radio buttons for order initiate event option.
             *
             * @access public
             * @param void
             * @return void
             */
			function field_option_status_checkbox($args) {
				$options = get_option($args['tab_key']);
				echo $options['initiate-order'];
				if($options['initiate-order'] == 'status_based' && $options[$args['key'].'-pending'] != 'on' && $options[$args['key'].'-processing'] != 'on' && $options[$args['key'].'-onhold'] != 'on' && $options[$args['key'].'-completed'] != 'on' && $options[$args['key'].'-cancelled'] != 'on' && $options[$args['key'].'-refunded'] != 'on' && $options[$args['key'].'-failed'] != 'on'){
					echo '<i style="color: #F00;">Warning: Select atleast one Order status for initiating sync to e-conomic!</i><br>';
				}
                ?><span><i><?php echo $args['desc']; ?></i></span>
                <div id="status_based_options">
                	<input type="checkbox" name="<?php echo $args['tab_key']; ?>[<?php echo $args['key'].'-pending'; ?>]" id="pending1" <?php echo $options[$args['key'].'-pending'] == 'on' ? 'checked="checked"' : ''; ?> /><label for="pending1">Pending Payment</label><br />
                    <input type="checkbox" name="<?php echo $args['tab_key']; ?>[<?php echo $args['key'].'-processing'; ?>]" id="processing" <?php echo $options[$args['key'].'-processing'] == 'on' ? 'checked="checked"' : ''; ?> /><label for="processing">Processing</label><br />
                    <input type="checkbox" name="<?php echo $args['tab_key']; ?>[<?php echo $args['key'].'-on-hold'; ?>]" id="onhold" <?php echo $options[$args['key'].'-on-hold'] == 'on' ? 'checked="checked"' : ''; ?> /><label for="onhold">On Hold</label><br />
                    <input type="checkbox" name="<?php echo $args['tab_key']; ?>[<?php echo $args['key'].'-completed'; ?>]" id="completed" <?php echo $options[$args['key'].'-completed'] == 'on' ? 'checked="checked"' : ''; ?> /><label for="completed">Completed</label><br />
                    <input type="checkbox" name="<?php echo $args['tab_key']; ?>[<?php echo $args['key'].'-cancelled'; ?>]" id="cancelled" <?php echo $options[$args['key'].'-cancelled'] == 'on' ? 'checked="checked"' : ''; ?> /><label for="cancelled">Cancelled</label><br />
                    <input type="checkbox" name="<?php echo $args['tab_key']; ?>[<?php echo $args['key'].'-refunded'; ?>]" id="refunded" <?php echo $options[$args['key'].'-refunded'] == 'on' ? 'checked="checked"' : ''; ?> /><label for="refunded">Refunded</label><br />
                    <input type="checkbox" name="<?php echo $args['tab_key']; ?>[<?php echo $args['key'].'-failed'; ?>]" id="failed" <?php echo $options[$args['key'].'-failed'] == 'on' ? 'checked="checked"' : ''; ?> /><label for="failed">Failed</label><br />
                </div>
                
            <?php
			}
			
			
			
			/**
             * Generates html for dropdown for given settings params (product and customer group)
             *
             * @access public
             * @param void
             * @return void
             */
            function field_option_group($args) {
				$options = get_option('woocommerce_economic_general_settings');
				$wce_api = new WCE_API();
				$client = $wce_api->woo_economic_client();
				if(!$client){
					_e('<span><i style="margin-left:25px; color: #F00;">ERROR: e-conomic client not loaded properly, please refresh the page to load properly.</i></span>', 'woocommerce-e-conomic-integration');
					return false;
				}
				if($args['key'] == 'product-group' || $args['key'] == 'shipping-group' || $args['key'] == 'coupon-group'){
					$groups = $client->ProductGroup_GetAll()->ProductGroup_GetAllResult->ProductGroupHandle;
					if(empty($groups)){
						_e('<span><i style="margin-left:25px; color: #F00;">ERROR: Getting product group failed, do you have a product group in e-conomic?</i></span>', 'woocommerce-e-conomic-integration');
						return false;
					}
					if(is_array($groups)){
						foreach($groups as $group){
							$groupnames[$group->Number] = $client->ProductGroup_GetName(array('productGroupHandle' => $group))->ProductGroup_GetNameResult;
						}
					}else{
						//print_r($groups);
						$groupnames[$groups->Number] = $client->ProductGroup_GetName(array('productGroupHandle' => $groups))->ProductGroup_GetNameResult;
					}
				}
				if($args['key'] == 'customer-group'){
					$groups = $client->DebtorGroup_GetAll()->DebtorGroup_GetAllResult->DebtorGroupHandle;
					if(empty($groups)){
						_e('<span><i style="margin-left:25px; color: #F00;">ERROR: Getting customer group failed, do you have a customer group in e-conomic?</i></span>', 'woocommerce-e-conomic-integration');
						return false;
					}
					if(is_array($groups)){
						foreach($groups as $group){
							$groupnames[$group->Number] = $client->DebtorGroup_GetName(array('debtorGroupHandle' => $group))->DebtorGroup_GetNameResult;
						}
					}else{
						$groupnames[$groups->Number] = $client->DebtorGroup_GetName(array('debtorGroupHandle' => $groups))->DebtorGroup_GetNameResult;
					}
				}
				
				?>
                <select name="<?php echo $args['tab_key']; ?>[<?php echo $args['key']; ?>]">
                	<option><?php _e('Select an option') ?></option>
                <?php
				if(is_array($groups)){
					foreach($groups as $group){
                ?>
                    <option <?php if(isset($options[$args['key']]) && $options[$args['key']] == $group->Number) echo 'selected'; ?> value='<?php echo $group->Number; ?>'><?php echo $group->Number.'-'.$groupnames[$group->Number]; ?></option>
                 
            	<?php
					}
				}else{
				?>
                	<option <?php if(isset($options[$args['key']]) && $options[$args['key']] == $groups->Number) echo 'selected'; ?> value='<?php echo $groups->Number; ?>'><?php echo $groups->Number.'-'.$groupnames[$groups->Number]; ?></option>
                <?php
				}
				?>
                </select>
                    <span><i><?php echo $args['desc']; ?></i></span>
                <?php
            }


            /**
             * Generates html for checkbox for given settings params
             *
             * @access public
             * @param void
             * @return void
             */
            function field_option_checkbox($args) {
                $options = get_option($args['tab_key']);
                $str = '';
                if(isset($options[$args['key']])){
                    if($options[$args['key']] == 'on'){
                        $str = 'checked = checked';
                    }
                }

                ?>
                <input <?php echo isset($args['id'])? 'id="'.$args['id'].'"': ''; ?> type="checkbox" name="<?php echo $args['tab_key']; ?>[<?php echo $args['key']; ?>]" <?php echo $str; ?> />
                <span><i><?php echo $args['desc']; ?></i></span>
            <?php
            }
			
			/**
             * Generates webhook URL for WooCommerce subscription
             *
             * @access public
             * @param void
             * @return webshook for handling payment capture when the invoice is booked and markeda as paid.
             */
            function field_woosubscription($args) {
				echo '<b>'.admin_url('admin-ajax.php').'?action=capture_payment&tono=[TOSERIALNO]&fromno=[FROMSERIALNO]</b>';
            ?>
                <br /><span><i><?php echo $args['desc']; ?></i></span>
            <?php
            }
			
			

            /**
             * WooCommerce Loads settigns
             *
             * @access public
             * @param void
             * @return void
             */
            function load_settings() {
                $this->general_settings = (array) get_option( $this->general_settings_key );
                $this->order_settings = (array) get_option( $this->order_settings_key );
            }

            /**
             * Tabs and plugin page setup
             *
             * @access public
             * @param void
             * @return void
             */
            function plugin_options_tabs() {
                $current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->start_action_key;
                $options = get_option('woocommerce_economic_general_settings');
                echo '<div class="wrap"><h2>WooCommerce e-conomic Integration</h2><div id="icon-edit" class="icon32"></div></div>';
                $key_status = $this->is_license_key_valid();
                if(!isset($options['license-key']) || $options['license-key'] == '' || $key_status!='Active'){
                    echo "<button type=\"button button-primary\" class=\"button button-primary\" title=\"\" style=\"margin:5px\" onclick=\"window.open('http://whmcs.onlineforce.net/cart.php?a=add&pid=56&carttpl=flex-web20cart&language=English','_blank');\">".__('Get license Key', 'woocommerce-e-conomic-integration')."</button> <div class='key_error'>".__('License Key Invalid', 'woocommerce-e-conomic-integration')."</div>";

                }

                echo '<h2 class="nav-tab-wrapper">';

                foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
                    $active = $current_tab == $tab_key ? 'nav-tab-active' : '';
                    echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->plugin_options_key . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
                }
                echo '</h2>';

            }

            /**
             * WooCommerce Billogram General Settings
             *
             * @access public
             * @param void
             * @return void
             */
            function register_woocommerce_economic_general_settings() {

                $this->plugin_settings_tabs[$this->general_settings_key] = __('General settings', 'woocommerce-e-conomic-integration');
				
				$options = get_option('woocommerce_economic_general_settings');

                register_setting( $this->general_settings_key, $this->general_settings_key );
				
                add_settings_section( 'section_general', __('API Keys', 'woocommerce-e-conomic-integration'), array( &$this, 'api_key_section_desc' ), $this->general_settings_key );
				
				add_settings_section( 'product_settings', __('Product sync settings', 'woocommerce-e-conomic-integration'), array( &$this, 'product_setting_section_desc' ), $this->general_settings_key );
				
				add_settings_section( 'order_settings', __('Order sync settings', 'woocommerce-e-conomic-integration'), array( &$this, 'order_setting_section_desc' ), $this->general_settings_key );
				
				add_settings_section( 'other_settings', __('Other settings', 'woocommerce-e-conomic-integration'), array( &$this, 'other_setting_section_desc' ), $this->general_settings_key );
				
				add_settings_field( 'woocommerce-economic-token', __('Token ID', 'woocommerce-e-conomic-integration'), array( &$this, 'field_option_text'), $this->general_settings_key, 'section_general', array ( 'tab_key' => $this->general_settings_key, 'key' => 'token', 'desc' => __('Token access ID from e-conomic.', 'woocommerce-e-conomic-integration')) );
				
                add_settings_field( 'woocommerce-economic-license-key', __('License key', 'woocommerce-e-conomic-integration'), array( &$this, 'field_option_text' ), $this->general_settings_key, 'section_general', array ( 'id' => 'license-key', 'tab_key' => $this->general_settings_key, 'key' => 'license-key', 'desc' => __('This is the License key you received from us by mail.', 'woocommerce-e-conomic-integration')) );
				
				//add_settings_field( 'woocommerce-economic-appToken', __('Private app ID', 'woocommerce-e-conomic-integration'), array( &$this, 'field_option_text'), $this->general_settings_key, 'section_general', array ( 'tab_key' => $this->general_settings_key, 'key' => 'appToken', 'desc' => __('Private app ID from e-conomic.', 'woocommerce-e-conomic-integration')) );
				
				//add_settings_field( 'woocommerce-economic-agreementNumber', 'Avtalsnr.', array( &$this, 'field_option_text'), $this->general_settings_key, 'section_general', array ( 'tab_key' => $this->general_settings_key, 'key' => 'agreementNumber', 'desc' => 'Här anges din avtalsnr. från e-conomic.') );
				
				//add_settings_field( 'woocommerce-economic-agreementNumber', 'Avtalsnr.', array( &$this, 'field_option_text'), $this->general_settings_key, 'section_general', array ( 'tab_key' => $this->general_settings_key, 'key' => 'agreementNumber', 'desc' => 'Här anges din avtalsnr. från e-conomic.') );
				
                //add_settings_field( 'woocommerce-economic-username', 'Användar-ID', array( &$this, 'field_option_text' ), $this->general_settings_key, 'section_general', array ( 'tab_key' => $this->general_settings_key, 'key' => 'username', 'desc' => 'Här anges din användar ID från e-conomic.') );
				
                //add_settings_field( 'woocommerce-economic-password', 'Lösenord', array( &$this, 'field_option_text' ), $this->general_settings_key, 'section_general', array ( 'tab_key' => $this->general_settings_key, 'key' => 'password', 'desc' => 'Här anges din lösenord från e-conomic.') );
				
				//add_settings_field( 'woocommerce-economic-sync-option', 'e-conomic synk alternativ', array( &$this, 'field_option_dropdown' ), $this->general_settings_key, 'section_general', array ( 'id' => 'sync-option', 'tab_key' => $this->general_settings_key, 'key' => 'sync-option', 'desc' => 'Välj vilken enhet som ska skapas på e-conomic') );
				
				//add_settings_field( 'woocommerce-economic-cashbook', 'Aktivera kassaböckerna', array( &$this, 'field_option_checkbox' ), $this->general_settings_key, 'section_general', array ( 'id' => 'activate-cashbook', 'tab_key' => $this->general_settings_key, 'key' => 'activate-cashbook', 'desc' => 'Skapa debattör betalnings matchande fakturabeloppet efter att ha skapat fakturan.') );
				
                //add_settings_field( 'woocommerce-economic-cashbook-name', 'Kassaböckerna namn', array( &$this, 'field_option_text'), $this->general_settings_key, 'section_general', array ( 'id' => 'cashbook-name', 'tab_key' => $this->general_settings_key, 'key' => 'cashbook-name', 'desc' => 'Välj kassaböckerna att lägga gäldenärens betalningar.'));
				
				add_settings_field( 'woocommerce-economic-initiate-order', __('Initiate order sync', 'woocommerce-e-conomic-integration'), array( &$this, 'field_option_order_sync_dropdown' ), $this->general_settings_key, 'order_settings', array ( 'tab_key' => $this->general_settings_key, 'key' => 'initiate-order', 'desc' => __('Select how do you want to sync WooCommerce order to e-conomic initially.', 'woocommerce-e-conomic-integration')) );
				
				add_settings_field( 'woocommerce-economic-initiate-order-event', __('', 'woocommerce-e-conomic-integration'), array( &$this, 'field_option_event_radio' ), $this->general_settings_key, 'order_settings', array ( 'tab_key' => $this->general_settings_key, 'key' => 'initiate-order-event', 'desc' => __('Chose an event for initial order sync.')) );
				
				add_settings_field( 'woocommerce-economic-initiate-order-status', __('', 'woocommerce-e-conomic-integration'), array( &$this, 'field_option_status_checkbox' ), $this->general_settings_key, 'order_settings', array ( 'tab_key' => $this->general_settings_key, 'key' => 'initiate-order-status', 'desc' => __('Chose all statuses for which an order is synced.')) );			
				
				add_settings_field( 'woocommerce-economic-other-checkout', __('Other checkout', 'woocommerce-e-conomic-integration'), array( &$this, 'field_option_dropdown' ), $this->general_settings_key, 'order_settings', array ( 'tab_key' => $this->general_settings_key, 'key' => 'other-checkout', 'desc' => __('What should be created at e-conomic when the checkout is made via any payment gateway but not e-conomic. <br><i style="margin-left:25px; color: #F00; font-weight: bold;">Note: </i><i>e-conomic Orders and Draft invoices can be updated later, but e-conomic Invoice are readonly.</i>', 'woocommerce-e-conomic-integration')) );
				
				add_settings_field( 'woocommerce-economic-economic-checkout', __('e-conomic checkout', 'woocommerce-e-conomic-integration'), array( &$this, 'field_option_dropdown' ), $this->general_settings_key, 'order_settings', array ( 'tab_key' => $this->general_settings_key, 'key' => 'economic-checkout', 'desc' => __('What should be created at e-conomic when the checkout is made via e-conomic. Go to WooCommerce>Settings>Checkout to enable e-Conomic Invoice as payment option. <br><i style="margin-left:25px; color: #F00; font-weight: bold;">Note: </i><i>e-conomic Orders and Draft invoices can be updated later, but e-conomic Invoice are readonly.</i>', 'woocommerce-e-conomic-integration')) );
				
				add_settings_field( 'woocommerce-economic-activate-oldordersync', __('Activate old orders sync', 'woocommerce-e-conomic-integration'), array( &$this, 'field_option_checkbox' ), $this->general_settings_key, 'order_settings', array ( 'tab_key' => $this->general_settings_key, 'key' => 'activate-oldordersync', 'desc' => __('Also sync orders created before wooconomics installation.', 'woocommerce-e-conomic-integration')) );
				
				add_settings_field( 'woocommerce-economic-product-sync', __('Activate product sync', 'woocommerce-e-conomic-integration'), array( &$this, 'field_option_checkbox' ), $this->general_settings_key, 'product_settings', array ( 'tab_key' => $this->general_settings_key, 'key' => 'product-sync', 'desc' => __('Sync product information from WooCommerce to e-conomic. Setting for disabling stock sync is below.', 'woocommerce-e-conomic-integration')) );
				
				add_settings_field( 'woocommerce-economic-product-stock-sync', __('Activate product stock sync', 'woocommerce-e-conomic-integration'), array( &$this, 'field_option_checkbox' ), $this->general_settings_key, 'product_settings', array ( 'tab_key' => $this->general_settings_key, 'key' => 'product-stock-sync', 'desc' => __('Sync product stock from e-conomic to WooCommerce', 'woocommerce-e-conomic-integration')) );
				
				add_settings_field( 'woocommerce-economic-scheduled-product-sync', __('Run scheduled product stock sync', 'woocommerce-e-conomic-integration'), array( &$this, 'field_option_schedule' ), $this->general_settings_key, 'product_settings', array ( 'tab_key' => $this->general_settings_key, 'key' => 'scheduled-product-sync', 'desc' => __('Run scheduled product stock sync from e-conomic to WooCommerce. Web hook option will update WooCommerce product when e-conomic product is updated.', 'woocommerce-e-conomic-integration')) );
				
				if(isset($options['token'])){
					add_settings_field( 'woocommerce-economic-product-group', __('Product group', 'woocommerce-e-conomic-integration'), array( &$this, 'field_option_group' ), $this->general_settings_key, 'product_settings', array ( 'id' => 'product-group', 'tab_key' => $this->general_settings_key, 'key' => 'product-group', 'desc' => __('e-conomic product group to which new products are added.', 'woocommerce-e-conomic-integration')) );
					
					add_settings_field( 'woocommerce-economic-product-prefix', __('Product prefix', 'woocommerce-e-conomic-integration'), array( &$this, 'field_option_text' ), $this->general_settings_key, 'product_settings', array ( 'id' => 'product-prefix', 'tab_key' => $this->general_settings_key, 'key' => 'product-prefix', 'desc' => __('Prefix added to the products stored to e-conomic from woocommerce', 'woocommerce-e-conomic-integration')) );
					
					add_settings_field( 'woocommerce-economic-customer-group', __('Customer group', 'woocommerce-e-conomic-integration'), array( &$this, 'field_option_group' ), $this->general_settings_key, 'other_settings', array ( 'id' => 'customer-group', 'tab_key' => $this->general_settings_key, 'key' => 'customer-group', 'desc' => __('e-conomic customer group to which new customers are added. <br><i style="margin-left:25px; color: #F00;">MUST be selected. Sync is not possible if not selected.</i>', 'woocommerce-e-conomic-integration')) );
					
					$wooSubscription = 'woocommerce-subscriptions/woocommerce-subscriptions.php';
					if(is_plugin_active($wooSubscription)){
						add_settings_field( 'woocommerce-economic-subscription', __('WooCommerce Subscription Hook', 'woocommerce-e-conomic-integration'), array( &$this, 'field_woosubscription' ), $this->general_settings_key, 'other_settings', array ( 'id' => 'subscription', 'tab_key' => $this->general_settings_key, 'key' => 'shipping-group', 'desc' => __('Add this URL to e-conomic Web hook type "Entries booked" in new ledger layout or "Day book booked" in old ledger layout to support WooCommerce subscriptions.', 'woocommerce-e-conomic-integration')) );
					}
					
					add_settings_field( 'woocommerce-economic-shipping-group', __('Shipping group', 'woocommerce-e-conomic-integration'), array( &$this, 'field_option_group' ), $this->general_settings_key, 'other_settings', array ( 'id' => 'shipping-group', 'tab_key' => $this->general_settings_key, 'key' => 'shipping-group', 'desc' => __('e-conomic product group to which shipping methods are added.', 'woocommerce-e-conomic-integration')) );
					
					add_settings_field( 'woocommerce-economic-coupon-group', __('Coupon group', 'woocommerce-e-conomic-integration'), array( &$this, 'field_option_group' ), $this->general_settings_key, 'other_settings', array ( 'id' => 'coupon-group', 'tab_key' => $this->general_settings_key, 'key' => 'coupon-group', 'desc' => __('e-conomic product group to which coupon discounts are added.', 'woocommerce-e-conomic-integration')) );
					
					add_settings_field( 'woocommerce-economic-order-reference-prefix', __('Order reference prefix', 'woocommerce-e-conomic-integration'), array( &$this, 'field_option_text' ), $this->general_settings_key, 'order_settings', array ( 'id' => 'order-reference-prefix', 'tab_key' => $this->general_settings_key, 'key' => 'order-reference-prefix', 'desc' => __('Prefix added to the order reference of an Order synced to e-conomic from woocommerce', 'woocommerce-e-conomic-integration')) );
				}
				//add_settings_field( 'woocommerce-economic-customer-prefix', 'Kund prefix', array( &$this, 'field_option_text' ), $this->general_settings_key, 'section_general', array ( 'id' => 'customer-prefix', 'tab_key' => $this->general_settings_key, 'key' => 'customer-prefix', 'desc' => 'Prefix läggs till kunder sparade till e-conomic från woocommerce') );
				//add_settings_field( 'woocommerce-economic-shipping-id', 'Frakt produktnummer', array( &$this, 'field_option_text' ), $this->general_settings_key, 'section_general', array ( 'id' => 'shipping-product-id', 'tab_key' => $this->general_settings_key, 'key' => 'shipping-product-id', 'desc' => 'Denna produkt numret läggs till alla fakturor som produktnummer för sjöfarten') );
            }


            /**
             * WooCommerce Manual Actions Settings
             *
             * @access public
             * @param void
             * @return void
             */
            function register_woocommerce_economic_manual_action() {

                $this->plugin_settings_tabs[$this->manual_action_key] = __('Manual functions', 'woocommerce-e-conomic-integration');
                register_setting( $this->manual_action_key, $this->manual_action_key );
            }


            /**
             * WooCommerce Start Actions
             *
             * @access public
             * @param void
             * @return void
             */
            function register_woocommerce_economic_start_action() {
                $this->plugin_settings_tabs[$this->start_action_key] = __('Welcome!', 'woocommerce-e-conomic-integration');
                register_setting( $this->start_action_key, $this->start_action_key );
            }


            /**
             * WooCommerce Billogram Accounting Settings
             *
             * @access public
             * @param void
             * @return void
             */
            function register_woocommerce_economic_support() {

                $this->plugin_settings_tabs[$this->support_key] = __('Support', 'woocommerce-e-conomic-integration');
                register_setting( $this->support_key, $this->support_key );
            }
			
			
			
			/**
             * The description for the general section API key settings
             *
             * @access public
             * @param void
             * @return void
             */
            function api_key_section_desc() { echo __('API key configuration', 'woocommerce-e-conomic-integration'); }
			
			/**
             * The description for the general section Order settings
             *
             * @access public
             * @param void
             * @return void
             */
            function order_setting_section_desc() { echo __('Basic settings for order sync between WooCommerce and e-conomic. You can control which parts you want to sync to e-conomic', 'woocommerce-e-conomic-integration'); }
			
			
			/**
             * The description for the general section Product settings
             *
             * @access public
             * @param void
             * @return void
             */
            function product_setting_section_desc() { echo __('Basic settings for product sync between WooCommerce and e-conomic. You can control which parts you want to sync to e-conomic', 'woocommerce-e-conomic-integration'); }
			
			/**
             * The description for the general section Product settings
             *
             * @access public
             * @param void
             * @return void
             */
            function other_setting_section_desc() { echo __('Basic settings for customers, shippings and coupons sync between WooCommerce and e-conomic.', 'woocommerce-e-conomic-integration'); }
			
			
			
			

            /**
             * The description for the general section
             *
             * @access public
             * @param void
             * @return void
             */
            function section_general_desc() { echo __('Specifies basic settings for the e-conomic integration and you can control which parts you want to sync to e-conomic', 'woocommerce-e-conomic-integration'); }

            /**
             * The description for the accounting section
             *
             * @access public
             * @param void
             * @return void
             */
            function section_accounting_desc() { echo __('Description Accounting settings.', 'woocommerce-e-conomic-integration'); }

            /**
             * The description for the shipping section
             *
             * @access public
             * @param void
             * @return void
             */
            function section_order_desc() { echo ''; }

            /**
             * Options page
             *
             * @access public
             * @param void
             * @return void
             */
            function woocommerce_economic_options_page() {
                $tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->start_action_key;
				$options = get_option('woocommerce_economic_general_settings');?>
                

                <!-- CSS -->
                <style>
                    li.logo,  {
                        float: left;
                        width: 100%;
                        padding: 20px;
                    }
                    li.full {
	                    padding: 10px 0;
                        height: 50px;
                    }
                    li.full img, img.test_load{
                        float: left;
                        margin: -5px 0 0 5px;
                        display: none;
                    }
					span.test_warning{
						float: left;
						margin:25px 0px 0px 10px;
					}
                    li.col-two {
                        /*float: left;
                        width: 380px;
                        margin-left: 1%;*/
						margin-left: 5%;
						list-style-type: disc;
						font-weight: 500;
						font-size: 24px;
						color: #0073aa;
						line-height: 30px;
                    }
					li.col-two a{
						text-decoration: none;
					}
                    li.col-onethird, li.col-twothird {
	                    float: left;
                    }
                    li.col-twothird {
	                    max-width: 772px;
	                    margin-right: 20px;
                    }
                    li.col-onethird {
	                    width: 300px;
                    }
                    .mailsupport {
	                	background: #dadada;
	                	border-radius: 4px;
	                	-moz-border-radius: 4px;
	                	-webkit-border-radius: 4px;
	                	max-width: 230px;
	                	padding: 1px 0 20px 20px;
	                }
	                .mailsupport > h2 {
		                font-size: 20px;
		            }
	                form#support table.form-table tbody tr td, form#installationSupport table.form-table tbody tr td {
		                padding: 4px 0 !important;
		            }
		            form#support input, form#support textarea, form#installationSupport input, form#support textarea {
			                border: 1px solid #b7b7b7;
			                border-radius: 3px;
			                -moz-border-radius: 3px;
			                -webkit-border-radius: 3px;
			                box-shadow: none;
			                width: 210px;
			        }
			        form#support textarea, form#installationSupport textarea {
				        height: 60px;
			        }
			        form#support button, form#installationSupport button {
				        float: left;
				        margin: 0 !important;
				        min-width: 100px;
				    }
				    ul.manuella li.full button.button {
					       clear: left;
					       float: left;
					       min-width: 250px;
				    }
				    ul.manuella li.full > p {
					        clear: right;
					        float: left;
					        margin: 2px 0 20px 11px;
					        max-width: 440px;
					        padding: 5px 10px;
					}
					.key_error
					{
						background-color: white;
					    color: red;
					    display: inline;
					    font-weight: bold;
					    margin-top: 5px;
					    padding: 5px;
					    position: absolute;
					    text-align: center;
					    width: 200px;
					}
					.testConnection{
						float:left;
					}
					
					.buttonDisable {
						background: #C8C1C1 !important;  
						border-color: #8C8989 !important;  
						-webkit-box-shadow: inset 0 1px 0 rgba(114, 117, 118, 0.5),0 1px 0 rgba(0,0,0,.15) !important; 
						box-shadow: inset 0 1px 0 rgba(176, 181, 182, 0.5),0 1px 0 rgba(0,0,0,.15) !important;
					}
					
					p.submit{
						float: left;
						width: auto;
						padding: 0px;
					}
					/*li.wp-first-item{
						display:none;
					}*/
					span#sandbox-mode{
						color:#F00
					}
					span.error{
						color:#F00
					}
					#sync_direction{
						float:left;
						min-width:250px;
						color: #555;
						border-color: #ccc;
						background: #f7f7f7;
						margin-top: 60px;
						margin-left: 8px;
					}
                </style>
                <script type="text/javascript">
					jQuery(document).ready(function() {
						jQuery('.ew_sync').each(function() {
                            jQuery(this).hide();
                        });
						jQuery('#sync_direction').change(function() {
							if(jQuery(this).val() == 'ew'){
								jQuery('.ew_sync').each(function() {
									jQuery(this).show();
								});
								jQuery('.we_sync').each(function() {
									jQuery(this).hide();
								});
							}else{
								jQuery('.ew_sync').each(function() {
									jQuery(this).hide();
								});
								jQuery('.we_sync').each(function() {
									jQuery(this).show();
								});
							}
                        });
						var element = jQuery('#cashbook-name').parent().parent();
						if(jQuery('#activate-cashbook').is(':checked')){
							element.show();
						}else{
							element.hide();
						}
						jQuery('#activate-cashbook').change(function() {
							if(this.checked) {
								element.show(300);							
							}else{
								element.hide(300);
							}
						});
						var event_based_option = jQuery("#event_based_options").parent().parent();
						var status_based_options = jQuery("#status_based_options").parent().parent();
						if(jQuery("#initiate_order").val() == 'event_based'){
							jQuery(status_based_options).hide();
						}
						if(jQuery("#initiate_order").val() == 'status_based'){
							jQuery(event_based_option).hide();
						}
						
						jQuery("#initiate_order").change(function(){
							if(jQuery(this).val() == 'event_based'){
								jQuery(event_based_option).show();
								jQuery(status_based_options).hide();
							}
							if(jQuery(this).val() == 'status_based'){
								jQuery(event_based_option).hide();
								jQuery(status_based_options).show();
							}
						});
					});
						
					jQuery("#license-key").live("keyup", function(){
						var str = jQuery("#license-key").val();
						var patt = /wem-[a-zA-Z0-9][^\W]+/gi;
						var licenseMatch = patt.exec(str);
						if(licenseMatch){
							licenseMatch = licenseMatch.toString();
							if(licenseMatch.length == 24){
								jQuery("#license-key").next().removeClass("error");
								jQuery("#license-key").next().children("i").html("Här anges License-nyckeln du har erhållit från oss via mail.");
							}else{
								jQuery("#license-key").next().children("i").html("Ogiltigt format");
								jQuery("#license-key").next().addClass("error");
							}
						}else{
							jQuery("#license-key").next().children("i").html("Ogiltigt format");
							jQuery("#license-key").next().addClass("error");
						}
						
					});
				</script>
                <?php
                if($tab == $this->support_key){ ?>
                    <div class="wrap">
                        <?php $this->plugin_options_tabs(); ?>
                        <ul>
                            <li class="logo"><?php echo '<img src="' . plugins_url( 'img/logo_landscape.png', __FILE__ ) . '" > '; ?></li>
                            <li class="col-two"><a style="" href="http://wooconomics.com/category/faq/"><?php _e('Our most frequently asked questions FAQ', 'woocommerce-e-conomic-integration'); ?></a></li>
                            <li class="col-two"><a href="http://wooconomics.com/"><?php _e('Support', 'woocommerce-e-conomic-integration'); ?></a></li>
                        </ul>
                    </div>
                <?php
                }
                else if($tab == $this->general_settings_key){ ?>
                    <div class="wrap">
                        <?php $this->plugin_options_tabs(); ?>
                        <form method="post" action="options.php">
                            <?php wp_nonce_field( 'update-options' ); ?>
                            <?php settings_fields( $tab ); ?>
                            <?php do_settings_sections( $tab ); ?>
                            <?php submit_button(__('Save changes', 'woocommerce-e-conomic-integration')); ?>
                            <?php if(!isset($options['token']) || $options['token'] == '' || !isset($options['license-key']) || $options['license-key'] ==''){ ?>
                            <button style="margin: 20px 0px 0px 10px;" type="button" name="testConnection" class="button button-primary buttonDisable testConnection" onclick="" /><?php echo __('Test connection', 'woocommerce-e-conomic-integration'); ?></button>
                            <?php }else{ ?>
                            <button style="margin: 20px 0px 0px 10px;" type="button" name="testConnection" class="button button-primary testConnection" onclick="test_connection()" /><?php echo __('Test connection', 'woocommerce-e-conomic-integration'); ?></button>
                            <?php } ?>
                            <span class="test_warning"><?php echo __('NOTE! Save changes before testing the connection', 'woocommerce-e-conomic-integration'); ?></span>
                            <img style="margin: 10px 0px 0px 10px;" src="<?php echo plugins_url( 'img/ajax-loader.gif', __FILE__ );?>" class="test_load" >
                        </form>
                    </div>
                <?php }
                else if($tab == $this->manual_action_key){ ?>
                    <div class="wrap">
                        <?php $this->plugin_options_tabs(); ?>
                        <ul class="manuella">
                        	<li class="full">
                            	<select id="sync_direction">
                                	<option value="we">WooCommerce to e-conomic</option>
                                    <option value="ew">e-conomic to WooCommerce</option>
                                </select>
                            	<p> <?php _e('Manual sync direction', 'woocommerce-e-conomic-integration') ?> <br /><i><?php _e('Choose this option before using "Manual sync customers" and "Manual sync products" syncs, default will be WooCommerce to e-conomic.<br>WooCommerce to e-conomic: Products and Customers data from WooCommerce send to e-conomic.<br>
e-conomic to WooCommerce: Products and Customers data from e-conomic saved at WooCommerce.', 'woocommerce-e-conomic-integration'); ?></i></p>
                                
                            </li>
                            <li class="full">
                                <button type="button" class="button" title="<?php _e('Manual sync products', 'woocommerce-e-conomic-integration'); ?>" style="margin:5px" onclick="sync_products('<?php _e('The synchronization can take a long time depending on how many products that will be exported. \ nA message will appear on this page when the synchronization is complete. Do not leave this page, which will suspended the import!', 'woocommerce-e-conomic-integration') ?>')"><?php _e('Manual sync products', 'woocommerce-e-conomic-integration'); ?></button>
                                <img src="<?php echo plugins_url( 'img/ajax-loader.gif', __FILE__ );?>" class="product_load" >
                                <p><?php _e('<span class="we_sync">Send all products to your e-conomic. If you have many products, it may take a while.</span><span class="ew_sync">Send all products to your WooCommerce store from e-conomic. If you have many products, it may take a while.</span>', 'woocommerce-e-conomic-integration'); ?></p>
                            </li>
                            <li class="full">
                                <button type="button" class="button" title="<?php _e('Manual sync delivery methods', 'woocommerce-e-conomic-integration'); ?>" style="margin:5px" onclick="sync_shippings('<?php _e('A message will appear on this page when the synchronization is complete. Do not leave this page, which will suspend the sync!', 'woocommerce-e-conomic-integration') ?>')"><?php _e('Manual sync delivery methods', 'woocommerce-e-conomic-integration'); ?></button>
                                <img src="<?php echo plugins_url( 'img/ajax-loader.gif', __FILE__ );?>" class="shipping_load" >
                                <p><?php _e('Send all delivery method costs to your e-conomic.', 'woocommerce-e-conomic-integration'); ?></p>
                            </li>
                            <li class="full">
                                <button type="button" class="button" title="<?php _e('Manual sync coupons', 'woocommerce-e-conomic-integration'); ?>" style="margin:5px" onclick="sync_coupons('<?php _e('A message will appear on this page when the synchronization is complete. Do not leave this page, which will suspend the sync!', 'woocommerce-e-conomic-integration') ?>')"><?php _e('Manual sync coupons', 'woocommerce-e-conomic-integration'); ?></button>
                                <img src="<?php echo plugins_url( 'img/ajax-loader.gif', __FILE__ );?>" class="coupon_load" >
                                <p><?php _e('Send all coupon codes to your e-conomic.', 'woocommerce-e-conomic-integration'); ?></p>
                            </li>
                            <li class="full">
                                <button type="button" class="button" title="Manuell synkning kontakter" style="margin:5px" onclick="sync_contacts('<?php _e('The synchronization can take a long time depending on how many customers to be imported. \ nA message will appear on this page when the synchronization is complete. Do not leave this page, which will suspend the sync!', 'woocommerce-e-conomic-integration') ?>')"><?php _e('Manual sync customers', 'woocommerce-e-conomic-integration'); ?></button>
                                <img src="<?php echo plugins_url( 'img/ajax-loader.gif', __FILE__ );?>" class="customer_load" >
                                <p><?php _e('<span class="we_sync">Sync customers created manually in woocommerce dashboard.</span><span class="ew_sync">Sync customers to WooCommerce from e-conomic.</span>', 'woocommerce-e-conomic-integration'); ?></p>
                            </li>
                            <li class="full">
                                <button type="button" class="button" title="Manuell Synkning beställningar/fakturor" style="margin:5px" onclick="sync_orders('<?php _e('The synchronization can take a long time depending on how many orders to be exported. \ nA message will appear on this page when the synchronization is complete. Do not leave this page, which will suspended the import!', 'woocommerce-e-conomic-integration') ?>')"><?php _e('Manual syncing orders/invoices', 'woocommerce-e-conomic-integration'); ?></button>
                                <img src="<?php echo plugins_url( 'img/ajax-loader.gif', __FILE__ );?>" class="order_load" >
                                <p><?php _e('Synchronizes all orders that failed to synchronize. (default sync is set to General Settings-> Create options)', 'woocommerce-e-conomic-integration'); ?></p>
                            </li>     
                        </ul>
                        <div class="clear"></div>
                    	<div id="result"></div>
                    </div>
                <?php }
                else if($tab == $this->start_action_key){
                    $options = get_option('woocommerce_economic_general_settings');
                    ?>
                    <div class="wrap">
                        <?php $this->plugin_options_tabs(); ?>
                        <ul>
                        	<li>
                        		<?php echo '<img src="' . plugins_url( 'img/banner-772x250.png', __FILE__ ) . '" > '; ?>
                        	</li>
                            <li class="col-twothird">
                                <iframe src="//player.vimeo.com/video/38627647" width="500" height="281" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                            </li>
                            <?php if(!isset($options['license-key']) || $options['license-key'] == ''){ ?>
                            <li class="col-onethird">
                            	<div class="mailsupport">
                            		<h2><?php echo __('Installation Support', 'woocommerce-e-conomic-integration'); ?></h2>
                            	    <form method="post" id="installationSupport">
                            	        <input type="hidden" value="send_support_mail" name="action">
                            	        <table class="form-table">
								
                            	            <tbody>
                            	            <tr valign="top">
                            	                <td>
                            	                    <input type="text" value="" placeholder="<?php echo __('Company', 'woocommerce-e-conomic-integration'); ?>" name="company">
                            	                </td>
                            	            </tr>
                            	            <tr valign="top">
                            	                <td>
                            	                    <input type="text" value="" placeholder="<?php echo __('Name', 'woocommerce-e-conomic-integration'); ?>" name="name">
                            	                </td>
                            	            </tr>
                            	            <tr valign="top">
                            	                <td>
                            	                    <input type="text" value="" placeholder="<?php echo __('Phone', 'woocommerce-e-conomic-integration'); ?>" name="telephone">
                            	                </td>
                            	            </tr>
                            	            <tr valign="top">
                            	                <td>
                            	                    <input type="text" value="" placeholder="<?php echo __('Email', 'woocommerce-e-conomic-integration'); ?>" name="email">
                            	                </td>
                            	            </tr>
                            	            <tr valign="top">
                            	                <td>
                            	                    <textarea placeholder="<?php echo __('Subject', 'woocommerce-e-conomic-integration'); ?>" name="subject"></textarea>
                            	                </td>
                            	            </tr>
                            	            <tr valign="top">
                            	                <td>
                            	                    <button type="button" class="button button-primary" title="send_support_mail" style="margin:5px" onclick="send_support_mail('installationSupport')"><?php echo __('Send', 'woocommerce-e-conomic-integration'); ?></button>
                            	                </td>
                            	            </tr>
                            	            </tbody>
                            	        </table>
                            	        <!-- p class="submit">
                            	           <button type="button" class="button button-primary" title="send_support_mail" style="margin:5px" onclick="send_support_mail()">Skicka</button> 
                            	        </p -->
                            	    </form>
                            	</div>
                            </li>
                        <?php } else{ ?>
                        	<li class="col-onethird">
                            	<div class="mailsupport">
                            		<h2><?php echo __('Support', 'woocommerce-e-conomic-integration'); ?></h2>
                            	    <form method="post" id="support">
                            	        <input type="hidden" value="send_support_mail" name="action">
                            	        <table class="form-table">
								
                            	            <tbody>
                            	            <tr valign="top">
                            	                <td>
                            	                    <input type="text" value="" placeholder="<?php echo __('Company', 'woocommerce-e-conomic-integration'); ?>" name="company">
                            	                </td>
                            	            </tr>
                            	            <tr valign="top">
                            	                <td>
                            	                    <input type="text" value="" placeholder="<?php echo __('Name', 'woocommerce-e-conomic-integration'); ?>" name="name">
                            	                </td>
                            	            </tr>
                            	            <tr valign="top">
                            	                <td>
                            	                    <input type="text" value="" placeholder="<?php echo __('Phone', 'woocommerce-e-conomic-integration'); ?>" name="telephone">
                            	                </td>
                            	            </tr>
                            	            <tr valign="top">
                            	                <td>
                            	                    <input type="text" value="" placeholder="<?php echo __('Email', 'woocommerce-e-conomic-integration'); ?>" name="email">
                            	                </td>
                            	            </tr>
                            	            <tr valign="top">
                            	                <td>
                            	                    <textarea placeholder="<?php echo __('Subject', 'woocommerce-e-conomic-integration'); ?>" name="subject"></textarea>
                            	                </td>
                            	            </tr>
                            	            <tr valign="top">
                            	                <td>
                                                	<input type="hidden" name="supportForm" value="support" />
                            	                    <button type="button" class="button button-primary" title="send_support_mail" style="margin:5px" onclick="send_support_mail('support')"><?php echo __('Send', 'woocommerce-e-conomic-integration'); ?></button>
                            	                </td>
                            	            </tr>
                            	            </tbody>
                            	        </table>
                            	        <!-- p class="submit">
                            	           <button type="button" class="button button-primary" title="send_support_mail" style="margin:5px" onclick="send_support_mail()">Skicka</button> 
                            	        </p -->
                            	    </form>
                            	</div>
                            </li>
                        <?php } ?>
                        </ul>
                    </div>
                <?php }
                else{ ?>
                    <div class="wrap">
                        <?php $this->plugin_options_tabs(); ?>
                        <form method="post" action="options.php">
                            <?php wp_nonce_field( 'update-options' ); ?>
                            <?php settings_fields( $tab ); ?>
                            <?php do_settings_sections( $tab ); ?>
                            <?php submit_button(); ?>
                        </form>
                    </div>
                <?php }
            }	

           

            /***********************************************************************************************************
             * WP-PLUGS API FUNCTIONS
             ***********************************************************************************************************/

            /**
             * Checks if license-key is valid
             *
             * @access public
             * @return void
             */
            public function is_license_key_valid() {
                include_once("class-economic-api.php");
                $wce_api = new WCE_API();
                $result = $wce_api->create_license_validation_request();
                switch ($result['status']) {
		            case "Active":
		                // get new local key and save it somewhere
		                $localkeydata = $result['localkey'];
		                update_option( 'local_key_economic_plugin', $localkeydata );
		                return $result['status'];
		                break;
		            case "Invalid":
		                logthis("License key is Invalid");
		            	return $result['status'];
		                break;
		            case "Expired":
		                logthis("License key is Expired");
                        return $result['status'];
		                break;
		            case "Suspended":
		                logthis("License key is Suspended");
		                return $result['status'];
		                break;
		            default:
                        logthis("Invalid Response");
		                break;
	        	}
            }
        }
        $GLOBALS['WC_Economic'] = new WC_Economic();
    }
}