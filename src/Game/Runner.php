<?php

namespace App\Game;

use App\Entity\Player;
use App\Game\Exception\LogicException;
use App\Game\Exception\RuntimeException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class Runner
{
    private $storage;
    private $wordList;
    private $tokenStorage;

    public function __construct(Storage $storage, RandomWordGeneratorInterface $wordList, TokenStorageInterface $tokenStorage)
    {
        $this->storage = $storage;
        $this->wordList = $wordList;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Loads the current game or creates a new one.
     */
    public function loadGame(): Game
    {
        if ($this->storage->hasGame()) {
            return $this->storage->loadGame();
        }

        return $this->createGame();
    }

    /**
     * Tests the given letter against the current game.
     */
    public function playLetter(string $letter): Game
    {
        $game = $this->storage->loadGame();

        $game->tryLetter($letter);
        $this->storage->save($game);

        return $game;
    }

    /**
     * Tests the given word against the current game.
     */
    public function playWord(string $word): Game
    {
        $game = $this->storage->loadGame();

        $game->tryWord($word);
        $this->storage->save($game);

        return $game;
    }

    public function resetGame(): void
    {
        $this->storage->loadGame();
        $this->storage->reset();
    }

    public function resetGameOnSuccess(): void
    {
        $game = $this->storage->loadGame();

        if (!$game->isOver()) {
            throw new LogicException('Current game is not yet over.');
        }

        if (!$game->isWon()) {
            throw new LogicException('Current game must be won.');
        }

        $this->resetGame();
    }

    public function resetGameOnFailure(): void
    {
        $game = $this->storage->loadGame();

        if (!$game->isOver()) {
            throw new LogicException('Current game is not yet over.');
        }

        if (!$game->isHanged()) {
            throw new LogicException('Current game must be lost.');
        }

        $this->resetGame();
    }

    private function createGame(): Game
    {
        $word = $this->wordList->getRandomWord();
        $game = $this->storage->newGame($word);
        $this->storage->save($game);

        return $game;
    }

    private function getPlayer(): Player
    {
        $token = $this->tokenStorage->getToken();

        if (null === $token || !$token->getUser() instanceof Player) {
            throw new RuntimeException('No player authenticated.');
        }

        return $token->getUser();
    }
}
