<?php

# PHP Google Translate (Free).
# https://github.com/nggit/php-gtranslate-free
# Copyright (c) 2021 nggit.

namespace Nggit\Google;
use Nggit\PHPSimpleClient\Client;

class Translate
{
    protected $text;
    protected $sl;
    protected $tl;
    protected $client;
    protected $halted = false;

    public function __construct($options = array('lang' => array('auto' => 'en')), $text = '')
    {
        $this->text = $text;
        foreach ($options['lang'] as $source => $target) {
            $this->sl = $source;
            $this->tl = $target;
        }
    }

    public function process()
    {
        if ($this->halted || strlen($this->text) < 2 || strlen($this->text) > 5000) {
            return $this;
        }
        $headers = array(
            'User-Agent: Mozilla/5.0 (X11; Linux x86_64; rv:78.0) Gecko/20100101 Firefox/78.0',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language: en-US,en;q=0.5',
            'Referer: https://translate.google.com/'
        );
        $this->client = Client::create();
        $this->client->setHeaders($headers);
        $this->client->setUrl('https://translate.google.com/_/TranslateWebserverUi/data/batchexecute');
        $data = sprintf('[[["MkEWBc","[[%s,\"%s\",\"%s\",true],[null]]",null,"generic"]]]',
                         addslashes(json_encode($this->text)), $this->sl, $this->tl);
        $this->client->request('POST', 'f.req=' . rawurlencode($data));
        $this->client->send();
        return $this->parse();
    }

    protected function parse()
    {
        $data = json_decode(substr($this->client->getBody(), strpos($this->client->getBody(), '['), strrpos($this->client->getBody(), ']')), true);
        if (isset($data[0][2])) {
            $items = json_decode($data[0][2], true);
            if (isset($items[1][0][0][5]) && is_array($items[1][0][0][5])) {
                $results = '';
                foreach ($items[1][0][0][5] as $item) {
                    $results .=  $item[0];
                }
                if ($results != '') {
                    $this->text = $results;
                }
            }
        }
        return $this;
    }

    public function setText($text = '')
    {
        if ($text == $this->text) {
            $this->halted = true;
        } else {
            $this->halted = false;
            $this->text   = $text;
        }
        return $this;
    }

    public function setSource($source = 'auto')
    {
        if ($source != $this->sl) {
            $this->halted = false;
            $this->sl     = $source;
        }
        return $this;
    }

    public function setTarget($target = 'en')
    {
        if ($target != $this->tl) {
            $this->halted = false;
            $this->tl     = $target;
        }
        return $this;
    }

    public function getResults()
    {
        return $this->text;
    }
}
