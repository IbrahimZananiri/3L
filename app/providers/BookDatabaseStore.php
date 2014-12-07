<?php

class BookDatabaseStore extends DataStoreBase implements DataStoreInterface {

    public function get($id)
    {
        $book = Book::with(array('attributeValues', 'attributeValues.attribute'))->find($id);
        return $book ? $book->toArray() : null;
    }

    public function put($id, $data)
    {
        // find and update or create
        $book = Book::find($id);
        if ($book) {
            $book->update($data);
        } else {
            $book = new Book($data);
            $book->id = $id;
            $book->save();
        }

    }

    public function delete($id)
    {
        Book::where('id', '=', $id)->delete();
    }

}