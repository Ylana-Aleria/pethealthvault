<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.user');
Route::get('/owner/dashboard', [AuthController::class, 'index'])->name('owner.dashboard');

Route::get("/forget-password", [AuthController::class, "forgotPassword"])->name("forgot.password");
Route::post("/forgot-password", [AuthController::class, "forgotPasswordPost"])->name("forgot.password.post");
Route::get("/reset-password/{token}", [AuthController::class, "resetPassword"])->name("reset.password");
Route::post("/reset-password", [AuthController::class, "resetPasswordPost"])->name("reset.password.post");

Route::get("/change-password", [AuthController::class, "changePassword"])->name("change.password");
Route::post("/update-password", [AuthController::class, "changePasswordUpdate"])->name("change.password.update");

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::post('/appointment-store', [AuthController::class, 'store'])->name('appointment.store');
