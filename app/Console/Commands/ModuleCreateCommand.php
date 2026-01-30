<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ModuleCreateCommand extends Command
{
    protected $signature = 'module:create 
        {name : Module name} 
        {--submodule= : Resource / Submodule name (required)}';

    protected $description = 'Create model and filament resource inside a module';

    public function handle()
    {
        $module = Str::studly($this->argument('name'));
        $resource = Str::studly($this->option('submodule'));

        if (!$resource) {
            $this->error('Submodule is required. Example: --submodule=DeliveryOrder');
            return 1;
        }

        $modulePath = base_path("Modules/{$module}");

        $modelPath = "{$modulePath}/Models/{$resource}.php";
        $resourcePath = "{$modulePath}/Filament/Resources/{$resource}Resource.php";

        if (File::exists($modelPath) || File::exists($resourcePath)) {
            $this->error("{$resource} already exists in module {$module}");
            return 1;
        }

        // Directories
        File::ensureDirectoryExists("{$modulePath}/Models");
        File::ensureDirectoryExists("{$modulePath}/Filament/Resources/{$resource}Resource/Pages");

        if (File::exists("{$modulePath}/Models/{$resource}.php")) {
            $this->error("Model {$resource} already exists.");
            return 1;
        }

        $this->createModel($modulePath, $module, $resource);
        $this->createFilamentResource($modulePath, $module, $resource);

        $this->info("✔ {$resource} successfully created in module {$module}");
        return 0;
    }

    private function createModel(string $modulePath, string $module, string $resource): void
    {
        File::put(
            "{$modulePath}/Models/{$resource}.php",
            "<?php

namespace Modules\\{$module}\\Models;

use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Factories\\HasFactory;

class {$resource} extends Model
{
    use HasFactory;

    protected \$fillable = [
        'name',
        'description',
        'status',
    ];

    protected \$casts = [
        'status' => 'boolean',
    ];
}
"
        );
    }

    private function createFilamentResource(string $modulePath, string $module, string $resource): void
    {
        $base = "{$modulePath}/Filament/Resources";

        File::put(
            "{$base}/{$resource}Resource.php",
            $this->filamentResourceStub($module, $resource)
        );

        File::put(
            "{$base}/{$resource}Resource/Pages/List{$resource}s.php",
            $this->listPageStub($module, $resource)
        );

        File::put(
            "{$base}/{$resource}Resource/Pages/Create{$resource}.php",
            $this->createPageStub($module, $resource)
        );

        File::put(
            "{$base}/{$resource}Resource/Pages/Edit{$resource}.php",
            $this->editPageStub($module, $resource)
        );
    }

    private function filamentResourceStub(string $module, string $resource): string
    {
        return "<?php

namespace Modules\\{$module}\\Filament\\Resources;

use Modules\\{$module}\\Models\\{$resource};
use Filament\\Resources\\Resource;
use Filament\\Forms;
use Filament\\Tables;
use Filament\\Forms\\Form;
use Filament\\Tables\\Table;
use Modules\\{$module}\\Filament\\Resources\\{$resource}Resource\\Pages;

class {$resource}Resource extends Resource
{
    protected static ?string \$model = {$resource}::class;
    protected static ?string \$navigationGroup = '{$module}';
    protected static ?string \$navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form \$form): Form
    {
        return \$form->schema([
            Forms\\Components\\TextInput::make('name')->required(),
            Forms\\Components\\Textarea::make('description'),
            Forms\\Components\\Toggle::make('status'),
        ]);
    }

    public static function table(Table \$table): Table
    {
        return \$table->columns([
            Tables\\Columns\\TextColumn::make('name')->searchable(),
            Tables\\Columns\\IconColumn::make('status')->boolean(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\\List{$resource}s::route('/'),
            'create' => Pages\\Create{$resource}::route('/create'),
            'edit' => Pages\\Edit{$resource}::route('/{record}/edit'),
        ];
    }
}
";
    }

    private function listPageStub(string $module, string $resource): string
    {
        return "<?php

namespace Modules\\{$module}\\Filament\\Resources\\{$resource}Resource\\Pages;

use Modules\\{$module}\\Filament\\Resources\\{$resource}Resource;
use Filament\\Resources\\Pages\\ListRecords;
use Filament\\Actions;

class List{$resource}s extends ListRecords
{
    protected static string \$resource = {$resource}Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\\CreateAction::make(),
        ];
    }
}
";
    }

    private function createPageStub(string $module, string $resource): string
    {
        return "<?php

namespace Modules\\{$module}\\Filament\\Resources\\{$resource}Resource\\Pages;

use Modules\\{$module}\\Filament\\Resources\\{$resource}Resource;
use Filament\\Resources\\Pages\\CreateRecord;

class Create{$resource} extends CreateRecord
{
    protected static string \$resource = {$resource}Resource::class;
}
";
    }

    private function editPageStub(string $module, string $resource): string
    {
        return "<?php

namespace Modules\\{$module}\\Filament\\Resources\\{$resource}Resource\\Pages;

use Modules\\{$module}\\Filament\\Resources\\{$resource}Resource;
use Filament\\Resources\\Pages\\EditRecord;
use Filament\\Actions;

class Edit{$resource} extends EditRecord
{
    protected static string \$resource = {$resource}Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\\DeleteAction::make(),
        ];
    }
}
";
    }
}
