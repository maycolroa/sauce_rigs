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
    <p>De acuerdo con el seguimiento del dia <b>{{ $mail->with['data']['date'] }}</b> realizado a <b>{{$mail->with['data']['check']['name']}}</b>, identificado(a) con <b>c.c. {{$mail->with['data']['check']['identification']}}</b>, cargo: <b>{{$mail->with['data']['check']['position']}}</b>, asignada a {{$mail->with['data']['check']['regional']}}, con fecha de ingreso {{$mail->with['data']['income_date']}}, quien ha tenido un evento de {{$mail->with['data']['check']['disease_origin']}}, le notificamos que las recomendaciones y/o restricciones han finalizado.</p>

    <br/>
    <p>Quedamos atentos a alguna inquietud o necesidad adicional.</p>
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