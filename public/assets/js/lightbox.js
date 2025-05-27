document.addEventListener('DOMContentLoaded', function () {
    const lightboxOverlay = document.getElementById('lightbox-overlay');
    const lightboxImg = document.getElementById('lightbox-img');
    const lightboxClose = document.getElementById('lightbox-close');

    const imagenesClickables = document.querySelectorAll('.carousel-item img, .miniatura img');

    imagenesClickables.forEach(img => {
        img.style.cursor = 'zoom-in';
        img.addEventListener('click', function () {
        lightboxImg.src = this.src;
        lightboxOverlay.style.display = 'flex';
        });
    });


    lightboxClose.addEventListener('click', function () {
      lightboxOverlay.style.display = 'none';
    });

    lightboxOverlay.addEventListener('click', function (e) {
      if (e.target === lightboxOverlay || e.target === lightboxImg) {
        lightboxOverlay.style.display = 'none';
      }
    });
  });