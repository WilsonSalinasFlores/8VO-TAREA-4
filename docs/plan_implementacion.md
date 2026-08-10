# Sistema Básico de Agenda Virtual — Plan de Implementación (v4)

## Descripción General

Aplicación web **Full-Stack** con separación de capas: **Angular SPA** como frontend que
consume una **Laravel REST API** con autenticación via tokens (Sanctum), persistencia en
**MySQL**, estilos con **Bootstrap 5**. El sistema aplica rigurosamente **Programación
Orientada a Objetos (POO)** y los principios **SOLID**, con énfasis en el **Principio
de Responsabilidad Única (SRP)**.

---

## Stack Tecnológico Final

| Capa | Tecnología |
|---|---|
| **Frontend** | Angular 17+ (SPA) |
| **Estilos** | Bootstrap 5.3 |
| **Backend / API** | Laravel 11 (REST API) |
| **Auth API** | Laravel Sanctum (tokens) |
| **ORM** | Eloquent (incluido en Laravel) |
| **Base de datos** | MySQL 8.x (local) |
| **Hashing contraseña** | Bcrypt (`Hash::make()`) |
| **Lenguaje Backend** | PHP 8.2+ |
| **Paradigma** | **Programación Orientada a Objetos (POO)** |
| **Principios de diseño** | **SOLID — énfasis en SRP** |

---

## Arquitectura

```
Angular SPA  ←→  Laravel REST API  ←→  MySQL
(puerto 4200)     (puerto 8000)
      ↑                  ↑
Bootstrap 5      POO + SOLID/SRP
```

---

## 🧱 Programación Orientada a Objetos (POO)

Tanto **PHP/Laravel** como **TypeScript/Angular** son lenguajes orientados a objetos.
Todo el código del sistema aplica los cuatro pilares de la POO de forma explícita.

---

### Pilar 1 — Encapsulamiento

> **"Ocultar el estado interno y exponer solo lo necesario."**

Se usan modificadores de visibilidad (`private`, `protected`, `public`) en todas las clases.
Ningún atributo interno es accesible directamente desde fuera de la clase.

**Backend (PHP):**
```php
// UsuarioRepository.php
class UsuarioRepository implements UsuarioRepositoryInterface
{
    private Usuario $model;  // privado: nadie accede al modelo desde fuera

    public function __construct(Usuario $model)
    {
        $this->model = $model;
    }

    public function buscarPorCedula(string $cedula): ?Usuario  // expuesto
    {
        return $this->model->where('cedula', $cedula)->first();
    }

    private function aplicarFiltroActivo(Builder $query): Builder  // interno
    {
        return $query->where('is_active', 1);
    }
}
```

**Frontend (TypeScript):**
```typescript
// auth.service.ts
export class AuthService {
    private readonly TOKEN_KEY = 'agenda_token'; // privado: solo esta clase gestiona el token
    private currentUser: IUsuario | null = null;

    public login(cedula: string, password: string): Observable<ILoginResponse> {
        return this.http.post<ILoginResponse>('/api/auth/login', { cedula, password })
            .pipe(tap(res => this.guardarSesion(res)));
    }

    private guardarSesion(response: ILoginResponse): void { // privado: lógica interna
        localStorage.setItem(this.TOKEN_KEY, response.token);
        this.currentUser = response.user;
    }
}
```

---

### Pilar 2 — Abstracción

> **"Definir contratos sin exponer la implementación concreta."**

Se usan **interfaces abstractas** (Contracts) en Laravel e **interfaces TypeScript** en Angular.

**Backend (PHP) — Repository Interface:**
```php
// Contracts/ContactoRepositoryInterface.php
interface ContactoRepositoryInterface
{
    public function obtenerActivos(int $usuarioId): Collection;
    public function guardar(array $datos): Contacto;
    public function actualizar(int $id, array $datos): Contacto;
    public function softDelete(int $id): void;      // abstrae UPDATE eliminado=1
    public function buscarPorId(int $id, int $usuarioId): ?Contacto;
}
```

