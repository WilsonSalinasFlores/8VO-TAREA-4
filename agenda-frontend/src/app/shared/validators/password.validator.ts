import { AbstractControl, ValidationErrors } from '@angular/forms';
export class PasswordValidator {
  static validar(control: AbstractControl): ValidationErrors | null {
    const v = control.value;
    if (!v) return null;
    return /^(?=.*[A-Z])(?=.*\d).{8,}$/.test(v) ? null : { passwordDebil: true };
  }
}
