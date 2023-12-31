<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class File extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getIsValidAttribute(): bool
    {
        $lines = explode(PHP_EOL, Storage::get($this['path'] . '/' . $this['name']));
        $count = 0;
        foreach ($lines as $line) {
            $line = str_replace(["\r", '+', ' '], '', $line);
            if ($line === '' || $line === 'CountryCode,PhoneNumber,IsLocal,Amount') {
                continue;
            }
            $item = explode(',', $line);
            if (count($item) !== 4) {
                continue;
            }

            $count++;
        }
        if ($count !== 0) {
            return true;
        }

        $this['status'] = 'INVALID';
        $this->save();

        return false;
    }

    /**
     * @throws \Exception
     */
    public function processNumbers(): void
    {
        if ($this['status'] !== 'PROCESSING') {
            return;
        }
        $lines = explode(PHP_EOL, Storage::get($this['path'] . '/' . $this['name']));
        foreach ($lines as $line) {
            $line = str_replace(["\r", '+', ' '], '', $line);
            if ($line === '' || $line === 'CountryCode,PhoneNumber,IsLocal,Amount') {
                continue;
            }
            $item = explode(',', $line);
            if (count($item) !== 4) {
                continue;
            }
            $operator = System::autoDetectOperator($item[1], $item[0], $this['id']);
            FileEntry::create([
                'file_id' => $this['id'],
                'country_id' => Country::where('iso', $item[0])->first()['id'],
                'operator_id' => $operator ? $operator['id'] : 0,
                'is_local' => $item[2] !== '0',
                'amount' => (float) $item[3],
                'number' => (float) $item[1],
            ]);
            sleep(random_int(0, 2));
        }
        $this['status'] = 'START';
        $this->save();
    }

    public function topups(): HasMany
    {
        return $this->hasMany(Topup::class);
    }

    public function numbers(): HasMany
    {
        return $this->hasMany(FileEntry::class);
    }

    public function getTotalAmountAttribute(): float
    {
        $amount = 0.0;
        foreach ($this['numbers'] as $number) {
            if ($number['is_local'] && isset($number['operator'])) {
                $amount += ($number['amount'] / $number['operator']['fx_rate']);
            } else {
                $amount += $number['amount'];
            }
        }

        return round($amount, 2);
    }
}
