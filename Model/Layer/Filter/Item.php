<?php

namespace MyCompany\LayerNavigationFilter\Model\Layer\Filter;

class Item extends \Magento\Catalog\Model\Layer\Filter\Item
{

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * Construct
     *
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Theme\Block\Html\Pager $htmlPagerBlock
     */
    public function __construct(
        \Magento\Framework\UrlInterface $url,
        \Magento\Theme\Block\Html\Pager $htmlPagerBlock,
        \Magento\Framework\App\RequestInterface $request,

    )
    {
        parent::__construct($url, $htmlPagerBlock);
        $this->request = $request;
    }

    /**
     * Get filter item url
     *
     * @return string
     */
    public function getUrl()
    {
        if ($this->getFilter()->getRequestVar() == 'cat') {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $categoryUrl = $objectManager->create('Magento\Catalog\Model\Category')->load($this->getValue())->getUrl();
            $queryParams = $this->request->getParams();

            unset($queryParams[$this->getFilter()->getRequestVar()]);
            unset($queryParams['id']);
            unset($queryParams[$this->_htmlPagerBlock->getPageVarName()]);

            if (empty($queryParams)) {
                return $categoryUrl;
            }

            $separator = strpos($categoryUrl, '?') === false ? '?' : '&';

            return $categoryUrl . $separator . http_build_query($queryParams);
        }

        $query = array(
            $this->getFilter()->getRequestVar() => $this->getValue(),
            $this->_htmlPagerBlock->getPageVarName() => null // exclude current page from urls
        );

        return $this->_url->getUrl('*/*/*', array('_current'=>true, '_use_rewrite'=>true, '_query'=>$query));
    }

}
