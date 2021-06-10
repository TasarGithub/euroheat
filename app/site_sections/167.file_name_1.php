<?php if (!empty($GLOBALS['tpl_all_site_sections'])): # выводим разделы сайта ?>
<?php for ($i=0;$i<count($GLOBALS['tpl_all_site_sections']);$i++): ?>

<div class="site_map_level_<?php echo $GLOBALS['tpl_all_site_sections'][$i]['level']; ?> site_map_item">
<a href="<?php echo $GLOBALS['tpl_all_site_sections'][$i]['url']; ?>"><?php echo $GLOBALS['tpl_all_site_sections'][$i]['name']; ?></a>

<?php if (!empty($GLOBALS['tpl_all_site_sections'][$i]['date_add'])):
  echo $GLOBALS['tpl_all_site_sections'][$i]['date_add'];
  endif; ?>
  
<?php if (!empty($GLOBALS['tpl_all_site_sections'][$i]['descr'])):
  echo $GLOBALS['tpl_all_site_sections'][$i]['descr'];
  endif; ?>

</div>

<?php endfor; endif; # /выводим разделы сайта ?>