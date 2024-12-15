<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class DeleteProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-product';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes soft deleted products and its images after 10 minutes.';

    /**
     * Deletes soft-deleted products that have been in the trash for more than 10 minutes.
     * Outputs the number of products deleted.
     */
    public function handle()
    {
        $deletedCount = Product::onlyTrashed()
            ->where('deleted_at', '<', now()->subMinutes(10))
            ->forceDelete();

        $this->info("Deleted $deletedCount soft-deleted products.");
    }
}
