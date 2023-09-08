import { Injectable } from '@angular/core';

@Injectable()
export class ValidacionesService {

  constructor() { }


  public validarMail(mail: string) {

      var emailRegex = /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
      if (!emailRegex.test(mail)) {
          return false;
      }
      return true;
  }

}