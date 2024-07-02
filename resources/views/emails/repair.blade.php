@component('mail::message')
{{ __('repair-business.email_repair-markdown-beginning') }} {{$mail_data->repair->customer_data->first_name}} {{ __('repair-business.email_repair-markdown-end') }}
@endcomponent
 