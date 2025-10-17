<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        @page { margin: 40px 5px; }
        #header { position: fixed; left: 100px; right: 100px; top: 5px; height: 50px;text-align: center; }
        #header .page:after { content: counter(page); }

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
<body style="margin: 50px; margin-top: 120px;">
    <div id="header">
        <table class="table-general" style='border: 1px solid black;'>
            <thead>
                <tr>
                    @if ($inspections["logo"])
                    <th rowspan="2" style='border-right: 1px solid black; padding: 1px;'><img src="{{ public_path('storage/administrative/logos/').$inspections['logo'] }}" width="50px" height="50px"/></th>
                    @endif
                    <th colspan="2" style='padding: 1px;'>
                        <p><b>Inspección no planeada</b></p>
                    </th>
                    <th style='padding: 1px;'>
                        <p>Fecha Creación: {{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $inspections["created_at"])->format('Y-m-d')}}</p>
                    </th>          
                </tr>    
                <tr>
                    
                    <th style='padding: 1px;'>
                        <p>Usuario: {{ $inspections["user"] }}</p>
                    </th>
                    
                    <th style='padding: 1px;'>
                        <p>Estado: {{  $inspections["state"] ? $inspections["state"] : 'Por Revisar' }}</p>
                    </th>
                    
                    <th style='padding: 1px;'>
                        <p class="page">Página </p>
                    </th>
                </tr>
            </thead>
        </table>
    </div>
    <div style="page-break-inside: avoid;">
        <table class="table-general">
            <thead>
                <tr>
                    <th>{{ Auth::user()->getKeywords()['regional'] }}</th>
                    @if ($inspections["headquarter"])
                        <th>{{ Auth::user()->getKeywords()['headquarter'] }}</th>
                    @endif
                </tr>
                <tr>
                    <td>{{ $inspections["regional"] }}</th>
                    @if ($inspections["headquarter"])
                        <td>{{ $inspections["headquarter"] }}</th>
                    @endif
                </tr>
                @if ($inspections["process"])
                    <tr>
                        @if ($inspections["process"] && !$inspections["area"])
                            <th>{{ Auth::user()->getKeywords()['process'] }}</th>
                            @if ($inspections["compliance"])
                                <th colspan="1">Porcentaje de cumplimiento</th>
                            @endif
                        @endif
                        @if ($inspections["area"])
                            <th>{{ Auth::user()->getKeywords()['area'] }}</th>
                        @endif
                    </tr>
                @endif
                @if ($inspections["process"])
                    <tr>
                        @if ($inspections["process"] && !$inspections["area"])
                            <td>{{ $inspections["process"] }}</th>
                        @endif
                        @if ($inspections["area"])
                            <td colspan=>{{ $inspections["area"] }}</td>
                        @endif
                    </tr>
                @endif
            </thead>
        </table>    
    </div>

    <br><br>

    <div style="page-break-inside: avoid;">
        <p style="text-align: center; font-size: 12px;"><b>Información</b></p>
        <table>
            <thead>
                <tr>
                    <th>Severidad:</th>
                    <td>{{ $inspections["rate"] }}</td>
                </tr>
                <tr>
                    <th>Tipo de reporte:</th>
                    <td>{{ $inspections["type_condition"] }}</td>
                </tr>
                <tr>
                    <th>Hallazgo:</th>
                    <td>{{ $inspections["condition"] }}</td>
                </tr>
                <tr>
                    <th>Observación:</th>
                    <td>{{ $inspections["observation"] }}</td>
                </tr>
                <tr>
                    <th>Otro Hallazgo:</th>
                    <td>{{ $inspections["other_condition"] }}</td>
                </tr> 
            </thead>
        </table>   
    </div>
    <br><br>
    @if($inspections["img_1"] || $inspections["img_2"] || $inspections['img_3'])
    <div style="page-break-inside: avoid;">
        <p style="text-align: center; font-size: 12px;"><b>Imágenes</b></p>       
        <table>
            <thead>
                <tr>
                    @if($inspections["img_1"])
                        <td style="border-right: none;">
                            <img width="200" height="150" src="{{$inspections['img_1']}}">
                        </td>                    
                    @endif
                    @if($inspections["img_2"])
                        <td style="border-right: none;">
                            <img width="200" height="150" src="{{$inspections['img_2']}}">
                        </td>                    
                    @endif
                    @if($inspections["img_3"])
                        <td style="border-right: none;">
                            <img width="200" height="150" src="{{$inspections['img_3']}}">
                        </td>                    
                    @endif
                </tr>                            
            </thead>
        </table>
    </div>    
    @endif 

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
                @foreach($inspections["actionPlan"]["activities"] as $key => $activity)
                    <tr>
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
</body>
</html>