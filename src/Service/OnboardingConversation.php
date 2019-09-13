<?php

namespace App\Service;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Conversations\Conversation;

class OnboardingConversation extends Conversation
{
    protected $firstname;

    public function askFirstname()
    {
        $this->ask('Hi, how can i help you?', function($answer) {
            if($answer->getText() === 'How do I change my name?'){
                $this->say('You access MyStaff portal, go to Create Request and then to Personnel - Change General Information. You can access MyStaff portal from InsideMAG - My resources - My holidays & payslips.');
            }
        });
    }

    public function run()
    {
        $this->askFirstname();
    }
}
