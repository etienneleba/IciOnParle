<?php

namespace App\Controller\App;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/app/user", name="app_user_")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/profile", name="profile")
     */
    public function profile()
    {
        return $this->render('app/user/profile.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    /**
     * @Route("/edit", name="edit")
     */
    public function edit(Request $request, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $user = $this->getUser();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $referer = $request->headers->get('referer');

            return new RedirectResponse($referer);
        }

        $errors = $validator->validate($form);

        foreach ($errors as $error) {
            $this->addFlash('error', $error->getMessage());
        }

        $referer = $request->headers->get('referer');

        return new RedirectResponse($referer);
    }
}
