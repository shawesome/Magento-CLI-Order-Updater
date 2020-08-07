<?php
declare(strict_types=1);

namespace Martinshaw\CLIOrderUpdater\Api;

use Magento\Sales\Api\Data\OrderInterface;

/**
 * Interface for updating an order's customer email address
 * @api
 */
interface OrderUpdaterInterface
{

    /**
     * @param OrderInterface[] $orders
     * @param string $customerEmail
     */
    public function execute($orders, $customerEmail);
}
