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
                    <th>Identificación</th>
                    <th>Nombre</th>
                    <th>Fecha de nacimiento</th>
                </tr>
                <tr>
                    <td>{{$check->employee->identification}}</td>
                    <td>{{$check->employee->name}}</td>
                    <td>{{$check->employee->date_of_birth}}</td>
                </tr>
                <tr>
                    <th>Sexo</th>
                    <th>Fecha de ingreso</th>
                    <th>Antigüedad</th>
                </tr>
                <tr>
                    <td>{{$check->employee->sex}}</td>
                    <td>{{$check->employee->income_date}}</td>
                    <td>{{$check->employee->antiquity}}</td>
                </tr>
                <tr>
                    <th>Edad</th>
                    <th>{{ Auth::user()->getKeywords()['position'] }}</th>
                    <th>{{ Auth::user()->getKeywords()['businesses'] }}</th>
                </tr>
                <tr>
                    <td>{{$check->employee->age}}</td>
                    <td>{{ $check->employee->position ? $check->employee->position->name : '' }}</td>
                    <td>{{ $check->employee->business ? $check->employee->business->name : '' }}</td>
                </tr>
                <tr>
                    <th colspan="2">{{ Auth::user()->getKeywords()['eps'] }}</th>
                    <th>{{ Auth::user()->getKeywords()['regional'] }}</th>
                </tr>
                <tr>
                    <td colspan="2">{{ $check->employee->eps ? $check->employee->eps->code.'-'.$check->employee->eps->name : '' }}</
                    <td>{{ $check->employee->regional ? $check->employee->regional->name : '' }}</td>
                </tr> 
                
                @if ($locationForm['headquarter'] == 'SI' && $locationForm['process'] == 'NO')
                    <tr>
                        <th colspan="3">{{ Auth::user()->getKeywords()['headquarter'] }}</th>
                    </tr>
                    <tr>
                        <td colspan="3">{{ $check->employee->headquarter ? $check->employee->headquarter->name : '' }}</td>
                    </tr>
                @endif

                @if ($locationForm['process'] == 'SI' && $locationForm['area'] == 'NO')
                    <tr>
                        <th>{{ Auth::user()->getKeywords()['headquarter'] }}</th>
                        <th colspan="2">{{ Auth::user()->getKeywords()['process'] }}</th>
                    </tr>
                    <tr>
                        <td>{{ $check->employee->headquarter ? $check->employee->headquarter->name : '' }}</td>
                        <td colspan="2">{{ $check->employee->process ? $check->employee->process->name : '' }}</td>
                    </tr>
                @endif

                @if ($locationForm['area'] == 'SI')
                    <tr>
                        <th>{{ Auth::user()->getKeywords()['headquarter'] }}</th>
                        <th>{{ Auth::user()->getKeywords()['process'] }}</th>
                        <th>{{ Auth::user()->getKeywords()['area'] }}</th>
                    </tr>
                    <tr>
                        <td>{{ $check->employee->headquarter ? $check->employee->headquarter->name : '' }}</td>
                        <td>{{ $check->employee->process ? $check->employee->process->name : '' }}</td>
                        <td>{{ $check->employee->area ? $check->employee->area->name : '' }}</td>
                    </tr>
                @endif
            </thead>
        </table>
    </div>

    <br><br>

    <div style="page-break-inside: avoid;">
        <table class="table-general">
            <thead>
                @if($check->state == 'CERRADO')
                <tr>
                    <th>Estado del reporte</th>
                    <th>Fecha de cierre</th>
                    <th>Motivo de cierre</th>
                </tr>
                <tr>
                    <td>{{$check->state}}</td>
                    <td>{{$check->deadline ? date('Y-m-d', strtotime($check->deadline)) : '-'}}</td>
                    <td>{{$check->motive_close}}</td>
                </tr>
                @endif
                <tr>
                    <th>{{ Auth::user()->getKeywords()['disease_origin'] }}</th>
                    <th colspan="2">Código CIE 10</th>
                    
                </tr>
                <tr>
                    <td>{{$check->disease_origin}}</td>
                    <td colspan="2">{{$check->cie10Code->code}} - {{$check->cie10Code->description}}</td>
                </tr>
                <tr>
                    <th>Sistema</th>
                    <th>Categoría</th>
                    <th>Lateralidad</th>
                </tr>
                <tr>
                    <td>{{$check->cie10Code->system}}</td>
                    <td>{{$check->cie10Code->category}}</td>
                    <td>{{$check->laterality}}</td>
                </tr>   
            </thead>
        </table>
    </div>

    <br><br>

    <div style="page-break-inside: avoid;">
        <table class="table-general">
            <thead>
                <tr>
                    <th>¿Tiene recomendaciones?</th>
                    @if($check->has_recommendations == 'SI')
                    <th>Fecha Inicio Recomendaciones</th>
                    <th>¿Recomendaciones indefinidas?</th>
                    @endif
                </tr>                
                <tr>
                    <td>{{$check->has_recommendations}}</td>
                    @if($check->has_recommendations == 'SI')
                    <td>{{date('Y-m-d', strtotime($check->start_recommendations))}}</td>
                    <td>{{$check->indefinite_recommendations}}</td>
                    @endif
                </tr>
                @if($check->has_recommendations == 'SI')
                <tr>
                    <th>Fecha Fin Recomendaciones</th>
                    <th>Fecha de seguimiento a recomendaciones</th>
                    <th>Procedencia de las recomendaciones</th>
                </tr>
                <tr>
                    <td>{{$check->end_recommendations ? date('Y-m-d', strtotime($check->end_recommendations)) : '-'}}</td>
                    <td>{{date('Y-m-d', strtotime($check->monitoring_recommendations))}}</td>
                    <td>{{$check->emitter_origin}}</td>
                </tr>
                <tr>
                    <th>Clasificación reintegro</th>
                    <th>¿Reubicado?</th>
                    @if($check->relocated == 'NO')
                    <th>{{ Auth::user()->getKeywords()['detail_recommendations'] }}</th>
                    @else
                    <th>{{ Auth::user()->getKeywords()['position'] }} Actualizado</th>
                    @endif
                </tr>
                <tr>
                    <td>{{$check->refund_classification}}</td>
                    <td>{{$check->relocated}}</td>
                    @if($check->relocated == 'NO')
                    <td>{{$check->detail}}</td>
                    @else
                    <td>{{ isset($check->relocatedPosition['name']) ? $check->relocatedPosition['name'] : '' }}</td>
                    @endif
                </tr>
                    @if($check->relocated == 'SI')
                        @if ($locationForm['headquarter'] == 'NO')
                            <tr>
                                <th>{{ Auth::user()->getKeywords()['regional'] }} Actualizada</th>
                                <th colspan="3">{{ Auth::user()->getKeywords()['detail_recommendations'] }}</th>
                            </tr>
                            <tr>
                                <td>{{ isset($check->relocatedRegional['name']) ? $check->relocatedRegional['name'] : '' }}</td>
                                <td colspan="3">{{$check->detail}}</td>
                            </tr>
                        @endif

                        @if ($locationForm['headquarter'] == 'SI' && $locationForm['process'] == 'NO')
                            <tr>
                                <th>{{ Auth::user()->getKeywords()['regional'] }} Actualizada</th>
                                <th>{{ Auth::user()->getKeywords()['headquarter'] }} Actualizada</th>
                                <th colspan="2">{{ Auth::user()->getKeywords()['detail_recommendations'] }}</th>
                            </tr>
                            <tr>
                                <td>{{ isset($check->relocatedRegional['name']) ? $check->relocatedRegional['name'] : '' }}</td>
                                <td>{{ isset($check->relocatedHeadquarter['name']) ? $check->relocatedHeadquarter['name'] : '' }}</td>
                                <td colspan="2">{{$check->detail}}</td>
                            </tr>
                        @endif

                        @if ($locationForm['process'] == 'SI' && ($locationForm['area'] == 'SI' || $locationForm['area'] == 'NO'))
                            <tr>
                                <th>{{ Auth::user()->getKeywords()['regional'] }} Actualizada</th>
                                <th>{{ Auth::user()->getKeywords()['headquarter'] }} Actualizada</th>
                                <th>{{ Auth::user()->getKeywords()['process'] }} Actualizada</th>
                                <th>{{ Auth::user()->getKeywords()['detail_recommendations'] }}</th>
                            </tr>
                            <tr>
                                <td>{{ isset($check->relocatedRegional['name']) ? $check->relocatedRegional['name'] : '' }}</td>
                                <td>{{ isset($check->relocatedHeadquarter['name']) ? $check->relocatedHeadquarter['name'] : '' }}</td>
                                <td>{{ isset($check->relocatedProcess['name']) ? $check->relocatedProcess['name'] : '' }}</td>
                                <td>{{$check->detail}}</td>
                            </tr>
                        @endif
                    @endif
                @endif         
            </thead>
        </table>
    </div>

    <br><br>

    <div style="page-break-inside: avoid;">
        <table class="table-general">
            <thead>
                <tr>
                    <th>¿Tiene Restricción?</th>
                    @if($check->has_restrictions == 'SI')
                    <th>Parte del cuerpo afectada</th>
                    @endif
                </tr>
                <tr>
                    <td>{{$check->has_restrictions}}</td>
                    @if($check->has_restrictions == 'SI')
                    <td>{{$check->restriction->name}}</td>
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
                    <th>¿Incapacitado?</th>
                    @if($check->has_incapacitated == 'SI')
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    @endif
                </tr>
                <tr>
                    <td>{{$check->has_incapacitated ? $check->has_incapacitated : 'NO'}}</td>
                    @if($check->has_incapacitated == 'SI')
                    <td>{{$check->start_incapacitated ? date('Y-m-d', strtotime($check->start_incapacitated)) : '-'}}</td>
                    <td>{{$check->end_incapacitated ? date('Y-m-d', strtotime($check->end_incapacitated)) : '-'}}</td>
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
                    <th>Fecha Seguimiento Médico</th>
                    <th>Conclusión Seguimiento Médico</th>
                </tr>
                @foreach($check->medicalMonitorings as $medical)
                <tr>
                    <td>{{date('Y-m-d', strtotime($medical->set_at))}}</td>
                    <td>{{$medical->conclusion}}</td>
                </tr>
                @endforeach
                <tr>
                    <th>Fecha Seguimiento Laboral</th>
                    <th>Conclusión Seguimiento Laboral</th>
                </tr>
                @foreach($check->laborMonitorings as $labor)
                <tr>
                    <td>{{date('Y-m-d', strtotime($labor->set_at))}}</td>
                    <td>{{$labor->conclusion}}</td>
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
                    <th>¿En proceso de calificación de origen?</th>
                    <th>Entidad que Califica Origen</th>
                    <th>Clasificación de origen</th>
                    <th>¿Es definitiva esta decisión?</th>
                    <th>Tipo de calificacion</th>
                </tr>
                <tr>
                    <td>{{$check->in_process_origin}}</td>
                    <td>{{$check->emitter_origin}}</td>
                    <td>{{$check->qualification_origin}}</td>
                    <td>{{$check->is_firm_process_origin}}</td>
                    <td>{{$check->type_qualification_origin}}</td>
                </tr>
                @if($check->is_firm_process_origin == 'NO')
                <tr>
                    <th colspan="5">Primera controversia</th>
                </tr>
                <tr>
                    <th>Fecha calificación primera controversia</th>
                    <th>Entidad que Califica la primera controversia</th>
                    <th>Clasificación de origen la primera controversia</th>
                    <th>¿Es definitiva esta decisión?</th>                    
                    <th>Tipo de calificacion de la primera controversia</th>
                </tr>
                <tr>
                    <td>{{date('Y-m-d', strtotime($check->date_controversy_origin_1))}}</td>
                    <td>{{$check->emitter_controversy_origin_1}}</td>
                    <td>{{$check->qualification_controversy_1}}</td>
                    <td>{{$check->is_firm_controversy_1}}</td>
                    <td>{{$check->type_controversy_origin_1}}</td>
                </tr>
                @endif
                @if($check->is_firm_controversy_1 == 'NO')
                <tr>
                    <th colspan="5">Segunda controversia</th>
                </tr>
                <tr>
                    <th>Fecha calificación segunda controversia</th>
                    <th colspan="2">Entidad que Califica la segunda controversia</th>
                    <th>Clasificación de origen la segunda controversia</th>   
                    <th>Tipo de calificacion de la segunda controversia</th>
                </tr>
                <tr>
                    <td>{{date('Y-m-d', strtotime($check->date_controversy_origin_2))}}</td>
                    <td colspan="2">{{$check->emitter_controversy_origin_2}}</td>
                    <td>{{$check->qualification_controversy_2}}</td>
                    <td>{{$check->type_controversy_origin_2}}</td>
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
                    <th>¿En proceso de PCL?</th>
                    @if($check->in_process_pcl == 'SI')
                    <th>Calificación PCL</th>
                    <th>Entidad que califica PCL</th>
                    <th>¿Es definitiva esta decisión?</th>
                    @else
                        <th>¿Ya se hizo el proceso de PCL?</th>

                        @if($check->process_pcl_done == 'SI')
                        <th>Fecha proceso PCL</th>
                        <th>Calificación PCL</th>
                        <th>Entidad que califica PCL</th>
                        <th>¿Es definitiva esta decisión?</th>
                        @endif
                    @endif
                </tr>
                <tr>
                    <td>{{$check->in_process_pcl}}</td>
                    @if($check->in_process_pcl == 'SI')
                    <td>{{$check->pcl}}</td>
                    <td>{{$check->entity_rating_pcl}}</td>
                    <td>{{$check->is_firm_process_pcl}}</td>
                    @else
                        <td>{{$check->process_pcl_done}}</td>

                        @if($check->process_pcl_done == 'SI')
                        <td>{{date('Y-m-d', strtotime($check->process_pcl_done_date))}}</td>
                        <td>{{$check->pcl}}</td>
                        <td>{{$check->entity_rating_pcl}}</td>
                        <td>{{$check->is_firm_process_pcl}}</td>
                        @endif
                    @endif
                </tr>
                @if($check->is_firm_process_pcl == 'NO' && $check->process_pcl_done == 'SI')
                    @if($check->in_process_pcl == 'NO')
                    <tr>
                        <th colspan="6">Primera controversia</th>
                    </tr>
                    <tr>
                        <th colspan="2">Fecha calificación primera controversia</th>
                        <th colspan="2">Entidad que Califica la primera controversia</th>
                        <th>Calificación</th>
                        <th>¿Es definitiva esta decisión?</th>
                    </tr>
                    <tr>
                        <td colspan="2">{{date('Y-m-d', strtotime($check->date_controversy_pcl_1))}}</td>
                        <td colspan="2">{{$check->emitter_controversy_origin_1}}</td>
                        <td>{{$check->qualification_controversy_1}}</td>
                        <td>{{$check->is_firm_controversy_1}}</td>
                    </tr>
                    @else
                    <tr>
                        <th colspan="4">Primera controversia</th>
                    </tr>
                    <tr>
                        <th>Fecha calificación primera controversia</th>
                        <th>Entidad que Califica la primera controversia</th>
                        <th>Calificación</th>
                        <th>¿Es definitiva esta decisión?</th>
                    </tr>
                    <tr>
                        <td>{{date('Y-m-d', strtotime($check->$check->date_controversy_pcl_1))}}</td>
                        <td>{{$check->emitter_controversy_origin_1}}</td>
                        <td>{{$check->qualification_controversy_1}}</td>
                        <td>{{$check->is_firm_controversy_1}}</td>
                    </tr>
                    @endif
                @endif

                @if($check->is_firm_process_pcl_1 == 'NO' && $check->process_pcl_done == 'SI')
                    @if($check->in_process_pcl == 'NO')
                    <tr>
                        <th colspan="6">Segunda controversia</th>
                    </tr>
                    <tr>
                        <th colspan="2">Fecha calificación segunda controversia</th>
                        <th colspan="2">Entidad que Califica la segunda controversia</th>
                        <th colspan="2">Clasificación de origen la segunda controversia</th>
                    </tr>
                    <tr>
                        <td colspan="2">{{date('Y-m-d', strtotime($check->$check->date_controversy_pcl_2))}}</td>
                        <td colspan="2">{{$check->punctuation_controversy_plc_2}}</td>
                        <td colspan="2">{{$check->punctuation_controversy_plc_2}}</td>
                    </tr>
                    @else
                        <tr>
                            <th colspan="4">Segunda controversia</th>
                        </tr>
                        <tr>
                            <th>Fecha calificación segunda controversia</th>
                            <th colspan="2">Entidad que Califica la segunda controversia</th>
                            <th>Clasificación de origen la segunda controversia</th>
                        </tr>
                        <tr>
                            <td>{{$date('Y-m-d', strtotime($check->$check->date_controversy_pcl_2))}}</td>
                            <td colspan="2">{{$check->punctuation_controversy_plc_2}}</td>
                            <td>{{$check->punctuation_controversy_plc_2}}</td>
                        </tr>                    
                    @endif
                @endif
            </thead>
        </table>
    </div>

    <br><br>

     <div style="page-break-inside: avoid;">
        <table class="table-general">
            <thead>
                <tr>
                    <td colspan="3">{{ Auth::user()->getKeywords()['tracings'] }}</td>
                </tr>
                <tr>
                    <th>Creador</th>
                    <th>Fecha</th>
                    <th>Descripción</th>
                </tr>
                @foreach($check->tracings as $tracing)
                <tr>
                    <td>{{$tracing->madeBy->name}}</td>
                    <td>{{date('Y-m-d', strtotime($tracing->created_at))}}</td>
                    <td>{{$tracing->description}}</td>
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
                    <td colspan="3">{{ Auth::user()->getKeywords()['labor_notes'] }}</td>
                </tr>
                <tr>
                    <th>Creador</th>
                    <th>Fecha</th>
                    <th>Descripción</th>
                </tr>
                @foreach($check->laborNotes as $labor)
                <tr>
                    <td>{{$labor->madeBy->name}}</td>
                    <td>{{date('Y-m-d', strtotime($labor->created_at))}}</td>
                    <td>{{$labor->description}}</td>
                </tr>
                @endforeach
            </thead>
        </table>
    </div>

    <br><br>

</body>
</html>