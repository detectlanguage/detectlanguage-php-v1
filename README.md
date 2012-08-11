Detect Language API PHP Client [![Build Status](https://secure.travis-ci.org/detectlanguage/detectlanguage-php.png)](http://travis-ci.org/detectlanguage/detectlanguage-php)
========

Detects language of given text. Returns detected language codes and scores.

Before using Detect Language API client you setup your personal API key.
You can get it by signing up at http://detectlanguage.com

## Usage

### Load client 

    require_once 'detectlanguage.php';

    $detectlanguage = new DetectLanguage("YOUR API KEY");

### Language detection

    $results = $detectlanguage->detect("Buenos dias señor");

#### Results

    Array
    (
        [0] => stdClass Object
            (
                [language] => es
                [isReliable] => 
                [confidence] => 0.32710280373832
            )

        [1] => stdClass Object
            (
                [language] => pt
                [isReliable] => 
                [confidence] => 0.083565459610028
            )

    )

### Simple language detection

If you need just a language code you can use `simpleDetect`. It returns just the language code.

    $languageCode = $detectlanguage->simpleDetect("Buenos dias señor");

#### Result

    "es"

## License

Detect Language API Client is free software, and may be redistributed under the terms specified in the MIT-LICENSE file.
