<?php

namespace App\Controller;

use App\Form\PlayerType;
use App\Entity\Player;
use App\Player\Manager as PlayerManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("/game")
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login", methods={"GET", "POST"})
     */
    public function login(AuthenticationUtils $authUtils): Response
    {
        return $this->render('security/login.html.twig', [
            'last_username' => $authUtils->getLastUsername(),
            'error' => $authUtils->getLastAuthenticationError(),
        ]);
    }

    /**
     * @Route("/logout", name="logout", methods="GET")
     */
    public function logout()
    {
        // Code never executed as the firewall intercept the request before the
        // Routing component can even match the pattern with the action.
    }

    /**
     * @Route("/register", name="register", methods={"GET", "POST"})
     */
    public function register(Request $request, PlayerManager $playerManager): Response
    {
        $form = $this->createForm(PlayerType::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $playerManager->register($form->getData());

            $this->addFlash('success', 'You have been successfully added to the big family of the hangman game!');

            return $this->redirectToRoute('login');
        }

        return $this->render('security/register.html.twig', [
            'registration_form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/players", name="players", methods="GET")
     */
    public function listPlayers(): Response
    {
        return $this->render('security/list_players.html.twig', [
            'players' => $this->getDoctrine()->getRepository(Player::class)->findAll(),
        ]);
    }
}
