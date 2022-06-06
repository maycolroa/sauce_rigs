<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        body: {
            padding-bottom: 100px;
            padding-right: 100px;
            padding-left : 100px;
            padding-top: 0px;
        }
    </style>
</head>
<body style="margin: 50px; margin-top: 0px;">
    <!-- Define header and footer blocks before your content -->
    @if ($logo)
    <div style="text-align: right"><img src="{{ public_path('storage/administrative/logos/').$logo }}" width="120px" height="120px"/></div>
    @endif
    <p>{{$date}}</p>
    <br/>
    <center><b>COMUNICACIÓN INTERNA</b></center>
    <br/><br/>
    <b>Para: {{ $to }}</b>
    <br/><br/>
    <b>De: {{$from}}</b>
    <br/><br/>
    <b>Asunto: {{$subject}}</b>
    <br/><br/>
    <p>Una vez analizado el estado de salud, de <b>{{$check->name}}</b>, identificado(a) con <b>c.c. {{$check->identification}}</b>, cargo: <b>{{$check->position}}</b>, asignada a {{$check->regional}}, con fecha de ingreso {{$income_date}}, quien ha tenido un evento de {{$check->disease_origin}}, nos permitimos comentarle que:</p>
    <p>De acuerdo a lo establecido los artículos 7 y 8 de la Ley 776/2002, nos permitimos dar algunas sugerencias con el fin de contribuir en la recuperación del estado de salud y lograr el mejor desempeño laboral posible de <b>{{$check->name}}:</b></p>

    @if ($check->check_detail)
        <div style="border: solid black 1px; padding: 0px 20px; text-align: justify; text-justify: inter-word; padding-top: 5px; padding-bottom: 5px;">
            {!! nl2br($check->check_detail) !!}
        </div>
    @endif

    <p>El cargo y funciones asignadas y/o reasignadas al trabajador son: </p>

    @if ($check->position_functions_assigned_reassigned)
        <div style="border: solid black 1px; padding: 0px 20px; text-align: justify; text-justify: inter-word; padding-top: 5px; padding-bottom: 5px;">
            {!! nl2br($check->position_functions_assigned_reassigned) !!}
        </div>
    @endif
    
    @if ($check->start_recommendations)
        
        @if ($check->indefinite_recommendations != "NO")
            <p>Las anteriores recomendaciones han sido emitidas por {{$check->origin_recommendations}} y son <b>indefinidas</b>.</p>
        @else
            <p>Las anteriores recomendaciones han sido emitidas por {{$check->origin_recommendations}} y son de <b>carácter temporal</b> por {{$check->time_different}} días a partir del {{$check->start_recommendations}} hasta el {{$check->end_recommendations}} fecha de reintegro.</p>
        @endif
    @else
        <p>Observaciones:</p>
        
        <div style="border: solid black 1px; padding: 0px 20px; text-align: justify; text-justify: inter-word; padding-top: 5px; padding-bottom: 5px;">
            {!! nl2br($check->Observations_recommendatios) !!}
        </div>
    @endif

    @if ($check->start_recommendations)
        
        @if ($check->indefinite_recommendations != "NO")
            <p>Las anteriores recomendaciones han sido emitidas por {{$check->origin_recommendations}} y son <b>indefinidas</b>.</p>
        @else
            <p>Las anteriores recomendaciones han sido emitidas por {{$check->origin_recommendations}} y son de <b>carácter temporal</b> por {{$check->time_different}} días a partir del {{$check->start_recommendations}} hasta el {{$check->end_recommendations}} fecha de reintegro.</p>
        @endif
        
    @endif

    <p>Le indicamos que usted _______________________________, como trabajador(a) de la empresa  ___________________,  tiene  el  deber  legal  de  cumplir  a  cabalidad  con  las  medidas recomendadas y es por ello que también notificamos a su jefe inmediato, quien velará por el cumplimiento de las mismas y será garante de que ninguna tarea de su trabajo esté en contra de estas indicaciones médicas. A la firma de esta acta, se da por entendido igualmente que usted comprende y acepta las funciones  asignadas/reasignadas  y  se  compromete  a  dar  cumplimiento  estricto  a  las mismas  bajo  las  recomendaciones/restricciones  generadas  para  su  caso,  las  cuales también debe aplicar en el desarrollo de actividades extralaborales.</p>

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
                <td style="border: 0px solid #dddddd; text-align: left; padding: 0px">Responsable SST</td>
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
        </table>
    </div>
</body>
</html>