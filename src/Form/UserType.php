<?php

namespace App\Form;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => ['class' => 'form-control']
            ])

            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'required' =>false,
                'label' => 'mot de passe',
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new Regex([
                        'pattern' => '/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/',
                        'message' => 'Le mot de passe doit avoir au moins 8 caractères,  1 chiffre et 1 lettre.',
                    ]),
                ],
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['class' => 'form-control']
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom',
            ])

            ->add('picture', FileType::class, [
                'label' => 'Photo',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/jpg',
                            'image/png',
                            'image/svg',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image format',
                    ])
                ],
            ])
            ->add('area', ChoiceType::class, [
                'choices' => [
                    'RH' => 'RH',
                    'Informatique' => 'Informatique',
                    'Comptabilité' => 'Comptabilité',
                    'Direction' => 'Direction'
                ],
                'label' => 'Secteur',
            ])
            ->add('contractType', ChoiceType::class, [
                'choices' => [
                    'CDI' => 'CDI',
                    'CDD' => 'CDD',
                    'INTERIM' => 'INTERIM'
                ],
                'label' => 'Contrat',
                'attr' => [
                    'data-conditional' => 'exitDate'
                ],
                'constraints' => [
                    new Choice([
                        'choices' => ['CDI', 'CDD', 'INTERIM'],
                        'message' => 'Veuillez sélectionner un contrat valide.',
                    ]),
                ],
            ])
            ->add('exitDate', DateType::class, [
                'label' => 'Date de fin de contrat',
                'required' => false,
                'format' => 'dd/MM/yyyy'
            ])
            ->add('Valider', SubmitType::class);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $user = $event->getData();
            $form = $event->getForm();
            if (isset($user['contractType']) && in_array($user['contractType'], ['CDD','INTERIM'])) {
                $form->add('exitDate', DateType::class, [
                    'label' => 'Date de fin de contrat',
                    'required' => false,
                    'format' => 'dd/MM/yyyy'
                ]);
            };
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => true,
        ]);
    }
}
