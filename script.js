document.addEventListener('DOMContentLoaded', () => {
    let element = document.querySelector('.container');
    let size = element.clientWidth;
    console.log(size);

    // Obtener el valor de la variable CSS --s-padding
    let sPadding = getComputedStyle(document.documentElement).getPropertyValue('--s-padding');
        
    // Convertir el valor de --s-padding a número
    sPadding = parseFloat(sPadding);

    // Calcular el nuevo width
    let width = (size - sPadding);

    // Establecer la nueva variable CSS --s-width
    document.documentElement.style.setProperty('--s-width', `${width}px`);
})