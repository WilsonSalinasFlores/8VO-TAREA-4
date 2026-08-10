import { Component, inject, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { ContactoService } from '../../../core/services/contacto.service';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-crear',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterModule],
  templateUrl: './crear.component.html',
  styleUrls: []
})
export class CrearComponent implements OnInit {
  private fb = inject(FormBuilder);
  private contactoService = inject(ContactoService);
  private router = inject(Router);

  crearForm!: FormGroup;
  mensaje: string = '';
  error: string = '';

  ngOnInit() {
    this.crearForm = this.fb.group({
      nombres: ['', Validators.required],
      apellidos: ['', Validators.required],
      tipo: ['Personal', Validators.required],
      direccion: ['', Validators.required],
      telefono: ['', [Validators.required, Validators.pattern('^[0-9]{10}$')]],
      sitio_web: [''],
      empresa: [''],
      cargo: ['']
    });
  }

  onSubmit() {
    if (this.crearForm.valid) {
      this.contactoService.crear(this.crearForm.value).subscribe({
        next: (res) => {
          if (res.exito) {
            this.mensaje = 'Contacto creado exitosamente.';
            setTimeout(() => this.router.navigate(['/agenda']), 1500);
          } else {
            this.error = res.mensaje || 'Error al crear contacto.';
          }
        },
        error: (err) => {
          this.error = 'Error de servidor.';
        }
      });
    } else {
      this.crearForm.markAllAsTouched();
    }
  }
}
