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
                [ 'src' => "./resources/{$fileName}" ]
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
                    'href' => "./resources/{$fileName}",
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
                HTMLBuilder::image('logo.png', [ 'class' => 'er-logo'])
            ),
            ['id' => 'upper-side-nav']
        );
    }

    private function buildPrevSearches(): HTMLElement {
        $isPremium = false;
        if ( AuthManager::isLoggedIn() ) {
            $db = new Database;
            $userId = AuthManager::getLoggedInUserId();
            if ( $db->isUserPremium( $userId ) ) {
                $isPremium = true;
            }
        }
        if ( !$isPremium || !$this->isReader ) {
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
                    'label',
                    'Term history',
                    ['id' => 'er-term-history-label']
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
            $loginOutLinks = [ HTMLBuilder::link(
                './logout.php',
                HTMLBuilder::element(
                    'button',
                    'Log out',
                    [ 'class' => 'er-navButton' ]
                ),
                []
            ) ];
        } else {
            $loginOutLinks = [
                HTMLBuilder::link(
                    './login.php',
                    HTMLBuilder::element(
                        'button',
                        'Log in',
                        [ 'class' => 'er-navButton' ]
                    ),
                    []
                ),
                HTMLBuilder::link(
                    './signup.php',
                    HTMLBuilder::element(
                        'button',
                        'Sign up',
                        [ 'class' => 'er-navButton' ]
                    ),
                    []
                ),
            ];
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
                HTMLBuilder::element(
                    'span',
                    $email,
                    // Long emails are truncated, include the full as a tooltip
                    ['id' => 'er-profEmail', 'title' => $email]
                ),
                HTMLBuilder::image( 'profile2.png', [ 'id' => 'er-imgProfile'] ),
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
                AuthManager::isPremium() && $this->isReader ? $clearHistory : '',
                HTMLBuilder::link(
                    './about.php',
                    HTMLBuilder::element(
                        'button',
                        'About',
                        [ 'class' => 'er-navButton' ]
                    )
                ),
                ...$loginOutLinks
            ],
            [ 'id' => 'er-profile']
        );
    }

    abstract protected function getBodyElements(): array;
}