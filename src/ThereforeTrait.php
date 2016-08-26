<?php
/**
 * Created by PhpStorm.
 * User: SinfCONGf
 * Date: 24/08/2016
 * Time: 15:31
 */

namespace Restoore\Therefore;


trait ThereforeTrait
{

    public function searchDocuments()
    {
        $searchableField = $this->thereforeSearchableField;
        $result = \Therefore::ExecuteSimpleQuery(['parameters' => ['CategoryNo' => $this->thereforeCategoryNo,
            'FieldNo' => $this->thereforeFieldNo,
            'Condition' => $this->$searchableField,
            'OrderByFieldNo' => $this->thereforeFieldNo]]);

        if (!isset($result->ExecuteSimpleQueryResult->QueryResult->ResultRows->WSQueryResultRow))
            return false; //aucun fichiers trouvés

        return $result->ExecuteSimpleQueryResult->QueryResult->ResultRows->WSQueryResultRow;
    }

    public function refreshCacheFiles()
    {
        $rows = $this->searchDocuments();
        foreach ($rows as $row) {
            $theDocument = \Therefore::getDocument(['parameters' => ['DocNo' => $row->DocNo, 'IsStreamsInfoNeeded' => true]]);
            //on supprime tous les fichiers présents pour cette catégorie
            $deletedRows = ThereforeFile::where('docNo', $row->DocNo)->delete(); //retourne le nombre de ligne supprimées

            //parcourt de tous les streams
            foreach ($theDocument->GetDocumentResult->StreamsInfo as $stream) {
                $file = new ThereforeFile;
                $file->docNo = $row->DocNo;
                $file->streamNo = $stream->StreamNo;
                $file->categoryNo = $theDocument->GetDocumentResult->IndexData->CategoryNo;
                $file->versionNo = $theDocument->GetDocumentResult->IndexData->VersionNo;
                $file->fileName = $stream->FileName;
                $searchableField = $this->thereforeSearchableField;
                $file->searchableField = $this->$searchableField;

                // TODO: Compare Therefore DB and local DB to keep files already save and increase performance
                /*if (ThereforeFile::where($file->toArray())->first())
                    continue;*/

                $file->saveOrFail();
                $this->transfertFile($file); //download file
            }
        }
    }

    public function listFiles()
    {
        $searchableField = $this->thereforeSearchableField;
        return ThereforeFile::where(['searchableField' => $this->$searchableField])->get();
    }

    private function transfertFile(ThereforeFile $file)
    {
        if (!is_dir($file->getDirPath())) {
            mkdir($file->getDirPath(), 0755, true);
        }
        $response = \Therefore::GetDocumentStream(['parameters' => ['DocNo' => $file->docNo, 'StreamNo' => $file->streamNo]]);
        $data = $response->GetDocumentStreamResult->FileData;
        $size = file_put_contents($file->getFullPath(), $data);
        if ($size)
            $file->update(['size' => $size]);
    }
}