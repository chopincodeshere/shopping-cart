<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductImport implements ToCollection, WithStartRow, WithValidation
{
    /**
     * Handles the import of product data from an Excel file.
     * 
     * Implements the ToCollection, WithStartRow, and WithValidation interfaces
     * to process and validate the data.
     * 
     * @method void collection(Collection $collection) Processes each row of the collection
     *         to create a new Product instance.
     * @method int startRow() Specifies the starting row for data import.
     * @method array rules() Defines validation rules for the imported data.
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $key => $row) {
            Product::create([
                'name'          => $row[0],
                'description'   => $row[1],
                'price'         => $row[2],
                'stock'         => $row[3],
                'admin_id'      => $row[4]
            ]);
        }
    }

    /**
     * Handles the import of product data from an Excel file.
     * 
     * This class implements the ToCollection, WithStartRow, and WithValidation
     * interfaces to process and validate the data during import.
     * 
     * @method void collection(Collection $collection) Processes each row of the collection
     *         to create a new Product instance.
     * @method int startRow() Specifies the starting row for data import.
     * @method array rules() Defines validation rules for the imported data.
     */
    public function startRow(): int
    {
        return 2;
    }

    /**
     * Handles the import of product data from an Excel file.
     * 
     * This class implements the ToCollection, WithStartRow, and WithValidation
     * interfaces to process and validate the data during import.
     * 
     * @method void collection(Collection $collection) Processes each row of the collection
     *         to create a new Product instance.
     * @method int startRow() Specifies the starting row for data import.
     * @method array rules() Defines validation rules for the imported data.
     */
    public function rules(): array
    {
        return [
            'stock' => ['required', 'integer'],
            'price' => ['required', 'numeric'],
            'name' => ['required']
        ];
    }
}
