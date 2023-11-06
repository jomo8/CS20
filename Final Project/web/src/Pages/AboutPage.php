<?php

/**
 * Used to create the output page
 */

namespace EasyReader\Pages;

use EasyReader\HTML\HTMLBuilder;
use EasyReader\HTML\HTMLElement;

class AboutPage extends SitePage {
    public function __construct() {
        parent::__construct( 'About' );
        $this->addStyleSheet( 'about-styles.css' );
    }

    private function getDaniel(): HTMLElement {
        return HTMLBuilder::element(
            'div',
            [
                HTMLBuilder::image('daniel.jpg'),
                HTMLBuilder::element('p',
            <<<END
            Daniel - From the Class of '24, Daniel is a Computer Science major in the Arts and Sciences School.
            Daniel first learned to code by teaching himself.
            END
                ),
            ],
            ['class' => 'er-about-bio']
        );
    }

    private function getEmily(): HTMLElement {
        return HTMLBuilder::element(
            'div',
            [
                HTMLBuilder::image('emily.jpg'),
                HTMLBuilder::element('p',
                <<<END
            Emily - From the Class of '23, Emily is a Computer Science major in the Arts and Sciences School. From Washington,
            her first concert was an ABBA cover band.
            END
                ),
            ],
            ['class' => 'er-about-bio']
        );
    }
    private function getJacob(): HTMLElement {
        return HTMLBuilder::element(
            'div',
            [
                HTMLBuilder::image('Jacob.jpg'),
                HTMLBuilder::element('p',
                <<<END
            From the Tufts Class of '25, Jacob is a Computer Science major and math minor from Lynn, MA who has
            goals of being a software engineer. Fun Fact: Jacob fell ~40 times when he first went snowboarding.
            END
                ),
            ],
            ['class' => 'er-about-bio']
        );
    }
    private function getJoey(): HTMLElement {
        return HTMLBuilder::element(
            'div',
            [
                HTMLBuilder::image('joey.png'),
                HTMLBuilder::element('p',
                <<<END
            From the Tufts Class of '25, Joey is a Computer Science major, from the Arts and Sciences School, Joey has ran
            the Boston Marathon and his middle is Robert.
            END
                ),
            ],
            ['class' => 'er-about-bio']
        );
    }

    protected function getBodyElements(): array {
        return [
            HTMLBuilder::element( 'h1', 'About' ),

            HTMLBuilder::element(
                'p',
                <<<END
                Welcome to Easy Reader! We are a reader that allows you to select and find information for your given text.
                Here you will be able to get information on any word, phrase, person through through our in-depth.
                Let EasyReader be your assistant as you parse through text. Useful for defining, context-analysis, translation and more!
                END
            ),
            HTMLBuilder::element(
                'p',
                <<<END
                With a subscription, you can save all of your previous text to be remembered across all your devices.
                Additionally, you will have access to an exclusive portal to submit bug reports, no ads, priority access to new features
                and feature requests - for users without a subscription this service is provided on an AS-IS BASIS with no guarantees of
                future reliability. Subscriptions start at $14.95/month(+tax).
                END
            ),
            HTMLBuilder::element('h2', 'Meet the Team!'),
            $this->getDaniel(),
            $this->getEmily(),
            $this->getJacob(),
            $this->getJoey(),
        ];
    }
}