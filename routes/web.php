<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\laravel_example\UserManagement;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

$controller_path = 'App\Http\Controllers';

// Main Page Auth Route
Route::get('/', $controller_path . '\authentications\LoginCover@index')->name('login');
Route::get('/login', $controller_path . '\Auth\LoginController@index')->name('login');
//AUTH
Route::post('/login', $controller_path . '\Auth\LoginController@login')->name('login.custom');

Route::post('/logout', $controller_path . '\Auth\LoginController@logout')->name('logout');
Route::get('forget-password', $controller_path . '\Auth\ForgotPasswordController@index')->name('forget.password.get');
Route::post('forget-password', $controller_path . '\Auth\ForgotPasswordController@submitForgetPasswordForm')->name('forget.password.post');
Route::get('reset-password/{token}', $controller_path . '\Auth\ForgotPasswordController@showResetPasswordForm')->name('reset.password.get');
Route::post('reset-password', $controller_path . '\Auth\ForgotPasswordController@submitResetPasswordForm')->name('reset.password.post');


//middlewear start
Route::group(['middleware' => 'auth'], function() {

  $controller_path = 'App\Http\Controllers';
//Dashboard
Route::get('/dashboard', $controller_path . '\dashboard\Analytics@index')->name('dashboard-admin');
Route::get('/user/dashboard', $controller_path . '\layouts\Horizontal@index')->name('dashboard-user');

//ROLE & Permission
Route::get('/app/roles', $controller_path . '\Configuration\RoleController@index')->name('app-roles');
Route::post('/app/roles/store', $controller_path . '\Configuration\RoleController@store')->name('app-roles-store');
Route::get('/app/roles/edit/{id}', $controller_path . '\Configuration\RoleController@editRole')->name('app-roles-edit');
Route::post('/app/roles/edit/{id}', $controller_path . '\Configuration\RoleController@update')->name('app-roles-update');
Route::get('/app/permission', $controller_path . '\Configuration\PermissionController@index')->name('app-permission');
Route::get('/app/permission/list', $controller_path . '\Configuration\PermissionController@getAllPermissions')->name('app-permission-list');
Route::post('/app/permission/store', $controller_path . '\Configuration\PermissionController@store')->name('app-permission-store');

//clients
Route::get('/client/list', $controller_path . '\Client\ClientController@index')->name('client-list');
Route::post('/client/store', $controller_path . '\Client\ClientController@store')->name('client-store');
Route::get('/client/edit/{id}', $controller_path . '\Client\ClientController@editClient')->name('client-edit');
Route::post('/client/edit/{id}', $controller_path . '\Client\ClientController@update')->name('client-update');
Route::get('/client/contactPersons', $controller_path . '\Client\ClientController@getAllContactPersons')->name('client-contact-persons');
Route::post('/client/contactPerson/store/{client}', $controller_path . '\Client\ClientController@contactPersonStore')->name('client-contact-store');
Route::get('/client/contactPerson/edit/{id}', $controller_path . '\Client\ClientController@editContactPerson')->name('client-contact-edit');
Route::post('/client/contactPerson/update/{id}', $controller_path . '\Client\ClientController@updateContactPerson')->name('client-contact-update');

//Projects
Route::get('/projects', $controller_path . '\Client\ProjectController@index')->name('client-projects');
Route::get('/project/list', $controller_path . '\Client\ProjectController@getAllProjects')->name('client-project-list');
Route::post('/project/store', $controller_path . '\Client\ProjectController@store')->name('app-permission-store');
Route::get('/project/edit/{id}', $controller_path . '\Client\ProjectController@editProject')->name('project-edit');
Route::post('/project/edit/{id}', $controller_path . '\Client\ProjectController@update')->name('project-update');

//Project Managaement
Route::get('/project/dashboard/{project_id}' , $controller_path . '\Client\ProjectController@dashboard')->name('project-dashboard');
Route::post('/project/milestone/store', $controller_path . '\Client\ProjectController@milestoneStore')->name('project-milestone-store');


//User Management admin

Route::get('/user/employee' , $controller_path . '\Employee\EmployeeController@index')->name('user-employee');
Route::get('/user/employee/list' , $controller_path . '\Employee\EmployeeController@employeeList')->name('user-employee-list');
Route::get('/user/employee/view/account/{id}' , $controller_path . '\Employee\EmployeeController@employeeView')->name('user-employee-view');
Route::post('/user/employee/store' , $controller_path . '\Employee\EmployeeController@store')->name('user-employee-store');
Route::get('/user/employee/edit/{id}', $controller_path . '\Employee\EmployeeController@edit')->name('user-employee-edit');
Route::post('/user/employee/update/{id}', $controller_path . '\Employee\EmployeeController@update')->name('user-employee-update');
Route::get('/user/employee/editInfo/{id}', $controller_path . '\Employee\EmployeeController@editInfo')->name('user-employee-editInfo');
Route::post('/user/employee/editInfo/{id}', $controller_path . '\Employee\EmployeeController@updateInfo')->name('user-employee-updateInfo');
Route::post('/user/employee/addBankAccount/{id}', $controller_path . '\Employee\EmployeeController@addBankAccount')->name('user-account-updateInfo');
Route::post('/user/employee/updateBankAccount', $controller_path . '\Employee\EmployeeController@updateBankAccount')->name('user-account-status-change');
Route::post('/user/employee/uploadImage', $controller_path . '\Employee\EmployeeController@uploadImage')->name('user-account-image-change');


Route::post('/user/employee/resetPasssword' , $controller_path . '\Employee\EmployeeController@resetPassword')->name('user.employee.resetPassword');
//fetchEvents
Route::get('/fetchEvents', $controller_path . '\Employee\EmployeeController@fetchEvents')->name('employee-event');


//Masters
Route::get('/master/designation', $controller_path . '\Masters\DesignationController@index')->name('master-designation');
Route::get('/master/designation/list', $controller_path . '\Masters\DesignationController@getAllDesignations')->name('master-designation-list');
Route::post('/master/designation/store', $controller_path . '\Masters\DesignationController@store')->name('master-designation-store');
Route::get('/master/designation/edit/{id}', $controller_path . '\Masters\DesignationController@edit')->name('master-designation-edit');
Route::post('/master/designation/update/{id}', $controller_path . '\Masters\DesignationController@update')->name('master-designation-update');

Route::get('/master/employment-type', $controller_path . '\Masters\EmploymentTypeController@index')->name('master-employment-type');
Route::get('/master/employment-type/list', $controller_path . '\Masters\EmploymentTypeController@getAllEmploymentTypes')->name('master-designation-list');
Route::post('/master/employment-type/store', $controller_path . '\Masters\EmploymentTypeController@store')->name('master-designation-store');
Route::get('/master/employment-type/edit/{id}', $controller_path . '\Masters\EmploymentTypeController@edit')->name('master-designation-edit');
Route::post('/master/employment-type/update/{id}', $controller_path . '\Masters\EmploymentTypeController@update')->name('master-designation-update');
Route::get('/helpers/emplymenttypeDesignation/{id}', $controller_path . '\MasterFunctionController@getEmploymenttypeDesignations')->name('emplymenttype-designation-list');


Route::get('/master/holiday', $controller_path . '\HolidayController@index')->name('master-holiday');
Route::get('/master/holiday/list', $controller_path . '\HolidayController@getAllHolidays')->name('master-holiday-list');
Route::post('/master/holiday/store', $controller_path . '\HolidayController@store')->name('master-holiday-store');
Route::get('/master/holiday/edit/{id}', $controller_path . '\HolidayController@edit')->name('master-holiday-edit');
Route::post('/master/holiday/update/{id}', $controller_path . '\HolidayController@update')->name('master-holiday-update');



//Leave Master
Route::get('/leave', $controller_path . '\Leave\LeaveController@index')->name('leave-master-index');
Route::get('/leave/list', $controller_path . '\Leave\LeaveController@getAllLeaves')->name('leave-list');
Route::post('/leave/store', $controller_path . '\Leave\LeaveController@store')->name('leave-store');
Route::get('/leave/edit/{id}', $controller_path . '\Leave\LeaveController@edit')->name('leave-edit');
Route::post('/leave/update/{id}', $controller_path . '\Leave\LeaveController@update')->name('leave-update');

Route::get('geneateHoliday', $controller_path . '\Leave\LeaveController@generateHoliday')->name('generate-holiday');

Route::get('/leave-assign', $controller_path . '\Leave\LeaveAssignController@index')->name('leave-assign');
Route::get('/leave-assign/list', $controller_path . '\Leave\LeaveAssignController@getAllAssignLeaves')->name('leave-assign-list');
Route::post('/leave-assign/store', $controller_path . '\Leave\LeaveAssignController@store')->name('leave-assign-store');
Route::get('/leave-assign/edit/{id}', $controller_path . '\Leave\LeaveAssignController@assignEdit')->name('leave-assign-edit');
Route::post('/leave-assign/update/{id}', $controller_path . '\Leave\LeaveAssignController@assignUpdate')->name('leave-assign-update');

Route::get('/attendance', $controller_path . '\Attendance\AttendanceController@index')->name('attendance-index');
// Route::post('/attendance/import', $controller_path . '\Attendance\AttendanceController@import')->name('attendance-import');
Route::post('/attendance/import', $controller_path . '\Attendance\AttendanceLogController@import')->name('attendance-import');
Route::get('/attendance-management', $controller_path . '\Attendance\AttendanceController@attendanceManagement')->name('attendance-hrView');
Route::get('/download', $controller_path . '\Attendance\AttendanceController@download')->name('attendance-download');
Route::POST('/downloadBulk', $controller_path . '\Attendance\AttendanceLogController@downloadBulk')->name('attendance-download-bulk');
Route::get('/movement', $controller_path . '\Attendance\MovementController@index')->name('attendance-movement');
Route::post('/movement/store', $controller_path . '\Attendance\MovementController@store')->name('attendance-movement-store');
Route::get('/movement/edit/{id}', $controller_path . '\Attendance\MovementController@edit')->name('attendance-movement-edit');
Route::post('/movement/update/{id}', $controller_path . '\Attendance\MovementController@update')->name('attendance-movement-update');
Route::get('/movement/delete/{id}', $controller_path . '\Attendance\MovementController@destroy')->name('attendance-movement-delete');


Route::get('/movement/approve-list', $controller_path . '\Attendance\MovementController@approveList')->name('leave-movement-approve-list');
Route::get('/movement/request-list', $controller_path . '\Attendance\MovementController@requestList')->name('attendance-movement-request-list');
Route::get('/movement/list', $controller_path . '\Attendance\MovementController@movementList')->name('attendance-movement-list');
Route::post('/movement/action/{id}', $controller_path . '\Attendance\MovementController@action')->name('attendance-movement-action');
Route::POST('/movement/downloadBulk', $controller_path . '\Attendance\MovementController@downloadBulk')->name('attendance-download-bulk');

Route::get('/leave/request', $controller_path . '\Leave\LeaveRequestController@index')->name('attendance-leave-request');

Route::get('/leave/approve-list', $controller_path . '\Leave\LeaveRequestController@approveList')->name('leave-approve-list');
Route::get('/leave/request-list', $controller_path . '\Leave\LeaveRequestController@requestList')->name('attendance-leave-list');
Route::get('/leave/request/list', $controller_path . '\Leave\LeaveRequestController@leaveList')->name('attendance-leave-list');
Route::post('/leave/request/store', $controller_path . '\Leave\LeaveRequestController@store')->name('attendance-leave-request-store');

Route::get('/leave/request/edit/{id}', $controller_path . '\Leave\LeaveRequestController@edit')->name('attendance-leave-request-edit');
Route::post('/leave/request/action/{id}', $controller_path . '\Leave\LeaveRequestController@action')->name('attendance-movement-action');

Route::get('/leave/request/delete/{id}', $controller_path . '\Leave\LeaveRequestController@destroy')->name('attendance-leave-delete');

Route::POST('/leave/downloadBulk', $controller_path . '\Leave\LeaveRequestController@downloadBulk')->name('attendance-download-bulk');


//Missed punch
Route::get('/misspunch', $controller_path . '\Attendance\MissedPunchController@index')->name('attendance-misspunch');
Route::post('/misspunch/store', $controller_path . '\Attendance\MissedPunchController@store')->name('attendance-misspunch-store');
Route::get('/misspunch/edit/{id}', $controller_path . '\Attendance\MissedPunchController@edit')->name('attendance-misspunch-edit');
Route::post('/misspunch/update/{id}', $controller_path . '\Attendance\MissedPunchController@update')->name('attendance-misspunch-update');
Route::get('/misspunch/delete/{id}', $controller_path . '\Attendance\MissedPunchController@destroy')->name('attendance-misspunch-delete');


Route::get('/misspunch/approve-list', $controller_path . '\Attendance\MissedPunchController@approveList')->name('leave-misspunch-approve-list');
Route::get('/misspunch/request-list', $controller_path . '\Attendance\MissedPunchController@requestList')->name('attendance-misspunch-request-list');
Route::get('/misspunch/list', $controller_path . '\Attendance\MissedPunchController@misspunchList')->name('attendance-misspunch-list');
Route::post('/misspunch/action/{id}', $controller_path . '\Attendance\MissedPunchController@action')->name('attendance-misspunch-action');
Route::POST('/misspunch/downloadBulk', $controller_path . '\Attendance\MissedPunchController@downloadBulk')->name('attendance-misspunch-download-bulk');
//
//Employee login Routes
//user view
Route::get('/user/profile' , $controller_path . '\Employee\EmployeeController@profileView')->name('user-profile');
Route::get('/user/project/list', $controller_path . '\Client\ProjectController@userProjectList')->name('user-project-list');


//Project employee Mngmnt
Route::get('/project/employee/{project_id}' , $controller_path . '\Project\ProjectEmployeeController@index')->name('project-employee');
Route::get('/project/employees/detail/list' , $controller_path . '\Project\ProjectEmployeeController@employeeList')->name('project-employee-list');
Route::get('/project/employee/view/account/{id}' , $controller_path . '\ProjectProjectEmployeeController@employeeView')->name('project-employee-view');
Route::post('/project/employee/store' , $controller_path . '\Project\ProjectEmployeeController@store')->name('project-employee-store');


//project Document

Route::get('/project/docs/{project_id}' , $controller_path . '\Project\ProjectDocumentController@index')->name('project-employee');




});//middlewear end

