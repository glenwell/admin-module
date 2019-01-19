
function tinymce_init_callback(editor)
{
    editor.remove();
    editor = null;

    tinymce.init({
        menubar: false,
        selector:'textarea.richTextBox',
        skin: 'voyager',
        min_height: 600,
        resize: 'vertical',
        plugins: 'link, image, code, youtube, giphy, table, textcolor, lists, wordcount',
        extended_valid_elements : 'input[id|name|value|type|class|style|required|placeholder|autocomplete|onclick]',
        file_browser_callback: function(field_name, url, type, win) {
            if(type =='image'){
                $('#upload_file').trigger('click');
            }
        },
        toolbar: 'styleselect bold italic underline | forecolor backcolor | alignleft aligncenter alignright | bullist numlist outdent indent | link image table youtube giphy | code',
        convert_urls: false,
        image_caption: true,
        image_title: true,
        setup:function(ed) {
            ed.on('load click change keyup cut paste', function(e) {
                populateTinyMceStats( $('<div>'+ed.getContent()+'</div>') );
            });
        }
      });
}

function populateTinyMceStats(content){
    if(true)
    {
        var keyword = $('#focus_keywords').val();
        var one = checkFleschRate(content);
        var two = checkWordCount(content);
        var three = checkParagraphLengths(content, 150);
        var four = checkKeywordFirstParagraph(keyword, content);
        var five = checkSentenceLength(content);
        var six = checkKeywordDensity(keyword, content);
        var seven = checkH1NotPresent(content);
        var eight = checkHeadingsPresence(content);
        var nine = checkKeywordHeading(keyword, content);
        var ten = checkKeywordHeadingBegins(keyword, content);
        var eleven = checkLinks(content);
        var twelve = checkImgsExist(content);
        var thirteen = checkImgsHaveAlts(content);
        var fourteen = checkImgAltsHaveKeywords(keyword, content);

        var total = (one + two + three + four + five + six + seven + eight + nine + ten + eleven
            + twelve + thirteen + fourteen);

        $('#body_score').val(total);
        
        calculateScore();
    }
}


/* Related to Tiny MCE */
{
    function checkFleschRate(body, idealRate = 60) {
        var rate = getFleschRate(body.text());
        return checkCondition(rate >= idealRate, '#body_flesch', rate);
    }

    function checkWordCount(body, minLength = 300) {
        var count = getWordCount(body.text());
        return checkCondition(count >= minLength, '#body_length', count+' Word/s');
    }

    function checkParagraphLengths(body, maxLength = 150) {
        
        var invalid = getParagraphsWithInvalidLengths(body, maxLength);
        if (typeof invalid == typeof false){
            return checkCondition(false, '#body_paragraphs');
        } else {
            var showText = invalid > 0 ? invalid+' exceed/s limit' : null;
            return checkCondition(invalid <= 0, '#body_paragraphs', showText);
        }
    }

    function checkKeywordFirstParagraph(keyword, body) {
        return checkCondition(hasKeyword(keyword, getFirstParagraph(body)), '#body_paragraph_intro');
    }

    function checkSentenceLength(body, maxLength = 20, idealPercentage = 25) {
        return checkCondition(bodyHasValidSentenceLength(body.text(), maxLength, idealPercentage), '#body_sentence_length');
    }

    function checkKeywordDensity(keyword, body, minPercentage = 0.5, maxPercentage = 2.5) {
        var keywordDensity = getKeywordDensity(keyword, body.text());
        return checkCondition( (keywordDensity >= minPercentage && keywordDensity <= maxPercentage), '#body_keyword_density', keywordDensity+'%');
    }

    function checkH1NotPresent(body) {
        return checkCondition( !(getHeadings(body, 'h1').length > 0), '#heading_h1');
    }

    function checkHeadingsPresence(body) {
        return checkCondition( (getHeadings(body, 'h2, h3').length > 0), '#heading_exists');
    }

    function checkKeywordHeading(keyword, body, minPercentage = 30, maxPercentage = 75) {
        var keywordHeading = getHeadingsWithKeyword(keyword, body);
        return checkCondition( (keywordHeading >= minPercentage && keywordHeading <= maxPercentage), '#heading_keyword', keywordHeading+'%');
    }

    function checkKeywordHeadingBegins(keyword, body, minPercentage = 30, maxPercentage = 75) {
        return checkCondition(hasKeywordInHeadingStart(keyword, body, minPercentage, maxPercentage) , '#heading_keyword_begins');
    }

    function checkLinks(body){
        var links = getInternaExternalLinks(body);
        var internal = false;
        var external = false;

        internal = checkCondition(links.internal > 0 , '#links_internal', links.internal);
        external = checkCondition(links.external > 0 , '#links_external', links.external);

        return external && internal;
    }

    function checkImgsExist(body) {
        return checkCondition(hasImages(body), '#images_exists');
    }

    function checkImgsHaveAlts(body) {
        return checkCondition(imagesHaveAlt(body), '#image_has_alt');
    }

    function checkImgAltsHaveKeywords(keyword, body, minPercentage = 20) {
        return checkCondition(imageAltsHaveKeyword(keyword, body, minPercentage), '#image_has_keyword');
    }

}

