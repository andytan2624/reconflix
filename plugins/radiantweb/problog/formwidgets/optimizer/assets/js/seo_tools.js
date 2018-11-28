var phraseWords = null;
var keyWords = null;
var linkUse = null;
var imageUse = null;
var altval = null;
var t5kw = [];
var t5pw = [];

function getSuggestedTagsFromText(data){
    /*
    $.each(data.keywords,function(i,k){
        console.log(k);
        $('#result').append('<br />"'+k.text+'" :  '+k.relevance);
    });
    */
}

function getContent(){
    return $('[name="Post[content]"]').val();
}

function getWordList(text){
    var wordRegExp = /\w+(?:'\w{1,2})?/g;
    var word_list = new Array();
    var matches;
    while ((matches = wordRegExp.exec(text)) != null)
    {
        word_list.push(matches[0]);
    }
    return word_list;
}

function getStatus(status){
    var states = {
        good: '<i class="fa fa-check-circle good"></i>',
        borderline: '<i class="fa fa-exclamation-circle borderline"></i>',
        poor: '<i class="fa fa-minus-circle poor"></i>'
    };

    return states[status];
}

function checkBlockedWords(word){
	
    var blocklist = [
'a','i','able','about','above','abroad','according','accordingly','across','actually','adj','after','afterwards','again','against','ago','ahead','ain\'t','all','allow','allows','almost','alone','along','alongside','already','also','although','always','am','amid','amidst','among','amongst','an','and','another','any','anybody','anyhow','anyone','anything','anyway','anyways','anywhere','apart','appear','appreciate','appropriate','are','aren\'t','around','as','a\'s','aside','ask','asking','associated','at','available','away','awfully','back','backward','backwards','be','became','because','become','becomes','becoming','been','before','beforehand','begin','behind','being','believe','below','beside','besides','best','better','between','beyond','both','brief','but','by','came','can','cannot','cant','can\'t','caption','cause','causes','certain','certainly','changes','clearly','c\'mon','co','co.','com','come','comes','concerning','consequently','consider','considering','contain','containing','contains','corresponding','could','couldn\'t','course','c\'s','currently','dare','daren\'t','definitely','described','despite','did','didn\'t','different','directly','do','does','doesn\'t','doing','done','don\'t','down','downwards','during','each','edu','eg','eight','eighty','either','else','elsewhere','end','ending','enough','entirely','especially','et','etc','even','ever','evermore','every','everybody','everyone','everything','everywhere','ex','exactly','example','except','fairly','far','farther','few','fewer','fifth','first','five','followed','following','follows','for','forever','former','formerly','forth','forward','found','four','from','further','furthermore','get','gets','getting','given','gives','go','goes','going','gone','got','gotten','greetings','had','hadn\'t','half','happens','hardly','has','hasn\'t','have','haven\'t','having','he','he\'d','he\'ll','hello','help','hence','her','here','hereafter','hereby','herein','here\'s','hereupon','hers','herself','he\'s','hi','him','himself','his','hither','hopefully','how','howbeit','however','hundred','i\'d','ie','if','ignored','i\'ll','i\'m','immediate','in','inasmuch','inc','inc.','indeed','indicate','indicated','indicates','inner','inside','insofar','instead','into','inward','is','isn\'t','it','it\'d','it\'ll','its','it\'s','itself','i\'ve','just','k','keep','keeps','kept','know','known','knows','last','lately','later','latter','latterly','least','less','lest','let','let\'s','like','liked','likely','likewise','little','look','looking','looks','low','lower','ltd','made','mainly','make','makes','many','may','maybe','mayn\'t','me','mean','meantime','meanwhile','merely','might','mightn\'t','mine','minus','miss','more','moreover','most','mostly','mr','mrs','much','must','mustn\'t','my','myself','name','namely','nd','near','nearly','necessary','need','needn\'t','needs','neither','never','neverf','neverless','nevertheless','new','next','nine','ninety','no','nobody','non','none','nonetheless','noone','no-one','nor','normally','not','nothing','notwithstanding','novel','now','nowhere','obviously','of','off','often','oh','ok','okay','old','on','once','one','ones','one\'s','only','onto','opposite','or','other','others','otherwise','ought','oughtn\'t','our','ours','ourselves','out','outside','over','overall','own','particular','particularly','past','per','perhaps','placed','please','plus','possible','presumably','probably','provided','provides','que','quite','qv','rather','rd','re','really','reasonably','recent','recently','regarding','regardless','regards','relatively','respectively','right','round','said','same','saw','say','saying','says','second','secondly','see','seeing','seem','seemed','seeming','seems','seen','self','selves','sensible','sent','serious','seriously','seven','several','shall','shan\'t','she','she\'d','she\'ll','she\'s','should','shouldn\'t','since','six','so','some','somebody','someday','somehow','someone','something','sometime','sometimes','somewhat','somewhere','soon','sorry','specified','specify','specifying','still','sub','such','sup','sure','take','taken','taking','tell','tends','th','than','thank','thanks','thanx','that','that\'ll','thats','that\'s','that\'ve','the','their','theirs','them','themselves','then','thence','there','thereafter','thereby','there\'d','therefore','therein','there\'ll','there\'re','theres','there\'s','thereupon','there\'ve','these','they','they\'d','they\'ll','they\'re','they\'ve','thing','things','think','third','thirty','this','thorough','thoroughly','those','though','three','through','throughout','thru','thus','till','to','together','too','took','toward','towards','tried','tries','truly','try','trying','t\'s','twice','two','un','under','underneath','undoing','unfortunately','unless','unlike','unlikely','until','unto','up','upon','upwards','us','use','used','useful','uses','using','usually','v','value','various','versus','very','via','viz','vs','want','wants','was','wasn\'t','way','we','we\'d','welcome','well','we\'ll','went','were','we\'re','weren\'t','we\'ve','what','whatever','what\'ll','what\'s','what\'ve','when','whence','whenever','where','whereafter','whereas','whereby','wherein','where\'s','whereupon','wherever','whether','which','whichever','while','whilst','whither','who','who\'d','whoever','whole','who\'ll','whom','whomever','who\'s','whose','why','will','willing','wish','with','within','without','wonder','won\'t','would','wouldn\'t','yes','yet','you','you\'d','you\'ll','your','you\'re','yours','yourself','yourselves','you\'ve','zero','&quot;','&amp;','&','&lt;','<','&gt;','>','&nbsp;','&iexcl;','¡','&cent;','¢','&pound;','£','&curren;','¤','&yen;','¥','&brvbar;','¦','&sect;','§','&uml;','¨','&copy;','©','&ordf;','ª','&laquo;','«','&not;','¬','&shy;','&reg;','®','&macr;','¯','&deg;','°','&plusmn;','±','&sup2','²','&sup3;','³','&acute;','´','&micro;','µ','&para;','¶','&middot;','·','&cedil;','¸','&sup1;','¹','&ordm;','º','&raquo;','»','&frac14;','¼','&frac12;','½','&frac34;','¾','&iquest;','¿','&times;','×','&divide;','÷','&ETH;','Ð','&eth;','ð','&THORN;','Þ','&thorn;','þ','&AElig;','Æ','&aelig;','æ','&OElig;','Œ','&oelig;','œ','&Aring;','Å','&Oslash;','Ø','&Ccedil;','Ç','&ccedil;','ç','&szlig;','ß','&Ntilde;','Ñ','&ntilde;','ñ'
    ];
    return $.inArray(word.toLowerCase(), blocklist)
}

function stripBlockedWords(word_list){
    var stripped_word_list = new Array();
    word_list.forEach(function(word){
        if( checkBlockedWords(word) == -1 ){
            stripped_word_list.push(word);
        }
    })
    return stripped_word_list;
}

function getKeywordsFromText(data){

    $('#phrase_result tbody').empty();
    var t = 0;
    var content = getContent();
    var text = content.replace(/(<([^>]+)>|&nbsp;)/ig,"");
    var word_list = text.split(/\W+/);
    var stripped_list = stripBlockedWords(word_list);
    $.each(data,function(i,k){
        var re = new RegExp(k.text, 'g');
        var count = 0;
        if(text.match(re)){
            count = text.match(re).length;
        }
        var density = count/stripped_list.length;
        var percent = Math.round((density * 100) * 10);

        if(percent > 2.0){
            var status = getStatus('good');
            phraseWords = 1;
        }else if(percent <= 2.0 && percent >= 0.8){
            var status = getStatus('borderline');
        }else if(percent < 0.8){
            var status = getStatus('poor');
        }

        if(t<6){
            t5pw.push(k.text.toLowerCase());
        }
        t++;

        $('#phrase_result tbody').append('<tr><td>'+k.text+'</td><td>'+k.relevance+'</td><td>'+count+'</td><td>'+percent+'%  '+status+'</td></tr>');
    });
}


function getTopNWords(text, n){

    var wordRegExp = /\w+(?:'\w{1,2})?/g;
    var words = {};
    var matches;
    while ((matches = wordRegExp.exec(text)) != null)
    {
        var word = matches[0].toLowerCase();
        if(!$.isNumeric( word )){
            if (typeof words[word] == "undefined")
            {
                words[word] = 1;
            }
            else
            {
                words[word]++;
            }
        }

    }

    var wordList = [];

    for (var word in words)
    {
        if( checkBlockedWords(word) == -1 ){
            if (words.hasOwnProperty(word))
            {
                wordList.push([word, words[word]]);
            }
        }
    }

    wordList.sort(function(a, b) { return  parseInt(b[1],10) -  parseInt(a[1],10); });

    var topWords = [];
    if(wordList.length > 0){
	    for (var i = 0; i < n; i++)
	    {
	        topWords.push(wordList[i][0]);
	    }
	}
    return topWords;
}


function scrapeSingleKeywords(){

    $('#single_result tbody').empty();

    var o = 0;
    var content = getContent();
    var text = content.replace(/(<([^>]+)>|&nbsp;)/ig,"").toLowerCase();
    var word_list = getWordList(text);

    var stripped_list = stripBlockedWords(word_list);

    var counts = {};

    for(var i = 0; i < word_list.length; ++i){
        var word = word_list[i];
        counts[word] = (counts[word] || 0) + 1;
    }

    var densities = {};

    for(word in counts){
        densities[word] = counts[word]/stripped_list.length;
    }

    var keywords = getTopNWords(text,10);

    $.each(keywords,function(t,word){
        var percent = Math.round((densities[word] * 100) * 10)/10;
        var state = null;
        if(percent > 2.0){
            var status = getStatus('good');
            state = 'good';
            keyWords = 1;
        }else if(percent <= 2.0 && percent >= 0.8){
            var status = getStatus('borderline');
        }else if(percent < 0.8){
            var status = getStatus('poor');
        }

        if(o<6){
            t5kw.push(word.toLowerCase());
        }
        o++;

        $('#single_result tbody').append('<tr class="'+state+'"><td>'+word+'</td><td>'+counts[word]+'</td><td>'+percent+'%  '+status+'</td></tr>' );
    });
}


function getArticleLinks(){

    $('#links_result tbody').empty();

    var content = getContent();
    var text = content.replace(/(<([^>]+)>|&nbsp;)/ig,"").toLowerCase();
    var word_list = getWordList(text);
    var stripped_list = stripBlockedWords(word_list);

    var doc = document.createElement("html");
    doc.innerHTML = getContent();
    var links = doc.getElementsByTagName("a");

    if(links.length > 0){
        var t = links.length;

        var densities = t/stripped_list.length;
        var percent = Math.round((densities * 100) * 10)/10;

        $('#link_metric').html('('+percent+'%)');

        if( percent > 0.5 ){
            $('#metrics .links').removeClass('fa-circle-o');
            $('#metrics .links').addClass('fa-check-circle good');
        }

        $.each(links,function(){
            var link = $(this).attr('href');
            var rel = $(this).attr('rel');

            $.ajax({
                url: checkUrlXmlrpc+'?link='+link,
                success: function(data){
                    var checks = $.parseJSON(data);
                    var valid = '';
                    var xmlrpc = '';
                    if(checks.valid_url === 1){
                        valid = getStatus('good');
                    }
                    if(checks.xmlrpc === 1){
                        xmlrpc = getStatus('good');
                    }
                    if(rel == 'nofollow'){
                        relnofollow = getStatus('good');
                    }else{
                        relnofollow = getStatus('borderline');
                    }
                    $('#links_result tbody').append('<tr><td>'+link+'</td><td>'+valid+'</td><td>'+xmlrpc+'</td><td>'+relnofollow+'</td></tr>');
                },
                error: function(e){

                }
            });
        });
    }

}


function getArticleImages(){

    $('#img_result tbody').empty();

    var content = getContent();
    var text = content.replace(/(<([^>]+)>)/ig,"");
    var word_list = text.split(/\W+/);
    var stripped_list = stripBlockedWords(word_list);

    var doc = document.createElement("html");
    doc.innerHTML = getContent();
    var images = doc.getElementsByTagName("img");

    if(images.length > 0){
        var t = images.length;

        var densities = t/stripped_list.length;
        var percent = Math.round((densities * 100) * 10)/10;

        $('#image_metric').html('('+percent+'%)');

        if( percent > 0.5 ){
            $('#metrics .images').removeClass('fa-circle-o');
            $('#metrics .images').addClass('fa-check-circle good');
        }

        $.each(images,function(){
            var link = $(this).attr('src');
            var alt = $(this).attr('alt');

            $.ajax({
                url: checkUrl+'?link='+link,
                success: function(data){
                    var checks = $.parseJSON(data);
                    var valid = '';
                    if(checks.valid_url === 1){
                        valid = getStatus('good');
                    }
                    if(alt){
                        altval = getStatus('good');
                    }
                    $('#img_result tbody').append('<tr><td>'+link+'</td><td>'+valid+'</td><td>'+altval+'</td></tr>');
                },
                error: function(e){

                }
            });
        });

    }

}

function doSeoChecks(){

    if($('input[name="Post[meta_title]"]').val().length > 0){
        $('#metrics .meta-title').removeClass('fa-circle-o');
        $('#metrics .meta-title').addClass('fa-check-circle good');
    }

    if($('textarea[name="Post[meta_description]"]').val().length > 0){
        $('#metrics .meta-description').removeClass('fa-circle-o');
        $('#metrics .meta-description').addClass('fa-check-circle good');
    }

    if($('textarea[name="Post[meta_keywords]"]').val().length > 0){
        $('#metrics .meta-keywords').removeClass('fa-circle-o');
        $('#metrics .meta-keywords').addClass('fa-check-circle good');
    }

    if(phraseWords > 0){
        $('#metrics .keyphrase').removeClass('fa-circle-o');
        $('#metrics .keyphrase').addClass('fa-check-circle good');
    }

    if(keyWords > 0){
        $('#metrics .keyword').removeClass('fa-circle-o');
        $('#metrics .keyword').addClass('fa-check-circle good');
    }
}

function doMetaChecks(){
    var meta_title = $('input[name="Post[meta_title]"]').val().toLowerCase();

    t5pw.forEach(function(phrase){
        if(meta_title.indexOf(phrase.toLowerCase()) >= 0){
            $('#meta_stats .meta-title').removeClass('fa-circle-o');
            $('#meta_stats .meta-title').addClass('fa-check-circle good');
        }
    });


    meta_description = $('textarea[name="Post[meta_description]"]').val();
    word_list = meta_description.split(/\W+/);
    var word_use = null;
    var c = 0;
    word_list.forEach(function(word){
        if($.inArray(word.toLowerCase(), t5kw) >= 0){
            c++
        }
    });
    if(c>4){
        $('#meta_stats .meta-description').removeClass('fa-circle-o');
        $('#meta_stats .meta-description').addClass('fa-check-circle good');
    }

    meta_keywords= $('textarea[name="Post[meta_keywords]"]').val();
    word_list = meta_keywords.split(',');

    var word_use = null;
    var c = 0;
    var t = 0;
    word_list.forEach(function(word){
        if($.inArray(word.toLowerCase(), t5pw) >= 0){
            c++
        }
        if($.inArray(word.toLowerCase(), t5kw) >= 0){
            t++
        }
    });
    if(c > 4 && t > 4){
        $('#meta_stats .meta-keywords').removeClass('fa-circle-o');
        $('#meta_stats .meta-keywords').addClass('fa-check-circle good');
    }


    var c = 0;
    $('#Relation-formTags-tags .checkbox label').each(function(){

        if($.inArray($(this).text().toLowerCase(), t5kw) >= 0){
            c++
        }
    });

    if(c > 2){
        $('#meta_stats .post-tags').removeClass('fa-circle-o');
        $('#meta_stats .post-tags').addClass('fa-check-circle good');
    }
}

function doHtmlChecks(){
    var title = $('input[name="Post[title]"]').val().toLowerCase();
    var c = 0;
    t5pw.forEach(function(phrase){
        if(title.indexOf(phrase.toLowerCase()) >= 0){
            c++
        }
    });
    if(c > 0){
        $('#html_stats .title').removeClass('fa-circle-o');
        $('#html_stats .title').addClass('fa-check-circle good');
    }

    var post = getContent();
    if(post.indexOf('<h1') == -1){
        $('#html_stats .no-h1s').removeClass('fa-circle-o');
        $('#html_stats .no-h1s').addClass('fa-check-circle good');
    }

    if(post.indexOf('<h2') >= 0){
        $('#html_stats .h2s').removeClass('fa-circle-o');
        $('#html_stats .h2s').addClass('fa-check-circle good');
    }

    if(post.indexOf('<blockquote') >= 0){
        $('#html_stats .blockquotes').removeClass('fa-circle-o');
        $('#html_stats .blockquotes').addClass('fa-check-circle good');
    }
}
