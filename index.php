<?php
/*
Plugin Name: Scout Units List
Plugin URI: http://www.krakowpodgorze.zhp.pl/
Description: Units management system for scout web pages.
Version: 0.1.0
Author: Wiktor Maj
Author URI: http://www.majpage.com/
License: GNU GPLv2
Text Domain: scout-units-list
*/

namespace ScoutUnitsList;

use ScoutUnitsList\Controller\AdminController;
use ScoutUnitsList\Controller\ApiController;
use ScoutUnitsList\Controller\InstallController;
use ScoutUnitsList\Controller\ShortcodesController;
use ScoutUnitsList\Manager\CacheManager;
use ScoutUnitsList\Manager\ConfigManager;
use ScoutUnitsList\Manager\DbManager;
use ScoutUnitsList\Manager\MessageManager;
use ScoutUnitsList\Manager\RoleManager;
use ScoutUnitsList\Model\Repository\PersonRepository;
use ScoutUnitsList\Model\Repository\PositionRepository;
use ScoutUnitsList\Model\Repository\UnitRepository;
use ScoutUnitsList\Model\Repository\UserRepository;
use ScoutUnitsList\System\Loader;
use ScoutUnitsList\System\Request;

global $wpdb;

require_once(dirname(__FILE__) . '/System/Loader.php');
$loader = Loader::run();
$loader->setVersion('0.1.0');

$request = new Request();

$dbManager = new DbManager($wpdb);
$configManager = new ConfigManager($loader->getName() . '_config');
$loader->set('manager.cache', new CacheManager($loader->getPath('Cache')))
    ->set('manager.config', $configManager)
    ->set('manager.db', $dbManager)
    ->set('manager.message', new MessageManager())
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

        $config = $configManager->get();
        wp_enqueue_script('google-maps-api', 'https://maps.googleapis.com/maps/api/js?v=3&key=' . $config->getMapKey());
        wp_enqueue_script('sul-admin-js', $loader->getFileUrl('admin.js'), [
            'google-maps-api',
            'jquery-core',
            'jquery-ui-autocomplete',
        ], $loader->getVersion());
        wp_localize_script('sul-admin-js', 'sul', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'map' => [
                'defaults' => [
                    'lat' => $config->getMapDefaultLat(),
                    'lng' => $config->getMapDefaultLng(),
                    'zoom' => $config->getMapDefaultZoom(),
                ],
            ],
        ]);
        wp_enqueue_style('sul-admin-css', $loader->getFileUrl('/admin.css'), false, $loader->getVersion());
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
register_uninstall_hook(__FILE__, [
    $installController,
    'uninstall',
]);

// AJAX requests
$apiController = new ApiController($loader, $request);
foreach (get_class_methods(ApiController::class) as $methodName) {
    if (1 <= $actionPos = strpos($methodName, 'Action')) {
        $actionName = substr($methodName, 0, $actionPos);
        add_action('wp_ajax_sul_' . $actionName, [
            $apiController,
            $methodName,
        ]);
    }
}

// Shortcodes
$shortcodesController = new ShortcodesController($loader, $request);
add_shortcode('sul-units-list', [
    $shortcodesController,
    'unitsList',
]);

// Admin panel
if (WP_ADMIN && is_admin()) {
    $adminController = new AdminController($loader, $request);
    add_action('admin_menu', [
        $adminController,
        'menu',
    ]);
}
