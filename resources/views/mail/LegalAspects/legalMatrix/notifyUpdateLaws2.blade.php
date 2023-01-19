@component('mail::message', ['module' => $mail->module])

@if(isset($mail->with['user']))
<h2>Hola {{ $mail->with['user'] }}</h2>
@else
<h2>Hola</h2>
@endif

@if(isset($mail->message))
<p>{{ $mail->message }}</p>
@endif

{{--@if(isset($mail->list))
@if($mail->list_order == 'ul')
<ul>
@foreach($mail->list as $index => $item)
<li><a href="{{ $mail->with['urls'][$index] }}" target="_blank">{!! nl2br($item) !!}</a></li>
@endforeach
</ul>
@elseif ($mail->list_order == 'ol')
<ol>
@foreach($mail->list as $index => $item)
<li><a href="{{ $mail->with['urls'][$index] }}" target="_blank">{!! nl2br($item) !!}</a></li>
@endforeach
</ol>
@else
@foreach($mail->list as $index => $item)
<a href="{{ $mail->with['urls'][$index] }}" target="_blank">{!! nl2br($item) !!}</a> <br>
@endforeach

@endif
@endif

@if(isset($mail->table))--}}
@if(isset($mail->with['data']))
<div>
    <center>
        <table class="table-general" style="width: 100%; border: solid gray 1px;">
            <thead>
                <tr>
                    <th style="width: 40%; border-bottom: solid gray 1px; border-right: solid gray 1px;">Ley</th>
                    <th style="width: 60%; border-bottom: solid gray 1px;">Modificación</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mail->with['data'] as $index => $item)
                <tr>
                    <td style="text-align: center; width: 40%; border-bottom: solid gray 1px;border-right: solid gray 1px;"><a href="{{ $mail->with['urls'][$index] }}" target="_blank">{!! nl2br($item['law']) !!}</a></td>
                    <td style="text-align: center; width: 60%; border-bottom: solid gray 1px;">{!! nl2br($item['modification']) !!}</td>
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