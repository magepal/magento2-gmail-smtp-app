<?php
/**
 * Copyright © MagePal LLC. All rights reserved.
 * See COPYING.txt for license details.
 * http://www.magepal.com | support@magepal.com
 */

namespace MagePal\GmailSmtpApp\Plugin\Mail\Template;

class TransportBuilderPlugin
{

    /** @var \MagePal\GmailSmtpApp\Model\Store */
    protected $storeModel;

    /**
     * @param \MagePal\GmailSmtpApp\Model\Store $storeModel
     */
    public function __construct(\MagePal\GmailSmtpApp\Model\Store $storeModel)
    {
        $this->storeModel = $storeModel;
    }

    /**
     * @param \Magento\Framework\Mail\Template\TransportBuilder $subject
     * @param $templateOptions
     * @return array
     */
    public function beforeSetTemplateOptions(
        \Magento\Framework\Mail\Template\TransportBuilder $subject,
        $templateOptions
    ) {
        if (array_key_exists('store', $templateOptions)) {
            $this->storeModel->setStoreId($templateOptions['store']);
        } else {
            $this->storeModel->setStoreId(null);
        }

        return [$templateOptions];
    }
}
