<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' ); ?>

<?php
 $the_computer_repair_woocommerce_shop_page_sidebar = get_theme_mod( 'the_computer_repair_woocommerce_shop_page_sidebar' );
 if ( 'Disable' == $the_computer_repair_woocommerce_shop_page_sidebar ) {
   $colmd = 'col-lg-12 col-md-12';
 } else { 
   $colmd = 'col-lg-12 col-md-12';
 } 
?>

<div class="container">
	<main id="maincontent" role="main" class="middle-align woocommerce-products">
	
		<div class="row m-0">

			<div class="<?php echo esc_html( $colmd ); ?>">
				<?php do_action( 'woocommerce_before_main_content' ); ?>
				<header class="woocommerce-products-header">
					<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
						<h1 class="woocommerce-products-header__title page-title"><?php woocommerce_page_title(); ?></h1>
					<?php endif; ?>

					<?php	do_action( 'woocommerce_archive_description' );		?>
				</header>


		<div class="layout">
			<input name="nav" type="radio" class="nav subscription-radio" id="subscription" checked="checked" />
			<div class="page subscription-page">
				<div class="page-contents">
					<!-- <h1>Subscriptions</h1> -->
					<ul class="products">
					<?php
				        $args = array( 'post_type' => 'product', 'posts_per_page' => '-1', 'product_cat' => 'subscription', 'orderby' => 'date' );
				        $loop = new WP_Query( $args );
				        while ( $loop->have_posts() ) : $loop->the_post(); global $product; ?>				            
				                <li class="product">    
				                    <a href="<?php echo get_permalink( $loop->post->ID ) ?>" title="<?php echo esc_attr($loop->post->post_title ? $loop->post->post_title : $loop->post->ID); ?>" class="product-item">
				                        <?php //woocommerce_show_product_sale_flash( $post, $product ); ?>			                      
										<?php /*
							                if ( has_post_thumbnail( $loop->post->ID ) ) 
							                    echo get_the_post_thumbnail( $loop->post->ID, 'shop_catalog' ); 
							                else 
							                    echo '<img src="' . wc_placeholder_img_src() . '" alt="Placeholder" width="65px" height="115px" />'; 
							                 */
							            ?>
				                        <h3><?php the_title(); ?></h3>
				                        <p><?php // the_content(); ?></p>									
				                        <span class="price"><?php echo $product->get_price_html(); ?></span>                    

				                    </a>

				                    <?php woocommerce_template_loop_add_to_cart( $loop->post, $product ); ?>

				                </li>

				    <?php endwhile; ?>
				    <?php wp_reset_query(); ?>
					</ul>
				</div>
			</div>
			<label class="nav" for="subscription">
				<span>
					<svg height="26" viewBox="0 0 25 25" width="26" xmlns="http://www.w3.org/2000/svg"  stroke="currentColor" fill="none" >
						<path d="m23.042 16.524c-.003-.003-.005-.005-.008-.008l-3.013-3.013c-.357-.357-.933-.358-1.288-.004-.355.355-.353.931.004 1.288l.323.323c.059.059.06.156.001.215s-.155.059-.215-.001l-.753-.753c-.357-.357-.933-.358-1.288-.004s-.353.931.004 1.288l.753.753c.059.059.06.156.001.215s-.155.059-.215-.001l-1.184-1.184c-.357-.357-.933-.358-1.288-.004-.355.355-.353.931.004 1.288l1.184 1.184c.059.059.06.156.001.215s-.155.059-.215-.001l-4.197-4.197c-.357-.357-.933-.358-1.288-.004s-.353.931.004 1.288l5.051 5.05c-.662-.103-1.776-.147-2.46.536-.642.642-.625 1.086-.426 1.286.543.543.961-.315 3.435.867 2.474 1.183 4.064 1.085 5.151.016l1.498-1.498c.008-.008-.008.008 0 0 1.524-1.522 1.96-3.604.424-5.14z"/><path d="m13 5h-6c-.553 0-1 .448-1 1s.447 1 1 1h6c.553 0 1-.448 1-1s-.447-1-1-1z"/><path d="m14 10c0-.552-.447-1-1-1h-6c-.553 0-1 .448-1 1s.447 1 1 1h6c.553 0 1-.448 1-1z"/><path d="m6.75 13c-.414 0-.75.336-.75.75s.336.75.75.75h.51c.414 0 .75-.336.75-.75s-.336-.75-.75-.75z"/><path d="m9.513 17.982c-4.213-.243-7.513-3.75-7.513-7.982 0-4.411 3.589-8 8-8s8 3.589 8 8c0 .124.001.248-.008.371-.036.551.38 1.028.932 1.064.549.046 1.027-.379 1.064-.931.011-.168.012-.336.012-.504 0-5.514-4.486-10-10-10s-10 4.485-10 10c0 5.292 4.128 9.675 9.397 9.979.02.001.039.001.059.001.525 0 .967-.411.997-.942.032-.551-.389-1.024-.94-1.056z"/></svg>

					<!-- <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg> -->
				Subscriptions
				</span>
			</label>

			<input name="nav" type="radio" class="addon-radio" id="addon" />
			<div class="page addon-page">
				<div class="page-contents">
					<!-- <h1>Add Ons</h1> -->
					<ul class="products">
				    <?php
				        $args = array( 'post_type' => 'product', 'posts_per_page' => '-1', 'product_cat' => 'add-ons', 'orderby' => 'date' );
				        $loop = new WP_Query( $args );
				        while ( $loop->have_posts() ) : $loop->the_post(); global $product; ?>				            
				                <li class="product">    
				                    <a href="<?php echo get_permalink( $loop->post->ID ) ?>" title="<?php echo esc_attr($loop->post->post_title ? $loop->post->post_title : $loop->post->ID); ?>" class="product-item">
				                        <?php //woocommerce_show_product_sale_flash( $loop->post, $product ); ?>			                      
										<?php /*
							                if ( has_post_thumbnail( $loop->post->ID ) ) 
							                    echo get_the_post_thumbnail( $loop->post->ID, 'shop_catalog' ); 
							                else 
							                    echo '<img src="' . wc_placeholder_img_src() . '" alt="Placeholder" width="65px" height="115px" />'; 
							                 */
							            ?>
				                        <h3><?php the_title(); ?></h3>
				                        <p><?php //the_content(); ?></p>									
				                        <span class="price"><?php echo $product->get_price_html(); ?></span>                    

				                    </a>

				                    <?php woocommerce_template_loop_add_to_cart( $loop->post, $product ); ?>

				                </li>

				    <?php endwhile; ?>
				    <?php wp_reset_query(); ?>
				</ul><!--/.products-->
				</div>
			</div>

			<label class="nav" for="addon">
				<span>
					<svg height="26" viewBox="0 0 600 600" width="26" xmlns="http://www.w3.org/2000/svg" stroke="#29c1ec" stroke-width="50" fill="none" stroke-linecap="round" stroke-linejoin="round"  >
						<path d="m437.019531 74.980469c-48.351562-48.351563-112.640625-74.980469-181.019531-74.980469s-132.667969 26.628906-181.019531 74.980469c-48.351563 48.351562-74.980469 112.640625-74.980469 181.019531s26.628906 132.667969 74.980469 181.019531c48.351562 48.351563 112.640625 74.980469 181.019531 74.980469s132.667969-26.628906 181.019531-74.980469c48.351563-48.351562 74.980469-112.640625 74.980469-181.019531s-26.628906-132.667969-74.980469-181.019531zm-181.019531 397.019531c-119.101562 0-216-96.898438-216-216s96.898438-216 216-216 216 96.898438 216 216-96.898438 216-216 216zm20-236.019531h90v40h-90v90h-40v-90h-90v-40h90v-90h40zm0 0"/></svg>

					<!-- <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12" y2="17"></line></svg> -->
					Add Ons
				</span>
			</label>
		</div>


				<?php
				//if ( woocommerce_product_loop() ) {

					/**
					 * Hook: woocommerce_before_shop_loop.
					 *
					 * @hooked woocommerce_output_all_notices - 10
					 * @hooked woocommerce_result_count - 20
					 * @hooked woocommerce_catalog_ordering - 30
					 */
				//	do_action( 'woocommerce_before_shop_loop' );

				//	woocommerce_product_loop_start();

				//	if ( wc_get_loop_prop( 'total' ) ) {
				//		while ( have_posts() ) {
				//			the_post();

							/**
							 * Hook: woocommerce_shop_loop.
							 *
							 * @hooked WC_Structured_Data::generate_product_data() - 10
							 */
				//			do_action( 'woocommerce_shop_loop' );

				//			wc_get_template_part( 'content', 'product' );
				//		}
				//	}

				//	woocommerce_product_loop_end();

					/**
					 * Hook: woocommerce_after_shop_loop.
					 *
					 * @hooked woocommerce_pagination - 10
					 */
				//	do_action( 'woocommerce_after_shop_loop' );
				//} else {
					/**
					 * Hook: woocommerce_no_products_found.
					 *
					 * @hooked wc_no_products_found - 10
					 */
				//	do_action( 'woocommerce_no_products_found' );
			//	}

				/**
				 * Hook: woocommerce_after_main_content.
				 *
				 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
				 */
				do_action( 'woocommerce_after_main_content' );?>
			</div>
		
		</div>
	</main>
</div>

<?php
get_footer( 'shop' );