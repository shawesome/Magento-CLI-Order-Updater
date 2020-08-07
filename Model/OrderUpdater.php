<?php
declare(strict_types=1);

namespace Martinshaw\CLIOrderUpdater\Model;

use Magento\Sales\Api\OrderRepositoryInterface;
use Martinshaw\CLIOrderUpdater\Api\OrderUpdaterInterface;
use Magento\Sales\Api\Data\OrderInterface;

class OrderUpdater implements OrderUpdaterInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * OrderUpdater constructor.
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param OrderInterface[] $orders
     * @param string $customerEmail
     */
    public function execute($orders, $customerEmail)
    {
        foreach ($orders as $order) {
            $order->setCustomerEmail($customerEmail);
            $this->orderRepository->save($order);
        }
    }
}
