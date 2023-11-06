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

    const daysNodeList = document.querySelectorAll('input[name="days"]');
    const days = Array.from(daysNodeList);

    const volunteerWaysNodeList = document.querySelectorAll('input[name="volunteer-way"]');
    const volunteerWays = Array.from(volunteerWaysNodeList);

    fldPhone.addEventListener('input', () => {
        fldPhone.setCustomValidity('');
        fldPhone.checkValidity();
    });
    fldPhone.addEventListener('invalid', () => {
        if (fldPhone.value === '') {
            fldPhone.setCustomValidity('Please fill out this field.');
        } else {
            fldPhone.setCustomValidity('Phone numbers should have 10 digits.');
        }
    });

    const anyChecked = (options) => options.some(opt => opt.checked);
    const requireAtLeastOne = (options, errorMsg) => {
        const inputCb = () => {
            if (anyChecked(options)) {
                options[0].setCustomValidity('');
            } else {
                options[0].setCustomValidity(errorMsg);
            }
        };
        options.forEach(opt => opt.addEventListener('input', inputCb));
        inputCb();
    };
    requireAtLeastOne(days, 'Please choose at least one day');
    requireAtLeastOne(volunteerWays, 'Please choose at least one way to help');

    const getSubmissionPopup = () => {
        const popupLabel = makeElement(
            'strong', 'Form submitted', [ 'submission-popup-label' ]
        );
        let popupMessage = 'Thank you for submitting a volunteering application.';
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
    const volunteerForm = document.getElementById('volunteer-form');
    volunteerForm.addEventListener('submit', onFormSubmit);
} );