<?php
/**
 * The Template for displaying all single posts.
 *
 * @plugin OpenMenu
 */

	// Load Stuff for Google Maps
	wp_register_script('google-maps', 'http://maps.google.com/maps/api/js?sensor=true');
	wp_enqueue_script( 'google-maps' );
	wp_enqueue_script( 'jquery' );
	
 	// Get Post Settings
 	the_post();
	$custom = get_post_custom(); 
	$restaurant_name = (isset($custom["_restaurant_name"][0])) ? $custom["_restaurant_name"][0] : '' ;
	$omf_url = (isset($custom["_omf_url"][0])) ? $custom["_omf_url"][0] : '' ;
	$menu_filter = ( !empty($custom["_menu_filter"][0]) ) ? $custom["_menu_filter"][0] : false ;
	$group_filter = ( !empty($custom["_group_filter"][0]) ) ? $custom["_group_filter"][0] : false ;
	
	// Get the Open Menu Options
	$options = get_option( 'openmenu_options' );
	
	$hide_sidebar = ( isset($options['hide_sidebar']) && $options['hide_sidebar'] ) ? true : false ;
	$one_column = ( isset($options['display_columns']) && $options['display_columns'] == 'One' ) ? true : false ;
	$display_columns = ( isset($options['display_columns']) && $options['display_columns'] == 'One' ) ? '1' : '2' ;
	
	$display_type = (isset($options['display_type'])) ? $options['display_type'] : 'Menu' ;;
	$background_color = ( !empty($options['background_color']) ) ? $options['background_color'] : '#fff' ;
	
	// See if we should override the width
	if ( $hide_sidebar ) {
		$content_width_css = ( !empty($options['width_override']) ) ? 'style="width:'.$options['width_override'].';margin:0 auto"' : 'style="width:95%; margin:0 auto"' ; 
	} else { 
		$content_width_css = ''; 
	} 
	
	// Get the menu
	$omf_details = _get_menu_details( $omf_url ); 
?>

<?php get_header() ?>

	<style type="text/css">
		#om_menu, #om_menu dt, #om_menu dd.price { background-color:<?php echo $background_color; ?> }
	</style>

	<div id="container">
		<div id="content" class="openmenu" <?php echo $content_width_css; ?>>
			<h1 class="entry-title"><?php the_title(); ?></h1>

			<?php the_content(); ?>

			<div id="openmenu">

<?php 
	// Display the restaurant information
	if ( strcasecmp($display_type, 'restaurant information / menu') == 0 || 
	  strcasecmp($display_type, 'restaurant information') == 0 ) {
?>

	<script type="text/javascript">
		var $j = jQuery.noConflict();
		var geocoder;
		var map;
	    var image = '<?php echo OPENMENU_TEMPLATES_URL; ?>/default/images/ico-32-restaurant.png';

		$j(document).ready(function() {
			// Initialize the mapping stuff
			initialize();

<?php

	// If we have the cords then no need to geo-code
	if (!empty($omf_details['restaurant_info']['latitude']) && !empty($omf_details['restaurant_info']['longitude'])) {
		echo 'map_cords("'.$omf_details['restaurant_info']['latitude'].'", "'.$omf_details['restaurant_info']['longitude'].'");';
	} else {
		echo 'map_address("'.$omf_details['formatted_address'].'");';
	}
	
?>
		});

	  function initialize() {
	    geocoder = new google.maps.Geocoder();
	    var myOptions = {
	      zoom: 14,
	      mapTypeId: google.maps.MapTypeId.ROADMAP
	    }
	    map = new google.maps.Map(document.getElementById("locationmap"), myOptions);
	  }

	  function map_cords(lat, lng) {
		var myLatLng = new google.maps.LatLng(lat, lng);
		var marker = new google.maps.Marker({
		    position: myLatLng,
		    map: map,
		    icon: image
		});
		map.setCenter(new google.maps.LatLng(lat, lng));
	  }

	  function map_address(address) {
	    if (geocoder) {
	      geocoder.geocode( { 'address': address}, function(results, status) {
	        if (status == google.maps.GeocoderStatus.OK) {
	          map.setCenter(results[0].geometry.location);
	          var marker = new google.maps.Marker({
	              map: map, 
	              icon: image, 
	              position: results[0].geometry.location,
	              title: '<?php echo addslashes($omf_details['restaurant_info']['restaurant_name']); ?>'
	          });
	        } else {
	          // alert("Geocode could not process the requested address\n" + status);
	        }
	      });
	    }
	  }
	</script>

			<div id="om_restaurant">

				<div id="location-map"> 
					<div id="locationmap"></div>
				</div>
				
				<!--
				<div id="rest_name"><?php echo clean($omf_details['restaurant_info']['restaurant_name']); ?></div>
				-->
				
				<div id="details">
		            <p><?php echo clean($omf_details['restaurant_info']['brief_description']); ?></p>
		            <p>
		            	<strong><?php _e('Address') ?>:</strong><br />
		            	<?php echo clean($omf_details['restaurant_info']['address_1']); ?><br />
		            	<?php echo clean($omf_details['restaurant_info']['city_town']); ?>, <?php echo $omf_details['restaurant_info']['state_province']; ?> <?php echo $omf_details['restaurant_info']['postal_code']; ?> <?php echo $omf_details['restaurant_info']['country']; ?>
		            </p>
		            <p>
			            <strong><?php _e('Phone') ?>: </strong> <?php echo $omf_details['restaurant_info']['phone']; ?><br />
			            <strong><?php _e('Website') ?>: </strong> <a href="<?php echo $omf_details['restaurant_info']['website_url']; ?>"><?php echo $omf_details['restaurant_info']['website_url']; ?></a>
		            </p>
		            
		            <p>
		            	<strong><?php _e('Hours') ?>:</strong><br />
<?php 
	foreach ($omf_details['operating_days']['printable'] AS $daytime) {
		echo $daytime.'<br />';
	}
?>
			        </p>
			        
			        <div><strong><?php _e('Type') ?>:</strong> <?php echo clean($omf_details['environment_info']['cuisine_type_primary']); ?></div>
				</div>

			<div class="clear"></div>
		</div>
		
		
<?php 
	} // end restaurant info

	// Display the Menu
	if ( strcasecmp($display_type, 'restaurant information / menu') == 0 || 
	 strcasecmp($display_type, 'menu') == 0 ) {
		echo build_menu_from_details($omf_details, $display_columns, $menu_filter, $group_filter); 
	
	}
?>

			</div> <!-- #openmenu -->

		</div><!-- #content -->
	</div><!-- #container -->


<?php 
	unset($omf_details);

	if ( !$hide_sidebar ) {
		get_sidebar();
	}
	
	get_footer();
?>