problog-plugin
===========

A powerful blogging app for October CMS.
```php
      $$\  $$\    $$$$$$$\                  $$\ $$\                      $$\     $$\      $$\           $$\
     $$  |$$  |   $$  __$$\                 $$ |\__|                     $$ |    $$ | $\  $$ |          $$ |
    $$  /$$  /$$\ $$ |  $$ | $$$$$$\   $$$$$$$ |$$\  $$$$$$\  $$$$$$$\ $$$$$$\   $$ |$$$\ $$ | $$$$$$\  $$$$$$$\
   $$  /$$  / \__|$$$$$$$  | \____$$\ $$  __$$ |$$ | \____$$\ $$  __$$\\_$$  _|  $$ $$ $$\$$ |$$  __$$\ $$  __$$\
  $$  /$$  /      $$  __$$<  $$$$$$$ |$$ /  $$ |$$ | $$$$$$$ |$$ |  $$ | $$ |    $$$$  _$$$$ |$$$$$$$$ |$$ |  $$ |
 $$  /$$  /   $$\ $$ |  $$ |$$  __$$ |$$ |  $$ |$$ |$$  __$$ |$$ |  $$ | $$ |$$\ $$$  / \$$$ |$$   ____|$$ |  $$ |
$$  /$$  /    \__|$$ |  $$ |\$$$$$$$ |\$$$$$$$ |$$ |\$$$$$$$ |$$ |  $$ | \$$$$  |$$  /   \$$ |\$$$$$$$\ $$$$$$$  |
\__/ \__/         \__|  \__| \_______| \_______|\__| \_______|\__|  \__|  \____/ \__/     \__| \_______|\_______/
```

## ProBlog Quickstart

The first thing we must do is set up a parent blog page in your site.

* **Create A Parent Blog Page** add a page to your site and call it "blog".
    * edit the "url" of the page to be "/blog/:filter?/:slug?/:month?/:instance?/"
    * add the ProBlog BlogList component to your parent /blog page and make sure that the "Blog Parent Page" field is set to none. this will list all posts regardless of parent.

The next thing we must do is set up our ProBlog Settings.  To do so, go to your Backend -> Settings -> ProBlog area.

* **Render**
	* *Choose a default Blog Parent Page* - This is the default parent page that will be used to generate Category list pages and the preferred parent page when creating new posts.
* **Editor**
	* *enable markdown* - Switch problog's content editor from wysiwyg to markdown editor with preview.
* **Social Settings**
	* *ShareThis* - If you provide a free ShareThis key, you can automatically utilize ProBlogs social icons.
	* *Facebook* - enable Facebook sharethis button
	* *Twiiter* - enable Twiiter sharethis button
	* *Google* - enable Google sharethis button
* **API Settings**
	* *embed.ly* - enable embed.ly media embed API. see below.
	* *Bluemix NLP Username* - enable IBM Watson Bluemix NLP API. see below.
  * *Blumix NLP Password* - enable IBM Watson Bluemix NLP API. see below.
* **Authorize**
	* *twitter* - Authorize Twitter API. see below.

> **Note:** All ProBlog components should have the Blog Search Page option set. This tells tag/category links where to search from the context of that respective component. This can be the same page as your parent blog page, but must be set non the less.

## Post Rendering Explained

ProBlog Posts can now be rendered via an injected BlogPost component directly from within the BlogList component results.  This is the preferred way to render posts. The following rules dictate how the BlogList component will resolve URLs:
* /blog :  a single url will return a blog list based on the component settings
* /blog/[categoryname] : a propertied url that will return a filtered blog list matching a provided category slug (does not apply to tags)
* /blog/[categoryname]/[slug] : ***Render a Blog Post*** a propertied url that will inject the BlogPost component and return a specific blog post of a match slug under a designated category
* /blog/category/[slug] : interpreted the same as /blog/[categoryname]
* /blog/tag/[slug] : a propertied url that will return a filtered blog list matching a provided tag slug
* /blog/search/[slug] : search blog posts by title & content
* /blog/author/[slug]: search blog posts by author
* /blog/[year]/[month]/[day]/: search blog posts by year/month/day. Month and day are both optional and can be filtered granularly.

Tags will always link to a searchPage as set in the BlogList or Category/Tag component options.

