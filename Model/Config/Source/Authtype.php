<?php
/**
 * Copyright © 2017 MagePal LLC. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MagePal\GmailSmtpApp\Model\Config\Source;

class Authtype implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'none', 'label' => __('None')],
            ['value' => 'ssl', 'label' => 'SSL (Gmail / Google Apps)'],
            ['value' => 'tls', 'label' => 'TLS (Gmail / Google Apps)']
        ];
    }
}
