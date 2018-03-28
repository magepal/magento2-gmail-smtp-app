<?php
/**
 * Copyright © MagePal LLC. All rights reserved.
 * See COPYING.txt for license details.
 * http://www.magepal.com | support@magepal.com
 */

namespace MagePal\GmailSmtpApp\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var null $_storeId
     */
    protected $_storeId = null;

    /** @var bool $_testMode */
    protected $_testMode = false;

    /**
     * @param null $store_id
     * @return bool
     */
    public function isActive($store_id = null)
    {
        if ($store_id == null && $this->getStoreId() > 0) {
            $store_id = $this->getStoreId();
        }

        return $this->scopeConfig->isSetFlag(
            'system/gmailsmtpapp/active',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store_id
        );
    }

    /**
     * Get local client name
     *
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE $store
     * @return string
     */
    public function getConfigName($store_id = null)
    {
        return $this->getConfigValue('name', $store_id);
    }

    /**
     * Get system config password
     *
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE $store
     * @return string
     */
    public function getConfigPassword($store_id = null)
    {
        return $this->getConfigValue('password', $store_id);
    }

    /**
     * Get system config username
     *
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE $store
     * @return string
     */
    public function getConfigUsername($store_id = null)
    {
        return $this->getConfigValue('username', $store_id);
    }

    /**
     * Get system config auth
     *
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE $store
     * @return string
     */
    public function getConfigAuth($store_id = null)
    {
        return $this->getConfigValue('auth', $store_id);
    }

    /**
     * Get system config ssl
     *
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE $store
     * @return string
     */
    public function getConfigSsl($store_id = null)
    {
        return $this->getConfigValue('ssl', $store_id);
    }

    /**
     * Get system config host
     *
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE $store
     * @return string
     */
    public function getConfigSmtpHost($store_id = null)
    {
        return $this->getConfigValue('smtphost', $store_id);
    }

    /**
     * Get system config port
     *
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE $store
     * @return string
     */
    public function getConfigSmtpPort($store_id = null)
    {
        return $this->getConfigValue('smtpport', $store_id);
    }

    /**
     * Get system config reply to
     *
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE $store
     * @return bool
     */
    public function getConfigSetReplyTo($store_id = null)
    {
        return $this->getConfigValue('set_reply_to', $store_id);
    }

    /**
     * Get system config set return path
     *
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE $store
     * @return int
     */
    public function getConfigSetReturnPath($store_id = null)
    {
        return $this->getConfigValue('set_return_path', $store_id);
    }

    /**
     * Get system config return path email
     *
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE $store
     * @return string
     */
    public function getConfigReturnPathEmail($store_id = null)
    {
        return $this->getConfigValue('return_path_email', $store_id);
    }

    /**
     * Get system config from
     *
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE $store
     * @return string
     */
    public function getConfigSetFrom($store_id = null)
    {
        return  $this->getConfigValue('set_from', $store_id);
    }

    /**
     * Get system config
     *
     * @param String path
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE $store
     * @return string
     */
    public function getConfigValue($path, $store_id = null)
    {
        //send test mail
        if ($this->isTestMode()) {
            $request = $this->_getRequest();
            return $request->getPost($path);
        }

        //return value from core config
        return $this->getScopeConfigValue(
            "system/gmailsmtpapp/{$path}",
            $store_id
        );
    }

    /**
     * @param String path
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE $store
     * @return mixed
     */
    public function getScopeConfigValue($path, $store_id = null)
    {
        //use global store id
        if ($store_id === null && $this->getStoreId() > 0) {
            $store_id = $this->getStoreId();
        }

        //return value from core config
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store_id
        );
    }

    /**
     * @return int/null
     */
    public function getStoreId()
    {
        return $this->_storeId;
    }

    /**
     * @param int/null $storeId
     */
    public function setStoreId($storeId = null)
    {
        $this->_storeId = $storeId;
    }

    /**
     * @return bool
     */
    public function isTestMode()
    {
        return (bool) $this->_testMode;
    }

    /**
     * @param bool $testMode
     */
    public function setTestMode(bool $testMode)
    {
        $this->_testMode = $testMode;
    }
}
