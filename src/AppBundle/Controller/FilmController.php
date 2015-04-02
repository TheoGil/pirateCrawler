<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FilmController extends Controller
{
    /**
     * @Route("/film/{id}", name="filmDetails")
     */
    public function filmDetailsAction($id)
    {
        $filmRepo = $this->getDoctrine()->getRepository("AppBundle:Film");
        $film = $filmRepo->findOneById($id);
        
        // CHECKER SI ON A BIEN RECUPERE UN FILM, ELSE THROW 404
        
        $params = array(
            "film" => $film
        );
        
        return $this->render('default/filmdetails.html.twig', $params);
    }
}
