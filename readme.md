# 3 Layer Bookshelf

## Overview
- Implemeted in MVC and other patterns, using the Laravel framework
- Uses Eloquent ORM for relational database access
- **laravel-mongodb** for MongoDB object access (https://github.com/jenssegers/laravel-mongodb)
- Uses Laravel's Cache for accessing file/memory cache
- Observers to invalidate document index and/or cache


## Installation
1. git clone repo
2. Install PHP-MongoDB (http://php.net/manual/en/book.mongo.php)
3. run composer install (make sure composer is installed)
4. run php artisan migrate --env=local
5. run php artisan db:seed --env=local
6. run php artisan serve --env=local



## Configuration
Configuration is located at app/config/ and app/config/{env}/
database.php in "testing", "local", and production, by default uses sqlite for relational database, to switch to mysql, simply set 'default' to 'mysql'
cache.php in "local", and production, by default uses file cache for simplicity, you can switch to "memcached" or "redis" as needed, for "testing" environment, it utilizes the Array cache


## Tests
Make sure phpunit is installed, then run phpunit.

Tests cover few important cases, including:
- Source of data in different scenarios ("database", "document", or "cache")
- Source is "database" of first retrieval
- Source is "cache" on second retrieval
- EAV value update invalidates cache, source is "database" 


## Models
- Book
- Attribute
- AttributeValue
- BookDocument
- User
- Interaction (with Polymorphic interactable relatio, user_id and timestamps, exposes extremely dynamic static::analytics($options) method)

## Observers
- BookObserver: Invalidates both cache and index document for the book on model update and Book delete
- AttributeValueObserver: Invalidates both cache and index document for related Book on AttributeValue addition, update, or removal
- Event binding in InteratableTrait, see below **Traits** section

## Traits
### InteractableTrait
	- Allows tracking of object events, (e.g. creation, updates) for Analytics
	- A Interaction instance is created with interactor user_id, object interactable_id, object interactable_type, optional interactable_related_id, and timestamp, and then stored.
	- Adds the functionality to Models that use this trait, through a polymorphic relationship.
	- This trait adds "interations" relation and binds model events
	- Book, Attribute and AttributeValue use this trait, so these models persist Interaction data.
	- Related Interactions are deleted when their Interactable object is deleted.

## BookManager
- Accessed via Singleton getInstance()
- BookManager::getInstance()->findBookDataForId($id): Attempts to locate book data in the following sources, in the following order:

	1. Cache: If found, sets lastSource to "cache", then returns Book Data
	2. Document Store: By querying through BookDocument. If found, sets lastSource to "document", stores data in Cache, and returns Book data
	3. Database: Finds Book or fails, if found, sets lastSource to "database", stores Book data in Cache for a period of time and in Document Store, then returns Book data

- $cacheMinutes in BookManager instance is used to determine TTL for cached object in minutes, default: 10 minutes
- BookManager::getInstance()->invalidateCacheForId($id): Invalidates cache for book by ID
- BookManager::getInstance()->invalidateIndexDocumentForId($id): Invalidates/removes document index record for book by ID
- BookManager::getInstance()->invalidateAllForId($id): invalidates both Cache and Index Document.


## Routes
- /: Welcome page, presents a API endpoint link to latest Book
- /api/books/{id}" REST Endpoint for accessing a book by ID
- /api/analytics/{ObjectType}?action={ActionName}&orderBy={OrderByField}&orderDirection={asc|desc}&groupBy={GroupByField}&dateStart={YYYY-MM-DD}&dateEnd={YYYY-MM-DD}
	- Parameters are optional, defaults are handled internally

## Analytics Examples
	- Attribute ID Usage Counts: 
		- Example Request: /api/analytics/attributevalue?action=created&orderBy=count&orderDirection=desc&groupBy=relatable_id&dateStart=2014-11-28&dateEnd=2014-12-01
		- Example Response:
		[{"interactable_id":"435","interactable_type":"AttributeValue","relatable_id":"60","relatable_type":"Attribute","count":"36","relatable":{"id":"60","name":"quote","is_multiple":"1","user_id":"67"}},{"interactable_id":"437","interactable_type":"AttributeValue","relatable_id":"59","relatable_type":"Attribute","count":"24","relatable":{"id":"59","name":"author","is_multiple":"1","user_id":"67"}},{"interactable_id":"432","interactable_type":"AttributeValue","relatable_id":"56","relatable_type":"Attribute","count":"12","relatable":{"id":"56","name":"summary","is_multiple":"0","user_id":"67"}}]

	- Book Creators By Book Count
		- Example Request: http://localhost:8000/api/analytics/book?action=created&orderBy=count&orderDirection=desc&groupBy=user_id&dateStart=2014-11-28&dateEnd=2014-12-01
		- Example Response:
		[{"interactable_id":"77","interactable_type":"Book","relatable_id":null,"relatable_type":null,"count":"3","user_id":"67","user":{"id":"67","created_at":"2014-11-28 19:20:42","updated_at":"2014-11-28 19:20:42","email":"ibrahim@exmaple.com"}},{"interactable_id":"73","interactable_type":"Book","relatable_id":null,"relatable_type":null,"count":"2","user_id":"68","user":{"id":"68","created_at":"2014-11-28 19:20:42","updated_at":"2014-11-28 19:20:42","email":"bob@example.com"}},{"interactable_id":"74","interactable_type":"Book","relatable_id":null,"relatable_type":null,"count":"2","user_id":"69","user":{"id":"69","created_at":"2014-11-28 19:20:42","updated_at":"2014-11-28 19:20:42","email":"lisa@example.com"}},{"interactable_id":"75","interactable_type":"Book","relatable_id":null,"relatable_type":null,"count":"2","user_id":"70","user":{"id":"70","created_at":"2014-11-28 19:20:42","updated_at":"2014-11-28 19:20:42","email":"anna@example.com"}},{"interactable_id":"76","interactable_type":"Book","relatable_id":null,"relatable_type":null,"count":"2","user_id":"71","user":{"id":"71","created_at":"2014-11-28 19:20:42","updated_at":"2014-11-28 19:20:42","email":"john@example.com"}},{"interactable_id":"78","interactable_type":"Book","relatable_id":null,"relatable_type":null,"count":"1","user_id":"72","user":{"id":"72","created_at":"2014-11-28 19:20:42","updated_at":"2014-11-28 19:20:42","email":"laura@example.com"}}]


## TODO
- Consider polymorphic relationship for AttributeValue
- More test coverage