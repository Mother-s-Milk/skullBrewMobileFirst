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

    let imagenes = [
        'cervezasDestacadas/destacada1.png',
        'cervezasDestacadas/destacada2.png',
        'cervezasDestacadas/destacada3.png'
    ];
    
    let left = document.getElementById('left-slider');
    let right = document.getElementById('right-slider');
    let imgContainer = document.getElementById('img-container');
    let index = 0;
    
    let i = document.createElement("img");
    i.src = imagenes[index];
    imgContainer.appendChild(i);
    
    function changeImage (newIndex) {
        i.style.opacity = 0;
    
        setTimeout (() => {
            i.src = imagenes[newIndex];
            i.style.opacity = 1;;
        }, 500);
    }
    
    left.onclick = () => {
        if (index === 0) {
            index = imagenes.length - 1;
        }
        else {
            index--;
        }
    
        changeImage(index);
    }
    
    right.onclick = () => {
        if (index === imagenes.length - 1) {
            index = 0;
        }
        else {
            index++;
        }
    
        changeImage(index);
    }
})