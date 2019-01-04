<?php
/**
     * Copyright © MagePal LLC. All rights reserved.
     * See COPYING.txt for license details.
     * http://www.magepal.com | support@magepal.com
     */

namespace MagePal\GmailSmtpApp\Model\TwoDotThree;

use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;

/**
 * Class Smtp
 * For Magento 2.3.x
 * @package MagePal\GmailSmtpApp\Model\TwoDotTwo
 */

class Smtp
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
     */
    public function sendSmtpMessage(
        \Magento\Framework\Mail\MessageInterface $message
    ) {
        $dataHelper = $this->dataHelper;
        $dataHelper->setStoreId($this->storeModel->getStoreId());

        $message = Message::fromString($message->getRawMessage());
        $message->getHeaders()->setEncoding('utf-8');

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
            foreach ($returnPathEmail as $address) {
                $message->setSender($address);
            }
        }

        if ($message->getReplyTo() === null && $dataHelper->getConfigSetReplyTo()) {
            foreach ($returnPathEmail as $address) {
                $message->setReplyTo($address);
            }
        }

        if ($returnPathEmail !== null && $dataHelper->getConfigSetFrom()) {
            foreach ($returnPathEmail as $address) {
                $message->setFrom($address);
            }
        }

        if (!$message->getFrom()->count()) {
            $result = $this->storeModel->getFrom();
            $message->setFrom($result['email'], $result['name']);
        }

        //set config
        $options   = new SmtpOptions([
            'name' => $dataHelper->getConfigName(),
            'host' => $dataHelper->getConfigSmtpHost(),
            'port' => $dataHelper->getConfigSmtpPort(),
        ]);

        $connectionConfig = [];

        $auth = strtolower($dataHelper->getConfigAuth());
        if ($auth != 'none') {
            $options->setConnectionClass($auth);

            $connectionConfig = [
                'username' => $dataHelper->getConfigUsername(),
                'password' => $dataHelper->getConfigPassword()
            ];
        }

        $ssl = $dataHelper->getConfigSsl();
        if ($ssl != 'none') {
            $connectionConfig['ssl'] = $ssl;
        }

        if (!empty($connectionConfig)) {
            $options->setConnectionConfig($connectionConfig);
        }

        try {
            $transport = new SmtpTransport();
            $transport->setOptions($options);
            $transport->send($message);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\MailException(
                new \Magento\Framework\Phrase($e->getMessage()),
                $e
            );
        }
    }
}
