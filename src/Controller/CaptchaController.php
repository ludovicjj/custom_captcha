<?php

namespace App\Controller;

use App\Domain\Captcha\ChallengeGenerator;
use App\Domain\Captcha\ChallengeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CaptchaController extends AbstractController
{
    #[Route('/captcha', name: 'captcha')]
    public function captcha(
        Request $request,
        ChallengeGenerator $generator
    ): Response {
        return $generator->generate($request->query->get('challenge', ''));
    }
}