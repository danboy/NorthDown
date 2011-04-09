<?php /* Template Name: Menus
 */
/**
 * @package WordPress
 * @subpackage NorthDown
 */

get_header(); ?>

    <?php
      $categories = get_terms('menu_categories');
      $menu_type = the_title('','',false);
    ?>
		<div id="primary">
      <div id="content" class="page" role="main">
        <article>
          <header>
            <h1><?php echo the_title(); ?></h1>
          </header>
          <?php
            foreach ($categories as $menu_category){
              $args = array( 
                'tax_query' => array(
                  array(
                    'taxonomy' => 'menu_types',
                    'field' => 'name',
                    'terms' => $menu_type
                  ),
                  array(
                    'taxonomy' => 'menu_categories',
                    'field' => 'slug',
                    'terms' => $menu_category->slug
                  )
                )
              );
              $loop = new WP_Query( $args );
              if ($loop->post_count){
                echo '<h2>';
                  echo $menu_category->name;
                echo '</h2>';
              while ( $loop->have_posts() ) : $loop->the_post();?>
                <h3><?php the_title(); ?></h3>
                <?php
                echo '<div class="entry-content">';
                  the_content();
                echo '</div>';
              endwhile;
              };
            };
            ?>
        </article>
			</div><!-- #content -->
		</div><!-- #primary -->
<?php get_footer(); ?>

