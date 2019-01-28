<?php

/**
 * Represent the specific feed type build strategy
 * Interface Omikron_Factfinder_Model_Export_BuildFeedStrategyInterface
 */
interface Omikron_Factfinder_Model_Export_BuildFeedStrategyInterface
{
    /**
     * Build feed for objects of specified entity
     *
     * @param Mage_Core_Model_Store $store
     * @return array
     */
    public function buildFeed(Mage_Core_Model_Store $store);

    /**
     * Get the channel name
     *
     * @return string
     */
    public function getChannel();

    /**
     * Check if specific feed export is enabled
     *
     * @return bool
     */
    public function isEnabled();

    /**
     * If two different entities are merged in one feed file, the corresponding column which is used in FACT-Finder
     * as main article number should be also filled with merged entities identifiers
     *
     * @param array $feedRow
     * @return array
     */
    public function mergeMainArticleColumn(array $feedRow);
}
