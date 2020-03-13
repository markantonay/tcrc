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

add_action('wp_footer', 'checkout_billing_email_js_ajax' );
function checkout_billing_email_js_ajax() {
    // Only on Checkout
    if( is_checkout() && ! is_wc_endpoint_url() ) :
    ?>
    <script type="text/javascript">
    //jQuery(function($){

  //    console.log('testrs')
        if (typeof wc_checkout_params === 'undefined') 
            return false;

    // wc_checkout_params.checkout_url = 'http://dev.local.com/api/v1/process-subscription';
     //    jQuery(document).ready(function($) {
        //     $(document).on("click", "#place_order" ,function(e) {
        //         e.preventDefault();
        //         var post_data = $( 'form.checkout' ).serialize()
        //         var data = {
        //             action: 'ajax_order',
        //             post_data : post_data
        //         };

        //         $.post('http://dev.local.com/api/v1/process-subscription', data);
        //     });
        // });
    //     $(document.body).on("click", "#place_order" ,function(evt) {
    //         evt.preventDefault();

    //         $.ajax({
    //             type: 'POST',
    //             url: 'http://dev.local.com/api/v1/process-subscription',
    //             contentType: "application/x-www-form-urlencoded; charset=UTF-8",
                // headers: {
                //  'Content-Type': 'application/x-www-form-urlencoded'
                // },
    //             enctype: 'multipart/form-data',
                // crossDomain: true,
    //             data: $('form.checkout').serialize(),              
    //             success: function (result) {
    //                 console.log(result); // For testing (to be removed)
    //             },
    //             error:   function(error) {
    //                 console.log(error); // For testing (to be removed)
    //             }
    //         });
    //     });
   // });
    </script>
    <?php
    endif;
}

add_action( 'woocommerce_order_status_completed', 'cb_push_data_manage', '1');
function cb_push_data_manage($order_id){
    $order = wc_get_order( $order_id ); // get order
    $user = $order->get_user();
    $order_data = $order->get_data(); // The Order data
    $bill_data = $order_data['billing'];
    //echo '<pre>';
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

        $subscription_arr[] = array('subscription_id' => $subscription_id, 'name' => $attribute_name, 'value' => $attribute_value);
       
    }


//   die();

     $bodyArr = array(
            'wp_order_id' => $order_id,
            'subscriptions' => $subscription_arr,           
            'email' => $bill_data['email'],
            'first_name' => $bill_data['first_name'],
            'last_name' => $bill_data['last_name'],
            'company' => $bill_data['company'],
            'mobile' => $bill_data['phone']                   
        );

//  echo 'text';
    $user = $order->get_user();

    //$url = 'https://demo.rvas.com.au/api/v1/process-subscription';
    $url = 'http://manage.rvas.com.au/api/v1/process-subscription';

    $args = array(
            'method' => 'POST',
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers'     => array('Content-type: application/x-www-form-urlencoded'),
            'body' => $bodyArr,
            );
//  print_r($args);
//  die();
     $return = wp_remote_post($url, $args );
    // print_r($return);
     exit;
    //$myvars = 'myvar1=' . $myvar1 . '&myvar2=' . $myvar2;


    //print_r($result);

//  global $wp_filter; // test is register action name with callback function
    //print_r($wp_filter); 
//  exit;
}

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
    } 
    else {
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

add_action('paypal_ipn_for_wordpress_payment_status_completed', 'update_wc_order_status', 10, 1);

?>