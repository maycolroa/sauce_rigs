@component('mail::message', ['module' => $mail->module])

@if(isset($mail->message))
    {!! nl2br($mail->message) !!}
@endif

@if(count($mail->with["modules_news"]) > 0)
<b>Módulos nuevos:</b>
<br>
<ol>
@foreach($mail->with["modules_news"] as $item)
<li>{{ $item }}</li>
@endforeach
</ol>
@endif

@if(count($mail->with["modules_olds"]) > 0)
<b>Módulos renovados:</b>
<br>
<ol>
@foreach($mail->with["modules_olds"] as $item)
<li>{{ $item }}</li>
@endforeach
</ol>
@endif

@endcomponent