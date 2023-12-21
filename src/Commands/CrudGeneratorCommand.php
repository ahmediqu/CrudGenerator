<?php

namespace Crud\CrudGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CrudGeneratorCommand extends Command
{
    protected $signature = 'crud:crud-generator {name}';
    protected $description = 'Generate CRUD operation files';

    public function handle()
    {
        $name = $this->argument('name');

        // Generate Model
        $this->call('make:model', ['name' => $name]);

        // Generate Views
        $this->generateViews($name);

        // Generate Migration
        $this->call('make:migration', ['name' => 'create_' . strtolower($name) . 's']);

        // Generate Validation Request
        $this->call('make:request', ['name' => $name . 'Request']);

        // Generate Routes
        $this->generateRoutes($name);

        $this->generateControllerCrudMethods($name);

        $this->info("CRUD for $name generated successfully.");

    }

    protected function generateViews($name)
    {

        // For simplicity, we'll just create a stub for an index view here
        $viewsPath = resource_path("views/{$name}");
        mkdir($viewsPath);

        $indexViewContent = '<h1>' . $name . ' Index</h1>';
        file_put_contents("{$viewsPath}/index.blade.php", $indexViewContent);
        // ... Generate other views (create, edit, show) as needed
    }

    protected function generateRoutes($name)
    {
        $controllerName = $name . 'Controller';
        $pluralizedName = strtolower(Str::plural($name));

        $routeContent = <<<EOD

Route::resource('$pluralizedName', $controllerName::class);
EOD;

        $routesPath = base_path('routes/web.php');
        file_put_contents($routesPath, $routeContent, FILE_APPEND);
    }

    protected function generateControllerCrudMethods($name)
    {
        $controllerName = $name . 'Controller';
        $modelName = $name;
        $modelVariable = lcfirst($name);
        $requestName = $name . 'Request';

        // Generate CRUD operations in Controller
        $controllerCode = <<<EOD
<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\\$modelName;
use App\Http\Requests\\$requestName;

class $controllerName extends Controller
{
    public function index()
    {
        \$$modelVariable = $modelName::all();
        return view('$modelVariable.index', compact('$modelVariable'));
    }

    public function create()
    {
        return view('$modelVariable.create');
    }

    public function store($requestName \$request)
    {
        \$validatedData = \$request->validated();
        \$$modelVariable = $modelName::create(\$validatedData);

        return redirect()->route('$modelVariable.index')
            ->with('success', '$modelName created successfully.');
    }

    public function show(\$id)
    {
        \$$modelVariable = $modelName::findOrFail(\$id);
        return view('$modelVariable.show', compact('$modelVariable'));
    }

    public function edit(\$id)
    {
        \$$modelVariable = $modelName::findOrFail(\$id);
        return view('$modelVariable.edit', compact('$modelVariable'));
    }

    public function update($requestName \$request, \$id)
    {
        \$validatedData = \$request->validated();
        $modelName::find(\$id)->update(\$validatedData);

        return redirect()->route('$modelVariable.index')
            ->with('success', '$modelName updated successfully.');
    }

    public function destroy(\$id)
    {
        $modelName::destroy(\$id);

        return redirect()->route('$modelVariable.index')
            ->with('success', '$modelName deleted successfully.');
    }
}
EOD;

        $controllerPath = app_path("Http/Controllers/{$controllerName}.php");
        file_put_contents($controllerPath, $controllerCode, FILE_APPEND);
    }


}
