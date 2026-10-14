<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/broadcasting/auth',
        'auth/*',
        '/login',
        '/simulationcreate',
        '/simulation_history',
        '/simulation_history_download',
        '/simulation_history_delete',
        '/regeneratepin',
        '/get_simulation',
        '/token_creation',
        '/print_token',
        '/nameupdate',
        '/simulationnamecheck',
        '/simulationlist',
        '/updatesimulationnamecheck',
        '/simulationsave',
        '/simulationupdate',
        '/simulationedit',
        '/settings',
        '/settingsupdate',
        '/pushercre',
        '/handlezip',
        '/resetipaddress',
        '/links',
        '/add_link',
        '/fileupload_store',
        '/file_delete',
        '/link_delete',
        '/linkupdate',
        '/imageupload_store',
        '/upload/storevideo',
        '/upload/storeimagepage',
        '/image_upload',
        '/file_upload',
        '/videoupload',
        '/videosearch',
        '/imagesearch',
        '/documentsearch',
        '/featuredvideo/search',
        '/featureddoc/search',
        '/featuredimg/search'

    ];
}
