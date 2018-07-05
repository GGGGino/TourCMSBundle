<?php

namespace GGGGino\TourCMSBundle\Service;

use TourCMS\Utils\TourCMS as BaseTourCMS;

class TourCMS extends BaseTourCMS
{
    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->base_url;
    }

    /**
     * @return int
     */
    public function getMarketpId()
    {
        return $this->marketp_id;
    }

    /**
     * @return string
     */
    public function getPrivateKey()
    {
        return $this->private_key;
    }

    /**
     * @return string
     */
    public function getResultType()
    {
        return $this->result_type;
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }
}