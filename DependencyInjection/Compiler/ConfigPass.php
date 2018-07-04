<?php

namespace GGGGino\TourCMSBundle\DependencyInjection\Compiler;

use Allyou\ManagementBundle\Admin\MyPool;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class AdminPoolPass
 *
 * Compiler utilizato per settare le configurazioni come parametri
 *
 * @package Allyou\ManagementBundle\DependencyInjection\Compiler
 */
class ConfigPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        //$container->setParameter('allyou_managementbundle.admin.configuration.dashboards', $container->config['dashboards']);
    }
}