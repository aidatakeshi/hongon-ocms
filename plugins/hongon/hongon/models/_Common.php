<?php namespace Hongon\Hongon\Models;

class _Common{

    public static $class = [

        'ref-image' => \Hongon\Hongon\Models\RefImage::class,

        'region-broader' => \Hongon\Hongon\Models\RegionBroader::class,
        'region' => \Hongon\Hongon\Models\Region::class,

        'operator-type' => \Hongon\Hongon\Models\OperatorType::class,
        'operator' => \Hongon\Hongon\Models\Operator::class,
    
        'line-type' => \Hongon\Hongon\Models\LineType::class,
        'line' => \Hongon\Hongon\Models\Line::class,
        'line-section' => \Hongon\Hongon\Models\LineSection::class,

        'station' => \Hongon\Hongon\Models\Station::class,

    ];

    public static $validation_error_messages = [

        'exists' => 'Not Exists',
        'gt' => 'Value Too Small',
        'gte' => 'Value Too Small',
        'lt' => 'Value Too Large',
        'lte' => 'Value Too Large',
        'min' => 'Too Short',
        'max' => 'Too Long',
        'size' => 'Incorrect Length',
        'string' => 'String Required',
        'uuid' => 'UUID Required',
        'integer' => 'Integer Required',
        'json' => 'JSON Required',
        'numeric' => 'Numeric Required',
        'boolean' => 'Boolean Required',
        'regex' => 'Invalid Format',
        'required' => 'Required',
        'filled' => 'Should Not Be Empty',
        'unique' => 'Should Be Unique',
        
    ];

    //Shared functions
    public static function keepOnlyAttributes($results, $keep_attributes, $inside_attribute = null){
        foreach ($results as $i => $result){
            if (!$inside_attribute){
                $results[$i] = self::keepOnlyAttributesForOne($result, $keep_attributes);
            }else if (isset($result[$inside_attribute])){
                $results[$i][$inside_attribute] = self::keepOnlyAttributesForOne($result[$inside_attribute], $keep_attributes);
            }
        }
        return $results;
    }

    public static function keepOnlyAttributesForOne($result, $keep_attributes){
        $item = [];
        if (isset($result['id'])){
            $item['id'] = $result['id'];
        }
        foreach ($keep_attributes as $attr){
            if (isset($result[$attr])){
                $item[$attr] = $result[$attr];
            }
        }
        return $item;
    }

    public static function getIDsFromResults($results, $attr = 'id'){
        $has_key = [];
        foreach ($results as $result) $has_key[$result[$attr]] = true;
        return array_keys($has_key);
    }

    public static function attachOneToMany($results, $class_name, $sub_results, $sub_class_name){
        $result_index_by_id = [];
        foreach ($results as $i => $result){
            $results[$i][$sub_class_name] = [];
            $result_index_by_id[$result['id']] = $i;
        }
        foreach ($sub_results as $i => $sub_result){
            $foreign_attr = $class_name.'_id';
            $foreign_id = $sub_result[$foreign_attr];
            $index = $result_index_by_id[$foreign_id] ?? null;
            if ($index !== null){
                unset($sub_result[$foreign_attr]);
                array_push($results[$index][$sub_class_name], $sub_result);
            }
        }
        return $results;
    }

    public static function attachManyToOne($results, $parent_class_name, $parent_results){
        $parent_by_id = [];
        foreach ($parent_results as $i => $parent_result){
            $parent_by_id[$parent_result['id']] = $parent_result;
        }
        foreach ($results as $i => $result){
            $parent_class_attr = $parent_class_name.'_id';
            $foreign_id = $result[$parent_class_attr];
            $results[$i][$parent_class_name] = $parent_by_id[$result[$parent_class_name.'_id']] ?? null;
            unset($results[$i][$parent_class_attr]);
        }
        return $results;
    }

}
