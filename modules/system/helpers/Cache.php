<?php namespace System\Helpers;

use App;
use File;
use Cache as CacheFacade;
use Config;

class Cache
{
    use \October\Rain\Support\Traits\Singleton;

    /**
     * Execute the console command.
     */
    public static function clear()
    {
        CacheFacade::flush();
        self::clearInternal();
    }

    public static function clearInternal()
    {
        $instance = self::instance();
        $instance->clearCombiner();
        $instance->clearCache();

        if (!Config::get('cms.twigNoCache')) {
            $instance->clearTwig();
        }

        $instance->clearMeta();
    }

    /*
     * Combiner
     */
    public function clearCombiner()
    {
        self::clearDir('/cms/combiner');
    }

    /*
     * Cache
     */
    public function clearCache()
    {
        self::clearDir('/cms/cache');
    }

    /*
     * Twig
     */
    public function clearTwig()
    {
        self::clearDir('/cms/twig');
    }
    /**
     * Generic function for clearing directories
     * Will check if directory exists before clearing it. If it does not,
     * it will create it accordingly.
     */
    private static function clearDir($path)
    {
        $storagePath = storage_path().$path;
        if (!file_exists($storagePath)) {
            mkdir($storagePath, octdec('0'.config('cms.defaultMask.folder')), true);
        }
        foreach (File::directories($storagePath) as $directory) {
            File::deleteDirectory($directory);
        }
    }
    /*
     * Meta
     */
    public function clearMeta()
    {
        File::delete(storage_path().'/cms/disabled.json');
        File::delete(App::getCachedCompilePath());
        File::delete(App::getCachedServicesPath());
    }
}
