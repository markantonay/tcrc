<?php
/**
 * the-computer-repair functions and definitions
 *
 * @package The Computer Repair Child
 */


add_action( 'wp_enqueue_scripts', 'enqueue_child_theme_styles', PHP_INT_MAX);

function enqueue_child_theme_styles() {
	wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
}

    
if ( ! function_exists( 'the_computer_repair_credit' ) ) {
    function the_computer_repair_credit(){
        echo "<a href=".esc_url(THE_COMPUTER_REPAIR_CREDIT)." target='_blank'>".esc_html__('Computer Repair WordPress Theme','the-computer-repair')."</a>";
    }
}


/* For Registration */
if ( ! function_exists( 'get_cart' ) ) {

    function get_cart($post_id){
        $product = wc_get_product( $post_id );
        return $product;
    }

}

add_action( 'init', 'wcc_remove_woo_wc_breadcrumbs' );
function wcc_remove_woo_wc_breadcrumbs() {
   // if ( is_woocommerce() || is_cart() || is_checkout() ) {
        remove_action( 'woo_main_after', 'woo_display_breadcrumbs', 10 );
    //}
}

// 


add_action('wp_footer', 'checkout_billing_email_js_ajax' );
function checkout_billing_email_js_ajax() {
    // Only on Checkout
    if( is_checkout() && ! is_wc_endpoint_url() ) :
    ?>
    <script type="text/javascript">
  //    console.log('testrs')
        if (typeof wc_checkout_params === 'undefined') 
            return false;
        </script>
    <?php
    endif;
}


// Orders Complete
add_action( 'woocommerce_order_status_completed', 'cb_push_data_manage', '1');
function cb_push_data_manage($order_id){
    $order = wc_get_order( $order_id ); // get order

    $next_renewal_date = '';
    $subscriptionId = NULL;
    if( wcs_order_contains_subscription( $order ) ){ // activate subscription
        // Get an array of WC_Subscription objects
        $subscriptions = wcs_get_subscriptions_for_order( $order_id );
        foreach( $subscriptions as $subscription_id => $subscription ){
            // Change the status of the WC_Subscription object
            $subscription->update_status( 'active' );
            $next_renewal_date = $subscription->get_date( 'next_payment_date' );
            $subscriptionId = $subscription->id;
        }    

    }

    // check if its new order, not a renewal
    if(!wcs_order_contains_renewal($order)){ // 
        // Product Order Data
        $user = $order->get_user();
        $order_data = $order->get_data(); // The Order data
        $bill_data = $order_data['billing'];
        
        $attribute_value = NULL;
        $attribute_name = NULL;
        $items = $order->get_items();
        foreach ( $items as $item ) {
            $product_name = $item->get_name();
            $product_id = $item->get_product_id();
            $product_variation_id = $item->get_variation_id();
            $subscription_id = get_post_meta($product_id, 'subscription_id', true );

            $product = $item->get_product();

            if($product->is_type('variation')){
                 // Get the variation attributes
                $variation_attributes = $product->get_variation_attributes();
                // Loop through each selected attributes
                foreach($variation_attributes as $attribute_taxonomy => $term_slug){
                    $taxonomy = str_replace('attribute_', '', $attribute_taxonomy );                
                    $attribute_name = get_taxonomy( $taxonomy )->labels->singular_name;             
                    $attribute_value = get_term_by( 'slug', $term_slug, $taxonomy )->name;
                }          
            }

            $terms = get_the_terms( $product_id, 'product_cat' );
            foreach ( $terms as $term ) {
                // Categories by slug
                $product_cat_slug = $term->slug;

                // Check if Product Category is Subscription
                if($term->slug == 'Subscription'){

                }
            }
            $subscription_arr[] = array('subscription_id' => $subscription_id, 'category' => $product_cat_slug);
           
        }
  
        $bodyArr = array(
                'wp_order_id' => $subscriptionId,
                'subscriptions' => $subscription_arr,  
                'renewal_date' => $next_renewal_date,         
                'email' => $bill_data['email'],
                'first_name' => $bill_data['first_name'],
                'last_name' => $bill_data['last_name'],
                'company' => $bill_data['company'],
                'mobile' => $bill_data['phone']                   
            );

        $user = $order->get_user();

        //$url = 'https://demo.rvas.com.au/api/v1/process-subscription';
        //$url = 'http://manage.rvas.com.au/api/v1/process-subscription';
        $url = 'http://dev.local.com/api/v1/process-subscription';

        $args = array(
                'method' => 'POST',
                'timeout' => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers'     => array('Content-type: application/x-www-form-urlencoded'),
                'body' => $bodyArr,
                );
      
        $return = wp_remote_post($url, $args );
        
    }
}

// Subscroption Complete
add_action('woocommerce_subscription_payment_complete', 'subscription_payment_complete_hook_callback', 10, 2);
function subscription_payment_complete_hook_callback( $subscription ) {
    // Get the last order
    $last_order = $subscription->get_last_order( 'all', 'any' );

    // Get the user ID from an Order ID
    //$user_id = get_post_meta( $order_id, '_customer_user', true );

    // Get an instance of the WC_Customer Object from the user ID
    //$customer = new WC_Customer( $user_id );

    $customer_email =  $subscription->get_billing_email();

    $sid = $subscription->id;
    $subscription    = wcs_get_subscription( $sid );
    $next_renewal_date = $subscription->get_date( 'next_payment_date' );

    // Check if the  last order is not false or order is a subscription renewal
    if( $last_order !== false  || wcs_order_contains_renewal($last_order)) {

        $sid = $subscription->id;
        $uid = $subscription->customer_user;
        $subscription    = wcs_get_subscription( $sid );
        $items = $subscription->get_items();    
        foreach ( $items as $item ) {
            $product_id = $item->get_product_id();
            $order_id = $subscription->get_parent_id();
            $subscription_id = get_post_meta($product_id, 'subscription_id', true );

        }

        // Query run update status
        $manage_db = new wpdb('root', '', 'rvas', '127.0.0.1');

        $query = "UPDATE customer_subscription 
                LEFT JOIN `customer` ON `customer_subscription`.`customer_id` = `customer`.`id`
                SET customer_subscription.expiry_date = '". date('Y-m-d',strtotime($next_renewal_date))."' 
                WHERE customer.email = '".$customer_email."' AND customer_subscription.wp_order_id = '".$sid."'";


        $results = $manage_db->get_results($query);

    }

}