// Route::get('/dashboard/analytics', $controller_path . '\dashboard\Analytics@index')->name('dashboard-analytics');
Route::get('/dashboard/crm', $controller_path . '\dashboard\Crm@index')->name('dashboard-crm');
Route::get('/dashboard/ecommerce', $controller_path . '\dashboard\Ecommerce@index')->name('dashboard-ecommerce');

// locale
Route::get('lang/{locale}', $controller_path . '\language\LanguageController@swap');

// layout
Route::get('/layouts/collapsed-menu', $controller_path . '\layouts\CollapsedMenu@index')->name('layouts-collapsed-menu');
Route::get('/layouts/content-navbar', $controller_path . '\layouts\ContentNavbar@index')->name('layouts-content-navbar');
Route::get('/layouts/content-nav-sidebar', $controller_path . '\layouts\ContentNavSidebar@index')->name('layouts-content-nav-sidebar');
Route::get('/layouts/navbar-full', $controller_path . '\layouts\NavbarFull@index')->name('layouts-navbar-full');
Route::get('/layouts/navbar-full-sidebar', $controller_path . '\layouts\NavbarFullSidebar@index')->name('layouts-navbar-full-sidebar');
// Route::get('/layouts/horizontal', $controller_path . '\layouts\Horizontal@index')->name('dashboard-analytics');
// Route::get('/layouts/vertical', $controller_path . '\layouts\Vertical@index')->name('dashboard-analytics');
Route::get('/layouts/without-menu', $controller_path . '\layouts\WithoutMenu@index')->name('layouts-without-menu');
Route::get('/layouts/without-navbar', $controller_path . '\layouts\WithoutNavbar@index')->name('layouts-without-navbar');
Route::get('/layouts/fluid', $controller_path . '\layouts\Fluid@index')->name('layouts-fluid');
Route::get('/layouts/container', $controller_path . '\layouts\Container@index')->name('layouts-container');
Route::get('/layouts/blank', $controller_path . '\layouts\Blank@index')->name('layouts-blank');

