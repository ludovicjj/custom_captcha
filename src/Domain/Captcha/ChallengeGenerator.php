<?php

namespace App\Domain\Captcha;

use Symfony\Component\HttpFoundation\Response;

interface ChallengeGenerator
{
    public function generate(string $key): Response;
}