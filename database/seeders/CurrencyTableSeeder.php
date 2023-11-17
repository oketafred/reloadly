<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currencies = [
            ['name' =>'US dollar', 'abbr' =>'USD', 'symbol' =>'$'],
            ['name' =>'Canadian dollars', 'abbr' =>'CAD', 'symbol' =>'$'],
            ['name' =>'Pounds sterling', 'abbr' =>'GBP', 'symbol' =>'£'],
            ['name' =>'Euro', 'abbr' =>'EUR', 'symbol' =>'€'],
            ['name' =>'Australian dollars', 'abbr' =>'AUD', 'symbol' =>'$'],
            ['name' =>'Bangladeshi taka', 'abbr' =>'BDT', 'symbol' =>'৳'],
            ['name' =>'Brazilian real', 'abbr' =>'BRL', 'symbol' =>'R$'],
            ['name' =>'Bulgarian lev', 'abbr' =>'BGN', 'symbol' =>'лв'],
            ['name' =>'Chilean peso', 'abbr' =>'CLP', 'symbol' =>'$'],
            ['name' =>'Chinese yuan', 'abbr' =>'CNY', 'symbol' =>'¥ /元'],
            ['name' =>'Colombian peso', 'abbr' =>'COP', 'symbol' =>'$'],
            ['name' =>'Croatian Kuna', 'abbr' =>'HRK', 'symbol' =>'kn'],
            ['name' =>'Czech koruna', 'abbr' =>'CZK', 'symbol' =>'Kč'],
            ['name' =>'Danish krone', 'abbr' =>'DKK', 'symbol' =>'kr'],
            ['name' =>'Emirati dirham', 'abbr' =>'AED', 'symbol' =>'د.إ'],
            ['name' =>'Georgian lari', 'abbr' =>'GEL', 'symbol' =>'₾'],
            ['name' =>'Hong Kong dollar', 'abbr' =>'HKD', 'symbol' =>'$ / HK$ / “元”'],
            ['name' =>'Hungarian forint', 'abbr' =>'HUF', 'symbol' =>'ft'],
            ['name' =>'Indian rupee', 'abbr' =>'INR', 'symbol' =>'₹'],
            ['name' =>'Indonesian rupiah', 'abbr' =>'IDR', 'symbol' =>'Rp'],
            ['name' =>'Israeli shekel', 'abbr' =>'ILS', 'symbol' =>'₪'],
            ['name' =>'Japanese yen', 'abbr' =>'JPY', 'symbol' =>'¥'],
            ['name' =>'Kenyan shilling', 'abbr' =>'KES', 'symbol' =>'Ksh'],
            ['name' =>'Malaysian ringgit', 'abbr' =>'MYR', 'symbol' =>'RM'],
            ['name' =>'Mexican peso', 'abbr' =>'MXN', 'symbol' =>'$'],
            ['name' =>'Moroccan dirham', 'abbr' =>'MAD', 'symbol' =>'.د.م'],
            ['name' =>'New Zealand dollar', 'abbr' =>'NZD', 'symbol' =>'$'],
            ['name' =>'Nigerian naira', 'abbr' =>'NGN', 'symbol' =>'₦'],
            ['name' =>'Norwegian krone', 'abbr' =>'NOK', 'symbol' =>'kr'],
            ['name' =>'Pakistani rupee', 'abbr' =>'PKR', 'symbol' =>'Rs'],
            ['name' =>'Peruvian sol', 'abbr' =>'PEN', 'symbol' =>'S/.'],
            ['name' =>'Philippine peso', 'abbr' =>'PHP', 'symbol' =>'₱'],
            ['name' =>'Polish zloty', 'abbr' =>'PLN', 'symbol' =>'zł'],
            ['name' =>'Romanian leu', 'abbr' =>'RON', 'symbol' =>'lei'],
            ['name' =>'Russian ruble', 'abbr' =>'RUB', 'symbol' =>'₽'],
            ['name' =>'Singapore dollar', 'abbr' =>'SGD', 'symbol' =>'$'],
            ['name' =>'South Korean won', 'abbr' =>'KRW', 'symbol' =>'₩'],
            ['name' =>'Sri Lankan rupee', 'abbr' =>'LKR', 'symbol' =>'Rs'],
            ['name' =>'Swedish krona', 'abbr' =>'SEK', 'symbol' =>'kr'],
            ['name' =>'Swiss franc', 'abbr' =>'CHF', 'symbol' =>'CHf'],
            ['name' =>'Thai baht', 'abbr' =>'THB', 'symbol' =>'฿'],
            ['name' =>'Turkish lira', 'abbr' =>'TRY', 'symbol' =>'₺'],
            ['name' =>'Ukrainian hryvna', 'abbr' =>'UAH', 'symbol' =>'₴'],
            ['name' =>'Vietnamese dong', 'abbr' =>'VND', 'symbol' =>'₫'],
        ];
        foreach ($currencies as $currency) {
            Currency::query()->updateOrCreate([
                'name' => $currency['name'], 'abbr' => $currency['abbr'], 'symbol' => $currency['symbol'],
            ]);
        }
    }
}
