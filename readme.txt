=== Scout Units List ===
Contributors: duzymaju
Tags: scouts, zhp, zhr, wosm, wagggs
Requires at least: 3.0.0
Tested up to: 4.7
Stable tag: 0.1.1
License: GNU GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Units management system for scout web pages.

== Description ==

This plugin allows you to create a structure of units which are dependent on main one and then present it inside page or post using variety of shortcodes equipped with customizable templates.

= Showing units list =

Add `[sul-units-list id="X"]` shortcode, where `X` is an ID of a base unit. You can use also other attributes:

* `current` (boolean, `false` as a default value) - allows to show/hide current unit on a list,
* `levels` (integer, `1` as a default value) - allows to define number of levels with dependent units.

To define your own template create `scout-units-list` directory in your current theme directory and then create there `UnitsList.phtml` or `UnitsList-X.phtml` file where `X` is an ID of a base unit. To customize template of dependent units level you have to create in the same location `UnitsListLevel.phtml` or `UnitsListLevel-X.phtml` file as well. To know how to access units data please check default templates in `View/Shortcodes` directory.

= Showing persons list =

Add `[sul-persons-list id="X"]` shortcode, where `X` is an ID of a base unit.

To define your own template create `scout-units-list` directory in your current theme directory and then create there `PersonsList.phtml` or `PersonsList-X.phtml` file where `X` is an ID of a base unit. To know how to access units data please check default template in `View/Shortcodes` directory.

= Future plans =

1. Finish default shortcode templates, add default CSSes.
2. Add possibility to define which unit types are needed by a shortcode.
3. Add a shortcode which implements a map with selected units marked on it.
4. Integrate with some plugins used to publish orders (selectable orders list).
5. Units/persons versioning.
6. Configurable types/subtypes lists and dependencies.
7. Sorting/searching on units/positions lists in admin panel.

== Installation ==

1. Upload plugin files to the "/wp-content/plugins/scout-units-list" directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. That's all - plugin is fully operational.

== Changelog ==

= 0.1 =
The first version of plugin.
