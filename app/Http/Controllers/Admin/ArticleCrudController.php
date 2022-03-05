<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ArticleRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ArticleCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ArticleCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;


    private function getFieldsData($show = FALSE) {
        return [
            [
                'name'=> 'title',
                'label' => 'Title',
                'type'=> 'text'
            ],
            [
                'name' => 'content',
                'label' => 'Content',
                'type' => ($show ? "textarea": 'ckeditor'),
            ],
            [    // Select2Multiple = n-n relationship (with pivot table)
                'label'     => "Tags",
                'type'      => ($show ? "select": 'select2_multiple'),
                'name'      => 'tags', // the method that defines the relationship in your Model
                // optional
                'entity'    => 'tags', // the method that defines the relationship in your Model
                'model'     => "App\Models\Tag", // foreign key model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'pivot'     => true, // on create&update, do you need to add/delete pivot table entries?
            ]
        ];

        [
            'label' => "Image",
            'name' => "image",
            'type' => 'image',
            'crop' => true, // set to true to allow cropping, false to disable
            'aspect_ratio' => 1, // omit or set to 0 to allow any aspect ratio
        ];
    }
    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Article::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/article');
        CRUD::setEntityNameStrings('article', 'articles');

    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        //CRUD::column('id');
        $this->crud->addColumn([ // image
            'label' => "Image",
            'name' => "image",
            'type'     => 'closure',
            'function' => function($entry) {
                // return "opk";
                return  "<img src='".asset('storage/'.$entry->image)."' width='25px'>";
            }
        ]);
        CRUD::column('title');
        CRUD::column('content');

        $this->crud->addColumn([ // image
            'label' => "Created_at",
            'name' => "created_at",
            'type'     => 'closure',
            'function' => function($entry) {
                return  \Carbon\Carbon::parse($entry->created_at)->format('d-m-Y H:i:s');
            }
        ]);
        $this->crud->addColumn([ // image
            'label' => "Updated_at",
            'name' => "updated_at",
            'type'     => 'closure',
            'function' => function($entry) {
                return  \Carbon\Carbon::parse($entry->updated_at)->format('d-m-Y H:i:s');
            }
        ]);

        $this->crud->set('show.setFromDb', false);
        $this->crud->addColumns($this->getFieldsData(TRUE));

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ArticleRequest::class);

        //CRUD::field('id');
        $this->crud->addField([
            'name' => 'image',
            'label' => 'Image',
            'type' => 'upload',
            'upload' => true
        ]);
        CRUD::field('title');
        CRUD::field('content');

        // CRUD::field('tag_id');
        $this->crud->AddField([   // relationship
            'type' => "relationship",
            'name' => 'tags', // the method on your model that defines the relationship

            // OPTIONALS:
            'label' => "tags",
            'attribute' => "name", // foreign key attribute that is shown to user (identifiable attribute)
            'entity' => 'tags', // the method that defines the relationship in your Model
            'placeholder' => "Select a tags", // placeholder for the select2 input
        ]);
        CRUD::field('created_at');
        CRUD::field('updated_at');


        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
    protected function setupShowOperation()
{
    // by default the Show operation will try to show all columns in the db table,
    // but we can easily take over, and have full control of what columns are shown,
    // by changing this config for the Show operation
    $this->crud->set('show.setFromDb', false);
    $this->crud->addColumns($this->getFieldsData(TRUE));
}
}
