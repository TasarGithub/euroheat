<?php if (!empty($GLOBALS['tpl_faq'])): # ����� ��������-������� ?>
<?php foreach ($GLOBALS['tpl_faq'] as $item): ?>
<div class="news_item">
    <a class="news_item_a" href="/vopros/<?php echo $item['url']; ?>/"><?php echo $item['h1']; ?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/vopros/<?php echo $item['url']; ?>/">��������� &raquo;</a>
</div>
<?php endforeach; ?>
<?php endif; # /����� ��������-������� ?>

<br />   

<?php if (!empty($GLOBALS['tpl_faq_pages'])): # ����� ������� ?>
<div class="paging_b">
    <div class="paging">
        <?php echo $GLOBALS['tpl_faq_pages']; ?>
    </div>

    <div class="sum_pages">�����: <?php echo $GLOBALS['tpl_faq_count']; ?></div>
    <div class="clear"></div>
</div>
<?php endif; # /����� ������� ?>