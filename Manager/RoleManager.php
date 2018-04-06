<?php

namespace ScoutUnitsList\Manager;

use WP_Role;

/**
 * Role manager
 */
class RoleManager
{
    /** @var array */
    protected $roles = [
        'subscriber' => null,
        'contributor' => null,
        'author' => null,
        'editor' => null,
        'administrator' => null,
        'super-admin' => null,
    ];

    /**
     * Get role
     *
     * @param string $name name
     *
     * @return WP_Role|false
     */
    protected function getRole($name)
    {
        if (!array_key_exists($name, $this->roles)) {
            return null;
        }

        if (!isset($this->roles[$name])) {
            $role = get_role($name);
            $this->roles[$name] = isset($role) ? $role : false;
        }

        return $this->roles[$name];
    }

    /**
     * Add capabilities
     *
     * @param string $roleFrom     role from
     * @param array  $capabilities capabilities
     * @param bool   $inherit      inherit
     *
     * @return self
     */
    public function addCapabilities($roleFrom, array $capabilities, $inherit = true)
    {
        $continue = false;
        foreach (array_keys($this->roles) as $name) {
            if ($continue || $name == $roleFrom) {
                $role = $this->getRole($name);
                if ($role) {
                    foreach ($capabilities as $capability) {
                        if (!array_key_exists($capability, $role->capabilities)) {
                            $role->add_cap($capability);
                        }
                    }
                }

                if ($inherit) {
                    $continue = true;
                } else {
                    break;
                }
            }
        }

        return $this;
    }
}
