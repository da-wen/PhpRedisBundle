<?php

namespace Dawen\Bundle\PhpRedisBundle\Tests\Fixtures;

use Dawen\Bundle\PhpRedisBundle\Tests\Kernel\TestKernel;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\HttpKernel;

abstract class AbstractKernelAwareTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HttpKernel
     */
    protected $kernel;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @return null
     */
    public function setUp()
    {
        $this->kernel = TestKernel::getKernel();
        assert($this->kernel !== null);
        $this->kernel->boot();

        $this->container = $this->kernel->getContainer();

        parent::setUp();
    }

    /**
     * @return null
     */
    public function tearDown()
    {
        $this->kernel->shutdown();

        parent::tearDown();
    }

}