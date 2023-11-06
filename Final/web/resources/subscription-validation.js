document.addEventListener( 'DOMContentLoaded', function () {

    // Form fields that we need to access specifically for extra validation
    const fldCvv = document.getElementById('pay-cvv');
    const fldZip = document.getElementById('pay-zip');
    const fldNum = document.getElementById('pay-cardNum');

    const addErrorMsg = ( fld, msg ) => {
        fld.addEventListener('input', () => {
            fld.setCustomValidity('');
            fld.checkValidity();
        });
        fld.addEventListener('invalid', () => {
            if (fld.value === '') {
                fld.setCustomValidity('Please fill out this field.');
            } else {
                fld.setCustomValidity(msg);
            }
        });
    };
    addErrorMsg( fldCvv, 'CVVs should have 3 digits' );
    addErrorMsg( fldZip, 'Zip codes should have 5 digits' );
    addErrorMsg( fldNum, 'Credit cards should be 16 digits' );

} );