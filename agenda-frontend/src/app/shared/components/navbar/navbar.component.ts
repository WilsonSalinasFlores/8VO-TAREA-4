import { Component, inject } from '@angular/core';
import { RouterLink, Router } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';
import { NgIf } from '@angular/common';

@Component({
  selector: 'app-navbar',
  standalone: true,
  imports: [RouterLink, NgIf],
  template: `
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
  `
})
export class NavbarComponent {
  public auth = inject(AuthService);
  private router = inject(Router);

  logout() {
    this.auth.logout();
    this.router.navigate(['/login']);
  }
}
