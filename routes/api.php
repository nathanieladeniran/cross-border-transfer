<?php

use App\Http\Controllers\Api\ApiProfileController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\BusinessCategoryController;
use App\Http\Controllers\ApiDashboardController;
use App\Http\Controllers\ApiReferralController;
use App\Http\Controllers\ApiSendMoneyController;
use App\Http\Controllers\ApiSettingController;
use App\Http\Controllers\ApiTransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Create your custom middleware map with the alias "is_profile_complete"
$middlewareAliases = [
    'is_profile_complete' => \App\Http\Middleware\IsProfileCompleted::class,
];

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'addNewUser'])->name('register');
    Route::post('/verify-email-otp/{email}', [AuthController::class, 'verifyEmailWithOtp'])->name('verify-email-otp');
    Route::post('/resend-otp/{email}', [AuthController::class, 'resendOtp'])->name('resend-otp');
    Route::post('/login', [AuthController::class, 'signIn'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth:sanctum');

    Route::prefix('recovery')->group(function () {
        Route::post('/reset-link', [AuthController::class, 'sendResetLink'])->name('password.email');
        Route::post('/password/reset', [AuthController::class, 'completePasswordReset'])->name('password.reset');
    });
});

Route::prefix('users')->group(function () {
    Route::post('/save-personal-details', [ApiProfileController::class, 'savePersonalDetails'])->name('personal-details');
    Route::post('/save-personal-address', [ApiProfileController::class, 'savePersonalAddress'])->name('personal-address');
    Route::post('/save-kyc-details', [ApiProfileController::class, 'saveKyc'])->name('kyc-details');
    Route::post('/save-kyc-response', [ApiProfileController::class, 'saveKycResponse'])->name('kyc-response');
    Route::prefix('file-upload')->group(function () {
        Route::post('/upload-document', [ApiProfileController::class, 'userUploadDocs'])->name('upload-document');
        Route::get('/{paginate}/{per_page}', [ApiProfileController::class, 'fetchAllUploads'])->name('fetch-uploadsß');
        Route::post('/search-upload', [ApiProfileController::class, 'filterUploadDocs'])->name('search-upload');
    });
})->middleware('auth:sanctum');

Route::prefix('business')->group(function () {
    Route::post('register-business', [ApiProfileController::class, 'registerBusiness'])->name('register-business');
    Route::post('update-business/{uuid}', [ApiProfileController::class, 'updateBusiness'])->name('update-business');
    Route::get('fetch-business', [ApiProfileController::class, 'getMyBusinessDetails'])->name('fetch-business');
    Route::delete('delete-business', [ApiProfileController::class, 'deleteBusinessAccount'])->name('delete-business');
    Route::get('/business-categories', [BusinessCategoryController::class, 'index'])->name('business-categories');
    Route::get('/jurisdiction-code', [ApiProfileController::class, 'getShuftiProJurisdictionCode'])->name('jurisdiction-code');
})->middleware('auth:sanctum');

Route::prefix('settings')->middleware([$middlewareAliases['is_profile_complete']])->group(function () {
    Route::post('update-password', [ApiSettingController::class, 'updatePassword'])->name('update-password');
    Route::post('update-email', [ApiSettingController::class, 'updateEmail'])->name('update-email');
    Route::post('verify-email-otp', [ApiSettingController::class, 'verifyEmailOtp'])->name('verify-email-otp');
    Route::post('update-phone', [ApiSettingController::class, 'updatePhone'])->name('update-phone');
    Route::post('verify-phone-otp', [ApiSettingController::class, 'verifyPhoneOtp'])->name('verify-phone-otp');
    Route::post('update-notifications', [ApiSettingController::class, 'updateNotificationMethod'])->name('update-notification');
    Route::post('update-language', [ApiSettingController::class, 'updateLanguage'])->name('update-language');
    Route::post('deactivation-request', [ApiSettingController::class, 'requestDeactivation'])->name('deactivation-request');
    Route::post('cancel-deactivation-request', [ApiSettingController::class, 'cancelDeativationRequest'])->name('cancel-deactivation-request');
});

Route::prefix('dashboard')->middleware([$middlewareAliases['is_profile_complete']])->group(function () {
    Route::get('meta', [ApiDashboardController::class, 'meta'])->name('meta');
});

Route::prefix('referrals')->group(function () {
    Route::get('/', [ApiReferralController::class, 'referrals'])->name('referrals');
});

Route::prefix('transactions')->group(function () {
    Route::post('search-transactions', [ApiTransactionController::class, 'searchTransactions'])->name('search-transactions');
    Route::post('filter-transactions', [ApiTransactionController::class, 'filterWithDate'])->name('filter-transactions');
    Route::get('list-transactions/{paginate?}/{per_page}', [ApiTransactionController::class, 'allTransactions'])->name('list-transactions');
    Route::post('recall-poli', [ApiSendMoneyController::class, 'recallPoli']);
});

// Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
//     Route::prefix('users')->group(function () {
//         Route::post('/save-personal-details', [ApiProfileController::class, 'savePersonalDetails'])->name('personal-details');
//         Route::post('/save-personal-address', [ApiProfileController::class, 'savePersonalAddress'])->name('personal-address');
//         Route::post('/save-kyc-details', [ApiProfileController::class, 'saveKyc'])->name('kyc-details');
//         Route::post('/save-kyc-response', [ApiProfileController::class, 'saveKycResponse'])->name('kyc-response');
        
//         Route::prefix('file-upload')->group(function () {
//             Route::post('/upload-document', [ApiProfileController::class, 'userUploadDocs'])->name('upload-document');
//             Route::get('/{paginate}/{per_page}', [ApiProfileController::class, 'fetchAllUploads'])->name('fetch-uploads');
//             Route::post('/search-upload', [ApiProfileController::class, 'filterUploadDocs'])->name('search-upload');
//         });
//     });
// });