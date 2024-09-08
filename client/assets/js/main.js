var swiper = new Swiper("#swiper-banner", {
    slidesPerView: 'auto',
    centeredSlides: true,
    spaceBetween: 30,
    freeMode: false,
    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },
    autoplay: {
        delay: 3000, // Tiempo en milisegundos entre transiciones (3 segundos)
        disableOnInteraction: false, // No desactivar autoplay al interactuar con el slider
    },
    loop: true
});

var swiper = new Swiper("#swiper-botones", {
    slidesPerView: 'auto',
    spaceBetween: 15,
    freeMode: true,
});

var swiper = new Swiper("#swiper-cards", {
    slidesPerView: 'auto',
    spaceBetween: 30,
    freeMode: true,
  });