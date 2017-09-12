<?php
/**
 * Copyright © 2017 MagePal LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MagePal\GmailSmtpApp\Block\Adminhtml\System\Config;

class Version extends \Magento\Config\Block\System\Config\Form\Field
{

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    protected $_moduleList;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_moduleList  = $moduleList;
    }

    /**
     * Render button
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        // Remove scope label
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }


    /**
     * Return element html
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return 'v' . $this->getVersion();
    }


    /**
     * Get Module version number
     *
     * @return string
     */
    public function getVersion(){
        $moduleInfo = $this->_moduleList->getOne($this->getModuleName());
        return $moduleInfo['setup_version'];
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        $classArray = explode('\\', get_class($this));

        return count($classArray) > 2 ? "{$classArray[0]}_{$classArray[1]}" : '';
    }
}
