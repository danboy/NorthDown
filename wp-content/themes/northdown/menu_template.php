<?php /* Template Name: Lunch Menu
 */
/**
 * @package WordPress
 * @subpackage NorthDown
 */

get_header(); ?>

		<div id="primary">
      <div id="content" role="main">
        <article>
          <h1><?php echo the_title(); ?></h1>
          <?php
            $args = array( 
              'tax_query' => array(
                array(
                  'taxonomy' => 'menu_types',
                  'field' => 'name',
                  'terms' => the_title('','',false)
                )
              )
            );

            $loop = new WP_Query( $args );

            while ( $loop->have_posts() ) : $loop->the_post();
            ?>
              <h2><?php the_title(); ?></h2>
            <?php
              echo '<div class="entry-content">';
              the_content();
              echo '</div>';
            endwhile;
          ?>
        </article>
			</div><!-- #content -->
		</div><!-- #primary -->
<?php get_footer(); ?>

