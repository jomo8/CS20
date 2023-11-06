<?php

/**
 * Used to create the output page
 */

namespace EasyReader\Pages;

use EasyReader\HTML\{HTMLBuilder, HTMLElement, HTMLPage};
use EasyReader\AuthManager;
use EasyReader\Database;

class SubscriptionPage extends SitePage {
    private string $paymentError;

    public function __construct() {
        parent::__construct('Subscription');
        $this->addStyleSheet('subscription-styles.css');
        $this->addScript( 'subscription-validation.js' );
        $this->paymentError = '';
    }

    protected function onBeforePageDisplay(): void {
        if ( ( $_SERVER['REQUEST_METHOD'] ?? 'GET' ) === 'POST'
            && AuthManager::isLoggedIn()
        ) {
            $this->paymentError = $this->trySubmit();
        }
    }

    protected function getBodyElements(): array {
        return [
            HTMLBuilder::element(
                'div',
                [
                    HTMLBuilder::element('h4', 'Premium Plan Subscription - $14.95/mo.'),
                    HTMLBuilder::element('em', '(cancel at any time)'),
                    ...$this->buildRow(),
                ],
                [ 'class' => 'wrapper' ]
            ),
        ];
    }

    private function buildRow(): array {
        return [
            HTMLBuilder::element(
                'div',
                [
                    HTMLBuilder::element('div', [...$this->getMainDisplay()], ['class' => 'card', 'id' => 'card1']),
                    HTMLBuilder::element('div', [...$this->buildCard2()], ['class' => 'card', 'id' => 'card2'])
                ],
                [ 'class' => 'row' ]
            ),
        ];
    }


    private function buildCard1(): array {
        return [
            HTMLBuilder::element(
                'div', [
                    HTMLBuilder::element(
                        'div',
                        HTMLBuilder::element('h4', 'Payment information'),
                        ['id' => 'pay-form-header']
                    ),
                    $this->getForm(),
                ], ['class' => 'pay-form']
            ),
            HTMLBuilder::element('em', 'Your payment method will be charged $14.95 + tax'),
        ];
    }

    private function buildCard2(): array {
        $features = [
            'No ads',
            'Save previous searches during session',
            'Save previous documents',
            'Priority access to new features',
        ];
        $tickIcon = HTMLBuilder::image('tick-icon.png', [ 'id' => 'tick-icon']);
        $features = array_map(
            function ( $feature ) use ( $tickIcon ) {
                return HTMLBuilder::element(
                    'p',
                    [ $tickIcon, HTMLBuilder::element( 'label', $feature ) ]
                );
            },
            $features
        );
        return [
            HTMLBuilder::element(
                'div',
                [
                    HTMLBuilder::element('h3', 'Your subscription includes:'),
                    ...$features,
                ],
                ['class' => 'sub-details']
            ),
            HTMLBuilder::element(
                'div',
                [
                    HTMLBuilder::element('strong', 'Billing details:'),
                    HTMLBuilder::element('p', 'By clicking Submit Payment you are agreeing to our Easy Reader Premium Plan Terms, Easy Reader Terms of Use, Privacy Policy and for Easy Reader to charge your payment method for $14.95 (+ tax if applicable) for your monthly subscription to Easy Reader Premium Plan Subscription. Easy Reader will automatically charge your payment method monthly until you elect to cancel by selecting cancel membership in My Account.', ['id' => 'bill-details']),
                ],
            )
        ];
    }

