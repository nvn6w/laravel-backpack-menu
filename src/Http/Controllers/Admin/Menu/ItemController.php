<?php

namespace Novius\Menu\Http\Controllers\Admin\Menu;

use App\Models\Form\Form;
use App\Models\Page;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Illuminate\Support\Facades\Input;
use Novius\Menu\Models\Item;
use Backpack\CRUD\app\Http\Requests\CrudRequest as StoreRequest;
use Backpack\CRUD\app\Http\Requests\CrudRequest as UpdateRequest;
use Novius\Menu\Models\Menu;

class ItemController extends CrudController
{
    public function setup()
    {
        $this->crud->setModel(Item::class);
        $this->crud->setRoute(route('crud.item.index'));

        $this->crud->addFilter([
            'name' => 'menu',
            'type' => 'select2',
            'label' => trans('laravel-menu::menu.menu'),
        ], function () {
            return Menu::all()->pluck('name', 'id')->toArray();
        }, function ($value) {
            $this->crud->addClause('where', 'menu_id', $value);
        });

        $this->crud->setEntityNameStrings(trans('laravel-menu::menu.item'), trans('laravel-menu::menu.items'));

        $this->crud->addColumn([
            'name' => 'name',
            'label' => trans('laravel-menu::menu.edit.name'),
        ]);

        $this->crud->addColumn([
            'label' => trans('laravel-menu::menu.menu'),
            'type' => 'select',
            'name' => 'menu_id',
            'entity' => 'menu',
            'attribute' => 'name',
            'model' => Menu::class,
        ]);

        $this->crud->addField([
            'name' => 'name',
            'label' => trans('laravel-menu::menu.edit.name'),
        ]);

        $this->crud->addField([
            'name' => 'menu_id',
            'label' => trans('laravel-menu::menu.menu'),
            'type' => 'select2_from_array',
            'options' => Menu::all()->pluck('name', 'id')->toArray(),
            'allows_null' => false,
            'allows_multiple' => false,
        ]);

        $pages = Page::linkableItems(trans('configuration.items.page'));
        $forms = Form::linkableItems(trans('configuration.items.form'));

        $links = array_merge(
            $pages,
            $forms
        );

        $this->crud->addField([
            'name' => 'links',
            'label' => 'Link',
            'type' => 'select2_from_array',
            'options' => $links,
            'allows_null' => false,
            'allows_multiple' => false,
        ]);

        $this->crud->orderBy('menu_id');
        $this->crud->orderBy('lft');

        $this->configureReorder();
    }

    public function edit($id)
    {
        $item = Item::find($id);
        $this->crud->setIndexRoute('crud.item.index', ['menu' => $item->menu_id]);

        return parent::edit($id);
    }

    public function store(StoreRequest $request)
    {
        return parent::storeCrud($request);
    }

    public function update(UpdateRequest $request)
    {
        return parent::updateCrud($request);
    }

    protected function configureReorder()
    {
        $this->crud->allowAccess('reorder');
        $this->crud->enableReorder('name', 5);
        $this->crud->setReorderRoute('crud.item.index', ['menu' => request('menu')]);

        // The correct way if the PR is accepted https://github.com/Laravel-Backpack/CRUD/pull/932
        // $this->setReorderFilterCallback(function(){});

        // Alternate way avoiding extension of CrudController in Novius Backpack extended
        // (overriding the view Reorder)
        $this->data['reorder_filter_callback'] = function ($value, $key) {
            $isValid = true;
            $menu_id = (int) Input::get('menu');
            if ($menu_id) {
                $isValid = $value->menu_id == $menu_id;
            }

            return $isValid;
        };
    }
}