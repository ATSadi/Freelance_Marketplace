<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoiceNumber }}</title>
    <style>
        body { font-family: Georgia, 'Times New Roman', serif; color: #1f2937; max-width: 720px; margin: 40px auto; padding: 0 24px; }
        h1 { font-size: 28px; margin: 0; }
        .muted { color: #6b7280; }
        table { width: 100%; border-collapse: collapse; margin-top: 32px; }
        th, td { text-align: left; padding: 10px 8px; border-bottom: 1px solid #e5e7eb; }
    </style>
</head>
<body>
    <h1>WorkVault Invoice</h1>
    <p class="muted">Invoice {{ $invoiceNumber }}</p>
    <p><strong>Project:</strong> {{ $project->title }}</p>
    <p><strong>Client:</strong> {{ $project->client->name }} ({{ $project->client->email }})</p>
    <p><strong>Freelancer:</strong> {{ $project->freelancer?->name }} ({{ $project->freelancer?->email }})</p>
    <table>
        <tr><th>Description</th><th>Amount</th></tr>
        <tr><td>Milestone: {{ $milestone->title }}</td><td>${{ number_format($milestone->amount, 2) }}</td></tr>
        <tr><td><strong>Total</strong></td><td><strong>${{ number_format($milestone->amount, 2) }}</strong></td></tr>
    </table>
</body>
</html>
