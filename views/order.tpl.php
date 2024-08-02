<div class="p-4">
    <table class="table">
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