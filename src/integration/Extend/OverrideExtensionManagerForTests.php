<?php

namespace Flarum\Testing\integration\Extend;

use Flarum\Extend\ExtenderInterface;
use Flarum\Extension\Extension;
use Flarum\Testing\integration\Extension\ExtensionManagerIncludeCurrent;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\ConnectionInterface;

class OverrideExtensionManagerForTests implements ExtenderInterface
{
    /**
     * IDs of extensions to boot
     */
    protected $extensions;

    protected $rollbackExistingExtensionMigrations;

    public function __construct($extensions, $rollbackExistingExtensionMigrations)
    {
        $this->extensions = $extensions;
        $this->rollbackExistingExtensionMigrations = $rollbackExistingExtensionMigrations;
    }

    public function extend(Container $container, Extension $extension = null)
    {
        $container->bind(ExtensionManager::class, ExtensionManagerIncludeCurrent::class);
        $extensionManager = $container->make(ExtensionManager::class);

        if ($this->rollbackExistingExtensionMigrations) {
            $extensionsWithMigrations = $container->make(ConnectionInterface::class)
                ->table('migrations')
                ->where('extension', '!=', null)
                ->groupBy('extension')
                ->pluck('extension');

            foreach ($extensionsWithMigrations->reverse() as $extensionId) {
                $extensionManager->uninstall($extensionId);
            }
        }

        if (count($this->extensions)) {

            foreach ($this->extensions as $extension) {
                $extensionManager->enable($extension);
            }

            $extensionManager->extend($container);
        }
    }
}
