<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Contract Expiry Warning</title>
</head>
<body style="margin:0; padding:0; background-color:#F3F4F6; font-family:Arial, Helvetica, sans-serif; color:#1F2937;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#F3F4F6; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="640" cellpadding="0" cellspacing="0" style="max-width:640px; width:100%; background-color:#FFFFFF; border-radius:8px; overflow:hidden; border:1px solid #E5E7EB;">
                    <tr>
                        <td style="background-color:#1C75BC; padding:20px 24px;">
                            <h1 style="margin:0; font-size:20px; color:#FFFFFF;">Contract Expiry Warning</h1>
                            <p style="margin:6px 0 0; font-size:13px; color:#DBEAFE;">Ethiopian Pharmaceutical Supply Service — Contract Management System</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px;">
                            <p style="margin:0 0 16px; font-size:15px;">Dear {{ $recipient->name }},</p>

                            @if($expiring->count())
                                <p style="margin:0 0 10px; font-size:14px; color:#92400E; font-weight:bold;">
                                    Expiring soon ({{ $expiring->count() }})
                                </p>
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin-bottom:20px; font-size:13px;">
                                    <thead>
                                        <tr style="background-color:#FFFBEB;">
                                            <th align="left" style="padding:8px; border:1px solid #FDE68A;">Contract</th>
                                            <th align="left" style="padding:8px; border:1px solid #FDE68A;">Department</th>
                                            <th align="left" style="padding:8px; border:1px solid #FDE68A;">End Date</th>
                                            <th align="left" style="padding:8px; border:1px solid #FDE68A;">Days Left</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($expiring as $row)
                                            @php($contract = $row['contract'])
                                            <tr>
                                                <td style="padding:8px; border:1px solid #E5E7EB;">
                                                    <a href="{{ route('contracts.show', $contract) }}" style="color:#1C75BC; text-decoration:none;">{{ $contract->name }}</a>
                                                    <div style="color:#6B7280; font-size:12px;">#{{ $contract->id }}</div>
                                                </td>
                                                <td style="padding:8px; border:1px solid #E5E7EB;">{{ $contract->getDepartmentDisplayName() }}</td>
                                                <td style="padding:8px; border:1px solid #E5E7EB;">{{ $contract->end_date->format('M d, Y') }}</td>
                                                <td style="padding:8px; border:1px solid #E5E7EB; font-weight:bold; color:#B45309;">{{ $row['days'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif

                            @if($expired->count())
                                <p style="margin:0 0 10px; font-size:14px; color:#991B1B; font-weight:bold;">
                                    Expired — requires renewal ({{ $expired->count() }})
                                </p>
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin-bottom:20px; font-size:13px;">
                                    <thead>
                                        <tr style="background-color:#FEF2F2;">
                                            <th align="left" style="padding:8px; border:1px solid #FECACA;">Contract</th>
                                            <th align="left" style="padding:8px; border:1px solid #FECACA;">Department</th>
                                            <th align="left" style="padding:8px; border:1px solid #FECACA;">End Date</th>
                                            <th align="left" style="padding:8px; border:1px solid #FECACA;">Days Overdue</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($expired as $row)
                                            @php($contract = $row['contract'])
                                            <tr>
                                                <td style="padding:8px; border:1px solid #E5E7EB;">
                                                    <a href="{{ route('contracts.show', $contract) }}" style="color:#1C75BC; text-decoration:none;">{{ $contract->name }}</a>
                                                    <div style="color:#6B7280; font-size:12px;">#{{ $contract->id }}</div>
                                                </td>
                                                <td style="padding:8px; border:1px solid #E5E7EB;">{{ $contract->getDepartmentDisplayName() }}</td>
                                                <td style="padding:8px; border:1px solid #E5E7EB;">{{ $contract->end_date->format('M d, Y') }}</td>
                                                <td style="padding:8px; border:1px solid #E5E7EB; font-weight:bold; color:#B91C1C;">{{ $row['days'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif

                            <p style="margin:0 0 8px; font-size:14px;">
                                You are receiving this email because you are authorized to extend or renew these contracts.
                            </p>
                            <p style="margin:0; font-size:14px;">
                                <a href="{{ route('contracts.alerts') }}" style="display:inline-block; background-color:#1C75BC; color:#FFFFFF; text-decoration:none; padding:10px 18px; border-radius:6px; font-weight:bold;">View Contract Alerts</a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 24px; background-color:#F9FAFB; border-top:1px solid #E5E7EB; font-size:12px; color:#6B7280;">
                            This is an automated message from the Contract Management System. Please do not reply to this email.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
