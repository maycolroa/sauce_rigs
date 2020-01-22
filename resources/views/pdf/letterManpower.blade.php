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
    <div style="text-align: left"><img src="{{ public_path('storage/administrative/logos/').$logo }}" width="120px" height="120px"/></div>
    @endif
    <br/>
    <center><b>COMUNICACIÓN INTERNA</b></center>
    <br/><br/>
    <b>Fecha: {{date('d/m/Y H:i:s')}}</b>
    <br/><br/>
    <b>Para: {{ $to }}</b>
    <br/>
    <b>De: {{$from}}</b>
    <br/>
    <b>Asunto: {{$subject}}</b>
    <br/><br/>
    <p style="text-align: justify;">En nuestra calidad de Médicos Laborales y como conocedores de los procesos productivos de la empresa, enmarcados en la Ley 776 de 2002 y en atención a las responsabilidades de los empleados incluidas en el Artículo 2.2.4.6.10. Del Decreto Único Reglamentario del Sector Trabajo 1072 de 2015, nos permitimos dar las siguientes recomendaciones con el fin de contribuir positivamente en el desempeño laboral de <b>{{$check->name}}:</b></p>

    <br/>
    @if ($check->check_detail)
        <div style="border: solid black 1px; padding: 0px 20px; text-align: justify; text-justify: inter-word; padding-top: 5px; padding-bottom: 5px;">
            {!! nl2br($check->check_detail) !!}
        </div>
    @endif
    <br/><br/>
    @if ($check->start_recommendations)
        
        @if ($check->indefinite_recommendations != "NO")
            <p>Las anteriores recomendaciones son <b>indefinidas</b>.</p>
        @else
            <p>Las anteriores recomendaciones son de <b>carácter temporal</b> por {{$check->time_different}} días a partir del {{$check->start_recommendations}} hasta el {{$check->end_recommendations}} fecha de reintegro.</p>
        @endif
        
    @endif
    <br/><br/>
    Atentamente,
    <br/><br/><br/>
	<div style="border: solid black 1px; width: 150px; height: 100px;">
    </div>
    <br/>

    <p><b>COMITÉ ASESORES MEDICINA LABORAL</b></p>
    <p><b>SG-SST MANPOWER DE COLOMBIA LTDA.</b></p>

</body>
</html>