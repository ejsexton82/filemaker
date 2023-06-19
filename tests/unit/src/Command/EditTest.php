<?php
namespace airmoi\FileMaker\Command;

use airmoi\FileMaker\FileMaker;
use airmoi\FileMaker\FileMakerException;
use PHPUnit\Framework\TestCase;


/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-09-09 at 20:40:04.
 */
class EditTest extends TestCase
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
     * @covers \airmoi\FileMaker\Command\Edit::execute
     */
    public function testExecute()
    {
        $record = $this->fm->newFindAnyCommand('sample')->execute()->getFirstRecord();
        $timestamp = time();
        $editCmd = $this->fm->newEditCommand('sample', $record->getRecordId(), ['text_field' => $timestamp]);

        $this->assertEquals($timestamp, $editCmd->execute()->getFirstRecord()->getField('text_field'));
    }

    /**
     * @covers \airmoi\FileMaker\Command\Edit::setField
     */
    public function testSetField()
    {
        $editCmd = $this->fm->newEditCommand('sample', 1);
        $this->assertEquals('Sample text', $editCmd->setField('text_field', 'Sample text'));
        $this->assertEquals('Sample text', $editCmd->setField('related_sample::text_field', 'Sample text'));

        $date = date('m/d/Y');
        $this->assertEquals($date, $editCmd->setField('date_field', $date));

        $time = date('H:i:s');
        $this->assertEquals($time, $editCmd->setField('time_field', $time));

        $timeStamp = date('m/d/Y H:i:s');
        $this->assertEquals($timeStamp, $editCmd->setField('timestamp_field', $timeStamp));
    }

    /**
     * @covers \airmoi\FileMaker\Command\Edit::setFieldFromTimestamp
     */
    public function testSetFieldFromTimestamp()
    {
        $editCmd = $this->fm->newEditCommand('sample', 1);

        $time = time();
        $this->assertEquals(date('m/d/Y', $time), $editCmd->setFieldFromTimestamp('date_field', $time));
        $this->assertEquals(date('m/d/Y H:i:s', $time), $editCmd->setFieldFromTimestamp('timestamp_field', $time));
        $this->assertEquals(date('H:i:s', $time), $editCmd->setFieldFromTimestamp('time_field', $time));
    }

    /**
     * @covers \airmoi\FileMaker\Command\Edit::setModificationId
     */
    public function testSetModificationId()
    {

        $record = $this->fm->newFindAnyCommand('sample')->execute()->getFirstRecord();
        $timestamp = time();
        $editCmd = $this->fm->newEditCommand('sample', $record->getRecordId(), ['text_field' => $timestamp]);
        $editCmd->setModificationId($record->getModificationId()+1);

        $this->expectException(FileMakerException::class);
        $this->expectExceptionCode(306);
        $editCmd->execute();
    }

    /**
     * @covers \airmoi\FileMaker\Command\Edit::setDeleteRelated
     */
    public function testSetDeleteRelated()
    {
        $record = $this->fm->newFindAnyCommand('sample')->execute()->getFirstRecord();
        $oldRelatedSet = $record->getRelatedSet('related_sample');

        $editCmd = $this->fm->newEditCommand('sample', $record->getRecordId());
        $editCmd->setDeleteRelated('related_sample.' . $oldRelatedSet[0]->getRecordId());

        $result = $editCmd->execute();

        $newRelatedSet = $result->getFirstRecord()->getRelatedSet('related_sample');
        $this->assertEquals(sizeof($oldRelatedSet) - 1, sizeof($newRelatedSet));
    }
}
