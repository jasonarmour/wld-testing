=== Parent Theme ===
Requires PHP: 7.4.0
Requires at least: WordPress 5.2.0
Tested up to: WordPress 5.2.0
Version: 5.6.7


== Description ==
The basis for the development of themes in the company WLD. Contains functions that simplify development and
help to comply with development standards.

= Required Plugins =
Advanced Custom Fields PRO
Classic Editor

= Recommended Plugins =
Gravity Forms
Intuitive Custom Post Order
Regenerate Thumbnails
WordPress Importer
WP Retina 2x
Yoast Duplicate Post
Yoast SEO

= Recommended Local Plugins =
Query Monitor plugin for WordPress - almost certainly helps to avoid mistakes and understand what is happening
Rewrite Rules Inspector - sometimes can be very useful
WordPress Exporter - you can export individual pages made locally
User Switching plugin for WordPress - you can switch to any user

= Instructions =
1) Functionality
This theme does not need to be modified directly, you need to create a child theme and work in it.

You can override all functions in the functions.php file of the child theme or if there is a lot of code
then break it into files, and put them in the inc directory and connect in functions.php.
You can override the classes by putting them in the inc folder, the classes in this folder will be loaded
automatically in the function.php of the parent (this) theme.

Everything related to the child theme needs to be written to the theme-functions.php file at this moment
loaded all the functionality of the parent theme.

Once again, the functions and classes of the parent theme are not available in the functions.php of the child theme,
so we use theme-functions.php, but if you need to redefine something in
parent use functions.php.

2) Flexible Content
We adhere to the block model, that is,
everything on the page between the header and the basement is divided into separate blocks.
Blocks should be clearly separated in layout. For each block, you will need to create a template file,
Group Field for him and will use the Flexible Content template.
The Flexible Content template selects from all Group Fields those with a header starting with "FC:"
collects from them one field of type Flexible Content.

That is, you need to create a Group Field with a heading like "FC: Text & Image",
and create all the fields for the block. The block template itself will then be called "text-image.php" and
should be in the templates / flexible-content folder.

For more details on how a block template should be formed, see templates / flexible-content / -demo.php
In the same folder there are several examples of templates, Groups Field for them can be viewed in the data folder.

3) Hat, basement and general settings
For global settings, there is a settings page, in which tabs are divided into logical blocks.
As a rule, everything fits there. settings are usually called wld_ {tab_name} _ {settings_name}
But if there are a lot of settings then create a subpage.
Examples of caps and basements, with useful functions can be found in this thread.

4) Blog
All sites have a blog, although sometimes it may be called in some other way "News".
In general, this is posts_page, it needs to be configured. For its display,
and for all other archives, the archive.php template is responsible
As a rule, you do not need to make changes to it, if there is no design for it, it is simply styled by a typesetter.

5) Not a Page
This is a template for a page that serves to combine subpages, but it is not available,
it is redirected to the first child.

6) Formatting
In general, you need to adhere to WP standards
https://make.wordpress.org/core/handbook/best-practices/coding-standards/
To do this, use:
- editorconfig - a little help with formatting
- eslint-config-wordpress - will prompt in JS
	- https://github.com/WordPress-Coding-Standards/eslint-config-wordpress
- phpcs.xml - very much needed, will not let you deviate from the standard
	- https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
	- https://kellenmace.com/set-up-php-codesniffer-in-phpstorm-with-wordpress-coding-standards/
- PHPStorm-WP-WLD.xml Code Style settings for PHPStorm

7) Last but important, if you make changes, please describe in Changelog what has changed and change the version.
If something is fixed a little, change the last digit.
If you added something or fixed a bug, change the second one.
Well, if something breaks compatibility, then change the first one.
Then everyone will know what has changed.


== Changelog ==

= 5.6.8 =
* Fixed by

= 5.6.7 =
Fixed gf acf filed empty select option, replaced the constant check with a class, it seems more correct.

= 5.6.6 =
Fixed skipped a constant that was removed, replaced with development environment

= 5.6.5 =
Fixed auto title level if first title empty
Fixed admin bar padding for new WP version
Fixed GF optimization minimize scripts and output header over buffer
Fixed GF new version scripts enqueue
Removed WP styles variables
Admin bar is now always enabled for roles that support content editing
Changed header and footer from app
* Changes related to moving to WPEngine
Removed the installation, since now the site is created from a template
Disabled cache
Changed constants