function analyzeFormFields(){
    var keyword = $('#focus_keywords').val();
    var title = $('#title').val();
    var seoTitle = $('#seo_title').val();
    var metaDesc = $('#meta_description').val();
    var imgMeta = $('#image_meta').val();

    return {
        "keyword" : keyword,
        "title" : title,
        "seoTitle" : seoTitle,
        "metaDesc" : metaDesc,
        "imgMeta" : imgMeta
    }
}

function populateFormStats (){
    var fields = analyzeFormFields();
    var one = checkKeywordSet(fields.keyword);
    var two = checkKeywordLength(fields.keyword);
    var three = checkTitleLength(fields.title);
    var four = checkTitleKeyword(fields.keyword, fields.title);
    var five = checkSeoTitleLength(fields.seoTitle);
    var six = checkSeoTitleKeyword(fields.keyword, fields.seoTitle);
    var seven = checkSeoTitleKeywordBegins(fields.keyword, fields.seoTitle);
    var eight = checkSeoTitleKeywordOnce(fields.keyword, fields.seoTitle);
    var nine = checkImgKeyword(fields.keyword, fields.imgMeta);
    var ten = checkMetaLength(fields.metaDesc);
    var eleven = checkMetaKeyword(fields.keyword, fields.metaDesc);
    
    var total = (one + two + three + four + five + six + seven + eight + nine + ten + eleven);

    $('#form_score').val(total);

    calculateScore();
}

function calculateScore() {
    var mceScore = $('#body_score').val();
    var formScore = $('#form_score').val();
    var sum = parseInt(mceScore) + parseInt(formScore);
    var percentage = getPercentage( sum, 25 );
    $('#seo_score_display').html(percentage);
    $('#seo_score').val(percentage);

    if(percentage >= 0 && percentage <= 33){
        $('.seo-highlight').css('background-color', '#ed181f');
    }else if(percentage >= 34 && percentage <= 66){
        $('.seo-highlight').css('background-color', '#ff9326');
    }else if(percentage >= 67 && percentage <= 100){
        $('.seo-highlight').css('background-color', '#2e7d32');
    }
}

