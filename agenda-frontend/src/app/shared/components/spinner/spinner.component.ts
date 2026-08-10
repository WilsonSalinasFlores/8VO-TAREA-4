import { Component, Input } from '@angular/core';
import { NgIf } from '@angular/common';

@Component({
  selector: 'app-spinner',
  standalone: true,
  imports: [NgIf],
  template: `
    <div class="d-flex justify-content-center my-3" *ngIf="show">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Cargando...</span>
      </div>
    </div>
  `
})
export class SpinnerComponent {
  @Input() show = false;
}
