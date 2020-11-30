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
<body style="margin: 50px; margin-top: 0px;">
    <div style="page-break-inside: avoid;">
        @if ($inspections["logo"])
        <div style="page-break-inside: avoid; text-align: right; padding-bottom: 10px;"><img src="{{ public_path('storage/administrative/logos/').$inspections['logo'] }}" width="120px" height="120px"/></div>
        @endif
        <table class="table-general">
            <thead>
                <tr>
                    <th colspan="3">Inspección</th>
                </tr>
                <tr>
                    <td colspan="3">{{ $inspections["inspection"] }}</td>
                </tr>
                <tr>
                    <th>Fecha Creación</th>
                    <th>Fecha Calificación</th>
                    <th>Responsable</th>
                </tr>
                <tr>
                    <td>{{$inspections["created_at"]}}</td>
                    <td>{{$inspections["qualification_date"]}}</td>
                    <td>{{$inspections["qualifier"]}}</td>
                </tr>
                <tr>
                    <th>{{ Auth::user()->getKeywords()['regional'] }}</th>
                    @if ($inspections["headquarter"])
                        <th>{{ Auth::user()->getKeywords()['headquarter'] }}</th>
                    @endif
                    @if ($inspections["process"])
                        <th>{{ Auth::user()->getKeywords()['process'] }}</th>
                    @endif
                </tr>
                <tr>
                    <th>{{ $inspections["regional"] }}</th>
                    @if ($inspections["headquarter"])
                        <th>{{ $inspections["headquarter"] }}</th>
                    @endif
                    @if ($inspections["process"])
                        <th>{{ $inspections["process"] }}</th>
                    @endif
                </tr>
                @if ($inspections["area"])
                    <tr>
                        <th colspan="3">{{ Auth::user()->getKeywords()['area'] }}</th>
                    </tr>
                    <tr>
                        <td colspan="3">{{ $inspections["area"] }}</td>
                    </tr>
                @endif
            </thead>
        </table>

    <br><br>

    @if (COUNT($inspections["add_fields"]) > 0)
        <table class="table-general">
            <thead>
                <tr>
                    <th colspan="2">Campos Adicionales</th>
                </tr>
                <tr>
                    <th>Nombre</th>
                    <th>Valor</th>
                </tr>
                @foreach($inspections["add_fields"] as $keyAdd => $add)
                <tr>
                    <td>{{$add["name"]}}</td>
                    <td>{{$add["value"]}}</td>
                </tr>
                @endforeach
        </table>

    <br><br>
    @endif

    <div style="page-break-inside: avoid;">
        <p style="text-align: center; font-size: 12px;"><b>Temas</b></p>

        @foreach($inspections["themes"] as $keyTheme => $theme)
            <p style="text-align: justify; font-size: 12px;"><b>{{ $keyTheme + 1 }} - {{$theme["name"]}}</b></p>
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Calificación</th>
                        <th>Hallazgo</th>
                    </tr>
                    @foreach($theme["items"] as $keyItem => $item)
                    <tr>
                        <td class="title-obj">{{ $keyTheme + 1 }}.{{ $keyItem + 1 }} - {{ $item["description"] }}</td>
                        <td>{{ $item["qualification"] }}</td>
                        <td>{{ $item["find"] }}</td>
                    </tr> 
                    @if($item["photo_1"] || $item["photo_2"])
                    <tr>
                        <td style="border-right: none;">
                            @if($item["photo_1"])
                            <img width="200" height="150" src="{{$item['path_1']}}">
                            @endif
                        </td>
                        <td style="border-right: none; border-left: none;"></td>
                        <td style="border-left: none;">
                            @if($item["photo_2"])
                            <img src="{{$item['path_2']}}" width="200" height="150">
                            @endif
                        </td>
                    </tr>
                    @endif
                    @endforeach                                      
                </thead>
            </table>
        @endforeach
    </div>

    <br><br>

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
                @foreach($inspections["themes"] as $keyTheme => $theme)
                    @foreach($theme["items"] as $keyItem => $item)
                        @foreach($item["actionPlan"]["activities"] as $activity)
                            <tr>
                                <td>{{ $keyTheme + 1 }}.{{ $keyItem + 1 }}</td>
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