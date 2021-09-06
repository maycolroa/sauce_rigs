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
          text-align: center;
          padding: 8px;
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
        @if ($report["logo"])
        <div style="page-break-inside: avoid; text-align: right; padding-bottom: 10px;"><img src="{{ public_path('storage/administrative/logos/').$report['logo'] }}" width="120px" height="120px"/></div>
        @endif
        @if ($report["filtros"])
        <div>
            @if ($report["filtros"]['regionals'])
            <p><b>{{ substr (Auth::user()->getKeywords()['regionals'], 3 ) }}:</b> 
                @foreach ($report["filtros"]['regionals'] as $keyR => $reg) {{$reg['name']}} 
                @if (COUNT($report["filtros"]['regionals']) == ($keyR + 1))
                @else , @endif 
                @endforeach</p>
            @endif

            @if ($report["filtros"]['headquarters'])
                <p><b>{{ substr (Auth::user()->getKeywords()['headquarters'], 3 ) }} :</b>  @foreach ($report["filtros"]['headquarters'] as $keyH => $head) {{$head['name']}} @if (COUNT($report["filtros"]['headquarters']) == ($keyH + 1))
                @else , @endif  @endforeach</p>
            @endif

            @if ($report["filtros"]["processes"])
                <p><b>{{ substr (Auth::user()->getKeywords()['processes'], 3 ) }} :</b>  @foreach ($report["filtros"]['processes'] as $keyP => $pro) {{$pro['name']}} @if (COUNT($report["filtros"]['processes']) == ($keyP + 1))
                    @else , @endif  @endforeach</p>
            @endif

            @if ($report["filtros"]["macroprocesses"])
                <p><b>Macroprocesos :</b>  @foreach ($report["filtros"]['macroprocesses'] as $keyM => $ma) {{$ma['name']}} @if (COUNT($report["filtros"]['macroprocesses']) == ($keyM + 1))
                    @else , @endif  @endforeach</p>
            @endif

            @if ($report["filtros"]["areas"])
                <p><b>{{ substr (Auth::user()->getKeywords()['areas'], 3 ) }} :</b>  @foreach ($report["filtros"]['areas'] as $keyA => $area) {{$area['name']}} @if (COUNT($report["filtros"]['areas']) == ($keyA + 1))
                    @else , @endif  @endforeach</p>
            @endif

            @if ($report["filtros"]["risks"])
                <p><b>Riesgos :</b>  @foreach ($report["filtros"]['risks'] as $keyRR => $risk) {{$risk['name']}} @if (COUNT($report["filtros"]['risks']) == ($keyRR + 1))
                    @else , @endif  @endforeach</p>
            @endif
        </div>
        @endif
        <div>
            <p style="text-align: center"><b>Mapa Riesgos Inherentes</b></p>
            <p style="text-align: center"><b>Eje Y: Impacto / Eje X: Frecuencia</b></p>
        </div>
        <div>
            <table class="table-general">
                <thead>
                    <tr>
                        <th> </th>
                        @foreach($report["inherent_report"]["headers"] as $keyH => $header)
                        <th>
                            {{$keyH + 1}}.{{ $header }}
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($report["inherent_report"]["data"] as $keyD => $data)
                        <tr>
                            <th>{{COUNT($data) - $keyD}}.{{ $data[0]['col'] }}</th>
                            @foreach($data as $keyR => $col)
                                @if($col['color'] == 'warning')
                                    <td style="background-color: #FFD950">
                                        {{ $col['count'] }}
                                    </td>
                                @endif
                                @if($col['color'] == 'orange')
                                    <td style="background-color: #ef7b38">
                                        {{ $col['count'] }}
                                    </td>
                                @endif
                                @if($col['color'] == 'success')
                                    <td style="background-color: #02BC77">
                                        {{ $col['count'] }}
                                    </td>
                                @endif
                                @if($col['color'] == 'primary')
                                    <td style="background-color: #f0635f">
                                        {{ $col['count'] }}
                                    </td>
                                @endif
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <br><br>

    <div style="page-break-inside: avoid;">
        <div>
            <p style="text-align: center"><b>Mapa Riesgos Resudiales</b></p>
            <p style="text-align: center"><b>Eje Y: Impacto / Eje X: Frecuencia</b></p>
        </div>
        <div>
            <table class="table-general">
                <thead>
                    <tr>
                        <th> </th>
                        @foreach($report["residual_report"]["headers"] as $keyH => $header)
                        <th>
                            {{$keyH + 1}}.{{ $header }}
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($report["residual_report"]["data"] as $keyD => $data)
                        <tr>
                            <th>{{COUNT($data) - $keyD}}.{{ $data[0]['col'] }}</th>
                            @foreach($data as $keyR => $col)
                                @if($col['color'] == 'warning')
                                    <td style="background-color: #FFD950">
                                        {{ $col['count'] }}
                                    </td>
                                @endif
                                @if($col['color'] == 'orange')
                                    <td style="background-color: #ef7b38">
                                        {{ $col['count'] }}
                                    </td>
                                @endif
                                @if($col['color'] == 'success')
                                    <td style="background-color: #02BC77">
                                        {{ $col['count'] }}
                                    </td>
                                @endif
                                @if($col['color'] == 'primary')
                                    <td style="background-color: #f0635f">
                                        {{ $col['count'] }}
                                    </td>
                                @endif
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <br><br>
    
    <div style="page-break-inside: avoid;">
        <div>
            <p style="text-align: center"><b>Listado de Riesgos con Frecuencia e Impacto Residual</b></p>
        </div>
        <table class="table-general">
            <thead>
                <tr>
                    <th>{{ Auth::user()->getKeywords()['process'] }}</th>
                    <th>{{ Auth::user()->getKeywords()['area'] }}</th>
                    <th>Riesgo</th>
                    <th>Evento de Riesgo</th> 
                </tr>
            </thead>
            <tbody>
                @foreach($report["table_report_residual"]['data'] as $keyD => $data)
                    <tr>
                        <td>{{$data['process']}}</td>
                        <td>{{$data['area']}}</td>

                        @if($data['risk']['color'] == 'warning')
                            <td style="background-color: #FFD950">
                                {{$data['risk']['sequence']}}
                            </td>
                        @endif
                        @if($data['risk']['color'] == 'orange')
                            <td style="background-color: #ef7b38">
                                {{$data['risk']['sequence']}}
                            </td>
                        @endif
                        @if($data['risk']['color'] == 'success')
                            <td style="background-color: #02BC77">
                                {{$data['risk']['sequence']}}
                            </td>
                        @endif
                        @if($data['risk']['color'] == 'primary')
                            <td style="background-color: #f0635f">
                                {{$data['risk']['sequence']}}
                            </td>
                        @endif

                        <td>{{$data['risk_name']}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div> 
</body>
</html>