**Frontend (TypeScript) — Interfaces de datos:**
```typescript
// interfaces/contacto.interface.ts
export interface IContacto {
    id: number;
    nombres: string;
    apellidos: string;
    tipo: 'Trabajo' | 'Personal' | 'Proveedores' | 'Otros';
    direccion: string;
    telefono: string;
    sitio_web?: string;   // opcional
    empresa?: string;
    cargo?: string;
}

// interfaces/respuesta-api.interface.ts
export interface IRespuestaApi<T> {
    data: T;
    mensaje: string;
    status: number;
}
```

---

### Pilar 3 — Herencia

> **"Las clases hijas reutilizan y extienden el comportamiento de la clase base."**

Se crean **clases base abstractas** para Repositories y Services con comportamiento común.

**Backend (PHP) — BaseRepository abstracto:**
```php
// Repositories/BaseRepository.php
abstract class BaseRepository
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function buscarPorId(int $id): ?Model  // comportamiento común heredado
    {
        return $this->model->find($id);
    }

    abstract public function guardar(array $datos): Model; // obliga a implementar
}

// Repositories/ContactoRepository.php — hereda de BaseRepository
class ContactoRepository extends BaseRepository implements ContactoRepositoryInterface
{
    public function __construct(Contacto $model)
    {
        parent::__construct($model); // llama constructor padre
    }

    public function guardar(array $datos): Model // implementa método abstracto
    {
        return $this->model->create($datos);
    }

    public function softDelete(int $id): void   // comportamiento propio
    {
        $this->model->where('id', $id)->update(['eliminado' => 1]);
    }
}
```

**Frontend (TypeScript) — BaseService abstracto:**
```typescript
// core/services/base.service.ts
export abstract class BaseService {
    protected baseUrl = 'http://localhost:8000/api';

    constructor(protected http: HttpClient) {}

    protected manejarError(error: HttpErrorResponse): Observable<never> { // heredado
        const mensaje = error.error?.mensaje || 'Error en el servidor';
        return throwError(() => new Error(mensaje));
    }
}

// core/services/contacto.service.ts — hereda de BaseService
export class ContactoService extends BaseService {
    private readonly endpoint = `${this.baseUrl}/contactos`;

    constructor(http: HttpClient) {
        super(http); // llama constructor padre
    }

    obtenerTodos(): Observable<IRespuestaApi<IContacto[]>> {
        return this.http.get<IRespuestaApi<IContacto[]>>(this.endpoint)
            .pipe(catchError(this.manejarError)); // reutiliza método del padre
    }
}
```

---

### Pilar 4 — Polimorfismo

> **"Un mismo contrato puede tener múltiples implementaciones intercambiables."**

El **Contenedor IoC de Laravel** liga interfaces a implementaciones concretas en runtime.
Si se cambia MySQL por otro motor, solo cambia el Repository, no el Service ni el Controller.

**Backend (PHP) — Binding en AppServiceProvider:**
```php
// AppServiceProvider.php
public function register(): void
{
    $this->app->bind(UsuarioRepositoryInterface::class,  UsuarioRepository::class);
    $this->app->bind(ContactoRepositoryInterface::class, ContactoRepository::class);
    $this->app->bind(PreguntaRepositoryInterface::class, PreguntaRepository::class);
    $this->app->bind(BitacoraRepositoryInterface::class, BitacoraRepository::class);
}

// AuthService.php — depende de la abstracción, NO de la implementación concreta
class AuthService
{
    public function __construct(
        private readonly UsuarioRepositoryInterface $usuarioRepo,   // interfaz
        private readonly PreguntaRepositoryInterface $preguntaRepo  // interfaz
    ) {}
}
```

**Frontend (TypeScript) — Polimorfismo en Validators:**
```typescript
// shared/validators/cedula.validator.ts
export class CedulaValidator {
    static validar(): ValidatorFn {
        return (control: AbstractControl): ValidationErrors | null => {
            const valor = control.value?.toString() ?? '';
            return /^\d{10}$/.test(valor) ? null : { cedulaInvalida: true };
        };
    }
}

// shared/validators/password.validator.ts
export class PasswordValidator {
    static validar(): ValidatorFn {
        return (control: AbstractControl): ValidationErrors | null => {
            const valor = control.value ?? '';
            const cumple = /^(?=.*[A-Z])(?=.*\d).{8,}$/.test(valor);
            return cumple ? null : { passwordDebil: true };
        };
    }
}
```

