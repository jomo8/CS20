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
        if ( !AuthManager::isLoggedIn() ) {
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
        return [
            HTMLBuilder::element( 'h1', 'Easy Reader' ),
            HTMLBuilder::element( 'div', 'Definition...', [ 'id' => 'er-def' ] ),
            HTMLBuilder::element( 'button', 'Search', [ 'id' => 'er-search' ] ),
            HTMLBuilder::element(
                'form',
                [
                    HTMLBuilder::element(
                        'textarea',
                        $this->getStartingText(),
                        [ 'id' => 'er-text', 'name' => 'er-text' ]
                    ),
                    AuthManager::isLoggedIn() ?
                        HTMLBuilder::element(
                            'button',
                            'Save text',
                            [ 'id' => 'er-text-save' ]
                        ) : '',
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