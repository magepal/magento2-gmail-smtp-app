<?php
/**
 * Copyright © MagePal LLC. All rights reserved.
 * See COPYING.txt for license details.
 * https://www.magepal.com | support@magepal.com
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
    public function isActive($scopeType = null, $scopeCode = null)
    {
        //use global store
        if ($scopeType === null) {
            $scopeType = ScopeInterface::SCOPE_STORE;
        }

        //use global store id
        if ($scopeCode === null && $this->getStoreId() > 0) {
            $scopeCode = $this->getStoreId();
        }

        return $this->scopeConfig->isSetFlag(
            'system/gmailsmtpapp/active',
            $scopeType,
            $scopeCode
        );
    }

    /**
     * Get local client name
     *
     * @param null $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getConfigName($scopeType = null, $scopeCode = null)
    {
        return $this->getConfigValue('name', $scopeType, $scopeCode);
    }

    /**
     * Get system config password
     *
     * @param null $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getConfigPassword($scopeType = null, $scopeCode = null)
    {
        return $this->getConfigValue('password', $scopeType, $scopeCode);
    }

    /**
     * Get system config username
     *
     * @param null $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getConfigUsername($scopeType = null, $scopeCode = null)
    {
        return $this->getConfigValue('username', $scopeType, $scopeCode);
    }

    /**
     * Get system config auth
     *
     * @param null $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getConfigAuth($scopeType = null, $scopeCode = null)
    {
        return $this->getConfigValue('auth', $scopeType, $scopeCode);
    }

    /**
     * Get system config ssl
     *
     * @param null $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getConfigSsl($scopeType = null, $scopeCode = null)
    {
        return $this->getConfigValue('ssl', $scopeType, $scopeCode);
    }

    /**
     * Get system config host
     *
     * @param null $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getConfigSmtpHost($scopeType = null, $scopeCode = null)
    {
        return $this->getConfigValue('smtphost', $scopeType, $scopeCode);
    }

    /**
     * Get system config port
     *
     * @param null $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getConfigSmtpPort($scopeType = null, $scopeCode = null)
    {
        return $this->getConfigValue('smtpport', $scopeType, $scopeCode);
    }

    /**
     * Get system config reply to
     *
     * @param null $scopeType
     * @param null $scopeCode
     * @return bool
     */
    public function getConfigSetReplyTo($scopeType = null, $scopeCode = null)
    {
        //use global store
        if ($scopeType === null) {
            $scopeType = ScopeInterface::SCOPE_STORE;
        }

        //use global store id
        if ($scopeCode === null && $this->getStoreId() > 0) {
            $scopeCode = $this->getStoreId();
        }

        return $this->scopeConfig->isSetFlag(
            'system/gmailsmtpapp/set_reply_to',
            $scopeType,
            $scopeCode
        );
    }

    /**
     * Get system config set return path
     *
     * @param null $scopeType
     * @param null $scopeCode
     * @return int
     */
    public function getConfigSetReturnPath($scopeType = null, $scopeCode = null)
    {
        return (int) $this->getConfigValue('set_return_path', $scopeType, $scopeCode);
    }

    /**
     * Get system config return path email
     *
     * @param null $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getConfigReturnPathEmail($scopeType = null, $scopeCode = null)
    {
        return $this->getConfigValue('return_path_email', $scopeType, $scopeCode);
    }

    /**
     * Get system config from
     *
     * @param null $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getConfigSetFrom($scopeType = null, $scopeCode = null)
    {
        return  (int) $this->getConfigValue('set_from', $scopeType, $scopeCode);
    }

    /**
     * Get system config from
     *
     * @param null $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getConfigCustomFromEmail($scopeType = null, $scopeCode = null)
    {
        return  $this->getConfigValue('custom_from_email', $scopeType, $scopeCode);
    }

    /**
     * Get system config
     *
     * @param String $path path
     * @param null $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getConfigValue($path, $scopeType = null, $scopeCode = null)
    {
        //send test mail
        if ($this->isTestMode()) {
            return $this->getTestConfig($path);
        }

        //return value from core config
        return $this->getScopeConfigValue(
            "system/gmailsmtpapp/{$path}",
            $scopeType,
            $scopeCode
        );
    }

    /**
     * @param String path
     * @param null $scopeType
     * @param null $scopeCode
     * @return mixed
     */
    public function getScopeConfigValue($path, $scopeType = null, $scopeCode = null)
    {
        //use global store id
        if ($scopeType === null) {
            $scopeType = ScopeInterface::SCOPE_STORE;
        }

        //use global store id
        if ($scopeCode === null && $this->getStoreId() > 0) {
            $scopeCode = $this->getStoreId();
        }

        //return value from core config
        return $this->scopeConfig->getValue(
            $path,
            $scopeType,
            $scopeCode
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
        return $this;
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
