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
  const TEST_API_KEY = '24c3185fef623b537a4df60df0a8d4d9';
  
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
}
