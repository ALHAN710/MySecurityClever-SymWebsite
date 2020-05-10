<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use Cocur\Slugify\Slugify;
use App\Entity\PasswordUpdate;
use App\Form\PasswordUpdateType;
use App\Repository\DevicesRepository;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends ApplicationController
{
    /**
     * Permet d'afficher et de gérer le formulaire de connexion
     * 
     * @Route("/login", name="account_login")
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();

        return $this->render('account/login.html.twig', [
            'hasError' => $error !== null,
            'username' => $username
        ]);
    }

    /**
     * Permet de se déconnecter
     *
     * @Route("/logout", name = "account_logout")
     * 
     * @return void
     */
    public function logout()
    {
    }

    /**
     * Permet d'afficher et de traiter le formulaire de modification de profil
     *
     * @Route("/account/profile/{id<\d+>}/{tab<\d>?0}", name="account_profile")
     * 
     * 
     * @return Response
     */
    //@IsGranted("ROLE_USER")
    public function profile(
        $id,
        $tab,
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordEncoderInterface $encoder,
        DevicesRepository $repoDevices
    ) {
        $user = $this->getUser();

        $lastAvatar = $user->getAvatar();

        $filesystem = new Filesystem();

        $slugify = new Slugify();

        $passwordUpdate = new PasswordUpdate();
        //dump($user);
        $form = $this->createForm(AccountType::class, $user);

        $formPassword = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $avatarFile */
            $avatarFile = $form->get('avatar')->getData();

            // this condition is needed because the 'avatar' field is not required
            // so the Image file must be processed only when a file is uploaded
            if ($avatarFile) {
                $originalFilename = pathinfo($avatarFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugify->slugify($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $avatarFile->guessExtension();

                // Move the file to the directory where avatars are stored
                try {
                    $avatarFile->move(
                        $this->getParameter('avatar_directory'),
                        $newFilename
                    );
                    $path = $this->getParameter('avatar_directory') . '/' . $lastAvatar;
                    if ($lastAvatar != NULL) $filesystem->remove($path);
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $user->setAvatar($newFilename);
            }

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Profile changes have been successfully saved"
            );

            return $this->redirectToRoute('home_page');
        }
        $errors = $formPassword['oldPassword']->getErrors();
        dump($errors);

        $formPassword->handleRequest($request);
        if ($formPassword->isSubmitted() && $formPassword->isValid()) {
            //1. Vérifier que le oldpassword soit le même que celui de l'user
            if (!password_verify($passwordUpdate->getOldPassword(), $user->getHash())) {
                //Gérer l'erreur
                $tab = 1;
                $formPassword->get('oldPassword')->addError(new FormError("The password entered is not your current password"));
                //return $this->redirectToRoute($this->get('router')->generate('account_profile', ['_fragment' => 'password']));
                return $this->redirectToRoute('account_profile', [
                    'id'  => $id,
                    'tab' => '1'
                ]);
            } else {
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $encoder->encodePassword($user, $newPassword);

                $user->setHash($hash);

                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "Your password has been successfully changed"
                );

                return $this->redirectToRoute('home_page');
            }
        }

        return $this->render('account/profile.html.twig', [
            'form'         => $form->createView(),
            'formPassword' => $formPassword->createView(),
            'user'         => $user,
            'tab'          => $tab,
            'devicesNb'  => $this->getRepoDevice($repoDevices)['devicesNb'],
            'devicesTab' => $this->getRepoDevice($repoDevices)['devicesTab']
        ]);
    }

    /**
     * Permet de modifier le mot de passe
     * 
     * @Route("/account/password-update", name="account_password")
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function updatePassword(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $manager)
    {
        $passwordUpdate = new PasswordUpdate();

        $user = $this->getUser();

        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //1. Vérifier que le oldpassword soit le même que celui de l'user
            if (!password_verify($passwordUpdate->getNewPassword(), $user->getHash())) {
                //Gérer l'erreur
                $form->get('oldPassword')->addError(new FormError("Le mot de passe saisi n'est pas votre mot de passe actuel"));
            } else {
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $encoder->encodePassword($user, $newPassword);

                $user->setHash($hash);

                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "Votre mot de passe a bien été modifié"
                );

                return $this->redirectToRoute('homepage');
            }
        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * Permet de supprimer un Utilisateur
     * 
     * @Route("/admin/user/{id}/delete", name="admin_user_delete")
     *
     * @param User $user
     * @param EntityManagerInterface $manager
     * @return void
     */
    public function delete(User $user, EntityManagerInterface $manager)
    {
        $_user = $user->getFullName();

        $manager->remove($user);
        $manager->flush();

        $this->addFlash(
            'success',
            "La suppression de l'utilisateur <strong>{$_user}</strong> a été effectuées avec succès !"
        );

        return $this->redirectToRoute("admin_user1_index");
    }
}