---

### Resumen de los 4 Pilares POO en el Proyecto

| Pilar POO | Backend Laravel (PHP) | Frontend Angular (TypeScript) |
|---|---|---|
| **Encapsulamiento** | `private/protected` en Repository y Service | `private` en Services y Components |
| **Abstracción** | `RepositoryInterface` (Contracts) | Interfaces TypeScript (`IContacto`, `IRespuestaApi`) |
| **Herencia** | `BaseRepository` → `ContactoRepository` | `BaseService` → `ContactoService` |
| **Polimorfismo** | IoC Container: interfaz → implementación intercambiable | `ValidatorFn` reutilizable en múltiples formularios |

---

## ⚙️ Principios SOLID Aplicados


### S — Single Responsibility Principle (Responsabilidad Única) — PRINCIPAL

> **"Cada clase debe tener una sola razón para cambiar."**

Cada archivo, clase, componente o servicio tiene **una sola responsabilidad** bien definida.
Ninguna clase mezcla validación + lógica de negocio + acceso a datos + respuesta HTTP.

### O — Open/Closed Principle
Interfaces en Repositories permiten extender sin modificar código existente.

### L — Liskov Substitution
Las implementaciones concretas de Repositorios respetan sus interfaces.

### I — Interface Segregation
Cada Repository Interface declara solo los métodos necesarios para su contexto.

### D — Dependency Inversion
Los Controllers y Services dependen de **abstracciones** (interfaces), no de implementaciones concretas. Laravel IoC Container resuelve las inyecciones.

---

## Aplicación de SRP — Backend Laravel

### Capas y responsabilidades

| Capa | Clase/Archivo | Única responsabilidad |
|---|---|---|
| **Controller** | `AuthController` | Recibir request HTTP y retornar response JSON |
| **FormRequest** | `RegistroRequest` | Validar y sanitizar datos de entrada |
| **Service** | `AuthService` | Ejecutar la lógica de negocio de autenticación |
| **Repository** | `UsuarioRepository` | Acceso y persistencia de datos en MySQL |
| **Model** | `Usuario` | Representar la estructura de datos de la entidad |
| **Middleware** | `VerificarUsuarioActivo` | Verificar estado del usuario en cada request |
| **Resource** | `UsuarioResource` | Transformar modelo a respuesta JSON |

### Ejemplo de flujo SRP — Registro de usuario

```
POST /api/auth/registro
       │
       ▼
RegistroRequest        ← valida: cédula, contraseña, preguntas
       │
       ▼
AuthController         ← recibe request validado, llama al servicio
       │
       ▼
AuthService            ← aplica lógica: hash contraseña, reglas de negocio
       │
       ▼
UsuarioRepository      ← inserta en tabla usuarios (MySQL)
PreguntaRepository     ← inserta en tabla preguntas_recuperacion
       │
       ▼
UsuarioResource        ← formatea respuesta JSON al frontend
```

---

## Aplicación de SRP — Frontend Angular

### Capas y responsabilidades

| Capa | Clase/Archivo | Única responsabilidad |
|---|---|---|
| **Component** | `LoginComponent` | Gestionar la vista y eventos del usuario (UI) |
| **Service** | `AuthService` | Comunicarse con la API REST (HTTP) |
| **Guard** | `AuthGuard` | Proteger rutas según estado de sesión |
| **Interceptor** | `AuthInterceptor` | Adjuntar token Bearer a cada request |
| **Interface** | `IUsuario`, `IContacto` | Definir contratos de tipos de datos |
| **Validator** | `CedulaValidator` | Validar lógica de campos de formulario |
| **Model** | `ContactoModel` | Mapear respuesta de la API a objeto tipado |

### Ejemplo de flujo SRP — Crear contacto

```
Usuario llena formulario (crear.component.html)
       │
       ▼
CrearContactoComponent  ← solo gestiona el formulario y eventos UI
       │
       ▼
ContactoService         ← solo realiza el POST a /api/contactos
       │
       ▼
AuthInterceptor         ← solo adjunta el token Bearer al request
       │
       ▼
API Laravel             ← responde JSON
       │
       ▼
ContactoModel           ← mapea la respuesta a objeto tipado IContacto
       │
       ▼
AlertaComponent         ← solo muestra el mensaje de éxito/error
```

