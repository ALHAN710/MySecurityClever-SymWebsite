<?php

namespace App\Controller;

use DateTime;
use App\Entity\Role;
use App\Entity\User;
use Cocur\Slugify\Slugify;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index()
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * Permet de créer un utilisateur
     *
     * @Route("/admin/user/create", name="admin_user_create")
     * 
     * @param User $user
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function create(EntityManagerInterface $manager, Request $request, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $slugify = new Slugify();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getHash());

            $userRole = new Role();
            $userRole->setTitre($user->getRole());
            $date = new DateTime(date('Y-m-d H:i:s'));
            $user->setCreatedAt($date);


            $manager->persist($userRole);

            $user->setHash($hash)
                ->addUserRole($userRole);

            /** @var UploadedFile $avatarFile */
            $avatarFile = $form->get('avatar')->getData();

            // this condition is needed because the 'avatar' field is not required
            // so the PDF file must be processed only when a file is uploaded
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
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $user->setAvatar($newFilename);
            }


            $manager->persist($user);
            $manager->flush();

            /*$this->addFlash(
                'success',
                "Compte utilisateur crée. Veuillez vous connecter !"
            );

            return $this->redirectToRoute('account_login');*/

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le Compte utilisateur <strong> {$user->getLastName()}</strong> a été crée avec succès. !"
            );

            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('user/new.html.twig', [
            'form' => $form->createView()
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
        $_user = $user->getLastName();

        $manager->remove($user);
        $manager->flush();

        $this->addFlash(
            'success',
            "La suppression de l'utilisateur <strong>{$_user}</strong> a été effectuées avec succès !"
        );

        return $this->redirectToRoute("admin_user_index");
    }
}
