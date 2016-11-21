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
use ScoutUnitsList\Manager\ConfigManager;
use ScoutUnitsList\Manager\DbManager;
use ScoutUnitsList\Manager\MessageManager;
use ScoutUnitsList\Model\Repository\PersonRepository;
use ScoutUnitsList\Model\Repository\PositionRepository;
use ScoutUnitsList\Model\Repository\UnitRepository;
use ScoutUnitsList\System\Loader;
use ScoutUnitsList\System\Request;

global $wpdb;

require_once(dirname(__FILE__) . '/System/Loader.php');
$loader = Loader::run();
$loader->setName('scout-units-list')
    ->setVersion('0.1.0');

$request = new Request();

$dbManager = new DbManager($wpdb);
$loader->set('manager.db', $dbManager)
    ->set('manager.config', new ConfigManager($loader->getName() . '_config'))
    ->set('repository.person', new PersonRepository($dbManager))
    ->set('repository.position', new PositionRepository($dbManager))
    ->set('repository.unit', new UnitRepository($dbManager))
    ->set('manager.message', new MessageManager());

// Initialization
add_action('init', [
    $loader,
    'init',
]);
add_action('init', function () use ($loader) {
    if (is_admin()) {
        wp_enqueue_script('sul_admin_js', $loader->getFileUrl('admin.js'), [
            'jquery-core',
            'jquery-ui-autocomplete',
        ], $loader->getVersion());
        wp_localize_script('sul_admin_js', 'sul', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
        ]);
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
add_shortcode('harcinreg-form', [
    $shortcodesController,
    'registrationForm',
]);
add_shortcode('harcinreg-list', [
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
