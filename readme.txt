=== Scout Units List ===
Contributors: duzymaju
Tags: scouts, zhp, zhr, wosm, wagggs
Requires at least: 3.0.0
Tested up to: 4.7
Stable tag: 0.4.1
License: GNU GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Units management system for scout web pages.

== Description ==

This plugin allows you to create a structure of units which are dependent on main one and then present it inside page or post using variety of shortcodes equipped with customizable templates.

= Showing units list =

Add `[sul-units-list id="X"]` shortcode, where `X` is an ID of a base unit. You can use also other attributes:

* `class` (string) - allows to add CSS class,
* `current` (boolean, `false` as a default value) - allows to show/hide current unit on a list,
* `levels` (integer, `1` as a default value) - allows to define number of levels with dependent units,
* `types` (empty as a default value) - allows to define list of types (separated by comma) which have to be showed.

To define your own template create `scout-units-list` directory in your current theme directory and then create there `UnitsList.phtml`, `UnitsList-Y.phtml` or `UnitsList-X.phtml` file where `Y` is a type and `X` is an ID of a base unit. To customize template of dependent units level you have to create in the same location `UnitsListLevel.phtml`, `UnitsListLevel-Y.phtml` or `UnitsListLevel-X.phtml` file as well. To know how to access units data please check default templates in `View/Shortcodes` directory.

= Showing persons list =

Add `[sul-persons-list id="X"]` shortcode, where `X` is an ID of a base unit. You can use also other attributes:

* `class` (string) - allows to add CSS class.

To define your own template create `scout-units-list` directory in your current theme directory and then create there `PersonsList.phtml`, `PersonsList-Y.phtml` or `PersonsList-X.phtml` file where `Y` is a type and `X` is an ID of a base unit. To know how to access units data please check default template in `View/Shortcodes` directory.

= Things to do before stable version release =

1. Add a shortcode which implements a map with selected units marked on it.
2. Configurable types/subtypes lists and dependencies.

= Future plans =

1. Add for each unit a list of changes (versions).
2. Add for each user a list of position changes (versions).
3. Sorting/searching on units/positions lists in admin panel.

== Installation ==

1. Upload plugin files to the "/wp-content/plugins/scout-units-list" directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. That's all - plugin is fully operational.

== Changelog ==

= 0.1 =
The first version of plugin.

= 0.2 =
Add migrations. Integrate with orders added into database as posts (configurable post category). Add versioning of units and persons.

= 0.3 =
Improve shortcodes for units/persons. Add API.

= 0.4 =
Add multiple order categories.
