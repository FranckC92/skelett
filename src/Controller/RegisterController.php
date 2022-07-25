<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{
    //mettre en place l entity manager doctrine
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }
    #[Route('/register', name: 'app_register')]
    public function index(Request $request, UserPasswordHasherInterface $encoder)
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);
        //  conditions pour le form si il est remplis et valid alors
        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();
            //encodage mdp
            $password = $encoder->hashPassword($user, $user->getPassword());

            $user->setPassword($password);
            

           // on envoye a la bdd
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
