<?php
/**
 * Copyright © MagePal LLC. All rights reserved.
 * See COPYING.txt for license details.
 * http://www.magepal.com | support@magepal.com
 */

namespace MagePal\GmailSmtpApp\Model\ZendMailOne;

use Exception;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\MessageInterface;
use Magento\Framework\Phrase;
use MagePal\GmailSmtpApp\Helper\Data;
use MagePal\GmailSmtpApp\Model\Store;
use Zend_mail;
use Zend_Mail_Exception;
use Zend_Mail_Transport_Smtp;

/**
 * Class Smtp
 * For Magento < 2.2.8
 * @package MagePal\GmailSmtpApp\Model\ZendMailOne
 */

class Smtp extends Zend_Mail_Transport_Smtp
{
    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var Store
     */
    protected $storeModel;

    /**
     * @param Data $dataHelper
     * @param Store $storeModel
     */
    public function __construct(
        Data $dataHelper,
        Store $storeModel
    ) {
        $this->dataHelper = $dataHelper;
        $this->storeModel = $storeModel;
    }

    /**
     * @param Data $dataHelper
     * @return Smtp
     */
    public function setDataHelper(Data $dataHelper)
    {
        $this->dataHelper = $dataHelper;
        return $this;
    }

    /**
     * @param Store $storeModel
     * @return Smtp
     */
    public function setStoreModel(Store $storeModel)
    {
        $this->storeModel = $storeModel;
        return $this;
    }

    /**
     * @param MessageInterface $message
     * @throws MailException
     * @throws Zend_Mail_Exception
     */
    public function sendSmtpMessage(
        MessageInterface $message
    ) {
        $dataHelper = $this->dataHelper;
        $dataHelper->setStoreId($this->storeModel->getStoreId());

        if ($message instanceof Zend_mail) {
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
        } catch (Exception $e) {
            throw new MailException(
                new Phrase($e->getMessage()),
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
