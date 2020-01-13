<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        table {
          font-family: arial, sans-serif;          width: 100%;
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
        <table class="table-general">
            <thead>
                <tr>
                    <th>Inspección</th>
                    <th>{{ Auth::user()->getKeywords()['headquarter'] }}</th>
                    <th>{{ Auth::user()->getKeywords()['area'] }}</th>
                </tr>
                <tr>
                    <td>{{$inspections["inspection"]}}</td>
                    <td>{{$inspections["headquarter"]}}</td>
                    <td>{{$inspections["area"]}}</td>
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
            </thead>
        </table>

    <br><br>

    <table>
        <thead>
            <tr>
                <th>Temas</th>
            </tr>
            @foreach($inspections["themes"] as $keyTheme => $theme)
            <tr>
                <td class="body-themes">
                    <table>
                        <thead>
                            <tr>
                                <th class="title-obj">{{ $keyTheme + 1 }} - {{$theme["name"]}}</th>
                            </tr>
                            <tr>
                                <td class="body-themes">
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
                                </td>
                            </tr>
                        </thead>
                    </table>
                </td>
            </tr>
            @endforeach
        </thead>
    </table>
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