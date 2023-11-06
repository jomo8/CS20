function toggleMenu() {
    document.querySelector('.menu-icon').classList.toggle('active');
    document.querySelectorAll('.small-text').forEach((element) => {
        element.classList.toggle('active');
    });
    document.querySelector('.boxes').classList.toggle('active');
    if (document.querySelector('.top-container.move-out')) {
        document.querySelector('.top-container.move-out').classList.toggle('active');
    };
    document.querySelectorAll('.big-text').forEach((element) => {
        element.classList.toggle('active');
    });
    document.querySelector('.container.projects').classList.toggle('active');
    document.querySelector('#contact .center-box').classList.toggle('active');
}



document.getElementById('home').classList.add('active');
document.querySelectorAll('.home-link').forEach((element) => { 
    element.addEventListener('click', () => navigateTo('home', false));
});
document.querySelectorAll('.about-link').forEach((element) => {
    element.addEventListener('click', () => navigateTo('about', false));
});
document.querySelectorAll('.projects-link').forEach((element) => {
    element.addEventListener('click', () => navigateTo('projects', false));
});
document.querySelectorAll('.contact-link').forEach((element) => {
    element.addEventListener('click', () => navigateTo('contact', false));
    console.log(element);
});
document.querySelector('.contact-link').addEventListener('click', () => navigateTo('contact', true));

let currPage = 'home';
let phone = false;
function navigateTo(page) {
    if (window.innerWidth < 844) {
        phoneMethod(page);
    } else {
        desktopMethod(page);
    }
}




function desktopMethod(page, contactButton) {
    // document.querySelector('.top-nav').style.opacity = '1';

    if (!contactButton) {
        toggleMenu();
    }

    // Remove active class from all sections
    document.querySelectorAll('.page-selection').forEach((section) => {
      section.classList.remove('active');
    });
    
    // Add active class to the targeted section
    const section = document.getElementById(page);
    section.classList.add('active');

    // Removing animations from the current page.
    if (currPage == 'about') {
        document.querySelector('.top-nav-border').classList.remove('animation');
        document.querySelector('.tmp-top').classList.remove('move-out');
        document.querySelector('.top-container').classList.remove('move-out');
        document.querySelector('.right-nav').classList.remove('move-out');
        document.querySelector('.bottom-nav').classList.remove('move-out');
        document.querySelector('.contact-tab').classList.remove('move-out');
    } else if (currPage == 'projects') {
        document.querySelector('.top-nav-border').classList.remove('animation');
        document.querySelector('.tmp-top').classList.remove('move-out');
        document.querySelector('.top-container').classList.remove('move-out');
        document.querySelector('.right-nav').classList.remove('move-out');
        document.querySelector('.bottom-nav').classList.remove('move-out');
        document.querySelector('.contact-tab').classList.remove('move-out');
        document.querySelector('.top-container').classList.remove('fade');

    } else if (currPage == 'contact') { 
        document.querySelector('.top-nav-border').classList.remove('animation');
        document.querySelector('.tmp-top').classList.remove('move-out');
        document.querySelector('.top-container').classList.remove('move-out');
        document.querySelector('.right-nav').classList.remove('move-out');
        document.querySelector('.bottom-nav').classList.remove('move-out');
        document.querySelector('.contact-tab').classList.remove('move-out');
        document.querySelector('.top-container').classList.remove('fade');
    }

    // Adding animation for the next page.
    if (page == 'about') {
        document.querySelector('.top-nav-border').classList.add('animation');
        document.querySelector('.tmp-top').classList.add('move-out');
        document.querySelector('.top-container').classList.add('move-out');
        document.querySelector('.right-nav').classList.add('move-out');
        document.querySelector('.bottom-nav').classList.add('move-out');
        document.querySelector('.contact-tab').classList.add('move-out');
    } else if (page == 'projects') {
        document.querySelector('.top-nav-border').classList.add('animation');
        document.querySelector('.tmp-top').classList.add('move-out');
        document.querySelector('.top-container').classList.add('move-out');
        document.querySelector('.right-nav').classList.add('move-out');
        document.querySelector('.bottom-nav').classList.add('move-out');
        document.querySelector('.contact-tab').classList.add('move-out');
        document.querySelector('.top-container').classList.add('fade');
        // document.querySelector('.big-text.projects').classList.toggle('fade');
    } else if (page == 'contact') {
        console.log('adding content');
        document.querySelector('.top-nav-border').classList.add('animation');
        document.querySelector('.tmp-top').classList.add('move-out');
        document.querySelector('.top-container').classList.add('move-out');
        document.querySelector('.right-nav').classList.add('move-out');
        document.querySelector('.bottom-nav').classList.add('move-out');
        document.querySelector('.contact-tab').classList.add('move-out');
        document.querySelector('.top-container').classList.add('fade');
    } else if (page == 'home') {
        document.querySelector('.top-nav-border').classList.remove('animation');
        document.querySelector('.tmp-top').classList.remove('move-out');
        document.querySelector('.top-container').classList.remove('activate');
        document.querySelector('.right-nav').classList.remove('move-out');
        document.querySelector('.bottom-nav').classList.remove('move-out');
        document.querySelector('.contact-tab').classList.remove('move-out');
    }


    currPage = page;
}




