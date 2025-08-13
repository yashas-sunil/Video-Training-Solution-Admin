<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#61-title
    |
    */

    'title' => 'EduEdgePro',
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    |
    | Here you can activate the favicon.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#62-favicon
    |
    */

    'use_ico_only' => false,
    'use_full_favicon' => false,

    /*
    |--------------------------------------------------------------------------
    | Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#63-logo
    |
    */

    'logo' => '<img src="../images/logo.png">',
    'logo_img' => '',
    'logo_img_class' => 'brand-image img-circle elevation-3 d-none',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'EduEdgePro',

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    |
    | Here you can activate and change the user menu.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#64-user-menu
    |
    */

    'usermenu_enabled' => true,
    'usermenu_header' => false,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => false,
    'usermenu_desc' => false,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#65-layout
    |
    */

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => null,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => null,

    /*
    |--------------------------------------------------------------------------
    | Extra Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#66-classes
    |
    */

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_header' => 'container-fluid',
    'classes_content' => 'container-fluid',
    'classes_sidebar' => 'sidebar-dark-primary elevation-0',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand-md',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#67-sidebar
    |
    */

    'sidebar_mini' => true,
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#68-control-sidebar-right-sidebar
    |
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#69-urls
    |
    */

    'use_route_url' => false,

    'dashboard_url' => 'home',

    'logout_url' => 'logout',

    'login_url' => 'login',

    // 'register_url' => 'register',
    'register_url' => null,


    'password_reset_url' => 'password/reset',

    'password_email_url' => 'password/email',

    'profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Laravel Mix
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Mix option for the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#610-laravel-mix
    |
    */

    'enabled_laravel_mix' => false,

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#611-menu
    |
    */

    'menu' => [
        [
            'text' => 'Dashboard',
            'url'  => 'home',
            'icon'  => 'fas fa-tachometer-alt',
        ],
        [
            'text'        => 'Admin',
            'url'         => 'admins',
            'icon'        => 'far fa-user',
        ],
        //  [
        //     'text'        => 'Ask A Question',
        //     'url'         => 'questions/professors',
        //     'icon'        => 'fa fa-question'
        // ],
        // [
        //     'text'        => 'Associates',
        //     'url'         => 'agents',
        //     'icon'        => 'fa fa-users',
        // ],
        // [
        //     'text'    => 'Blogs',
        //     'icon'    => 'fa fa-file',
        //     'submenu' => [
        //         [
        //             'text'        => 'Blogs',
        //             'url'         => 'blogs',
        //             'icon'        => 'fa fa-blog',
        //         ],
        //         [
        //             'text'        => 'Categories',
        //             'url'         => 'blogs/categories',
        //         ],
        //         [
        //             'text'        => 'Tags',
        //             'url'         => 'blogs/tags',
        //         ],
        //     ],
        // ],
        // [
        //     'text'        => 'Call Requests',
        //     'url'         => 'call-requests',
        //     'icon'        => 'fa fa-phone',
        // ],
        // [
        //     'text'    => 'Campaigns',
        //     'icon'    => 'fa fa-bullhorn',
        //     'submenu' => [
              
        //         [
        //             'text'        => 'Campaign Registration',
        //             'url'         => 'campaign-registrations'
        //         ],
        //         [
        //             'text'        => 'Spin Wheel Campaign',
        //             'url'         => 'spin-wheel-campaigns',
        //         ]
        //     ],
        // ],
        // [
        //     'text'        => 'Can Not Find Enquiry',
        //     'url'         => 'can-not-find-enquire',
        //     'icon'        => 'fas fa-envelope'
        // ],
        // [
        //     'text'        => 'CSEET Students',
        //     'url'         => 'cseet-students',
        //     'icon'        => 'fa fa-users'
        // ],
        // [
        //     'text'    => ' Deal Of the day',
        //     'icon'    => 'fa fa-book-reader',
        //     'url'     => 'deal_of_day',

        // ],
        // [
        //     'text'        => 'Dispatch',
        //     'url'         => 'purchases',
        //     'icon'        => 'far fa-paper-plane',
        // ],
        // [
        //     'text'        => 'Free Resources',
        //     'url'         => 'free-resource',
        //     'icon'        => 'far fa-fw fa-file',
        // ],
        // [
        //     'text'        => 'Home Page',
        //     'icon'        => 'far fa-images',
        //     'submenu'     =>[
        //         [
        //             'text'        => 'Banners',
        //             'url'         => 'banners',
        //         ],
        //         [
        //             'text'        => 'Count Setting',
        //             'url'         => 'count-setting',
        //             ],
        //         [
        //             'text'        => 'Sections',
        //             'url'         => 'sections',
    
        //         ],
                

        //     ]
        // ],
        // [
        //     'text'        => 'Import Students',
        //     'url'         => 'students/import',
        //     'icon'        => 'fa fa-users',
        // ],
        // [
        //     'text'        => 'Invoice Regenerate',
        //     'url'         => 'invoiceRegenerate',
        //     'icon'        => 'fa fa-file'
        // ],
        [
            'text'    => 'Masters',
            'icon'    => 'fas fa-users-cog',
            'submenu' => [
                // [
                //     'text'        => 'Chapters',
                //     'url'         => 'chapters'
                // ],
                // [
                //     'text'        => 'Courier Partners',
                //     'url'         => 'couriers'
                // ],
                [
                    'text'        => 'Courses',
                    'url'         => 'courses'
                ],
                   [
                    'text'        => 'Assigned-Courses',
                    'url'         => 'assigned-courses'
                ],
                // [
                //     'text'        => 'Levels',
                //     'url'         => 'levels'
                // ],
                // // [
                //     'text'        => 'Modules',
                //     'url'         => 'modules'
                // ],
                // [
                //     'text'        => 'SMS Master',
                //     'url'         => 'sms'
                // ],
                // [
                //     'text'        => 'Subjects',
                //     'url'         => 'subjects'
                // ],
                // [
                //     'text'        => 'Study Materials',
                //     'url'         => 'study-materials'
                // ],
                // [
                //     'text'        => 'Types',
                //     'url'         => 'type'
                // ],
                   
            ],
        ],
        // [
        //     'text'    => 'Notifications',
        //     'icon'    => 'fas fa-bell',
        //     'submenu' => [
        //         [
        //             'text'        => 'Custom Notifications',
        //             'url'         => 'custom-notifications'
        //         ],
        //         [
        //             'text'        => 'High Priority Notifications',
        //             'url'         => 'high-priority-notifications'
        //         ],
        //     ],
        // ],
        // [
        //     'text'    => 'Offers',
        //     'icon'    => 'fas fa-fw fa-tags',
        //     'submenu' => [
        //         [
        //             'text'        => 'Create Coupons',
        //             'url'         => 'coupons/create',
        //         ],
        //         [
        //             'text'        => 'Holiday Scheme',
        //             'url'         => 'holiday-scheme',
        //         ],
        //         [
        //             'text'        => 'Holiday Scheme Usage',
        //             'url'         => 'holiday-scheme-usage',
        //         ],
        //         [
        //             'text'        => 'J-Koin Settings',
        //             'url'         => 'j-money-settings/create',
        //         ],
        //         [
        //             'text'        => 'J-Koin Usage',
        //             'url'         => 'j-money',
        //         ],
        //         [
        //             'text'        => 'List Coupons',
        //             'url'         => 'coupons',
        //         ],
               
        //     ],
        // ],
        // [
        //     'text' => 'Orders',
        //     'url'  => 'agent-orders',
        //     'icon'  => 'fas fa-user',
        // ],
//         [
//             'text'    => 'Packages',
//             'icon'    => 'fa fa-book-reader',
//             'submenu' => [
// //                [
// //                    'text'        => 'All Packages',
// //                    'url'         => 'packages'
// //                ],
//                 [
//                     'text' => 'All packages',
//                     'url'  => 'all-packages'
//                 ],
//                 [
//                     'text' => 'Archived packages',
//                     'url'  => 'archived-packages'

//                 ],
//                 [
//                     'text'        => 'Chapter Level Package',
//                     'url'         => 'packages/chapter/create'
//                 ],
//                 [
//                     'text'        => 'Customize Package',
//                     'url'         => 'packages/customize/create'
//                 ],
//                 [
//                     'text' => 'Drafted Packages',
//                     'url'  => 'drafted-packages'

//                 ],
//                 [
//                     'text'        => 'Package Extensions',
//                     'url'         => 'package-extensions'
//                 ],
//                 [
//                     'text'        => 'Professor Revenues',
//                     'url'         => 'packages/professor/revenues'
//                 ],
//                 [
//                     'text' => 'Published Packages',
//                     'url'  => 'published-packages'

//                 ],
// //                [
// //                    'text' => 'Archived packages',
// //                    'url'  => 'archived-packages'
// //
// //                ],
               
//                 [
//                     'text'        => 'Subject Level Package',
//                     'url'         => 'packages/subject/create'
//                 ],
               
                
              
//             ],
//         ],
//         [
//             'text'        => 'Prepaid',
//             'url'         => 'prepaid-packages',
//             'icon'        => 'fas fa-file'
//         ],
//         [
//             'text'        => 'Professors',
//             'url'         => 'professors',
//             'icon'        => 'fa fa-users',
//         ],
//         [
//             'text'    => 'Quiz',
//             'icon'    => 'fa fa-question',
//             'submenu' => [
//                 [
//                     'text'        => 'Master',
//                     'icon'        => 'fas fa-table',
//                     'submenu' => [
//                 [
//                     'text'        => 'Instruction',
//                     'icon'        => 'far fa-circle',
//                     'url'         => 'quiz/instruction',
//                 ],],
//                 ],
//                 [
//                     'text'        => 'Paragraph',
//                     'icon'        => 'fas fa-paragraph',
//                     'url'         => 'quiz/paragraph',
//                 ],
//                 [
//                     'text'        => 'Question Bank',
//                     'icon'        => 'fas fa-money-check',
//                     'url'         => 'quiz/question',
//                 ],
//                 // [
//                 //     'text'        => 'Modules',
//                 //     'icon'        => 'fas fa-archive',
//                 //     'url'         => 'quiz/module',
//                 // ],
//                 [
//                     'text'        => 'Test',
//                     'icon'        => 'fas fa-box',
//                     'url'         => 'quiz/test',
//                 ],
//                 // [
//                 //     'text'        => 'Event',
//                 //     'icon'        => 'fas fa-calendar-week',
//                 //     'url'         => 'quiz/event',
//                 // ],
//             ],
//         ],
//         [
//             'text'        => 'Refund',
//             'url'         => 'refunds',
//             'icon'        => 'fa fa-users',
//         ],
//         [
//             'text'    => 'Reports',
//             'icon'    => 'fa fa-file',
//             'submenu' => [
//                 [

//                     'text'        => 'Admin Activity',
//                     'url'         => 'admin-activity',
//                 ],
//                 [

//                     'text'        => 'Admin Activity Action',
//                     'url'         => 'admin-activity-action',
//                 ],
//                 [
//                     'text'        => 'Associate Orders',
//                     'url'         => 'reports/associate-orders',
//                 ],
//                 [
//                     'text'        => 'Call Requests',
//                     'url'         => 'call-requests',
//                 ],
//                 [
//                     'text'        => 'Email Log Report',
//                     'url'         => 'email-log',
//                 ],
//                 [
//                     'text'        => 'Imported Students',
//                     'url'         => 'reports/imported-students',
//                 ],
//                 [

//                     'text'        => 'Mobile Sign-Up Users',
//                     'url'         => 'user-list',

//                 ],
//                 [
//                     'text'        => 'Order Revenue',
//                     'url'         => 'order-revenue',
//                 ],
//                 [
//                     'text'        => 'Orders',
//                     'url'         => 'orders',
//                 ],
//                 [
//                     'text'        => 'Packages',
//                     'url'         => 'package-reports',
//                 ],
//                 [
//                     'text'        => 'Payments',
//                     'url'         => 'reports/payments',
//                 ],
//                 [
//                     'text'        => 'Professor revenues',
//                     'url'         => 'professor-revenues',
//                 ],
//                 [
//                     'text'        => 'Sales',
//                     'url'         => 'sales'
//                 ],
//                 [

//                     'text'        => 'Sales Revenue',
//                     'url'         => 'salesrevenue',

//                 ],
//                 [

//                     'text'        => 'Student Analytics',
//                     'url'         => 'student-analytics',
//                 ],
//                 [
//                     'text'        => 'Students',
//                     'url'         => 'students',
//                 ],
//                 [

//                     'text'        => 'Thane Vaibhav Registration',
//                     'url'         => 'reports/vaibhav-registration-details',

//                 ],
// //                [
// //                    'text'        => 'Agents',
// //                    'url'         => 'reports/agents',
// //                ],
              
//                 [
//                     'text'        => 'Third Party Orders',
//                     'url'         => 'reports/third-party-orders',
//                 ],
              
// //                [
// //                    'text'        => 'Professor Payouts',
// //                    'url'         => 'reports/professor-payouts',
// //                ]
//                 [
//                     'text'        => 'Videos',
//                     'url'         => 'reports/videos',
//                 ],
               
              
               
               
               
             
              
               
//             ],
//         ],
//         [
//             'text'        => 'Settings',
//             'url'         => 'settings',
//             'icon'        => 'fas fa-cog'
//         ],
//         [
//             'text'        => 'Student Usage',
//             'url'         => 'users/usage',
//             'icon'        => 'fa fa-users',
//         ],
//         [
//             'text'    => 'Study Materials',
//             'icon'    => 'fas fa-fw fa-tags',
//             'submenu' => [
//                 [
//                     'text'        => 'Package Study Materials',
//                     'url'         => 'package-study-materials',
//                 ],
//             ],
//         ],
//         [
//             'text'        => 'Tech Support',
//             'url'         => 'techsupport',
//             'icon'        => 'fa fa-question'
//         ],
//         [
//             'text'    => ' Testimonials',
//             'icon'    => 'fas fa-quote-left',
//             'submenu' => [
              
//                 [
//                     'text'        => 'Custom Testimonials',
//                     'url'         => 'custom-testimonials',
//                 ],
//                 [
//                     'text'        => 'Feedback',
//                     'url'         => 'feedback',
//                 ],
//                 [
//                     'text'        => 'Feedback List',
//                     'url'         => 'feedback-list',
//                 ],
//                 [
//                     'text'        => 'Student Testimonials',
//                     'url'         => 'student-testimonials',
//                 ],
//             ],
//         ],
//         [
//             'text'        => 'Third Party',
//             'url'         => 'third-party-agents',
//             'icon'        => 'fa fa-users',
//         ],
//         [
//             'text'        => 'Videos',
//             'url'         => 'videos',
//             'icon'        => 'fa fa-file-video',
//         ],
//         [
//             'text'        => 'S3 Videos',
//             'url'         => 's3-videos/create',
//             'icon'        => 'fa fa-file-video',
//         ],
//         [
//             'text'        => 'Rulebook',
//             'url'         => 'rule-book',
//             'icon'        => 'fa fa-file',
//         ],
      
        
//         [
//             'text' => 'blog',
//             'url'  => 'admin/blog',
//             'can'  => 'manage-blog',
//         ],
       
        
//         [
//             'text'        => 'New Order',
//             'url'         => 'third-party-orders',
//             'icon'        => 'fa fa-users',
//         ],
       
       
           
      
       
       
      
       
      
        
//        [
//            'text'        => 'Study Materials',
//            'url'         => 'purchases',
//            'icon'        => 'fa fa-shopping-cart',
//        ],
       
        
       
       
       
       
      
       
       
       
      
       
       
        
      
   
   

    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#612-menu-filters
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SubmenuFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        App\AdminLte\Menu\Filters\GateFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#613-plugins
    |
    */

    'plugins' => [
        [
            'name' => 'Datatables',
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/datatables/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/datatables/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'vendor/datatables/css/dataTables.bootstrap4.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/datatables/js/dataTables.buttons.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/datatables/js/pdfmake.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/datatables/js/vfs_fonts.js',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/datatables/js/buttons.html5.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/datatables/js/buttons.print.min.js',
                ],
            ],
        ],
        [
            'name' => 'jQuery UI',
            'active' => true,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => 'https://code.jquery.com/ui/1.12.1/jquery-ui.js',
                ],
            ],
        ],
        [
            'name' => 'Select2',
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        [
            'name' => 'BootstrapDatePicker',
            'active' => true,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => 'https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.9.0/dist/css/bootstrap-datepicker3.min.css'
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => 'https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.9.0/dist/js/bootstrap-datepicker.min.js'
                ],
            ],
        ],
        [
            'name' => 'Chartjs',
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        [
            'name' => 'FlotCharts',
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => 'https://cdn.jsdelivr.net/npm/flot-charts@0.8.3/jquery.flot.min.js',
                ],
            ],
        ],
        [
            'name' => 'BootstrapDatePicker',
            'active' => true,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => 'https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.9.0/dist/css/bootstrap-datepicker3.min.css'
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => 'https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.9.0/dist/js/bootstrap-datepicker.min.js'
                ],
            ],
        ],
        [
            'name' => 'Sweetalert2',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@8',
                ],
            ],
        ],[
            'name' => 'DateRangepicker',
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => '//cdn.jsdelivr.net/momentjs/latest/moment.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => '//cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => '//cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js',
                ],

            ],
        ],
        [
            'name' => 'Pace',
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
        [
            'name' => 'JQuery-Validate',
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/jquery-validate/jquery.validate.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/jquery-validate/additional-methods.min.js',
                ],
            ],
        ],
        [
            'name' => 'Croppie',
            'active' => true,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'https://cdnjs.cloudflare.com/ajax/libs/croppie/2.4.0/croppie.css',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'https://cdnjs.cloudflare.com/ajax/libs/croppie/2.4.0/croppie.js',
                ],
            ],
        ],
        [
            'name' => 'Dropify',
            'active' => true,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'vendor/dropify/css/dropify.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/dropify/js/dropify.min.js',
                ],
            ],
        ],
        [
            'name' => 'Toastr',
            'active' => true,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'vendor/toastr/toastr.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/toastr/toastr.min.js',
                ],
            ],
        ],
        [
            'name' => 'jstree',
            'active' => true,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'vendor/jstree/themes/default/style.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/jstree/jstree.min.js',
                ],
            ],
        ],
        [
            'name' => 'jQuery Smart Wizard',
            'active' => true,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'https://cdn.jsdelivr.net/npm/smartwizard@5.0.0/dist/css/smart_wizard_all.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'https://cdn.jsdelivr.net/npm/smartwizard@5.0.0/dist/js/jquery.smartWizard.min.js',
                ],
            ],
        ],
        [
            'name' => 'FlotCharts',
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => 'https://cdn.jsdelivr.net/npm/flot-charts@0.8.3/jquery.flot.min.js',
                ],
            ],
        ],
        [
            'name' => 'EditorJS',
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => 'https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest',
                ],
            ],
        ],
        [
            'name' => 'EditorJS Header',
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => 'https://cdn.jsdelivr.net/npm/@editorjs/header@latest',
                ],
            ],
        ],
        [
            'name' => 'EditorJS Link',
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => 'https://cdn.jsdelivr.net/npm/@editorjs/link@2.3.1/dist/bundle.min.js',
                ],
            ],
        ],
        [
            'name' => 'EditorJS Image',
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => 'https://cdn.jsdelivr.net/npm/@editorjs/image@2.3.0',
                ],
            ],
        ],
        [
            'name' => 'Magnific PopUp',
            'active' => true,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.0.0/magnific-popup.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.0.0/jquery.magnific-popup.min.js',
                ],
            ],
        ],
    ],
];
