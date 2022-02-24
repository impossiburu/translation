class AbstractApi {
    protected function request($url, $params) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        if(curl_exec($ch) === false) {
            throw new Exception('Timeout');
        }

        $response = curl_exec($ch);
        curl_close($ch);
        $oResponse = json_decode($response, true);
        return $oResponse ;
    }
}

interface Response {
  public function data($text, lang, $format = 'text');
}

class TranslationYaApi extends Api {
    private $key = '';
    private $detectUrl = '';
    private $translateUrl = '';


    public function requestApi($text, $lang, $format = 'text')
    {
        $params = [
            'key'       => $this->key,
            'text'      => $text,
            'lang'      => $lang,
            'format'    => $format == 'text' ?? 'plain'
        ];

        return $this->request($this->detectUrl, json_encode($params));
    }
}

class Translation implements Response {

    private $api;

    function __construct(TranslationYaApi $api) {
        $this->api = $api;
    }

    public function data($text, lang, $format = 'text') {
        return $this->api->requestApi($text, lang);
    }
}

$translate = new Translation(new TranslationYaApi())
