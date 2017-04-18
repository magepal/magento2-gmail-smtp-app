<?php
/**
 * Mail Transport
 * Copyright © 2015-2017 MagePal. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @category    MagePal
 * @package     MagePal_GmailSmtpApp
 * @author      MagePal Team <info@magepal.com>
 * @copyright   MagePal (http://www.magepal.com)
 */

namespace MagePal\GmailSmtpApp\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @param null $store_id
     * @return bool
     */
    public function isActive($store_id = null){
        return $this->scopeConfig->isSetFlag('system/gmailsmtpapp/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store_id);
    }
    
    /**
     * Get local client name
     *
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE $store
     * @return string
     */
    public function getConfigName($store_id = null){
        return $this->scopeConfig->getValue('system/gmailsmtpapp/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store_id);
    }

    /**
     * Get system config password
     * 
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE $store
     * @return string
     */
    public function getConfigPassword($store_id = null){
        return $this->scopeConfig->getValue('system/gmailsmtpapp/password', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store_id);
    }
    
    /**
     * Get system config username
     * 
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE $store
     * @return string
     */
    public function getConfigUsername($store_id = null){
        return $this->scopeConfig->getValue('system/gmailsmtpapp/username', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store_id);
    }    
    
    /**
     * Get system config auth
     * 
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE $store
     * @return string
     */
    public function getConfigAuth($store_id = null){
        return $this->scopeConfig->getValue('system/gmailsmtpapp/auth', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store_id);
    }
    
    /**
     * Get system config ssl
     * 
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE $store
     * @return string
     */
    public function getConfigSsl($store_id = null){
        return $this->scopeConfig->getValue('system/gmailsmtpapp/ssl', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store_id);
    }
    
    /**
     * Get system config host
     * 
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE $store
     * @return string
     */
    public function getConfigSmtpHost($store_id = null){
        return $this->scopeConfig->getValue('system/gmailsmtpapp/smtphost', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store_id);
    }

    /**
     * Get system config port
     *
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE $store
     * @return string
     */
    public function getConfigSmtpPort($store_id = null){
        return $this->scopeConfig->getValue('system/gmailsmtpapp/smtpport', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store_id);
    }
    
    /**
     * Get system config reply to
     * 
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE $store
     * @return bool
     */
    public function getConfigSetReplyTo($store_id = null){
        return $this->scopeConfig->getValue('system/gmailsmtpapp/set_reply_to', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store_id);
    }

    /**
     * Get system config set return path
     * 
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE $store
     * @return int
     */
    public function getConfigSetReturnPath($store_id = null){
        return $this->scopeConfig->getValue('system/gmailsmtpapp/set_return_path', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store_id);
    }
    
    
    /**
     * Get system config return path email
     * 
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE $store
     * @return string
     */
    public function getConfigReturnPathEmail($store_id = null){
        return $this->scopeConfig->getValue('system/gmailsmtpapp/return_path_email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store_id);
    }
    
    /**
     * Get system config from
     * 
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE $store
     * @return bool
     */
    public function getConfigSetFrom($store_id = null){
        return $this->scopeConfig->getValue('system/gmailsmtpapp/set_from', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store_id);
    }
    
}
