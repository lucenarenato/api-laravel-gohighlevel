<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Setting;


class RefreshGhlTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ghl-source-token:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh ghl tokens';

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
     * @return int
     */
    public function handle()
    {
        \Log::info("ghl-source-token:refresh - Refresh All GHL tokens"); 
        $setting = Setting::where('name', 'ghl_integration')->first(); 
        $body = [
            'client_id' => env('GO_HIGH_LEVEL_CLIENT_ID'),
            'client_secret' => env('GO_HIGH_LEVEL_SECRET'),
            'grant_type' => 'refresh_token',
            'refresh_token' => $setting->meta_data['refresh_token'],
        ];
       

        $response = Http::asForm()->post('https://services.leadconnectorhq.com/oauth/token', $body);

        if ($response->successful()) {
            $data = $response->json();
            Setting::updateOrCreate([
                'name' => 'ghl_integration'
            ],
            [
                'name' => 'ghl_integration',
                'value' => $data['access_token'],
                'meta_data' => $data
            ]);
        } else {
            \Log::info("ghl-source-token:refresh - Token Expired"); 
            \Log::info($response->body());
        }
    }
}
