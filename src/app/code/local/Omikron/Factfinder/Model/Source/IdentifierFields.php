<?php
/**
 * Class Omikron_Factfinder_Model_Source_IdentifierFields
 */
class Omikron_Factfinder_Model_Source_IdentifierFields
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => Omikron_Factfinder_Model_Export_Strategy_Product_Schema::MAGENTO_ENTITY_ID,
                'label' => Omikron_Factfinder_Model_Export_Strategy_Product_Schema::MAGENTO_ENTITY_ID
            ],
            [
                'value' => Omikron_Factfinder_Model_Export_Strategy_Product_Schema::MASTER_PRODUCT_NUMBER,
                'label' => Omikron_Factfinder_Model_Export_Strategy_Product_Schema::MASTER_PRODUCT_NUMBER
            ],
            [
                'value' => Omikron_Factfinder_Model_Export_Strategy_Product_Schema::PRODUCT_NUMBER,
                'label' => Omikron_Factfinder_Model_Export_Strategy_Product_Schema::PRODUCT_NUMBER
            ],
        ];
    }
}
