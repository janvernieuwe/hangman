<?php

namespace App\Game;

use App\Entity\Player;

interface RunnerInterface
{
    /**
     * Loads the current game or creates a new one.
     */
    public function loadGame(): Game;

    /**
     * Tests the given letter against the current game.
     */
    public function playLetter(string $letter): Game;

    /**
     * Tests the given word against the current game.
     */
    public function playWord(string $word): Game;

    public function resetGame(): void;

    public function resetGameOnSuccess(): void;

    public function resetGameOnFailure(): void;

    public function createGame(): Game;

    public function getPlayer(): Player;
}