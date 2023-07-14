<?php

namespace App\Interfaces;

use App\Data\Enums\SyncDirections;
use Carbon\Carbon;

interface Comparable {
    public function getComparisonDate() : Carbon;
    public function getComparisonData(): array;
    public function compare(Comparable $comparable) : SyncDirections;
}
