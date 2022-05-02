<?php


namespace App\Form\Article;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ArticleEditForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => ['size' => 100]
            ])
            ->add('content', TextType::class, [
                'attr' => ['size' => 100]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'save'
            ]);
    }
}
