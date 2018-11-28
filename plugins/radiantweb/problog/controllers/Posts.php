<?php namespace Radiantweb\Problog\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Radiantweb\Problog\Models\Post;
use Carbon\Carbon;
use Flash;
use Radiantweb\Problog\Models\Settings as ProblogSettingsModel;

class Posts extends Controller
{
    public $requiredPermissions = ['radiantweb.problog.access_problog_posts'];

    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public $bodyClass = 'compact-container';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Radiantweb.Problog', 'problog', 'posts');
    }

    public function formExtendModel($model)
    {
        if (!$model->parent){
            $model->parent = ProblogSettingsModel::get('defaultParent');
        }

        if (ProblogSettingsModel::get('markdownMode', false)) {
            $model->content = $model->content_markdown;
        }

        return $model;
    }

    public function formExtendFieldsBefore($widget)
    {
        if (ProblogSettingsModel::get('markdownMode', false)) {
            $widget->tabs['fields']['content']['type'] = 'markdown';
        }
    }

    public function listInjectRowClass($model, $definition)
    {
        $model->published = ($model->published == 1) ? 'yes' : 'no';
        $model->published_at = new Carbon($model->published_at);
    }

    public function index_onDuplicate()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {
            $plural = null;

            if (count($checkedIds) > 1) {
                $plural = 's';
            }

            foreach ($checkedIds as $postId) {
                if (!$post = Post::find($postId)) continue;

                $post->clonePost($post);
            }

            Flash::success('Blog'.$plural.' Successfully Duplicated!');
        }

        return $this->listRefresh();
    }

    public function index_onDelete()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {
            $plural = null;
            if (count($checkedIds) > 1) {
                $plural = 's';
            }
            foreach ($checkedIds as $postId) {
                if (!$post = Post::find($postId)) continue;

                $post->delete();
            }

            Flash::success('Blog'.$plural.' Successfully Deleted.');
        }

        return $this->listRefresh();
    }
}