// apps
Route::get('/app/email', $controller_path . '\apps\Email@index')->name('app-email');
Route::get('/app/chat', $controller_path . '\apps\Chat@index')->name('app-chat');
Route::get('/app/calendar', $controller_path . '\apps\Calendar@index')->name('app-calendar');
Route::get('/app/kanban', $controller_path . '\apps\Kanban@index')->name('app-kanban');
Route::get('/app/invoice/list', $controller_path . '\apps\InvoiceList@index')->name('app-invoice-list');
Route::get('/app/invoice/preview', $controller_path . '\apps\InvoicePreview@index')->name('app-invoice-preview');
Route::get('/app/invoice/print', $controller_path . '\apps\InvoicePrint@index')->name('app-invoice-print');
Route::get('/app/invoice/edit', $controller_path . '\apps\InvoiceEdit@index')->name('app-invoice-edit');
Route::get('/app/invoice/add', $controller_path . '\apps\InvoiceAdd@index')->name('app-invoice-add');
Route::get('/app/user/list', $controller_path . '\apps\UserList@index')->name('app-user-list');
Route::get('/app/user/view/account', $controller_path . '\apps\UserViewAccount@index')->name('app-user-view-account');
Route::get('/app/user/view/security', $controller_path . '\apps\UserViewSecurity@index')->name('app-user-view-security');
Route::get('/app/user/view/billing', $controller_path . '\apps\UserViewBilling@index')->name('app-user-view-billing');
Route::get('/app/user/view/notifications', $controller_path . '\apps\UserViewNotifications@index')->name('app-user-view-notifications');
Route::get('/app/user/view/connections', $controller_path . '\apps\UserViewConnections@index')->name('app-user-view-connections');
Route::get('/app/access-roles', $controller_path . '\apps\AccessRoles@index')->name('app-access-roles');
Route::get('/app/access-permission', $controller_path . '\apps\AccessPermission@index')->name('app-access-permission');