{
    function checkCondition(condition, id, stat = null) {
        stat != null ? $(id+'_extra').html('('+stat+')') : $(id+'_extra').html('');
        if (condition){
            $(id).closest('span').css('background-color', '#7ad03a');
            return 1;
        } else {
            $(id).closest('span').css('background-color', '');
            return 0;
        }
    }

    function checkKeywordSet (keyword){
        return checkCondition(keyword.trim() != '', '#keyword_set')
    }

    function checkKeywordLength(keyword) {
        return checkCondition(hasRequiredLength( getWords(keyword) , 0, 5 ), '#keyword_length', getWordCount(keyword)+' Words')
    }

    function checkTitleLength(title) {
        return checkCondition(hasRequiredLength( title , 45, 60 ), '#title_length', title.length+' Chars')
    }

    function checkTitleKeyword(keyword, title) {
        return checkCondition(hasKeyword(keyword, title), '#title_keyword')
    }

    function checkSeoTitleLength(seoTitle) {
        return checkCondition(hasRequiredLength( seoTitle , 40, 55 ), '#title_seo_length',  seoTitle.length+' Chars')
    }

    function checkSeoTitleKeyword(keyword, title) {
        return checkCondition(hasKeyword(keyword, title), '#title_seo_keyword')
    }

    function checkSeoTitleKeywordBegins(keyword, seoTitle) {
        return checkCondition(keywordBegins(keyword, seoTitle), '#title_seo_keyword_begins')
    }

    function checkSeoTitleKeywordOnce(keyword, seoTitle) {
        //console.log(getKeywordFrequency(keyword, seoTitle));
        return checkCondition(getKeywordFrequency(keyword, seoTitle) == 1, '#title_seo_keyword_once')
    }

    function checkImgKeyword(keyword, imgMeta) {
        if(imgMeta != ''){
            var alt = JSON.parse(imgMeta).alt;
            return checkCondition(hasKeyword(keyword, alt), '#image_post_alt')
        }
        
        return false;
    }

    function checkMetaLength(meta, minLength = 100, maxLength = 155) {
        return checkCondition(hasRequiredLength(meta, minLength, maxLength), '#meta_desc_length', meta.length+' Chars')
    }

    function checkMetaKeyword(keyword, meta) {
        return checkCondition(hasKeyword(keyword, meta), '#meta_desc_has_keyword')
    }
}

function populateFields () {
    var lists = [
        {
            "title" : "Keyword",
            "labels" : [
                "Keyword set",
                /* "Keyword never used before", */
                "Keyword length"
            ],
            "ids" : [
                'keyword_set',
                /* 'keyword_unique', */
                'keyword_length',
            ]
        },
        {
            "title" : "Title",
            "labels" : [
                "Title length",
                "Primary keyword in title",
                "SEO title",
                "Primary keyword in SEO Title",
                "Keyword towards beginning of SEO Title",
                "Keyword used once in SEO title"
            ],
            "ids" : [
                'title_length',
                'title_keyword',
                'title_seo_length',
                'title_seo_keyword',
                'title_seo_keyword_begins',
                'title_seo_keyword_once'
            ]
        },
        {
            "title" : "Body",
            "labels" : [
                "Flesch Readability score",
                "Post length",
                "Paragraph length",
                "Keyword in introduction",
                "Sentence length",
                "Keyword density"
            ],
            "ids" : [
                'body_flesch',
                'body_length',
                'body_paragraphs',
                'body_paragraph_intro',
                'body_sentence_length',
                'body_keyword_density'
            ]
        },
        {
            "title" : "Headings",
            "labels" : [
                "No H1 headings found",
                "Has subheadings (H2 and H3)",
                "Keyword present in headings",
                "Keyword at beginning of subheading"
            ],
            "ids" : [
                'heading_h1',
                'heading_exists',
                /* 'heading_distribution', */
                'heading_keyword',
                'heading_keyword_begins'
            ]
        },
        {
            "title" : "Links",
            "labels" : [
                "Internal links found",
                "External links found"
            ],
            "ids" : [
                'links_internal',
                'links_external'
            ]
        },
        {
            "title" : "Images",
            "labels" : [
                "Post image alt attribute set",
                "Images found in post",
                "Images in post have alt attributes",
                "Images in the body have keyword in alt attributes"
            ],
            "ids" : [
                'image_post_alt',
                'images_exists',
                'image_has_alt',
                'image_has_keyword'
            ]
        },
        {
            "title" : "Meta Description",
            "labels" : [
                "Length of metadescription met",
                "Keyword found in metadescription"
            ],
            "ids" : [
                'meta_desc_length',
                'meta_desc_has_keyword'
            ]
        },
    ]

    var holder = '';

    lists.forEach(function(list, listIndex) {
        var child = '';
        var group = '';
        var first = '';

        list.labels.forEach(function(label, index){
            child = child + '<li><span id="'+list.ids[index]+'" class="stat-icon"></span>'+label+' <span id="'+list.ids[index]+'_extra"></span></li>';
        });

        first = (listIndex == 0) ? 'in' : '';
        
        group = '<div class="panel panel-accordion"><div class="panel-heading">'+
            '<a class="link-unstyled" data-toggle="collapse" data-parent="#accordion" href="#collapse'+listIndex+'">'+list.title+'</a></div>'+
            '<div id="collapse'+listIndex+'" class="panel-collapse collapse '+first+'"><ul id="seo_analysis" class="list-unstyled list-regular">'+child+
            '</ul></div></div>';
        
        holder = holder + group;
    });

    $('#accordion').html(holder);

    holder = '';

    populateFormStats();
}

