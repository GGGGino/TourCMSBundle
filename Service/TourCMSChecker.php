<?php
/**
 * Created by PhpStorm.
 * User: gino
 * Date: 05/07/18
 * Time: 16.26
 */

namespace GGGGino\TourCMSBundle\Service;


class TourCMSChecker
{
    const RENDER_BOOL = 0;
    const RENDER_HTML = 1;
    const RENDER_STRUCTURE = 2;
    /**
     *  Minimum php version
     */
    const PHP_VERSION_MIN = '5.3.0';

    /**
     * @var TourCMS
     */
    private $tourCMS;

    /**
     * @var string
     */
    private $channelId;

    /**
     * If this is true, then every test return a string, otherwise the test return a bool
     *
     * @var integer
     */
    private $renderType;

    /**
     * TourCMSChecker constructor.
     * @param TourCMS $tourCMS
     * @param string $channelId
     * @param integer $renderType
     */
    public function __construct(TourCMS $tourCMS, string $channelId, $renderType = self::RENDER_HTML)
    {
        $this->tourCMS = $tourCMS;
        $this->channelId = $channelId;
        $this->renderType = $renderType;
    }

    /**
     * Run all the checks in an array
     *
     * @return array
     */
    public function checkAll()
    {
        return array(
            'checkPhpVersion' => $this->checkPhpVersion(),
            'checkSimpleXML' => $this->checkSimpleXML(),
            'checkUrl' => $this->checkCurl(),
            'checkDownload' => $this->checkDownload(),
            'checkDateTime' => $this->checkDateTime(),
            'checkTours' => $this->checkTours(),
            'chckApiSettings' => $this->checkApiSettings(),
            'checkKey' => $this->checkKey()
        );
    }

    /**
     * Check the php version. It must be at least self::PHP_VERSION_MIN
     *
     * @return string|bool|array
     */
    public function checkPhpVersion()
    {
        $has_phpversion = strnatcmp(phpversion(), self::PHP_VERSION_MIN) >= 0;
        return $this->renderStatus($has_phpversion, "You have a recent enough version of PHP", "PHP " . self::PHP_VERSION_MIN . " or newer is required");
    }

    /**
     * Check that controls the simplexml flow
     *
     * @return string|bool|array
     */
    public function checkSimpleXML()
    {
        $has_simplexml = function_exists("simplexml_load_file");
        return $this->renderStatus($has_simplexml, "SimpleXML is available", "SimpleXML is not loaded <a href='http://www.php.net/manual/en/simplexml.installation.php'>?</a>");
    }

    /**
     * Control that the curl functions are available
     *
     * @return string|bool|array
     */
    public function checkCurl()
    {
        $has_curl = function_exists("curl_init");
        return $this->renderStatus($has_curl, "CURL is available", "CURL is not loaded <a href='http://uk3.php.net/manual/en/curl.installation.php'>?</a>");

    }

    /**
     * @return string|bool|array
     */
    public function checkDownload()
    {
        $has_curl = function_exists("curl_init");

        if($has_curl) {
            $ch = curl_init("https://live.tourcms.com/favicon.ico");
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30 );
            $c = curl_exec($ch);
            $curl_info = curl_getinfo($ch);

            if(isset($curl_info['http_code'])) {
                $curl_ok = (int)$curl_info['http_code']==200;
                return $this->renderStatus($curl_ok, "Downloaded a test file from TourCMS ok", "Unable to download a test file from TourCMS, status: <strong>".$curl_info['http_code']."</strong>");
            } else {
                return $this->renderStatus(false, "", "Unable to contact TourCMS server, no status code returned");
            }
        }

        return $this->renderStatus(false, "", "");
    }

    /**
     * @return array|bool|string
     */
    public function checkDateTime()
    {
        $api_check = $this->tourCMS->api_rate_limit_status($this->channelId);

        $api_ok = (string) $api_check->error == "OK";

        if(!$api_ok && strpos((string)$api_check->error, "_TIME")!==false) {
            return $this->renderStatus(false, "", "It looks like the Date/Time of your server is incorrect. According to your server the time in GMT is: <strong>" . gmdate('H:i  l (\G\M\T)') . "</strong>. You can compare that to the actual time in GMT by using this <a href=\"https://www.google.co.uk/search?q=current+time+gmt\">Google search</a><br />(it doesn't matter if it's a few minutes out).");
        }

        return $this->renderStatus(false, "", "");
    }

    /**
     * @return array|bool|string
     */
    public function checkTours()
    {
        $api_check = $this->tourCMS->api_rate_limit_status($this->channelId);

        $api_ok = (string) $api_check->error == "OK";

        if($api_ok) {
            $tour_search = $this->tourCMS->search_tours("", $this->channelId);

            $has_tours = (int) $tour_search->total_tour_count > 0;

            return $this->renderStatus($has_tours, "Found <strong>" . $tour_search->total_tour_count . "</strong> tours", "No tours found");
        }

        return $this->renderStatus(false, "", "");
    }

    /**
     * @return string|bool|array
     */
    public function checkApiSettings()
    {
        $api_check = $this->tourCMS->api_rate_limit_status($this->channelId);

        $api_ok = (string) $api_check->error == "OK";

        return $this->renderStatus($api_ok, "Your API settings work", "Your API settings return the following error: <em>" . $api_check->error . "</em> <a href='http://www.tourcms.com/support/api/mp/error_messages.php'>?</a>");
    }

    /**
     * Method that checks the authenticity of the key in combination with the channel and marketId
     *
     * @return string|bool|array
     */
    public function checkKey()
    {
        if($this->tourCMS->getPrivateKey() == "") {
            return $this->renderStatus(false, "", "You have not provided an API Key");
        } else {
            if($this->channelId == 0 && $this->tourCMS->getMarketpId() == 0) {
                return $this->renderStatus(false, "", "If you are calling the API as an operator you must pass a Channel ID when calling <strong>test_environment();</strong><br>&nbsp;<br>If you are calling as an agent you must use their Marketplace ID when you initiate the <strong>TourCMS</strong> class (optonally also pass a Channel ID to <strong>test_environment</strong> your API connection to a specific operator).");
            } else {
                if($this->tourCMS->getMarketpId() != 0) {
                    return $this->renderStatus(true, "Attempting to call the API as Agent <strong>" . $this->tourCMS->getMarketpId() .  "</strong>", "");
                } else {
                    return $this->renderStatus(true, "Attempting to call the API as Operator with Channel ID <strong>" . $this->channelId . "</strong>", "");
                }
            }
        }
    }

    /**
     * Utility method to use the right class and label
     *
     * @param $status
     * @param $okText
     * @param $failText
     * @return string|bool|array
     */
    private function renderStatus($status, $okText, $failText)
    {
        if( $this->renderType == self::RENDER_BOOL )
            return (bool) $status;

        $liClass = $status ? 'ok' : 'fail';
        $textToPrint = $status ? $okText : $failText;

        if( $this->renderType == self::RENDER_STRUCTURE ) {
            return array($status, $textToPrint);
        }

        if( empty($textToPrint) )
            return "";

        return "<li class='" . $liClass . "'>" . $textToPrint . "</li>";
    }

    /**
     * @param boolean $renderType
     * @return TourCMSChecker
     */
    public function setRenderType($renderType)
    {
        $this->renderType = $renderType;
        return $this;
    }

    /**
     * @return TourCMS
     */
    public function getTourCMS()
    {
        return $this->tourCMS;
    }
}