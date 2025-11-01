<?php

namespace RobinNcode\LaravelDbCraft\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MigrationGenerator
{
    protected $connection;
    protected $excludeTables;

    public function __construct($connection = null)
    {
        $this->connection = $connection ?? config('database.default');
        $this->excludeTables = config('db-craft.exclude_tables', []);
    }

    /**
     * Generate migrations for all tables
     */
    public function generateAll()
    {
        $tables = $this->getAllTables();
        $files = [];

        foreach ($tables as $table) {
            if (!in_array($table, $this->excludeTables)) {
                $files[] = $this->generateForTable($table);
            }
        }

        return $files;
    }

    /**
     * Generate migration for a specific table
     */
    public function generateForTable($tableName)
    {
        $columns = $this->getTableColumns($tableName);
        $indexes = $this->getTableIndexes($tableName);
        $foreignKeys = $this->getTableForeignKeys($tableName);

        $migrationContent = $this->buildMigrationContent($tableName, $columns, $indexes, $foreignKeys);
        
        return $this->saveMigration($tableName, $migrationContent);
    }

    /**
     * Get all tables from database
     */
    protected function getAllTables()
    {
        $tables = [];
        $driver = DB::connection($this->connection)->getDriverName();

        switch ($driver) {
            case 'mysql':
                $tables = DB::connection($this->connection)
                    ->select('SHOW TABLES');
                $key = 'Tables_in_' . DB::connection($this->connection)->getDatabaseName();
                $tables = array_map(function ($table) use ($key) {
                    return $table->$key;
                }, $tables);
                break;
            case 'pgsql':
                $tables = DB::connection($this->connection)
                    ->select("SELECT tablename FROM pg_catalog.pg_tables WHERE schemaname = 'public'");
                $tables = array_map(function ($table) {
                    return $table->tablename;
                }, $tables);
                break;
            case 'sqlite':
                $tables = DB::connection($this->connection)
                    ->select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
                $tables = array_map(function ($table) {
                    return $table->name;
                }, $tables);
                break;
        }

        return $tables;
    }

    /**
     * Get table columns information
     */
    protected function getTableColumns($tableName)
    {
        $driver = DB::connection($this->connection)->getDriverName();
        $columns = [];

        switch ($driver) {
            case 'mysql':
                $columns = DB::connection($this->connection)
                    ->select("SHOW FULL COLUMNS FROM `{$tableName}`");
                break;
            case 'pgsql':
                $columns = DB::connection($this->connection)
                    ->select("SELECT * FROM information_schema.columns WHERE table_name = '{$tableName}'");
                break;
            case 'sqlite':
                $columns = DB::connection($this->connection)
                    ->select("PRAGMA table_info({$tableName})");
                break;
        }

        return $columns;
    }

    /**
     * Get table indexes
     */
    protected function getTableIndexes($tableName)
    {
        $driver = DB::connection($this->connection)->getDriverName();
        $indexes = [];

        switch ($driver) {
            case 'mysql':
                $indexes = DB::connection($this->connection)
                    ->select("SHOW INDEXES FROM `{$tableName}`");
                break;
            case 'pgsql':
                $indexes = DB::connection($this->connection)
                    ->select("SELECT * FROM pg_indexes WHERE tablename = '{$tableName}'");
                break;
            case 'sqlite':
                $indexes = DB::connection($this->connection)
                    ->select("PRAGMA index_list({$tableName})");
                break;
        }

        return $indexes;
    }

