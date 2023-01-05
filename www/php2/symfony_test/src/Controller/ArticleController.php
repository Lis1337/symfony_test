<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\Article\ArticleCreateForm;
use App\Form\Article\ArticleEditForm;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ArticleController extends AbstractController
{
    public ManagerRegistry $doctrine;
    public EntityManagerInterface $entityManager;
    public ArticleRepository $articleRepository;
    private UserRepository $userRepository;

    /**
     * @param ManagerRegistry $doctrine
     * @param EntityManagerInterface $entityManager
     * @param ArticleRepository $articleRepository
     * @param UserRepository $userRepository
     */
    public function __construct(
        ManagerRegistry $doctrine,
        EntityManagerInterface $entityManager,
        ArticleRepository $articleRepository,
        UserRepository $userRepository
    )
    {
        $this->doctrine = $doctrine;
        $this->entityManager = $entityManager;
        $this->articleRepository = $articleRepository;
        $this->userRepository = $userRepository;
    }

    public function index(): Response
    {
        $articles = $this->articleRepository->findAll();
        
        return $this->render(
            'article/index.html.twig',
            ['articles' => $articles]
        );
    }

    public function show($id): Response
    {
        $article = $this->articleRepository->find($id);

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
                $author = $this->userRepository->findOneBy(
                    ['id' => $data['author_id']]
                );

                $article = new Article(
                    $data['title'],
                    $author,
                    $data['content']
                );

                $this->articleRepository->add($article);
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
        $article = $this->articleRepository->find($id);
        $form = $this->createForm(ArticleEditForm::class);
        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            if ($form->isSubmitted() && $form->isValid()) {

                $data = $form->getData();
                $article->setContent($data['content']);
                $article->setTitle($data['title']);

                $this->articleRepository->add($article);
                $this->addFlash('success', 'Article successfully changed!');

                return $this->redirectToRoute('article_index');
            }
        }

        return $this->render(
            'article/edit.html.twig',
            ['form' => $form->createView(), 'id' => $id, 'article' => $article]
        );
    }

    public function delete(Article $article): RedirectResponse
    {
        $this->articleRepository->remove($article);
        $this->addFlash('success', 'Article successfully deleted!');

        return $this->redirectToRoute('article_index');
    }
}
