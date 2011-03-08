<?php
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// ** Open Menu, LLC http://www.openmenu.com
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// ** Copyright (C) 2010, 2011 OpenMenu, All rights reserved
// **		Authored By: Chris Hanscom
// **
// **		This library is copyrighted software by OpenMenu; you can not
// **		redistribute it and/or modify it in any way without expressed written
// **		consent from Open Menu or Author.
// **
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// ** Version: 1.3
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// ** Includes: 
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// ** Constants: 
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	define('OMF_VERSION', '1.3');
	
	// Days
	define('WEEKDAY_1', 'mon');
	define('WEEKDAY_2', 'tue');
	define('WEEKDAY_3', 'wed');
	define('WEEKDAY_4', 'thu');
	define('WEEKDAY_5', 'fri');
	define('WEEKDAY_6', 'sat');
	define('WEEKDAY_7', 'sun');
	
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// ** Class
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

class cOmfReader {
	
	// Set if the data is being used for display purposes on a website
	//   this forces special characters into html entities
	public $use_htmlspecialcahrs = true;
	
	// Flags to see if we have things
	public $has_menu = false;
	public $has_menu_items = false;
	
	// MD5 Hash of the menu
	public $menu_hash = '';
	
	function read_file($omf_file_location) {
		// -------------------------------------
		// Crawl an OMF file and return an array of the values
		// -------------------------------------

		// Get the XML contents for the OMF file
		$xml = $this->get_xml_from_url($omf_file_location);

		// Update the hash
		$this->menu_hash = md5($xml);
		
		$omf_data = array();
		// Now parse it
		if ($xml) {
			// OMF information
			$omf_data['omf_uuid'] = $this->_clean(@$xml['uuid']);
			$omf_data['omf_version'] = $this->_clean(@$xml->omf_version->version);
			$omf_data['omf_editor'] = $this->_clean(@$xml->omf_version->editor);
			$omf_data['omf_general_info'] = $this->_clean(@$xml->omf_version->general_info);
			
			$omf_data['restaurant_info']['restaurant_name'] = $this->_clean(@$xml->restaurant_info->restaurant_name, 255);
		    $omf_data['restaurant_info']['brief_description'] = $this->_clean(@$xml->restaurant_info->brief_description, 255);
		    $omf_data['restaurant_info']['full_description'] = $this->_clean(@$xml->restaurant_info->full_description, 2000);
		    $omf_data['restaurant_info']['location_id'] = $this->_clean(@$xml->restaurant_info->location_id, 25);
		    $omf_data['restaurant_info']['business_type'] = $this->_clean(@$xml->restaurant_info->business_type, 11);
		    $omf_data['restaurant_info']['address_1'] = $this->_clean(@$xml->restaurant_info->address_1, 120);
		    $omf_data['restaurant_info']['address_2'] = $this->_clean(@$xml->restaurant_info->address_2, 120);
		    $omf_data['restaurant_info']['city_town'] = $this->_clean(@$xml->restaurant_info->city_town, 50);
		    $omf_data['restaurant_info']['state_province'] = $this->_clean(@$xml->restaurant_info->state_province, 2);
		    $omf_data['restaurant_info']['postal_code'] = $this->_clean(@$xml->restaurant_info->postal_code, 30);
		    $omf_data['restaurant_info']['country'] = $this->_clean(@$xml->restaurant_info->country, 2);
		    $omf_data['restaurant_info']['phone'] = $this->_clean(@$xml->restaurant_info->phone, 40);
			$omf_data['restaurant_info']['longitude'] = $this->_clean(@$xml->restaurant_info->longitude, 11);
			$omf_data['restaurant_info']['latitude'] = $this->_clean(@$xml->restaurant_info->latitude, 10);
			$omf_data['restaurant_info']['utc_offset'] = $this->_clean(@$xml->restaurant_info->utc_offset, 6);
		    $omf_data['restaurant_info']['fax'] = $this->_clean(@$xml->restaurant_info->fax, 40);
		    $omf_data['restaurant_info']['website_url'] = $this->_clean(@$xml->restaurant_info->website_url, 120);
		    $omf_data['restaurant_info']['omf_file_url'] = $this->_clean(@$xml->restaurant_info->omf_file_url, 120);
			$omf_data['formatted_address'] = $this->format_address($omf_data['restaurant_info']['address_1'], $omf_data['restaurant_info']['city_town'], $omf_data['restaurant_info']['state_province'], $omf_data['restaurant_info']['postal_code'], $omf_data['restaurant_info']['country']);
		
			// Environment Information
		    $omf_data['environment_info']['seating_qty'] = $this->_clean(@$xml->restaurant_info->environment->seating_qty);
		    $omf_data['environment_info']['smoking_allowed'] = $this->_clean(@$xml->restaurant_info->environment->smoking_allowed, 1);
		    $omf_data['environment_info']['takeout_available'] = $this->_clean(@$xml->restaurant_info->environment->takeout_available, 1);
		    $omf_data['environment_info']['delivery_available'] = $this->_clean(@$xml->restaurant_info->environment->delivery_available, 1);
		    $omf_data['environment_info']['catering_available'] = $this->_clean(@$xml->restaurant_info->environment->catering_available, 1);
		    $omf_data['environment_info']['reservations'] = $this->_clean(@$xml->restaurant_info->environment->reservations, 9);
		    $omf_data['environment_info']['alcohol_type'] = $this->_clean(@$xml->restaurant_info->environment->alcohol_type, 13);
		    $omf_data['environment_info']['music_type'] = $this->_clean(@$xml->restaurant_info->environment->music_type, 13);
		    $omf_data['environment_info']['pets_allowed'] = $this->_clean(@$xml->restaurant_info->environment->pets_allowed, 1);
		    $omf_data['environment_info']['max_group_size'] = $this->_clean(@$xml->restaurant_info->environment->max_group_size);
		    $omf_data['environment_info']['dress_code'] = $this->_clean(@$xml->restaurant_info->environment->dress_code);
			$omf_data['environment_info']['age_level_preference'] = $this->_clean(@$xml->restaurant_info->environment->age_level_preference, 4);
			$omf_data['environment_info']['cuisine_type_primary'] = $this->_clean(@$xml->restaurant_info->environment->cuisine_type_primary, 120);
		    $omf_data['environment_info']['cuisine_type_secondary'] = $this->_clean(@$xml->restaurant_info->environment->cuisine_type_secondary, 120);
		    
			// Parent company
			$omf_data['parent_company']['parent_company_name'] = $this->_clean(@$xml->restaurant_info->environment->parent_company->parent_company_name, 255);
			$omf_data['parent_company']['parent_company_website'] = $this->_clean(@$xml->restaurant_info->environment->parent_company->parent_company_website, 120);
		    $omf_data['parent_company']['address_1'] = $this->_clean(@$xml->restaurant_info->environment->parent_company->address_1, 120);
		    $omf_data['parent_company']['address_2'] = $this->_clean(@$xml->restaurant_info->environment->parent_company->address_2, 120);
		    $omf_data['parent_company']['city_town'] = $this->_clean(@$xml->restaurant_info->environment->parent_company->city_town, 50);
		    $omf_data['parent_company']['state_province'] = $this->_clean(@$xml->restaurant_info->environment->parent_company->state_province, 2);
		    $omf_data['parent_company']['postal_code'] = $this->_clean(@$xml->restaurant_info->environment->parent_company->postal_code, 30);
		    $omf_data['parent_company']['country'] = $this->_clean(@$xml->restaurant_info->environment->parent_company->country, 2);
		    $omf_data['parent_company']['phone'] = $this->_clean(@$xml->restaurant_info->environment->parent_company->phone, 40);
		    $omf_data['parent_company']['fax'] = $this->_clean(@$xml->restaurant_info->environment->parent_company->fax, 40);
		    
		    // Operating Days
		    $omf_data['operating_days'] = array();
	    	// Start with a blank structure
			$omf_data['operating_days']['mon_open_time'] = '';
			$omf_data['operating_days']['mon_close_time'] = '';
			$omf_data['operating_days']['tue_open_time'] = '';
			$omf_data['operating_days']['tue_close_time'] = '';
			$omf_data['operating_days']['wed_open_time'] = '';
			$omf_data['operating_days']['wed_close_time'] = '';
			$omf_data['operating_days']['thu_open_time'] = '';
			$omf_data['operating_days']['thu_close_time'] = '';
			$omf_data['operating_days']['fri_open_time'] = '';
			$omf_data['operating_days']['fri_close_time'] = '';
			$omf_data['operating_days']['sat_open_time'] = '';
			$omf_data['operating_days']['sat_close_time'] = '';
			$omf_data['operating_days']['sun_open_time'] = '';
			$omf_data['operating_days']['sun_close_time'] = '';
		    if (isset($xml->restaurant_info->environment->operating_days)) {
			    foreach ($xml->restaurant_info->environment->operating_days->operating_day AS $day) {
			    	if (isset($day->day_of_week) && !empty($day->day_of_week)) {
			    		$omf_data['operating_days'][constant('WEEKDAY_'.$day->day_of_week).'_open_time'] = $this->_clean(@$day->open_time);
			    		$omf_data['operating_days'][constant('WEEKDAY_'.$day->day_of_week).'_close_time'] = $this->_clean(@$day->close_time);
			    	}
			    }
		    }
		    
		    // Get a print friendly version of the operation days 
			$omf_data['operating_days']['printable'] = $this->print_friendly_hours($omf_data['operating_days']);
		    
		    // Get the Seating Locations
		    $omf_data['seating_locations'] = array();
		    if (isset($xml->restaurant_info->environment->seating_locations)) {
			    foreach ($xml->restaurant_info->environment->seating_locations->seating_location AS $seating) {
				    $omf_data['seating_locations'][] = $this->_clean(@$seating);
			    }
		    }
		    
		    // Get Accepted currencies
		    $omf_data['accepted_currencies'] = array();
		    if (isset($xml->restaurant_info->environment->accepted_currencies)) {
			    foreach ($xml->restaurant_info->environment->accepted_currencies->accepted_currency AS $currency) {
				    $omf_data['accepted_currencies'][] = $this->_clean(@$currency);
			    }
		    }
			
		    // Get Logo URLs
		    $omf_data['logo_urls'] = array();
		    if (isset($xml->restaurant_info->logo_urls)) {
		    	$i=0;
			    foreach ($xml->restaurant_info->logo_urls->logo_url AS $logo) {
			    	if ( !empty($logo) ) {
					    $omf_data['logo_urls'][$i]['logo_url'] = $this->_clean(@$logo);
					    $omf_data['logo_urls'][$i]['width'] = $this->_clean(@$logo['width']);
					    $omf_data['logo_urls'][$i]['height'] = $this->_clean(@$logo['height']);
					    $omf_data['logo_urls'][$i]['image_type'] = $this->_clean(@$logo['type']);
					    $omf_data['logo_urls'][$i]['image_media'] = $this->_clean(@$logo['media']);
					    
					    $i++;
					}
			    }
		    }

		    // Get Online Reservations
		    $omf_data['online_reservations'] = array();
		    if (isset($xml->restaurant_info->environment->online_reservations)) {
		    	$i=0;
			    foreach ($xml->restaurant_info->environment->online_reservations->online_reservation AS $reserv) {
			    	if ( !empty($reserv) ) {
			    		$omf_data['online_reservations'][$i]['online_reservation_name'] = $this->_clean(@$reserv->online_reservation_name, 50);
					    $omf_data['online_reservations'][$i]['online_reservation_url'] = $this->_clean(@$reserv->online_reservation_url, 120);
					    $omf_data['online_reservations'][$i]['online_reservation_type'] = $this->_clean(@$reserv['type']);

					    $i++;
					}
			    }
		    }
		    
		    // Get Parking
		    $omf_data['parking'] = array();
		    if (isset($xml->restaurant_info->environment->parking)) {
		    	$park = $xml->restaurant_info->environment->parking;
				$omf_data['parking']['street_free'] = $this->check_attribute('street_free', @$park['street_free']);
				$omf_data['parking']['street_metered'] = $this->check_attribute('street_metered', @$park['street_metered']);
				$omf_data['parking']['private_lot'] = $this->check_attribute('private_lot', @$park['private_lot']);
				$omf_data['parking']['garage'] = $this->check_attribute('garage', @$park['garage']);
				$omf_data['parking']['valet'] = $this->check_attribute('valet', @$park['valet']);
		    }
		    
		    // Get Contacts
		    $omf_data['contacts'] = array();
		    if (isset($xml->restaurant_info->contacts)) {
		    	$i=0;
			    foreach ($xml->restaurant_info->contacts->contact AS $contact) {
			    	if ( !empty($contact->first_name) || !empty($contact->last_name) || !empty($contact->email) ) {
					    $omf_data['contacts'][$i]['first_name'] = $this->_clean(@$contact->first_name);
					    $omf_data['contacts'][$i]['last_name'] = $this->_clean(@$contact->last_name);
					    $omf_data['contacts'][$i]['email'] = $this->_clean(@$contact->email);
					    $omf_data['contacts'][$i]['contact_type'] = $this->_clean(@$contact['type']);
					    
					    $i++;
					}
			    }
		    }
		    
		    // Now parse the menu, menu groups, menu items and menu item sizes
		    $menu_id = 0;
			// Loop through all menus;
			if (isset($xml->menus->menu)) {
				$this->has_menu = true;
					
				foreach ($xml->menus->menu AS $menu) { 

					$omf_data['menus'][$menu_id]['menu_name'] = $this->_clean(@$menu['name'], 50);
					$omf_data['menus'][$menu_id]['menu_description'] = $this->_clean(@$menu->menu_description, 255);
					$omf_data['menus'][$menu_id]['currency_symbol'] = $this->_clean(@$menu['currency_symbol'], 3);
					$omf_data['menus'][$menu_id]['menu_uid'] = $this->_clean(@$menu['uid']);
					$omf_data['menus'][$menu_id]['disabled'] = $this->check_attribute('disabled', @$menu['disabled']);
							
					// Grab the duration for this menu
			    	$omf_data['menus'][$menu_id]['menu_duration_name'] = $this->_clean(@$menu->menu_duration->menu_duration_name);
					$omf_data['menus'][$menu_id]['menu_duration_time_start'] = $this->_clean(@$menu->menu_duration->menu_duration_time_start);
					$omf_data['menus'][$menu_id]['menu_duration_time_end'] = $this->_clean(@$menu->menu_duration->menu_duration_time_end);

			    	// Loop through the groups in this menu
			    	$group_id = 0;
			    	if (isset($menu->menu_groups->menu_group)) {
				    	foreach ($menu->menu_groups->menu_group AS $group) {
					    	// Grab the group name
					    	$omf_data['menus'][$menu_id]['menu_groups'][$group_id]['group_name'] = $this->_clean(@$group['name'], 50);
					    	$omf_data['menus'][$menu_id]['menu_groups'][$group_id]['group_description'] = $this->_clean(@$group->menu_group_description, 255);
							$omf_data['menus'][$menu_id]['menu_groups'][$group_id]['group_uid'] = $this->_clean(@$group['uid']);
							$omf_data['menus'][$menu_id]['menu_groups'][$group_id]['disabled'] = $this->check_attribute('disabled', @$group['disabled']);

							// Group Options
					    	$go_id = 0;
					    	if ( isset($group->menu_group_options) ) {
						    	foreach ($group->menu_group_options->menu_group_option AS $opt) { 
							    	// Menu item options
							    	$omf_data['menus'] [$menu_id] ['menu_groups'] [$group_id] ['menu_group_options'] [$go_id] ['group_options_name'] = $this->_clean(@$opt['name']);
							    	$omf_data['menus'] [$menu_id] ['menu_groups'] [$group_id] ['menu_group_options'] [$go_id] ['menu_group_option_min_selected'] = $this->_clean(@$opt['min_selected']);
							    	$omf_data['menus'] [$menu_id] ['menu_groups'] [$group_id] ['menu_group_options'] [$go_id] ['menu_group_option_max_selected'] = $this->_clean(@$opt['max_selected']);
							    	$omf_data['menus'] [$menu_id] ['menu_groups'] [$group_id] ['menu_group_options'] [$go_id] ['menu_group_option_information'] = $this->_clean(@$opt->menu_group_option_information, 255);

							    	// Check for Option Items
							    	$oi_id = 0; 
							    	foreach ($opt->menu_group_option_item AS $opt_item) { 
							    		$omf_data['menus'] [$menu_id] ['menu_groups'] [$group_id] ['menu_group_options'] [$go_id] ['option_items'] [$oi_id] ['menu_group_option_name'] = $this->_clean(@$opt_item->menu_group_option_name, 50);
							    		$omf_data['menus'] [$menu_id] ['menu_groups'] [$group_id] ['menu_group_options'] [$go_id] ['option_items'] [$oi_id] ['menu_group_option_additional_cost'] = $this->_clean(@$opt_item->menu_group_option_additional_cost);
							    		$oi_id++;
							    	}
							    	
							    	$go_id++;
							    	
						    	} // efs
						    } // es

					    	// Loop through the menu items in this group
					    	$item_id = 0;
					    	if (isset($group->menu_items->menu_item)) { 
					    		$this->has_menu_items = true;
					    			
						    	foreach ($group->menu_items->menu_item AS $item) {
							    	// Menu item details
							    	$omf_data['menus'] [$menu_id] ['menu_groups'] [$group_id] ['menu_items'] [$item_id]['menu_item_name'] = $this->_clean(@$item->menu_item_name, 75);
									$omf_data['menus'] [$menu_id] ['menu_groups'] [$group_id] ['menu_items'] [$item_id]['menu_item_description'] = $this->_clean(@$item->menu_item_description, 450);
									$omf_data['menus'] [$menu_id] ['menu_groups'] [$group_id] ['menu_items'] [$item_id]['menu_item_price'] = $this->_clean(@$item->menu_item_price, 7);
									$omf_data['menus'] [$menu_id] ['menu_groups'] [$group_id] ['menu_items'] [$item_id]['menu_item_calories'] = $this->_clean(@$item->menu_item_calories);
									$omf_data['menus'] [$menu_id] ['menu_groups'] [$group_id] ['menu_items'] [$item_id]['menu_item_heat_index'] = $this->_clean(@$item->menu_item_heat_index, 1);
									$omf_data['menus'] [$menu_id] ['menu_groups'] [$group_id] ['menu_items'] [$item_id]['menu_item_allergy_information'] = $this->_clean(@$item->menu_item_allergy_information, 450);
									$omf_data['menus'] [$menu_id] ['menu_groups'] [$group_id] ['menu_items'] [$item_id]['menu_item_allergy_information_allergens'] = $this->_clean(@$item->menu_item_allergy_information['allergens']);
									
									$omf_data['menus'] [$menu_id] ['menu_groups'] [$group_id] ['menu_items'] [$item_id]['item_uid'] = $this->_clean(@$item['uid']);
									$omf_data['menus'] [$menu_id] ['menu_groups'] [$group_id] ['menu_items'] [$item_id]['disabled'] = $this->check_attribute('disabled', @$item['disabled']);
									$omf_data['menus'] [$menu_id] ['menu_groups'] [$group_id] ['menu_items'] [$item_id]['special'] = $this->check_attribute('special', @$item['special']);
									$omf_data['menus'] [$menu_id] ['menu_groups'] [$group_id] ['menu_items'] [$item_id]['vegetarian'] = $this->check_attribute('vegetarian', @$item['vegetarian']);
									$omf_data['menus'] [$menu_id] ['menu_groups'] [$group_id] ['menu_items'] [$item_id]['vegan'] = $this->check_attribute('vegan', @$item['vegan']);
									$omf_data['menus'] [$menu_id] ['menu_groups'] [$group_id] ['menu_items'] [$item_id]['kosher'] = $this->check_attribute('kosher', @$item['kosher']);
									$omf_data['menus'] [$menu_id] ['menu_groups'] [$group_id] ['menu_items'] [$item_id]['halal'] = $this->check_attribute('halal', @$item['halal']);
									
									// Options
							    	$option_id = 0;
							    	if ( isset($item->menu_item_options) ) {
								    	foreach ($item->menu_item_options->menu_item_option AS $opt) { 
									    	// Menu item options
									    	$omf_data['menus'] [$menu_id] ['menu_groups'] [$group_id] ['menu_items'] [$item_id] ['menu_item_options'] [$option_id] ['item_options_name'] = $this->_clean(@$opt['name']);
									    	$omf_data['menus'] [$menu_id] ['menu_groups'] [$group_id] ['menu_items'] [$item_id] ['menu_item_options'] [$option_id] ['menu_item_option_min_selected'] = $this->_clean(@$opt['min_selected']);
									    	$omf_data['menus'] [$menu_id] ['menu_groups'] [$group_id] ['menu_items'] [$item_id] ['menu_item_options'] [$option_id] ['menu_item_option_max_selected'] = $this->_clean(@$opt['max_selected']);
									    	$omf_data['menus'] [$menu_id] ['menu_groups'] [$group_id] ['menu_items'] [$item_id] ['menu_item_options'] [$option_id] ['menu_item_option_information'] = $this->_clean(@$opt->menu_item_option_information, 255);
									    	
									    	
									    	// Check for Option Items
									    	$option_item_id = 0; 
									    	foreach ($opt->menu_item_option_item AS $opt_item) { 
									    		$omf_data['menus'] [$menu_id] ['menu_groups'] [$group_id] ['menu_items'] [$item_id] ['menu_item_options'] [$option_id] ['option_items'] [$option_item_id] ['menu_item_option_name'] = $this->_clean(@$opt_item->menu_item_option_name, 50);
									    		$omf_data['menus'] [$menu_id] ['menu_groups'] [$group_id] ['menu_items'] [$item_id] ['menu_item_options'] [$option_id] ['option_items'] [$option_item_id] ['menu_item_option_additional_cost'] = $this->_clean(@$opt_item->menu_item_option_additional_cost);
									    		$option_item_id++;
									    	}
									    	
									    	$option_id++;
									    	
								    	} // efs
								    } // es
									
									// Images 
							    	$image_id = 0;
							    	if (isset($item->menu_item_image_urls)) {
								    	foreach ($item->menu_item_image_urls->menu_item_image_url AS $image) {
									    	// Menu item images
									    	$omf_data['menus'] [$menu_id] ['menu_groups'] [$group_id] ['menu_items'] [$item_id] ['menu_item_images'] [$image_id] ['image_url'] = $this->_clean(@$image);
									    	$omf_data['menus'] [$menu_id] ['menu_groups'] [$group_id] ['menu_items'] [$item_id] ['menu_item_images'] [$image_id] ['width'] = $this->_clean(@$image['width']);
									    	$omf_data['menus'] [$menu_id] ['menu_groups'] [$group_id] ['menu_items'] [$item_id] ['menu_item_images'] [$image_id] ['height'] = $this->_clean(@$image['height']);
									    	$omf_data['menus'] [$menu_id] ['menu_groups'] [$group_id] ['menu_items'] [$item_id] ['menu_item_images'] [$image_id] ['image_type'] = $this->_clean(@$image['type']);
									    	$omf_data['menus'] [$menu_id] ['menu_groups'] [$group_id] ['menu_items'] [$item_id] ['menu_item_images'] [$image_id] ['image_media'] = $this->_clean(@$image['media']);
									    	$image_id++;
									    	
								    	} // efs
								    } // es
									
									// Tags
							    	$tag_id = 0;
							    	if (isset($item->menu_item_tags)) {
								    	foreach ($item->menu_item_tags->menu_item_tag AS $tag) {
									    	// Menu item size
									    	$omf_data['menus'] [$menu_id] ['menu_groups'] [$group_id] ['menu_items'] [$item_id]['menu_item_tags'] [$tag_id] ['menu_item_tag'] = $this->_clean(@$tag, 35);
									    	$size_id++;
									    	
								    	} // efs
								    } // es
									
							    	// Loop through the menu item sizes for this item
							    	$size_id = 0;
							    	if (isset($item->menu_item_sizes->menu_item_size)) {
								    	foreach ($item->menu_item_sizes->menu_item_size AS $size) {
									    	// Menu item size
									    	$omf_data['menus'] [$menu_id] ['menu_groups'] [$group_id] ['menu_items'] [$item_id]['menu_item_sizes'] [$size_id] ['menu_item_size_name'] = $this->_clean(@$size->menu_item_size_name, 25);
											$omf_data['menus'] [$menu_id] ['menu_groups'] [$group_id] ['menu_items'] [$item_id]['menu_item_sizes'] [$size_id] ['menu_item_size_description'] = $this->_clean(@$size->menu_item_size_description, 120);
											$omf_data['menus'] [$menu_id] ['menu_groups'] [$group_id] ['menu_items'] [$item_id]['menu_item_sizes'] [$size_id] ['menu_item_size_price'] = $this->_clean(@$size->menu_item_size_price, 7);
											$omf_data['menus'] [$menu_id] ['menu_groups'] [$group_id] ['menu_items'] [$item_id]['menu_item_sizes'] [$size_id] ['menu_item_size_calories'] = $this->_clean(@$size->menu_item_size_calories);
											
									    	$size_id++;
									    	
								    	} // efs
								    } // es
								    
								    $item_id++;
								} // efe
					    	} // ei
					    	$group_id++;
				    	} // efe
			    	} // ei
			    	$menu_id++;
			    } // efe
		    } // ei
		   
		}

		return $omf_data;
	}

