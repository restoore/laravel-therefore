<?php

namespace Restoore\Therefore\Models;

use Restoore\Therefore\Models\ThereforeFile;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ThereforeDocument extends Model
{
    protected $fillable = ['categoryNo', 'docNo', 'versionNo', 'searchableField', 'lastChangeTime', 'title', 'ctgryName'];
    protected $dates = ['lastChangeTime'];

    public function files()
    {
        return $this->hasMany('Restoore\Therefore\Models\ThereforeFile');
    }

    public function setLastChangeTimeAttribute($value)
    {
        $this->attributes['lastChangeTime'] = Carbon::createFromFormat('Y-m-d\TH:i:s.u\Z', $value);
    }

    public function getDirPath()
    {
        return config('therefore.file_path') . "{$this->categoryNo}/{$this->docNo}";
    }

    public function deleteFromServer()
    {
        foreach ($this->files as $file) {
            // delete files
            $file->deleteFromServer();
            // delete thumbnails
            $file->deleteThumbnail();
        }
        //delete folder
        if(is_dir($this->getDirPath()))
            rmdir($this->getDirPath());
        // delete local DB row
        $this->delete();
    }
}
