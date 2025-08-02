<?php

namespace App\Console\Commands;

use App\Models\Currency;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateCurrencyRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currencies:update {--date= : Specific date in DD.MM.YYYY format}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update currency exchange rates from CBAR API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting currency rates update...');

        try {
            // Get date (today by default or from option)
            $date = $this->option('date') ?: now()->format('d.m.Y');
            $url = "https://cbar.az/currencies/{$date}.xml";

            $this->info("Fetching rates from: {$url}");

            // Make HTTP request to CBAR API
            $response = Http::timeout(30)->get($url);

            if (!$response->successful()) {
                $this->error("Failed to fetch data from CBAR API. Status: {$response->status()}");
                Log::error("CBAR API request failed", [
                    'url' => $url,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return 1;
            }

            $xml = simplexml_load_string($response->body());

            if (!$xml) {
                $this->error('Failed to parse XML response from CBAR API');
                Log::error("CBAR API XML parsing failed", [
                    'url' => $url,
                    'response' => $response->body()
                ]);
                return 1;
            }

            $updatedCount = 0;
            $createdCount = 0;
            $errors = [];

            // First, ensure AZN exists and is at the top
            $aznCurrency = Currency::where('code', 'AZN')->first();
            if (!$aznCurrency) {
                Currency::create([
                    'code' => 'AZN',
                    'course' => 1.0000,
                    'order' => 0,
                    'status' => true
                ]);
                $createdCount++;
                $this->line("Created: AZN = 1.0000");
            } else {
                // Update AZN course to 1.0000 and ensure it's at the top
                $aznCurrency->update([
                    'course' => 1.0000,
                    'order' => 0
                ]);
                $updatedCount++;
                $this->line("Updated: AZN = 1.0000 (set to top)");
            }

            // Process foreign currencies (Xarici valyutalar)
            foreach ($xml->ValType as $valType) {
                $type = (string)$valType['Type'];
                
                if ($type === 'Xarici valyutalar') {
                    foreach ($valType->Valute as $valute) {
                        $code = (string)$valute['Code'];
                        $value = (float)$valute->Value;
                        $nominal = (int)$valute->Nominal;
                        
                        // Skip if no value or invalid code
                        if (empty($value) || empty($code) || strlen($code) !== 3) {
                            continue;
                        }

                        // Calculate rate per 1 unit (normalize by nominal)
                        $rate = $value / $nominal;

                        try {
                            // Find existing currency or create new one
                            $currency = Currency::where('code', $code)->first();

                            if ($currency) {
                                // Update existing currency (keep existing order)
                                $currency->update([
                                    'course' => $rate
                                ]);
                                $updatedCount++;
                                $this->line("Updated: {$code} = {$rate}");
                            } else {
                                // Create new currency (after AZN)
                                $maxOrder = Currency::max('order') ?? 0;
                                Currency::create([
                                    'code' => $code,
                                    'course' => $rate,
                                    'order' => $maxOrder + 1,
                                    'status' => true
                                ]);
                                $createdCount++;
                                $this->line("Created: {$code} = {$rate}");
                            }
                        } catch (\Exception $e) {
                            $errors[] = "Error processing {$code}: " . $e->getMessage();
                            $this->error("Error processing {$code}: " . $e->getMessage());
                        }
                    }
                    break; // Only process foreign currencies
                }
            }

            // Summary
            $this->newLine();
            $this->info("Currency update completed!");
            $this->info("Date: {$date}");
            $this->info("Updated: {$updatedCount} currencies");
            $this->info("Created: {$createdCount} new currencies");
            
            if (!empty($errors)) {
                $this->warn("Errors encountered: " . count($errors));
                foreach ($errors as $error) {
                    $this->error($error);
                }
            }

            // Log the update
            Log::info("Currency rates updated successfully", [
                'date' => $date,
                'updated' => $updatedCount,
                'created' => $createdCount,
                'errors' => count($errors)
            ]);

            return 0;

        } catch (\Exception $e) {
            $this->error("Unexpected error: " . $e->getMessage());
            Log::error("Currency update command failed", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}
