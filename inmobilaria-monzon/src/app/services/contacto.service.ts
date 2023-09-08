import { Injectable } from '@angular/core';
import { RequestService } from './request.service';
import { HttpHeaders } from '@angular/common/http';

@Injectable()
export class ContactoService {

  constructor(private req:RequestService) { }

  
  NuevoMensaje(mensaje: FormData) {
    return this.req.postFormData("contacto.guardar_mensaje.php", mensaje);
  }

  MensajeFeed(mensaje: FormData) {
    console.log(mensaje);
    return this.req.postFormData("contacto.guardar_feedback.php", mensaje);
  }


  


}
