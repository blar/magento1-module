<?php

/**
 * Class Omikron_Factfinder_Model_Export_Feed_Error
 */
class Omikron_Factfinder_Model_Export_Feed_Error
{
    /**
     * @var string
     */
    protected $location;

    /**
     * @var string
     */
    protected $message;

    /**
     * Error constructor.
     *
     * @param string $location
     * @param string $message
     */
    public function __construct($location, $message)
    {
        $this->location = $location;
        $this->message  = $message;
    }

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function __toString()
    {
       return $this->getLocation() . ' : ' . $this->getMessage();
    }
}
