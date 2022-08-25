<?php

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         *  Inquiry Type Module Permissions
         */
        Permission::firstOrCreate([
            'display_name' => 'View Enquiry Type',
            'name' => 'inquiry-type-view',
            'description' => 'Be able to view Inquiry Type listing page',
            'grouping' => 'Inquiry Type',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'create Enquiry Type',
            'name' => 'inquiry-type-create',
            'description' => 'Be able to create enquiry type',
            'grouping' => 'Inquiry Type',
        ]); 
        Permission::firstOrCreate([
            'display_name' => 'Update Enquiry Type',
            'name' => 'inquiry-type-update',
            'description' => 'Be able to update Inquiry Type information',
            'grouping' => 'Inquiry Type',
        ]); 
                
        /**
         *  Status Module Permissions
         */
        Permission::firstOrCreate([
            'display_name' => 'View Status',
            'name' => 'status-view',
            'description' => 'Be able to view status listing page',
            'grouping' => 'Status',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Create Status',
            'name' => 'status-create',
            'description' => 'Be able to create a new status',
            'grouping' => 'Status',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Update status',
            'name' => 'status-update',
            'description' => "Be able to update status information",
            'grouping' => 'Status',
        ]);
        
        /**
         *  Unit Module Permissions
         */
        Permission::firstOrCreate([
            'display_name' => 'View Unit',
            'name' => 'unit-view',
            'description' => 'Be able to view units listing page',
            'grouping' => 'Unit',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Create Unit',
            'name' => 'unit-create',
            'description' => 'Be able to create a new unit',
            'grouping' => 'Unit',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Update Unit',
            'name' => 'unit-update',
            'description' => "Be able to update units information",
            'grouping' => 'Unit',
        ]);
        
        /**
         *  Plan Module Permissions
         */
        Permission::firstOrCreate([
            'display_name' => 'View Plan',
            'name' => 'plan-view',
            'description' => 'Be able to view plans listing page',
            'grouping' => 'Plan',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Create Plan',
            'name' => 'plan-create',
            'description' => 'Be able to create a new plan',
            'grouping' => 'Plan',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Update Plan',
            'name' => 'plan-update',
            'description' => "Be able to update plans information",
            'grouping' => 'Plan',
        ]);
        
        /**
         *  Subscription Module Permissions
         */
        Permission::firstOrCreate([
            'display_name' => 'View Subscription',
            'name' => 'subscription-view',
            'description' => 'Be able to view subscriptions listing page',
            'grouping' => 'Subscription',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Create Subscription',
            'name' => 'subscription-create',
            'description' => 'Be able to create a new subscription',
            'grouping' => 'Subscription',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Update Subscription',
            'name' => 'subscription-update',
            'description' => "Be able to update subscriptions information",
            'grouping' => 'Subscription',
        ]);
        
        /**
         *  Client Module Permissions
         */
        Permission::firstOrCreate([
            'display_name' => 'View Client',
            'name' => 'client-view',
            'description' => 'Be able to view clients listing page',
            'grouping' => 'Client',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Create Client',
            'name' => 'client-create',
            'description' => 'Be able to create a new client',
            'grouping' => 'Client',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Update Client',
            'name' => 'client-update',
            'description' => "Be able to update clients information",
            'grouping' => 'Client',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'View Clients Timeline',
            'name' => 'client-timeline-view',
            'description' => 'Be able to view clients timeline on clients listing page',
            'grouping' => 'Client',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Update Clients Timeline',
            'name' => 'client-timeline-update',
            'description' => 'Be able to update clients timeline',
            'grouping' => 'Client',
        ]);
        
        /**
         *  Salon Module Permissions
         */
        Permission::firstOrCreate([
            'display_name' => 'View Salon',
            'name' => 'salon-view',
            'description' => 'Be able to view salons listing page',
            'grouping' => 'Salon',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Create Salon',
            'name' => 'salon-create',
            'description' => 'Be able to create a new salon',
            'grouping' => 'Salon',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Update Salon',
            'name' => 'salon-update',
            'description' => "Be able to update salons information",
            'grouping' => 'Salon',
        ]);
        
        /**
         *  Inquiry Module Permissions
         */
        Permission::firstOrCreate([
            'display_name' => 'View Inquiry',
            'name' => 'inquiry-view',
            'description' => 'Be able to view inquirys listing page',
            'grouping' => 'Inquiry',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Create Inquiry',
            'name' => 'inquiry-create',
            'description' => 'Be able to create a new inquiry',
            'grouping' => 'Inquiry',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Update Inquiry',
            'name' => 'inquiry-update',
            'description' => "Be able to update inquirys information",
            'grouping' => 'Inquiry',
        ]);
        
        /**
         *  Appointment Module Permissions
         */
        Permission::firstOrCreate([
            'display_name' => 'View Appointment',
            'name' => 'appointment-view',
            'description' => 'Be able to view appointments listing page',
            'grouping' => 'Appointment',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Create Appointment',
            'name' => 'appointment-create',
            'description' => 'Be able to create a new appointment',
            'grouping' => 'Appointment',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Update Appointment',
            'name' => 'appointment-update',
            'description' => "Be able to update appointments information",
            'grouping' => 'Appointment',
        ]);
        
        /**
         *  Category Module Permissions
         */
        Permission::firstOrCreate([
            'display_name' => 'View Category',
            'name' => 'category-view',
            'description' => 'Be able to view categorys listing page',
            'grouping' => 'Category',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Create Category',
            'name' => 'category-create',
            'description' => 'Be able to create a new category',
            'grouping' => 'Category',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Update Category',
            'name' => 'category-update',
            'description' => "Be able to update categorys information",
            'grouping' => 'Category',
        ]);
        
        /**
         *  Product / Service Module Permissions
         */
        Permission::firstOrCreate([
            'display_name' => 'View Product / Service',
            'name' => 'product-view',
            'description' => 'Be able to view product/service listing page',
            'grouping' => 'Product / Service',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Create Product / Service',
            'name' => 'product-create',
            'description' => 'Be able to create a new product/service',
            'grouping' => 'Product / Service',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Update Product / Service',
            'name' => 'product-update',
            'description' => "Be able to update product/service information",
            'grouping' => 'Product / Service',
        ]);
        
        /**
         *  Product / Service Module Permissions
         */
        Permission::firstOrCreate([
            'display_name' => 'View Product / Service',
            'name' => 'product-view',
            'description' => 'Be able to view product/service listing page',
            'grouping' => 'Product / Service',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Create Product / Service',
            'name' => 'product-create',
            'description' => 'Be able to create a new product/service',
            'grouping' => 'Product / Service',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Update Product / Service',
            'name' => 'product-update',
            'description' => "Be able to update product/service information",
            'grouping' => 'Product / Service',
        ]);
        
        /**
         *  Employee Module Permissions
         */
        Permission::firstOrCreate([
            'display_name' => 'View Employee',
            'name' => 'employee-view',
            'description' => 'Be able to view employees listing page',
            'grouping' => 'Employee',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Create Employee',
            'name' => 'employee-create',
            'description' => 'Be able to create a new employee',
            'grouping' => 'Employee',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Update Employee',
            'name' => 'employee-update',
            'description' => "Be able to update employees information",
            'grouping' => 'Employee',
        ]);
        
        /**
         *  Commissions Module
         */
        Permission::firstOrCreate([
            'display_name' => 'View Employees Commission',
            'name' => 'employee-commission-view',
            'description' => 'Be able to view employees commission',
            'grouping' => 'Commission',
        ]);  
        Permission::firstOrCreate([
            'display_name' => 'Release Employees Commission',
            'name' => 'employee-commission-release',
            'description' => 'Be able to release employees commission',
            'grouping' => 'Commission',
        ]);  
        Permission::firstOrCreate([
            'display_name' => 'View Distributor Commission',
            'name' => 'distributor-commission-view',
            'description' => 'Be able to view distributors commission',
            'grouping' => 'Commission',
        ]);  
        Permission::firstOrCreate([
            'display_name' => 'Release Distributor Commission',
            'name' => 'distributor-commission-release',
            'description' => 'Be able to release distributors commission',
            'grouping' => 'Commission',
        ]);  
        
        /**
         *  Inventory Module Permissions
         */
        Permission::firstOrCreate([
            'display_name' => 'View Incoming Stock',
            'name' => 'incoming-stock-view',
            'description' => 'Be able to view incoming socks listing page',
            'grouping' => 'Inventory',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Create Incoming Stock',
            'name' => 'incoming-stock-create',
            'description' => 'Be able to create a new incominig stock',
            'grouping' => 'Inventory',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Update Incoming Stock',
            'name' => 'incoming-stock-update',
            'description' => "Be able to update incoming stock information",
            'grouping' => 'Inventory',
        ]); 
        Permission::firstOrCreate([
            'display_name' => 'View Stock',
            'name' => 'stock-level-view',
            'description' => "Be able to view stock level",
            'grouping' => 'Inventory',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Update Stock',
            'name' => 'stock-level-update',
            'description' => "Be able to update stock level",
            'grouping' => 'Inventory',
        ]); 
        
        /**
         *  Deals Module Permissions
         */
        Permission::firstOrCreate([
            'display_name' => 'View deals & discounts',
            'name' => 'deal-view',
            'description' => 'Be able to view deals & discounts listing page',
            'grouping' => 'Deals & Discount',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Create deals & discount',
            'name' => 'deal-create',
            'description' => 'Be able to create a new deals & discounts',
            'grouping' => 'Deals & Discount',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Update deals & discounts',
            'name' => 'deal-update',
            'description' => "Be able to update deals & discounts information",
            'grouping' => 'Deals & Discount',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Active/Inactive deals & discounts',
            'name' => 'deal-toggle',
            'description' => "Be able to active/inactive deals & discounts",
            'grouping' => 'Deals & Discount',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Archive deals & discount',
            'name' => 'deal-delete',
            'description' => "Be able to archive deals & discount",
            'grouping' => 'Deals & Discount',
        ]);
  
        /**
         *  Orders Menamgement Permissions
         */
        Permission::firstOrCreate([
            'display_name' => 'View orders',
            'name' => 'order-view',
            'description' => 'Be able to view orders listing page',
            'grouping' => 'Orders',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Create orders',
            'name' => 'order-create',
            'description' => 'Be able to create a new orders',
            'grouping' => 'Orders',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Update orders',
            'name' => 'order-update',
            'description' => "Be able to update orders information",
            'grouping' => 'Orders',
        ]); 
        Permission::firstOrCreate([
            'display_name' => 'Cancel orders',
            'name' => 'order-delete',
            'description' => "Be able to cancel orders",
            'grouping' => 'Orders',
        ]);
        
        /**
         * Daybook module permissions
         */
        Permission::firstOrCreate([
            'display_name' => 'View Daybook',
            'name' => 'daybook-view',
            'description' => 'Be able to view daybook summery page',
            'grouping' => 'Daybook',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Cash In Entry',
            'name' => 'daybook-cash-in-entry',
            'description' => 'Be able to do cash in entry',
            'grouping' => 'Daybook',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Cash Out Entry',
            'name' => 'daybook-cash-out-entry',
            'description' => 'Be able to do cash out entry',
            'grouping' => 'Daybook',
        ]); 
   
        /**
         *  Holiday Module Permissions
         */
        Permission::firstOrCreate([
            'display_name' => 'View Holidays',
            'name' => 'holiday-view',
            'description' => 'Be able to view holidays listing page',
            'grouping' => 'Holiday',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Create Holiday',
            'name' => 'holiday-create',
            'description' => 'Be able to create a new holiday',
            'grouping' => 'Holiday',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Update holiday',
            'name' => 'holiday-update',
            'description' => "Be able to update holiday information",
            'grouping' => 'Holiday',
        ]); 
                
        /**
         *  Email Template Permissions
         */
        Permission::firstOrCreate([
            'display_name' => 'View Email Template',
            'name' => 'email-template-view',
            'description' => 'Be able to view email templates listing page',
            'grouping' => 'Email Template',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Create Email Template',
            'name' => 'email-template-create',
            'description' => 'Be able to create a new email template',
            'grouping' => 'Email Template',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Edit Email Template',
            'name' => 'email-template-update',
            'description' => 'Be able to create a email template',
            'grouping' => 'Email Template',
        ]); 
                
        /**
         *  SMS Template Permissions
         */
        Permission::firstOrCreate([
            'display_name' => 'View SMS Template',
            'name' => 'sms-view',
            'description' => 'Be able to view SMS templates listing',
            'grouping' => 'SMS Template',
        ]);   
        Permission::firstOrCreate([
            'display_name' => 'Create SMS Template',
            'name' => 'sms-create',
            'description' => 'Be able to create SMS templates',
            'grouping' => 'SMS Template',
        ]);   
        Permission::firstOrCreate([
            'display_name' => 'Edit SMS Template',
            'name' => 'sms-edit',
            'description' => 'Be able to edit SMS templates',
            'grouping' => 'SMS Template',
        ]);
    
        /**
         *  Reports Module Permissions
         */
        Permission::firstOrCreate([
            'display_name' => 'View & Run Reports',
            'name' => 'reports-view',
            'description' => 'Be able to view & run reports',
            'grouping' => 'Report Builder',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Create Reports',
            'name' => 'reports-create',
            'description' => 'Be able to create a report',
            'grouping' => 'Report Builder',
        ]);   
        Permission::firstOrCreate([
            'display_name' => 'Edit Reports',
            'name' => 'reports-edit',
            'description' => 'Be able to edit a report',
            'grouping' => 'Report Builder',
        ]);   

        /**
         *  Logs page
         */
        Permission::firstOrCreate([
            'display_name' => 'View Email Logs',
            'name' => 'view-email-logs',
            'description' => 'Be able to view email logs',
            'grouping' => 'Logs',
        ]);   
        Permission::firstOrCreate([
            'display_name' => 'View SMS Logs',
            'name' => 'view-sms-logs',
            'description' => 'Be able to view sms logs',
            'grouping' => 'Logs',
        ]);   

        /**
         *  Branch Module Permissions
         */
        Permission::firstOrCreate([
            'display_name' => 'View branch',
            'name' => 'branch-view',
            'description' => 'Be able to view branches listing page',
            'grouping' => 'Branch',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Create Branch',
            'name' => 'branch-create',
            'description' => 'Be able to create a new branch',
            'grouping' => 'Branch',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Update Branch',
            'name' => 'branch-update',
            'description' => "Be able to update branches information",
            'grouping' => 'Branch',
        ]); 

        /**
         *  Branch Module Permissions
         */
        Permission::firstOrCreate([
            'display_name' => 'View vendor',
            'name' => 'vendor-view',
            'description' => 'Be able to view vendores listing page',
            'grouping' => 'Vendor',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Create vendor',
            'name' => 'vendor-create',
            'description' => 'Be able to create a new vendor',
            'grouping' => 'Vendor',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Update vendor',
            'name' => 'vendor-update',
            'description' => "Be able to update vendores information",
            'grouping' => 'Vendor',
        ]); 
    }
}
