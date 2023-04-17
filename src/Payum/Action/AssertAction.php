<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusSaferpayPlugin\Payum\Action;

use CommerceWeavers\SyliusSaferpayPlugin\Client\SaferpayClientInterface;
use CommerceWeavers\SyliusSaferpayPlugin\Payum\Request\Assert;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Sylius\Component\Core\Model\PaymentInterface;

final class AssertAction implements ActionInterface
{
    public function __construct(
        private SaferpayClientInterface $saferpayClient,
    ) {
    }

    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getModel();

        $response = $this->saferpayClient->assert($payment);

        $paymentDetails = $payment->getDetails();
        $paymentDetails['status'] = $response['Transaction']['Status'];
        $paymentDetails['transaction_id'] = $response['Transaction']['Id'];

        $payment->setDetails($paymentDetails);
    }

    public function supports($request): bool
    {
        return ($request instanceof Assert) && ($request->getModel() instanceof PaymentInterface);
    }
}
