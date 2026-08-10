import { Routes } from '@angular/router';
import { authGuard } from './core/guards/auth.guard';
import { adminGuard } from './core/guards/admin.guard';

export const routes: Routes = [
  { path: 'login', loadComponent: () => import('./modules/auth/login/login.component').then(m => m.LoginComponent) },
  { path: 'registro', loadComponent: () => import('./modules/auth/registro/registro.component').then(m => m.RegistroComponent) },
  { path: 'recuperar', loadComponent: () => import('./modules/auth/recuperar/recuperar.component').then(m => m.RecuperarComponent) },
  { path: 'primer-login', loadComponent: () => import('./modules/auth/primer-login/primer-login.component').then(m => m.PrimerLoginComponent), canActivate: [authGuard] },
  { path: 'agenda', loadComponent: () => import('./modules/agenda/lista/lista.component').then(m => m.ListaComponent), canActivate: [authGuard] },
  { path: 'agenda/nuevo', loadComponent: () => import('./modules/agenda/crear/crear.component').then(m => m.CrearComponent), canActivate: [authGuard] },
  { path: 'agenda/editar/:id', loadComponent: () => import('./modules/agenda/editar/editar.component').then(m => m.EditarComponent), canActivate: [authGuard] },
  { path: 'admin', loadComponent: () => import('./modules/admin/dashboard/dashboard.component').then(m => m.DashboardComponent), canActivate: [adminGuard] },
  { path: 'admin/usuarios', loadComponent: () => import('./modules/admin/usuarios/usuarios.component').then(m => m.UsuariosComponent), canActivate: [adminGuard] },
  { path: 'admin/bitacora', loadComponent: () => import('./modules/admin/bitacora/bitacora.component').then(m => m.BitacoraComponent), canActivate: [adminGuard] },
  { path: '', redirectTo: '/login', pathMatch: 'full' },
  { path: '**', redirectTo: '/login' }
];
