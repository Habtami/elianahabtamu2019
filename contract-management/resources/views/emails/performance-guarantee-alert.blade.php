<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('performance_guarantee.mail_heading') }}</title>
</head>
<body style="margin:0; padding:0; background-color:#F3F4F6; font-family:Arial, Helvetica, sans-serif; color:#1F2937;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#F3F4F6; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="640" cellpadding="0" cellspacing="0" style="max-width:640px; width:100%; background-color:#FFFFFF; border-radius:8px; overflow:hidden; border:1px solid #E5E7EB;">
                    <tr>
                        <td style="background-color:#1C75BC; padding:20px 24px;">
                            <h1 style="margin:0; font-size:20px; color:#FFFFFF;">{{ __('performance_guarantee.mail_heading') }}</h1>
                            <p style="margin:6px 0 0; font-size:13px; color:#DBEAFE;">{{ __('performance_guarantee.footer') }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px;">
                            <p style="margin:0 0 6px; font-size:14px; font-weight:bold; color:#1C75BC;">
                                {{ __('performance_guarantee.concerned_party') }}
                            </p>
                            <p style="margin:0 0 20px; font-size:14px;">
                                {{ __('performance_guarantee.mail_intro') }}
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin-bottom:20px; font-size:14px;">
                                <tr>
                                    <td style="padding:8px; border:1px solid #E5E7EB; background-color:#F9FAFB; width:45%;">{{ __('performance_guarantee.contract_name') }}</td>
                                    <td style="padding:8px; border:1px solid #E5E7EB;">
                                        <a href="{{ route('contracts.show', $contract) }}" style="color:#1C75BC; text-decoration:none;">{{ $contract->name }}</a>
                                        <div style="color:#6B7280; font-size:12px;">#{{ $contract->id }}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:8px; border:1px solid #E5E7EB; background-color:#F9FAFB;">{{ __('performance_guarantee.supplier_name') }}</td>
                                    <td style="padding:8px; border:1px solid #E5E7EB;">{{ $contract->supplier_name ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px; border:1px solid #E5E7EB; background-color:#F9FAFB;">{{ __('performance_guarantee.contract_amount') }}</td>
                                    <td style="padding:8px; border:1px solid #E5E7EB;">
                                        {{ $contract->contract_amount !== null ? number_format((float) $contract->contract_amount, 2) : '—' }}
                                        @if($contract->currency_type)
                                            {{ $contract->currency_type }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:8px; border:1px solid #E5E7EB; background-color:#F9FAFB;">{{ __('performance_guarantee.guarantee_end_date') }}</td>
                                    <td style="padding:8px; border:1px solid #E5E7EB; font-weight:bold;">
                                        {{ $contract->performance_guarantee_end_date?->format('M d, Y') ?? '—' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:8px; border:1px solid #E5E7EB; background-color:#F9FAFB;">{{ __('performance_guarantee.days_remaining') }}</td>
                                    <td style="padding:8px; border:1px solid #E5E7EB; font-weight:bold; color:#B45309;">
                                        {{ __('performance_guarantee.days_value', ['count' => max(0, (int) $daysRemaining)]) }}
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 20px; font-size:14px; color:#92400E;">
                                {{ __('performance_guarantee.mail_action') }}
                            </p>

                            <p style="margin:0; font-size:12px; color:#6B7280;">
                                {{ __('performance_guarantee.footer') }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
