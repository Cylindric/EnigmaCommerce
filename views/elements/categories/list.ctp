<table class="list">

    <?php foreach ($categories as $category): ?>
    <tr>
        <td><?php echo $this->element('categories/list_category', $category); ?></td>
    </tr>
    <?php endforeach; ?>

</table>