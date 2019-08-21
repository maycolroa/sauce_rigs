<?php

namespace App\Facades\StorageFiles;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Traits\UtilsTrait;
use Carbon\Carbon;
use Exception;
use Session;

class StorageFile
{
    use UtilsTrait;

    /**
     * 
     *
     * @var String
     */
    private $disk;

    public function __construct()
    {
        $this->disk = $this->diskStorageFiles();
    }

    public function storeAs($file)
    {
        
    }

    public function delete($path)
    {
        if (!$path)
            throw new \Exception('Path file required');

        Storage::disk($this->disk)->delete($path);
    }
    
}