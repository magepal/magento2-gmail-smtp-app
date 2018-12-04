<?php
/**
 * Copyright © MagePal LLC. All rights reserved.
 * See COPYING.txt for license details.
 * http://www.magepal.com | support@magepal.com
 */

namespace MagePal\GmailSmtpApp\Plugin\Mail;

class TransportPlugin extends \Zend_Mail_Transport_Smtp
{
    /**
     * @var \MagePal\GmailSmtpApp\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \MagePal\GmailSmtpApp\Model\Store
     */
    protected $storeModel;

    /**
     * @param \MagePal\GmailSmtpApp\Helper\Data $dataHelper
     * @param \MagePal\GmailSmtpApp\Model\Store $storeModel
     */
    public function __construct(
        \MagePal\GmailSmtpApp\Helper\Data $dataHelper,
        \MagePal\GmailSmtpApp\Model\Store $storeModel
    ) {
        $this->dataHelper = $dataHelper;
        $this->storeModel = $storeModel;
    }

    /**
     * @param \Magento\Framework\Mail\TransportInterface $subject
     * @param \Closure $proceed
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Zend_Mail_Exception
     */
    public function aroundSendMessage(
        \Magento\Framework\Mail\TransportInterface $subject,
        \Closure $proceed
    ) {
        if ($this->dataHelper->isActive()) {
            if (method_exists($subject, 'getStoreId')) {
                $this->storeModel->setStoreId($subject->getStoreId());
            }

            $message = $subject->getMessage();

            //For Magento 2.0, 2.1, 2.2 else 2.3
            if ($message instanceof \Zend_mail) {
                $smtp = new \MagePal\GmailSmtpApp\Model\TwoDotTwo\Smtp($this->dataHelper, $this->storeModel);
                $smtp->sendSmtpMessage($message);
            } elseif ($message instanceof \Magento\Framework\Mail\Message) {
                $smtp = new \MagePal\GmailSmtpApp\Model\TwoDotThree\Smtp($this->dataHelper, $this->storeModel);
                $smtp->sendSmtpMessage($message);
            } else {
                $proceed();
            }
        } else {
            $proceed();
        }
    }
}