// pages
Route::get('/pages/profile-user', $controller_path . '\pages\UserProfile@index')->name('pages-profile-user');
Route::get('/pages/profile-teams', $controller_path . '\pages\UserTeams@index')->name('pages-profile-teams');
Route::get('/pages/profile-projects', $controller_path . '\pages\UserProjects@index')->name('pages-profile-projects');
Route::get('/pages/profile-connections', $controller_path . '\pages\UserConnections@index')->name('pages-profile-connections');
Route::get('/pages/account-settings-account', $controller_path . '\pages\AccountSettingsAccount@index')->name('pages-account-settings-account');
Route::get('/pages/account-settings-security', $controller_path . '\pages\AccountSettingsSecurity@index')->name('pages-account-settings-security');
Route::get('/pages/account-settings-billing', $controller_path . '\pages\AccountSettingsBilling@index')->name('pages-account-settings-billing');
Route::get('/pages/account-settings-notifications', $controller_path . '\pages\AccountSettingsNotifications@index')->name('pages-account-settings-notifications');
Route::get('/pages/account-settings-connections', $controller_path . '\pages\AccountSettingsConnections@index')->name('pages-account-settings-connections');
Route::get('/pages/faq', $controller_path . '\pages\Faq@index')->name('pages-faq');
Route::get('/pages/help-center-landing', $controller_path . '\pages\HelpCenterLanding@index')->name('pages-help-center-landing');
Route::get('/pages/help-center-categories', $controller_path . '\pages\HelpCenterCategories@index')->name('pages-help-center-categories');
Route::get('/pages/help-center-article', $controller_path . '\pages\HelpCenterArticle@index')->name('pages-help-center-article');
Route::get('/pages/pricing', $controller_path . '\pages\Pricing@index')->name('pages-pricing');
Route::get('/pages/misc-error', $controller_path . '\pages\MiscError@index')->name('pages-misc-error');
Route::get('/pages/misc-under-maintenance', $controller_path . '\pages\MiscUnderMaintenance@index')->name('pages-misc-under-maintenance');
Route::get('/pages/misc-comingsoon', $controller_path . '\pages\MiscComingSoon@index')->name('pages-misc-comingsoon');
Route::get('/pages/misc-not-authorized', $controller_path . '\pages\MiscNotAuthorized@index')->name('pages-misc-not-authorized');

