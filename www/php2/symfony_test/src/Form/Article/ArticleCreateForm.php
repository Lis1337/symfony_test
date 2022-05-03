<?php

namespace App\Form\Article;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ArticleCreateForm extends AbstractType
{
    public EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => ['size' => 100]
            ])
            ->add('content', TextType::class, [
                'attr' => ['size' => 100], 'required' => false
            ])
            ->add('author_id', EntityType::class, [
                'class' => User::class,
                'choices' => $this->entityManager->getRepository(User::class)->findAll()
            ])
            ->add('save', SubmitType::class, [
                'label' => 'save'
            ]);
    }
}
