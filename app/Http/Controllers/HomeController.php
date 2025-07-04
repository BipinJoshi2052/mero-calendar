<?php

namespace App\Http\Controllers;

use App\Helpers\HelperFile;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use App\Models\Event;
use App\Models\Transaction;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Fetch the common data
        $data = HelperFile::getData();
        // dd($data);
        return view('landing', compact('data'));
    }
    
    public function getCalendarData(Request $request)
    {
        $year = $request->input('year');
        $month = $request->input('month');

        // Fetch the categories, events, and transactions for the selected year and month
        $categories = Category::with('subCategories')->get();

        $categoryData = [
            'income' => [],
            'expense' => [],
        ];

        foreach ($categories as $category) {
            if ($category['type'] === 0) {
                $categoryData['expense'][] = $category;
            } else {
                $categoryData['income'][] = $category;
            }
        }

        $events = Event::where('month_value', $month)->where('year_value', $year)->get();

        $transactions = [];
        if (Auth::check()) {
            $userId = Auth::id();
            $transactions = Transaction::with(['category', 'subCategory'])
                ->where('user_id', $userId)
                ->where('year_value', $year)
                ->where('month_value', $month)
                ->get();
        }

        return response()->json([
            'events' => $events,
            'transactions' => $transactions
        ]);
    }

}
