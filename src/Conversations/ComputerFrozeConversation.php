<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Incoming\Answer;

class ComputerFrozeConversation extends BaseConversation
{
        
    public function howCanIHelpYou()
    {
        $this->ask('How can I help you?', function(Answer $answer) {
            
            $matches = $this->match($answer, '.*comp|laptop.*froze.*');
            if (count($matches)) {
                $this->answers[] = [$answer->getText()]+$matches;
                $this->haveYouTriedTurningItOffAndOn();
                    
                return;
            }
            $matches = $this->match($answer, '.*internet.*not.*work.*');
            if (count($matches)) {
                $this->answers[] = [$answer->getText()]+$matches;
                $this->say('This is an internet bot. Your internet is fine!');
                    
                return;
            }
            $this->answers[] = [$answer->getText()];
            $this->say('I have no idea!!!');
        });
    }
        
    public function haveYouTriedTurningItOffAndOn()
    {
        $this->ask('Have you tried turning it off and on?', function(Answer $answer) {
            
            $matches = $this->match($answer, 'Yes');
            if (count($matches)) {
                $this->answers[] = [$answer->getText()]+$matches;
                $this->say('Please bring your computer to @daniel at room 42 floor 4');
                    
                return;
            }
            $matches = $this->match($answer, 'No');
            if (count($matches)) {
                $this->answers[] = [$answer->getText()]+$matches;
                $this->say('I have no idea!!!');
                
                return;
            }
            $this->answers[] = [$answer->getText()];
            $this->say('I have no idea!!!');
        });
    }
        
        
        public function run()
        {
            $this->howCanIHelpYou();
        }
}
