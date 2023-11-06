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

    // Form fields that we need to access specifically for extra validation
    const fldPhone = document.getElementById('number');
    const fldZip = document.getElementById('zipcode');

    // Add custom invalid messages for an input when the value is non-empty
    // but does not match the pattern
    const addValidation = (fldElem, nonEmptyError) => {
        fldElem.addEventListener('input', () => {
            fldElem.setCustomValidity('');
            fldElem.checkValidity();
        });
        fldElem.addEventListener('invalid', () => {
            if (fldElem.value === '') {
                fldElem.setCustomValidity('Please fill out this field.');
            } else {
                fldElem.setCustomValidity(nonEmptyError);
            }
        });
    };
    addValidation(fldPhone, 'Phone numbers should have 10 digits.');
    addValidation(fldZip, 'Zip code should have 5 digits.');

    const getSubmissionPopup = () => {
        const popupLabel = makeElement(
            'strong', 'Form submitted', [ 'submission-popup-label' ]
        );
        let popupMessage = 'Thank you for submitting an adoption application.';
        popupMessage += ' We will contact you in the next few days.'
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
    const adoptForm = document.getElementById('adopt-form');
    adoptForm.addEventListener('submit', onFormSubmit);
} );