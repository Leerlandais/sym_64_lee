<?php

namespace App\Controller;


use App\Entity\Section;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'public_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {

        $authors = $entityManager->getRepository(User::class)->findAll();
        $categs = $entityManager->getRepository(Section::class)->findAll();
        return $this->render('main/index.html.twig', [
            'authors' => $authors,
            'cats' => $categs,
          //  'arts' => $articleRepository->findAll(),
        ]);
    }
}
