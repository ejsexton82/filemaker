<?php

namespace airmoi\FileMaker\Object;

use airmoi\FileMaker\FileMaker;
use airmoi\FileMaker\FileMakerException;
use airmoi\FileMaker\FileMakerValidationException;
use PHPUnit\Framework\TestCase;


/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-09-09 at 19:46:51.
 */
class RecordTest extends TestCase
{

    /**
     * @var FileMaker
     */
    protected $fm;

    /**
     *
     * @var Record
     */
    protected $record;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp():void
    {
        $this->fm = new FileMaker($GLOBALS['DB_FILE'], $GLOBALS['DB_HOST'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD']);
        //$this->fm->newPerformScriptCommand('sample', "create sample data", 50)->execute();
        $this->record = $this->fm->newFindAnyCommand('sample')->execute()->getFirstRecord();
    }

    public static function setUpBeforeClass():void
    {
        $fm = new FileMaker($GLOBALS['DB_FILE'], $GLOBALS['DB_HOST'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD']);
        $fm->newPerformScriptCommand('sample', 'create sample data', 50)->execute();
        //$this->record = $this->fm->newFindAnyCommand('sample')->execute()->getFirstRecord();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown():void
    {
    }

    /**
     * @covers \airmoi\FileMaker\Object\Record::getLayout
     */
    public function testGetLayout()
    {
        $this->assertInstanceOf(Layout::class, $this->record->getLayout());
    }

    /**
     * @covers \airmoi\FileMaker\Object\Record::getFields
     */
    public function testGetFields()
    {
        $this->assertTrue(in_array('text_field', $this->record->getFields(), true));
    }

    /**
     * @covers \airmoi\FileMaker\Object\Record::getField
     */
    public function testGetField()
    {
        $this->assertStringStartsWith('record #', $this->record->getField('text_field'));
        $this->assertStringEndsWith('repeat 2', $this->record->getField('text_field', 1));
        $this->assertStringStartsWith('<h1>', $this->record->getField('text_field', 2, true));
        $this->assertStringStartsWith('&lt;', $this->record->getField('text_field', 2));
        $this->assertEmpty($this->record->getField('text_field', 4));
    }

    /**
     * @covers \airmoi\FileMaker\Object\Record::getFieldValueListTwoFields
     */
    public function testGetFieldValueListTwoFields()
    {
        $valueList = $this->record->getFieldValueListTwoFields('id_sample');
        $this->assertEquals(50, count($valueList));

        $rnd = rand(1, 50);
        $this->assertEquals($rnd, $valueList[$rnd . ' record #'.$rnd]);
    }

    /**
     * @covers \airmoi\FileMaker\Object\Record::getFieldAsTimestamp
     */
    public function testGetFieldAsTimestamp()
    {
        /*$this->markTestIncomplete(
            'This test has not been implemented yet.'
        );*/
        $dtObject = \DateTime::createFromFormat('U', $this->record->getFieldAsTimestamp('date_field'));
        $dtObject->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        $this->assertEquals( $this->record->getField('date_field') , $dtObject->format('m/d/Y') );

    }

    /**
     * @covers \airmoi\FileMaker\Object\Record::setField
     */
    public function testSetField()
    {
        $this->record->setField('text_field', __METHOD__);
        $this->assertEquals(__METHOD__, $this->record->getField('text_field'));

        $this->record->setField('text_field', __METHOD__ . ' repeat 1', 1);
        $this->assertEquals(__METHOD__ . ' repeat 1', $this->record->getField('text_field', 1));

        $this->record->setField('related_sample::text_field', __METHOD__ . 'related[1]');
        $this->assertEquals(__METHOD__ . 'related[1]', $this->record->getField('related_sample::text_field'));

        $this->record->setField('related_sample::text_field', __METHOD__ . 'related[2]', 1);
        $this->assertEquals(__METHOD__ . 'related[2]', $this->record->getField('related_sample::text_field', 1));

        $this->record->setField('date_field', date('m/d/Y'));
        $this->assertEquals(date('m/d/Y'), $this->record->getField('date_field'));


        try {
            $this->record->setField('missing_field', __METHOD__);
            $this->fail(FileMakerException::class . ' should have been thrown');
        } catch (\Exception $e) {
            $this->assertInstanceOf(FileMakerException::class, $e);
        }

        /**
         * Date autoformat
         */
        $this->fm->setProperty('dateFormat', 'd/m/Y');

        $this->record->setField('date_field', '16/06/2016');
        $this->assertEquals('06/16/2016', $this->record->fields['date_field'][0]);

        $this->record->setField('date_field', '01/02/0001');
        $this->assertEquals('02/01/0001', $this->record->fields['date_field'][0]);

        $this->record->setField('date_field', '31/12/4000');
        $this->assertEquals('12/31/4000', $this->record->fields['date_field'][0]);


        try {
            $this->record->setField('date_field', '2016-08-19');
            $this->fail(FileMakerException::class . ' should have been thrown');
        } catch (\Exception $e) {
            $this->assertInstanceOf(FileMakerException::class, $e);
        }

        $this->record->setField('timestamp_field', '08/01/1942 00:00:00');
        $this->assertEquals('01/08/1942 00:00:00', $this->record->fields['timestamp_field'][0]);

        try {
            $this->record->setField('timestamp_field', '2016-08-19');
            $this->fail(FileMakerException::class . ' should have been thrown');
        } catch (\Exception $e) {
            $this->assertInstanceOf(FileMakerException::class, $e);
        }
    }

    /**
     * @covers \airmoi\FileMaker\Object\Record::setFieldFromTimestamp
     */
    public function testSetFieldFromTimestamp()
    {

        /*
         * Date fields
         */
        $dt = new \DateTime();
        $this->record->setFieldFromTimestamp('date_field', $dt->format('U'));
        $this->assertEquals(date('m/d/Y'), $this->record->getField('date_field'));

        $time = time()+3600;
        $this->record->setFieldFromTimestamp('date_field', $time);
        $this->assertEquals(date('m/d/Y', time()+3600), $this->record->getField('date_field'));

        /*
         * To be activated when setFieldFromTimestamp will implement DateTime
         */
        //$dt = new \DateTime('0001-01-01T00:00:00');
        //$this->record->setFieldFromTimestamp('date_field', $dt->format('U') );
        //$this->assertEquals('01/01/0001', $this->record->getField('date_field'));

        //$dt = new \DateTime('4000-12-31T23:59:59');
        //$this->record->setFieldFromTimestamp('date_field', $dt->format('U') );
        //$this->assertEquals('12/31/4000', $this->record->getField('date_field'));

        /*
         * timestamp fields
         */
        $dt = new \DateTime();
        $this->record->setFieldFromTimestamp('timestamp_field', $dt->format('U'));
        $this->assertEquals($dt->format('m/d/Y H:i:s'), $this->record->getField('timestamp_field'));

        $time = time()+3600;
        $this->record->setFieldFromTimestamp('timestamp_field', $time);
        $this->assertEquals(date('m/d/Y H:i:s', $time), $this->record->getField('timestamp_field'));

        /*
         * To be activated when setFieldFromTimestamp will implement DateTime
         */
        //$dt = new \DateTime('0001-01-01T00:00:00');
        //$this->record->setFieldFromTimestamp('timestamp_field', $dt->format('U') );
        //$this->assertEquals('01/01/0001 00:00:00', $this->record->getField('timestamp_field'));

        //$dt = new \DateTime('4000-12-31T23:59:59');
        //$this->record->setFieldFromTimestamp('timestamp_field', $dt->format('U') );
        //$this->assertEquals('12/31/4000 23:59:59', $this->record->getField('timestamp_field'));

        /*
         * time field
         */
        $dt = new \DateTime();
        $this->record->setFieldFromTimestamp('time_field', $dt->format('U'));
        $this->assertEquals($dt->format('H:i:s'), $this->record->getField('time_field'));

        $time = time()+3600;
        $this->record->setFieldFromTimestamp('time_field', $time);
        $this->assertEquals(date('H:i:s', $time), $this->record->getField('time_field'));

        /*
         * To be activated when setFieldFromTimestamp will implement DateTime
         */
        //$dt = new \DateTime('0001-01-01T00:00:00');
        //$this->record->setFieldFromTimestamp('time_field', $dt->format('U') );
        //$this->assertEquals('00:00:00', $this->record->getField('time_field'));

        //$dt = new \DateTime('4000-12-31T23:59:59');
        //$this->record->setFieldFromTimestamp('time_field', $dt->format('U') );
        //$this->assertEquals('23:59:59', $this->record->getField('time_field'));
    }

    /**
     * @covers \airmoi\FileMaker\Object\Record::getRecordId
     */
    public function testGetRecordId()
    {
        $this->assertNotNull($this->record->getRecordId());
    }

    /**
     * @covers \airmoi\FileMaker\Object\Record::getModificationId
     */
    public function testGetModificationId()
    {
        $this->assertNotNull($this->record->getModificationId());
    }

    /**
     * @covers \airmoi\FileMaker\Object\Record::getRelatedSet
     */
    public function testGetRelatedSet()
    {
        $this->assertContainsOnlyInstancesOf(Record::class, $this->record->getRelatedSet('related_sample'));
    }

    /**
     * @covers \airmoi\FileMaker\Object\Record::newRelatedRecord
     */
    public function testNewRelatedRecord()
    {
        $relatedRecord = $this->record->newRelatedRecord('related_sample');

        $this->assertEquals($this->record, $relatedRecord->getParent());
        $this->assertEquals('related_sample', $relatedRecord->relatedSetName);
    }

    /**
     * @covers \airmoi\FileMaker\Object\Record::setParent
     */
    public function testSetParent()
    {
        $record = new Record($this->record->getLayout());
        $record->setParent($this->record);
        $this->assertEquals($this->record, $record->getParent());
    }

    /**
     * @covers \airmoi\FileMaker\Object\Record::validate
     */
    public function testValidate()
    {
        $this->fm->errorHandling = "default";
        $this->record->setField('date_field', 'incorrect Date');
        $this->assertInstanceOf(FileMakerValidationException::class, $this->record->validate('date_field'));

        $e = $this->record->validate();
        $this->assertInstanceOf(FileMakerValidationException::class, $this->record->validate());
        $this->assertEquals(1, $this->record->validate()->numErrors());

        $this->record->setField('text_field', str_repeat('0', 51));
        $this->assertEquals(2, $this->record->validate()->numErrors());

        $this->record->setField('text_field', '', 1);
        $this->assertEquals(3, $this->record->validate()->numErrors());

        $this->record->setField('timestamp_field', 'incorrect timestamp', 1);
        $this->assertEquals(4, $this->record->validate()->numErrors());

        $this->record->setField('time_field', 'incorrect time', 1);
        $this->assertEquals(5, $this->record->validate()->numErrors());

        $this->record->setField('number_field', 'incorrect number', 1);
        $this->assertEquals(6, $this->record->validate()->numErrors());

        //$this->record->setField('date_field', '06/16/16', 1);
        //$this->assertEquals(7, $this->record->validate()->numErrors());
    }

    /**
     * @covers \airmoi\FileMaker\Object\Record::commit
     * @todo   Implement testCommit().
     */
    public function testCommit()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \airmoi\FileMaker\Object\Record::delete
     * @todo   Implement testDelete().
     */
    public function testDelete()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \airmoi\FileMaker\Object\Record::getRelatedRecordById
     * @todo   Implement testGetRelatedRecordById().
     */
    public function testGetRelatedRecordById()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}
