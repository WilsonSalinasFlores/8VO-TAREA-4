import { Component, inject, OnInit } from '@angular/core';
import { ContactoService } from '../../../core/services/contacto.service';
import { IContacto } from '../../../core/interfaces/contacto.interface';
import { Router, RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-lista',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './lista.component.html',
  styleUrls: []
})
export class ListaComponent implements OnInit {
  private contactoService = inject(ContactoService);
  private router = inject(Router);

  contactos: IContacto[] = [];
  cargando: boolean = true;
  error: string = '';

  ngOnInit() {
    this.cargarContactos();
  }

  cargarContactos() {
    this.cargando = true;
    this.contactoService.obtenerTodos().subscribe({
      next: (res) => {
        if (res.exito) {
          this.contactos = res.data;
        } else {
          this.error = 'No se pudieron cargar los contactos.';
        }
        this.cargando = false;
      },
      error: (err) => {
        this.error = 'Error de servidor.';
        this.cargando = false;
      }
    });
  }

  eliminar(id: number) {
    if (confirm('¿Está seguro de eliminar este contacto?')) {
      this.contactoService.eliminar(id).subscribe({
        next: (res) => {
          if (res.exito) {
            this.cargarContactos();
          } else {
            alert(res.mensaje || 'Error al eliminar');
          }
        },
        error: (err) => alert('Error de servidor al eliminar')
      });
    }
  }

  editar(id: number) {
    this.router.navigate(['/agenda/editar', id]);
  }
}
