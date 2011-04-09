=== Kalin's PDF Creation Station ===
Contributors: kalinbooks
Tags: PDF, document, export, print, pdf, creation
Requires at least: 3.0
Tested up to: 3.1
Stable tag: trunk

Build highly customizable PDF documents from any combination of pages and posts, or add a link to any page to download a PDF of that post.
== Description ==

<p>
Build highly customizable PDF documents from any combination of pages and posts, or add a link to any page to download a PDF of that post.                  
</p>
<p>
Kalin's PDF Creation Station will add two menus to your WordPress admin. One under tools and one under settings. 
</p>
<p>In the tools menu you will be able to build PDF documents from any combination of pages and posts. Select any or all pages and posts from your site, then add a custom title page, end page and custom headers. Adjust font sizes, file names, or insert information such as timestamps, excerpts and urls through the use of shortcodes. Finally, adjust page order through a simple drag and drop interface. All created PDF files will display in a convenient list for you to delete, download or link to.
</p>
<p>
In the settings menu you will be able to setup options for a link that can be automatically added to some or all pages and posts. This link will point to an automatically generated PDF version of that page. Most of the same customization options are available here that are available in the creation tool, like title page and font size, as well as the option to fully customize the link itself. On individual page/post edit pages you will be able to override the default link placement so you can show links on some pages and not on others. PDF files are saved to your server so they only need to be created once, reducing server load compared to other PDF generation plugins that create a new PDF every time the link is clicked. The PDF file is automatically deleted when a page or post is edited, so the PDF always matches the page.
</p>
<p>
Plugin by Kalin Ringkvist at http://kalinbooks.com/
</p>
<p>
Plugin URL: http://kalinbooks.com/pdf-creation-station/
</p>
<p>
Bugs: http://kalinbooks.com/pdf-creation-station/known-bugs/ If you have any problems please comment on this page or email Kalin at kalin@kalinflash.com and I'll do my best to figure out your issues.
</p>
<p>
Future features: http://kalinbooks.com/pdf-creation-station/pdf-creation-possible-features/ If you have feature requests or are interested in my plans for PDF Creation Station
</p>


== Installation ==

1. Unzip `kalins-pdf-creation-station.zip` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Find the PDF Creation Station menu under 'tools' and begin creating custom PDF documents of your website. Or go into the PDF Creaction Station menu under 'settings' and begin setting up the options for automatic individual page generation.

Note: probably requires PHP 5.2 and WordPress 3.0.

== Frequently Asked Questions ==

= Where do I find instructions and help? =

In both the settings and tool pages you can find help in the built-in wordpress help dropdown menu in the upper right side of the screen. If you continue to have problems, feel free to make a comment at http://kalinbooks.com/pdf-creation-station/. Try to include as much specific information as you can, especially if you think you've found a bug.

= Font, href or align tags don't work in inserted HTML. =

Make sure to use double quotes instead of single quotes when inserting arbitrary HTML attributes because of a bug with the core PDF creation engine (TCPDF).


== Screenshots ==

1. A portion of the creator tool that creates custom PDF documents for large portions of your website.
2. A different shot of the creator tool with a couple sub-menus expanded.
3. Settings for PDF creation for individual posts. Note the link customization options.
4. A shot of the box that is added to the page/post edit page to control link placement.

== Changelog ==

= 0.7 =
* First version. Beta. Includes basic functionality for tool menu and settings menu including page order, title page, include images, font size, ajaxified interface, shortcodes, etc.

= 0.8 =
* Added a create now button for someone who had trouble getting the jquery page-ordering popup to work.

= 0.9 =
*Moved some initialization functions into kalins_pdf_init() so that they are only run in the admin.
*Added new security check to make sure the plugin pages are only being run from within wordpress.
*Added 'default' option to page/post edit box so you aren't forced to make a permanent choice when saving a page/post.
*Added checkbox at the bottom of settings page to turn off the plugin's deactivation routine.
*changed default link placement to 'none' so that links are not added to pages/posts until the user authorizes it

= 0.9.1 =
*Changed all code to direct, and/or create the kalins-pdf folder inside the uploads directory instead of placing the PDF files in the plugin directory to squash the bug where files were deleted upon plugin upgrade.

