<?php

namespace App\Domain\Captcha;

interface ChallengeInterface
{
    public function generateKey(): string;

    public function getSolution(string $key): mixed;

    public function verify(string $key, string $solution): bool;
}