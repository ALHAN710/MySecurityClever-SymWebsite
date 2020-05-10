<?php

namespace App\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class PasswordUpdateType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, $this->getConfiguration("Old password", "Enter your old password ..."))
            ->add('newPassword', PasswordType::class, $this->getConfiguration("New password", "Enter your new password ..."))
            ->add('confirmPassword', PasswordType::class, $this->getConfiguration("Confirm password", "Re-enter your new password ..."));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
