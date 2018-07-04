<?php

namespace GGGGino\TourCMSBundle;

use GGGGino\TourCMSBundle\DependencyInjection\Compiler\ConfigPass;
use GGGGino\TourCMSBundle\DependencyInjection\GGGGinoTourCMSExtension;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class GGGGinoTourCMSBundle extends Bundle
{
    /**
     * @inheritdoc
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ConfigPass());
    }

    /**
     * @inheritdoc
     */
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new GGGGinoTourCMSExtension();
        }

        return $this->extension;
    }
}
