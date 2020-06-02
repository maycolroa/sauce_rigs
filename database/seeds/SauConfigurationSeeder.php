<?php

use Illuminate\Database\Seeder;
use App\Models\General\Configuration;

class SauConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'key' => 'biologicalmonitoring_audiometries_select_epp',
                'value' => '["Copa","Moldeable","Inserción","Ninguno","Otro"]',
                'observation' => 'Select para EPP de audiometrias'
            ],
            [
                'key' => 'biologicalmonitoring_audiometries_select_exposition_level',
                'value' => '["85 a 95 dB","No realizada","Menos de 80 dB","80 a 84.9 dB"]',
                'observation' => 'select para Nivel de exposicion de audiometrias'
            ],
            [
                'key' => 'action_plans_states',
                'value' => '["Pendiente","Ejecutada"]',
                'observation' => 'select para los estados de los planes de acción'
            ],
            [
                'key' => 'days_alert_user_suspension',
                'value' => '1000',
                'observation' => 'Cantidad de días para consultar si un usuario no ha iniciado sesión y enviar la notificación de alerta de suspensión'
            ],
            [
                'key' => 'days_user_suspension',
                'value' => '1007',
                'observation' => 'SI el usuario no ha iniciado sesion en esta cantidad de dias sera supendido'
            ],
            [
                'key' => 'days_alert_expired_license',
                'value' => '30',
                'observation' => 'Cantidad de días para consultar si una licencia esta próxima a vencerse y enviar la notificación de alerta'
            ],
            [
                'key' => 'admin_license_notification_email',
                'value' => 'carolina.madrid@rigs.com.co,caladsantiago@thotstrategy.com,asistenterl@rigs.com.co',
                'observation' => 'Administrador al que se notificara sobre las licencias próximas a vencer'
            ],
            [
                'key' => 'reinc_select_disease_origin',
                'value' => '["Enfermedad Laboral","Enfermedad General","Accidente de Trabajo","Maternidad"]',
                'observation' => 'Reincorporaciones - Opciones de Tipo de Evento'
            ],
            [
                'key' => 'reinc_select_lateralities',
                'value' => '["Derecho","Izquierdo","Derecho e izquierdo","NA"]',
                'observation' => 'Reincorporaciones - Opciones de Lateralidad'
            ],
            [
                'key' => 'reinc_select_origin_advisors',
                'value' => '["ARL","EPS","Médico de la empresa"]',
                'observation' => 'Reincorporaciones - Procedencia de las recomendaciones'
            ],
            [
                'key' => 'reinc_select_medical_conclusions',
                'value' => '["Estable","Mejorando","Empeorando","Sin información médica"]',
                'observation' => 'Reincorporaciones - Conclusión Seguimiento Médico'
            ],
            [
                'key' => 'reinc_select_labor_conclusions',
                'value' => '["Leve","Moderado","Severo"]',
                'observation' => 'Reincorporaciones - Conclusión Seguimiento Laboral'
            ],
            [
                'key' => 'reinc_select_emitter_origin',
                'value' => '["ARL","EPS","AFP","JUNTA REGIONAL","JUNTA NACIONAL","Sin Información"]',
                'observation' => 'Reincorporaciones - Entidad que Califica Origen'
            ],
            [
                'key' => 'days_delete_files_temporal',
                'value' => '10',
                'observation' => 'Cantidad de días para borrar archivos temporales'
            ],
            [
                'key' => 'ph_text_terms_conditions',
                'value' => '<h4><center>APP RiGS</center></h4>
                    <br>
                    <h4><center>REGLAMENTO DE USO DE LA APP RiGS</center></h4>
                    <br>
                    <p>El presente documento establece las condiciones mediante las cuales se regirá el uso de la
                    aplicación móvil: Condiciones Peligrosas (en adelante la aplicación), la cual es operada por RiGS
                    S.A.S., compañía constituida en Colombia y domiciliada en la ciudad de Medellín (en adelante
                    RIGS).
                    La aplicación funcionará como un canal para la realización de ciertas actividades descritas más
                    adelante con el objeto de facilitar la identificación y control de riesgos en las empresa clientes
                    directas e indirectas de RiGS.
                    El usuario se compromete a leer los términos y condiciones aquí establecidas, previamente a la
                    descarga de la aplicación, por tanto, en caso de realizar la instalación se entiende que cuenta
                    con el conocimiento integral de este documento y la consecuente aceptación de la totalidad de
                    sus estipulaciones.
                    El Usuario reconoce que el ingreso de su información personal, y los datos que contiene la
                    aplicación a su disposición de RiGS en Colombia, la realizan de manera voluntaria, quienes
                    optan por acceder a esta aplicación en Colombia o desde fuera del territorio nacional, lo hacen
                    por iniciativa propia y son responsables del cumplimiento de las leyes locales, en la medida en
                    que dichas leyes sean aplicables en su correspondiente país. En caso de que se acceda por parte
                    de menores de edad, deben contar con la supervisión de un adulto en todo momento desde la
                    descarga y durante el uso de la aplicación, en el evento en que no se cumpla esta condición, le
                    agradecemos no hacer uso de la aplicación.</p>
                    <br>
                    <h4><center>ALCANCE Y USO</center></h4>
                    <br>
                    <p>El usuario de la aplicación entiende y acepta que la información contenida en la misma será la
                    referente a su vínculo comercial o contractual con RiGS.
                    La aplicación permite al usuario propender la participación de los trabajadores y la gestión
                    misma de los riesgos de seguridad y salud de la empresa al facilitar la identificación de peligros a
                    través del reporte de condiciones peligrosas y comportamientos inseguros y la gestión de los
                    mismos a través de la definición y seguimiento de los planes de acción.
                    Los tiempos de respuesta, tramites y demás solicitudes efectuadas por el usuario mediante la
                    aplicación serán procesadas de conformidad con las especificaciones de cada producto activo
                    con RiGS.
                    El usuario acepta y autoriza que los registros electrónicos de las actividades mencionadas, que
                    realice en la aplicación constituyen plena prueba de los mismos.</p>
                    <br>
                    <h4><center>REQUISITOS PARA USO</center></h4>
                    <br>
                    <p>El usuario deberá contar con un dispositivo móvil inteligente (Smartphone) o Tableta con
                    sistema operativo Android o IOS, cualquiera de estos con acceso a internet, ambos seguros y
                    confiables. RiGS, no será responsable por la seguridad de los equipos Smartphone propiedad de
                    los usuarios utilizados para el acceso al canal, ni por la disponibilidad del servicio en los
                    dispositivos en los cuales se descargue la aplicación.
                    En la forma permitida por la ley, los materiales de la aplicación se suministran sin garantía de
                    ningún género, expresa o implícita, incluyendo sin limitación las garantías de calidad
                    satisfactoria, comerciabilidad, adecuación para un fin particular o no infracción, por tanto, RiGS
                    no garantiza el funcionamiento adecuado en los distintos sistemas operativos o dispositivos en
                    los cuales se haga uso de la aplicación.
                    Para acceder al portal, EL CLIENTE contará con Usuario y Clave, que lo identifica en su relación
                    con RIGS, los cuales serán los mismos utilizados en el portal web. Adicional a lo anterior se
                    requerirá a EL CLIENTE, registrar preguntas de seguridad, las cuales serán solicitadas al
                    momento de intentar ingresar el portal, sólo cuando el cliente ingrese desde un equipo
                    registrado no se solicitará responder las preguntas definidas con anterioridad.</p>
                    <br>
                    <h4><center>OBLIGACIONES DE LOS USUARIOS</center></h4>
                    <br>
                    <p>El Usuario se obliga a usar la aplicación y los contenidos encontrados en ella de una manera
                    diligente, correcta, lícita y en especial, se compromete a NO realizar las conductas descritas a
                    continuación:
                    <ul>
                    <li>(a) Utilizar los contenidos de forma, con fines o efectos contrarios a la ley, a la moral y a las
                    buenas costumbres generalmente aceptadas o al orden público.</li>
                    <li>(b) Reproducir, copiar, representar, utilizar, distribuir, transformar o modificar los contenidos
                    de la aplicación, por cualquier procedimiento o sobre cualquier soporte, total o parcial, o
                    permitir el acceso del público a través de cualquier modalidad de comunicación pública.</li>
                    <li>(c) Utilizar los contenidos de cualquier manera que entrañen un riesgo de daño o inutilización
                    de la aplicación o de los contenidos o de terceros.</li>
                    <li>(d) Suprimir, eludir o manipular el derecho de autor y demás datos identificativos de los
                    derechos de autor incorporados a los contenidos, así como los dispositivos técnicos de
                    protección, o cualesquiera mecanismos de información que pudieren tener los
                    contenidos.</li>
                    <li>(e) Emplear los contenidos y, en particular, la información de cualquier clase obtenida a
                    través de la aplicación para distribuir, transmitir, remitir, modificar, rehusar o reportar
                    la publicidad o los contenidos de esta con fines de venta directa o con cualquier otra
                    clase de finalidad comercial, mensajes no solicitados dirigidos a una pluralidad de
                    personas con independencia de su finalidad, así como comercializar o divulgar de
                    cualquier modo dicha información.</li>
                    <li>(f) No permitir que terceros ajenos a usted usen la aplicación móvil con su clave.</li>
                    <li>(g) Utilizar la aplicación y los contenidos con fines lícitos y/o ilícitos, contrarios a lo establecido
                    en estos Términos y Condiciones, o al uso mismo de la aplicación, que sean lesivos de los
                    derechos e intereses de terceros, o que de cualquier forma puedan dañar, inutilizar,
                    sobrecargar o deteriorar la aplicación y los contenidos o impedir la normal utilización o
                    disfrute de esta y de los contenidos por parte de los usuarios.</li></ul></p>
                    <br>
                    <h4><center>PROPIEDAD INTELECTUAL</center></h4>
                    <br>
                    <p>Todo el material informático, gráfico, publicitario, fotográfico, de multimedia, audiovisual y de
                    diseño, así como todos los contenidos, textos y bases de datos puestos a su disposición en esta
                    aplicación están protegidos por derechos de autor y/o propiedad industrial cuyo titular es RiGS,
                    en algunos casos, de terceros que han autorizado su uso o explotación. Igualmente, el uso en la
                    aplicación de algunos materiales de propiedad de terceros se encuentra expresamente
                    autorizado por la ley o por dichos terceros. Todos los contenidos en la aplicación están
                    protegidos por las normas sobre derecho de autor y por todas las normas nacionales e
                    internacionales que le sean aplicables.
                    Exceptuando lo expresamente estipulado en estos Términos y Condiciones, queda prohibido
                    todo acto de copia, reproducción, modificación, creación de trabajos derivados, venta o
                    distribución, exhibición de los contenidos de esta aplicación, de manera o por medio alguno,
                    incluyendo, más no limitado a, medios electrónicos, mecánicos, de fotocopiado, de grabación o
                    de cualquier otra índole, sin el permiso previo y por escrito de RiGS o del titular de los
                    respectivos derechos.
                    En ningún caso estos Términos y Condiciones confieren derechos, licencias ni autorizaciones
                    para realizar los actos anteriormente prohibidos. Cualquier uso no autorizado de los contenidos
                    constituirá una violación del presente documento y a las normas vigentes sobre derechos de
                    autor, a las normas vigentes nacionales e internacionales sobre Propiedad Industrial, y a
                    cualquier otra que sea aplicable.</p>
                    <br>
                    <h4><center>LICENCIA PARA COPIAR PARA USO PERSONAL</center></h4>
                    <br>
                    <p>Usted podrá leer, visualizar, imprimir y descargar el material de sus productos.
                    Ninguna parte de la aplicación podrá ser reproducida o transmitida o almacenada en otro sitio
                    web o en otra forma de sistema de recuperación electrónico.
                    Ya sea que se reconozca específicamente o no, las marcas comerciales, las marcas de servicio y
                    los logos visualizados en esta aplicación pertenecen a RiGS, sus socios promocionales u otros
                    terceros.
                    RiGS no interfiere, no toma decisiones, ni garantiza las relaciones que los usuarios lleguen a
                    sostener o las vinculaciones con terceros que pauten y/o promocionen sus productos y
                    servicios. Estas marcas de terceros se utilizan solamente para identificar los productos y
                    servicios de sus respectivos propietarios y el patrocinio o el aval por parte de RiGS no se deben
                    inferir con el uso de estas marcas comerciales.</p>
                    <br>
                    <h4><center>INTEGRACIÓN CON OTRAS APLICACIONES</center></h4>
                    <br>
                    <p>Ver si aplica | RiGS no acepta responsabilidad por el contenido del sitio de un tercero con el cual existe un link
                    de hipertexto y no ofrece garantía (explícita o implícita) en cuanto al contenido de la
                    información en esos sitios, ya que no recomienda estos sitios.
                    Usted debe verificar las secciones de términos y condiciones, política legal y de privacidad de
                    algunos otros sitios de RiGS o de un tercero con los cuales se enlaza.
                    RiGS no asume ninguna responsabilidad por pérdida directa, indirecta o consecuencial por el
                    uso de un sitio de un tercero.</p>
                    <br>
                    <h4><center>USO DE INFORMACIÓN Y PRIVACIDAD</center></h4>
                    <br>
                    <p>Con la descarga de la Aplicación usted acepta y autoriza que RiGS, utilice sus datos en calidad
                    de responsable del tratamiento para fines derivados de la ejecución de la Aplicación. RiGS
                    informa que podrá ejercer sus derechos a conocer, actualizar, rectificar y suprimir su
                    información personal; así como el derecho a revocar el consentimiento otorgado para el
                    tratamiento de datos personales previstos en la ley 1581 de 2012, observando nuestra política
                    de tratamiento de información disponible en: www.rigs.com.co; o a través de
                    administracion@rigs.com.co o del teléfono (4) 444 2989, siendo voluntario responder
                    preguntas sobre información sensible o de menores de edad.
                    RiGS podrá dar a conocer, transferir y/o trasmitir sus datos personales dentro y fuera del país a
                    cualquier empresa miembro del grupo RiGS, así como a terceros a consecuencia de un contrato,
                    ley o vínculo lícito que así lo requiera, para todo lo anterior otorgo mi autorización expresa e
                    inequívoca.
                    De conformidad a lo anterior autoriza el tratamiento de su información en los términos
                    señalados, y transfiere a RiGS de manera total, y sin limitación mis derechos de imagen y
                    patrimoniales de autor, de manera voluntaria, previa, explicita, informada e inequívoca.</p>
                    <br>
                    <h4><center>RESPONSABILIDAD DE RIGS</center></h4>
                    <br>
                    <p>RiGS procurará garantizar disponibilidad, continuidad o buen funcionamiento de la aplicación.
                    RiGS podrá bloquear, interrumpir o restringir el acceso a esta cuando lo considere necesario
                    para el mejoramiento de la aplicación o por dada de baja de la misma.
                    Se recomienda al Usuario tomar medidas adecuadas y actuar diligentemente al momento de
                    acceder a la aplicación, como por ejemplo, contar con programas de protección, antivirus, para
                    manejo de malware, spyware y herramientas similares.
                    RiGS no será responsable por:
                    <ul>
                    <li>a) Fuerza mayor o caso fortuito.</li>
                    <li>b) Por la pérdida, extravío o hurto de su dispositivo móvil que implique el acceso de terceros a la aplicación móvil.</li>
                    <li>c) Por errores en la digitación o accesos por parte del cliente.</li>
                    <li>d) Por los perjuicios, lucro cesante, daño emergente, morales, y en general sumas a cargo de RiGS, por los retrasos, no procesamiento de
                    información o suspensión del servicio del operador móvil o daños en los dispositivos móviles.</li></ul></p>
                    <br>
                    <h4><center>DENEGACIÓN Y RETIRADA DEL ACCESO A LA APLICACIÓN</center></h4>
                    <br>
                    <p>En el Evento en que un Usuario incumpla estos Términos y Condiciones, o cualesquiera otras
                    disposiciones que resulten de aplicación, RiGS podrá suspender su acceso a la aplicación.</p>
                    <br>
                    <h4><center>TÉRMINOS Y CONDICIONES</center></h4>
                    <br>
                    <p>El Usuario acepta expresamente los Términos y Condiciones, siendo condición esencial para la
                    utilización de la aplicación. En el evento en que se encuentre en desacuerdo con estos Términos
                    y Condiciones, solicitamos abandonar la aplicación inmediatamente. RiGS podrá modificar los
                    presentes términos y condiciones, avisando a los usuarios de la aplicación mediante publicación
                    en la página web www.rigs.com.co o mediante la difusión de las modificación por algún medio
                    electrónico y/o correo electrónico, lo cual se entenderá aceptado por el usuario si éste continua
                    con el uso de la aplicación.</p>
                    <br>
                    <h4><center>JURISDICCIÓN</center></h4>
                    <br>
                    <p>Estos términos y condiciones y todo lo que tenga que ver con esta aplicación, se rigen por las
                    leyes de colombianas.</p>
                    <br>
                    <h4><center>USO DE INFORMACIÓN NO PERSONAL</center></h4>
                    <br>
                    <p>RiGS también recolecta información no personal en forma agregada para seguimiento de datos
                    como el número total de descargas de la aplicación. Utilizamos esta información, que
                    permanece en forma agregada, para entender el comportamiento de la aplicación.</p>
                    <br>
                    <h4><center>USO DE DIRECCIONES IP</center></h4>
                    <br>
                    <p>Una dirección de Protocolo de Internet (IP) es un conjunto de números que se asigna
                    automáticamente a su o dispositivo móvil cuando usted accede a su proveedor de servicios de
                    internet, o a través de la red de área local (LAN) de su organización o la red de área amplia
                    (WAN). Los servidores web automáticamente identifican su dispositivo móvil por la dirección IP
                    asignada a él durante su sesión en línea.
                    RiGS podrán recolectar direcciones IP para propósitos de administración de sistemas y para
                    auditar el uso de nuestro sitio, todo lo anterior de acuerdo con la autorización de protección de
                    datos que se suscribe para tal efecto. Normalmente no vinculamos la dirección IP de un usuario
                    con la información personal de ese usuario, lo que significa que cada sesión de usuario se
                    registra, pero el usuario sigue siendo anónimo para nosotros. Sin embargo, podemos usar las
                    direcciones IP para identificar a los usuarios de nuestro sitio cuando sea necesario con el objeto
                    de para exigir el cumplimiento de los términos de uso del sitio, o para proteger nuestro servicio,
                    sitio u otros usuarios</p>
                    <br>
                    <h4><center>SEGURIDAD</center></h4>
                    <br>
                    <p>Utilizamos Amazon Web Services (AWS - https://aws.amazon.com/es/) para el hospedaje de
                    nuestros servicios. Para todos nuestros servidores Web utilizamos Amazon EC2
                    (https://aws.amazon.com/es/ec2/) y para nuestras bases de datos utilizamos Amazon RDS
                    (https://aws.amazon.com/es/rds/). Por ultimo para todo el almacenamiento físico utilizamos
                    Amazon S3 (https://aws.amazon.com/es/s3/).
                    Internamente dentro de Amazon configuramos una red virtual interna que nos permite
                    asegurar nuestras aplicaciones garantizando un excelente desempeño sin comprometer la
                    seguridad de nuestros diferentes aplicativos. Toda nuestra infraestructura se encuentra
                    desplegada en USA en la actualidad particularmente en uno de los data centers de Virginia que
                    posee Amazon.</p>
                    <br>
                    <h4><center>CERTIFICADOS SSL</center></h4>
                    <br>
                    <p>Los servidores web que administramos cuentan con certificados SSL (HTTPS) para realizar el
                    cifrado de la información que fluye entre los navegadores de los clientes y nuestros servidores.</p>
                    <br>
                    <h4><center>POLITICA DE BACK UPS</center></h4>
                    <br>
                    <p>Diariamente se crean copias de seguridad de todas nuestras bases de datos de forma
                    automática. Almacenamos los últimos 30 días calendario en caso de cualquier evento. Nuestros
                    servidores de archivos generan versionamiento de todos los elementos que se almacenan en
                    los mismos y de forma automática se genera un espejo de la información en otro data center en
                    Brasil también de AWS.</p>
                    <br>
                    <h4><center>CUENTAS DE USUARIO Y ROLES</center></h4>
                    <br>
                    <p>Cada persona cuenta con un usuario y contraseña para ingresar a las diferentes plataformas. En
                    el momento de comenzar el uso de las mismas se le crea a la empresa una cuenta base que sirve
                    como administrador y que permite crear usuarios adicionales dependiendo de las necesidades
                    del cliente. Nuestras plataformas cuentan con la capacidad de creación y asignación de roles y
                    perfiles de manera que nuestros clientes tengan la flexibilidad de configurar los accesos y
                    capacidades de todas las personas que ingresan a los diferentes aplicativos. La empresa cliente
                    determinará de acuerdo a sus propias políticas a quien o quienes se les asignan usuarios y
                    contraseñas
                    Todas nuestras plataformas obligan a que los nombres de usuario contengan mínimo 8
                    caracteres, adicionalmente nuestras contraseñas deben contener ese mismo número y
                    adicionalmente es obligatorio que contengan un carácter especial, mínimo un número y una
                    mezcla de mayúsculas y minúsculas.
                    No es obligatorio cambiar la contraseña con una frecuencia específica, pero si el usuario no ha
                    cambiado su contraseña en los últimos 30 días, se le sugiere hacerlo para tratar de incentivar
                    una mejor práctica de seguridad informática.</p>',
                'observation' => 'Terminos y condiciones de la aplicación Condiciones Peligrosas'
            ],
        ];

        foreach ($data as $key => $value)
        {
            Configuration::updateOrCreate(['key' => $value['key']], $value);
        }
    }
}
