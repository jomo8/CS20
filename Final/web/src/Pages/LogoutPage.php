<?php

/**
 * Used to create the output page
 */

namespace EasyReader\Pages;

use EasyReader\HTML\HTMLBuilder;
use EasyReader\AuthManager;

class LogoutPage extends SitePage {
    public function __construct() {
        parent::__construct( 'Logout' );
        $this->addStyleSheet( 'form-styles.css' );
    }

    protected function onBeforePageDisplay(): void {
        AuthManager::logOut();
    }

    protected function getBodyElements(): array {
        return [
            HTMLBuilder::element(
                'div',
                [
                    HTMLBuilder::element( 'h1', 'Log out' ),
                    HTMLBuilder::element(
                        'p',
                        'Log out successful'
                    ),
                ],
                [ 'class' => 'center-table' ]
            ),
        ];
    }

}