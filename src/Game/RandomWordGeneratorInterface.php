<?php
declare(strict_types=1);

namespace App\Game;

interface RandomWordGeneratorInterface
{
    public function getRandomWord(): string;
}
