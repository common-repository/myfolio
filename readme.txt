=== MyFolio===
Contributors: rixeo
Tags: portfolio

Requires at least: 3.0
Tested up to: 3.8
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

MyFolio is a Wordpress portfolio plugin

== Description ==

Quickly create your portfolio using custom post types and display them using a shortcode on any page defining the dimensions of each thumbnail.

Main Features:

* Custom Portfolio pst type
* Settings page to define border colors for portfolio display
* Automatic image resizing using inbuilt WordPress functions


We are still working to make this excellent

== Installation ==

1. Upload the myfolio folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Once the plugin is activated there will be a custom Post type called 'Folio' that will be created. Use this to add your portfolios
4. A settings page will be created under the WordPress Settings menu called 'MyFolio Settings'
5. Use the shortcode [my_folio] with the attributes image_width and image_height . For example [my_folio image_width='200' image_height = '200']

== Frequently Asked Questions ==

= My Portfolio does not show after I use the shortcode =

Make sure you have added a Portfolio under the 'Folio' menu


= The images are not resizing =

Make sure your server supports GD library that is used by WordPress to resize images


= The portfolio view has no color =

Make sure you set the color and the mouse over in the Settings Page



== Screenshots ==

1. Here's a screenshot of it in action


== Changelog ==

= 1.1 =
Added different types of views : circle and box

= 1.0 =
First version. Still working on more for it