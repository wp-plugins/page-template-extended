=== Page Template Extended ===
Contributors: thomasdk81, jacobask
Donate link: http://wp-plugins.dk/pte
Tags: templates, page template, is_page, page, parent, subpage, template hierarchy, danish
Requires at least: 2.5
Tested up to: 2.5.1
Stable tag: 1.1.1

Create templates for a specific page by its ID - like categories.
Subpages inherit templates from their parents.

== Description ==

It is possible to create templates for a specific page by its ID - like categories.
But the reason why I made this plugin, is so that subpages can inherit their parents template.
If the subpage hasn't a template assigned, the parent page template will be used if it exists.

The plugin works with an infinit number of subpages. If lets say a subpage is 6 levels down.
It will look at the first parent for the template, if it doesn't exist it will look one level up at the next parent.
So you can assign a template for the first level and fifth level. If you do so, level 1-4 would have the same page template and 5-6 a different page template.

Its planned to include the build-in template structure.

Italian language files by [Gianni Diurno](http://gidibao.net/ "Go to http://gidibao.net/")

== Installation ==

1. Upload the `page-template-ex` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Create a page template in our theme (page-*ID*.php).
4. If you like to translate it to your language, you will find the .pot file in folder.

== Frequently Asked Questions ==

= Why isn't this already built into WP? =

I don't know. Maybe the demand isn't there.