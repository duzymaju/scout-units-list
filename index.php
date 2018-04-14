<?php
/*
Plugin Name: Scout Units List
Plugin URI: http://www.krakowpodgorze.zhp.pl/
Description: Units management system for scout web pages.
Version: 0.6.0
Author: Wiktor Maj
Author URI: http://www.majpage.com/
License: GNU GPLv2
Text Domain: scout-units-list
*/

namespace ScoutUnitsList;

use ScoutUnitsList\Controller\AdminController;
use ScoutUnitsList\Controller\Admin\UserController;
use ScoutUnitsList\Controller\AjaxController;
use ScoutUnitsList\Controller\ApiController;
use ScoutUnitsList\Controller\InstallController;
use ScoutUnitsList\Controller\ShortcodesController;
use ScoutUnitsList\Manager\CacheManager;
use ScoutUnitsList\Manager\ConfigManager;
use ScoutUnitsList\Manager\DbManager;
use ScoutUnitsList\Manager\MessageManager;
use ScoutUnitsList\Manager\MigrationManager;
use ScoutUnitsList\Manager\RoleManager;
use ScoutUnitsList\Model\Repository\AttachmentRepository;
use ScoutUnitsList\Model\Repository\PersonRepository;
use ScoutUnitsList\Model\Repository\PositionRepository;
use ScoutUnitsList\Model\Repository\UnitRepository;
use ScoutUnitsList\Model\Repository\UserRepository;
use ScoutUnitsList\System\Loader;
use ScoutUnitsList\System\Request;

global $wpdb;

require_once(dirname(__FILE__) . '/System/Loader.php');
$loader = Loader::run();
$loader->setVersion('0.6.0');

$request = new Request();

$dbManager = new DbManager($wpdb);
$configManager = new ConfigManager($loader->getName() . '_config');
$config = $configManager->get();
$loader->set('manager.cache', new CacheManager($loader->getPath('Cache'), $config->getCacheTtl()))
    ->set('manager.config', $configManager)
    ->set('manager.db', $dbManager)
    ->set('manager.message', new MessageManager())
    ->set('manager.migration', new MigrationManager($dbManager, $loader->getName() . '_versions',
        $loader->getPath('Migration')))
    ->set('repository.attachment', new AttachmentRepository($dbManager))
    ->set('repository.person', new PersonRepository($dbManager))
    ->set('repository.position', new PositionRepository($dbManager))
    ->set('repository.unit', new UnitRepository($dbManager))
    ->set('repository.user', new UserRepository($dbManager));

// Initialization
add_action('init', [
    $loader,
    'init',
]);
add_action('init', function () use ($loader, $configManager) {
    $config = $configManager->get();
    wp_register_script('google-maps-api', 'https://maps.googleapis.com/maps/api/js?v=3&key=' . $config->getMapKey());

    if (is_admin()) {
        $roleManager = new RoleManager();
        $roleManager
            ->addCapabilities('subscriber', [
                'sul_modify_own_units',
            ])
            ->addCapabilities('editor', [
                'sul_manage_persons',
                'sul_manage_units',
            ])
            ->addCapabilities('administrator', [
                'sul_manage_config',
                'sul_manage_positions',
            ])
        ;

        wp_enqueue_script('sul-admin', $loader->getFileUrl('admin.js'), [
            'google-maps-api',
            'jquery-core',
            'jquery-ui-autocomplete',
            'jquery-ui-sortable',
        ], $loader->getVersion());
        wp_localize_script('sul-admin', 'sul', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'map' => [
                'defaults' => [
                    'lat' => $config->getMapDefaultLat(),
                    'lng' => $config->getMapDefaultLng(),
                    'zoom' => $config->getMapDefaultZoom(),
                ],
            ],
        ]);
        wp_enqueue_style('sul-admin', $loader->getFileUrl('admin.css'), false, $loader->getVersion());
    } else {
        wp_register_script('sul-user', $loader->getFileUrl('user.js'), [
            'google-maps-api',
            'jquery-core',
        ], $loader->getVersion());
        wp_enqueue_style('sul-user', $loader->getFileUrl('style.css'), false, $loader->getVersion());
    }
});

// Installation
$installController = new InstallController($loader, $request);
register_activation_hook(__FILE__, [
    $installController,
    'activate',
]);
register_deactivation_hook(__FILE__, [
    $installController,
    'deactivate',
]);
if (function_exists('sul_uninstall')) {
    function sul_uninstall() {
        global $installController;
        return $installController->uninstall();
    }
}
register_uninstall_hook(__FILE__, 'sul_uninstall');

// AJAX requests
$ajaxController = new AjaxController($loader, $request);
foreach (get_class_methods(AjaxController::class) as $methodName) {
    if (1 <= $actionPos = strpos($methodName, 'Action')) {
        $actionName = substr($methodName, 0, $actionPos);
        add_action('wp_ajax_sul_' . $actionName, [
            $ajaxController,
            $methodName,
        ]);
    }
}

// API requests
$apiController = new ApiController($loader, $request);
$apiParamName = 'sul_api_v';
$installController->addRewriteRule('^sul-api/v([1-9][0-9]*)/([a-z-]+)/?',
    'index.php?' . $apiParamName . '=$matches[1]&action=$matches[2]');
add_filter('query_vars', function (array $queryVars) use ($apiParamName) {
    $queryVars[] = $apiParamName;

    return $queryVars;
});
add_action('parse_request', function (&$wp) use ($apiController, $apiParamName) {
    if (array_key_exists($apiParamName, $wp->query_vars)) {
        $apiController->routes((int) $wp->query_vars[$apiParamName]);
    }
});

// Shortcodes
$shortcodesController = new ShortcodesController($loader, $request);
add_shortcode('sul-units-list', [
    $shortcodesController,
    'unitsList',
]);
add_shortcode('sul-units-map', [
    $shortcodesController,
    'unitsMap',
]);
add_shortcode('sul-persons-list', [
    $shortcodesController,
    'personsList',
]);

// Admin panel
if (is_admin() && defined('WP_ADMIN') && WP_ADMIN) {
    $adminController = new AdminController($loader, $request);
    add_action('admin_menu', [
        $adminController,
        'menu',
    ]);

    $userController = new UserController($loader, $request);
    add_action('show_user_profile', [
        $userController,
        'form',
    ]);
    add_action('edit_user_profile', [
        $userController,
        'form',
    ]);
    add_action('personal_options_update', [
        $userController,
        'update',
    ]);
    add_action('edit_user_profile_update', [
        $userController,
        'update',
    ]);
}
