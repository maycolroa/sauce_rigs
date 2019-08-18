@component('mail::message')

@if(isset($mail->with['responsible']))
<h2>Hola {{ $mail->with['responsible'] }}</h2>
@else
<h2>Hola</h2>
@endif

@if(isset($mail->message))
<p>{{ $mail->message }}</p>
@endif

@if(isset($mail->list))
@if($mail->list_order == 'ul')
<ul>
@foreach($mail->list as $item)
<li>{{ $item }}</li>
@endforeach
</ul>
@elseif ($mail->list_order == 'ol')
<ol>
@foreach($mail->list as $item)
<li>{{ $item }}</li>
@endforeach
</ol>
@else
@foreach($mail->list as $item)
{{ $item }} <br>
@endforeach

@endif
@endif

@if(isset($mail->table))
# Actividades #
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