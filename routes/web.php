<?php

use App\Http\Controllers\Web\CartController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\OrderController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//DEFAULT
Route::get('/', function () {
    return redirect()->route('products.index'); // Redirects to the products index page
});

//AUTH MANAGEMENT
Route::get('/login', function () {
    if (Auth::user()) {
        return redirect('/dashboard');
    }

    return view('UI.auth.login');
});
Route::post('/login', [UserController::class, 'login'])->name('login');

Route::group(['prefix' => 'products', 'as' => 'products.'], function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/api', [ProductController::class, 'indexApi'])->name('api');
});

Route::group(['prefix' => 'carts', 'as' => 'carts.'], function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::get('/api', [CartController::class, 'indexApi'])->name('api');
    Route::post('/add', [CartController::class, 'addToCart'])->name('add');
    Route::post('/delete', [CartController::class, 'delete'])->name('delete');
});


Route::group(['middleware' => 'auth'], function () {
    Route::get('dashboard', [DashboardController::class, 'show'])->name('dashboard');

    Route::group(['prefix' => 'users', 'as' => 'users.', 'middleware' => ['super_admin_checker']], function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/api', [UserController::class, 'indexApi'])->name('api');
    });

    Route::group(['middleware' => ['admin_checker']], function () {
        Route::group(['prefix' => 'products', 'as' => 'products.'], function () {
            Route::post('/delete', [ProductController::class, 'delete'])->name('delete');
            Route::post('/upload', [ProductController::class, 'uploadImages'])->name('upload-images');
            Route::post('/import', [ProductController::class, 'importFromExcel'])->name('import');
        });
    });

    Route::group(['middleware' => ['user_checker']], function () {
        Route::group(['prefix' => 'orders', 'as' => 'orders.'], function () {
            Route::post('/place-order', [OrderController::class, 'placeOrder'])->name('placeOrder');
            Route::post('/status/update', [OrderController::class, 'updateOrderStatus'])->name('update.status');
            Route::get('/', [OrderController::class, 'index'])->name('index');
            Route::get('/api', [OrderController::class, 'indexApi'])->name('api');
        });
    });

    Route::post('logout', [UserController::class, 'logout'])->name('logout');
});
