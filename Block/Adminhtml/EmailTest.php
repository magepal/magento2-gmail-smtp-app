<?php
/**
 * Copyright © MagePal LLC. All rights reserved.
 * See COPYING.txt for license details.
 * http://www.magepal.com | support@magepal.com
 */

namespace MagePal\GmailSmtpApp\Block\Adminhtml;

class EmailTest extends \Magento\Backend\Block\Template
{
    /**
     * @var \MagePal\GmailSmtpApp\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \MagePal\GmailSmtpApp\Model\Email
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
     * EmailTest constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \MagePal\GmailSmtpApp\Helper\Data $dataHelper
     * @param \MagePal\GmailSmtpApp\Model\Email $email
     * @param array $data
     * @throws \Zend_Validate_Exception
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \MagePal\GmailSmtpApp\Helper\Data $dataHelper,
        \MagePal\GmailSmtpApp\Model\Email $email,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_dataHelper = $dataHelper;
        $this->_email = $email;

        $this->init();
    }

    /**
     * @throws \Zend_Validate_Exception
     */
    protected function init()
    {
        $request = $this->getRequest();

        $this->storeId = $request->getParam('store', null);

        //if password mask (6 stars)
        if ($request->getPost('password') === '******') {
            $password = $this->_dataHelper->getConfigPassword($this->storeId);
            $request->setPostValue('password', $password);
        }

        $this->toAddress = $request->getPost('email') ? $request->getPost('email') : $request->getPost('username');

        $this->fromAddress = trim($request->getPost('from_email'));

        if (!\Zend_Validate::is($this->fromAddress, 'EmailAddress')) {
            $this->fromAddress = $this->toAddress;
        }

        $this->hash = time() . '.' . rand(300000, 900000);
    }

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

        foreach ($settings as $key => $functionName) {
            $result = call_user_func([$this, $functionName]);

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
     * @throws \Zend_Mail_Exception
     * @throws \Zend_Validate_Exception
     */
    protected function validateServerEmailSetting()
    {
        $request = $this->getRequest();

        $username = $request->getPost('username');
        $password = $request->getPost('password');

        $auth = strtolower($request->getPost('auth'));

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

        //SMTP server configuration
        $smtpHost = $request->getPost('smtphost');

        $smtpConf = [
            'name' => $request->getPost('name'),
            'port' => $request->getPost('smtpport')
        ];

        if ($auth != 'none') {
            $smtpConf['auth'] = $auth;
            $smtpConf['username'] = $username;
            $smtpConf['password'] = $password;
        }

        $ssl = $request->getPost('ssl');
        if ($ssl != 'none') {
            $smtpConf['ssl'] = $ssl;
        }

        $transport = new \Zend_Mail_Transport_Smtp($smtpHost, $smtpConf);

        $from = trim($request->getPost('from_email'));
        $from = \Zend_Validate::is($from, 'EmailAddress') ? $from : $username;
        $this->fromAddress = $from;

        //Create email
        $name = 'Test from MagePal SMTP';
        $mail = new \Zend_Mail();
        $mail->setFrom($this->fromAddress, $name);
        $mail->addTo($this->toAddress, $this->toAddress);
        $mail->setSubject('Hello from MagePal SMTP (1 of 2)');

        $htmlBody = $this->_email->setTemplateVars(['hash' => $this->hash])->getEmailBody();

        $mail->setBodyHtml($htmlBody);

        $result = $this->error();

        try {
            //only way to prevent zend from giving a error
            if (!$mail->send($transport) instanceof \Zend_Mail) {
            }
        } catch (\Exception $e) {
            $result =  $this->error(true, __($e->getMessage()));
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function validateMagentoEmailSetting()
    {
        $result = $this->error();

        $this->_dataHelper->setTestMode(true);
        $this->_dataHelper->setStoreId($this->storeId);

        try {
            $this->_email
                ->setTemplateVars(['hash' => $this->hash])
                ->send(
                    ['email' => $this->fromAddress, 'name' => $this->fromAddress],
                    ['email' => $this->toAddress, 'name' => $this->toAddress]
                );
        } catch (\Exception $e) {
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

        if (!$this->getRequest()->getPost('active')) {
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
            '<a href="$1" target="_blank">$1</a>',
            nl2br($s)
        );
    }

    /**
     * @param bool $hasError
     * @param string $msg
     * @return array
     */
    public function error(bool $hasError = false, string $msg = '')
    {
        return [
            'has_error' => $hasError,
            'msg' => $msg
        ];
    }
}