// authentication
Route::get('/auth/login-basic', $controller_path . '\authentications\LoginBasic@index')->name('auth-login-basic');
Route::get('/auth/login-cover', $controller_path . '\authentications\LoginCover@index')->name('auth-login-cover');
Route::get('/auth/register-basic', $controller_path . '\authentications\RegisterBasic@index')->name('auth-register-basic');
Route::get('/auth/register-cover', $controller_path . '\authentications\RegisterCover@index')->name('auth-register-cover');
Route::get('/auth/register-multisteps', $controller_path . '\authentications\RegisterMultiSteps@index')->name('auth-register-multisteps');
Route::get('/auth/verify-email-basic', $controller_path . '\authentications\VerifyEmailBasic@index')->name('auth-verify-email-basic');
Route::get('/auth/verify-email-cover', $controller_path . '\authentications\VerifyEmailCover@index')->name('auth-verify-email-cover');
Route::get('/auth/reset-password-basic', $controller_path . '\authentications\ResetPasswordBasic@index')->name('auth-reset-password-basic');
Route::get('/auth/reset-password-cover', $controller_path . '\authentications\ResetPasswordCover@index')->name('auth-reset-password-cover');
Route::get('/auth/forgot-password-basic', $controller_path . '\authentications\ForgotPasswordBasic@index')->name('auth-reset-password-basic');
Route::get('/auth/forgot-password-cover', $controller_path . '\authentications\ForgotPasswordCover@index')->name('auth-forgot-password-cover');
Route::get('/auth/two-steps-basic', $controller_path . '\authentications\TwoStepsBasic@index')->name('auth-two-steps-basic');
Route::get('/auth/two-steps-cover', $controller_path . '\authentications\TwoStepsCover@index')->name('auth-two-steps-cover');

