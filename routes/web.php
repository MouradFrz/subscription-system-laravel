<?php

use App\Http\Controllers\PayementController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

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

Auth::routes();
Route::get('/', [SubscriptionController::class, 'home'])->name('home');
Route::middleware('auth')->group(function () {
    Route::middleware('free')->group(function () {
        Route::post('/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscribe');
        Route::get('/subscribe', [SubscriptionController::class, 'showSubscribe'])->name('showSubscribe');
        Route::post('/start-trial', [SubscriptionController::class, 'startTrial'])->name('startTrial');
        Route::get('/trial', [SubscriptionController::class, 'trial'])->name('trial');
    });
    Route::middleware('subscribed')->group(function () {
        Route::get('/plans', [SubscriptionController::class, 'getPlans'])->name('showPlans');
        Route::post('/cancel-subscription', [SubscriptionController::class, 'cancelSubscription'])->name('cancelSubscription');
        Route::post('/terminate-subscription', [SubscriptionController::class, 'terminateSubscription'])->name('terminateSubscription');
        Route::post('/resume-subscription', [SubscriptionController::class, 'resumeSubscription'])->name('resumeSubscription');
        Route::get('/manage-subscription', [SubscriptionController::class, 'manageSubscriptionPage'])->name('manageSubscriptionPage');
    });
    Route::get('/billing-methods', [PayementController::class, 'getBillingMethods'])->name('billingMethods');
    Route::post('/remove-billing-method', [PayementController::class, 'removeBillingMethod'])->name('removeBillingMethod');
    Route::post('/add-billing-method', [PayementController::class, 'addBillingMethod'])->name('addBillingMethod');
});
