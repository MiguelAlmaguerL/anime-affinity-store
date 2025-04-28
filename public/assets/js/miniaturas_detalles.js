// Código para manejar miniaturas y actualizar el carrusel en detalles.php
document.addEventListener("DOMContentLoaded", function () {
    const thumbnails = document.querySelectorAll('.carousel-thumbnails img');
    const carousel = document.querySelector('#carouselProducto');
    const carouselInstance = bootstrap.Carousel.getInstance(carousel) || new bootstrap.Carousel(carousel);
  
    // Al hacer clic en una miniatura
    thumbnails.forEach((thumb, index) => {
      thumb.addEventListener('click', () => {
        carouselInstance.to(index);
  
        // Actualizar clases activas
        thumbnails.forEach(img => img.classList.remove('active-thumb'));
        thumb.classList.add('active-thumb');
      });
    });
  
    // Al cambiar de slide con los controles del carrusel
    carousel.addEventListener('slid.bs.carousel', function (event) {
      const activeIndex = event.to;
  
      thumbnails.forEach((thumb, index) => {
        if (index === activeIndex) {
          thumb.classList.add('active-thumb');
        } else {
          thumb.classList.remove('active-thumb');
        }
      });
    });
  });
  