// wizard example
Route::get('/wizard/ex-checkout', $controller_path . '\wizard_example\Checkout@index')->name('wizard-ex-checkout');
Route::get('/wizard/ex-property-listing', $controller_path . '\wizard_example\PropertyListing@index')->name('wizard-ex-property-listing');
Route::get('/wizard/ex-create-deal', $controller_path . '\wizard_example\CreateDeal@index')->name('wizard-ex-create-deal');

// modal
Route::get('/modal-examples', $controller_path . '\modal\ModalExample@index')->name('modal-examples');

// cards
Route::get('/cards/basic', $controller_path . '\cards\CardBasic@index')->name('cards-basic');
Route::get('/cards/advance', $controller_path . '\cards\CardAdvance@index')->name('cards-advance');
Route::get('/cards/statistics', $controller_path . '\cards\CardStatistics@index')->name('cards-statistics');
Route::get('/cards/analytics', $controller_path . '\cards\CardAnalytics@index')->name('cards-analytics');
Route::get('/cards/actions', $controller_path . '\cards\CardActions@index')->name('cards-actions');

// User Interface
Route::get('/ui/accordion', $controller_path . '\user_interface\Accordion@index')->name('ui-accordion');
Route::get('/ui/alerts', $controller_path . '\user_interface\Alerts@index')->name('ui-alerts');
Route::get('/ui/badges', $controller_path . '\user_interface\Badges@index')->name('ui-badges');
Route::get('/ui/buttons', $controller_path . '\user_interface\Buttons@index')->name('ui-buttons');
Route::get('/ui/carousel', $controller_path . '\user_interface\Carousel@index')->name('ui-carousel');
Route::get('/ui/collapse', $controller_path . '\user_interface\Collapse@index')->name('ui-collapse');
Route::get('/ui/dropdowns', $controller_path . '\user_interface\Dropdowns@index')->name('ui-dropdowns');
Route::get('/ui/footer', $controller_path . '\user_interface\Footer@index')->name('ui-footer');
Route::get('/ui/list-groups', $controller_path . '\user_interface\ListGroups@index')->name('ui-list-groups');
Route::get('/ui/modals', $controller_path . '\user_interface\Modals@index')->name('ui-modals');
Route::get('/ui/navbar', $controller_path . '\user_interface\Navbar@index')->name('ui-navbar');
Route::get('/ui/offcanvas', $controller_path . '\user_interface\Offcanvas@index')->name('ui-offcanvas');
Route::get('/ui/pagination-breadcrumbs', $controller_path . '\user_interface\PaginationBreadcrumbs@index')->name('ui-pagination-breadcrumbs');
Route::get('/ui/progress', $controller_path . '\user_interface\Progress@index')->name('ui-progress');
Route::get('/ui/spinners', $controller_path . '\user_interface\Spinners@index')->name('ui-spinners');
Route::get('/ui/tabs-pills', $controller_path . '\user_interface\TabsPills@index')->name('ui-tabs-pills');
Route::get('/ui/toasts', $controller_path . '\user_interface\Toasts@index')->name('ui-toasts');
Route::get('/ui/tooltips-popovers', $controller_path . '\user_interface\TooltipsPopovers@index')->name('ui-tooltips-popovers');
Route::get('/ui/typography', $controller_path . '\user_interface\Typography@index')->name('ui-typography');

