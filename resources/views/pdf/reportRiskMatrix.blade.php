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
        {{--<div>
            <table>
                <thead>
                    <tr>
                        <th><b>{{ Auth::user()->getKeywords()['regionals'] }}</b></th>
                        @if ($report["filtros"]['headquarters'])
                        <th><b>{{ Auth::user()->getKeywords()['headquarters'] }}</b></th>
                        @endif
                        @if ($report["filtros"]['processes'])
                        <th><b>{{ Auth::user()->getKeywords()['processes'] }}</b></th>
                        @endif
                        @if ($report["filtros"]['macroprocesses'])
                        <th><b>Macroprocesos</b></th>
                        @endif
                        @if ($report["filtros"]['areas'])
                        <th><b>{{ Auth::user()->getKeywords()['areas'] }}</b></th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>@foreach ($report["filtros"]['regionals'] as $reg) {{$reg['name']}}, @endforeach</td>
                        @if ($report["filtros"]['headquarters'])
                        <td> @foreach ($report["filtros"]['headquarters'] as $head) {{$head['name']}}, @endforeach</td>
                        @endif
                        @if ($report["filtros"]['processes'])
                        <td>@foreach ($report["filtros"]['processes'] as $pro) {{$pro['name']}}, @endforeach</td>
                        @endif
                        @if ($report["filtros"]['macroprocesses'])
                        <td>@foreach ($report["filtros"]['macroprocesses'] as $ma) {{$ma['name']}}, @endforeach</td>
                        @endif
                        @if ($report["filtros"]['areas'])
                        <td>@foreach ($report["filtros"]['areas'] as $area) {{$area['name']}}, @endforeach</td>
                        @endif
                    </tr>
                </tbody>
            </table>
        </div>--}}
        <div>
            @if ($report["filtros"]['regionals'])
            <p style="text-align: center"><b>Filtros:</b></p>
            <p><b>{{ Auth::user()->getKeywords()['regionals'] }} :</b>  
                @foreach ($report["filtros"]['regionals'] as $keyR => $reg) {{$reg['name']}} 
                @if (COUNT($report["filtros"]['regionals']) == ($keyR + 1))
                @else , @endif 
                @endforeach</p>
            @endif

            @if ($report["filtros"]['headquarters'])
            <p><b>{{ Auth::user()->getKeywords()['headquarters'] }} :</b>  @foreach ($report["filtros"]['headquarters'] as $keyH => $head) {{$head['name']}} @if (COUNT($report["filtros"]['headquarters']) == ($keyH + 1))
                @else , @endif  @endforeach</p>
            @endif

            @if ($report["filtros"]["processes"])
                <p><b>{{ Auth::user()->getKeywords()['processes'] }} :</b>  @foreach ($report["filtros"]['processes'] as $keyP => $pro) {{$pro['name']}} @if (COUNT($report["filtros"]['processes']) == ($keyP + 1))
                    @else , @endif  @endforeach</p>
            @endif

            @if ($report["filtros"]["macroprocesses"])
                <p><b>Macroprocesos :</b>  @foreach ($report["filtros"]['macroprocesses'] as $keyM => $ma) {{$ma['name']}} @if (COUNT($report["filtros"]['macroprocesses']) == ($keyM + 1))
                    @else , @endif  @endforeach</p>
            @endif

            @if ($report["filtros"]["areas"])
                <p><b>{{ Auth::user()->getKeywords()['areas'] }} :</b>  @foreach ($report["filtros"]['areas'] as $keyA => $area) {{$area['name']}} @if (COUNT($report["filtros"]['areas']) == ($keyA + 1))
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

    @if(COUNT($report["inherent_report_table"]) > 0)
        <div style="page-break-inside: avoid;">
            <div>
                <p style="text-align: center"><b>Riesgos {{$report["inherent_report_table"][0]['frequency']}} - {{$report["inherent_report_table"][0]['impact']}}</b></p>
            </div>
            <table class="table-general">
                <thead>
                    <tr>
                        <th>Riesgo</th>
                        <th>Categoría</th> 
                        <th>{{ Auth::user()->getKeywords()['regional'] }}</th>
                        <th>{{ Auth::user()->getKeywords()['headquarter'] }}</th>
                        <th>{{ Auth::user()->getKeywords()['process'] }}</th>
                        <th>{{ Auth::user()->getKeywords()['area'] }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report["inherent_report_table"] as $keyD => $data)
                        <tr>
                            <td>{{$data['name']}}</td>
                            <td>{{$data['category']}}</td>
                            <td>{{$data['regional']}}</td>
                            <td>{{$data['headquarter']}}</td>
                            <td>{{$data['process']}}</td>
                            <td>{{$data['area']}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>        
    @endif

    <br><br>

    <div style="page-break-inside: avoid;">
        {{--@if ($inspections["logo"])
        <div style="page-break-inside: avoid; text-align: right; padding-bottom: 10px;"><img src="{{ public_path('storage/administrative/logos/').$inspections['logo'] }}" width="120px" height="120px"/></div>
        @endif--}}
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
    
    @if(COUNT($report["residual_report_table"]) > 0)
        <div style="page-break-inside: avoid;">
            <div>
                <p style="text-align: center"><b>Riesgos {{$report["residual_report_table"][0]['frequency']}} - {{$report["residual_report_table"][0]['impact']}}</b></p>
            </div>
            <table class="table-general">
                <thead>
                    <tr>
                        <th>Riesgo</th>
                        <th>Categoría</th> 
                        <th>{{ Auth::user()->getKeywords()['regional'] }}</th>
                        <th>{{ Auth::user()->getKeywords()['headquarter'] }}</th>
                        <th>{{ Auth::user()->getKeywords()['process'] }}</th>
                        <th>{{ Auth::user()->getKeywords()['area'] }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report["residual_report_table"] as $keyD => $data)
                        <tr>
                            <td>{{$data['name']}}</td>
                            <td>{{$data['category']}}</td>
                            <td>{{$data['regional']}}</td>
                            <td>{{$data['headquarter']}}</td>
                            <td>{{$data['process']}}</td>
                            <td>{{$data['area']}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>        
    @endif

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