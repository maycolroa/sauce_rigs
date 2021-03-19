@component('mail::message', ['module' => $mail->module])

@if(isset($mail->with['user']))
<h2>Hola {{ $mail->with['user'] }}</h2>
@else
<h2>Hola</h2>
@endif

@if(isset($mail->message))
<p>{{ $mail->message }}</p>
@endif

@if(isset($mail->with['data']))
<div>
    <center>
        <table class="table-general" style="width: 80%">
            <thead>
                <tr>
                    <th>Empleado</th>
                    <th>Capacitación</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mail->with['data'] as $index => $item)
                <tr>
                    <td style="text-align: center">{{$item['employee']}}</td>
                    <td style="text-align: center"><a href="{{ $item['url'] }}" target="_blank">{!! nl2br($item['training']) !!}</a></td>
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