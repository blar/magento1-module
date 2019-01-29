<?php

/**
 * Class Omikron_Factfinder_Model_Source_CmsPages
 */
class Omikron_Factfinder_Model_Source_CmsPages
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $pages   = Mage::getModel('cms/page')->getCollection();
        $options = [];

        /** @var Mage_Cms_Model_Page $page */
        foreach ($pages as $page) {
            $options[] = [
                'value' => $page->getId(),
                'label' => $page->getTitle()
            ];
        }

        return $options;
    }
}
