<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ArticleController extends AbstractController
{
    public object $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine->getManager();
    }

    public function index(): Response
    {
        $articles = $this->doctrine->getRepository(Article::class)->findAll();
        return $this->render(
            'article/index.html.twig',
            ['articles' => $articles]
        );
    }

    public function show($id): Response
    {
        $article = $this->doctrine->getRepository(Article::class)->find($id);

        return $this->render(
            'article/show.html.twig',
            ['article' => $article]
        );
    }

    public function create(): Response
    {
        $getUsers = $this->doctrine->getRepository(User::class)->findAll();

        $article = new Article();

        $form = $this->createFormBuilder($article)
            ->add('title', TextType::class, [
                'attr' => ['size' => 100]
            ])
            ->add('content', TextType::class, [
                'attr' => ['size' => 100]
            ])
            ->add('author_id', EntityType::class, [
                'class' => User::class,
                'choices' => $article->getUsers()
            ])
            ->getForm();

        return $this->render('article/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function edit($id): Response
    {
        $article = $this->doctrine->getRepository(Article::class)->find($id);

        return $this->render(
            'article/edit.html.twig',
            ['article' => $article,]
        );
    }


    /*public function save(): Response
    {
        $doctrine = $this->getDoctrine()->getManager();

        $article = new Article();
        $users = $doctrine->getRepository(User::class)->findAll();
        $usersIds = [];
        foreach ($users as $user) {
            $usersIds[] = $user->id;
        }

        $form = $this->createFormBuilder($article)
            ->add('title', TextType::class)
            ->add('content', TextType::class)
            ->add('author_id', ChoiceType::class, [
                'authors' => [
                    $usersIds
                ]
            ])
            ->getForm();

        return $this->renderForm('article/create.html.twig', [
            'form' => $form,
            ]
        );


        $entityManager = $doctrine->getManager();

        if (isset($_POST['id'])) {
            $article = $doctrine->getRepository(Article::class)->find($_POST['id']);

        } else {
            $article = new Article();
            $userId =  $doctrine->getRepository(User::class)->find($_POST['author_id']);
            $article->setAuthorId($userId);
            }

        $article->setTitle($_POST['title']);
        if (isset($_POST['content'])) {
            $article->setContent($_POST['content']);
        }
        $entityManager->persist($article);
        $entityManager->flush();

        return $this->redirectToRoute('article_index');
    } */

    public function delete($id): RedirectResponse
    {
        $article = $this->doctrine->getRepository(Article::class)->find($id);

        $this->doctrine->remove($article);
        $this->doctrine->flush();

        return $this->redirectToRoute('article_index');
    }
}
