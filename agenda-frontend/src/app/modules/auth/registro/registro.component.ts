import { Component, inject, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule, FormArray } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';
import { CatalogoService } from '../../../core/services/catalogo.service';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-registro',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './registro.component.html',
  styleUrls: []
})
export class RegistroComponent implements OnInit {
  private fb = inject(FormBuilder);
  private authService = inject(AuthService);
  private catalogoService = inject(CatalogoService);
  private router = inject(Router);

  registroForm!: FormGroup;
  preguntas: any[] = [];
  mensaje: string = '';
  error: string = '';

  ngOnInit() {
    this.initForm();
    this.cargarPreguntas();
  }

  initForm() {
    this.registroForm = this.fb.group({
      cedula: ['', [Validators.required, Validators.pattern('^[0-9]{10}$')]],
      nombre: ['', [Validators.required, Validators.minLength(3)]],
      correo: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(8), Validators.pattern('^(?=.*[A-Z])(?=.*\\d).*$')]],
      respuestas: this.fb.array([])
    });
  }

  get respuestasFormArray() {
    return this.registroForm.get('respuestas') as FormArray;
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
      },
      error: (err) => { this.error = 'Error cargando preguntas de seguridad.'; }
    });
  }

  addRespuesta() {
    this.respuestasFormArray.push(this.fb.group({
      id_pregunta: ['', Validators.required],
      respuesta: ['', Validators.required]
    }));
  }

  onSubmit() {
    if (this.registroForm.valid) {
      const selectedIds = this.respuestasFormArray.value.map((r: any) => r.id_pregunta);
      const uniqueIds = new Set(selectedIds);
      if (uniqueIds.size !== 3) {
        this.error = 'Debe seleccionar 3 preguntas diferentes.';
        return;
      }
      this.error = '';
      const payload = { ...this.registroForm.value };
      payload.preguntas = payload.respuestas.map((r: any) => ({
        id: r.id_pregunta,
        respuesta: r.respuesta
      }));
      delete payload.respuestas;

      this.authService.registro(payload).subscribe({
        next: (res) => {
          if (res.exito) {
            this.mensaje = 'Registro exitoso. Puede iniciar sesión ahora.';
            setTimeout(() => this.router.navigate(['/auth/login']), 2000);
          } else {
            this.error = res.mensaje || 'Error en el registro';
          }
        },
        error: (err) => {
          if (err.error && err.error.errores) {
            this.error = Object.values(err.error.errores).flat().join(' ');
          } else {
            this.error = err.error?.mensaje || 'Error de servidor';
          }
        }
      });
    } else {
      this.registroForm.markAllAsTouched();
    }
  }
}
