=== WP Custom Tags ===
Contributors: appsdevpk
Tags: tags, custom tags, components, web components, riotjs
Donate link: http://example.com/
Requires at least: 4.0
Tested up to: 4.8
Requires PHP: 5.6
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Create, re-use and share custom tags using riotjs library, helps to reduce development time.

== Description ==
Create, re-use and share custom tags using riotjs library, helps to reduce development time.

Now why use riotjs:

1 **Custom HTML tags** - allows you to create reusable HTML and JS components and create complex HTML views with a better readability
2 **Small size, minimalism, simplicity** - Riot.js is one of the most lightweight libraries, the syntax is as simple as possible and it claims to have 10-100 times faster API functions
3 **Modular design** - different components of the applications are independent and can be changed or removed without a major impact on the application itself. Thus the application is highly scalable.

**NOTE:** The share feature will be implemented in next version (soon). Using this feature you will be able to share your tags with other users of the plugin through a central repository of tags. And will also be able to use tags shared by the other users.

== Installation ==
1. Upload \"wpcustomtags\" to the \"/wp-content/plugins/\" directory.
2. Activate the plugin through the \"Plugins\" menu in WordPress.
3. Use \'[wpcEmbedTag tag=\"yourcustomtagname\" option1=\"value1\" option2=\"value2\"]\' in any page or post.
4. Here option1 and option1 will be passed to the riot tag as opts.option1 and opts.option2

== Frequently Asked Questions ==
= Why i cannot use the custom tag like 
Because wordpress editor does not support custom tags. The short code will be converted to the above mentioned syntax at runtime

= How to use 
[wpcEmbedTag tag=\"mytag\"]Here is the content you want to yield to your tag[/wpcEmbedTag]

== Screenshots ==
1. Template code for the tag.
2. CSS for the tag.
3. Script for creating the logic of the tag.
4. Server side code for the tag (optional)
5. Sample tag output

== Changelog ==
= 0.1 =
* Initial release.