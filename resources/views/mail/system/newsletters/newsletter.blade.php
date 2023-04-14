@component('mail::message', ['module' => $mail->module])

@if(isset($mail->with["data"]))
    <img src="{{$mail->with["data"]["path"]}}">
@endif

@endcomponent