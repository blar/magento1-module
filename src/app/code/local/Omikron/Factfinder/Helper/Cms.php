<?php

class Omikron_Factfinder_Helper_Cms
{
    const PATH_CMS_EXPORT_ENABLED     = 'factfinder/cms_export/ff_cms_export_enabled';
    const PATH_USE_SEPARATE_CHANNEL   = 'factfinder/cms_export/ff_cms_use_separate_channel';
    const PATH_ADDITIONAL_CMS_CHANNEL = 'factfinder/cms_export/ff_cms_channel';
    const PATH_DISABLE_CMS_PAGES      = 'factfinder/cms_export/ff_cms_blacklist';
    const PATH_MAIN_PRODUCT_ARTICLE   = 'factfinder/cms_export/ff_cms_merge_identiefier';
    const CMS_PAGE_ID_PREFIX          = 'P';

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var TemplateFilter
     */
    protected $templateFilter;

    /**
     * @var PageInterface
     */
    protected $page;

    /**
     * Init helper with cms page context
     *
     * @param PageInterface $page
     */
    public function init(PageInterface $page)
    {
        $this->page = $page;
    }

    /**
     * @param null|int|string $scopeCode
     *
     * @return bool
     */
    public function isCmsExportEnabled($scopeCode = null)
    {
        return boolval(Mage::getStoreConfig(self::PATH_CMS_EXPORT_ENABLED, $scopeCode));
    }

    /**
     * @param null|int|string $scopeCode
     *
     * @return bool
     */
    public function useSeparateCmsChannel($scopeCode = null)
    {
        return boolval(Mage::getStoreConfig(self::PATH_USE_SEPARATE_CHANNEL, $scopeCode));
    }

    /**
     * @param null|int|string $scopeCode
     *
     * @return string
     */
    public function getCmsBlacklist($scopeCode = null)
    {
        return Mage::getStoreConfig(self::PATH_DISABLE_CMS_PAGES, $scopeCode);
    }

    /**
     * Returns the FACT-Finder additional cms channel
     * name if uses separate channel for cms is turned on, otherwise returns standard channel name
     *
     * @param null|int|string $scopeCode
     * @return string
     */
    public function getChannel($scopeCode = null)
    {
        if ($this->useSeparateCmsChannel()) {
            return Mage::getStoreConfig(self::PATH_ADDITIONAL_CMS_CHANNEL, $scopeCode);
        } else {
            return Mage::getStoreConfig(Omikron_Factfinder_Helper_Data::PATH_CHANNEL, $scopeCode);
        }
    }

    /**
     * @param Mage_Core_Model_Store $store
     *
     * @return string
     * @throws Exception
     */
    public function getPageUrl(Mage_Core_Model_Store $store)
    {
        $this->urlBuilder->setScope($store->getId());

        return $this->urlBuilder->getUrl(null, ['_direct' => $this->getPage()->getIdentifier()]);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getPageId()
    {
        return self::CMS_PAGE_ID_PREFIX . $this->getPage()->getId();
    }

    /**
     * Sanitize cms page content
     *
     * @return string
     * @throws \Exception
     */
    public function getContent()
    {
        $content = trim(
            html_entity_decode(
                strip_tags(
                    $this->consolidateWhitespaces(
                        $this->removeStyleAndScriptTags(
                            $this->replaceReturnToSpace(
                                $this->removeMagentoTemplateDirectives($this->getPage()->getContent())
                            )
                        )
                    )
                )
            )
        );

        return $content;
    }

    /**
     * Parse content to find first image link in it
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     * @throws \Exception
     */
    public function getPageImage(Mage_Core_Model_Store $store)
    {
        $content = $this->getPage()->getContent();
        $pattern         = '/(http:\/\/|https:\/\/)[a-zA-Z0-9\.\/_]+\.(jpg|png)/';
        $matches         = [];
        preg_match($pattern, $content, $matches);

        if (isset($matches[0])) {
            return $matches[0];
        }

        $skinPattern = '/{{skin\surl=\'([a-zA-Z0-9_\/\.]+)\'}}/';
        preg_match($skinPattern, $content, $matches);

        if (isset($matches[1])) {
            return Mage::getDesign()->getSkinUrl($matches[1], array('_store' => $store->getId()));
        }

        return '';
    }

    /**
     * @param null|int|string $scopeCode
     *
     * @return string
     */
    public function getMainProductArticle($scopeCode = null)
    {
        return $this->scopeConfig->getValue(self::PATH_MAIN_PRODUCT_ARTICLE, 'store', $scopeCode);
    }

    /**
     * @param string $content
     *
     * @return string
     */
    protected function removeStyleAndScriptTags($content)
    {
        return preg_replace('#\<(?:style|script)[^\>]*\>[^\<]*\</(?:style|script)\>#siU', '', $content);
    }

    /**
     * @param string $content
     *
     * @return string
     */
    protected function removeMagentoTemplateDirectives($content)
    {
        return preg_replace('#{{[^}]*}}#siU', '', $content);
    }

    /**
     * @param string $content
     *
     * @return string
     */
    protected function consolidateWhitespaces($content)
    {
        return preg_replace('#(\s|&nbsp;)+#s', ' ', $content);
    }

    /**
     * @param string $content
     *
     * @return string
     */
    protected function replaceReturnToSpace($content)
    {
        return preg_replace('#<br\s?\/?>#', ' ', $content);
    }

    /**
     * @return Mage_Cms_Model_Page
     * @throws Exception
     */
    protected function getPage()
    {
        if ($this->page == null) {
            throw new \Exception(__('Helper was not initialized with Page. See `initialize` function'));
        }

        return $this->page;
    }
}