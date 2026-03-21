<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\CargoTrackController;
use App\Http\Controllers\LogisticsController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ServiceProviderController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StockOrderController;
use App\Http\Controllers\StockReturnController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('home');
Route::get('/terms', fn () => view('pages.legal.terms'))->name('terms');
Route::get('/privacy', fn () => view('pages.legal.privacy'))->name('privacy');
Route::get('receipts/installments/{payment}/verify', [PaymentController::class, 'verifyReceipt'])
    ->middleware('signed')
    ->name('payments.receipts.verify');

Route::get('track-cargo', [CargoTrackController::class, 'form'])->name('cargo.track.form');
Route::post('track-cargo', [CargoTrackController::class, 'lookup'])
    ->middleware('throttle:20,1')
    ->name('cargo.track.lookup');

Route::get('track/cargo/{flow_token}', [CargoTrackController::class, 'show'])
    ->middleware('throttle:60,1')
    ->name('cargo.track');

Route::middleware(['auth'])->get('pending-approval', fn () => view('pages.auth.pending-approval'))->name('pending-approval');

Route::middleware(['auth', 'verified', 'approved'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('settings/business', [SettingsController::class, 'business'])->name('settings.business');
    Route::get('settings/billing', [SettingsController::class, 'billing'])->name('settings.billing');
    Route::get('settings/notifications', [SettingsController::class, 'notifications'])->name('settings.notifications');

    Route::get('stores', [StoreController::class, 'index'])->name('stores.index');
    Route::get('stores/create', [StoreController::class, 'create'])->name('stores.create');
    Route::post('stores', [StoreController::class, 'store'])->name('stores.store');
    Route::get('stores/{store}/edit', [StoreController::class, 'edit'])->name('stores.edit');
    Route::put('stores/{store}', [StoreController::class, 'update'])->name('stores.update');

    Route::get('product-categories', [ProductCategoryController::class, 'index'])->name('product-categories.index');
    Route::get('product-categories/create', [ProductCategoryController::class, 'create'])->name('product-categories.create');
    Route::post('product-categories', [ProductCategoryController::class, 'store'])->name('product-categories.store');

    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('products', [ProductController::class, 'store'])->name('products.store');
    Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');

    Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('inventory/create', [InventoryController::class, 'create'])->name('inventory.create');
    Route::post('inventory', [InventoryController::class, 'store'])->name('inventory.store');
    Route::get('inventory/{inventory}', [InventoryController::class, 'show'])->name('inventory.show');
    Route::patch('inventory/{inventory}', [InventoryController::class, 'update'])->name('inventory.update');
    Route::post('inventory/{inventory}/add-stock', [InventoryController::class, 'addStock'])->name('inventory.add-stock');
    Route::post('inventory/{inventory}/remove-stock', [InventoryController::class, 'removeStock'])->name('inventory.remove-stock');

    Route::get('service-providers', [ServiceProviderController::class, 'index'])->name('service-providers.index');
    Route::get('service-providers/create', [ServiceProviderController::class, 'create'])->name('service-providers.create');
    Route::post('service-providers', [ServiceProviderController::class, 'store'])->name('service-providers.store');
    Route::get('service-providers/{serviceProvider}/edit', [ServiceProviderController::class, 'edit'])->name('service-providers.edit');
    Route::put('service-providers/{serviceProvider}', [ServiceProviderController::class, 'update'])->name('service-providers.update');
    Route::delete('service-providers/{serviceProvider}', [ServiceProviderController::class, 'destroy'])->name('service-providers.destroy');

    Route::get('stock-orders', [StockOrderController::class, 'index'])->name('stock-orders.index');
    Route::get('stock-orders/create', [StockOrderController::class, 'create'])->name('stock-orders.create');
    Route::post('stock-orders', [StockOrderController::class, 'store'])->name('stock-orders.store');
    Route::get('stock-orders/{stockOrder}', [StockOrderController::class, 'show'])->name('stock-orders.show');
    Route::patch('stock-orders/{stockOrder}', [StockOrderController::class, 'update'])->name('stock-orders.update');
    Route::patch('stock-orders/{stockOrder}/status', [StockOrderController::class, 'updateStatus'])->name('stock-orders.update-status');
    Route::post('stock-orders/{stockOrder}/receive', [StockOrderController::class, 'receive'])->name('stock-orders.receive');

    Route::get('clients', [ClientController::class, 'index'])->name('clients.index');
    Route::post('clients', [ClientController::class, 'store'])->name('clients.store');
    Route::get('api/clients/search', [ClientController::class, 'apiSearch'])->name('clients.api.search');
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/{plan}', [PaymentController::class, 'show'])->name('payments.show');
    Route::post('payments/plans', [PaymentController::class, 'storePlan'])->name('payments.plans.store');
    Route::post('payments/installments', [PaymentController::class, 'storeInstallment'])->name('payments.installments.store');
    Route::post('payments/{plan}/remind', [PaymentController::class, 'sendReminder'])->name('payments.remind');

    Route::get('sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('sales/create', [SaleController::class, 'create'])->name('sales.create');
    Route::post('sales', [SaleController::class, 'store'])->name('sales.store');
    Route::get('sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
    Route::get('sales/{sale}/receipt', [SaleController::class, 'receipt'])->name('sales.receipt');

    Route::get('logistics', [LogisticsController::class, 'index'])->name('logistics.index');
    Route::get('logistics/cargo/create', [LogisticsController::class, 'createCargo'])->name('logistics.cargo.create');
    Route::post('logistics/cargo', [LogisticsController::class, 'storeCargo'])->name('logistics.cargo.store');
    Route::get('logistics/flow/{flow_token}', [LogisticsController::class, 'flow'])->name('logistics.flow');
    Route::patch('logistics/flow/{flow_token}/delivery-status', [LogisticsController::class, 'updateDeliveryStatus'])->name('logistics.update-status');

    Route::get('stock-returns', [StockReturnController::class, 'index'])->name('stock-returns.index');
    Route::get('stock-returns/create', [StockReturnController::class, 'create'])->name('stock-returns.create');
    Route::post('stock-returns', [StockReturnController::class, 'store'])->name('stock-returns.store');

    Route::get('expense-categories', [ExpenseCategoryController::class, 'index'])->name('expense-categories.index');
    Route::post('expense-categories', [ExpenseCategoryController::class, 'store'])->name('expense-categories.store');
    Route::delete('expense-categories/{expenseCategory}', [ExpenseCategoryController::class, 'destroy'])->name('expense-categories.destroy');

    Route::get('expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::get('expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
    Route::post('expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::get('expenses/{expense}/receipt', [ExpenseController::class, 'receipt'])->name('expenses.receipt');

    Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
    Route::post('invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
    Route::post('invoices/{invoice}/mark-paid', [InvoiceController::class, 'markPaid'])->name('invoices.mark-paid');
    Route::post('invoices/{invoice}/mark-unpaid', [InvoiceController::class, 'markUnpaid'])->name('invoices.mark-unpaid');

    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::get('users/{user}', [AdminController::class, 'showUser'])->name('users.show');
        Route::post('users/{user}/approve', [AdminController::class, 'approve'])->name('users.approve');
        Route::post('users/{user}/suspend', [AdminController::class, 'suspend'])->name('users.suspend');
        Route::post('users/{user}/terminate', [AdminController::class, 'terminate'])->name('users.terminate');
        Route::get('organizations/{organization}', [AdminController::class, 'showOrganization'])->name('organizations.show');
    });
});

require __DIR__.'/settings.php';
