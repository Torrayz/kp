<?php

use Illuminate\Database\Seeder;

class AboutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $about = new \App\About;
        $about->caption = '<p></p>';
        $about->image = '1580829269_journey.svg';
        $about->save();
        $this->command->info("About berhasil di insert");
    }
}
