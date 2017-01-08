select company_id, company_name, concat('http://', name, '.newswire.com'), is_active
from nr_newsroom n inner join nr_user u on n.user_id = u.id where n.is_active = 1
and u.email = '.....................'