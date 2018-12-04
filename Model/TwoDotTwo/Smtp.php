<?php
/**
     * Copyright © MagePal LLC. All rights reserved.
     * See COPYING.txt for license details.
     * http://www.magepal.com | support@magepal.com
     */

namespace MagePal\GmailSmtpApp\Model\TwoDotTwo;

/**
 * Class Smtp
 * For Magento 2.0.x, 2.1.x, 2.2.x
 * @package MagePal\GmailSmtpApp\Model\TwoDotTwo
 */

class Smtp extends \Zend_Mail_Transport_Smtp
{
    /**
     * @var \MagePal\GmailSmtpApp\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \MagePal\GmailSmtpApp\Model\Store
     */
    protected $storeModel;

    /**
     * @param \MagePal\GmailSmtpApp\Helper\Data $dataHelper
     * @param \MagePal\GmailSmtpApp\Model\Store $storeModel
     */
    public function __construct(
        \MagePal\GmailSmtpApp\Helper\Data $dataHelper,
        \MagePal\GmailSmtpApp\Model\Store $storeModel
    ) {
        $this->dataHelper = $dataHelper;
        $this->storeModel = $storeModel;
    }

    /**
     * @param \MagePal\GmailSmtpApp\Helper\Data $dataHelper
     * @return Smtp
     */
    public function setDataHelper(\MagePal\GmailSmtpApp\Helper\Data $dataHelper)
    {
        $this->dataHelper = $dataHelper;
        return $this;
    }

    /**
     * @param \MagePal\GmailSmtpApp\Model\Store $storeModel
     * @return Smtp
     */
    public function setStoreModel(\MagePal\GmailSmtpApp\Model\Store $storeModel)
    {
        $this->storeModel = $storeModel;
        return $this;
    }

    /**
     * @param \Magento\Framework\Mail\MessageInterface $message
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Zend_Mail_Exception
     */
    public function sendSmtpMessage(
        \Magento\Framework\Mail\MessageInterface $message
    ) {
        $dataHelper = $this->dataHelper;
        $dataHelper->setStoreId($this->storeModel->getStoreId());

        if ($message instanceof \Zend_mail) {
            if ($message->getDate() === null) {
                $message->setDate();
            }
        }

        //Set reply-to path
        $setReturnPath = $dataHelper->getConfigSetReturnPath();
        switch ($setReturnPath) {
            case 1:
                $returnPathEmail = $message->getFrom();
                break;
            case 2:
                $returnPathEmail = $dataHelper->getConfigReturnPathEmail();
                break;
            default:
                $returnPathEmail = null;
                break;
        }

        if ($returnPathEmail !== null && $dataHelper->getConfigSetReturnPath()) {
            $message->setReturnPath($returnPathEmail);
        }

        if ($message->getReplyTo() === null && $dataHelper->getConfigSetReplyTo()) {
            $message->setReplyTo($returnPathEmail);
        }

        if ($returnPathEmail !== null && $dataHelper->getConfigSetFrom()) {
            $message->clearFrom();
            $message->setFrom($returnPathEmail);
        }

        if (!$message->getFrom()) {
            $result = $this->storeModel->getFrom();
            $message->setFrom($result['email'], $result['name']);
        }

        //set config
        $smtpConf = [
            'name' => $dataHelper->getConfigName(),
            'port' => $dataHelper->getConfigSmtpPort(),
        ];

        $auth = strtolower($dataHelper->getConfigAuth());
        if ($auth != 'none') {
            $smtpConf['auth'] = $auth;
            $smtpConf['username'] = $dataHelper->getConfigUsername();
            $smtpConf['password'] = $dataHelper->getConfigPassword();
        }

        $ssl = $dataHelper->getConfigSsl();
        if ($ssl != 'none') {
            $smtpConf['ssl'] = $ssl;
        }

        $smtpHost = $dataHelper->getConfigSmtpHost();
        $this->initialize($smtpHost, $smtpConf);

        try {
            parent::send($message);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\MailException(
                new \Magento\Framework\Phrase($e->getMessage()),
                $e
            );
        }
    }

    /**
     * @param string $host
     * @param array $config
     */
    public function initialize($host = '127.0.0.1', array $config = [])
    {
        if (isset($config['name'])) {
            $this->_name = $config['name'];
        }
        if (isset($config['port'])) {
            $this->_port = $config['port'];
        }
        if (isset($config['auth'])) {
            $this->_auth = $config['auth'];
        }

        $this->_host = $host;
        $this->_config = $config;
    }
}
