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

// VALIDATORS
writeFile('shared/validators/cedula.validator.ts', `
import { AbstractControl, ValidationErrors } from '@angular/forms';
export class CedulaValidator {
  static validar(control: AbstractControl): ValidationErrors | null {
    const v = control.value;
    if (!v) return null;
    return /^\\d{10}$/.test(v) ? null : { cedulaInvalida: true };
  }
}
`);

writeFile('shared/validators/password.validator.ts', `
import { AbstractControl, ValidationErrors } from '@angular/forms';
export class PasswordValidator {
  static validar(control: AbstractControl): ValidationErrors | null {
    const v = control.value;
    if (!v) return null;
    return /^(?=.*[A-Z])(?=.*\\d).{8,}$/.test(v) ? null : { passwordDebil: true };
  }
}
`);

writeFile('shared/validators/match.validator.ts', `
import { AbstractControl, ValidationErrors, ValidatorFn } from '@angular/forms';
export class MatchValidator {
  static validar(field1: string, field2: string): ValidatorFn {
    return (control: AbstractControl): ValidationErrors | null => {
      const f1 = control.get(field1)?.value;
      const f2 = control.get(field2)?.value;
      if (f1 !== f2) {
        control.get(field2)?.setErrors({ mismatch: true });
        return { mismatch: true };
      }
      return null;
    };
  }
}
`);

// SHARED COMPONENTS
writeFile('shared/components/navbar/navbar.component.ts', `
import { Component, inject } from '@angular/core';
import { RouterLink, Router } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';
import { NgIf } from '@angular/common';

@Component({
  selector: 'app-navbar',
  standalone: true,
  imports: [RouterLink, NgIf],
  template: \`
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4" *ngIf="auth.isLoggedIn()">
      <div class="container">
        <a class="navbar-brand" routerLink="/agenda">Agenda Virtual</a>
        <div class="collapse navbar-collapse">
          <ul class="navbar-nav me-auto">
            <li class="nav-item">
              <a class="nav-link" routerLink="/agenda">Contactos</a>
            </li>
            <li class="nav-item" *ngIf="auth.isSuperusuario()">
              <a class="nav-link" routerLink="/admin">Administración</a>
            </li>
          </ul>
          <div class="d-flex text-white me-3 align-items-center">
            <i class="bi bi-person-circle me-2"></i> {{ auth.getCurrentUser()?.nombre }}
          </div>
          <button class="btn btn-light btn-sm" (click)="logout()">Cerrar Sesión</button>
        </div>
      </div>
    </nav>
  \`
})
export class NavbarComponent {
  public auth = inject(AuthService);
  private router = inject(Router);

  logout() {
    this.auth.logout();
    this.router.navigate(['/login']);
  }
}
`);

writeFile('shared/components/alerta/alerta.component.ts', `
import { Component, Input } from '@angular/core';
import { NgClass, NgIf } from '@angular/common';

@Component({
  selector: 'app-alerta',
  standalone: true,
  imports: [NgClass, NgIf],
  template: \`
    <div *ngIf="mensaje" class="alert alert-{{tipo}} alert-dismissible fade show" role="alert">
      {{ mensaje }}
      <button *ngIf="dismissible" type="button" class="btn-close" (click)="mensaje = ''"></button>
    </div>
  \`
})
export class AlertaComponent {
  @Input() tipo: 'success'|'danger'|'warning'|'info' = 'info';
  @Input() mensaje = '';
  @Input() dismissible = true;
}
`);

writeFile('shared/components/spinner/spinner.component.ts', `
import { Component, Input } from '@angular/core';
import { NgIf } from '@angular/common';

@Component({
  selector: 'app-spinner',
  standalone: true,
  imports: [NgIf],
  template: \`
    <div class="d-flex justify-content-center my-3" *ngIf="show">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Cargando...</span>
      </div>
    </div>
  \`
})
export class SpinnerComponent {
  @Input() show = false;
}
`);

writeFile('shared/components/modal-confirmacion/modal-confirmacion.component.ts', `
import { Component } from '@angular/core';

@Component({
  selector: 'app-modal-confirmacion',
  standalone: true,
  template: \`
    <div class="modal fade" id="confirmModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Confirmar</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <p>¿Estás seguro de realizar esta acción?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary">Confirmar</button>
          </div>
        </div>
      </div>
    </div>
  \`
})
export class ModalConfirmacionComponent {}
`);

