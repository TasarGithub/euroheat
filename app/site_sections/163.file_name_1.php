<?php if (!empty($GLOBALS['tpl_articles'])): # ����� ������ ?>
<?php foreach ($GLOBALS['tpl_articles'] as $item): ?>
<div class="news_item">
    <a class="news_item_a" href="/sovet/<?php echo $item['url']; ?>/"><?php echo $item['h1']; ?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/sovet/<?php echo $item['url']; ?>/">��������� &raquo;</a>
</div>
<?php endforeach; ?>
<?php endif; # /����� ������ ?>

<?php if (!empty($GLOBALS['tpl_articles_pages'])): # ����� ������� ?>
<div class="paging_b">
    <div class="paging">
        <?php echo $GLOBALS['tpl_articles_pages']; ?>
    </div>

    <div class="sum_pages">�����: <?php echo $GLOBALS['tpl_articles_count']; ?></div>
    <div class="clear"></div>
</div>
<?php endif; # /����� ������� ?>