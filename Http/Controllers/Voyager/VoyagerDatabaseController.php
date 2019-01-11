<?php

namespace Modules\Admin\Http\Controllers\Voyager;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use TCG\Voyager\Database\DatabaseUpdater;
use TCG\Voyager\Database\Schema\Column;
use TCG\Voyager\Database\Schema\Identifier;
use TCG\Voyager\Database\Schema\SchemaManager;
use TCG\Voyager\Database\Schema\Table;
use TCG\Voyager\Database\Types\Type;
use TCG\Voyager\Events\TableAdded;
use TCG\Voyager\Events\TableDeleted;
use TCG\Voyager\Events\TableUpdated;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Http\Controllers\VoyagerDatabaseController as BaseVoyagerDatabaseController;

class VoyagerDatabaseController extends BaseVoyagerDatabaseController
{
    public function index()
    {
        Voyager::canOrFail('browse_database');

        $dataTypes = Voyager::model('DataType')->select('id', 'name', 'slug')->get()->keyBy('name')->toArray();

        $tables = array_map(function ($table) use ($dataTypes) {
            $table = [
                'name'       => $table,
                'slug'       => isset($dataTypes[$table]['slug']) ? $dataTypes[$table]['slug'] : null,
                'dataTypeId' => isset($dataTypes[$table]['id']) ? $dataTypes[$table]['id'] : null,
            ];

            return (object) $table;
        }, SchemaManager::listTableNames());

        return Voyager::view('admin::voyager.tools.database.index')->with(compact('dataTypes', 'tables'));
    }

    public function create()
    {
        Voyager::canOrFail('browse_database');

        $db = $this->prepareDbManager('create');

        return Voyager::view('admin::voyager.tools.database.edit-add', compact('db'));
    }

    public function edit($table)
    {
        Voyager::canOrFail('browse_database');

        if (!SchemaManager::tableExists($table)) {
            return redirect()
                ->route('voyager.database.index')
                ->with($this->alertError(__('voyager::database.edit_table_not_exist')));
        }

        $db = $this->prepareDbManager('update', $table);

        return Voyager::view('admin::voyager.tools.database.edit-add', compact('db'));
    }
}
