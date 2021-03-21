<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Console\Output\ConsoleOutput;

class CreateForeignKeys extends Migration {

    /**
     * Foreign key list
     * table_name =>
     *          [
     *              foreign_key => [table => related_table],
     *              ...
     *          ]
     *
     * @var array
     */
    private array $keys = [];

    /**
     * Create all foreign keys from $this->keys array
     * @throws Exception
     */
    public function up(): void
    {
        $output = new ConsoleOutput();
        $this->keys = config('foreign-keys') ?? [];
        if (count($this->keys) === 0) {
            return;
        }

        $countTables = 0;
        $countForeignKeys = 0;
        foreach ($this->keys as $tableModelName => $keys) {
            if (! class_exists($tableModelName)) {
                $tableName = $tableModelName;
            } else {
                $tableModel = new $tableModelName();
                $tableName = $tableModel->getTable();
            }

            $countTables++;
            Schema::table($tableName, function (Blueprint $table) use ($tableName, $keys, &$countForeignKeys, $output) {
                $i = 1;
                foreach ($keys as $foreignKey => $relatedModel) {
                    if (! class_exists($relatedModel)) {
                        throw new Exception("'{$relatedModel}' not existing model for foreign key '{$foreignKey}'.");
                    }
                    $model = new $relatedModel;
                    $relatedTable = $model->getTable();

                    if (! App::runningUnitTests()) {
                        $output->writeln('<info>Creating foreign key:</info> ' . $tableName . '.' . $foreignKey . ' -> ' . $relatedTable . '.id');
                    }
                    $table->foreign($foreignKey, $tableName . '_ibfk_' . $i)->references($model->getKeyName())->on($relatedTable)->onUpdate('RESTRICT')->onDelete('RESTRICT');
                    $i++;
                    $countForeignKeys++;
                }
            });
        }

        if (! App::runningUnitTests()) {
            $output->writeln('<info>Created:</info> ' . $countForeignKeys . ' foreign keys in ' . $countTables . ' tables.');
        }
    }

    /**
     * Drop all foreign keys based on $this->keys array
     */
    public function down(): void
    {
        $this->keys = config('foreign_keys') ?? [];
        if (count($this->keys) === 0) {
            return;
        }

        foreach ($this->keys as $tableName => $keys) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName, $keys) {
                $i = 1;
                foreach ($keys as $foreignKey => $relatedData) {
                    $table->dropForeign($tableName . '_ibfk_' . $i);
                    $i++;
                }
            });
        }
    }

}