@component('mail::message', ['module' => $mail->module])

@if(isset($mail->with['data']))
<div>
    <p>{{$mail->with['data']['date']}}</p>
    <br/><br/>
    Para: {{ $mail->with['data']['to'] }}
    <br/><br/>
    De: {{$mail->with['data']['from']}}
    <br/><br/>
    Asunto: {{$mail->with['data']['subject']}}
    <br/><br/>
    <p>Una vez analizado el estado de salud, de <b>{{$mail->with['data']['check']['name']}}</b>, identificado(a) con <b>c.c. {{$mail->with['data']['check']['identification']}}</b>, cargo: <b>{{$mail->with['data']['check']['position']}}</b>, asignada a {{$mail->with['data']['check']['regional']}}, con fecha de ingreso {{$mail->with['data']['income_date']}}, quien ha tenido un evento de {{$mail->with['data']['check']['disease_origin']}} nos permitimos comentarle que:</p>
    <p>De acuerdo a lo establecido los artículos 7 y 8 de la Ley 776/2002, nos permitimos dar algunas sugerencias con el fin de contribuir en la recuperación del estado de salud y lograr el mejor desempeño laboral posible de <b>{{$mail->with['data']['check']['name']}}:</b></p>

    <br/>
    @if ($mail->with['data']['check']['check_detail'])
        <div style="border: solid black 1px; padding: 0px 20px; text-align: justify; text-justify: inter-word; padding-top: 5px; padding-bottom: 5px;">
            {!! nl2br($mail->with['data']['check']['check_detail']) !!}
        </div>
    <br/><br/>
    @endif
    
    @if ($mail->with['data']['check']['start_recommendations'])
        
        @if ($mail->with['data']['check']['indefinite_recommendations'] != "NO")
            <p>Las anteriores recomendaciones han sido emitidas por {{$mail->with['data']['check']['origin_recommendations']}} y son <b>indefinidas</b>.</p>
        @else
            <p>Las anteriores recomendaciones han sido emitidas por {{$mail->with['data']['check']['origin_recommendations']}} y son de <b>carácter temporal</b> por {{$mail->with['data']['check']['time_different']}} días a partir del {{$mail->with['data']['check']['start_recommendations']}} hasta el {{$mail->with['data']['check']['end_recommendations']}} fecha de reintegro.</p>
        @endif
        
    @endif
    <br/><br/><br/>
    <p><b>{{$mail->with['data']['user']['name']}}</b>
    <br>
    <b>{{$mail->with['data']['user']['medical_record'] ? "Registro Médico: " . $mail->with['data']['user']['medical_record'] : ''}}</b>
    <br>
    <b>{{$mail->with['data']['user']['sst_license'] ? "Licencia SST: " . $mail->with['data']['user']['sst_license'] : ''}}</b></p>
    <p></p>
</div>
<br>
@endif

@endcomponent