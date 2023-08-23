<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\IdxLog;

/**
 * Class IdxlogRepository
 *
 * @package App\Repositories
 */
final class IdxlogRepository
{
    /**
     * @param int $id
     *
     * @return \App\Models\IdxLog|null
     */
    public function get(int $id): ?IdxLog
    {
        return IdxLog::find($id);
    }

    /**
     * @param \App\Models\IdxLog $model
     * @param array              $data
     *
     * @return \App\Models\IdxLog
     */
    public function update(IdxLog $model, array $data): IdxLog
    {
        $model->update($data);

        return $model;
    }

    /**
     * @param array $data
     */
    public function saveLogs(array $data): void
    {
        IdxLog::create($data);
    }

}
