<?php

/**
 * Class Omikron_Factfinder_Model_Export_Strategy_Cms
 */
class Omikron_Factfinder_Model_Export_Strategy_Cms implements Omikron_Factfinder_Model_Export_BuildFeedStrategyInterface
{
    use Omikron_Factfinder_Model_Export_StoreAware;

    const FEED_FILE = 'export.cms.';

    /**
     * @var Mage_Cms_Model_Resource_Page_Collection
     */
    protected $pageCollection;

    /**
     * @var Omikron_Factfinder_Helper_Data
     */
    protected $configHelper;

    /**
     * @var Omikron_Factfinder_Helper_Cms
     */
    protected $cmsHelper;

    /**
     * @var Mage_Core_Model_App_Emulation
     */
    protected $appEmulation;

    /**
     * @var array
     */
    protected $exportedAttributes;

    /**
     * Omikron_Factfinder_Model_Export_Strategy_Cms constructor.
     */
    public function __construct()
    {
        $this->pageCollection = Mage::getModel('cms/page')->getCollection();
        $this->configHelper = Mage::helper('factfinder');
        $this->cmsHelper = Mage::helper('factfinder/cms');
        $this->appEmulation =  Mage::getSingleton('core/app_emulation');
    }

    public function buildFeed(Mage_Core_Model_Store $store)
    {
        // TODO: Implement buildFeed() method.
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getChannel()
    {
        return $this->cmsHelper->getChannel($this->getStore());
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->cmsHelper->isCmsExportEnabled($this->getStore()) && $this->cmsHelper->useSeparateCmsChannel($this->getStore());
    }

    /**
     * {@inheritdoc]
     *
     * @param array $feedRow
     * @return array
     */
    public function mergeMainArticleColumn(array $feedRow)
    {
        $mainArticleNumber = $this->cmsHelper->getMainProductArticle($this->getStore()->getId());
        $feedRow[$mainArticleNumber] = $feedRow[Omikron_Factfinder_Model_Export_Strategy_Cms_Schema::PAGE_ID];

        return $feedRow;
    }
    /**
     * @return array
     * @throws \ReflectionException
     */
    protected function getAttributesToExport()
    {
        if (!empty($this->exportedAttributes)) {
            return $this->exportedAttributes;
        }
        $this->exportedAttributes = Omikron_Factfinder_Model_Export_Strategy_Product_Schema::getColumns();

        return $this->exportedAttributes;
    }

    /**
     * @param Mage_Cms_Model_Page $page
     * @return Row
     * @throws LocalizedException
     * @throws \ReflectionException
     */
    protected function buildFeedRow(Mage_Cms_Model_Page $page)
    {
        $row = [];
        $context = null;
        foreach (Omikron_Factfinder_Model_Export_Strategy_Cms_Schema::getColumns() as  $attribute) {
            if (is_callable([$this->cmsHelper, "get$attribute" ])) {
                $row[$attribute] =  $this->cmsHelper->{"get$attribute"}($this->getStore());
            } else if (is_callable([$page, "get$attribute"])) {
                $row[$attribute] =  $page->{"get$attribute"}();
            } else {
                throw new LocalizedException(__('Cant get cms page value for %1'),[$attribute]);
            }
        }

        return new Row($row);
    }

    /**
     * @return string
     */
    protected function getFeedFileName()
    {
        return self::FEED_FILE . $this->getChannel();
    }

    /**
     * @return PageSearchResultsInterface
     * @throws LocalizedException
     */
    protected function getCmsPageCollection()
    {
        return $this->pageCollection
            ->addAttributeToFilter('identifier', ['nin' => $this->cmsHelper->getCmsBlacklist($this->getStore())]);
        }
}
