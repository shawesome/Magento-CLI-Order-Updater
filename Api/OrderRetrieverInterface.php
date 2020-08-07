<?php
declare(strict_types=1);

namespace Martinshaw\CLIOrderUpdater\Api;

use Magento\Sales\Api\Data\OrderInterface;

/**
 * Interface for retrieving orders based on an identifier, which can be either an order entity ID or a customer email address.
 * @api
 */
interface OrderRetrieverInterface
{
    /**
     * @param string $identifier
     * @return OrderInterface[]
     */
    public function execute($identifier);
}
