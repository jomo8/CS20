<?php

/**
 * Utility for HTML elements that have been built and the contents should not
 * be escaped.
 */

namespace EasyReader\HTML;

class HTMLElement {
    private string $contents;

    /** @param string $contents */
    public function __construct( string $contents ) {
        $this->contents = $contents;
    }

    /** @return string */
    public function toString(): string {
        return $this->contents;
    }

}