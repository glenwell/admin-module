<?php

namespace Modules\Admin\Http\Controllers\Voyager;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use TCG\Voyager\Database\Schema\Column;
use TCG\Voyager\Database\Schema\SchemaManager;
use TCG\Voyager\Database\Schema\Table;
use TCG\Voyager\Database\Types\Type;
use TCG\Voyager\Events\BreadAdded;
use TCG\Voyager\Events\BreadDeleted;
use TCG\Voyager\Events\BreadUpdated;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\DataRow;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Models\Permission;
use TCG\Voyager\Http\Controllers\VoyagerBreadController as BaseVoyagerBreadController;

class VoyagerBreadController extends BaseVoyagerBreadController
{
    public function index()
    {
        Voyager::canOrFail('browse_bread');

        $dataTypes = Voyager::model('DataType')->select('id', 'name', 'slug')->get()->keyBy('name')->toArray();

        $tables = array_map(function ($table) use ($dataTypes) {
            $table = [
                'name'       => $table,
                'slug'       => isset($dataTypes[$table]['slug']) ? $dataTypes[$table]['slug'] : null,
                'dataTypeId' => isset($dataTypes[$table]['id']) ? $dataTypes[$table]['id'] : null,
            ];

            return (object) $table;
        }, SchemaManager::listTableNames());

        return Voyager::view('admin::voyager.tools.bread.index')->with(compact('dataTypes', 'tables'));
    }

    /**
     * Create BREAD.
     *
     * @param Request $request
     * @param string  $table   Table name.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request, $table)
    {
        Voyager::canOrFail('browse_bread');

        $dataType = Voyager::model('DataType')->whereName($table)->first();

        $data = $this->prepopulateBreadInfo($table);
        $data['fieldOptions'] = SchemaManager::describeTable((isset($dataType) && strlen($dataType->model_name) != 0)
            ? app($dataType->model_name)->getTable()
            : $table
        );

        return Voyager::view('admin::voyager.tools.bread.edit-add', $data);
    }

    /**
     * Edit BREAD.
     *
     * @param string $table
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($table)
    {
        Voyager::canOrFail('browse_bread');

        $dataType = Voyager::model('DataType')->whereName($table)->first();

        $fieldOptions = SchemaManager::describeTable((strlen($dataType->model_name) != 0)
            ? app($dataType->model_name)->getTable()
            : $dataType->name
        );

        $isModelTranslatable = is_bread_translatable($dataType);
        $tables = SchemaManager::listTableNames();
        $dataTypeRelationships = Voyager::model('DataRow')->where('data_type_id', '=', $dataType->id)->where('type', '=', 'relationship')->get();

        return Voyager::view('admin::voyager.tools.bread.edit-add', compact('dataType', 'fieldOptions', 'isModelTranslatable', 'tables', 'dataTypeRelationships'));
    }

    private function prepopulateBreadInfo($table)
    {
        $displayName = Str::singular(implode(' ', explode('_', Str::title($table))));
        $modelNamespace = config('voyager.models.namespace', app()->getNamespace());
        if (empty($modelNamespace)) {
            $modelNamespace = app()->getNamespace();
        }

        return [
            'isModelTranslatable'  => true,
            'table'                => $table,
            'slug'                 => Str::slug($table),
            'display_name'         => $displayName,
            'display_name_plural'  => Str::plural($displayName),
            'model_name'           => $modelNamespace.Str::studly(Str::singular($table)),
            'generate_permissions' => true,
            'server_side'          => false,
        ];
    }

    private function getRelationshipField($request)
    {
        // We need to make sure that we aren't creating an already existing field

        $dataType = Voyager::model('DataType')->find($request->data_type_id);

        $field = str_singular($dataType->name).'_'.$request->relationship_type.'_'.str_singular($request->relationship_table).'_relationship';

        $relationshipFieldOriginal = $relationshipField = strtolower($field);

        $existingRow = Voyager::model('DataRow')->where('field', '=', $relationshipField)->first();
        $index = 1;

        while (isset($existingRow->id)) {
            $relationshipField = $relationshipFieldOriginal.'_'.$index;
            $existingRow = Voyager::model('DataRow')->where('field', '=', $relationshipField)->first();
            $index += 1;
        }

        return $relationshipField;
    }
}
