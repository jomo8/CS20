document.addEventListener( 'DOMContentLoaded', function () {

    // elements
    const img1 = document.getElementById('slideshow-image-left');
    const img2 = document.getElementById('slideshow-image-mid');
    const img3 = document.getElementById('slideshow-image-right');

    const toggleBtn = document.getElementById('slideshow-toggle');

    // Image sources
    const imgURLs = [
        [ './images/cat3.jpg', './images/dog3.jpg', './images/cat4.jpg' ],
        [ './images/dog4.jpg', './images/cat5.jpg', './images/dog5.jpg' ],
        [ './images/cat6.jpg', './images/dog6.jpg', './images/cat7.jpg' ],
        [ './images/dog7.jpg', './images/cat8.jpg', './images/dog8.jpg' ]
    ];
    // HTML is coded to start with the first set of images
    let currImgIdx = 0;
    let slideshowRunning = true;

    const afterFadeOut = () => {
        // Stopping the slideshow means that the next image won't fade out,
        // but we will still finish fading in the current one
        currImgIdx = (currImgIdx + 1) % imgURLs.length;
        img1.src = imgURLs[currImgIdx][0];
        img2.src = imgURLs[currImgIdx][1];
        img3.src = imgURLs[currImgIdx][2];
        img1.classList.remove('faded');
        img2.classList.remove('faded');
        img3.classList.remove('faded');
    };
    const afterFadeIn = () => {
        // Don't continue if the slideshow is stopped
        if (slideshowRunning) {
            img1.classList.add('faded');
            img2.classList.add('faded');
            img3.classList.add('faded');
        }
    };

    // Only need to be checking a single image
    const onTransitionEnd = () => {
        if (img1.classList.contains('faded')) {
            // just faded out
            afterFadeOut();
        } else {
            afterFadeIn();
        }
    }

    const onButtonToggle = () => {
        if (slideshowRunning) {
            slideshowRunning = false;
            toggleBtn.textContent = 'Resume';
        } else {
            toggleBtn.textContent = 'Pause';
            slideshowRunning = true;
            // start the next fade out
            // for some reason the page is sometimes cached with faded
            onTransitionEnd();
        }
    };

    toggleBtn.addEventListener('click', onButtonToggle);

    img1.addEventListener('transitionend', onTransitionEnd);

    // started true, but also trigger the first fade
    // for some reason the page sometimes loads with the images already
    // faded out, don't die then
    onTransitionEnd();
    
} );