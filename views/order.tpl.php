<div class="p-4">
    <table class="table table-dark">
        <?= $tableHeader; ?>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= $order->key(); ?></td>
                    <td><?= $order->get('Total'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>