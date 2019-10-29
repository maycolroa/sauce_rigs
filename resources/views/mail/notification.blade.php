@component('mail::message', ['module' => $mail->module])

@if(isset($mail->message))
    {!! nl2br($mail->message) !!}
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
@component('mail::table')
{{ $mail->table->render() }}
@endcomponent
@endif

@if(isset($mail->buttons))
@component('mail::button', ['url' => $mail->buttons[0]['url'], 'color' => isset($mail->buttons[0]['color']) ? $mail->buttons[0]['color'] : 'red'])
    {{ $mail->buttons[0]['text'] }}
@endcomponent
@endif

@endcomponent