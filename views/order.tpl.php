<div class="p-4">
    <table class="table table-dark">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Total</th>
            </tr>
        </thead>
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