ProBlog Posts can also be rendered via the BlogPost component directly by providing a given posts slug value.

> **Note:** If you would like to route your posts to a specific page for single posts rendering via the BlogPost component, simply make sure the render page containing the BlogPost component has a filter param available in the URL like so: /blogrenderpage/:filter?/:slug?/ : the filter will be ignored and the BlogPost component will simply render the post by slug.

## Managing Multiple Blogs

You can have multiple blogs with ProBlog. Each blog section would serve as a given posts "parent" page. Each blog with their own BlogList component.

So you might have the following:

* /blog
* /news


When posting new blog entries, simply chose one of your category pages.

> **Note:** When posting by category, the Blog Parent Page and the selected Category for the post typically will match. But they do not have to.  This is to provide greater flexibility with linking and routing.

## Passive Component Params

Both the ProBlog BlogList and the ProBlog Post components are designed to "passively" receive input params.

Setting up the BlogList component to receive filtering:

```php
url = /blog/:filter?/:slug?/:instance?/
```
Removing the "?" will make these parameters "forced" and not roll gracefully back.

```php
url = /blog/:filter/:slug/
```

The following filter types are available:

* **category** /category/category-name/
* **tag** /tag/tag-name/
* **author** /author/authorID/
* **[number]** /year/month/day/ (requires own page with passive vars /:filter?/:slug?/:instance?/) (month and day are both optional)  if the filter interpolates as a number, the filter is assumed as cannonical.
* **search** /search/search-word/ (will str_replace '-' and '%20' with ' ')

So any page that contains the blogList component that also has passive variables added to the url can utilize these filters.

For example:  If you have your parent /blog page set up this way, you can search your blog posts by accessing /blog/search/{keyword}/

You can also predispose the component to filtering by virtue of the components settings.

The following predisposed filters are available for the blogList component

* **category** /category/category-name/  accepts @string value
* **tag** /tag/tag-name/  accepts @string value
* **author** /author/authorID  accepts @number value

## BlogList Component

The BlogList component comes with several helpful settings to customize your blog functionality.

* **Alias** The Component Alias name
* **Posts Per Page** The number of posts per page
* **Pagination** Pagination is enabled when this is checked
* **Filter Type** A predisposed filter option. When set, the block will always filter by this type
  * none - no filter
  * tag - filter by tag: requires a value
  * category - filter by category: requires a value
  * canonical - filter by date: requires passed url /:year?/:month?/:day?/
  * author - filter by author: requires author id
  * popular - filter by most popular
  * trending - fitler by most popular of posts created over the last 30days
* **Filter Value** The Value used in conjunction with the Filter Type: Filter Type = Category, Filter Value = uncategorized.  Leave blank for Connonical lists, and use date url params.  See below note.
* **Filter By Parent Page** Blog Posts are filtered by this parent page.
* **Blog Search Page** The Page in your site you would like tags/categories to link to and search
* **Render Posts By** How posts will be rendered: By that posts' parent [preferred/default], By the global setting, By a specific page
* **Render Page** The page that will render posts if "Specific Page" is selected for the "Render Posts' By" option.
* **Enable RSS Feed** Will display an RSS link at the bottom of your list views
* **RSS Title** Your Feed Title
* **RSS Description** Your Feed Description

> **Note:** for a date (cannonical) based Filtered component list, make the URL params simply pagename/:year?/:month?/:day?/, and then set the filter to 'cannonical' and leave the filter value empty.

You can place the BlogList component on a page utilizing it's alias like so:

```php
{% component 'proBlogList' %}
```

## Blog Post Component

The Post component allows you to view individual blog entries.

* **Alias** The Component Alias name
* **Id Parameter** "advanced" - do not change unless you know what partials need to reflect this change.
* **Blog Search Page** The page you would like any tag or category links to be directed to.

You can place the Post component on a page utilizing it's alias like so:

```php
{% component 'proBlogPost' %}
```

If the Blog Post component is set up with sub page urls, automatic category list rendering is assumed. For example: if my post rendering page is /blog/category1/:slug?/ and the url /blog/category1/ is accessed, the post component will automatically render the BlogList component predisposed to 'category1' filtering.


## Blog Category List

The Blog Category List component allows you to list out categories with relational linking.

