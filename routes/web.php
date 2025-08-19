<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MyBookController;
use App\Http\Controllers\PreviewController;
use App\Http\Controllers\SearchController;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\BorrowController;
use App\Http\Controllers\Admin\LibrarianController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\RestoreController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', HomeController::class)->name('home');
Route::get('/search', SearchController::class)->name('search');
Route::get('/preview/{book}', PreviewController::class)->name('preview');

// ✅ Tangani login dari QR Code (via query param `?via=qr`)
Route::get('/login', function () {
    if (request()->query('via') === 'qr') {
        session()->put('from_qr', true);
    }
    return view('login');
})->middleware('guest')->name('login');

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::view('/register', 'register')->name('register');
    Route::post('/register', [AuthController::class, 'store']);
    Route::post('/login', [AuthController::class, 'authenticate']);
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::delete('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::resource('/my-books', MyBookController::class)->only('index', 'update');
    Route::post('/my-books/{book}', [MyBookController::class, 'store'])->name('my-books.store');

    /*
    |--------------------------------------------------------------------------
    | Admin Area
    |--------------------------------------------------------------------------
    */
    Route::middleware('superuser')->prefix('/admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::middleware('admin')->group(function () {
            Route::resource('/librarians', LibrarianController::class)->except('show');
        });

        Route::middleware('librarian')->group(function () {
            Route::resource('/members', MemberController::class)->except('show');
            Route::resource('/books', BookController::class)->except('show');
            Route::resource('/borrows', BorrowController::class)->except('show', 'create', 'store');
            Route::resource('/returns', RestoreController::class)->except('show', 'create', 'store');
        });
    });
});
