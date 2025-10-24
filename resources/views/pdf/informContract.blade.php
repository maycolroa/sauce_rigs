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
            font-size: 12px;
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
<body style="margin: 20px; margin-top: 0px;">
    <div style="page-break-inside: avoid;">
        @if ($inform->logo)
        <div style="page-break-inside: avoid; text-align: right; padding-bottom: 10px;"><img src="{{ $inform->logo }}" width="400px" height="120px"/></div>
        @endif
        <h3> {{$inform->inform_base->name}}</h3>
        <h3> Fecha de Evaluación: {{$inform->inform_date}}</h3>
        <br><br>
        <table>
            <thead>
                <tr>
                    <th>Contratista</th>
                    <th>Evaluador</th>
                    <th>Observación</th>
                    @if($inform->proyect_id)
                    <th>Proyecto</th>
                    @endif
                </tr>
                 <tr>
                     <td valign="top">
                        {{$inform->contract->social_reason}}
                    </td>
                    <td valign="top">
                        {{$inform->evaluator->name}}
                    </td>
                    <td valign="top">
                        {{$inform->observation}}
                    </td>
                    @if($inform->proyect_id)
                    <td valign="top">
                        {{$inform->proyect->name}}
                    </td>
                    @endif
                </tr>
            </thead>
        </table>
        <br><br>
        <p style="text-align: center; font-size: 12px;"><b>Temas</b></p>
        @foreach($inform->inform->themes as $keyObj => $objective)
            <div style="page-break-inside: avoid;">
                <p style="text-align: justify; font-size: 12px;"><b>{{ $keyObj + 1 }} - {{$objective->description}}</b></p>
                <div style="page-break-inside: avoid;">
                @foreach($objective->items as $keyItem => $item)
                    <table>
                        <thead>
                            <tr style="width:100%">
                                @if($item->show_program_value == 'SI')
                                <th style="width:25%">Item</th>
                                @else
                                <th style="width:50%">Item</th>
                                @endif
                                @if($item->show_program_value == 'SI')
                                <th style="width:25%">Programado</th>
                                @endif
                                @if($item->show_program_value == 'SI')
                                <th style="width:25%">Ejecutado</th>
                                @else
                                <th style="width:50%">Ejecutado</th>
                                @endif
                                @if($item->show_program_value == 'SI')
                                <th style="width:25%">% Cumplimiento</th>
                                @endif
                            </tr>
                            <tr style="width:100%">
                                @if($item->show_program_value == 'SI')
                                <td style="width:25%" class="title-obj">{{ $keyObj + 1 }}.{{ $keyItem + 1 }} - {{ $item->description }}</td>
                                @else
                                <td style="width:50%" class="title-obj">{{ $keyObj + 1 }}.{{ $keyItem + 1 }} - {{ $item->description }}</td>
                                @endif
                                
                                @if($item->show_program_value == 'SI')
                                <td style="width:25%">{{$item->programmed ? $item->programmed : 'Sin Calificar'}}</td>
                                @endif

                                @if($item->show_program_value == 'SI')
                                <td style="width:25%">{{$item->executed > 0 ? $item->executed  : ($item->executed != null ? '0' : 'Sin Calificar')}}</td>
                                @else
                                <td style="width:50%">{{$item->executed > 0 ? $item->executed  : ($item->executed != null ? '0' : 'Sin Calificar')}}</td>
                                @endif

                                @if($item->show_program_value == 'SI')
                                <td style="width:25%">{{$item->compliance > 0 ? $item->compliance : ($item->compliance != null ? '0' :'Sin Cumplimiento')}}</td>
                                @endif
                            </tr>
                            @if(COUNT($item->files) > 0)
                                @if($item->show_program_value == 'SI')
                                <tr>
                                    <th colspan="4">Archivos</th>
                                </tr>
                                @else
                                <tr>
                                    <th colspan="2">Archivos</th>
                                </tr>
                                @endif   
                                @foreach($item->files_pdf as $row)
                                    <tr>
                                    @foreach($row as $col)
                                        @if($col["type"] != 'pdf')
                                        <td style="border-right: none;">
                                            <img width="200" height="150" src="{{$col['file']}}">
                                        </td>
                                        @else
                                            <td style="border-right: none;">
                                                <p>{{$col['name']}}</p>
                                            </td>
                                        @endif
                                    @endforeach
                                    </tr>
                                @endforeach
                            @endif
                            @if(COUNT($item->observations) > 0)
                                @if($item->show_program_value == 'SI')
                                <tr>
                                    <th colspan="4">Observaciones</th>
                                </tr>
                                @foreach($item->observations as $observation)
                                    <tr>
                                        <td colspan="4" class="title-obj">{{$observation->description}}</td>
                                    </tr>
                                @endforeach
                                @else
                                <tr>
                                    <th colspan="2">Observaciones</th>
                                </tr>
                                @foreach($item->observations as $observation)
                                    <tr>
                                        <td colspan="2" class="title-obj">{{$observation->description}}</td>
                                    </tr>
                                @endforeach
                                @endif
                            @endif      
                        </thead>
                    </table>
                <br>                             
            @endforeach
            </div>
            </div>
        @endforeach        
    </div>

    <br><br><br>

    <div style="page-break-inside: avoid;">
        <table>
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
                @foreach($inform->inform->themes as $keyObj => $theme)
                    @foreach($theme->items as $keyItem => $item)
                        @foreach($item->actionPlan["activities"] as $activity)
                            <tr>
                                <td>{{ $keyObj + 1 }}.{{ $keyItem + 1 }}</td>
                                <td class="title-obj">{{$activity["description"]}}</td>
                                <td>{{$activity["multiselect_responsible"]["name"]}}</td>
                                <td>{{$activity["expiration_date"] ? date('Y-m-d', strtotime($activity["expiration_date"])) : '-' }}</td>
                                <td>{{$activity["execution_date"] ? date('Y-m-d', strtotime($activity["execution_date"])) : '-' }}</td>
                                <td>{{$activity["state"]}}</td>
                            </tr>
                        @endforeach
                    @endforeach
                @endforeach
            </thead>
        </table>   
    </div>
</body>
</html>