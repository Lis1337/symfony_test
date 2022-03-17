<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class ArticleController extends AbstractController
{
    public function index(ManagerRegistry $doctrine): Response
    {
        $articles = $doctrine->getRepository(Article::class)->findAll();
        return $this->render(
            'article/index.html.twig',
            ['articles' => $articles]
        );
    }

    public function show(ManagerRegistry $doctrine, $id): Response
    {
        $article = $doctrine->getRepository(Article::class)->find($id);

        return $this->render(
            'article/show.html.twig',
            ['article' => $article]
        );
    }

    public function create(ManagerRegistry $doctrine): Response
    {
        $users = $doctrine->getRepository(User::class)->findAll();
        return $this->render(
            'article/create.html.twig',
            ['users' => $users]
        );
    }


    public function save(ManagerRegistry $doctrine): RedirectResponse
    {
        $entityManager = $doctrine->getManager();

        $article = new Article();
        $article->setTitle($_POST['title']);
        $article->setContent($_POST['content']);
        $article->setAuthorId($_POST['author_id']);

        $entityManager->persist($article);
        $entityManager->flush();

        return $this->redirectToRoute('index');
    }
}
