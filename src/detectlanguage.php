<?php
/**
 * DetectLanguage
 * 
 * detectlanguage.com API client
 *
 * @author   Laurynas Butkus <info@detectlanguage.com>
 * @license  MIT
 * @version  GIT: $Id$
 * @link     http://github.com/detectlanguage/detectlanguage-php
 */

class DetectLanguageException extends Exception
{
}

if (!function_exists('json_decode')) {
  throw new DetectLanguageException('DetectLanguage needs the JSON PHP extension.');
}

class DetectLanguage
{
  /**
   * API Client Version.
   */
  const VERSION = '1.0.1';

  /**
   * The API key.
   *
   * @var string
   */
  public $apiKey;


  /**
   * Request engine.
   *
   * @var string Request engine (curl or stream).
   */
  public $requestEngine;

  /**
   * API host.
   *
   * @var string
   */
  public $host = 'ws.detectlanguage.com';

  /**
   * API version.
   *
   * @var string
   */
  public $apiVersion = '0.2';

  /**
   * Request timeout.
   *
   * @var int
   */
  public $requestTimeout = 60;

  /**
   * Connect timeout.
   *
   * @var int
   */
  public $connectTimeout = 10;

  /**
   * User agent.
   *
   * @var string
   */
  public $userAgent = 'detectlanguage-php-1.0.1';

  
  /**
   * Initialize a Detect Language API Client.
   *
   * The configuration:
   * - appId: the API key
   *
   * @param string $apiKey The API key
   */
  public function __construct($apiKey) {
    $this->apiKey = $apiKey;

    if (function_exists('curl_init')) 
      $this->requestEngine = 'curl';
    else
      $this->requestEngine = 'stream';
  }

  /**
   * Detect text language.
   *
   * @param string @text The text for language detection
   * @return array detected languages information
   */
  public function detect($text) {
    $result = $this->request('detect', array('q' => $text));

    return $result->data->detections;
  }

  /**
   * Simple detection. If you need just the language code.
   *
   * @param string @text The text for language detection
   * @return string detected language code
   */
  public function simpleDetect($text) {
    $detections = $this->detect($text);

    if (count($detections) > 0)
      return $detections[0]->language;
    else
      return null;
  }

  /**
   * Perform a request
   *
   * @param string $method Method name
   * @param array $params The parameters to use for the POST body
   *
   * @return array
   */
  protected function request($method, $params) {
    $url = $this->getUrl($method);

    $params['key'] = $this->apiKey;

    $request_method = $this->getReuquestMethodName();

    $response_body = $this->$request_method($url, $params);

    $response = json_decode($response_body);

    if (isset($response->error))
      throw new DetectLanguageException($response->error->message);

    return $response;
  }

  /**
   * Get request method name.
   *
   * @return string
   */
  protected function getReuquestMethodName() {
    switch ($this->requestEngine) {
      case 'curl':
        return 'requestCurl';
      
      case 'stream':
        return 'requestStream';

      default:
        throw new DetectLanguageException("Invalid request engine: " . $this->requestEngine);
    }
  }


  /**
   * Perform request using native PHP streams
   *
   * @param string $url Request URL
   * @param array $params The parameters to use for the POST body
   *
   * @return string Response body
   */
  protected function requestStream($url, $params) {
    $postdata = http_build_query($params);

    $opts = array('http' =>
      array(
        'method'            => 'POST',
        'header'            => implode("\n", $this->getHeaders()),
        'content'           => $postdata,
        'timeout'           => $this->requestTimeout
      )
    );

    $context = stream_context_create($opts);

    return file_get_contents($url, false, $context);
  }


  /**
   * Perform request using CURL extension.
   *
   * @param string $url Request URL
   * @param array $params The parameters to use for the POST body
   *
   * @return string Response body
   */
  protected function requestCurl($url, $params) {
    $ch = curl_init();

    $options = array(
      CURLOPT_URL             => $url,
      CURLOPT_HTTPHEADER      => $this->getHeaders(),
      CURLOPT_POSTFIELDS      => http_build_query($params),
      CURLOPT_CONNECTTIMEOUT  => $this->connectTimeout,
      CURLOPT_TIMEOUT         => $this->requestTimeout,
      CURLOPT_USERAGENT       => $this->userAgent,
      CURLOPT_RETURNTRANSFER  => true
      );

    curl_setopt_array($ch, $options);

    $result = curl_exec($ch);

    if ($result === false) {
      $e = new DetectLanguageException(curl_error($ch));
      curl_close($ch);
      throw $e;
    }

    curl_close($ch);

    return $result;
  }

  /**
   * Build URL for given method
   *
   * @param string $method Method name
   * @return string
   */
  protected function getUrl($method) {
    return 'http://' . $this->host . '/' . $this->apiVersion . '/' . $method;
  }

  /**
   * Build request headers.
   *
   * @return string
   */
  protected function getHeaders() {
    return array(
      "Content-Type: application/x-www-form-urlencoded",
      "Accept-Encoding: gzip, deflate",
      "User-Agent: " . $this->userAgent
    );
  }

  /**
   * Prints to the error log if you aren't in command line mode.
   *
   * @param string $msg Log message
   */
  protected static function errorLog($msg) {
    // disable error log if we are running in a CLI environment
    // @codeCoverageIgnoreStart
    if (php_sapi_name() != 'cli') {
      error_log($msg);
    }
    // uncomment this if you want to see the errors on the page
    // print 'error_log: '.$msg."\n";
    // @codeCoverageIgnoreEnd
  }
}
