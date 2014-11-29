<?php

class BookManagerTest extends TestCase {


    public function setUp()
    {
        parent::setUp();
        Cache::flush();
        BookDocument::truncate();
        Artisan::call('migrate');
        $this->seed();
    }

    public function testEavUpdateInvalidatesCache()
    {
        $book = Book::orderBy('id', 'desc')->firstOrFail();
        BookManager::getInstance()->invalidateAllForId($book->id);
        $bookData = BookManager::getInstance()->findBookDataById($book->id);
        $this->assertEquals(BookManager::getInstance()->getLastSource(), 'database');
        $bookData = BookManager::getInstance()->findBookDataById($book->id);
        $this->assertEquals(BookManager::getInstance()->getLastSource(), 'cache');
        $book->load('attributeValues', 'attributeValues.attribute');
        $this->assertNotNull($book->attributeValues);

        $attributeValue = $book->attributeValues[0];
        $attributeValue->value = 'new value!';
        $attributeValue->save();

        $bookData = BookManager::getInstance()->findBookDataById($book->id);
        $this->assertEquals(BookManager::getInstance()->getLastSource(), 'database');
    }

    public function testFirstRetrieval()
    {
        $book = Book::orderBy('id', 'desc')->firstOrFail();
        $bookData = BookManager::getInstance()->findBookDataById($book->id);
        $this->assertTrue(!!$bookData);
        $this->assertEquals(BookManager::getInstance()->getLastSource(), 'database');
    }

    public function testCacheRetrieval()
    {
        $book = Book::orderBy('id', 'desc')->firstOrFail();
        BookManager::getInstance()->invalidateAllForId($book->id);
        $bookData = BookManager::getInstance()->findBookDataById($book->id);
        $this->assertTrue(!!$bookData);
        $this->assertEquals(BookManager::getInstance()->getLastSource(), 'database');
        $bookData = BookManager::getInstance()->findBookDataById($book->id);
        $this->assertEquals(BookManager::getInstance()->getLastSource(), 'cache');
    }

    public function testDocumentRetrieval()
    {
        $book = Book::orderBy('id', 'desc')->firstOrFail();
        BookManager::getInstance()->invalidateAllForId($book->id);
        $bookData = BookManager::getInstance()->findBookDataById($book->id);
        $this->assertTrue(!!$bookData);
        $this->assertEquals(BookManager::getInstance()->getLastSource(), 'database');
        BookManager::getInstance()->invalidateCacheForId($book->id);
        $bookData = BookManager::getInstance()->findBookDataById($book->id);
        $this->assertEquals(BookManager::getInstance()->getLastSource(), 'document');
    }


}
