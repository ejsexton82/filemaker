<?php

namespace airmoi\FileMaker;

use airmoi\FileMaker\FileMaker;
use PHPUnit\Framework\TestCase;


/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-09-09 at 19:46:51.
 */
class FileMakerExceptionTest extends TestCase
{

    /**
     * @var FileMakerException
     */
    protected $fm;

    /**
     * @var array
     */
    protected $locales;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp():void
    {
        $this->fm = new FileMaker($GLOBALS['DB_FILE'], $GLOBALS['DB_HOST'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD']);
        $this->locales = require(dirname(__FILE__) . '/../../../src/Error/en.php');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown():void
    {
    }

    /**
     * @covers \airmoi\FileMaker\FileMakerException::getErrorString
     */
    public function testGetErrorString()
    {
        $exception = new FileMakerException($this->fm, null, 0);
        $this->assertEquals($this->locales[0], $exception->getErrorString(0));

        $this->assertEquals($this->locales[15], $exception->getErrorString(15));

        $this->assertEquals($this->locales[-1], $exception->getErrorString(-5));

        $this->assertEquals($this->locales[-1], $exception->getErrorString(155555));

        $this->assertEquals($this->locales[-1], $exception->getErrorString(null));
    }

    /**
     * @covers \airmoi\FileMaker\FileMakerException::isValidationError
     */
    public function testIsValidationError()
    {
        $exception = new FileMakerException($this->fm, null, 0);
        $this->assertFalse($exception->isValidationError());
    }
}
