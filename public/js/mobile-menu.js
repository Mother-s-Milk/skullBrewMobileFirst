document.addEventListener('DOMContentLoaded', () => {
    const mobileMenu = document.getElementById("mobile-menu");
    const openMobileMenuBtn = document.getElementById("open-mobile-menu-btn");
    const closeMobileMenuBtn = document.getElementById("close-mobile-menu-btn");

    //Abrir menú
    openMobileMenuBtn.onclick = () => {
        mobileMenu.classList.add("show-menu");
        document.body.classList.add("no-scroll");
    };

    //Cerrar menú
    closeMobileMenuBtn.onclick = () => {
        mobileMenu.classList.remove("show-menu");
        document.body.classList.remove("no-scroll");
    };

    //Cerrar menú al hacer clic en un enlace
    let mobileMenuLinks = document.querySelectorAll("#mobile-menu .mobile-links a");
    mobileMenuLinks.forEach(link => {
        link.onclick = () => {
            mobileMenu.classList.remove("show-menu");
            document.body.classList.remove("no-scroll");
        };
    });

    /**/
    /*const beerInfo = {
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
    });*/
})