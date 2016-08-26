<?php

namespace Restoore\Therefore;

use Illuminate\Database\Eloquent\Model;

class ThereforeFile extends Model
{
    protected $fillable = ['categoryNo', 'docNo', 'streamNo', 'versionNo', 'fileName', 'searchableField', 'size'];

    public function exist()
    {
        return file_exists($this->getFullPath());
    }

    public function getFullPath()
    {
        return $this->getDirPath() . "/{$this->fileName}";
    }

    public function getDirPath()
    {
        return config('therefore.file_path') . "{$this->categoryNo}/{$this->docNo}-{$this->streamNo}";
    }

    public function deleteFromServer()
    {
        if ($this->isDownloaded())
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
}
