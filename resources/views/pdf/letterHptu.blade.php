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
    <p>Una vez analizado el estado de salud, de <b>{{$check->name}}</b>, identificado(a) con <b>c.c. {{$check->identification}}</b>, cargo: <b>{{$check->position}}</b>, asignada a {{$check->regional}}, con fecha de ingreso {{$income_date}}, quien ha tenido un evento de {{$check->disease_origin}} nos permitimos comentarle que:</p>
    <p>De acuerdo a lo establecido los artículos 7 y 8 de la Ley 776/2002, nos permitimos dar algunas sugerencias con el fin de contribuir en la recuperación del estado de salud y lograr el mejor desempeño laboral posible de <b>{{$check->name}}:</b></p>

    <br/>
    @if ($check->check_detail)
        <!--<div style="border:solid black 1px;padding:0px 20px;">
            <pre style="font-family: sans-serif; text-align: justify; text-justify: inter-word;">{!! nl2br($check->check_detail) !!}</pre>
        </div>-->
        <div style="border: solid black 1px; padding: 0px 20px; text-align: justify; text-justify: inter-word; padding-top: 5px; padding-bottom: 5px;">
            {!! nl2br($check->check_detail) !!}
        </div>
    @endif
    
    @if ($check->start_recommendations)
        
        @if ($check->indefinite_recommendations != "NO")
            <p>Las anteriores recomendaciones han sido emitidas por {{$check->origin_recommendations}} y son <b>indefinidas</b>.</p>
        @else
            <p>Las anteriores recomendaciones han sido emitidas por {{$check->origin_recommendations}} y son de <b>carácter temporal</b> por {{$check->time_different}} días a partir del {{$check->start_recommendations}} hasta el {{$check->end_recommendations}} fecha de reintegro.</p>
        @endif
        
    @endif
    <br/><br/><br/><br/><br/><br/><br/>
    @if($firm_user)
    <center>
        <img src="{{$firm_user}}" width="150px" height="75px"/>
    </center>
    <p style="text-align: center; font-size: 12px;"><b>{{ $user->name }}</b><p>
    @else
    <p><b>{{$user->name}}</b>
    @endif
    <br>
    <b>{{$user->medical_record ? "Registro Médico: " . $user->medical_record : ''}}</b>
    <br>
    <b>{{$user->sst_license ? "Licencia SST: " . $user->sst_license : ''}}</b></p>
    <p></p>

</body>
</html>