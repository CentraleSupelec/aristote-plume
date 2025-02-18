<?php

namespace App\Form;

use App\Constants;
use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleCreationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('requestedTopic', TextareaType::class, [
                'label' => 'article.form.requested_topic.label',
            ])
            ->add('requestedType', ChoiceType::class, [
                'label' => 'article.form.requested_type.label',
                'choices' => Constants::getArticleTypes(),
                'disabled' => true,
            ])
            ->add('requestedLanguage', ChoiceType::class, [
                'label' => 'article.form.requested_language.label',
                'choices' => Constants::getArticleLanguages(),
                'disabled' => false,
            ])
            ->add('requestedLanguageModel', ChoiceType::class, [
                'label' => 'article.form.requested_language_model.label',
                'choices' => Constants::getArticleGenerationModels(),
                'disabled' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Article::class]);
    }
}
