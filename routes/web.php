<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
// DashboardController removed - does not exist
use App\Http\Controllers\Users\UsersController; // Namespace fixed
use App\Http\Controllers\POWAS\ShowPowasListController; // Namespace fixed
use App\Http\Controllers\Chatbot\PocaController; // Namespace fixed
// MyAccountController removed - does not exist
use App\Http\Controllers\Transactions\TransactionsListController; // Namespace fixed
use App\Http\Controllers\PowasMembersController; // Namespace verified (root)
use App\Http\Controllers\ReadingsController; // Namespace verified (root)
use App\Http\Controllers\Receipts\BillingReceiptController; // Namespace fixed
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\POWAS\ShowPowasRecordsController;
use App\Http\Controllers\POWAS\ShowMembersListController;
use App\Http\Controllers\Settings\SettingsController;
use App\Http\Controllers\POWAS\ManagePowasController;
use App\Http\Controllers\Readings\ReadingSheetController;
use App\Http\Controllers\Billings\CollectionSheetController;
use App\Http\Controllers\Billings\BillPrinterController;
use App\Http\Controllers\POWAS\BillingGenerateController;
use App\Http\Controllers\Receipts\OtherReceiptController;
use App\Http\Controllers\ReceiptMaintenanceController;
use App\Http\Controllers\DebugController;
use App\Http\Controllers\ApplicationFormController;
use App\Livewire\Powas\Apply;
use App\Http\Controllers\Readings\ExcelUploadController;
use App\Http\Controllers\Members\AddMemberController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/powas-os/public/index.php', function () {
    return redirect()->route('login');
});

Route::get('/debug-log', function() {
    $path = storage_path('logs/laravel.log');
    if (!file_exists($path)) return 'No log file found.';
    
    $file = fopen($path, 'r');
    fseek($file, max(0, filesize($path) - 10000));
    $contents = fread($file, 10000);
    fclose($file);
    return '<pre>' . htmlspecialchars($contents) . '</pre>';
});

Route::get('/apply', Apply::class)->name('apply');

