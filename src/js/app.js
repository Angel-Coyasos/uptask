const mobileMenuBtn = document.querySelector('#mobile-menu');
const cerrarMenuBtn = document.querySelector('#cerrar-menu');
const sidebar = document.querySelector('.sidebar');

if (mobileMenuBtn) {
    mobileMenuBtn.addEventListener('click', function () {
        sidebar.classList.add('mostrar');
    });
}

if (cerrarMenuBtn) {
    cerrarMenuBtn.addEventListener('click', function () {
        sidebar.classList.add('ocultar');

        setTimeout( () => {
            sidebar.classList.remove('mostrar');
             sidebar.classList.remove('ocultar');
        }, 1000);
    });
}

// Elimina la clase de mostrar, en un tamanio de tablet y mayores
const anchoPantalla = window.document.body.clientWidth;

// cuando se cambia el tamanio d ela pantalla
window.addEventListener('resize', function () {
    // esta variable la vamos evaluando constante mente segun cambiamos el tamanio de la pantalla
    const anchoPantalla = window.document.body.clientWidth;
    if (anchoPantalla >= 768) {
        sidebar.classList.remove('mostrar');
    }
});