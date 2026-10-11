document.addEventListener("DOMContentLoaded", () => {
    /********/
    /*Slider*/
    /********/
    let imagenes = [
        'images/imgSlider/1.png',
        'images/imgSlider/2.png',
        'images/imgSlider/3.png'
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
});