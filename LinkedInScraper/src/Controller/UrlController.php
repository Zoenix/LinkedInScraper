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
use Cake\Datasource\ConnectionManager;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\WebDriverException;
use Facebook\WebDriver\WebDriver;

class UrlController extends AppController
{
    protected $email;
    protected $password;


    public function get()
    {


        // Set email here
        $this->email = 'email';
        // Set Password here
        $this->password = 'password';
        $url = $_GET['url'];

        $host = "localhost:4444";
        $driver = RemoteWebDriver::create($host, DesiredCapabilities::chrome());

        sleep(1);

        $this->login($driver);
        $driver->get($url);
        $name =  $driver->findElement(WebDriverBy::className('t-24'))->getText();

        $experiencesArray = $this->getData($driver, $url);

        $this->insertSQL($experiencesArray, $name);

        $this->display($experiencesArray, $name);

        $driver->close();
    }
    public function display($experiencesArray, $name)
    {
        $contentView = $name;

        for ($i = 0; $i < count($experiencesArray); $i++) {
            $contentView .= '<div class="content">
                Title: ' . $experiencesArray[$i]["title"] . '<br>' .
                'Job: ' . $experiencesArray[$i]["job"] . '<br>' .
                'Time: ' . $experiencesArray[$i]["time"] . '<br>' .
                'Description: ' . $experiencesArray[$i]["description"] . '<br>' .
                '</div>';
        }
        $this->set('contentView', $contentView);
    }
    public function insertSQL($experiencesArray, $name)
    {
        $dsn = 'mysql://root:@localhost/linkedin';
        ConnectionManager::setConfig('linkedin', ['url' => $dsn]);
        $connection = ConnectionManager::get('linkedin');
        $experiencesJSON = json_encode($experiencesArray);
        $result = $connection->execute('SELECT * FROM experience WHERE name="' . $name . '"');
        if (count($result) == 0) {
            $connection->insert('experience', [
                'name' => $name,
                'experience' => $experiencesJSON
            ]);
        } else {
            $connection->update('experience', ['Name' => $name], ['Experience' => $experiencesJSON]);
        }
    }
    public function login($driver)
    {
        $driver->get('https://www.linkedin.com/login');
        $email  = $driver->findElement(WebDriverBy::id('username'))->click();
        sleep(0.5);
        $driver->getKeyboard()->sendKeys($this->email);
        $password  = $driver->findElement(WebDriverBy::id('password'))->click();
        sleep(0.5);
        $driver->getKeyboard()->sendKeys($this->password);
        $password->submit();
        sleep(1);
    }
    public function getData($driver, $url)
    {
        $experienceElement = $driver->findElement(WebDriverBy::xpath('//*[@id="experience-section"]'));
        try {
            $experienceElement->findElement(WebDriverBy::className('pv-profile-section__see-more-inline'))->click();
            sleep(0.5);
            $experienceElement->findElement(WebDriverBy::className('pv-profile-section__see-more-inline'))->click();
        } catch (NoSuchElementException  $th) {
            //throw $th;
        }

        $experienceElementArray = $experienceElement->findElements(WebDriverBy::tagName('li'));



        $experiencesArray = array();
        for ($i = 0; $i < count($experienceElementArray); $i++) {
            $title = "";
            $job = "";
            $time = "";
            $description = "";

            try {
                $title = $experienceElementArray[$i]->findElement(WebDriverBy::tagName('h3'))->getText();
                $job = $experienceElementArray[$i]->findElement(WebDriverBy::className('pv-entity__secondary-title'))->getText();
                $time = $experienceElementArray[$i]->findElement(WebDriverBy::className('pv-entity__date-range'))->findElements(WebDriverBy::tagName('span'))[1]->getText();
                $description = $experienceElementArray[$i]->findElement(WebDriverBy::className('pv-entity__extra-details'))->findElement(WebDriverBy::tagName('p'))->getText();
            } catch (NoSuchElementException $th) {
                //throw $th;
            }


            $experiencesArray[$i] = array(
                "title" => $title,
                "job" => $job,
                "time" => $time,
                "description" => $description
            );
        }
        return $experiencesArray;
    }
}
