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
    <p><b>{{$date}}</b></p>
    <br/>
    <p style="text-align: center"><b>COMUNICACIÓN INTERNA</b></p>
    <br/>
    <b>Para: {{ $to }}</b>
    <br/><br/>
    <b>De: {{$from}}</b>
    <br/><br/>
    <b>Asunto: {{$subject}}</b>
    <br/><br/>
    <p style="text-align: justify;">Una vez analizado el estado de salud, de <b>{{$check->name}}</b>, identificado(a) con C.C. <b>{{$check->identification}}</b> cargo: <b>{{$check->position}}</b>, asignado a <b>{{$check->headquarter}}</b>, con fecha de ingreso {{$income_date}}, quien ha tenido un evento de {{$check->disease_origin}}, nos permitimos informarle que:</p>    
    <p style="text-align: justify;">De acuerdo a lo establecido en los artículos 7 y 8 de la Ley 776/2002, nos permitimos dar algunas sugerencias con el fin de favorecer el proceso de recuperación del estado de salud y lograr el mejor desempeño laboral posible de <b>{{$check->name}}</b>:</p>

    <br/>
    @if ($check->check_detail)
        <div style="border: solid black 1px; padding: 0px 20px; text-align: justify; text-justify: inter-word; padding-top: 5px; padding-bottom: 5px;">
            {!! nl2br($check->check_detail) !!}
        </div>
    @endif
    
    @if ($check->start_recommendations)
        
        @if ($check->indefinite_recommendations != "NO")
            <p style="text-align: justify;">Las anteriores recomendaciones han sido emitidas por {{$check->origin_recommendations}} y tienen un carácter <b>indefinido</b>, y para ello se realizará el respectivo acompañamiento y seguimiento desde Seguridad y Salud en el Trabajo.</p>
        @else
            <p style="text-align: justify;">Las anteriores recomendaciones han sido emitidas por {{$check->origin_recommendations}} y son de <b>carácter temporal</b> a partir de {{$check->start_recommendations}} hasta el {{$check->end_recommendations}}.</p>
        @endif
        
    @endif
    <br/><br/>
    <div>
        <table style="width: 100%">
            <tr>
                <td style="text-align: letf">
                    <center>__________________________________</center>
                    <center>Firma Médico:</center>                    
                </td>
                <td style="text-align: right;">
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
            </tr>
            <tr style="padding-top: 15px;">
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
            @if($firm_user)
            <tr style="padding-top: 15px;">
                <td colspan="2" style="padding-top: 15px;">
                    <center>
                        <img src="{{$firm_user}}" width="150px" height="75px"/>
                    </center>
                    <p style="text-align: center; font-size: 12px;"><b>{{ $user->name }}</b><p>
                </td>
            </tr>
            @endif
        </table>
    </div>
</body>
</html>