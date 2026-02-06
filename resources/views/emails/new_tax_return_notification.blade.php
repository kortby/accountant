<x-mail::message>
# New Tax Return Submission

A new tax return has been submitted and requires your attention.

## Client Information

**Name:** {{ $clientName }}  
**Email:** {{ $clientEmail }}  
@if($clientPhone)
**Phone:** {{ $clientPhone }}  
@endif
**Tax Year:** {{ $taxYear }}  
**Submitted:** {{ $submittedAt->format('F j, Y \a\t g:i A') }}

## Return Details

**Return ID:** #{{ $returnId }}  
**Status:** {{ ucfirst(str_replace('_', ' ', $taxReturn->status)) }}

## Income Information

{!! nl2br(e($initialMessage)) !!}

<x-mail::button :url="config('app.url') . '/tax-information/' . $returnId">
View Tax Return
</x-mail::button>

Please review this submission and assign it to an accountant if needed.

Thanks,<br>
{{ config('app.name') }} System
</x-mail::message>
