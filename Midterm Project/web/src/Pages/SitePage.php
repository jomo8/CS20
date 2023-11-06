<?php

/**
 * Used to create the output page
 */

namespace EasyReader\Pages;

use EasyReader\HTML\{HTMLBuilder, HTMLElement, HTMLPage};
use EasyReader\AuthManager;
use EasyReader\Database;

abstract class SitePage {
    private HTMLPage $page;
    private bool $loadedBodyContent = false;
    protected bool $isReader = false;

    /** @param string $pageTitle */
    protected function __construct( string $pageTitle ) {
        $this->page = new HTMLPage();
        $this->page->addHeadElement(
            HTMLBuilder::element( 'title', $pageTitle )
        );
        // Prevent trying to read a favicon that we don't have
        $this->page->addHeadElement(
            HTMLBuilder::element(
                'link',
                [],
                [ 'rel' => 'icon', 'href' => 'data:,' ]
            )
        );
        // Always add global-styles.css
        $this->addStyleSheet( 'global-styles.css' );

        // Body from getBodyElements() is added in getOutput() so that subclass
        // constructor code after calling this parent constructor can take
        // effect
    }

    protected function addScript( string $fileName ): void {
        $this->page->addHeadElement(
            HTMLBuilder::element(
                'script',
                [],
                [ 'src' => "/resources/{$fileName}" ]
            )
        );
    }
    protected function addStyleSheet( string $fileName ): void {
        $this->page->addHeadElement(
            HTMLBuilder::element(
                'link',
                [],
                [
                    'rel' => 'stylesheet',
                    'type' => 'text/css',
                    'href' => "/resources/{$fileName}",
                ]
            )
        );
    }

    // Some pages (Login and Logout) need to be able to do stuff session-related
    // *before* the sidebar is created, if nothing is needed just leave empty
    protected function onBeforePageDisplay(): void {
        // No-op by default
    }

    public function getOutput(): string {
        // Don't load multiple times
        if ( !$this->loadedBodyContent ) {
            $this->onBeforePageDisplay();
            $this->loadedBodyContent = true;

            $sideNav = $this->getSideNav();
            foreach ( $sideNav as $thing ) {
                $this->page->addBodyElement( $thing );
            }
            $this->page->addBodyElement(
                HTMLBuilder::element(
                    'div',
                    $this->getBodyElements(),
                    [ 'class' => 'body-content-wrapper' ]
                )
            );
        }
        return $this->page->getPageOutput();
    }

   private function getSideNav(): array {
        return [
            HTMLBuilder::element(
                'div',
                [
                    $this->buildUpperSideNav(),
                    $this->buildPrevSearches(),
                    $this->buildLowerSideNav()
                ],
                [ 'class' => 'side-nav' ]
            ),
        ];    
    }
    
    private function buildUpperSideNav(): HTMLElement {
        return
        HTMLBuilder::element(
            'div',
            HTMLBuilder::link(
                './index.php',
                HTMLBuilder::image('logo.svg', [ 'class' => 'er-logo'])
            ),
            ['id' => 'upper-side-nav']
        );
    }
    

    

    private function buildPrevSearches(): HTMLElement {
        if ( !AuthManager::isLoggedIn() || !$this->isReader ) {
            // Empty placeholder
            return HTMLBuilder::element(
                'div',
                [],
                ['id' => 'er-search-history-div']
            );
        }
        return HTMLBuilder::element(
            'div',
            [
                HTMLBuilder::element(
                    'strong',
                    'Term history'
                ),
                HTMLBuilder::element(
                    'div',
                    [],
                    AuthManager::isLoggedIn() ? [ 'id' => 'er-search-history' ] : []   
                ),
            ],
            ['id' => 'er-search-history-div']
        );
    }

    private function buildLowerSideNav(): HTMLElement {
        if ( AuthManager::isLoggedIn() ) {
            $loginOutLink = HTMLBuilder::link(
                './logout.php',
                HTMLBuilder::element(
                    'button',
                    'Log out',
                    [ 'class' => 'er-navButton' ]
                ),
                []
            );
        } else {
            $loginOutLink = HTMLBuilder::link(
                './login.php',
                HTMLBuilder::element(
                    'button',
                    'Log in',
                    [ 'class' => 'er-navButton' ]
                ),
                []
            );
        }

        if ( AuthManager::isLoggedIn() ) {
            $db = new Database;
            $email = $db->getAccountEmail( AuthManager::getLoggedInUserId() );
        } else {
            $email = 'Not logged in!';
        }
        $profile = HTMLBuilder::element(
            'p',
            [
                $email,
                HTMLBuilder::image( 'profile.png', [ 'id' => 'er-imgProfile'] ),
            ],
            ['id' => 'er-profLine']
        );
        $clearHistory = HTMLBuilder::element(
            'button',
            'Clear History',
            [ 'class' => 'er-navButton', 'id' => 'er-clear-history' ]
        );
        return HTMLBuilder::element(
            'div',
            [
                $profile,
                AuthManager::isLoggedIn() && $this->isReader ? $clearHistory : '',
            HTMLBuilder::link(
                './about.php',
                HTMLBuilder::element(
                    'button',
                    'About',
                    [ 'class' => 'er-navButton' ]
                ),
                [ 'class' => 'er-navButton' ]
            ),
            $loginOutLink
            ],
            [ 'id' => 'er-profile']
        );
    }

    abstract protected function getBodyElements(): array;
}