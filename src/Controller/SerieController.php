<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Form\SerieType;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\OptimisticLockException;
//use http\Client\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/series')]
class SerieController extends AbstractController
{
    #[Route('/', name: 'app_list')]
    public function List(SerieRepository $serieRepository): Response
    {
        //aller chercher les series en bdd
        //Repository内のメソッドを使うのでList( )の中に定義を追加する→→自動的にuseにもインポートされる

        $series = $serieRepository->findBestSeriess();
        //$series =$serieRepository->find([],['popularity'=>'DESC', 'vote'=>'DESC'],30);
        //  $series =$serieRepository->findAll();
        //dd($series); dd出の確認OKなのでtwigとの表示作業に移る　ma requete bien fonctionne
        //twig内に渡すにはコントローラー内の変数$serieはキー名を与えねばいけない　"series"=>$seriesのように.

        return $this->render('serie/list.html.twig', [
            "series" => $series
        ]);
    }

    #[Route('/details/{id}', name: 'app_details')]
    public function detail(int $id, SerieRepository $serieRepository): Response
    {
        //aller chercher les series en bdd
        $serie = $serieRepository->find($id);

        if (!$serie){
            throw $this->createNotFoundException('ohhhh nooo');
        }

        return $this->render('serie/details.html.twig', [
            "serie" => $serie
        ]);
    }

    #[Route('/create', name: 'app_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        //creer une instance d'une serie(creer une notre entite)
        $serie = new Serie();
        $serie->setDateCreated(new \DateTime);
        //creer une instance d'une SerieType mon formulaire et pour generer on n besoin "createForm" ce n'est pas New!!!
        //notre formulaire, associée à l'entité vide
        $serieForm = $this->createForm(SerieType::class, $serie);
        //recupere les données du form et les injecte dans notre $serie
        $serieForm->handleRequest($request);
        //si le formulaire est soumis et validé...
        if ($serieForm->isSubmitted() && $serieForm->isValid()) {
            //sauvegarde en BDD
            $entityManager->persist($serie);
            $entityManager->flush();

            //pdo->lastInsertId()=>

            $this->addFlash('success', 'Good Job!!!');
            return $this->redirectToRoute('app_details', ['id' => $serie->getId()]);
        }
        return $this->render('serie/create.html.twig', [
            'serieForm' => $serieForm
        ]);
    }

    #[Route('/demo', name: 'app_demo')]
    public function demo(EntityManagerInterface $entityManager): Response
    {
        //create une instance de mon entity
        $serie = new Serie();
        //hydrate toutes les propriétés
        $serie->setName('pif');
        $serie->setBackdrop('daf sdf');
        $serie->setPoster('daf sdf');
        $serie->setLastAirDate(new \DateTime("-6 month"));
        $serie->setDateCreated(new \DateTime());
        $serie->setFirstAirDate(new \DateTime("-1 year"));
        $serie->setGenres('drama');
        $serie->setOverview('bla bla bla');
        $serie->setPopularity(123.00);
        $serie->setVote(8.2);
        $serie->setStatus('returning');
        $serie->setTmdbId(77185);

        dump($serie);

        $entityManager->persist($serie);
        $entityManager->flush();

        dump($serie);

        //supprimer
        //$entityManager->remove($serie);

        //modifier
        $serie->setGenres('comedy');
        $entityManager->flush();

        return $this->render('serie/create.html.twig', [
        ]);
    }


    #[Route('/delete/{id}', name: 'app_delete')]
    public function delete(Serie $serie, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($serie);
        $entityManager->flush();

        return $this->redirectToRoute("main_home");

    }
}