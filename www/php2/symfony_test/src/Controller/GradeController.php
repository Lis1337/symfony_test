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

    public function dateTimePractice(): Response
    {
        setcookie(
            'test',
            'happy new year!',
            strtotime('+1 hour')
        );

        $currentTime = '2023-01-07 13:06:19.0';
        $anotherTimeFormat = '12:00 07.01.25';

        $unixTime = strtotime($currentTime);

        $timeFromString = new \DateTime($currentTime);
        $timeFromUnixTime = (new \DateTime())->setTimestamp($unixTime);

        $curTimeAtom = '2023-01-07T08:20:25+00:00';
        $timeAtom = (new \DateTime())->format(\DateTimeInterface::ATOM);

        $time = new \DateTime($currentTime);
        $time->add(new \DateInterval('P1Y'));
        $time->add(new \DateInterval('P1M'));


        $dtImmutable = new \DateTimeImmutable($currentTime);
        $anotherDtImmutable = (new \DateTimeImmutable())->setTimestamp($unixTime);

        $dateFromAnotherFormat = new \DateTime();
        $dateFromAnotherFormat = $dateFromAnotherFormat::createFromFormat('G:i d.m.y', $anotherTimeFormat);

        return $this->render(
            'grade/dateTimePractice.html.twig',
            [
                'dateTimeModified' => $time->format('Y-m-d H:i:s.u'),
                'dateTimeImmutable' => $dtImmutable->format(\DateTimeInterface::ATOM),
                'dateTimeAnotherFormat' => $dateFromAnotherFormat->format('Y-m-d H:i:s.u')
            ]
        );

    }

    public function getCookie(): Response
    {
        return $this->render(
            'grade/getCookie.html.twig',
            [
                'cookie' => $_COOKIE['test']
            ]
        );
    }
}
