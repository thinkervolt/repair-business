@component('mail::message')
{{ __('repair-business.email_repair-markdown-beginning') }} {{$mail_data->invoice->customer_name}}, {{ __('repair-business.email_repair-markdown-end') }}
@endcomponent

