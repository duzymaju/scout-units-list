<?php

namespace ScoutUnitsList\Controller;

use Exception;
use ScoutUnitsList\Manager\MigrationManager;

/**
 * Install controller
 */
class InstallController extends Controller
{
    /**
     * Activate
     */
    public function activate()
    {
        if (version_compare(PHP_VERSION, '5.6.0', '<')) {
            return $this->error('PHP version 5.6 or higher is required to properly activate and work of this plugin.');
        }

        try {
            $this->get('manager.migration')
                ->migrate();
            $this->get('manager.config')
                ->save();
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Deactivate
     */
    public function deactivate()
    {
        // nothing to do
    }

    /**
     * Uninstall
     */
    public function uninstall()
    {
        try {
            $this->get('manager.migration')
                ->migrate(MigrationManager::VERSION_EMPTY);
            $this->get('manager.config')
                ->remove();
        } catch (Exception $e) {
            return $this->error(sprintf('%s Please try to uninstall plugin again or remove options and tables ' .
                'manually from database.', $e->getMessage()));
        }
    }

    /**
     * Error
     *
     * @param string $message message
     */
    private function error($message)
    {
        $pluginName = $this->loader->getName();
        if (is_plugin_active($pluginName)) {
            deactivate_plugins($pluginName);
        }
        wp_die($message);
    }
}
