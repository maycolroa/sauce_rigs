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
    <div style="text-align: right"><img src="{{ $logo }}" width="120px" height="120px"/></div>
    @endif
    <p><b>Fecha: {{$date}}</b></p>
    <p><b>Hora: {{date('H:i:s')}}</b></p>
    <br/>
    <p><b>Comunicación interna</b></p>
    <p><b>Programa de reintegro laboral post incapacidad</b></p>
    <br/>
    <b>Para: {{ $to }}</b>
    <br/><br/>
    <b>De: {{$from}}</b>
    <br/><br/>
    <b>Asunto: {{$subject}}</b>
    <br/><br/>
    <p>Una vez analizada la información suministrada a Seguridad y Salud en el Trabajo por parte de <b>{{$check->name}}</b>, identificado(a) con DNI <b>{{$check->identification}}</b> y en el cargo de <b>{{$check->position}}</b> de la base <b>{{$check->headquarter}}</b>, con fecha de ingreso {{$income_date}}, quien ha tenido un evento de {{$check->disease_origin}}, nos permitimos informarle que de acuerdo a lo establecido en los artículos 7 y 8 de la Ley 776/2002 para Colombia y RM 312-2011-MINSA para Peru; a continuación se realizan algunas sugerencias con el fin de contribuir en la recuperación del estado de salud y lograr el mejor desempeño laboral posible.</p>

    <br/>
    @if ($check->check_detail)
        <b>Recomendaciones:</b><br><br>
        <div style="border: solid black 1px; padding: 0px 20px; text-align: justify; text-justify: inter-word; padding-top: 5px; padding-bottom: 5px;">
            {!! nl2br($check->check_detail) !!}
        </div>
    @endif
    
    @if ($check->start_recommendations)
        
        @if ($check->indefinite_recommendations != "NO")
            <p>Las anteriores recomendaciones han sido emitidas por {{$check->origin_recommendations}} y tienen un carácter <b>indefinido</b>, y para ello se realizará el respectivo acompañamiento y seguimiento desde Seguridad y Salud en el Trabajo.</p>
        @else
            <p>Las anteriores recomendaciones han sido emitidas por {{$check->origin_recommendations}} y tienen un carácter <b>temporal</b>, y para ello se realizará el respectivo acompañamiento y seguimiento desde Seguridad y Salud en el Trabajo.</p>
        @endif
        
    @endif
    <br/><br/><br/><br/>
    <table style="width: 100%">
        <tr>
            <td>FIRMA COLABORADOR</td>
            <td colpan="2"></td>
            <td>FIRMA JEFE INMEDIATO</td>
            <td colpan="2"></td>
            <td>FIRMA SST</td>
            <td colpan="2"></td>
        </tr>
        <tr>
            <td>DNI</td>
            <td colpan="2"></td>
            <td>DNI</td>
            <td colpan="2"></td>
            <td>DNI</td>
            <td colpan="2"></td>
        </tr>
        <tr>
            <td>CARGO</td>
            <td colpan="2"></td>
            <td>CARGO</td>
            <td colpan="2"></td>
            <td>CARGO</td>
            <td colpan="2"></td>
        </tr>
        @if($firm_user)
            <tr style="padding-top: 15px;">
                <td colspan="6" style="padding-top: 15px;">
                    <center>
                        <img src="{{$firm_user}}" width="150px" height="75px"/>
                    </center>
                    <p style="text-align: center; font-size: 12px;"><b>{{ $user->name }}</b><p>
                </td>
            </tr>
        @endif
    </table>

</body>
</html>