<?php
namespace WebTest\General;

use \WebTest\AcceptanceTestCase;

class DatabaseListTest extends AcceptanceTestCase
{
    public function testDatabaseList()
    {
        $this->session->visit($this->url);

        $page = $this->session->getPage();

        $this->assertDatabaseList($page);

        // Open the database list from the menu
        $page->find('css', '#mnuFile')->click();
        $page->find('css', '#mnuFileOpen')->click();

        $page = $this->session->getPage();
        $this->assertDatabaseList($page);
    }

    private function assertDatabaseList($page)
    {
        $this->assertTrue($page->has('css', 'div#divRegionList'));
    }
}
