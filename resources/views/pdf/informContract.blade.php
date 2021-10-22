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
        @if ($inform->logo)
        <div style="page-break-inside: avoid; text-align: right; padding-bottom: 10px;"><img src="{{ public_path('storage/administrative/logos/').$inform->logo }}" width="120px" height="120px"/></div>
        @endif
    <div style="page-break-inside: avoid;">
        <h3> Fecha de Evaluación: {{$inform->inform_date}}</h3>
    </div>

    <br><br>

    <div style="page-break-inside: avoid;">
        <table>
            <thead>
                <tr>
                    <th>Evaluador</th>
                </tr>
                 <tr>
                    <td valign="top">
                        {{$inform->evaluator->name}}
                    </td>
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
                    <td class="title-obj">{{$inform->observation}}</td>
                </tr>
            </thead>
        </table>
    </div>

    <br><br>

    <div style="page-break-inside: avoid;">
        <p style="text-align: center; font-size: 12px;"><b>Temas</b></p>
        @foreach($inform->inform->themes as $keyObj => $objective)
            <p style="text-align: justify; font-size: 12px;"><b>{{ $keyObj + 1 }} - {{$objective->description}}</b></p>
            @foreach($objective->items as $keyItem => $item)
                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Programado</th>
                            <th>Ejecutado</th>
                            <th>% Cumplimiento</th>
                        </tr>
                        <tr>
                            <td class="title-obj">{{ $keyObj + 1 }}.{{ $keyItem + 1 }} - {{ $item->description }}</td>
                            
                            <td>{{$item->programed ? $item->programed : 'Sin Calificar'}}</td>

                            <td>{{$item->executed ? $item->executed : 'Sin Calificar'}}</td>

                            <td>{{$item->compliance ? $item->compliance : 'Sin Cumplimiento'}}</td>
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