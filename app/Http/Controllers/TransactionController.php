<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{

    public function index22(Request $request)
    {
        // Fetch categories and subcategories
        $categories = Category::all(); // Assuming you have a 'Category' model
        $subcategories = SubCategory::all(); // Assuming you have a 'SubCategory' model

        if ($request->ajax()) {
            // Handle AJAX request for filtered data
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $categoryId = $request->input('category_id');
            $subCategoryId = $request->input('sub_category_id');

            $query = Transaction::with(['category', 'subCategory']);

            // Apply filters based on date range
            if ($startDate) {
                $startDate = Carbon::parse($startDate)->startOfDay();
                $query->where('created_at', '>=', $startDate);
            }

            if ($endDate) {
                $endDate = Carbon::parse($endDate)->endOfDay();
                $query->where('created_at', '<=', $endDate);
            }

            // Apply category filter if available
            if ($categoryId) {
                $query->where('category_id', $categoryId);
            }

            // Apply subcategory filter if available
            if ($subCategoryId) {
                $query->where('sub_category_id', $subCategoryId);
            }

            // Pagination settings
            $perPage = $request->input('length', 10);
            $offset = $request->input('start', 0);

            // Paginate the data
            $transactions = $query->orderby('id', 'desc')->skip($offset)->take($perPage)->get();

            return response()->json([
                'draw' => intval($request->get('draw')),
                'recordsTotal' => Transaction::count(),
                'recordsFiltered' => $query->count(),
                'data' => $transactions,
            ]);
        }

        // Pass categories and subcategories to the view
        return view('transactions.index', compact('categories', 'subcategories'));
    }

    public function index(Request $request)
    {
        // Check if the user is authenticated
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get the authenticated user's ID
        $userId = auth()->id();
        // Get input data
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $searchValue = $request->input('search_value');
        // dd($searchValue);
        $categoryId = $request->input('category_id');
        $subCategoryId = $request->input('sub_category_id');
        
        // Fetch categories and subcategories
        $categories = Category::all();
        $subcategories = SubCategory::where('category_id',$categoryId)->get();

        if ($request->ajax()) {

            // Create the base query
            $query = Transaction::with(['category', 'subCategory']);

            // Apply date filters
            if ($startDate) {
                $startDate = Carbon::parse($startDate)->startOfDay();
                $query->where('transaction_date', '>=', $startDate);
            }

            if ($endDate) {
                $endDate = Carbon::parse($endDate)->endOfDay();
                $query->where('transaction_date', '<=', $endDate);
            }

            // Apply search filter
            if ($searchValue) {
                $query->where(function($q) use ($searchValue) {
                    $q->where('title', 'like', '%' . $searchValue . '%');
                });
            }

            // Apply category filter
            if ($categoryId) {
                $query->where('category_id', $categoryId);
            }

            // Apply subcategory filter
            if ($subCategoryId) {
                $query->where('sub_category_id', $subCategoryId);
            }
            $query->where('user_id', $userId);

            // Calculate the total records (without pagination)
            $totalRecords = Transaction::where('user_id', $userId)->count();  // Total records (unfiltered)
            
            // Get filtered records count (without pagination)
            $filteredRecords = clone $query;
            $filteredRecords = $filteredRecords->count(); // Filtered records after applied filters

            // Pagination settings
            $perPage = $request->input('length', 10);  // Default to 10 if not provided
            $offset = $request->input('start', 0);  // Get the offset value (DataTables will send 'start')

            // Paginate the data for the current page
            $transactions = clone $query;
            $transactions = $transactions->orderby('transaction_date', 'desc')->skip($offset)->take($perPage)->get();

            // Calculate totals for income (type = 1) and expense (type = 2)
            $totalIncomeQuery = clone $query;
            $totalIncome = $totalIncomeQuery->where('type', 1)->sum('amount');
            $totalIncomeCount = $totalIncomeQuery->where('type', 1)->count();

            $totalExpenseQuery = clone $query;
            $totalExpense = $query->where('type', 0)->sum('amount');
            $totalExpenseCount = $query->where('type', 0)->count();


            // Return the required structure for DataTables
            return response()->json([
                'draw' => intval($request->get('draw')),
                'recordsTotal' => $totalRecords,  // Total records (unfiltered)
                'recordsFiltered' => $filteredRecords,  // Total records (filtered)
                'data' => $transactions,  // Data for the current page
                'totalIncome' => $totalIncome,
                'totalIncomeCount' => $totalIncomeCount,
                'totalExpense' => $totalExpense,
                'totalExpenseCount' => $totalExpenseCount,
            ]);
        }

        return view('transactions.index', compact('categories', 'subcategories'));
    }

    public function analytics(Request $request)
    {
        $userId = Auth::id();
        $type = $request->input('type'); // income or expense or category
        $from = $request->input('from', Carbon::now()->subDays(15)->format('Y-m-d'));
        $to = $request->input('to', Carbon::now()->format('Y-m-d'));

        $query = Transaction::with('category')
            ->where('user_id', $userId)
            ->whereBetween('transaction_date', [$from, $to])
            ->orderBy('transaction_date','asc');

        $chartData = [];
        $doughnutData = [];
        //     $typeVal = $type === 'income' ? 1 : 0;
        // dd($query->where('type', $typeVal)->get()->toArray());

        if ($type === 'income' || $type === 'expense') {
            $typeVal = $type === 'income' ? 1 : 0;

            $chartData = $query->where('type', $typeVal)
                ->get()
                ->groupBy(function ($item) {
                    // return $item->transaction_date->format('Y-m-d');
                    return $item->transaction_date;
                })
                ->map(function ($group) {
                    return $group->sum('amount');
                });
        }
        // dd($chartData);

        if ($type === 'category') {
            $doughnutData = $query->where('type', 0) // only expenses
                ->get()
                ->groupBy('category.title')
                ->map(function ($group) {
                    return $group->sum('amount');
                })->sortDesc();
        }

        return view('transactions.analytics', compact('type', 'from', 'to', 'chartData', 'doughnutData'));
    }

    public function getSubcategories($categoryId)
    {
        $subcategories = SubCategory::where('category_id', $categoryId)->get();
        return response()->json($subcategories);
    }


    public function index2(Request $request)
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
            // dd($transactions);
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
