<h1 style="color:#c8483f; font-size:20px; font-family:Helvetica, Arial, sans-serif; font-weight:500; margin:0; 
	padding:0 30px 0 30px; text-align:center;">Your subscription for <?= $item->name ?> has been cancelled.</h1>
<br />

Subscription: <span style="color:#f79432;"><?= $item->name ?></span>
<br />
Date Created: <span style="color:#f79432;"><?= $dt_created->format('M j, Y H:i') ?> UTC</span>
<br />
Date Cancelled: <span style="color:#f79432;"><?= Date::utc()->format('M j, Y H:i') ?> UTC</span>