---

## Actores y Roles

| Actor | Rol en sistema | Flujo especial |
|---|---|---|
| **Visitante** | Sin sesión | Registro y login |
| **Usuario Estándar** | `rol = usuario` | CRUD contactos propios |
| **Superusuario** | `rol = superusuario` | **Único**, creado por Seeder, primer login forzado |

---

## Modelo de Base de Datos (MySQL — 5 tablas)

### Tabla: `usuarios`

| Columna | Tipo | Restricciones |
|---|---|---|
| `id` | INT (PK, AI) | — |
| `cedula` | VARCHAR(10) | UNIQUE, NOT NULL |
| `nombre` | VARCHAR(255) | NOT NULL |
| `correo` | VARCHAR(255) | NOT NULL |
| `password` | VARCHAR(255) | NOT NULL (Bcrypt) |
| `rol` | ENUM('usuario','superusuario') | DEFAULT 'usuario' |
| `is_active` | TINYINT(1) | DEFAULT 1 |
| `primer_login` | TINYINT(1) | DEFAULT 0 (superusuario = 1 al crearse) |
| `created_at` | TIMESTAMP | Auto |
| `updated_at` | TIMESTAMP | Auto |

> **Seeder superusuario**: `cedula='admin'`, `password=Hash::make('admin')`,
> `rol='superusuario'`, `primer_login=1`

---

### Tabla: `catalogo_preguntas`

| Columna | Tipo | Restricciones |
|---|---|---|
| `id` | INT (PK, AI) | — |
| `pregunta` | VARCHAR(255) | NOT NULL |

**Las 10 preguntas predefinidas (Seeder):**

| # | Pregunta |
|---|---|
| 1 | ¿Cuál es el nombre de tu primera mascota? |
| 2 | ¿En qué ciudad naciste? |
| 3 | ¿Cuál es el apellido de soltera de tu madre? |
| 4 | ¿Cuál fue el nombre de tu primera escuela? |
| 5 | ¿Cuál es tu comida favorita? |
| 6 | ¿Cuál es el nombre de tu mejor amigo de la infancia? |
| 7 | ¿Cuál es tu película favorita? |
| 8 | ¿Cuál fue el modelo de tu primer vehículo? |
| 9 | ¿Cuál es el nombre de tu libro favorito? |
| 10 | ¿Cuál es tu equipo deportivo favorito? |

---

### Tabla: `preguntas_recuperacion`

| Columna | Tipo | Restricciones |
|---|---|---|
| `id` | INT (PK, AI) | — |
| `usuario_id` | INT (FK) | → `usuarios.id` |
| `catalogo_pregunta_id` | INT (FK) | → `catalogo_preguntas.id` |
| `respuesta` | VARCHAR(255) | NOT NULL |

---

### Tabla: `contactos`

| Columna | Tipo | Restricciones |
|---|---|---|
| `id` | INT (PK, AI) | — |
| `usuario_id` | INT (FK) | → `usuarios.id` |
| `nombres` | VARCHAR(255) | NOT NULL |
| `apellidos` | VARCHAR(255) | NOT NULL |
| `tipo` | ENUM('Trabajo','Personal','Proveedores','Otros') | NOT NULL |
| `direccion` | VARCHAR(255) | NOT NULL |
| `telefono` | VARCHAR(20) | NOT NULL |
| `sitio_web` | VARCHAR(255) | NULL |
| `empresa` | VARCHAR(255) | NULL |
| `cargo` | VARCHAR(255) | NULL |
| `eliminado` | TINYINT(1) | DEFAULT 0 |
| `created_at` | TIMESTAMP | Auto |
| `updated_at` | TIMESTAMP | Auto |

---

### Tabla: `bitacora_auditoria`

