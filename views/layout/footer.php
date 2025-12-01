    <footer class="footer">
        <p>
            &copy; <?= date('Y'); ?> NEXERA. Crafted with care for modern academic ecosystems.
        </p>
    </footer>
    <div id="modal-development" class="modal" role="dialog" aria-modal="true" aria-label="Feature in development">
        <div class="modal__content glass-card">
            <img src="assets/spinner-neon.gif" alt="" class="glow-icon" />
            <h3>In Progress</h3>
            <p>The student location tracking feature is under active development. Stay tuned!</p>
            <button type="button" class="neon-button" data-close-modal>Close</button>
        </div>
    </div>
    <script type="module" src="js/main.js"></script>
    <?php if (!empty($extraScriptsBody)): ?>
        <?php foreach ($extraScriptsBody as $script): ?>
            <script type="module" src="<?= e($script); ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>


