<?php
use Radiantweb\Problog\Models\Settings as ProblogSettingsModel;

echo "<" . "?" . "xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<"."?"."xml-stylesheet type=\"text/css\" media=\"screen\" href=\"http://feeds.feedburner.com/~d/styles/itemcontent.css\"?>\n";
echo '<rss version="2.0">';
echo '  <channel>';
echo '    <title><![CDATA['.$rss_title.' Blog Feed ]]></title>';
echo '    <link>'.$rss_page.'</link>';
echo '    <description><![CDATA['.$rss_description.']]></description> ';
echo '    <lastBuildDate></lastBuildDate>';

$page_src = explode('/',$rss_page);
$page_src = 'http://'.$page_src[2];

if (count($posts)>0 && is_array($posts)) {
    foreach($posts as $post){
        echo '  <item>';
        echo '    <title><![CDATA['.htmlspecialchars($post->title).']]></title>';
        echo '    <link>';
        echo        $page_src.'/'.$post->parent.'/'.$post->slug.'/';
        echo '    </link>';
        echo '    <description><![CDATA['.$post->excerpt.']]></description>';

        /*
        $tags = preg_split('/\n/', $cobj->getAttribute('tags'));
        if ($tags) {
            foreach($tags as $tag) {
              $feed .= "    <category>";
              $feed .= $tag;
              $feed .= "    </category>";
            }
        }
        */
        echo '    <pubDate>'.date( DATE_RFC822,strtotime($post->published_at)).'</pubDate>';
        echo '  </item>';
    }
}
else {
    echo 'no posts';
}
echo '  </channel>';
echo '</rss>';
