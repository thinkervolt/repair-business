<?php

use Illuminate\Database\Seeder;

class BusinessProfileSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        DB::table('settings')->insert([
            'name' => 'name',
            'data' => 'INCOMPLETE BUSINESS PROFILE - BUSINESS NAME',
            'group' => 'business_profile',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);
        DB::table('settings')->insert([
            'name' => 'phone',
            'data' => 'INCOMPLETE BUSINESS PROFILE - BUSINESS PHONE',
            'group' => 'business_profile',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);
        DB::table('settings')->insert([
            'name' => 'email',
            'data' => 'INCOMPLETE BUSINESS PROFILE - BUSINESS EMAIL',
            'group' => 'business_profile',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);
        DB::table('settings')->insert([
            'name' => 'address',
            'data' => 'INCOMPLETE BUSINESS PROFILE - BUSINESS ADDRESS',
            'group' => 'business_profile',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);
        DB::table('settings')->insert([
            'name' => 'terms',
            'data' => 'INCOMPLETE BUSINESS PROFILE - BUSINESS TERMS',
            'group' => 'business_profile',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);

    }
}
