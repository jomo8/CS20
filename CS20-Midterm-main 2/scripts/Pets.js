// Specifically NOT using DOMContentLoaded but rather the load event so that
// we can be sure that jQuery has also finished loading
// See https://developer.mozilla.org/en-US/docs/Web/API/Window/load_event
window.addEventListener( 'load', function () {

    const $petImages = $('.pet-card img');

    const ANIMATE_DURATION = 500;
    const animateWidth = ($img, widthPx) => {
        $img.animate(
            {'width': widthPx + 'px'},
            ANIMATE_DURATION
        );
    }
    // NOT using arrow functions so that `this` is the current card
    const onHoverEnter = function () {
        animateWidth($(this), 300);
    };
    const onHoverLeave = function () {
        animateWidth($(this), 250);
    };

    $petImages.hover(onHoverEnter, onHoverLeave);
} );