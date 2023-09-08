import { Component, OnInit, ViewChild } from '@angular/core';
import { UntypedFormGroup, UntypedFormBuilder, Validators } from '@angular/forms';
import { GoogleMap } from '@angular/google-maps';
import { emailValidator } from '../../utils/app-validators';
import { ContactoService } from 'src/app/services/contacto.service';
import { MessageService } from 'primeng/api';

@Component({
  selector: 'app-footer',
  templateUrl: './footer.component.html',
  styleUrls: ['./footer.component.scss'],
  providers:[MessageService]
})
export class FooterComponent implements OnInit {

    @ViewChild(GoogleMap) map: GoogleMap;
    center: google.maps.LatLngLiteral = { lat: 40.678178, lng: -73.944158};
    zoom: number = 12;
    public preloader = false;
    markerOptions: google.maps.MarkerOptions = { draggable: false };
    markerPositions: google.maps.LatLngLiteral[] = [
      { lat: 40.678178, lng: -73.944158 }
    ];
    mapStyles: any = [
        {
            "featureType": "all",
            "elementType": "labels.text.fill",
            "stylers": [
                {
                    "saturation": 36
                },
                {
                    "color": "#000000"
                },
                {
                    "lightness": 40
                }
            ]
        },
        {
            "featureType": "all",
            "elementType": "labels.text.stroke",
            "stylers": [
                {
                    "visibility": "on"
                },
                {
                    "color": "#000000"
                },
                {
                    "lightness": 16
                }
            ]
        },
        {
            "featureType": "all",
            "elementType": "labels.icon",
            "stylers": [
                {
                    "visibility": "off"
                }
            ]
        },
        {
            "featureType": "administrative",
            "elementType": "geometry.fill",
            "stylers": [
                {
                    "color": "#000000"
                },
                {
                    "lightness": 20
                }
            ]
        },
        {
            "featureType": "administrative",
            "elementType": "geometry.stroke",
            "stylers": [
                {
                    "color": "#000000"
                },
                {
                    "lightness": 17
                },
                {
                    "weight": 1.2
                }
            ]
        },
        {
            "featureType": "administrative",
            "elementType": "labels.text.fill",
            "stylers": [
                {
                    "color": "#8b9198"
                }
            ]
        },
        {
            "featureType": "landscape",
            "elementType": "geometry",
            "stylers": [
                {
                    "color": "#000000"
                },
                {
                    "lightness": 20
                }
            ]
        },
        {
            "featureType": "landscape",
            "elementType": "geometry.fill",
            "stylers": [
                {
                    "color": "#323336"
                }
            ]
        },
        {
            "featureType": "landscape.man_made",
            "elementType": "geometry.stroke",
            "stylers": [
                {
                    "color": "#414954"
                }
            ]
        },
        {
            "featureType": "poi",
            "elementType": "geometry",
            "stylers": [
                {
                    "color": "#000000"
                },
                {
                    "lightness": 21
                }
            ]
        },
        {
            "featureType": "poi",
            "elementType": "geometry.fill",
            "stylers": [
                {
                    "color": "#2e2f31"
                }
            ]
        },
        {
            "featureType": "road",
            "elementType": "labels.text.fill",
            "stylers": [
                {
                    "color": "#7a7c80"
                }
            ]
        },
        {
            "featureType": "road.highway",
            "elementType": "geometry.fill",
            "stylers": [
                {
                    "color": "#242427"
                },
                {
                    "lightness": 17
                }
            ]
        },
        {
            "featureType": "road.highway",
            "elementType": "geometry.stroke",
            "stylers": [
                {
                    "color": "#202022"
                },
                {
                    "lightness": 29
                },
                {
                    "weight": 0.2
                }
            ]
        },
        {
            "featureType": "road.arterial",
            "elementType": "geometry",
            "stylers": [
                {
                    "color": "#000000"
                },
                {
                    "lightness": 18
                }
            ]
        },
        {
            "featureType": "road.arterial",
            "elementType": "geometry.fill",
            "stylers": [
                {
                    "color": "#393a3f"
                }
            ]
        },
        {
            "featureType": "road.arterial",
            "elementType": "geometry.stroke",
            "stylers": [
                {
                    "color": "#202022"
                }
            ]
        },
        {
            "featureType": "road.local",
            "elementType": "geometry",
            "stylers": [
                {
                    "color": "#000000"
                },
                {
                    "lightness": 16
                }
            ]
        },
        {
            "featureType": "road.local",
            "elementType": "geometry.fill",
            "stylers": [
                {
                    "color": "#393a3f"
                }
            ]
        },
        {
            "featureType": "road.local",
            "elementType": "geometry.stroke",
            "stylers": [
                {
                    "color": "#202022"
                }
            ]
        },
        {
            "featureType": "transit",
            "elementType": "geometry",
            "stylers": [
                {
                    "color": "#000000"
                },
                {
                    "lightness": 19
                }
            ]
        },
        {
            "featureType": "water",
            "elementType": "geometry",
            "stylers": [
                {
                    "color": "#000000"
                },
                {
                    "lightness": 17
                }
            ]
        },
        {
            "featureType": "water",
            "elementType": "geometry.fill",
            "stylers": [
                {
                    "color": "#202124"
                }
            ]
        }
    ];
    mapOptions: google.maps.MapOptions = {
        styles: this.mapStyles
    }
    feedbackForm: UntypedFormGroup;
    subscribeForm: UntypedFormGroup;

    constructor(public formBuilder: UntypedFormBuilder,private servicioContacto: ContactoService,private MessageService: MessageService) { }

    ngOnInit() {
        this.feedbackForm = this.formBuilder.group({ 
            email: ['', Validators.compose([Validators.required, emailValidator])], 
            message: ['', Validators.required]
        });
        this.subscribeForm = this.formBuilder.group({
            email: ['', Validators.compose([Validators.required, emailValidator])]
        })      
    }

    onFeedbackFormSubmit() {
        if (this.feedbackForm.valid) {
            // Crear FormData y agregar los campos necesarios
            const formData = new FormData();
            formData.append('email', this.feedbackForm.get('email').value);
            formData.append('message', this.feedbackForm.get('message').value);

            // Llamar a la función para enviar el mensaje
            this.enviarMensaje(formData);
        }
    }

    enviarMensaje(mensaje: FormData) {
        this.preloader = true;
        this.servicioContacto.MensajeFeed(mensaje).subscribe(
            () => {
                // Show success message
             
                this.MessageService.add({ severity: 'success', summary: 'Contacto', detail: 'Mensaje enviado correctamente!' });
                // Reset the form
                this.preloader = false;
                this.feedbackForm.reset();
                
                // Scroll to the top of the page
                window.scrollTo(0, 0);
                
                // You can also use Angular Material's MatSnackBar to display a snackbar or toast
                // Example: this.snackBar.open('Mensaje enviado correctamente', 'Cerrar', { duration: 3000 });
            },
            error => {
                // Show error message
                console.error('Error al enviar el mensaje');
                this.preloader = false;
            }
        );
    }

    ngAfterViewInit() {
        // this.map.googleMap.setOptions({styles: this.mapStyles});
    }

    public onSubscribeFormSubmit(values:Object):void {
        if (this.subscribeForm.valid) {
            console.log(values);
        }
    }

}
