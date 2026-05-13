<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitor Recovered</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .header { background: #16a34a; padding: 24px 32px; }
        .header h1 { color: #fff; margin: 0; font-size: 20px; }
        .header p { color: #bbf7d0; margin: 4px 0 0; font-size: 14px; }
        .body { padding: 32px; }
        .status-badge { display: inline-block; background: #dcfce7; color: #16a34a; font-weight: bold; font-size: 13px; padding: 4px 12px; border-radius: 999px; margin-bottom: 24px; }
        .detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
        .detail-row:last-child { border-bottom: none; }
        .label { color: #6b7280; }
        .value { color: #111827; font-weight: 500; }
        .footer { padding: 20px 32px; background: #f9fafb; font-size: 12px; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Monitor Recovered</h1>
            <p>{{ config('app.name') }} — your service is back online</p>
        </div>
        <div class="body">
            <span class="status-badge">RECOVERED</span>
            <div class="detail-row">
                <span class="label">Monitor</span>
                <span class="value">{{ $monitor->name }}</span>
            </div>
            <div class="detail-row">
                <span class="label">URL</span>
                <span class="value">{{ $monitor->url }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Incident Started</span>
                <span class="value">{{ $incident->created_at->format('M j, Y g:i A') }} UTC</span>
            </div>
            <div class="detail-row">
                <span class="label">Resolved At</span>
                <span class="value">{{ $incident->resolved_at->format('M j, Y g:i A') }} UTC</span>
            </div>
            <div class="detail-row">
                <span class="label">Downtime Duration</span>
                <span class="value">{{ $incident->created_at->diffForHumans($incident->resolved_at, true) }}</span>
            </div>
        </div>
        <div class="footer">
            You are receiving this alert because you own this monitor in {{ config('app.name') }}.
        </div>
    </div>
</body>
</html>
