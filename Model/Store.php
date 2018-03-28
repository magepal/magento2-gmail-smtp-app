<?php
/**
 * Copyright © MagePal LLC. All rights reserved.
 * See COPYING.txt for license details.
 * http://www.magepal.com | support@magepal.com
 */

namespace MagePal\GmailSmtpApp\Model;

class Store
{
    /** @var int/null  */
    protected $store_id = null;

    /**
     * @return null
     */
    public function getStoreId()
    {
        return $this->store_id;
    }

    /**
     * @param $store_id
     * @return $this
     */
    public function setStoreId($store_id)
    {
        $this->store_id = $store_id;
        return $this;
    }
}
