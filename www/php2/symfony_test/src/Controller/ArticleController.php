<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\Article\ArticleCreateForm;
use App\Form\Article\ArticleEditForm;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ArticleController extends AbstractController
{
    public ManagerRegistry $doctrine;
    public EntityManagerInterface $entityManager;

    public function __construct(
        ManagerRegistry $doctrine,
        EntityManagerInterface $entityManager
    )
    {
        $this->doctrine = $doctrine;
        $this->entityManager = $entityManager;
    }

    public function index(): Response
    {
        $articles = $this->doctrine->getManager()->getRepository(Article::class)->findAll();
        return $this->render(
            'article/index.html.twig',
            ['articles' => $articles]
        );
    }

    public function show($id): Response
    {
        $article = $this->doctrine->getManager()->getRepository(Article::class)->find($id);

        return $this->render(
            'article/show.html.twig',
            ['article' => $article]
        );
    }

    public function create(Request $request): Response
    {

        $form = $this->createForm(ArticleCreateForm::class);

        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            if ($form->isSubmitted() && $form->isValid()) {

                $data = $form->getData();
                $article = new Article();
                $article->setTitle($data['title']);
                $article->setAuthorId($data['author_id']);
                $article->setContent($data['content']);

                $this->save($article);
                $this->addFlash('success', 'Article successfully created!');

                return $this->redirectToRoute('article_index');
            }
        }

        return $this->render('article/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function edit(Request $request, int $id): Response
    {
        /** @var Article $article */
        $article = $this->doctrine->getManager()->getRepository(Article::class)->find($id);
        $form = $this->createForm(ArticleEditForm::class);
        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            if ($form->isSubmitted() && $form->isValid()) {

                $data = $form->getData();
                $article->setContent($data['content']);
                $article->setTitle($data['title']);

                $this->save($article);
                $this->addFlash('success', 'Article successfully changed!');

                return $this->redirectToRoute('article_index');
            }
        }

        return $this->render(
            'article/edit.html.twig',
            ['form' => $form->createView(), 'id' => $id]
        );
    }

    public function save(Article $article): Void
    {
        try {
            $this->entityManager->persist($article);
            $this->entityManager->flush();

        } catch(Exception $ex) {
            echo 'Cannot create new article';
        }
    }

    public function delete($id): RedirectResponse
    {
        $article = $this->doctrine->getRepository(Article::class)->find($id);

        $this->doctrine->getManager()->remove($article);
        $this->doctrine->getManager()->flush();

        return $this->redirectToRoute('article_index');
    }
}
