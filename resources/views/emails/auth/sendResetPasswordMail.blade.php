@component('mail::message')
# Welcome to JSTORE!

Use the below otp to reset your password

@component('mail::panel')
{{ $otp }}
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