function phoneMethod(page) {
    // Remove active class from all sections
    toggleMenu();
    document.querySelectorAll('.page-selection').forEach((section) => {
        section.classList.remove('active');
      });
      
      // Add active class to the targeted section
      const section = document.getElementById(page);
      section.classList.add('active');
  
      // Removing animations from the current page.
      if (currPage == 'about') {
          document.querySelector('.top-nav-border').classList.remove('animation');
          document.querySelector('.tmp-top').classList.remove('move-out');
          document.querySelector('.top-container').classList.remove('move-out');
          document.querySelector('.right-nav').classList.remove('move-out');
          document.querySelector('.bottom-nav').classList.remove('move-out');
          document.querySelector('.contact-tab').classList.remove('move-out');
      } else if (currPage == 'projects') {
          document.querySelector('.top-nav-border').classList.remove('animation');
          document.querySelector('.tmp-top').classList.remove('move-out');
          document.querySelector('.top-container').classList.remove('move-out');
          document.querySelector('.right-nav').classList.remove('move-out');
          document.querySelector('.bottom-nav').classList.remove('move-out');
          document.querySelector('.contact-tab').classList.remove('move-out');
          document.querySelector('.top-nav').style.opacity = '1';
      } else if (currPage == 'contact') { 
          console.log('removing content');
          document.querySelector('.top-nav-border').classList.remove('animation');
          document.querySelector('.tmp-top').classList.remove('move-out');
          document.querySelector('.top-container').classList.remove('activate');
          document.querySelector('.right-nav').classList.remove('move-out');
          document.querySelector('.bottom-nav').classList.remove('move-out');
          document.querySelector('.contact-tab').classList.remove('move-out');
          document.querySelector('.top-nav').style.opacity = '1';
      }
  
      // Adding animation for the next page.
      if (page == 'about') {
          document.querySelector('.top-nav-border').classList.add('animation');
          document.querySelector('.tmp-top').classList.add('move-out');
          document.querySelector('.top-container').classList.add('move-out');
          document.querySelector('.right-nav').classList.add('move-out');
          document.querySelector('.bottom-nav').classList.add('move-out');
          document.querySelector('.contact-tab').classList.add('move-out');
      } else if (page == 'projects') {
          document.querySelector('.top-nav-border').classList.add('animation');
          document.querySelector('.tmp-top').classList.add('move-out');
          document.querySelector('.top-container').classList.add('move-out');
          document.querySelector('.right-nav').classList.add('move-out');
          document.querySelector('.bottom-nav').classList.add('move-out');
          document.querySelector('.contact-tab').classList.add('move-out');
          document.querySelector('.top-nav').style.opacity = '0';
          // document.querySelector('.big-text.projects').classList.toggle('fade');
      } else if (page == 'contact') {
          console.log('adding content');
          document.querySelector('.top-nav-border').classList.add('animation');
          document.querySelector('.tmp-top').classList.add('move-out');
          document.querySelector('.top-container').classList.add('activate');
          document.querySelector('.right-nav').classList.add('move-out');
          document.querySelector('.bottom-nav').classList.add('move-out');
          document.querySelector('.contact-tab').classList.add('move-out');
          document.querySelector('.top-nav').style.opacity = '0';
      } else if (page == 'home') {
          document.querySelector('.top-nav-border').classList.remove('animation');
          document.querySelector('.tmp-top').classList.remove('move-out');
          document.querySelector('.top-container').classList.remove('activate');
          document.querySelector('.right-nav').classList.remove('move-out');
          document.querySelector('.bottom-nav').classList.remove('move-out');
          document.querySelector('.contact-tab').classList.remove('move-out');
      }
  
  
      currPage = page;
}