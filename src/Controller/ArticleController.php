<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/article')]
final class ArticleController extends AbstractController
{

    private function getMenu(EntityManagerInterface $em)
    {
        return $em->getRepository(Article::class)->findAll();
    }
    #[Route(name: 'app_article_index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository): Response
    {

        $user = $this->getUser();
        $userId = $user?->getId();
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
            'user' => $userId
        ]);
    }

    #[Route('/update/{author}',name: 'app_article_update', methods: ['GET'])]
    public function update(ArticleRepository $articleRepository,int  $author=null, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $userId = $user?->getId();
        $menuArts = $entityManager->getRepository(Article::class)->findAll();
        if (!$author){
            return $this->render('article/index.html.twig', [
                'articles' => $articleRepository->findAll(),
                'user' => $userId,
                'menuArts' => $menuArts,

            ]);
        }
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->getAllArticlesByAuthorId($author),
            'user' => $userId,
            'menuArts' => $menuArts,

        ]);
    }

    #[Route('/new', name: 'app_article_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = new Article();
        $user = $this->getUser();
        $userId = $user?->getId(); // Ooh, nice trick by PHPStorm - turned my if/else into this :)
        $menuArts = $entityManager->getRepository(Article::class)->findAll();

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setArticleDateCreated(new \DateTime());
            $article->setUser($user);
            $title = $article->getTitle();
            $slug = Slugify::create()->slugify($title);
            $article->setTitleSlug($slug);
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_article_index', [
                "user" => $userId,
                'menuArts' => $menuArts,
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form,
            'user' => $userId,
            'menuArts' => $menuArts,
        ]);
    }

    #[Route('/{id}', name: 'app_article_show', methods: ['GET'])]
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $userId = $user?->getId();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_article_index', [
                'user' => $userId,
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
            'user' => $userId,
        ]);
    }

    #[Route('/{id}', name: 'app_article_delete', methods: ['POST'])]
    public function delete(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
    }
}
