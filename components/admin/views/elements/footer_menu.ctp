<?php
if (count($footerMenuItems) > 0):
?>
<ul class="footer_menu">

<?php foreach ($footerMenuItems as $footerItem): ?>
    <li>
        <?php echo $this->Html->link($footerItem['title'], $footerItem['url'], $footerItem['options']); ?>
    </li>    
<?php endforeach; ?>

</ul>
<?php
endif;
?>
