<?php
/**
 * Copyright © MagePal LLC. All rights reserved.
 * See COPYING.txt for license details.
 * https://www.magepal.com | support@magepal.com
 */

namespace MagePal\GmailSmtpApp\Mail;

use Exception;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport\TransportInterface as SymfonyTransportInterface;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Transport\Smtp\Auth\LoginAuthenticator;
use Symfony\Component\Mailer\Transport\Smtp\Auth\PlainAuthenticator;
use Symfony\Component\Mime\Message as SymfonyMessage;
use Symfony\Component\Mime\Address;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Phrase;
use MagePal\GmailSmtpApp\Helper\Data;
use MagePal\GmailSmtpApp\Model\Store;

/**
 * Class Smtp
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
     * @param SymfonyMessage $message
     * @throws MailException
     */
    public function sendSmtpMessage(
        $message
    ) {
        $dataHelper = $this->dataHelper;
        $dataHelper->setStoreId($this->storeModel->getStoreId());

        $this->setReplyToPath($message);
        $this->setSender($message);

        try {
            $host = $dataHelper->getConfigSmtpHost();
            $port = $dataHelper->getConfigSmtpPort();
            $username = $dataHelper->getConfigUsername();
            $password = $dataHelper->getConfigPassword();
            $auth = strtolower($dataHelper->getConfigAuth());
            $ssl = $dataHelper->getConfigSsl();

            $tls = ($auth !== 'none') && ($ssl !== 'starttls');

            /** @var SymfonyTransportInterface $transport */
            $transport = new EsmtpTransport($host, $port, $tls);

            if ($username) {
                $transport->setUsername($username);
            }

            if ($password) {
                $transport->setPassword($password);
            }

            switch ($auth) {
                case 'plain':
                    $transport->setAuthenticators([new PlainAuthenticator()]);
                    break;
                case 'login':
                    $transport->setAuthenticators([new LoginAuthenticator()]);
                    break;
                case 'none':
                    break;
                default:
                    throw new \InvalidArgumentException('Invalid authentication type: ' . $auth);
            }


            $mailer = new Mailer($transport);
            $mailer->send($message);

        } catch (Exception $e) {
            throw new MailException(
                new Phrase($e->getMessage()),
                $e
            );
        }
    }

    /**
     *
     * @param SymfonyMessage $message
     */
    protected function setSender($message)
    {
        $dataHelper = $this->dataHelper;
        $messageFromAddress = $this->getMessageFromAddressObject($message);

        //Set from address
        switch ($dataHelper->getConfigSetFrom()) {
            case 1:
                $setFromEmail = $messageFromAddress;
                break;
            case 2:
                $setFromEmail = $dataHelper->getConfigCustomFromEmail();
                break;
            default:
                $setFromEmail = null;
                break;
        }

        if ($setFromEmail !== null && $dataHelper->getConfigSetFrom()) {
            if ($setFromEmail instanceof Address) {
                $message->getHeaders()->addMailboxHeader('Sender', $setFromEmail);
            } elseif (!empty($setFromEmail)) {
                $name = $messageFromAddress instanceof Address ? $messageFromAddress->getName() : $setFromEmail;

                $message->getHeaders()->addMailboxHeader(
                    'Sender',
                    new Address($setFromEmail, $name)
                );
            }
        }
    }

    /**
     * @param SymfonyMessage $message
     */
    protected function setReplyToPath($message)
    {
        $dataHelper = $this->dataHelper;
        $messageFromAddress = $this->getMessageFromAddressObject($message);
        /*
         * Set reply-to path
         * 0 = No
         * 1 = From
         * 2 = Custom
         */
        switch ($dataHelper->getConfigSetReturnPath()) {
            case 1:
                $returnPathEmail = $messageFromAddress;
                break;
            case 2:
                $returnPathEmail = $dataHelper->getConfigReturnPathEmail();
                break;
            default:
                $returnPathEmail = null;
                break;
        }

        if (empty($message->getHeaders()->get('reply-to')?->getAddresses()) && $dataHelper->getConfigSetReplyTo()) {
            if ($returnPathEmail instanceof Address) {
                $message->getHeaders()->addMailboxHeader('reply-to', $returnPathEmail);
            } elseif (!empty($returnPathEmail)) {
                $name = $messageFromAddress instanceof Address ? $messageFromAddress->getName() : $returnPathEmail;

                $message->getHeaders()->addMailboxHeader(
                    'reply-to',
                    new Address($returnPathEmail, $name)
                );
            }
        }
    }

    /**
     *
     * @param SymfonyMessage $message
     * @return null|Address
     */
    protected function getMessageFromAddressObject($message)
    {
        if (!empty($fromAddresses = $message->getHeaders()->get('From')?->getAddresses())) {
            reset($fromAddresses);
            return current($fromAddresses);
        }

        return null;
    }
}
