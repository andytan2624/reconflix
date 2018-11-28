<?php namespace Radiantweb\Problog\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Flash;
use Radiantweb\Problog\Models\Category;

class Categories extends Controller
{
    public $requiredPermissions = ['radiantweb.problog.access_problog_posts'];

    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Radiantweb.Problog', 'problog', 'categories');
    }

    public function index_onDelete()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {
            $plural = 'y';
            if (count($checkedIds) > 1) {
                $plural = 'ies';
            }
            foreach ($checkedIds as $categoryId) {
                if (!$category = Category::find($categoryId)) continue;

                $category->delete();
            }

            Flash::success('Categor'.$plural.' Successfully Deleted.');
        }

        return $this->listRefresh();
    }
}