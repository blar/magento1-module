<?php

/**
 * Class Omikron_Factfinder_Model_Export_Strategy_Cms_Schema
 */
class Omikron_Factfinder_Model_Export_Strategy_Cms_Schema
{
    const PAGE_ID          = 'PageId';
    const IDENTIFIER       = 'Identifier';
    const TITLE            = 'Title';
    const CONTENT_HEADING  = 'ContentHeading';
    const CONTENT          = 'Content';
    const META_KEYWORDS    = 'MetaKeywords';
    const META_DESCRIPTION = 'MetaDescription';
    const PAGE_URL         = 'PageUrl';
    const PAGE_IMAGE       = 'PageImage';

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
