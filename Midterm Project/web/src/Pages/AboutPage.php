<?php

/**
 * Used to create the output page
 */

namespace EasyReader\Pages;

use EasyReader\HTML\HTMLBuilder;

class AboutPage extends SitePage {
    public function __construct() {
        parent::__construct( 'About' );
    }

    protected function getBodyElements(): array {
        return [
            HTMLBuilder::element( 'h1', 'About' ),
            // JACOB WRITE THE CONTENTS HERE:
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
                Additionally, you will have access to an exclusive portal to submit bug reports and feature requests - for
                users without a subscription this service is provided on an AS-IS BASIS with no guarantees of future reliability.
                END                
            ),
            HTMLBuilder::element('h2', 'Meet the team'),
            HTMLBuilder::rawElement(
'div',
<<<END
<h1>Testing</h1>
END
            ),
        ];
    }
}