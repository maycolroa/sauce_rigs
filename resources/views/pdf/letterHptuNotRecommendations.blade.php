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
    <div style="text-align: right"><img src="{{ $logo }}" width="200px" height="120px"/></div>
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
    <p>De acuerdo con el seguimiento del dia <b>{{ $date }}</b> realizado a <b>{{$check->name}}</b>, identificado(a) con <b>c.c. {{$check->identification}}</b>, cargo: <b>{{$check->position}}</b>, asignada a {{$check->regional}}, con fecha de ingreso {{$income_date}}, quien ha tenido un evento de {{$check->disease_origin}}, le notificamos que las recomendaciones y/o restricciones han finalizado.</p>

    <br/>
    <p>Quedamos atentos a alguna inquietud o necesidad adicional.</p>
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