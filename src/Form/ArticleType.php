<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Section;
use App\Entity\Tag;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
           // ->add('user_id')
            ->add('title')
           // ->add('title_slug')
            ->add('text')
           // ->add('article_date_created', null, [
           //     'widget' => 'single_text',
           // ])
           // ->add('article_date_posted', null, [
           //     'widget' => 'single_text',
           // ])
            ->add('published')
           // ->add('user', EntityType::class, [
           //     'class' => User::class,
           //     'choice_label' => 'id',
           // ])
            ->add('sections', EntityType::class, [
                'class' => Section::class,
                'choice_label' => 'sectionTitle',
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'tagName',
                'multiple' => true,
                'expanded' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
