# Prompts de Antigravity para Agenda Virtual

Este archivo contiene los prompts exactos utilizados para generar la arquitectura inicial del proyecto "Agenda Virtual" (Backend en Laravel y Frontend en Angular) utilizando agentes de Antigravity.

Puedes copiar estos prompts y pegárselos a Antigravity en otra computadora para que construya el proyecto desde cero.

---

## 1. Prompt para generar el Backend (Laravel)

Copia el siguiente texto y envíaselo a Antigravity una vez que tengas un proyecto de Laravel vacío instalado en la nueva computadora:

```text
Build the complete Laravel 11 REST API backend for the Agenda Virtual system. 

## CRITICAL: PHP and Artisan Commands
ALWAYS run PHP/Artisan with your local PHP executable.
ALWAYS run Composer with your local Composer executable.

## Step 1: Configure .env
Edit the .env file to set:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=agenda_virtual
DB_USERNAME=root
DB_PASSWORD=root
APP_URL=http://localhost:8000

## Step 2: Install Sanctum
Require laravel/sanctum

## Step 3: Configure CORS
Update config/cors.php to add 'http://localhost:4200' to allowed_origins.

## Step 4: Create MySQL Database
CREATE DATABASE IF NOT EXISTS agenda_virtual CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

## Step 5: Create 5 Migrations (in order)
Create migration files directly in database/migrations/ with these schemas:
1. usuarios (cedula, nombre, correo, password, rol enum usuario/superusuario, is_active, primer_login)
2. catalogo_preguntas (pregunta)
3. preguntas_recuperacion (usuario_id, catalogo_pregunta_id, respuesta)
4. contactos (usuario_id, nombres, apellidos, tipo enum, direccion, telefono, sitio_web, empresa, cargo, eliminado)
5. bitacora_auditoria (accion, descripcion, target_usuario_id, superusuario_id)

## Step 6: Create Seeders
- SuperusuarioSeeder: admin, Administrador, admin@agenda.com, rol superusuario, primer_login=1
- CatalogoPreguntasSeeder: Insert 10 security questions.

## Step 7: Models
Create all 5 models with Eloquent relationships.

## Step 8: Repository Pattern
Create BaseRepository, Contracts (Interfaces) and Implementations for Usuario, Contacto, Pregunta, Bitacora.

## Step 9: Middleware
- VerificarUsuarioActivo: verify is_active=1
- EsSuperusuario: verify rol='superusuario'

## Step 10: FormRequests
Create Auth/RegistroRequest, Auth/LoginRequest, Auth/CambioPasswordRequest, Auth/RecuperacionRequest, Contacto/CrearContactoRequest. Override failedValidation to return 422 JSON.

## Step 11: Services
Create BaseService, AuthService, RecuperacionService, PrimerLoginService, ContactoService, AdminService.

## Step 12: API Resources
UsuarioResource, ContactoResource (with estado=Activo/Eliminado), BitacoraResource. Ensure additional(['exito' => true]) is appended in controllers.

## Step 13: Controllers
AuthController, RecuperacionController, PrimerLoginController, ContactoController, AdminController using the Services.
```

---

## 2. Prompt para generar el Frontend (Angular)

Copia el siguiente texto y envíaselo a Antigravity una vez que tengas un proyecto de Angular vacío instalado (con Bootstrap) en la nueva computadora:

```text
Build the complete Angular 20 SPA frontend for the Agenda Virtual system following all the instructions in your system prompt. 

Angular 20 is already installed with Bootstrap 5.3 and Bootstrap Icons configured in angular.json.

Create ALL the files described in your instructions:
1. Global styles in src/styles.css
2. Update src/app/app.routes.ts with all routes
3. Update src/app/app.config.ts with HttpClient, Router providers
4. Create all interface files in src/app/core/interfaces/
5. Create src/app/core/models/
6. Create src/app/core/services/ (base, auth, contacto, admin, catalogo)
7. Create src/app/core/guards/ (auth, admin)
8. Create src/app/core/interceptors/ (auth)
9. Create src/app/shared/validators/ (cedula, password, match)
10. Create src/app/shared/components/ (navbar, alerta, spinner, modal-confirmacion)
11. Create src/app/modules/auth/ (login, registro, recuperar, primer-login)
12. Create src/app/modules/agenda/ (lista, crear, editar)
13. Create src/app/modules/admin/ (dashboard, usuarios, bitacora)
14. Update src/app/app.ts (root component with router-outlet)

Use Angular standalone components. Use Bootstrap 5 classes for ALL styling. Make the UI premium quality with proper UX (loading states, validation feedback, responsive design). Add a dynamic search bar in the contact list to filter by any string.

Report when ALL files are created with a complete file list.
```
