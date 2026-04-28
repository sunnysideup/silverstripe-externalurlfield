<?php

namespace Sunnysideup\ExternalURLField\Tests;

use SilverStripe\Forms\Validation\RequiredFieldsValidator;
use SilverStripe\Dev\SapphireTest;
use Sunnysideup\ExternalURLField\ExternalURLField;

/**
 * @internal
 * @coversNothing
 */
class ExternalURLFieldTest extends SapphireTest
{
    public function testSetConfig()
    {
        $field = ExternalURLField::create('URL', 'URL');

        //test example from README
        $field->setConfig([
            //these are always required / set
            'defaultparts' => [
                'scheme' => 'http',
            ],
            //these parts are removed from saved urls
            'removeparts' => [
                'scheme' => false,
                'user' => true,
                'pass' => true,
                'host' => false,
                'port' => false,
                'path' => false,
                'query' => false,
                'fragment' => false,
            ],
            'html5validation' => true,
        ]);

        $field->setConfig('defaultparts', [
            'scheme' => 'https',
            'host' => 'example.com',
        ]);
        $this->assertEquals($field->getConfig('defaultparts'), [
            'scheme' => 'https',
            'host' => 'example.com',
        ]);

        $field->setConfig('removeparts', [
            'query' => true,
            'fragment' => true,
        ]);
        $this->assertEquals($field->getConfig('removeparts'), [
            'scheme' => false,
            'user' => true,
            'pass' => true,
            'host' => false,
            'port' => false,
            'path' => false,
            'query' => true,
            'fragment' => true,
        ]);

        $field->setConfig('html5validation', false);
        $this->assertEquals(false, $field->getConfig('html5validation'));
    }

    public function testDefaultSaving()
    {
        $field = ExternalURLField::create('URL', 'URL');

        $field->setValue(
            'http://username:password@www.hostname.com:81/path?arg=value#anchor'
        );
        $this->assertEquals('http://www.hostname.com:81/path?arg=value#anchor', $field->dataValue());

        $field->setValue('https://hostname.com/path');
        $this->assertEquals('https://hostname.com/path', $field->dataValue());

        $field->setValue('');
        $this->assertEquals('', $field->dataValue());

        $field->setValue('www.hostname.com');
        $this->assertEquals('http://www.hostname.com', $field->dataValue());

        $field->setValue('http://');
        $this->assertEquals('', $field->dataValue());
    }

    public function testValidation()
    {
        $field = ExternalURLField::create('URL', 'URL');
        $validator = RequiredFieldsValidator::create();

        $field->setValue(
            'http://username:password@www.hostname.com:81/path?arg=value#anchor'
        );
        $this->assertTrue($field->validate());

        $field->setValue('');
        $this->assertTrue($field->validate());

        $field->setValue('asefasdfasfasfasfasdfasfasdfas');
        $this->assertFalse($field->validate());

        $field->setValue('http://3628126748');
        $this->assertFalse($field->validate());
    }
}
