import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from "@angular/common/http";
import { Observable } from 'rxjs';

@Injectable()
export class RequestService {

  public url_base = "http://192.168.1.37:90/Inmobilaria_Mozon/API/"; //test

  constructor(private http: HttpClient) {
    if (window.location.origin.indexOf('applusitv') >= 0) {
      this.url_base = "https://app.applusitv.uy/WEB_URUGUAY/Uruguay-WebAPI/";
    }
  }

  get(url: string, params: any = "", headers: HttpHeaders | null = null) {
    let params_string: string = "";
    for (const key in params) {
      if (params.hasOwnProperty(key)) {
        params_string = params_string + key + "=" + params[key] + "&";
      }
    }
    
    if (params_string) {
      params_string = "?" + params_string;
    }
    
    return this.http.get(this.url_base + url + params_string, this.headers(headers));
  }

  postFormData(url: string, formData: FormData) {
    return this.http.post(this.url_base + url, formData);
  }
  post(url: string, body: any, headers: HttpHeaders | null = null) {
    return this.http.post(this.url_base + url, body, this.headers(headers));
  }

  fetch(url: string, body: any, method: string = 'POST') {
    return fetch(this.url_base + url, { method: method, body: body });
  }

  private headers(headers: HttpHeaders | null = null): { headers: HttpHeaders } {
    if (headers === null) {
      return { headers: new HttpHeaders({ 'Content-Type': 'application/x-www-form-urlencoded' }) };
    }
    return { headers };
  }

}
