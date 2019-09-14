<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Incoming\Answer;

class OrderIssueConversation extends BaseConversation
{
        
    public function pleaseProvideTheOrderNumber()
    {
        $this->ask('Please provide the order number?', function(Answer $answer) {
            
            $matches = $this->match($answer, '{order_number}');
            if (count($matches)) {
                $this->answers[] = [$answer->getText()]+$matches;
                $this->thankYouWhereDoYouHaveIssues();
                    
                return;
            }
            $this->answers[] = [$answer->getText()];
            $this->say('T*: 021 200.52.00 M*: 0722.25.00.00 Program: Luni – Duminica: 24/24');
        });
    }
        
    public function thankYouWhereDoYouHaveIssues()
    {
        $this->ask('Thank you! Where do you have issues?', function(Answer $answer) {
            
            $matches = $this->match($answer, '.*invoice.*');
            if (count($matches)) {
                $this->answers[] = [$answer->getText()]+$matches;
                $this->say($this->webhook('http://77.81.105.198/regenerate-invoice.php'));
                    
                return;
            }
            $matches = $this->match($answer, '.*AWB.*');
            if (count($matches)) {
                $this->answers[] = [$answer->getText()]+$matches;
                $this->say($this->webhook('http://77.81.105.198/regenerate-awb.php'));
                    
                return;
            }
            $matches = $this->match($answer, '.*delivery.*');
            if (count($matches)) {
                $this->answers[] = [$answer->getText()]+$matches;
                $this->say($this->webhook('http://77.81.105.198/delivery-status.php'));
                    
                return;
            }
            $matches = $this->match($answer, '.*retour.*');
            if (count($matches)) {
                $this->answers[] = [$answer->getText()]+$matches;
                $this->say('Please fill in this form. https://www.emag.ro/user/return_form');
                    
                return;
            }
            $this->answers[] = [$answer->getText()];
            $this->say('T*: 021 200.52.00 M*: 0722.25.00.00 Program: Luni – Duminica: 24/24');
        });
    }
        
        
        public function run()
        {
            $this->pleaseProvideTheOrderNumber();
        }
}
