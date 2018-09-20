@component('mail::message')

@if(isset($mail->message))
    {{ $mail->message }}
@endif

@if(isset($mail->list))
@foreach($mail->list as $item)
1.  {{ $item }}
@endforeach
@endif

@if(isset($mail->table))
@component('mail::table')
{{ $mail->table->render() }}
@endcomponent
@endif

@if(isset($mail->buttons))
@component('mail::button', ['url' => $mail->buttons[0]['url'], 'color' => isset($mail->buttons[0]['color']) ? $mail->buttons[0]['color'] : 'blue'])
    {{ $mail->buttons[0]['text'] }}
@endcomponent

@component('mail::subcopy')
    Este link es valido por 24 horas
@endcomponent
@endif

@endcomponent