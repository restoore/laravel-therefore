<?php

namespace Restoore\Therefore\Models;

use Illuminate\Database\Eloquent\Model;
use Restoore\Therefore\Models\ThereforeDocument;

class ThereforeFile extends Model
{
    protected $fillable = ['therefore_document_id', 'streamNo',  'fileName', 'size'];

    public function exist()
    {
        return file_exists($this->getFullPath());
    }

    public function document()
    {
        return $this->belongsTo('Restoore\Therefore\Models\ThereforeDocument','therefore_document_id');
    }

    public function getFullPath()
    {
        return $this->document->getDirPath() . "/{$this->fileName}";
    }

    public function getFileNameWithoutExtension()
    {
        return pathinfo($this->fileName, PATHINFO_FILENAME);
    }

    public function getExtension()
    {
        return pathinfo($this->fileName, PATHINFO_EXTENSION);
    }

    public function deleteFromServer()
    {
        $this->delete();
        if ($this->exist())
            return unlink($this->getFullPath());

        return false;
    }

    public function getUrl()
    {
        return url($this->getFullPath());
    }

    public function getSizeAttribute($value)
    {
        if ((int)$value / 1000000 > 0) {
            return round((int)$value / 1000000, 1) . ' Mo';
        } else {
            return round((int)$value / 1000, 1) . ' Ko';
        }
    }

    public function getThumbnailUrl()
    {
        $thumbFullPath = $this->document->getDirPath() . "/" . $this->getFileNameWithoutExtension() . '_thumb.jpg';
        if(file_exists($thumbFullPath))
            return url($thumbFullPath);

        if (!is_dir($this->getDirPath()))
            mkdir($this->getDirPath(), 0755, true);

        $response = \Therefore::GetThumbnail(['parameters' => ['DocNo' => $this->document->docNo]]);

        $data = $response->GetThumbnailResult->ThumbnailFileData;

        $size = file_put_contents($thumbFullPath, $data);
        if ($size > 0)
            return url($thumbFullPath);

        return false;
    }

    public function deleteThumbnail()
    {
        $thumbFullPath = $this->document->getDirPath() . "/" . $this->getFileNameWithoutExtension() . '_thumb.jpg';
        if (file_exists($thumbFullPath))
            return unlink($thumbFullPath);
    }

    public function transfert()
    {
        if (!is_dir($this->document->getDirPath())) {
            mkdir($this->document->getDirPath(), 0755, true);
        }
        $response = \Therefore::GetDocumentStream(['parameters' => ['DocNo' => $this->document->docNo, 'StreamNo' => $this->streamNo]]);
        $data = $response->GetDocumentStreamResult->FileData;
        $size = file_put_contents($this->getFullPath(), $data);
        if ($size)
            $this->update(['size' => $size]);
    }
}
