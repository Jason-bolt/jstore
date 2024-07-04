@component('mail::message')
# Welcome to JSTORE!

Kindly find your resent otp below.

@component('mail::panel')
{{ $otp }}
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
