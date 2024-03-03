<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ["GET"])]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

	#[Route('/design-guide', name:'app_design_guide', methods: ["GET"])]
	public function designGuide(): Response
	{
		return $this->render('home/design-guide.html.twig', [
			'controller_name' => 'HomeController',
		]);
	}
}
