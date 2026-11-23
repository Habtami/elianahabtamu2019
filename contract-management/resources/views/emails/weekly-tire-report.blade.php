@component('mail::message')
# Weekly Tire Report

**Period:** {{ now()->subWeek()->format('M d') }} to {{ now()->format('M d') }}

## Summary

| Metric | Count |
|--------|-------|
| Total Tires | {{ $stats['total_tires'] }} |
| Active Drivers | {{ $stats['total_drivers'] }} |
| Tires Given This Week | {{ $stats['gave_this_week'] }} |
| Tires Returned This Week | {{ $stats['returned_this_week'] }} |
| Tires In Use | {{ $stats['tires_in_use'] }} |
| License Expiring Soon | {{ $stats['expiring_licenses'] }} |

## Tires Given This Week

@component('mail::table')
| Tire | Driver | KM Given | Date |
|------|--------|----------|------|
@foreach($gave as $give)
| {{ $give->tire->serial_number }} | {{ $give->driver->name }} | {{ $give->given_kilometer }} | {{ $give->give_date->format('Y-m-d') }} |
@endforeach
@endcomponent

## Tires Returned This Week

@component('mail::table')
| Tire | Driver | KM Returned | Date |
|------|--------|-------------|------|
@foreach($returned as $return)
| {{ $return->serial_number }} | {{ $return->driver->name }} | {{ $return->current_kilometer }} | {{ $return->return_date->format('Y-m-d') }} |
@endforeach
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
