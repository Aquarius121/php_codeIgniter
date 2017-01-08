SELECT cm.name, c.title, c.date_publish, 
	IF (c.is_premium=1, 'Premium', 'Basic'), 
	IF (crp.provider='provider' AND crp.is_confirmed=1, 'PR Newswire', '') 
from nr_content c 
inner join nr_company cm on cm.id = c.company_id 
inner join nr_user u on cm.user_id = u.id 
left join nr_content_release_plus crp on crp.content_id = c.id
where c.type = 'pr' and u.email = '...................' 
order by date_publish desc;
