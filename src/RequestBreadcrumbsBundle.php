<?php

declare(strict_types=1);

namespace skvskv\LogBreadcrumbsBundle;

use Exception;
use Symfony\Component\{Config\FileLocator, HttpKernel\Bundle\Bundle};
use Symfony\Component\DependencyInjection\{ContainerBuilder, Loader\YamlFileLoader};

class RequestBreadcrumbsBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     *
     * @return void
     * @throws Exception
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/Resources/Config'));
        $loader->load('services.yaml');
    }
}