// extended ui
Route::get('/extended/ui-avatar', $controller_path . '\extended_ui\Avatar@index')->name('extended-ui-avatar');
Route::get('/extended/ui-blockui', $controller_path . '\extended_ui\BlockUI@index')->name('extended-ui-blockui');
Route::get('/extended/ui-drag-and-drop', $controller_path . '\extended_ui\DragAndDrop@index')->name('extended-ui-drag-and-drop');
Route::get('/extended/ui-media-player', $controller_path . '\extended_ui\MediaPlayer@index')->name('extended-ui-media-player');
Route::get('/extended/ui-perfect-scrollbar', $controller_path . '\extended_ui\PerfectScrollbar@index')->name('extended-ui-perfect-scrollbar');
Route::get('/extended/ui-star-ratings', $controller_path . '\extended_ui\StarRatings@index')->name('extended-ui-star-ratings');
Route::get('/extended/ui-sweetalert2', $controller_path . '\extended_ui\SweetAlert@index')->name('extended-ui-sweetalert2');
Route::get('/extended/ui-text-divider', $controller_path . '\extended_ui\TextDivider@index')->name('extended-ui-text-divider');
Route::get('/extended/ui-timeline-basic', $controller_path . '\extended_ui\TimelineBasic@index')->name('extended-ui-timeline-basic');
Route::get('/extended/ui-timeline-fullscreen', $controller_path . '\extended_ui\TimelineFullscreen@index')->name('extended-ui-timeline-fullscreen');
Route::get('/extended/ui-tour', $controller_path . '\extended_ui\Tour@index')->name('extended-ui-tour');
Route::get('/extended/ui-treeview', $controller_path . '\extended_ui\Treeview@index')->name('extended-ui-treeview');
Route::get('/extended/ui-misc', $controller_path . '\extended_ui\Misc@index')->name('extended-ui-misc');

