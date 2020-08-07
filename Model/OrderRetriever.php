<?php
declare(strict_types=1);

namespace Martinshaw\CLIOrderUpdater\Model;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Martinshaw\CLIOrderUpdater\Api\OrderRetrieverInterface;

class OrderRetriever implements OrderRetrieverInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var FilterBuilder
     */
    private $filterBuilder;
    /**
     * @var FilterGroupBuilder
     */
    private $filterGroupBuilder;

    /**
     * OrderRetriever constructor.
     * @param OrderRepositoryInterface $orderRepository
     * @param FilterBuilder $filterBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        FilterBuilder $filterBuilder,
        FilterGroupBuilder $filterGroupBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
    }


    /**
     * @param string $identifier
     * @return OrderInterface[]
     */
    public function execute($identifier)
    {
        $emailFilter = $this->filterBuilder->setField('customer_email')
            ->setValue($identifier)
            ->setConditionType('eq')
            ->create();
        $idFilter = $this->filterBuilder->setField('entity_id')
            ->setValue($identifier)
            ->setConditionType('eq')
            ->create();

        $filterGroup = $this->filterGroupBuilder->addFilter($emailFilter)
            ->addFilter($idFilter)
            ->create();

        $searchCriteria = $this->searchCriteriaBuilder->setFilterGroups([$filterGroup])
            ->create();

        $result = $this->orderRepository->getList($searchCriteria);
        return $result->getItems();
    }
}
