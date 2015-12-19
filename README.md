moses-php-client
================

This package is a simplest possible client for Moses xmlrpc server. Moses is a machine translation system. 
You can read more about it on [its official website](http://statmt.org/moses).

Installation
----------------

The best way to install the package is to install it via [composer](http://getcomposer.org). If you're still not using this awesome manager it's right time to start!


    composer require enl/moses-php-client

Or you can add the following string to your `composer.json` by hand:

    {
        "require": {
            "enl/moses-php-client": "~1.0"
        }
    }

And run `composer update`.

Usage
-----------------

First of all, you need to instantiate the Client:

    use Enl\MosesClient\Client;
    use Enl\MosesClient\Transport;

    $transport = new Transport('http://your-moses-server.ltd:8080/RPC2');
    $client = new Client($transport);

Or just use `Client::factory` method:

    use Enl\MosesClient\Client;

    $client = Client::factory('http://your-moses-server.ltd:8080/RPC2');


### How to translate a text ###

Text translation is simple as this line of code:

    $translation = $client->translate('Text to translate goes here.');

### Alignment option ###

Actually, `Client::translate` function takes two parameters. The second one is optional `align` `boolean` parameter:

    $translation = $client->translate('Text to translate goes here.', $align);

What this parameter mean? The [official doc for Moses](http://www.statmt.org/moses/?n=Advanced.Moses) says the following:

> To access the Moses server, an XMLRPC request should be sent to http://host:port/RPC2 where the parameter is a map containing the keys `text` and (optionally) `align`. The value of the first of these parameters is the text to be translated and the second, if present, causes alignment information to be returned to the client. The client will receive a map containing the same two keys, where the value associated with the `text` key is the translated text, and the `align` key (if present) maps to a list of maps. The alignment gives the segmentation in target order, with each list element specifying the target start position (`tgt-start`), source start position (`src-start`) and source end position (`src-end`).

So, if you set `align` parameter as a `true`, you will get the array with `text` and `align` keys as a response and just text otherwise.




