# Sistema de Gestión de Auditorías Internas (SGSI) — ISO/IEC 27001:2022

Backend en **Laravel 12** para la gestión de auditorías internas de un Sistema de Gestión de Seguridad
de la Información, basado en **ISO/IEC 27001:2022**. Incluye planificación de auditorías, checklist de
requisitos y controles del Anexo A, gestión de hallazgos y no conformidades, acciones correctivas (CAPA),
evidencias digitales con verificación de integridad (SHA-256), y un dashboard ejecutivo con generación de
informes en PDF.

La interfaz web (Blade + Alpine.js) y la API REST (para integraciones externas, probada con Postman)
comparten la misma base de código y las mismas reglas de negocio.

## Requisitos

- [Docker Engine](https://docs.docker.com/engine/install/) 24+ y el plugin **Docker Compose** (`docker compose version`)
- Git

No necesitas tener PHP, Composer, Node ni MySQL instalados en tu máquina — todo corre dentro de los contenedores.

## Instalación con Docker

### 1. Clonar el repositorio

```bash
git clone https://github.com/TheSL2/Proyecto---SGSI.git
cd Proyecto---SGSI
```

### 2. Configurar variables de entorno

```bash
cp .env.example .env
```

Edita `.env` y define al menos estos valores (los demás pueden quedarse con su default):

```env
DB_CONNECTION=mysql
DB_DATABASE=SGSI
DB_USERNAME=root
DB_PASSWORD=elige-una-contraseña-segura

SANCTUM_STATEFUL_DOMAINS=localhost,localhost:8090,127.0.0.1,127.0.0.1:8090,localhost:8443,127.0.0.1:8443
```

`DB_PASSWORD` es también la contraseña que usará el contenedor de MySQL para el usuario `root`
(`docker-compose.yml` la toma como `MYSQL_ROOT_PASSWORD`) — usa el mismo valor en ambos lados, ya está
resuelto automáticamente porque ambos leen la misma variable del `.env`.

### 3. Generar un certificado SSL autofirmado (para HTTPS local)

El proyecto sirve tráfico por HTTP (puerto `8090`) y HTTPS (puerto `8443`). Para HTTPS necesitas un
certificado en `docker/nginx/certs/`. Genera uno autofirmado (válido para desarrollo/demo local):

```bash
mkdir -p docker/nginx/certs
openssl req -x509 -nodes -newkey rsa:2048 \
  -keyout docker/nginx/certs/selfsigned.key \
  -out docker/nginx/certs/selfsigned.crt \
  -days 365 \
  -subj "/CN=localhost"
```

El navegador mostrará una advertencia de "certificado no confiable" al entrar por `https://localhost:8443`
— es esperado con un certificado autofirmado; acepta la excepción para continuar. **Para un entorno de
producción real**, sustituye este certificado por uno emitido por una autoridad confiable (por ejemplo,
[Let's Encrypt](https://letsencrypt.org/) vía Certbot) apuntando al dominio real.

### 4. Levantar los contenedores

```bash
docker compose up -d --build
```

Esto construye la imagen de la aplicación (multi-stage: Composer → build de assets con Node/Vite →
runtime PHP-FPM en Alpine, ejecutando como usuario no-root `sgsi`) y levanta 4 servicios:

| Servicio | Descripción | Puerto expuesto |
|---|---|---|
| `app` | PHP-FPM 8.2 (Laravel) | interno (9000) |
| `nginx` | Servidor web | `8090` (HTTP), `8443` (HTTPS) |
| `db` | MySQL 8.0 | interno (3306) |
| `redis` | Cache/colas | interno (6379) |

Verifica que los 4 contenedores estén corriendo:

```bash
docker compose ps
```

### 5. Generar la clave de la aplicación, migrar y sembrar datos

```bash
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan storage:link
```

El seeder carga el catálogo completo de los 93 controles del Anexo A + cláusulas 4-10 de ISO/IEC
27001:2022, áreas de ejemplo, y usuarios de prueba.

### 6. Acceder a la aplicación

- **Web (HTTP)**: [http://localhost:8090](http://localhost:8090)
- **Web (HTTPS)**: [https://localhost:8443](https://localhost:8443)
- **API**: mismas URLs con prefijo `/api/...`

## Desarrollo local sin Docker (opcional)

Si prefieres correr el proyecto directo en tu máquina para desarrollo activo (hot-reload de Vite, por ejemplo):

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm run dev &
php artisan serve
```

## Pruebas

### Tests automatizados (PHPUnit)

```bash
docker compose exec app php artisan test
```

### Análisis estático (Larastan / PHPStan)

```bash
docker compose exec app vendor/bin/phpstan analyse
```

### Colección de Postman

La colección con las pruebas de la API REST está en [`postman/`](./postman). Impórtala en Postman para
correr la suite completa contra el entorno que prefieras (local o Docker).

### Análisis de vulnerabilidades (Trivy)

El escaneo de la imagen de contenedor con [Trivy](https://github.com/aquasecurity/trivy) se documenta en
[`trivy-report.txt`](./trivy-report.txt). Para regenerarlo:

```bash
trivy image sgsi_app:latest --output trivy-report.txt
```

## Integración continua

Cada push a `main`, `dev`, o cualquier rama `feature/*` corre automáticamente, vía GitHub Actions
(`.github/workflows/ci.yml`):
- Análisis estático con PHPStan/Larastan
- Suite completa de tests con PHPUnit (SQLite en memoria)

## Estructura del proyecto

Proyecto Laravel estándar, sin subcarpetas adicionales — el código vive directo en la raíz del repositorio.

```
app/Http/Controllers/Api/   → Controladores REST (Auditorías, Checklists, Hallazgos, Evidencias, etc.)
app/Http/Controllers/Auth/  → Autenticación (Breeze) + Autenticación de dos factores (2FA)
app/Models/                 → Modelos Eloquent
app/Services/               → AuditoriaService (canal de logging de auditoría)
resources/views/            → Vistas Blade (interfaz web completa)
resources/js/pages/         → Componentes Alpine.js que consumen la API
routes/web.php              → Rutas de la interfaz web
routes/api.php              → Rutas de la API REST
docker/                     → Configuración de Nginx (HTTP+HTTPS) y PHP (opcache)
postman/                    → Colección de Postman para probar la API
```

## Seguridad

- Autenticación de dos factores (TOTP) disponible para todos los usuarios en `/2fa/setup`.
- Rate limiting en login (5 intentos/min) y en la API.
- Headers de seguridad (HSTS, X-Frame-Options, X-Content-Type-Options) configurados en Nginx.
- Evidencias digitales con hash SHA-256 para garantizar integridad.
- El contenedor de la aplicación corre como usuario no-root (`sgsi`).