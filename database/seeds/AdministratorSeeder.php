<?php

use Illuminate\Database\Seeder;

class AdministratorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $administrator = new \App\User;
        $administrator->name        = "Administrator";
        $administrator->email       = "tripujiantoro12@gmail.com";
        $administrator->password    = \Hash::make("torray");
        $administrator->save();
        $this->command->info("User Admin berhasil diinsert");
    }
}
