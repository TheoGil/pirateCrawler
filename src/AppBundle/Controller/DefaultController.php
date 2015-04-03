<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepageAction()
    {
        $filmRepo = $this->getDoctrine()->getRepository("AppBundle:Film");
        $lastFilms = $filmRepo->paginate(0);
        
        $params = array(
            "lastFilms" => $lastFilms
        );
        
        return $this->render('default/index.html.twig', $params);
    }
}