/**
 *  
 * CHARACTER METHODS
 * 
 */

{
    
    /* Return the number of characters in the text */
    function getCharacterCount(selectedText) {
        return selectedText.length;
    }
}


/**
 *  
 * WORDS METHODS
 * 
*/

{
    /* Return an array of words based on a delimiter*/
    function getWords(selectedText, delimiter = ' ') {
        //return selectedText.trim().replace(/\s+/gi, ' ').split(' ');
        return selectedText.trim().replace(/\s+/gi, ' ')
            .split(delimiter).map(Function.prototype.call, String.prototype.trim);
    }

    /* Return an integer with the number of words */
    function getWordCount(selectedText) {
        var wordsArray = getWords(selectedText);
        return wordsArray[0].trim() == '' ? 0 : wordsArray.length;
    }

    /* Return a float with number of words per sentence */
    function getWordsPerSentence(selectedText) {
        return getWordCount(selectedText) / getSentenceCount(selectedText);
    }

    /* Return a boolean indicating if the text has required length */
    function hasRequiredLength(selectedText, min, max) {
        if (!selectedText.length || selectedText[0] == '') {
            return false;
        }
        return !(selectedText.length < min || selectedText.length > max);
    }

    /**
     * Check if the text has the keyword
     * 
     * @param keyword string - The keyword 
     * @param selectedText string - The text to search the keyword from
     */
    function hasKeyword(keyword, selectedText) {
        return keyword != '' ? new RegExp( '\\b' + keyword + '\\b', 'i').test(selectedText) : false;
    }

    /* Return a boolean indicating if the keyword is in the first half of the text */
    function keywordBegins(keyword, selectedText) {
        //Must have keyword in first half of sentence
        return hasKeyword(keyword, selectedText) ? 
        selectedText.search(keyword) < (getCharacterCount(selectedText) / 2) : false;
    }

    /* Return an integer indicating the number of times a keyword appears in the text */
    function getKeywordFrequency(keyword, selectedText){
        keyword = keyword.toLowerCase();
        selectedText = selectedText.toLowerCase();

        if(hasKeyword(keyword, selectedText)) {
            var freqMap = {};
            getWords(selectedText).forEach(function(w) {
                if (!freqMap[w]) {
                    freqMap[w] = 0;
                }
                freqMap[w] += 1;
            });

            return freqMap[keyword];
        }
        return 0;
    }

    function getKeywordDensity (keyword, selectedText){
        var frequency = getKeywordFrequency(keyword, selectedText);
        var totalWords = getWordCount(selectedText);
        
        /* 
            Check keyword words in the text
            var keywordsArray = getWords(keyword);
            var sentencesArray = getSentences(selectedText);
            sentencesArray.forEach(function(sentence, sentenceIndex) {
            var lacks = false;
            keywordsArray.forEach(function(word, keywordIndex) {
                if(!hasKeyword(word, sentence)){
                    lacks = true;
                    break;
                }
            }); 
        }); */
    
        return getPercentage(frequency, totalWords);
    }

    /* Return a boolean indicating if the text has enough keywords */
    function hasEnoughKeywords(keyword, selectedText, min) {
        var frequency = getKeywordFrequency(keyword, selectedText);
        return frequency > 0 && frequency <= min;
    }
}

