<?php

/**
 * Used to create the output page
 */

namespace EasyReader\Pages;

use EasyReader\HTML\{HTMLBuilder, HTMLElement, HTMLPage};
use EasyReader\AuthManager;
use EasyReader\Database;

class SignUpPage extends SitePage {
    private string $signUpError;

    public function __construct() {
        parent::__construct( 'SignUp' );
        $this->addStyleSheet( 'signup-styles.css' );
        $this->signUpError = '';
    }

    protected function getBodyElements(): array {
        return [
            HTMLBuilder::element(
                'div',
                [
                    HTMLBuilder::element( 'h1', 'Sign up' ),
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
            $this->signUpError = $this->trySubmit();
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
        $submitError = $this->signUpError;
        if ( $this->signUpError !== '' ) {
            return [ $this->getForm() ];
        }
        return [
            HTMLBuilder::element(
                'p',
                'Account successfully created!'
            ),
        ];
    }

    private function trySubmit(): string {
        $email = $_POST['er-email'];
        $pass = $_POST['er-password'];
        $passConfirm = $_POST['er-password-confirm'];
        if ( $email === '' ) {
            return 'Missing email';
        } else if ( $pass === '' || $passConfirm === '' ) {
            return 'Missing password';
        } else if ( $pass !== $passConfirm ) {
            return 'Passwords do not match';
        }
        $db = new Database;
        if ( $db->accountExists( $email ) ) {
            return 'Email already taken';
        }
        $hash = md5( $pass );
        $id = $db->createAccount( $email, $hash );
        AuthManager::loginSession( $id );
        return '';
    }

    private function getAlreadyLoggedInError(): HTMLElement {
        return HTMLBuilder::element(
            'div',
            'ERROR: Already logged in to an account!',
            [ 'class' => 'oc-error' ]
        );
    }

    private function getForm(): HTMLElement {
        return HTMLBuilder::element(
            'form',
            $this->getFormFields(),
            [
                'id' => 'oc-create-account',
                'action' => './signup.php',
                'method' => 'POST',
            ]
        );
    }

    private function getFormFields(): array {
        $fields = [
            HTMLBuilder::element(
                'label',
                'Email:',
                [ 'for' => 'er-email' ]
            ),
            HTMLBuilder::element(
                'input',
                [],
                [ 'type' => 'email', 'id' => 'er-email', 'name' => 'er-email' ]
            ),
            HTMLBuilder::element( 'br' ),
            HTMLBuilder::element(
                'label',
                'Password:',
                [ 'for' => 'er-password' ]
            ),
            HTMLBuilder::element(
                'input',
                [],
                [ 'type' => 'password',
                    'id' => 'er-password',
                    'name' => 'er-password' ]
            ),
            HTMLBuilder::element( 'br' ),
            HTMLBuilder::element(
                'label',
                'Confirm Password:',
                [ 'for' => 'er-password-confirm' ]
            ),
            HTMLBuilder::element(
                'input',
                [],
                [ 'type' => 'password',
                    'id' => 'er-password-confirm',
                    'name' => 'er-password-confirm' ]
            ),
        ];
        if ( $this->signUpError != '' ) {
            $fields[] = HTMLBuilder::element('div', [], ['class' => 'half-space']);
            $fields[] = HTMLBuilder::element(
                'p',
                $this->signUpError,
                [ 'class' => 'er-error ' ]
            );
            $fields[] = HTMLBuilder::element('div', [], ['class' => 'half-space']);
        } else {
            $fields[] = HTMLBuilder::element('div', [], ['class' => 'space']);
        }
        $fields[] = HTMLBuilder::element(
            'button',
            'Create account',
            [ 'type' => 'submit', 'id' => 'er-create-account-submit' ]
        );
        return $fields;
    }
}