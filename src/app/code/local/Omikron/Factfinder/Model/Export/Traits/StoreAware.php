<?php

/**
 * Trait Omikron_Factfinder_Model_Export_StoreAware
 */
trait Omikron_Factfinder_Model_Export_StoreAware
{
    /**
     * @var Mage_Core_Model_Store
     */
    protected $store;

    /**
     * {@inheritdoc}
     *
     * @return Mage_Core_Model_Store
     * @throws \BadFunctionCallException
     */
    public function getStore()
    {
        if (!$this->store instanceof Mage_Core_Model_Store) {
            throw new \BadFunctionCallException(__('Store was not set'));
        }
        return $this->store;
    }

    /**
     * {@inheritdoc}
     *
     * @param Mage_Core_Model_Store $storeId
     */
    public function setStore(Mage_Core_Model_Store $store)
    {
        $this->store = $store;

        return $this;
    }
}
