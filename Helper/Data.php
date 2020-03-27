<?php
/**
 * Copyright © MagePal LLC. All rights reserved.
 * See COPYING.txt for license details.
 * http://www.magepal.com | support@magepal.com
 */

namespace MagePal\GmailSmtpApp\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    /**
     * @var null $storeId
     */
    protected $storeId = null;

    /** @var bool $testMode */
    protected $testMode = false;

    /** @var array $testConfig */
    protected $testConfig = [];

    /**
     * @param null $key
     * @return array|mixed|string
     */
    public function getTestConfig($key = null)
    {
        if ($key === null) {
            return $this->testConfig;
        } elseif (!array_key_exists($key, $this->testConfig)) {
            return '';
        } else {
            return $this->testConfig[$key];
        }
    }

    /**
     * @param null $fields
     * @return $this
     */
    public function setTestConfig($fields)
    {
        $this->testConfig = (array) $fields;
        return $this;
    }

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
            ScopeInterface::SCOPE_STORE,
            $store_id
        );
    }

    /**
     * Get local client name
     *
     * @param ScopeInterface::SCOPE_STORE $store
     * @return string
     */
    public function getConfigName($store_id = null)
    {
        return $this->getConfigValue('name', $store_id);
    }

    /**
     * Get system config password
     *
     * @param ScopeInterface::SCOPE_STORE $store
     * @return string
     */
    public function getConfigPassword($store_id = null)
    {
        return $this->getConfigValue('password', $store_id);
    }

    /**
     * Get system config username
     *
     * @param ScopeInterface::SCOPE_STORE $store
     * @return string
     */
    public function getConfigUsername($store_id = null)
    {
        return $this->getConfigValue('username', $store_id);
    }

    /**
     * Get system config auth
     *
     * @param ScopeInterface::SCOPE_STORE $store
     * @return string
     */
    public function getConfigAuth($store_id = null)
    {
        return $this->getConfigValue('auth', $store_id);
    }

    /**
     * Get system config ssl
     *
     * @param ScopeInterface::SCOPE_STORE $store
     * @return string
     */
    public function getConfigSsl($store_id = null)
    {
        return $this->getConfigValue('ssl', $store_id);
    }

    /**
     * Get system config host
     *
     * @param ScopeInterface::SCOPE_STORE $store
     * @return string
     */
    public function getConfigSmtpHost($store_id = null)
    {
        return $this->getConfigValue('smtphost', $store_id);
    }

    /**
     * Get system config port
     *
     * @param ScopeInterface::SCOPE_STORE $store
     * @return string
     */
    public function getConfigSmtpPort($store_id = null)
    {
        return $this->getConfigValue('smtpport', $store_id);
    }

    /**
     * Get system config reply to
     *
     * @param ScopeInterface::SCOPE_STORE $store
     * @return bool
     */
    public function getConfigSetReplyTo($store_id = null)
    {
        return $this->scopeConfig->isSetFlag(
            'system/gmailsmtpapp/set_reply_to',
            ScopeInterface::SCOPE_STORE,
            $store_id
        );
    }

    /**
     * Get system config set return path
     *
     * @param ScopeInterface::SCOPE_STORE $store
     * @return int
     */
    public function getConfigSetReturnPath($store_id = null)
    {
        return (int) $this->getConfigValue('set_return_path', $store_id);
    }

    /**
     * Get system config return path email
     *
     * @param ScopeInterface::SCOPE_STORE $store
     * @return string
     */
    public function getConfigReturnPathEmail($store_id = null)
    {
        return $this->getConfigValue('return_path_email', $store_id);
    }

    /**
     * Get system config from
     *
     * @param ScopeInterface::SCOPE_STORE $store
     * @return string
     */
    public function getConfigSetFrom($store_id = null)
    {
        return  (int) $this->getConfigValue('set_from', $store_id);
    }

    /**
     * Get system config from
     *
     * @param ScopeInterface::SCOPE_STORE $store
     * @return string
     */
    public function getConfigCustomFromEmail($store_id = null)
    {
        return  $this->getConfigValue('custom_from_email', $store_id);
    }

    /**
     * Get system config
     *
     * @param String path
     * @param ScopeInterface::SCOPE_STORE $store
     * @return string
     */
    public function getConfigValue($path, $store_id = null)
    {
        //send test mail
        if ($this->isTestMode()) {
            return $this->getTestConfig($path);
        }

        //return value from core config
        return $this->getScopeConfigValue(
            "system/gmailsmtpapp/{$path}",
            $store_id
        );
    }

    /**
     * @param String path
     * @param ScopeInterface::SCOPE_STORE $store
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
            ScopeInterface::SCOPE_STORE,
            $store_id
        );
    }

    /**
     * @return int/null
     */
    public function getStoreId()
    {
        return $this->storeId;
    }

    /**
     * @param int/null $storeId
     */
    public function setStoreId($storeId = null)
    {
        $this->storeId = $storeId;
    }

    /**
     * @return bool
     */
    public function isTestMode()
    {
        return (bool) $this->testMode;
    }

    /**
     * @param bool $testMode
     * @return Data
     */
    public function setTestMode($testMode)
    {
        $this->testMode = (bool) $testMode;
        return $this;
    }
}
