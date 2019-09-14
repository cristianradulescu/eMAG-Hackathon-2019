<?php

namespace App\Controller;

use App\Entity\Flow;
use App\Service\Mindmap2Botman;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Cache\SymfonyCache;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Middleware\ApiAi;
use BotMan\Drivers\Web\WebDriver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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

        DriverManager::loadDriver(WebDriver::class);

        $adapter = new FilesystemAdapter();

        $botman = BotManFactory::create($config, new SymfonyCache($adapter));

        $botman->hears('hello|hey|hi|welcome|salut|let\'s start', function(Botman $bot)  {
            $bot->reply(
                'Hello, how can i help you?'
            );
        });

        /** @var Flow[] $flows */
        $flows = $this->getDoctrine()->getRepository(Flow::class)->findAll();

        foreach ($flows as $flow){
            $botman->hears($flow->getTriggerWords(), function($bot) use ($flow) {
                $className = 'App\Conversations\\'.ucfirst(Mindmap2Botman::getMethodName($flow->getName())).'Conversation';
                $bot->startConversation(new $className());
            });
        }

        $botman->hears('worklog', function($bot)  {
            $bot->reply(
                'Please find your suggested worklog <a href="'.$this->generateUrl('app_worklog_suggestworklog').'" target="_blank">here</a>'
            );
        });

        $dialogflow = ApiAi::create($_ENV['DIALOGFLOW_TOKEN'])->listenForAction();

        $botman->middleware->received($dialogflow);

        foreach ($this->intentCategories as $intentCategoryName){
            $botman->hears($intentCategoryName, function (BotMan $bot) {
                $extras = $bot->getMessage()->getExtras();
                $apiReply = $extras['apiReply'];

                $bot->reply($apiReply);
            })->middleware($dialogflow);
        }

        $botman->hears('I need a certificate of employee|certificate of employee|Can you help with a certificate of employee', function(Botman $botman){
            $attachment = new Image('http://www.darcomshop.ro/images/detailed/0/adeverinta_salariat.jpg', [
                'custom_payload' => true,
            ]);

            $message = OutgoingMessage::create('Here is a template of certificate')
                ->withAttachment($attachment);

            $botman->reply($message);
        });

        $botman->listen();

        return new Response('', Response::HTTP_OK);
    }
}