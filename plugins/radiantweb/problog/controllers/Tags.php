<?php namespace Radiantweb\Problog\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Flash;
use Radiantweb\Problog\Models\Tag;

class Tags extends Controller
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

        BackendMenu::setContext('Radiantweb.Problog', 'problog', 'tags');
    }

    public function index_onDelete()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {
            $plural = null;
            if (count($checkedIds) > 1) {
                $plural = 's';
            }
            foreach ($checkedIds as $tagId) {
                if (!$tag = Tag::find($tagId)) continue;

                $tag->delete();
            }

            Flash::success('Tag'.$plural.' Successfully Deleted.');
        }

        return $this->listRefresh();
    }
}