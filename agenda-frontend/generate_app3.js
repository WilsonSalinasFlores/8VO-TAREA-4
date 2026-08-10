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

writeFile('modules/agenda/lista/lista.component.ts', `
import { Component, inject, OnInit } from '@angular/core';
import { ContactoService } from '../../../core/services/contacto.service';
import { IContacto } from '../../../core/interfaces/contacto.interface';
import { NgFor, NgIf } from '@angular/common';
import { RouterLink } from '@angular/router';

@Component({
  selector: 'app-lista',
  standalone: true,
  imports: [NgFor, NgIf, RouterLink],
  template: \`
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Mi Agenda</h2>
      <a routerLink="/agenda/nuevo" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Nuevo Contacto
      </a>
    </div>
    
    <div class="card premium-card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Nombres</th>
                <th>Apellidos</th>
                <th>Tipo</th>
                <th>Teléfono</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr *ngFor="let c of contactos">
                <td>{{ c.nombres }}</td>
                <td>{{ c.apellidos }}</td>
                <td><span class="badge bg-info">{{ c.tipo }}</span></td>
                <td>{{ c.telefono }}</td>
                <td>
                  <a [routerLink]="['/agenda/editar', c.id]" class="btn btn-sm btn-outline-primary me-2"><i class="bi bi-pencil"></i></a>
                  <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                </td>
              </tr>
              <tr *ngIf="contactos.length === 0">
                <td colspan="5" class="text-center text-muted py-4">No hay contactos registrados.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  \`
})
export class ListaComponent implements OnInit {
  private contactoSvc = inject(ContactoService);
  contactos: IContacto[] = [];

  ngOnInit() {
    this.contactoSvc.obtenerTodos().subscribe({
      next: (res) => {
        if(res.data) this.contactos = res.data;
      }
    });
  }
}
`);

writeFile('modules/admin/dashboard/dashboard.component.ts', `
import { Component } from '@angular/core';
import { RouterLink } from '@angular/router';

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [RouterLink],
  template: \`
    <h2 class="mb-4">Panel de Administración</h2>
    <div class="row">
      <div class="col-md-6 mb-4">
        <div class="card premium-card bg-primary text-white h-100">
          <div class="card-body">
            <h5 class="card-title"><i class="bi bi-people-fill me-2"></i>Usuarios</h5>
            <p class="card-text">Gestiona los usuarios del sistema, cambia sus estados y revisa sus agendas.</p>
            <a routerLink="/admin/usuarios" class="btn btn-light mt-3">Ir a Usuarios</a>
          </div>
        </div>
      </div>
      <div class="col-md-6 mb-4">
        <div class="card premium-card bg-success text-white h-100">
          <div class="card-body">
            <h5 class="card-title"><i class="bi bi-journal-text me-2"></i>Bitácora</h5>
            <p class="card-text">Revisa el registro de auditoría y todas las acciones realizadas en el sistema.</p>
            <a routerLink="/admin/bitacora" class="btn btn-light mt-3">Ir a Bitácora</a>
          </div>
        </div>
      </div>
    </div>
  \`
})
export class DashboardAdminComponent {}
`);

console.log('Script 3 done');
