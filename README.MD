Catálogo virtual 

Facebook: Gabriel Meza
Instagram: gabotoxf




/minitienda/
│
├── /client/                      # Frontend de la aplicación
│   ├── /assets/                  # Recursos estáticos
│   │   ├── /css/                 # Archivos CSS
│   │   ├── /images/              # Imágenes para el catálogo
│   │   ├── /js/                  # Archivos JavaScript
│   │   
│   ├── /pages/                   # Páginas de la aplicación
│   │   ├── /index.html           # Página principal del catálogo
│   │   ├── /dashboard.html        # Página del panel de administración
│   │
│   ├── /partials/                # Partes reutilizables de HTML
│   │   ├── /footer.html           # Pie de página
│   │   ├── /header.html           # Encabezado
│   │
├── /server/                      # Backend - PHP (API)
│   ├── /app/                     # Lógica de la aplicación
│   │   ├── /controllers/         # Controladores de la API
│   │   ├── /models/              # Modelos de la API
│   │   └── /services/            # Servicios auxiliares
│   ├── /config/                  # Configuración de la aplicación
│   │   ├── database.php          # Configuración de la base de datos
│   │   ├── config.php            # Otras configuraciones
│   │
│   │__ .htaccess                  # Configuración para el servidor
│   └── index.php                 # Archivo principal de la API
│
└── README.md                     # Documentación del proyecto
