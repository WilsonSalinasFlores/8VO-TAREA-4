const fs = require('fs');
const path = require('path');

const srcDir = 'd:/UNIANDES/8VO/HERRAMIENTAS DE DESARROLLO DE SOFTWARE/CLASES/Tarea Semana 4/agenda-frontend/src';
const appDir = path.join(srcDir, 'app');

function writeFile(relPath, content) {
    const fullPath = path.join(appDir, relPath);
    const dir = path.dirname(fullPath);
    if (!fs.existsSync(dir)) fs.mkdirSync(dir, { recursive: true });
    fs.writeFileSync(fullPath, content.trim() + '\n', 'utf8');
}

function writeSrcFile(relPath, content) {
    const fullPath = path.join(srcDir, relPath);
    const dir = path.dirname(fullPath);
    if (!fs.existsSync(dir)) fs.mkdirSync(dir, { recursive: true });
    fs.writeFileSync(fullPath, content.trim() + '\n', 'utf8');
}

writeSrcFile('styles.css', `
@import 'bootstrap/dist/css/bootstrap.min.css';
@import 'bootstrap-icons/font/bootstrap-icons.css';
body { background-color: #f8f9fa; }
.premium-card { border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: none; }
`);

writeFile('app.config.ts', `
import { ApplicationConfig } from '@angular/core';
import { provideRouter } from '@angular/router';
import { routes } from './app.routes';
import { provideHttpClient, withInterceptors } from '@angular/common/http';
import { authInterceptor } from './core/interceptors/auth.interceptor';

export const appConfig: ApplicationConfig = {
  providers: [
    provideRouter(routes),
    provideHttpClient(withInterceptors([authInterceptor]))
  ]
};
`);

writeFile('app.routes.ts', `
import { Routes } from '@angular/router';
import { authGuard } from './core/guards/auth.guard';
import { adminGuard } from './core/guards/admin.guard';

export const routes: Routes = [
  { path: 'login', loadComponent: () => import('./modules/auth/login/login.component').then(m => m.LoginComponent) },
  { path: 'registro', loadComponent: () => import('./modules/auth/registro/registro.component').then(m => m.RegistroComponent) },
  { path: 'recuperar', loadComponent: () => import('./modules/auth/recuperar/recuperar.component').then(m => m.RecuperarComponent) },
  { path: 'primer-login', loadComponent: () => import('./modules/auth/primer-login/primer-login.component').then(m => m.PrimerLoginComponent), canActivate: [authGuard] },
  { path: 'agenda', loadComponent: () => import('./modules/agenda/lista/lista.component').then(m => m.ListaComponent), canActivate: [authGuard] },
  { path: 'agenda/nuevo', loadComponent: () => import('./modules/agenda/crear/crear.component').then(m => m.CrearContactoComponent), canActivate: [authGuard] },
  { path: 'agenda/editar/:id', loadComponent: () => import('./modules/agenda/editar/editar.component').then(m => m.EditarContactoComponent), canActivate: [authGuard] },
  { path: 'admin', loadComponent: () => import('./modules/admin/dashboard/dashboard.component').then(m => m.DashboardAdminComponent), canActivate: [adminGuard] },
  { path: 'admin/usuarios', loadComponent: () => import('./modules/admin/usuarios/usuarios.component').then(m => m.UsuariosComponent), canActivate: [adminGuard] },
  { path: 'admin/bitacora', loadComponent: () => import('./modules/admin/bitacora/bitacora.component').then(m => m.BitacoraComponent), canActivate: [adminGuard] },
  { path: '', redirectTo: '/login', pathMatch: 'full' },
  { path: '**', redirectTo: '/login' }
];
`);

writeFile('app.component.ts', `
import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { NavbarComponent } from './shared/components/navbar/navbar.component';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [RouterOutlet, NavbarComponent],
  template: \`
    <app-navbar></app-navbar>
    <div class="container mt-4 mb-5">
      <router-outlet></router-outlet>
    </div>
  \`
})
export class AppComponent {
  title = 'agenda-frontend';
}
`);

// INTERFACES
writeFile('core/interfaces/usuario.interface.ts', `
export interface IUsuario {
  id: number;
  cedula: string;
  nombre: string;
  correo: string;
  rol: string;
  is_active: boolean;
  primer_login: boolean;
}
export interface ILoginResponse {
  token: string;
  user: IUsuario;
  primer_login: boolean;
}
`);

writeFile('core/interfaces/contacto.interface.ts', `
export interface IContacto {
  id: number;
  nombres: string;
  apellidos: string;
  tipo: string;
  direccion: string;
  telefono: string;
  sitio_web?: string;
  empresa?: string;
  cargo?: string;
  eliminado?: boolean;
}
`);

writeFile('core/interfaces/catalogo-pregunta.interface.ts', `
export interface ICatalogoPregunta {
  id: number;
  pregunta: string;
}
export interface IPreguntaRecuperacion {
  catalogo_pregunta_id: number;
  respuesta: string;
}
`);