* **Alias** The Component Alias name
* **Filter By Parent Page** Blog Posts are filtered by this parent page.
* **Blog Search Page** this should be your parent blog page typically. But can be set to any page that contains the BlogList block.

You can place the Blog Category List component on a page utilizing it's alias like so:

```php
{% component 'proBlogCategories' %}
```

## Blog Tag List

The Blog Tag List component allows you to list out tags with relational linking.

* **Alias** The Component Alias name
* **Filter By Parent Page** Blog Posts are filtered by this parent page.
* **Blog Search Page** this should be your parent blog page typically. But can be set to any page that contains the BlogList block.

You can place the Blog Tag List component on a page utilizing it's alias like so:

```php
{% component 'proBlogTags' %}
```

## Blog Archive List

The Blog Archive List component allows you to list out posts by expand/collaps date navigation with direct linking.

* **Alias** The Component Alias name
* **Filter By Parent Page** Blog Posts are filtered by this parent page.
* **Render Posts By** How posts will be rendered: By that posts' parent, By the global setting, By a specific page
* **Render Page** The page that will render posts if "Specific Page" is selected for the "Render Posts' By" option.

You can place the Blog Archive List component on a page utilizing it's alias like so:

```php
{% component 'proBlogArchive' %}
```

## Related Posts

The Related Posts component provides a list of posts related to the currently viewed posts' tags.

* **Alias** The Component Alias name
* **Number of Related Posts** The number of related posts to list
* **Description Size** Truncate the excerpt size
* **Filter By Parent Page** Blog Posts are filtered by this parent page.
* **Render Posts By** How posts will be rendered: By that posts' parent, By the global setting, By a specific page
* **Render Page** The page that will render posts if "Specific Page" is selected for the "Render Posts' By" option.

You can place the Related Posts component on a page utilizing it's alias like so:

```php
{% component 'proBlogRelated' %}
```
> **Note:** you typically would add this component just under your proBlogPost or proBlogList component.


## ProBlog Integrations

ProBlog conveniently integrates with RainLab's amazing Static Pages plugin.  You can set your Static Menu's to list
and filter results by your ProBlog Categories and Tags.  Simply follow the Static Pages docs to utilize. Having
both plugins installed will provide ProBlog Category and Tag filters as Static Menu Items.


## Utilizing Embed.ly API

If you chose to nab your free Embed.ly API Key and enter it, you can get stupid simple embeding in your posts.

Simply add a link to your embedable content and make it's text = "embedly", and ProBlog will do the rest.

You can see a list of providers here:  http://embed.ly/embed/features/providers

```html
<a href="https://vimeo.com/94705479">embedly</a>
```

## Utilizing the IBM Watson Bluemix NLP

By signing up for IBM Bluemix and creating an NLP service, you can get some rediculous awesome SEO tools when posting your blog entries.

### What is NLP?

NLP stands for Natural Language Processing.  IBM calls this NLU (for understanding) as the technology is an ongoing learning AI that takes mass data and evaluates common language patterns as related to the internet world around us. This is a tremendous asset in terms of evaluating the health and robustness of your Blog content.  ProBlog takes feedback from Bluemix's API results and formulates inteligent information to help you tidy up your content from top to bottom for the most optimal SEO results.

### Setting up Bluemix NLU API

1. Create an IBM Watson Bluemix account.  You will need to provide credit card information, but will only be billed once you have surpassed 30k API Calls in a single month.
2. Go to the Services Dashboard and click on "Create Service"
3. in the search type "Natural Language" and select the Natural Language Understanding API
4. Fill out the information and click on "create"
5. From your Services Dashboard click on your new NLU service item.
6. form the left sidebar click on "Service Credentials"
7. Create a new credential if you do not have one
8. Click on the "View credentials" link in the credential line item you wish to use.
9. Go to your ProBlog settings page in your October backend and click on the "API Settings" tab, and enter your IBM Watson NLU Username and Password information.  Then click 'save'.

And you're good to go!

## Post To Twitter

If you chose to authorize your Twitter account in your ProBlog Settings area, you can post messages directly to twitter from your post create/upadte pane.

* Use the {{url}} twig to auto swap the blog post you are tweeting from.
* If you log into your twitter account and connect your company facebook page to your twitter acount, you can post to both at the same time. First to Twitter, and then from twitter to facebook automatically.
