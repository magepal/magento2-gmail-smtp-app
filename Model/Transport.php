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
     * @param MessageInterface $message
     * @param null $parameters
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @throws \InvalidArgumentException
     */
    public function __construct(\Magento\Framework\Mail\MessageInterface $message, \MagePal\GmailSmtpApp\Helper\Data $dataHelper)
    {
        if (!$message instanceof \Zend_Mail) {
            throw new \InvalidArgumentException('The message should be an instance of \Zend_Mail');
        }

         $smtpHost = $dataHelper->getConfigSmtpHost(\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
         
         $smtpConf = array(
            'auth' => strtolower($dataHelper->getConfigAuth(\Magento\Store\Model\ScopeInterface::SCOPE_STORE)),
            'ssl' => $dataHelper->getConfigSsl(\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
            'username' => $dataHelper->getConfigUsername(\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
            'password' => $dataHelper->getConfigPassword(\Magento\Store\Model\ScopeInterface::SCOPE_STORE)
         );
                 
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
