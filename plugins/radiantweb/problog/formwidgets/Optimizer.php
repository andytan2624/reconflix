<?php namespace Radiantweb\Problog\FormWidgets;

use App;
use URL;
use Backend\Classes\FormWidgetBase;
use Radiantweb\Problog\Models\Settings as ProblogSettingsModel;

/**
 * Optimizer SEO Tool
 *
 * @package radiantweb\problog
 * @author ChadStrat
 */
class Optimizer extends FormWidgetBase
{

    /**
     * {@inheritDoc}
     */
    public function init()
    {

    }

    /**
     * {@inheritDoc}
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('optimizer');
    }

    /**
     * Prepares the list data
     */
    public function prepareVars()
    {
        $settings = ProblogSettingsModel::instance();
        $this->vars['bluemix_nlp_username'] = $settings->get('bluemix_nlp_username');
        $this->vars['bluemix_nlp_password'] = $settings->get('bluemix_nlp_password');
        $this->vars['checkUrl'] = URL::to('/radiantweb_api/problog/check_url/valid/');
        $this->vars['checkUrlXmlrpc'] = URL::to('/radiantweb_api/problog/check_url/valid_link/');
    }

    /**
     * {@inheritDoc}
     */
    public function loadAssets()
    {
        $this->addCss('css/font-awesome.css');
        $this->addCss('css/seo_tools.css');
        $this->addJs('js/seo_tools.js');
    }

    /**
    * Process the postback data for this widget.
    * @param $value The existing value for this widget.
    * @return string The new value for this widget.
    */
    public function getSaveValue($value)
    {

    }


    public function onFetchKewordNLP()
    {
        //curl -u "{username}":"{password}" "https://gateway.watsonplatform.net/natural-language-understanding/api/v1/analyze?version=2017-02-27&text=I%20still%20have%20a%20dream%2C%20a%20dream%20deeply%20rooted%20in%20the%20American%20dream%20one%20day%20this%20nation%20will%20rise%20up%20and%20live%20up%20to%20its%20creed%20We%20hold%20these%20truths%20to%20be%20self%20evident%3A%20that%20all%20men%20are%20created%20equal.&features=sentiment,keywords&keywords.sentiment=true&sentiment.targets=the%20American%20dream"

        $url = 'https://gateway.watsonplatform.net/natural-language-understanding/api/v1/analyze';

        $settings = ProblogSettingsModel::instance();
        $username = $settings->get('bluemix_nlp_username');
        $password = $settings->get('bluemix_nlp_password');

        $curl = curl_init($url);

        $text = $_REQUEST['text'];

        if($text){
            // Set some options - we are passing in a useragent too here
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_USERAGENT => 'ProBlog NLP Request',
                CURLOPT_POST => 1,
                CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
                CURLOPT_USERPWD => "$username:$password",
                CURLOPT_POSTFIELDS => json_encode(array(
                    'version' => '2017-02-27',
                    'text' => $text,
                    'features' => array(
                        'keywords' => array(
                            'sentiment' => true,
                            'emotion' => true,
                            'limit' => 10
                        )
                    )
                ))
            ));

            // Send the request & save response to $resp
            $resp = curl_exec($curl);

            $textObj = json_decode($resp);

            // Close request to clear up some resources
            curl_close($curl);

            return json_encode( (object) $textObj->keywords);
        }else{
            return '{}';
        }
    }


}
