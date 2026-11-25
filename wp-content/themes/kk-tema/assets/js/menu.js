// AI-assisteret (Claude), tilpasset og testet af mig - se AI-log 23.11


/* 
Hamburger-menu 
*/
const knap = document.querySelector('.menu-knap');
const overlay = document.querySelector('.overlay');

function saetMenu(aaben) {
    knap.setAttribute('aria-expanded', String(aaben));
}

// Knappen åbner og lukker
knap.addEventListener('click', () => {
    saetMenu(knap.getAttribute('aria-expanded') !== 'true');
});

// Klik på den mørke baggrund lukker
overlay.addEventListener('click', () => saetMenu(false));

// Escape lukker og sender fokus tilbage til knappen (G-05)
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && knap.getAttribute('aria-expanded') === 'true') {
        saetMenu(false);
        knap.focus();
    }
});

// Skift til desktop lukker, ellers hænger fokus-fælden og scroll-låsen fast
const desktop = window.matchMedia('(min-width: 48em)');
desktop.addEventListener('change', () => {
    if (desktop.matches) saetMenu(false);
});

/* 
Hamburger-menu 
*/


/* 
Fokus-fælde: holder Tab inde i den åbne menu (G-05) 
*/

// AI-assisteret (Claude), tilpasset og testet af mig - se AI-log 24.11
const menuLinks = document.querySelectorAll('.hovedmenu a');
const iFaelden = [knap, ...menuLinks];

document.addEventListener('keydown', (e) => {
    if (e.key !== 'Tab' || knap.getAttribute('aria-expanded') !== 'true') return;

    const foerste = knap;
    const sidste = menuLinks[menuLinks.length - 1];

    // Står fokus et andet sted (fx på <body>), hentes det ind i menuen
    if (!iFaelden.includes(document.activeElement)) {
        e.preventDefault();
        foerste.focus();
    } else if (e.shiftKey && document.activeElement === foerste) {
        e.preventDefault();
        sidste.focus();
    } else if (!e.shiftKey && document.activeElement === sidste) {
        e.preventDefault();
        foerste.focus();
    }
});
/* 
Fokus-fælde: holder Tab inde i den åbne menu (G-05) 
*/
