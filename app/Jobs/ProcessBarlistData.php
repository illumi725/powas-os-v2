<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessBarlistData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $barlist = storage_path('app/bar_list.json');
        $processedData = $this->processBarlist($barlist);
    }

    private function processBarlist($barlist)
    {
        $jsonData = json_decode(file_get_contents($barlist), true);

        $datalist = [];

        foreach ($jsonData as $regionID => $regionData) {
            $regionName = $regionData['region_name'];

            foreach ($regionData['province_list'] as $provinceName => $provinceData) {
                foreach ($provinceData['municipality_list'] as $municipalityName => $municipalityData) {
                    foreach ($municipalityData['barangay_list'] as $barangayName) {
                        $datalist[] = "$barangayName, $provinceName, $municipalityName - $regionName";
                    }
                }
            }
        }

        return $datalist;
    }
}