| Columna | Tipo | Restricciones |
|---|---|---|
| `id` | INT (PK, AI) | — |
| `accion` | VARCHAR(100) | NOT NULL |
| `descripcion` | TEXT | NULL |
| `target_usuario_id` | INT | NOT NULL |
| `superusuario_id` | INT (FK) | → `usuarios.id` |
| `created_at` | TIMESTAMP | Auto |

---

## Flujos Especiales

### 🔐 Superusuario — Primer Login

```
1. Ingresa: cedula="admin", password="admin"
2. Sistema detecta primer_login = 1
3. Angular redirige → "Cambio de Contraseña Obligatorio"
4. Ingresa: contraseña_actual, nueva_contraseña, confirmar_contraseña
5. Laravel PrimerLoginService valida con Hash::check()
6. Actualiza password + primer_login = 0
7. Angular redirige → "Configurar Preguntas Secretas"
8. Selecciona 3 preguntas del catálogo y define respuestas
9. PreguntaRepository guarda en preguntas_recuperacion
10. Angular redirige → Panel de Administración
```

### 🔑 Recuperación de Contraseña (Usuario Estándar)

```
1. Usuario ingresa cédula
2. RecuperacionService carga las 3 preguntas secretas del usuario
3. Usuario llena respuestas en Angular
4. RecuperacionService valida las 3 respuestas
5. Si válidas → formulario nueva contraseña + confirmar
6. UsuarioRepository actualiza password con Bcrypt
7. Redirige → login con mensaje de éxito
```

---

## Estructura del Proyecto — Backend Laravel (SRP aplicado)

```
agenda-api/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/               ← SRP: solo HTTP request/response
│   │   │   ├── Auth/
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── RecuperacionController.php
│   │   │   │   └── PrimerLoginController.php
│   │   │   ├── ContactoController.php
│   │   │   ├── AdminController.php
│   │   │   └── CatalogoController.php
│   │   │
│   │   ├── Requests/                      ← SRP: solo validación de inputs
│   │   │   ├── Auth/
│   │   │   │   ├── RegistroRequest.php
│   │   │   │   ├── LoginRequest.php
│   │   │   │   ├── CambioPasswordRequest.php
│   │   │   │   └── RecuperacionRequest.php
│   │   │   └── Contacto/
│   │   │       ├── CrearContactoRequest.php
│   │   │       └── EditarContactoRequest.php
│   │   │
│   │   ├── Resources/                     ← SRP: solo transformación a JSON
│   │   │   ├── UsuarioResource.php
│   │   │   ├── ContactoResource.php
│   │   │   └── BitacoraResource.php
│   │   │
│   │   └── Middleware/                    ← SRP: solo control de acceso
│   │       ├── VerificarUsuarioActivo.php
│   │       └── EsSuperusuario.php
│   │
│   ├── Services/                          ← SRP: solo lógica de negocio
│   │   ├── Auth/
│   │   │   ├── AuthService.php
│   │   │   ├── RecuperacionService.php
│   │   │   └── PrimerLoginService.php
│   │   ├── ContactoService.php
│   │   └── AdminService.php
│   │
│   ├── Repositories/                      ← SRP: solo acceso a datos (MySQL)
│   │   ├── Contracts/                     ← Interfaces (DIP + ISP)
│   │   │   ├── UsuarioRepositoryInterface.php
│   │   │   ├── ContactoRepositoryInterface.php
│   │   │   ├── PreguntaRepositoryInterface.php
│   │   │   └── BitacoraRepositoryInterface.php
│   │   ├── UsuarioRepository.php
│   │   ├── ContactoRepository.php
│   │   ├── PreguntaRepository.php
│   │   └── BitacoraRepository.php
│   │
│   └── Models/                            ← SRP: solo estructura de datos
│       ├── Usuario.php
│       ├── Contacto.php
│       ├── CatalogoPregunta.php
│       ├── PreguntaRecuperacion.php
│       └── BitacoraAuditoria.php
│
├── database/
│   ├── migrations/                        ← 5 archivos
│   └── seeders/
│       ├── SuperusuarioSeeder.php
│       └── CatalogoPreguntasSeeder.php
│
└── routes/
    └── api.php
```

---

## Estructura del Proyecto — Frontend Angular (SRP aplicado)

