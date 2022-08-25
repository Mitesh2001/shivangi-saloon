<?php

use Illuminate\Database\Seeder;

class ExtraRolePermissionTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         *  Users Module Permissions
         */
        Permission::firstOrCreate([
            'display_name' => 'View Users',
            'name' => 'users-view',
            'description' => 'Be able to view users listing page',
            'grouping' => 'user',
        ]);

        /**
         *  Client Module Permissions
         */
        Permission::firstOrCreate([
            'display_name' => 'View Clients',
            'name' => 'client-view',
            'description' => 'Be able to view clients listing page',
            'grouping' => 'client',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'View Clients Timeline',
            'name' => 'client-timeline-view',
            'description' => 'Be able to view clients timeline on clients listing page',
            'grouping' => 'client',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Update Clients Timeline',
            'name' => 'client-timeline-update',
            'description' => 'Be able to update clients timeline',
            'grouping' => 'client',
        ]);


        /**
         *  Role Module Permissions
         */
        Permission::firstOrCreate([
            'display_name' => 'View Role',
            'name' => 'role-view',
            'description' => 'Be able to view roles listing page',
            'grouping' => 'role',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Create Role',
            'name' => 'role-create',
            'description' => 'Be able to create a new role',
            'grouping' => 'role',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Update Role',
            'name' => 'role-update',
            'description' => "Be able to update role's permissions",
            'grouping' => 'role',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Delete Role',
            'name' => 'role-delete',
            'description' => "Be able to delete role",
            'grouping' => 'role',
        ]);

        /**
         *  Vendor Module Permissions
         */
        Permission::firstOrCreate([
            'display_name' => 'View Vendor',
            'name' => 'vendor-view',
            'description' => 'Be able to view vendors listing page',
            'grouping' => 'vendor',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Create Vendor',
            'name' => 'vendor-create',
            'description' => 'Be able to create a new vendor',
            'grouping' => 'vendor',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Update Vendor',
            'name' => 'vendor-update',
            'description' => "Be able to update vendor's information",
            'grouping' => 'vendor',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Delete Vendor',
            'name' => 'vendor-delete',
            'description' => "Be able to delete vendor",
            'grouping' => 'vendor',
        ]);

        /**
         *  Enquiry Type Module Permissions
         */
        Permission::firstOrCreate([
            'display_name' => 'View Enquiry Type',
            'name' => 'enquiry-type-view',
            'description' => 'Be able to view enquiry type listing page',
            'grouping' => 'enquiry-type',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'create Enquiry Type',
            'name' => 'enquiry-type-create',
            'description' => 'Be able to create enquiry type',
            'grouping' => 'enquiry-type',
        ]); 
        Permission::firstOrCreate([
            'display_name' => 'Update Enquiry Type',
            'name' => 'enquiry-type-update',
            'description' => 'Be able to update enquiry type information',
            'grouping' => 'enquiry-type',
        ]); 
        Permission::firstOrCreate([
            'display_name' => 'Delete Enquiry Type',
            'name' => 'enquiry-type-delete',
            'description' => 'Be able to delete enquiry type information',
            'grouping' => 'enquiry-type',
        ]); 

        
        /**
         *  Status Module Permissions
         */
        Permission::firstOrCreate([
            'display_name' => 'View Status',
            'name' => 'status-view',
            'description' => 'Be able to view statuses listing page',
            'grouping' => 'status',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Create Status',
            'name' => 'status-create',
            'description' => 'Be able to create a new status',
            'grouping' => 'status',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Update status',
            'name' => 'status-update',
            'description' => "Be able to update statuses information",
            'grouping' => 'status',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Delete Status',
            'name' => 'status-delete',
            'description' => "Be able to delete status",
            'grouping' => 'status',
        ]);
        
        
        /**
         *  Branch Module Permissions
         */
        Permission::firstOrCreate([
            'display_name' => 'View branch',
            'name' => 'branch-view',
            'description' => 'Be able to view branches listing page',
            'grouping' => 'branch',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Create Branch',
            'name' => 'branch-create',
            'description' => 'Be able to create a new branch',
            'grouping' => 'branch',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Update Branch',
            'name' => 'branch-update',
            'description' => "Be able to update branches information",
            'grouping' => 'branch',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Delete Branch',
            'name' => 'branch-delete',
            'description' => "Be able to delete branch",
            'grouping' => 'branch',
        ]);
        

        /**
         *  Unit Module Permissions
         */
        Permission::firstOrCreate([
            'display_name' => 'View Unit',
            'name' => 'unit-view',
            'description' => 'Be able to view units listing page',
            'grouping' => 'unit',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Create Unit',
            'name' => 'unit-create',
            'description' => 'Be able to create a new unit',
            'grouping' => 'unit',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Update Unit',
            'name' => 'unit-update',
            'description' => "Be able to update units information",
            'grouping' => 'unit',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Delete Unit',
            'name' => 'unit-delete',
            'description' => "Be able to delete unit",
            'grouping' => 'unit',
        ]);
        

        /**
         *  Package Module Permissions
         */
        Permission::firstOrCreate([
            'display_name' => 'View Package',
            'name' => 'package-view',
            'description' => 'Be able to view packages listing page',
            'grouping' => 'package',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Create Package',
            'name' => 'package-create',
            'description' => 'Be able to create a new package',
            'grouping' => 'package',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Update Package',
            'name' => 'package-update',
            'description' => "Be able to update packages information",
            'grouping' => 'package',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Delete Package',
            'name' => 'package-delete',
            'description' => "Be able to delete package",
            'grouping' => 'package',
        ]);


        /**
         *  Enquiry Module Permissions
         */
        Permission::firstOrCreate([
            'display_name' => 'View Enquiry',
            'name' => 'enquiry-view',
            'description' => 'Be able to view enquiries listing page',
            'grouping' => 'enquiry',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Create Enquiry',
            'name' => 'enquiry-create',
            'description' => 'Be able to create a new enquiry',
            'grouping' => 'enquiry',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Update Enquiry',
            'name' => 'enquiry-update',
            'description' => "Be able to update enquiries information",
            'grouping' => 'enquiry',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Delete Enquiry',
            'name' => 'enquiry-delete',
            'description' => "Be able to delete enquiry",
            'grouping' => 'enquiry',
        ]);


        /**
         *  Category Module Permissions
         */
        Permission::firstOrCreate([
            'display_name' => 'View Category',
            'name' => 'category-view',
            'description' => 'Be able to view categories listing page',
            'grouping' => 'category',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Create Category',
            'name' => 'category-create',
            'description' => 'Be able to create a new category',
            'grouping' => 'category',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Update Category',
            'name' => 'category-update',
            'description' => "Be able to update categories information",
            'grouping' => 'category',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Delete Category',
            'name' => 'category-delete',
            'description' => "Be able to delete category",
            'grouping' => 'category',
        ]);
 

        /**
         *  Product Module Permissions
         */
        Permission::firstOrCreate([
            'display_name' => 'View Product',
            'name' => 'product-view',
            'description' => 'Be able to view products listing page',
            'grouping' => 'product',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Create Product',
            'name' => 'product-create',
            'description' => 'Be able to create a new product',
            'grouping' => 'product',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Update Product',
            'name' => 'product-update',
            'description' => "Be able to update products information",
            'grouping' => 'product',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Delete Product',
            'name' => 'product-delete',
            'description' => "Be able to delete product",
            'grouping' => 'product',
        ]); 
		Permission::firstOrCreate([
            'display_name' => 'View Employee',
            'name' => 'employee-view',
            'description' => 'Be able to view employees listing page',
            'grouping' => 'employee',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Create Employee',
            'name' => 'employee-create',
            'description' => 'Be able to create a new employee',
            'grouping' => 'employee',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Update Employee',
            'name' => 'employee-update',
            'description' => "Be able to update employees information",
            'grouping' => 'employee',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Delete Employee',
            'name' => 'employee-delete',
            'description' => "Be able to delete employee",
            'grouping' => 'employee',
        ]); 

        /**
         *  Inventory Module Permissions
         */
        Permission::firstOrCreate([
            'display_name' => 'View Incoming Stock',
            'name' => 'incoming-stock-view',
            'description' => 'Be able to view incoming socks listing page',
            'grouping' => 'inventory',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Create Incoming Stock',
            'name' => 'incoming-stock-create',
            'description' => 'Be able to create a new incominig stock',
            'grouping' => 'inventory',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Update Incoming Stock',
            'name' => 'incoming-stock-update',
            'description' => "Be able to update incoming stock information",
            'grouping' => 'inventory',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Delete Incoming Stock',
            'name' => 'incoming-stock-delete',
            'description' => "Be able to delete incoming stock",
            'grouping' => 'inventory',
        ]); 
        Permission::firstOrCreate([
            'display_name' => 'View Stock',
            'name' => 'stock-level-view',
            'description' => "Be able to view stock level",
            'grouping' => 'inventory',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Update Stock',
            'name' => 'stock-level-update',
            'description' => "Be able to update stock level",
            'grouping' => 'inventory',
        ]); 

        /**
         * Daybook module permissions
         */
        Permission::firstOrCreate([
            'display_name' => 'View Daybook',
            'name' => 'daybook-view',
            'description' => 'Be able to view daybook summery page',
            'grouping' => 'daybook',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Cash In Entry',
            'name' => 'daybook-cash-in-entry',
            'description' => 'Be able to do cash in entry',
            'grouping' => 'daybook',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Cash Out Entry',
            'name' => 'daybook-cash-out-entry',
            'description' => 'Be able to do cash out entry',
            'grouping' => 'daybook',
        ]);
         
        
        /**
         *  Holiday Module Permissions
         */
        Permission::firstOrCreate([
            'display_name' => 'View Holidays',
            'name' => 'holiday-view',
            'description' => 'Be able to view holidays listing page',
            'grouping' => 'holiday',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Create Holiday',
            'name' => 'holiday-create',
            'description' => 'Be able to create a new holiday',
            'grouping' => 'holiday',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Update holiday',
            'name' => 'holiday-update',
            'description' => "Be able to update holiday information",
            'grouping' => 'holiday',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Delete holiday',
            'name' => 'holiday-delete',
            'description' => "Be able to delete holiday",
            'grouping' => 'holiday',
        ]);
        
        /**
         *  Deals Module Permissions
         */
        Permission::firstOrCreate([
            'display_name' => 'View deals & discounts',
            'name' => 'deal-view',
            'description' => 'Be able to view deals & discounts listing page',
            'grouping' => 'deals',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Create deals & discount',
            'name' => 'deal-create',
            'description' => 'Be able to create a new deals & discounts',
            'grouping' => 'deals',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Update deals & discounts',
            'name' => 'deal-update',
            'description' => "Be able to update deals & discounts information",
            'grouping' => 'deals',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Active/Inactive deals & discounts',
            'name' => 'deal-toggle',
            'description' => "Be able to active/inactive deals & discounts",
            'grouping' => 'deals',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Archive deals & discount',
            'name' => 'deal-delete',
            'description' => "Be able to archive deals & discount",
            'grouping' => 'deals',
        ]);
        
        /**
         *  Tag Management Permissions
         */
        Permission::firstOrCreate([
            'display_name' => 'View tags',
            'name' => 'tag-view',
            'description' => 'Be able to view tags listing page',
            'grouping' => 'tags',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Create tags',
            'name' => 'tag-create',
            'description' => 'Be able to create a new tags',
            'grouping' => 'tags',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Update tags',
            'name' => 'tag-update',
            'description' => "Be able to update tags information",
            'grouping' => 'tags',
        ]); 
        Permission::firstOrCreate([
            'display_name' => 'Archive tags',
            'name' => 'tag-delete',
            'description' => "Be able to archive tags",
            'grouping' => 'tags',
        ]);
        
        /**
         *  Orders Menamgement Permissions
         */
        Permission::firstOrCreate([
            'display_name' => 'View orders',
            'name' => 'order-view',
            'description' => 'Be able to view orders listing page',
            'grouping' => 'orders',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Create orders',
            'name' => 'order-create',
            'description' => 'Be able to create a new orders',
            'grouping' => 'orders',
        ]);
        Permission::firstOrCreate([
            'display_name' => 'Update orders',
            'name' => 'order-update',
            'description' => "Be able to update orders information",
            'grouping' => 'orders',
        ]); 
        Permission::firstOrCreate([
            'display_name' => 'Cancel orders',
            'name' => 'order-delete',
            'description' => "Be able to cancel orders",
            'grouping' => 'orders',
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
            'display_name' => 'Edit Email Template',
            'name' => 'email-template-edit',
            'description' => 'Be able to create a email template',
            'grouping' => 'Email Template',
        ]); 
    }
}
