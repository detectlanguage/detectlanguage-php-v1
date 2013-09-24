<?php
/**
 * DetectLanguage
 *
 * @author   Laurynas Butkus <info@detectlanguage.com>
 * @license  MIT
 * @version  GIT: $Id$
 * @link     http://github.com/detectlanguage/detectlanguage-php
 */

class DetectLanguageTestCase extends PHPUnit_Framework_TestCase {
  const TEST_API_KEY = '93dfb956a294140a4370a09584af2ef6';

  public function testConstructor() {
    $detectlanguage = new DetectLanguage(self::TEST_API_KEY);

    $this->assertEquals($detectlanguage->apiKey, self::TEST_API_KEY,
                        'Expect the API key to be set.');
  }

  public function testDetection() {
    $detectlanguage = new DetectLanguage(self::TEST_API_KEY);

    $result = $detectlanguage->detect('Hello world');

    $this->assertEquals('en', $result[0]->language, 
                        'To detect English language.');

    $result = $detectlanguage->detect('Jau saulelė vėl atkopdama budino svietą');

    $this->assertEquals('lt', $result[0]->language, 
                        'To detect Lithuanian language.');
  }
   
  public function testSimpleDetection() {
    $detectlanguage = new DetectLanguage(self::TEST_API_KEY);

    $result = $detectlanguage->simpleDetect('Hello world');

    $this->assertEquals('en', $result, 
                        'To detect English language.');
  } 

  public function testCurlRequest() {
    $detectlanguage = new DetectLanguage(self::TEST_API_KEY);
    $detectlanguage->requestEngine = 'curl';

    $result = $detectlanguage->simpleDetect('Hello world');

    $this->assertEquals('en', $result, 'To detect English language.');
  } 

  public function testStreamRequest() {
    $detectlanguage = new DetectLanguage(self::TEST_API_KEY);
    $detectlanguage->requestEngine = 'stream';

    $result = $detectlanguage->simpleDetect('Hello world');

    $this->assertEquals('en', $result, 'To detect English language.');
  } 

  public function testInvalidApiKey() {
    $this->setExpectedException('DetectLanguageException');

    $detectlanguage = new DetectLanguage('invalid');

    $result = $detectlanguage->simpleDetect('Hello world');
  } 

  public function testBatchDetectionWithCurl() {
    $detectlanguage = new DetectLanguage(self::TEST_API_KEY);
    $detectlanguage->requestEngine = 'curl';

    $result = $detectlanguage->detect(array('Hello world', 'Jau saulelė vėl atkopdama budino svietą'));

    $this->assertEquals('en', $result[0][0]->language, 
                        'To detect English language.');

    $this->assertEquals('lt', $result[1][0]->language, 
                        'To detect Lithuanian language.');
  }
  public function testBatchDetectionWithStream() {
    $detectlanguage = new DetectLanguage(self::TEST_API_KEY);
    $detectlanguage->requestEngine = 'stream';

    $result = $detectlanguage->detect(array('Hello world', 'Jau saulelė vėl atkopdama budino svietą'));

    $this->assertEquals('en', $result[0][0]->language, 
                        'To detect English language.');

    $this->assertEquals('lt', $result[1][0]->language, 
                        'To detect Lithuanian language.');
  }

  public function testBatchDetectionOrderWithCurl() {
    $this->__batchDetectionOrder('curl');
  }

  public function testBatchDetectionOrderWithStream() {
    $this->__batchDetectionOrder('stream');
  }

  private function __batchDetectionOrder($engine) {
    $request = array(
      'привет',
      'hello',
      'привет',
      'hello',
      'привет',
      'hello',
      'привет',
      'hello',
      'привет',
      'hello',
      'привет'
    );

    $detectlanguage = new DetectLanguage(self::TEST_API_KEY);
    $detectlanguage->requestEngine = $engine;

    $result = $detectlanguage->detect($request);

    foreach ($request as $i=>$phrase)
    {
      $language = $phrase == 'hello' ? 'en' : 'ru';
      $this->assertEquals($language, $result[$i][0]->language);
    }
  }

}
