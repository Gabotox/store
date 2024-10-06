// Función POST para agregar un nuevo producto
async function addProduct(product) {
    try {
        const response = await fetch('http://localhost/store/productos', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Basic ' + btoa(`${id_cliente}:${llave_secreta}`)
            },
            body: JSON.stringify(product)
        });

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(`Error: ${response.status} - ${errorData.Respuesta || 'Error al agregar el producto'}`);
        }

        const data = await response.json();
        console.log('Producto agregado:', data);
        fetchProducts(); // Recargar la lista de productos
    } catch (error) {
        console.error('Error al agregar el producto:', error.message);
    }
}

// Función PUT para editar un producto existente
async function editProduct(productId, updatedProduct) {
    try {
        const response = await fetch(`http://localhost/store/productos/${productId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Basic ' + btoa(`${id_cliente}:${llave_secreta}`)
            },
            body: JSON.stringify(updatedProduct)
        });

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(`Error: ${response.status} - ${errorData.Respuesta || 'Error al editar el producto'}`);
        }

        const data = await response.json();
        console.log('Producto editado:', data);
        fetchProducts(); // Recargar la lista de productos
    } catch (error) {
        console.error('Error al editar el producto:', error.message);
    }
}

// Función DELETE para eliminar un producto
async function deleteProduct(productId) {
    try {
        const response = await fetch(`http://localhost/store/productos/${productId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Basic ' + btoa(`${id_cliente}:${llave_secreta}`)
            }
        });

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(`Error: ${response.status} - ${errorData.Respuesta || 'Error al eliminar el producto'}`);
        }

        const data = await response.json();
        console.log('Producto eliminado:', data);
        fetchProducts(); // Recargar la lista de productos
    } catch (error) {
        console.error('Error al eliminar el producto:', error.message);
    }
}