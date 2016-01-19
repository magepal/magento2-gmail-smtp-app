<?php
/**
 * MagePal_GmailSmtpApp Magento component
 *
 * @category    MagePal
 * @package     MagePal_GmailSmtpApp
 * @author      MagePal Team <info@magepal.com>
 * @copyright   MagePal (http://www.magepal.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace MagePal\GmailSmtpApp\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\ObjectManagerInterface
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager
    )
    {
        $this->_objectManager = $objectManager;
        parent::__construct($context);
    }
    
    /**
     * Get system config password
     * 
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE $store
     */
    public function getConfigPassword($store_id = null){
        return $this->scopeConfig->getValue('system/gmailsmtpapp/password', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store_id);
    }
    
    /**
     * Get system config username
     * 
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE $store
     */
    public function getConfigUsername($store_id = null){
        return $this->scopeConfig->getValue('system/gmailsmtpapp/username', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store_id);
    }    
    
    /**
     * Get system config password
     * 
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE $store
     */
    public function getConfigAuth($store_id = null){
        return $this->scopeConfig->getValue('system/gmailsmtpapp/auth', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store_id);
    }
    
    /**
     * Get system config ssl
     * 
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE $store
     */
    public function getConfigSsl($store_id = null){
        return $this->scopeConfig->getValue('system/gmailsmtpapp/ssl', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store_id);
    }
    
    /**
     * Get system config password
     * 
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE $store
     */
    public function getConfigSmtpHost($store_id = null){
        return $this->scopeConfig->getValue('system/gmailsmtpapp/smtphost', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store_id);
    }
    
}