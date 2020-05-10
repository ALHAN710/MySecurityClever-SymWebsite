<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'firstName',
                TextType::class,
                $this->getConfiguration("First Name :", "Your First Name please...")
            )

            ->add(
                'lastName',
                TextType::class,
                $this->getConfiguration("Last Name :", "Your Last Name please...")
            )

            ->add(
                'email',
                EmailType::class,
                $this->getConfiguration("Email : ", "Your email address please...")

            )

            ->add(
                'avatar',
                TextType::class,
                $this->getConfiguration("Avatar :", "Choose your avatar", [
                    'required' => false
                ])
            )

            ->add(
                'hash',
                PasswordType::class,
                $this->getConfiguration("Password :", "Password")
            )

            ->add(
                'passwordConfirm',
                PasswordType::class,
                $this->getConfiguration("Confirm password :", "Confirm your Password")
            )

            ->add(
                'countryCode',
                TextType::class,
                $this->getConfiguration("Country code :", "Telephone code of your country")

            )

            ->add(
                'phoneNumber',
                TextType::class,
                $this->getConfiguration("Phone Number :", "Your Phone Number please...")

            )

            ->add(
                'role',
                ChoiceType::class,
                [
                    'choices' => [
                        'USER' => 'ROLE_USER',
                        'ADMIN' => 'ROLE_ADMIN'
                    ],
                    'label'    => 'User Role :'
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
