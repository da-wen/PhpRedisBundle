<?php

namespace Dawen\Bundle\PhpRedisBundle\Tests\Kernel;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel
{
    /**
     * Returns an array of bundles to register.
     *
     * @return BundleInterface[] An array of bundle instances.
     *
     * @api
     */
    public function registerBundles()
    {
        $bundles = array(
        );

        return $bundles;
    }

    /**
     * Loads the container configuration.
     *
     * @param LoaderInterface $loader A LoaderInterface instance
     *
     * @api
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config.yml');
    }

    /**
     * @param bool $rebuildCache
     * @return TestKernel
     */
    public static function getKernel($rebuildCache = false)
    {
        $kernel = new TestKernel('test', true);

        if ($rebuildCache) {
            $cacheFile = $kernel->getCacheDir() . DIRECTORY_SEPARATOR . $kernel->getContainerClass() . '.php';

            touch($cacheFile, 0);
        }

        $kernel->boot();

        return $kernel;
    }
}
