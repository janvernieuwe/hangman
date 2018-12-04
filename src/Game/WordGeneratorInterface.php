<?php
declare(strict_types=1);

namespace App\Game;

interface WordGeneratorInterface
{
    public function getRandomWord(): string;
}
