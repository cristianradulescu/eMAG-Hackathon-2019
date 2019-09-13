<?php

namespace App\Controller;

use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Cache\SymfonyCache;
use BotMan\BotMan\Drivers\DriverManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\OnboardingConversation;

class DefaultController extends AbstractController
{
    /**
     * @Route("/inabot", name="inabot")
     */
    public function renderBot()
    {
        return $this->render('index.html.twig');
    }

    /**
     * @Route("/botman", name="botman")
     */
    public function botman()
    {
        $config = [];

        DriverManager::loadDriver(\BotMan\Drivers\Web\WebDriver::class);

        $adapter = new FilesystemAdapter();

        $botman = BotManFactory::create($config, new SymfonyCache($adapter));

        $botman->hears('Hello', function($bot) {
            $bot->startConversation(new OnboardingConversation);
        });

        $botman->listen();
        
        return new Response('', Response::HTTP_OK);
    }
}