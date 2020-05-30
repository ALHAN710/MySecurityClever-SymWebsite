<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use Cocur\Slugify\Slugify;
use App\Entity\PasswordUpdate;
use App\Form\PasswordUpdateType;
use App\Repository\UserRepository;
use App\Repository\DevicesRepository;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
     * @Route("/account/profile/{id<\d+>}", name="account_profile")
     * 
     * @Security("is_granted('ROLE_USER')", message = "Vous n'avez pas le droit d'accéder à cette ressource")
     * 
     * 
     * @return Response
     */
    //@IsGranted("ROLE_USER")
    public function profile(
        $id,
        Request $request,
        EntityManagerInterface $manager,
        UserRepository $repoUsers,
        DevicesRepository $repoDevices
    ) {
        $user = $repoUsers->findOneBy(['id' => $id]);

        //$lastAvatar = $user->getAvatar();

        //$filesystem = new Filesystem();

        //$slugify = new Slugify();

        //dump($user);
        $form = $this->createForm(AccountType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Profile changes have been successfully saved"
            );

            return $this->redirectToRoute('home_page');
        }


        return $this->render('account/profile.html.twig', [
            'form'           => $form->createView(),
            //'passwordUpdate' => $passwordUpdate,
            'user'           => $user,
            'devicesNb'      => $this->getRepoDevice($repoDevices)['devicesNb'],
            'devicesTab'     => $this->getRepoDevice($repoDevices)['devicesTab']
        ]);
    }

    /**
     * Permet de modifier le mot de passe
     * 
     * @Route("/account/password-update/{id<\d+>}", name="account_password")
     * 
     * @Security("(is_granted('ROLE_USER') and user.id == id) or is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN')", message = "Vous n'avez pas le droit d'accéder à cette ressource")
     * 
     *
     * @return Response
     */
    public function updatePassword(
        $id,
        Request $request,
        UserPasswordEncoderInterface $encoder,
        EntityManagerInterface $manager,
        DevicesRepository $repoDevices
    ) {

        $user = $this->getUser();

        $passwordUpdate = new PasswordUpdate();
        //dump($user);
        //$form = $this->createForm(AccountType::class, $user);

        $formPassword = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        $formPassword->handleRequest($request);
        //dump($request);
        //dump($formPassword->isSubmitted());
        //dump($passwordUpdate);
        if ($formPassword->isSubmitted() && $formPassword->isValid()) {
            //1. Vérifier que le oldpassword soit le même que celui de l'user
            if (!password_verify($passwordUpdate->getOldPassword(), $user->getHash())) {
                //Gérer l'erreur
                $formPassword->get('oldPassword')->addError(new FormError("The password entered is not your current password"));
                //return $this->redirectToRoute($this->get('router')->generate('account_profile', ['_fragment' => 'password']));
                return $this->redirectToRoute('account_password', [
                    'id'  => $id,
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
        } else if ($request->request->has('password_update')) {
            //dump($request->request);
        }

        return $this->render('account/password.html.twig', [
            'user'           => $user,
            'formPassword'   => $formPassword->createView(),
            'devicesNb'      => $this->getRepoDevice($repoDevices)['devicesNb'],
            'devicesTab'     => $this->getRepoDevice($repoDevices)['devicesTab']
        ]);
    }

    /**
     * Permet de supprimer un Utilisateur
     * 
     * @Route("/admin/user/{id}/delete", name="admin_user_delete")
     *
     * @IsGranted("ROLE_SUPER_ADMIN")
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
