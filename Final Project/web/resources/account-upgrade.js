// Alert the user that they do not have premium...
document.addEventListener( 'DOMContentLoaded', function () {

    /**
     * Create a new element for the given tag with the specified content. If
     * it is a string, it is set as the *text content*, otherwise it should be
     * an array, and each element is appended.
     *
     * @param {string} tagName 
     * @param {string|Element[]} contents 
     * @param {string[]} classes
     * @return {Element}
     */
    const makeElement = (tagName, contents, classes) => {
        const elem = document.createElement(tagName);
        if (typeof contents === 'string') {
            elem.textContent = contents;
        } else {
            elem.append(...contents);
        }
        (classes || []).forEach((c) => elem.classList.add(c));
        return elem;
    };

    const getPremiumPopup = () => {
        const popupLabel = makeElement(
            'strong', 'Upgrade to premium', [ 'er-premium-popup-label' ]
        );
        let popupMessage = 'You have not purchased premium yet! ';
        popupMessage += 'You do not have access to any of the premium features.'

        const subscribeLink = document.createElement('a');
        subscribeLink.href = './subscription.php';
        subscribeLink.text = 'Subscribe to premium!';
        const popupText = makeElement(
            'span', [popupMessage], [ 'er-premium-popup-text' ]
        );
        const popupBtn = makeElement(
            'button', 'Close', [ 'er-premium-popup-btn' ]
        );
        const popupBtnWrapper = makeElement(
            'div', [popupBtn], [ 'er-premium-popup-btn-wrapper' ]
        );
        const hr = makeElement('hr', '');
        const popupDialog = makeElement(
            'div',
            [ popupLabel, popupText, subscribeLink, hr, popupBtnWrapper],
            [ 'er-premium-popup-dialog' ]
        );
        const popupWrapper = makeElement(
            'div', [popupDialog], [ 'er-premium-popup-wrapper' ]
        );
        // When the button gets clicked, make overall wrapper emit `click`
        popupBtn.addEventListener('click', () => {
            const clickEvent = new Event('click');
            popupWrapper.dispatchEvent(clickEvent);
        });
        return popupWrapper;
    }

    const popup = getPremiumPopup();
    document.querySelector('body').append(popup);
    popup.addEventListener('click', () => {
        popup.remove();
    });
} );