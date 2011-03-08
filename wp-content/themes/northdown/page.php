<?php
/**
 * @package WordPress
 * @subpackage NorthDown
 */

get_header(); ?>

		<div id="primary">
			<div id="content" class="page" role="main">

				<?php the_post(); ?>

				<?php get_template_part( 'content', 'page' ); ?>

			</div><!-- #content -->
		</div><!-- #primary -->

<?php get_footer(); ?>
