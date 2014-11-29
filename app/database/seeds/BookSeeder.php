<?php

class BookSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('interactions')->delete();

        DB::table('users')->delete();
        $users = array();
        $users[] = User::create(array('email' => 'ibrahim@exmaple.com', 'password' => Hash::make('secret'.rand())));
        $users[] = User::create(array('email' => 'bob@example.com', 'password' => Hash::make('secret'.rand())));
        $users[] = User::create(array('email' => 'lisa@example.com', 'password' => Hash::make('secret'.rand())));
        $users[] = User::create(array('email' => 'anna@example.com', 'password' => Hash::make('secret'.rand())));
        $users[] = User::create(array('email' => 'john@example.com', 'password' => Hash::make('secret'.rand())));

        $lastUser = User::create(array('email' => 'laura@example.com', 'password' => Hash::make('secret'.rand())));


        DB::table('attributes')->delete();
        $summaryAttribute = Attribute::create(array('name' => 'summary', 'user_id' => $users[0]->id));
        $genreAttribute = Attribute::create(array('name' => 'genre', 'is_multiple' => true, 'user_id' => $users[0]->id));
        $yearAttribute =Attribute::create(array('name' => 'year', 'user_id' => $users[0]->id));
        $authorAttribute = Attribute::create(array('name' => 'author', 'is_multiple' => true, 'user_id' => $users[0]->id));
        $quoteAttribute = Attribute::create(array('name' => 'quote', 'is_multiple' => true, 'user_id' => $users[0]->id));


        DB::table('books')->delete();
        DB::table('attribute_values')->delete();

        $books = array();

        $bookIteratorArray = array('One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', "Nine", "Ten", "Eleven", "Twelve");
        foreach ($bookIteratorArray as $index => $name) {

            $book = Book::create(array('title' => 'Book '.$name, 'user_id' => ($index==count($bookIteratorArray)-1) ? $lastUser->id : $users[$index%count($users)]->id));
            $book->createAttributeValue('summary', 'Summary of Book '.$name);
            $book->createAttributeValue('summary', 'Summary of Book '.$name.' Updated');
            $book->createAttributeValue('quote', 'Lorem Ipsum Quote 1 of Book '.$name.', Lorem Ipsum');
            $book->createAttributeValue('quote', 'Lorem Ipsum Quote 2 of Book '.$name.', Lorem Ipsum');
            $book->createAttributeValue('quote', 'Lorem Ipsum Quote 3 of Book '.$name.', Lorem Ipsum');
            $book->createAttributeValue('author', $name.'\'s Author');
            $book->createAttributeValue('author', $name.'\'s Co Author');
            $books[] = $book;
        }

    }

}
