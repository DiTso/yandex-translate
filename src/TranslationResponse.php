<?php
namespace Beeyev\YaTranslate;

class TranslationResponse
{
    private $callResult;
    private $sourceText;
    
    function __construct($callResult, $sourceText){
        $this->callResult = $callResult;
        $this->sourceText = $sourceText;
    }
    
    public function sourceText(){
        return $this->sourceText;
    }
    
    public function translation(){
        return $this->__toString();
    }
    
    public function translationDirection(){
        return $this->callResult['lang'];
    }
    
    public function __toString()
    {
        if (isset($this->callResult['text'][1])) {
            return implode("\n", $this->callResult['text']);
        }
        return $this->callResult['text'][0];
    }
}