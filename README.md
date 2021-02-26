# PHP Google Translate (Free)
A simple PHP library to translate texts using Google Translate for free. There is also a [paid version](https://github.com/nggit/php-google-translate) of Google Translate (requires an API key).
## Install
```
composer require nggit/php-gtranslate-free:dev-master
```
## Usage
```php
require __DIR__ . '/vendor/autoload.php';
use Nggit\Google\Translate;

$translate = new Translate(array('lang' => array('de' => 'en'))); // translate from german to english
$translate->setText('Der schnelle Braune Fuchs springt über den faulen Hund');

echo $translate->process()->getResults();
```
Enjoy!
