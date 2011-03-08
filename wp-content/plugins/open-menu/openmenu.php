<?php
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// ** OpenMenu Plugin, Copyright 2011, 2011  OpenMenu, LLC
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

/**
	@package OpenMenu
	@version 1.3.2

	Plugin Name: OpenMenu
	Plugin URI: http://openmenu.com/wordpress-plugin.php
	Description: This plugin allows you to easily create posts that are based on your OpenMenu Format menu.  This plugin fully integrates an OpenMenu Format menu or menus into an existing theme.  Widget / Menu ready themes work best.
	Author: OpenMenu, LLC
	Version: 1.3.2
	Author URI: http://openmenu.com

	*Icon designed by Ben Dunkle, core designer for Wordpress.org. 
	*	Website: http://field2.com
	*	Contact ben@field2.com

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; version 3 of the License.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// ** Preload & Setup:
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	/** Install Folder */
	define('OPENMENU_FOLDER', '/' . dirname( plugin_basename(__FILE__)));
	
	/** Path for Includes */
	define('OPENMENU_PATH', WP_PLUGIN_DIR . OPENMENU_FOLDER);

	/** Path for front-end links */
	define('OPENMENU_URL', WP_PLUGIN_URL . OPENMENU_FOLDER);

	/** Directory path for includes of template files  */
	define('OPENMENU_TEMPLATES_PATH', WP_PLUGIN_DIR . OPENMENU_FOLDER. '/templates');
	define('OPENMENU_TEMPLATES_URL', WP_PLUGIN_URL . OPENMENU_FOLDER . '/templates');
	
	// Post type
	define('OPENMENU_POSTYPE', 'openmenu');
	define('OPENMENU_SLUG', 'openmenu');
	
	// Make sure we don't expose any info if called directly
	if ( !function_exists( 'add_action' ) ) {
		echo "OpenMenu Plugin - http://OpenMenu.com ...";
		exit;
	}
	
	// Include widgets module
	include OPENMENU_PATH . '/widgets.php';
	
	// Register the style so its available site-wide
	wp_register_style('OpenMenu-Template-Default', OPENMENU_TEMPLATES_URL. '/default/styles/style.css');
	wp_enqueue_style( 'OpenMenu-Template-Default');

	// Override jquery
	//wp_deregister_script('jquery');
	//wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js', false, '1.4.2');
	//wp_enqueue_script('jquery');

// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// ** Activation hook for flushing 
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	
	register_activation_hook( __FILE__, 'openmenu_activate' );
	
	function openmenu_activate() { 
		// ------------------------------------- 
		//  Perform stuff on activation
		// ------------------------------------- 
		
		// plugin uses WP Rewrite, need to flush rules so they get added properly
		flush_rewrite_rules();
	}
	
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// ** Add Shortcode [openmenu parameter="value"]
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	
	add_shortcode('openmenu', 'openmenu_shortcode');
	
	function openmenu_shortcode($atts, $content = null) { 
		// ------------------------------------- 
		//  Create / Handle the openmenu shortcode
		// ------------------------------------- 
		
		// Get the OpenMenu Options
		$options = get_option( 'openmenu_options' );
		$display_columns = ( $options['display_columns'] == 'Two' ) ? '2' : '1' ;
		$display_type = ( isset($options['display_type']) ) ? $options['display_type'] : 'Menu' ;
		
		$atts = shortcode_atts(array(
			'omf_url' => '',
			'menu_filter' => '',
			'group_filter' => '',
			'display_columns' => $display_columns,
			'display_type' => $display_type
		), $atts);
		
		$display = '';
		if ( !empty($atts['omf_url']) ) {
			// Get the menu
			$omf_details = _get_menu_details( $atts['omf_url'] ); 

			if ( strcasecmp($atts['display_type'], 'restaurant information / menu') == 0 || 
	 strcasecmp($atts['display_type'], 'menu') == 0 ) {
				$display .= build_menu_from_details($omf_details, $atts['display_columns'], $atts['menu_filter'], $atts['group_filter']);
			}
			
		} else {
			$display = __('OpenMenu Format URL must be provided');
		}
		
		return $display;
	}

// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// ** Add Settings link to OpenMenu on the plugin page [REMOVED]
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

//	add_filter('plugin_action_links', 'add_settings_link', 10, 2 );

//	function add_settings_link($links, $file) {
//		static $this_plugin;
//		if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);

//		if ($file == $this_plugin){
//			$settings_link = '<a href="'.admin_url( 'options-general.php?page='. OPENMENU_POSTYPE . '/openmenu.php' ) . '">'.__("Settings", "openmenu-settings").'</a>';
//			array_unshift($links, $settings_link);
//		}
//		return $links;
//	}

// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// ** Determines if OpenMenu posts are shown on the homepage 
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	
	//if( is_home() ){
	//	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	//	query_posts( array('post_type'=>array( 'post', 'linkpost'),'paged'=>$paged ) );
	//}

	
	add_filter( 'pre_get_posts', 'my_get_posts' );
	 
	function my_get_posts( $query ) {
		$options = get_option('openmenu_options');
		if ( isset($options['show_posts_homepage']) && $options['show_posts_homepage'] ) {
			if ( is_home() )
				$query->set( 'post_type', array( 'post', OPENMENU_POSTYPE ) );
		}
		return $query;
	}

// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// ** Update RSS Feed to include custom post type: 
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

	add_filter('request', 'myfeed_request');

	function myfeed_request($qv) { 
		if (isset($qv['feed'])) {
			$qv['post_type'] = get_post_types();
		}
		return $qv; 
	} 

// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// ** Rewrite rules:
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

	add_filter( 'generate_rewrite_rules', 'add_rewrite_rules' );

	function add_rewrite_rules( $wp_rewrite ) {
		$new_rules = array();
		$new_rules[OPENMENU_SLUG . '/page/?([0-9]{1,})/?$'] = 'index.php?post_type=' . OPENMENU_POSTYPE . '&paged=' . $wp_rewrite->preg_index(1);
		$new_rules[OPENMENU_SLUG . '/(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?post_type=' . OPENMENU_POSTYPE . '&feed=' . $wp_rewrite->preg_index(1);
		$new_rules[OPENMENU_SLUG . '/?$'] = 'index.php?post_type=' . OPENMENU_POSTYPE;

		$wp_rewrite->rules = array_merge($new_rules, $wp_rewrite->rules);
		return $wp_rewrite;
	}

// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// ** Custom Post Template:
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

	add_action("template_redirect", 'openmenu_template_redirect');

	function openmenu_template_redirect() { 

		global $wp;
		global $wp_query;
		
		if (isset($wp->query_vars["post_type"]) && $wp->query_vars["post_type"] == OPENMENU_POSTYPE) { 
			// Default
			if ( is_robots() || is_feed() || is_trackback() ) { 
				return;
			}

			if ( isset($wp->query_vars["name"]) && $wp->query_vars["name"] ) {
				include(OPENMENU_TEMPLATES_PATH . '/default/single-openmenu.php');
				die();
			} else {
				include(OPENMENU_TEMPLATES_PATH . '/default/openmenu.php');
				die();
			}
			$wp_query->is_404 = true;
			
		}
	}

// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// ** Custom Post Type:
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	add_action( 'init', 'create_post_type' );
	add_action( 'admin_head', 'my_custom_posttype_icon' );
	
	function create_post_type() {
		// ------------------------------------- 
		//  Register a custom post type
		//   custom post type = openmenu
		// ------------------------------------- 
		
		// Define the labels
		$labels = array(
			'name' => _x('OpenMenu', 'post type general name'),
			'singular_name' => _x('OpenMenu', 'post type singular name'),
			'add_new' => _x('Add New Menu', 'restaurant menu'),
			'add_new_item' => __('Add New Menu'),
			'edit_item' => __('Edit Menu'),
			'new_item' => __('New Menu'),
			'view_item' => __('View Menu'),
			'search_items' => __('Search Menus'),
			'not_found' =>  __('No menus found'),
			'not_found_in_trash' => __('No menus found in Trash'),
			'parent_item_colon' => ''
		);
		
		// Register the openmenu post type
		register_post_type(OPENMENU_POSTYPE, array(
			'labels' => $labels,
			'public' => true,
			'show_ui' => true,
			'show_in_nav_menus' => true, 
			'capability_type' => 'post',
			'hierarchical' => false,     // If true acts like a page
			'rewrite' => array('slug' => OPENMENU_SLUG),
			'query_var' => true,
			'menu_position' => '5',
			'menu_icon' => OPENMENU_URL .'/images/openmenu-16-color.png', 
			'register_meta_box_cb' => 'sp_add_custom_box',
			'supports' => array(
				'title',
			//	'excerpt',
				'editor')
		));

		// ------------------------------------- 
		//  Register custom for taxonomies OpenMenu
		// ------------------------------------- 
		// Cuisine Types
	    register_taxonomy( 'cuisine_type', OPENMENU_POSTYPE,
			array(
				 'hierarchical' => true,   // false acts like tags
				 'public' => true,
				 'labels' => array(
						'name' => __( 'Cuisine Types' ),
						'singular_name' => __( 'Cuisine Type' )
					),
				 'query_var' => 'cuisine_type',
				 'rewrite' => array('slug' => 'cuisine_type' )
			)
		);
		
	}

	function my_custom_posttype_icon() {
		
		global $post_type;
		$qry_postype = ( isset($_GET['post_type']) ) ? $_GET['post_type'] : '' ; 
		
		if (($qry_postype == OPENMENU_POSTYPE) || ($post_type == OPENMENU_POSTYPE)) {
		    $icon_url = OPENMENU_URL . '/images/openmenu-32.png';
		    ?>
		    <style type="text/css" media="all">
		    /*<![CDATA[*/
		        .icon32 {
		            background: url(<?php echo $icon_url; ?>) no-repeat 1px !important;
		        }
		    /*]]>*/
		    </style>
		    <?php
		}
	}
	
		// Some default values
		//$default_values = array('American', 'Afghan', 'African', 'Argentinean', 'Asian/Oriental', 'Bakery', 'Barbeque', 'Brazilian', 'Brew/Pubs/Microbrewery', 'Cajun/Creole', 'California', 'Caribbean', 'Chinese', 'Coffee House', 'Continental', 'Cuban', 'Desserts', 'Diner', 'Ethiopian', 'Family/Homestyle', 'French/French Bistro', 'Fusion', 'German', 'Greek', 'Hamburger', 'Hawaiian', 'Hot Dog', 'Indian', 'International', 'Irish', 'Italian', 'Japanese', 'Latin', 'Kosher', 'Malaysian', 'Mediterranean', 'Mexican', 'Moroccan', 'Pacific Rim', 'Pizza', 'Portuguese', 'Russian', 'Sandwiches', 'Seafood', 'Soup', 'Southwest', 'Southern Cuisine', 'Spanish', 'Steakhouse', 'Sunday Brunch', 'Sushi', 'Tapas', 'Thai', 'Vegetarian', 'Vietnamese');
		//foreach ($default_values AS $cuisine) {
		//	wp_insert_term($cuisine, 'cuisine_type',
		//	  array(
		//	    'slug' => str_replace(array(' ', '/'), array('_', '_'), strtolower($cuisine))
		//	  )
		//	);
		//}

// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// ** Custom Filter in Edit Post by Taxonomy:
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

	add_action('restrict_manage_posts','restrict_listings_by_cuisine_type');
	
	function restrict_listings_by_cuisine_type() {
	    global $typenow;
	    global $wp_query;
	    if ($typenow == OPENMENU_POSTYPE) {
	        $taxonomy = 'cuisine_type';
	        $cuisine_type_taxonomy = get_taxonomy($taxonomy);
	        $selected = (isset($wp_query->query['term'])) ? $wp_query->query['term'] : '' ;
	        wp_dropdown_categories(array(
	            'show_option_all' =>  __("Show All Cuisine Types {$cuisine_type_taxonomy->label}"),
	            'taxonomy'        =>  $taxonomy,
	            'name'            =>  'cuisine_type',
	            'orderby'         =>  'name',
	            'selected'        =>  $selected,
	            'hierarchical'    =>  true,
	            'depth'           =>  3,
	            'show_count'      =>  true, // Show # listings in parens
	            'hide_empty'      =>  true, // Don't show businesses w/o listings
	        ));
	    }
	}

	add_filter('parse_query','convert_cuisine_type_id_to_taxonomy_term_in_query');
	
	function convert_cuisine_type_id_to_taxonomy_term_in_query($query) {
	    global $pagenow;
	    $qv = &$query->query_vars;
	    if ($pagenow=='edit.php' &&
	            isset($qv['taxonomy']) && $qv['taxonomy']=='cuisine_type' &&
	            isset($qv['term']) && is_numeric($qv['term'])) {
	        $term = get_term_by('id',$qv['term'],'cuisine_type');
	        $qv['term'] = $term->slug;
	    }
	}
	
	// TODO: Add Default Cusine list based on current specification

// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// ** OpenMenu Settings:
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

	register_activation_hook(__FILE__, 'add_defaults_fn');
	add_action('admin_init', 'openmenu_options_init_fn' );
	add_action('admin_menu', 'openmenu_options_add_page_fn');

	// Define default option settings
	function add_defaults_fn() {
		$tmp = get_option('openmenu_options');
	    if( !is_array($tmp) ) {
			$arr = array("display_type"=>"Menu", "hide_sidebar" => "on", "display_columns" => "One");
			update_option('openmenu_options', $arr);
		}
	}

	// Register our settings. Add the settings section, and settings fields
	function openmenu_options_init_fn(){
		register_setting('openmenu_options', 'openmenu_options', 'openmenu_options_validate' );
		
		add_settings_section('lookfeel_section', __('Look &amp Feel'), 'section_lookfeel_fn', __FILE__);

		add_settings_field('drop_down1', __('Display Type'), 'setting_displaytype_fn', __FILE__, 'lookfeel_section');
		add_settings_field('radio_buttons', __('How many columns?'), 'setting_displaycolumn_fn', __FILE__, 'lookfeel_section');
		add_settings_field('drop_down2', __('Theme'), 'setting_theme_fn', __FILE__, 'lookfeel_section');
		
		add_settings_section('om_section', __('OpenMenu Listing'), 'section_om_fn', __FILE__);
		add_settings_field('plugin_om_title', __('Title'), 'setting_om_title_fn', __FILE__, 'om_section');
		add_settings_field('plugin_om_description', __('Description'), 'setting_om_description_fn', __FILE__, 'om_section');
		
		add_settings_section('wordpress_section', __('Wordpress Theme'), 'section_wordpress_fn', __FILE__);
		add_settings_field('plugin_chk1', __('Show Posts on Homepage'), 'setting_showposts_fn', __FILE__, 'wordpress_section');
		add_settings_field('plugin_chk2', __('Hide Sidebar'), 'setting_hidesidebar_fn', __FILE__, 'wordpress_section');
		add_settings_field('plugin_text_string', __('Width Override'), 'setting_widthoverride_fn', __FILE__, 'wordpress_section');
		add_settings_field('plugin_backcolor', __('Menu Background Color'), 'setting_backgroundcolor_fn', __FILE__, 'wordpress_section');
		
	}

	// Add sub page to the Settings Menu
	function openmenu_options_add_page_fn() {
		add_options_page('OpenMenu Options', 'OpenMenu', 'manage_options', __FILE__, 'options_page_fn');
	}

	// *************************
	// Callback functions
	// *************************

	// Section HTML, displayed before the first option
	function  section_lookfeel_fn() {
		echo '<p>'.__('Control what is displayed and how it is displayed').'</p>';
	}
	function  section_wordpress_fn() {
		echo '<p>'.__('Changes how the menu interacts with the current theme').'</p>';
	}
	function  section_om_fn() {
		echo '<p>'.__('Controls the main OpenMenu page (used to display a list of all menus in the system)').'</p>';
	}
	
	// DROP-DOWN-BOX - Name: plugin_options[display_type]
	function  setting_displaytype_fn() {
		$options = get_option('openmenu_options');
		$items = array("Menu", "Restaurant Information", "Restaurant Information / Menu");
		echo "<select id='drop_down1' name='openmenu_options[display_type]'>";
		foreach($items as $item) {
			$selected = ($options['display_type']==$item) ? 'selected="selected"' : '';
			echo "<option value='$item' $selected>$item</option>";
		}
		echo "</select>";
	}

	// DROP-DOWN-BOX - Name: plugin_options[theme]
	function  setting_theme_fn() {
		$options = get_option('openmenu_options');
		$items = array("(default)");
		echo "<select id='drop_down2' name='openmenu_options[theme]'>";
		foreach($items as $item) {
			$selected = ($options['theme']==$item) ? 'selected="selected"' : '';
			echo "<option value='$item' $selected>$item</option>";
		}
		echo "</select>";
	}
	
	// RADIO-BUTTON - Name: plugin_options[display_columns]
	function setting_displaycolumn_fn() {
		$options = get_option('openmenu_options');
		$items = array("One", "Two");
		foreach($items as $item) {
			$checked = ($options['display_columns']==$item) ? ' checked="checked" ' : '';
			echo "<label><input ".$checked." value='$item' name='openmenu_options[display_columns]' type='radio' /> $item</label><br />";
		}
	}

	// Width override
	function setting_widthoverride_fn() {
		$options = get_option('openmenu_options');
		echo "<input id='plugin_text_string' name='openmenu_options[width_override]' size='10' type='text' value='{$options['width_override']}' /> ".__('(Used when hiding sidebar - add units: ex. 900px or 95%)');
	}

	// Background Color
	function setting_backgroundcolor_fn() {
		$options = get_option('openmenu_options');
		echo "<input id='plugin_text_string' name='openmenu_options[background_color]' size='10' type='text' value='{$options['background_color']}' /> ".__('(Background color - HTML color format: #ffffff)');
	}
	
	// OpenMenu title
	function setting_om_title_fn() {
		$options = get_option('openmenu_options');
		echo "<input id='plugin_text_string' name='openmenu_options[om_title]' size='20' type='text' value='{$options['om_title']}' /> ".__('(defaults to OpenMenu)');
	}

	// OpenMenu Description
	function setting_om_description_fn() {
		$options = get_option('openmenu_options');
		echo "<textarea id='plugin_textarea_string' name='openmenu_options[om_description]' rows='7' cols='50' type='textarea'>{$options['om_description']}</textarea>";
	}
	
	// PASSWORD-TEXTBOX - Name: plugin_options[pass_string]
	function setting_pass_fn() {
		$options = get_option('openmenu_options');
		echo "<input id='plugin_text_pass' name='openmenu_options[pass_string]' size='40' type='password' value='{$options['pass_string']}' />";
	}

	// CHECKBOX - Name: plugin_options[hide_sidebar]
	function setting_hidesidebar_fn() {
		$checked = '';
		$options = get_option('openmenu_options');
		if( isset($options['hide_sidebar']) ) { $checked = ' checked="checked" '; }
		echo "<input ".$checked." id='plugin_chk2' name='openmenu_options[hide_sidebar]' type='checkbox' />";
	}

	// CHECKBOX - Name: plugin_options[show_posts_homepage]
	function setting_showposts_fn() {
		$checked = '';
		$options = get_option('openmenu_options');
		if( isset($options['show_posts_homepage']) ) { $checked = ' checked="checked" '; }
		echo "<input ".$checked." id='plugin_chk1' name='openmenu_options[show_posts_homepage]' type='checkbox' />";
	}

	// Display the admin options page
	function options_page_fn() {
	?>
		<div class="wrap">
			<div class="icon32" id="icon-options-general"><br></div>
			<h2><?php _e('OpenMenu Options Page'); ?></h2>
			<?php _e('Control the overall look and feel for the menus displayed.'); ?>
			<form action="options.php" method="post">
			<?php settings_fields('openmenu_options'); ?>
			<?php do_settings_sections(__FILE__); ?>
			<p class="submit">
				<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
			</p>
			</form>
		</div>
	<?php
	}

	// Validate user data for some/all of your input fields
	function openmenu_options_validate($input) {
		// Check our textbox option field contains no HTML tags - if so strip them out
		//$input['text_string'] =  wp_filter_nohtml_kses($input['text_string']);	
		return $input; // return validated input
	}

// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// ** Menu Count for Dashboard:
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

	add_action('right_now_content_table_end', 'add_menu_counts');

	function add_menu_counts() {
		// ------------------------------------- 
		//  Add Menu Counts to Dashboard
		// ------------------------------------- 
		
        if (!post_type_exists(OPENMENU_POSTYPE)) {
             return;
        }

		// Get count
        $num_posts = wp_count_posts( OPENMENU_POSTYPE );
        $pending = $num_posts->pending;
        $drafts = $num_posts->draft;
        $publish = $num_posts->publish;

        // Handle Published
        $num = number_format_i18n( $publish );
        $text = _n( 'Menu', 'Menus', intval($publish) );
        if ( current_user_can( 'edit_posts' ) ) {
            $num = '<a href="edit.php?post_type=' . OPENMENU_POSTYPE . '">' . $num . '</a>';
            $text = '<a href="edit.php?post_type=' . OPENMENU_POSTYPE . '">' . $text . '</a>';
        }
        echo '<td class="first b b_pages">' . $num . '</td>';
        echo '<td class="t posts">' . $text . '</td>';

        echo '</tr>';
		
		// Handle Pending
        if ($pending > 0) {
            $num = number_format_i18n( $pending );
            $text = _n( 'Menu Pending', 'Menus Pending', intval($pending) );
            if ( current_user_can( 'edit_posts' ) ) {
                $num = '<a href="edit.php?post_status=pending&post_type=' . OPENMENU_POSTYPE . '">' . $num . '</a>';
                $text = '<a href="edit.php?post_status=pending&post_type=' . OPENMENU_POSTYPE . '">' . $text . '</a>';
            }
            echo '<td class="first b b-openmenu">' . $num . '</td>';
            echo '<td class="t openmenu">' . $text . '</td>';

            echo '</tr>';
        }
        
        // Handle Drafts
        if ($drafts > 0) {
            $num = number_format_i18n( $drafts );
            $text = _n( 'Menu Draft', 'Menu Drafts', intval($drafts) );
            if ( current_user_can( 'edit_posts' ) ) {
                $num = '<a href="edit.php?post_status=draft&post_type=' . OPENMENU_POSTYPE . '">'.$num.'</a>';
                $text = '<a href="edit.php?post_status=draft&post_type=' . OPENMENU_POSTYPE . '">'.$text.'</a>';
            }
            echo '<td class="first b b-openmenu">' . $num . '</td>';
            echo '<td class="t openmenu">' . $text . '</td>';

            echo '</tr>';
        }
	}
	
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// ** Custom Columns when viewing all menus:
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

	add_filter('manage_edit-openmenu_columns', 'add_new_openmenu_columns');
	add_action('manage_posts_custom_column', 'manage_openmenu_columns', 10, 2);
	
	function manage_openmenu_columns($column_name, $id) {
		// ------------------------------------- 
		//  Get the data for the custum columns
		// ------------------------------------- 
		
		global $wpdb;
		
		// Get custom data
		$custom = get_post_custom($id);
		$omf_url = (isset($custom["_omf_url"][0])) ? $custom["_omf_url"][0] : '' ;
		$restaurant_name = (isset($custom["_restaurant_name"][0])) ? $custom["_restaurant_name"][0] : '' ;
		$location = (isset($custom["_restaurant_location"][0])) ? $custom["_restaurant_location"][0] : '' ;
		
		// See which column we are getting data for
		switch ($column_name) {
			case 'id':
				echo $id;
				break;
			case 'omf_url':
				if ( !empty($omf_url) ) {
					echo '<a href="'.$omf_url.'" target="_blank">'.$omf_url.'</a>';
				}
				break;
			case 'restaurant_location':
				echo $restaurant_name.'<br />'.$location;
			    break;
			case 'cuisine_type':
				$tags = get_the_terms($id, 'cuisine_type'); //lang is the first custom taxonomy slug
				if ( !empty( $tags ) ) {
					$out = array();
					foreach ( $tags as $c ) {
						$out[] = "<a href='edit.php?post_type=" . OPENMENU_POSTYPE . "&cuisine_type=$c->slug'> " . esc_html(sanitize_term_field('name', $c->name, $c->term_id, 'cuisine_type', 'display')) . "</a>";
					}
					echo join( ', ', $out );
				} else {
					_e('No Cuisine Types.');
				}
			    break;
			default:
				break;
		} // end switch
	}
		
	function add_new_openmenu_columns($openmenu_columns) {
		// ------------------------------------- 
		//  Define the columns for the OpenMenu post type
		// ------------------------------------- 
		
		$new_columns['cb'] = '<input type="checkbox" />';
		// $new_columns['id'] = __('ID');
		$new_columns['title'] = _x('Title', 'column name');
		$new_columns['omf_url'] = __('Menu Location');
		$new_columns['restaurant_location'] = __('Restaurant / Location');
		$new_columns['cuisine_type'] = __('Cuisine Types');

		return $new_columns;
	}

// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// ** Custom Post Fields:
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

	add_action( 'save_post', 'sp_save_postdata', 1, 2 );

/*
	Each box has a name and a set of fields. Currently,
	only text and textarea fields are suppoted. 'text'
	fields are the default.
	To add a box named: "Name Box" with a field named
	"_name", add this:
	'Name Box' => array (
		array( '_name', 'Name:', 'text', 'bottom_label' ),
	),
	You can leave the 'text' field off. It is the default.
	'Name Box' => array (
		array( '_name', 'Name:' ),
	),
	
	// Displaying meta in a template
	<?php echo get_post_meta($post->ID, "_location", true); ?>
*/

	$sp_boxes = array (
		'OpenMenu Location (required)' => array (
			array( '_omf_url', 'Location of the OpenMenu Format menu (URL):', 'text', '(sample menu: http://openmenu.com/menu/sample)' )
		),
		'Menu Settings' => array (
			array( '_menu_filter', 'Menu Filter - Menu Name to display:', 'text', '(Use the <strong>Menu Name</strong> field to display that menu only)' ),
			array( '_group_filter', 'Menu Group Filter - Group Name to display:', 'text', '(Use the <strong>Group Name</strong> field to display that group only)' )
		),
		'Restaurant Information' => array (
			array( '_restaurant_name', 'Restaurant Name:', 'text', '' ),
			array( '_restaurant_location', 'Location (address):', 'text', '' ),
			array( '_brief_description', 'Brief Description:', 'textarea', '' ),
		)
	);

	// Adds a custom section to the "advanced" Post and Page edit screens
	function sp_add_custom_box() {
		global $sp_boxes;
		if ( function_exists( 'add_meta_box' ) ) {
			foreach ( array_keys( $sp_boxes ) as $box_name ) {
				add_meta_box( $box_name, __( $box_name, 'sp' ), 'sp_post_custom_box', OPENMENU_POSTYPE, 'normal', 'high' );
			}
		}
	}
	function sp_post_custom_box ( $obj, $box ) {
		global $sp_boxes;
		static $sp_nonce_flag = false;
		// Run once
		if ( ! $sp_nonce_flag ) {
			echo_sp_nonce();
			$sp_nonce_flag = true;
		}
		// Genrate box contents
		foreach ( $sp_boxes[$box['id']] as $sp_box ) {
			echo field_html( $sp_box );
		}
	}
	
	function field_html ( $args ) {
		switch ( $args[2] ) {
			case 'textarea':
				return text_area( $args );
			case 'checkbox':
				// To Do
			case 'radio':
				// To Do
			case 'text':
			default:
				return text_field( $args );
		}
	}
	
	function text_field ( $args ) {
		global $post;
		// adjust data
		$label = $args[3];
		$args[2] = get_post_meta($post->ID, $args[0], true);
		$args[1] = __($args[1], 'sp' );
		$label_format =
			  '<br /><label for="%1$s">%2$s</label><br />'
			. '<input style="width: 95%%;" type="text" name="%1$s" value="%3$s" />';
		
		// labels
		if ( !empty($label) ) {
			$label_format .= '<div style="padding-top:4px;text-align:center">' . $label . '</div>';
		}
		
		$label_format .= '<br />';
		return vsprintf( $label_format, $args );
	}
	
	function text_area ( $args ) {
		global $post;
		// adjust data
		$args[2] = get_post_meta($post->ID, $args[0], true);
		$args[1] = __($args[1], 'sp' );
		$label_format =
			  '<br /><label for="%1$s">%2$s</label><br />'
			. '<textarea style="width: 95%%;" name="%1$s">%3$s</textarea><br /><br />';
		return vsprintf( $label_format, $args );
	}
	
	/* When the post is saved, saves our custom data */
	function sp_save_postdata($post_id, $post) {
		
		global $sp_boxes;
		
		// verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times
		if (!isset($_POST['sp_nonce_name'])) $_POST['sp_nonce_name'] = '';
		if ( ! wp_verify_nonce( $_POST['sp_nonce_name'], plugin_basename(__FILE__) ) ) {
			return $post->ID;
		}
		
		// Is the user allowed to edit the post or page?
		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post->ID ))
				return $post->ID;
		} else {
			if ( ! current_user_can( 'edit_post', $post->ID ))
				return $post->ID;
		}
		
		// OK, we're authenticated: we need to find and save the data
		// We'll put it into an array to make it easier to loop though.
		// The data is already in $sp_boxes, but we need to flatten it out.
		foreach ( $sp_boxes as $sp_box ) {
			foreach ( $sp_box as $sp_fields ) {
				$my_data[$sp_fields[0]] =  $_POST[$sp_fields[0]];
			}
		}
		
		// Add values of $my_data as custom fields
		// Let's cycle through the $my_data array!
		foreach ($my_data as $key => $value) {
			if ( 'revision' == $post->post_type  ) {
				// don't store custom data twice
				return;
			}
			// if $value is an array, make it a CSV (unlikely)
			$value = implode(',', (array)$value);
			if ( get_post_meta($post->ID, $key, FALSE) ) {
				// Custom field has a value.
				update_post_meta($post->ID, $key, $value);
			} else {
				// Custom field does not have a value.
				add_post_meta($post->ID, $key, $value);
			}
			if (!$value) {
				// delete blanks
				delete_post_meta($post->ID, $key);
			}
		}
	}
	
	function echo_sp_nonce () {
		// Use nonce for verification ... ONLY USE ONCE!
		echo sprintf(
			'<input type="hidden" name="%1$s" id="%1$s" value="%2$s" />',
			'sp_nonce_name',
			wp_create_nonce( plugin_basename(__FILE__) )
		);
	}
	
	// A simple function to get data stored in a custom field
	if ( !function_exists('get_custom_field') ) {
		function get_custom_field($field) {
		   global $post;
		   $custom_field = get_post_meta($post->ID, $field, true);
		   echo $custom_field;
		}
	}

// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// ** Common Functions:
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	function _get_menu_details ( $omf_url ) {
		// ------------------------------------- 
		//  Return the menu details from an OMF URL
		// ------------------------------------- 

		$omf_details = false;
		if ( !empty($omf_url) ) {
			include_once OPENMENU_PATH.'/toolbox/class-omf-reader.php'; 
			$omfr = new cOmfReader; 
			$omf_details = $omfr->read_file($omf_url); 
			unset($omfr);
		}
		
		return $omf_details;
	}

	function build_menu_from_details ($omf_details, $columns = '1', $menu_filter = '', $group_filter = '') {
		// ------------------------------------- 
		//  Create a menu display from OMF Details
		// ------------------------------------- 
		
		$retval = '';
		$one_column = ($columns == '1') ? true : false ;

		if ( !empty($omf_details) ) {
			$retval .= '<div id="om_menu">';

		  if ( isset($omf_details['menus']) && !empty($omf_details['menus']) ) {
			foreach ($omf_details['menus'] AS $menu) {
				
				// Check for a menu filter
				if ( !$menu_filter || strcasecmp($menu_filter, $menu['menu_name']) == 0 ) {
				
					// Start a new menu
					$retval .= '<div class="menu_name">';
						if ( !empty($menu['menu_name']) ) {
							$retval .= clean($menu['menu_name']);
						} else {
							$retval .= clean(ucwords($menu['menu_duration_name']));
						}
					// Check for a description
					if ( !empty($menu['menu_description']) ) {
						$retval .= '<br /><span class="sm_norm">'.$menu['menu_description'].'</span>';
					}
					$retval .= '</div><div class="menu_content">'."\n";
					
					// How many groups are there in this menu
					//  used for 2 column displays
					$group_count = count($menu['menu_groups']);
					$current_group = 1;
					
					foreach ($menu['menu_groups'] AS $group) {
						// Check for a group filter
						if ( !$group_filter || strcasecmp($group_filter, $group['group_name']) == 0 ) {
					
							// Should we start the left or right column 
							if ( !$one_column ) {
								if ($current_group == 1) { 
									// Start the left Column
									$retval .= '<div class="left-menu">';
								} elseif ($current_group == (1 + (int)($group_count/2)) ) {
									// Close the left column and start the right
									$retval .= '</div><!-- END left menu -->';
									$retval .= '<div class="right-menu">';
								}
							}
							
							// Start a group
							$retval .= '<h2>'.clean($group['group_name']);
							
							if ( !empty($group['group_description']) ) {
								$retval .= '<br /><span class="sm_norm">'.$group['group_description'].'</span>';
							}
							$retval .= '</h2>'."\n";
							
							if ( !empty($group['menu_items']) ) {
								foreach ($group['menu_items'] AS $item) {
									$is_special = ($item['special'] == 1) ? '<span class="item_tag special">Special</span>' : '' ;
									$is_vegetarian = ($item['vegetarian'] == 1) ? '<span class="item_tag vegetarian">Vegetarian</span>' : '' ;
									$is_vegan = ($item['vegan'] == 1) ? '<span class="item_tag vegan">Vegan</span>' : '' ;
									$is_kosher = ($item['kosher'] == 1) ? '<span class="item_tag kosher">Kosher</span>' : '' ;
									$is_halal = ($item['halal'] == 1) ? '<span class="item_tag halal">Halal</span>' : '' ;
									$tags = $is_special.$is_vegetarian.$is_vegan.$is_kosher.$is_halal;
									$price = (!empty($item['menu_item_price'])) ? number_format($item['menu_item_price'], 2) : '' ;
									
									// See if a thumbnail exists
									$thumbnail = '';
									if ( isset($item['menu_item_images']) ) {
										$thumbnail = extract_thumbnail($item['menu_item_images']);
										if ($thumbnail) {
											$thumbnail = '<img class="mi_thumb" src="'.$thumbnail.'" />';
										}
									}

						            $retval .= '<dl>';
						            $retval .= '<dt class="pepper_' . $item['menu_item_heat_index'] . '">' . $thumbnail . $tags . clean($item['menu_item_name']) . '</dt>';
						            $retval .= '<dd class="price">'.$price.'</dd>';
						            $retval .= '<dd class="description">'.clean($item['menu_item_description']).'</dd>';

						            // Check for item size
						            if ( !empty($item['menu_item_sizes']) && is_array($item['menu_item_sizes']) ) {
						            	$retval .= '<dd class="sizes">';
							            foreach ($item['menu_item_sizes'] AS $size) {
							            	$size_price = (!empty($size['menu_item_size_price'])) ? ' - '.number_format($size['menu_item_size_price'], 2) : '' ;
							            	$retval .= '<span>'.clean($size['menu_item_size_name']).$size_price.'</span>';
							            }
							            $retval .= '</dd>';
							        }
							        
							    	// Check for options
						            if ( isset($item['menu_item_options']) && !empty($item['menu_item_options']) && is_array($item['menu_item_options']) ) {
						            	$retval .= '<dd class="item_options">';
							            foreach ($item['menu_item_options'] AS $option) {
							            	$retval .= '<div><strong>'.clean($option['item_options_name']).'</strong>: ';
							            	 if ( isset($option['option_items']) && !empty($option['option_items']) ) {
							            	 	 foreach($option['option_items'] AS $option_item) { 
							            	 	 	$retval .= clean($option_item['menu_item_option_name']).' | ';
							            	 	 }
							            	 	 // Strip the trailing |
												$retval = rtrim($retval, ' | ');
							            	 }
							            	
							            	$retval .= '</div>';
							            }
							            $retval .= '</dd>';
							        }
							        
							        // close the item
						            $retval .= '</dl>'."\n";
								} // end item
							}
							// Display Group Options
							if ( isset($group['menu_group_options']) && is_array($group['menu_group_options']) ) { 
								foreach($group['menu_group_options'] AS $option) { 
									$retval .= '<div class="goptions">';
									$retval .= '<div class="goptions-title">'.clean($option['group_options_name']);
									if ( !empty($option['menu_group_option_information']) ) {
										$retval .= '<br /><span class="goptions-desc">'.$option['menu_group_option_information'].'</span>';
									}
									$retval .= '</div>';
								
									// Check for Option Items
									if ( isset($option['option_items']) && is_array($option['option_items']) ) { 
										foreach($option['option_items'] AS $option_item) { 
											$retval .= clean($option_item['menu_group_option_name']).' | ';
										}
										// Strip the trailing |
										$retval = rtrim($retval, ' | ');
									}
									
									$retval .= '</div>';
								} 
							}
							
							// End a group
							if ( $one_column ) {
								$retval .= '<span class="separator big"></span>'."\n";
							} else {
								$retval .= '<span class="separator small"></span>'."\n";
							}
							
							$current_group++;
							
						} // end group filter
					} // end group
					
					if ( !$one_column ) {
						// Close the menu colums
						if ( $current_group > 1 ) {
							$retval .= '</div><!-- END right menu -->'."\n";
						}
						$retval .= '<div class="clear"></div>'."\n";
					}
					
					// Close the menu 
					$retval .= '</div><br clear="all" /><!-- END #menu -->'."\n";
					
					if ( !$one_column ) {
						$retval .= '<div class="page-break"></div>'."\n";
					}
				
				} // end menu filter
				
			} // end menu loop
		} else {
			$retval .= 'There was an error displaying this menu';
		}
	
		$retval .= '<div class="om_tag"><a href="http://openmenu.com">'.__('powered by').' OpenMenu</a></div>';
		$retval .= '</div><!-- #om_menu -->';
		}
		
		return $retval;
	}
	
	function extract_thumbnail( $item_images ) {
		// ------------------------------------- 
		//  Attempt to extract a thumbnail image from a menu item's image list
		//    Looks for a Thumbnail for a media type of Web
		// ------------------------------------- 
		$retval = '';
		foreach ($item_images AS $img) {
			if ( strcasecmp($img['image_type'], 'Thumbnail' ) === 0 && 
				 strcasecmp($img['image_media'], 'Web' ) === 0 &&
				 !empty($img['image_url']) ) {
				$retval = $img['image_url'];
			}
		}

		return $retval;
	}
	
	if ( ! function_exists( 'openmenu_posted_on' ) ) :
	function openmenu_posted_on() {
		printf( __( '<span class="%1$s">Posted on</span> %2$s <span class="meta-sep">by</span> %3$s', 'openmenu' ),
			'meta-prep meta-prep-author',
			sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><span class="entry-date">%3$s</span></a>',
				get_permalink(),
				esc_attr( get_the_time() ),
				get_the_date()
			),
			sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span>',
				get_author_posts_url( get_the_author_meta( 'ID' ) ),
				sprintf( esc_attr__( 'View all posts by %s', 'openmenu' ), get_the_author() ),
				get_the_author()
			)
		);
	}
	endif;
	
	if ( ! function_exists( 'clean' ) ) :
	function clean ($str) { 
		// Function not required
		return $str;
		// return htmlentities($str, ENT_COMPAT, 'UTF-8');
	}
	endif;
	
?>