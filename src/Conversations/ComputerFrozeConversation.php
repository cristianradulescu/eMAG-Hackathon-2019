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
            $matches = $this->match($answer, '.*VPN.*not.*work.*');
            if (count($matches)) {
                $this->answers[] = [$answer->getText()]+$matches;
                $this->say('The login procedure has cahnged. Please read https://www.emag.ro/guide/corporate?ref=footer_2_4#testimoniale');
                    
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
                $this->say($this->webkoock('http://77.81.105.198/add-ticket.php'));
                    
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