/**
 *  
 * PARAGRAPH METHODS
 * 
*/

{
    /* Return an object of paragraphs in the text */
    function getParagraphs(selectedText) {
        return selectedText.find('p');
    }

    /* Return a boolean indicating whether the text has paragraphs */
    /* function hasParagraphs(selectedText) {
        var paragraphsArray = getParagraphs(selectedText);
        return paragraphsArray.length > 0 ? $(paragraphsArray[0]).text() != '' : false;
    } */

    /* Return a string of the first paragraph in the text */
    function getFirstParagraph(selectedText) {
        return $(getParagraphs(selectedText).first()).text();
    }

    /* Return a string of the last paragraph in the text */
    function getLastParagraph(selectedText) {
        return $(getParagraphs(selectedText).last()).text();
    }

    /* Return an integer of the number of words in the paragraph */
    function getParagraphLength(selectedText) {
        return getWordCount($(selectedText).text());
    }

    function getParagraphsWithInvalidLengths(selectedText, maxLength) {
        var invalidParagraphs = 0;
        var paragraphsArray = getParagraphs(selectedText);

        if(paragraphsArray.length > 0 && $(paragraphsArray[0]).text() != '') {
            getParagraphs(selectedText).each( function (index, paragraph) {
                if(!(getParagraphLength(paragraph) <= maxLength)) {
                    invalidParagraphs++;
                }
            });
    
            return invalidParagraphs;
        }

        return false;
    }

    /* Return an boolean indicating whether the paragraph has the keyword */
    function paragraphHasKeyword(keyword, selectedText) {
        return hasKeyword(keyword, selectedText);
    }
}

/**
 *  
 * HEADING METHODS
 * 
*/

{
    /* Return an object of headings in the text */
    function getHeadings(selectedText, size = 'h2, h3') {
        return selectedText.find(size);
    }

    /* Return a boolean indicating whether the text has H1 headings */
    function hasH1Headings(selectedText) {
        return selectedText.find('h1').length > 0;
    }

    /* Return an integer with the percentage of  headings that contain the main keyword */
    function getHeadingsWithKeyword(keyword, selectedText) {
        var validHeadings =  0;
        var invalidHeadings = 0;

        getHeadings(selectedText).each( function (index, value) {
            if(hasKeyword(keyword, $(value).text())) {
                validHeadings++;
            } else {
                invalidHeadings++;
            }
        });

        return getPercentage(validHeadings, validHeadings + invalidHeadings);
    }

    /* Return an integer with the percentage of  headings that contain the main keyword in the first half */
    function keywordBeginsInHeadings(keyword, selectedText) {
        var validHeadings =  0;
        var invalidHeadings = 0;

        getHeadings(selectedText).each( function (index, value) {
            if(keywordBegins(keyword, $(value).text())) {
                validHeadings++;
            } else {
                invalidHeadings++;
            }
        });

        return getPercentage(validHeadings, (validHeadings + invalidHeadings));
    }

    /* Return a boolean indicating if the headings have the keyword */
    function hasKeywordsInHeading(keyword, selectedText, minPercentage, maxPercentage) {
        var headingsPercentage = getHeadingsWithKeyword(keyword, selectedText);
        return headingsPercentage >= minPercentage && headingsPercentage <= maxPercentage;
    }

    /* Return a boolean indicating if the headings have the keywords at the start */
    function hasKeywordInHeadingStart(keyword, selectedText, minPercentage, maxPercentage) {
        var headingsPercentage = keywordBeginsInHeadings(keyword, selectedText);
        return headingsPercentage >= minPercentage && headingsPercentage <= maxPercentage;
    }

    function getPercentage(completed, total){
        var percentage = 0;

        if(isNaN(total) || isNaN(completed) || (completed == 0 && total == 0 ) ){
            percentage = 0;
        }else{
            percentage = ((completed/total) * 100).toFixed(2);
        }

        return percentage;
    }
}



