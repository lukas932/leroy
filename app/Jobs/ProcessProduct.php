<?php

namespace App\Jobs;

use Excel;
use App\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $path;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = Excel::load($this->path)->get();
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $product = Product::where('lm', '=', $value->lm)->first();
                if (!$product) {
                    $product = new Product();
                }
                $product = new Product();
                $product->lm = $value->lm;
                $product->name = $value->name;
                $product->free_shipping = $value->free_shipping;
                $product->description = $value->description;
                $product->price = $value->price;
                $product->save();
            }
        }
    }
}
