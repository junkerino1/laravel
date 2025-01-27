<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Bet2Controller;
use App\Http\Controllers\GiftController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\SlotBetRecordController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\SmsController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Contracts\Permission;
use Spatie\Permission\Models\Role;

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

Route::post('/auth', [AuthController::class, 'checkAuth'])->name('auth');
Route::group(['middleware' => ['auth']], function () {
    Route::get('/', function () {
        return view('default');
    })->name('home');
    Route::get('/category', [ProductController::class, 'getData']);
    Route::get('/category/create',[CategoryController::class, 'index']);
    Route::post('/category/create', [CategoryController::class, 'createCategory'])->name('category.create');
    Route::get('/product', [ProductController::class, 'index'])->name('product.view');
    Route::post('/product/create', [ProductController::class, 'createProduct'])->name('product.create');
    Route::get('/product/{category_id}', [ProductController::class, 'getProduct']);
    Route::post('/product/edit/{product_id}', [ProductController::class, 'editProduct'])->name('product.edit');
    Route::delete('/product/delete/{product_id}', [ProductController::class, 'deleteProduct'])->name('product.delete');

    // Permissions
    Route::get('/permission', [PermissionController::class, 'showPermission']);
    Route::get('/permission/user', [PermissionController::class, 'fetchUser']);
    Route::get('/permission/role', [PermissionController::class, 'fetchRole']);
    Route::post('/permission/update/role', [PermissionController::class, 'updateRole'])->name('permission-role');
    Route::post('/permission/update/user', [PermissionController::class, 'updateUser'])->name('permission-user');
    Route::post('/permission/create', [PermissionController::class, 'createPermission'])->name('permission-create');
    Route::get('/permission/user/{user_id}', [PermissionController::class, 'fetchUserPermission']);
    Route::get('/permission/role/{role_id}', [PermissionController::class, 'fetchRolePermission']);

    //gift
    Route::get('/gift', [TransactionController::class, 'checkBalance'])->name('gift');
    Route::get('/gift/check', [GiftController::class, 'checkGift'])->name('checkGift');
    Route::post('/gift/credit', [TransactionController::class, 'credit'])->name('credit');

    // sms
    Route::get('/sms', function () {
        return view('sms');
    })->name('sms');
    Route::get('/otp', function () {
        return view('otp');
    })->name('enterOTP');
    Route::post('/send-sms', [SmsController::class, 'send'])->name('sendSMS');
    Route::post('/check-otp', [SmsController::class, 'checkOTP'])->name('checkOTP');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/send-mail', [SmsController::class, 'sendMail']);

