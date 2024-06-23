<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Og\Cruid\Models\DataType;

class DataTypesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     */
    public function run()
    {
        $dataType = $this->dataType('slug', 'users');
        if (!$dataType->exists) {
            $dataType->fill([
                'name'                  => 'users',
                'display_name_singular' => __('cruid::seeders.data_types.user.singular'),
                'display_name_plural'   => __('cruid::seeders.data_types.user.plural'),
                'icon'                  => 'cruid-person',
                'model_name'            => 'Og\\Cruid\\Models\\User',
                'policy_name'           => 'Og\\Cruid\\Policies\\UserPolicy',
                'controller'            => 'Og\\Cruid\\Http\\Controllers\\CruidUserController',
                'generate_permissions'  => 1,
                'description'           => '',
            ])->save();
        }

        $dataType = $this->dataType('slug', 'menus');
        if (!$dataType->exists) {
            $dataType->fill([
                'name'                  => 'menus',
                'display_name_singular' => __('cruid::seeders.data_types.menu.singular'),
                'display_name_plural'   => __('cruid::seeders.data_types.menu.plural'),
                'icon'                  => 'cruid-list',
                'model_name'            => 'Og\\Cruid\\Models\\Menu',
                'controller'            => '',
                'generate_permissions'  => 1,
                'description'           => '',
            ])->save();
        }

        $dataType = $this->dataType('slug', 'roles');
        if (!$dataType->exists) {
            $dataType->fill([
                'name'                  => 'roles',
                'display_name_singular' => __('cruid::seeders.data_types.role.singular'),
                'display_name_plural'   => __('cruid::seeders.data_types.role.plural'),
                'icon'                  => 'cruid-lock',
                'model_name'            => 'Og\\Cruid\\Models\\Role',
                'controller'            => 'Og\\Cruid\\Http\\Controllers\\CruidRoleController',
                'generate_permissions'  => 1,
                'description'           => '',
            ])->save();
        }
    }

    /**
     * [dataType description].
     *
     * @param [type] $field [description]
     * @param [type] $for   [description]
     *
     * @return [type] [description]
     */
    protected function dataType($field, $for)
    {
        return DataType::firstOrNew([$field => $for]);
    }
}
