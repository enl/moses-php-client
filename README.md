moses-php-client
================

This package is a simplest possible client for Moses xmlrpc server. Moses is a machine translation system. 
You can read more about it on [its official website](http://statmt.org/moses).

Installation
----------------

The best way to install the package is to install it via [composer](http://getcomposer.org). If you're still not using this awesome manager it's right time to start!


    composer require enl/moses-php-client


Usage
-----------------

$client = new Enl\MosesClient\Client('http://your-moses-server.ltd:8080/RPC2');

$translatedText = $client->translate('Text to translate goes here');

Function `Client::translate()` actually has four parameters:

1. $text. Text to translate
2. $align. false by default
3. $reportAllFactors. false by default. These two parameters are for Moses server too.
4. $returnOnlyText. true by default. If set to true this function will return only the translated text and throw away rest parts of response.




