<?php
 /**
  * https://jpegify.com
  */
class Jpegify {
    protected $auth = array();
    private $apiUrl = 'https://jpegify.com/api/v1/';
    private $sandboxStatus = null;
    private $timeout;
    private $proxyParams;
    private $localFileName;
    private $optimizedData;
    private $optimizeParams;

    public function __construct($key = '', $secret = '', $timeout = 30, $proxyParams = array()) {
        $this->auth = array(
            "auth" => array(
                "api_key" => $key,
                "api_secret" => $secret
            )
        );
        $this->timeout = $timeout;
        $this->proxyParams = $proxyParams;
    }

    public function enableSandbox( $status = true )
    {
        $this->sandboxStatus = $status;
    }

    public function fromUrl($imageUrl, $callbackUrl = null, $with = null, $height = null, $strategy = null ) 
    { 
        self::buildOptimizingParams($imageUrl, null, null, $callbackUrl, $with, $height, $strategy, $this->sandboxStatus );
        return $this;
    }

    public function fromFile($file, $callbackUrl = null, $with = null, $height = null, $strategy = null ) 
    { 
        self::buildOptimizingParams(null, $file, null, $callbackUrl, $with, $height, $strategy, $this->sandboxStatus);
        return $this;
    }

    public function fromBuffer($binaryImageData, $callbackUrl = null, $with = null, $height = null, $strategy = null ) 
    { 
        self::buildOptimizingParams(null, null, $binaryImageData, $callbackUrl, $with, $height, $strategy, $this->sandboxStatus);
        return $this;
    }

    public function toFile($filename)
    {
        if($this->optimizedData && isset($this->optimizedData['download_url']) && $this->optimizedData['download_url']){
            $this->fileDownload( $this->optimizedData['download_url'], $filename);
            return $this->optimizedData;
        }

        if(isset($this->optimizeParams['file'])){
            $this->optimizedData = $this->fileUploader();
            if(isset($this->optimizedData['download_url']) && $this->optimizedData['download_url'] )
                $this->fileDownload( $this->optimizedData['download_url'], $filename);
            return $this->optimizedData;
        }

        if(isset($this->optimizeParams['url'])){
            $data = json_encode(array_merge($this->auth, $this->optimizeParams));
            $this->optimizedData = $this->callApi($data, $this->apiUrl . 'fromurl', 'url');
            if(isset($this->optimizedData['download_url']) && $this->optimizedData['download_url'] )
                $this->fileDownload( $this->optimizedData['download_url'], $filename);
            return $this->optimizedData;                  
        }

        if(isset($this->optimizeParams['base64'])){
            $data = json_encode(array_merge($this->auth, $this->optimizeParams));
            $this->optimizedData = $this->callApi($data, $this->apiUrl . 'frombuffer', 'url');
            if(isset($this->optimizedData['download_url']) && $this->optimizedData['download_url'] )
                $this->fileDownload( $this->optimizedData['download_url'], $filename);
            return $this->optimizedData;                  
        }

        return [
                "success" => false,
                "error" => 'Parameter error'
            ];
    }

    public function status() 
    {
        $data = array('auth' => array(
            'api_key' => $this->auth['auth']['api_key'],
            'api_secret' => $this->auth['auth']['api_secret']
        ));

        $response = $this->callApi(json_encode($data), $this->apiUrl . 'user_status', 'url');

        return $response;
    }    

    private function fileUploader()
    {
        if (!file_exists($this->optimizeParams['file'])) {
            return array(
                "success" => false,
                "error" => 'File `' . $this->optimizeParams['file'] . '` does not exist'
            );
        }

        if (class_exists('CURLFile')) {
            $file = new CURLFile($this->optimizeParams['file']);
        } else {
            $file = '@' . $this->optimizeParams['file'];
        }

        $data = array_merge(array(
            "file" => $file,
            "data" => json_encode(array_merge($this->auth, $this->optimizeParams))
        ));

        unset($this->optimizeParams['file']);

        return $this->callApi($data, $this->apiUrl . 'fromfile', 'file');        
    }

    private function callApi($data, $url, $type) 
    {
        $ch = curl_init();

        if ($type === 'url' || $type === 'base64') {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json'
            ));
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.96 Safari/537.36');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . "/cacert.pem");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);        

        if (isset($this->proxyParams['proxy'])) {
            curl_setopt($ch, CURLOPT_PROXY, $this->proxyParams['proxy']);
        }
        $data = curl_exec($ch);
        $response = json_decode($data, true);

        if ($response === null || !$response || !is_array($response)) {
            $response = array (
                "success" => false,
                "error" => 'cURL Error: ' . curl_error($ch)
            );
        }
        curl_close($ch);

        return $response;
    }

    private function buildOptimizingParams($imageUrl = null, $imageFifile = null, $binaryImageData = null, $callbackUrl = null, $with = null, $height = null, $strategy = null, $sandbox = false)
    {
        $this->optimizeParams = [];

        if($imageUrl){
            $this->optimizeParams['url'] = $imageUrl;  
        }
        if($imageFifile){
            $this->optimizeParams['file'] = $imageFifile;  
        }
        if($binaryImageData){
            $this->optimizeParams['base64'] = base64_encode($binaryImageData);
        }                
        if($callbackUrl){
            $this->optimizeParams['callback_url'] = $callbackUrl;
        }
        if(is_numeric($with)){
            $this->optimizeParams['resize']['width'] = $with;
        }        
        if(is_numeric($height)){
            $this->optimizeParams['resize']['height'] = $height;
        }
        if($strategy){
            $this->optimizeParams['resize']['strategy'] = $strategy;
        }
        if($sandbox){
            $this->optimizeParams['sandbox']= true;
        }
    }

    private function fileDownload( $url, $targetFile, $connecttimeout = 3, $timeout = 300 )
    {    
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_HEADER, false );
        curl_setopt($ch, CURLOPT_VERBOSE, false );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connecttimeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout );
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.96 Safari/537.36');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        $data = curl_exec($ch);
        curl_close($ch);

       if( $data ) @file_put_contents( $targetFile, $data );

       return $data;
    }
}
