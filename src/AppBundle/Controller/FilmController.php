<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
        
        return $this->render('includes/filmdetails.html.twig', $params);
    }
    
    /**
     * @Route("/loadMoarFilms/{offset}", name="loadMoarFilms")
     */
    public function loadMoarFilms($offset)
    {
        $numPerPage = 4;
                
        $filmRepo = $this->getDoctrine()->getRepository("AppBundle:Film");
        $lastFilms = $filmRepo->paginate($offset);
        
        $params = array(
            "lastFilms" => $lastFilms
        );
        
        return $this->render('includes/moarfilms.html.twig', $params);
    }
}
