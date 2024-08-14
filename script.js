document.addEventListener('DOMContentLoaded', () => {
    /*Calculo el ancho de la ventana para asignar los anchos de los
    contenedores considerando el padding establecido en una variable*/
    function setComponentWidth () {
        let element = document.querySelector('.container');
        let size = (element.clientWidth) / 16;

        // Obtener el valor de la variable CSS --s-padding
        let sPadding = getComputedStyle(document.documentElement).getPropertyValue('--s-padding');
        
        // Convertir el valor de --s-padding a número
        sPadding = parseFloat(sPadding);

        // Calcular el nuevo width
        let width = (size) - (sPadding + sPadding);

        // Establecer la nueva variable CSS --s-width
        document.documentElement.style.setProperty('--s-width', `${width}rem`);
    }

    setComponentWidth();

    window.addEventListener('resize', setComponentWidth);
    /**/
    /**/
    /**/

    /******************/
    /*Menú desplegable*/
    /******************/
    //Abrir menú
    let btnMenu = document.getElementById("btn-menu");
    btnMenu.onclick = () => {
        let menuDesplegable = document.getElementById("menu-desplegable");
        menuDesplegable.classList.add("show-menu");
        document.body.classList.add("no-scroll");
    };

    //Cerrar menú
    let btnCloseMenu = document.getElementById("btn-close-menu");
    btnCloseMenu.onclick = () => {
        let menuDesplegable = document.getElementById("menu-desplegable");
        menuDesplegable.classList.remove("show-menu");
        document.body.classList.remove("no-scroll");
    };

    //Cerrar menú al hacer clic en un enlace
    let menuLinks = document.querySelectorAll("#menu-desplegable .links-nav a");
    menuLinks.forEach(link => {
        link.onclick = () => {
            let menuDesplegable = document.getElementById("menu-desplegable");
            menuDesplegable.classList.remove("show-menu");
            document.body.classList.remove("no-scroll");
        };
    });
    /**/
    /**/
    /**/

    /********/
    /*Slider*/
    /********/
    let imagenes = [
        'imgSlider/1.png',
        'imgSlider/2.png',
        'imgSlider/3.png'
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

    /**/
    const beerInfo = {
        novedad: {
            title: "Lúpulo Salvaje",
            url: "cervezasDestacadas/destacada1.png",
            details: "Esta es una cerveza especial que se ha lanzado recientemente. Su sabor es único y cuenta con ingredientes frescos."
        },
        'mas-pedida': {
            title: "Dorado Miel",
            url: "cervezasDestacadas/destacada2.png",
            details: "La cerveza más popular entre nuestros clientes. Su sabor y calidad la hacen la favorita."
        },
        clasica: {
            title: "Trigo Suave",
            url: "cervezasDestacadas/destacada3.png",
            details: "Nuestra cerveza clásica, con una receta que se mantiene igual desde hace años. El sabor de siempre."
        }
    };

    const buttons = document.querySelectorAll('.more-info-btn');
    const modal = document.getElementById('beer-info-modal');
    const modalTitle = document.getElementById('beer-title');
    const modalImg = document.getElementById('beer-img');
    const modalDetails = document.getElementById('beer-details');
    const closeButton = document.querySelector('.close-btn');

    buttons.forEach(button => {
        button.addEventListener('click', (event) => {
            const beerType = event.target.getAttribute('data-beer');
            modalTitle.textContent = beerInfo[beerType].title;
            modalImg.src = beerInfo[beerType].url;
            modalDetails.textContent = beerInfo[beerType].details;
            modal.style.display = 'flex'; // Mostrar modal
            setTimeout(() => {
                modal.classList.add('show'); // Activar transición después de que el modal se muestra
            }, 10); // Retraso corto para asegurar la transición
        });
    });

    closeButton.addEventListener('click', () => {
        modal.classList.remove('show'); // Quitar la clase show para iniciar la animación de salida
        setTimeout(() => {
            modal.style.display = 'none'; // Ocultar el modal después de la transición
        }, 300); // Tiempo de la animación, coincidente con el tiempo de transición
    });

    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }
    });
})