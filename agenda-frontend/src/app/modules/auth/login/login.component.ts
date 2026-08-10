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
  template: `
    <div class="row justify-content-center">
      <div class="col-12 col-md-6 col-lg-4">
        <div class="card premium-card">
          <div class="card-body p-4">
            <h3 class="text-center mb-4">Iniciar Sesión</h3>
            <div *ngIf="errorMessage" class="alert alert-danger">{{ errorMessage }}</div>
            <form [formGroup]="loginForm" (ngSubmit)="onSubmit()" class="needs-validation" [class.was-validated]="submitted">
              <div class="mb-3">
                <label class="form-label">Cédula / Usuario</label>
                <input type="text" class="form-control" formControlName="cedula" required>
                <div class="invalid-feedback">Este campo es requerido.</div>
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
  `
})
export class LoginComponent {
  private fb = inject(FormBuilder);
  private auth = inject(AuthService);
  private router = inject(Router);
  
  loading = false;
  submitted = false;
  errorMessage = '';

  loginForm = this.fb.group({
    cedula: ['', Validators.required],
    password: ['', Validators.required]
  });

  onSubmit() {
    this.submitted = true;
    if (this.loginForm.invalid) return;
    
    this.loading = true;
    this.auth.login(this.loginForm.value).subscribe({
      next: () => {
        if (this.auth.isSuperusuario()) {
          this.router.navigate(['/admin/usuarios']);
        } else {
          this.router.navigate(['/agenda']);
        }
      },
      error: (err) => { 
        this.loading = false; 
        this.errorMessage = err.error?.mensaje || 'Credenciales incorrectas.';
      },
      complete: () => { this.loading = false; }
    });
  }
}
