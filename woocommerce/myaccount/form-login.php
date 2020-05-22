<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 4.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

do_action( 'woocommerce_before_customer_login_form' ); ?>



<div class="u-columns col2-set" id="customer_login">

	<div id="login-row" class="row justify-content-center align-items-center">
		<div id="login-column" class="col-md-6">
		    <div id="login-box" class="col-md-12">
		        <form id="login-form" class="form" action="" method="post">		   
		        	<?php do_action( 'woocommerce_login_form_start' ); ?>
     	
		            <h3 class="text-center w-header-3">Login</h3>
		            <div class="form-group">
		                <label for="username" class="username"><?php esc_html_e( 'Email address', 'woocommerce' ); ?></label><br>
		                <input type="text" name="username" id="username" class="form-control" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
		            </div>
		            <div class="form-group">
		                <label for="password" class="password"><?php esc_html_e( 'Password', 'woocommerce' ); ?></label><br>
		                <input  type="password" name="password" id="password" autocomplete="current-password" >
		            </div>
		            	<?php do_action( 'woocommerce_login_form' ); ?>

		            <div class="form-group">
		                <label for="remember-me" class="remember-me"><span>Remember me</span> <span><input id="remember-me" name="remember-me" type="checkbox"></span></label><br>
		                <?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
		                <button type="submit" class="woocommerce-button button woocommerce-form-login__submit login-btn" name="login" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>"><?php esc_html_e( 'Log in', 'woocommerce' ); ?></button>
		            </div>

		            <div class="text-right woocommerce-LostPassword lost_password">
                           	<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'woocommerce' ); ?></a>
                    </div>

					<?php do_action( 'woocommerce_login_form_end' ); ?>

		        </form>
		    </div>
		</div>
	</div>

</div>


<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
