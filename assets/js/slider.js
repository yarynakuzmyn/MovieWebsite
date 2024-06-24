const carousel = document.querySelector('#carouselExampleIndicators');
if (carousel) {
    const carouselInstance = new bootstrap.Carousel(carousel, {
        interval: 5000, // Change image every 5 seconds
        wrap: true // Loop the carousel
    });
}