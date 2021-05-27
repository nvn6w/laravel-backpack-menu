<?php

namespace Novius\Menu\Http\Controllers\Admin\Menu;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Novius\Menu\Models\Menu;
use Novius\Menu\Http\Requests\Admin\MenuRequest as StoreRequest;
use Novius\Menu\Http\Requests\Admin\MenuRequest as UpdateRequest;

class MenuController extends CrudController
{
    public function setup()
    {
        $this->crud->setModel(Menu::class);
        $this->crud->setRoute(config('backpack.laravel-backpack-menu.prefix', 'admin').'/menu');
        $this->crud->setEntityNameStrings(trans('laravel-backpack-menu::menu.menu'), trans('laravel-backpack-menu::menu.menus'));

        $this->crud->addColumn([
            'name' => 'name',
            'label' => trans('laravel-backpack-menu::menu.edit.name'),
        ]);

        $this->crud->addButton('line', 'items', 'view', 'laravel-backpack-menu::buttons.items', 'beginning');

        $this->crud->addField([
            'name' => 'name',
            'label' => trans('laravel-backpack-menu::menu.edit.name'),
        ]);

        $this->crud->addField([
            'name' => 'slug',
            'label' => trans('laravel-backpack-menu::menu.edit.slug'),
            'type' => 'text',
            'attributes' => ['disabled' => 'disabled'],
        ]);

        $this->crud->orderBy('name');
    }

    public function store(StoreRequest $request)
    {
        return parent::storeCrud($request);
    }

    public function update(UpdateRequest $request)
    {
        return parent::updateCrud($request);
    }

    public function destroy($id)
    {
        Menu::find($id)->items()->delete();

        return parent::destroy($id);
    }
}
