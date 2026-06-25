<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixPostmanCollection extends Command
{
    protected $signature = 'postman:fix';
    protected $description = 'Fix Postman collection path variables format';

    public function handle()
    {
        $path = base_path('public/docs/collection.json');

        if (!file_exists($path)) {
            $this->error('Collection not found at public/docs/collection.json');
            return 1;
        }

        $collection = json_decode(file_get_contents($path), true);

        array_walk_recursive($collection, function (&$value, $key) {
            if (in_array($key, ['path', 'raw'])) {
                $value = preg_replace('/:([a-zA-Z_]+)_id/', ':$1', $value);
            }
            if ($key === 'key' && str_ends_with($value, '_id')) {
                $value = str_replace('_id', '', $value);
            }
            if ($key === 'id' && str_ends_with($value, '_id')) {
                $value = str_replace('_id', '', $value);
            }
        });

        file_put_contents($path, json_encode($collection, JSON_PRETTY_PRINT));
        $this->info('Collection fixed!');
        return 0;
    }
}
