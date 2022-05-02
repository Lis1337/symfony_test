<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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

    public function create(Request $request): Response
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
                'choices' => $getUsers
            ])

            ->add('save', SubmitType::class, [
                'label' => 'save',
            ])

            ->getForm();


        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->doctrine->persist($data);
            $this->doctrine->flush();

            return $this->redirectToRoute('article_index');
        }

        return $this->render('article/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function save(): RedirectResponse
    {

    }

    public function edit($id): Response
    {
        $article = $this->doctrine->getRepository(Article::class)->find($id);

        return $this->render(
            'article/edit.html.twig',
            ['article' => $article,]
        );
    }



    public function delete($id): RedirectResponse
    {
        $article = $this->doctrine->getRepository(Article::class)->find($id);

        $this->doctrine->remove($article);
        $this->doctrine->flush();

        return $this->redirectToRoute('article_index');
    }
}
