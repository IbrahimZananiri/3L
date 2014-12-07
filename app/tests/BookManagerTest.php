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
        $this->assertEquals(BookManager::getInstance()->getLastSource(), 'BookDatabaseStore');
        $bookData = BookManager::getInstance()->findBookDataById($book->id);
        $this->assertEquals(BookManager::getInstance()->getLastSource(), 'BookCacheStore');
        $book->load('attributeValues', 'attributeValues.attribute');
        $this->assertNotNull($book->attributeValues);

        $attributeValue = $book->attributeValues[0];
        $attributeValue->value = 'new value!';
        $attributeValue->save();

        $bookData = BookManager::getInstance()->findBookDataById($book->id);
        $this->assertEquals(BookManager::getInstance()->getLastSource(), 'BookDatabaseStore');
    }

    public function testFirstRetrieval()
    {
        $book = Book::orderBy('id', 'desc')->firstOrFail();
        $bookData = BookManager::getInstance()->findBookDataById($book->id);
        $this->assertTrue(!!$bookData);
        $this->assertEquals(BookManager::getInstance()->getLastSource(), 'BookDatabaseStore');
    }

    public function testCacheRetrieval()
    {
        $book = Book::orderBy('id', 'desc')->firstOrFail();
        BookManager::getInstance()->invalidateAllForId($book->id);
        $bookData = BookManager::getInstance()->findBookDataById($book->id);
        $this->assertTrue(!!$bookData);
        $this->assertEquals(BookManager::getInstance()->getLastSource(), 'BookDatabaseStore');
        $bookData = BookManager::getInstance()->findBookDataById($book->id);
        $this->assertEquals(BookManager::getInstance()->getLastSource(), 'BookCacheStore');
    }

    public function testDocumentRetrieval()
    {
        $book = Book::orderBy('id', 'desc')->firstOrFail();
        BookManager::getInstance()->invalidateAllForId($book->id);
        $bookData = BookManager::getInstance()->findBookDataById($book->id);
        $this->assertTrue(!!$bookData);
        $this->assertEquals(BookManager::getInstance()->getLastSource(), 'BookDatabaseStore');
        Cache::forget('object:'.$book->id);
        $bookData = BookManager::getInstance()->findBookDataById($book->id);
        $this->assertEquals(BookManager::getInstance()->getLastSource(), 'BookDocumentStore');
    }


}
