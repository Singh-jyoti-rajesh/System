<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\LeaderRequestController;
use App\Models\LeaderRequest;
use App\Http\Controllers\User\GameController;
use App\Http\Controllers\User\Dashboard1Controller;
use App\Http\Controllers\User\Dashboard5Controller;
use App\Http\Controllers\User\Dashboard3Controller;
use App\Http\Controllers\User\Dashboard30Controller;
use App\Http\Controllers\User\WalletController;

// Default welcome route
Route::get('/', function () {
    return view('welcome');
});

// Default dashboard (unused if role-based dashboards are active)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Auth routes
require __DIR__ . '/auth.php';
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// Registration & OTP
Route::get('/register/{code?}', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::get('/invite/{code}', [RegisterController::class, 'showRegisterForm'])->name('invite');
Route::get('/join/{code}', [RegisterController::class, 'showRegisterForm'])->name('join');
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/verify-otp', [RegisterController::class, 'showOtpForm'])->name('otp.verify.form');
Route::post('/verify-otp', [RegisterController::class, 'verifyOtp'])->name('otp.verify');

// Admin dashboard & leader request management
Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/leader-request/{id}', [AdminDashboardController::class, 'updateLeaderRequest'])->name('admin.leader.request.update');
    Route::get('/leader-data', [AdminDashboardController::class, 'leader_request_data'])->name('admin.leader.data');

    Route::get('admin/leader-request', [AdminDashboardController::class, 'leader_request_data'])->middleware(['auth']);
    Route::get('admin/accept-leader-request-{id}', [AdminDashboardController::class, 'accept_leader'])->middleware(['auth']);
    Route::get('admin/reject-leader-request-{id}', [AdminDashboardController::class, 'reject_leader'])->middleware(['auth']);

    Route::get('/accept-leader/{id}', [AdminDashboardController::class, 'accept_leader'])->name('admin.leader.accept');
    Route::get('/reject-leader/{id}', [AdminDashboardController::class, 'reject_leader'])->name('admin.leader.reject');
    Route::get('/admin/adminlist', [AdminDashboardController::class, 'adminlist'])->name('admin.adminlist');
    Route::delete('/adminlist/{id}', [AdminDashboardController::class, 'destroy'])->name('admin.adminlist.destroy');
    Route::get('/admin/promotion', [AdminDashboardController::class, 'promotion'])->name('admin.promotion');
    Route::get('/pending', [AdminDashboardController::class, 'pending'])->name('admin.pending');
    Route::get('/approved', [AdminDashboardController::class, 'approved'])->name('admin.approved');
    Route::get('/accept-leader/{id}', [AdminDashboardController::class, 'accept_leader'])->name('admin.acceptLeader');
    Route::get('/reject-leader/{id}', [AdminDashboardController::class, 'reject_leader'])->name('admin.rejectLeader');
});

// User dashboard and leader application
Route::middleware(['auth'])->group(function () {
    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::post('/leader/apply', [LeaderRequestController::class, 'apply'])->name('leader.apply');
    Route::post('/admin/leader-request/{id}', [LeaderRequestController::class, 'update'])->middleware('auth');
    Route::get('/user/userlist', [UserDashboardController::class, 'userlist'])->name('user.userlist');
    Route::post('/apply-leader', [UserDashboardController::class, 'applyLeader'])->name('apply.leader');
    Route::delete('/userlist/{id}', [UserDashboardController::class, 'destroy'])->name('user.userlist.delete');
});

