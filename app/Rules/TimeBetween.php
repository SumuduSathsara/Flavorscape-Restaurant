<?php

namespace App\Rules;

use Closure;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\ValidationRule;

class TimeBetween implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
       
       $pickupDate = Carbon::parse($value);
       $pickupTime = Carbon::createFromTime($pickupDate->hour, $pickupDate->minute, $pickupDate->second);
       
       $earliestTime = Carbon::createFromTimeString('17:00:00');
       $lastTime = Carbon::createFromTimeString('23:00:00');

       // Check if the pickup time is not within the allowed range
       if (!$pickupTime->between($earliestTime, $lastTime)) {
           $fail('Please choose a time between 17:00 and 23:00.');
       
        }
    }
}
