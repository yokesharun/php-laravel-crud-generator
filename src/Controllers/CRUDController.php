<?php

namespace Yokesharun\Laravelcrud\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class CRUDController extends Controller
{

	/**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->viewDirectoryPath =  __DIR__ . '/../DummyFiles/';

        $this->delimiter = config('crudgenerator.custom_delimiter') ? config('crudgenerator.custom_delimiter') : ['%%', '%%'];
    }

	 /**
     * CRUD wizard.
     *
     * @return  array
     */
    public function index(Request $request)
    {
    	$Columns= [];
    	if($request->has('table') && $request->has('model')){
    		$Columns = $this->getColumns($request->table);
    	}
        return view('laravelcrud::index',compact('Columns'));
    }


     /**
     * Generate Files.
     *
     * @return  array
     */
    public function generateFiles(Request $request)
    {

    	$this->BuildController($request);

    	// Updating the Http/routes.php file
        $routeFile = app_path('Http/routes.php');
        if (\App::VERSION() >= '5.3') {
            $routeFile = base_path('routes/web.php');
        }

        \File::append($routeFile, "\n" . implode("\n", $this->addRoutes($request->table, $request->model)));

        dd('done');
    }


    /**
     * Build Controller.
     *
     * @return  array
     */
    protected function BuildController($request)
    {
    	$Table = $request->table;
    	$Model = $request->model;
    	$Dummy = \File::get($this->getDummyController());

    	$validationRules = '';
        $validationRules = "\$this->validate(\$request, [";

        $columns = $this->getColumns($Table);
        foreach ($columns as $c) {
            if($request->has($c.'_include') && $request->has($c.'_required')){
				$validationRules .= "\n\t\t\t'".$c."' => 'required',";
            }
        }

        $validationRules = substr($validationRules, 0, -1); // lose the last comma
        $validationRules .= "\n\t\t]);";


        return \File::put(app_path('Http/Controllers/'.$Model.'Controller.php') , $this->replaceViewName($Dummy, $Table)
            ->replaceCrudName($Dummy, $Table)
            ->replaceCrudNameSingular($Dummy, str_singular($Table))
            ->replaceModelName($Dummy, $Model)
            ->replaceValidationRules($Dummy, $validationRules)
            ->replaceClass($Dummy, $Model));
    }


    /**
     * Get Dummy Controller.
     *
     * @return  array
     */
    protected function getDummyController()
    {
        return __DIR__ . '/../DummyFiles/Controller.dummy';
    }


    /**
     * Default template configuration if not provided
     *
     * @return array
     */
    private function defaultTemplating()
    {
        return [
            'index' => ['formHeadingHtml', 'formBodyHtml', 'crudName', 'crudNameCap', 'modelName', 'viewName', 'routeGroup', 'primaryKey'],
            'form' => ['formFieldsHtml'],
            'create' => ['crudName', 'crudNameCap', 'modelName', 'modelNameCap', 'viewName', 'routeGroup', 'viewTemplateDir'],
            'edit' => ['crudName', 'crudNameSingular', 'crudNameCap', 'modelNameCap', 'modelName', 'viewName', 'routeGroup', 'primaryKey', 'viewTemplateDir'],
            'show' => ['formHeadingHtml', 'formBodyHtml', 'formBodyHtmlForShowView', 'crudName', 'crudNameSingular', 'crudNameCap', 'modelName', 'viewName', 'routeGroup', 'primaryKey'],
        ];
    }



    /**
     * Generate files from stub
     *
     * @param $path
     */
    protected function templateStubs($path)
    {
        $dynamicViewTemplate = $this->defaultTemplating();

        foreach ($dynamicViewTemplate as $name => $vars) {
            $file = $this->viewDirectoryPath . $name . '.blade.stub';
            $newFile = $path . $name . '.blade.php';
            if (!File::copy($file, $newFile)) {
                echo "failed to copy $file...\n";
            } else {
                $this->templateVars($newFile, $vars);
                $this->userDefinedVars($newFile);
            }
        }
    }


    /**
     * Create a generic input field using the form helper.
     *
     * @param  array $item
     *
     * @return string
     */
    protected function createInputField($item)
    {
        $required = ($item['required'] === true) ? ", 'required' => 'required'" : "";

        $markup = File::get($this->viewDirectoryPath . 'Fields/input-text.blade.dummy');
        $markup = str_replace('%%required%%', $required, $markup);
        $markup = str_replace('%%fieldType%%', 'type="text"', $markup);
        $markup = str_replace('%%itemName%%', $item['name'], $markup);

        return $this->wrapField(
            $item,
            $markup
        );
    }


    /**
     * Form field wrapper.
     *
     * @param  string $item
     * @param  string $field
     *
     * @return string
     */
    protected function wrapField($item, $field)
    {
        $formGroup = File::get($this->viewDirectoryPath . 'form-fields/wrap-field.blade.stub');

        $labelText = "'" . ucwords(strtolower(str_replace('_', ' ', $item['name']))) . "'";

        return sprintf($formGroup, $item['name'], $labelText, $field);
    }


    /**
     * Replace the viewName fo the given stub.
     *
     * @param string $stub
     * @param string $viewName
     *
     * @return $this
     */
    protected function replaceViewName(&$Dummy, $viewName)
    {
        $Dummy = str_replace(
            '{{viewName}}', $viewName, $Dummy
        );

        return $this;
    }


    /**
     * Replace the crudName for the given stub.
     *
     * @param  string  $stub
     * @param  string  $crudName
     *
     * @return $this
     */
    protected function replaceCrudName(&$stub, $crudName)
    {
        $stub = str_replace(
            '{{crudName}}', $crudName, $stub
        );

        return $this;
    }


    /**
     * Replace the crudNameSingular for the given stub.
     *
     * @param  string  $stub
     * @param  string  $crudNameSingular
     *
     * @return $this
     */
    protected function replaceCrudNameSingular(&$stub, $crudNameSingular)
    {
        $stub = str_replace(
            '{{crudNameSingular}}', $crudNameSingular, $stub
        );

        return $this;
    }

    /**
     * Replace the modelName for the given stub.
     *
     * @param  string  $stub
     * @param  string  $modelName
     *
     * @return $this
     */
    protected function replaceModelName(&$stub, $modelName)
    {
        $stub = str_replace(
            '{{modelName}}', $modelName, $stub
        );

        return $this;
    }


    /**
     * Replace the validationRules for the given stub.
     *
     * @param  string  $stub
     * @param  string  $validationRules
     *
     * @return $this
     */
    protected function replaceValidationRules(&$stub, $validationRules)
    {
        $stub = str_replace(
            '{{validationRules}}', $validationRules, $stub
        );

        return $this;
    }


    /**
     * Add routes.
     *
     * @return  array
     */
    protected function addRoutes($Table, $Model)
    {
        return ["Route::resource('" . str_singular($Table) . "', '" . $Model . "Controller');"];
    }


    /**
     * Return all columns of the table.
     *
     * @return  array
     */
	protected function getColumns($Table){

		return DB::getSchemaBuilder()->getColumnListing($Table);
	}


	/**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $class = str_replace($this->getNamespace($name).'\\', '', $name);

        return str_replace('DummyClass', $class, $stub);
    }


    /**
     * Get the full namespace name for a given class.
     *
     * @param  string  $name
     * @return string
     */
    protected function getNamespace($name)
    {
        return trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
    }

}