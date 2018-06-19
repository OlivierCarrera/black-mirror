<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Service\NetflixApi;

class EpisodeController extends Controller
{
    /**
     * @Route("/episode/{season}/{number}", name="episode", requirements={"season"="\d+", "number"="\d+"})
     *
     * @param NetflixApi $netflixApi
     */
    public function indexAction(NetflixApi $netflixApi, $season, $number)
    {
        // Get episode data
        $params['episode'] = $netflixApi->getEpisode($season, $number);

        // Random number to prevent gif from being put in cache
        $params['number'] = rand(1, 1000);

        return $this->render('episode/index.html.twig', $params);
    }
}