= 5.6.4 =
Fixed WLD Fields if link is not array
Fixed Global Site Tag sprintf invalid count arguments
Fixed get_post_id_taking_into_preview for $_GET['page_id'] variable
Fixed set_editor_styles if Gutenberg enabled
Disabled check Gutenberg required for theme
Removed favicon from customizer
Changed assets url, get_theme_file_uri replaced by get_stylesheet_directory_uri

= 5.6.3 =
Fixed attributes for images, backgrounds and logo
Set jpeg quality 100

= 5.6.2 =
* Updated GF load optimization
* Fixed GF Multiple IDs
* Fixed social links

= 5.6.1 =
* Fixed GF Multiple IDs

= 5.6.0 =
* New By
* Logo loading eager for header
* Logo added sizes attribute
* jQuery from theme
* GF load optimization
* Fixed GF multiple ids
* Disabled WC block styles

= 5.5.12 =
* Revert acf-json in child theme
* Rename site-name folder to child-theme
* Fixed acf fields request before acf/init
* Fixed gravity forms in cache don`t include js
* Fixed vimeo video functions
* Added 1440 endpoint in backgrounds

= 5.5.11 =
* Removed wptexturize from acf content
* Removed classname field for Flexible Template
* Enabled title for social links in footer

= 5.5.10 =
* Fixed wld_get_the_excerpt remove the space if the number of circumcision fell on it
* Add post_password_required for Flexible Content template

= 5.5.9 =
* Removed the restriction on the location of the menu and the restriction on the depth of the menu

= 5.5.8 =
* Fixed nested wld_loop()

= 5.5.7 =
* Fix woo quantity allow_in_product_page

= 5.5.6 =
* Fixed wld_remove_filter_for_class
* Fixed Fatal PHP error if use get_post_permalink() with null $post
* Code review WLD_Tax

= 5.5.5 =
* Fixed found errors related to the Gravity Forms update.
* Fixed saving the "All" option for the relationship field.
* Fixed the display in the preview.
* Fixed incompatibility with WPML.
* Fixed admin bar print footer scripts.
* Updated the log.
* Updated the cache.
* Updated the font loader script.
* Removed inline SVG sprite support.
* Added an iframe to the allowed tags for KSES.
* Added theme.url JS variable.

= 5.5.4 =
* Added WLD_WC_Video_Thumbnail
* Added a size parameter for cover background
* Added We Accept card icons in cart
* Fix theme cache now clear after updated options, menus or ACF fields
* Fix WLD_Yoast_SEO_Score_Fix clear all buffers
* Fix Woo thank_you_background

= 5.5.3 =
* Fix WLD_Fix_GF_Multiple_IDs for GF init before jQuery in footer
* Fix twice QM empty image size error log
* Add js for new way fonts loader
* Performance improvements
* Remove Dashicon cache
* Remove wp_clean_themes_cache
* Experiment with partial theme caching

= 5.5.2 =
* Fix regenerate thumbnails with cover crop
* PHPCS exclude "Disallow Short Ternary" and "Disallow Short Array Syntax"

= 5.5.1 =
* Fix WLD_Yoast_SEO_Score_Fix. Due to the fact that the rating was performed first, there was no H1 on the pages

= 5.5.0 =
* Added Woo Default Helpers
* Added WLD_GA_GTM, now Google Analytics and Google Taf Manager can be added in the site settings, on the API tab
* Added the ability to display the cart amount in the menu link for the cart
* Redesigned WLD_ACF_Google_Maps_API to use a key in JS
* Removed duplicate ACF JSON, header & footer as they are in the child theme

= 5.4.8 =
* Added WLD_Yoast_SEO_Score_Fix
* Added WLD_Fix_GF_Multiple_IDs

= 5.4.7 =
* GTM field added
* Fix GF scripts to footer

= 5.4.6 =
* WLD_Importer & WLD_XLSX_CSV now saves the processed lines to a file, so as not to do it again if the data file has not changed
* WLD_Importer added log level "progress", this is an alias for "init", just in some cases it is more descriptive
* WLD_List_Table fixed filtering, now you can specify zero as a value for the filter
* Add wld_put_file_content function to write data correctly to a file

= 5.4.5 =
* Fix missing space between fields in search due to this word stuck together

= 5.4.4 =
* Fix loop now it will not print a wrapper if no fields are filled in the group
* Fix XMLReader open fix static method

= 5.4.3 =
* Fix title always returned HTML even when the title is empty

= 5.4.2 =
* Now SVG images are displayed inline and through a symbol to avoid duplicate IDs and other problems
* SVG dimensions are now calculated based on the viewBox if dimensions are not specified
* Fix social links title field - after updating, you need to re save the theme settings
* Applies shortcodes to the title field output
* Added wld_get_file_content function to get file content correctly

= 5.4.1 =
* Add WLD_GTM
* JQuery connected from google CDN to footer
* Gravity Forms scripts moved to footer
* Deleted ChromePhp
* Deleted PHPStorm-WP-WLD.xml
* Fixed extra quote in wld_wrap
* Recaptcha now does not allow submitting a form without JS
* Defaults to a more consistent look(wld_the, esc_attr_e...)
* All files are auto-formatted PHPStorm PHP WordPress Style

= 5.4.0 =
* Add background object fit
* Add wld_image_init_get_bg_fit_endpoints hook for change background object fit endpoints
* Add images sizes in admin select
* Add title h1 in blog archive
* Add post state for sitemap page
* Fix single post translators text

= 5.3.1 =
* Add init admin notice
* Add html5 full support
* Fix WP_Error log message
* Fix social link gaps
* Fix margin admin bar in mobile
* Fix display sub total price in menu cart
* Fix phpstorm notice

= 5.3.0 =
* Add wld_the_as & wld_get_as to get images from the background field and vice versa
* Add wld_has to check multiple fields
* Strip all tags in except
* Woo add new image size for menu cart
* Other small fix

= 5.2.0 =
* Add title level select. By default, it is disabled and enabled for SeoDog projects. You can disabled auto level for the filter "wld_acf_title_only_auto_level".
* Restore image sizes attributes for lazy load images.
* Set "thumbnail" background size in helper.js

= 5.1.2 =
* Fix set_video_url_in_full_gallery_size nullable error
* Add wld_importer_get_rows for change import rows
* Add support is_sub_loop for options

= 5.1.1 =
* Fix WLD pagination
* Fix vimeo video URL

= 5.1.0 =
* Add WLD_Fields class & functions for new template logic. See -new-demo.php
* ACF helper allow "on" in titles
* Add WLD_User_Avatar
* Add ACF Background Field
* Add ACF Relationship "All" checkbox
* Add ACF WYSIWYG Height field
* Update js helpers(default title & open add field, inactivate field for FC:, update types logic)
* Fix ACF Contact Link input type

= 5.0.5 =
* Add wld_remove_filter_for_class function
* Add & update woo classes
* Fix ACF Social Links Field - warning undefined index value

= 5.0.4 =
* Added the ability to specify the display fields by filter wld_flex_display
* Added SVG support
* Added title field in ACF Social Links field
* Added woocommerce started files
* Log 'shutdown' is hung on WP action 'shutdown' instead of register_shutdown_function
* Admin bar can now be minimized even if there are errors
* Fix QM errors in the admin bar
* Fix ACF Contact Link Field + remove icons
* Now all ACF pages are available, just hidden from the menu

= 5.0.3 =
* Added the ability to specify the block class by filter wld_flex_block_class
* Added the ability to specify the body class by filter wld_flex_body_class
* Added add_theme_support( 'html5' );

= 5.0.2 =
* New WLD_Log: identical messages are recorded once a day, PHP shutdown errors are logged, hash & prefix in filenames

= 5.0.1 =
* Remove pagination plugin, use wld_the_pagination()
* Extend & fix WLD_Importer
* Add WLD_Admin_Notices, WLD_List_Table

= 5.0.0 =
* ACF Local JSON
* Added more stringent typing
* New helpers added, but somewhat removed
* Changing the order of properties in WLD_ACF_Flex_Content::the_content()
* Remove WLD_ACF_Map_Zoom_Field, WLD_Schema
* Add WLD_Importer, WLD_Mail, WLD_XLSX_CSV
* Fix readme
* Fix import posts for install
* Fix .editorconfig
* Fix image crop size

= 4.0.3 =
* Add support shortcode in title and links
* Fix inspections, cleanup code

= 4.0.2 =
* Add replaced [] link title in WLD_ACF_Contact_Link_Field
* Add WLD_Log
* Disable WLD TAX default term
* Disable wp_fatal_error_handler

= 4.0.1 =
* Add seo title hook
* Added functionality for setting multiple replacements in a title field
* Fix wld_get_the_title
* Exclude JS files from phpcs

= 4.0.0 =
* New field types "Social Links" and "Copyright"
* New logic for author & seo links see
* Change function wld_the_socials_links for new field type
* Remove old social links json
* Refactoring helpers function, some function is deprecated see inc/deprecated.php
* Refactoring header & footer
* Replace "-" to space for labels in WLD_Tax & WLD_CPT
* Disable wpautop for editor
* Disable WLD_Schema if WPSEO plugin enabled
* Fix inspections, cleanup code

= 3.0.1 =
* Change All on SEO to WP SEO
* Disable forms field, if GF not activate
* Fix social link JSON
* Favicon allow all types
* Admin bar check can
* Fix map filter
* Add wpautop param in wld_get_copyright
* Fix remove ver in scripts and styles

= 3.0.0 =
* June 11, 2019
* Rename theme
* Rename global js variables
* Remove author
* Remove license
* Change textdomain & update translate files

= 2.0.2 =
* June 04, 2019
* Fix WLD_Tax default taxonomy labels
* Fix media links in WLD_Extend_WPLink
* Add default value $class in wld_the_titles functions
* Add typing

= 2.0.1 =
* May 22, 2019
* Fix WLD_Tax default taxonomy & labels
* Add a page to main query if rewrite rules have worked but have not found page(404).
* Fix name: wld_file_link_format >>> _hook_file_link_format

= 2.0.0 =
* May 21, 2019
* The default templates are changed and there is not yet a mechanism to support them from version to version.
  I decided that it is better if you need to update the theme and break the templates,
  then before that you can transfer them to the child theme
* wld_the_socials_links function has changed under the new standard and will break the layout in the old theme;
  when upgrading to this version, you can override the function in the child theme
* WLD_YouTube::get_image changed, you can now set the image before getting it from YouTube
* Add default text domain in phpcs.xml
* Fix ACF Map Zoom default zoom value
* Remove data-btn from Extend WP Link
* Refactoring
* Requires WordPress 5.2.0

= 1.1.1 =
* May 12, 2019
* Update ACF JSON: add footer logo, set phone type, new standards social links and favicon allow svg
* Fix replace wrappers in flex content
* Add thumbnails support in registration cpt
* Fix labels and add special labels arguments in registration cpt
* Add $attr in image output functions
* Update wld_the_socials_links for new standards
* Other small fix and format

= 1.1.0 =
* May 07, 2019
*
  !!! Attention !!!
  I took <main> from templates to the header and footer, and there may be problems during the update.
  This is done so that if you need to insert anything on all pages in <main>, you do not have to override templates.
  And updated default templates blog for the latest standard
  This may break default templates(archive, single, page, 404, search).
  To fix this you will need to copy the old default templates to a child theme.

* Fix version
* Add WLD_DISABLE_COMMENTS for enable comments menu
* Hide bar if not installed Query Monitor
* Check pages.xml and "Hello world!" before installation
* Activate QM during installation

= 1.0.9 =
* May 02, 2019
* Fix seodogs link
* Update header.php

= 1.0.8 =
* April 24, 2019
* Fix js undefined acf for extend link
* Fix replace prefix title acf group
* Change acf json

= 1.0.7 =
* April 4, 2019
* Load google map js if get map
* Add custom class to contact link

= 1.0.6 =
* March 29, 2019
* Fix admin output value acf form field
* Fix invisible reload error first render
* Fix notice in acf tools
* Format code for WLD_YouTube
* Change 2x bg logic, Fix default sizes, Use WLD_Images in helpers

= 1.0.5 =
* March 28, 2019
* Fix wld_flex_replace_wrappers filter

= 1.0.4 =
* March 26, 2019
* Fix sql by get forms field
* Fix WLD_Extend_WPLink return format
* Change prefix for hook functions(wld_ to _hook_). This for clear wld_ IDE code completion
* Add setting for install by Gravity and Retina Plugins
* Remove gform_confirmation_anchor filter
* Show admin bar in development and local sites
* Set Gravity Forms license key

= 1.0.3 =
* March 25, 2019
* Fix ACF Map Zoom JS
* Fix ACF replace flex-content wraps
* Add wld_the_title for output titles
* Add wld.pot for translate
* Moved .editorconfig to themes folder
* Exclude app folder from phpcs.xml

= 1.0.2 =
* March 19, 2019
* Fix ACF add helper title format
* Fix ACF export flex-content
* Fix ACF replace flex-content headers and wraps
* Fix login styles logo size
* Add link_color in login styles
* Add const WLD_SAMPLES for hide sample local json data

= 1.0.1 =
* Released: March 18, 2019

Initial release
