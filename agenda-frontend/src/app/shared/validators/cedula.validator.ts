import { AbstractControl, ValidationErrors } from '@angular/forms';
export class CedulaValidator {
  static validar(control: AbstractControl): ValidationErrors | null {
    const v = control.value;
    if (!v) return null;
    return /^\d{10}$/.test(v) ? null : { cedulaInvalida: true };
  }
}
