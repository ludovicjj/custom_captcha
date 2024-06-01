<?php

namespace App\Domain\Captcha\Puzzle;

use App\Domain\Captcha\ChallengeInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PuzzleChallenge implements ChallengeInterface
{
    public const WIDTH = 350;
    public const HEIGHT = 200;
    public const PIECE_WIDTH = 80;
    public const PIECE_HEIGHT = 50;
    private const SESSION_KEY = 'puzzles';

    private const PRECISION = 2;

    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function generateKey(): string
    {
        $session = $this->getSession();

        $now = time();
        $x = mt_rand(0, self::WIDTH - self::PIECE_WIDTH);
        $y = mt_rand(0, self::HEIGHT - self::PIECE_HEIGHT);

        $puzzles = $session->get(self::SESSION_KEY, []);
        $puzzles[] = ['key' => $now, 'solution' => [$x, $y]];
        $session->set(self::SESSION_KEY, array_slice($puzzles, -10));

        return $now;
    }

    public function getSolution(string $key): ?array
    {
        $puzzles = $this->getSession()->get(self::SESSION_KEY, []);

        foreach ($puzzles as $puzzle) {
            if ($puzzle['key'] !== intval($key)) {
                continue;
            }

            return $puzzle['solution'];
        }

        return null;
    }

    public function verify(string $key, string $solution): bool
    {
        $expected = $this->getSolution($key);

        if (!$expected) {
            return false;
        }

        $session = $this->getSession();
        $puzzles = $session->get(self::SESSION_KEY, []);

        $session->set(self::SESSION_KEY, array_filter($puzzles, function(array $puzzle) use ($key) {
            return $puzzle['key'] !== $key;
        }));

        $answer = $this->stringToSolution($solution);

        return abs($expected[0] - $answer[0]) <= self::PRECISION && abs($expected[1] - $answer[1]) <= self::PRECISION;
    }

    private function getSession(): SessionInterface
    {
        return $this->requestStack->getMainRequest()->getSession();
    }

    private function stringToSolution(string $string): array
    {
        $parts = explode('-', $string, 2);

        if (count($parts) !== 2) {
            return [-1, -1];
        }

        return [intval($parts[0]), intval($parts[1])];
    }
}