<?php

namespace ScoutUnitsList\Manager;

use ScoutUnitsList\Exception\MigrationException;
use ScoutUnitsList\Manager\DbManager;

/**
 * Migration manager
 */
class MigrationManager
{
    /** @const string */
    const VERSION_EMPTY = 'empty';

    /** @const string */
    const VERSION_PATTERN = '[0-9]{12}';

    /** @var DbManager */
    private $db;

    /** @var string */
    private $paramName;

    /** @var string */
    private $dirPath;

    /** @var string */
    private $fileNamePrefix;

    /**
     * Constructor
     *
     * @param DbManager $db              database manager
     * @param string    $paramName       parameter name
     * @param string    $dirPath         directory path
     * @param string    $fileNamePrefix  file name prefix
     */
    public function __construct(DbManager $db, $paramName, $dirPath, $fileNamePrefix = 'Version')
    {
        $this->db = $db;
        $this->paramName = $paramName;
        $this->dirPath = $dirPath;
        $this->fileNamePrefix = $fileNamePrefix;
    }

    /**
     * Status
     *
     * @return stdClass
     */
    public function status()
    {
        $existedVersions = $this->getExistedVersions();
        $lastVersion = count($existedVersions) > 0 ? end($existedVersions) : null;

        $implementedVersions = $this->getImplementedVersions();
        $currentVersion = count($implementedVersions) > 0 ? end($implementedVersions) : null;

        $missedVersions = [];
        $newVersions = [];
        foreach (array_diff($existedVersions, $implementedVersions) as $version) {
            if ($version < $currentVersion) {
                $missedVersions[] = $version;
            } else {
                $newVersions[] = $version;
            }
        }

        $status = (object) [
            'current' => $currentVersion,
            'existed' => $existedVersions,
            'implemented' => $implementedVersions,
            'last' => $lastVersion,
            'missed' => $missedVersions,
            'new' => $newVersions,
            'unknown' => array_diff($implementedVersions, $existedVersions),
        ];

        return $status;
    }

    /**
     * Migrate
     *
     * @param string $outputVersion output version
     *
     * @return self
     *
     * @throws MigrationException
     */
    public function migrate($outputVersion = null, $removeUnknown = false)
    {
        $status = $this->status();
        if (!isset($outputVersion)) {
            $outputVersion = $status->last;
        }
        $hasUnknown = count($status->unknown) > 0;

        if ($outputVersion == $status->current) {
            return $this;
        } elseif (count($status->existed) == 0) {
            throw new MigrationException('There are no migration files.');
        } elseif ($outputVersion != self::VERSION_EMPTY && !in_array($outputVersion, $status->existed)) {
            throw new MigrationException(sprintf('There is no migration version %s.', $outputVersion));
        } elseif ($hasUnknown && !$removeUnknown) {
            throw new MigrationException('There are unknown migrations on list. You have to remove it first.');
        }

        $implementedVersions = $this->getImplementedVersions();
        if ($hasUnknown) {
            foreach ($implementedVersions as $i => $version) {
                if (in_array($version, $status->unknown)) {
                    unset($implementedVersions[$i]);
                }
            }
        }

        $versions = [];
        if ($outputVersion == self::VERSION_EMPTY) {
            $direction = 'down';
            foreach ($status->implemented as $version) {
                $versions[] = $version;
            }
        } elseif ($outputVersion < $status->current) {
            $direction = 'down';
            foreach (array_diff($status->implemented, $status->missed) as $version) {
                if ($outputVersion < $version) {
                    $versions[] = $version;
                }
            }
        } else {
            $direction = 'up';
            foreach ($status->new as $version) {
                if ($outputVersion >= $version) {
                    $versions[] = $version;
                }
            }
        }

        if ($direction == 'down') {
            rsort($versions);
        }
        foreach ($versions as $version) {
            $migrationClass = 'ScoutUnitsList\\Migration\\' . $this->getFileName($version);
            $migration = new $migrationClass($this->db);
            $migration->$direction();
        }
        $newImplementedVersions = $direction == 'down' ? array_diff($implementedVersions, $versions) :
            array_merge($implementedVersions, $versions);

        $this->setImplementedVersions($newImplementedVersions);

        return $this;
    }

    /**
     * Diff
     *
     * @return self
     */
    public function diff()
    {
        // @TODO: Rebuild it after implementing mechanysm which can compare existed vs. desired structure
        $this->createMigrationFile('        // add SQL queries', '        // add SQL queries');

        return $this;
    }

    /**
     * Get existed versions
     *
     * @return array
     */
    private function getExistedVersions()
    {
        $versions = [];
        if (is_dir($this->dirPath) && $dir = opendir($this->dirPath)) {
            while (false !== $file = readdir($dir)) {
                if (preg_match('#^' . $this->fileNamePrefix . self::VERSION_PATTERN . '\.php$#', $file)) {
                    $fileParts = explode('.', $file);
                    $fileName = array_shift($fileParts);
                    $versions[] = $this->getVersion($fileName);
                }
            }
            closedir($dir);
        }

        return $versions;
    }

    /**
     * Get implemented versions
     *
     * @return array
     */
    private function getImplementedVersions()
    {
        $versionString = get_option($this->paramName, '');
        $versions = $this->normalizeVersions(empty($versionString) ? [] : explode(',', $versionString));

        return $versions;
    }

    /**
     * Set implemented versions
     *
     * @param array $versions versions
     *
     * @return array
     */
    private function setImplementedVersions(array $versions)
    {
        if (count($versions) > 0) {
            update_option($this->paramName, implode(',', $this->normalizeVersions($versions)), false);
        } else {
            delete_option($this->paramName);
        }

        return $this;
    }

    /**
     * Normalize versions
     *
     * @param array $versions versions
     *
     * @return array
     */
    private function normalizeVersions(array $versions)
    {
        foreach ($versions as $i => $version) {
            if (!preg_match('#^' . self::VERSION_PATTERN . '$#', $version)) {
                unset($versions[$i]);
            }
        }
        sort($versions);

        return $versions;
    }

    /**
     * Get file name
     *
     * @param string $version version
     *
     * @return string
     */
    private function getFileName($version)
    {
        $fileName = $this->fileNamePrefix . $version;

        return $fileName;
    }

    /**
     * Get version
     *
     * @param string $fileName file name
     *
     * @return string
     */
    private function getVersion($fileName)
    {
        $version = substr($fileName, strlen($this->fileNamePrefix));

        return $version;
    }

    /**
     * Create migration file
     *
     * @param string $up   up
     * @param string $down down
     *
     * @return self
     */
    private function createMigrationFile($up, $down)
    {
        $fileName = $this->getFileName(date('YmdHi'));
        file_put_contents($this->dirPath . '/' . $fileName . '.php', '<?php

namespace ScoutUnitsList\Migration;

/**
 * Migration ' . date('Y-m-d H:i') . '
 */
class ' . $fileName . ' extends Migration
{
    /**
     * Up
     */
    public function up()
    {' . "\n" . $up . "\n" . '    }

    /**
     * Down
     */
    public function down()
    {' . "\n" . $down . "\n" . '    }
}
');

        return $this;
    }
}
