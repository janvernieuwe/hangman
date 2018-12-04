<?php

namespace App\Controller;

use App\Game\Exception\LogicException;
use App\Game\Runner;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/game")
 */
class GameController extends AbstractController
{
    /**
     * @Route("/", name="game_home", methods="GET", defaults={"_locale": "en"})
     */
    public function home(Runner $gameRunner): Response
    {
        return $this->render('game/home.html.twig', [
            'game' => $gameRunner->loadGame()
        ]);
    }

    /**
     * @Route("/won", name="game_won", methods="GET")
     */
    public function won(Runner $gameRunner): Response
    {
        $game = $gameRunner->loadGame();

        try {
            $gameRunner->resetGameOnSuccess();
        } catch (LogicException $e) {
            throw $this->createAccessDeniedException($e->getMessage(), $e);
        }

        return $this->render('game/won.html.twig', [
            'game' => $game,
        ]);
    }

    /**
     * @Route("/failed", name="game_failed", methods="GET")
     */
    public function failed(Runner $gameRunner): Response
    {
        $game = $gameRunner->loadGame();

        try {
            $gameRunner->resetGameOnFailure();
        } catch (LogicException $e) {
            throw $this->createAccessDeniedException($e->getMessage(), $e);
        }

        return $this->render('game/failed.html.twig', [
            'game' => $game,
        ]);
    }

    /**
     * @Route("/reset", name="game_reset", methods={"GET", "POST"})
     */
    public function reset(Runner $gameRunner): RedirectResponse
    {
        $gameRunner->resetGame();

        return $this->redirectToRoute('game_home');
    }

    /**
     * This action plays a letter.
     *
     * @Route("/play/{letter}", name="game_play_letter", methods={"GET"}, requirements={
     *   "letter"="[A-Z]"
     * })
     */
    public function playLetter(Runner $gameRunner, string $letter): RedirectResponse
    {
        $game = $gameRunner->playLetter($letter);

        if ($game->isOver()) {
            return $this->redirectToRoute($game->isWon() ? 'game_won' : 'game_failed');
        }

        return $this->redirectToRoute('game_home');
    }

    /**
     * This action plays a word.
     *
     * @Route("/play", name="game_play_word", condition="request.request.has('word')", methods={"POST"})
     */
    public function playWord(Request $request, Runner $gameRunner): RedirectResponse
    {
        $game = $gameRunner->playWord($request->request->get('word'));

        return $this->redirectToRoute($game->isWon() ? 'game_won' : 'game_failed');
    }

    /**
     * This method doesn't define a @Route annotation because it's not accessed
     * publicly via an URL but included in the template via Twig's render() function.
     */
    public function testimonials(): Response
    {
        return $this->render('testimonials.html.twig', [
            'testimonials' => [
                'John Doe' => 'I love this game, so addictive!',
                'Martin Durand' => 'Best web application ever',
                'Paula Smith' => 'Awesomeness!',
            ],
        ]);
    }
}