	function get_blank_omf_structure() {
		// -------------------------------------
		// Get a blank structure for the OMF spec
		// -------------------------------------
		
		$omf_data = array();
		$omf_data['restaurant_info']['restaurant_name'] = '';
	    $omf_data['restaurant_info']['brief_description'] = '';
	    $omf_data['restaurant_info']['full_description'] = '';
	    $omf_data['restaurant_info']['location_id'] = '';
	    $omf_data['restaurant_info']['address_1'] = '';
	    $omf_data['restaurant_info']['address_2'] = '';
	    $omf_data['restaurant_info']['city_town'] = '';
	    $omf_data['restaurant_info']['state_province'] = '';
	    $omf_data['restaurant_info']['postal_code'] = '';
	    $omf_data['restaurant_info']['country'] = '';
	    $omf_data['restaurant_info']['region_area'] = '';
	    $omf_data['restaurant_info']['phone'] = '';
	    $omf_data['restaurant_info']['fax'] = '';
	    $omf_data['restaurant_info']['longitude'] = '';
	    $omf_data['restaurant_info']['latitude'] = '';
	    $omf_data['restaurant_info']['website_url'] = '';
	    $omf_data['restaurant_info']['omf_file_url'] = '';
	    
	    $omf_data['environment_info']['cuisine_type_primary'] = '';
	    $omf_data['environment_info']['cuisine_type_secondary'] = '';
	    $omf_data['environment_info']['seating_qty'] = '';
	    $omf_data['environment_info']['max_group_size'] = '';
	    $omf_data['environment_info']['age_level_preference'] = '';
	    $omf_data['environment_info']['smoking_allowed'] = '';
	    $omf_data['environment_info']['takeout_available'] = '';
	    $omf_data['environment_info']['pets_allowed'] = '';
	    $omf_data['environment_info']['dress_code'] = '';

		$omf_data['parent_company']['parent_company_name'] = '';
		$omf_data['parent_company']['parent_company_website'] = '';
		$omf_data['parent_company']['address_1'] = '';
		$omf_data['parent_company']['address_2'] = '';
		$omf_data['parent_company']['city_town'] = '';
		$omf_data['parent_company']['state_province'] = '';
		$omf_data['parent_company']['postal_code'] = '';
		$omf_data['parent_company']['country'] = '';
		$omf_data['parent_company']['phone'] = '';
		$omf_data['parent_company']['fax'] = '';
      	
      	$omf_data['parking'] = array();
      	$omf_data['online_reservations'] = array();
	   	$omf_data['seating_locations'] = array();
	    $omf_data['accepted_currencies'] = array();
		$omf_data['contacts'] = array();
      	$omf_data['logo_urls'] = array();
      	$omf_data['operating_days'] = array();
      	$omf_data['menus'] = array();
      	
		return $omf_data;
	}

// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// ** Private functions
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

