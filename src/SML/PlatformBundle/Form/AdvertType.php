<?php

namespace SML\PlatformBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AdvertType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $pattern = 'D%';

        $builder->add('date',  DateType::class)
            ->add('title',     TextType::class)
            ->add('author',    TextType::class)
            ->add('content',   CkeditorType::class)
            ->add('published', CheckboxType::class, array('required' => false))
            ->add('image',     ImageType::class)
            ->add('categories', EntityType::class, array(
                'class'         => 'SML\PlatformBundle\Entity\Category',
                'choice_label'  => 'name',
                'multiple'      => true,
                /*'query_builder' => function(CategoryRepository $repository) use ($pattern){
                    return $repository->getLikeQueryBuilder($pattern);
                }*/
            ))
            ->add('save',      SubmitType::class);


        // On ajoute une fonction qui va écouter un évènement
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function(FormEvent $event) {
                $advert = $event->getData();

                if (null === $advert) {
                    return;
                }

                // Si l'annonce n'est pas publiée, ou si elle n'existe pas encore en base (id est null)
                if (!$advert->getPublished() || null === $advert->getId()) {
                    $event->getForm()->add('published', CheckboxType::class, array('required' => false));
                } else {
                    $event->getForm()->remove('published');
                }
            }
        );

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SML\PlatformBundle\Entity\Advert'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sml_platformbundle_advert';
    }


}
