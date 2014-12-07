<?php

class BookDocumentStore extends DataStoreBase implements DataStoreInterface {

    public function get($id)
    {
        $key = $this->getKeyForId($id);
        $bookDocument = BookDocument::find($key);
        if ($bookDocument)
        {
            $bookDocumentData = $bookDocument->toArray();
            // for consistency, clear mongodb's _id, it is only used for retrieval from the books collection
            unset($bookDocumentData[$bookDocument->getKeyName()]);
            return $bookDocumentData;
        }
        return null;
    }

    public function put($id, $data)
    {
        $key = $this->getKeyForId($id);
        $bookDocument = new BookDocument($data);
        $bookDocument->setAttribute($bookDocument->getKeyName(), $key);
        $bookDocument->save();
    }

    public function delete($id)
    {
        $key = $this->getKeyForId($id);
        BookDocument::where('_id', '=', $key)->delete();
    }

}