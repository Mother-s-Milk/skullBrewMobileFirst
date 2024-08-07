document.addEventListener('DOMContentLoaded', () => {
    function setComponentWidth () {
        let element = document.querySelector('.container');
        let size = element.clientWidth;
        console.log(size);

        // Obtener el valor de la variable CSS --s-padding
        let sPadding = getComputedStyle(document.documentElement).getPropertyValue('--s-padding');
        
        // Convertir el valor de --s-padding a número
        sPadding = parseFloat(sPadding);

        // Calcular el nuevo width
        let width = (size) - (sPadding + sPadding);

        // Establecer la nueva variable CSS --s-width
        document.documentElement.style.setProperty('--s-width', `${width}px`);
    }
    
    setComponentWidth();

    window.addEventListener('resize', setComponentWidth);


    /*
    * Menú desplegable
    */
    // Abrir menú
let btnMenu = document.getElementById("btn-menu");
btnMenu.onclick = () => {
    let menuDesplegable = document.getElementById("menu-desplegable");
    menuDesplegable.classList.add("show");
    //document.body.classList.add("no-scroll");
};

// Cerrar menú
let btnCloseMenu = document.getElementById("btn-close-menu");
btnCloseMenu.onclick = () => {
    let menuDesplegable = document.getElementById("menu-desplegable");
    menuDesplegable.classList.remove("show");
    //document.body.classList.remove("no-scroll");
};

// Cerrar menú al hacer clic en un enlace
let menuLinks = document.querySelectorAll("#menu-desplegable .links-nav a");
menuLinks.forEach(link => {
    link.onclick = () => {
        let menuDesplegable = document.getElementById("menu-desplegable");
        menuDesplegable.classList.remove("show");
        //document.body.classList.remove("no-scroll");
    };
});
})