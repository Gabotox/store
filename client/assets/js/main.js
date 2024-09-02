var swiper = new Swiper(".mySwiper", {
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