<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

use App\Models\Setting;

class RefreshGhl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:refresh-ghl-integration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $ghl_integration = Setting::where('name', 'ghl_integration')->first();

        $response = Http::asForm()->post('https://services.leadconnectorhq.com/oauth/token', [
            'client_id' => env('GO_HIGH_LEVEL_CLIENT_ID'),
            'client_secret' => env('GO_HIGH_LEVEL_SECRET'),
            'grant_type' => 'refresh_token',
            'refresh_token' => $ghl_integration->meta_data['refresh_token'],
        ]);

        if ($response->successful()) {
            $tokenObj = $response->json();
            $ghl_integration->value = $tokenObj['access_token'];
            $ghl_integration->meta_data = $tokenObj;
            $ghl_integration->save();
        } else {
            \Log::info('==== RefreshGhl - (job) ====');
            \Log::info(json_encode($response->json()));
        }
    }
}