/**
 *  
 * LINK METHODS
 * 
*/

{
    /* Return an object of headings in the text */
    function getLinks(selectedText) {
        return selectedText.find("a");
    }

    /* Return a boolean indicating if a link is external */
    function isLinkExternal(url) {
        var host         = window.location.hostname.toLowerCase(),
        regex        = new RegExp('^(?:(?:f|ht)tp(?:s)?\:)?//(?:[^\@]+\@)?([^:/]+)', 'im'),
        match        = url.match(regex),
        domain       = ((match ? match[1].toString() : ((url.indexOf(':') < 0) ? host : ''))).toLowerCase();
        
        return domain != host;
    }

    /* Return an object holding integers with the number of external and internal links */
    function getInternaExternalLinks(selectedText) {
        var internalLink =  [];
        var externalLink = [];
        getLinks(selectedText).each(function(index, value) {
            if(isLinkExternal($(value).attr('href'))) {
                externalLink.push($(value).attr('href'));
            } else {
                internalLink.push($(value).attr('href'));
            }
        });

        var internal = internalLink.length > 0 ? internalLink.length : 0;
        var external = externalLink.length > 0 ? externalLink.length : 0;

        return {
            "internal" : internal,
            "external" : external
        };
    }
}

/**
 *  
 * IMAGE METHODS
 * 
*/
{
    /* Return an object of images in the text */
    function getImages(selectedText) {
        return selectedText.find("img");
    }

    /* Return an integer with number of images in the text */
    function getImagesCount(selectedText) {
        return getImages(selectedText).length;
    }

    function hasImages(selectedText) {
        return getImagesCount(selectedText) > 0;
    }

    function imageHasAltTag(selectedImage) {
        var attr = $(selectedImage).attr('alt');
        return typeof attr !== typeof undefined && attr !== false;
    }

    /* Return a boolean indicating if all images have alt tags */
    function imagesHaveAlt(selectedText) {
        if(hasImages(selectedText)) {
            var lacksAlt = [];
            getImages(selectedText).each(function(index, value) {
                if(!imageHasAltTag(value)) {
                    lacksAlt.push(true);
                }
            })

            return !(lacksAlt.length > 0);
        }
        
        return false;
    }

    /* Return a boolean indicating if ideal number of image alt tags have keywords */
    function imageAltsHaveKeyword(keyword, selectedText, idealPercentage = 40) {
        if(hasImages(selectedText)) {
            var altHasKeyword = [];
            var altLacksKeyword = [];
            getImages(selectedText).each(function(index, value) {
                if(imageHasAltTag(value)) {
                    if(hasKeyword(keyword, $(value).attr('alt')))
                    {
                        altHasKeyword.push(true);
                    } else {
                        altLacksKeyword.push(false);
                    }
                }
            })

            var hasKeywordCount = altHasKeyword.length;
            var lacksKeywordCount = altLacksKeyword.length;
            var totalCount = hasKeywordCount + lacksKeywordCount;

            return !(getPercentage(hasKeywordCount, totalCount) <= idealPercentage);
        }
        
        return false;
    }
}

/**
 *  
 * SENTENCES METHODS
 * 
*/

