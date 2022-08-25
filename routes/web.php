<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/
Route::group(['prefix' => 'admin'], function () {

    Route::auth();

    // Route::get('/password/reset', 'ForgotPassword@showLinkRequestForm')->name('password.request');
    // Route::post('/password/reset', 'ResetPassword@reset')->name('change_password');
    // Route::get('/password/reset/{token}', 'ResetPassword@showResetForm')->name('password.reset');

    Route::get('logout','Auth\LoginController@logout');

    Route::group(['middleware' => ['auth']], function () {
        /**
         * Main
         */
        Route::get('/', 'PagesController@dashboard');
        Route::get('dashboard', 'PagesController@dashboard')->name('dashboard');

        /**
         * Users
         */
        Route::group(['prefix' => 'users'], function () {
            Route::get('/data', 'UsersController@anyData')->name('users.data');
            Route::get('/taskdata/{id}', 'UsersController@taskData')->name('users.taskdata');
            Route::get('/leaddata/{id}', 'UsersController@leadData')->name('users.leaddata');
            Route::get('/clientdata/{id}', 'UsersController@clientData')->name('users.clientdata');
            Route::get('/users', 'UsersController@users')->name('users.users');
            Route::get('/calendar-users', 'UsersController@calendarUsers')->name('users.calendar');

            Route::get('/professional_details/{external_id}/', 'UsersController@showProfessionalDetails')->name('users.showProfessionalDetails');
            Route::get('/other_details/{external_id}/', 'UsersController@showOtherDetails')->name('users.showOtherDetails');

            Route::get('/ajax-users', 'UsersController@getUserByName')->name('users.userbyname');
            Route::post('/check-repeat-email', 'UsersController@checkEmailRepeat')->name('users.checkemail');
             Route::post('/check-repeat-number', 'UsersController@checkPrimaryNumberRepeat')->name('users.checkPrimaryNumber');

            // Ajax delete enquiry type
            Route::post('/check-user-delete', 'UsersController@checkUserDelete')->name('users.checkdelete');
            Route::post('/ajax-user-delete', 'UsersController@ajaxUserDelete')->name('users.delete');

            // Get Services of perticular User/Emaployee
            Route::get('/ajax-user-services', 'UsersController@getServices')->name('users.getServices');

            // Profile
            Route::get('/profile', 'UsersController@profile')->name('users.profile');
            Route::post('/update-personal', 'UsersController@updatePersonal')->name('users.updatePersonal');
            Route::post('/update-other', 'UsersController@updateOther')->name('users.updateOther');

            Route::get('/filter', 'UsersController@typeWiseFilter')->name('users.filter');
        });
        Route::resource('users', 'UsersController');

        /**
        * Roles
        */
        Route::group(['prefix' => 'roles'], function () {
            Route::get('/data', 'RolesController@indexData')->name('roles.data');
            Route::patch('/update/{external_id}', 'RolesController@update')->name('roles.update');

            // Ajax delete status
            Route::post('/check-role-delete', 'RolesController@checkRoleDelete')->name('roles.checkdelete');
            Route::post('/ajax-role-delete', 'RolesController@ajaxRoleDelete')->name('roles.delete');
        });
        Route::resource('roles', 'RolesController', ['except' => [
            'update'
        ]]);

        /**
         * Clients
         */
        Route::group(['prefix' => 'clients'], function () {
            Route::get('/data', 'ClientsController@anyData')->name('clients.data');
            Route::get('/taskdata/{external_id}', 'ClientsController@taskDataTable')->name('clients.taskDataTable');
            Route::get('/projectdata/{external_id}', 'ClientsController@projectDataTable')->name('clients.projectDataTable');
            Route::get('/leaddata/{external_id}', 'ClientsController@leadDataTable')->name('clients.leadDataTable');
            Route::get('/invoicedata/{external_id}', 'ClientsController@invoiceDataTable')->name('clients.invoiceDataTable');
            Route::post('/create/cvrapi', 'ClientsController@cvrapiStart');
            Route::post('/upload/{external_id}', 'DocumentsController@upload')->name('document.upload');
            Route::patch('/updateassign/{external_id}', 'ClientsController@updateAssign');
            Route::post('/updateassign/{external_id}', 'ClientsController@updateAssign');
            Route::post('/updateassign/{external_id}', 'ClientsController@updateAssign');

            Route::post('/check-repeat-email', 'ClientsController@checkEmailRepeat')->name('clients.checkemail');
            Route::post('/check-repeat-number', 'ClientsController@checkPrimaryNumberRepeat')->name('clients.checkPrimaryNumber');

            // Ajax (Dropdown)
            Route::get('/ajax-industries', 'ClientsController@getIntustryByName')->name('products.industrybyname');
            Route::post('/find_by_id', 'ClientsController@findById')->name('clients.findbyid');

            // Store client basic details (for enquiry model - ajax)
            Route::post('/store_basic', 'ClientsController@storeBasic')->name('clients.storeBasic');

            // Ajax delete enquiry type
            Route::post('/check-clients-delete', 'ClientsController@checkClientDelete')->name('clients.checkdelete');
            Route::post('/ajax-clients-delete', 'ClientsController@ajaxClientDelete')->name('clients.delete');

            /**
             * Timeline
             */
            Route::get('timeline-setup', 'ClientsController@clienTimelineView')->name('clients.timelineSetup');
            Route::post('timeline-update', 'ClientsController@clientTimelineUpdate')->name('clients.updateTimeline');

        });

        Route::group(['prefix' => 'clients_timeline'], function () {
            Route::post('timeline-update', 'ClientsTimelineController@clientTimelineUpdate')->name('clients_timeline.updateTimeline');

            // Timeline api
            Route::post('all-status-api', 'ClientsTimelineController@allStatuses')->name('clients_timeline.allStatuses');
            Route::post('report-api', 'ClientsTimelineController@reportApi')->name('clients_timeline.reportApi');
            Route::get('client-list', 'ClientsTimelineController@ClientsList')->name('clients_timeline.ClientsList');
            Route::get('client-list-api', 'ClientsTimelineController@ClientsListAPI')->name('clients_timeline.ClientsListAPI');
        });
        Route::resource('clients_timeline', 'ClientsTimelineController');

        Route::resource('clients', 'ClientsController');
        Route::get('document/{external_id}', 'DocumentsController@view')->name('document.view');
        Route::get('document/download/{external_id}', 'DocumentsController@download')->name('document.download');
        Route::resource('documents', 'DocumentsController');

        /**
         * Leads
         */
        Route::group(['prefix' => 'leads'], function () {
            Route::get('/all-leads-data', 'LeadsController@allLeads')->name('leads.all');

            Route::get('/all-leads', 'LeadsController@allData')->name('leads.alldata');
            Route::post('/lead-detail-by-id', 'LeadsController@datailById')->name('leads.detailbyid');

            Route::post('/update-status', 'LeadsController@updateStatus')->name('status.updateStatus');

            Route::post('/covert-to-qualified/{lead}', 'LeadsController@convertToQualifiedLead')->name('lead.convert.qualified');
            Route::post('/covert-to-order/{lead}', 'LeadsController@convertToOrder')->name('lead.convert.order');

            Route::get('/ajax-clients', 'LeadsController@getClientsByName')->name('leads.clientsbyname');
            Route::get('/ajax-all-clients', 'LeadsController@getAllClientsByName')->name('leads.allclientsbyname');
            Route::get('/ajax-users', 'LeadsController@getUsersByName')->name('leads.usersbyname');
        });
        Route::resource('leads', 'LeadsController');
        Route::post('/comments/{type}/{external_id}', 'CommentController@store')->name('comments.create');

        /**
         * Settings
         */
        Route::group(['prefix' => 'settings'], function () {
            Route::get('/', 'SettingsController@index')->name('settings.index');
            Route::patch('/overall', 'SettingsController@updateOverall')->name('settings.update');
            Route::post('/first-steps', 'SettingsController@updateFirstStep')->name('settings.update.first_step');
            Route::get('/business-hours', 'SettingsController@businessHours')->name('settings.business_hours');
            Route::get('/date-formats', 'SettingsController@dateFormats')->name('settings.date_formats');
        });

        /**
         * Integrations
         */
        Route::group(['prefix' => 'integrations'], function () {
            Route::post('/revokeAccess', 'IntegrationsController@revokeAccess')->name('integration.revoke-access');
            Route::post('/sync/dinero', 'IntegrationsController@dineroSync')->name('sync.dinero');
        });
        Route::resource('integrations', 'IntegrationsController');

        /**
         * Notifications
         */
        Route::group(['prefix' => 'notifications'], function () {
            Route::post('/markread', 'NotificationsController@markRead')->name('notification.read');
            Route::get('/markall', 'NotificationsController@markAll');
            Route::get('/{id}', 'NotificationsController@markRead');
        });


        /**
         * Appointments
         */
        Route::group(['prefix' => 'appointments'], function () {
            Route::get('/data', 'AppointmentsController@allData')->name('appointments.data');
            Route::get('/todays-data', 'AppointmentsController@todaysAppointments')->name('appointments.today');
            Route::post('/find-by-id', 'AppointmentsController@findById')->name('appointments.findById');

            Route::post('/reschedule-appointment', 'AppointmentsController@reschedule')->name('appointments.reschedule');

            // Calendar View & data
            Route::get('/calendar', 'AppointmentsController@calendar')->name('appointments.calendar');
            Route::get('/calendar-data', 'AppointmentsController@calendarData')->name('appointments.calendarData');
            Route::get('/calendar-resources', 'AppointmentsController@getResourceEmployees')->name('appointments.resources');

            // Appointment Images
            Route::post('/store-images', 'AppointmentsController@storeImages')->name('appointments.storeImages');
            Route::post('/update-images', 'AppointmentsController@updateImage')->name('appointments.updateImage');
            Route::post('/delete-images', 'AppointmentsController@deleteImage')->name('appointments.deleteImage');

            // update status
            Route::post('/update-status', 'AppointmentsController@updateStatus')->name('appointments.updateStatus');
        });

        Route::resource('appointments', 'AppointmentsController');


        /**
         * Absence
         */
        Route::group(['prefix' => 'absences'], function () {
            Route::get('/data', 'AbsenceController@indexData')->name('absence.data');
            Route::get('/', 'AbsenceController@index')->name('absence.index');
            Route::get('/create', 'AbsenceController@create')->name('absence.create');
            Route::post('/', 'AbsenceController@store')->name('absence.store');
            Route::delete('/{absence}', 'AbsenceController@destroy')->name('absence.destroy');
        });

        // /**
        //  * Employee
        //  */
        // Route::group(['prefix' => 'employee'], function () {
        //     // Route::get('/index', 'EmployeesController@index')->name('employee.index');
        // });

        // Route::resource('employee', 'EmployeesController');

        /**
         * Category
         */
        Route::group(['prefix' => 'category'], function () {
            Route::get('/data', 'CategoriesController@anyData')->name('category.data');

            Route::get('/ajax-category', 'CategoriesController@getCategoryByName')->name('category.categorybyname');
            Route::get('/ajax-sub-category', 'CategoriesController@getSubCategoryByName')->name('category.subCategoryByName');
            Route::post('/check-category-delete', 'CategoriesController@checkCategoryDelete')->name('category.checkdelete');
            Route::post('/ajax-category-delete', 'CategoriesController@ajaxCategoryDelete')->name('category.delete');
        });
        Route::resource('category', 'CategoriesController');

        /**
         * Products
         */
        Route::group(['prefix' => 'product'], function () {
            Route::get('/view/{external_id}', 'ProductsController@show')->name('product.view');
            Route::get('/data', 'ProductsController@anyData')->name('product.data');

            Route::post('/check-product-delete', 'ProductsController@checkProductDelete')->name('product.checkdelete');
            Route::post('/ajax-product-delete', 'ProductsController@ajaxProductDelete')->name('product.delete');

            Route::get('/ajax-product-by-name', 'ProductsController@getProductByName')->name('product.byname');
            Route::get('/ajax-services-by-name', 'ProductsController@getServicesByName')->name('product.servicesByname');
            Route::get('/ajax-product-by-name-type', 'ProductsController@getProductTypeByName')->name('product.typebyname');
            Route::get('/ajax-product-by-category', 'ProductsController@getProductByCategory')->name('product.byCategory');
            Route::post('/ajax-product-by-id', 'ProductsController@findById')->name('product.byid');
            Route::post('/ajax-check-sku', 'ProductsController@checkSKUCode')->name('product.checkSKUCode');

            // Manage Commission (Employee wise)
            Route::get('/users_commission/{user_external_id}', 'ProductsController@viewEmployeeCommission')->name('product.viewCommission');
            Route::get('/commission_data', 'ProductsController@allCommissionData')->name('product.commissionData');

            Route::post('/update-commission', 'ProductsController@updateCommission')->name('product.updateCommission');
            Route::post('/reset-commission', 'ProductsController@resetCommission')->name('product.resetCommission');
        });
        Route::resource('product', 'ProductsController');

        /**
         * Enquiry Types
         */

        Route::group(['prefix' => 'enquirytype'], function () {
            Route::get('/data', 'EnquirytypeController@anyData')->name('enquirytype.data');
            Route::post('/check-name', 'EnquirytypeController@checkName')->name('enquirytype.checkname');

            // Ajax delete enquiry type
            Route::post('/check-enquirytype-delete', 'EnquirytypeController@checkEnquirytpyDelete')->name('enquirytype.checkdelete');
            Route::post('/ajax-enquirytype-delete', 'EnquirytypeController@ajaxEnquirytpyDelete')->name('enquirytype.delete');
        });
        Route::resource('enquirytype', 'EnquirytypeController');

        /**
         * Status
         */
        Route::group(['prefix' => 'status'], function () {
            Route::get('/data', 'StatusController@anyData')->name('status.data');
            Route::post('/check-name', 'StatusController@checkName')->name('status.checkname');

            // Ajax delete status
            Route::post('/check-status-delete', 'StatusController@checkStatusDelete')->name('status.checkdelete');
            Route::post('/ajax-status-delete', 'StatusController@ajaxStatusDelete')->name('status.delete');
        });
        Route::resource('status', 'StatusController');

        /**
         * Branch
         */
        Route::group(['prefix' => 'branch'], function () {
            Route::get('/data', 'BranchController@anyData')->name('branch.data');
            Route::post('/check-name', 'BranchController@checkName')->name('branch.checkname');
            Route::post('/branch-detail-by-id', 'BranchController@datailById')->name('branch.detailbyid');

            // Ajax delete branch
            Route::post('/ajax-branch-delete', 'BranchController@ajaxBranchDelete')->name('branch.delete');
            Route::get('/ajax-branch', 'BranchController@branchByDistributor')->name('branch.branchByDistributor');

            // Check Duplicate
            Route::post('/check-repeat-number', 'BranchController@checkPrimaryNumberRepeat')->name('branch.checkPrimaryNumber');
            Route::post('/check-repeat-email', 'BranchController@checkPrimaryEmailRepeat')->name('branch.checkPrimaryEmail');

            Route::get('/ajax-banches', 'BranchController@getBranchByName')->name('branch.getBranchByName');
            Route::get('/ajax-banches-by-distributor', 'BranchController@getBranchByDistributor')->name('branch.getBranchByDistributor');
        });
        Route::resource('branch', 'BranchController');


        /**
         * Units
         */
        Route::group(['prefix' => 'unit'], function () {
            Route::get('/data', 'UnitsController@anyData')->name('unit.data');
            Route::post('/check-name', 'UnitsController@checkName')->name('unit.checkname');
            Route::post('/unit-detail-by-id', 'UnitsController@datailById')->name('unit.detailbyid');

            // Ajax delete status
            Route::post('/check-unit-delete', 'UnitsController@checkUnitDelete')->name('unit.checkdelete');
            Route::post('/ajax-unit-delete', 'UnitsController@ajaxUnitDelete')->name('unit.delete');
        });
        Route::resource('unit', 'UnitsController');


        /**
         * packages
         */
        Route::group(['prefix' => 'package'], function () {
            Route::get('/data', 'PackagesController@anyData')->name('package.data');
            Route::post('/check-name', 'PackagesController@checkName')->name('package.checkname');
            Route::post('/package-detail-by-id', 'PackagesController@datailById')->name('package.detailbyid');

            // Ajax delete status
            Route::post('/check-package-delete', 'PackagesController@checkPackageDelete')->name('package.checkdelete');
            Route::post('/ajax-package-delete', 'PackagesController@ajaxPackageDelete')->name('package.delete');
        });
        Route::resource('package', 'PackagesController');

        /**
         * Vendors
         */
        Route::group(['prefix' => 'vendors'], function () {
            Route::get('/data', 'VendorsController@anyData')->name('vendors.data');
            Route::post('/check-name', 'VendorsController@checkName')->name('vendors.checkname');
            Route::post('/vendor-detail-by-id', 'VendorsController@datailById')->name('vendors.detailbyid');

            // Ajax delete status
            Route::post('/check-vendor-delete', 'VendorsController@checkVendorDelete')->name('vendors.checkdelete');
            Route::post('/ajax-vendor-delete', 'VendorsController@ajaxVendorDelete')->name('vendors.delete');

            // For Select2
            Route::get('/ajax-vendor-by-name', 'VendorsController@getVendorsByName')->name('vendor.byname');

            // Check Duplicate
            Route::post('/check-repeat-number', 'VendorsController@checkPrimaryNumberRepeat')->name('vendors.checkPrimaryNumber');
            Route::post('/check-repeat-email', 'VendorsController@checkPrimaryEmailRepeat')->name('vendors.checkPrimaryEmail');
        });
        Route::resource('vendors', 'VendorsController');

        /**
         * Distributor
         */
        Route::group(['prefix' => 'salons'], function () {
            Route::get('/data', 'DistributorController@anyData')->name('salons.data');
            Route::post('/check-name', 'DistributorController@checkName')->name('salons.checkname');
            Route::post('/salon-detail-by-id', 'DistributorController@datailById')->name('salons.detailbyid');

            // Ajax delete distributor
            Route::post('/check-salon-delete', 'DistributorController@checkDistributorDelete')->name('salons.checkdelete');
            Route::post('/ajax-salon-delete', 'DistributorController@ajaxDistributorDelete')->name('salons.delete');

            // For Select2
            Route::get('/ajax-salon-by-name', 'DistributorController@getDistributorByName')->name('salons.byname');

            // Check Duplicate
            Route::post('/check-repeat-number', 'DistributorController@checkPrimaryNumberRepeat')->name('salons.checkPrimaryNumber');
            Route::post('/check-repeat-email', 'DistributorController@checkPrimaryEmailRepeat')->name('salons.checkPrimaryEmail');
        });
        Route::resource('salons', 'DistributorController');

        /**
         * Inventory
         */
        Route::group(['prefix' => 'stock_master'], function () {
            Route::get('/ajax-invoice_id', 'StockMasterController@getInvoiceNumber')->name('stock_master.invoiceByNumber');
            Route::get('/data', 'StockMasterController@anyData')->name('stock_master.data');
        });
        Route::group(['prefix' => 'incoming_inventory'], function () {
            Route::post('/update-invoice', 'IncomingInventoryController@updateFullInvoice')->name('incoming_inventory.updateInvoice');
            Route::post('/remove-product', 'IncomingInventoryController@remove_product_entry')->name('incoming_inventory.removeProduct');

            Route::get('/data', 'IncomingInventoryController@anyData')->name('incoming_inventory.data');
            Route::post('/check-repeat-invoice', 'IncomingInventoryController@checkInvoiceNumber')->name('incoming_inventory.checkInvoice');
            Route::post('/get-invoice-product', 'IncomingInventoryController@getInvoiceProduct')->name('incoming_inventory.getInvoiceProduct');
            Route::post('/products-array', 'IncomingInventoryController@productsById')->name('incoming_inventory.products_by_id');
        });
        Route::resource('incoming_inventory', 'IncomingInventoryController');
        Route::resource('stock_master', 'StockMasterController');

        /**
         * Daybook
         */
        Route::group(['prefix' => 'daybook'], function () {
            Route::post('/store-cash-in', 'DaybookController@storeCashIn')->name('daybook.storeCashIn');
            Route::post('/store-cash-out', 'DaybookController@storeCashOut')->name('daybook.storeCashOut');

            Route::post('/entries-by-date', 'DaybookController@entriesByDate')->name('daybook.entriesByDate');
            Route::post('/entries-details', 'DaybookController@getEntryDetails')->name('daybook.getEntryDetails');
        });
        Route::resource('daybook', 'DaybookController');

        /**
         * Daybook
         */
        Route::group(['prefix' => 'holidays'], function () {
            Route::get('/data', 'HolidaysController@anyData')->name('holidays.data');
            Route::post('/check-name', 'HolidaysController@checkName')->name('holidays.checkname');

            // Ajax delete status
            Route::post('/check-holiday-delete', 'HolidaysController@checkHolidayDelete')->name('holidays.checkdelete');
            Route::post('/ajax-holiday-delete', 'HolidaysController@ajaxHolidayDelete')->name('holidays.delete');
        });
        Route::resource('holidays', 'HolidaysController');

        /**
         * Deals
         */
        Route::group(['prefix' => 'deals'], function () {
            Route::get('/data', 'DealsAndDiscountController@anyData')->name('deals.data');
            Route::get('/segaments', 'DealsAndDiscountController@getSegaments')->name('deals.getSegaments');
            Route::post('/archive-deal', 'DealsAndDiscountController@archiveDeal')->name('deals.archive');
            Route::post('/toggle-deal-status', 'DealsAndDiscountController@toggleDealStatus')->name('deals.toggleDealStatus');

            Route::post('/remove-product', 'DealsAndDiscountController@remove_product_entry')->name('deals.removeProduct');
            Route::post('/check-repeat-code', 'DealsAndDiscountController@checkCodeRepeat')->name('deals.checkCode');

            Route::get('/ajax-segaments', 'DealsAndDiscountController@getSegamentsByName')->name('deals.segamentsByName');
        });
        Route::resource('deals', 'DealsAndDiscountController');

        /**
         * Tag Management
         */
        Route::group(['prefix' => 'tags'], function () {
            Route::get('/data', 'TagsController@anyData')->name('tags.data');
            Route::get('/get-kpi', 'TagsController@getKpi')->name('tags.getKpi');
            Route::post('/check-name', 'TagsController@checkName')->name('tags.checkname');

            // Archive tag
            Route::post('/archive-tag', 'TagsController@archiveTag')->name('tags.archive');
            // Remove Condition
            Route::post('/remove-condition', 'TagsController@removeCondtionEntry')->name('tags.removeCondition');
        });
        Route::resource('tags', 'TagsController');

        /**
         * Campaign Management
         */
        Route::group(['prefix' => 'campaigns'], function () {
            // Route::get('/data', 'TagsController@anyData')->name('tags.data');
            // Route::post('/check-name', 'TagsController@checkName')->name('tags.checkname');

            Route::get('/create-email-campaign', 'CampaignController@createEmailCampaign')->name('campaigns.createEmail');
            Route::get('/create-sms-campaign', 'CampaignController@createSMSCampaign')->name('campaigns.createSMS');
        });
        Route::resource('campaigns', 'CampaignController');

        // Subscriptions
		Route::group(['prefix' => 'orders'], function () {
			Route::get('/data', 'OrdersController@anyData')->name('orders.data');
			Route::get('/alldata', 'OrdersController@allData')->name('orders.allData');
			Route::get('/product-details', 'OrdersController@getProductDetail')->name('orders.product.detail');
			Route::post('/product-stock-level', 'OrdersController@checkStockLevel')->name('orders.product.stock');
			Route::get('/company-details', 'OrdersController@getCompanyDetail')->name('orders.company.detail');
			Route::get('/product-delete/{id}/{branch_id}', 'OrdersController@deleteProduct');
			Route::get('/cancel/{id}', 'OrdersController@cancel')->name('order.cancel');
			Route::get('/all', 'OrdersController@all')->name('orders.all');
			Route::post('/apply-code', 'OrdersController@applyCode')->name('orders.applyCode');

            // Sales
            Route::get('/sales', 'OrdersController@salesSummeryView')->name('orders.sales');
            Route::get('/sales-data','OrdersController@salesSummeryReport')->name('orders.salesSummery');

            // Manage stock before submit
            Route::post('/manage_stock_before_submit', 'OrdersController@manageStockBeforeSubmit')->name('orders.manageStockBeforeSubmit');
		});
		Route::resource('orders', 'OrdersController');

        // Reports
		Route::group(['prefix' => 'reports'], function () {
            Route::post('/get_rules_set', 'ReportsController@getModuleRuleSet')->name('reports.getModuleRuleSet');
            Route::post('/group_by_options', 'ReportsController@getGroupBySelectOptions')->name('reports.getGroupByOptions');
			Route::get('/all-data', 'ReportsController@anyData')->name('reports.data');
            Route::post('/ajax-report-run', 'ReportsController@runWithoutSave')->name('reports.runWithoutSave');
            Route::post('/ajax-report-delete', 'ReportsController@ajaxReportDelete')->name('reports.delete');

            Route::post('/check-name', 'ReportsController@checkName')->name('reports.checkname');
			
			Route::get('/appointment-report', 'ReportsController@appointmentReport')->name('reports.appointment');
			Route::get('/client-report', 'ReportsController@clientReport')->name('reports.client');
			Route::get('/stock-report', 'StockMasterController@stockReport')->name('reports.stock');
		});
		Route::resource('reports', 'ReportsController');

        //email template
		Route::group(['prefix' => 'emails'], function () {
            Route::get('data','EmailsController@anydata')->name('emails.data');
            Route::get('logs-data','EmailsController@logsData')->name('emails.logsData');
            Route::get('logs','EmailsController@logs')->name('emails.logs');
		});

        Route::get('emails/edit/{id}','EmailsController@edit');
		Route::resource('emails','EmailsController');

        // commission
		Route::group(['prefix' => 'commissions'], function () {
            Route::get('data','CommissionController@anydata')->name('commissions.data');
            Route::get('details','CommissionController@commissionDetails')->name('commissions.details');
            Route::post('invoice-details','CommissionController@commissionDetailsById')->name('commissions.detailsById');
            Route::post('release-commission','CommissionController@releaseCommission')->name('commissions.release');

            Route::get('distributors-commission','CommissionController@DistributorsCommission')->name('commissions.distributors');
            Route::get('my-commission','CommissionController@myCommission')->name('commissions.myCommission');
		});
		Route::resource('commissions','CommissionController');

        // SMS Routes
		Route::group(['prefix' => 'sms'], function () {
            Route::get('data','SMSController@anydata')->name('sms.data');
            Route::post('/check-name', 'SMSController@checkName')->name('sms.checkname');

            // SMS Configuration
            Route::get('/configuration', 'SMSController@configView')->name('sms.config');
            Route::post('/configuration', 'SMSController@storeConfig')->name('sms.storeConfig');
            Route::post('/test_api', 'SMSController@updateParameters')->name('sms.updateParameters');
            Route::post('/update_parameters', 'SMSController@testAPI')->name('sms.test_api');

            Route::get('logs-data','SMSController@logsData')->name('sms.logsData');
            Route::get('logs','SMSController@logs')->name('sms.logs');
		});
		Route::resource('sms','SMSController');

        // commission
		Route::group(['prefix' => 'plans'], function () {
            Route::get('data','PlansController@anydata')->name('plans.data');
            Route::get('plans-by-name','PlansController@getPlanByName')->name('plans.byname');
		});
		Route::resource('plans','PlansController');

        // Subscriptions Module
        Route::group(['prefix' => 'subscriptions'], function () {
			Route::get('/data', 'SubscriptionsController@anyData')->name('subscriptions.data');
			Route::get('/alldata', 'SubscriptionsController@allData')->name('subscriptions.allData');
			Route::get('/plan-details', 'SubscriptionsController@getPlanDetail')->name('subscriptions.plan.detail');
			Route::get('/company-details', 'SubscriptionsController@getCompanyDetail')->name('subscriptions.company.detail');
			Route::get('/plan-delete/{id}', 'SubscriptionsController@deletePlan');
			Route::get('/cancel/{id}', 'SubscriptionsController@cancel');
			Route::get('/all', 'SubscriptionsController@all')->name('subscriptions.all');

            Route::get('/delete-subscription/{id}', 'SubscriptionsController@destroy')->name('subscriptions.delete');
		});
		Route::resource('subscriptions', 'SubscriptionsController');
    });
});

Route::group(['middleware' => ['auth']], function () {
    Route::get('/dropbox-token', 'CallbackController@dropbox')->name('dropbox.callback');
    Route::get('/googledrive-token', 'CallbackController@googleDrive')->name('googleDrive.callback');
});
