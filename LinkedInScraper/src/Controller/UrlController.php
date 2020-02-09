<?php

namespace App\Controller;

use App\LinkedinProfile;
use App\Skill;
use Facebook\WebDriver\Exception\UnknownServerException;
use Illuminate\Http\Request;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Support\Facades\DB;


class UrlController extends AppController
{
    public function get()
    {
        $url = $_GET['url'];
        $host = "localhost:4444";
        $driver = RemoteWebDriver::create($host, DesiredCapabilities::chrome());
        $driver->get('https://www.linkedin.com');

        echo $url;
    }
}