    /**
     * Get table foreign keys
     */
    protected function getTableForeignKeys($tableName)
    {
        $driver = DB::connection($this->connection)->getDriverName();
        $foreignKeys = [];

        try {
            switch ($driver) {
                case 'mysql':
                    $foreignKeys = DB::connection($this->connection)
                        ->select("
                            SELECT 
                                CONSTRAINT_NAME,
                                COLUMN_NAME,
                                REFERENCED_TABLE_NAME,
                                REFERENCED_COLUMN_NAME
                            FROM information_schema.KEY_COLUMN_USAGE
                            WHERE TABLE_SCHEMA = DATABASE()
                            AND TABLE_NAME = '{$tableName}'
                            AND REFERENCED_TABLE_NAME IS NOT NULL
                        ");
                    break;
                case 'pgsql':
                    $foreignKeys = DB::connection($this->connection)
                        ->select("
                            SELECT
                                tc.constraint_name,
                                kcu.column_name,
                                ccu.table_name AS foreign_table_name,
                                ccu.column_name AS foreign_column_name
                            FROM information_schema.table_constraints AS tc
                            JOIN information_schema.key_column_usage AS kcu
                                ON tc.constraint_name = kcu.constraint_name
                            JOIN information_schema.constraint_column_usage AS ccu
                                ON ccu.constraint_name = tc.constraint_name
                            WHERE tc.constraint_type = 'FOREIGN KEY'
                            AND tc.table_name = '{$tableName}'
                        ");
                    break;
                case 'sqlite':
                    $foreignKeys = DB::connection($this->connection)
                        ->select("PRAGMA foreign_key_list({$tableName})");
                    break;
            }
        } catch (\Exception $e) {
            // Foreign keys might not be available in all configurations
        }

        return $foreignKeys;
    }

    /**
     * Build migration file content
     */
    protected function buildMigrationContent($tableName, $columns, $indexes, $foreignKeys)
    {
        $className = 'Create' . Str::studly($tableName) . 'Table';
        $columnsCode = $this->buildColumnsCode($columns);
        $indexesCode = $this->buildIndexesCode($indexes);
        $foreignKeysCode = $this->buildForeignKeysCode($foreignKeys);

        return <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration class for creating the {$tableName} table.
 * Generated by Laravel DB Craft.
 * Generated on: {{ date('Y-m-d H:i:s') }}
 */

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('{$tableName}', function (Blueprint \$table) {
{$columnsCode}
{$indexesCode}
{$foreignKeysCode}
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('{$tableName}');
    }
};
PHP;
    }

    /**
     * Build columns code for migration
     */
    protected function buildColumnsCode($columns)
    {
        $code = [];
        $driver = DB::connection($this->connection)->getDriverName();

        foreach ($columns as $column) {
            $columnCode = $this->getColumnDefinition($column, $driver);
            if ($columnCode) {
                $code[] = "            " . $columnCode;
            }
        }

        return implode("\n", $code);
    }

    /**
     * Get column definition
     */
    protected function getColumnDefinition($column, $driver)
    {
        $name = $driver === 'mysql' ? $column->Field : ($driver === 'sqlite' ? $column->name : $column->column_name);
        $type = $driver === 'mysql' ? $column->Type : ($driver === 'sqlite' ? $column->type : $column->data_type);
        $nullable = $driver === 'mysql' ? ($column->Null === 'YES') : ($driver === 'sqlite' ? !$column->notnull : ($column->is_nullable === 'YES'));
        $default = $driver === 'mysql' ? $column->Default : ($driver === 'sqlite' ? $column->dflt_value : $column->column_default);

        // Handle auto-increment primary keys
        if ($driver === 'mysql' && $column->Extra === 'auto_increment') {
            return "\$table->id('{$name}');";
        }

        if ($driver === 'sqlite' && $column->pk && strtolower($type) === 'integer') {
            return "\$table->id('{$name}');";
        }

        // Map database types to Laravel types
        $laravelType = $this->mapToLaravelType($type);
        $definition = "\$table->{$laravelType}('{$name}')";

        if ($nullable) {
            $definition .= "->nullable()";
        }

        if ($default !== null && $default !== '') {
            $defaultValue = is_numeric($default) ? $default : "'{$default}'";
            $definition .= "->default({$defaultValue})";
        }

        return $definition . ";";
    }

    /**
     * Map database type to Laravel migration type
     */
    protected function mapToLaravelType($type)
    {
        $type = strtolower($type);

        // Handle types with parameters like varchar(255)
        if (preg_match('/^(\w+)\((\d+)\)/', $type, $matches)) {
            $baseType = $matches[1];
            $length = $matches[2];

            if ($baseType === 'varchar') {
                return "string";
            }
            if ($baseType === 'char') {
                return "char, {$length}";
            }
        }

        $typeMap = [
            'int' => 'integer',
            'integer' => 'integer',
            'bigint' => 'bigInteger',
            'smallint' => 'smallInteger',
            'tinyint' => 'tinyInteger',
            'mediumint' => 'mediumInteger',
            'decimal' => 'decimal',
            'float' => 'float',
            'double' => 'double',
            'boolean' => 'boolean',
            'bool' => 'boolean',
            'date' => 'date',
            'datetime' => 'dateTime',
            'timestamp' => 'timestamp',
            'time' => 'time',
            'year' => 'year',
            'text' => 'text',
            'mediumtext' => 'mediumText',
            'longtext' => 'longText',
            'json' => 'json',
            'jsonb' => 'jsonb',
            'binary' => 'binary',
            'enum' => 'enum',
        ];

        foreach ($typeMap as $dbType => $laravelType) {
            if (strpos($type, $dbType) !== false) {
                return $laravelType;
            }
        }

        return 'string';
    }

    /**
     * Build indexes code
     */
    protected function buildIndexesCode($indexes)
    {
        $code = [];
        $processedIndexes = [];

        foreach ($indexes as $index) {
            $indexName = $this->getIndexName($index);
            
            if (in_array($indexName, $processedIndexes) || $indexName === 'PRIMARY') {
                continue;
            }

            $columnName = $this->getIndexColumnName($index);
            $isUnique = $this->isUniqueIndex($index);

            if ($isUnique) {
                $code[] = "            \$table->unique('{$columnName}');";
            } else {
                $code[] = "            \$table->index('{$columnName}');";
            }

            $processedIndexes[] = $indexName;
        }

        return empty($code) ? '' : "\n" . implode("\n", $code);
    }

    /**
     * Build foreign keys code
     */
    protected function buildForeignKeysCode($foreignKeys)
    {
        $code = [];

        foreach ($foreignKeys as $fk) {
            $columnName = $this->getForeignKeyColumn($fk);
            $referencedTable = $this->getForeignKeyReferencedTable($fk);
            $referencedColumn = $this->getForeignKeyReferencedColumn($fk);

            $code[] = "            \$table->foreign('{$columnName}')" .
                      "->references('{$referencedColumn}')" .
                      "->on('{$referencedTable}')" .
                      "->onDelete('cascade');";
        }

        return empty($code) ? '' : "\n" . implode("\n", $code);
    }

    protected function getIndexName($index)
    {
        $driver = DB::connection($this->connection)->getDriverName();
        return $driver === 'mysql' ? $index->Key_name : ($driver === 'sqlite' ? $index->name : $index->indexname);
    }

    protected function getIndexColumnName($index)
    {
        $driver = DB::connection($this->connection)->getDriverName();
        return $driver === 'mysql' ? $index->Column_name : ($driver === 'sqlite' ? $index->name : $index->column_name);
    }

    protected function isUniqueIndex($index)
    {
        $driver = DB::connection($this->connection)->getDriverName();
        return $driver === 'mysql' ? ($index->Non_unique == 0) : ($driver === 'sqlite' ? $index->unique : false);
    }

    protected function getForeignKeyColumn($fk)
    {
        $driver = DB::connection($this->connection)->getDriverName();
        return $driver === 'mysql' ? $fk->COLUMN_NAME : ($driver === 'sqlite' ? $fk->from : $fk->column_name);
    }

    protected function getForeignKeyReferencedTable($fk)
    {
        $driver = DB::connection($this->connection)->getDriverName();
        return $driver === 'mysql' ? $fk->REFERENCED_TABLE_NAME : ($driver === 'sqlite' ? $fk->table : $fk->foreign_table_name);
    }

    protected function getForeignKeyReferencedColumn($fk)
    {
        $driver = DB::connection($this->connection)->getDriverName();
        return $driver === 'mysql' ? $fk->REFERENCED_COLUMN_NAME : ($driver === 'sqlite' ? $fk->to : $fk->foreign_column_name);
    }

    /**
     * Save migration file
     */
    protected function saveMigration($tableName, $content)
    {
        $timestamp = date('Y_m_d_His');
        $filename = "{$timestamp}_create_{$tableName}_table.php";
        $path = config('db-craft.migrations_path', database_path('migrations'));
        
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }

        $filepath = $path . '/' . $filename;
        file_put_contents($filepath, $content);

        return $filename;
    }
}