<?php namespace Radiantweb\Problog;

use App;
use Event;
use Backend;
use Cms\Classes\Theme;
use Cms\Classes\Partial;
use System\Classes\PluginManager;
use System\Classes\PluginBase;
use Backend\Models\User as BackendUserModel;
use Backend\Controllers\Users as BackendUserController;
use Radiantweb\Problog\Models\Author as ProblogAuthor;;
use Radiantweb\Problog\Models\Category;
use Radiantweb\Problog\Models\Tag;
use Radiantweb\Problog\Classes\TagProcessor;
use Twig\Lexer;

class Plugin extends PluginBase
{

    public function pluginDetails()
    {
        return [
            'name'        => 'radiantweb.problog::lang.plugin.name',
            'description' => 'radiantweb.problog::lang.plugin.description',
            'author'      => '://radiantweb',
            'icon'        => 'icon-edit'
        ];
    }

    public function boot()
    {
        Event::listen('pages.menuitem.listTypes', function() {
            return [
                'problog-category'=>'ProBlog Category',
                'all-problog-categories'=>'All ProBlog Categories',
                'problog-tag'=>'ProBlog Tag',
                'all-problog-tags'=>'All ProBlog Tags',
            ];
        });

        Event::listen('pages.menuitem.getTypeInfo', function($type) {
            if ($type == 'problog-category' || $type == 'all-problog-categories')
                return Category::getMenuTypeInfo($type);

            if ($type == 'problog-tag' || $type == 'all-problog-tags')
                return Tag::getMenuTypeInfo($type);
        });

        Event::listen('pages.menuitem.resolveItem', function($type, $item, $url, $theme) {
            if ($type == 'problog-category' || $type == 'all-problog-categories')
                return Category::resolveMenuItem($item, $url, $theme);

            if ($type == 'problog-tag' || $type == 'all-problog-tags')
                return Tag::resolveMenuItem($item, $url, $theme);
        });

        BackendUserModel::extend(function($model){
            $model->hasOne['author'] = ['Radiantweb\Problog\Models\Author'];
        });

        BackendUserController::extendFormFields(function($form,$model,$context){
            if ( ! $model instanceof BackendUserModel) {
                return;
            }
            
            $model->author = ProblogAuthor::getFromUser($model);

            $form->addTabFields([
                'author[bio]' => [
                    'label' => 'Author Bio',
                    'tab' => 'ProBlog',
                    'type' => 'textarea'
                ]
            ]);
        });
    }

    public function registerComponents()
    {
        return [
            'Radiantweb\Problog\Components\Post' => 'proBlogPost',
            'Radiantweb\Problog\Components\Bloglist' => 'proBlogList',
            'Radiantweb\Problog\Components\Tags' => 'proBlogTags',
            'Radiantweb\Problog\Components\Categories' => 'proBlogCategories',
            'Radiantweb\Problog\Components\Archive' => 'proBlogArchive',
            'Radiantweb\Problog\Components\Related' => 'proBlogRelated',
        ];
    }

    public function registerPageSnippets()
    {
        return [
            //'Radiantweb\Problog\Components\Post' => 'proBlogPost',
            'Radiantweb\Problog\Components\Bloglist' => 'proBlogList',
            //'Radiantweb\Problog\Components\Tags' => 'proBlogTags',
            //'Radiantweb\Problog\Components\Categories' => 'proBlogCategories',
            //'Radiantweb\Problog\Components\Archive' => 'proBlogArchive',
            'Radiantweb\Problog\Components\Related' => 'proBlogRelated',
        ];
    }

