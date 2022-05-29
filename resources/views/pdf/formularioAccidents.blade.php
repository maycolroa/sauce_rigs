<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        table {
          font-family: arial, sans-serif;          
          width: 100%;
        }

        td, th {
          border: 1px solid #dddddd;
          text-align: center;
          padding: 8px;
        }

        tr:nth-child(even) {
          background-color: #dddddd;
        }

        body{
            font-size: 14px;
        }

        .body-themes {
            background-color: white;
        }

        .title-obj {
          text-align: justify;
        }

        .title-sub {
            text-align: justify;
        }

    </style>
</head>
<body style="margin: 50px; margin-top: 0px;">
    <div style="page-break-inside: avoid;">
        <table class="table-general">
            <thead>
                <tr>
                    <th colspan="5">Información de la persona que se accidento</th>
                </tr>
                <tr>
                    <th>Tipo de vinculación laboral</th>
                    <th>Identificación</th>
                    <th>Nombre</th>
                    <th>Fecha de nacimiento</th>
                    <th>Sexo</th>
                </tr>
                <tr>
                    <td>{{$form->tipo_vinculador_laboral}}</td>
                    <td>{{$form->tipo_identificacion_persona}} - {{$form->identificacion_persona}}</td>
                    <td>{{$form->nombre_persona}}</td>
                    <td>{{date('Y-m-d', strtotime($form->fecha_nacimiento_persona))}}</td>
                    <td>{{$form->sexo_persona}}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>{{ Auth::user()->getKeywords()['position'] }}</th>
                    <th>Tipo de vinculación</th>
                    <th>Dirección</th>
                </tr>
                <tr>
                    <td>{{$form->email_persona}}</td>
                    <td>{{$form->telefono_persona}}</td>
                    <td>{{$form->cargo_persona}}</td>
                    <td>{{$form->tipo_vinculacion_persona}}</td>
                    <td>{{$form->direccion_persona}}</td>
                </tr>
                <tr>
                    <th>Departamento</th>
                    <th>Ciudad</th>
                    <th>Zona</th>
                    <th>Fecha de ingreso a la empresa</th>
                    <th>Tiempo de ocupacion habitual al momento del accidente</th>
                </tr>
                <tr>
                    <td>{{$form->departamentPerson->name}}</td>
                    <td>{{$form->ciudadPerson->name}}</td>
                    <td>{{$form->zona_persona}}</td>
                    <td>{{date('Y-m-d', strtotime($form->fecha_ingreso_empresa_persona))}}</td>
                    <td>{{$form->tiempo_ocupacion_habitual_persona}}</td>
                </tr>
                <tr>
                    <th colspan="2">Salario</th>
                    <th colspan="3">Jorada de trabajo habitual</th>
                </tr>
                <tr>
                    <td colspan="2">{{$form->salario_persona}}</td>
                    <td colspan="3">{{$form->jornada_trabajo_habitual_persona}}</td>
                </tr>
            </thead>
        </table>
    </div>

    <br><br>

    <div style="page-break-inside: avoid;">
        <table class="table-general">
            <thead>
                <tr>
                    <th colspan="5">Identificación del empleador, contratante o cooperativa</th>
                </tr>
                <tr>
                    <th>Nombre de la actividad económica</th>
                    <th>Nombre o razón social</th>
                    <th>Identificación</th>
                    <th>Dirección</th>
                    <th>Email</th>
                </tr>
                <tr>
                    <td>{{$form->nombre_actividad_economica_sede_principal}}</td>
                    <td>{{$form->razon_social}}</td>
                    <td>{{$form->tipo_identificacion_sede_principal}} - {{$form->identificacion_sede_principal}}</td>
                    <td>{{$form->direccion_sede_principal}}</td>
                    <td>{{$form->email_sede_principal}}</td>
                </tr>
                <tr>
                    <th>Teléfono</th>
                    <th>Departamento</th>
                    <th>Ciudad</th>
                    <th>Zona</th>
                    <th>¿Son los datos del centro de trabajo los mismos de la sede principal?</th>
                </tr>
                <tr>
                    <td>{{$form->telefono_sede_principal}}</td>
                    <td>{{$form->departamentSede->name}}</td>
                    <td>{{$form->ciudadSede->name}}</td>
                    <td>{{$form->zona_sede_principal}}</td>
                    <td>{{$form->info_sede_principal_misma_centro_trabajo}}</td>
                </tr>
            </thead>
        </table>
        @if ($form->info_sede_principal_misma_centro_trabajo == 'NO')
        <br><br>
        <table class="table-general">
            <thead>
                <tr>
                    <th colspan="4">Información centro de trabajo</th>
                </tr>
                <tr>
                    <th>Nombre de la actividad económica</th>
                    <th>Dirección</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                </tr>
                <tr>
                    <td>{{$form->nombre_actividad_economica_centro_trabajo}}</td>
                    <td>{{$form->direccion_centro_trabajo}}</td>
                    <td>{{$form->email_centro_trabajo}}</td>
                    <td>{{$form->telefono_centro_trabajo}}</td>
                </tr>
                <tr>
                    <th colspan="2">Departamento</th>
                    <th>Ciudad</th>
                    <th>Zona</th>
                </tr>
                <tr>
                    <td colspan="2">{{$form->departamentCentro->name}}</td>
                    <td>{{$form->ciudadCentro->name}}</td>
                    <td>{{$form->zona_centro_trabajo}}</td>
                </tr>
                @endif
            </thead>
        </table>
    </div>

    <br><br>

    <div style="page-break-inside: avoid;">
        <table class="table-general">
            <thead>
                <tr>
                    <th colspan="5">Información básica</th>
                </tr>
                <tr>
                    <th>Nivel de accidente</th>
                    <th>Fecha en que se envía la investigación a la ARL</th>
                    <th>Fecha en que se envía recomendación a la empresa</th>
                    <th>Coordinador delegado</th>
                    <th>Cargo</th>
                </tr>
                <tr>
                    <td>{{$form->nivel_accidente}}</td>
                    <td>{{date('Y-m-d', strtotime($form->fecha_envio_arl))}}</td>
                    <td>{{date('Y-m-d', strtotime($form->fecha_envio_empresa))}}</td>
                    <td>{{$form->coordinador_delegado}}</td>
                    <td>{{$form->cargo}}</td>
                </tr>
                <tr>
                    <th>{{ Auth::user()->getKeywords()['eps'] }} a la que esta afiliado</th>
                    <th>{{ Auth::user()->getKeywords()['afp'] }} a la que esta afiliado</th>
                    <th>{{ Auth::user()->getKeywords()['arl'] }} a la que esta afiliado</th>
                    @if ($form->tiene_seguro_social == 'SI')
                    <th>Seguro Social</th>
                    <th>Nombre Seguro Social</th>
                    @else
                    <th colspan="2">Seguro Social</th>
                    @endif
                </tr>
                <tr>
                    <td>{{$form->eps->name}}</td>
                    <td>{{$form->afp->name}}</td>
                    <td>{{$form->arl->name}}</td> 
                    @if ($form->tiene_seguro_social == 'SI')                    
                    <td>{{$form->tiene_seguro_social}}</td>
                    <td>{{$form->nombre_seguro_social}}</td>
                    @else                 
                    <td colspan="2">{{$form->tiene_seguro_social}}</td>
                    @endif
                </tr>
            </thead>
        </table>
    </div>

    <br><br>

    <div style="page-break-inside: avoid;">
        <table class="table-general">
            <thead>
                <tr>
                    <th colspan="5">Información sobre el accidente</th>
                </tr>
                <tr>
                    <th>Fecha del accidente</th>
                    <th>Dia de la semana en que ocurrio el accidente</th>
                    <th>Jornada en que sucede</th>
                    <th>¿Estaba realizando su labor habitual?</th>
                    @if ($form->estaba_realizando_labor_habitual == 'NO')
                    <th>¿Qué labor realizaba?</th>
                    @else
                    <th>Total tiempo laborado previo al accidente</th>
                    @endif
                </tr>
                <tr>
                    <th>{{date('Y-m-d', strtotime($form->fecha_accidente))}}</th>
                    <th>{{$form->dia_accidente}}</th>
                    <th>{{$form->jornada_accidente}}</th>
                    <th>{{$form->estaba_realizando_labor_habitual}}</th>
                    @if ($form->estaba_realizando_labor_habitual == 'NO')
                    <th>{{$form->otra_labor_habitual}}</th>
                    @else
                    <th>{{$form->total_tiempo_laborado}}</th>
                    @endif
                </tr>
                <tr>
                    @if ($form->estaba_realizando_labor_habitual == 'SI')
                    <th>Total tiempo laborado previo al accidente</th>
                    <th>Lugar donde ocurrió el accidente</th>
                    <th>Tipo de accidente</th>
                    <th>Departamento</th>
                    <th>Ciudad</th>
                    @else
                    <th colspan="2">Lugar donde ocurrió el accidente</th>
                    <th>Tipo de accidente</th>
                    <th>Departamento</th>
                    <th>Ciudad</th>
                    @endif
                </tr>
                <tr>
                    @if ($form->estaba_realizando_labor_habitual == 'SI')
                    <th>{{$form->total_tiempo_laborado}}</th>
                    <th>{{$form->accidente_ocurrio_dentro_empresa}}</th>
                    <th>{{$form->tipo_accidente}}</th>
                    <th>{{$form->departamentAccident->name}}</th>
                    <th>{{$form->ciudadAccident->name}}</th>
                    @else
                    <th colspan="2">{{$form->accidente_ocurrio_dentro_empresa}}</th>
                    <th>{{$form->tipo_accidente}}</th>
                    <th>{{$form->departamentAccident->name}}</th>
                    <th>{{$form->ciudadAccident->name}}</th>
                    @endif
                </tr>
                <tr>
                    <th>Zona</th>
                    <th>¿Causó la muerte del trabajador?</th>
                    @if ($form->causo_muerte == 'SI')
                    <th>Fecha de la muerte</th>
                    <th>Agente del accidente (con que se lesionó el trabajador)</th>
                    <th>Mecanismo o forma del accidente</th>
                    @else
                    <th colspan="2">Agente del accidente (con que se lesionó el trabajador)</th>
                    <th>Mecanismo o forma del accidente</th>
                    @endif
                </tr>
                <tr>
                    <td>{{$form->zona_accidente}}</td>
                    <td>{{$form->causo_muerte}}</td>
                    @if ($form->causo_muerte == 'SI')
                    <td>{{$form->fecha_muerte}}</td>
                    <td>{{$form->agentAccident->name}}</td>
                    <td>{{$form->mechanismAccident->name == 'Otro' ? $form->otro_mecanismo : $form->mechanismAccident->name}}</td>
                    @else
                    <td colspan="2">{{$form->agentAccident->name}}</td>
                    <td>{{$form->mechanismAccident->name == 'Otro' ? $form->otro_mecanismo : $form->mechanismAccident->name}}</td>
                    @endif
                </tr>
                <tr>
                    <th>Sitio donde ocurrió el accidente</th>
                    <th colspan="2">Partes del cuerpo aparentemente afectado</th>
                    <th colspan="2">Tipo de lesión</th>
                </tr>
                <tr>
                    <td>{{$form->siteAccident->name}}</td>
                    <td colspan="2">{{$form->parts_body}}</td>
                    <td colspan="2">{{$form->lesions_id}}</td>
                </tr>
            </thead>
        </table>
    </div>

    <br><br>

    <div style="page-break-inside: avoid;">
        <table class="table-general">
            <thead>
                <tr>
                    <th colspan="5">Descripción del accidente</th>
                </tr>
                <tr>
                    <th colspan="5">Fecha de diligenciamiento del informe</th>
                </tr>
                <tr>
                    <td colspan="5">{{date('Y-m-d', strtotime($form->fecha_diligenciamiento_informe))}}</td>
                </tr>
                <tr>
                    <th colspan="5">Responsable del Informe</th>
                </tr>
                <tr>
                    <th colspan="2">Nombre</th>
                    <th>Identificación</th>
                    <th colspan="2">Cargo</th>
                </tr>
                <tr>
                    <td colspan="2">{{$form->nombres_apellidos_responsable_informe}}</td>
                    <td>{{$form->tipo_identificacion_responsable_informe}} - {{$form->identificacion_responsable_informe}}</td>
                    <td colspan="2">{{$form->cargo_responsable_informe}}</td>
                </tr>
                <tr>
                    <th colspan="4">Descripción del accidente.</th>
                    <th>¿Hubo personas que presenciaron el accidente?</th>
                </tr>
                <tr>
                    <td colspan="4">{{$form->descripcion_accidente}}</td>
                    <td>{{$form->personas_presenciaron_accidente}}</td>
                </tr>
                @if ($form->personas_presenciaron_accidente == 'SI')
                <tr>
                    <th colspan="5">Personas que presenciaron el accidente</th>
                </tr>
                <tr>
                    <th colspan="2">Nombre.</th>
                    <th>Identificacion</th>
                    <th colspan="2">Cargo</th>
                </tr>
                @foreach($form->persons as $person)
                <tr>
                    <td colspan="2">{{$person->name}}</td>
                    <td>{{$person->type_document}} - {{$person->document}}</td>
                    <td colspan="2">{{$person->position}}</td>
                </tr>
                @endforeach
                @endif
            </thead>
        </table>
    </div>

    <br><br>

    <div style="page-break-inside: avoid;">
        <table class="table-general">
            <thead>
                <tr>
                    <th colspan="4">Observaciones de la empresa y registro visual</th>
                </tr>
                <tr>
                    <th colspan="4">Observaciones de la empresa (Equipo de salud ocupacional, jefe inmediato y comité paritario)</th>
                </tr>
                <tr>
                    <td colspan="4">{{$form->observaciones_empresa}}</td>
                </tr><tr>
                    <th colspan="4">Registro visual</th>
                </tr>
                @foreach($form->files_pdf as $row)
                <tr>
                    @foreach($row as $col)
                        <td style="border-right: none;">
                            <img width="200" height="150" src="{{$col['file']}}">
                        </td>
                    @endforeach
                    </tr>
                @endforeach
            </thead>
        </table>
    </div>

    <br><br>

    <div style="page-break-inside: avoid;">
        <table class="table-general">
            <thead>
                <tr>
                    <th colspan="3">Participantes de la investigación</th>
                </tr>
                <tr>
                    <th>Nombre.</th>
                    <th>Identificacion</th>
                    <th>Cargo</th>
                </tr>
                @foreach($form->participants_investigations as $person)
                <tr>
                    <td>{{$person->name}}</td>
                    <td>{{$person->type_document}} - {{$person->document}}</td>
                    <td>{{$person->position}}</td>
                </tr>
               @endforeach
            </thead>
        </table>
    </div>

    <br><br>

    <div style="page-break-inside: avoid;">
        <table class="table-general">
            <thead>
                <tr>
                    <th colspan="6">Planes de acción</th>
                </tr>
                <tr>
                    <th>Item</th>
                    <th>Descripción</th>
                    <th>Responsable</th>
                    <th>Fecha de vencimiento</th>
                    <th>Fecha de ejecución</th>
                    <th>Estado</th>
                </tr>                
                @foreach($form->actionPlan["activities"] as $activity)
                    <tr>
                        <td>{{ $keyObj + 1 }}.{{ $keySub + 1 }}.{{ $keyItem + 1 }}</td>
                        <td class="title-obj">{{$activity["description"]}}</td>
                        <td>{{$activity["multiselect_responsible"]["name"]}}</td>
                        <td>{{$activity["expiration_date"] ? date('Y-m-d', strtotime($activity["expiration_date"])) : '-' }}</td>
                        <td>{{$activity["execution_date"] ? date('Y-m-d', strtotime($activity["execution_date"])) : '-' }}</td>
                        <td>{{$activity["state"]}}</td>
                    </tr>
                @endforeach
            </thead>
        </table>
    </div>

    <br><br>

</body>
</html>