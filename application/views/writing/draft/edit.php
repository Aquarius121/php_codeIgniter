<div class="row-fluid">
    <div class="span12">
        <header class="page-header">
            <div class="row-fluid">
                <div class="span12">										
                    <h1>Edit Press Release</h1>                  
                </div>
            </div>
        </header>
	</div>
</div>
<div class="row-fluid">
    <div class="span12">
        <div class="content">		
            <div class="row-fluid">
                <div class="span12">
                    <form class="tab-content required-form" method="post" action="" id="edit_pr_form">								
                        <div class="row-fluid">
                            <div class="span8 information-panel">						
                                <section>                        	
                                    <ul>
                                        <li>
                                            <h2>Title</h2>                                                
                                            <input class="in-text span12 required required-callback" 
                                                type="text" name="title" 
                                                id="title" placeholder="Title of Press Release" 
                                                maxlength="<?= $ci->conf('title_max_length') ?>"
                                                value="<?= $vd->esc(@$vd->content->title) ?>" 
                                                data-required-name="Title"
                                                data-required-callback="title-min-words title-max-chars" />
                                            <script>
                                            
                                            $(function() {
                                                
                                                required_js.add_callback("title-min-words", function(value) {
                                                   var response = {valid:false,text:"must have at least 4 words"};
                                                   response.valid = /([a-z0-9]\S*(\s+[^a-z0-9]*|$)){4,}/i.test(value);
                                                   return response;
                                                });
                                                
                                            });
                                            
                                            </script>    
                                        </li>
                                        
                                        <li>
                                            <h2>Summary</h2>
                                            <textarea class="in-text span12 required required-callback" 
                                                id="summary" name="summary"
                                                data-required-name="Summary" 
                                                placeholder="Summary of Press Release"
                                                data-required-callback="summary-min-words" 
                                                ><?= $vd->esc(@$vd->content_data->summary) ?></textarea>
                                            <p class="help-block ta-right" id="summary_countdown_text">
                                                <span id="summary_countdown"></span> Characters Left</p>
                                            
                                            <script>                                                
                                            $(function() {                                                    
                                                $("#summary").limit_length(<?= $ci->conf('summary_max_length') ?>, 
                                                    $("#summary_countdown_text"), 
                                                    $("#summary_countdown"));                                                  
                                                required_js.add_callback("summary-min-words", function(value) {
                                                    var response = { valid: false, text: "must have at least 10 words" };
                                                    response.valid = /([a-z0-9]\S*(\s+[^a-z0-9]*|$)){10,}/i.test(value);
                                                    return response;
                                                });
                                                
                                            });
                                            
                                            </script>
                                        </li>
                                        <li class="marbot-20 cke-container" id="content-container">
                                            <h2>Press Release Body</h2>
                                            <textarea class="in-text in-content span12 required required-callback"
                                            	id="content"
                                                data-required-name="Press Release Body" name="content" 
                                                data-required-callback="content-min-words content-max-chars 
                                                    				content-max-links-premium"
                                                placeholder="Press Release Body"><?= 
                                                $vd->esc(@$vd->content_data->content) 
                                            ?></textarea>	                                            								
                                            <script>
                                            
                                            $(function() {
                                                
                                                var min_word_count = <?= $ci->conf('press_release_min_words') ?>;
                                                
                                                var convert_to_text_format = function(value) {
                                                    value = value.replace(/<[^>]*>/g, " ");
                                                    value = value.replace(/&nbsp;/g, " ");
                                                    return value;
                                                };
                                                
                                                // the word regex used for counting words
                                                var word_count = /([a-z0-9]\S*(\s+[^a-z0-9]*|$))/ig;
                                                
                                                required_js.add_callback("content-min-words", function(value) {
                                                    value = convert_to_text_format(value);
                                                    var response = { valid: false, text: "must have at least <?= 
                                                        $ci->conf('press_release_min_words') ?> words" };
                                                    var match = value.match(word_count);
                                                    var count = match ? match.length : 0;
                                                    response.valid = count >= min_word_count;
                                                    return response;
                                                });
                                                
                                                required_js.add_callback("content-max-chars", function(value) {
                                                    value = convert_to_text_format(value);
                                                    var response = { valid: false, text: "must not exceed <?= 
                                                        $ci->conf('press_release_max_length') ?> characters" };
                                                    response.valid = value.length <= <?= 
                                                        $ci->conf('press_release_max_length') ?>;
                                                    return response;
                                                });
                                                
                                                required_js.add_callback("content-max-links-premium", function(value) {                                                    
                                                    var response = { valid: false, text: "can have at most <?= 
                                                        $ci->conf('press_release_links_premium') ?> external links" };
                                                    var a_links = value.match(/(<a[^>]*>)/gi);
                                                    response.valid = !a_links || a_links.length <= <?= 
                                                        $ci->conf('press_release_links_premium') ?>;
                                                    return response;
                                                });
                                                
                                                window.init_editor($("#content"), { height: 400 }, function() {

                                                    var _this = this;		
                                                    var content_word_text = $("#content_word_text");
                                                    var content_word_count = $("#content_word_count");
                                                    var show_word_count = function() {
                                                        var text = convert_to_text_format(_this.getData());
                                                        var match = text.match(word_count);
                                                        var count = match ? match.length : 0;
                                                        content_word_text.toggleClass("status-true", count >= min_word_count);
                                                        content_word_count.text(count);
                                                    };
                                                    
                                                    _this.on("contentDom", function() {
                                                        _this.document.on("keyup", function(ev) {
                                                            window.rate_limit(show_word_count, min_word_count);
                                                        });
                                                    });
                                                    
                                                    _this.on("instanceReady", function() {
                                                        var link_button = $("#content-container .cke_button__link");
                                                        link_button_handler = link_button[0].onclick;
                                                        link_button.removeAttr("onclick");
                                                        link_button.on("click", function(ev) {
                                                            // max number of links is 3 for premium, 0 otherwise
                                                            var max = <?= $ci->conf('press_release_links_premium') ?>;
                                                            var value = _this.getData();
                                                            var a_links = value.match(/(<a[^>]*>)/gi);
                                                            var count = a_links ? a_links.length : 0;
                                                            if (count < max) return link_button_handler.call(this);
                                                            // show an alert about reaching limit
                                                            bootbox.alert("You are limited to <strong>" + 
                                                                max + "<\/strong> embedded links with a premium" +
                                                                " press release.");
                                                        });
                                                    });
                                                    
                                                    show_word_count();
                                                    
                                                });
                                                
                                            });
                                            
                                            </script>
                                            <p class="help-block ta-right" id="content_word_text">
                                                <span id="content_word_count">0</span> Words (<?= 
                                                    $ci->conf('press_release_min_words') ?> Required)</p>
                                        </li>
                                        
                                        <section class="form-section supporting_quote">
                                            <h2>Supporting Quote</h2>
                                                <ul>
                                                    <li>
                                                        <textarea placeholder="Enter Supporting Quote" 
                                                            name="supporting_quote" class="in-text span12"
                                                        ><?= $vd->esc(@$vd->content_data->supporting_quote) ?></textarea>
                                                    </li>
                                                    <li>
                                                        <div class="row-fluid">
                                                            <div class="span6">
                                                                <input type="text" class="in-text span12" 
                                                                value="<?=$vd->content_data->supporting_quote_name ?>" 
                                                                    placeholder="Name of Person" 
                                                                    name="supporting_quote_name">
                                                            </div>
                                                            <div class="span6">
                                                                <input type="text" class="in-text span12" 
                                                                value="<?=$vd->content_data->supporting_quote_title ?>" 
                                                                    placeholder="Title of Person" 
                                                                    name="supporting_quote_title">
                                                            </div>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </section>
                                        
                                        <li>
                                            <h2>About Company</h2>
                                            <textarea class="in-text span12 required required-callback" 
                                                id="about_company" name="about_company"
                                                data-required-name="About Company" 
                                                placeholder="About Company"
                                                data-required-callback="about-company-min-words" 
                                                ><?= $vd->esc(@$vd->company_profile->summary) ?></textarea>
                                            <p class="help-block ta-right" id="about_comp_countdown_text">
                                                <span id="about_company_countdown"></span> Characters Left</p>
                                            
                                            <script>                                                
                                            $(function() {                                                    
                                                $("#about_company").limit_length(250, 
                                                    $("#about_comp_countdown_text"), 
                                                    $("#about_company_countdown"));                                                  
                                                required_js.add_callback("about-company-min-words", function(value) {
                                                    var response = { valid: false, text: "must have at least 10 words" };
                                                    response.valid = /([^\s]*[a-z][^\s]*(\s+|$)){10,}/i.test(value);
                                                    return response;
                                                });
                                                
                                            });
                                            
                                            </script>
                                        </li>
                                        <li>
                                            <div class="span6">
                                                <button type="submit" value="1" 
                                                            class="span11 bt-silver" name="save">Save</button>
                                            </div>                                             
                                        </li>
                                        
                                    </ul>                                        
                                </section>
                            </div>	
                    </form>
                </div>
            </div>
        </div>
     </div>
</div>

<?php 

    $loader = new Assets\JS_Loader(
        $ci->conf('assets_base'), 
        $ci->conf('assets_base_dir'));
    $loader->add('lib/bootbox.min.js');
    $loader->add('js/required.js');
    $render_basic = $ci->is_development();
    echo $loader->render($render_basic);

?>                       	
