<?php
/**
 * Copyright © MagePal LLC. All rights reserved.
 * See COPYING.txt for license details.
 * http://www.magepal.com | support@magepal.com
 */

namespace MagePal\GmailSmtpApp\Model;

class Email
{
    const XML_PATH_EMAIL_TEMPLATE_ZEND_TEST  = 'system/gmailsmtpapp/zend_email_template';

    const XML_PATH_EMAIL_TEMPLATE_MAGENTO_TEST  = 'system/gmailsmtpapp/magento_email_template';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \MagePal\GmailSmtpApp\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Magento\Framework\Mail\Template\Factory
     */
    protected $templateFactory;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    private $templateVars = [];

    private $templateOptions = [];

    /**
     * Template Model
     *
     * @var string
     */
    private $templateModel;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @param \MagePal\GmailSmtpApp\Helper\Data $dataHelper
     * @param \Magento\Framework\Mail\Template\Factory $templateFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     */
    public function __construct(
        \MagePal\GmailSmtpApp\Helper\Data $dataHelper,
        \Magento\Framework\Mail\Template\Factory $templateFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
    ) {
        $this->dataHelper = $dataHelper;
        $this->templateFactory = $templateFactory;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
    }

    /**
     * [generateTemplate description]  with template file and templates variables values
     * @param  Mixed $senderInfo
     * @param  Mixed $receiverInfo
     * @return $this
     */
    public function generateTemplate($senderInfo, $receiverInfo)
    {
        $templateId = $this->getTemplateId(self::XML_PATH_EMAIL_TEMPLATE_MAGENTO_TEST);
        $this->getTransportBuilder()
            ->setTemplateIdentifier($templateId)
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_ADMINHTML,
                    'store' => $this->getStore()->getId(),
                ]
            )
            ->setTemplateVars($this->templateVars)
            ->setFrom($senderInfo)
            ->addTo($receiverInfo['email'], $receiverInfo['name']);

        return $this;
    }

    /**
     * @param $senderInfo
     * @param $receiverInfo
     * @throws \Magento\Framework\Exception\MailException
     */
    public function send($senderInfo, $receiverInfo)
    {
        $this->inlineTranslation->suspend();
        $this->generateTemplate($senderInfo, $receiverInfo);
        $transport = $this->_transportBuilder->getTransport();
        $result = $transport->sendMessage();
        $this->inlineTranslation->resume();

        return $result;
    }

    /**
     * @return $this
     */
    protected function getTemplate()
    {
        $this->setTemplateOptions(
            [
                'area' => \Magento\Framework\App\Area::AREA_ADMINHTML,
                'store' => $this->getStore()->getId(),
            ]
        );

        $templateIdentifier = $this->getTemplateId(self::XML_PATH_EMAIL_TEMPLATE_ZEND_TEST);

        return $this->templateFactory->get($templateIdentifier, $this->templateModel)
                        ->setVars($this->templateVars)
                        ->setOptions($this->templateOptions);
    }

    /**
     * @return mixed
     */
    public function getEmailBody()
    {
        return $this->getTemplate()->processTemplate();
    }

    /**
     * Return template id according to store
     *
     * @param $xmlPath
     * @return mixed
     */
    public function getTemplateId($xmlPath)
    {
        return $this->getConfigValue($xmlPath, $this->getStore()->getStoreId());
    }

    /**
     * Return store configuration value of your template field that which id you set for template
     *
     * @param string $path
     * @param int $storeId
     * @return mixed
     */
    protected function getConfigValue($path, $storeId)
    {
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Return store
     *
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getStore()
    {
        return $this->storeManager->getStore();
    }

    /**
     * @param mixed $templateVars
     * @return Email
     */
    public function setTemplateVars($templateVars)
    {
        $this->templateVars = (array) $templateVars;
        return $this;
    }

    /**
     * @param mixed $templateOptions
     * @return Email
     */
    public function setTemplateOptions($templateOptions)
    {
        $this->templateOptions = (array) $templateOptions;
        return $this;
    }

    /**
     * Set template model
     *
     * @param string $templateModel
     * @return $this
     */
    public function setTemplateModel($templateModel)
    {
        $this->templateModel = $templateModel;
        return $this;
    }

    /**
     * @return \Magento\Framework\Mail\Template\TransportBuilder
     */
    public function getTransportBuilder()
    {
        return $this->_transportBuilder;
    }
}
