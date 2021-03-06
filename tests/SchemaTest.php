<?php
require_once('tests/app.php');

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SchemaTest extends PHPUnit_Framework_TestCase {

	public function setUp() {}

	public function tearDown()
	{
		Schema::drop('newcollection');
	}

	public function testCreate()
	{
		Schema::create('newcollection');
		$this->assertTrue(Schema::hasCollection('newcollection'));
	}

	public function testDrop()
	{
		Schema::create('newcollection');
		Schema::drop('newcollection');
		$this->assertFalse(Schema::hasCollection('newcollection'));
	}

	public function testIndex()
	{
		Schema::collection('newcollection', function($collection)
		{
			$collection->index('mykey');
		});

		$index = $this->getIndex('newcollection', 'mykey');
		$this->assertEquals(1, $index['key']['mykey']);
	}

	public function testUnique()
	{
		Schema::collection('newcollection', function($collection)
		{
			$collection->unique('uniquekey');
		});

		$index = $this->getIndex('newcollection', 'uniquekey');
		$this->assertEquals(1, $index['unique']);
	}

	public function testDropIndex()
	{
		Schema::collection('newcollection', function($collection)
		{
			$collection->unique('uniquekey');
			$collection->dropIndex('uniquekey');
		});

		$index = $this->getIndex('newcollection', 'uniquekey');
		$this->assertEquals(null, $index);
	}

	public function testBackground()
	{
		Schema::collection('newcollection', function($collection)
		{
			$collection->background('backgroundkey');
		});

		$index = $this->getIndex('newcollection', 'backgroundkey');
		$this->assertEquals(1, $index['background']);
	}

	public function testSparse()
	{
		Schema::collection('newcollection', function($collection)
		{
			$collection->background('backgroundkey');
		});

		$index = $this->getIndex('newcollection', 'backgroundkey');
		$this->assertEquals(1, $index['background']);
	}

	public function testExpire()
	{
		Schema::collection('newcollection', function($collection)
		{
			$collection->expire('expirekey', 60);
		});

		$index = $this->getIndex('newcollection', 'expirekey');
		$this->assertEquals(60, $index['expireAfterSeconds']);
	}

	protected function getIndex($collection, $name)
	{
		$collection = DB::getCollection($collection);

		foreach ($collection->getIndexInfo() as $index)
		{
			if (isset($index['key'][$name])) return $index;
		}

		return false;
	}

}