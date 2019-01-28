<?php

/**
 * Represents feed file
 *
 * Class Feed
 */
class Omikron_Factfinder_Model_Export_Feed
{
    /**
     * @var Omikron_Factfinder_Model_Export_Feed_Row[]
     */
    protected $header = [];

    /**
     * @var Omikron_Factfinder_Model_Export_Feed_Row[]
     */
    protected $rows = [];

    /**
     * @var string
     */
    protected $fileName;

    /**
     * Feed constructor.
     *
     * @param Omikron_Factfinder_Model_Export_Feed_Row $header
     */
    public function __construct(Omikron_Factfinder_Model_Export_Feed_Row $header)
    {
        $keys = array_map(function ($element) {
            return ucfirst(implode('', array_map('ucfirst', explode('_', $element))));
        },  $header->getContent());

        $header->setContent(array_combine($keys, $keys));

        $this->header = $header;
    }

    /**
     * @return Omikron_Factfinder_Model_Export_Feed_Row
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param Omikron_Factfinder_Model_Export_Feed_Row $row
     * @return $this
     */
    public function addRow(Omikron_Factfinder_Model_Export_Feed_Row $row)
    {
        $this->rows[] = $row;

        return $this;
    }

    /**
     * @return Omikron_Factfinder_Model_Export_Feed_Row[]
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * Return feed as array of rows
     *
     * @param bool $header
     * @return array
     */
    public function getContent($header = true)
    {
        $output = [];
        if ($header) {
            $output = [$this->getHeader()->getContent()];
        }
        foreach ($this->rows as $row) {
            $output[] = $row->getContent();
        }

        return $output;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param $fileName
     * @return $this
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param Error $error
     */
    public function addError(Error $error)
    {
        $this->errors[] = $error;
    }

    /**
     * Merge two feed into one using specific Main Article Column merging strategy
     *
     * @param Feed                                                $feed
     * @param Omikron_Factfinder_Model_BuildFeedStrategyInterface $buildStrategy
     * @return $this
     */
    public function merge(Feed $feed, Omikron_Factfinder_Model_BuildFeedStrategyInterface $buildStrategy)
    {
        $header        = $feed->getHeader()->getContent();
        $headerCurrent = $this->getHeader()->getContent();
        $placeholder   = array_fill_keys($feed->getHeader()->getContent(), '');
        $newHeader     = array_merge($headerCurrent, $header);
        $this->header  = new Omikron_Factfinder_Model_Export_Feed_Row($newHeader);

        foreach ($this->getRows() as &$row) {
            $rowContent = $row->getContent();
            $newContent = array_merge($rowContent, $placeholder);
            $row->setContent($newContent);
        }

        $placeholder = array_fill_keys($headerCurrent, '');

        foreach ($feed->getRows() as $row) {
            $rowContent = $row->getContent();
            $newContent = $buildStrategy->mergeMainArticleColumn(array_merge($placeholder, $rowContent));

            $this->addRow(new Omikron_Factfinder_Model_Export_Feed_Row($newContent));
        }

        return $this;
    }
}
