import { Component, inject, OnInit } from '@angular/core';
import { AdminService } from '../../../core/services/admin.service';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-bitacora',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './bitacora.component.html',
  styleUrls: []
})
export class BitacoraComponent implements OnInit {
  private adminService = inject(AdminService);

  registros: any[] = [];
  cargando: boolean = true;
  error: string = '';

  ngOnInit() {
    this.cargarBitacora();
  }

  cargarBitacora() {
    this.cargando = true;
    this.adminService.listarBitacora().subscribe({
      next: (res) => {
        if (res.exito) {
          this.registros = res.data;
        } else {
          this.error = 'No se pudo cargar la bitácora.';
        }
        this.cargando = false;
      },
      error: (err) => {
        this.error = 'Error de servidor.';
        this.cargando = false;
      }
    });
  }
}
