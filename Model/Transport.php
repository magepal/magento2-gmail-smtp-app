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
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @param MessageInterface $message
     * @param null $parameters
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @throws \InvalidArgumentException
     */
    public function __construct(\Magento\Framework\Mail\MessageInterface $message, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        if (!$message instanceof \Zend_Mail) {
            throw new \InvalidArgumentException('The message should be an instance of \Zend_Mail');
        }

        $this->_scopeConfig = $scopeConfig;

         $smtpHost = $this->_scopeConfig->getValue('system/gmailsmtpapp/smtphost', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
         $smtpConf = array(
            'auth' => $this->_scopeConfig->getValue('system/gmailsmtpapp/auth', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
            'ssl' => $this->_scopeConfig->getValue('system/gmailsmtpapp/ssl', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
            'username' => $this->_scopeConfig->getValue('system/gmailsmtpapp/username', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
            'password' => $this->_scopeConfig->getValue('system/gmailsmtpapp/password', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
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
