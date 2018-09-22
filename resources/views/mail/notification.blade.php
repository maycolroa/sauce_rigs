@component('mail::message')

@if(isset($mail->message))
    {{ $mail->message }}
@endif

@if(isset($mail->list))
@foreach($mail->list as $item)
@if($mail->list_order)
1.  {{ $item }}
@else
*  {{ $item }}
@endif
@endforeach
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