```
agenda-frontend/
└── src/app/
    │
    ├── core/                              ← Infraestructura transversal
    │   ├── services/                      ← SRP: solo comunicación HTTP
    │   │   ├── auth.service.ts
    │   │   ├── contacto.service.ts
    │   │   ├── admin.service.ts
    │   │   └── catalogo.service.ts
    │   │
    │   ├── guards/                        ← SRP: solo protección de rutas
    │   │   ├── auth.guard.ts
    │   │   └── admin.guard.ts
    │   │
    │   ├── interceptors/                  ← SRP: solo manejo de headers HTTP
    │   │   └── auth.interceptor.ts
    │   │
    │   ├── interfaces/                    ← SRP: solo contratos de tipos
    │   │   ├── usuario.interface.ts
    │   │   ├── contacto.interface.ts
    │   │   └── bitacora.interface.ts
    │   │
    │   └── models/                        ← SRP: solo mapeo de datos API→objeto
    │       ├── usuario.model.ts
    │       └── contacto.model.ts
    │
    ├── modules/
    │   ├── auth/
    │   │   ├── login/
    │   │   │   ├── login.component.ts     ← SRP: solo lógica de vista login
    │   │   │   └── login.component.html
    │   │   ├── registro/
    │   │   │   ├── registro.component.ts  ← SRP: solo lógica de vista registro
    │   │   │   └── registro.component.html
    │   │   ├── recuperar/
    │   │   │   ├── recuperar.component.ts
    │   │   │   └── recuperar.component.html
    │   │   └── primer-login/
    │   │       ├── primer-login.component.ts
    │   │       └── primer-login.component.html
    │   │
    │   ├── agenda/
    │   │   ├── lista/
    │   │   │   ├── lista.component.ts     ← SRP: solo listar y acciones UI
    │   │   │   └── lista.component.html
    │   │   ├── crear/
    │   │   │   ├── crear.component.ts     ← SRP: solo formulario de creación
    │   │   │   └── crear.component.html
    │   │   └── editar/
    │   │       ├── editar.component.ts    ← SRP: solo formulario de edición
    │   │       └── editar.component.html
    │   │
    │   └── admin/
    │       ├── dashboard/
    │       │   ├── dashboard.component.ts
    │       │   └── dashboard.component.html
    │       ├── usuarios/
    │       │   ├── usuarios.component.ts  ← SRP: solo gestión de usuarios
    │       │   └── usuarios.component.html
    │       └── bitacora/
    │           ├── bitacora.component.ts
    │           └── bitacora.component.html
    │
    └── shared/
        ├── validators/                    ← SRP: solo lógica de validación
        │   ├── cedula.validator.ts
        │   └── password.validator.ts
        └── components/                   ← SRP: solo presentación reutilizable
            ├── navbar/
            │   ├── navbar.component.ts
            │   └── navbar.component.html
            ├── modal-confirmacion/
            │   ├── modal.component.ts
            │   └── modal.component.html
            ├── alerta/
            │   ├── alerta.component.ts
            │   └── alerta.component.html
            └── spinner/
                ├── spinner.component.ts
                └── spinner.component.html
```

---

## Mapa de Endpoints REST API (Laravel)

### Públicos (sin token)

| Método | Endpoint | Acción |
|---|---|---|
| POST | `/api/auth/registro` | Registrar nuevo usuario |
| POST | `/api/auth/login` | Iniciar sesión → retorna token |
| GET | `/api/catalogo/preguntas` | Listar las 10 preguntas del catálogo |
| POST | `/api/auth/recuperar/verificar` | Validar respuestas secretas |
| POST | `/api/auth/recuperar/restablecer` | Guardar nueva contraseña |

### Usuario Autenticado (con token Sanctum)

| Método | Endpoint | Acción | HU |
|---|---|---|---|
| POST | `/api/auth/logout` | Cerrar sesión | HU-001 |
| GET | `/api/contactos` | Listar contactos activos | HU-002 |
| POST | `/api/contactos` | Crear nuevo contacto | HU-002 |
| GET | `/api/contactos/{id}` | Ver detalle de contacto | HU-002 |
| PUT | `/api/contactos/{id}` | Editar contacto | HU-005 |
| DELETE | `/api/contactos/{id}` | Soft delete (eliminado=1) | HU-003 |

