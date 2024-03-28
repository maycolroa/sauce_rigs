<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\IndustrialSecure\RoadSafety\DriverInfractionType;
use App\Models\IndustrialSecure\RoadSafety\DriverInfractionTypeCode;

class AddInformationTypeInfractionsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $types_infractions = [
            'Tipo A', 
            'Tipo B', 
            'Tipo C', 
            'Tipo D', 
            'Tipo E', 
            'Tipo F', 
            'Tipo G', 
            'Tipo H', 
            'Tipo I'
        ];

        foreach ($types_infractions as $key => $value) {
            DriverInfractionType::firstOrCreate(
                [
                    'name' => $value
                ],
                [
                    'name' => $value
                ]
            );
        }

        $codes = [
            [
                'type_id' => 1,
                'code' => 'A.1',
                'description' => 'No transitar por la derecha de la vía.'
            ],
            [
                'type_id' => 1,
                'code' => 'A.2',
                'description' => 'Agarrarse de otro vehículo en circulación.'
            ],
            [
                'type_id' => 1,
                'code' => 'A.3',
                'description' => 'Transportar personas o cosas que disminuyan su visibilidad e incomoden la conducción.'
            ],
            [
                'type_id' => 1,
                'code' => 'A.4',
                'description' => 'Transitar por andenes y demás lugares destinados al tránsito de peatones.'
            ],
            [
                'type_id' => 1,
                'code' => 'A.5',
                'description' => 'No respetar las señales de tránsito.'
            ],
            [
                'type_id' => 1,
                'code' => 'A.6',
                'description' => 'Transitar sin los dispositivos luminosos requeridos.'
            ],
            [
                'type_id' => 1,
                'code' => 'A.7',
                'description' => 'Transitar sin dispositivos que permitan la parada inmediata o con ellos, pero en estado defectuoso.'
            ],
            [
                'type_id' => 1,
                'code' => 'A.8',
                'description' => 'Transitar por zonas prohibidas.'
            ],
            [
                'type_id' => 1,
                'code' => 'A.9',
                'description' => 'Adelantar entre dos (2) vehículos automotores que estén en sus respectivos carriles.'
            ],
            [
                'type_id' => 1,
                'code' => 'A.10',
                'description' => 'Conducir por la vía férrea o por zonas de protección y seguridad.'
            ],
            [
                'type_id' => 1,
                'code' => 'A.11',
                'description' => 'Transitar por zonas restringidas o por vías de alta velocidad como autopistas y arterias, en este caso el vehículo automotor será inmovilizado.'
            ],
            [
                'type_id' => 1,
                'code' => 'A.12',
                'description' => 'Prestar servicio público con este tipo de vehículos. Además, el vehículo será inmovilizado por primera vez por el término de 5 días, por segunda vez 20 días y por tercera vez 40 días.'
            ],
            [
                'type_id' => 2,
                'code' => 'B.1',
                'description' => '*Conducir un vehículo sin llevar consigo la licencia de conducción.'
            ],
            [
                'type_id' => 2,
                'code' => 'B.2',
                'description' => '*Conducir un vehículo con la licencia de conducción vencida.'
            ],
            [
                'type_id' => 2,
                'code' => 'B.3',
                'description' => '*Sin placas, o sin el permiso vigente expedido por autoridad de tránsito.'
            ],
            [
                'type_id' => 2,
                'code' => 'B.4',
                'description' => '*Con placas adulteradas.'
            ],
            [
                'type_id' => 2,
                'code' => 'B.5',
                'description' => '*Con una sola placa, o sin el permiso vigente expedido por autoridad de tránsito.'
            ],
            [
                'type_id' => 2,
                'code' => 'B.6',
                'description' => '*Con placas falsas. En los anteriores casos los vehículos serán inmovilizados.'
            ],
            [
                'type_id' => 2,
                'code' => 'B.7',
                'description' => 'No informar a la autoridad de tránsito competente el cambio de motor o color de un vehículo. En ambos casos, el vehículo será inmovilizado.'
            ],
            [
                'type_id' => 2,
                'code' => 'B.8',
                'description' => 'No pagar el peaje en los sitios establecidos.'
            ],
            [
                'type_id' => 2,
                'code' => 'B.9',
                'description' => 'Utilizar equipos de sonido a volúmenes que incomoden a los pasajeros de un vehículo de servicio público.'
            ],
            [
                'type_id' => 2,
                'code' => 'B.10',
                'description' => 'Conducir un vehículo con vidrios polarizados, entintados u oscurecidos, sin portar el permiso respectivo de acuerdo a la reglamentación existente sobre la materia'
            ],
            [
                'type_id' => 2,
                'code' => 'B.11',
                'description' => 'Conducir  un  vehículo  con  propaganda,  publicidad  o  adhesivos  en  sus  vidrios  que  obstaculicen  la visibilidad.'
            ],
            [
                'type_id' => 2,
                'code' => 'B.12',
                'description' => 'No respetar las normas establecidas por la autoridad competente para el tránsito de cortejos fúnebres.'
            ],
            [
                'type_id' => 2,
                'code' => 'B.13',
                'description' => 'No respetar las formaciones de tropas, la marcha de desfiles, procesiones, entierros, filas estudiantiles y las manifestaciones públicas y actividades deportivas, debidamente autorizadas por las autoridades de tránsito.'
            ],
            [
                'type_id' => 2,
                'code' => 'B.14',
                'description' => 'Remolcar otro vehículo violando lo dispuesto este Código.'
            ],
            [
                'type_id' => 2,
                'code' => 'B.15',
                'description' => 'Conducir un vehículo de servicio público que no lleve el aviso de tarifas oficiales en condiciones de fácil lectura para los pasajeros o poseer este aviso deteriorado o adulterado.'
            ],
            [
                'type_id' => 2,
                'code' => 'B.16',
                'description' => 'Permitir que en un vehículo de servicio público para transporte de pasajeros se lleven animales u objetos que incomoden a los pasajeros.'
            ],
            [
                'type_id' => 2,
                'code' => 'B.17',
                'description' => 'Abandonar un vehículo de servicio público con pasajeros.'
            ],
            [
                'type_id' => 2,
                'code' => 'B.18',
                'description' => 'Conducir un vehículo de transporte público individual de pasajeros sin cumplir con lo estipulado en el Código Nacional de Tránsito Terrestre.'
            ],
            [
                'type_id' => 2,
                'code' => 'B.19',
                'description' => 'Realizar  el  cargue  o  descargue  de  un  vehículo  en  sitios  y  horas  prohibidas  por  las  autoridades competentes, de acuerdo con lo establecido en las normas correspondientes.'
            ],
            [
                'type_id' => 2,
                'code' => 'B.20',
                'description' => 'Transportar  carne,  pescado  o  alimentos  fácilmente  corruptibles,  en  vehículos  que  no  cumplan  las condiciones fijadas por el Ministerio de Transporte'
            ],
            [
                'type_id' => 2,
                'code' => 'B.21',
                'description' => 'Lavar vehículos en vía pública, en ríos, en canales, en quebradas, etc.'
            ],
            [
                'type_id' => 2,
                'code' => 'B.22',
                'description' => 'Llevar niños menores de diez (10) años en el asiento delantero.'
            ],
            [
                'type_id' => 2,
                'code' => 'B.23',
                'description' => 'Utilizar radios, equipos de sonido o de amplificación a volúmenes que superen los decibeles máximos establecidos por las autoridades ambientales.  De igual forma utilizar pantallas, proyectores de imagen o similares en la parte delantera de los vehículos mientras esté en movimiento.'
            ],
            [
                'type_id' => 3,
                'code' => 'C.1',
                'description' => 'Presentar licencia de conducción adulterada o ajena lo cual dará lugar a la inmovilización del vehículo.'
            ],
            [
                'type_id' => 3,
                'code' => 'C.2',
                'description' => 'Estacionar un vehículo en sitios prohibidos.'
            ],
            [
                'type_id' => 3,
                'code' => 'C.3',
                'description' => 'Bloquear una calzada o intersección con un vehículo, salvo cuando el bloqueo obedezca a la ocurrencia de un accidente de tránsito.'
            ],
            [
                'type_id' => 3,
                'code' => 'C.4',
                'description' => 'Estacionar un vehículo sin tomar las debidas precauciones o sin colocar a la distancia señalada por este Código, las señales de peligro reglamentarias.'
            ],
            [
                'type_id' => 3,
                'code' => 'C.5',
                'description' => 'No reducir la velocidad según lo indicado por este Código, cuando transite por un cruce escolar en los horarios y días de funcionamiento de la institución educativa. Así mismo, cuando transite por cruces de hospitales o terminales de pasajeros.'
            ],
            [
                'type_id' => 3,
                'code' => 'C.6',
                'description' => 'No utilizar el cinturón de seguridad por parte de los ocupantes del vehículo.'
            ],
            [
                'type_id' => 3,
                'code' => 'C.7',
                'description' => 'Dejar de señalizar con las luces direccionales o mediante señales de mano y con la debida anticipación, la maniobra de giro o de cambio de carril.'
            ],
            [
                'type_id' => 3,
                'code' => 'C.8',
                'description' => 'Transitar sin los dispositivos luminosos requeridos o sin los elementos determinados en éste Código.'
            ],
            [
                'type_id' => 3,
                'code' => 'C.9',
                'description' => 'No respetar las señales de detención en el cruce de una línea férrea, o conducir por la vía férrea o por las zonas de protección y seguridad de ella.'
            ],            
            [
                'type_id' => 3,
                'code' => 'C.10',
                'description' => 'Conducir un vehículo con una o varias puertas abiertas.'
            ],
            [
                'type_id' => 3,
                'code' => 'C.11',
                'description' => 'No portar el equipo de prevención y seguridad establecido en este Código o en la reglamentación correspondiente.'
            ],
            [
                'type_id' => 3,
                'code' => 'C.12',
                'description' => 'Proveer de combustible un vehículo automotor con el motor encendido.'
            ],
            [
                'type_id' => 3,
                'code' => 'C.13',
                'description' => 'Conducir  un  vehículo  automotor  sin  las  adaptaciones  pertinentes, cuando  el conductor  padece de limitación física.'
            ],
            [
                'type_id' => 3,
                'code' => 'C.14',
                'description' => '*Transitar por sitios restringidos o en horas prohibidas por la autoridad competente. Además, el vehículo será inmovilizado.'
            ],
            [
                'type_id' => 3,
                'code' => 'C.15',
                'description' => 'Conducir un vehículo particular o de servicio público, excediendo la capacidad autorizada en la licencia de tránsito o tarjeta de operación.'
            ],
            [
                'type_id' => 3,
                'code' => 'C.16',
                'description' => 'Conducir un vehículo escolar sin el permiso respectivo o los distintivos reglamentarios. Además el vehículo será inmovilizado'
            ],            
            [
                'type_id' => 3,
                'code' => 'C.17',
                'description' => 'Circular con combinaciones de vehículos de dos (2) o más unidades remolcadas, sin autorización especial de autoridad competente.'
            ],            
            [
                'type_id' => 3,
                'code' => 'C.18',
                'description' => 'Conducir un vehículo autorizado para prestar servicio público con el taxímetro dañado, con los sellos rotos o etiquetas adhesivas con calibración vencida o adulterada o cuando se carezca de él, o cuando aun teniéndolo, no cumpla con las normas mínimas de calidad y seguridad exigidas por la autoridad competente o éste no esté en funcionamiento.'
            ],
            [
                'type_id' => 3,
                'code' => 'C.19',
                'description' => 'Dejar o recoger pasajeros en sitios distintos de los demarcados por las autoridades.'
            ],
            [
                'type_id' => 3,
                'code' => 'C.20',
                'description' => '*Conducir un vehículo de carga en que se transporten materiales de construcción o a granel sin las medidas de protección, higiene y seguridad ordenadas. Además el vehículo será inmovilizado.'
            ],            
            [
                'type_id' => 3,
                'code' => 'C.21',
                'description' => 'No  asegurar  la  carga  para  evitar  que  se  caigan  en  la  vía  las  cosas  transportadas.  Además,  se inmovilizará el vehículo hasta tanto se remedie la situación.'
            ],
            [
                'type_id' => 3,
                'code' => 'C.22',
                'description' => 'Transportar carga de dimensiones superiores a las autorizadas sin cumplir con los requisitos exigidos. Además, el vehículo será inmovilizado hasta que se remedie dicha situación.'
            ],
            [
                'type_id' => 3,
                'code' => 'C.23',
                'description' => 'Impartir en vías públicas al público enseñanza práctica para conducir, sin estar autorizado para ello.'
            ],
            [
                'type_id' => 3,
                'code' => 'C.24',
                'description' => 'Conducir motocicleta sin observar las normas establecidas en el presente Código.'
            ],
            [
                'type_id' => 3,
                'code' => 'C.25',
                'description' => 'Transitar cuando hubiere más de un carril, por el carril izquierdo de la vía a velocidad que entorpezca el tránsito de los demás vehículos.'
            ],
            [
                'type_id' => 3,
                'code' => 'C.26',
                'description' => 'Transitar en vehículos de 3.5 o más toneladas por el carril izquierdo de la vía cuando hubiere más de un carril.'
            ],
            [
                'type_id' => 3,
                'code' => 'C.27',
                'description' => 'Conducir un vehículo cuya carga o pasajeros obstruyan la visibilidad del conductor hacia el frente, atrás o costados, o impidan el control sobre el sistema de dirección, frenos o seguridad. Además el vehículos será inmovilizado'
            ],
            [
                'type_id' => 3,
                'code' => 'C.28',
                'description' => 'Hacer uso de dispositivos propios de vehículos de emergencia, por parte de conductores de otro tipo de vehículos.'
            ],
            [
                'type_id' => 3,
                'code' => 'C.29',
                'description' => 'Conducir un vehículo a velocidad superior a la máxima permitida.'
            ],
            [
                'type_id' => 3,
                'code' => 'C.30',
                'description' => 'No atender una señal de ceda el paso.'
            ],
            [
                'type_id' => 3,
                'code' => 'C.31',
                'description' => 'No acatar las señales o requerimientos impartidos por los agentes de tránsito.'
            ],
            [
                'type_id' => 3,
                'code' => 'C.32',
                'description' => 'No respetar el paso de peatones que cruzan una vía en sitio permitido para ellos o no darles la prelación en las franjas para ello establecidas.'
            ],
            [
                'type_id' => 3,
                'code' => 'C.33',
                'description' => 'Poner un vehículo en marcha sin las precauciones para evitar choques.'
            ],
            [
                'type_id' => 3,
                'code' => 'C.34',
                'description' => 'Reparar un vehículo en las vías públicas, parque o acera, o hacerlo en caso de emergencia, sin atender el procedimiento señalado en este Código.'
            ],
            [
                'type_id' => 3,
                'code' => 'C.35',
                'description' => '*No realizar la revisión técnico-mecánica en el plazo legal establecido o cuando el vehículo no se encuentre en adecuadas condiciones técnico-mecánicas o de emisiones contaminantes, aun cuando porte los certificados correspondientes. Además el vehículo será inmovilizado.'
            ],
            [
                'type_id' => 3,
                'code' => 'C.36',
                'description' => '*Transportar  carga  en  contenedores  sin  los  dispositivos  especiales  de  sujeción.  El  vehículo  será inmovilizado.'
            ],
            [
                'type_id' => 3,
                'code' => 'C.37',
                'description' => 'Transportar pasajeros en el platón de una camioneta picó o en la plataforma de un vehículo de carga, trátese de furgón o plataforma de estacas.'
            ],
            [
                'type_id' => 3,
                'code' => 'C.38',
                'description' => 'Usar sistemas móviles de comunicación o teléfonos instalados en los vehículos al momento de conducir, exceptuando si éstos son utilizados con accesorios o equipos auxiliares que permitan tener las manos libres.'
            ],
            [
                'type_id' => 3,
                'code' => 'C.39',
                'description' => 'Vulnerar las reglas de estacionamiento contenidas en el artículo 77 de este Código'
            ],
            [
                'type_id' => 3,
                'code' => 'C.40',
                'description' => 'Los conductores con movilidad normal que estacionen sus vehículos en lugares públicos de estacionamiento específicamente demarcados con el símbolo internacional de accesibilidad para los automotores que transporten o sean conducidos por personas con movilidad reducida o vehículos para centros de educación especial o de rehabilitación.'
            ],
            [
                'type_id' => 4,
                'code' => 'D.1',
                'description' => 'Guiar un vehículo sin haber obtenido la licencia de conducción correspondiente. Además, el vehículo será inmovilizado en el lugar de los hechos, hasta que éste sea retirado por una persona autorizada por el infractor con licencia de conducción.'
            ],
            [
                'type_id' => 4,
                'code' => 'D.2',
                'description' => '*Conducir sin portar los seguros ordenados por la ley. Además, el vehículo será inmovilizado.'
            ],
            [
                'type_id' => 4,
                'code' => 'D.3',
                'description' => 'Transitar en sentido contrario al estipulado para la vía, calzada o carril. En el caso de motocicletas se procederá a su inmovilización hasta tanto no se pague el valor de la multa o la autoridad competente decida sobre su imposición en los términos de los artículos 135 y 136 del Código Nacional de Tránsito.'
            ],
            [
                'type_id' => 4,
                'code' => 'D.4',
                'description' => 'No detenerse ante una luz roja o amarilla de semáforo, una señal de “PARE” o un semáforo intermitente en rojo. En el caso de motocicletas se procederá a su inmovilización hasta tanto no se pague el valor de la multa o la autoridad competente decida sobre su imposición en los términos de los artículos 135 y 136 del Código Nacional de Tránsito.'
            ],
            [
                'type_id' => 4,
                'code' => 'D.5',
                'description' => 'Conducir un vehículo sobre aceras, plazas, vías peatonales, separadores, bermas, demarcaciones de canalización, zonas verdes o vías especiales para vehículos no motorizados. En el caso de motocicletas se procederá a su inmovilización hasta tanto no se pague el valor de la multa o la autoridad competente decida sobre su imposición en los términos de los artículos 135 y 136 del Código Nacional de Tránsito.'
            ],
            [
                'type_id' => 4,
                'code' => 'D.6',
                'description' => 'Adelantar a otro vehículo en berma, túnel, puente, curva, pasos a nivel y cruces no regulados o al aproximarse a la cima de una cuesta o donde la señal de tránsito correspondiente lo indique. En el caso de motocicletas se procederá a su inmovilización hasta tanto no se pague el valor de la multa o la autoridad competente decida sobre su imposición en los términos de los artículos 135 y 136 del Código Nacional de Tránsito.'
            ],
            [
                'type_id' => 4,
                'code' => 'D.7',
                'description' => 'Conducir realizando maniobras altamente peligrosas e irresponsables que pongan en peligro a las personas o las cosas. En el caso de motocicletas se procederá a su inmovilización hasta tanto no se pague el valor de la multa o la autoridad competente decida sobre su imposición en los términos de los artículos 135 y 136 del Código Nacional de Tránsito.'
            ],
            [
                'type_id' => 4,
                'code' => 'D.8',
                'description' => '*Conducir un vehículo sin luces o sin los dispositivos luminosos de posición, direccionales o de freno, o con alguna de ellas dañada, en las horas o circunstancias en que lo exige este código. Además, el vehículo será inmovilizado, cuando no le funcionen dos (2) o más de estas luces.'
            ],
            [
                'type_id' => 4,
                'code' => 'D.9',
                'description' => 'No permitir el paso de los vehículos de emergencia.'
            ],
            [
                'type_id' => 4,
                'code' => 'D.10',
                'description' => 'Conducir un vehículo para transporte escolar con exceso de velocidad.'
            ],
            [
                'type_id' => 4,
                'code' => 'D.11',
                'description' => 'Permitir el servicio público de pasajeros que no tenga las salidas de emergencia exigidas. En este caso, la multa se impondrá solidariamente a la empresa a la cual esté afiliado y al propietario. Si se tratare de vehículo particular, se impondrá la sanción solidariamente al propietario.'
            ],
            [
                'type_id' => 4,
                'code' => 'D.12',
                'description' => '*Conducir un vehículo que, sin la debida autorización, se destine a un servicio diferente de aquel para el cual tiene licencia de tránsito. Además, el vehículo será inmovilizado por primera vez, por el término de cinco días, por segunda vez veinte días y por tercera vez cuarenta días.'
            ],
            [
                'type_id' => 4,
                'code' => 'D.13',
                'description' => 'En caso de transportar carga con peso superior al autorizado el vehículo será inmovilizado y el exceso deberá ser transbordado.'
            ],
            [
                'type_id' => 4,
                'code' => 'D.14',
                'description' => '*Las autoridades de tránsito ordenarán la inmovilización inmediata de los vehículos que usen para su movilización combustibles no regulados como gas propano u otros que pongan en peligro la vida de los usuarios o de los peatones.'
            ],
            [
                'type_id' => 4,
                'code' => 'D.15',
                'description' => '*Cambio del recorrido o trazado de la ruta para vehículo de servicio de transporte público de pasajeros, autorizado por el organismo de tránsito correspondiente. En este caso, la multa se impondrá solidariamente a la empresa a la cual esté afiliado el vehículo y al propietario. Además el vehículo será inmovilizado salvo casos de fuerza mayor que sean debidamente  autorizados por el agente de tránsito.'
            ],
            [
                'type_id' => 4,
                'code' => 'D.16',
                'description' => 'Arrojar residuos sólidos al espacio público desde un vehículo automotor o de tracción animal o humana, estacionado o en movimiento.'
            ],
            [
                'type_id' => 4,
                'code' => 'D.17',
                'description' => 'Cuando se detecte o advierta una infracción a las normas de emisión contaminantes o de generación de ruido por vehículos automotores.'
            ],
            [
                'type_id' => 5,
                'code' => 'E.1',
                'description' => 'Proveer combustible de servicio público con pasajeros a bordo'
            ],
            [
                'type_id' => 5,
                'code' => 'E.2',
                'description' => 'Negarse a prestar el servicio público sin causa justificada, siempre que dicha negativa cause alteración del orden público.'
            ],
            [
                'type_id' => 5,
                'code' => 'E.3',
                'description' => '*Conducir en estado de embriaguez, o bajo los efectos de sustancias alucinógenas, se entenderá lo establecido en el artículo 152 de este Código. Si se trata de conductor de vehículos de servicio público, de transporte escolar o de instructor de conducción, la multa pecuniaria y el período de suspensión de la licencia se duplicarán.  En todos los casos de embriaguez, el vehículo será inmovilizado y el estado de embriaguez o alcoholemia se determinará mediante una prueba que no cause lesión, la cual será determinada por el Instituto de Medicina Legal y Ciencias Forenses'
            ],
            [
                'type_id' => 5,
                'code' => 'E.4',
                'description' => 'Transportar en el mismo vehículo y al mismo tiempo personas y sustancias peligrosas como explosivos, tóxicos, radioactivos, combustibles no autorizados, etc. En estos casos se suspenderá la licencia por un (1) y por dos (2) años cada vez que reincida. El vehículo será inmovilizado'
            ],
            [
                'type_id' => 6,
                'code' => 'F.01',
                'description' => 'Invadir la zona destinada al tránsito de vehículos, ni transitar en ésta en patines, monopatines, patinetas o similares.'
            ],
            [
                'type_id' => 6,
                'code' => 'F.02',
                'description' => 'Llevar, sin las debidas precauciones, elementos que puedan obstaculizar o afectar el tránsito.'
            ],
            [
                'type_id' => 6,
                'code' => 'F.03',
                'description' => 'Cruzar por sitios no permitidos o transitar sobre el guardavías del ferrocarril.'
            ],
            [
                'type_id' => 6,
                'code' => 'F.04',
                'description' => 'Colocarse delante o detrás de un vehículo que tenga el motor encendido.'
            ],
            [
                'type_id' => 6,
                'code' => 'F.05',
                'description' => 'Remolcarse de vehículos en movimiento.'
            ],
            [
                'type_id' => 6,
                'code' => 'F.06',
                'description' => 'Actuar de manera que ponga en peligro su integridad física.'
            ],
            [
                'type_id' => 6,
                'code' => 'F.07',
                'description' => 'Cruzar la vía atravesando el tráfico vehicular en lugares en donde existen pasos peatonales.'
            ],
            [
                'type_id' => 6,
                'code' => 'F.08',
                'description' => 'Ocupar la zona de seguridad y protección de la vía férrea, la cual se establece a una distancia no menor de doce (12) metros a lado y lado del eje de la vía férrea.'
            ],
            [
                'type_id' => 6,
                'code' => 'F.09',
                'description' => 'Subirse o bajarse de los vehículos, estando éstos en movimiento, cualquiera que sea la operación o maniobra que estén realizando.'
            ],
            [
                'type_id' => 6,
                'code' => 'F.10',
                'description' => 'Transitar por los túneles, puentes y viaductos de las vías férreas.'
            ],
            [
                'type_id' => 6,
                'code' => 'F.11',
                'description' => 'En relación con el SUMP, éstos no deben ocupar la zona de seguridad y corredores de tránsito de los vehículos del SUMP, fuera de los lugares expresamente autorizados y habilitados para ello.'
            ],
            [
                'type_id' => 6,
                'code' => 'F.12',
                'description' => 'Dentro del perímetro urbano, el cruce debe hacerse sólo para las zonas autorizadas como los puentes peatonales, los pasos peatonales y las bocacalles.'
            ],
            [
                'type_id' => 7,
                'code' => 'G.01',
                'description' => 'El pasajero que sea sorprendido fumando en un vehículo de servicio público, será obligado a abandonar el automotor y deberá asistir a un curso de seguridad vial.'
            ],
            [
                'type_id' => 7,
                'code' => 'G.02',
                'description' => 'Los peatones y ciclistas que no cumplan con las disposiciones de este código, serán amonestados por la autoridad de tránsito competente y deberá asistir a un curso formativo dictado por las autoridades de tránsito. La inasistencia al curso será sancionada con arresto de uno (1) a seis (6) días.'
            ],
            [
                'type_id' => 8,
                'code' => 'H.01',
                'description' => 'Circular portando defensas rígidas diferentes de las instaladas originalmente por el fabricante. Además el vehículo será inmovilizados preventivamente hasta que sean retiradas'
            ],
            [
                'type_id' => 8,
                'code' => 'H.02',
                'description' => 'El conductor que no porte la licencia de trasmito, además el vehículo será inmovilizado'
            ],
            [
                'type_id' => 8,
                'code' => 'H.03',
                'description' => 'El conductor pasajero o peatón, que obstaculice, perjudique oponga en riesgo a las demás personas o que no cumplan las normas y señales de tránsito que le sean aplicadas o no obedezca las indicaciones que les den las autoridades'
            ],
            [
                'type_id' => 8,
                'code' => 'H.04',
                'description' => 'El conductor que no respete los derechos e integridad de los peatones'
            ],
            [
                'type_id' => 8,
                'code' => 'H.05',
                'description' => 'El conductor que no respete la prelación de paso en intersecciones o giros o según la clasificación de las vías'
            ],
            [
                'type_id' => 8,
                'code' => 'H.06',
                'description' => 'El conductor que no tome las medidas necesarias para evitar el movimiento del vehículo estacionado. En vehículo de tracción animal no bloquear las ruedas para evitar su movimiento'
            ],
            [
                'type_id' => 8,
                'code' => 'H.07',
                'description' => 'El conductor que lleve pasajeros en la parte exterior del vehículo fuera de la cabina o en los estribos de los mismos…..'
            ],
            [
                'type_id' => 8,
                'code' => 'H.08',
                'description' => 'El conductor que porte luces exploradoras en la parte posterior del Vehículo'
            ],
            [
                'type_id' => 8,
                'code' => 'H.09',
                'description' => 'El pasajero que profiera expresiones injuriosas o groseras, promueva riñas o cause cualquier molestia a los demás pasajeros.'
            ],
            [
                'type_id' => 8,
                'code' => 'H.10',
                'description' => 'Los conductores de vehículos no automotores que incurran en las siguientes infracciones:'
            ],
            [
                'type_id' => 8,
                'code' => 'H.11',
                'description' => 'Viajar los menores de dos (2) años solos en el asiento posterior sin hacer uso de una silla que garantice su seguridad y que permita su fijación a el…'
            ],
            [
                'type_id' => 8,
                'code' => 'H.12',
                'description' => 'Transitar en vehículo de alto tonelaje por las vías de sitios que estén declarados o se declaren como monumentos de conservación histórica'
            ],
            [
                'type_id' => 8,
                'code' => 'H.13',
                'description' => 'Las demás conductas que constituyan infracciones a las normas de tránsito y que no se encuentren descritas en este acto administrativo'
            ],
            [
                'type_id' => 9,
                'code' => 'I.01',
                'description' => 'El conductor que sea sorprendido fumando mientras conduce, dará lugar a la imposición de diez (10) salarios mínimos legales diarios vigentes. Si se tratare de un conductor de servicio público, además deberá asistir a un curso de seguridad vial'
            ],
            [
                'type_id' => 9,
                'code' => 'I.02',
                'description' => 'Quien incumpla la obligación consagrada en el artículo 21 de este código y se le compruebe que en caso de un accidente la deficiencia de carácter orgánico o funcional que fue su causa, el conductor se hará acreedor de una multa de hasta cien (100) salarios mínimos'
            ]
        ];

        foreach ($codes as $key => $code) {
            DriverInfractionTypeCode::firstOrCreate(
                [
                    'code' => $code['code']
                ],
                [
                    'type_id' => $code['type_id'],
                    'code' => $code['code'],
                    'description' => $code['description']
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
