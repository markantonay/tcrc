<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package The Computer Repair
 */

get_header(); ?>

<?php do_action( 'the_computer_repair_page_top' ); ?>

<main id="maincontent" role="main"> 
    <div class="middle-align container">
        <div class="col-lg-12">
            <?php while ( have_posts() ) : the_post();

                get_template_part( 'template-parts/content-page'); 

            endwhile; ?>
        </div>

      
    </div>
</main>

<?php do_action( 'the_computer_repair_page_bottom' ); ?>

<?php get_footer(); ?>