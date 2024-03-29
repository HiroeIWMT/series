<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    /*indexページにするためにはRouteの中を"/"のみすにするだけ*/
    #[Route("/home", name: "main_home")]
    public function home()
    {
        return $this->render('main/home.html.twig');
    }


    #[Route("/test", name: "main_test")]
    public function test()
    {
        $serie = [
            "title" => "serie tableauの０番",
            "year" => "2024 tableau１番",
        ];
        return $this->render('main-test.html.twig', [
            "mySerieTableau" => $serie,
            "autreVar" => 100228
        ]);

    }
}
