import { Component, inject, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule, FormArray } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';
import { CatalogoService } from '../../../core/services/catalogo.service';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-primer-login',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './primer-login.component.html',
  styleUrls: []
})
export class PrimerLoginComponent implements OnInit {
  private fb = inject(FormBuilder);
  private authService = inject(AuthService);
  private catalogoService = inject(CatalogoService);
  private router = inject(Router);

  primerLoginForm!: FormGroup;
  preguntas: any[] = [];
  mensaje: string = '';
  error: string = '';

  ngOnInit() {
    if (!this.authService.isPrimerLogin()) {
      this.router.navigate(['/agenda']);
    }
    this.initForm();
    this.cargarPreguntas();
  }

  initForm() {
    this.primerLoginForm = this.fb.group({
      password: ['', [Validators.required, Validators.minLength(6)]],
      respuestas: this.fb.array([])
    });
  }

  get respuestasFormArray() {
    return this.primerLoginForm.get('respuestas') as FormArray;
  }

  cargarPreguntas() {
    this.catalogoService.obtenerPreguntas().subscribe({
      next: (res) => {
        if (res.exito) {
          this.preguntas = res.data;
          this.addRespuesta();
          this.addRespuesta();
          this.addRespuesta();
        }
      }
    });
  }

  addRespuesta() {
    this.respuestasFormArray.push(this.fb.group({
      id_pregunta: ['', Validators.required],
      respuesta: ['', Validators.required]
    }));
  }

  onSubmit() {
    if (this.primerLoginForm.valid) {
      const selectedIds = this.respuestasFormArray.value.map((r: any) => r.id_pregunta);
      const uniqueIds = new Set(selectedIds);
      if (uniqueIds.size !== 3) {
        this.error = 'Debe seleccionar 3 preguntas diferentes.';
        return;
      }
      this.error = '';
      
      const payload = {
        password: this.primerLoginForm.value.password,
        respuestas: this.primerLoginForm.value.respuestas
      };
      
      this.authService.primerLogin(payload).subscribe({
        next: (res) => {
          if (res.exito) {
            this.mensaje = 'Configuración completada exitosamente.';
            setTimeout(() => {
              if (this.authService.isSuperusuario()) {
                this.router.navigate(['/admin/dashboard']);
              } else {
                this.router.navigate(['/agenda']);
              }
            }, 2000);
          } else {
            this.error = res.mensaje || 'Error al completar la configuración.';
          }
        },
        error: (err) => {
          this.error = 'Error de servidor.';
        }
      });
    } else {
      this.primerLoginForm.markAllAsTouched();
    }
  }
}
