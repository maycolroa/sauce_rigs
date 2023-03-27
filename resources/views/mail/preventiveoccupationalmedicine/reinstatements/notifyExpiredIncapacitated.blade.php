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
        <table class="table-general" style="width: 80%">
            <thead>
                <tr>
                    <th style="width: 15%">Identificacion</th>
                    <th style="width: 15%">Nombre</th>
                    <th style="width: 10%">Codigo Diagnostico</th>
                    <th style="width: 25%">Nombre Diagnostico</th>
                    <th style="width: 15%">Fecha Inicial</th>
                    <th style="width: 5%">Dias</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mail->with['data'] as $index => $item)
                <tr>
                    <td style="text-align: center; width: 15%">{{$item['Identificacion']}}</td>
                    <td style="text-align: center; width: 15%">{{$item['Nombre']}}</td>
                    <td style="text-align: center; width: 10%">{{$item['Codigo Diagnostico']}}</td>
                    <td style="text-align: center; width: 25%">{{$item['Nombre Diagnostico']}}</td>
                    <td style="text-align: center; width: 15%">{{$item['Fecha Inicial']}}</td>
                    <td style="text-align: center; width: 5%">{{$item['Dias']}}</td>
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