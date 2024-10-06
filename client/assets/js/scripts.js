async function fetchProducts() {
    try {
        const id_cliente = 'MmhDYUdtOaB4aHN1ejJNS3RVUjJJUT09';
        const llave_secreta = 'SEVnaW9aSXVvSU45SGpHOVJWbngyQUFsTlEzeXFsSDVac0g5ZFlYWlNiUXZqOEhEOWhPMmRVZUdKQ1JKMVFSQQ==';

        const response = await fetch('http://192.168.1.145/store/productos', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Basic ' + btoa(`${id_cliente}:${llave_secreta}`)
            }
        });

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(`Error: ${response.status} - ${errorData.Respuesta || 'Error desconocido'}`);
        }

        const data = await response.json();

        if (data.Respuesta && Array.isArray(data.Respuesta)) {
            const productsByCategory = groupProductsByCategory(data.Respuesta);
            renderCategories(productsByCategory);
            crearBotonesCategoria(productsByCategory); // Llama a crear botones aquí
        } else {
            throw new Error('No se encontraron productos en la respuesta.');
        }
    } catch (error) {
        console.error('Error al obtener los productos:', error.message);
    }
}

function groupProductsByCategory(productos) {
    return productos.reduce((acc, product) => {
        const category = product.nombre_categoria; // Cambia 'nombre_categoria' según tu estructura
        if (!acc[category]) {
            acc[category] = [];
        }
        acc[category].push(product);
        return acc;
    }, {});
}






function crearBotonesCategoria(productsByCategory) {
    const container = document.getElementById("botones");
    container.innerHTML = ""; // Limpia el contenedor

    const categorias = Object.keys(productsByCategory); // Obtiene las categorías

    categorias.forEach((categoria, index) => {
        const boton = document.createElement('button');
        boton.classList.add("swiper-slide");
        boton.textContent = categoria; // Texto del botón

        boton.addEventListener("click", function() {
            const allButtons = container.querySelectorAll('button');
            allButtons.forEach(btn => btn.classList.remove('selected'));

            boton.classList.add('selected');

            console.log("Categoría seleccionada:", categoria);
        });

        container.appendChild(boton);

        // Si es el primer botón, lo seleccionamos por defecto
        if (index === 0) {
            boton.classList.add('selected');
        }
    });
}






function renderCategories(productsByCategory) {
    const container = document.getElementById('categorias-container');
    container.innerHTML = ''; // Limpia el contenido previo

    let categoryIndex = 0;

    for (const [category, products] of Object.entries(productsByCategory)) {
        const categorySection = document.createElement('div');
        categorySection.classList.add('categories-container');

        const categoryTitle = document.createElement('h3');
        categoryTitle.classList.add('mb-3', 'mt-5');
        categoryTitle.innerText = category;

        const mySwiper = document.createElement('div');
        mySwiper.className = 'swiper mySwiper';
        mySwiper.id = `swiper-cards-${category.replace(/\s+/g, '-')}`;
        const swiperWrapper = document.createElement('div');
        swiperWrapper.className = 'swiper-wrapper';

        products.forEach(product => {
            const cardElement = createProductCard(product);
            swiperWrapper.appendChild(cardElement);
        });

        mySwiper.appendChild(swiperWrapper);
        categorySection.appendChild(categoryTitle);
        categorySection.appendChild(mySwiper);
        container.appendChild(categorySection);

        const delay = (categoryIndex + 1) * 2000;

        new Swiper(mySwiper, {
            slidesPerView: 'auto',
            spaceBetween: 20,
            freeMode: true,
            autoplay: {
                delay: delay,
                disableOnInteraction: false,
            }
        });

        categoryIndex++;
    }
}






function createProductCard(product) {
    const cardElement = document.createElement('div');
    cardElement.className = 'swiper-slide card card-horizontal';

    const imgElement = document.createElement('img');
    imgElement.className = 'card-img-left';
    imgElement.alt = 'Imagen del Producto';
    imgElement.src = product.imagen_producto ? `../../server/uploads/productos/${product.imagen_producto}` : 'https://via.placeholder.com/200x150';
    imgElement.onerror = function () {
        imgElement.src = 'https://via.placeholder.com/200x150';
    };

    const cardBodyHTML = `
        <div class="card-body">
            <h5 class="card-title">${product.nombre_producto}</h5>
            <p class="card-text">${product.descripcion_producto}</p>
            <div class="d-flex justify-content-between align-items-center mt-2">
                <span class="price">$${product.precio_producto}</span>
                <a href="#" class="btn btn-add-to-cart py-2 d-flex align-items-center gap-2" style="width: max-content;">
                    <i class="fa-solid fa-bag-shopping"></i> Agregar
                </a>
            </div>
        </div>
    `;

    cardElement.appendChild(imgElement);
    cardElement.insertAdjacentHTML('beforeend', cardBodyHTML);
    return cardElement;
}







function displayProductss(products) {
    const swiperWrapper = document.querySelector('#tar');
    swiperWrapper.innerHTML = ''; // Limpiar el contenido previo

    products.forEach(product => {
        const cardElement = document.createElement('div');
        cardElement.className = 'swiper-slide card pb-3';

        const cardHeader = document.createElement('div');
        cardHeader.className = 'card-header';

        const bannerSpan = document.createElement('span');
        bannerSpan.className = 'card-banner';
        bannerSpan.textContent = 'Nuevo'; // Cambiar si necesario

        const imgElement = document.createElement('img');
        imgElement.className = 'card-img-top';
        imgElement.alt = 'Imagen del Producto';
        imgElement.src = product.imagen_producto ? `../../server/uploads/productos/${product.imagen_producto}` : 'https://via.placeholder.com/400x200';

        imgElement.onerror = function () {
            imgElement.src = 'https://via.placeholder.com/400x200';
        };

        cardHeader.appendChild(bannerSpan);
        cardHeader.appendChild(imgElement);

        const cardBody = document.createElement('div');
        cardBody.className = 'card-body';

        const titleElement = document.createElement('h5');
        titleElement.className = 'card-title';
        titleElement.textContent = product.nombre_producto;

        const descElement = document.createElement('p');
        descElement.className = 'card-text';
        descElement.textContent = product.descripcion_producto;

        const priceNew = document.createElement('span');
        priceNew.className = 'price-new';
        priceNew.textContent = `$${product.precio_producto}`;

        const addButton = document.createElement('a');
        addButton.href = '#';
        addButton.className = 'btn btn-add-to-cart py-2 mt-3';
        addButton.innerHTML = '<i class="fa-solid fa-bag-shopping"></i> Agregar';

        // Agregar elementos al cuerpo de la tarjeta
        cardBody.appendChild(titleElement);
        cardBody.appendChild(descElement);
        cardBody.appendChild(priceNew);
        cardBody.appendChild(addButton);

        // Agregar el header y el cuerpo a la tarjeta
        cardElement.appendChild(cardHeader);
        cardElement.appendChild(cardBody);

        // Verifica que cardElement sea un nodo válido
        swiperWrapper.appendChild(cardElement);
    });

    // Inicializar Swiper para este slider específico
    new Swiper('#swiper-tarjetas', {
        slidesPerView: 'auto',
        spaceBetween: 30,
        freeMode: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        }
    });
}




// Llama a la función al cargar la página
document.addEventListener('DOMContentLoaded', () => {
    fetchProducts();
});
