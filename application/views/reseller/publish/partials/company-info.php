<section>                        	
     <h2>Company Information</h2>     
     <li>
        <input class="in-text span12 required" type="email" name="email" 
            id="email" placeholder="Email Address"
            value="<?= $vd->esc($vd->c_contact->email) ?>" 
            data-required-name="Email Address" />
    </li>
</section>

<section class="form-section basic-information">                        	
    <ul>
        <li>
            <div class="row-fluid">
                <div class="span12">
                    <input class="in-text span12 required" type="text" name="company_name" 
                    id="company_name" placeholder="Your Company Name"
                    value="<?= $vd->esc($vd->company->name) ?>" data-required-name="Company Name" />
                </div>
            </div>
            <div class="row-fluid">
                <div class="span6">
                    <input class="in-text span12" type="text" name="c_contact_name" 
                    id="c_contact_name" placeholder="Company Contact Name"
                    value="<?= $vd->esc($vd->c_contact->name) ?>" />                                   
                </div>
           
            
                <div class="span6">
                    <input class="in-text span12 required" type="url" name="website" 
                    id="website" placeholder="Company Website"
                    value="<?= $vd->esc($vd->c_profile->website) ?>" data-required-name="Company Website" />                </div>
            </div>                                      
                                                      
        </li>
    </ul>
</section>

   
<section class="form-section company-address">
<h2>Company Address</h2>
<ul>
    <li>
        <div class="row-fluid">
            <div class="span8">
                <input class="in-text span12" name="address_street" 
                placeholder="Street Address" type="text"
                value="<?= $vd->esc($vd->c_profile->address_street) ?>" />
            </div>
            <div class="span4">
                <input class="in-text span12"  name="address_apt_suite"
                    type="text" placeholder="Apt / Suite" 
                    value="<?= $vd->esc($vd->c_profile->address_apt_suite) ?>" />
            </div>
        </div>
    </li>
    <li>
        <div class="row-fluid">
            <div class="span4">
                <input class="in-text span12" type="text" 
                    name="address_city" placeholder="City"
                    value="<?= $vd->esc($vd->c_profile->address_city) ?>" />
            </div>
            <div class="span4">
                <input class="in-text span12" type="text" 
                    name="address_state" placeholder="State / Region"
                    value="<?= $vd->esc($vd->c_profile->address_state) ?>" />
            </div>
            <div class="span4">
                <input class="in-text span12" type="text" 
                    name="address_zip" placeholder="Zip Code"
                    value="<?= $vd->esc($vd->c_profile->address_zip) ?>" />
            </div>
        </div>
    </li>
    
    <li>
        <div class="row-fluid">
            <div class="span6" id="select-country">
                <select class="show-menu-arrow span12" name="address_country_id" 
                    data-required-name="Country">
                    <option class="selectpicker-default" title="Select Country" value=""
                        <?= value_if_test(!$vd->c_profile->address_country_id, 'selected') ?>>Select Country</option>
                    <?php foreach ($vd->countries as $country): ?>
                    <option value="<?= $country->id ?>"
                        <?= value_if_test(($vd->c_profile->address_country_id == $country->id), 'selected') ?>>
                        <?= $vd->esc($country->name) ?>
                    </option>
                    <?php endforeach ?>
                </select>
                <script>

                $(function() {
                    
                    $("#select-country select")
                        .selectpicker({ size: 10 })
                        .addClass("required");
                    
                });
                
                </script>
            </div>
            <div class="span6">
                <input class="in-text span12" type="text" 
                    name="phone" placeholder="Phone Number"
                    value="<?= $vd->esc($vd->c_profile->phone) ?>" />
            </div>
        </div>
    </li>
</ul>
</section>


<section>
    <ul>
        <li>
            <h2>Company Details</h2>
            <div class="header-help-block">Write a brief summary of what your business is about.</div>            
            <textarea class="in-text span12 required required-callback"  
                id="company_summary" 
                name="company_summary"
                placeholder="Your Company Details" 
                data-required-name="Company Details" 
                data-required-callback="comp-detail-min-words"
                ><?= $vd->esc($vd->c_profile->summary) ?></textarea>		
            <ul>
                <li>
                    <div class="span9 help-block" id="companydetails_words">
                        <span id="companydetails_wordscount"></span> Words
                    </div>
                     <div class="span3 help-block" id="companydetails_countdown_text">
                        <span id="companydetails_countdown"></span> Characters Left
                     </div>
                </li>
            </ul>            
            <script>
            
            $(function() {
    
                var companydetails = $("#company_summary");
                var companydetails_wordscount = $("#companydetails_wordscount");
                var min_word_count = 20;
    
                companydetails.limit_length(400,
                    $("#companydetails_countdown_text"),
                    $("#companydetails_countdown"));
    
                var count_words = function(value) {
                    var pattern = /([a-z0-9]+([^\s]*[\s]+[^a-z0-9]*|$))/ig;
                    var match = value.match(pattern);
                    var count = match ? match.length : 0;
                    return count;
                };
    
                var show_word_count = function() {
                    var count = count_words(companydetails.val());
                    companydetails_wordscount.html(count);
                };
    
                companydetails.keyup(show_word_count);
                show_word_count();
    
                required_js.add_callback("comp-detail-min-words", function(value) {
                    var response = { valid: false, text: "must have at least " 
                        + min_word_count 
                        + " words" };
                    var count = count_words(value);
                    response.valid = count >= min_word_count;
                    return response;
                });
    
            });
            </script>							
        </li>
    </ul>
</section>