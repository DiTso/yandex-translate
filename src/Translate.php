<?php
/**
 * MyClass Class Doc Comment
 *
 * @package MyPackage
 * @author  Aleksandr Tebiev <tebiev@mail.com>
 * @link    http://www.hashbangcode.com/
 */
namespace Beeyev\YaTranslate;

/**
 * MyClass Class Doc Comment
 */
class Translate
{
    
    /**
     * The API base URL.
     */
    const   API_URL = 'https://translate.yandex.net/api/v1.5/tr.json/';
    
    private $_apiKey;
    
    function __construct($apiKey = null)
    {
        if ($apiKey) {
            $this->setApiKey($apiKey);    
        }
    }
    
    public function setApiKey($apiKey)
    {
        if ($apiKey) {
            $this->_apiKey = $apiKey;    
        } else {
            throw new TranslateException('Error: setApiKey() - Api key is required');
        }
        
        return $this;
    }
    
    public function getApiKey()
    {
        return $this->_apiKey;
    }
    
    public function getPossibleTranslations($detailsLang = null)
    {
        return $this->makeCall('getLangs', array(
                'ui' => $detailsLang
            ));
    }
    
    public function detectLanguage($text, $hint = null)
    {
        if (is_array($hint)) {
            $hint = implode(',', $hint);
        }
        
        $callResult = $this->makeCall('detect', array(
            'text' => $text,
            'hint' => $hint
        ));
        
        return $callResult['lang'];
    }
    
    public function translate($text, $language, $format = null, $options = null)
    {
        
        /*
            html code autodetection
            i will appreciate if smb tell me a beetter way
            to detect html code in a string
        */
        if ($format == 'auto') {
            if (is_array($text)) {
                $textD = implode('', $text);
            } else {
                $textD = $text;
            }
            $format = $textD == strip_tags($textD) ? 'plain' : 'html';
        } elseif ($format == null) {
            $format = 'plain';
        }
        
        $callResult = $this->makeCall('translate', array(
            'text'      => $text,
            'lang'      => $language,
            'format'    => $format,
        ));
        return new TranslationResponse($callResult, $text);
    }
    
    
    protected function makeCall($uri, array $requestParameters)
    {
        if ($this->getApiKey()) {
            $requestParameters['key'] = $this->getApiKey();
        } else {
            throw new TranslateException('Error: makeCall() - API key is not set');
        }
        
        $text = '';
        if (isset($requestParameters['text']) && is_array($requestParameters['text'])) {
              $text = '&text=' . implode('&text=', $requestParameters['text']);
              unset($requestParameters['text']);
        }
        
        $requestParameters = http_build_query($requestParameters) . $text;

        $curlOptions = array(
                CURLOPT_URL             => self::API_URL . $uri,
                CURLOPT_POSTFIELDS      => $requestParameters,
                CURLOPT_RETURNTRANSFER  => true,
                CURLOPT_CONNECTTIMEOUT  => 20,
                CURLOPT_TIMEOUT         => 60,
                CURLOPT_SSL_VERIFYPEER  => false,
                CURLOPT_CUSTOMREQUEST   => 'POST',
            );
            
        $ch = curl_init();
        curl_setopt_array($ch, $curlOptions);
        $callResult = curl_exec($ch);
        
        if (!$callResult) {
            throw new TranslateException('Error: makeCall() - cURL error: ' . curl_error($ch));
        }
        curl_close($ch);
        
        $callResult = json_decode($callResult, true);
        
        if (isset($callResult['code']) && $callResult['code'] > 200) {
            throw new TranslateException('API error: ' .$callResult['message'], $callResult['code']);
        }
        
        return $callResult;
    }
}