### Superusuario (con token + middleware EsSuperusuario)

| Método | Endpoint | Acción | HU |
|---|---|---|---|
| POST | `/api/auth/primer-login` | Cambio forzado de contraseña | — |
| POST | `/api/auth/primer-login/preguntas` | Registrar 3 preguntas secretas | — |
| GET | `/api/admin/usuarios` | Listar todos los usuarios | HU-004 |
| GET | `/api/admin/usuarios/{id}/agenda` | Ver agenda de un usuario | HU-004 |
| PATCH | `/api/admin/usuarios/{id}/estado` | Habilitar/Deshabilitar usuario | HU-004 |
| GET | `/api/admin/bitacora` | Ver bitácora de auditoría | HU-004 |

---

## Rutas Angular (SPA)

| Ruta | Componente | Guard |
|---|---|---|
| `/login` | LoginComponent | — |
| `/registro` | RegistroComponent | — |
| `/recuperar` | RecuperarComponent | — |
| `/primer-login` | PrimerLoginComponent | AuthGuard |
| `/agenda` | ListaComponent | AuthGuard |
| `/agenda/nuevo` | CrearContactoComponent | AuthGuard |
| `/agenda/editar/:id` | EditarContactoComponent | AuthGuard |
| `/admin` | DashboardAdminComponent | AdminGuard |
| `/admin/usuarios` | UsuariosComponent | AdminGuard |
| `/admin/bitacora` | BitacoraComponent | AdminGuard |

---

## Reglas de Negocio → Implementación Técnica

| RN | Regla | Implementación |
|---|---|---|
| RN-01 | Cédula única, 10 dígitos | `RegistroRequest` → `Rule::unique + digits:10` |
| RN-02 | Contraseña: min 8, 1 mayúscula, 1 número | `RegistroRequest` → `Password::min(8)->mixedCase()->numbers()` |
| RN-03 | Contraseña hasheada | `AuthService` → `Hash::make()` antes de llamar al Repository |
| RN-04 | 3 preguntas de recuperación | `RegistroRequest` → `array\|size:3` + `PreguntaRepository::guardar()` |
| RN-05 | Tipos de contacto restringidos | `CrearContactoRequest` → `Rule::in([...])` |
| RN-06 | Campos opcionales contacto | `CrearContactoRequest` → `nullable` |
| RN-07 | Contacto asociado al usuario | `ContactoService` → asigna `usuario_id = auth()->id()` |
| RN-08 | Solo eliminación lógica | `ContactoRepository::softDelete()` → `update(['eliminado'=>1])` |
| RN-09 | Bandera eliminado 0→1 | `ContactoRepository` → nunca llama a `delete()` |
| RN-10 | Contactos eliminados ocultos | `ContactoRepository::obtenerActivos()` → `where('eliminado',0)` |
| RN-11 | Superusuario ve todas las agendas | `AdminRepository::obtenerAgendaDeUsuario($id)` sin filtro de sesión |
| RN-12 | Usuario deshabilitado bloqueado | `VerificarUsuarioActivo` → si `is_active=0` → 401 |
| RN-13 | Auditoría incluye todos | `AdminRepository` → sin filtro `eliminado`, con campo `estado` visible |
| RN-14 | Primer login forzado | `PrimerLoginService` detecta `primer_login=1` → flag en respuesta login |
| RN-15 | Superusuario único | Solo un registro con `rol='superusuario'` vía Seeder |

---

## Comunicación Angular ↔ Laravel (Inyección de dependencias)

```typescript
// auth.interceptor.ts — SRP: solo adjunta token
intercept(req: HttpRequest<any>, next: HttpHandler) {
  const token = localStorage.getItem('token');
  if (token) {
    req = req.clone({ setHeaders: { Authorization: `Bearer ${token}` } });
  }
  return next.handle(req);
}

// auth.service.ts — SRP: solo comunicación HTTP de autenticación
login(cedula: string, password: string): Observable<ILoginResponse> {
  return this.http.post<ILoginResponse>('/api/auth/login', { cedula, password });
}

// login.component.ts — SRP: solo maneja UI y delega al servicio
onSubmit(): void {
  this.authService.login(this.form.value.cedula, this.form.value.password)
    .subscribe({ next: (res) => this.handleSuccess(res), error: (e) => this.handleError(e) });
}
```