// Debug route (TEMPORARY - Remove after debugging)
Route::get('/debug/user-info', [DebugController::class, 'showUserInfo'])->middleware('auth');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // REPLACED DashboardController with Closure
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // COMMENTED OUT missing controller route
    // Route::get('/my-account', [MyAccountController::class, 'index'])->name('my-account');

    // DEBUG ROUTE - DELETE AFTER FIXING
    Route::get('/debug-role', function () {
        $user = Auth::user();
        if (!$user)
            return 'Not logged in';
        return response()->json([
            'id' => $user->user_id,
            'username' => $user->username,
            'roles' => $user->getRoleNames(),
            // 'permissions' => $user->getAllPermissions()->pluck('name'),
            'guard_name_prop' => 'protected',
        ]);
    });

    // Route::middleware('role:admin|secretary|treasurer')->group(function () {
    Route::get('/transaction/{powasID}/{transactionMonth}', [TransactionsListController::class, 'transactions'])->name('transactions');
    Route::get('/accounting/{powasID}/{transactionMonth}', [TransactionsListController::class, 'accounting'])->name('accounting');
    Route::get('/books-of-accounts/{powasID}', [TransactionsListController::class, 'booksOfAccounts'])->name('books-of-accounts');
    Route::get('/voucher/print/{powasID}/{voucherID?}', [TransactionsListController::class, 'printVoucher'])->name('print-voucher');
    Route::get('/allowance-attachment', \App\Livewire\Voucher\AllowanceAttachment::class)->name('allowance-attachment');
    Route::get('/printing-expenses-attachment', \App\Livewire\Voucher\PrintingExpensesAttachment::class)->name('printing-expenses-attachment');
    Route::get('/acknowledgement-receipt', App\Livewire\Voucher\AcknowledgementReceipt::class)->name('acknowledgement-receipt');
    // });

    // Adjusted middleware with web guard
    Route::middleware(['role:admin,web', 'account_status'])->group(function () {
        Route::get('/users', [UsersController::class, 'index'])->name('users');
        Route::get('/powas', [ShowPowasListController::class, 'index'])->name('powas');
        Route::get('/poca-memory', [PocaController::class, 'index'])->name('poca-brain');
    });

    // Receipt Maintenance Routes (Auth only - no role check needed)
    Route::middleware(['auth', 'account_status'])->prefix('admin/receipts')->group(function () {
        Route::get('/', [ReceiptMaintenanceController::class, 'index'])->name('admin.receipts.index');
        Route::post('/analyze', [ReceiptMaintenanceController::class, 'analyze'])->name('admin.receipts.analyze');
        Route::post('/preview-fix', [ReceiptMaintenanceController::class, 'previewFix'])->name('admin.receipts.preview');
        Route::post('/apply-fix', [ReceiptMaintenanceController::class, 'applyFix'])->name('admin.receipts.apply');
    });

    // Route::middleware('role:admin|secretary|treasurer|collector-reader')->group(function () {
    // Route::middleware('role:admin|secretary|treasurer|collector-reader')->group(function () {
    Route::get('/powas/add-readings/{powasID}', [ReadingsController::class, 'index'])->name('powas.add.reading');
    Route::get('/powas/reading/template/{powasID}', [ReadingsController::class, 'createReadingTemplate'])->name('create-reading-template');
    Route::post('/powas/excel-upload', [ExcelUploadController::class, 'upload'])->name('powas.excel-upload');
    Route::get('/billing-receipts', [BillingReceiptController::class, 'index'])->name('billing-receipts');
    // });
    // });

    Route::middleware(['role:admin|member|secretary|president|treasurer,web', 'account_status'])->group(function () {
        Route::get('/member/edit/{memberID}', [PowasMembersController::class, 'personalInfo'])->name('member-info');
    });
    // MISSING ROUTES RESTORED
    Route::middleware(['account_status'])->group(function () {
        Route::get('/powas/records/{powasID}', [ShowPowasRecordsController::class, 'index'])->name('powas.records');
        Route::get('/transactions-view/{powasID}', [TransactionsListController::class, 'index'])->name('view-transactions');
        Route::get('/members', [ShowMembersListController::class, 'index'])->name('members');
        Route::get('/members/add/{powasID}', [AddMemberController::class, 'index'])->name('members.add');
        Route::get('/applications', [\App\Http\Controllers\POWAS\ShowApplicationListController::class, 'index'])->name('applications');
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
        // Provide mapping for powas.show
        Route::get('/powas/manage/{powas_id}', [ManagePowasController::class, 'index'])->name('powas.show');

        // MORE RESTORED ROUTES
        Route::get('/powas/reading-sheet/{powasID}/{readingDate?}', [ReadingSheetController::class, 'view'])->name('powas.reading-sheet');
        Route::get('/powas/collection-sheet/{powasID}/{billingMonth?}', [CollectionSheetController::class, 'view'])->name('powas.collection-sheet');
        Route::get('/powas/print-billing', [BillPrinterController::class, 'view'])->name('powas.print-billing');
        Route::get('/powas/billing/add/{powasID}/{regen}', [BillingGenerateController::class, 'index'])->name('powas.add.billing');
        Route::get('/other-receipt/view', [OtherReceiptController::class, 'view'])->name('other-receipt.view');
        Route::get('/application/download/{applicationid}', [ApplicationFormController::class, 'download'])->name('application-form.download');
        Route::get('/application/view/{applicationid}', [ApplicationFormController::class, 'view'])->name('application-form.view');
    });

});

// Route::get('/api/readings/{powasID?}', [ReadingsAPIController::class, 'readingsIndex']);

// TEMPORARY DEBUG ROUTE - REMOVE AFTER DEBUGGING
Route::get('/debug-log', function () {
    $logPath = storage_path('logs/laravel.log');
    if (!file_exists($logPath))
        return 'No log file found.';
    $lines = array_slice(file($logPath), -150);
    return response(implode('', $lines), 200)->header('Content-Type', 'text/plain');
});

Route::get('/clear-all-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('permission:cache-reset');
    return "All caches cleared!";
});
