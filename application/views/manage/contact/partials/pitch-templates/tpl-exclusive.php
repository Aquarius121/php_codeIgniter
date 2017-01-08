<p>Hi ((first-name))</p>

<p>Since you cover ((industry)) news and stories, we thought that this would be an interesting story for your audience. We would like to provide you with an exclusive back story of <span class="ei-highlight">[Angle of Press Release]</span>.</p>

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