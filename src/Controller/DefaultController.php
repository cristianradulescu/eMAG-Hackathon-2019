<?php

namespace App\Controller;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Cache\SymfonyCache;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Middleware\ApiAi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    private $intentCategories = [
        "hr",
        "finance"
    ];

    /**
     * @Route("/", name="homepage")
     */
    public function homepage()
    {
        return $this->render('homepage.html.twig');
    }

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

        $dialogflow = ApiAi::create($_ENV['DIALOGFLOW_TOKEN'])->listenForAction();

        $botman->middleware->received($dialogflow);

        foreach ($this->intentCategories as $intentCategoryName){
            $botman->hears($intentCategoryName, function (BotMan $bot) {
                $extras = $bot->getMessage()->getExtras();
                $apiReply = $extras['apiReply'];
//                $apiAction = $extras['apiAction'];
//                $apiIntent = $extras['apiIntent'];

                $bot->reply($apiReply);
            })->middleware($dialogflow);
        }

        $botman->fallback(function(Botman $bot) {
            $bot->reply($bot->getMessage()->getExtras()['apiReply']);
        });

        $botman->listen();
        
        return new Response('', Response::HTTP_OK);
    }
}