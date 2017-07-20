<?php
namespace WebTest\General;

use \WebTest\AcceptanceTestCase;

class UserLoginTest extends AcceptanceTestCase
{
    public function testUserLogin()
    {
        $this->session->visit($this->url);

        $this->assertMenuWithoutUserSession();
        $this->doOpenAndCloseLoginDialog();
        $this->doLoginWithWrongData();
        $this->doLoginWithCorrectData();
    }

    private function doOpenAndCloseLoginDialog()
    {
        $page = $this->session->getPage();
        // User login dialog is opening + closing
        $page->find('css', '#mnuUser')->click();
        $this->assertMenuItemIsEnabled('mnuUserLogin');
        $page->find('css', '#mnuUserLogin')->click();
        $dialog = $page->find('css', '#divUserLoginWindow');
        $this->assertTrue(null !== $dialog);
        $this->assertTrue($dialog->isVisible());
        $dialog->find('css', 'a.button.Cancel')->click();
        $this->assertFalse($dialog->isVisible());
    }

    private function doLoginWithWrongData()
    {
        $page = $this->session->getPage();
        // Open login window
        $page->find('css', '#mnuUser')->click();
        $page->find('css', '#mnuUserLogin')->click();
        // Login window should be visible
        $dialog = $page->find('css', '#divUserLoginWindow');
        $this->assertTrue($dialog->isVisible());
        // Status messages should be hidden
        $this->assertFalse($dialog->find('css', 'span.status')->isVisible());

        // Attempt login with no data
        $this->assertFalse($dialog->find('css', 'span.msgEmptyFields')->isVisible());
        $dialog->find('css', 'a.button.Send')->click();
        $this->assertTrue($dialog->find('css', 'span.msgEmptyFields')->isVisible());

        // Attempt login with wrong data
        $this->assertFalse($dialog->find('css', 'span.msgInvalidPasswd')->isVisible());
        $dialog->find('css', '#fldUserId')->setValue('wrong_user');
        $dialog->find('css', '#fldUserPasswd')->setValue('wrong_password');
        $dialog->find('css', 'a.button.Send')->click();
        $this->waitForAjax();
        $this->assertTrue($dialog->find('css', 'span.msgInvalidPasswd')->isVisible());
        $dialog->find('css', 'a.button.Cancel')->click();
        $this->assertFalse($dialog->isVisible());
    }

    private function doLoginWithCorrectData()
    {
        $page = $this->session->getPage();
        // Login with correct data
        $page->find('css', '#mnuUser')->click();
        $page->find('css', '#mnuUserLogin')->click();
        $dialog = $page->find('css', '#divUserLoginWindow');
        $dialog->find('css', '#fldUserId')->setValue('root');
        $dialog->find('css', '#fldUserPasswd')->setValue('desinventar');
        $dialog->find('css', 'a.button.Send')->click();
        $this->waitForAjax();
        // @TODO: Find a better method to Wait for menu options to be updated
        sleep(3);
        $this->assertMenuWithUserSession();
        $this->waitForAjax();
        sleep(2);
        // Logout
        $menu = $page->find('css', '#mnuFile');
        $menu->click();
        $this->assertMenuItemIsEnabled('mnuFileLogout');
        $page->find('css', '#mnuFileLogout')->click();
        $this->waitForAjax();
        sleep(3);
        $this->assertMenuWithoutUserSession();
    }

    private function assertMenuWithoutUserSession()
    {
        $this->assertSubMenuStatus(
            'mnuFile',
            ['mnuFileOpen', 'mnuFileUpload', 'mnuFileLanguage'],
            ['mnuFileCreate', 'mnuFileDownload', 'mnuFileInfo', 'mnuFileLogout']
        );
        $this->assertSubMenuStatus('mnuUser', ['mnuUserLogin'], ['mnuUserChangePasswd']);
        $this->waitForAjax();
    }

    private function assertMenuWithUserSession()
    {
        $this->assertSubMenuStatus('mnuFile', ['mnuFileLogout'], []);
        $this->assertSubMenuStatus('mnuUser', ['mnuUserChangeLogin', 'mnuUserChangePasswd'], []);
    }

    private function assertSubMenuStatus($menuId, $enabledOptions, $disabledOptions)
    {
        $page = $this->session->getPage();
        $page->find('css', '#container')->click();
        $menu = $page->find('css', '#' . $menuId);
        $menu->click();
        $this->waitForAjax();
        foreach ($enabledOptions as $itemId) {
            $this->assertMenuItemIsEnabled($itemId);
        }
        foreach ($disabledOptions as $itemId) {
            $this->assertMenuItemIsDisabled($itemId);
        }
        $menu->click();
        $this->waitForAjax();
        $page->find('css', '#container')->click();
        $this->waitForAjax();
    }

    private function assertMenuItemIsDisabled($id)
    {
        $item = $this->getMenuItem($id);
        $this->assertTrue($item->hasClass('x-item-disabled'));
    }

    private function assertMenuItemIsEnabled($id)
    {
        $item = $this->getMenuItem($id);
        $this->assertFalse($item->hasClass('x-item-disabled'));
    }

    private function getMenuItem($id)
    {
        $item = $this->session->getPage()->find('css', '#x-menu-el-' . $id);
        if (null === $item) {
            throw new \Exception('Cannot find menu element ' . $id);
        }
        return $item;
    }
}
