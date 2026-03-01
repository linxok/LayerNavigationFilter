<?php

namespace MyCompany\LayerNavigationFilter\Model;

class StockFilterState
{
    private $stockStatus = null;

    public function setStockStatus(?int $status): void
    {
        $this->stockStatus = $status;
    }

    public function getStockStatus(): ?int
    {
        return $this->stockStatus;
    }
}
