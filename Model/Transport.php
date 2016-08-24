<?php
/**
 * Mail Transport
 * Copyright © 2015 MagePal. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MagePal\GmailSmtpApp\Model;



class Transport extends \Zend_Mail_Transport_Smtp implements \Magento\Framework\Mail\TransportInterface
{
    /**
     * @var \Magento\Framework\Mail\MessageInterface
     */
    protected $_message;


    /**
     * @param \Magento\Framework\Mail\MessageInterface $message
     * @param \MagePal\GmailSmtpApp\Helper\Data $dataHelper
     * @throws \Zend_Mail_Exception
     */
    public function __construct(\Magento\Framework\Mail\MessageInterface $message, \MagePal\GmailSmtpApp\Helper\Data $dataHelper)
    {
        if (!$message instanceof \Zend_Mail) {
            throw new \InvalidArgumentException('The message should be an instance of \Zend_Mail');
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

        if ($message->getReplyTo() === NULL && $dataHelper->getConfigSetReplyTo()) {
            $message->setReplyTo($returnPathEmail);
        }
       
        //set config
        $smtpConf = [
           'auth' => strtolower($dataHelper->getConfigAuth()),
           'ssl' => $dataHelper->getConfigSsl(),
           'username' => $dataHelper->getConfigUsername(),
           'password' => $dataHelper->getConfigPassword(),
           'port' => $dataHelper->getConfigSmtpPort(),
        ];
        
        
        $smtpHost = $dataHelper->getConfigSmtpHost();
        parent::__construct($smtpHost, $smtpConf);
        $this->_message = $message;
    }

    /**
     * Send a mail using this transport
     *
     * @return void
     * @throws \Magento\Framework\Exception\MailException
     */
    public function sendMessage()
    {

        try {
            parent::send($this->_message);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\MailException(new \Magento\Framework\Phrase($e->getMessage()), $e);
        }
    }
}
