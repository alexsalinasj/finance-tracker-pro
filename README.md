# Finance Tracker Pro

Finance Tracker Pro es una aplicación web full stack para gestionar finanzas personales con una experiencia moderna: ingresos, gastos, categorías, presupuestos mensuales, metas de ahorro y reportes visuales con exportación a PDF.

## Stack

**Frontend**

- Vue 3 + TypeScript + Vite
- Pinia
- Vue Router
- Bootstrap 5
- Chart.js
- Axios

**Backend**

- Laravel
- Laravel Sanctum
- MySQL
- API REST
- DomPDF para reportes PDF

**Infraestructura**

- Docker
- Docker Compose
- Nginx + PHP-FPM

## Estructura

```text
finance-tracker-pro/
|-- backend/
|-- frontend/
|-- docker-compose.yml
|-- .env.example
`-- README.md
```

## Inicio rápido con Docker

Requisitos:

- Docker Desktop activo
- Docker Compose

```bash
cp .env.example .env
docker compose up --build
```

Servicios:

- Frontend: http://localhost:5173
- API Laravel: http://localhost:8000/api
- MySQL: localhost:3306

El contenedor del backend instala dependencias, genera `APP_KEY` si hace falta y ejecuta migraciones con seeders.

Usuario demo:

```text
email: demo@financepro.test
password: password
```

## Comandos útiles

```bash
docker compose exec app php artisan migrate:fresh --seed
docker compose exec app php artisan route:list
docker compose exec app php artisan test
docker compose exec frontend npm run build
```

## Desarrollo sin Docker

Backend:

```bash
cd backend
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve --port=8000
```

Frontend:

```bash
cd frontend
cp .env.example .env.local
npm install
npm run dev
```

## Módulos incluidos

- Dashboard con saldo actual, ingresos del mes, gastos del mes, ahorro disponible, porcentaje de metas, gráfica de ingresos vs gastos, gráfica de gastos por categoría y alertas de presupuesto.
- CRUD de ingresos.
- CRUD de gastos con método de pago.
- CRUD de categorías por tipo, color e icono.
- CRUD de presupuestos mensuales por categoría con alertas al 80% y 100%.
- CRUD de metas de ahorro con porcentaje de avance.
- Reportes por mes y año con resumen, balance final, top 5 categorías de gasto y exportación PDF.
- Registro, login, logout y protección de rutas con Laravel Sanctum.

## Seguridad

Todas las rutas de datos usan `auth:sanctum`. Cada modelo financiero contiene `user_id`, y los controladores consultan desde la relación del usuario autenticado. Esto evita que un usuario lea o modifique información de otra cuenta.

## Endpoints principales

```text
POST   /api/auth/register
POST   /api/auth/login
POST   /api/auth/logout
GET    /api/auth/me

GET    /api/dashboard

GET    /api/categories
POST   /api/categories
PUT    /api/categories/{id}
DELETE /api/categories/{id}

GET    /api/incomes
POST   /api/incomes
PUT    /api/incomes/{id}
DELETE /api/incomes/{id}

GET    /api/expenses
POST   /api/expenses
PUT    /api/expenses/{id}
DELETE /api/expenses/{id}

GET    /api/budgets
POST   /api/budgets
PUT    /api/budgets/{id}
DELETE /api/budgets/{id}

GET    /api/savings-goals
POST   /api/savings-goals
PUT    /api/savings-goals/{id}
DELETE /api/savings-goals/{id}

GET    /api/reports
GET    /api/reports/export/pdf
```

## Variables de entorno clave

```text
APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:5173
VITE_API_URL=http://localhost:8000/api

DB_HOST=mysql
DB_DATABASE=finance_tracker_pro
DB_USERNAME=finance_user
DB_PASSWORD=finance_password
```

## Notas técnicas

- El frontend guarda el token de Sanctum en `localStorage` y lo envía como `Bearer token`.
- Los formularios validan campos obligatorios en Vue y el backend valida todos los payloads con reglas Laravel.
- Las respuestas 422/401 se muestran como alertas reutilizables.
- Los reportes PDF se generan en Laravel con una vista Blade.
- Las gráficas se renderizan con Chart.js en componentes Vue reutilizables.
