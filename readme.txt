=== Plugin Name ===
Contributors: akirk
Tags: gettext, security, escape, html, translation, po, mo
Requires at least: 2.0.11
Tested up to: 4.4
Stable Tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Escapes translated text before it gets output. This adds an extra security layer around translated text.

== Description ==

This plugin ensures that any text coming from a translation file (`.po` or `.mo`) is run through an escaping function before it is output to the user.

Escaping refers to the modification of the text so that special control characters do not have an effect (for example `<` in HTML).

Example: If an original text does not contain HTML, then a translated text should not be allowed to contain HTML either. Thus, an HTML link introduced by a translator should have no effect because this was not intended by the developer.

This plugin is in the proof-of-concept stage, it was created to test if the escaping of translated text can be handled in a general way, whether it severely affects the performance of a site, and if it breaks things.

For text without HTML, the text is sent through `esc_html()`, for text containing HTML tags, it is sent through `wp_kses()` which is provided with a list of allowed HTML tags and attributes, derived from the original string.

Thus this plugin tries to show a generic way of how to make sure that translated text is escaped. This is something that can eventually be ported to core.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/secure-gettext` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress

== Frequently Asked Questions ==

= Are you trying to convey that I cannot trust translations? =

Yes and no. The translation system on translate.wordpress.org is built on trust. Translation Editors will only approve strings that are just the translations of original text. This has worked very well so far. So indeed you can trust translations coming from there, for example through language packs.

On the other hand, translation files provide a potential vector for attackers to insert malicious content. This could be spam links, or even JavaScript code. If you receive a translation file from an untrusted source, then it might be unsafe.

This plugin doesn't fully protect you from such dangers, but makes it harder for potential attackers to insert their own content into translated texts.

= How can I see that the plugin is working? =

If the plugin is activated, in the best case it doesn't change anything visually. Translated text should behave the same way as before, there might be some escaping taking place (for example) something that had no HTML in the original text will have any HTML tags contained in the translated text be printed verbose.

In order to be able to verify if the plugin is in fact active, there is a special URL parameter that you can use when you view a page with a logged-in user: ?secure-gexttext=show

This mode will modify all screen text to be wrapped with a `[Escaped: <text>]`. This is purely for debugging functionality and might be removed in future.

== Screenshots ==

There are no screenshots because in the optimum case, you won't notice that the plugin is activated.

== Upgrade Notice ==

First release.

== Changelog ==

= 0.1 =
* Proof of concept
