<?php

namespace App\Helpers;

use Carbon\Carbon;
use App\Models\Category;
use App\Models\Event;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class HelperFile
{
    /**
     * Get the required data for the landing page.
     *
     * @return array
     */
    public static function getData($type = "all")
    {
        if($type == 'empty'){
            return [
                'categories' => [],
                'events' => [],
                'transactions' => [],
            ];
        }
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

        // Loop through the data and separate into income and expense
        foreach ($categories as $category) {
            if ($category['type'] === 0) {
                $categoryData['expense'][] = $category;
            } else {
                $categoryData['income'][] = $category;
            }
        }

        // Fetch events for the current month
        $events = Event::where('month_value', $currentMonth)->get();
        
        $transactions = [];

        if (Auth::check()) {
            $userId = Auth::id();

            $transactions = Transaction::with(['category', 'subCategory'])
                ->where('user_id', $userId)
                ->where('year_value', $currentYear)
                ->where('month_value', $currentMonth)
                ->get();
        }

        return [
            'categories' => $categoryData,
            'events' => $events,
            'transactions' => $transactions,
        ];
    }
}
