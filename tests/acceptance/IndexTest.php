<?php


class IndexTest extends \Codeception\Test\Unit
{
    /**
     * @var \AcceptanceTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testMe()
    {
    	$this->tester->amOnPage('/');
    	$this->tester->see('WordPress');
    }
}