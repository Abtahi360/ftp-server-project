<?php $basePath = $basePath ?? '../../'; ?>
</div><!-- /.container -->

<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-brand">
            <span class="icon">💾</span>
            FTP Media System
        </div>
        <nav class="footer-links">
            <a href="<?= $basePath ?>views/member/home.php">Home</a>
            <a href="<?= $basePath ?>views/member/browse.php">Browse</a>
            <a href="<?= $basePath ?>views/member/search.php">Search</a>
            <a href="<?= $basePath ?>views/member/request_box.php">Request Content</a>
        </nav>
    </div>
    <p class="footer-copy">&copy; <?= date('Y') ?> FTP Media — ISP Content Portal &nbsp;·&nbsp; Web Technologies Project 04_A</p>
</footer>

</body>
</html>
