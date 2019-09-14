<?php

namespace App\Controller;

use App\Entity\Flow;
use App\Service\Mindmap2Botman;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Cache\SymfonyCache;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Middleware\ApiAi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Conversations;

class DefaultController extends AbstractController
{
    private $intentCategories = [
        "hr",
        "finance",
        "apps",
        "teams",
        "facility"
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

        /** @var Flow[] $flows */
        $flows = $this->getDoctrine()->getRepository(Flow::class)->findAll();

        foreach ($flows as $flow){
            $botman->hears($flow->getTriggerWords(), function($bot) use ($flow) {
                $className = 'App\Conversations\\'.ucfirst(Mindmap2Botman::getMethodName($flow->getName())).'Conversation';
                $bot->startConversation(new $className());
            });
        }

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


        $botman->listen();
        
        return new Response('', Response::HTTP_OK);
    }
}