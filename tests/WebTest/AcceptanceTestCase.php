<?php
namespace WebTest;

class AcceptanceTestCase extends \PHPUnit_Framework_TestCase
{
    protected $session;

    public function setUp()
    {
        $browser = $this->getVar('DESINVENTAR_BROWSER', 'chrome');
        $this->url = $this->getVar('DESINVENTAR_URL', '');
        $this->session = new \Behat\Mink\Session($this->getDriver($browser));
        $this->session->start();
    }

    public function getDriver($browser)
    {
        if ($browser == 'chromedriver') {
            $wdHost = $this->getVar('DESINVENTAR_WDHOST', 'http://127.0.0.1:9222');
            return new \DMore\ChromeDriver\ChromeDriver(
                $wdHost,
                new \DMore\ChromeDriver\HttpClient(),
                ''
            );
        }
        $wdHost = $this->getVar('DESINVENTAR_WDHOST', 'http://127.0.0.1:4444/wd/hub');
        return new \Behat\Mink\Driver\Selenium2Driver($browser, null, $wdHost);
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

    protected function waitForAjax($time = 10000)
    {
        $this->session->wait(
            $time,
            '('
            . '(typeof(jQuery)=="undefined" || (0 === jQuery.active && 0 === jQuery(\':animated\').length))'
            . '|| (document.readyState === "complete")'
            . ')'
        );
    }
}
