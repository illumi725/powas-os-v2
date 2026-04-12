<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeTraitCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:trait {name : The name of the trait}';

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
        $traitName = $this->argument('name');
        $traitPath = app_path("Http/Traits/{$traitName}.php");

        if (File::exists($traitPath)) {
            $this->error("Trait {$traitName} already exists!");
            return;
        }

        $traitContent = $this->getTraitTemplate($traitName);

        File::put($traitPath, $traitContent);

        $this->info("Trait {$traitName} created successfully!");
    }

    protected function getTraitTemplate($traitName)
    {
        // Customize the template as per your needs
        return "<?php

namespace App\Http\Traits;

trait {$traitName}
{
    // Your trait content goes here
}
        ";
    }
}
