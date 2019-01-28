<?php

/**
 * Represents a single feed row
 * Class Omikron_Factfinder_Model_Export_Feed_Row
 */
class Omikron_Factfinder_Model_Export_Feed_Row
{
    /**
     * Row constructor.
     *
     * @param array $content
     */
    public function __construct($content)
    {
        $this->content = $content;
    }

    /**
     * @var array
     */
    protected $content;

    /**
     * @param $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return array
     */
    public function getContent()
    {
        return $this->content;
    }
}
