@component('mail::message')
Se ha generador una exportacion de audiometrias.

@component('mail::button', ['url' => $url, 'color' => 'red'])
Descargar
@endcomponent


@component('mail::subcopy')
Este link es valido por 24 horas
@endcomponent

@endcomponent