<?php
if (count($footerMenuItems) > 0):
?>
<ul>

<?php foreach ($footerMenuItems as $footerItem): ?>
    <li>
        <?php echo $footerItem; ?>
    </li>    
<?php endforeach; ?>

</ul>
<?php
endif;
?>
