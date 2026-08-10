import { Injectable, inject } from '@angular/core';
import { BaseService } from './base.service';
import { HttpClient } from '@angular/common/http';
import { catchError } from 'rxjs/operators';
import { IRespuestaApi } from '../interfaces/respuesta-api.interface';

@Injectable({ providedIn: 'root' })
export class CatalogoService extends BaseService {
  private readonly http = inject(HttpClient);

  obtenerPreguntas() {
    return this.http.get<IRespuestaApi<any[]>>(`${this.baseUrl}/catalogo/preguntas`).pipe(catchError(this.manejarError));
  }
}
