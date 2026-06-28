<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Finance Report {{ $report['period_label'] }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; font-size: 12px; }
        h1 { margin-bottom: 4px; font-size: 24px; }
        h2 { margin-top: 24px; font-size: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border-bottom: 1px solid #e5e7eb; padding: 8px; text-align: left; }
        th { background: #f8fafc; }
        .grid { display: table; width: 100%; margin-top: 20px; }
        .cell { display: table-cell; width: 33%; padding: 12px; background: #f8fafc; border: 1px solid #e5e7eb; }
        .label { color: #64748b; font-size: 11px; text-transform: uppercase; }
        .value { font-size: 20px; font-weight: bold; margin-top: 6px; }
        .positive { color: #047857; }
        .negative { color: #b91c1c; }
    </style>
</head>
<body>
    <h1>Finance Tracker Pro</h1>
    <div>Monthly report for {{ $report['period_label'] }}</div>

    <div class="grid">
        <div class="cell">
            <div class="label">Income</div>
            <div class="value positive">${{ number_format($report['income_summary']['total'], 2) }}</div>
        </div>
        <div class="cell">
            <div class="label">Expenses</div>
            <div class="value negative">${{ number_format($report['expense_summary']['total'], 2) }}</div>
        </div>
        <div class="cell">
            <div class="label">Final balance</div>
            <div class="value">${{ number_format($report['final_balance'], 2) }}</div>
        </div>
    </div>

    <h2>Income by category</h2>
    <table>
        <thead>
            <tr><th>Category</th><th>Total</th></tr>
        </thead>
        <tbody>
            @forelse ($report['income_summary']['by_category'] as $category)
                <tr>
                    <td>{{ $category->name }}</td>
                    <td>${{ number_format((float) $category->total, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="2">No income registered.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Top expense categories</h2>
    <table>
        <thead>
            <tr><th>Category</th><th>Total</th></tr>
        </thead>
        <tbody>
            @forelse ($report['top_expense_categories'] as $category)
                <tr>
                    <td>{{ $category->name }}</td>
                    <td>${{ number_format((float) $category->total, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="2">No expenses registered.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

