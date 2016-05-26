<?php

use Illuminate\Database\Seeder;
use App\Models\Codec;

class CodecsSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $codecs = array(
            array('group' => 'common', 'name' => '成功', 'value' => 1),
            array('group' => 'common', 'name' => '失败', 'value' => 2),
        );

        foreach ($codecs as $data) {
            $group = $data['group'];
            $code = $data['value'];
            $codec = Codec::where('group', '=', $group)->where('value', '=', $code)->first();
            if (!$codec) {
                Codec::create($data);
            }
        }
    }

}
