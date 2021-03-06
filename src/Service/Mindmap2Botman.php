<?php
namespace App\Service;
use App\Entity\Flow;

/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 9/13/2019
 * Time: 6:26 PM
 */
class Mindmap2Botman
{

    private $code = '';

    function parseTree(&$tree, $root = null) {
        $return = array();
        # Traverse the tree and search for direct children of the root
        foreach($tree as $id => &$item) {
            # A direct child is found
            if($item['parent'] == $root) {
                # Remove item from tree (we don't need to traverse this again)
                unset($tree[$id]);
                # Append the child into result array and parse its children
                $return[] = array(
                    'question' => $item['text'],
                    'responses' => $this->parseTree($tree, $item['key'])
                );
            }
        }
        return empty($return) ? [] : $return;
    }

    public function generate(Flow $flow)
    {
        $this->code = '';
        $data = json_decode($flow->getFlow(), true);
        $data['nodeDataArray'][0]['parent'] = null;
        $map = $this->parseTree($data['nodeDataArray']);
        reset($map);
        $key = key($map);
        $className = ucfirst(static::getMethodName($flow->getName())).'Conversation';
        $firstMethod = static::getMethodName($map[$key]['question']);
        $this->code .="<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Incoming\Answer;

class {$className} extends BaseConversation
{
        ";

        $this->generateCode($map[$key], $flow->getFallbackMessage());
        $this->code .="
        
        public function run()
        {
            \$this->{$firstMethod}();
        }
}
";


        file_put_contents(__DIR__."/../Conversations/$className.php", $this->code);
    }

    public static function getMethodName($question){
        return lcfirst(preg_replace("/[^a-zA-Z0-9]+/", "", ucwords($question)));
    }

    public function generateCode($aQuery, $fallbackMessage){

        $question = $aQuery['question'];
        $methodName = static::getMethodName($question);

        $this->code .= "
    public function $methodName()
    {
        \$this->ask('$question', function(Answer \$answer) {
            ";
        foreach ($aQuery['responses'] as $response) {
            $this->code .="
            \$matches = \$this->match(\$answer, '{$response['question']}');
            if (count(\$matches)) {
                \$this->answers[] = [\$answer->getText()]+\$matches;";
            if (!isset($response['responses'][0])){
                $this->code .="
                \$this->say('$fallbackMessage');
                ";
            } else {
                if (count($response['responses'][0]['responses'])){
                    $method = static::getMethodName($response['responses'][0]['question']);
                    $this->code .="
                \$this->{$method}();
                    ";
                } else {
                    preg_match_all('/\|\|([a-z]*):(.*)/',$response['responses'][0]['question'], $matches);
                    var_dump($matches);
                    if (count($matches[1])){

                        $this->code .= "
                \$this->say(\$this->{$matches[1][0]}('{$matches[2][0]}'));
                    ";
                    } else {
                        $this->code .= "
                \$this->say('{$response['responses'][0]['question']}');
                    ";
                    }
                }
            }
            $this->code .="
                return;
            }";
            }
        $this->code .="
            \$this->answers[] = [\$answer->getText()];
            \$this->say('$fallbackMessage');
        });
    }
        ";

        foreach ($aQuery['responses'] as $response) {
            if (isset($response['responses'][0]) && count($response['responses'][0]['responses'])) {
                $this->generateCode($response['responses'][0], $fallbackMessage);
            }
        }

    }

    public function remove(Flow $flow)
    {
        $className = ucfirst(static::getMethodName($flow->getName())).'Conversation';
        unlink(__DIR__."/../Conversations/$className.php", $this->code);
    }
}