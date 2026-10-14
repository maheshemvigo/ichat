<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Zipper;
use DB;

class ZipCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:zipnow';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $files = public_path('uploads'); 
        \Zipper::make(public_path('uploads.zip'))->add($files)->close();
        return response()->download(public_path('uploads.zip'));
        $this->info('folder ziped successfully!');
    }
}
