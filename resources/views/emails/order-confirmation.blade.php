<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{ __('mail.order_confirmation.subject', ['reference' => $order->reference]) }}</title>
</head>
<body style="margin:0;padding:0;background:#f5f0e6;font-family:Arial,sans-serif;color:#171813;">
<div style="max-width:560px;margin:0 auto;padding:32px 20px;">
    <p style="font:900 18px Georgia,serif;margin:0 0 24px;">GB <span style="font:900 13px Arial;">GAYA B MARKETING</span></p>

    <div style="background:#ffffff;border-radius:8px;padding:28px;">
        <p style="margin:0 0 16px;">{{ __('mail.order_confirmation.greeting', ['name' => $order->user->name]) }}</p>
        <p style="margin:0 0 20px;line-height:1.6;color:#68675f;">{{ __('mail.order_confirmation.intro') }}</p>

        <p style="margin:0 0 4px;font-size:13px;color:#68675f;">{{ __('mail.order_confirmation.order') }}</p>
        <p style="margin:0 0 16px;font-weight:800;">{{ $order->reference }}</p>

        <table style="width:100%;border-collapse:collapse;margin-bottom:16px;">
            @foreach ($order->items as $item)
            <tr>
                <td style="padding:6px 0;border-bottom:1px solid #eee;">{{ $item->product_name }} × {{ $item->quantity }}</td>
                <td style="padding:6px 0;border-bottom:1px solid #eee;text-align:right;">RM{{ number_format($item->line_total_cents / 100, 2) }}</td>
            </tr>
            @endforeach
            <tr>
                <td style="padding:10px 0 0;font-weight:800;">{{ __('mail.order_confirmation.total') }}</td>
                <td style="padding:10px 0 0;font-weight:800;text-align:right;">RM{{ number_format($order->total_cents / 100, 2) }}</td>
            </tr>
        </table>

        <a href="{{ route('library.index') }}" style="display:inline-block;background:#f05a24;color:#ffffff;text-decoration:none;font-weight:800;padding:14px 22px;border-radius:4px;">{{ __('mail.order_confirmation.cta') }} →</a>
    </div>

    <p style="margin:24px 0 0;font-size:12px;color:#8b897f;">{{ __('mail.order_confirmation.footer') }}</p>
</div>
</body>
</html>
