<?php

namespace App\Controller;

use App\Entity\Fruit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class GradeController extends AbstractController
{
    public function showFunctions(): Response
    {
        return $this->render(
            'grade/showFunctions.html.twig',
            [
                'callFunctionWithString' => $this->callFunctionWithString(),
                'anonymousFunction' => $this->anonymousFunction(),
                'arrowFunctions' => $this->arrowFunctions()
            ]
        );
    }

    private function printMeSomething(): string
    {
        return 'Something has been printed';
    }

    private function callFunctionWithString(): string
    {
        $example = 'printMeSomething';
        return $this->$example();
    }

    private function anonymousFunction(): int
    {
        $totalCount = 0;
        $fruits = [
            'apples' => 10,
            'peaches' => 7,
            'bananas' => 4
        ];

        $callBack =
            static function ($fruits) use (&$totalCount)
            {
                $totalCount += $fruits;
            };

        array_walk($fruits, $callBack);

        return $totalCount;
    }

    private function arrowFunctions(): int
    {
        $num = 5;

        $func = fn($multiply) => $multiply * $num;

       return $func(5);
    }

    public function cloneObjects(): Response
    {
        $apple = new Fruit(
            'red',
            'small'
        );
        $linkToApple = $apple;

        $anotherApple = clone $apple;

        $banana = clone $apple;
        $banana->setColor('yellow');
        $banana->setSize('big');

        $comparisonFirst = $apple !== $anotherApple;
        $comparisonThird = $apple === $linkToApple;

        return $this->render(
            'grade/cloneObjects.html.twig',
            [
                'comparisonFirst' => $comparisonFirst,
                'comparisonThird' => $comparisonThird,
                'apple' => $apple,
                'anotherApple' => $anotherApple,
                'banana' => $banana
            ]
        );
    }
}
