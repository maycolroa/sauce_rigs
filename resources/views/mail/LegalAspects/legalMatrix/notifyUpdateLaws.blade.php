@component('mail::message')

@if(isset($mail->with['user']))
<h2>Hola {{ $mail->with['user'] }}</h2>
@else
<h2>Hola</h2>
@endif

@if(isset($mail->message))
<p>{{ $mail->message }}</p>
@endif

@if(isset($mail->list))
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

@if(isset($mail->table))
@component('mail::table')
{{ $mail->table->render() }}
@endcomponent
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