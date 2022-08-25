<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers\V1', 'as' => 'api.'], function () {

    Route::post('login', 'Auth@authenticate');
    Route::post('forgot-password', 'Auth@forgotpassword');
    Route::post('verify-otp', 'Auth@verifyOTP');
    Route::post('reset-password', 'Auth@resetpassword');

    Route::group(['middleware' => ['jwt.verify']], function () {
        Route::post('logout', 'Auth@logout');

        // User Details
        Route::get('users/details/{id}', 'UsersController@getUserDetails');
        Route::post('users/update-personal-details', 'UsersController@updatePersonalDetails');
        Route::post('users/update-bank-details', 'UsersController@updateBankDetails');
        Route::get('ajax-employee-search', 'UsersController@getUsersByBranch');
        Route::get('user-services-search', 'UsersController@getServices');

        // Listing for employee specific listing (provide particular salons employee listing only)
        Route::get('users/employees-list', 'UsersController@employeesList');
        Route::post('users/create-employee', 'UsersController@storeUser');
        Route::post('users/update-employee/{external_id}', 'UsersController@updateUser');


        // Commission
        Route::group(['prefix' => 'commission'], function () {
            Route::get('my-commission', 'CommissionController@myCommission');
        });

        // Clients Module
        Route::resource('clients', 'ClientsController', [
            'as' => 'client-api', // naming it because it was comflict with (web.php) resource naming
        ]);
        Route::post('clients/store-basic-details', 'ClientsController@storeBasicDetails');

        // appointments Module
        Route::group(['prefix' => 'appointments'], function () {
            // Update appointment
            Route::post('re-schedule', 'AppointmentsController@reschedule');
            Route::post('status-update', 'AppointmentsController@updateStatus');
            // Appointment images
            Route::get('list-images', 'AppointmentsController@listImages');
            Route::post('store-images', 'AppointmentsController@storeImages');
            Route::post('update-images', 'AppointmentsController@updateImage');
            Route::post('delete-images', 'AppointmentsController@deleteImage');
        });
        Route::resource('appointments', 'AppointmentsController', [
            'as' => 'appointments-api',
        ]);

		// unit module
        Route::resource('units', 'UnitsController', [
            'as' => 'unit-api',
        ]);

        // Inquiry module
        Route::resource('inquiry', 'InquiryController', [
            'as' => 'inquiry-api',
        ]);
        Route::group(['prefix' => 'inquiry'], function () {
            Route::post('status-update', 'InquiryController@updateStatus');
        });
        Route::get('inquiry-type-search', 'InquiryTypeController@searchByName');

        // branch Module
        Route::resource('branch', 'BranchController', [
            'as' => 'branch-api',
        ]);
		//Products module
		Route::group(['prefix' => 'products'], function () {
			Route::post('/ajax-product-by-name', 'ProductsController@getProductByName');
            Route::post('/ajax-services-by-name', 'ProductsController@getServicesByName');
            Route::post('/ajax-package-by-name', 'ProductsController@getPackagesByName');
            Route::post('/ajax-product-by-category', 'ProductsController@getProductByCategory');
		});
		Route::resource('products', 'ProductsController', [
            'as' => 'products-api',
        ]);
        Route::get('auth-branch-search', 'BranchController@branchByDistributor');

        // Status Module
        Route::get('status-ajax-search', 'StatusController@statusByName');

        // Daybook
        Route::group(['prefix' => 'daybook'], function () {
            Route::get('listing', 'DaybookController@index');
            Route::post('cash-in', 'DaybookController@storeCashIn');
            Route::post('cash-out', 'DaybookController@storeCashOut');
        });

        // countries list
        Route::get('countries', 'CountryController@index');
        // state list
        Route::get('states/{country_id}', 'StateController@index');
        // Role search
        Route::get('roles/', 'RolesController@rolesByName');

		/**
         * Vendors
         */
        Route::group(['prefix' => 'vendors'], function () {
            /* Route::post('/check-name', 'VendorsController@checkName')->name('vendors.checkname');
            Route::post('/vendor-detail-by-id', 'VendorsController@datailById')->name('vendors.detailbyid');

            // Ajax delete status
            Route::post('/check-vendor-delete', 'VendorsController@checkVendorDelete')->name('vendors.checkdelete');
            Route::post('/ajax-vendor-delete', 'VendorsController@ajaxVendorDelete')->name('vendors.delete');

            // For Select2
            Route::get('/ajax-vendor-by-name', 'VendorsController@getVendorsByName')->name('vendor.byname');

            // Check Duplicate
            Route::post('/check-repeat-number', 'VendorsController@checkPrimaryNumberRepeat')->name('vendors.checkPrimaryNumber');
            Route::post('/check-repeat-email', 'VendorsController@checkPrimaryEmailRepeat')->name('vendors.checkPrimaryEmail'); */
        });
        Route::resource('vendors', 'VendorsController', [
            'as' => 'vendor-api',
        ]);
		
		Route::resource('holidays', 'HolidaysController', [
            'as' => 'holiday-api',
        ]);
		
		Route::resource('categories', 'CategoriesController');
		
		Route::group(['prefix' => 'orders'], function () {
			Route::post('/check-product-stock', 'OrdersController@CheckProductStock');
			Route::get('/cancel-order/{id}', 'OrdersController@cancelOrder');
			Route::post('/apply-code', 'OrdersController@applyCode');
			Route::post('/calculate-order', 'OrdersController@calculateOrder');
        });
		Route::resource('orders', 'OrdersController');
    });
});