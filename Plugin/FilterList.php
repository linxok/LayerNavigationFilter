<?php

namespace MyCompany\LayerNavigationFilter\Plugin;

use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\FilterList as LayerFilterList;
use Magento\Framework\ObjectManagerInterface;
use MyCompany\LayerNavigationFilter\Model\Layer\Filter\Stock;

class FilterList
{
    protected $objectManager;

    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    public function afterGetFilters(
        LayerFilterList $subject,
        array $filters,
        Layer $layer
    ) {
        foreach ($filters as $filter) {
            if ($filter instanceof Stock) {
                return $filters;
            }
        }
        
        $stockFilter = $this->objectManager->create(
            Stock::class,
            ['layer' => $layer]
        );
        array_splice($filters, 1, 0, [$stockFilter]);
        return $filters;
    }
}
