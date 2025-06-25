<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            $query = Transaction::with(['category', 'subCategory']);
            if ($startDate && $endDate) {
                // Parse start and end dates using Carbon
                $startDate = Carbon::parse($startDate)->startOfDay(); // Set to the start of the day (00:00:00)
                $endDate = Carbon::parse($endDate)->endOfDay(); // Set to the end of the day (23:59:59)

                // Apply the date range filter
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
            // Pagination: Get current page and per_page (DataTable settings)
            $perPage = $request->input('length', 10);  // Default to 10 if not provided
            $page = $request->input('start', 0) / $perPage + 1; // Calculate page number from DataTable
            $offset = $request->input('start', 0);  // Get the offset value (DataTables will send 'start')

            // Paginate with offset and perPage
            $transactions = $query->orderby('id','desc')->skip($offset)->take($perPage)->get(); // Adjust pagination for custom offset

            return response()->json([
                'draw' => intval($request->get('draw')), // Required for DataTables to track the requests
                'recordsTotal' => Transaction::count(), // Total records (no filtering)
                'recordsFiltered' => Transaction::count(), // Total records (filtered by date range if applied)
                'data' => $transactions,  // Return the current page's records
                'page' => $page,
                'perPage' => $perPage,
            ]);
        }

        return view('transactions.index');
    }


    public function index1()
    {
        $transactions = Transaction::with(['category', 'subCategory'])->get();
        return response()->json($transactions);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|integer',
            'category_id' => 'nullable|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'amount' => 'required|numeric',
            'transaction_date' => 'required|date',
        ]);

        // Get the raw transaction date from the request (e.g. "Thu Jun 19 2025")
        $transactionDateStr = $request->transaction_date;

        // Convert the raw date string to a Carbon instance (e.g., "Thu Jun 19 2025" -> "2025-06-19")
        $transactionDate = Carbon::parse($transactionDateStr)->format('Y-m-d');
        
        // Extract the month, day, and year from the Carbon instance
        $monthValue = Carbon::parse($transactionDateStr)->month;
        $dateValue = Carbon::parse($transactionDateStr)->day;
        $yearValue = Carbon::parse($transactionDateStr)->year;

        // Create the transaction
        $transaction = Transaction::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'type' => $request->type,
            'category_id' => $request->category_id,
            'sub_category_id' => $request->sub_category_id,
            'amount' => $request->amount,
            'transaction_date' => $transactionDate, // Store the formatted date (Y-m-d)
            'month_value' => $monthValue,
            'date_value' => $dateValue,
            'year_value' => $yearValue,
        ]);


        return response()->json($transaction, 201);
    }

    public function show($id)
    {
        $transaction = Transaction::with(['category', 'subCategory'])->find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        return response()->json($transaction);
    }

    public function update(Request $request, $id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'type' => 'required|integer',
            'category_id' => 'nullable|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'amount' => 'required|numeric',
            'month_value' => 'required|integer',
            'date_value' => 'required|integer',
        ]);

        $transaction->update($request->all());

        return response()->json($transaction);
    }

    public function destroy($id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $transaction->delete();

        return response()->json(['message' => 'Transaction deleted successfully']);
    }
}
