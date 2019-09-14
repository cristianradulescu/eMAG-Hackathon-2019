<?php
namespace App\Conversations;
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 9/14/2019
 * Time: 12:46 AM
 */

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;

abstract class BaseConversation extends Conversation
{
    /**
     * regular expression to capture named parameters but not quantifiers
     * captures {name}, but not {1}, {1,}, or {1,2}.
     */
    const PARAM_NAME_REGEX = '/\{((?:(?!\d+,?\d+?)\w)+?)\}/';

    protected $answers = [];

    public function match(Answer $answer, $pattern)
    {
        $pattern = str_replace('/', '\/', $pattern);
        $text = '/^'.preg_replace(self::PARAM_NAME_REGEX, '(?<$1>.*)', $pattern).' ?$/miu';
        $matches = [];
        preg_match($text, $answer->getText(), $matches);

        return array_unique($matches);
    }

    public function webhook($url){
        return file_get_contents($url.'?'.http_build_query($this->answers));
    }
}