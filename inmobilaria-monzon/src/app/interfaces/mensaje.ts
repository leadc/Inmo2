export interface Mensaje {
    nombre:string,
    empresa:string,
    mail:string,
    telefono:string,
    mensaje:string,
    archivo: any
}

export interface MensajeError{
    severity:string,
    summary:string,
    detail:string
}