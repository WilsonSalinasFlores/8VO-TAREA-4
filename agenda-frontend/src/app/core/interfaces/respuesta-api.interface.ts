export interface IRespuestaApi<T> {
  data: T;
  mensaje?: string;
  status?: boolean;
  exito?: boolean;
}
