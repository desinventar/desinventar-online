<?php
namespace WebTest;

class AcceptanceTestCase extends \PHPUnit_Framework_TestCase
{
    protected $session;

    public function setUp()
    {
        $browser = $this->getVar('DESINVENTAR_BROWSER', 'chrome');
        $this->url = $this->getVar('DESINVENTAR_URL', '');
        if ($browser == 'chromedriver') {
            $wdHost = $this->getVar('DESINVENTAR_WDHOST', 'http://127.0.0.1:9222');
            $driver = new \DMore\ChromeDriver\ChromeDriver(
                $wdHost,
                new \DMore\ChromeDriver\HttpClient(),
                ''
            );
        } else {
            $wdHost = $this->getVar('DESINVENTAR_WDHOST', 'http://127.0.0.1:4444/wd/hub');
            $driver = new \Behat\Mink\Driver\Selenium2Driver($browser, null, $wdHost);
        }
        $this->session = new \Behat\Mink\Session($driver);
        $this->session->start();
    }

    public function tearDown()
    {
        $this->session->stop();
    }

    protected function getVar($key, $defaultValue = '')
    {
        $value = getenv($key);
        if (!empty($value)) {
            return $value;
        }
        if (!empty($GLOBALS[$key])) {
            return $GLOBALS[$key];
        }
        return $defaultValue;
    }
}
