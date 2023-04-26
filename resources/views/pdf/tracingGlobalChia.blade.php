<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        body: {
            padding: 100px;
        },
        div#abajo {
            position: fixed;
            bottom: 0;
            width:100%;
        }
    </style>
</head>
<body style="margin: 50px; margin-top: 0px;">
    <!-- Define header and footer blocks before your content -->
    @if ($logo)
    <div style="text-align: center"><img src="{{ public_path('storage/administrative/logos/').$logo }}" width="140px" height="120px"/></div>
    @endif
    <br>
    <center><b>FORMATO DE SEGUIMIENTO</b></center>
    <center><b>LABORAL</b></center>
    <br><br>

    <b>Nombre del colaborador: </b>{{$check->name}}<br>
    <b>Identificación: </b>{{$check->identification}}<br>
    {{--<b>Fecha de seguimiento: </b>{{$check->monitoring_recommendations}}<br>--}}
    <b>Cargo: </b>{{$check->position}}<br>
    <b>Área o unidad: </b>{{$check->headquarter}}<br>
    <b>Contingencia: </b>{{$check->disease_origin}}<br>

    @if ($check->start_recommendations)
        
        @if ($check->indefinite_recommendations != "NO")
            <b>Procedencia de las recomendaciones: </b> {{$check->origin_recommendations}}<br>
            <b>Tipo de recomendaciones: </b>Definitivas<br><br>
            <table style="width: 100%">
                <tr>
                    <td style="text-align: center">
                        <b>Duración inicial (dias): </b>
                    </td>
                    <td style="text-align: center">
                        <b>Fecha de inicio: </b>
                    </td>
                    <td style="text-align: center">
                        <b>Fecha de finalización: </b>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center">
                        - 
                    </td>
                    <td style="text-align: center">
                        {{$check->start_recommendations}}
                    </td>
                    <td style="text-align: center">
                        - 
                    </td>
                </tr>
            </table>
        @else
            <b>Procedencia de las recomendaciones: </b> {{$check->origin_recommendations}}<br>
            <b>Tipo de recomendaciones: </b>Temporales<br><br>
            <table style="width: 100%">
                <tr>
                    <td style="text-align: center">
                        <b>Duración inicial (dias): </b>
                    </td>
                    <td style="text-align: center">
                        <b>Fecha de inicio: </b>
                    </td>
                    <td style="text-align: center">
                        <b>Fecha de finalización: </b>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center">
                        {{$check->time_different}} 
                    </td>
                    <td style="text-align: center">
                        {{$check->start_recommendations}}
                    </td>
                    <td style="text-align: center">
                        {{$check->end_recommendations}}
                    </td>
                </tr>
            </table>
        @endif
    @else
        <b>Procedencia de las recomendaciones: </b>No aplica<br>
        <b>Tipo de recomendaciones: </b>No aplica<br><br>
        
        <table style="width: 100%">
            <tr>
                <td style="text-align: center">
                    <b>Duración inicial (dias): </b>
                </td>
                <td style="text-align: center">
                    <b>Fecha de inicio: </b>
                </td>
                <td style="text-align: center">
                    <b>Fecha de finalización: </b>
                </td>
            </tr>
            <tr>
                <td style="text-align: center">
                    - 
                </td>
                <td style="text-align: center">
                    -
                </td>
                <td style="text-align: center">
                    - 
                </td>
            </tr>
        </table>
    @endif

    <br>

    <table style="width: 100%">
        <thead>
            <tr>
                <th>
                    <center><b>Seguimientos: </b></center>
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tracings as $key => $tracing)
                <tr>
                    <td>
                        {{$key+1}}. {!!$tracing->description!!}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <br/><br/>
    <table style="width: 100%">
        <tr>
            <td style="text-align: letf">
                <center>__________________________________</center>
                <center>Firma Médico:</center>                    
            </td>
            <td style="text-align: right">
                <center>__________________________________</center>
                 <center>Firma Colaborador</center>
            </td>
        </tr>
        <tr style="padding-top: 15px;">
            <td style="padding-top: 15px; text-align: letf;">
                <br><br>
                <center>__________________________________</center>
                <center>Firma Analista SST y/o Analista en salud:</center>
            </td>
            <td style="padding-top: 15px; text-align: right;">
                <br><br>
                <center>__________________________________</center>
                <center>Firma Director Gestión Humana:</center>
            </td>
        </tr><tr style="padding-top: 15px;">
            <td style="padding-top: 15px; text-align: letf;">
                <br><br>
                <center>__________________________________</center>
                <center>Firma Jefe de área:</center>
            </td>
            <td style="padding-top: 15px; text-align: right;">
                <br><br>
                <center>__________________________________</center>
                <center>Firma Supervisor:</center>
            </td>
        </tr>
        @if($firm)
        <tr style="padding-top: 15px;">
            <td colspan="2" style="padding-top: 15px;">
                <center>
                    <img src="{{$firm['firm']}}" width="150px" height="75px"/>
                </center>
                <p style="text-align: center; font-size: 12px;"><b>{{ $firm['name'] }}</b><p>
            </td>
        </tr>
        @endif
    </table>
    <br>
    <div id="abajo" align="center" style="text-align: right;">
        <font color="#000000" size="2" face="Verdana">Programa de reincorporaciones laborales</font>
    </div>

</body>
</html>