<?php
namespace HashtagCms\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrenciesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table_name = 'currencies';
        $date = date('Y-m-d H:i:s');
        $currencies = array(
            array('id' => '1','name' => 'US Dollar','iso_code' => 'USD','iso_code_num' => '840','sign' => '$','blank' => '1','format' => '1','decimals' => '1','conversion_rate' => '1.000000','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '2','name' => 'Dirhams','iso_code' => 'AED','iso_code_num' => '784','sign' => 'د.إ','blank' => '1','format' => '1','decimals' => '1','conversion_rate' => '0.272479','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '3','name' => 'Bahraini Dinars','iso_code' => 'BHD','iso_code_num' => '048','sign' => '.د.ب','blank' => '1','format' => '1','decimals' => '1','conversion_rate' => '2.655900','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '4','name' => 'Indian Rupees','iso_code' => 'INR','iso_code_num' => '356','sign' => '₹','blank' => '1','format' => '1','decimals' => '1','conversion_rate' => '0.021730','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '5','name' => 'Jordanian Dinars','iso_code' => 'JOD','iso_code_num' => '400','sign' => 'JD','blank' => '1','format' => '1','decimals' => '1','conversion_rate' => '1.412030','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '6','name' => 'Kuwaiti Dinars','iso_code' => 'KWD','iso_code_num' => '414','sign' => 'د.ك','blank' => '1','format' => '1','decimals' => '1','conversion_rate' => '3.457810','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '7','name' => 'Lebanese Pounds','iso_code' => 'LBP','iso_code_num' => '422','sign' => 'LL','blank' => '1','format' => '1','decimals' => '1','conversion_rate' => '0.000692','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '8','name' => 'Omani Riyals','iso_code' => 'QMR','iso_code_num' => '512','sign' => 'ر.ع.','blank' => '1','format' => '1','decimals' => '1','conversion_rate' => '2.597070','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '9','name' => 'Qatari Riyals','iso_code' => 'QAR','iso_code_num' => '634','sign' => 'ر.ق','blank' => '1','format' => '1','decimals' => '1','conversion_rate' => '0.274820','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '10','name' => 'Saudi Riyals','iso_code' => 'SAR','iso_code_num' => '682','sign' => 'ر.س','blank' => '1','format' => '1','decimals' => '1','conversion_rate' => '0.266670','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '11','name' => 'Hong Kong Dollar','iso_code' => 'HKD','iso_code_num' => '344','sign' => '$','blank' => '1','format' => '1','decimals' => '1','conversion_rate' => '0.127980','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '12','name' => 'Singapore Dollar','iso_code' => 'SGD','iso_code_num' => '702','sign' => '$','blank' => '1','format' => '1','decimals' => '1','conversion_rate' => '0.649930','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '13','name' => 'Indonesian Rupiah','iso_code' => 'IDR','iso_code_num' => '360','sign' => 'Rp','blank' => '1','format' => '1','decimals' => '1','conversion_rate' => '0.000110','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '14','name' => 'Philippine Peso','iso_code' => 'PHP','iso_code_num' => '608','sign' => '₱','blank' => '1','format' => '1','decimals' => '1','conversion_rate' => '0.020780','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '15','name' => 'Thai Baht','iso_code' => 'THB','iso_code_num' => '764','sign' => '฿','blank' => '1','format' => '1','decimals' => '1','conversion_rate' => '1.000000','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '16','name' => 'Vietnamese Dong','iso_code' => 'VND','iso_code_num' => '704','sign' => '₫','blank' => '1','format' => '1','decimals' => '1','conversion_rate' => '0.000064','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '17','name' => 'Yuan Renminbi','iso_code' => 'CNY','iso_code_num' => '156','sign' => '¥','blank' => '1','format' => '1','decimals' => '1','conversion_rate' => '1.000000','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '18','name' => 'Ringgit','iso_code' => 'MYR','iso_code_num' => '458','sign' => 'RM','blank' => '1','format' => '1','decimals' => '1','conversion_rate' => '0.286200','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '19','name' => 'Taiwan Dollar','iso_code' => 'TWD','iso_code_num' => '901','sign' => '$','blank' => '1','format' => '1','decimals' => '1','conversion_rate' => '0.030320','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '20','name' => 'Pataca','iso_code' => 'MOP','iso_code_num' => '446','sign' => 'MOP$','blank' => '1','format' => '1','decimals' => '1','conversion_rate' => '0.129830','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL)
        );

        if(DB::table($table_name)->get()->count() == 0) {
            DB::table($table_name)->insert($currencies);
        } else {
            echo "SeedingError: `$table_name` table is not empty\n";
        }
    }
}
