<?php

namespace App\Http\Controllers;

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
        // Get current month (1-12)
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;
        $currentDay = Carbon::now()->day;
        
        // Fetch categories and subcategories
        $categories = Category::with('subCategories')->get();

        // Prepare category data
        $categoryData = [
            'income' => [],
            'expense' => [],
        ];

        // Organize categories and subcategories by type
        foreach ($categories as $category) {
            $categoryType = $category->type == 1 ? 'income' : 'expense';
            $subCategoryNames = $category->subCategories->pluck('title')->toArray();

            $categoryData[$categoryType][$category->title] = $subCategoryNames;
        }
        $data['categories'] = $categoryData;

        // Fetch events for the current month
        $data['events'] = Event::where('month_value', $currentMonth)->where('date_value', $currentDay)->get();

        // Prepare transactions
        $data['transactions'] = [];

        if (Auth::check()) {
            $userId = Auth::id();

            $data['transactions'] = Transaction::with(['category', 'subCategory'])
                ->where('user_id', $userId)
                ->where('year_value', $currentYear)
                ->where('month_value', $currentMonth)
                ->get();
        }
        // dd($data);
        return view('landing', compact('data'));
    }
}
