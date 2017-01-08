<p>Hi ((first-name))</p>

<p>I’m <?= value_or($tpl_data->full_name, '<span class="ei-highlight">{FULL NAME}</span>') ?> from <?= value_or($company->name, '<span class="ei-highlight">{COMPANY}</span>') ?>. Since you cover ((industry)) news and stories, we thought that this would be an interesting story for your audience. <span class="ei-highlight">[Insert interesting fact about the story]</span>. We would like to provide you a first look at <span class="ei-highlight">[Your product]</span>. Please see our press release below.</p>

<p>Here are some highlights:</p>
<ul>
	<li><span class="ei-highlight">{Feature 1}</span></li>
	<li><span class="ei-highlight">{Feature 2}</span></li>
	<li><span class="ei-highlight">{Feature 3}</span></li>
</ul>

<p>Should you have any questions, please feel free to reach out. My direct email is <?= $tpl_data->email ?>.</p>

<p>Regards,</br>
<?= value_or($tpl_data->full_name, '<span class="ei-highlight">{FULL NAME}</span>') ?></br>
<?= value_or($company->name, '<span class="ei-highlight">{COMPANY}</span>') ?></p>