= 0.9.2 =
*Fixed a PHP error thrown on the Menus page when in debug mode. Got rid of warnings for previous upgrade problem.

= 1.0 =
Added [post_permalink] shortcode. Also added "Use post slug for PDF filename" and "Show on home, category, tag and search pages" options on settings page. Changed the clunky character count to word count, which should now function more accurately.

= 1.1 =
Bug fix. I broke the PDF creation popup with v 1.0 and had to make an emergency fix.

= 1.2 =
removed testing alerts

= 2.0 =
*Added support for custom post types
*moved the code identifying the default PDF directory and URL into a few constants at the top of kalins-pdf-creation-station.pdf, so that hackers can easily change them to whatever they want. Added example code that can be un-commented to change the PDF directory to use the base domain of your site instead of the wordpress uploads directory.
*Fixed minor bug where 'reset defaults' on the settings page wasn't refreshing the 'post slug' and 'show on home' checkboxes
*Added "create all" button on settings page
*Added "automatically generate PDFs on publish and update" option on settings page
*changed blockquote code so it uses the 'pre' tag because it was the only way to get TCPDF to actually display anything since it doesn't want to render blockquotes or tables properly
*added post_excerpt code to use "wp_trim_excerpt", which doesn't appear to be functioning anymore -- then changed to manually extract 250 characters from the page content
*added option to run other plugin shortcodes to both settings and tool pages
*added option to convert embedded youtube videos into a link to that video
*added 'format' parameter to all time shortcodes for total custom date/time formatting
*added 'length' parameter to the post_excerpt shortcode to set character count of the excerpt

= 2.0.1 =
*Bug fix. This plugin no longer destroys all other admin help menus.

= 2.0.2 =
*Bug fix. PDFs now properly generate when using 'quick edit' on posts when 'auto generate' is turned on.

= 3.0 =
*upgraded TCPDF engine. This should improve image handling and also fixes the blockquotes issue, so blockquotes no longer need to use a monospaced font
*added option to automatically construct a Table of Contents page in the creator tool
*added post_meta shortcode for post's custom fields
*added option on Tool page to turn off automatic page breaks between posts
*added ability for hackers to translate/change the word 'page' to whatever they want
*added option to run other plugin content filters
*added post category(s) shortcode
*added post tags shortcode
*added option to convert Vimeo videos (both object and iframe style embeds)
*added Ted Talk video link conversion option
*YouTube link conversion now works for iframe style embeds as well as objects
*added ability for hackers to change the order of the post list on the tool page
*added post comments shortcode. Includes easy way for PHP coders to fully customize the display
*added post parent shortcode
*added post thumbnail shortcode
*added call to set_time_limit(0) to help prevent timeouts on very large PDF builds


== Upgrade Notice ==

= 0.7 =
First version. Beta. Use with Caution.

= 0.8 =
No point in upgrading unless you have problems with the Create PDF! button

= 0.9 =
Slight overall blog performance increase. Minor security improvement. New 'Use default' option on page/post edit screen. New feature: disable database cleanup upon plugin deactivation

= 0.9.1 =
Bug fix: After this, your PDF files should not disappear after future plugin upgrades.

= 0.9.2 = 
Not a terribly important release.

= 1.0 =
I broke this release. Move on to next version.

= 1.1 =
Bug fix. Added a couple new little features. Character count is now Word Count. You will need to update your settings.

= 1.2 =
removed testing alerts

= 2.0 =
A few new features. Default formatting on Date/time shortcodes changed a little with the new formatting possibilities.

= 2.0.1 =
My sincerest apologies to everyone who has been wondering what the hell happened to their help menus.

= 2.0.2 =
Bug fix. PDFs now properly generate when using 'quick edit' on posts when 'auto generate' is turned on.

= 3.0 =
Some new shortcodes, features and other improvements. New Table of Contents feature. Better image handling, improved integration with other plugins. Tool page less likely to fail/timeout on large/complex PDF generation.


== About ==

If you find this plugin useful please pay it forward to the community, or visit http://kalinbooks.com/ and check out some of my science fiction or political writings.
Thanks to Marcos Rezende's Blog as PDF and Aleksander Stacherski's AS-PDF plugins which provided a good starting point.

