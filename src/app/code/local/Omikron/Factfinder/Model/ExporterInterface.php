<?php

/**
 * Represents exporter for specific entity type.
 *
 * Interface Omikron_Factfinder_Model_ExporterInterface
 */
interface Omikron_Factfinder_Model_ExporterInterface
{
    const FEED_PATH               = 'factfinder/';
    const FEED_FILE_CSV_FILE_TYPE = '.csv';

    /**
     * Export all objects of specified entity for all stores
     * to separate file, upload it to FACT-Finder server and trigger import
     * with the newly uploaded file
     *
     * @return Result
     */
    public function export();

    /**
     * Export all objects of specified entity for a given store
     *
     * @param $store
     * @return Result
     */
    public function exportForStore($store = null);

    /**
     * @return Result[]
     */
    public function isEnabled();

    /**
     * Set correct store context
     *
     * @param Mage_Core_Model_Store $store
     * @return ExporterInterface
     */
    public function setStore(Mage_Core_Model_Store $store);
}
