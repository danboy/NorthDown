<?php
/**
 * @package OpenMenu
 * @version 1.3.2
 */
/*

Copyright 2010, 2011  OpenMenu, LLC

*/

// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// ** Widgets:
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	/* Add our function to the widgets_init hook. */
	add_action('widgets_init', create_function('', 'return register_widget("openmenu_restaurant_location");'));
	add_action('widgets_init', create_function('', 'return register_widget("openmenu_specials");'));
	add_action('widgets_init', create_function('', 'return register_widget("openmenu_tagcloud");'));
	add_action('widgets_init', create_function('', 'return register_widget("openmenu_menu");'));

	class openmenu_menu extends WP_Widget {  
		function openmenu_menu() {  
			/* Widget settings. */
			$widget_ops = array( 'classname' => 'om-menu', 'description' => __('Display a list of Menus and their Menu Groups') );

			/* Widget control settings. */
			$control_ops = array( 'width' => 400, 'height' => 350, 'id_base' => 'om-menu' );

		    parent::WP_Widget('om-menu', 'Open Menu: Menu Listing', $widget_ops, $control_ops );
		}
		
		function form($instance) {  
		     // outputs the options form on admin
		     
			/* Set up some default widget settings. */ 
			$defaults = array( 
							'title' => 'Our Menu', 
							'omf_url' => 'http://', 
							'display_menugroups' => true,
							'menu_filter' => '', 
						);
			$instance = wp_parse_args( (array) $instance, $defaults ); ?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'omf_url' ); ?>"><?php _e('Location of the Open Menu Format menu (URL)'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'omf_url' ); ?>" name="<?php echo $this->get_field_name( 'omf_url' ); ?>" value="<?php echo $instance['omf_url']; ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'menu_filter' ); ?>"><?php _e('Menu Filter'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'menu_filter' ); ?>" name="<?php echo $this->get_field_name( 'menu_filter' ); ?>" value="<?php echo $instance['menu_filter']; ?>" />
			</p>
			<p>
				<input class="checkbox" type="checkbox" <?php checked($instance['display_menugroups'], true) ?> id="<?php echo $this->get_field_id('display_menugroups'); ?>" name="<?php echo $this->get_field_name('display_menugroups'); ?>" />
				<label for="<?php echo $this->get_field_id('display_menugroups'); ?>"><?php _e('Display Menu Groups'); ?></label><br />
			</p>
		<?php
		}
		
		function update($new_instance, $old_instance) {  
		     // processes widget options to be saved  
			$instance = $old_instance;

			/* Strip tags (if needed) and update the widget settings. */
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['omf_url'] = $new_instance['omf_url'];
			$instance['menu_filter'] = strip_tags($new_instance['menu_filter']);
			$instance['display_menugroups'] = isset($new_instance['display_menugroups']) ? 1 : 0 ;
			
			return $instance;
		}
		
		function widget($args, $instance) {  
			extract( $args );

			/* User-selected settings. */
			$title = apply_filters('widget_title', $instance['title'] );
			$omf_url = isset( $instance['omf_url'] ) ? $instance['omf_url'] : false;
			$menu_filter = isset( $instance['menu_filter'] ) ? $instance['menu_filter'] : false;
			$display_menugroups = isset( $instance['display_menugroups'] ) ? $instance['display_menugroups'] : false;
			
			/* Before widget (defined by themes). */
			echo $before_widget;

			/* Title of widget (before and after defined by themes). */
			if ( $title )
				echo $before_title . $title . $after_title;
			
			if ( $omf_url ) {
				$omf_details = _get_menu_details($omf_url);

				echo _get_menus_and_groups( $omf_details, $menu_filter, $display_menugroups);

				unset($omf_details);
				
			}

			/* After widget (defined by themes). */
			echo $after_widget;
		}  
	}
	
	class openmenu_tagcloud extends WP_Widget {  
		function openmenu_tagcloud() {  
			/* Widget settings. */
			$widget_ops = array( 'classname' => 'om-tagcloud', 'description' => __('Display a tag cloud for the cusines types') );

			/* Widget control settings. */
			// $control_ops = array( 'width' => 400, 'height' => 350, 'id_base' => 'om-tagcloud' );
			$control_ops = array( 'id_base' => 'om-tagcloud' );
			
		    parent::WP_Widget('om-tagcloud', 'Open Menu: Tag Cloud', $widget_ops, $control_ops );
		}
		
		function form($instance) {  
		     // outputs the options form on admin
		     
			/* Set up some default widget settings. */
			$defaults = array( 'title' => 'Cuisine Types' );
			$instance = wp_parse_args( (array) $instance, $defaults ); ?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
			</p>
		<?php
		}
		
		function update($new_instance, $old_instance) {  
		     // processes widget options to be saved  
			$instance = $old_instance;

			/* Strip tags (if needed) and update the widget settings. */
			$instance['title'] = strip_tags( $new_instance['title'] );

			return $instance;
		}
		
		function widget($args, $instance) {  
			extract( $args );

			/* User-selected settings. */
			$title = apply_filters('widget_title', $instance['title'] );

			/* Before widget (defined by themes). */
			echo $before_widget;

			/* Title of widget (before and after defined by themes). */
			if ( $title )
				echo $before_title . $title . $after_title;
			
			// tag cloud
			$args = array(
			    'smallest'  => 8, 
			    'largest'   => 22,
			    'unit'      => 'pt', 
			    'number'    => 0,  				// Number of tags to display (0 = all)
			    'format'    => 'flat',
			    'separator' => ' ',
			    'orderby'   => 'name', 
			    'order'     => 'ASC',
			    'link'      => 'view', 
			    'taxonomy'  => 'cuisine_type',
			    'echo'      => true );
			
			echo '<div style="margin-top:10px">';
			wp_tag_cloud( $args );
			echo '</div>';
			
			/* After widget (defined by themes). */
			echo $after_widget;
		}  
	}
	
	class openmenu_specials extends WP_Widget {  
		function openmenu_specials() {  
			/* Widget settings. */
			$widget_ops = array( 'classname' => 'om-specials', 'description' => __('Display a list of specials as defined in an Open Menu Format menu') );

			/* Widget control settings. */
			$control_ops = array( 'width' => 400, 'height' => 350, 'id_base' => 'om-specials' );

		    parent::WP_Widget('om-specials', 'Open Menu: Specials', $widget_ops, $control_ops );
		}
		
		function form($instance) {  
		     // outputs the options form on admin
		     
			/* Set up some default widget settings. */
			$defaults = array( 
							'title' => 'Our Specials', 
							'omf_url' => 'http://', 
							'menu_filter' => '', 
						);
			$instance = wp_parse_args( (array) $instance, $defaults ); ?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'omf_url' ); ?>"><?php _e('Location of the Open Menu Format menu (URL)'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'omf_url' ); ?>" name="<?php echo $this->get_field_name( 'omf_url' ); ?>" value="<?php echo $instance['omf_url']; ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'menu_filter' ); ?>"><?php _e('Filter - Menu Name to display specials from'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'menu_filter' ); ?>" name="<?php echo $this->get_field_name( 'menu_filter' ); ?>" value="<?php echo $instance['menu_filter']; ?>" />
			</p>
		<?php
		}
		
		function update($new_instance, $old_instance) {  
		     // processes widget options to be saved  
			$instance = $old_instance;

			/* Strip tags (if needed) and update the widget settings. */
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['omf_url'] = $new_instance['omf_url'];
			$instance['menu_filter'] = $new_instance['menu_filter'];
			
			return $instance;
		}
		
		function widget($args, $instance) {  
			extract( $args );

			/* User-selected settings. */
			$title = apply_filters('widget_title', $instance['title'] );
			$omf_url = isset( $instance['omf_url'] ) ? $instance['omf_url'] : false;
			$menu_filter = isset( $instance['menu_filter'] ) ? $instance['menu_filter'] : false;
			
			/* Before widget (defined by themes). */
			echo $before_widget;

			/* Title of widget (before and after defined by themes). */
			if ( $title )
				echo $before_title . $title . $after_title;
			
			if ( $omf_url ) {
				$omf_details = _get_menu_details($omf_url);
				
				echo _get_menu_specials( $omf_details, $menu_filter );
				unset($omf_details);
				
			}

			/* After widget (defined by themes). */
			echo $after_widget;
		}  
	}
	
	class openmenu_restaurant_location extends WP_Widget {  
		function openmenu_restaurant_location() {  
			/* Widget settings. */
			$widget_ops = array( 'classname' => 'example', 'description' => __('Display a restaurant\'s location as defined in an Open Menu Format menu') );

			/* Widget control settings. */
			$control_ops = array( 'width' => 400, 'height' => 350, 'id_base' => 'om-restaurant-location' );

		    parent::WP_Widget('om-restaurant-location', 'Open Menu: Restaurant Location', $widget_ops, $control_ops );
		}
		
		function form($instance) {  
		     // outputs the options form on admin
		     
			/* Set up some default widget settings. */
			$defaults = array( 
							'title' => 'Our Location', 
							'omf_url' => 'http://',
							'include_hours' => true,
						);
			$instance = wp_parse_args( (array) $instance, $defaults ); ?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'omf_url' ); ?>"><?php _e('Location of the Open Menu Format menu (URL)'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'omf_url' ); ?>" name="<?php echo $this->get_field_name( 'omf_url' ); ?>" value="<?php echo $instance['omf_url']; ?>" />
			</p>
			<p>
				<input class="checkbox" type="checkbox" <?php checked($instance['include_hours'], true) ?> id="<?php echo $this->get_field_id('include_hours'); ?>" name="<?php echo $this->get_field_name('include_hours'); ?>" />
				<label for="<?php echo $this->get_field_id('include_hours'); ?>"><?php _e('Include hours'); ?></label><br />
			</p>
		<?php
		}
		
		function update($new_instance, $old_instance) {  
		     // processes widget options to be saved  
			$instance = $old_instance;

			/* Strip tags (if needed) and update the widget settings. */
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['omf_url'] = $new_instance['omf_url'];
			$instance['include_hours'] = isset($new_instance['include_hours']) ? 1 : 0 ;
			
			return $instance;
		}
		
		function widget($args, $instance) {  
			extract( $args );

			/* User-selected settings. */
			$title = apply_filters('widget_title', $instance['title'] );
			$omf_url = isset( $instance['omf_url'] ) ? $instance['omf_url'] : false;
			$include_hours = isset( $instance['include_hours'] ) ? $instance['include_hours'] : false;
			
			/* Before widget (defined by themes). */
			echo $before_widget;

			/* Title of widget (before and after defined by themes). */
			if ( $title )
				echo $before_title . $title . $after_title;
			
			// Get the Open Menu Format details
			if ( $omf_url ) {
				$omf_details = _get_menu_details($omf_url);

		        echo _get_restaurant_location($omf_details, $include_hours);
				unset($omf_details);

			}

			/* After widget (defined by themes). */
			echo $after_widget;
		}  
	}
	
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// ** Functions for non-widget users:
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	// Restaurant Location
	function openmenu_location( $post_id, $title = false ) {
		// ------------------------------------- 
		//  Return a box (widget) of a restaurants locations
		// ------------------------------------- 
		
		if ( empty($post_id) ) {
			return;
		}
		
		$title = ( !empty($title) ) ? $title : __('Our Location') ;
		
		$custom = get_post_custom( $post_id );
		$omf_url = $custom["_omf_url"][0];
		$omf_details = _get_menu_details($omf_url);
		
		?>
		<style type="text/css">
			.om_header { font-weight:bold;font-size:1.2em }
		</style>
		<div class="om_block">
			<div class="om_header"><?php echo $title; ?></div>

<?php 
	if ( empty($omf_details) ) {
		echo '<p>information not available</p>';
	} else {
        echo _get_restaurant_location($omf_details);
        unset($omf_details);
	} 
?>
	   </div>
<?php
	}

	// Specials
	function openmenu_specials( $post_id, $title = 'Our Specials' ) {
		// ------------------------------------- 
		//  Return a box (widget) of specials
		// ------------------------------------- 
		
		if ( empty($post_id) ) {
			return;
		}
		
		$title = ( !empty($title) ) ? $title : __('Our Specials') ;
		
		$custom = get_post_custom( $post_id );
		$omf_url = $custom["_omf_url"][0];
		$omf_details = _get_menu_details($omf_url);
			
		?>
		<style type="text/css">
			.om_header { font-weight:bold;font-size:1.2em }
		</style>
		<div class="om_block">
			<div class="om_header"><?php echo $title; ?></div>

<?php 
	if ( empty($omf_details) ) {
		echo '<p>information not available</p>';
	} else {
		echo _get_menu_specials( $omf_details );
		unset($omf_details);
	} 
?>
	   </div>
<?php
	}

// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// ** Private functions:
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

	function _get_restaurant_location ( $omf_details, $include_hours = true ) {
		// ------------------------------------- 
		//  Return a a restaurants address
		// ------------------------------------- 
		$location = '';
		
		if ( !empty($omf_details) ) {
			$location .= '<div style="margin-top:5px;">';
			$location .= '<p><strong>Address:</strong><br />';
		    $location .= clean($omf_details['restaurant_info']['address_1']).'<br />';
		    $location .= clean($omf_details['restaurant_info']['city_town']).', '.
		    			$omf_details['restaurant_info']['country'].' '.
		    		    $omf_details['restaurant_info']['postal_code'].'<br />'.
		    	        '<strong>Phone: </strong> '.$omf_details['restaurant_info']['phone'];
		    $location .= '<br /></p>';
		    
		    if ($include_hours) {
			    $location .= '<p><strong>Our Hours:</strong><br />';
			
				foreach ($omf_details['operating_days']['printable'] AS $daytime) {
					$location .= $daytime.'<br />';
				}
				
				$location .= '</p>';
			}
			
			$location .= '</div>';
		}
		
		return $location;
	}

	function _get_menu_specials ( $omf_details, $menu_filter = false ) {
		// ------------------------------------- 
		//  Return a preformatted HTML list of specials
		// ------------------------------------- 
		
		$specials = '';
		if ( isset($omf_details['menus']) ) {
			$specials .= '<div style="margin-top:5px;">';
			foreach ( $omf_details['menus'] AS $menu ) {
				if ( !$menu_filter || strcasecmp($menu_filter, $menu['menu_name']) == 0 ) {
					if ( isset($menu['menu_groups']) ) {
						foreach ($menu['menu_groups'] AS $group) {
							if ( isset($group['menu_items']) ) {
								foreach ($group['menu_items'] AS $item) {
									if ( $item['special'] ) {
										$price = ( !empty($item['menu_item_price']) ) ? ' - $'.number_format($item['menu_item_price'], 2) : '' ;
										$specials .= '<p><strong>'.$item['menu_item_name'].
											$price.'</strong> ';
										$specials .= '<br />'.$item['menu_item_description'];
										$specials .= '</p>';
									}
								}
							}
						}
					}
				}
			}
			$specials .= '</div>';
		}
		return $specials;
	}

	function _get_menus_and_groups ( $omf_details, $menu_filter = false, $include_groups = false ) {
		// ------------------------------------- 
		//  Return a preformatted HTML list of Menus and Menu Groups
		// ------------------------------------- 
		
		$menus = '';
		if ( isset($omf_details['menus']) ) {
			$menus .= '<div style="margin-top:5px;">';
			foreach ( $omf_details['menus'] AS $menu ) {
				if ( !$menu_filter || strcasecmp($menu_filter, $menu['menu_name']) == 0 ) {
					
					$menus .= '<strong>'.$menu['menu_name'].'</strong>';
					
					if ( $include_groups && isset($menu['menu_groups']) ) {
						$menus .= '<ul>';
						foreach ($menu['menu_groups'] AS $group) {
							$menus .= '<li>'.$group['group_name'].'</li>';
						}
						$menus .= '</ul>';
					}
					$menus .= '<br />';
				}
			}
			$menus .= '</div>';
		}
		return $menus;
	}
?>