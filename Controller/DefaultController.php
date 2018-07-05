<?php

namespace GGGGino\TourCMSBundle\Controller;

use GGGGino\TourCMSBundle\Service\TourCMS;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * Test page reset from the test.php page of the TourCMS library
     *
     * @Route("/test", name="ggggino_tour_cms")
     * @return Response
     */
    public function testAction()
    {
        /** @var TourCMS $tourCms */
        $tourCms = $this->get(TourCMS::class);
        $channel = $this->getParameter('ggggino_tourcms.channel_id');
        $curl_ok = false;
        $statusDownload = "";
        $has_phpversion = strnatcmp(phpversion(),'5.3.0') >= 0;
        $has_simplexml = function_exists("simplexml_load_file");
        $has_curl = function_exists("curl_init");

        $statusPhpVersion = $this->printStatus($has_phpversion, "You have a recent enough version of PHP", "PHP 5.1.2 or newer is required");
        $statusSimpleXml = $this->printStatus($has_simplexml, "SimpleXML is available", "SimpleXML is not loaded <a href='http://www.php.net/manual/en/simplexml.installation.php'>?</a>");
        $statusCurl = $this->printStatus($has_curl, "CURL is available", "CURL is not loaded <a href='http://uk3.php.net/manual/en/curl.installation.php'>?</a>");

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
                $statusDownload = $this->printStatus($curl_ok, "Downloaded a test file from TourCMS ok", "Unable to download a test file from TourCMS, status: <strong>".$curl_info['http_code']."</strong>");
            } else {
                $statusDownload = $this->printStatus(false, "", "Unable to contact TourCMS server, no status code returned");
            }
        }

        $api_check = $tourCms->api_rate_limit_status($channel);

        $api_ok = (string) $api_check->error == "OK";

        $statusDateTime = "";
        if(!$api_ok && strpos((string)$api_check->error, "_TIME")!==false) {
            $statusDateTime = "<li class=\"fail\">It looks like the Date/Time of your server is incorrect. According to your server the time in GMT is: <strong><?php print gmdate('H:i  l (\G\M\T)'); ?></strong>. You can compare that to the actual time in GMT by using this <a href=\"https://www.google.co.uk/search?q=current+time+gmt\">Google search</a><br />(it doesn't matter if it's a few minutes out).</li>";
        }

        $statusTours = "";
        if($api_ok) {
            $tour_search = $tourCms->search_tours("", $channel);

            $has_tours = (int) $tour_search->total_tour_count > 0;

            $statusTours = $this->printStatus($has_tours, "Found <strong>" . $tour_search->total_tour_count . "</strong> tours", "No tours found");
        }

        $statusApiSettings = $this->printStatus($api_ok, "Your API settings work", "Your API settings return the following error: <em>" . $api_check->error . "</em> <a href='http://www.tourcms.com/support/api/mp/error_messages.php'>?</a>");

        return $this->render('@GGGGinoTourCMS/test.html.twig', array(
            'statusPhpVersion' => $statusPhpVersion,
            'statusSimpleXml' => $statusSimpleXml,
            'statusCurl' => $statusCurl,
            'statusDownload' => $statusDownload,
            'keyCheck' => $this->keyCheck(),
            'statusDateTime' => $statusDateTime,
            'statusApiSettings' => $statusApiSettings,
            'statusTours' => $statusTours
        ));
    }

    /**
     * Utility method to use the right class and label
     *
     * @param $status
     * @param $okText
     * @param $failText
     * @return string
     */
    private function printStatus($status, $okText, $failText)
    {
        $liClass = $status ? 'ok' : 'fail';
        $textToPrint = $status ? $okText : $failText;

        return "<li class='" . $liClass . "'>" . $textToPrint . "</li>";
    }

    /**
     * Method that checks the authenticity of the key in combination with the channel and marketId
     *
     * @return string
     */
    private function keyCheck()
    {
        /** @var TourCMS $tourCms */
        $tourCms = $this->get(TourCMS::class);
        $channel = $this->getParameter('ggggino_tourcms.channel_id');

        if($tourCms->getPrivateKey() == "") {
            return "<li class=\"fail\">You have not provided an API Key</li>";
        } else {
            if($channel == 0 && $tourCms->getMarketpId() == 0) {
                return "<li class=\"fail\">If you are calling the API as an operator you must pass a Channel ID when calling <strong>test_environment();</strong><br>&nbsp;<br>If you are calling as an agent you must use their Marketplace ID when you initiate the <strong>TourCMS</strong> class (optonally also pass a Channel ID to <strong>test_environment</strong> your API connection to a specific operator).</li>";
            } else {
                if($tourCms->getMarketpId() != 0) {
                    return "<li class=\"ok\">Attempting to call the API as Agent <strong>" . $tourCms->getMarketpId() .  "</strong></li>";
                } else {
                    return "<li class=\"ok\">Attempting to call the API as Operator with Channel ID <strong>" . $channel . "</strong></li>";
                }
            }
        }
    }
}
