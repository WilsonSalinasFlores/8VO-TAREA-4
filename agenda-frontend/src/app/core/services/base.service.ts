import { Injectable } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { throwError } from 'rxjs';

@Injectable({ providedIn: 'root' })
export abstract class BaseService {
  protected readonly baseUrl = 'http://localhost:8000/api';

  protected manejarError(error: HttpErrorResponse) {
    let msg = 'Error desconocido';
    if (error.error instanceof ErrorEvent) {
      msg = error.error.message;
    } else {
      msg = error.error?.mensaje || error.message;
    }
    return throwError(() => new Error(msg));
  }
}