    public function registerNavigation()
    {
        return [
            'problog' => [
                'label'       => 'ProBlog',
                'url'         => Backend::url('radiantweb/problog/posts'),
                'icon'        => 'icon-edit',
                'permissions' => ['radiantweb.problog.*'],
                'order'       => 500,

                'sideMenu' => [
                    'posts' => [
                        'label'       => 'radiantweb.problog::lang.sidemenu.posts',
                        'icon'        => 'icon-list-ul',
                        'url'         => Backend::url('radiantweb/problog/posts'),
                        'permissions' => ['radiantweb.problog.access_problog_posts'],
                    ],
                    'series' => [
                        'label'       => 'radiantweb.problog::lang.sidemenu.series',
                        'icon'        => 'icon-list-alt',
                        'url'         => Backend::url('radiantweb/problog/series'),
                        'permissions' => ['radiantweb.problog.access_problog_posts'],
                    ],
                    'categories' => [
                        'label'       => 'radiantweb.problog::lang.sidemenu.categories',
                        'icon'        => 'icon-list-alt',
                        'url'         => Backend::url('radiantweb/problog/categories'),
                        'permissions' => ['radiantweb.problog.access_problog_posts'],
                    ],
                    'tags' => [
                        'label'       => 'radiantweb.problog::lang.sidemenu.tags',
                        'icon'        => 'icon-tags',
                        'url'         => Backend::url('radiantweb/problog/tags'),
                        'permissions' => ['radiantweb.problog.access_problog_posts'],
                    ],
                ]

            ]
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'ProBlog',
                'description' => 'radiantweb.problog::lang.settings.description',
                'icon'        => 'icon-edit',
                'class'       => 'Radiantweb\Problog\Models\Settings',
                'url'         => Backend::url('radiantweb/problog/series'),
                'order'       => 100,
                'permissions' => ['radiantweb.problog.access_problog_settings']
            ]
        ];
    }

    public function registerFormWidgets()
    {
        return [
            'Radiantweb\Problog\FormWidgets\Optimizer' => [
                'label' => 'Optimizer',
                'code' => 'optimizer'
            ],
            'Radiantweb\Problog\FormWidgets\Livetag' => [
                'label' => 'Livetag',
                'code' => 'livetag'
            ],
            'Radiantweb\Problog\FormWidgets\AuthenticateTwitter' => [
                'label' => 'AuthenticateTwitter',
                'code' => 'authenticatetwiter'
            ],
            'Radiantweb\Problog\FormWidgets\PostToTwitter' => [
                'label' => 'PostToTwitter',
                'code' => 'posttotwitter'
            ],
            'Radiantweb\Problog\FormWidgets\PostHistory' => [
                'label' => 'Post History',
                'code' => 'posthistory'
            ]
        ];
    }

    public function registerPermissions()
    {
        return [
            'radiantweb.problog.access_problog_posts' => ['label' => 'Can Manage Blogs', 'tab' => 'ProBlog'],
            'radiantweb.problog.access_other_posts' => ['label' => 'Can Manage Other Users Blogs', 'tab' => 'ProBlog'],
            'radiantweb.problog.access_problog_settings' => ['label' => 'Can Manage ProBlog Settings', 'tab' => 'ProBlog']
        ];
    }

    public function registerMarkupTags()
    {
        // Check the translate plugin is installed
        $filters = array();
        if (!PluginManager::instance()->exists('RainLab.Translate')) {
            $filters['_'] = ['Lang', 'get'];
            $filters['__'] = ['Lang', 'choice'];
        }

        $filters['truncate_content'] = [$this,'truncateDescription'];

        return [
            'functions' => [
                'getAuthorInfo' => [$this, 'getAuthorInfo'],
                'parseBlogPost' => [$this, 'parseBlogPost'],
                'truncateDescription' => [$this, 'truncateDescription']
            ],
            'filters' => $filters
        ];
    }

    public function parseBlogPost($content)
    {
        $newContent = array();
        $tempContent = preg_split(
            '/\[\[(.*?)\]\]/',
            $content,
            null,
            PREG_SPLIT_DELIM_CAPTURE
        );

        if (is_array($tempContent)) {
            foreach($tempContent as $instance) {
                if (count($tempContent)>1) {
                    $type_set = explode('::',$instance);
                    $type = $type_set[0];
                    if (count($type_set)>1) {
                        $name = $type_set[1];
                        switch($type) {
                            case 'partial':
                                $newContent[]['partial'] = $name;
                                break;
                            case 'component':
                                if (count($type_set)>2) {
                                    $newContent[]['component_vars'] = array('name' => $name,'vars' => $name = $type_set[2]);
                                }
                                else {
                                    $newContent[]['component'] = array('name' => $name);
                                }
                                break;
                        }
                    }
                    else {
                        $newContent[] = $type_set[0];
                    }
                }
                else {
                    $newContent[] = $content;
                }
            }
        }
        return $newContent;
    }

    public function getAuthorInfo($id)
    {
        $user = BackendUserModel::where('id',$id)->first();
        if ($user->avatar) {
            $user->image = $user->avatar->getThumb(100, 100, ['mode' => 'crop']);
        }
        return $user;
    }

    public function truncateDescription($string, $limit, $end='...')
    {
        $with_html_count = strlen($string);
        $without_html_count = strlen(strip_tags($string));
        $html_tags_length = $with_html_count-$without_html_count;

        return str_limit($string, $limit+$html_tags_length, $end);
    }


    /**
     * Check if a plugin exists and is enabled
     * @param plugin ID Namespace.Pluginname
     */
    public static function pluginExists($id)
    {
        $pi = PluginManager::instance();
        if (!$pi->findByIdentifier($id) || $pi->isDisabled($id)) {
            return false;
        }
        return true;
    }

}
