<?php
namespace airmoi\FileMaker\Command;

use airmoi\FileMaker\FileMaker;
use PHPUnit\Framework\TestCase;


/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-09-09 at 19:46:49.
 */
class FindRequestTest extends TestCase
{
    /**
     * @var FileMaker
     */
    protected $fm;

    public static function setUpBeforeClass():void
    {
        parent::setUpBeforeClass();
        $fm = new FileMaker($GLOBALS['DB_FILE'], $GLOBALS['DB_HOST'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD']);
        $fm->newPerformScriptCommand('sample', 'create sample data', 10)->execute();
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp():void
    {
        $this->fm = new FileMaker($GLOBALS['DB_FILE'], $GLOBALS['DB_HOST'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD']);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown():void
    {
    }

    /**
     * @covers \airmoi\FileMaker\Command\FindRequest::setOmit
     * @todo   Implement testSetOmit().
     */
    public function testSetOmit()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \airmoi\FileMaker\Command\FindRequest::addFindCriterion
     * @todo   Implement testAddFindCriterion().
     */
    public function testAddFindCriterion()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \airmoi\FileMaker\Command\FindRequest::clearFindCriteria
     * @todo   Implement testClearFindCriteria().
     */
    public function testClearFindCriteria()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \airmoi\FileMaker\Command\FindRequest::isEmpty
     * @todo   Implement testIsEmpty().
     */
    public function testIsEmpty()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}
