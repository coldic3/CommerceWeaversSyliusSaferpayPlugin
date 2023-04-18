<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusSaferpayPlugin\DependencyInjection;

use CommerceWeavers\SyliusSaferpayPlugin\Entity\TransactionLog;
use CommerceWeavers\SyliusSaferpayPlugin\Entity\TransactionLogInterface;
use CommerceWeavers\SyliusSaferpayPlugin\Repository\TransactionLogRepository;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Resource\Factory\Factory;
use Sylius\Plus\Returns\Application\Factory\ReturnRequestUnitFactory;
use Sylius\Plus\Returns\Domain\Model\ReturnRequestImage;
use Sylius\Plus\Returns\Domain\Model\ReturnRequestImageInterface;
use Sylius\Plus\Returns\Domain\Model\ReturnRequestUnit;
use Sylius\Plus\Returns\Domain\Model\ReturnRequestUnitInterface;
use Sylius\Plus\Returns\Infrastructure\Doctrine\ORM\ReturnRequestUnitRepository;
use Sylius\Plus\Returns\Infrastructure\Form\Type\ReturnRequestImageType;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('commerce_weavers_sylius_saferpay');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('driver')->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM)->end()
                ->scalarNode('api_base_url')->defaultValue('https://saferpay.com/api/')->end()
                ->scalarNode('test_api_base_url')->defaultValue('https://test.saferpay.com/api/')->end()
            ->end()
        ;

        $this->addResourcesSection($rootNode);

        return $treeBuilder;
    }

    private function addResourcesSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('resources')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('transaction_log')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(TransactionLog::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(TransactionLogInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(TransactionLogRepository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                        ->scalarNode('form')->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }
}
