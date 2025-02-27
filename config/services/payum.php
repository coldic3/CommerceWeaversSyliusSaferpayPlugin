<?php

declare(strict_types=1);

use CommerceWeavers\SyliusSaferpayPlugin\Client\SaferpayClientInterface;
use CommerceWeavers\SyliusSaferpayPlugin\Payum\Action\AssertAction;
use CommerceWeavers\SyliusSaferpayPlugin\Payum\Action\AuthorizeAction;
use CommerceWeavers\SyliusSaferpayPlugin\Payum\Action\CaptureAction;
use CommerceWeavers\SyliusSaferpayPlugin\Payum\Action\RefundAction;
use CommerceWeavers\SyliusSaferpayPlugin\Payum\Action\ResolveNextCommandAction;
use CommerceWeavers\SyliusSaferpayPlugin\Payum\Action\ResolveNextRouteAction;
use CommerceWeavers\SyliusSaferpayPlugin\Payum\Action\StatusAction;
use CommerceWeavers\SyliusSaferpayPlugin\Payum\Factory\SaferpayGatewayFactory;
use CommerceWeavers\SyliusSaferpayPlugin\Payum\Provider\TokenProvider;
use CommerceWeavers\SyliusSaferpayPlugin\Payum\Provider\TokenProviderInterface;
use CommerceWeavers\SyliusSaferpayPlugin\Payum\Status\StatusChecker;
use CommerceWeavers\SyliusSaferpayPlugin\Payum\Status\StatusCheckerInterface;
use CommerceWeavers\SyliusSaferpayPlugin\Payum\Status\StatusMarker;
use CommerceWeavers\SyliusSaferpayPlugin\Payum\Status\StatusMarkerInterface;
use Payum\Core\Bridge\Symfony\Builder\GatewayFactoryBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services
        ->set(SaferpayGatewayFactory::class, GatewayFactoryBuilder::class)
        ->args([
            SaferpayGatewayFactory::class,
        ])
        ->tag('payum.gateway_factory_builder', ['factory' => 'saferpay'])
    ;

    $services
        ->set(AuthorizeAction::class)
        ->public()
        ->args([
            service(SaferpayClientInterface::class),
        ])
        ->tag('payum.action', ['factory' => 'saferpay', 'alias' => 'payum.action.authorize'])
    ;

    $services
        ->set(AssertAction::class)
        ->public()
        ->args([
            service(SaferpayClientInterface::class),
        ])
        ->tag('payum.action', ['factory' => 'saferpay', 'alias' => 'payum.action.assert'])
    ;

    $services
        ->set(CaptureAction::class)
        ->public()
        ->args([
            service(SaferpayClientInterface::class),
            service(StatusCheckerInterface::class),
        ])
        ->tag('payum.action', ['factory' => 'saferpay', 'alias' => 'payum.action.capture'])
    ;

    $services
        ->set(RefundAction::class)
        ->public()
        ->args([
            service(SaferpayClientInterface::class),
        ])
        ->tag('payum.action', ['factory' => 'saferpay', 'alias' => 'payum.action.refund'])
    ;

    $services
        ->set(ResolveNextCommandAction::class)
        ->public()
        ->args([
            service(TokenProviderInterface::class),
            service(StatusCheckerInterface::class),
        ])
        ->tag('payum.action', ['factory' => 'saferpay', 'alias' => 'payum.action.resolve_next_command'])
    ;

    $services
        ->set(ResolveNextRouteAction::class)
        ->public()
        ->args([
            service(StatusCheckerInterface::class),
        ])
        ->tag('payum.action', ['factory' => 'saferpay', 'alias' => 'payum.action.resolve_next_route'])
    ;

    $services
        ->set(StatusAction::class)
        ->public()
        ->args([
            service(StatusMarkerInterface::class),
        ])
        ->tag('payum.action', ['factory' => 'saferpay', 'alias' => 'payum.action.status'])
    ;

    $services
        ->set(StatusMarkerInterface::class, StatusMarker::class)
        ->args([
            service(StatusCheckerInterface::class),
        ])
    ;

    $services
        ->set(StatusCheckerInterface::class, StatusChecker::class)
    ;

    $services
        ->set(TokenProviderInterface::class, TokenProvider::class)
        ->public()
        ->args([
            service('payum'),
        ])
    ;
};
