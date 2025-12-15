<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Specific Language Lines
    |--------------------------------------------------------------------------
    */

    // Navigation
    'nav' => [
        'dashboard' => 'Dashboard',
        'shops' => 'Shops',
        'products' => 'Products',
        'orders' => 'Orders',
        'clients' => 'Clients',
        'settings' => 'Settings',
        'logout' => 'Logout',
        'profile' => 'Profile',
        'admin' => 'Admin Panel',
        'users' => 'Users',
    ],

    // Dashboard
    'dashboard' => [
        'welcome' => 'Welcome to Telegram Shops',
        'total_revenue' => 'Total Revenue',
        'total_orders' => 'Total Orders',
        'pending_orders' => 'Pending Orders',
        'average_order' => 'Average Order',
        'orders_chart' => 'Orders Last 7 Days',
        'top_products' => 'Top Products',
        'recent_orders' => 'Recent Orders',
        'no_orders' => 'No orders yet',
    ],

    // Shops
    'shops' => [
        'my_shops' => 'My Shops',
        'create_shop' => 'Create Shop',
        'edit_shop' => 'Edit Shop',
        'shop_name' => 'Shop Name',
        'bot_token' => 'Telegram Bot Token',
        'welcome_message' => 'Welcome Message',
        'footer_message' => 'Footer Message',
        'status' => 'Status',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'blocked' => 'Blocked',
    ],

    // Products
    'products' => [
        'title' => 'Products',
        'all_products' => 'All Products',
        'create_product' => 'Create Product',
        'edit_product' => 'Edit Product',
        'add_product' => 'Add Product',
        'add_first_product' => 'Add Your First Product',
        'product_name' => 'Product Name',
        'name' => 'Name',
        'description' => 'Description',
        'price' => 'Price',
        'category' => 'Category',
        'image' => 'Image',
        'stock' => 'Stock',
        'stock_quantity' => 'Stock Quantity',
        'in_stock' => 'In Stock',
        'out_of_stock' => 'Out of Stock',
        'no_products' => 'No products',
        'get_started' => 'Get started by adding a new product.',
    ],

    // Orders
    'orders' => [
        'all_orders' => 'All Orders',
        'order_details' => 'Order Details',
        'order_number' => 'Order #',
        'customer_name' => 'Customer Name',
        'customer_phone' => 'Customer Phone',
        'customer_email' => 'Customer Email',
        'total_amount' => 'Total Amount',
        'status' => 'Status',
        'pending' => 'Pending',
        'processing' => 'Processing',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'refunded' => 'Refunded',
        'created_at' => 'Created At',
        'comment' => 'Comment',
    ],

    // Admin
    'admin' => [
        'dashboard' => 'Admin Dashboard',
        'user_management' => 'User Management',
        'shop_management' => 'Shop Management',
        'total_users' => 'Total Users',
        'total_shops' => 'Total Shops',
        'total_revenue' => 'Total Revenue',
        'search' => 'Search',
        'actions' => 'Actions',
        'view' => 'View',
        'edit' => 'Edit',
        'delete' => 'Delete',
    ],

    // Plans
    'plans' => [
        'free' => 'Free',
        'base' => 'Base',
        'pro' => 'Pro',
        'choose_plan' => 'Choose Plan',
        'current_plan' => 'Current Plan',
        'upgrade' => 'Upgrade',
        'month' => 'month',
    ],

    // Common
    'common' => [
        'save' => 'Save',
        'cancel' => 'Cancel',
        'delete' => 'Delete',
        'edit' => 'Edit',
        'create' => 'Create',
        'search' => 'Search',
        'filter' => 'Filter',
        'export' => 'Export',
        'import' => 'Import',
        'yes' => 'Yes',
        'no' => 'No',
        'confirm' => 'Confirm',
        'close' => 'Close',
        'back' => 'Back',
        'next' => 'Next',
        'previous' => 'Previous',
        'loading' => 'Loading...',
        'status' => 'Status',
        'actions' => 'Actions',
        'currency' => 'USD',
        'language' => 'Language',
    ],

    // Messages
    'messages' => [
        'success' => 'Operation completed successfully',
        'error' => 'An error occurred',
        'confirm_delete' => 'Are you sure you want to delete this?',
        'no_data' => 'No data available',
    ],

    // Welcome Page
    'welcome' => [
        'title' => 'Create Your Telegram Shop in Minutes',
        'subtitle' => 'Build and manage your online store directly in Telegram. No coding required. Start selling products to millions of Telegram users today.',
        'start_free_trial' => 'Start Free Trial',
        'learn_more' => 'Learn More',
        'sign_in' => 'Sign In',
        'get_started' => 'Get Started',
        'my_shops' => 'My Shops',
        'dashboard' => 'Dashboard',

        // Features
        'features_title' => 'Everything You Need to Sell on Telegram',
        'features_subtitle' => 'Powerful features to manage your online business',

        'feature_catalog_title' => 'Product Catalog',
        'feature_catalog_desc' => 'Create unlimited products with categories, descriptions, and prices. Showcase your entire inventory beautifully.',

        'feature_cart_title' => 'Smart Shopping Cart',
        'feature_cart_desc' => 'Built-in cart system for your Telegram bot. Customers can easily browse and order products.',

        'feature_orders_title' => 'Order Management',
        'feature_orders_desc' => 'Track all orders in real-time. Update statuses, view customer details, and manage your sales efficiently.',

        'feature_ai_title' => 'AI-Powered Descriptions',
        'feature_ai_desc' => 'Generate compelling product descriptions automatically using AI. Save time and boost sales.',

        'feature_payment_title' => 'Payment Integration',
        'feature_payment_desc' => 'Accept payments seamlessly with Stripe integration. Secure and reliable payment processing.',

        'feature_language_title' => 'Multi-Language',
        'feature_language_desc' => 'Support for multiple languages. Reach customers worldwide with English and Ukrainian support.',

        // Pricing
        'pricing_title' => 'Simple, Transparent Pricing',
        'pricing_subtitle' => 'Choose the perfect plan for your business',

        'plan_free_title' => 'Free',
        'plan_free_subtitle' => 'Perfect for getting started',
        'plan_free_price' => '$0',
        'plan_free_feature_1' => '1 Shop',
        'plan_free_feature_2' => 'Up to 50 products',
        'plan_free_feature_3' => 'Basic support',

        'plan_base_title' => 'Base',
        'plan_base_subtitle' => 'For growing businesses',
        'plan_base_price' => '$19',
        'plan_base_feature_1' => '3 Shops',
        'plan_base_feature_2' => 'Up to 500 products',
        'plan_base_feature_3' => 'AI descriptions',
        'plan_base_feature_4' => 'Priority support',

        'plan_pro_title' => 'Pro',
        'plan_pro_subtitle' => 'For power sellers',
        'plan_pro_price' => '$49',
        'plan_pro_badge' => 'Popular',
        'plan_pro_feature_1' => 'Unlimited shops',
        'plan_pro_feature_2' => 'Unlimited products',
        'plan_pro_feature_3' => 'Advanced AI features',
        'plan_pro_feature_4' => 'Premium support',
        'plan_pro_feature_5' => 'Custom branding',

        'per_month' => '/month',

        // CTA
        'cta_title' => 'Ready to get started?',
        'cta_subtitle' => 'Create your first shop today.',

        // Footer
        'footer_copyright' => 'All rights reserved.',
    ],
];
