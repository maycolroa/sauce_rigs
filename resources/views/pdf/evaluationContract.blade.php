<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        table {
          font-family: arial, 
          sans-serif;          
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
        <h3> Fecha de Evaluación: {{$evaluations->evaluation_date}}</h3>
        <h3>Contratista: {{$evaluations->contract->social_reason}}</h3>
    </div>

    <br><br>

    <div style="page-break-inside: avoid;">
        <table class="table-general">
            <thead> 
                    <tr>
                        <th>Categoria</th>
                        <th>Total</th>
                        <th>Total Cumple</th>
                        <th>Cumplimiento (%)</th>
                    </tr>                   
                @foreach($evaluations->evaluation->report_total as $rate)
                    <tr>
                        <td>{{ $rate["category"] }}</td>
                        <td>{{ $rate["total"] }}</td>
                        <td>{{ $rate["total_c"] }}</td>
                        <td>{{ $rate["percentage"] }}%</td>
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
                    <th>Nit</th>
                    <th>Dirección</th>
                    <th>Teléfono</th>
                    <th>Clase de riesgo</th>
                </tr>
                <tr>
                    <td>{{$evaluations->contract->nit}}</td>
                    <td>{{$evaluations->contract->address}}</td>
                    <td>{{$evaluations->contract->phone}}</td>
                    <td>{{$evaluations->contract->high_risk_work}}</td>
                </tr>
                <tr>
                    <th>Nombre del coordinador de gestión ambiental</th>
                    <th>Nombre del coordinador de SST</th>
                    <th>Actividad económica general de la empresa</th>
                    <th>N° de personas que laboran en el HPTU</th>
                </tr>
                <tr>
                    <td>{{$evaluations->contract->legal_representative_name}}</td>
                    <td>{{$evaluations->contract->SG_SST_name}}</td>
                    <td>{{$evaluations->contract->economic_activity_of_company}}</td>
                    <td>{{$evaluations->contract->number_workers}}</td>
                </tr>
            </thead>
        </table>
    </div>

    <br><br>

    <div style="page-break-inside: avoid;">
        <table>
            <thead>
                 <tr>
                    <td valign="top">
                        <table>
                            <thead>
                                <tr>
                                    <th>Evaluadores</th>
                                </tr>
                                @foreach($evaluations->evaluators as $evaluator)
                                <tr>
                                    <td>{{$evaluator->name}}</td>
                                </tr>
                                @endforeach     
                            </thead>
                        </table> 
                    </td>
                    @if(COUNT($evaluations->interviewees) > 0)
                        <td valign="top">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Entrevistados</th>
                                    </tr>
                                    @foreach($evaluations->interviewees as $interviewe)
                                    <tr>
                                        <td>{{$interviewe->name}} - {{$interviewe->position}}</td>
                                    </tr>
                                    @endforeach     
                                </thead>
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
    
    <div style="page-break-inside: avoid;">
    <table>
        <thead>
            <tr>
                <th>Temas</th>
            </tr>
            @foreach($evaluations->evaluation->objectives as $keyObj => $objective)
            <tr>
                <td class="body-themes">
                    <table>
                        <thead>
                            <tr>
                                <th class="title-obj">{{ $keyObj + 1 }} - {{$objective->description}}</th>
                            </tr>
                            @foreach($objective->subobjectives as $keySub => $subobjective)
                            <tr>
                                <td class="body-themes">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="title-sub">{{ $keyObj + 1 }}.{{ $keySub + 1 }} - {{$subobjective->description}}</th>
                                            </tr>
                                            <tr>
                                                <td class="body-themes">
                                                    <table>
                                                        <thead>
                                                            <tr>
                                                                <th>Item</th>
                                                                @foreach($evaluations->evaluation->types_rating as $type)
                                                                @if($type["apply"] == 'SI')
                                                                <th>{{$type["name"]}}</th>
                                                                @endif
                                                                @endforeach
                                                            </tr>
                                                            @foreach($subobjective->items as $keyItem => $item)
                                                            <tr>
                                                                <td class="title-obj">{{ $keyObj + 1 }}.{{ $keySub + 1 }}.{{ $keyItem + 1 }} - {{ $item->description }}</td>
                                                                @foreach($item->ratings as $rating)
                                                                <td>{{$rating["value"] ? ($rating["value"] == 'pending' ? 'NO' : $rating["value"]) : 'N/A'}}</td>
                                                                @endforeach
                                                            </tr>
                                                            @if(COUNT($item->files) > 0)
                                                            
                                                                @foreach($item->files_pdf as $row)
                                                                    <tr>
                                                                    @foreach($row as $col)
                                                                        <td style="border-right: none;">
                                                                            <img width="200" height="150" src="{{$col}}">
                                                                        </td>
                                                                    @endforeach
                                                                    </tr>
                                                                @endforeach
                                                            @endif                   
                                                            @endforeach
                                                            <tr>
                                                                <td>Porcentaje de cumplimiento</td>
                                                                @foreach($subobjective->report as $repor)
                                                                <td>{{$repor["percentage"]}}%</td>
                                                                @endforeach
                                                            </tr>
                                                        </thead>
                                                    </table>
                                                </td>
                                            </tr>
                                        </thead>                                        
                                    </table>
                                </td>                                
                            </tr>
                            @endforeach
                        </thead>
                    </table>
                </td>
            </tr>
            @endforeach
        </thead>
    </table>
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
                @foreach($evaluations->evaluation->objectives as $keyObj => $objective)
                    @foreach($objective->subobjectives as $keySub => $subobjective)
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

    <div style="page-break-inside: avoid;">
        <table>
            <thead>
                <tr>
                    <th colspan="2">Observaciones de los items</th>
                </tr>
                <tr>
                    <th>Item</th>
                    <th>Descripción</th>
                </tr>                
                @foreach($evaluations->evaluation->objectives as $keyObj => $objective)
                    @foreach($objective->subobjectives as $keySub => $subobjective)
                        @foreach($subobjective->items as $keyItem => $item)
                            @foreach($item->observations as $observation)
                                <tr>
                                    <td>{{ $keyObj + 1 }}.{{ $keySub + 1 }}.{{ $keyItem + 1 }}</td>
                                    <td class="title-obj">{{$observation->description}}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    @endforeach
                @endforeach
            </thead>
        </table>   
    </div>
</body>
</html>