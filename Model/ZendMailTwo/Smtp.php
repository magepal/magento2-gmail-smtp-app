<?php
/**
 * Copyright © MagePal LLC. All rights reserved.
 * See COPYING.txt for license details.
 * http://www.magepal.com | support@magepal.com
 */

namespace MagePal\GmailSmtpApp\Model\ZendMailTwo;

use Exception;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\MessageInterface;
use Magento\Framework\Phrase;
use MagePal\GmailSmtpApp\Helper\Data;
use MagePal\GmailSmtpApp\Model\Store;
use Zend\Mail\AddressList;
use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;

/**
 * Class Smtp
 * For Magento > 2.2.7
 * @package MagePal\GmailSmtpApp\Model\ZendMailTwo
 */

class Smtp
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
     */
    public function sendSmtpMessage(
        MessageInterface $message
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
            if (is_string($returnPathEmail)) {
                $message->setSender(trim($returnPathEmail));
            } elseif ($returnPathEmail instanceof AddressList) {
                foreach ($returnPathEmail as $address) {
                    $message->setSender($address);
                }
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
        } catch (Exception $e) {
            throw new MailException(
                new Phrase($e->getMessage()),
                $e
            );
        }
    }
}
