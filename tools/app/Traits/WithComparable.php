<?php

namespace App\Traits;

use App\Data\Enums\SyncDirections;
use App\Interfaces\Comparable;

trait WithComparable
{
    public function compare(Comparable $other) : SyncDirections
    {
        $shouldDownload = $this->getComparisonDate()->lt($other->getComparisonDate());
        $comparisonKeys = array_keys($this->toArray());

        if ($shouldDownload)
        {
            return SyncDirections::DOWNLOAD;
        } else {
            foreach($comparisonKeys as $key)
            {
                if(data_get($this->getComparisonData(), $key) !== data_get($other->getComparisonData(), $key)){
                    return SyncDirections::UPLOAD;
                }
            }

            return SyncDirections::NO_ACTION;
        }
    }
}
