<?php

declare(strict_types=1);

namespace App\Imports;

use App\Models\Promocode;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithUpserts;

/**
 * Class PromocodeImport
 *
 * @package App\Imports
 */
class PromocodeImport implements ToModel, WithBatchInserts, WithChunkReading, WithUpserts
{
    use Importable;

    /**
     * @var
     */
    private $tradeNetworkId;

    /**
     * @var
     */
    private $productId;

    /**
     * @var int
     */
    private $rowCount = 0;

    /**
     * @param $tradeNetworkId
     * @param $productId
     */
    public function __construct($tradeNetworkId, $productId)
    {
        $this->tradeNetworkId = $tradeNetworkId;
        $this->productId = $productId;
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        ++$this->rowCount;

        return new Promocode([
            'code'     => $row[0],
            'trade_network_id'    => $this->tradeNetworkId,
            'product_id' => $this->productId,
            'free' => true,
        ]);
    }

    public function getRowCount(): int
    {
        return $this->rowCount;
    }

    /**
     * @return int
     */
    public function batchSize(): int
    {
        return 5000;
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 5000;
    }

    /**
     * @return string[]
     */
    public function uniqueBy(): array
    {
        return ['code'];
    }
}