	function format_address($address = '', $city = '', $state = '', $postal_code = '', $country = '') {
		// -------------------------------------
		//  Formats an address in the format of
		//   address, city, state, zipcode, country
		// -------------------------------------
		$retval = (!empty($address)) ? $address.', ' : '' ;
		$retval .= (!empty($city)) ? $city.', ' : '' ;
		$retval .= (!empty($state)) ? $state.', ' : '' ;
		$retval .= (!empty($postal_code)) ? $postal_code.', ' : '' ;
		$retval .= (!empty($country)) ? $country.', ' : '' ;
		// remove any trailing comma before returning
		$retval = rtrim($retval, ', ');
		return $retval;
	}

	function print_friendly_hours($operating_hours) {
		// -------------------------------------
		//  Formats the operating hours into:
		//  	Sun-Thu: 3pm - 10pm
		//  	Fri-Sat: 3pm - 11pm
		// -------------------------------------

		$retval = array();
		$current_day = 'Sun';
		// Sun/Mon
		if ($operating_hours['sun_open_time'] == $operating_hours['mon_open_time'] && 
		  $operating_hours['sun_close_time'] == $operating_hours['mon_close_time']) {
		} else {
			$retval[] = $current_day.': '.$this->format_time($operating_hours['sun_open_time']).' - '.$this->format_time($operating_hours['sun_close_time']);
			$current_day = 'Mon';
		}
		// Mon/Tue
		if ($operating_hours['mon_open_time'] == $operating_hours['tue_open_time'] && 
		  $operating_hours['mon_close_time'] == $operating_hours['tue_close_time']) {
		} else {
			$day = ($current_day == 'Mon') ? $current_day : $current_day.'-Mon' ;
			$retval[] = $day.': '.$this->format_time($operating_hours['mon_open_time']).' - '.$this->format_time($operating_hours['mon_close_time']);
			$current_day = 'Tue';
		}
		// Tue/Wed
		if ($operating_hours['tue_open_time'] == $operating_hours['wed_open_time'] && 
		  $operating_hours['tue_close_time'] == $operating_hours['wed_close_time']) {
		} else {
			$day = ($current_day == 'Tue') ? $current_day : $current_day.'-Tue' ;
			$retval[] = $day.': '.$this->format_time($operating_hours['tue_open_time']).' - '.$this->format_time($operating_hours['tue_close_time']);
			$current_day = 'Wed';
		}
		// Wed/Thu
		if ($operating_hours['wed_open_time'] == $operating_hours['thu_open_time'] && 
		  $operating_hours['wed_close_time'] == $operating_hours['thu_close_time']) {
		} else {
			$day = ($current_day == 'Wed') ? $current_day : $current_day.'-Wed' ;
			$retval[] = $day.': '.$this->format_time($operating_hours['wed_open_time']).' - '.$this->format_time($operating_hours['wed_close_time']);
			$current_day = 'Thu';
		}
		// Thu/Fri
		if ($operating_hours['thu_open_time'] == $operating_hours['fri_open_time'] && 
		  $operating_hours['thu_close_time'] == $operating_hours['fri_close_time']) {
		} else {
			$day = ($current_day == 'Thu') ? $current_day : $current_day.'-Thu' ;
			$retval[] = $day.': '.$this->format_time($operating_hours['thu_open_time']).' - '.$this->format_time($operating_hours['thu_close_time']);
			$current_day = 'Fri';
		}
		// Fri/Sat
		if ($operating_hours['fri_open_time'] == $operating_hours['sat_open_time'] && 
		  $operating_hours['fri_close_time'] == $operating_hours['sat_close_time']) {
		  	$day = $current_day.'-Sat' ;
			$retval[] = $day.': '.$this->format_time($operating_hours['fri_open_time']).' - '.$this->format_time($operating_hours['fri_close_time']);
		} else {
			$day = ($current_day == 'Fri') ? $current_day : $current_day.'-Fri' ;
			$retval[] = $day.': '.$this->format_time($operating_hours['fri_open_time']).' - '.$this->format_time($operating_hours['fri_close_time']);
			$retval[] = 'Sat: '.$this->format_time($operating_hours['sat_open_time']).' - '.$this->format_time($operating_hours['sat_close_time']);
		}
		
		return $retval;
	}

