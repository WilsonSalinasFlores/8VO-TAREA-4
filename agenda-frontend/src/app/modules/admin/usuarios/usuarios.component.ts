import { Component, inject, OnInit } from '@angular/core';
import { AdminService } from '../../../core/services/admin.service';
import { IUsuario } from '../../../core/interfaces/usuario.interface';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-usuarios',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './usuarios.component.html',
  styleUrls: []
})
export class UsuariosComponent implements OnInit {
  private adminService = inject(AdminService);

  usuarios: IUsuario[] = [];
  cargando: boolean = true;
  error: string = '';
  
  agendaUsuarioSeleccionado: any[] = [];
  usuarioAuditoria: string = '';
  viendoAgenda: boolean = false;

  ngOnInit() {
    this.cargarUsuarios();
  }

  cargarUsuarios() {
    this.cargando = true;
    this.adminService.listarUsuarios().subscribe({
      next: (res) => {
        if (res.exito) {
          this.usuarios = res.data;
        } else {
          this.error = 'No se pudieron cargar los usuarios.';
        }
        this.cargando = false;
      },
      error: (err) => {
        this.error = 'Error de servidor.';
        this.cargando = false;
      }
    });
  }

  cambiarEstado(usuario: IUsuario) {
    const nuevoEstado = !usuario.is_active;
    const accion = nuevoEstado ? 'habilitar' : 'inhabilitar';
    if (confirm(`¿Está seguro de ${accion} al usuario ${usuario.nombre}?`)) {
      this.adminService.cambiarEstado(usuario.id, nuevoEstado).subscribe({
        next: (res) => {
          if (res.exito) {
            usuario.is_active = nuevoEstado;
          } else {
            alert('Error al cambiar el estado del usuario.');
          }
        },
        error: (err) => alert('Error de servidor al cambiar el estado.')
      });
    }
  }

  verAgenda(usuario: IUsuario) {
    this.adminService.verAgendaUsuario(usuario.id).subscribe({
      next: (res) => {
        if (res.exito) {
          this.agendaUsuarioSeleccionado = res.data;
          this.usuarioAuditoria = usuario.nombre;
          this.viendoAgenda = true;
        } else {
          alert('Error al obtener la agenda del usuario.');
        }
      },
      error: (err) => alert('Error de servidor al consultar la agenda.')
    });
  }

  cerrarAgenda() {
    this.viendoAgenda = false;
    this.agendaUsuarioSeleccionado = [];
    this.usuarioAuditoria = '';
  }
}
