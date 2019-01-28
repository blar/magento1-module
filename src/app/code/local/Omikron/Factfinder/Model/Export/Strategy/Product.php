<?php

class Omikron_Factfinder_Model_Export_Strategy_Product implements Omikron_Factfinder_Model_Export_BuildFeedStrategyInterface
{
    use Omikron_Factfinder_Model_Export_StoreAware;

    const FEED_FILE                         = 'export.';
    const BATCH_SIZE                        = 3000;
    const ADDITIONAL_ATTRIBUTES_COLUMN_NAME = 'Attributes';

    /**
     * @var Mage_Catalog_Model_Resource_Product_Collection
     */
    protected $productCollection;

    /**
     * @var Omikron_Factfinder_Helper_Data
     */
    protected $configHelper;

    /**
     * @var Omikron_Factfinder_Helper_Product
     */
    protected $productHelper;

    /**
     * @var Mage_Core_Model_App_Emulation
     */
    protected $appEmulation;

    /**
     * @var array
     */
    protected $exportedAttributes;

    /**
     * Omikron_Factfinder_Model_Export_Strategy_Product constructor.
     */
    public function __construct()
    {
        $this->productCollection = Mage::getModel('catalog/product')->getCollection();
        $this->configHelper      = Mage::helper('factfinder');
        $this->productHelper     = Mage::helper('factfinder/product');
        $this->appEmulation      = Mage::getSingleton('core/app_emulation');
    }

    /**
     * {@inheritdoc}
     *
     * @param Mage_Core_Model_Store $store
     * @return array|Omikron_Factfinder_Model_Export_Feed
     * @throws ReflectionException
     */
    public function buildFeed(Mage_Core_Model_Store $store)
    {
        $this->setStore($store);
        $this->appEmulation->startEnvironmentEmulation($store->getId(), Mage_Core_Model_App_Area::AREA_ADMINHTML);
        $attributes    = $this->getAttributesToExport();
        $feed          = new Omikron_Factfinder_Model_Export_Feed(new Omikron_Factfinder_Model_Export_Feed_Row($attributes));
        $productCount  = $this->getFilteredProductCollection($store)->getSize();
        $currentOffset = 0;

        while ($currentOffset < $productCount) {
            /** @var Mage_Catalog_Model_Resource_Product_Collection $products */
            $products = $this->getProductsBatch($store, $currentOffset);
            try {
                /** @var Mage_Catalog_Model_Product $product */
                foreach ($products->getItems() as $product) {
                    $feed->addRow($this->buildFeedRow($product));
                }
            } catch (\Exception $e) {
                $feed->addError(new Error($e->getLine(), $e->getMessage()));
            }

            $currentOffset += $products->count();
        }
        $this->appEmulation->stopEnvironmentEmulation();
        $feed->setFileName($this->getFeedFileName());

        return $feed;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getChannel()
    {
        return $this->configHelper->getChannel($this->getStore());
    }

    /**
     * @return bool|void
     */
    public function isEnabled()
    {
        return $this->configHelper->isEnabled($this->getStore()); //@todo implement isEnabled
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return Row
     * @throws LocalizedException
     * @throws \ReflectionException
     */
    protected function buildFeedRow(Mage_Catalog_Model_Product $product)
    {
        $row        = [];
        foreach ($this->getAttributesToExport() as $attribute) {
            if (is_callable([$this->productHelper, "get$attribute"])) {
                $row[$attribute] = $this->productHelper->{"get$attribute"}($product, $this->getStore());
            }
        }

        return new Row($row);
    }

    /**
     * {@inheritdoc}
     *
     * @param array $feedRow
     * @throws \BadMethodCallException
     */
    public function mergeMainArticleColumn(array $feedRow)
    {
        throw new \BadMethodCallException(
            __(
                'Product feed is base of each feed and it cannot be merged with different entity.
        In a contrary, other entities feed can be merged into product feed'
            )
        );
    }

    /**
     * @return array|void
     * @throws ReflectionException
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
     * @param Mage_Core_Model_Store $store
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function getFilteredProductCollection(Mage_Core_Model_Store $store)
    {
        return $this->productCollection
            ->clear()
            ->addAttributeToFilter('visibility', ['in' => $this->productHelper->getProductVisibility($store)])
            ->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
            ->addWebsiteFilter($store->getWebsiteId())
            ->setStoreId($store->getId());
    }

    /**
     * @param Mage_Core_Model_Store $store
     * @param int $currentOffset
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function getProductsBatch(Mage_Core_Model_Store $store, $currentOffset)
    {
        $attributesToSelect = array_merge(
            $this->productHelper->getMandatoryAttributes(),
            explode(',', $this->productHelper->getAdditionalAttributes($store)),
            [$this->productHelper->getEANAttributeCode($store)],
            [$this->productHelper->getManufacturerAttributeCode($store)]
        );

        $collection = $this->getFilteredProductCollection($store)->addAttributeToSelect($attributesToSelect);
        $collection->getSelect()->limit(self::BATCH_SIZE, $currentOffset);

        return $collection;
    }

    /**
     * @return string
     */
    protected function getFeedFileName()
    {
        return self::FEED_FILE . $this->getChannel();
    }
}
