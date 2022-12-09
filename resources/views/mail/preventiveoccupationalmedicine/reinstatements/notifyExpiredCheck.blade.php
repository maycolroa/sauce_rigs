@component('mail::message', ['module' => $mail->module])

@if(isset($mail->with['user']))
<h2>Hola {{ $mail->with['user'] }}</h2>
@else
<h2>Hola</h2>
@endif

@if(isset($mail->message))
<p>{!! nl2br($mail->message) !!}</p>
@endif

@if(isset($mail->with['data']))
<div>
    <center>
        <table class="table-general" style="width: 100%">
            <thead>
                <tr>
                    <th style="width: 15%">Empleado</th>
                    <th style="width: 15%">Tipo de evento</th>
                    <th style="width: 10%">Código CIE</th>
                    <th style="width: 20%">Descripción CIE</th>
                    <th style="width: 15%">Fecha</th>
                    <th style="width: 15%">Regional</th>
                    <th style="width: 10%">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mail->with['data'] as $index => $item)
                <tr>
                    <td style="text-align: center; width: 15%">{{$item['Empleado']}}</td>
                    <td style="text-align: center; width: 15">{{$item['Tipo de Evento']}}</td>
                    <td style="text-align: center; width: 10%">{{$item['Codigo CIE']}}</td>
                    <td style="text-align: center; width: 20%">{{$item['Descripción CIE']}}</td>
                    <td style="text-align: center; width: 15%">{{$item['Fecha']}}</td>
                    <td style="text-align: center; width: 15%">{{$item['Regional']}}</td>
                    <td style="text-align: center; width: 10%">{{$item['Estado']}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </center>
</div>
<br>
@endif

@if(isset($mail->buttons))
@component('mail::button', ['url' => $mail->buttons[0]['url'], 'color' => isset($mail->buttons[0]['color']) ? $mail->buttons[0]['color'] : 'red'])
    {{ $mail->buttons[0]['text'] }}
@endcomponent
@endif

@if(isset($mail->subcopy))
@component('mail::subcopy')
    {{ $mail->subcopy }}
@endcomponent
@endif

@endcomponent