{
    /* Return an array of sentences (Cannot count for indirect speech) */
    function getSentences(selectedText) {
        return selectedText.trim().split(/[\.\?\!]\s/).map(Function.prototype.call, String.prototype.trim);
    }

    /* Return an integer with the number of sentences */
    function getSentenceCount(selectedText) {
        return getSentences(selectedText).length;
    }

    /* Return an integer with the length of a sentence */
    function getSentenceLength(selectedText) {
        return getWordCount(selectedText);
    }

    /* Return a boolean indicating if a sentence has a valid length */
    function sentenceHasValidLength(selectedText, maxLength = 20) {
        return getSentenceLength(selectedText) <= maxLength;
    }

    /* Return a boolean indicating if a sentence has a valid length */
    function bodyHasValidSentenceLength(selectedText, maxLength = 20, idealPercentage = 25) {
        var validSentences =  [];
        var invalidSentences = [];
        var sentencesArray = getSentences(selectedText);

        if(sentencesArray.length > 0 && sentencesArray[0] != '') {
            sentencesArray.forEach(function (value, index) {
                if(sentenceHasValidLength(value, maxLength)) {
                    validSentences.push(true);
                } else {
                    invalidSentences.push(false);
                }
            })

            var validCount = validSentences.length;
            var invalidCount = invalidSentences.length;
            var totalCount = validCount + invalidCount;

            return getPercentage(invalidCount, totalCount) <= idealPercentage;
        }
        
        return false;
    }
}

/**
 *  
 * FLESCH RATE METHODS
 * 
*/

{
    /* Return an integer with the number of syllables in the text */
    function getSyllables(selectedText) {
        // Borrowed from https://github.com/daveross/flesch-kincaid/blob/master/flesch-kincaid.js
        var subSyl = [/cial/, /tia/, /cius/, /cious/, /giu/, // belgium!
        /ion/, /iou/, /sia$/, /.ely$/, // absolutely! (but not ely!)
        /sed$/];

        var addSyl = [/ia/, /riet/, /dien/, /iu/, /io/, /ii/, /[aeiouym]bl$/, // -Vble, plus -mble
        /[aeiou]{3}/, // agreeable
        /^mc/, /ism$/, // -isms
        /([^aeiouy])\1l$/, // middle twiddle battle bottle, etc.
        /[^l]lien/, // // alien, salient [1]
        /^coa[dglx]./, // [2]
        /[^gq]ua[^auieo]/, // i think this fixes more than it breaks
        /dnt$/];

        var xx = selectedText.toLowerCase().replace(/'/g, '').replace(/e\b/g, '');
        var scrugg = xx.split(/[^aeiouy]+/).filter(Boolean); // '-' should be perhaps added?

        return undefined === selectedText || null === selectedText || '' === selectedText ? 0 : 1 === xx.length ? 1 : subSyl.map(function (r) {
            return (xx.match(r) || []).length;
        }).reduce(function (a, b) {
            return a - b;
        }) + addSyl.map(function (r) {
            return (xx.match(r) || []).length;
        }).reduce(function (a, b) {
            return a + b;
        }) + scrugg.length - (scrugg.length > 0 && '' === scrugg[0] ? 1 : 0) +
        // got no vowels? ("the", "crwth")
        xx.split(/\b/).map(function (selectedText) {
            return selectedText.trim();
        }).filter(Boolean).filter(function (selectedText) {
            return !selectedText.match(/[.,'!?]/g);
        }).map(function (selectedText) {
            return selectedText.match(/[aeiouy]/) ? 0 : 1;
        }).reduce(function (a, b) {
            return a + b;
        });
    };

    /* Return a float with the number of syllables per word */
    function getSyllablesPerWord(selectedText) {
        var syllables = getSyllables(selectedText);
        var words = getWordCount(selectedText);
        return isNaN(syllables/words) ?  0 : (syllables/words);
    }

    /* Return a float with the Flesch Ease of Reading rate */
    function getFleschRate(selectedText) {
        var rate = 206.835 - 1.015 * getWordsPerSentence(selectedText) - 84.6 * getSyllablesPerWord(selectedText);
        //Add three points to resemble MS Word's ratings.
        rate < 90 ? rate = rate + 3 : false;
        return rate > 100 ? 100 : rate < 0 ? 0 : rate.toFixed(2);
    }
}
