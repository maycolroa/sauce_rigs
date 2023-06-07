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
                    <th style='padding: 1px; width: 20%'>
                        <p>Período de vigencia: {{$listCheck['validity_period']}}</p>
                    </th>
                    <th style='padding: 1px; width: 20%'>
                        <p>Contratista: {{$listCheck['contract_name']}}</p>
                    </th>
                    <th style='padding: 1px; width: 20%'>
                        <p>Estado: {{$listCheck['state']}}</p>
                    </th>
                    <th style='padding: 1px; width: 20%'>
                        <p>Usuario Creador: {{$listCheck["user_Creator"]}}</p>
                        <p class="page">Página </p>
                    </th>
                </tr>
            </thead>
        </table>
    </div>

    <div>
        <p style="text-align: center; font-size: 12px;"><b>Procentajes de cumplimiento</b></p>
        <table>
            <thead>
                <tr>
                    <th>Cumple</th>
                    <th>No Cumple</th>
                    <th>No aplica</th>
                    <th>Total</th>
                </tr>
                <tr>                    
                    <td>{{ $listCheck["cumplimiento"]['p_cumple'] }}%</td>
                    <td>{{ $listCheck["cumplimiento"]['pp_no_cumple'] }}%</td>
                    <td>{{ $listCheck["cumplimiento"]['p_no_aplica'] }}%</td>
                    <td>{{ $listCheck["cumplimiento"]['p_total'] }}%</td>
                </tr>              
            </thead>
        </table>
    </div>
    <br><br>

    <div>
        <p style="text-align: center; font-size: 12px;"><b>Estándares Mínimos</b></p>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Calificación</th>
                    <th>Observaciones</th>
                </tr>
                @foreach($listCheck["items"] as $keyItem => $item)
                <tr>
                    <td class="title-obj">
                        <p>{{ $keyItem + 1 }} - {{ $item["item_name"] }}</p>
                        <p>{{ $item["criterion_description"] }}</p>
                    </td>
                    <td>{{ $item["qualification"] }}</td>
                    <td>{{ $item["observations"] }}</td>
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
                @foreach($listCheck["items"] as $keyItem => $item)
                    @foreach($item["actionPlan"]["activities"] as $activity)
                        <tr>
                            <td>{{ $keyItem + 1 }}</td>
                            <td class="title-obj">{{$activity["description"]}}</td>
                            <td>{{$activity["multiselect_responsible"]["name"]}}</td>
                            <td>{{$activity["expiration_date"] ? date('Y-m-d', strtotime($activity["expiration_date"])) : '-' }}</td>
                            <td>{{$activity["execution_date"] ? date('Y-m-d', strtotime($activity["execution_date"])) : '-' }}</td>
                            <td>{{$activity["state"]}}</td>
                        </tr>
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
                    <th colspan="3">Archivos</th>
                </tr>
                <tr>
                    <th>Item</th>
                    <th>Nombre</th>
                    <th>Fecha de vencimiento</th>
                </tr>                
                @foreach($listCheck["items"] as $keyItem => $item)
                    @foreach($item["files"] as $file)
                        <tr>
                            <td>{{ $keyItem + 1 }}</td>
                            <td>{{$file["name"]}}</td>
                            <td>{{$file["expirationDate"] ? date('Y-m-d', strtotime($file["expirationDate"])) : '-' }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </thead>
        </table>   
    </div>
</body>
</html>