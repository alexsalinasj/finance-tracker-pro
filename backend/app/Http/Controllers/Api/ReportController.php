<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    public function summary(Request $request): JsonResponse
    {
        $data = $this->buildReportData($request);

        return response()->json(['data' => $data]);
    }

    public function exportPdf(Request $request): Response
    {
        $data = $this->buildReportData($request);
        $pdf = Pdf::loadView('reports.monthly', ['report' => $data]);

        return $pdf->download("finance-report-{$data['year']}-{$data['month']}.pdf");
    }

    private function buildReportData(Request $request): array
    {
        $validated = $request->validate([
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['nullable', 'integer', 'between:2000,2100'],
        ]);

        $month = (int) ($validated['month'] ?? now()->month);
        $year = (int) ($validated['year'] ?? now()->year);
        $user = $request->user();

        $incomeTotal = (float) $user->incomes()->whereMonth('date', $month)->whereYear('date', $year)->sum('amount');
        $expenseTotal = (float) $user->expenses()->whereMonth('date', $month)->whereYear('date', $year)->sum('amount');

        $incomeByCategory = $user->incomes()
            ->select('categories.name', 'categories.color', DB::raw('SUM(incomes.amount) as total'))
            ->join('categories', 'categories.id', '=', 'incomes.category_id')
            ->whereMonth('incomes.date', $month)
            ->whereYear('incomes.date', $year)
            ->groupBy('categories.id', 'categories.name', 'categories.color')
            ->orderByDesc('total')
            ->get();

        $expenseByCategory = $user->expenses()
            ->select('categories.name', 'categories.color', DB::raw('SUM(expenses.amount) as total'))
            ->join('categories', 'categories.id', '=', 'expenses.category_id')
            ->whereMonth('expenses.date', $month)
            ->whereYear('expenses.date', $year)
            ->groupBy('categories.id', 'categories.name', 'categories.color')
            ->orderByDesc('total')
            ->get();

        return [
            'month' => $month,
            'year' => $year,
            'period_label' => Carbon::create($year, $month, 1)->format('F Y'),
            'income_summary' => [
                'total' => round($incomeTotal, 2),
                'count' => $user->incomes()->whereMonth('date', $month)->whereYear('date', $year)->count(),
                'by_category' => $incomeByCategory,
            ],
            'expense_summary' => [
                'total' => round($expenseTotal, 2),
                'count' => $user->expenses()->whereMonth('date', $month)->whereYear('date', $year)->count(),
                'by_category' => $expenseByCategory,
            ],
            'final_balance' => round($incomeTotal - $expenseTotal, 2),
            'top_expense_categories' => $expenseByCategory->take(5)->values(),
        ];
    }
}

