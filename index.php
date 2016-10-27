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
use ScoutUnitsList\Model\Repository\PersonRepository;
use ScoutUnitsList\Model\Repository\PositionRepository;
use ScoutUnitsList\Model\Repository\UnitRepository;

global $wpdb;

require_once(dirname(__FILE__) . '/Loader.php');
$loader = Loader::run();
$loader->setName('scout-units-list')
    ->setVersion('0.1.0');

$dbManager = new DbManager($wpdb);
$loader->set('manager.config', new ConfigManager($loader->getName() . '_config'))
    ->set('repository.person', new PersonRepository($dbManager))
    ->set('repository.position', new PositionRepository($dbManager))
    ->set('repository.unit', new UnitRepository($dbManager));

// Initialization
add_action('init', [
    $loader,
    'init',
]);

// Installation
$installController = new InstallController($loader);
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
$apiController = new ApiController($loader);
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
$shortcodesController = new ShortcodesController($loader);
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
    $adminController = new AdminController($loader);
    add_action('admin_menu', [
        $adminController,
        'menu',
    ]);
}
