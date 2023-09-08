<?php
require_once 'clases/acceso_datos/DB_Conexion.php';
require_once 'clases/entidades/class.XMLToAssoc.php';
require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';


use BasicORM\LOGS\Log;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Inmuebles
{
    public $id;
    public $title;
    public $desc;
    public $propertyType;
    public $propertyStatus;
    public $city;
    public $formattedAddress;
    public $featured;
    public $bedrooms;
    public $bathrooms;
    public $garages;
    public $yearBuilt;
    public $ratingsCount;
    public $ratingsValue;
    public $published;
    public $lastUpdate;
    public $views;
    

    // public static function ObtenerInmuebles() {
    //     $resultado = ConecxionPDO::GetResultados("SELECT * FROM Properties");
    //     $inmuebles_array = [];
    
    //     foreach ($resultado as $inmuebleData) {
    //         $inmueble = new Inmuebles();
    //         $inmueble->id = $inmuebleData['id'];
    //         $inmueble->title = $inmuebleData['title'];
    //         $inmueble->desc = $inmuebleData['desc'];
    //         $inmueble->propertyType = $inmuebleData['propertyType'];
    //         $inmueble->propertyStatus = $inmuebleData['propertyStatus'];
    //         $inmueble->city = $inmuebleData['city'];
    //         $inmueble->formattedAddress = $inmuebleData['formattedAddress'];
    //         $inmueble->featured = $inmuebleData['featured'];
    //         $inmueble->bedrooms = $inmuebleData['bedrooms'];
    //         $inmueble->bathrooms = $inmuebleData['bathrooms'];
    //         $inmueble->garages = $inmuebleData['garages'];
    //         $inmueble->yearBuilt = $inmuebleData['yearBuilt'];
    //         $inmueble->ratingsCount = $inmuebleData['ratingsCount'];
    //         $inmueble->ratingsValue = $inmuebleData['ratingsValue'];
    //         $inmueble->published = $inmuebleData['published'];
    //         $inmueble->lastUpdate = $inmuebleData['lastUpdate'];
    //         $inmueble->views = $inmuebleData['views'];
    
    //         // Additional Features
    //         $additionalFeaturesData = json_decode($inmuebleData['additionalFeatures'], true);
    //         $additionalFeatures = [];
    //         if (is_array($additionalFeaturesData)) {
    //             foreach ($additionalFeaturesData as $feature) {
    //                 $additionalFeature = new AdditionalFeature();
    //                 $additionalFeature->id = $feature['id'];
    //                 $additionalFeature->name = $feature['name'];
    //                 $additionalFeature->value = $feature['value'];
    //                 $additionalFeatures[] = $additionalFeature;
    //             }
    //         }
    //         $inmueble->additionalFeatures = $additionalFeatures;
    
    //         // Gallery
    //         $galleryData = json_decode($inmuebleData['gallery'], true);
    //         $gallery = [];
    //         if (is_array($galleryData)) {
    //             foreach ($galleryData as $image) {
    //                 $galleryItem = new Gallery();
    //                 $galleryItem->id = $image['id'];
    //                 $galleryItem->small = $image['small'];
    //                 $galleryItem->medium = $image['medium'];
    //                 $galleryItem->big = $image['big'];
    //                 $gallery[] = $galleryItem;
    //             }
    //         }
    //         $inmueble->gallery = $gallery;
    
    //         // Videos
    //         $videosData = json_decode($inmuebleData['videos'], true);
    //         $videos = [];
    //         if (is_array($videosData)) {
    //             foreach ($videosData as $video) {
    //                 $videoItem = new Video();
    //                 $videoItem->id = $video['id'];
    //                 $videoItem->name = $video['name'];
    //                 $videoItem->link = $video['link'];
    //                 $videos[] = $videoItem;
    //             }
    //         }
    //         $inmueble->videos = $videos;
      
    //         $inmuebles_array[] = $inmueble;
           


    //     }
    
    //     // Ordenar el array de inmuebles por ID
    //     usort($inmuebles_array, function ($a, $b) {
    //         return $a->id - $b->id;
    //     });
    
    //     return $inmuebles_array;
    // }


    public static function ObtenerInmueblePorId($id) {
        $inmuebleData = ConecxionPDO::GetResultados("SELECT * FROM Properties WHERE id = $id");
        
        if (count($inmuebleData) > 0) {
            $inmueble = new stdClass(); // Crea un objeto Inmueble
    
            // Asigna propiedades directamente al objeto $inmueble
            $inmueble->id = (int)$inmuebleData[0]['id'];
            $inmueble->title = $inmuebleData[0]['title'];
            $inmueble->desc = $inmuebleData[0]['desc'];
            $inmueble->propertyType = $inmuebleData[0]['propertyType'];
            $inmueble->propertyStatus = self::ObtenerStatus($inmueble->id); 
            $inmueble->city = $inmuebleData[0]['city'];
            $inmueble->zipcode = $inmuebleData[0]['zip'];
            $inmueble->neighborhood = self::ObtenerNeighborhood($inmueble->id);
            $inmueble->street = self::ObtenerNeighborhood($inmueble->id);
            $inmueble->location = self::ObtenerLocation($inmueble->id);
            $inmueble->formattedAddress = $inmuebleData[0]['formattedAddress'];
            $inmueble->features = self::ObtenerFeatures($inmueble->id);
            $inmueble->featured = (bool)$inmuebleData[0]['featured'];
            $inmueble->priceDollar = self::ObtenerPrice($inmueble->id); // Usar función para obtener precios en dólares
            $inmueble->priceEuro = self::ObtenerPrice($inmueble->id); // Usar función para obtener precios en euros
            $inmueble->bedrooms = (int)$inmuebleData[0]['bedrooms'];
            $inmueble->bathrooms = (int)$inmuebleData[0]['bathrooms'];
            $inmueble->garages = (int)$inmuebleData[0]['garages'];
            $inmueble->area = self::ObtenerArea($inmueble->id); 
            $inmueble->yearBuilt = (int)$inmuebleData[0]['yearBuilt'];
            $inmueble->ratingsCount = (int)$inmuebleData[0]['ratingsCount'];
            $inmueble->ratingsValue = (int)$inmuebleData[0]['ratingsValue'];
            $inmueble->additionalFeatures = self::ObtenerAdditionalFeatures($inmueble->id);
            $inmueble->gallery = self::ObtenerGallery($inmueble->id);
            $inmueble->plans = self::ObtenerPlans($inmueble->id);
            $inmueble->videos = self::ObtenerVideos($inmueble->id);
            $inmueble->published = $inmuebleData[0]['published'];
            $inmueble->lastUpdate = $inmuebleData[0]['lastUpdate'];
            $inmueble->views = (int)$inmuebleData[0]['views'];
            
            // ... continúa asignando las propiedades restantes
    
            return $inmueble;
        } else {
            return null;
        }
    }
    
    // Resto del código de funciones como lo tienes
    
    
    


    public static function ObtenerInmuebles() {
        $resultado = ConecxionPDO::GetResultados("SELECT * FROM Properties");
        $inmuebles_array = [];
        foreach ($resultado as $inmuebleData) {
            $inmueble = new Inmuebles();
            $inmueble->id = $inmuebleData['id'];
            $inmueble->title = $inmuebleData['title'];
            $inmueble->desc = $inmuebleData['desc'];
            $inmueble->propertyType = $inmuebleData['propertyType'];
            $inmueble->propertyStatus = self::ObtenerStatus($inmueble->id);
            $inmueble->city = $inmuebleData['city'];
            $inmueble->formattedAddress = $inmuebleData['formattedAddress'];
            $inmueble->neighborhood = self::ObtenerNeighborhood($inmueble->id); // Usar función para obtener barrios
            $inmueble->location = self::ObtenerLocation($inmueble->id); // Usar función para obtener ubicación
            $inmueble->features = self::ObtenerFeatures($inmueble->id);
            $inmueble->featured = (bool)$inmuebleData['featured'];
            $inmueble->priceDollar = self::ObtenerPrice($inmueble->id); // Usar función para obtener precios en dólares
            $inmueble->priceEuro = self::ObtenerPrice($inmueble->id); // Usar función para obtener precios en euros
            $inmueble->bedrooms = $inmuebleData['bedrooms'];
            $inmueble->bathrooms = $inmuebleData['bathrooms'];
            $inmueble->garages = $inmuebleData['garages'];
            $inmueble->area = self::ObtenerArea($inmueble->id); // Usar función para obtener el área
            $inmueble->yearBuilt = $inmuebleData['yearBuilt'];
            $inmueble->ratingsCount = $inmuebleData['ratingsCount'];
            $inmueble->ratingsValue = $inmuebleData['ratingsValue'];
            $inmueble->additionalFeatures = self::ObtenerAdditionalFeatures($inmueble->id);
            $inmueble->gallery = self::ObtenerGallery($inmueble->id);
            $inmueble->plans = self::ObtenerPlans($inmueble->id);
            $inmueble->videos = self::ObtenerVideos($inmueble->id);
            $inmueble->published = $inmuebleData['published'];
            $inmueble->lastUpdate = $inmuebleData['lastUpdate'];
            $inmueble->views = $inmuebleData['views'];

            $inmuebles_array[] = $inmueble;
        }
        
        // Ordenar el array de inmuebles por ID
        usort($inmuebles_array, function ($a, $b) {
            return $a->id - $b->id;
        });
        
        return $inmuebles_array;
    }




    //func
    public static function ObtenerFeatures($propertyId) {
        $featuresData = ConecxionPDO::GetResultados("SELECT Features.name FROM Features
                                                     INNER JOIN PropertyFeatures ON Features.id = PropertyFeatures.featureId
                                                     WHERE PropertyFeatures.propertyId = $propertyId");

    
        $features = [];
        foreach ($featuresData as $featureData) {
            $features[] = $featureData['name'];
        }
        
        return $features;
    }
    
    public static function ObtenerAdditionalFeatures($propertyId) {
        $additionalFeaturesData = ConecxionPDO::GetResultados("SELECT name, value FROM AdditionalFeatures WHERE propertyId = $propertyId");
        
        $additionalFeatures = [];
        foreach ($additionalFeaturesData as $featureData) {
            $feature = new stdClass();
            $feature->name = $featureData['name'];
            $feature->value = $featureData['value'];
            $additionalFeatures[] = $feature;
        }
        
        return $additionalFeatures;
    }
    
    public static function ObtenerGallery($propertyId) {
        $galleryData = ConecxionPDO::GetResultados("SELECT id, small, medium, big FROM Gallery WHERE propertyId = $propertyId");
        
        $gallery = [];
        foreach ($galleryData as $imageData) {
            $image = new stdClass();
            $image->id = $imageData['id'];
            $image->small = $imageData['small'];
            $image->medium = $imageData['medium'];
            $image->big = $imageData['big'];
            $gallery[] = $image;
        }
        
        return $gallery;
    }


    public static function ObtenerArea($propertyId) {
        $resultado = ConecxionPDO::GetResultados("SELECT * FROM Area WHERE propertyId = $propertyId");

        if (count($resultado) > 0) {
            $area = new stdClass();
            $area->value = $resultado[0]['value'];
            $area->unit = $resultado[0]['unit'];
            return $area;
        }

        return null; // O un valor por defecto si es necesario
    }
    
    public static function ObtenerPlans($propertyId) {
        $plansData = ConecxionPDO::GetResultados("SELECT name, [desc], areaValue, areaUnit, rooms, baths, image FROM Plans WHERE propertyId = $propertyId");
        
        $plans = [];
        foreach ($plansData as $planData) {
            $plan = new stdClass(); // Crea un objeto Plan
            $plan->name = $planData['name'];
            $plan->desc = $planData['desc'];
            $plan->area = new stdClass(); // Crea un objeto Area en el plan
            $plan->area->value = $planData['areaValue'];
            $plan->area->unit = $planData['areaUnit'];
            $plan->rooms = $planData['rooms'];
            $plan->baths = $planData['baths'];
            $plan->image = $planData['image'];
            $plans[] = $plan;
        }
        
        return $plans;
    }
    
    public static function ObtenerVideos($propertyId) {
        $videosData = ConecxionPDO::GetResultados("SELECT name, link FROM Video WHERE propertyId = $propertyId");
        
        $videos = [];
        foreach ($videosData as $videoData) {
            $video = new stdClass();
            $video->name = $videoData['name'];
            $video->link = $videoData['link'];
            $videos[] = $video;
        }
        
        return $videos;
    }



    public static function ObtenerLocation($propertyId) {
        $locationData = ConecxionPDO::GetResultados("SELECT lat, lng FROM Location WHERE propertyId = $propertyId");
        
        if (count($locationData) > 0) {
            $location = [
                "lat" => floatval($locationData[0]['lat']), // Convertir a número flotante
                "lng" => floatval($locationData[0]['lng'])  // Convertir a número flotante
            ];
            return $location;
        } else {
            return null; // Devolver null si no hay datos de ubicación
        }
    }
    
    // public static function ObtenerPrice($propertyId) {
    //     $priceData = ConecxionPDO::GetResultados("SELECT sale, rent FROM Price WHERE propertyId = $propertyId");
    //     return $priceData;
    // }


    public static function ObtenerPrice($propertyId) {
        $priceData = ConecxionPDO::GetResultados("SELECT sale, rent FROM Price WHERE propertyId = $propertyId");
        
        if (count($priceData) > 0) {
            $price = [
                "sale" => isset($priceData[0]['sale']) ? floatval($priceData[0]['sale']) : null,
                "rent" => isset($priceData[0]['rent']) ? floatval($priceData[0]['rent']) : null
            ];
            return $price;
        } else {
            return null; // Devolver null si no hay datos de precio
        }
    }




    public static function ObtenerNeighborhood($propertyId) {
        $neighborhoodIds = ConecxionPDO::GetResultados("SELECT neighborhoodId FROM PropertyNeighborhood WHERE propertyId = $propertyId");
        $neighborhoods = [];
        
        foreach ($neighborhoodIds as $neighborhoodIdData) {
            $neighborhoodIdValue = $neighborhoodIdData['neighborhoodId'];
            $neighborhoodData = ConecxionPDO::GetResultados("SELECT name FROM Neighborhood WHERE id = $neighborhoodIdValue");
            
            if (count($neighborhoodData) > 0) {
                $neighborhoodName = $neighborhoodData[0]['name'];
                $neighborhoods[] = $neighborhoodName;
            }
        }
    
        return $neighborhoods; // Devuelve el array de nombres de barrios
    }
    

    public static function ObtenerStatus($propertyId) {
        $statusIds = ConecxionPDO::GetResultados("SELECT propertyStatusId FROM PropertyStatuses WHERE propertyId = $propertyId");
        $statuses = [];
        
        foreach ($statusIds as $statusIdData) {
            $statusIdValue = $statusIdData['propertyStatusId'];
            $statusData = ConecxionPDO::GetResultados("SELECT name FROM PropertyStatus WHERE id = $statusIdValue");
            
            if (count($statusData) > 0) {
                $statusName = $statusData[0]['name'];
                $statuses[] = $statusName;
            }
        }
    
        return $statuses;
    }
    
    
    
    

    //
    
   
    


    public static function ObtenerPropiedadesDestacadas() {
        $resultado = ConecxionPDO::GetResultados("SELECT * FROM Properties WHERE featured = 1");
        $propiedadesDestacadas = [];
    
        foreach ($resultado as $inmuebleData) {
            $inmueble = new Inmuebles();
            $inmueble->id = $inmuebleData['id'];
            $inmueble->title = $inmuebleData['title'];
            $inmueble->desc = $inmuebleData['desc'];
            $inmueble->propertyType = $inmuebleData['propertyType'];
            $inmueble->propertyStatus = $inmuebleData['propertyStatus'];
            $inmueble->city = $inmuebleData['city'];
            $inmueble->formattedAddress = $inmuebleData['formattedAddress'];
            $inmueble->neighborhood = self::ObtenerNeighborhood($inmueble->id); // Usar función para obtener barrios
            $inmueble->location = self::ObtenerLocation($inmueble->id); // Usar función para obtener ubicación
            $inmueble->features = self::ObtenerFeatures($inmueble->id);
            $inmueble->featured = (bool)$inmuebleData['featured'];
            $inmueble->priceDollar = self::ObtenerPrice($inmueble->id); // Usar función para obtener precios en dólares
            $inmueble->priceEuro = self::ObtenerPrice($inmueble->id); // Usar función para obtener precios en euros
            $inmueble->bedrooms = $inmuebleData['bedrooms'];
            $inmueble->bathrooms = $inmuebleData['bathrooms'];
            $inmueble->garages = $inmuebleData['garages'];
            $inmueble->area = self::ObtenerArea($inmueble->id); // Usar función para obtener el área
            $inmueble->yearBuilt = $inmuebleData['yearBuilt'];
            $inmueble->ratingsCount = $inmuebleData['ratingsCount'];
            $inmueble->ratingsValue = $inmuebleData['ratingsValue'];
            $inmueble->additionalFeatures = self::ObtenerAdditionalFeatures($inmueble->id);
            $inmueble->gallery = self::ObtenerGallery($inmueble->id);
            $inmueble->plans = self::ObtenerPlans($inmueble->id);
            $inmueble->videos = self::ObtenerVideos($inmueble->id);
            $inmueble->published = $inmuebleData['published'];
            $inmueble->lastUpdate = $inmuebleData['lastUpdate'];
            $inmueble->views = $inmuebleData['views'];

            $propiedadesDestacadas[] = $inmueble;
        }
        
        // Ordenar el array de inmuebles por ID
        usort($propiedadesDestacadas, function ($a, $b) {
            return $a->id - $b->id;
        });
        
        return $propiedadesDestacadas;
    }


    public static function ObtenerMaximoIdPropiedades() {
        $resultado = ConecxionPDO::GetResultados("SELECT MAX(ID) AS max_id FROM Properties");
        $maxId = $resultado[0]['max_id'];
    
        return $maxId;
    }
    

    public static function ObtenerPropiedadesRelacionadas() {
        $resultado = ConecxionPDO::GetResultados("SELECT * FROM [Propiedades] WHERE id <> :id LIMIT 3", ["id" => $id]);
        $propiedadesRelacionadas = [];
    
        foreach ($resultado as $inmuebleData) {
            $inmueble = new Inmuebles();
            $inmueble->id = $inmuebleData['id'];
            $inmueble->title = $inmuebleData['title'];
            $inmueble->desc = $inmuebleData['desc'];
            $inmueble->propertyType = $inmuebleData['propertyType'];
            $inmueble->propertyStatus = $inmuebleData['propertyStatus'];
            $inmueble->city = $inmuebleData['city'];
            $inmueble->formattedAddress = $inmuebleData['formattedAddress'];
            $inmueble->neighborhood = self::ObtenerNeighborhood($inmueble->id); // Usar función para obtener barrios
            $inmueble->location = self::ObtenerLocation($inmueble->id); // Usar función para obtener ubicación
            $inmueble->features = self::ObtenerFeatures($inmueble->id);
            $inmueble->featured = (bool)$inmuebleData['featured'];
            $inmueble->priceDollar = self::ObtenerPrice($inmueble->id); // Usar función para obtener precios en dólares
            $inmueble->priceEuro = self::ObtenerPrice($inmueble->id); // Usar función para obtener precios en euros
            $inmueble->bedrooms = $inmuebleData['bedrooms'];
            $inmueble->bathrooms = $inmuebleData['bathrooms'];
            $inmueble->garages = $inmuebleData['garages'];
            $inmueble->area = self::ObtenerArea($inmueble->id); // Usar función para obtener el área
            $inmueble->yearBuilt = $inmuebleData['yearBuilt'];
            $inmueble->ratingsCount = $inmuebleData['ratingsCount'];
            $inmueble->ratingsValue = $inmuebleData['ratingsValue'];
            $inmueble->additionalFeatures = self::ObtenerAdditionalFeatures($inmueble->id);
            $inmueble->gallery = self::ObtenerGallery($inmueble->id);
            $inmueble->plans = self::ObtenerPlans($inmueble->id);
            $inmueble->videos = self::ObtenerVideos($inmueble->id);
            $inmueble->published = $inmuebleData['published'];
            $inmueble->lastUpdate = $inmuebleData['lastUpdate'];
            $inmueble->views = $inmuebleData['views'];

            $propiedadesRelacionadas[] = $inmueble;
        }
        
        // Ordenar el array de inmuebles por ID
        usort($propiedadesRelacionadas, function ($a, $b) {
            return $a->id - $b->id;
        });
        
        return $propiedadesRelacionadas;
    }
    
    
    


    public static function ObtenerUbicaciones() {
        $resultado = ConecxionPDO::GetResultados("SELECT DISTINCT propertyId, lat, lng FROM Location");
        $ubicaciones = [];
    
        foreach ($resultado as $ubicacionData) {
          $ubicacion = [
            'propertyId' => $ubicacionData['propertyId'],
            'lat' => $ubicacionData['lat'],
            'lng' => $ubicacionData['lng']
          ];
          $ubicaciones[] = $ubicacion;
        }
    
        return $ubicaciones;
      }


      public static function NuevoInmueble($formData) {
        // Acceder a los datos del formulario básico
    
        $maxPropertyId = self::ObtenerMaximoIdPropiedades();
    
        $nextPropertyId = $maxPropertyId + 1;
    
        $propertyFolder = "C:/Apache24/htdocs/Inmobilaria_Mozon/inmobilaria-monzon/src/assets/images/props/{$nextPropertyId}";
    
        if (!file_exists($propertyFolder)) {
            mkdir($propertyFolder, 0777, true); // Crea la carpeta si no existe
        }
    
        $basicData = $formData['basic'];
        $title = $basicData['title'];
        $desc = $basicData['desc'];
        $priceDollar = $basicData['priceDollar'];
        $priceEuro = $basicData['priceEuro'];
        $propertyType = $basicData['propertyType']['id']; // Accede al ID del tipo de propiedad
        $propertyStatus = $basicData['propertyStatus'];
    
        // Acceder a las fotos de la galería
        $images = $formData['images'];

        $imagePaths = [];
    
        foreach ($images as $index => $imageData) {
            // Genera un nombre único para la imagen
            $imageName = uniqid() . '_' . rand(1000, 9999) . '.jpg'; // Puedes usar la extensión correspondiente
    
            // Ruta completa para la imagen
            $imagePath = "{$propertyFolder}/{$imageName}";
    
            // Mueve el archivo a la carpeta
            if (move_uploaded_file($imageData['tempFilePath'], $imagePath)) {
                // Operación exitosa
                // Registra un mensaje de éxito en el log
                Log::WriteLog(MAIN_LOG, ["Imagen recibida y guardada con éxito: $imagePath --> NOMBRE DEL ARCHIVO <--"]);
            } else {
                // Manejar el error, por ejemplo, registrar el error
                Log::WriteLog(MAIN_LOG, ["Error al mover el archivo: $imagePath --> NOMBRE DEL ARCHIVO <--"]);
            }
    
            // Ahora $imagePath contiene la ruta de la imagen guardada, que puedes almacenar en la base de datos o hacer lo que necesites con ella.
            $imagePaths[] = $imagePath;
    
            // Log y almacenamiento en la base de datos...
        }
        // Acceder a los datos de la dirección
        $addressData = $formData['address'];
        $location = $addressData['location'];
        $city = $addressData['city'];
        $zipCode = $addressData['zipCode'];
        $neighborhood = $addressData['neighborhood'];
        $street = $addressData['street'];
    
        // Acceder a los datos adicionales
        $additionalData = $formData['additional'];
        $bedrooms = $additionalData['bedrooms'];
        $bathrooms = $additionalData['bathrooms'];
        $garages = $additionalData['garages'];
        $area = $additionalData['area'];
        $yearBuilt = $additionalData['yearBuilt'];
        $features = $additionalData['features'];
    
        // Acceder a los datos de medios
        $mediaData = $formData['media'];
        $videos = $mediaData['videos'];
        $plans = $mediaData['plans'];
        $additionalFeatures = $mediaData['additionalFeatures'];
        $featured = $mediaData['featured'];
    
        // Aquí puedes hacer lo que necesites con estos datos, como guardarlos en la base de datos o realizar alguna otra lógica de negocio específica.
    
        // Por ejemplo, para imprimir los datos en formato JSON de nuevo
        $result = [
            'basicData' => $basicData,
            'galleryImages' => $galleryImages,
            'addressData' => $addressData,
            'additionalData' => $additionalData,
            'mediaData' => $mediaData
        ];
    
        return $result;
    }
    




    
}
