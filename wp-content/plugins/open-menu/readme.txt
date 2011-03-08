=== OpenMenu - The official plugin for OpenMenu ===
Contributors: openmenu
Donate link: http://openmenu.com
Tags: openmenu, restaurant, menu, restaurants, menus, open menu, dining, food, openmenu
Requires at least: 3.0
Tested up to: 3.0.4
Stable tag: 1.3.2

Easily create posts that are based on your OpenMenu Format restaurant menu.  Fully integrates an OpenMenu Format menu or menus into an existing theme.

== Description ==
This plugin allows you to easily create posts that are based on your OpenMenu Format menu and thus embedding restaurant menus in any Wordpress website.  This plugin fully integrates an OpenMenu Format menu or menus into an existing theme.  Widget / Menu ready themes work best.

The OpenMenu Plugin is the official plugin for OpenMenu and adding restaurant menus to any Wordpress website.

Features:

* OpenMenu Custom Post Type
* Widgets: Restaurant Location / Specials / Cuisine Tag Cloud
* [openmenu] Shortcode
* Custom Functions
* Site wide setiings


== Detailed Features ==
OpenMenu Custom Post Type: 
	Create custom posts which are menus based off of your OpenMenu Format menu.  Choose what to display, how to display it and the plugin does the rest.
	
	Settings:
		OpenMenu Location (URL) - This is a required field that points to your OpenMenu Format menu
		
		Filters
			Menu Name to display: If your OpenMenu Format menu contains multiple menus (ex. Lunch / Dinner) you can choose which menu to display in your post by entering the menu name here.
			Group Name to display: If your OpenMenu Format menu contains multiple menu groups (ex. salads / deserts) you can choose which group to display in your post by entering the group name here.

		Restaurant Information: Stores basic information about the restaurant that is referenced by the menu. This is primarly used in scenarios where many restaurant menu's will be displayed.  Information, along with the excerpt, will be used to generate a single page of all menus.

		Cuisine Types: Define which cuisine type describes this restaurant.

Widgets:
	OpenMenu: Location  - Displays the restaurants location and hours
	OpenMenu: Specials  - Displays the menu items marked as special
	OpenMenu: Tag Cloud - A tag cloud for the cuisine types

Short code:
	[openmenu]
	
	Parameters:
		omf_url         = URL pointing to the OpenMenu Format menu
		display_type    = menu (only option currently available)
		menu_filter     = Will display only the menu name matching this filter
		group_filter    = Will display only the group name matching this filter
		display_columns = 1 | 2 - How many columns to display a menu in
	
		[defaults to OpenMenu Option setting]

	Samples: 
		[openmenu omf_url="http://openmenu.com/menu/sample"]
		[openmenu omf_url="http://openmenu.com/menu/sample" display_type="menu" display_columns="1"]

Custom Functions: 
	Display a location block: openmenu_location( post_id, title );
	Display a specials block: openmenu_specials( post_id, title );

Site Wide OpenMenu Settings:
	
	Look & Feel: 
		Display Type: What information will be displayed: Menu, Restaurant Information or Both
		How many columns: How many columns will be used to display a menu (1 or 2)
		Theme: only default is currently supported
	
	Wordpress Theme: 
		Show posts on homepage: Determines whether OpenMenu post types are displayed on the homepage blog post listing and in the RSS feed for the website.
		Hidesidebar: Forces the sidebar of a post to be hidden.  Gives the impression of a full-width page and may be more desirable when displaying menus.
		Width override: Attempts to force the width of the post to this amount.  Can be helpful for adjusting the display on troublesome themes.
		Menu background color: Set the background color the menu will display on (defaults to white - #fff)

Icon designed by Ben Dunkle, core designer for Wordpress.org. Website: http://field2.com - Contact ben@field2.com

== Installation ==

1. Unzip the openmenu.zip file
2. Upload the entire 'openmenu' folder to the '/wp-content/plugins/' directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Update Site Wide options through the Settings -> OpenMenu Options


== Frequently Asked Questions ==

= How do I get a menu in the OpenMenu Format so I can use this awesome plugin? =

Goto: http://OpenMenu.com/about.php and read about OpenMenu
Online Menu Creator: http://OpenMenu.com/creator

= How do I find out about updates to this plugin? =

Any updates will be posted on the OpenMenu - http://OpenMenu.com/blog

= Can I display menus for multiple restaurants? =

Yes.  This is the main reason for using custom post types.  This allows you to create an entire Wordpress website of restaurants and menus

= Can I add a menu to a page? =

Yes.  All you need to do is use the shortcode described above.  Very simple and can be added anywhere in a page in minutes.


== Screenshots ==
1. OpenMenu Overview
2. Adding/Editing a Menu
3. OpenMenu Options
4. Sample Rendered Menu


== Changelog ==

= 1.3 =
* Added Thumbnail images to menu listing
* Remove Setting link from plugin page (permission issue for some users)
* Update the location of the sample menu to http://openmenu.com/menu/sample
* Moved the styling of the OpenMenu tag to the OpenMenu theme stylesheet

= 1.2.1 =
* Fixed display issue with 2-column menu

= 1.2 =
* Updated to handle v1.2 of the OpenMenu Format
* Added group name filter to shortcode and OpenMenu posts

= 1.1.3 =
* Fixed issue where special characters were being double encoded and therefore not dispayed properly

= 1.1.2 =
* Updated OMF Reader class to handle server configuration that don't support simple_xml
* Fixed issue with handling missing information from custom post types / options

= 1.1.1 =
* Fixed issue with Dashboard display

= 1.1 =
* Added Menu / Menu Group Widget
* Added auto-detection of installed plugin folder (no longer assume /openmenu folder)
* Updated Restaurant Location widget to add the Include Hours setting
* Updated Specials Widget to include Menu Name filter
* Fixed issue where empty menu group (no menu items) caused crash

= 1.0.1 =
* Initial public release


== Upgrade Notice ==

= 1.0.1 =
* Initial public release