<?php

namespace Dawen\Bundle\PhpRedisBundle\Tests\Fixtures;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\HttpKernel;

require_once dirname(__DIR__).'../../../../../../app/AppKernel.php';

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
        $this->kernel = new \AppKernel('test', true);
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