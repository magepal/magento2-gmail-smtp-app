<?php
/**
 * Copyright © MagePal LLC. All rights reserved.
 * See COPYING.txt for license details.
 * http://www.magepal.com | support@magepal.com
 */

namespace MagePal\GmailSmtpApp\Block\Adminhtml;

use Exception;
use Laminas\Mime\Message as MineMessage;
use Laminas\Mime\Part as MinePart;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Validator\EmailAddress;
use MagePal\GmailSmtpApp\Helper\Data;
use MagePal\GmailSmtpApp\Model\Email;
use Laminas\Mail\Message;
use Laminas\Mail\Transport\Smtp;
use Laminas\Mail\Transport\SmtpOptions;

class ValidateConfig extends Template
{
    /**
     * @var Data
     */
    protected $_dataHelper;

    /**
     * @var Email
     */
    protected $_email;

    /**
     * @var string
     */
    protected $toAddress;

    /**
     * @var string
     */
    protected $fromAddress;

    /**
     * @var string
     */
    protected $storeId;

    /**
     * @var string
     */
    protected $hash;

    /**
     * Remove values from global post and store values locally
     * @var array()
     */
    protected $configFields = [
        'active' => '',
        'name' => '',
        'auth' => '',
        'ssl' => '',
        'smtphost' => '',
        'smtpport' => '',
        'username' => '',
        'password' => '',
        'set_reply_to' => '',
        'set_from' => '',
        'set_return_path' => '',
        'return_path_email' => '',
        'custom_from_email' => '',
        'email' => '',
        'from_email' => ''
    ];

    /**
     * @var EmailAddress
     */
    protected $emailAddressValidator;

    /**
     * EmailTest constructor.
     * @param Context $context
     * @param Data $dataHelper
     * @param Email $email
     * @param EmailAddress $emailAddressValidator
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $dataHelper,
        Email $email,
        EmailAddress $emailAddressValidator,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_dataHelper = $dataHelper;
        $this->_email = $email;
        $this->emailAddressValidator = $emailAddressValidator;

        $this->init();
    }

    /**
     * @param $id
     * @return $this
     */
    public function setStoreId($id)
    {
        $this->storeId = $id;
        return $this;
    }

    /**
     * @return int \ null
     */
    public function getStoreId()
    {
        return $this->storeId;
    }

    /**
     * @param null $key
     * @return array|mixed|string
     */
    public function getConfig($key = null)
    {
        if ($key === null) {
            return $this->configFields;
        } elseif (!array_key_exists($key, $this->configFields)) {
            return '';
        } else {
            return $this->configFields[$key];
        }
    }

    /**
     * @param string $key
     * @param string $value
     * @return array|mixed|string
     */
    public function setConfig($key, $value = null)
    {
        if (array_key_exists($key, $this->configFields)) {
            $this->configFields[$key] = $value;
        }

        return $this;
    }

    /**
     * Load default config if config is lock using "bin/magento config:set"
     */
    public function loadDefaultConfig()
    {
        $request = $this->getRequest();
        $formPostArray = (array) $request->getPost();

        $fields = array_keys($this->configFields);
        foreach ($fields as $field) {
            if (!array_key_exists($field, $formPostArray)) {
                $this->setConfig($field, $this->_dataHelper->getConfigValue($field), $this->getStoreId());
            } else {
                $this->setConfig($field, $request->getPost($field));
            }
        }

        //if password mask (6 stars)
        if ($this->getConfig('password') === '******') {
            $password = $this->_dataHelper->getConfigPassword($this->getStoreId());
            $this->setConfig('password', $password);
        }

        return $this;
    }

    /**
     * @return void
     */
    protected function init()
    {
        $request = $this->getRequest();
        $this->setStoreId($request->getParam('store', null));

        $this->loadDefaultConfig();

        $this->toAddress = $this->getConfig('email') ? $this->getConfig('email') : $this->getConfig('username');

        $this->fromAddress = trim((string) $this->getConfig('from_email'));

        if (!$this->emailAddressValidator->isValid($this->fromAddress)) {
            $this->fromAddress = $this->toAddress;
        }

        $this->hash = time() . '.' . rand(300000, 900000);
    }