// icons
Route::get('/icons/tabler', $controller_path . '\icons\Tabler@index')->name('icons-tabler');
Route::get('/icons/font-awesome', $controller_path . '\icons\FontAwesome@index')->name('icons-font-awesome');

// form elements
Route::get('/forms/basic-inputs', $controller_path . '\form_elements\BasicInput@index')->name('forms-basic-inputs');
Route::get('/forms/input-groups', $controller_path . '\form_elements\InputGroups@index')->name('forms-input-groups');
Route::get('/forms/custom-options', $controller_path . '\form_elements\CustomOptions@index')->name('forms-custom-options');
Route::get('/forms/editors', $controller_path . '\form_elements\Editors@index')->name('forms-editors');
Route::get('/forms/file-upload', $controller_path . '\form_elements\FileUpload@index')->name('forms-file-upload');
Route::get('/forms/pickers', $controller_path . '\form_elements\Picker@index')->name('forms-pickers');
Route::get('/forms/selects', $controller_path . '\form_elements\Selects@index')->name('forms-selects');
Route::get('/forms/sliders', $controller_path . '\form_elements\Sliders@index')->name('forms-sliders');
Route::get('/forms/switches', $controller_path . '\form_elements\Switches@index')->name('forms-switches');
Route::get('/forms/extras', $controller_path . '\form_elements\Extras@index')->name('forms-extras');

// form layouts
Route::get('/form/layouts-vertical', $controller_path . '\form_layouts\VerticalForm@index')->name('form-layouts-vertical');
Route::get('/form/layouts-horizontal', $controller_path . '\form_layouts\HorizontalForm@index')->name('form-layouts-horizontal');
Route::get('/form/layouts-sticky', $controller_path . '\form_layouts\StickyActions@index')->name('form-layouts-sticky');

// form wizards
Route::get('/form/wizard-numbered', $controller_path . '\form_wizard\Numbered@index')->name('form-wizard-numbered');
Route::get('/form/wizard-icons', $controller_path . '\form_wizard\Icons@index')->name('form-wizard-icons');
Route::get('/form/validation', $controller_path . '\form_validation\Validation@index')->name('form-validation');

// tables
Route::get('/tables/basic', $controller_path . '\tables\Basic@index')->name('tables-basic');
Route::get('/tables/datatables-basic', $controller_path . '\tables\DatatableBasic@index')->name('tables-datatables-basic');
Route::get('/tables/datatables-advanced', $controller_path . '\tables\DatatableAdvanced@index')->name('tables-datatables-advanced');
Route::get('/tables/datatables-extensions', $controller_path . '\tables\DatatableExtensions@index')->name('tables-datatables-extensions');

// charts
Route::get('/charts/apex', $controller_path . '\charts\ApexCharts@index')->name('charts-apex');
Route::get('/charts/chartjs', $controller_path . '\charts\ChartJs@index')->name('charts-chartjs');

// maps
Route::get('/maps/leaflet', $controller_path . '\maps\Leaflet@index')->name('maps-leaflet');

// laravel example
Route::get('/laravel/user-management', [UserManagement::class, 'UserManagement'])->name('laravel-example-user-management');
Route::resource('/user-list', UserManagement::class);