<?php

/**
 * Used to create the output page
 */

namespace EasyReader\Pages;

use EasyReader\HTML\{HTMLBuilder, HTMLElement, HTMLPage};
use EasyReader\AuthManager;
use EasyReader\Database;

class ReaderPage extends SitePage {
    public function __construct() {
        $this->isReader = true;
        parent::__construct( 'Reader' );
        $this->addScript( 'term-lookup.js' );
        $this->addStyleSheet( 'reader-styles.css' );
    }

    private const DEFAULT_TEXT = 'Select any word in this text to define it.';

    protected function onBeforePageDisplay(): void {
        if ( ( $_SERVER['REQUEST_METHOD'] ?? 'GET' ) !== 'POST'
            || !AuthManager::isLoggedIn()
            || !isset( $_POST['er-text'] )
        ) {
            return;
        }
        $db = new Database;
        $db->setCurrentUserText(
            AuthManager::getLoggedInUserId(),
            $_POST['er-text']
        );
        // Redirect to same page so that refresh won't trigger warnings about
        // post resubmission
        header('Location: ./index.php');
    }

    private function getStartingText(): string {
        if ( !AuthManager::isPremium() ) {
            return self::DEFAULT_TEXT;
        }
        $db = new Database;
        $text = $db->getCurrentUserText( AuthManager::getLoggedInUserId() );
        if ( $text === null ) {
            return self::DEFAULT_TEXT;
        }
        return $text;
    }

    protected function getBodyElements(): array {
        $isPremium = false;
        if ( AuthManager::isLoggedIn() ) {
            if ( AuthManager::isPremium() ) {
                $isPremium = true;
            } else {
                $this->addScript( 'account-upgrade.js' );
            }
        }
        return [
            HTMLBuilder::element( 'h1', 'Easy Reader' ),
            HTMLBuilder::element( 'div', 'Definition...', [ 'id' => 'er-def' ] ),
            HTMLBuilder::element(
                'form',
                [
                    HTMLBuilder::element( 'button', 'Search', [ 'type' => 'button', 'id' => 'er-search', 'class' => 'er-navButton' ] ),

                    $isPremium ?
                        HTMLBuilder::element(
                            'button',
                            'Save text (and reload)',
                            [ 'id' => 'er-text-save', 'class' => 'er-navButton'  ]
                        ) : '',
                    HTMLBuilder::element(
                        'textarea',
                        $this->getStartingText(),
                        [ 'id' => 'er-text', 'name' => 'er-text' ]
                    ),
                ],
                [
                    'id' => 'er-text-form',
                    'action' => './index.php',
                    'method' => 'POST',
                ]
            ),
        ];
    }
}