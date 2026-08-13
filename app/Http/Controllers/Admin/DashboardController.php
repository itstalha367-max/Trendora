<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 📊 Basic Stats
        $stats = [
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'total_users' => User::count(),
            'total_categories' => Category::count(),
            'pending_orders' => Order::where('order_status', 'pending')->count(),
            'recent_orders' => Order::with('user')->latest()->take(5)->get(),
        ];

        // 📈 Sales Data for Chart (Last 7 Days)
        $salesData = $this->getSalesData();
        
        // 📊 Top Products
        $topProducts = $this->getTopProducts();
        
        // 🔔 Low Stock Alerts
        $lowStockProducts = Product::where('stock_quantity', '<=', 10)
            ->where('status', true)
            ->take(5)
            ->get();

        // 📊 Total Revenue
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total');

        return view('admin.dashboard', compact(
            'stats', 
            'salesData', 
            'topProducts', 
            'lowStockProducts',
            'totalRevenue'
        ));
    }

    private function getSalesData()
    {
        $data = [
            'labels' => [],
            'sales' => [],
            'orders' => []
        ];
        
        $days = 7;
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dayName = $date->format('D');
            
            $total = Order::whereDate('created_at', $date->toDateString())
                ->where('payment_status', 'paid')
                ->sum('total');
            
            $count = Order::whereDate('created_at', $date->toDateString())->count();
            
            $data['labels'][] = $dayName;
            $data['sales'][] = (float) $total;
            $data['orders'][] = (int) $count;
        }
        
        return $data;
    }

    private function getTopProducts()
    {
        try {
            return Product::withCount('orderItems')
                ->orderBy('order_items_count', 'desc')
                ->take(5)
                ->get();
        } catch (\Exception $e) {
            return collect(); // Empty collection if error
        }
    }
}