// Additional field
// add_filter('woocommerce_billing_fields', 'custom_woocommerce_billing_fields');

// function custom_woocommerce_billing_fields($fields)
// {

//     $fields['billing_options'] = array(
//         'label' => __('Company', 'woocommerce'), // Add custom field label
//         'placeholder' => _x('Your NIF here....', 'placeholder', 'woocommerce'), // Add custom field placeholder
//         'required' => true, // if field is required or not
//         'clear' => false, // add clear or not
//         'type' => 'text', // add field type
//         'class' => array('my-css')    // add class name
//     );

//     return $fields;
// }

// When Order is "completed" auto-change the status of the WC_Subscription object to 'on-hold'
// add_action('woocommerce_order_status_completed','updating_order_status_completed_with_subscription');
// function updating_order_status_completed_with_subscription($order_id) {
//     $order = wc_get_order($order_id);  // Get an instance of the WC_Order object

//     if( wcs_order_contains_subscription( $order ) ){

//         // Get an array of WC_Subscription objects
//         $subscriptions = wcs_get_subscriptions_for_order( $order_id );
//         foreach( $subscriptions as $subscription_id => $subscription ){
//             // Change the status of the WC_Subscription object
//             $subscription->update_status( 'active' );
//         }
//     }
// }


// add_filter( 'woocommerce_get_checkout_url', 'custom_checkout_url', 30 );
// function custom_checkout_url( $checkout_url ) {

//     return $url = 'http://dev.local.com/api/v1/process-subscription'; // custom URL   
// }

// Hook in when payment is complete
// add_action( 'woocommerce_payment_complete', 'so_payment_complete' );
// function so_payment_complete( $order_id ){
//     $order = wc_get_order( $order_id );

//     if( $user ){
//         // do something with the user
//         print_r($user);
//         die();
        
//     }
// }


// function your_function( $order_id ){
//  // echo 'a';
//  // die();
//    // $order = new WC_Order( $order_id );
//     //$to_email = 'anthonyg@cloudstaff.com';
//  //   $payment = $order->get_payment_method_title();
//     //$headers = 'From: Your Name <Your_site_mail@address.com>' . "\r\n";
//     //wp_mail($to_email, 'subject', 'asdsa', $headers );

//      //user posted variables
//      $name = 'test';
//      $email ='markhaitus@gmail.com';
//      $message = 'asda';

//      //php mailer variables
//      $to = get_option('admin_email');
//      $subject = "Some text in subject...";
//      $headers = 'From: '. $email . "\r\n" .
//      'Reply-To: ' . $email . "\r\n";
        

//      //Here put your Validation and send mail
//      $sent = wp_mail($to, $subject, strip_tags($message), $headers);
//        if($sent) {
//          echo 'sent';
//        }//message sent!
//        else  {
//              echo 'failed';
//        }//message wasn't sent
// }

// add_action( 'wp_footer', 'your_function');


// show wp_mail() errors
// add_action( 'wp_mail_failed', 'onMailError', 10, 1 );
// function onMailError( $wp_error ) {
//     echo "<pre>";
//     print_r($wp_error);
//     echo "</pre>";
// }   

// add_action('wp_mail_failed', 'log_mailer_errors', 10, 1);
// function log_mailer_errors( $wp_error ){
//  print_r($wp_error);
//   $fn = ABSPATH . '/mail.log'; // say you've got a mail.log file in your server root
//   $fp = fopen($fn, 'a');
//   fputs($fp, "Mailer Error: " . $wp_error->get_error_message() ."\n");
//   fclose($fp);
// }

function get_paypal_order($raw_custom) {
    $custom = json_decode($raw_custom);
    if ($custom && is_object($custom)) {
        $order_id = $custom->order_id;
        $order_key = $custom->order_key;
    } else {
        return false;
    }
    
    $order = wc_get_order($order_id);
    if (!$order) {
        $order_id = wc_get_order_id_by_order_key($order_key);
        $order = wc_get_order($order_id);
    }
    if (!$order || $order->get_order_key() !== $order_key) {
        return false;
    }
    return $order;
}
 
function update_wc_order_status($posted) {
    $order = !empty($posted['custom']) ? get_paypal_order($posted['custom']) : false;
    if ($order) {
        $posted['payment_status'] = strtolower($posted['payment_status']);
        if ('completed' === $posted['payment_status']) {
            $order->add_order_note(__('IPN payment completed', ''));
            $order->payment_complete(!empty($posted['txn_id']) ? wc_clean($posted['txn_id']) : '' );
        }
    }
}

// add_action('paypal_ipn_for_wordpress_payment_status_completed', 'update_wc_order_status', 10, 1);

// add_action( 'woocommerce_subscription_renewal_payment_complete', 'wsrp_complete', 10, 1 );
// function wsrp_complete($subscription){
//     // complete renewal
//     echo "renewal complete";
//     die();
//     // Init Update manage database
//     $manage_db = new wpdb(DB_USER, DB_PASSWORD, $database_name, DB_HOST);

//     // $results = $manage_db->get_results("
//     //         Update 
//     //     ");


// }

// 




?>