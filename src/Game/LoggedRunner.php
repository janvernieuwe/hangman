<?php

namespace App\Game;

use App\Entity\Player;
use Psr\Log\LoggerInterface;

class LoggedRunner implements RunnerInterface
{

    /**
     * @var Runner
     */
    private $runner;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * LoggedRunner constructor.
     *
     * @param Runner          $runner
     * @param LoggerInterface $logger
     */
    public function __construct(Runner $runner, LoggerInterface $logger)
    {
        $this->runner = $runner;
        $this->logger = $logger;
    }

    /**
     * Loads the current game or creates a new one.
     */
    public function loadGame(): Game
    {
        $this->logger->debug(__FUNCTION__);
        return $this->runner->loadGame();
    }

    /**
     * Tests the given letter against the current game.
     */
    public function playLetter(string $letter): Game
    {
        $this->logger->debug(__FUNCTION__, [$letter]);
        return $this->runner->playLetter($letter);
    }

    /**
     * Tests the given word against the current game.
     */
    public function playWord(string $word): Game
    {
        $this->logger->debug(__FUNCTION__, [$word]);
        return $this->runner->playWord($word);
    }

    public function resetGame(): void
    {
        $this->logger->debug(__FUNCTION__);
        $this->runner->resetGame();
    }

    public function resetGameOnSuccess(): void
    {
        $this->logger->debug(__FUNCTION__);
        $this->runner->resetGameOnSuccess();
    }

    public function resetGameOnFailure(): void
    {
        $this->logger->debug(__FUNCTION__);
        $this->runner->resetGameOnFailure();
    }

    public function createGame(): Game
    {
        $this->logger->debug(__FUNCTION__);
        return $this->runner->createGame();
    }

    public function getPlayer(): Player
    {
        $this->logger->debug(__FUNCTION__);
        return $this->runner->getPlayer();
    }
}