import { Injectable, inject } from '@angular/core';
import { BaseService } from './base.service';
import { HttpClient } from '@angular/common/http';
import { IContacto } from '../interfaces/contacto.interface';
import { IRespuestaApi } from '../interfaces/respuesta-api.interface';
import { catchError } from 'rxjs/operators';

@Injectable({ providedIn: 'root' })
export class ContactoService extends BaseService {
  private readonly http = inject(HttpClient);
  
  obtenerTodos() {
    return this.http.get<IRespuestaApi<IContacto[]>>(`${this.baseUrl}/contactos`).pipe(catchError(this.manejarError));
  }

  obtenerPorId(id: number) {
    return this.http.get<IRespuestaApi<IContacto>>(`${this.baseUrl}/contactos/${id}`).pipe(catchError(this.manejarError));
  }

  crear(contacto: Partial<IContacto>) {
    return this.http.post<IRespuestaApi<IContacto>>(`${this.baseUrl}/contactos`, contacto).pipe(catchError(this.manejarError));
  }

  actualizar(id: number, contacto: Partial<IContacto>) {
    return this.http.put<IRespuestaApi<IContacto>>(`${this.baseUrl}/contactos/${id}`, contacto).pipe(catchError(this.manejarError));
  }

  eliminar(id: number) {
    return this.http.delete<IRespuestaApi<any>>(`${this.baseUrl}/contactos/${id}`).pipe(catchError(this.manejarError));
  }
}
