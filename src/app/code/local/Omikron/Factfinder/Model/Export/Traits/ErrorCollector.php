<?php


/**
 * Trait Omikron_Factfinder_Model_Export_ErrorCollector
 */
trait Omikron_Factfinder_Model_Export_ErrorCollector
{
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
     * @param Omikron_Factfinder_Model_Export_Feed_Error $error
     */
    public function addError(Omikron_Factfinder_Model_Export_Feed_Error $error)
    {
        $this->errors[] = $error;
    }
}
