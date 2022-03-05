<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class CustomerCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CustomerCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Customer::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/customer');
        CRUD::setEntityNameStrings('customer', 'customers');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->addFilter([ // select2 filter
            'name' => 'name',
            'type' => 'select2',
            'label'=> 'Search name'
          ],
           function() {
              return Customer::pluck('name','id')->toArray();
          }, function($value) { // if the filter is active
               $this->crud->addClause('where', 'name', $value);
          });

        //CRUD::column('id');
        CRUD::column('code');
        $this->crud->addColumn([ // image
            'label' => "Profile",
            'name' => "profile",
            'type' => 'text'
        ]);

        CRUD::column('name');
        CRUD::column('gender');
        $this->crud->addColumn([ // image
            'label' => "date_of_birth",
            'name' => "date_of_birth",
            'type'     => 'closure',
            'function' => function($entry) {
                return  \Carbon\Carbon::parse($entry->date_of_birth)->format('d-m-Y');
            }
        ]);
        CRUD::column('phone');
        CRUD::column('email');
        CRUD::column('address');
        //CRUD::column('created_at');
        $this->crud->addColumn([ // image
            'label' => "Created_at",
            'name' => "created_at",
            'type'     => 'closure',
            'function' => function($entry) {
                return  \Carbon\Carbon::parse($entry->created_at)->format('d-m-Y H:i:s');
            }
        ]);
        // CRUD::column('updated_at');
        $this->crud->addColumn([ // image
            'label' => "Updated_at",
            'name' => "updated_at",
            'type'     => 'closure',
            'function' => function($entry) {
                return  \Carbon\Carbon::parse($entry->updated_at)->format('d-m-Y H:i:s');
            }
        ]);

        // $this->crud->set('show.setFromDb', false);
        // $this->crud->addColumns($this->getFieldsData(TRUE));

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
        CRUD::setValidation(CustomerRequest::class);

        //CRUD::field('id');
        CRUD::field('code');
        CRUD::field('name');
        CRUD::field('gender');
        CRUD::field('date_of_birth');
        CRUD::field('phone');
        CRUD::field('email');
        CRUD::field('address');
        //CRUD::field('profile');
        $this->crud->addField([
            'name' => 'profile',
            'label' => 'Profile',
            'type' => 'upload',
            'upload' => true
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
}
