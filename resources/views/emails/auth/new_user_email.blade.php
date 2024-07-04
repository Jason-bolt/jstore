@component('mail::message')
# Welcome to JSTORE!

Kindly verify your account by using the otp below.

@component('mail::panel')
{{ $otp }}
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
