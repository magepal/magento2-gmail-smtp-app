<?php
/**
 * Copyright © MagePal LLC. All rights reserved.
 * See COPYING.txt for license details.
 * http://www.magepal.com | support@magepal.com
 */
namespace MagePal\GmailSmtpApp\Plugin\Mail\Template;

class TransportBuilderByStorePlugin
{
    /**
     * @var \MagePal\GmailSmtpApp\Model\Store
     */
    protected $storeModel;

    /**
     * Sender resolver.
     *
     * @var \Magento\Framework\Mail\Template\SenderResolverInterface
     */
    private $senderResolver;

    /**
     * @param \MagePal\GmailSmtpApp\Model\Store $storeModel
     * @param \Magento\Framework\Mail\Template\SenderResolverInterface $senderResolver
     */
    public function __construct(
        \MagePal\GmailSmtpApp\Model\Store $storeModel,
        \Magento\Framework\Mail\Template\SenderResolverInterface $senderResolver
    ) {
        $this->storeModel = $storeModel;
        $this->senderResolver = $senderResolver;
    }

    public function beforeSetFromByStore(
        \Magento\Framework\Mail\Template\TransportBuilderByStore $subject,
        $from,
        $store
    ) {
        if (!$this->storeModel->getStoreId()) {
            $this->storeModel->setStoreId($store);
        }

        $email = $this->senderResolver->resolve($from, $store);
        $this->storeModel->setFrom($email);

        return [$from, $store];
    }
}
