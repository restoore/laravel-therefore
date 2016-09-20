<?php

namespace Restoore\Therefore;

use Restoore\Therefore\Models\ThereforeDocument;
use Restoore\Therefore\Models\ThereforeFile;

trait ThereforeTrait
{

    public function searchDocuments()
    {
        $searchableField = $this->thereforeSearchableField;
        $response = \Therefore::ExecuteSimpleQuery(['parameters' => ['CategoryNo' => $this->thereforeCategoryNo,
            'FieldNo' => $this->thereforeFieldNo,
            'Condition' => $this->$searchableField,
            'OrderByFieldNo' => $this->thereforeFieldNo]]);
        $result = isset($response->ExecuteSimpleQueryResult->QueryResult->ResultRows->WSQueryResultRow) ?
            $response->ExecuteSimpleQueryResult->QueryResult->ResultRows->WSQueryResultRow : false;

        if (!$result)
            return false; // files not found

        return !is_array($result) ? [$result] : $result;
    }

    public function refreshCacheFiles()
    {
        // get document in local DB
        $searchableField = $this->thereforeSearchableField;
        $docsDB = ThereforeDocument::where(['searchableField' => $this->$searchableField, 'CategoryNo' => $this->thereforeCategoryNo])->get();

        $rows = $this->searchDocuments();
        // if documents have been found
        if ($rows) {
            foreach ($rows as $row) {
                $theDocument = \Therefore::getDocument(['parameters' => ['DocNo' => $row->DocNo, 'IsStreamsInfoNeeded' => true]]);
                $docThe = new ThereforeDocument;
                $docThe->docNo = $theDocument->GetDocumentResult->IndexData->DocNo;
                $docThe->categoryNo = $theDocument->GetDocumentResult->IndexData->CategoryNo;
                $docThe->ctgryName = $theDocument->GetDocumentResult->IndexData->CtgryName;
                $docThe->versionNo = $theDocument->GetDocumentResult->IndexData->VersionNo;
                $docThe->lastChangeTime = $theDocument->GetDocumentResult->IndexData->LastChangeTime;
                $docThe->title = $theDocument->GetDocumentResult->IndexData->Title;
                $docThe->searchableField = $this->$searchableField;

                // check if document has streams
                $streams = isset($theDocument->GetDocumentResult->StreamsInfo->WSStreamInfo) ?
                    $theDocument->GetDocumentResult->StreamsInfo->WSStreamInfo : false;
                if ($streams)
                    $streams = is_array($streams) ? $streams : [$streams]; // get streams info of a document

                $isFound = false;
                // check all docs in DB and compare to Therefore DB
                foreach ($docsDB as $key => $docDB) {
                    if ($docDB->docNo == $docThe->docNo) {
                        // doc already exists, check LastchangeTime
                        if ($docDB->lastChangeTime->ne($docThe->lastChangeTime)) {
                            // delete doc
                            $docDB->deleteFromServer();
                            // save new document in local DB
                            $docThe->saveOrFail();
                            // let's get all streams
                            $this->saveStreams($streams, $docThe->id);
                        }
                        $docsDB->forget($key);
                        $isFound = true;
                    }
                }
                // new document
                if (!$isFound) {
                    $docThe->saveOrFail();
                    $this->saveStreams($streams, $docThe->id);
                }
            }
        }
        // delete documents still in local DB
        foreach ($docsDB as $key => $docDB) {
            $docDB->deleteFromServer();
        }
    }

    public function listDocuments()
    {
        $searchableField = $this->thereforeSearchableField;
        return ThereforeDocument::where(['searchableField' => $this->$searchableField])->get();
    }

    private function saveStreams($streams, $document_id)
    {
        if(!$streams)
            return false;

        foreach ($streams as $stream) {
            $fileThe = new ThereforeFile;
            $fileThe->streamNo = $stream->StreamNo;
            $fileThe->fileName = $stream->FileName;
            $fileThe->therefore_document_id = $document_id;
            $fileThe->saveOrFail();
            $fileThe->transfert();
        }
    }
}