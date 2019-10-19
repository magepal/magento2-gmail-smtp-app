<?php
/**
 * Copyright © MagePal LLC. All rights reserved.
 * See COPYING.txt for license details.
 * http://www.magepal.com | support@magepal.com
 */

namespace MagePal\GmailSmtpApp\Model\TwoDotThree;

use Exception;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\EmailMessageInterface;
use Magento\Framework\Phrase;
use MagePal\GmailSmtpApp\Helper\Data;
use MagePal\GmailSmtpApp\Model\Store;
use Zend\Mail\AddressList;
use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;

/**
 * Class Smtp
 * For Magento = 2.3.3
 * @package MagePal\GmailSmtpApp\Model\TwoDotThree
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
     * @param EmailMessageInterface $message
     * @throws MailException
     */
    public function sendSmtpMessage(
        EmailMessageInterface $message
    ) {
        $dataHelper = $this->dataHelper;
        $dataHelper->setStoreId($this->storeModel->getStoreId());

        $message = Message::fromString($message->getRawMessage());

        //Set reply-to path
        switch ($dataHelper->getConfigSetReturnPath()) {
            case 1:
                $returnPathEmail = $message->getFrom()->count() ? $message->getFrom() : $this->getFromEmailAddress();
                break;
            case 2:
                $returnPathEmail = $dataHelper->getConfigReturnPathEmail();
                break;
            default:
                $returnPathEmail = null;
                break;
        }

        if (!$message->getReplyTo()->count() && $dataHelper->getConfigSetReplyTo()) {
            if (is_string($returnPathEmail)) {
                $name = $this->getFromName();
                $message->setReplyTo(trim($returnPathEmail), $name);
            } elseif ($returnPathEmail instanceof AddressList) {
                foreach ($returnPathEmail as $address) {
                    $message->setReplyTo($address);
                }
            }
        }

        //Set from address
        switch ($dataHelper->getConfigSetFrom()) {
            case 1:
                $setFromEmail = $message->getFrom()->count() ? $message->getFrom() : $this->getFromEmailAddress();
                break;
            case 2:
                $setFromEmail = $dataHelper->getConfigCustomFromEmail();
                break;
            default:
                $setFromEmail = null;
                break;
        }

        if ($setFromEmail !== null && $dataHelper->getConfigSetFrom()) {
            if (is_string($setFromEmail)) {
                $name = $this->getFromName();
                $message->setFrom(trim($setFromEmail), $name);
                $message->setSender(trim($setFromEmail), $name);
            } elseif ($setFromEmail instanceof AddressList) {
                foreach ($setFromEmail as $address) {
                    $message->setFrom($address);
                    $message->setSender($address);
                }
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

    /**
     * @return string
     */
    public function getFromEmailAddress()
    {
        $result = $this->storeModel->getFrom();
        return $result['email'];
    }

    /**
     * @return string
     */
    public function getFromName()
    {
        $result = $this->storeModel->getFrom();
        return $result['name'];
    }
}
