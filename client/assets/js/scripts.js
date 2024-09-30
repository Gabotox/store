async function fetchProducts() {
    try {
        const id_cliente = 'MmhDYUdtOaB4aHN1ejJNS3RVUjJJUT09';
        const llave_secreta = 'SEVnaW9aSXVvSU45SGpHOVJWbngyQUFsTlEzeXFsSDVac0g5ZFlYWlNiUXZqOEhEOWhPMmRVZUdKQ1JKMVFSQQ==';

        const response = await fetch('http://localhost/store/productos', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Basic ' + btoa(`${id_cliente}:${llave_secreta}`)
            }
        });

        // Verifica si la respuesta es válida
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(`Error: ${response.status} - ${errorData.Respuesta || 'Error desconocido'}`);
        }

        // Procesa la respuesta JSON
        const data = await response.json();

        // Asegúrate de que 'data.Respuesta' sea un array
        if (data.Respuesta && Array.isArray(data.Respuesta)) {
            displayProducts(data.Respuesta); // Para tarjetas pequeñas
            displayProductss(data.Respuesta); // Para tarjetas grandes
        } else {
            throw new Error('No se encontraron productos en la respuesta.');
        }
    } catch (error) {
        console.error('Error al obtener los productos:', error.message); // Mostrar mensaje de error
    }
}

function displayProducts(products) {
    const swiperWrapper = document.querySelector('#carra');
    swiperWrapper.innerHTML = ''; // Limpiar el contenido previo

    products.forEach(product => {
        // Crear un contenedor para la tarjeta
        const cardElement = document.createElement('div');
        cardElement.className = 'swiper-slide card card-horizontal';

        // Crear el elemento de imagen
        const imgElement = document.createElement('img');
        imgElement.className = 'card-img-left';
        imgElement.alt = 'Imagen del Producto';
        imgElement.src = product.imagen_producto ? `../../server/uploads/productos/${product.imagen_producto}` : 'https://via.placeholder.com/200x150';

        // Manejar el error de carga
        imgElement.onerror = function () {
            imgElement.src = 'https://via.placeholder.com/200x150';
        };

        // Crear el contenido de la tarjeta
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

        // Agregar la imagen y el contenido a la tarjeta
        cardElement.appendChild(imgElement);
        cardElement.insertAdjacentHTML('beforeend', cardBodyHTML);

        // Insertar la tarjeta en el DOM
        swiperWrapper.appendChild(cardElement);
    });
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

        imgElement.onerror = function() {
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
        if (cardElement instanceof Node) {
            swiperWrapper.appendChild(cardElement);
        } else {
            console.error('El elemento de la tarjeta no es un nodo válido:', cardElement);
        }
    });
}




// Llama a la función al cargar la página
document.addEventListener('DOMContentLoaded', fetchProducts);
