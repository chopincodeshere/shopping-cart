<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Displays the dashboard view with user and product statistics.
 *
 * @param Request $request The HTTP request instance.
 * @return \Illuminate\View\View The view for the dashboard page.
 */
class DashboardController extends Controller
{
    public function show(Request $request)
    {
        $user_count = User::whereIn('role', [User::USER_ROLE, User::ADMIN_ROLE])->count();
        $products_count = Product::count();

        return view('UI.home', [
            'page_title' => 'Dashboard',
            'user_count' => $user_count,
            'products_count' => $products_count
        ]);
    }
}