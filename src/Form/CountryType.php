<?php

namespace App\Form;

use App\Entity\Country;
use App\Repository\RegionRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class CountryType extends AbstractType
{
    private $regionRepository;

    function __construct(RegionRepository $regionRepository)
    {
        $this->regionRepository = $regionRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Load Region list to display it in a select.
        $regionOptions = [];
        foreach($this->regionRepository->findAll() as $regionEntity)
            $regionOptions[$regionEntity->getId() . ' - ' . $regionEntity->getName()] = $regionEntity;

        $builder
            ->add('commonName')
            ->add('officialName')
            ->add('flag')
            ->add('population')
            ->add('region', ChoiceType::class, [
                'choices' => $regionOptions
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Country::class,
        ]);
    }
}
