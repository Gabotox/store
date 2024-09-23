/minitienda/
│
├── /client/                     # Frontend - HTML, CSS, JavaScript
│   ├── /assets/                 # Recursos estáticos como imágenes y CSS
│   │   ├── /css/                # Archivos CSS
│   │   │   ├── main.css         # Estilos globales
│   │   │   └── dashboard.css    # Estilos específicos del dashboard
│   │   ├── /images/             # Imágenes
│   │   └── /js/                 # Archivos JavaScript
│   │       ├── app.js           # Script principal de la aplicación
│   │       └── services.js      # Servicios para llamar a la API
│   │
│   ├── /pages/                  # Páginas HTML de la aplicación
│   │   ├── index.html            # Página principal de la tienda
│   │   ├── dashboard.html        # Página de administración de productos
│   │   └── ...                  # Otras páginas
│   │
│   └── /partials/               # Archivos HTML parciales (cabeceras, pies de página)
│       ├── header.html           # Cabecera común
│       ├── footer.html           # Pie de página común
│       └── ...                  # Otros fragmentos comunes
│
├── /server/                     # Backend - PHP
│   ├── /app/                    # Lógica de la aplicación
│   │   ├── /controllers/        # Controladores
│   │   │
│   │   ├── /models/             # Modelos
│   │   │   ├── mainModel.php    # Modelo principal
│   │   │
│   │   ├── /services/           # Servicios auxiliares
│   │   │
│   │   └── /views/              # Vistas (si las necesitas)
│   │
│   ├── /config/                 # Configuración de la aplicación
│   │   ├── database.php         # Configuración de la base de datos
│   │   ├── config.php           # Configuración general
│   │   └── ...                  # Otros archivos de configuración
│   │
│   ├── /public/                 # Archivos públicos (servidos por el servidor web)
│   │   ├── /assets/             # Recursos estáticos como imágenes y CSS
│   │   ├── index.php            # Punto de entrada del backend
│   │   └── /api/                # Endpoints de la API
│   │
│   ├── /routes/                 # Rutas de la API
│   │
│   └── composer.json            # Dependencias y configuraciones de PHP
│
├── /scripts/                    # Scripts de despliegue y configuración
│
├── /docs/                       # Documentación del proyecto
│
└── README.md                    # Documentación básica del proyecto



store
|
|--- app/
|--------controladores/
|-----------------------rutasControladores.php
|
|
|
|--- rutas/
|-------- rutas.php

index.php
.htaccess