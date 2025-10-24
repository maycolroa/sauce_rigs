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
    <div style="text-align: center"><img src="{{ $logo }}" width="140px" height="120px"/></div>
    @endif
    <br>
    <center>FORMATO DE SEGUIMIENTO</center>
    <center>PROGRAMA DE REINTEGRO LABORAL</center>
    <br><br>

    <b>Nombre del colaborador: </b>{{$check->name}}<br>
    <b>Identificación: </b>{{$check->identification}}<br>
    <b>Fecha de seguimiento: </b>{{$check->monitoring_recommendations}}<br>
    <b>Cargo: </b>{{$check->position}}<br>
    <b>Área o unidad: </b>{{$check->headquarter}}<br>
    <b>Diagnóstico CIE10: </b>{{$check->diagnosis}}<br>
    @if($check->diagnosisCie11)
    <b>Diagnóstico CIE11: </b>{{$check->diagnosisCie11}}<br>
    @endif
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
    <b>Observaciones y plan de acción si aplica: </b><br>
    {!!$tracing_description!!}<br><br>

    <table style="width: 100%">
        <tr>
            <td style="text-align: letf">
                <b>¿Requiere nuevo seguimiento?: </b> {{$has_tracing}}
            </td>
            <td style="text-align: letf">
                    <b>Fecha propuesta: </b> @if($has_tracing == 'SI') {{$new_date_tracing ? $new_date_tracing : '-'}}@else - @endif <br>
            </td>
        </tr>
    </table>

    <br/><br/>
    <table style="width: 100%">
        <tr>
            <td style="text-align: letf">
                    <center>Firma Colaborador:</center>
                    <br>
                    <center>_______________________</center>
            </td>
            <td style="text-align: right">
                    <center>Firma Jefe Dpto./sección/unidad</center>
                    <br>
                    <center>__________________________________</center>
            </td>
        </tr>
        <tr style="padding-top: 15px;">
            <td style="padding-top: 15px; text-align: letf;">
                    <center>Firma Salud Ocupacional:</center>
                    <br>
                    <center>_______________________</center>
            </td>
            <td style="padding-top: 15px; text-align: right;">
                    <center>Otro:</center>
                    <br>
                    <center>__________________________________</center>
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