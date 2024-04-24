<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ImageUploadServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->bind('uploadImage', function ($app) {
            return function ($userId, $img, $imgPath) {
                $fileName = $userId . '_' . time() . '.' . $img->getClientOriginalExtension();
                $img->move(public_path($imgPath), $fileName);
                $imageUrl = getenv('APP_URL') .$imgPath. $fileName;
                return $imageUrl;
            };
        });
    }

}
