<?php

namespace App\Domain\Captcha\Puzzle;

use App\Domain\Captcha\ChallengeGenerator;
use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\Response;

class PuzzleGenerator implements ChallengeGenerator
{
    public function __construct(private readonly PuzzleChallenge $puzzleChallenge)
    {
    }

    public function generate(string $key): Response
    {
        $position = $this->puzzleChallenge->getSolution($key);

        if (empty($position)) {
            return new Response(null, Response::HTTP_NOT_FOUND);
        }

        [$x, $y] = $position;
        $backgroundPath = sprintf('%s/img/renard-puzzle.png', __DIR__);
        $piecePath = sprintf('%s/img/piece-puzzle.png', __DIR__);

        $manager = new ImageManager(['driver' => 'gd']);
        $piece = $manager->make($piecePath);
        $image = $manager->make($backgroundPath);
        $hole = clone $piece;

        // piece
        $piece
            ->insert($image, 'top-left', -$x, -$y)
            ->mask($hole, true);

        // hole
        $hole->opacity(60);

        // image
        $image
                // resize canvas
                // add PIECE_WIDTH on axe x
                // add 0 on axe y
                // anchor ?
                // relative ?
                // background white
            ->resizeCanvas(
                PuzzleChallenge::PIECE_WIDTH,
                0,
                'left',
                true,
                'rgba(0,0,0,0)'
            )
            // insert piece into image from 'top-left' at $x, $y
            ->insert($piece, 'top-right')
            // insert hole into image from 'top-left' at $x, $y
            ->insert($hole, 'top-left', $x, $y);


        return $image->response('png');

        //return new Response('test', Response::HTTP_OK);
    }
}