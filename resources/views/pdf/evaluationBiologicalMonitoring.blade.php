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
        @if ($evaluations->logo)
        <div style="page-break-inside: avoid; text-align: right; padding-bottom: 10px;"><img src="{{ public_path('storage/administrative/logos/').$evaluations->logo }}" width="120px" height="120px"/></div>
        @endif
    <div style="page-break-inside: avoid;">
        <h3> Fecha de Evaluación: {{$evaluations->evaluation_date}}</h3>
    </div>

    <br><br>

    <div style="page-break-inside: avoid;">
        <table>
            <thead>
                <tr>
                    <th>Evaluadores</th>
                    @if(COUNT($evaluations->interviewees) > 0)
                    <th>Otros participantes</th>
                    @endif
                </tr>
                 <tr>
                    <td valign="top">
                        <table>
                            <tbody>
                                @foreach($evaluations->evaluators as $evaluator)
                                <tr>
                                    <td>{{$evaluator->name}}</td>
                                </tr>
                                @endforeach     
                            </tbody>
                        </table> 
                    </td>
                    @if(COUNT($evaluations->interviewees) > 0)
                        <td valign="top">
                            <table>
                                <tbody>
                                    @foreach($evaluations->interviewees as $interviewe)
                                    <tr>
                                        <td>{{$interviewe->name}} - {{$interviewe->position}}</td>
                                    </tr>
                                    @endforeach     
                                </tbody>
                            </table>
                        </td>
                    @endif
                </tr>
            </thead>
        </table>
    </div>
    
    <br><br>

    <div style="page-break-inside: avoid;">
        <table>
            <thead>
                <tr>
                    <th>Observación</th>
                </tr>
                <tr>
                    <td class="title-obj">{{$evaluations->observation}}</td>
                </tr>
            </thead>
        </table>
    </div>

    <br><br>

    <div style="page-break-inside: avoid;">
        <p style="text-align: center; font-size: 12px;"><b>Etapas</b></p>
        @foreach($evaluations->evaluation->stages as $keyObj => $objective)
            <p style="text-align: justify; font-size: 12px;"><b>{{ $keyObj + 1 }} - {{$objective->description}}</b></p>

            @foreach($objective->criterion as $keySub => $subobjective)
                <p style="text-align: justify; font-size: 12px;"><b>{{ $keyObj + 1 }}.{{ $keySub + 1 }} - {{$subobjective->description}}</b></p><br>
                    @foreach($subobjective->items as $keyItem => $item)
                        <table>
                            <thead>
                                <tr>
                                    <th colspan="3">Item</th>
                                    <th>Cumplimiento</th>
                                </tr>
                                <tr>
                                    <td colspan="3" class="title-obj">{{ $keyObj + 1 }}.{{ $keySub + 1 }}.{{ $keyItem + 1 }} - {{ $item->description }}</td>
                                    
                                    <td>{{$item->value ? $item->value : 'Sin Calificar'}}</td>
                                </tr>
                                @if(COUNT($item->files) > 0)
                                    <tr>
                                        <th colspan="4">Archivos</th>
                                    </tr>   
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
                                    <tr>
                                        <th colspan="4">Observaciones</th>
                                    </tr>                          
                                    @foreach($item->observations as $observation)
                                        <tr>
                                            <td colspan="4" class="title-obj">{{$observation->description}}</td>
                                        </tr>
                                    @endforeach
                                @endif      
                            </thead>
                        </table>
                        <br>                             
                    @endforeach
            @endforeach
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
                @foreach($evaluations->evaluation->stages as $keyObj => $objective)
                    @foreach($objective->criterion as $keySub => $subobjective)
                        @foreach($subobjective->items as $keyItem => $item)
                            @foreach($item->actionPlan["activities"] as $activity)
                                <tr>
                                    <td>{{ $keyObj + 1 }}.{{ $keySub + 1 }}.{{ $keyItem + 1 }}</td>
                                    <td class="title-obj">{{$activity["description"]}}</td>
                                    <td>{{$activity["multiselect_responsible"]["name"]}}</td>
                                    <td>{{$activity["expiration_date"] ? date('Y-m-d', strtotime($activity["expiration_date"])) : '-' }}</td>
                                    <td>{{$activity["execution_date"] ? date('Y-m-d', strtotime($activity["execution_date"])) : '-' }}</td>
                                    <td>{{$activity["state"]}}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    @endforeach
                @endforeach
            </thead>
        </table>   
    </div>

    <br><br>

    {{--<div style="page-break-inside: avoid;">
        <table>
            <thead>
                <tr>
                    <th>Porcentaje de cumplimiento</th>
                    @foreach($evaluations->evaluation->types_rating as $type)
                        @if($type["apply"] == 'SI')
                        <th>{{$type["name"]}}</th>
                        @endif
                    @endforeach
                <tr>
                    <td><b>{{ $keyObj + 1 }}.{{ $keySub + 1 }} - {{$subobjective->description}}</b></td>
                    @foreach($subobjective->report as $repor)
                    <td>{{$repor["percentage"]}}%</td>
                    @endforeach
                </tr>
            </thead>
        </table>
    </div>--}}
</body>
</html>