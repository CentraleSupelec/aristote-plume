<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\PlumeUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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

    public function getArticlesForUserQueryBuilder(PlumeUser $user): QueryBuilder
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.author = :author')
            ->andWhere('a.articleGeneratedAt IS NOT NULL')
            ->addOrderBy('a.articleGeneratedAt', 'DESC')
            ->setParameter('author', $user);
    }
}
