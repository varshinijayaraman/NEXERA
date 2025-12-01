<?php if (!empty($flash['success'])): ?>
    <div class="alert alert-success" role="status">
        <?= e($flash['success']); ?>
    </div>
<?php endif; ?>

<?php if (!empty($flash['error'])): ?>
    <div class="alert alert-error" role="alert">
        <?= e($flash['error']); ?>
    </div>
<?php endif; ?>


