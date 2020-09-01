<?php

namespace App\Form;

use App\Entity\SocialNetwork;
use App\Entity\SocialNetworkType as EntitySocialNetworkType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SocialNetworkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pseudo', TextType::class)
            ->add('socialNetworkType', EntityType::class, [
                'class' => EntitySocialNetworkType::class,
                'choice_label' => function (EntitySocialNetworkType $socialNetworkType) {
                    return $socialNetworkType->getName();
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SocialNetwork::class,
        ]);
    }
}
