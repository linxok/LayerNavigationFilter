<?php

namespace MyCompany\LayerNavigationFilter\Plugin;

use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Search\Api\SearchInterface;
use MyCompany\LayerNavigationFilter\Model\StockFilterState;

class SearchResultFilter
{
    private $stockFilterState;
    private $resourceConnection;

    public function __construct(
        StockFilterState $stockFilterState,
        ResourceConnection $resourceConnection
    ) {
        $this->stockFilterState = $stockFilterState;
        $this->resourceConnection = $resourceConnection;
    }

    public function aroundSearch(
        SearchInterface $subject,
        callable $proceed,
        SearchCriteriaInterface $searchCriteria
    ) {
        $stockStatus = $this->stockFilterState->getStockStatus();
        if ($stockStatus === null) {
            return $proceed($searchCriteria);
        }

        $originalPageSize = $searchCriteria->getPageSize();
        $originalCurrentPage = $searchCriteria->getCurrentPage();

        $searchCriteria->setPageSize(10000);
        $searchCriteria->setCurrentPage(0);

        $result = $proceed($searchCriteria);

        $searchCriteria->setPageSize($originalPageSize);
        $searchCriteria->setCurrentPage($originalCurrentPage);

        $items = $result->getItems();
        if (empty($items)) {
            return $result;
        }

        $ids = [];
        foreach ($items as $item) {
            $ids[] = (int)$item->getId();
        }

        $connection = $this->resourceConnection->getConnection();
        $stockTable = $this->resourceConnection->getTableName('cataloginventory_stock_status');

        $select = $connection->select()
            ->from($stockTable, ['product_id'])
            ->where('product_id IN (?)', $ids)
            ->where('stock_status = ?', $stockStatus)
            ->where('website_id = 0');

        $validIds = $connection->fetchCol($select);
        $validIdsMap = array_flip($validIds);

        $filteredItems = [];
        foreach ($items as $item) {
            if (isset($validIdsMap[(int)$item->getId()])) {
                $filteredItems[] = $item;
            }
        }

        $result->setItems($filteredItems);
        $result->setTotalCount(count($filteredItems));

        return $result;
    }
}
