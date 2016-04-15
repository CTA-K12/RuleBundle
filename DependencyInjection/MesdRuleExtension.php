<?php

namespace Mesd\RuleBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class MesdRuleExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        //Setup the definition manager loader factory service
        $dmLoaderFactoryDefinition = $container->getDefinition('mesd_rule.definition_manager_loader_factory');
        $dmLoaderFactoryDefinition->addMethodCall('setSource', [$config['definitions']['source']]);
        $dmLoaderFactoryDefinition->addMethodCall('setEmName', [$config['definitions']['entity_manager']]);
        $dmLoaderFactoryDefinition->addMethodCall('setDefinitionFile', [$config['definitions']['file_path']]);
    }
}
