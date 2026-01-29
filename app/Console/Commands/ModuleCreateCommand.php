<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ModuleCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:create {name : The name of the module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $moduleName = Str::studly($name);
        $modulePath = base_path("modules/{$moduleName}");

        if (File::exists($modulePath)) {
            $this->error("Module {$moduleName} already exists!");
            return 1;
        }

        $this->info("Creating module: {$moduleName}");

        // Create module directory structure
        $directories = [
            'Http/Controllers',
            'Models',
            'Filament/Resources',
            'Providers',
            'routes',
            'resources/views',
        ];

        foreach ($directories as $directory) {
            File::makeDirectory("{$modulePath}/{$directory}", 0755, true);
        }

        // Create module.json
        $moduleConfig = [
            'name' => $moduleName,
            'description' => "The {$moduleName} module",
            'version' => '1.0.0',
            'enabled' => true,
            'dependencies' => []
        ];

        File::put(
            "{$modulePath}/module.json",
            json_encode($moduleConfig, JSON_PRETTY_PRINT)
        );

        // Create module service provider
        $this->createServiceProvider($modulePath, $moduleName);

        // Create routes files
        $this->createRoutes($modulePath, $moduleName);

        // Create example model
        $this->createExampleModel($modulePath, $moduleName);

        // Create example controller
        $this->createExampleController($modulePath, $moduleName);

        // Create example Filament resource
        $this->createExampleFilamentResource($modulePath, $moduleName);

        $this->info("Module {$moduleName} created successfully!");
        $this->info("Run 'composer dump-autoload' to register the new module.");

        return 0;
    }

    private function createServiceProvider($modulePath, $moduleName)
    {
        $content = "<?php

        namespace Modules\\{$moduleName}\\Providers;

        use Illuminate\Support\ServiceProvider;
        use Illuminate\Support\Facades\Route;

        class {$moduleName}ServiceProvider extends ServiceProvider
        {
            /**
             * Register any application services.
             */
            public function register(): void
            {
                //
            }

            /**
             * Bootstrap any application services.
             */
            public function boot(): void
            {
                \$this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
                \$this->loadViewsFrom(__DIR__ . '/../resources/views', '" . strtolower($moduleName) . "');
                \$this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
            }
        }";

        File::put("{$modulePath}/Providers/{$moduleName}ServiceProvider.php", $content);
    }

    private function createRoutes($modulePath, $moduleName)
    {
        $webContent = "
        <?php

        use Illuminate\\Support\\Facades\\Route;
        use Modules\\{$moduleName}\\Http\\Controllers\\{$moduleName}Controller;

        Route::middleware(['auth'])->group(function () {
            Route::get('/{$moduleName}', [{$moduleName}Controller::class, 'index'])->name('" . strtolower($moduleName) . ".index');
        });";

        File::put("{$modulePath}/routes/web.php", $webContent);
    }

    private function createExampleModel($modulePath, $moduleName)
    {
        $modelName = Str::singular($moduleName);
        $content = "
                <?php

                namespace Modules\\{$moduleName}\\Models;

                use Illuminate\\Database\\Eloquent\\Factories\\HasFactory;
                use Illuminate\\Database\\Eloquent\\Model;

                class {$modelName} extends Model
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
        ";

        File::put("{$modulePath}/Models/{$modelName}.php", $content);
    }

    private function createExampleController($modulePath, $moduleName)
    {
        $content = "
            <?php

            namespace Modules\\{$moduleName}\\Http\\Controllers;

            use App\\Http\\Controllers\\Controller;
            use Illuminate\\Http\\Request;

            class {$moduleName}Controller extends Controller
            {
                public function index()
                {
                    return view('" . strtolower($moduleName) . "::index');
                }
            }
        ";

        File::put("{$modulePath}/Http/Controllers/{$moduleName}Controller.php", $content);
    }

    private function createExampleFilamentResource($modulePath, $moduleName)
    {
        $modelName = Str::singular($moduleName);
        $content = "
            <?php

            namespace Modules\\{$moduleName}\\Filament\\Resources;

            use Modules\\{$moduleName}\\Models\\{$modelName};
            use Filament\\Forms;
            use Filament\\Forms\\Form;
            use Filament\\Resources\\Resource;
            use Filament\\Tables;
            use Filament\\Tables\\Table;
            use Modules\\{$moduleName}\\Filament\\Resources\\{$modelName}Resource\\Pages;

            class {$modelName}Resource extends Resource
            {
                protected static ?string \$model = {$modelName}::class;

                protected static ?string \$navigationIcon = 'heroicon-o-rectangle-stack';
                
                protected static ?string \$navigationGroup = '{$moduleName}';

                public static function form(Form \$form): Form
                {
                    return \$form
                        ->schema([
                            Forms\\Components\\TextInput::make('name')
                                ->required()
                                ->maxLength(255),
                            Forms\\Components\\Textarea::make('description')
                                ->maxLength(65535)
                                ->columnSpanFull(),
                            Forms\\Components\\Toggle::make('status')
                                ->required(),
                        ]);
                }

                public static function table(Table \$table): Table
                {
                    return \$table
                        ->columns([
                            Tables\\Columns\\TextColumn::make('name')
                                ->searchable(),
                            Tables\\Columns\\TextColumn::make('description')
                                ->limit(50),
                            Tables\\Columns\\IconColumn::make('status')
                                ->boolean(),
                            Tables\\Columns\\TextColumn::make('created_at')
                                ->dateTime()
                                ->sortable()
                                ->toggleable(isToggledHiddenByDefault: true),
                            Tables\\Columns\\TextColumn::make('updated_at')
                                ->dateTime()
                                ->sortable()
                                ->toggleable(isToggledHiddenByDefault: true),
                        ])
                        ->filters([
                            //
                        ])
                        ->actions([
                            Tables\\Actions\\EditAction::make(),
                            Tables\\Actions\\DeleteAction::make(),
                        ])
                        ->bulkActions([
                            Tables\\Actions\\BulkActionGroup::make([
                                Tables\\Actions\\DeleteBulkAction::make(),
                            ]),
                        ])
                        ->emptyStateActions([
                            Tables\\Actions\\CreateAction::make(),
                        ]);
                }

                public static function getRelations(): array
                {
                    return [
                        //
                    ];
                }

                public static function getPages(): array
                {
                    return [
                        'index' => Pages\\List{$modelName}s::route('/'),
                        'create' => Pages\\Create{$modelName}::route('/create'),
                        'edit' => Pages\\Edit{$modelName}::route('/{record}/edit'),
                    ];
                }
            }
        ";

        File::put("{$modulePath}/Filament/Resources/{$modelName}Resource.php", $content);

        // Create resource pages directory and files
        File::makeDirectory("{$modulePath}/Filament/Resources/{$modelName}Resource/Pages", 0755, true);

        $this->createFilamentPages($modulePath, $moduleName, $modelName);
    }

    private function createFilamentPages($modulePath, $moduleName, $modelName)
    {
        // Create List page
        $listContent = "
            <?php

            namespace Modules\\{$moduleName}\\Filament\\Resources\\{$modelName}Resource\\Pages;

            use Modules\\{$moduleName}\\Filament\\Resources\\{$modelName}Resource;
            use Filament\\Actions;
            use Filament\\Resources\\Pages\\ListRecords;

            class List{$modelName}s extends ListRecords
            {
                protected static string \$resource = {$modelName}Resource::class;

                protected function getHeaderActions(): array
                {
                    return [
                        Actions\\CreateAction::make(),
                    ];
                }
            }";

        File::put("{$modulePath}/Filament/Resources/{$modelName}Resource/Pages/List{$modelName}s.php", $listContent);

        // Create Create page
        $createContent = "<?php

            namespace Modules\\{$moduleName}\\Filament\\Resources\\{$modelName}Resource\\Pages;

            use Modules\\{$moduleName}\\Filament\\Resources\\{$modelName}Resource;
            use Filament\\Actions;
            use Filament\\Resources\\Pages\\CreateRecord;

            class Create{$modelName} extends CreateRecord
            {
                protected static string \$resource = {$modelName}Resource::class;
            }";

        File::put("{$modulePath}/Filament/Resources/{$modelName}Resource/Pages/Create{$modelName}.php", $createContent);

        // Create Edit page
        $editContent = "<?php

            namespace Modules\\{$moduleName}\\Filament\\Resources\\{$modelName}Resource\\Pages;

            use Modules\\{$moduleName}\\Filament\\Resources\\{$modelName}Resource;
            use Filament\\Actions;
            use Filament\\Resources\\Pages\\EditRecord;

            class Edit{$modelName} extends EditRecord
            {
                protected static string \$resource = {$modelName}Resource::class;

                protected function getHeaderActions(): array
                {
                    return [
                        Actions\\DeleteAction::make(),
                    ];
                }
            }
        ";

        File::put("{$modulePath}/Filament/Resources/{$modelName}Resource/Pages/Edit{$modelName}.php", $editContent);
    }
}
