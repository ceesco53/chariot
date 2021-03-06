<?php

namespace App\Console\Commands\Cf;

use Exception;
use Illuminate\Console\Command;
use Symfony\Component\Yaml\Yaml;

class SetSecretsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:set-secrets 
                            {--show : Display the secrets instead of modifying files}
                            {--path=secrets.yml : The path of the secrets file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sets the secrets missing in the Cloud Foundry secrets file with environment variables';

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Exception
     */
    public function handle()
    {
        $path = $this->option('path');
        $show = $this->option('show');

        // Parse the YAML into an array.
        $secrets = Yaml::parseFile($path);

        // Loop through each key.
        foreach ($secrets as $key => &$secret) {
            $matches = [];

            // Check to see if the value should be replaced with the following syntax: `KEY: ${ENV_VAR}`.
            $isReplaceable = preg_match('/^\${(.+)}$/', $secret, $matches) >= 1;
            if (!$isReplaceable) {
                continue;
            }

            // Check if the environment variable exists and set to null if it doesn't.
            $envName = $matches[1];
            $secret = env($envName, null);
        }

        // Export the array back to a YAML string.
        $contents = Yaml::dump($secrets);

        // Either output the contents to the terminal or save to the file.
        if ($show) {
            $this->warn($contents);
        } else {
            file_put_contents($path, $contents);
            $this->info("$path has been updated with the environment variables inserted.");
        }

        return true;
    }
}
