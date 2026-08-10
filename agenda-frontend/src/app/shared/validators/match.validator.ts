import { AbstractControl, ValidationErrors, ValidatorFn } from '@angular/forms';
export class MatchValidator {
  static validar(field1: string, field2: string): ValidatorFn {
    return (control: AbstractControl): ValidationErrors | null => {
      const f1 = control.get(field1)?.value;
      const f2 = control.get(field2)?.value;
      if (f1 !== f2) {
        control.get(field2)?.setErrors({ mismatch: true });
        return { mismatch: true };
      }
      return null;
    };
  }
}
