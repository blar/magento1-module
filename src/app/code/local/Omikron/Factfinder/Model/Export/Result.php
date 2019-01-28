<?php

/**
 * Class Omikron_Factfinder_Model_Export_Result
 */
class Omikron_Factfinder_Model_Export_Result
{
    /**
     * @var array
     */
    protected $messages = [];

    /**
     * @var bool
     */
    protected $success = false;

    /**
     * @var Omikron_Factfinder_Model_Export_Feed
     */
    protected $feed;

    /**
     * @param $message
     *
     * @return $this
     */
    public function addMessage($message)
    {
        $this->messages[] = $message;

        return $this;
    }

    /**
     * @return bool
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * @param $success
     *
     * @return $this
     */
    public function setSuccess($success)
    {
        $this->success = boolval($success);

        return $this;
    }

    /**
     * @return Omikron_Factfinder_Model_Export_Feed
     */
    public function getFeed()
    {
        return $this->feed;
    }

    /**
     * @param Omikron_Factfinder_Model_Export_Feed $feed
     *
     * @return $this
     */
    public function setFeed(Omikron_Factfinder_Model_Export_Feed $feed)
    {
        $this->feed = $feed;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $output = '';
        foreach (array_reverse($this->messages) as $message) {
            $output .= $message . PHP_EOL;
        }

        return $output;
    }
}
