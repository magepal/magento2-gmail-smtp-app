<?php
/**
 * Copyright © MagePal LLC. All rights reserved.
 * See COPYING.txt for license details.
 * http://www.magepal.com | support@magepal.com
 */

namespace MagePal\GmailSmtpApp\Model;

use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Template\Factory;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Mail\TemplateInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use MagePal\GmailSmtpApp\Helper\Data;

class Email
{
    const XML_PATH_EMAIL_TEMPLATE_ZEND_TEST  = 'system/gmailsmtpapp/zend_email_template';

    const XML_PATH_EMAIL_TEMPLATE_MAGENTO_TEST  = 'system/gmailsmtpapp/magento_email_template';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var Factory
     */
    protected $templateFactory;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var array
     */
    private $templateVars = [];

    /**
     * @var array
     */
    private $templateOptions = [];

    /**
     * Template Model
     *
     * @var string
     */
    private $templateModel;

    /**
     * @var StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @param Data $dataHelper
     * @param Factory $templateFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param StateInterface $inlineTranslation
     * @param TransportBuilder $transportBuilder
     */
    public function __construct(
        Data $dataHelper,
        Factory $templateFactory,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder
    ) {
        $this->dataHelper = $dataHelper;
        $this->templateFactory = $templateFactory;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
    }

    /**
     * @param  Mixed  $senderInfo
     * @param  Mixed  $receiverInfo
     * @return $this
     * @throws NoSuchEntityException
     */
    public function generateTemplate($senderInfo, $receiverInfo)
    {
        $templateId = $this->getTemplateId(self::XML_PATH_EMAIL_TEMPLATE_MAGENTO_TEST);
        $this->getTransportBuilder()
            ->setTemplateIdentifier($templateId)
            ->setTemplateOptions(
                [
                    'area' => Area::AREA_ADMINHTML,
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
     * @throws MailException
     * @throws NoSuchEntityException
     * @throws LocalizedException
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
     * @return TemplateInterface
     * @throws NoSuchEntityException
     */
    protected function getTemplate()
    {
        $this->setTemplateOptions(
            [
                'area' => Area::AREA_ADMINHTML,
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
     * @throws NoSuchEntityException
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
     * @throws NoSuchEntityException
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
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Return store
     *
     * @return StoreInterface
     * @throws NoSuchEntityException
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
     * @return TransportBuilder
     */
    public function getTransportBuilder()
    {
        return $this->_transportBuilder;
    }
}
