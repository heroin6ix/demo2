document.addEventListener("DOMContentLoaded", () => {

    const burger = document.querySelector('.burger');
    const navMenu = document.querySelector('.nav-menu');

    if (burger && navMenu) {

        burger.addEventListener('click', () => {
            burger.classList.toggle('active');
            navMenu.classList.toggle('active');
        });

        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                burger.classList.remove('active');
                navMenu.classList.remove('active');
            });
        });
    }

    const header = document.querySelector('.header');

    if (header) {

        window.addEventListener('scroll', () => {

            if (window.pageYOffset > 100) {

                header.style.background = 'rgba(255,255,255,0.98)';
                header.style.backdropFilter = 'blur(30px)';
                header.style.boxShadow = '0 8px 32px rgba(0,123,255,0.15)';

            } else {

                header.style.background = 'rgba(255,255,255,0.95)';
                header.style.backdropFilter = 'blur(20px)';
                header.style.boxShadow = '0 4px 20px rgba(0,123,255,0.1)';
            }
        });
    }

});