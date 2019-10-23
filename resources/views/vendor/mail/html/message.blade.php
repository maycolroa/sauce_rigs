@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header')
            @isset($module)
                @if($module->isMain())
                    @if($module->logo)
                        @slot('logo')
                            {{ $module->logo }}
                        @endslot
                    @endif
                    @slot('title')
                     {{ $module->display_name }}
                    @endslot
                @endif
            @endisset
        @endcomponent
    @endslot

    {{-- Body --}}
    {{ $slot }}

    {{-- Information --}}
    @slot('information')
        @component('mail::information')
        @endcomponent
    @endslot

    {{-- Subcopy --}}
    @slot('subcopy')
        @component('mail::subcopy')
            Por favor no contestar este correo. Éste es enviado automáticamente.
        @endcomponent
    @endslot

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            © {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.') Desarrollado por <img src="https://sauce.rigs.com.co/images/Rigs-thot-gris.png" style="width:180px; heigth:60px; vertical-align: middle;">
        @endcomponent
    @endslot
@endcomponent
