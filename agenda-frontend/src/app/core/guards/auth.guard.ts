import { inject } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../services/auth.service';

export const authGuard = (route: any, state: any) => {
  const auth = inject(AuthService);
  const router = inject(Router);
  if (!auth.isLoggedIn()) return router.parseUrl('/login');
  if (auth.isPrimerLogin() && state.url !== '/primer-login') return router.parseUrl('/primer-login');
  if (!auth.isPrimerLogin() && state.url === '/primer-login') return router.parseUrl('/agenda');
  return true;
};
