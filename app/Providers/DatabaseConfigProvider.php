<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use DB;

class DatabaseConfigProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        try {
            if (!\Schema::hasTable('settings')) {
                return;
            }
            $selectpusher = DB::table('settings')->select('*')->first();
        } catch (\Exception $e) {
            return;
        }

        if(isset($selectpusher) && $selectpusher != null){

        //  $this->app['config']->set('PUSHER_APP_KEY', $selectpusher->app_key);
         
        //  $this->app['config']->set('PUSHER_APP_SECRET', $selectpusher->secret);
         
        //  $this->app['config']->set('PUSHER_APP_ID', $selectpusher->app_id);
         
        //  $this->app['config']->set('PUSHER_APP_CLUSTER', $selectpusher->cluster);

            $this->app['config']->set('broadcasting.connections.driver', 'pusher');
            $this->app['config']->set('broadcasting.connections.pusher.key', $selectpusher->app_key);
            $this->app['config']->set('broadcasting.connections.pusher.secret', $selectpusher->secret);
            $this->app['config']->set('broadcasting.connections.pusher.app_id', $selectpusher->app_id);
            $this->app['config']->set('broadcasting.connections.pusher.options.cluster', $selectpusher->cluster);
          
 //dd(config());

         putenv('PUSHER_APP_KEY='.$selectpusher->app_key);
         putenv('PUSHER_APP_SECRET='.$selectpusher->secret);
         putenv('PUSHER_APP_ID='.$selectpusher->app_id);
         putenv('PUSHER_APP_CLUSTER='.$selectpusher->cluster);
         //dd(config());

                     
        }    
       
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        
        //
    }
}
