<?php

namespace App\Form\Type;

use App\Domain\Captcha\ChallengeInterface;
use App\Domain\Captcha\Puzzle\PuzzleChallenge;
use App\Validator\Challenge;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class CaptchaType extends AbstractType
{
    public function __construct(
        private readonly ChallengeInterface $challenge,
        private readonly UrlGeneratorInterface $urlGenerator
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('challenge', HiddenType::class, [
                'attr' => [
                    'class' => 'captcha-challenge'
                ]
            ])
            ->add('answer', HiddenType::class, [
                'attr' => [
                    'class' => 'captcha-anwser'
                ],
            ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $key = $this->challenge->generateKey();

        $view->vars['attr'] = [
            'width'         => PuzzleChallenge::WIDTH,
            'height'        => PuzzleChallenge::HEIGHT,
            'piece-width'   => PuzzleChallenge::PIECE_WIDTH,
            'piece-height'  => PuzzleChallenge::PIECE_HEIGHT,
            'src'           => $this->urlGenerator->generate($options['routeName'], ['challenge' => $key])
        ];

        $view->vars['challenge'] = $key;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'constraints' => [
                new NotBlank(),
                new Challenge()
            ],
            'routeName' => 'captcha'
        ]);
    }
}