// MODULES: AUTH
writeFile('modules/auth/login/login.component.ts', `
import { Component, inject } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';
import { CedulaValidator } from '../../../shared/validators/cedula.validator';
import { NgIf } from '@angular/common';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [ReactiveFormsModule, RouterLink, NgIf],
  template: \`
    <div class="row justify-content-center">
      <div class="col-12 col-md-6 col-lg-4">
        <div class="card premium-card">
          <div class="card-body p-4">
            <h3 class="text-center mb-4">Iniciar Sesión</h3>
            <form [formGroup]="loginForm" (ngSubmit)="onSubmit()" class="needs-validation" [class.was-validated]="submitted">
              <div class="mb-3">
                <label class="form-label">Cédula</label>
                <input type="text" class="form-control" formControlName="cedula" required>
                <div class="invalid-feedback">Ingrese una cédula válida de 10 dígitos.</div>
              </div>
              <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" class="form-control" formControlName="password" required>
                <div class="invalid-feedback">La contraseña es requerida.</div>
              </div>
              <button type="submit" class="btn btn-primary w-100 mb-3" [disabled]="loading">
                <span *ngIf="loading" class="spinner-border spinner-border-sm me-2"></span>Ingresar
              </button>
              <div class="text-center">
                <a routerLink="/registro" class="d-block mb-2">Crear nueva cuenta</a>
                <a routerLink="/recuperar" class="text-muted small">¿Olvidaste tu contraseña?</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  \`
})
export class LoginComponent {
  private fb = inject(FormBuilder);
  private auth = inject(AuthService);
  private router = inject(Router);
  
  loading = false;
  submitted = false;

  loginForm = this.fb.group({
    cedula: ['', [Validators.required, CedulaValidator.validar]],
    password: ['', Validators.required]
  });

  onSubmit() {
    this.submitted = true;
    if (this.loginForm.invalid) return;
    
    this.loading = true;
    this.auth.login(this.loginForm.value).subscribe({
      next: () => this.router.navigate(['/agenda']),
      error: () => { this.loading = false; },
      complete: () => { this.loading = false; }
    });
  }
}
`);

writeFile('modules/auth/registro/registro.component.ts', `import { Component } from '@angular/core';\n@Component({ selector: 'app-registro', standalone: true, template: '<div>Registro</div>' }) export class RegistroComponent {}`);
writeFile('modules/auth/recuperar/recuperar.component.ts', `import { Component } from '@angular/core';\n@Component({ selector: 'app-recuperar', standalone: true, template: '<div>Recuperar</div>' }) export class RecuperarComponent {}`);
writeFile('modules/auth/primer-login/primer-login.component.ts', `import { Component } from '@angular/core';\n@Component({ selector: 'app-primer-login', standalone: true, template: '<div>Primer Login</div>' }) export class PrimerLoginComponent {}`);

writeFile('modules/agenda/lista/lista.component.ts', `import { Component } from '@angular/core';\n@Component({ selector: 'app-lista', standalone: true, template: '<div>Lista Agenda</div>' }) export class ListaComponent {}`);
writeFile('modules/agenda/crear/crear.component.ts', `import { Component } from '@angular/core';\n@Component({ selector: 'app-crear', standalone: true, template: '<div>Crear Contacto</div>' }) export class CrearContactoComponent {}`);
writeFile('modules/agenda/editar/editar.component.ts', `import { Component } from '@angular/core';\n@Component({ selector: 'app-editar', standalone: true, template: '<div>Editar Contacto</div>' }) export class EditarContactoComponent {}`);

writeFile('modules/admin/dashboard/dashboard.component.ts', `import { Component } from '@angular/core';\n@Component({ selector: 'app-dashboard', standalone: true, template: '<div>Dashboard Admin</div>' }) export class DashboardAdminComponent {}`);
writeFile('modules/admin/usuarios/usuarios.component.ts', `import { Component } from '@angular/core';\n@Component({ selector: 'app-usuarios', standalone: true, template: '<div>Usuarios Admin</div>' }) export class UsuariosComponent {}`);
writeFile('modules/admin/bitacora/bitacora.component.ts', `import { Component } from '@angular/core';\n@Component({ selector: 'app-bitacora', standalone: true, template: '<div>Bitacora Admin</div>' }) export class BitacoraComponent {}`);

console.log('Rest of scaffolding done');
