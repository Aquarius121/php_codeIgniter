<?= $vd->writer_first_name ?>,<br /><Br />
The pitch: "<?= $vd->esc($vd->pitch_subject) ?>" you wrote requires some editing.<br /><br />

Editor Comments:<br />
<code><?= $vd->esc($vd->comments) ?></code><br /><br />

To edit the pitch click here<br />
<?= $ci->conf('mot_host_url') ?>writing_task/pitch/write/<?= $vd->pitch_order_id ?>/pending
<br /><br />

Best Regards,<br />
MOT Admin