	function format_time ($time) {
		// -------------------------------------
		//  Convert pass time to the format of hh:mm AM / PM
		// -------------------------------------
		return (empty($time)) ? '' : date('g:iA', strtotime($time)) ;
	}
	
	private function _clean ($data, $length = false) {
		// -------------------------------------
		// Clean crawled data
		// -------------------------------------
		
		// Trim to length if required
		if ($length) {
			$data = substr($data, 0, $length);
		} else {
			$data = (string)$data;
		}
		
		// Return the cleaned and trimmed data
		return ($this->use_htmlspecialcahrs) ? htmlspecialchars($data) : $data;
	}
	
	private function check_attribute ($expected_value, $set_value) {
		// -------------------------------------
		// Check for an attribute like disabled="disabled" and 
		//   returns 1 if set, else returns blank
		// -------------------------------------
		
		return ( strcasecmp($expected_value, $set_value) === 0 ) ? 1 : '' ;
	
	}

	private function get_xml_from_url( $omf_file_location ) {
		// -------------------------------------
		// Get the XML from the URL
		// -------------------------------------
		
		$xml = false;
		
		// Get the XML contents for the OMF file
		if ( false && function_exists('simplexml_load_file') ) {
			$xml = @simplexml_load_file($omf_file_location);
		} else {
			if ( function_exists( 'curl_init' ) ) {

				$curl = curl_init ();
				curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 );
				curl_setopt ( $curl, CURLOPT_URL, $omf_file_location );
				$contents = curl_exec ( $curl );
				curl_close ( $curl );

				if ( $contents )
					$xml = simplexml_load_string($contents);
				else 
					$xml = false;
					
			} else {
				$xml = file_get_contents ( $omf_file_location );
				$xml = simplexml_load_string($xml);
			}
		}

		return $xml;
	}
	
} // END CLASS

?>