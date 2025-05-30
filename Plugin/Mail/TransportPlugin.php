<?php
/**
 * Copyright © MagePal LLC. All rights reserved.
 * See COPYING.txt for license details.
 * https://www.magepal.com | support@magepal.com
 */

namespace MagePal\GmailSmtpApp\Plugin\Mail;

use Closure;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\TransportInterface;
use MagePal\GmailSmtpApp\Helper\Data;
use MagePal\GmailSmtpApp\Mail\SmtpFactory;
use MagePal\GmailSmtpApp\Mail\Smtp;
use MagePal\GmailSmtpApp\Model\Store;
use Symfony\Component\Mime\Message as SymfonyMessage;

class TransportPlugin
{

    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var Store
     */
    protected $storeModel;

    /**
     * @var Smtp
     */
    private $smtpFactory;

    /**
     * @param Data $dataHelper
     * @param Store $storeModel
     * @param SmtpFactory $smtpFactory
     */
    public function __construct(
        Data $dataHelper,
        Store $storeModel,
        SmtpFactory $smtpFactory
    ) {
        $this->dataHelper = $dataHelper;
        $this->storeModel = $storeModel;
        $this->smtpFactory = $smtpFactory;
    }

    /**
     * @param TransportInterface $subject
     * @param Closure $proceed
     * @throws MailException
     */
    public function aroundSendMessage(
        TransportInterface $subject,
        Closure $proceed
    ) {
        if ($this->dataHelper->isActive()) {
            if (method_exists($subject, 'getStoreId')) {
                $this->storeModel->setStoreId($subject->getStoreId());
            }

            $message = $subject->getMessage()->getSymfonyMessage();

            if ($message instanceof SymfonyMessage) {
                /** @var Smtp $smtp */
                $smtp = $this->smtpFactory->create(
                    ['dataHelper' => $this->dataHelper, 'storeModel' => $this->storeModel]
                );
                $smtp->sendSmtpMessage($message);
            } else {
                $proceed();
            }
        } else {
            $proceed();
        }
    }
}
