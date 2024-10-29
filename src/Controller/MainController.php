<?php

namespace App\Controller;


use App\Entity\Article;
use App\Entity\Section;
use App\Entity\Tag;
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
        $arts = $entityManager->getRepository(Article::class)->findAll();
        $tags = $entityManager->getRepository(Tag::class)->findAll();
        return $this->render('main/index.html.twig', [
            'authors' => $authors,
            'cats' => $categs,
            'arts' => $arts,
            'tags' => $tags,
        ]);
    }

    #[Route('/author/{id}', name: 'public_author', methods: ['GET'])]
    public function author(EntityManagerInterface $entityManager, int $id): Response
    {
        $arts = $entityManager->getRepository(Article::class)->getArticlesByAuthorId($id);
        $author = $entityManager->getRepository(User::class)->find($id);
        return $this->render('main/author.html.twig', [
            'author' => $author,
            'arts' => $arts,
        ]);
    }

    #[Route('/section/{slug}', name: 'public_section', methods: ['GET'])]
    public function section(EntityManagerInterface $entityManager, string $slug): Response
    {
        $arts = $entityManager->getRepository(Article::class)->getArticlesBySectionSlug($slug);
        foreach ($arts as $art) {
            foreach ($art->getSections() as $sect) {
                $sect->getSectionTitle();
            }
        }

        return $this->render('main/section.html.twig', [
            'section' => $sect,
            'arts' => $arts,
        ]);
    }

    #[Route('/tag/{slug}', name: 'public_tag', methods: ['GET'])]
    public function tag(EntityManagerInterface $entityManager, string $slug): Response
    {
        $arts = $entityManager->getRepository(Article::class)->getArticlesByTagSlug($slug);

        foreach ($arts as $art) {
            foreach ($art->getTags() as $tag) {
                $tag->getTagName();
            }
        }

        return $this->render('main/tag.html.twig', [
            'arts' => $arts,
            'tag' => $tag,
        ]);
    }

    #[Route('/art/{slug}', name: 'public_article', methods: ['GET'])]
    public function article(EntityManagerInterface $entityManager, string $slug): Response
    {
        $article = $entityManager->getRepository(Article::class)->getArticlesByTitleSlug($slug);
        $arts = $entityManager->getRepository(Article::class)->findAll();

        return $this->render('main/article.html.twig', [
            'article' => $article,
            'arts' => $arts,
        ]);
    }

}
