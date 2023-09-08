import { Injectable } from '@angular/core';
import { RequestService } from './request.service';
import { map } from 'rxjs/operators';
import { Inmueble } from '../interfaces/inmuebles';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class InmueblesService {

  public inmuebles: Inmueble[] = [];

  constructor(private req: RequestService) { }

  public obtenerInmuebles(): Observable<Inmueble[]> {
    return this.req.get("obtener_inmuebles.php").pipe(
      map((resultado: any) => {
        this.inmuebles = resultado as Inmueble[];
        return this.inmuebles;
      })
    );
  }
}
