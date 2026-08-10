import { Injectable, inject } from '@angular/core';
import { BaseService } from './base.service';
import { HttpClient } from '@angular/common/http';
import { catchError } from 'rxjs/operators';
import { IRespuestaApi } from '../interfaces/respuesta-api.interface';
import { IUsuario } from '../interfaces/usuario.interface';

@Injectable({ providedIn: 'root' })
export class AdminService extends BaseService {
  private readonly http = inject(HttpClient);
  
  listarUsuarios() {
    return this.http.get<IRespuestaApi<IUsuario[]>>(`${this.baseUrl}/admin/usuarios`).pipe(catchError(this.manejarError));
  }

  verAgendaUsuario(idUsuario: number) {
    return this.http.get<IRespuestaApi<any[]>>(`${this.baseUrl}/admin/usuarios/${idUsuario}/agenda`).pipe(catchError(this.manejarError));
  }

  cambiarEstado(idUsuario: number, estado: boolean) {
    return this.http.patch<IRespuestaApi<any>>(`${this.baseUrl}/admin/usuarios/${idUsuario}/estado`, { is_active: estado }).pipe(catchError(this.manejarError));
  }

  listarBitacora() {
    return this.http.get<IRespuestaApi<any[]>>(`${this.baseUrl}/admin/bitacora`).pipe(catchError(this.manejarError));
  }
}
