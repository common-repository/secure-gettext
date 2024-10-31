<?php
/*
Plugin Name: Secure Gettext
Description: Escapes translated text before it gets output.
Author: Alex Kirk
Author URI: https://alexander.kirk.at/
Version: 0.1
*/

class Secure_Gettext {
	// Regular Expression for matching an HTML tag
	const html_tag_pattern = '/<(\w+)(\s[^>]+)?\s*\/?>/';
	// Regular Expression for matching an attribute within an HTML tag
	const html_attribute_pattern = '/(\w+)\s*=/';

	/**
	 * Attaches to the gettext and gettext_with_context hook
	 *
	 * @param  string $translation translated string as provided by the hook
	 * @param  string $original    original string as provided by the hook
	 * @return string              escaped translated string
	 */
	public static function gettext( $translation, $original ) {

		// special debugging functionality only available to logged-in users
		if ( isset( $_GET['secure-gexttext'] ) && $_GET['secure-gexttext'] === 'show' && is_user_logged_in() ) {
			$translation = '[Escaped: ' . $translation . ']';
		}

		if ( strpos( $original, '<' ) === false ) {
			// does not contain a HTML tag, thus use esc_html()
			return esc_html( $translation );
		}

		return wp_kses( $translation, self::extract_allowed_tags( $original ) );
	}

	/**
	 * Attaches to the ngettext and ngettext_with_context hook
	 *
	 * @param  string $translation translated string as provided by the hook
	 * @param  string $single      original singular string as provided by the hook
	 * @param  string $plural      original plural string as provided by the hook
	 * @return string              escaped translated string
	 */
	public static function ngettext( $translation, $single, $plural ) {

		// the HTML in the translation can contain tags that appear either in the singular or the plural
		return self::gettext( $translation, $single . $plural );
	}

	/**
	 * The returned array will contain all tags with their attributes that are contained in the input HTML.
	 * The format of that array is one that wp_kses will accept.
	 *
	 * @param  string $html HTML text
	 * @return array        array with tags and attributes contained in the input HTML
	 */
	private static function extract_allowed_tags( $html ) {

		// default is no allowed HTML tags
		$tags = array();

		if ( preg_match_all( self::html_tag_pattern, $html, $html_tags, PREG_SET_ORDER ) ) {

			foreach ( $html_tags as $html_tag ) {
				$tag = strtolower( $html_tag[1] );

				// whitelist the HTML tag
				$tags[$tag] = array();

				if ( !empty( $html_tag[2] ) ) { // the HTML tag contains attributes

					if ( preg_match_all( self::html_attribute_pattern, $html_tag[2], $attributes, PREG_SET_ORDER ) ) {

						foreach ( $attributes as $attribute ) {
							// whitelist this attribute for the HTML tag from the outer loop
							$tags[$tag][strtolower( $attribute[1] )] = array();
						}
					}
				}
			}
		}

		return $tags;
	}
};

// setup the WordPress hooks
add_filter( 'gettext', array( 'Secure_Gettext', 'gettext' ), 1, 2 );
add_filter( 'gettext_with_context', array( 'Secure_Gettext', 'gettext' ), 1, 2 );
add_filter( 'ngettext', array( 'Secure_Gettext', 'ngettext' ), 1, 3 );
add_filter( 'ngettext_with_context', array( 'Secure_Gettext', 'ngettext' ), 1, 3 );