    private function getForm(): HTMLElement {
        return HTMLBuilder::element(
            'form',
            $this->getFormFields(),
            [
                'id' => 'payment-frm',
                'action' => './subscription.php',
                'method' => 'POST',
            ]
        );
    }
    private function getFormFields(): array {
        // Only used when already logged in
        $db = new Database;
        $emailPrefill = $db->getAccountEmail( AuthManager::getLoggedInUserId() );
        $fields = [
            HTMLBuilder::element('label', 'Account email', ['for' => 'pay-email']),
            HTMLBuilder::input(
                'email',
                [ 'id' => 'pay-email', 'value' => $emailPrefill,
                    'readonly' => true ]
            ),
            HTMLBuilder::element('br'),
            HTMLBuilder::element(
                'label',
                'Name on card',
                [ 'for' => 'pay-name' ]
            ),
            HTMLBuilder::element('br'),
            HTMLBuilder::input(
                'text',
                ['id' => 'pay-name' ]
            ),
            HTMLBuilder::element('br'),
            HTMLBuilder::element(
                'label',
                'Card number',
                [ 'for' => 'pay-cardNum' ]
            ),
            HTMLBuilder::element('br'),
            HTMLBuilder::input(
                'text',
                [ 'id' => 'pay-cardNum', 'int-length' => 16]
            ),
            $this->buildPayLine(),
            HTMLBuilder::element('br'),
            HTMLBuilder::element(
                'label',
                'Zip/Postal Code',
                [ 'for' => 'pay-zip' ]
            ),
            HTMLBuilder::element('br'),
            HTMLBuilder::input(
                'text',
                [ 'id' => 'pay-zip', 'int-length' => 5 ]
            ),
        ];
        if ( $this->paymentError != '' ) {
            $fields[] = HTMLBuilder::element('div', [], ['class' => 'half-space']);
            $fields[] = HTMLBuilder::element(
                'p',
                [
                    HTMLBuilder::element('label', '*', ['id' => 'er-star']),
                    $this->paymentError
                ],
                [ 'class' => 'error' ]
            );
            $fields[] = HTMLBuilder::element('div', [], ['class' => 'half-space']);
        } else {
            $fields[] = HTMLBuilder::element('div', [], ['class' => 'space']);
        }
        $fields[] = HTMLBuilder::element(
            'button',
            'Submit Payment',
            [ 'type' => 'submit', 'class' => 'er-navButton', 'id' => 'pay-submitButton']
        );
        return $fields;
    }

    private function buildPayLine():HTMLElement {
        return HTMLBuilder::element(
            'div',
            [
                HTMLBuilder::element(
                    'div',
                    [HTMLBuilder::element(
                        'label',
                        'Exp. date',
                        [ 'for' => 'pay-exp' ]
                    ),
                    HTMLBuilder::element('br'),
                    HTMLBuilder::input( 'month', ['id' => 'pay-exp'] )
                ],
                    ['class' => 'pay-cell']
                ),
                HTMLBuilder::element(
                    'div',
                    [HTMLBuilder::element(
                        'label',
                        'CVV',
                        [ 'for' => 'pay-cvv' ]
                    ),
                    HTMLBuilder::element('br'),
                    HTMLBuilder::input(
                        'text',
                        ['id' => 'pay-cvv', 'int-length' => 3]
                    )], ['class' => 'pay-cell'])
            ],
            ['id' => 'pay-line']
        );
    }

    private function trySubmit(): string {
        $db = new Database;
        $accountEmail = $_POST['pay-email'];
        if ( $accountEmail === '' ) {
            return 'Missing account email';
        }
        $accountInfo = $db->getAccount( $accountEmail );
        if ( $accountInfo === null ) {
            return 'Email not associated with an account';
        }

        $name = $_POST['pay-name'];
        $cardNum = $_POST['pay-cardNum'];
        $expDate = $_POST['pay-exp'];
        $CVV = $_POST['pay-cvv'];
        $zip = $_POST['pay-zip'];

        if ($name === '') {
            return 'Missing cardholder name';
        } else if ($cardNum === '') {
            return 'Missing card number';
        } else if (strlen($cardNum) !== 16) {
            return 'Invalid card number';
        } else if ($expDate === '') {
            return 'Missing expiration date';
        } else if ($CVV === '') {
            return 'Missing CVV code';
        } else if (strlen($CVV) !== 3) {
            return 'Invalid CVV code';
        }  else if ($zip === '') {
            return 'Missing Zip/Postal code';
        }
        $db->markUserPremium( $accountEmail );
        return '';
    }

    private function getMainDisplay(): array {
        if ( !AuthManager::isLoggedIn() ) {
            return [ $this->getNotLoggedInError() ];
        }
        $isPost = ( $_SERVER['REQUEST_METHOD'] ?? 'GET' ) === 'POST';
        if ( $isPost && $this->paymentError === '') {
            return [
                HTMLBuilder::element( 'p', 'Subscription successful' ),
            ];
        }
        return [ ...$this->buildCard1() ];
    }

    private function getNotLoggedInError(): HTMLElement {
        return HTMLBuilder::element(
            'div',
            'ERROR: Not logged in to an account!',
            [ 'class' => 'er-error' ]
        );
    }

}