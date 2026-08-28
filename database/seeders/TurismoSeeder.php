<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lugar;
use App\Models\Hotel;
use App\Models\Restaurante;

use App\Models\Transporte;

class TurismoSeeder extends Seeder
{
    public function run(): void
    {
        // LUGARES
        Lugar::insert([
            ['nombre' => 'Desierto de la Tatacoa', 'descripcion' => 'El desierto de la Tatacoa es un área de bosque seco tropical ubicado en el municipio de Villavieja. Es el segundo desierto más grande de Colombia con paisajes rojizos y grises únicos.', 'municipio' => 'Villavieja', 'latitud' => 3.2258, 'longitud' => -75.2231, 'categoria' => 'natural', 'calificacion' => 4.8, 'imagen' => null, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Parque Arqueológico San Agustín', 'descripcion' => 'Patrimonio de la Humanidad UNESCO. Alberga la colección más grande de monumentos religiosos y esculturas precolombinas en Sudamérica.', 'municipio' => 'San Agustín', 'latitud' => 1.8792, 'longitud' => -76.2696, 'categoria' => 'cultural', 'calificacion' => 4.9, 'imagen' => null, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Cascada Los Tres Chorros', 'descripcion' => 'Impresionante cascada de tres caídas de agua ubicada en San Agustín, rodeada de naturaleza exuberante y fauna diversa.', 'municipio' => 'San Agustín', 'latitud' => 1.8792, 'longitud' => -76.2696, 'categoria' => 'natural', 'calificacion' => 4.7, 'imagen' => null, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Termales de Rivera', 'descripcion' => 'Aguas termales naturales con propiedades medicinales, perfectas para el descanso y la relajación en el municipio de Rivera.', 'municipio' => 'Rivera', 'latitud' => 2.7822, 'longitud' => -75.2547, 'categoria' => 'natural', 'calificacion' => 4.6, 'imagen' => null, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Alto de los Ídolos', 'descripcion' => 'Sitio arqueológico declarado Patrimonio de la Humanidad, con estatuas monumentales de la cultura agustiniana.', 'municipio' => 'Isnos', 'latitud' => 1.9236, 'longitud' => -76.2967, 'categoria' => 'cultural', 'calificacion' => 4.7, 'imagen' => null, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Cueva de los Guácharos', 'descripcion' => 'Impresionante sistema de cuevas en Palestina, hogar de guácharos (aves nocturnas). Formaciones rocosas y estalactitas únicas.', 'municipio' => 'Palestina', 'latitud' => 1.7386, 'longitud' => -76.1364, 'categoria' => 'natural', 'calificacion' => 4.6, 'imagen' => null, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Caja de Agua', 'descripcion' => 'Hermoso espejo de agua natural en Paicol, ideal para nadar y disfrutar de la naturaleza. Rodeado de vegetación nativa.', 'municipio' => 'Paicol', 'latitud' => 2.4514, 'longitud' => -75.9989, 'categoria' => 'natural', 'calificacion' => 4.5, 'imagen' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // HOTELES
        Hotel::insert([
            ['nombre' => 'Hotel Akawanka', 'descripcion' => 'Hotel boutique ubicado en San Agustín con hermosas vistas a las montañas y atardeceres únicos. Ofrece habitaciones confortables y restaurante típico.', 'municipio' => 'San Agustín', 'latitud' => 1.8792, 'longitud' => -76.2696, 'precio_noche' => 180000, 'estrellas' => 4, 'telefono' => '3102345678', 'direccion' => 'Cra 11 # 4-10 San Agustín', 'imagen' => null, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Hotel Togoima', 'descripcion' => 'Hotel familiar con amplia zona verde y piscina en San Agustín. Perfecto para familias y grupos de viajeros.', 'municipio' => 'San Agustín', 'latitud' => 1.8792, 'longitud' => -76.2696, 'precio_noche' => 150000, 'estrellas' => 3, 'telefono' => '3113456789', 'direccion' => 'Calle 5 # 15-20 San Agustín', 'imagen' => null, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Hotel Timanco', 'descripcion' => 'Hotel de lujo ubicado en el centro de Neiva con todas las comodidades para viajeros de negocios y turistas.', 'municipio' => 'Neiva', 'latitud' => 2.9273, 'longitud' => -75.2819, 'precio_noche' => 250000, 'estrellas' => 5, 'telefono' => '3124567890', 'direccion' => 'Calle 20 # 5-30 Neiva', 'imagen' => null, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Glamping Tatacoa', 'descripcion' => 'Experiencia de glamping bajo las estrellas en el desierto de la Tatacoa. Carpas equipadas con camas y baño privado.', 'municipio' => 'Villavieja', 'latitud' => 3.2258, 'longitud' => -75.2231, 'precio_noche' => 200000, 'estrellas' => 4, 'telefono' => '3135678901', 'direccion' => 'Vereda La Victoria, Villavieja', 'imagen' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // RESTAURANTES
        Restaurante::insert([
            ['nombre' => 'Asadero El Huilense', 'descripcion' => 'El mejor asado huilense de Neiva, con más de 20 años de tradición. Especialidad en asado de cerdo y achiras.', 'municipio' => 'Neiva', 'latitud' => 2.9273, 'longitud' => -75.2819, 'tipo_cocina' => 'Típica huilense', 'precio_promedio' => 35000, 'telefono' => '3146789012', 'direccion' => 'Av. Circunvalar # 10-20 Neiva', 'imagen' => null, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Restaurante La Mona', 'descripcion' => 'Restaurante típico huilense especializado en tamales y bizcochos de achira. Ambiente familiar y acogedor.', 'municipio' => 'Neiva', 'latitud' => 2.9273, 'longitud' => -75.2819, 'tipo_cocina' => 'Típica huilense', 'precio_promedio' => 25000, 'telefono' => '3157890123', 'direccion' => 'Calle 8 # 6-15 Neiva', 'imagen' => null, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Fogón Agustiniano', 'descripcion' => 'Cocina tradicional del sur del Huila con vistas al paisaje montañoso de San Agustín. Especialidad en trucha y cocido.', 'municipio' => 'San Agustín', 'latitud' => 1.8792, 'longitud' => -76.2696, 'tipo_cocina' => 'Criolla', 'precio_promedio' => 30000, 'telefono' => '3168901234', 'direccion' => 'Cra 13 # 3-25 San Agustín', 'imagen' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // TOURS
        Tour::insert([
            ['nombre' => 'Tour Desierto Tatacoa', 'descripcion' => 'Recorrido completo por el desierto de la Tatacoa incluyendo observación de estrellas nocturna con telescopio profesional.', 'municipio' => 'Villavieja', 'latitud' => 3.2258, 'longitud' => -75.2231, 'precio' => 85000, 'duracion_dias' => 1, 'incluye' => 'Transporte, guía especializado, observación astronómica, hidratación', 'cupo_maximo' => 15, 'imagen' => null, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Tour Arqueológico San Agustín', 'descripcion' => 'Visita guiada por los principales sitios arqueológicos de San Agustín declarados Patrimonio de la Humanidad por la UNESCO.', 'municipio' => 'San Agustín', 'latitud' => 1.8792, 'longitud' => -76.2696, 'precio' => 120000, 'duracion_dias' => 2, 'incluye' => 'Transporte, guía certificado, entrada a parques, alojamiento 1 noche, desayuno', 'cupo_maximo' => 12, 'imagen' => null, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Tour Termales Rivera', 'descripcion' => 'Día de relax en los termales naturales de Rivera con masajes y tratamientos de bienestar incluidos.', 'municipio' => 'Rivera', 'latitud' => 2.7822, 'longitud' => -75.2547, 'precio' => 95000, 'duracion_dias' => 1, 'incluye' => 'Transporte, entrada a termales, masaje relajante, almuerzo típico', 'cupo_maximo' => 20, 'imagen' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);
        // TRANSPORTES
        Transporte::insert([
            ['nombre' => 'TransHuila Turismo', 'descripcion' => 'Servicio de transporte turistico en buses comodos y climatizados hacia los principales destinos del Huila: Tatacoa, San Agustin y Termales de Rivera.', 'municipio' => 'Neiva', 'tipo' => 'Bus', 'precio' => 45000, 'capacidad' => 40, 'telefono' => '3201234567', 'latitud' => 2.9273, 'longitud' => -75.2819, 'imagen' => null, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Vans Tatacoa Explorer', 'descripcion' => 'Transporte en vans para grupos pequenos al desierto de la Tatacoa. Salidas diarias desde Neiva con guia a bordo y flexibilidad de horarios.', 'municipio' => 'Villavieja', 'tipo' => 'Van', 'precio' => 60000, 'capacidad' => 12, 'telefono' => '3212345678', 'latitud' => 3.2258, 'longitud' => -75.2231, 'imagen' => null, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Taxi Turistico San Agustin', 'descripcion' => 'Recorridos privados por el parque arqueologico y sitios cercanos en vehiculos particulares. Conductor local certificado con amplio conocimiento de la zona.', 'municipio' => 'San Agustin', 'tipo' => 'Taxi turistico', 'precio' => 120000, 'capacidad' => 4, 'telefono' => '3223456789', 'latitud' => 1.8792, 'longitud' => -76.2696, 'imagen' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}