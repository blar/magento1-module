<?php

/**
 * Class Omikron_Factfinder_Model_Export_Strategy_Product_Schema
 */
class Omikron_Factfinder_Model_Export_Strategy_Product_Schema
{
    const PRODUCT_NUMBER        = 'ProductNumber';
    const MASTER_PRODUCT_NUMBER = 'MasterProductNumber';
    const NAME                  = 'Name';
    const DESCRIPTION           = 'Description';
    const SHORT                 = 'Short';
    const PRODUCT_URL           = 'ProductUrl';
    const IMAGE_URL             = 'ImageUrl';
    const PRICE                 = 'Price';
    const MANUFACTURER          = 'Manufacturer';
    const CATEGORY_PATH         = 'CategoryPath';
    const AVAILABILITY          = 'Availability';
    const EAN                   = 'EAN';
    const MAGENTO_ENTITY_ID     = 'MagentoEntityId';

    /**
     *
     * @return array
     * @throws \ReflectionException
     */
    public static function getColumns()
    {
        $reflection = new \ReflectionClass(get_class());

        return $reflection->getConstants();
    }
}
