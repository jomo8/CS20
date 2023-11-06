<?php

/**
 * Used to create the output page
 */

namespace EasyReader\Pages;

use EasyReader\HTML\{HTMLBuilder, HTMLElement, HTMLPage};
use EasyReader\AuthManager;
use EasyReader\Database;

class LoginPage extends SitePage {
    private string $loginError;

    public function __construct() {
        parent::__construct( 'Login' );
        $this->addStyleSheet( 'form-styles.css' );
        $this->loginError = '';
    }

    protected function getBodyElements(): array {
        return [
            HTMLBuilder::element(
                'div',
                [
                    HTMLBuilder::element( 'h1', 'Login' ),
                    ...$this->getMainDisplay(),
                ],
                [ 'class' => 'center-table' ]
            ),
        ];
    }

    protected function onBeforePageDisplay(): void {
        if ( ( $_SERVER['REQUEST_METHOD'] ?? 'GET' ) === 'POST'
            && !AuthManager::isLoggedIn()
        ) {
            $this->loginError = $this->trySubmit();
        }
    }

    private function getMainDisplay(): array {
        $isPost = ( $_SERVER['REQUEST_METHOD'] ?? 'GET' ) === 'POST';
        if ( !$isPost ) {
            if ( AuthManager::isLoggedIn() ) {
                return [ $this->getAlreadyLoggedInError() ];
            }
            return [ $this->getForm() ];
        }
        if ( $this->loginError !== '' ) {
            return [ $this->getForm() ];
        }
        return [
            HTMLBuilder::element(
                'p',
                'Account login successful!'
            ),
            HTMLBuilder::element( 'br' ),
            HTMLBuilder::element( 'br' ),
            HTMLBuilder::element( 'br' ),
            HTMLBuilder::link(
                './index.php',
                HTMLBuilder::element(
                    'button',
                    'Go Home',
                    [ 'class' => 'er-form-redirect' ]
                )
            ),
        ];
    }

    private function trySubmit(): string {
        $email = $_POST['er-email'];
        $pass = $_POST['er-password'];
        if ( $email === '' ) {
            return 'Missing email';
        } else if ( $pass === '' ) {
            return 'Missing password';
        }
        $db = new Database;
        $accountInfo = $db->getAccount( $email );
        if ( $accountInfo === null ) {
            return 'Email not associated with an account';
        }
        $hash = md5( $pass );
        if ( $hash !== $accountInfo->user_pass_hash ) {
            return 'Incorrect password';
        }
        AuthManager::loginSession( $accountInfo->user_id );
        return '';
    }

    private function getAlreadyLoggedInError(): HTMLElement {
        return HTMLBuilder::element(
            'div',
            'ERROR: Already logged in to an account!',
            [ 'class' => 'er-error' ]
        );
    }

    private function getForm(): HTMLElement {
        return HTMLBuilder::element(
            'form',
            $this->getFormFields(),
            [
                'id' => 'er-login',
                'action' => './login.php',
                'method' => 'POST',
            ]
        );
    }

    private function getFormFields(): array {
        $fields = [
            HTMLBuilder::element( 'br' ),
            HTMLBuilder::element(
                'label',
                'Email:',
                [ 'for' => 'er-email' ]
            ),
            HTMLBuilder::input(
                'email',
                [ 'id' => 'er-email', 'placeholder' => 'email' ]
            ),
            HTMLBuilder::element( 'br' ),
            HTMLBuilder::element( 'br' ),
            HTMLBuilder::element(
                'label',
                'Password:',
                [ 'for' => 'er-password' ]
            ),
            HTMLBuilder::input(
                'password',
                [ 'id' => 'er-password', 'placeholder' => 'password' ]
            ),
            HTMLBuilder::element( 'br' ),
            HTMLBuilder::element( 'br' ),
        ];
        if ( $this->loginError != '' ) {
            $fields[] = HTMLBuilder::element('div', [], ['class' => 'half-space']);
            $fields[] = HTMLBuilder::element(
                'p',
                $this->loginError,
                [ 'class' => 'er-error ' ]
            );
            $fields[] = HTMLBuilder::element('div', [], ['class' => 'half-space']);
        } else {
            $fields[] = HTMLBuilder::element('div', [], ['class' => 'space']);
        }
        $fields[] = HTMLBuilder::element(
            'button',
            'Login',
            [ 'type' => 'submit', 'id' => 'er-login-submit', 'class' => 'er-form-button' ]
        );
        return $fields;
    }
}