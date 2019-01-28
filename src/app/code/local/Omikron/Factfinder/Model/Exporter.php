<?php

/**
 * Class Omikron_Factfinder_Model_Exporter
 */
class Omikron_Factfinder_Model_Exporter implements Omikron_Factfinder_Model_ExporterInterface
{
    use Omikron_Factfinder_Model_Export_StoreAware;

    /**
     * @var Omikron_Factfinder_Model_BuildFeedStrategyInterface
     */
    protected $feedBuilder;
    /**
     * @var Omikron_Factfinder_Helper_Communication
     */
    protected $communicationHelper;

    /**
     * @var Omikron_Factfinder_Helper_Data
     */
    protected $configHelper;

    /**
     * @var Omikron_Factfinder_Helper_Upload
     */
    protected $uploadHelper;

    /**
     * @var Mage_Core_Model_Store
     */
    protected $store;

    /**
     * Omikron_Factfinder_Model_Exporter constructor.
     *
     * @param Omikron_Factfinder_Model_BuildFeedStrategyInterface $buildFeedStrategy
     */
    public function __construct(Omikron_Factfinder_Model_BuildFeedStrategyInterface $buildFeedStrategy)
    {
        $this->feedBuilder = $buildFeedStrategy;
        $this->communicationHelper = Mage::helper('factfinder/communication');
        $this->configHelper = Mage::helper('factfinder');
        $this->uploadHelper = Mage::helper('factfinder/upload');
    }

    public function export()
    {
        $results = [];
        foreach (Mage::getModel('store/store')->getCollection() as $store) {
            $result = $this->exportForStore($store);

            if ($result->getSuccess()) {
                $results[$store->getId()] = $result;
            }
        }

        return $results;
    }

    public function exportForStore($store = null)
    {
        if ($store == null) {
            $store = $this->getStore();
        } else {
            $this->setStore($store);
        }
        /** @var Omikron_Factfinder_Model_Export_Feed $feed */
        $feed = $this->feedBuilder->buildFeed($store);
        $feed->setFileName($feed->getFileName() . self::FEED_FILE_CSV_FILE_TYPE);
        $this->communicationHelper->updateFieldRoles($store, $this->getChannel());
        $this->writeFeedToFile($feed);
        $result = $this->uploadFeed($feed->getFileName());
        $this->deleteFeedFile($feed->getFileName());

        if (!$result->getSuccess()) {
            return $result;
        }

        if ($this->configHelper->isPushImportEnabled($store->getId())) { //@todo implement isPushImportEnabled
            if ($this->communicationHelper->pushImport($this->getChannel())) {
                $result->addMessage(__('Import successfully pushed.'));
            } else {
                $result->addMessage(__('Import not successful.'));
            }
        }

        return $result;
    }

    public function isEnabled()
    {
        return $this->feedBuilder->isEnabled();
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    protected function getChannel()
    {
        return $this->feedBuilder->getChannel();
    }

    /**
     * Write the feed output into a file
     *
     * @param Omikron_Factfinder_Model_Export_Feed $feed
     * @return array
     * @throws Exception
     */
    protected function writeFeedToFile(Omikron_Factfinder_Model_Export_Feed $feed)
    {
        $result = [];
        $io = new Varien_Io_File();
        $path = Mage::getBaseDir() . DS . self::FEED_PATH . DS;
        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $path));
        $io->streamOpen($feed->getFileName(), 'w+');
        $io->streamLock(true);

        foreach ($feed->getContent() as $row) {
            $io->streamWriteCsv($row, ';');
        }
        $io->streamClose();
        $io->close();

        return $result;
    }

    /**
     * Upload the specified product feed file to factfinder
     *
     * @param string $filename
     * @return array
     */
    protected function uploadFeed($filename)
    {
        $uploadResult = $this->uploadHelper->upload(Mage::getBaseDir(). DS . self::FEED_PATH . DS .
            $filename, $filename);

        return $uploadResult;
    }
}