writeFile('core/interfaces/respuesta-api.interface.ts', `
export interface IRespuestaApi<T> {
  data: T;
  mensaje: string;
  status: boolean;
}
`);

// SERVICES
writeFile('core/services/base.service.ts', `
import { Injectable } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { throwError } from 'rxjs';

@Injectable({ providedIn: 'root' })
export abstract class BaseService {
  protected readonly baseUrl = 'http://localhost:8000/api';

  protected manejarError(error: HttpErrorResponse) {
    let msg = 'Error desconocido';
    if (error.error instanceof ErrorEvent) {
      msg = error.error.message;
    } else {
      msg = error.error?.mensaje || error.message;
    }
    return throwError(() => new Error(msg));
  }
}
`);

writeFile('core/services/auth.service.ts', `
import { Injectable, inject } from '@angular/core';
import { BaseService } from './base.service';
import { HttpClient } from '@angular/common/http';
import { ILoginResponse, IUsuario } from '../interfaces/usuario.interface';
import { tap, catchError } from 'rxjs/operators';
import { IRespuestaApi } from '../interfaces/respuesta-api.interface';

@Injectable({ providedIn: 'root' })
export class AuthService extends BaseService {
  private readonly http = inject(HttpClient);

  login(credenciales: any) {
    return this.http.post<IRespuestaApi<ILoginResponse>>(\`\${this.baseUrl}/auth/login\`, credenciales).pipe(
      tap(res => {
        if(res.data && res.data.token) {
          localStorage.setItem('agenda_token', res.data.token);
          localStorage.setItem('agenda_user', JSON.stringify(res.data.user));
        }
      }),
      catchError(this.manejarError)
    );
  }

  logout() {
    localStorage.removeItem('agenda_token');
    localStorage.removeItem('agenda_user');
  }

  getToken() { return localStorage.getItem('agenda_token'); }
  isLoggedIn() { return !!this.getToken(); }
  getCurrentUser(): IUsuario | null {
    const u = localStorage.getItem('agenda_user');
    return u ? JSON.parse(u) : null;
  }
  isSuperusuario() { return this.getCurrentUser()?.rol === 'admin'; }
  isPrimerLogin() { return this.getCurrentUser()?.primer_login === true; }
  
  // Other methods mock
  registro(data: any) { return this.http.post(\`\${this.baseUrl}/auth/registro\`, data).pipe(catchError(this.manejarError)); }
}
`);

writeFile('core/services/contacto.service.ts', `
import { Injectable, inject } from '@angular/core';
import { BaseService } from './base.service';
import { HttpClient } from '@angular/common/http';
import { IContacto } from '../interfaces/contacto.interface';
import { IRespuestaApi } from '../interfaces/respuesta-api.interface';
import { catchError } from 'rxjs/operators';

@Injectable({ providedIn: 'root' })
export class ContactoService extends BaseService {
  private readonly http = inject(HttpClient);
  obtenerTodos() { return this.http.get<IRespuestaApi<IContacto[]>>(\`\${this.baseUrl}/contactos\`).pipe(catchError(this.manejarError)); }
  // others mock
}
`);

writeFile('core/services/admin.service.ts', `
import { Injectable, inject } from '@angular/core';
import { BaseService } from './base.service';
import { HttpClient } from '@angular/common/http';
import { catchError } from 'rxjs/operators';

@Injectable({ providedIn: 'root' })
export class AdminService extends BaseService {
  private readonly http = inject(HttpClient);
  listarUsuarios() { return this.http.get(\`\${this.baseUrl}/admin/usuarios\`).pipe(catchError(this.manejarError)); }
}
`);

writeFile('core/services/catalogo.service.ts', `
import { Injectable, inject } from '@angular/core';
import { BaseService } from './base.service';
import { HttpClient } from '@angular/common/http';

@Injectable({ providedIn: 'root' })
export class CatalogoService extends BaseService {
  private readonly http = inject(HttpClient);
}
`);

// INTERCEPTOR
writeFile('core/interceptors/auth.interceptor.ts', `
import { HttpInterceptorFn } from '@angular/common/http';

export const authInterceptor: HttpInterceptorFn = (req, next) => {
  const token = localStorage.getItem('agenda_token');
  if (token) {
    req = req.clone({ setHeaders: { Authorization: \`Bearer \${token}\` } });
  }
  return next(req);
};
`);

// GUARDS
writeFile('core/guards/auth.guard.ts', `
import { inject } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../services/auth.service';

export const authGuard = () => {
  const auth = inject(AuthService);
  const router = inject(Router);
  if (!auth.isLoggedIn()) return router.parseUrl('/login');
  if (auth.isPrimerLogin()) return router.parseUrl('/primer-login');
  return true;
};
`);

writeFile('core/guards/admin.guard.ts', `
import { inject } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../services/auth.service';

export const adminGuard = () => {
  const auth = inject(AuthService);
  const router = inject(Router);
  if (!auth.isLoggedIn()) return router.parseUrl('/login');
  if (!auth.isSuperusuario()) return router.parseUrl('/agenda');
  return true;
};
`);

console.log("Scaffolding done.");
