<?php

namespace MyCompany\LayerNavigationFilter\Model\Layer\Filter;

use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Framework\App\RequestInterface;
use MyCompany\LayerNavigationFilter\Model\StockFilterState;

class Stock extends AbstractFilter
{
    const FILTER_IN_STOCK = 1;
    const FILTER_OUT_OF_STOCK = 0;

    protected $_requestVar = 'stock';

    private $stockFilterState;
    private $resourceConnection;

    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        StockFilterState $stockFilterState,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        array $data = []
    ) {
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $data
        );
        $this->_requestVar = 'stock';
        $this->stockFilterState = $stockFilterState;
        $this->resourceConnection = $resourceConnection;
    }

    public function apply(RequestInterface $request)
    {
        $filter = $request->getParam($this->_requestVar);
        if (is_null($filter)) {
            return $this;
        }

        $filter = (int)$filter;
        if ($filter !== self::FILTER_IN_STOCK && $filter !== self::FILTER_OUT_OF_STOCK) {
            return $this;
        }

        $this->stockFilterState->setStockStatus($filter);

        $label = $filter == self::FILTER_IN_STOCK ? __('In Stock') : __('Out of Stock');

        $this->getLayer()
            ->getState()
            ->addFilter($this->_createItem($label, $filter));

        $this->setItems([]);

        return $this;
    }

    public function getName()
    {
        return __('Stock Status');
    }

    protected function _getItemsData()
    {
        if ($this->stockFilterState->getStockStatus() !== null) {
            return [];
        }

        $connection = $this->resourceConnection->getConnection();
        $stockTable = $this->resourceConnection->getTableName('cataloginventory_stock_status');

        $productCollection = $this->getLayer()->getProductCollection();
        $select = clone $productCollection->getSelect();
        $select->reset(\Magento\Framework\DB\Select::COLUMNS);
        $select->reset(\Magento\Framework\DB\Select::ORDER);
        $select->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $select->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);

        $select->joinLeft(
            ['stock_count' => $stockTable],
            'e.entity_id = stock_count.product_id AND stock_count.website_id = 0',
            []
        );

        $select->columns([
            'stock_status' => 'stock_count.stock_status',
            'count' => new \Zend_Db_Expr('COUNT(DISTINCT e.entity_id)')
        ]);
        $select->group('stock_count.stock_status');

        $stockCounts = $connection->fetchPairs($select);

        $items = [];

        if (isset($stockCounts[self::FILTER_IN_STOCK]) && $stockCounts[self::FILTER_IN_STOCK] > 0) {
            $items[] = [
                'label' => __('In Stock'),
                'value' => self::FILTER_IN_STOCK,
                'count' => $stockCounts[self::FILTER_IN_STOCK],
            ];
        }

        if (isset($stockCounts[self::FILTER_OUT_OF_STOCK]) && $stockCounts[self::FILTER_OUT_OF_STOCK] > 0) {
            $items[] = [
                'label' => __('Out of Stock'),
                'value' => self::FILTER_OUT_OF_STOCK,
                'count' => $stockCounts[self::FILTER_OUT_OF_STOCK],
            ];
        }

        return $items;
    }
}
