<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\LeaderRequestController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';


Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);


Route::get('/register/{code?}', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/verify-otp', [RegisterController::class, 'showOtpForm'])->name('otp.verify.form');
Route::post('/verify-otp', [RegisterController::class, 'verifyOtp'])->name('otp.verify');

// Support multiple URL patterns
Route::get('/register/{code?}', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::get('/invite/{code}', [RegisterController::class, 'showRegisterForm'])->name('invite');

Route::get('/join/{code}', [RegisterController::class, 'showRegisterForm'])->name('join');
Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('admin.dashboard');
Route::get('admin/leader-request', [AdminDashboardController::class, 'leader_request_data'])->middleware(['auth']);
Route::get('admin/accept-leader-request-{id}', [AdminDashboardController::class, 'accept_leader'])->middleware(['auth']);
Route::get('admin/reject-leader-request-{id}', [AdminDashboardController::class, 'reject_leader'])->middleware(['auth']);

Route::get('/user/dashboard', [UserDashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('user.dashboard');

Route::post('/apply-leader', [UserDashboardController::class, 'applyLeader'])->name('user.applyLeader');


// Route::middleware('auth')->group(function () {
//     Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
//     Route::post('/leader-request', [LeaderRequestController::class, 'store'])->name('leader.request');
// });
Route::middleware(['auth'])->group(function () {
    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::post('/leader/apply', [LeaderRequestController::class, 'apply'])->name('leader.apply');
    Route::post('/admin/leader-request/{id}', [LeaderRequestController::class, 'update'])->middleware('auth');
});
