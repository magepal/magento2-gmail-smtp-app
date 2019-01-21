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
     * @var \MagePal\GmailSmtpApp\Model\TwoDotTwo\SmtpFactory
     */
    private $smtp22;

    /**
     * @var \MagePal\GmailSmtpApp\Model\TwoDotThree\SmtpFactory
     */
    private $smtp23;

    /**
     * @param \MagePal\GmailSmtpApp\Helper\Data                   $dataHelper
     * @param \MagePal\GmailSmtpApp\Model\Store                   $storeModel
     * @param \MagePal\GmailSmtpApp\Model\TwoDotTwo\SmtpFactory   $smtp22
     * @param \MagePal\GmailSmtpApp\Model\TwoDotThree\SmtpFactory $smtp23
     */
    public function __construct(
        \MagePal\GmailSmtpApp\Helper\Data $dataHelper,
        \MagePal\GmailSmtpApp\Model\Store $storeModel,
        \MagePal\GmailSmtpApp\Model\TwoDotTwo\SmtpFactory $smtp22,
        \MagePal\GmailSmtpApp\Model\TwoDotThree\SmtpFactory $smtp23
    ) {
        $this->dataHelper = $dataHelper;
        $this->storeModel = $storeModel;
        $this->smtp22 = $smtp22;
        $this->smtp23 = $smtp23;
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

            // For Magento 2.0, 2.1, 2.2 else 2.3
            if ($message instanceof \Zend_mail) {
                $smtp = $this->smtp22->create(['dataHelper' => $this->dataHelper, 'storeModel' => $this->storeModel]);
                $smtp->sendSmtpMessage($message);
            } elseif ($message instanceof \Magento\Framework\Mail\Message) {
                $smtp = $this->smtp23->create(['dataHelper' => $this->dataHelper, 'storeModel' => $this->storeModel]);
                $smtp->sendSmtpMessage($message);
            } else {
                $proceed();
            }
        } else {
            $proceed();
        }
    }
}
