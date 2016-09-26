# laravel-therefore
This a package to integrate Therefore Webservice with Laravel 5. It includes a ServiceProvider to register the webservice. You also have facade to use increase the webservice usage.

#	Installation for Laravel 5.x
Require this package with composer :
```
composer require restoore/laravel-therefore
```
After updating composer, add the ServiceProvider to the providers array in config/app.php
```php
Restoore\Therefore\ThereforeServiceProvider::class
```
If you want to use the facade add this to your facades in your app.php
```php
'Therefore' => Restoore\Therefore\Facades\Therefore::class
```
Copy the package config to your local config with the publish command :
```
php artisan vendor:publish --provider="Restoore\Therefore\ThereforeServiceProvider" --tag="config"
```
Copy migration files to your local migration folder with the publish command :
```
php artisan vendor:publish --provider="Restoore\Therefore\ThereforeServiceProvider" --tag="migrations"
```
Launch migration :
```
php artisan migrate
```

## Configuration
If you have used the following command :
```
php artisan vendor:publish --provider="Restoore\Therefore\ThereforeServiceProvider" --tag="config"
```
You've a therefore.php in your local config path, by default : /config/therefore.php, if not, you have to create this file. In this file, you have four parameters that have to be returned in an array :
* **wsdl**, url of your therefore wsld file
* **login**, your therefore account login
* **password**; your threfore account password
* **file_path**, file location where your want to save files and thumbnails. All files will be saved in your laravel public folder. For exemple, if you put docs/therefore/, all files we be created in public/docs/therefore/

### Environment
If you want to use different environments of development, you can override wsdl, login and password parameters by using **.env** file.
```php
THEREFORE_WSDL=
THEREFORE_LOGIN=
THEREFORE_PASSWORD=
```

## Usage
You can now connect your model to Therefore document by using php traits. First, you have to add ThereforeTrait to your model :
```php
use \Restoore\Therefore\ThereforeTrait;
```
Now you have to configure attributes to bind your model to Therefore documents :
  * $thereforeCategoryNo will contain CategoryNo from your Therefore document
  * $thereforeFieldNo will contain the id of the field you want to use to link Therefore document and your model
  * $thereforeSearchableField will contain the name of your model attribute witch will be used to linked your model
This should looks like this :
```php
    //therefore
    use \Restoore\Therefore\ThereforeTrait;
    protected $thereforeCategoryNo = 8;
    protected $thereforeFieldNo = 92;
    protected $thereforeSearchableField = 'id';
```
In this example the model id have to be the same that the value in the field 92 of the Therefore document.

### Retrieve documents and files
Now you can use function **listDocuments** to retrieve all linked documents of your model. For example :
```php
$class->refreshCacheFiles();
dd($class->listDocuments());
```
will produce for my example :
```php
Collection {#545 ▼
  #items: array:1 [▼
    0 => ThereforeDocument {#539 ▼
      #fillable: array:7 [▼
        0 => "categoryNo"
        1 => "docNo"
        2 => "versionNo"
        3 => "searchableField"
        4 => "lastChangeTime"
        5 => "title"
        6 => "ctgryName"
      ]
     ...
    }
  ]
}
```
**refreshCacheFiles** will check if you have last version of documents and update database and files. You can decide to not use this function everytime and let user manually refresh file list.

Now if you want files of a document :
```php
@foreach($documents as $document)
    @foreach($document->files as $file)
		...
	@endforeach
@endforeach
```
Here you have file model structure :
```php
ThereforeFile {#554 ▼
  #fillable: array:4 [▼
    0 => "therefore_document_id"
    1 => "streamNo"
    2 => "fileName"
    3 => "size"
  ]
  ...
    }
  ]
}
```
All attributes of ThereforeDocument and ThereforeFile can be reachable with their names. For example :
```php
$thereforedocument->categoryNo
$thereforedocument->lastChangeTime
$thereforedocument->ctgryName
$thereforefile->fileName
$thereforefile->size
```
#### Some useful functions from ThereforeFile model
```php
  public function getFullPath()						//Get full server path
  public function getFileNameWithoutExtension() 	//Get filename without his extension
  public function getExtension()					//Only get the extension
  public function deleteFromServer()				//Delete file from web server
  public function getUrl()							//Return full link of your file
  public function getSizeAttribute($value)			//Return formatted size of your file like "16.5 Mo" or "500 ko"
  public function getThumbnailUrl()					//Return thumbnail url and if thumbnail doesn t exist create him
  public function deleteThumbnail()					//Delete thumbnail from web server
```