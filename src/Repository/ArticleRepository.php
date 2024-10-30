<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function getArticlesByAuthorId(int $authorId): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.user', 'u')
            ->addSelect('u')
            ->where('p.published = :published')
            ->setParameter('published', true)
            ->andWhere('u.id = :id')
            ->setParameter('id', $authorId)


            ->getQuery()
            ->getResult();
    }

    public function getAllArticlesByAuthorId(int $authorId): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.user', 'u')
            ->addSelect('u')
            ->andWhere('u.id = :id')
            ->setParameter('id', $authorId)


            ->getQuery()
            ->getResult();
    }

    public function getArticlesBySectionSlug(string $slug): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.sections', 's')
            ->addSelect('s')
            ->where('p.published = :published')
            ->setParameter('published', true)
            ->andWhere('s.section_slug = :slug')
            ->setParameter('slug', $slug)


            ->getQuery()
            ->getResult();
    }

    public function getArticlesByTagSlug(string $tagSlug): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.tags', 't')
            ->addSelect('t')
            ->where('p.published = :published')
            ->setParameter('published', true)
            ->andWhere('t.tag_slug = :slug')
            ->setParameter('slug', $tagSlug)


            ->getQuery()
            ->getResult();
    }

    public function getArticlesByTitleSlug(string $artSlug): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.published = :published')
            ->setParameter('published', true)
            ->andWhere('p.title_slug = :slug')
            ->setParameter('slug', $artSlug)


            ->getQuery()
            ->getResult();
    }


}
