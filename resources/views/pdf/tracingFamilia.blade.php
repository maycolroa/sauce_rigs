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
    <div style="text-align: right"><img src="{{ $logo }}" width="200px" height="140px"/></div>
    @endif
    <br>
    <center><b>FORMATO DE SEGUIMIENTO A</b></center>
    <center><b>RECOMENDACIONES Y/O RESTRICCIONES</b></center>
    <center><b>MÉDICO LABORALES</b></center>
    <br><br>

    <b>Nombre del colaborador: </b>{{$check->name}}<br>
    <b>Identificación: </b>{{$check->identification}}<br>
    <b>Fecha de seguimiento: </b>{{$check->monitoring_recommendations}}<br>
    <b>Cargo: </b>{{$check->position}}<br>
    <b>Área o unidad: </b>{{$check->headquarter}}<br>
    <b>Diagnóstico: </b>{{$check->diagnosis}}<br>
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
    <br><br>

    <div style="page-break-inside: avoid;">
        <table class="table-general" style="border: 1px solid black;">
            <tr style="width:100%;">
                <td style="width:80%; border-right: 1px solid black; border-bottom: 1px solid black;"><center><b>REPORTE SST</b></center></b></td>
                <td style="width:10%; border-right: 1px solid black; ; border-bottom: 1px solid black;"><center><b>SI</b></center></td>
                <td style="width:10%; border-bottom: 1px solid black;"><center><b>NO</b></center></td>
            </tr>
            <tr style="width:100%;">
                <td style="width:80%; border-right: 1px solid black; border-bottom: 1px solid black;">1. ¿El  colaborador  ha  reportado  molestias,  síntomas  o  dificultades  para desempeñar las tareas asignadas?</td>
                <td style="width:10%; border-right: 1px solid black; border-bottom: 1px solid black;"></td>
                <td style="width:10%; border-bottom: 1px solid black;"></td>
            </tr>
            <tr style="width:100%;">
                <td style="width:80%; border-right: 1px solid black; border-bottom: 1px solid black;">2. ¿El colaborador ha manifestado conformidad con el proceso de Reincorporación Laboral?</td>
                <td style="width:10%; border-right: 1px solid black; border-bottom: 1px solid black;"></td>
                <td style="width:10%; border-bottom: 1px solid black;"></td>
            </tr>
            <tr style="width:100%;">
                <td style="width:80%; border-right: 1px solid black;">3. ¿El colaborador ha presentado incapacidades médicas después del proceso de Reincorporación laboral en cualquiera de sus modalidades?</td>
                <td style="width:10%; border-right: 1px solid black;"></td>
                <td style="width:10%;"></td>
            </tr>
        </table>
    </div>
    <br>

    <div style="page-break-inside: avoid;">
        <table class="table-general" style="border: 1px solid black;">
            <tr style="width:100%;">
                <td style="width:80%; border-right: 1px solid black; border-bottom: 1px solid black;"><center><b>REPORTE TRABAJADOR</center></b></td>
                <td style="width:10%; border-right: 1px solid black; border-bottom: 1px solid black;"><center><b>SI</b></center></td>
                <td style="width:10%; border-bottom: 1px solid black;"><center><b>NO</b></center></td>
            </tr>
            <tr style="width:100%;">
                <td style="width:80%; border-right: 1px solid black; border-bottom: 1px solid black;">1. ¿Siente usted que las tareas asignadas complementan y favorecen su proceso de rehabilitación?</td>
                <td style="width:10%; border-right: 1px solid black; border-bottom: 1px solid black;"></td>
                <td style="width:10%; border-bottom: 1px solid black;"></td>
            </tr>
            <tr style="width:100%;">
                <td style="width:80%; border-right: 1px solid black; border-bottom: 1px solid black;">2. ¿Las funciones ejecutadas actualmente le permiten el cumplimiento total de las recomendaciones médicas?</td>
                <td style="width:10%; border-right: 1px solid black; border-bottom: 1px solid black;"></td>
                <td style="width:10%; border-bottom: 1px solid black;"></td>
            </tr>
            <tr style="width:100%;">
                <td style="width:80%; border-right: 1px solid black;">3. ¿Requiere de alguna ayuda o modificación para el buen desempeño de sus tareas habituales?</td>
                <td style="width:10%; border-right: 1px solid black;"></td>
                <td style="width:10%"></td>
            </tr>
        </table>
    </div>
    <br>

    <div style="page-break-inside: avoid;">
        <table class="table-general" style="border: 1px solid black;">
            <tr>
                <td style="width:80%; border-right: 1px solid black; border-bottom: 1px solid black;"><center><b>REPORTE JEFE INMEDIATO</center></b></td>
                <td style="width:10%; border-right: 1px solid black; border-bottom: 1px solid black;"><center><b>SI</b></center></td>
                <td style="width:10%; border-bottom: 1px solid black;"><center><b>NO</b></center></td>
            </tr>
            <tr>
                <td style="width:80%; border-right: 1px solid black; border-bottom: 1px solid black;">1. ¿Tuvo conocimiento de las recomendaciones laborales del trabajador  al momento de su reintegro?</td>
                <td style="width:10%; border-right: 1px solid black; border-bottom: 1px solid black;"></td>
                <td style="width:10%; border-bottom: 1px solid black;"></td>
            </tr>
            <tr>
                <td style="width:80%; border-right: 1px solid black; border-bottom: 1px solid black;">2. ¿Las tareas que le fueron asignadas al trabajador cumple con las recomendaciones dadas?</td>
                <td style="width:10%; border-right: 1px solid black; border-bottom: 1px solid black;"></td>
                <td style="width:10%; border-bottom: 1px solid black;"></td>
            </tr>
            <tr>
                <td style="width:80%; border-right: 1px solid black; border-bottom: 1px solid black;">3. ¿Al trabajador se le dificulta la realización de alguna de las tareas asignadas?</td>
                <td style="width:10%; border-right: 1px solid black; border-bottom: 1px solid black;"></td>
                <td style="width:10%; border-bottom: 1px solid black;"></td>
            </tr>
            <tr>
                <td style="width:80%; border-right: 1px solid black;">4. ¿El trabajador manifiesta síntomas durante el trascurso de la jornada laboral?</td>
                <td style="width:10%; border-right: 1px solid black;"></td>
                <td style="width:10%"></td>
            </tr>
        </table>
    </div>
    <br>

    <div style="page-break-inside: avoid;">
        <table class="table-general" style="border: 1px solid black; width: 100%">
            <tr style="width:100%">
                <td style="width:50%; border-right: 1px solid black;"><b>Horario de trabajo</b></td>
                <td style="width:50%;"></td>
            </tr>
        </table>
    </div>

    <br/><br/>
    <div>
        <table style="background-color: white; width: 100%">
            <tr style="background-color: white;">
                <td style="border: 0px solid #dddddd; text-align: left; padding: 0px">
                    <p>__________________________</p>
                </td>
                <td style="border: 0px solid #dddddd; text-align: letf; padding: 0px">
                    <p>__________________________</p>
                </td>
            </tr>
            <tr style="background-color: white;">
                <td style="border: 0px solid #dddddd; text-align: left; padding: 0px">Trabajador</td>
                <td style="border: 0px solid #dddddd; text-align: left; padding: 0px">Jefe Inmediato</td>
            </tr>
            <tr style="background-color: white;">
                <td style="border: 0px solid #dddddd; text-align: left; padding: 0px">CC:</td>
                <td style="border: 0px solid #dddddd; text-align: left; padding: 0px">CC:</td>
            </tr>
            <tr style="background-color: white;">
                <td style="border: 0px solid #dddddd; text-align: left; padding: 0px">Cargo</td>
                <td style="border: 0px solid #dddddd; text-align: left; padding: 0px">Cargo</td>
            </tr>
            <tr style="background-color: white;">
                <td style="border: 0px solid #dddddd; text-align: left; padding: 0px">
                    <p>__________________________</p>
                </td>
                <td style="border: 0px solid #dddddd; text-align: left; padding: 0px">
                    <p>__________________________</p>
                </td>
            </tr>
            <tr style="background-color: white;">
                <td style="border: 0px solid #dddddd; text-align: left; padding: 0px">Responsable RESH</td>
                <td style="border: 0px solid #dddddd; text-align: left; padding: 0px">Médico laboral de empresa</td>
            </tr>
            <tr style="background-color: white;">
                <td style="border: 0px solid #dddddd; text-align: left; padding: 0px">CC:</td>
                <td style="border: 0px solid #dddddd; text-align: left; padding: 0px">CC:</td>
            </tr>
            <tr style="background-color: white;">
                <td style="border: 0px solid #dddddd; text-align: left; padding: 0px">Cargo</td>
                <td style="border: 0px solid #dddddd; text-align: left; padding: 0px">Cargo</td>
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
    </div>
    <br>
    <div id="abajo" align="center" style="text-align: right;">
        <font color="#000000" size="2" face="Verdana">Programa de reincorporaciones laborales</font>
    </div>

</body>
</html>