// Game & dashboard routes for different game types
Route::middleware(['auth'])->group(function () {
    // General game routes
    Route::get('/game/{type}', [GameController::class, 'showGame'])->name('game.play');
    Route::post('/game/submit', [GameController::class, 'store'])->name('game.submit');
    Route::get('/game/result/{type}', [GameController::class, 'checkResult'])->name('game.result.check');
    Route::get('/game/play/{type}', [GameController::class, 'play'])->name('game.play');

    // Color game bet
    Route::post('/submit-color-bet', [GameController::class, 'submitColorBet'])->name('color.bet.submit');
    Route::get('/check-color-result', [GameController::class, 'checkColorResult'])->name('color.bet.result');

    // Dashboard 1
    Route::get('/user/dashboard1', [Dashboard1Controller::class, 'index'])->name('user.dashboard1');
    Route::post('/game1/submit', [Dashboard1Controller::class, 'store'])->name('game1.submit');
    Route::get('/game1/result/{type}', [Dashboard1Controller::class, 'checkResult'])->name('game1.result.check');
    Route::get('/game1/check-color-result', [Dashboard1Controller::class, 'checkColorResult'])->name('game1.color.result');
    Route::get('/current-round1', [Dashboard1Controller::class, 'getCurrentRound']);

    // Dashboard 3
    Route::get('/user/dashboard3', [Dashboard3Controller::class, 'index'])->name('user.dashboard3');
    Route::get('/current-round3', [Dashboard3Controller::class, 'getCurrentRound']);
    Route::post('/game3/submit', [Dashboard3Controller::class, 'store'])->name('game3.submit');
    Route::get('/game/result3/{type}', [Dashboard3Controller::class, 'checkResult']);
    Route::get('/check-color-result3', [Dashboard3Controller::class, 'checkColorResult']);

    // Dashboard 5
    Route::get('/user/dashboard5', [Dashboard5Controller::class, 'index'])->name('user.dashboard5');
    Route::post('/game5/submit', [Dashboard5Controller::class, 'store'])->name('game5.submit');
    Route::get('/current-round5', [Dashboard5Controller::class, 'getCurrentRound']);
    Route::get('/game5/result/{type}', [Dashboard5Controller::class, 'checkResult']);
    Route::get('/check-color-result5', [Dashboard5Controller::class, 'checkColorResult']);

    // Dashboard 30
    Route::get('/user/landing', [Dashboard30Controller::class, 'landing'])->name('user.landing');
    Route::get('/user/dashboard30', [Dashboard30Controller::class, 'index'])->name('user.dashboard30');
    Route::post('/game30/submit', [Dashboard30Controller::class, 'store'])->name('game30.submit');
    Route::get('/current-round30', [Dashboard30Controller::class, 'getCurrentRound']);
    Route::get('/game/result30/{type}', [Dashboard30Controller::class, 'checkResult']);
    Route::get('/check-color-result30', [Dashboard30Controller::class, 'checkColorResult']);

    // Wallet
    Route::get('/user/wallet', [WalletController::class, 'index'])->name('user.wallet');
    Route::get('/user/account', [WalletController::class, 'account'])->name('user.account');
    Route::get('/user/promotion', [WalletController::class, 'promotion'])->name('user.promotion');
});

// Super Admin Routes
Route::get('/super-admin/register', [\App\Http\Controllers\Auth\SuperAdminRegisterController::class, 'showRegisterForm'])->name('superadmin.register');
Route::post('/super-admin/register', [\App\Http\Controllers\Auth\SuperAdminRegisterController::class, 'register']);
Route::get('/super-admin/login', [\App\Http\Controllers\Auth\SuperAdminLoginController::class, 'showLoginForm'])->name('superadmin.login');
Route::post('/super-admin/login', [\App\Http\Controllers\Auth\SuperAdminLoginController::class, 'login']);
Route::get('/super-admin/dashboard', function () {
    return view('superadmin.dashboard');
})->middleware(['auth', 'role:super_admin'])->name('superadmin.dashboard');

// ✅ ADDED: Role-Based Route Group Dashboards (non-conflicting)
Route::prefix('superadmin')->middleware(['auth', 'role:super_admin'])->group(function () {
    Route::get('/role-dashboard', function () {
        return view('superadmin.dashboard');
    })->name('role.superadmin.dashboard');
});

Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/role-dashboard', function () {
        return view('admin.dashboard');
    })->name('role.admin.dashboard');
});

Route::prefix('user')->middleware(['auth', 'role:user'])->group(function () {
    Route::get('/role-dashboard', function () {
        return view('user.dashboard');
    })->name('role.user.dashboard');
});
