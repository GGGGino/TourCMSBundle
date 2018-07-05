<?php

namespace GGGGino\TourCMSBundle\Controller;

use GGGGino\TourCMSBundle\Service\TourCMS;
use GGGGino\TourCMSBundle\Service\TourCMSChecker;
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
        /** @var TourCMSChecker $tourCmsChecker */
        $tourCmsChecker = $this->get(TourCMSChecker::class);

        return $this->render('@GGGGinoTourCMS/test.html.twig', array(
            'statusPhpVersion' => $tourCmsChecker->checkPhpVersion(),
            'statusSimpleXml' => $tourCmsChecker->checkSimpleXML(),
            'statusCurl' => $tourCmsChecker->checkCurl(),
            'statusDownload' => $tourCmsChecker->checkDownload(),
            'keyCheck' => $tourCmsChecker->checkKey(),
            'statusDateTime' => $tourCmsChecker->checkDateTime(),
            'statusApiSettings' => $tourCmsChecker->checkApiSettings(),
            'statusTours' => $tourCmsChecker->checkTours()
        ));
    }
}
