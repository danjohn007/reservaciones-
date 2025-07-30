# Sistema de Reservaciones PHP + MySQL

Sistema online de reservaciones, compras y servicios desarrollado en **PHP 8.2 puro** y **MySQL 5.7**, sin frameworks. Incluye dashboards para **Administrador**, **Comercio** y **Cliente**.

---

## 🚀 Características principales

- ✅ Autenticación por tipo de usuario (Admin, Comercio, Cliente)
- ✅ Reservaciones por día, hora, número de personas y amenidad (mesa, sala, etc.)
- ✅ Compra de productos con entrega a domicilio o recolección (pickup)
- ✅ Solicitud de servicios personalizados con seguimiento
- ✅ Subida de comprobantes de pago
- ✅ Asignación de personal a reservaciones o servicios
- ✅ Historial de comentarios/actividades por solicitud
- ✅ Paneles independientes para cada tipo de usuario
- ✅ Reportes y gráficas de operación
- ✅ Diseño responsivo con Bootstrap 5
- ✅ Validaciones en tiempo real
- ✅ Sistema de filtros y búsqueda

---

## 📁 Estructura del proyecto

```
├── config/
│   └── database.php
├── controllers/
│   ├── AuthController.php
│   ├── DashboardController.php
│   ├── ReservacionController.php
│   └── CompraController.php
├── models/
│   ├── Usuario.php
│   ├── Reservacion.php
│   ├── Producto.php
│   └── Servicio.php
├── views/
│   ├── auth/
│   │   ├── login.php
│   │   └── register.php
│   ├── dashboard/
│   │   ├── admin.php
│   │   ├── comercio.php
│   │   └── cliente.php
│   ├── forms/
│   │   ├── reservacion.php
│   │   ├── compra.php
│   │   └── servicio.php
│   └── details/
│       └── reservacion.php
├── public/
│   ├── assets/
│   ├── index.php
│   └── .htaccess
├── uploads/
├── db_schema.sql
└── README.md
```

---

## 🧩 Formularios disponibles

### 📌 Reservación
- Día y hora
- Número de personas
- Campo de mesa u otra amenidad
- Notas adicionales

### 📦 Compra de productos
- Productos seleccionables por el cliente
- Cantidad con validación de stock
- Opción: pickup o envío a domicilio
- Resumen de compra en tiempo real

### 🛎 Solicitud de servicio
- Día y hora
- Número de personas
- Amenidad requerida
- Descripción detallada del servicio

---

## 🛠 Funcionalidades de seguimiento

Cada solicitud tiene:
- ✅ Estatus: pendiente, confirmado, rechazado, completado
- ✅ Personal asignado
- ✅ Campo para comentarios o notas
- ✅ Costo total
- ✅ Subida de comprobante de pago
- ✅ Actividades registradas por comercio o admin
- ✅ Reportes y gráficas de control

---

## 💾 Base de datos

Requiere **MySQL 5.7**. Incluye las siguientes tablas principales:

- **usuarios** - Gestión de usuarios con roles
- **reservaciones** - Reservaciones de mesas/amenidades
- **productos** - Catálogo de productos
- **compras** - Órdenes de compra
- **compra_detalles** - Detalles de productos por compra
- **servicios** - Solicitudes de servicios personalizados
- **actividades** - Historial de actividades
- **amenidades** - Mesas y espacios disponibles
- **comprobantes_pago** - Archivos de comprobantes

**Incluye script `db_schema.sql` con toda la estructura.**

---

## 🔧 Requisitos

- PHP 8.2
- MySQL 5.7
- Servidor web (Apache o Nginx)
- Extensiones habilitadas: PDO, mysqli, fileinfo, mbstring

---

## ⚙️ Instalación

1. **Clonar el repositorio:**
   ```bash
   git clone https://github.com/danjohn007/reservaciones-.git
   cd reservaciones-
   ```

2. **Configurar la base de datos:**
   ```bash
   # Crear la base de datos en MySQL
   mysql -u root -p < db_schema.sql
   ```

3. **Configurar la conexión:**
   - Editar `config/database.php` con sus credenciales de MySQL
   ```php
   private $host = 'localhost';
   private $db_name = 'reservaciones';
   private $username = 'tu_usuario';
   private $password = 'tu_password';
   ```

4. **Configurar el servidor web:**
   - Apuntar el DocumentRoot a la carpeta `public/`
   - Asegurar que `.htaccess` esté habilitado

5. **Configurar permisos:**
   ```bash
   chmod 755 uploads/
   chmod 644 public/.htaccess
   ```

---

## 👥 Usuarios de Prueba

El sistema incluye usuarios de prueba:

| Tipo | Email | Contraseña | Permisos |
|------|--------|------------|----------|
| **Admin** | admin@reservaciones.com | password | Acceso completo al sistema |
| **Comercio** | comercio@reservaciones.com | password | Gestión de solicitudes y reportes |
| **Cliente** | cliente@reservaciones.com | password | Crear reservaciones, compras y servicios |

---

## 📊 Dashboards

### 🔑 Admin Panel
- Estadísticas generales del sistema
- Gestión de todos los usuarios
- Control total de reservaciones, compras y servicios
- Gráficas de rendimiento
- Asignación de personal

### 🏪 Panel Comercio
- Vista de solicitudes asignadas
- Actualización de estados
- Gestión de costos
- Filtros por estado
- Reportes específicos

### 👤 Panel Cliente
- Mis reservaciones
- Mis compras
- Mis servicios
- Subida de comprobantes
- Seguimiento en tiempo real

---

## 🔒 Características de Seguridad

- ✅ Autenticación segura con password_hash()
- ✅ Validación de roles por página
- ✅ Protección CSRF
- ✅ Validación de archivos subidos
- ✅ Headers de seguridad en .htaccess
- ✅ Sanitización de datos de entrada

---

## 📱 Características Técnicas

- **Frontend:** Bootstrap 5, Chart.js, Font Awesome
- **Backend:** PHP 8.2 puro (sin frameworks)
- **Base de datos:** MySQL 5.7 con PDO
- **Arquitectura:** MVC simplificado
- **Responsive:** Totalmente adaptable a dispositivos móviles
- **Validaciones:** Cliente y servidor
- **Uploads:** Soporte para imágenes y PDFs

---

## 🎯 Funcionalidades Destacadas

1. **Sistema de Estados:** Seguimiento completo del ciclo de vida de cada solicitud
2. **Actividades:** Registro automático de todas las acciones realizadas
3. **Validaciones Dinámicas:** Control de stock, capacidades y fechas en tiempo real
4. **Reportes Visuales:** Gráficas interactivas con Chart.js
5. **Gestión de Archivos:** Subida segura de comprobantes con validación
6. **Responsive Design:** Funciona perfectamente en móviles y tablets

---

## 🚀 Uso del Sistema

1. **Acceder al sistema** en `http://tu-dominio/`
2. **Iniciar sesión** con uno de los usuarios de prueba
3. **Explorar las funcionalidades** según el tipo de usuario
4. **Crear solicitudes** desde el panel del cliente
5. **Gestionar solicitudes** desde los paneles admin/comercio

---

## 📬 Contribuciones

Las contribuciones están abiertas. Sugerencias, issues y pull requests son bienvenidos.

---

## 📄 Licencia

Este proyecto está disponible bajo la licencia MIT.
