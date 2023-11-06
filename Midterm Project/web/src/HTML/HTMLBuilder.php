<?php

/**
 * Utility for building HTML elements
 */

namespace EasyReader\HTML;

class HTMLBuilder {
    /**
     * Escape raw strings, but keep instances of HTMLElement which were already
     * built
     *
     * @param string|HTMLElement $original
     * @return string|HTMLElement
     */
    private static function maybeEscape( $original ) {
        if ( is_string( $original ) ) {
            return htmlspecialchars( $original );
        }
        return $original;
    }

	/**
	 * Should a tag be self-closing?
	 *
	 * @param string $tag
	 * @return bool
	 */
	private static function isVoidElement( string $tag ): bool {
		switch ( $tag ) {
			case "hr":
			case "br":
				return true;
		}
		return false;
	}

	/**
	 * Contents are escaped if needed
	 *
	 * @param string $tag
     * @param string|HTMLElement|(string|HTMLElement)[] $contents any strings
	 *   are escaped, if there is only one item it doesn't need to be in an
	 *   array
	 * @param array $attributes
	 * @return HTMLElement
	 */
	public static function element(
		string $tag,
		$contents = [],
		array $attributes = []
	): HTMLElement {
		if ( !is_array( $contents ) ) {
			// using (array) casts doesn't work
			$contents = [ $contents ];
		}
		return self::rawElement(
			$tag,
            array_map( [ __CLASS__, 'maybeEscape' ], $contents ),
			$attributes
		);
	}
	/**
	 * Raw element, contents used as-is
	 *
	 * @param string $tag
     * @param string|HTMLElement|(string|HTMLElement)[] $contents any strings
	 *   are NOT escaped, if there is only one item it doesn't need to be in an
	 *   array
	 * @param array $attributes
	 * @return HTMLElement
	 */
	public static function rawElement(
		string $tag,
		$contents = [],
		array $attributes = []
	): HTMLElement {
		if ( !is_array( $contents ) ) {
			// using (array) casts doesn't work
			$contents = [ $contents ];
		}
		$res = "<$tag";
		foreach ( $attributes as $name => $rawValue ) {
			if ( $rawValue === true ) {
				// boolean attribute just needs to be there
				$res .= " $name";
			} else {
				$useValue = htmlspecialchars( $rawValue, ENT_QUOTES );
				$res .= " $name=\"$useValue\"";
			}
		}
		if ( !$contents && self::isVoidElement( $tag ) ) {
			return new HTMLElement( "$res />" );
		}
        $contentStr = '';
        foreach ( $contents as $child ) {
            if ( $contentStr !== '' ) {
                $contentStr .= "\n";
            }
            if ( is_string( $child ) ) {
                $contentStr .= $child;
            } else {
                // Must be HTMLElement
                $contentStr .= $child->toString();
            }
        }
		return new HTMLElement( "{$res}>{$contentStr}</{$tag}>" );
	}

	/**
	 * Create a <select> for the given option values, with the first one
	 * selected
	 *
	 * @param string[] $options
	 * @param array $attribs
	 * @return HTMLElement
	 */
	public static function select(
		array $options,
		array $attribs = []
	): HTMLElement {
		$firstOpt = array_shift( $options );
		$firstOptElem = self::element(
			'option',
			$firstOpt,
			[
				'value' => $firstOpt,
				'selected' => true,
			]
		);
		$optElements = array_map(
			fn( $o ) => self::element( 'option', $o, [ 'value' => $o ] ),
			$options
		);
		array_unshift( $optElements, $firstOptElem );
		return self::element( 'select', $optElements, $attribs );
	}

	/**
	 * Creates a <tr> for the given cells, which can each be either an
	 * HTMLElement for the contents, or a string, or an HTMLElement that is
	 * already a <td> if attributes were desired.
	 *
	 * @param (string|HTMLElement)[] $cells
	 * @param array $attribs
	 * @return HTMLElement
	 */
	public static function tableRow(
		array $cells,
		array $attribs = []
	): HTMLElement {
		$makeCell = function( $contents ) {
			if ( $contents instanceof HTMLElement
				// REQUIRES 8.0
				&& str_starts_with( $contents->toString(), '<td' )
			) {
				return $contents;
			}
			return self::element( 'td', [ $contents ] );
		};
		return self::element( 'tr', array_map( $makeCell, $cells ), $attribs );
	}

	/**
	 * Shortcut to create an <img> with the given source, should be the name
	 * of a file within ./resources/images/
	 *
	 * @param string $imgName
	 * @param array $attribs
	 * @return HTMLElement
	 */
	public static function image(
		string $imgName,
		array $attribs = []
	): HTMLElement {
		$attribs['src'] = "./resources/images/$imgName";
		return self::element( 'img', [], $attribs );
	}

	/**
	 * Shortcut to create a <a> with the given target
	 *
	 * @param string $target
     * @param string|HTMLElement|(string|HTMLElement)[] $contents any strings
	 *   are escaped, if there is only one item it doesn't need to be in an
	 *   array
	 * @param array $attributes
	 * @return HTMLElement
	 */
	public static function link(
		string $target,
		$contents,
		array $attribs = []
	): HTMLElement {
		$attribs['href'] = $target;
		return self::element('a', $contents, $attribs);
	}
}