```php
// AuthController.php — SRP: solo recibe y responde HTTP
public function login(LoginRequest $request): JsonResponse {
    return response()->json($this->authService->login($request->validated()));
}

// AuthService.php — SRP: solo lógica de negocio
public function login(array $data): array {
    $usuario = $this->usuarioRepository->buscarPorCedula($data['cedula']);
    // validar hash, estado activo, primer_login, generar token...
}

// UsuarioRepository.php — SRP: solo consultas a MySQL
public function buscarPorCedula(string $cedula): ?Usuario {
    return Usuario::where('cedula', $cedula)->first();
}
```

---

## UX / Diseño Bootstrap 5 — Buenas Prácticas

- ✅ **Formularios**: Validación inline `is-valid` / `is-invalid` en tiempo real
- ✅ **Tablas responsivas**: `table-responsive` con paginación en listados
- ✅ **Modales**: Confirmación antes de eliminar o deshabilitar
- ✅ **Alertas**: Componente reutilizable `alert-success`, `alert-danger`
- ✅ **Navbar**: Dinámico según rol
- ✅ **Spinner**: Indicador de carga durante peticiones HTTP
- ✅ **Breadcrumbs**: Navegación contextual en pantallas de edición
- ✅ **Cards**: Panel de administración organizado

---

## Plan de Verificación

### Automatizadas

```bash
# Laravel
php artisan test --filter AuthTest
php artisan test --filter ContactoTest
php artisan test --filter AdminTest

# Angular
ng test
```

### Manuales

| Escenario | Resultado esperado |
|---|---|
| Registrar cédula duplicada | Error "La cédula ya está registrada" |
| Login superusuario primera vez | Redirige a cambio forzado de contraseña |
| Login usuario deshabilitado | Error "Cuenta deshabilitada por administrador" |
| Eliminar contacto → verificar en DB | `eliminado=1`, registro sigue presente |
| Verificar campo `password` en DB | Cadena `$2y$10$...` (Bcrypt) |
| Superusuario ver agenda ajena | Tabla con contactos del usuario consultado |
| Deshabilitar usuario → bitácora | Registro en `bitacora_auditoria` con timestamp |

---

## Orden de Implementación (con capas SRP)

| # | Tarea | Capa | SRP Clase |
|---|---|---|---|
| 1 | Proyecto Laravel + MySQL + Sanctum + CORS | Backend | Configuración |
| 2 | 5 Migrations | Backend | Migration |
| 3 | Seeders: Superusuario + Catálogo de preguntas | Backend | Seeder |
| 4 | Models + relaciones Eloquent | Backend | Model |
| 5 | Repository Interfaces (Contracts) | Backend | Interface |
| 6 | Implementaciones de Repositories | Backend | Repository |
| 7 | Middleware: VerificarUsuarioActivo + EsSuperusuario | Backend | Middleware |
| 8 | FormRequests (validación) | Backend | Request |
| 9 | Services: Auth, Recuperacion, PrimerLogin | Backend | Service |
| 10 | Services: Contacto, Admin | Backend | Service |
| 11 | Controllers + Resources (API Resources) | Backend | Controller / Resource |
| 12 | Proyecto Angular + Bootstrap 5 + módulos y rutas | Frontend | Módulos |
| 13 | Interfaces y Models de datos | Frontend | Interface / Model |
| 14 | Core: Services HTTP (auth, contacto, admin) | Frontend | Service |
| 15 | Core: Interceptor + Guards | Frontend | Interceptor / Guard |
| 16 | Shared: Validators, Navbar, Modal, Alerta, Spinner | Frontend | Shared Components |
| 17 | Módulo Auth: login, registro, recuperar, primer-login | Frontend | Component |
| 18 | Módulo Agenda: lista, crear, editar | Frontend | Component |
| 19 | Módulo Admin: dashboard, usuarios, bitácora | Frontend | Component |
| 20 | Pruebas integrales y ajustes de UX | Ambos | — |
