<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Google Drive Service Account
    |--------------------------------------------------------------------------
    */

    'service_account_path' => env(
        'GDRIVE_SERVICE_ACCOUNT_PATH',
        storage_path('app/google/service-account.json')
    ),

    /*
    |--------------------------------------------------------------------------
    | Root Folder ID (Shared Drive / Subfolder)
    |--------------------------------------------------------------------------
    */

    'root_folder_id' => env('GOOGLE_DRIVE_SHARED_FOLDER_ID'),

];
