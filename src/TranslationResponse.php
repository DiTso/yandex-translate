<?php
namespace Beeyev\YaTranslate;

class TranslationResponse
{
    private $_callResult;
    private $_sourceText;
    
    function __construct($callResult, $sourceText)
    {
        $this->_callResult = $callResult;
        $this->_sourceText = $sourceText;
    }
    
    public function sourceText()
    {
        return $this->_sourceText;
    }
    
    public function translation()
    {
        return $this->__toString();
    }
    
    public function translationDirection()
    {
        return $this->_callResult['lang'];
    }
    
    public function __toString()
    {
        if (isset($this->_callResult['text'][1])) {
            return implode("\n", $this->_callResult['text']);
        }
        return $this->_callResult['text'][0];
    }
}