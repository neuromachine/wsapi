<?php

namespace App\Models;

#use Fico7489\Laravel\EloquentJoin\Traits\EloquentJoin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class DictionaryItem extends Model
{

    #use EloquentJoin;

    protected $fillable = [
        'id',
        'name',
        'dictionary_id',

        'sort_order',
        'created_at',
        'updated_at'
    ];

    protected $guarded = [
        'id',
        'name',
        'dictionary_id',

        'sort_order',
        'created_at',
        'updated_at'
    ];


    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    protected static function boot()
    {


        parent::boot();


        static::created(function ($model) {
            //

            //ddd(request());
        });


        static::updating(function ($model) {


            $request = request();

            foreach ($request->all() as $key => $value) {

                if (Str::startsWith($key, 'property_')) {
                    $param_id = Str::replace('property_', '', $key);


                    $item_prop = DictionaryItemProperty::updateOrCreate(
                        [
                            'dictionary_item_id' => $model->id,
                            'dictionary_property_id' => $param_id,
                        ],
                        [
                            'value' => $value,
                        ]);

                    $item_prop->save();

                }

            }


            $attributes = $model->getAttributes();

            foreach ($attributes as $key => $value) {

                if (Str::startsWith($key, 'property_')) {
                    unset($attributes[$key]);
                }
            }


            $model->setRawAttributes($attributes, false);


        });


    }


    public function Dictionary()
    {
        return $this->belongsTo(\App\Models\Dictionary::class);
    }


    public function DictionaryItemCategory()
    {
        return $this->belongsToMany(\App\Models\DictionaryItemCategory::class, 'dictionary_items_dictionary_item_categories','dictionary_items_id','dictionary_item_categories_id');
    }



    public function DictionaryItemModels()
    {
        return $this->belongsToMany(\App\Models\ProductModel::class, 'dictionary_item_models','dictionary_item_id','product_models_id');
    }

    public function DictionaryItemProducts()
    {
        return $this->belongsToMany(\App\Models\Product::class, 'dictionary_item_products','dictionary_item_id','product_id');
    }


    /////////////////////////////

    public function getItemProperties(){

        $item = $this;
        $result_item = (object)[];

        $result_item->id = $item->id;
        $result_item->name = $item->name;
        $result_item->key = $item->key;

        $result_item->dictionary_id = $item->Dictionary->id;
        $result_item->dictionary_name = $item->Dictionary->name;
        $result_item->key = $item->Dictionary->key;


        $result_item->categories = [];
        foreach ($item->DictionaryItemCategory as $category) {

            $category_items = (object)[];
            $category_items->id = $category->id ?? '';
            $category_items->parent_id = $category->parent_id ?? '';
            $category_items->name = $category->name ?? '';
            $category_items->key = $category->key ?? '';

            $result_item->categories[] = $category_items;
        }


        $result_item->properties = [];

        foreach ($item->Dictionary->DictionaryProperties as $property) {

            $result_property = (object)[];

            $result_property->property_id = $property->id;
            $result_property->property_key = $property->key;
            $result_property->property_name = $property->name;

            $store_property = \App\Models\DictionaryItemProperty
                ::where('dictionary_item_id', $item->id)
                ->where('dictionary_property_id', $property->id)
                ->value('value');


            if ($store_property) {
                $result_property->value = $store_property;
            } elseif ($property->default_value !== null) {
                $result_property->value = $property->default_value;
            } else {
                $result_property->value = NULL;
            }

            $result_item->properties[$property->key] = $result_property;

        }


        foreach ($result_item->properties as $property) {

            if ($property->property_key === 'eval' && $property->property_name != '') {
                $result_item->properties = $this->DictionaryItemsPropertyMutators($result_item->properties, $property->value);
            }

        }


        return $result_item;

    }


    public function DictionaryItemsPropertyMutators($properties, $type)
    {


        switch ($type):

            case "get_width_values":

                $api = new \App\Services\Verchina1c();
                $result_item = $api->getSerchParamsTyres();
                if (!empty($result_item->width)) {
                    $properties['options'] = $this->convert_for_vueselect($result_item->width);
                }

                break;

            case "get_width_truck_values":

                $api = new \App\Services\Verchina1c();
                $result_item = $api->getSerchParamsTruckTires();
                if (!empty($result_item->width)) {
                    $properties['options'] = $this->convert_for_vueselect($result_item->width);
                }

                break;

            case "get_height_values":

                $api = new \App\Services\Verchina1c();
                $result_item = $api->getSerchParamsTyres();
                if (!empty($result_item->height)) {
                    $arrayReturn = array();
                    foreach ($result_item->height as $arr_height_item) {
                        $arrayReturn[] = $arr_height_item;
                    }
                    $properties['options'] = $this->convert_for_vueselect($arrayReturn);
                }

                break;

            case "get_height_truck_values":

                $api = new \App\Services\Verchina1c();
                $result_item = $api->getSerchParamsTruckTires();
                if (!empty($result_item->height)) {
                    $arrayReturn = array();
                    foreach ($result_item->height as $arr_height_item) {
                        $arrayReturn[] = $arr_height_item;
                    }
                    $properties['options'] = $this->convert_for_vueselect($arrayReturn);
                }

                break;


            case "get_diametr_values":

                $api = new \App\Services\Verchina1c();
                $result_item = $api->getSerchParamsTyres();
                if (!empty($result_item->diameter)) {
                    $properties['options'] = $this->convert_for_vueselect($result_item->diameter);
                }

                break;

            case "get_diametr_truck_values":

                $api = new \App\Services\Verchina1c();
                $result_item = $api->getSerchParamsTruckTires();
                if (!empty($result_item->diameter)) {
                    $properties['options'] = $this->convert_for_vueselect($result_item->diameter);
                }

                break;

            case "get_values_sezon":

                $arrayReturn = array('Летние', 'Зимние', 'Всесезонные'); // TODO: наполнить
                $properties['options'] = $this->convert_for_vueselect($arrayReturn);

                break;

            case "get_values_sub_type":

                $arrayReturn = array('Легкогрузовая', 'Легковая'); // TODO: наполнить
                $properties['options'] = $this->convert_for_vueselect($arrayReturn);

                break;

            case "get_values_brand":

                $api = new \App\Services\Verchina1c();
                $result_item = $api->getSerchParamsTyres();
                if (!empty($result_item->brand)) {
                    $properties['options'] = $this->convert_for_vueselect($result_item->brand);
                }

                break;

            case "get_brand_truck_values":

                $api = new \App\Services\Verchina1c();
                $result_item = $api->getSerchParamsTruckTires();
                if (!empty($result_item->brand)) {
                    $properties['options'] = $this->convert_for_vueselect($result_item->brand);
                }

                break;

            case "get_axle_truck_values":

                $api = new \App\Services\Verchina1c();
                $result_item = $api->getSerchParamsTruckTires();
                if (!empty($result_item->axle)) {
                    $properties['options'] = $this->convert_for_vueselect($result_item->axle);
                }

                break;

            case "get_ply_rate_truck_values":

                $api = new \App\Services\Verchina1c();
                $result_item = $api->getSerchParamsTruckTires();
                if (!empty($result_item->ply_rate)) {
                    $properties['options'] = $this->convert_for_vueselect($result_item->ply_rate);
                }

                break;

            case "get_values_spikes":

                $arrayReturn = array(array('title' => 'Есть', 'value' => 1), array('title' => 'Нет', 'value' => 2));
                $properties['options'] = $this->convert_for_vueselect($arrayReturn);

                break;


            case "get_values_protect":

                $arrayReturn = array(array('title' => 'Есть', 'value' => 1), array('title' => 'Нет', 'value' => 2)); // TODO: наполнить
                $properties['options'] = $this->convert_for_vueselect($arrayReturn);

                break;

            case "get_tools_sub_category_values":

                $api = new \App\Services\Verchina1c();
                $result_item = $api->getSerchParamsTools();
                if (!empty($result_item->tools_sub_category)) {
                    $properties['options'] = $this->convert_for_vueselect($result_item->tools_sub_category);
                }

                break;

            case "get_rims_width_values":

                $api = new \App\Services\Verchina1c();
                $result_item = $api->getSerchParamsRims();
                if (!empty($result_item->rims_width)) {
                    $properties['options'] = $this->convert_for_vueselect($result_item->rims_width);
                }

                break;

            case "get_rims_height_values":

                $api = new \App\Services\Verchina1c();
                $result_item = $api->getSerchParamsRims();
                if (!empty($result_item->rims_height)) {
                    $properties['options'] = $this->convert_for_vueselect($result_item->rims_height);
                }

                break;

            case "get_rims_count_bolt_values":

                $api = new \App\Services\Verchina1c();
                $result_item = $api->getSerchParamsRims();
                if (!empty($result_item->rims_count_bolt)) {
                    $properties['options'] = $this->convert_for_vueselect($result_item->rims_count_bolt);
                }

                break;

            case "get_rims_distance_bolt_values":

                $api = new \App\Services\Verchina1c();
                $result_item = $api->getSerchParamsRims();
                if (!empty($result_item->rims_distance_bolt)) {
                    $properties['options'] = $this->convert_for_vueselect($result_item->rims_distance_bolt);
                }

                break;

            case "get_rims_et_values":

                $api = new \App\Services\Verchina1c();
                $result_item = $api->getSerchParamsRims();
                if (!empty($result_item->rims_et)) {
                    $properties['options'] = $this->convert_for_vueselect($result_item->rims_et);
                }

                break;

            case "get_rims_brand_values":

                $api = new \App\Services\Verchina1c();
                $result_item = $api->getSerchParamsRims();
                if (!empty($result_item->rims_brand)) {
                    $properties['options'] = $this->convert_for_vueselect($result_item->rims_brand);
                }

                break;

            case "get_rims_type_values":

                $api = new \App\Services\Verchina1c();
                $result_item = $api->getSerchParamsRims();
                if (!empty($result_item->rims_type)) {
                    $properties['options'] = $this->convert_for_vueselect($result_item->rims_type);
                }

                break;

            case "get_rims_hub_values":

                $api = new \App\Services\Verchina1c();
                $result_item = $api->getSerchParamsRims();
                if (!empty($result_item->rims_hub)) {
                    $properties['options'] = $this->convert_for_vueselect($result_item->rims_hub);
                }

                break;

            case "get_rims_color_values":

                $api = new \App\Services\Verchina1c();
                $result_item = $api->getSerchParamsRims();
                if (!empty($result_item->rims_color)) {
                    $properties['options'] = $this->convert_for_vueselect($result_item->rims_color);
                }

                break;

            case "get_moto_width_values":

                $api = new \App\Services\Verchina1c();
                $result_item = $api->getSerchParamsMotoTyres();
                if (!empty($result_item->width)) {

                    $properties['options'] = $this->convert_for_vueselect($result_item->width);
                }

                break;

            case "get_moto_height_values":

                $api = new \App\Services\Verchina1c();
                $result_item = $api->getSerchParamsMotoTyres();
                if (!empty($result_item->height)) {
                    $properties['options'] = $this->convert_for_vueselect($result_item->height);
                }

                break;

            case "get_moto_diameter_values":

                $api = new \App\Services\Verchina1c();
                $result_item = $api->getSerchParamsMotoTyres();
                if (!empty($result_item->diameter)) {
                    $properties['options'] = $this->convert_for_vueselect($result_item->diameter);
                }

                break;

            case "get_moto_brand_values":

                $api = new \App\Services\Verchina1c();
                $result_item = $api->getSerchParamsMotoTyres();
                if (!empty($result_item->brand)) {
                    $properties['options'] = $this->convert_for_vueselect($result_item->brand);
                }

                break;

            case "get_industrial_width_values":

                $api = new \App\Services\Verchina1c();
                $result_item = $api->getSerchParamsMotoTyres();
                if (!empty($result_item->width)) {
                    $properties['options'] = $this->convert_for_vueselect($result_item->width);
                }

                break;

            case "get_industrial_height_values":

                $api = new \App\Services\Verchina1c();
                $result_item = $api->getSerchParamsIndustrialTyres();
                if (!empty($result_item->height)) {
                    $properties['options'] = $this->convert_for_vueselect($result_item->height);
                }

                break;

            case "get_industrial_diameter_values":

                $api = new \App\Services\Verchina1c();
                $result_item = $api->getSerchParamsIndustrialTyres();
                if (!empty($result_item->diameter)) {
                    $properties['options'] = $this->convert_for_vueselect($result_item->diameter);
                }

                break;

            case "get_industrial_brand_values":

                $api = new \App\Services\Verchina1c();
                $result_item = $api->getSerchParamsIndustrialTyres();
                if (!empty($result_item->brand)) {
                    $properties['options'] = $this->convert_for_vueselect($result_item->brand);
                }

                break;

            case "get_components_subtypes_values":

                $arrayReturn = array(
                    array('title' => 'Болты', 'value' => 'bolts'),
                    array('title' => 'Вентили', 'value' => 'valves'),
                    array('title' => 'Гайки', 'value' => 'nuts'),
                    array('title' => 'Груза', 'value' => 'burden'),
                    array('title' => 'Датчики давления', 'value' => 'sensors'),
                    array('title' => 'Камеры', 'value' => 'cameras'),
                    array('title' => 'Латки', 'value' => 'patches'),
                    array('title' => 'Ободные ленты', 'value' => 'rim_tapes'),
                    array('title' => 'Пакеты', 'value' => 'packs'),
                    array('title' => 'Проставочные кольца', 'value' => 'rings'),

                    array('title' => 'Тормозные колодки', 'value' => 'brake_pads'),
                    array('title' => 'Тормозные диски', 'value' => 'brake_disc'),
                    array('title' => 'Датчик тормозные колодок', 'value' => 'brake_sensor'),
                    array('title' => 'Фильтра', 'value' => 'filters'),
                    array('title' => 'Расходные материалы', 'value' => 'consumables'),
                    array('title' => 'Автомобильные принадлежности', 'value' => 'accessories'),


                ); // TODO: создать на основе данных!
                $properties['options'] = $this->convert_for_vueselect($arrayReturn);

                break;

            case "get_accessories_subtypes_values":

                $arrayReturn = array(
                    array('title' => 'Паста Ш/М', 'value' => 'tire_paste'),
                    array('title' => 'Химия', 'value' => 'chemicals'),

                    array('title' => 'Смазки', 'value' => 'lubricant'),
                    array('title' => 'Масла', 'value' => 'oil'),
                ); // TODO: создать на основе данных!
                $properties['options'] = $this->convert_for_vueselect($arrayReturn);

                break;

            case "get_clothes_subtypes_values":

                $arrayReturn = array(
                    array('title' => 'Ботинки', 'value' => 'shoes'),
                    array('title' => 'Рабочая одежда', 'value' => 'workwear'),
                ); // TODO: создать на основе данных!
                $properties['options'] = $this->convert_for_vueselect($arrayReturn);

                break;


        endswitch;

        return $properties;
    }


    public function convert_for_vueselect($inter_array = array())
    {
        $return_array = array();
        foreach ($inter_array as $key => $value) {
            //if(is_numeric($key))
            if (!is_array($value)) {
                $return_array[] = array('title' => $value, 'value' => $value);
            } else {
                if (!empty($value['title']) && !empty($value['value'])) {
                    $return_array[] = array('title' => $value['title'], 'value' => $value['value']);
                }
            }
        }
        return $return_array;
    }

}
