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

    const getSubmissionPopup = () => {
        const popupLabel = makeElement(
            'strong', 'Form submitted', [ 'submission-popup-label' ]
        );
        let popupMessage = 'Thank you for contacting us.';
        popupMessage += ' We will respond in the next few days.'
        const popupText = makeElement(
            'span', popupMessage, [ 'submission-popup-text' ]
        );
        const popupBtn = makeElement(
            'button', 'Return to home', [ 'submission-popup-btn' ]
        );
        const popupBtnWrapper = makeElement(
            'div', [popupBtn], [ 'submission-popup-btn-wrapper' ]
        );
        const hr = makeElement('hr', '');
        const popupDialog = makeElement(
            'div',
            [ popupLabel, popupText, hr, popupBtnWrapper],
            [ 'submission-popup-dialog' ]
        );
        const popupWrapper = makeElement(
            'div', [popupDialog], [ 'submission-popup-wrapper' ]
        );
        // When the button gets clicked, make overall wrapper emit `click`
        popupBtn.addEventListener('click', () => {
            const clickEvent = new Event('click');
            popupWrapper.dispatchEvent(clickEvent);
        });
        return popupWrapper;
    }

    const onFormSubmit = (e) => {
        e.preventDefault();
        // Validation is done with the individual fields, if we get here we
        // are already validated
        const popup = getSubmissionPopup();
        document.querySelector('body').append(popup);
        popup.addEventListener('click', () => {
            location.assign('./index.html');
        });
    };
    const contactForm = document.getElementById('contact-form');
    contactForm.addEventListener('submit', onFormSubmit);
} );