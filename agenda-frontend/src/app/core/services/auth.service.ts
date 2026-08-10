import { Injectable, inject } from '@angular/core';
import { BaseService } from './base.service';
import { HttpClient } from '@angular/common/http';
import { ILoginResponse, IUsuario } from '../interfaces/usuario.interface';
import { tap, catchError, switchMap as import_rxjs_switchMap } from 'rxjs/operators';
import { IRespuestaApi } from '../interfaces/respuesta-api.interface';

@Injectable({ providedIn: 'root' })
export class AuthService extends BaseService {
  private readonly http = inject(HttpClient);

  login(credenciales: any) {
    return this.http.post<IRespuestaApi<ILoginResponse>>(`${this.baseUrl}/auth/login`, credenciales).pipe(
      tap(res => {
        if(res.data && res.data.token) {
          localStorage.setItem('agenda_token', res.data.token);
          localStorage.setItem('agenda_user', JSON.stringify(res.data.user));
        }
      }),
      catchError(this.manejarError)
    );
  }

  registro(data: any) {
    return this.http.post<IRespuestaApi<any>>(`${this.baseUrl}/auth/registro`, data).pipe(catchError(this.manejarError));
  }

  recuperarPassword(data: any) {
    return this.http.post<IRespuestaApi<any>>(`${this.baseUrl}/auth/recuperar`, data).pipe(catchError(this.manejarError));
  }

  verificarPreguntas(data: any) {
    return this.http.post<IRespuestaApi<any>>(`${this.baseUrl}/auth/verificar-preguntas`, data).pipe(catchError(this.manejarError));
  }

  primerLogin(data: any) {
    const pregData = { preguntas: data.respuestas };
    const passData = { 
        password_actual: data.password_actual, 
        password_nuevo: data.password_nuevo, 
        password_confirmacion: data.password_confirmacion 
    };

    return this.http.post<IRespuestaApi<any>>(`${this.baseUrl}/auth/primer-login/preguntas`, pregData).pipe(
      import_rxjs_switchMap(() => this.http.post<IRespuestaApi<any>>(`${this.baseUrl}/auth/primer-login`, passData)),
      tap(res => {
        if (res.exito) {
          const user = this.getCurrentUser();
          if (user) {
            user.primer_login = false;
            localStorage.setItem('agenda_user', JSON.stringify(user));
          }
        }
      }),
      catchError(this.manejarError)
    );
  }

  logout() {
    localStorage.removeItem('agenda_token');
    localStorage.removeItem('agenda_user');
  }

  getToken() { return localStorage.getItem('agenda_token'); }
  isLoggedIn() { return !!this.getToken(); }
  getCurrentUser(): IUsuario | null {
    const u = localStorage.getItem('agenda_user');
    return u ? JSON.parse(u) : null;
  }
  isSuperusuario() { return this.getCurrentUser()?.rol === 'superusuario'; }
  isPrimerLogin() { return this.getCurrentUser()?.primer_login === true; }
}