    /**
     * @return array
     */
    public function verify()
    {
        $settings = [
            'server_email' => 'validateServerEmailSetting',
            'magento_email_setting' => 'validateMagentoEmailStatus',
            'module_email_setting' => 'validateModuleEmailStatus',
            'magento_email' => 'validateMagentoEmailSetting'
        ];

        $result = $this->error();
        $hasError = false;

        foreach ($settings as $functionName) {
            $result = $this->$functionName();

            if (array_key_exists('has_error', $result)) {
                if ($result['has_error'] === true) {
                    $hasError = true;
                    break;
                }
            } else {
                $hasError = true;
                $result = $this->error(true, 'MP103 - Unknown Error');
                break;
            }
        }

        if (!$hasError) {
            $result['msg'] = __('Please check your email') . ' ' . $this->toAddress . ' ' .
                __('and flush your Magento cache');
        }

        return [$result];
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function validateServerEmailSetting()
    {
        $request = $this->getRequest();

        $username = $this->getConfig('username');
        $password = $this->getConfig('password');

        $auth = strtolower($this->getConfig('auth'));

        //if default view
        //see https://github.com/magento/magento2/issues/3019
        if (!$request->getParam('store', false)) {
            if ($auth != 'none' && (empty($username) || empty($password))) {
                return $this->error(
                    true,
                    __('Please enter a valid username/password')
                );
            }
        }

        $name = 'Test from MagePal SMTP';
        $from = trim((string) $this->getConfig('from_email'));
        $from = filter_var($from, FILTER_VALIDATE_EMAIL) ? $from : $username;
        $this->fromAddress = filter_var($username, FILTER_VALIDATE_EMAIL) ? $username : $from;
        $htmlBody = $this->_email->setTemplateVars(['hash' => $this->hash])->getEmailBody();

        $transport = $this->getMailTransportSmtp();

        $bodyMessage    = new MinePart($htmlBody);
        $bodyMessage->type = 'text/html';

        $body = new MineMessage();
        $body->addPart($bodyMessage);

        $message = new Message();
        $message->addTo($this->toAddress, 'MagePal SMTP')
            ->addFrom($this->fromAddress, $name)
            ->setSubject('Hello from MagePal SMTP (1 of 2)')
            ->setBody($body)
            ->setEncoding('UTF-8');

        $result = $this->error();

        try {
            $transport->send($message);
        } catch (Exception $e) {
            $result =  $this->error(true, __($e->getMessage()));
        }

        return $result;
    }

    public function getMailTransportSmtp()
    {
        $username = $this->getConfig('username');
        $password = $this->getConfig('password');
        $auth = strtolower($this->getConfig('auth'));

        $optionsArray = [
            'name' => $this->getConfig('name'),
            'host' => $this->getConfig('smtphost'),
            'port' => $this->getConfig('smtpport')
        ];

        if ($auth != 'none') {
            $optionsArray['connection_class'] = $auth;
            $optionsArray['connection_config'] = [
                'username' => $username,
                'password' => $password,
            ];
        }

        $ssl = $this->getConfig('ssl');
        if ($ssl != 'none') {
            $optionsArray = array_merge_recursive(
                ['connection_config' => ['ssl' => $ssl]],
                $optionsArray
            );
        }

        $options   = new SmtpOptions($optionsArray);
        $transport = new Smtp();
        $transport->setOptions($options);

        return $transport;
    }

    /**
     * @return array
     */
    protected function validateMagentoEmailSetting()
    {
        $result = $this->error();

        $this->_dataHelper->setTestMode(true);
        $this->_dataHelper->setStoreId($this->getStoreId());
        $this->_dataHelper->setTestConfig($this->getConfig());

        try {
            $this->_email
                ->setTemplateVars(['hash' => $this->hash])
                ->send(
                    ['email' => $this->fromAddress, 'name' => 'Test from MagePal SMTP'],
                    ['email' => $this->toAddress, 'name' => "MagePal SMTP"]
                );
        } catch (Exception $e) {
            $result = $this->error(true, __($e->getMessage()));
        }

        $this->_dataHelper->setTestMode(false);

        return $result;
    }

    /**
     * @return array
     */
    public function validateMagentoEmailStatus()
    {
        $result = $this->error();
        // system_smtp_disable

        if ($this->_dataHelper->getScopeConfigValue('system/smtp/disable')) {
            $result = $this->error(
                true,
                __('"Disable Email Communications" is set is "Yes", please set to "NO" in "Mail Sending Setting"')
            );
        }

        return $result;
    }

    /**
     * @return array
     */
    public function validateModuleEmailStatus()
    {
        $result = $this->error();

        if (!$this->getConfig('active')) {
            $result = $this->error(
                true,
                __('SMTP module is not enabled')
            );
        }

        return $result;
    }

    /**
     * Format error msg
     * @param string $s
     * @return string
     */
    public function formatErrorMsg($s)
    {
        return preg_replace(
            '@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@',
            '<a href="https://www.magepal.com/help/docs/smtp-magento/" target="_blank">$1</a>',
            nl2br($s)
        );
    }

    /**
     * @param bool $hasError
     * @param string $msg
     * @return array
     */
    public function error($hasError = false, $msg = '')
    {
        return [
            'has_error' => (bool) $hasError,
            'msg' => (string) $msg
        ];
    }
}
