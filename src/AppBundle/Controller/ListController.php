<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Service\NetflixApi;

class ListController extends Controller
{
    /**
     * @Route("/", name="home")
     *
     * @param NetflixApi $netflixApi
     * @return
     */
    public function indexAction(NetflixApi $netflixApi)
    {
        // Fetch data about the show and episodes
        $params['showData'] = $netflixApi->getShowData();
        $params['episodesData'] = $netflixApi->getEpisodesData();

        // Random number to prevent gif from being put in cache
        $params['number'] = rand(1, 1000);

        return $this->render('list/index.html.twig', $params);
    }
}
